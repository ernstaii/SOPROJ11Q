const presetBox = document.querySelector("#preset-box");
const presetNameInput = document.querySelector("#preset_name");
const durationInput = document.querySelector("#duration");
const intervalInput = document.querySelector("#interval");
const colourInput = document.querySelector("#colour");
const logoInput = document.querySelector("#logo");
const presetSelector = document.querySelector("#presets");
const savePresetButton = document.querySelector("#save_preset_button");

let presets = [];

async function savePreset() {
    // TODO: Add game theme
    let lootLats = [];
    let lootLngs = [];
    let borderLats = [];
    let borderLngs = [];

    let mapDataValid = (lootLatLngs.length > 0 && markerLatLngs.length > 0 && policeStationLatLng != null);
    let durationInputValid = (durationInput.value && durationInput.checkValidity());
    let intervalInputValid = (intervalInput.value && intervalInput.checkValidity());
    let presetNameValid = (presetNameInput.value && presetNameInput.value.trim() !== "");

    if (presetNameValid) {
        let passedCheck = true;
        presets.forEach(preset => {
            if (preset.name === presetNameInput.value) {
                showValidationError('De naam ' + preset.name + ' is al in gebruik. Vul a.u.b. een andere naam in.');
                passedCheck = false;
            }
        });
        if (!passedCheck)
            return;
    }
    if (!mapDataValid) {
        showValidationError('Plaats a.u.b. alle pins op de kaart.');
        return;
    }
    if (!durationInputValid) {
        showValidationError('Vul a.u.b. een valide speelduur in.');
        return;
    }
    if (!intervalInputValid) {
        showValidationError('Vul a.u.b. een valide interval in tussen locatieupdates.');
        return;
    }
    if (!presetNameValid) {
        showValidationError('Vul a.u.b. een naam in voor het template.');
        return;
    }

    lootLatLngs.forEach(lootItem => {
        lootLats.push(lootItem.lat);
        lootLngs.push(lootItem.lng);
    });
    markerLatLngs.forEach(border => {
        borderLats.push(border.lat);
        borderLngs.push(border.lng);
    });

    $.ajaxSetup({
        headers: {
            'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
        }
    });
    await $.ajax({
        url: '/presets',
        type: 'POST',
        data: {
            name: presetNameInput.value,
            duration: durationInput.value,
            interval: intervalInput.value,
            police_station_lat: policeStationLatLng.lat,
            police_station_lng: policeStationLatLng.lng,
            loot_lats: lootLats,
            loot_lngs: lootLngs,
            loot_names: lootNames,
            border_lats: borderLats,
            border_lngs: borderLngs,
            colour: colourInput.value,
            logo: logoInput.value
        },
        success: function () {
            location.reload();
        },
        error: function (err) {
            showValidationError(err.responseJSON.message);
        }
    });
}

async function loadPreset(game_id) {
    if (presetSelector.value == -1)
        return;

    presetSelector.disabled = true;
    savePresetButton.disabled = true;

    let preset = JSON.parse(presetSelector.value);
    durationInput.value = preset.duration;
    intervalInput.value = preset.interval;
    colourInput.value = preset.colour_theme;
    logoInput.value = preset.logo;

    createButtons = false;
    let latLng = preset.police_station_location.split(',');
    if (policeStationMarker != null) {
        removePoliceStation();
    }
    applyExistingPoliceStation(latLng[0], latLng[1]);
    savePoliceStation(game_id);


    $.ajaxSetup({
        headers: {
            'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
        }
    });
    await $.ajax({
        url: '/games/' + game_id + '/loot',
        type: 'DELETE',
        success: function () {
            $.ajax({
                url: '/presets/' + preset.id + '/loot',
                type: 'GET',
                success: function (loot) {
                    removeAllLoot();
                    loot.forEach(loot_item => {
                        let latLng = loot_item.location.split(',');
                        applyExistingLoot(latLng[0], latLng[1], loot_item.name);
                    });
                    saveLoot(game_id);
                }
            });
        }
    });

    await $.ajax({
        url: '/games/' + game_id + '/border-markers',
        type: 'DELETE',
        success: function () {
            $.ajax({
                url: '/presets/' + preset.id + '/border-markers',
                type: 'GET',
                success: function (borderMarkers) {
                    removeAllMarkers();
                    borderMarkers.forEach(marker => {
                        let latLng = marker.location.split(',');
                        applyExistingMarker(latLng[0], latLng[1]);
                    });
                    drawLinesForExistingMarkers();
                    saveMarkers(game_id);
                    presetSelector.disabled = false;
                    savePresetButton.disabled = false;
                }
            });
        }
    });
}

function showValidationError (message) {
    let validationBox = document.querySelector('#template_validation_msg');

    if (validationBox == null) {
        validationBox = document.createElement('div');
        validationBox.classList.add('form-item');
        validationBox.id = "template_validation_msg";
        presetBox.appendChild(validationBox);
    }

    let errorMsgElem = document.createElement('p');
    errorMsgElem.id = 'validation_msg';
    errorMsgElem.style.color = 'red';
    errorMsgElem.textContent = message;
    validationBox.appendChild(errorMsgElem);

    if (validationBox.children.length > 4) {
        validationBox.removeChild(validationBox.children[3]);
    }

    setTimeout(function () {
        if (validationBox.children.length > 0) {
            validationBox.removeChild(validationBox.children[0]);
        }
    }, 7500);
}

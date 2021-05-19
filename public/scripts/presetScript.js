const presetBox = document.querySelector("#preset-box");
const presetNameInput = document.querySelector("#preset_name");
const durationInput = document.querySelector("#duration");
const intervalInput = document.querySelector("#interval");
const colourInput = document.querySelector("#colour");
const presetSelector = document.querySelector("#presets");
const savePresetButton = document.querySelector("#save_preset_button");
const imageElementBox = document.querySelector('#img_element_box');
const logoInput = document.querySelector("#logo");

let presets = [];

async function savePreset() {
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
                showMessage('De naam ' + preset.name + ' is al in gebruik. Vul a.u.b. een andere naam in.', 'red');
                passedCheck = false;
            }
        });
        if (!passedCheck)
            return;
    }
    if (!mapDataValid) {
        showMessage('Plaats a.u.b. alle pins op de kaart.', 'red');
        return;
    }
    if (!durationInputValid) {
        showMessage('Vul a.u.b. een valide speelduur in.', 'red');
        return;
    }
    if (!intervalInputValid) {
        showMessage('Vul a.u.b. een valide interval in tussen locatieupdates.', 'red');
        return;
    }
    if (!presetNameValid) {
        showMessage('Vul a.u.b. een naam in voor het template.', 'red');
        return;
    }

    let logoBase64 = null;
    if (imageElementBox.children.length > 0)
        logoBase64 = imageElementBox.children[0].src;

    if (logoBase64 != null) {
        let imageElem = new Image();

        imageElem.onload = function() {
            if (imageElem.width > 300 || imageElem.height > 200) {
                showMessage('Upload a.u.b. een foto met een maximale grootte van 300x200 pixels.', 'red');
                return;
            }

            savePresetToDB(lootLats, lootLngs, borderLats, borderLngs, logoBase64); //やばい
        };

        imageElem.src = logoBase64;
    }
    else {
        await savePresetToDB(lootLats, lootLngs, borderLats, borderLngs, logoBase64);
    }
}

async function savePresetToDB(lootLats, lootLngs, borderLats, borderLngs, logoBase64) {
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
            colour_theme: colourInput.value,
            logo: logoBase64
        },
        success: function () {
            location.reload();
            showMessage('Het nieuwe template is succesvol aangemaakt.', 'green');
        },
        error: function (err) {
            showMessage(err.responseJSON.message, 'red');
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

    if (imageElementBox.children.length > 0) {
        imageElementBox.innerHTML = '';
    }

    if (preset.logo != null) {
        let newImageElement = document.createElement('img');
        newImageElement.id = 'logo_image';
        newImageElement.src = 'data:image/png;base64,' + preset.logo;
        newImageElement.name = 'logo_upload';
        let hiddenInput = document.createElement('input');
        hiddenInput.id = 'logo_image_input';
        hiddenInput.type = 'hidden';
        hiddenInput.value = preset.logo;
        hiddenInput.name = 'logo_upload';
        imageElementBox.appendChild(newImageElement);
        imageElementBox.appendChild(hiddenInput);
    }

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

function showMessage (message, colour) {
    let validationBox = document.querySelector('#template_validation_msg');

    if (validationBox == null) {
        validationBox = document.createElement('div');
        validationBox.classList.add('form-item');
        validationBox.id = "template_validation_msg";
        presetBox.appendChild(validationBox);
    }

    let errorMsgElem = document.createElement('p');
    errorMsgElem.id = 'validation_msg';
    errorMsgElem.style.color = colour;
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

function changeImageElement() {
    let files = logoInput.files;
    if (FileReader && files && files.length) {
        let fr = new FileReader();
        fr.onload = function () {
            if (imageElementBox.children.length > 0) {
                imageElementBox.innerHTML = '';
            }
            let newImageElement = document.createElement('img');
            newImageElement.id = 'logo_image';
            newImageElement.src = fr.result;
            let hiddenInput = document.createElement('input');
            hiddenInput.id = 'logo_image_input';
            hiddenInput.type = 'hidden';
            hiddenInput.value = fr.result;
            hiddenInput.name = 'logo_upload';
            imageElementBox.appendChild(newImageElement);
            imageElementBox.appendChild(hiddenInput);
        };
        fr.readAsDataURL(files[0]);
    }
}

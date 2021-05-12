const presetBox = document.querySelector("#preset-box");

async function savePreset() {
    // TODO: Add game theme
    let presetNameInput = document.querySelector("#preset_name");
    let durationInput = document.querySelector("#duration");
    let intervalInput = document.querySelector("#interval");
    let lootLats = [];
    let lootLngs = [];
    let borderLats = [];
    let borderLngs = [];

    let mapDataValid = (lootLatLngs.length > 0 && markerLatLngs.length > 0 && policeStationLatLng != null);
    let durationInputValid = (durationInput.value && durationInput.checkValidity());
    let intervalInputValid = (intervalInput.value && intervalInput.checkValidity());
    let presetNameValid = (presetNameInput.value && presetNameInput.value.trim() !== "");

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
            border_lngs: borderLngs
        },
        error: function (err) {
            console.log(err);
        }
    });
}

function loadPreset() {

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

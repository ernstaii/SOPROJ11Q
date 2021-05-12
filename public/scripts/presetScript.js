async function savePreset() {
    // TODO: Add game theme
    let presetNameInput = document.querySelector("#preset_name")
    let durationInput = document.querySelector("#duration");
    let intervalInput = document.querySelector("#interval");
    let lootLats = [];
    let lootLngs = [];
    let borderLats = [];
    let borderLngs = [];

    if (lootLatLngs.length !== 0 || markerLatLngs.length !== 0 || policeStationLatLng == null || !(presetNameInput.value || presetNameInput.value.trim() === "") || !durationInput.value || !intervalInput.value) {
        let errorMsg = document.createElement('p');
        return;
    }

    lootLatLngs.forEach(latLng => {
        lootLats.push(latLng.lat);
        lootLngs.push(latLng.lng);
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

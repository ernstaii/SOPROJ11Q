const mapCenter = [51.950180, 5.235682];
const maxPopupWidth = 400;
const zoomLevel = 9;
const minZoomLevel = 9;
const corner1 = L.latLng(53.828464, 2.871753),
    corner2 = L.latLng(50.559772, 7.521491),
    mapBounds = L.latLngBounds(corner1, corner2);
const removeMarkerButton = document.querySelector('#button_remove_markers');
const mapBox = document.querySelector('.mapbox');
const tab_2 = document.querySelector('#tab_2');
const tab_3 = document.querySelector('#tab_3');

const lootIcon = new L.Icon({
    iconUrl: 'https://raw.githubusercontent.com/pointhi/leaflet-color-markers/master/img/marker-icon-gold.png',
    shadowUrl: 'https://cdnjs.cloudflare.com/ajax/libs/leaflet/0.7.7/images/marker-shadow.png',
    iconSize: [25, 41],
    iconAnchor: [12, 41],
    popupAnchor: [1, -34],
    shadowSize: [41, 41]
});

const policeStationIcon = new L.Icon({
    iconUrl: 'https://raw.githubusercontent.com/pointhi/leaflet-color-markers/master/img/marker-icon-red.png',
    shadowUrl: 'https://cdnjs.cloudflare.com/ajax/libs/leaflet/0.7.7/images/marker-shadow.png',
    iconSize: [25, 41],
    iconAnchor: [12, 41],
    popupAnchor: [1, -34],
    shadowSize: [41, 41]
});

const borderIcon = new L.Icon({
    iconUrl: 'https://raw.githubusercontent.com/pointhi/leaflet-color-markers/master/img/marker-icon-green.png',
    shadowUrl: 'https://cdnjs.cloudflare.com/ajax/libs/leaflet/0.7.7/images/marker-shadow.png',
    iconSize: [25, 41],
    iconAnchor: [12, 41],
    popupAnchor: [1, -34],
    shadowSize: [41, 41]
});

let saveLootButton;
let lootNameInput;
let removeLootButton;
let removePoliceStationButton;
let savePoliceStationButton;

let saveMarkerButton = document.querySelector('#button_save_markers');
let createButtons = true;
let markerLatLngs = [];
let markers = [];
let lines = [];

let lootLatLngs = [];
let loot_markers = [];
let lootNames = [];

let policeStationMarker = null;
let policeStationLatLng = null;

setTimeout(() => {
    mymap.invalidateSize(true);
}, 0);

let mymap;
initMap();

function initMap() {
    mymap = L.map(document.getElementById('map')).setView(mapCenter, zoomLevel);
    mymap.options.minZoom = minZoomLevel;
    mymap.setMaxBounds(mapBounds);
    mymap.addControl(new L.Control.Fullscreen());
    const attribution = 'Map data &copy; <a href="https://www.openstreetmap.org/">OpenStreetMap</a> contributors, <a href="https://creativecommons.org/licenses/by-sa/2.0/">CC-BY-SA</a>, Imagery © <a href="https://www.mapbox.com/">Mapbox</a>';
    const tileURL = 'https://a.tile.openstreetmap.org/{z}/{x}/{y}.png';
    const tiles = L.tileLayer(tileURL, {attribution});

    tiles.addTo(mymap);
    mymap.on('click', addMarker);

    saveMarkerButton.disabled = true;
}

function addMarker(e) {
    let contains = false;
    markerLatLngs.forEach(latlng => {
        if (latlng.equals(e.latlng)) {
            contains = true;
        }
    });
    if (contains) {
        return;
    }
    let newMarker = L.marker(e.latlng, {icon: borderIcon})
        .bindPopup(L.popup({maxWidth: maxPopupWidth})
            .setContent('Border marker ' + (markers.length + 1)))
        .addTo(mymap);
    applyEvents(newMarker);
    markers.push(newMarker);
    markerLatLngs.push(newMarker.getLatLng());
    if (lines.length > 1) {
        mymap.removeLayer(lines[lines.length - 1]);
        lines.pop();
    }
    if (markers.length > 1) {
        lines.push(L.polyline(markerLatLngs, {color: 'black', dashArray: '30, 30', dashOffset: '0'}).addTo(mymap));
    }
    if (markers.length > 2) {
        addNewLineBetweenFirstAndLast();
    }

    if (markers.length >= 3) {
        saveMarkerButton.disabled = false;
        saveMarkerButton.title = '';
    }
}
function removeAllMarkers() {
    markers.forEach(item => {
        mymap.removeLayer(item);
    });
    lines.forEach(line => {
        mymap.removeLayer(line);
    });
    markers = [];
    markerLatLngs = [];
    lines = [];
}

function removeLastMarker() {
    if (markers.length > 0) {
        mymap.removeLayer(markers[markers.length - 1]);
        markers.pop();
        markerLatLngs.pop();
    }
    if (lines.length > 0) {
        if (markers.length > 1) {
            for (let i = 0; i < 2; i++) {
                if (lines.length > 1) {
                    mymap.removeLayer(lines[lines.length - 1]);
                    lines.pop();
                }
            }
        } else {
            mymap.removeLayer(lines[lines.length - 1]);
            lines.pop();
        }
    }
    if (markers.length > 2) {
        addNewLineBetweenFirstAndLast();
    }

    if (markers.length < 3 && saveMarkerButton != null) {
        saveMarkerButton.disabled = true;
        saveMarkerButton.title = 'Er zijn minstens 3 markers nodig voordat het veld opgeslagen kan worden.';
    }
}

function addNewLineBetweenFirstAndLast() {
    let firstLastMarkerLatLngs = [];
    firstLastMarkerLatLngs.push(markerLatLngs[0], markerLatLngs[markerLatLngs.length - 1]);
    lines.push(L.polyline(firstLastMarkerLatLngs, {color: 'black', dashArray: '30, 30', dashOffset: '0'}).addTo(mymap));
}

async function saveMarkers(id) {
    if (markers.length < 3) {
        return;
    }
    $.ajaxSetup({
        headers: {
            'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
        }
    });
    let lats = [];
    let lngs = [];
    markerLatLngs.forEach(latLng => {
        lats.push(latLng.lat);
        lngs.push(latLng.lng);
    });

    await $.ajax({
        url: '/games/' + id + '/border-markers',
        type: 'POST',
        data: {lats: lats, lngs: lngs},
        success: function (data) {
            if (!createButtons)
                saveMarkerButton = null;
            if (saveMarkerButton != null) {
                mymap.off('click');
                mapBox.removeChild(saveMarkerButton);
                mapBox.removeChild(removeMarkerButton);
                createLootButtons(id);
                mymap.on('click', addLoot);
            }
        },
        error: function (err) {
            console.log(err);
        },
    });
}

function applyExistingMarker(lat, lng) {
    let latlng = L.latLng(lat, lng);
    let newMarker = L.marker(latlng, {icon: borderIcon})
        .bindPopup(L.popup({maxWidth: maxPopupWidth})
            .setContent('Border marker ' + (markers.length + 1)))
        .addTo(mymap);
    applyEvents(newMarker);
    markers.push(newMarker);
    markerLatLngs.push(newMarker.getLatLng());
}

function drawLinesForExistingMarkers(game_id) {
    if (createButtons)
        if (markers.length > 0) {
            mymap.off('click');
            mapBox.removeChild(saveMarkerButton);
            mapBox.removeChild(removeMarkerButton);
            createLootButtons(game_id);
            mymap.on('click', addLoot);
        }
    for (let i = 0; i < markerLatLngs.length - 1; i++) {
        if (i < markerLatLngs.length - 2) {
            lines.push(L.polyline(markerLatLngs, {color: 'black', dashArray: '30, 30', dashOffset: '0'}).addTo(mymap));
        } else {
            addNewLineBetweenFirstAndLast();
        }
    }
}

function createLootButtons(game_id) {
    let button_remove_last = document.createElement('button');
    button_remove_last.textContent = 'Verwijder laatste buit';
    button_remove_last.onclick = removeLoot;
    button_remove_last.id = 'button_remove_loot';

    let button_save_loot = document.createElement('button');
    button_save_loot.textContent = 'Sla buit op';
    button_save_loot.onclick = () => saveLoot(game_id);
    button_save_loot.id = 'button_save_loot';

    let inputField = document.createElement('input');
    inputField.type = 'text';
    inputField.placeholder = 'Voer hier de buit naam in...';
    inputField.id = 'input_loot_name';

    mapBox.appendChild(button_remove_last);
    mapBox.appendChild(button_save_loot);
    mapBox.appendChild(inputField);

    removeLootButton = document.querySelector('#button_remove_loot');
    saveLootButton = document.querySelector('#button_save_loot');
    lootNameInput = document.querySelector('#input_loot_name');

    saveLootButton.disabled = true;
    saveLootButton.title = 'Er is minstens 1 buit pin nodig voordat de buit opgeslagen kan worden.';

    tab_2.style.background = 'white';
    tab_2.style.color = '#888';
}

function addLoot(e) {
    if (!lootNameInput.value || lootNameInput.value.trim() === '') {
        if (mapBox.children.length > 4) {
            return;
        }
        let errorMsg = document.createElement('p');
        errorMsg.style.color = 'red';
        errorMsg.textContent = 'Vul a.u.b. een naam in voor de buit.';
        mapBox.appendChild(errorMsg);

        setTimeout(() => {
            if (mapBox.children.length > 4) {
                mapBox.removeChild(errorMsg);
            }
        }, 5000);
        return;
    }

    let contains = false;
    lootLatLngs.forEach(latlng => {
        if (latlng.equals(e.latlng)) {
            contains = true;
        }
    });
    if (contains) {
        return;
    }
    let newMarker = L.marker(e.latlng, {icon: lootIcon})
        .bindPopup(L.popup({maxWidth: maxPopupWidth})
            .setContent('Buit: ' + lootNameInput.value.trim()))
        .addTo(mymap);
    applyEvents(newMarker);
    loot_markers.push(newMarker);
    lootLatLngs.push(newMarker.getLatLng());

    if (loot_markers.length >= 1) {
        saveLootButton.disabled = false;
        saveLootButton.title = '';
    }

    lootNames.push(lootNameInput.value.trim());
}

function removeAllLoot() {
    loot_markers.forEach(item => {
        mymap.removeLayer(item);
    });
    loot_markers = [];
    lootLatLngs = [];
    lootNames = [];
}

function removeLoot() {
    if (loot_markers.length > 0) {
        mymap.removeLayer(loot_markers[loot_markers.length - 1]);
        loot_markers.pop();
        lootLatLngs.pop();
    }

    if (loot_markers.length < 1) {
        saveLootButton.disabled = true;
        saveLootButton.title = 'Er is minstens 1 buit pin nodig voordat de buit opgeslagen kan worden.';
    }

    if (lootNames.length > 0) {
        lootNames.pop();
    }
}

async function saveLoot(id) {
    if (loot_markers.length < 1) {
        return;
    }
    $.ajaxSetup({
        headers: {
            'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
        }
    });
    let lats = [];
    let lngs = [];
    lootLatLngs.forEach(latLng => {
        lats.push(latLng.lat);
        lngs.push(latLng.lng);
    });

    await $.ajax({
        url: '/games/' + id + '/loot',
        type: 'POST',
        data: {lats: lats, lngs: lngs, names: lootNames},
        success: function (data) {
            if (!createButtons)
                saveLootButton = null;
            if (saveLootButton != null) {
                mymap.off('click');
                mapBox.removeChild(saveLootButton);
                mapBox.removeChild(removeLootButton);
                mapBox.removeChild(lootNameInput);
                createPoliceStationButton(id);
                mymap.on('click', addPoliceStation);
            }
        },
        error: function (err) {
            console.log(err);
        },
    });
}

function applyExistingLoot(lat, lng, loot_name) {
    let latlng = L.latLng(lat, lng);
    let newMarker = L.marker(latlng, {icon: lootIcon})
        .bindPopup(L.popup({maxWidth: maxPopupWidth})
            .setContent('Buit: ' + loot_name))
        .addTo(mymap);
    applyEvents(newMarker);
    loot_markers.push(newMarker);
    lootLatLngs.push(newMarker.getLatLng());
    lootNames.push(loot_name);
}

function checkLootState(game_id) {
    window.history.pushState({}, document.title, '/games/' + game_id);
    if (loot_markers.length > 0) {
        if (saveLootButton != null) {
            mymap.off('click');
            mapBox.removeChild(saveLootButton);
            mapBox.removeChild(removeLootButton);
            mapBox.removeChild(lootNameInput);
            createPoliceStationButton(game_id);
            mymap.on('click', addPoliceStation);
        }
    }
}

function createPoliceStationButton(game_id) {
    let button_remove = document.createElement('button');
    button_remove.textContent = 'Verwijder politiebureau';
    button_remove.onclick = removePoliceStation;
    button_remove.id = 'button_remove_police_station';

    let button_save = document.createElement('button');
    button_save.textContent = 'Sla politiebureau op';
    button_save.onclick = () => savePoliceStation(game_id);
    button_save.id = 'button_save_police_station';

    mapBox.appendChild(button_remove);
    mapBox.appendChild(button_save);

    removePoliceStationButton = document.querySelector('#button_remove_police_station');
    savePoliceStationButton = document.querySelector('#button_save_police_station');

    savePoliceStationButton.disabled = true;
    savePoliceStationButton.title = 'Plaats eerst een politiebureau op de kaart.';

    tab_3.style.background = 'white';
    tab_3.style.color = '#888';
}

function addPoliceStation(e) {
    if (policeStationMarker !== null) {
        return;
    }

    let newMarker = L.marker(e.latlng, {icon: policeStationIcon})
        .bindPopup(L.popup({maxWidth: maxPopupWidth})
            .setContent('Politiebureau'))
        .addTo(mymap);
    applyEvents(newMarker);
    policeStationMarker = newMarker;
    policeStationLatLng = newMarker.getLatLng();

    if (policeStationMarker !== null) {
        savePoliceStationButton.disabled = false;
        savePoliceStationButton.title = '';
    }
}

function removePoliceStation() {
    if (policeStationMarker !== null) {
        mymap.removeLayer(policeStationMarker);
        policeStationMarker = null;
        policeStationLatLng = null;
    }

    if (savePoliceStationButton != null) {
        savePoliceStationButton.disabled = true;
        savePoliceStationButton.title = 'Plaats eerst een politiebureau op de kaart.';
    }
}

async function savePoliceStation(id) {
    if (policeStationMarker === null) {
        return;
    }
    $.ajaxSetup({
        headers: {
            'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
        }
    });

    await $.ajax({
        url: '/games/' + id + '/police-station',
        type: 'PATCH',
        data: {lat: policeStationLatLng.lat, lng: policeStationLatLng.lng},
        success: function (data) {
            mymap.off('click');
            if (savePoliceStationButton != null) {
                mapBox.removeChild(savePoliceStationButton);
                mapBox.removeChild(removePoliceStationButton);
            }
        },
        error: function (err) {
            console.log(err);
        },
    });
}

function applyExistingPoliceStation(lat, lng) {
    let latlng = L.latLng(lat, lng);
    let newMarker = L.marker(latlng, {icon: policeStationIcon})
        .bindPopup(L.popup({maxWidth: maxPopupWidth})
            .setContent('Politiebureau'))
        .addTo(mymap);
    applyEvents(newMarker);
    policeStationMarker = newMarker;
    policeStationLatLng = newMarker.getLatLng();

    if (policeStationMarker !== null) {
        mymap.off('click');
        if (!createButtons)
            savePoliceStationButton = null;
        if (savePoliceStationButton != null) {
            mapBox.removeChild(savePoliceStationButton);
            mapBox.removeChild(removePoliceStationButton);
        }
    }
}

function applyEvents(marker) {
    marker.on('mouseover', function (e) {
        this.openPopup();
    });
    marker.on('mouseout', function (e) {
        this.closePopup();
    });
}

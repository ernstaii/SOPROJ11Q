const mapCenter = [51.950180, 5.235682];
const maxPopupWidth = 400;
const zoomLevel = 9;
const minZoomLevel = 9;
const corner1 = L.latLng(53.828464, 2.871753),
    corner2 = L.latLng(50.559772, 7.521491),
    mapBounds = L.latLngBounds(corner1, corner2);
const mapBox = document.querySelector('.mapbox');
const timerElmt = document.querySelector('.timer');
const remove_loot_button = document.querySelector('#remove_loot_button');
const lootNameInput = document.querySelector('#loot_name_input');
const sideBarItem2 = document.querySelector('#side_bar_link2');
const sideBarItem3 = document.querySelector('#side_bar_link3');
const left_column = document.querySelector('.left-column');
const right_column = document.querySelector('.right-column');

let markerLatLngs = [];
let markers = [];
let lines = [];
let userMarkers = [];
let lootMarkers = [];
let lootLatLngs = [];
let lootIds = [];

let selectedLootId = -1;
let gameId = -1;
let gameName = "";

const lootIcon = new L.Icon({
    iconUrl: 'https://raw.githubusercontent.com/pointhi/leaflet-color-markers/master/img/marker-icon-gold.png',
    shadowUrl: 'https://cdnjs.cloudflare.com/ajax/libs/leaflet/0.7.7/images/marker-shadow.png',
    iconSize: [25, 41],
    iconAnchor: [12, 41],
    popupAnchor: [1, -34],
    shadowSize: [41, 41]
});

const thiefIcon = new L.Icon({
    iconUrl: 'https://raw.githubusercontent.com/pointhi/leaflet-color-markers/master/img/marker-icon-black.png',
    shadowUrl: 'https://cdnjs.cloudflare.com/ajax/libs/leaflet/0.7.7/images/marker-shadow.png',
    iconSize: [25, 41],
    iconAnchor: [12, 41],
    popupAnchor: [1, -34],
    shadowSize: [41, 41]
});

const agentIcon = new L.Icon({
    iconUrl: 'https://raw.githubusercontent.com/pointhi/leaflet-color-markers/master/img/marker-icon-blue.png',
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

const policeStationIcon = new L.Icon({
    iconUrl: 'https://raw.githubusercontent.com/pointhi/leaflet-color-markers/master/img/marker-icon-red.png',
    shadowUrl: 'https://cdnjs.cloudflare.com/ajax/libs/leaflet/0.7.7/images/marker-shadow.png',
    iconSize: [25, 41],
    iconAnchor: [12, 41],
    popupAnchor: [1, -34],
    shadowSize: [41, 41]
});

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
    const tiles = L.tileLayer(tileURL, { attribution });

    tiles.addTo(mymap);
    mymap.on('click', addLoot);
}

function setGameDetails(game_id, game_name) {
    gameId = game_id;
    gameName = game_name;
}

function applyLootMarker(lat, lng, loot_name, loot_id) {
    let latlng = L.latLng(lat, lng);
    let newMarker = L.marker(latlng, {icon: lootIcon})
        .bindPopup(L.popup({ maxWidth: maxPopupWidth })
            .setContent('Buit: ' + loot_name))
        .addTo(mymap);
    newMarker.on('click', function (e) {
        selectedLootId = loot_id;
        remove_loot_button.textContent = 'Verwijder buit: ' + loot_name;
        remove_loot_button.disabled = false;
    });
    applyEvents(newMarker);
    lootMarkers.push(newMarker);
    lootLatLngs.push(newMarker.getLatLng());
    lootIds.push(parseInt(loot_id));
}

function addLoot(e) {
    if (!lootNameInput.value || lootNameInput.value.trim() === '') {
        if (mapBox.children.length > 3) {
            return;
        }
        let errorMsg = document.createElement('p');
        errorMsg.style.color = 'red';
        errorMsg.textContent = 'Vul a.u.b. een naam in voor de buit.';
        mapBox.appendChild(errorMsg);

        setTimeout(() => {
            if (mapBox.children.length > 3) {
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
    let newMarker = L.marker(e.latlng, { icon: lootIcon })
        .bindPopup(L.popup({ maxWidth: maxPopupWidth})
            .setContent('Buit: ' + lootNameInput.value.trim()))
        .addTo(mymap);
    applyEvents(newMarker);
    lootMarkers.push(newMarker);
    lootLatLngs.push(newMarker.getLatLng());
    saveLoot(gameId, lootNameInput.value.trim());
}

async function saveLoot(game_id, loot_name) {
    if (lootMarkers.length < 1) {
        return;
    }
    $.ajaxSetup({
        headers: {
            'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
        }
    });
    let lats = [];
    let lngs = [];
    lats.push(lootLatLngs[lootLatLngs.length - 1].lat);
    lngs.push(lootLatLngs[lootLatLngs.length - 1].lng);

    let lootNamesArray = [];
    lootNamesArray.push(loot_name);

    await $.ajax({
        url: '/games/' + game_id + '/loot',
        type: 'POST',
        data: { lats: lats, lngs: lngs, names: lootNamesArray },
        success: function (data) {
            lootIds.push(parseInt(data[0].id));
            lootMarkers[lootMarkers.length - 1].on('click', function (e) {
                selectedLootId = parseInt(data[0].id);
                remove_loot_button.textContent = 'Verwijder buit: ' + loot_name;
                remove_loot_button.disabled = false;
            });
        },
        error: function (err) {
            console.log(err);
        },
    });
}

async function deletePrompt(loot_id) {
    if (loot_id === -1) {
        return;
    }

    $.ajaxSetup({
        headers: {
            'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
        }
    });

    await $.ajax({
        url: '/api/loot/' + loot_id,
        type: 'DELETE',
        data: {},
        success: function (data) {
            let index = lootIds.indexOf(parseInt(loot_id));
            mymap.removeLayer(lootMarkers[index]);
            lootMarkers.splice(index, 1);
            lootLatLngs.splice(index, 1);
            lootIds.splice(index, 1);
            selectedLootId = -1;
            remove_loot_button.textContent = 'Selecteer a.u.b. een buit';
            remove_loot_button.disabled = true;
        },
        error: function (err) {
            console.log(err);
        },
    });
}

function applyUserMarker(lat, lng, name, role) {
    let latlng = L.latLng(lat, lng);
    let newMarker;

    if (role === 'thief') {
        newMarker = L.marker(latlng, {icon: thiefIcon})
            .bindPopup(L.popup({ maxWidth: maxPopupWidth })
                .setContent('Boef: ' + name))
            .addTo(mymap);
    } else {
        newMarker = L.marker(latlng, {icon: agentIcon})
            .bindPopup(L.popup({ maxWidth: maxPopupWidth })
                .setContent('Agent: ' + name))
            .addTo(mymap);
    }

    applyEvents(newMarker);
    userMarkers.push(newMarker);
}

function updateUserPinsOnChange(interval, status, game_id) {
    if (status === 'on-going') {
        setTimeout(() => {
            setInterval(() => {
                getLatestUserLocations(game_id);
                getLatestLoot(game_id);
            }, (interval * 1000));
        }, 5000);
    }
}

async function getLatestUserLocations(game_id) {
    $.ajaxSetup({
        headers: {
            'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
        }
    });

    await $.ajax({
        url: '/api/games/' + game_id + '/users-with-role',
        type: 'GET',
        data: {},
        success: function (data) {
            let originalLastIndex = (userMarkers.length - 1);
            for (let i = originalLastIndex; i >= 0; i--) {
                mymap.removeLayer(userMarkers[i]);
                userMarkers.pop();
            }

            data.forEach(user => {
                applyUserMarker(Number(user.location.split(',')[0]), Number(user.location.split(',')[1]), user.username, user.role);
                if (!gadgetBoxExists(user.id)) {
                    user_ids.push(user.id);
                    if (user.role === 'police') {
                        let userBox = createGenericUserBox(user.username, user.id);
                        userBox.children[1].appendChild(createGadgetButtons('alarm', 'Alarm', user.id));
                        userBox.children[1].appendChild(createGadgetButtons('drone', 'Drone', user.id));
                        left_column.appendChild(userBox);
                    }
                    else {
                        let userBox = createGenericUserBox(user.username, user.id);
                        userBox.children[1].appendChild(createGadgetButtons('smokescreen', 'Rookgordijn', user.id));
                        right_column.appendChild(userBox);
                    }
                }
            });
        },
        error: function (err) {
            console.log(err);
        },
    });
}

function gadgetBoxExists(userId) {
    let userWithId = document.querySelector('#user_' + userId);
    return !!userWithId;
}

function createGenericUserBox(username, userId) {
    let user_box = document.createElement('div');
    user_box.className = 'user-box';

    let user_box_name = document.createElement('span');
    user_box_name.className = 'user-box-name';
    user_box_name.id = 'user_' + userId;
    user_box_name.textContent = username;

    let user_box_buttons_box = document.createElement('div');
    user_box_buttons_box.className = 'user-box-buttons-box';

    user_box.appendChild(user_box_name);
    user_box.appendChild(user_box_buttons_box);
    return user_box;
}

function createGadgetButtons(labelId, gadgetName, userId) {
    let user_box_buttons_divider = document.createElement('div');
    user_box_buttons_divider.className = 'user-box-buttons-divider';

    let gadgetLabel = document.createElement('label');
    gadgetLabel.id = labelId;
    gadgetLabel.textContent = gadgetName;

    let amountOfGadgetsLabel = document.createElement('label');
    amountOfGadgetsLabel.id = 'amount_of_' + labelId + 's_' + userId;
    amountOfGadgetsLabel.textContent = '0';

    let addButton = document.createElement('a');
    addButton.className = 'user-box-button add-button';
    addButton.id = 'add_' + labelId + '_button';
    addButton.onclick = function() { manageGadget(labelId, 'add', userId) };
    addButton.textContent = '+';

    let removeButton = document.createElement('a');
    removeButton.className = 'user-box-button remove-button';
    removeButton.id = 'remove_' + labelId + '_button';
    removeButton.onclick = function() { manageGadget(labelId, 'remove', userId) };
    removeButton.textContent = '─';

    user_box_buttons_divider.appendChild(gadgetLabel);
    user_box_buttons_divider.appendChild(amountOfGadgetsLabel);
    user_box_buttons_divider.appendChild(addButton);
    user_box_buttons_divider.appendChild(removeButton);
    return user_box_buttons_divider;
}

async function getLatestLoot(game_id) {
    $.ajaxSetup({
        headers: {
            'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
        }
    });

    await $.ajax({
        url: '/api/games/' + game_id + '/loot',
        type: 'GET',
        data: {},
        success: function (data) {
            let originalLastIndex = (lootMarkers.length - 1);
            for (let i = originalLastIndex; i >= 0; i--) {
                mymap.removeLayer(lootMarkers[i]);
                lootMarkers.pop();
                lootLatLngs.pop();
                lootIds.pop();
            }

            data.forEach(loot => {
                applyLootMarker(Number(loot.location.split(',')[0]), Number(loot.location.split(',')[1]), loot.name, loot.id);
            });
        },
        error: function (err) {
            console.log(err);
        },
    });
}

function applyExistingMarker(lat, lng) {
    let latlng = L.latLng(lat, lng);
    let newMarker = L.marker(latlng, {icon: borderIcon})
        .bindPopup(L.popup({ maxWidth: maxPopupWidth })
            .setContent('Grens-pin ' + (markers.length + 1)))
        .addTo(mymap);
    applyEvents(newMarker);
    markers.push(newMarker);
    markerLatLngs.push(newMarker.getLatLng());
}

function drawLinesForExistingMarkers() {
    for(let i = 0; i < markerLatLngs.length - 1; i++) {
        if (i < markerLatLngs.length - 2) {
            lines.push(L.polyline(markerLatLngs, {color: 'black', dashArray: '30, 30', dashOffset: '0'}).addTo(mymap));
        }
        else {
            addNewLineBetweenFirstAndLast();
        }
    }
    if (markerLatLngs.length > 0) {
        fitMapToLocation();
    }
}

function fitMapToLocation() {
    let fieldBounds = new L.LatLngBounds(markerLatLngs);
    mymap.setMaxBounds(fieldBounds);
    mymap.fitBounds(fieldBounds);
    setTimeout(() => {
        mymap.options.minZoom = mymap.getZoom();
    }, 400);
}

function addNewLineBetweenFirstAndLast() {
    let firstLastMarkerLatLngs = [];
    firstLastMarkerLatLngs.push(markerLatLngs[0], markerLatLngs[markerLatLngs.length - 1]);
    lines.push(L.polyline(firstLastMarkerLatLngs, {color: 'black', dashArray: '30, 30', dashOffset: '0'}).addTo(mymap));
}

function applyEvents(marker) {
    marker.on('mouseover', function (e) {
        this.openPopup();
    });
    marker.on('mouseout', function (e) {
        this.closePopup();
    });
}

function handleTimerElement(status, time_left, duration) {
    let seconds = (duration * 60) - time_left;
    timerElmt.textContent = new Date(seconds * 1000).toISOString().substr(11, 8);
    if (status === 'on-going') {
        setInterval(() => {
            seconds++;
            timerElmt.textContent = new Date(seconds * 1000).toISOString().substr(11, 8);
        }, 1000);
    }
}

function applyExistingPoliceStation(lat, lng) {
    let latlng = L.latLng(lat, lng);
    let newMarker = L.marker(latlng, { icon: policeStationIcon })
        .bindPopup(L.popup({ maxWidth: maxPopupWidth })
            .setContent('Politiebureau'))
        .addTo(mymap);
    applyEvents(newMarker);
}


function callGameDetails(game_id) {
    getGameNotifications(game_id);
    setInterval(() => {
        getGameDetails(game_id);
        getGameNotifications(game_id);
    }, 5000);
    sideBarItem2.href = '/games/' + gameName;
    sideBarItem3.href = '/games/' + gameName;
}

async function getGameDetails(game_id) {
    $.ajaxSetup({
        headers: {
            'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
        }
    });

    await $.ajax({
        url: '/api/games/' + game_id,
        type: 'GET',
        data: {},
        success: function (data) {
            let thieves_score = document.querySelector('#score_1');
            let police_score = document.querySelector('#score_2');
            thieves_score.textContent = 'Boeven score: ' + data.thieves_score;
            police_score.textContent = 'Politie score: ' + data.police_score;
        },
        error: function (err) {
            console.log(err);
        },
    });
}

async function getGameNotifications(game_id) {
    $.ajaxSetup({
        headers: {
            'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
        }
    });

    await $.ajax({
        url: '/api/games/' + game_id + '/notifications?all=true',
        type: 'GET',
        data: {},
        success: function (data) {
            let messages_div = document.querySelector('.messages');
            messages_div.innerHTML = '';
            data.forEach(notification => {
                let message = document.createElement('p');

                message.textContent = new Date(notification.created_at).toLocaleString() + ': ' + notification.message;
                messages_div.appendChild(message);
            });
        },
        error: function (err) {
            console.log(err);
        },
    });
}

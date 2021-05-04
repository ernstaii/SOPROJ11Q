const mapCenter = [51.950180, 5.235682];
const maxPopupWidth = 400;
const zoomLevel = 9;
const minZoomLevel = 9;
const corner1 = L.latLng(53.828464, 2.871753),
    corner2 = L.latLng(50.559772, 7.521491),
    mapBounds = L.latLngBounds(corner1, corner2);
const mapBox = document.querySelector('.mapbox');
const timerElmt = document.querySelector('.timer');

let markerLatLngs = [];
let markers = [];
let lines = [];
let userMarkers = [];
let lootMarkers = [];
let totalSeconds = 0;

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

setTimeout(() => {
    mymap.invalidateSize(true);
}, 0);

let mymap;
initMap();

function initMap() {
    mymap = L.map(document.getElementById('map')).setView(mapCenter, zoomLevel);
    mymap.options.minZoom = minZoomLevel;
    mymap.setMaxBounds(mapBounds);
    const attribution = 'Map data &copy; <a href="https://www.openstreetmap.org/">OpenStreetMap</a> contributors, <a href="https://creativecommons.org/licenses/by-sa/2.0/">CC-BY-SA</a>, Imagery © <a href="https://www.mapbox.com/">Mapbox</a>';
    const tileURL = 'https://a.tile.openstreetmap.org/{z}/{x}/{y}.png';
    const tiles = L.tileLayer(tileURL, { attribution });

    tiles.addTo(mymap);
}

function applyLootMarker(lat, lng, loot_name) {
    let latlng = L.latLng(lat, lng);
    let newMarker = L.marker(latlng, {icon: lootIcon})
        .bindPopup(L.popup({ maxWidth: maxPopupWidth })
            .setContent('Buit: ' + loot_name))
        .addTo(mymap);
    applyEvents(newMarker);
    lootMarkers.push(newMarker);
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
            .setContent('Border marker ' + (markers.length + 1)))
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

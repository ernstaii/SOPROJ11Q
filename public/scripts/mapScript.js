const mapCenter = [51.950180, 5.235682];
const maxPopupWidth = 400;
const zoomLevel = 9;
const minZoomLevel = 9;
const corner1 = L.latLng(53.828464, 2.871753),
    corner2 = L.latLng(50.559772, 7.521491),
    mapBounds = L.latLngBounds(corner1, corner2);
const removeMarkerButton = document.querySelector('#button_remove_markers');
const saveMarkerButton = document.querySelector('#button_save_markers');
const mapBox = document.querySelector('.mapbox');

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

let markerLatLngs = [];
let markers = [];
let lines = [];

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
    let newMarker = L.marker(e.latlng, { icon: borderIcon })
        .bindPopup(L.popup({ maxWidth: maxPopupWidth})
            .setContent('Locatie marker ' + (markers.length + 1)))
        .addTo(mymap);
    markers.push(newMarker);
    markerLatLngs.push(newMarker.getLatLng());
    if (lines.length > 1) {
        mymap.removeLayer(lines[lines.length - 1]);
        lines.pop();
    }
    if (markers.length > 1) {
        lines.push(L.polyline(markerLatLngs, {color: 'red'}).addTo(mymap));
    }
    if (markers.length > 2) {
        addNewLineBetweenFirstAndLast();
    }

    if (markers.length >= 3) {
        saveMarkerButton.disabled = false;
        saveMarkerButton.title = '';
    }
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
        }
        else {
            mymap.removeLayer(lines[lines.length - 1]);
            lines.pop();
        }
    }
    if (markers.length > 2) {
        addNewLineBetweenFirstAndLast();
    }

    if (markers.length < 3) {
        saveMarkerButton.disabled = true;
        saveMarkerButton.title = 'Er zijn minstens 3 markers nodig voordat het veld opgeslagen kan worden.';
    }
}

function addNewLineBetweenFirstAndLast() {
    let firstLastMarkerLatLngs = [];
    firstLastMarkerLatLngs.push(markerLatLngs[0], markerLatLngs[markerLatLngs.length - 1]);
    lines.push(L.polyline(firstLastMarkerLatLngs, {color: 'red'}).addTo(mymap));
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
        data: { lats: lats, lngs: lngs },
        success: function (data) {
            mymap.off('click');
            mapBox.removeChild(saveMarkerButton);
            mapBox.removeChild(removeMarkerButton);
        },
        error: function (err) {
            console.log(err);
        },
    });
}

function applyExistingMarker(lat, lng) {
    let latlng = L.latLng(lat, lng);
    let newMarker = L.marker(latlng, { icon: borderIcon })
        .bindPopup(L.popup({ maxWidth: maxPopupWidth })
            .setContent('Locatie marker ' + (markers.length + 1)))
        .addTo(mymap);
    markers.push(newMarker);
    markerLatLngs.push(newMarker.getLatLng());
}

function drawLinesForExistingMarkers() {
    if (markers.length > 0) {
        mymap.off('click');
        mapBox.removeChild(saveMarkerButton);
        mapBox.removeChild(removeMarkerButton);
    }
    for(let i = 0; i < markerLatLngs.length - 1; i++) {
        if (i < markerLatLngs.length - 2) {
            lines.push(L.polyline(markerLatLngs, {color: 'red'}).addTo(mymap));
        }
        else {
            addNewLineBetweenFirstAndLast();
        }
    }
}

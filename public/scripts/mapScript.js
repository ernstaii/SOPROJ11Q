const mapCenter = [51.560596, 5.0919143];
const maxPopupWidth = 400;
const zoomLevel = 9;
const minZoomLevel = 9;
const corner1 = L.latLng(53.828464, 2.871753),
    corner2 = L.latLng(50.696721, 9.188892),
    mapBounds = L.latLngBounds(corner1, corner2);
let markerLatLngs = [];
let markers = [];
let lines = [];

setTimeout(() => {
    mymap.invalidateSize(true);
}, 200);

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
    mymap.on('click', addMarker)
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
    let newMarker = L.marker(e.latlng)
        .bindPopup(L.popup({ maxWidth: maxPopupWidth })
            .setContent(`text in popup window!`))
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
}

function addNewLineBetweenFirstAndLast() {
    let firstLastMarkerLatLngs = [];
    firstLastMarkerLatLngs.push(markerLatLngs[0], markerLatLngs[markerLatLngs.length - 1]);
    lines.push(L.polyline(firstLastMarkerLatLngs, {color: 'red'}).addTo(mymap));
}

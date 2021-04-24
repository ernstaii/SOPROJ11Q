const mapCenter = [51.560596, 5.0919143];
const maxPopupWidth = 400;
const zoomLevel = 9;
const minZoomLevel = 9;
const corner1 = L.latLng(53.828464, 2.871753),
    corner2 = L.latLng(50.696721, 9.188892),
    mapBounds = L.latLngBounds(corner1, corner2);

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
}

function addMarker(lat, lon) {
    let marker = L.marker(lat, lon)
        .bindPopup(L.popup({ maxWidth: maxPopupWidth })
            .setContent(`text in popup window!`))
        .addTo(mymap);
}


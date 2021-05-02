const mapCenter = [51.560596, 5.0919143];
const maxPopupWidth = 400;
const zoomLevel = 9;
const minZoomLevel = 9;
const corner1 = L.latLng(53.828464, 2.871753),
    corner2 = L.latLng(50.696721, 9.188892),
    mapBounds = L.latLngBounds(corner1, corner2);
const mapBox = document.querySelector('.mapbox');

let markerLatLngs = [];
let markers = [];
let lines = [];

let lootIcon = new L.Icon({
    iconUrl: 'https://raw.githubusercontent.com/pointhi/leaflet-color-markers/master/img/marker-icon-gold.png',
    shadowUrl: 'https://cdnjs.cloudflare.com/ajax/libs/leaflet/0.7.7/images/marker-shadow.png',
    iconSize: [25, 41],
    iconAnchor: [12, 41],
    popupAnchor: [1, -34],
    shadowSize: [41, 41]
});

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

function applyLootMarker(lat, lng, loot_name) {
    let latlng = L.latLng(lat, lng);
    let newMarker = L.marker(latlng, {icon: lootIcon})
        .bindPopup(L.popup({ maxWidth: maxPopupWidth })
            .setContent('Buit: ' + loot_name))
        .addTo(mymap);
    markers.push(newMarker);
    markerLatLngs.push(newMarker.getLatLng());
}

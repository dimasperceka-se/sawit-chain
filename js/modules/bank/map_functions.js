

function clear_map() {
    $('#map_canvas').gmap3({clear: {}});
    bounds = new google.maps.LatLngBounds();
}
function setMapSize() {
    if (screenfull.isFullscreen) {
        height = screen.height;
    } else {
        height = window.innerHeight - 100;
    }
    $map_canvas.css('height', height);
}
function fixMap(map) {
    if (!map) {
        map = $('#map_canvas').gmap3('get');
    }
    var center = map.getCenter();
    google.maps.event.trigger(map, 'resize');
    map.setCenter(center);
}
function destroyMap () {
    $map_canvas.gmap3({
        clear: {
            name: ['marker', 'line', 'polyline', 'polygon']
        }
    })
    $map_canvas.gmap3('destroy');
}
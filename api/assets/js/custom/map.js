function fixMap(map)
{
    var center = map.getCenter();
    google.maps.event.trigger(map, 'resize');
    map.setCenter(center);
}
<?php
/**
 * @Author: nikolius
 * @Date:   2017-07-28 13:42:40
 */
// echo "<pre>";
// print_r($area);
// die;
?>
<html lang="en">
    <head>
        <script type="text/javascript" src="<?php echo base_url(); ?>assets/js/libs/markerwithlabel.js"></script>
        <script type="text/javascript" src="<?php echo base_url(); ?>assets/js/libs/gmap3.js"></script>
    </head>
    <body>
        <div class="row-fluid">
            <div class="span12">
                <div id="map_canvas" class="gmap3" style="height:550px;"></div>
            </div>

            <script type="text/javascript">

                function show_polygon2 () {
                    var areas = <?php echo $area; ?>;
                    var cek = 0;
                    $.each(areas, function(index, val) {
                        addMarker(val[1], val[0]);
                        cek++;
                    });
                }

                function addMarker(lat, lng){
                    var mark = new Array();
                    var id = markers.length;
                    if (!lat || !lng) {
                        var lat = map.getCenter().lat();
                        var lng = map.getCenter().lng();
                    }
                    mark["lat"] = lat;
                    mark["lng"] = lng;

                    markers[id] = mark;

                    $("#map_canvas").gmap3({
                        marker: {
                            latLng: [lat, lng],
                            id: id+1,
                            options: {
                                draggable: false
                            }
                        }
                    });
                    makePoly();
                };

                function makePoly(){
                    var tmp_marker = new Array();

                    $('#markers-text').html('');
                    $.each(markers, function(i){
                        if (markers[i]) {
                            tmp_marker.push([markers[i].lat, markers[i].lng]);
                            $('#markers-text').append('['+markers[i].lat + ',' + markers[i].lng + '],<br/>');
                        }
                    });

                    $("#map_canvas").gmap3({
                        clear: {
                            name: "polyline"
                        }
                    }).gmap3({
                        clear: {
                            name: "polygon"
                        }
                    });

                    if (tmp_marker.length == 2) {
                        $("#map_canvas").gmap3({
                            polyline: {
                                options: {
                                    strokeColor: "#FF0000",
                                    strokeOpacity: 1.0,
                                    strokeWeight: 2,
                                    path: tmp_marker
                                }
                            }
                        })
                    } else if (tmp_marker.length > 2) {
                        tmp_marker.push(tmp_marker[0]);
                        $("#map_canvas").gmap3({
                            polygon: {
                                options: {
                                    strokeColor: "#FF0000",
                                    strokeOpacity: 0.8,
                                    strokeWeight: 2,
                                    fillColor: "#FF0000",
                                    fillOpacity: 0.35,
                                    paths: tmp_marker
                                }
                            }
                        })
                    }
                }

                function fixMap(map)
                {
                    var center = map.getCenter();
                    google.maps.event.trigger(map, 'resize');
                    map.setCenter(center);
                }

                var earthRadiusMeters   = 6367460.0;
                var metersPerDegree     = 2.0*Math.PI*earthRadiusMeters/360.0;
                var radiansPerDegree    = Math.PI/180.0;

                var area_bounds = null;
                area_bounds = new google.maps.LatLngBounds();

                var map;
                var markers = new Array();
                var mode = 'normal';

                $(function() {
                    $("#map_canvas").gmap3({
                        map: {
                            options: {
                                center: [<?php echo $centerLatLong[0]?>,<?php echo $centerLatLong[1]?>],
                                zoom: 16
                            }
                        }
                    });

                    map = $("#map_canvas").gmap3({get: {name: "map"}});

                    show_polygon2();

                    setTimeout(function(){
                        map = $('#map_canvas').gmap3('get');
                        fixMap(map);
                    }, 10);
                });

            </script>
        </div>
    </body>
</html>
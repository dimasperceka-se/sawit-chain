<?php
if($hakAksesPolygon == "true"){
    $hakAksesPolygonStyle = "display:none;";
}else{
    $hakAksesPolygonStyle = "";
}
?>
<!DOCTYPE html>
<html lang="en">
    <head>
        <script type="text/javascript" src="<?php echo base_url(); ?>assets/js/libs/markerwithlabel.js"></script>
        <script type="text/javascript" src="<?php echo base_url(); ?>assets/js/libs/gmap3.js"></script>
    </head>
    <body>
            <div class="row-fluid">
                <div class="span12">
                    <div id="map_canvas_nursery_idcoop" class="gmap3" style="width: 100%; min-height:460px"></div>
                </div>
            </div>
            <input  style="display:none;float:left; height:37px;margin-right: 5px;" type="text" class="" id="luas_nursery_idcoop" placeholder="Area (Ha)" size="10" value="<?php echo $luas?>" readonly>
            <button style="display:none" type="button" class="btn" data-toggle="button" id="button-add-marker_nursery_idcoop">Add Marker</button>
            <button style="display:none; color: #fff;background-color: #d9534f;border-color: #d43f3a;" type="button" class="btn btn-danger" id="clear-marker_nursery_idcoop">Clear All Marker</button>
            <button style="display:none; color: #fff;background-color: #f0ad4e;border-color: #eea236;"                     type="button" class="btn" id="delete-marker_nursery_idcoop"  data-id="" ></button>
            <button style="display:none; color: #fff;background-color: #337ab7;border-color: #2e6da4;"  type="button" class="btn" id="save-polygon_nursery_idcoop">Save Polygon</button>
            <button style="display:none; color: #fff;background-color: #403b3b;border-color: #3a3636;" type="button" class="btn" id="cancel-polygon_nursery_idcoop">Cancel</button>
            <button type="button" style="<?php echo $hakAksesPolygonStyle?>" class="btn btn-primary" id="edit-polygon_nursery_idcoop">Edit Polygon</button>

            <script type="text/javascript">
                var earthRadiusMeters   = 6367460.0;
                var metersPerDegree     = 2.0*Math.PI*earthRadiusMeters/360.0;
                var radiansPerDegree    = Math.PI/180.0;
                function PlanarPolygonAreaMeters(points) {
                    var a = 0.0;
                    for(var i = 0;i<points.length;++i) {
                        var j = (i+1)%points.length;
                        var xi  = points[i].lng*metersPerDegree*Math.cos(points[i].lat*radiansPerDegree);
                        var yi  = points[i].lat*metersPerDegree;
                        var xj  = points[j].lng*metersPerDegree*Math.cos(points[j].lat*radiansPerDegree);
                        var yj  = points[j].lat*metersPerDegree;
                        a += xi*yj-xj*yi;
                    }
                    return Math.abs(a/2.0);
                }

                function send_data () {
                    map = $('#map_canvas_nursery_idcoop').gmap3('get');
                    var center = map.getCenter();
                    var lati =  center.lat();
                    var langi = center.lng();
                    map = $("#map_canvas_nursery_idcoop").gmap3({get: {name: "map"}});
                    fixMap(map);
                    $.ajax({
                        url: "<?php echo $nursery_polygon; ?>",
                        type: 'PUT',
                        data: {
                            nursery_id : "<?php echo $NurseryID; ?>",
                            nursery_nr : "<?php echo $NurseryNr; ?>",
                            area: area,
                            lat: lati,
                            long : langi,
                        }
                    })
                    .done(function(result) {

                        if(result.success==true){

                        }else{
                            alert(result.message);
                        }
                        if(result.success=='duplicated'){
                            $('#edit-polygon_nursery_idcoop').click();
                        }
                    })
                    .fail(function() {
                        alert('Error. Please reload page and try again.');// console.log("error");
                    })
                    .always(function() {
                        // console.log("complete");
                    });

                }

                function calc_area () {
                    if(area!=undefined){
                        var point = [];
                        $.each(area, function(index, val) {
                            if(val!=undefined){
                                point.push({'lat':val[0],'lng':val[1]});
                            }
                        });
                        area_hectare = PlanarPolygonAreaMeters(point);
                        area_hectare = area_hectare/10000;
                        var hasil = area_hectare.toFixed(2);
                        $('#luas_nursery_idcoop').val(hasil);
                        return hasil;
                    }
                }

                function calc_area_marker () {
                    if(area!=undefined){
                        var point = [];
                        $.each(markers, function(index, val) {
                            if(val!=undefined){
                                point.push({'lat':val[0],'lng':val[1]});
                            }
                        });
                        area_hectare = PlanarPolygonAreaMeters(point);
                        area_hectare = area_hectare/10000;
                        var hasil = area_hectare.toFixed(2);
                        $('#luas_nursery_idcoop').val(hasil);
                        return hasil;
                    }
                }

                function save_polygon () {

                    area = [];
                    $.each(markers, function(index, val) {
                        if(val!=undefined){
                            area.push([val.lat, val.lng]);
                        }

                    });
                    calc_area();
                    send_data();
                }

                function save_polygon2 () {

                    area = [];
                    $.each(markers, function(index, val) {
                        if(val!=undefined){
                            area.push([val.lat, val.lng]);
                        }

                    });
                    edit_polygon();
                    clearMarker();
                    markers.splice(0, markers.length);
                    //calc_area();
                    //send_data();
                }

                function fixMap(map)
                {
                    var center = map.getCenter();
                    google.maps.event.trigger(map, 'resize');
                    map.setCenter(center);
                }
                function addMarker(lat, lng)
                {
                    var mark = new Array();
                    var id = markers.length;
                    if (!lat || !lng) {
                        var lat = map.getCenter().lat();
                        var lng = map.getCenter().lng();
                    }
                    mark["lat"] = lat;
                    mark["lng"] = lng;

                    markers[id] = mark;

                    $("#map_canvas_nursery_idcoop").gmap3({
                        marker: {
                            latLng: [lat, lng],
                            id: id+1,
                            options: {
                                draggable: true
                            },
                            events: {
                                dragend: function(marker, event, data) {
                                    markers[data.id-1]["lat"] = marker.getPosition().lat();
                                    markers[data.id-1]["lng"] = marker.getPosition().lng();
                                    makePoly();
                                },
                                click: function(marker, event, data) {
                                    var button = $('#delete-marker_nursery_idcoop');
                                    button.text('Delete Marker ' + data.id);
                                    button.data('id', data.id);
                                    button.show();
                                }
                            }
                        }
                    });
                    makePoly();
                };

                function clearMarker(id) {
                    if (id) {
                        delete markers[id-1];
                        $("#map_canvas_nursery_idcoop").gmap3({
                            clear: {
                                name: "marker",
                                id: id
                            }
                        });
                    } else {
                        $("#map_canvas_nursery_idcoop").gmap3({
                             clear: {
                                name: "marker"
                            }
                        });
                        markers = new Array();
                    }
                    makePoly();
                }

                function clearMarkerAfterSave(id) {
                    if (id) {
                        delete markers[id-1];
                        $("#map_canvas_nursery_idcoop").gmap3({
                            clear: {
                                name: "marker",
                                id: id
                            }
                        });
                    } else {
                        $("#map_canvas_nursery_idcoop").gmap3({
                             clear: {
                                name: "marker"
                            }
                        });
                        markers = new Array();
                    }
                }

                function makePoly()
                {
                    var tmp_marker = new Array();
                    $('#markers-text_nursery_idcoop').html('');
                    $.each(markers, function(i){
                        if (markers[i]) {
                            tmp_marker.push([markers[i].lat, markers[i].lng]);
                            $('#markers-text_nursery_idcoop').append('['+markers[i].lat + ',' + markers[i].lng + '],<br/>');
                        }
                    });
                    $("#map_canvas_nursery_idcoop").gmap3({
                        clear: {
                            name: "polyline"
                        }
                    }).gmap3({
                        clear: {
                            name: "polygon"
                        }
                    });
                    if (tmp_marker.length == 2) {
                        // console.log('polyline');
                        $("#map_canvas_nursery_idcoop").gmap3({
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
                        // console.log('polygon');
                        tmp_marker.push(tmp_marker[0]);
                        $("#map_canvas_nursery_idcoop").gmap3({
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

                function clearMarkerByTag (tag) {
                    $("#map_canvas_nursery_idcoop").gmap3({
                        clear: {
                            name: "marker",
                            tag: tag
                        }
                    });
                }

                function hide_polygon () {
                    $("#map_canvas_nursery_idcoop").gmap3({
                        clear: {
                            name: "polyline"
                        }
                    }).gmap3({
                        clear: {
                            name: "polygon"
                        }
                    });
                }

                var area_bounds = null;
                area_bounds = new google.maps.LatLngBounds();

                function show_polygon () {
                    $.each(area, function(index, val) {
                        var myLatLng = new google.maps.LatLng(val[0],val[1]);
                        area_bounds.extend(myLatLng);
                    });

                    $('#map_canvas_nursery_idcoop').gmap3("get").fitBounds(area_bounds);

                    $("#map_canvas_nursery_idcoop").gmap3({
                        polygon: {
                            tag: 'Area',
                            options: {
                                strokeColor: "yellow",
                                strokeOpacity: 0.8,
                                strokeWeight: 2,
                                fillColor: "yellow",
                                fillOpacity: 0.35,
                                paths: area
                            },
                            events: {
                                click: function(polygon, event, data) {
                                    //contentString = 'Area : ' + area_hectare.toFixed(2) + ' ha';

                                    infowindow  = $(this).gmap3({get:{name:"infowindow"}});
                                    if (infowindow){
                                        infowindow.open(map, polygon);
                                        infowindow.setContent(contentString);
                                        infowindow.setPosition(event.latLng);
                                    } else {
                                        $(this).gmap3({
                                            infowindow:{
                                                anchor:polygon,
                                                options:{
                                                    content: contentString,
                                                    position: event.latLng
                                                }
                                            }
                                        });
                                    }
                                }
                            }
                        }
                    });
                }

                function hide_polygon () {
                    $("#map_canvas_nursery_idcoop").gmap3({
                        clear: {
                            name: "polyline"
                        }
                    }).gmap3({
                        clear: {
                            name: "polygon"
                        }
                    });
                }

                function edit_polygon () {
                	var cek = 0;
                    if(area!=undefined){
                        hide_polygon();
                        markers = [];
                        $.each(area, function(index, val) {
                            addMarker(val[0], val[1]);
                            cek++;
                        });
                        if(cek==1){
                        	clearMarker();
	                        markers.splice(0, markers.length);
	                        makePoly();
                        }
                    }

                }

                var map;
                var markers = new Array();
                var mode = 'normal';
                function setMapSize() {
                    var width = $(window).width();
                    var height = $(window).height();
                    // $('#map_canvas_nursery_idcoop').css('width', width * (90 / 100));
                    // $('#map_canvas_nursery_idcoop').css('height', height * (80 / 100));
                }

                function show_polygon2 () {
                        var areas = <?php echo $area; ?>;
                    	var cek = 0;
                        $.each(areas, function(index, val) {
                            addMarker(val[0], val[1]);
                            cek++;
                        });
                        save_polygon2();
                        //update nursery polygon center
                        //alert(cek);
                        if(cek>2){
                        	map = $('#map_canvas_nursery_idcoop').gmap3('get');
	                        var center = map.getCenter();
	                        var lati  = center.lat();
	                        var langi = center.lng();
	                        clearMarkerByTag('area');
	                        clearMarkerAfterSave();
	                        hide_polygon();
	                        show_polygon();
                        	$.ajax({
	                            url: "<?php echo $nursery_polygon_center; ?>",
	                            type: 'PUT',
	                            data: {
	                                nursery_id : "<?php echo $NurseryID; ?>",
	                                nursery_nr : "<?php echo $NurseryNr; ?>",
	                                lat: lati,
	                                long : langi,
	                            }
	                        })
	                        .done(function(result) {
	                        })
	                        .fail(function() {
	                        })
	                        .always(function() {
	                        });
                        }


                }
                 //center: [-0.312180, 113.757509],

                $(function() {
                    // $(window).resize(setMapSize());
                    //alert(area);
                    $("#map_canvas_nursery_idcoop").gmap3({
                        map: {
                            options: {
                                center: [<?php if($latitude!=''){ echo $latitude;}else{ echo -1.2674336; }?>, <?php if($longitude!=''){ echo $longitude;}else{ echo 113.6939433; }?>],
                                zoom: <?php if($latitude!=''){ echo 9;}else{ echo 5; }?>
                            },
                            events: {
                                click: function(map, event, data){
                                    if (mode == 'add') {
                                        // add marker here
                                        // console.log(map);
                                        // console.log(event.latLng.toString());
                                        var latLng = event.latLng.toString();
                                        latLng = latLng.replace('(','');
                                        latLng = latLng.replace(')','');
                                        latLng = latLng.split(',');
                                        // console.log(latLng);
                                        addMarker(latLng[0].trim(), latLng[1].trim());
                                        // console.log(data);
                                        // $('#button-add-marker_nursery_idcoop').click();
                                    }
                                }
                            }
                        }
                    });
                    map = $("#map_canvas_nursery_idcoop").gmap3({get: {name: "map"}});
                    // addMarker();

                    show_polygon2();
                    //$('#add-marker').click(function() {
                        //addMarker();
                    //});
                    $('#button-add-marker_nursery_idcoop').click(function() {
                        // switch mode
                        mode = (mode == 'normal')?'add':'normal';
                        var map_hidden_div = $('#map_canvas_nursery_idcoop > div').children('div:first').children('div');
                        if (mode == 'add') {
                            $(this).addClass('btn-primary');
                            map_hidden_div.css('cursor', 'default');
                        } else {
                            $(this).removeClass('btn-primary');
                            map_hidden_div.css('cursor', 'url("http://maps.gstatic.com/mapfiles/openhand_8_8.cur"), default');
                        };
                    });
                    $('#delete-marker_nursery_idcoop').click(function() {
                        var id = $(this).data('id');
                        clearMarker(id);
                        markers.splice(id, 1);
                        $(this).hide();
                    });
                    $('#clear-marker_nursery_idcoop').click(function() {
                        clearMarker();
                        markers.splice(0, markers.length);
                        makePoly();
                        $('#delete-marker_nursery_idcoop').hide();
                        $('#luas_nursery_idcoop').val('');
                    });

                    $('#save-polygon_nursery_idcoop').click(function() {
                        var luas_area = calc_area_marker();
                        if(luas_area==0.00){
                            alert('No polygon found!')
                        }else{
                        	save_polygon();
                            clearMarkerByTag('area');
                            clearMarkerAfterSave();
                            hide_polygon();
                            show_polygon();
                            $('#button-add-marker_nursery_idcoop').hide();
                            $('#clear-marker_nursery_idcoop').hide();
                            $('#save-polygon_nursery_idcoop').hide();
                            $('#cancel-polygon_nursery_idcoop').hide();
                            $('#luas_nursery_idcoop').hide();
                            $('#edit-polygon_nursery_idcoop').show();
                            mode = 'normal';
                            var map_hidden_div = $('#map_canvas_nursery_idcoop > div').children('div:first').children('div');
                            $('#button-add-marker_nursery_idcoop').removeClass('btn-primary');
                            map_hidden_div.css('cursor', 'url("http://maps.gstatic.com/mapfiles/openhand_8_8.cur"), default');
                            $('#cLosePolygon_nursery_idcoop').click();
                        }

                    });

                    $('#cancel-polygon_nursery_idcoop').click(function() {
                        clearMarker();
                        markers.splice(0, markers.length);
                        makePoly();
                        $('#delete-marker_nursery_idcoop').hide();
                        show_polygon();
                        $('#button-add-marker_nursery_idcoop').hide();
                        $('#clear-marker_nursery_idcoop').hide();
                        $('#save-polygon_nursery_idcoop').hide();
                        $('#cancel-polygon_nursery_idcoop').hide();
                        $('#luas_nursery_idcoop').hide();
                        $('#edit-polygon_nursery_idcoop').show();
                        mode = 'normal';
                        var map_hidden_div = $('#map_canvas_nursery_idcoop > div').children('div:first').children('div');
                        $('#button-add-marker_nursery_idcoop').removeClass('btn-primary');
                        map_hidden_div.css('cursor', 'url("http://maps.gstatic.com/mapfiles/openhand_8_8.cur"), default');
                    });

                    $('#edit-polygon_nursery_idcoop').click(function() {
                        $('#button-add-marker_nursery_idcoop').show();
                        $('#clear-marker_nursery_idcoop').show();
                        $('#save-polygon_nursery_idcoop').show();
                        $('#cancel-polygon_nursery_idcoop').show();
                        $('#luas_nursery_idcoop').show();
                        $('#edit-polygon_nursery_idcoop').hide();
                        edit_polygon();
                        mode = 'normal';
                        var map_hidden_div = $('#map_canvas_nursery_idcoop > div').children('div:first').children('div');
                        $('#button-add-marker_nursery_idcoop').removeClass('btn-primary');
                        map_hidden_div.css('cursor', 'url("http://maps.gstatic.com/mapfiles/openhand_8_8.cur"), default');
                    });

                    setTimeout(function(){
                        map = $('#map_canvas_nursery_idcoop').gmap3('get');
                        fixMap(map);
                    }, 10);
                });

            </script>
    </body>
</html>

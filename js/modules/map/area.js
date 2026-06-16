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

var area_bounds = null;
area_bounds = new google.maps.LatLngBounds();

var MemberID, PlotNr, SurveyNr;

var edit_mode = false;
var map;
// var mode = 'normal';
var markers = [];

function setMapAreaSize() {
    var width = $(window).width();
    var height = $(window).height();
    $('#map_area').css('width', width*0.9-250);
    $('#map_area').css('height', height*0.7-$('#areawindow_header').height());
}

jQuery(function($) {
	setMapAreaSize();
    // map
    $("#map_area").gmap3({
        map: {
            options: {
                center: [-0.8129392009883654,100.37221343321222],
                // zoom: 15,
                //mapTypeControl: false,
                panControl: true,
                zoomControl: true,
                //scaleControl: false,
                streetViewControl: false,
                rotateControl: false,
                rotateControlOptions: false,
                overviewMapControl: false,
                OverviewMapControlOptions: false,
                //scrollwheel: false,
                mapTypeId: google.maps.MapTypeId.HYBRID

            }
        }                    
    });
    $.get(api_farmer, function(data) {
        MemberID    = data.MemberID;
        PlotNr      = data.PlotNr;
        SurveyNr    = data.SurveyNr;
        
        $('#MemberID').text(data.MemberDisplayID);
        $('#MemberName').text(data.MemberName);
        $('#PlotNr').text(data.PlotNr);
        $('#SurveyNr').text(data.SurveyNr);
        $('#GardenAreaHa').text(data.GardenAreaHa);
        $('#area_hectare').text(data.GardenAreaPolygon);
        Latitude = data.Latitude;
        Longitude = data.Longitude;
        // garden point
        if (Latitude !== '' && Longitude !== '') {
            var icon_path = base_url + 'img/maps/';
            $("#map_area").gmap3({
                marker:{
                    values:[
                        {
                            latLng:[parseFloat(Latitude),parseFloat(Longitude)],
                            tag:'garden',
                            options: {
                                icon: icon_path + "farmer.png"
                            },
                        }
                    ]
                }
            });

            var myLatLng = new google.maps.LatLng(parseFloat(Latitude),parseFloat(Longitude));
            area_bounds.extend(myLatLng);
            $('#map_area').gmap3("get")
                .fitBounds(area_bounds);
        }
    });
    $('#tr_area').hide();
    $.get(api_polygon, function(data) {
        area = data;
        if (area) {
            // calc_area();   
            // setTimeout(calc_area(), 2000);
            $('#tr_area').show();
            show_polygon();
        }
    });

    $('#show-polygon').change(function() {
        if ($(this).is(':checked')) {
            show_polygon();
        } else {
            hide_polygon();
        }
    });

    $('#btn-edit-polygon').click(function(event) {
        if (!edit_mode) {
            $(this).hide();
            $('#btn-save-polygon').show();
            $('#btn-cancel').show();
            edit_mode = true;
            edit_polygon();
        }
    });

    $('#btn-save-polygon').click(function(event) {
        if (edit_mode) {
            $(this).hide();
            $('#btn-cancel').hide();
            $('#btn-edit-polygon').show();
            edit_mode = false;
            save_polygon();
            clearMarkerByTag('area');
            hide_polygon();
            show_polygon();
        }
    });
    $('#btn-cancel').click(function(event) {
        if (edit_mode) {
            $(this).hide();
            $('#btn-save-polygon').hide();
            $('#btn-edit-polygon').show();
            edit_mode = false;
            clearMarkerByTag('area');
            hide_polygon();
            show_polygon();
            calc_area();
        }
    });
    
    setTimeout(function(){                    
        map = $('#map_area').gmap3('get');
        fixMap(map);
    }, 100);
}); 

function calc_area (point) {
    // console.log('calculate area');
    if (!point) {
        point = [];
        $.each(area, function(index, val) {
            point.push({'lat':val[0],'lng':val[1]});
        });
    }
    area_hectare = PlanarPolygonAreaMeters(point);
    area_hectare = area_hectare/10000;
    $('#area_hectare').text(area_hectare.toFixed(2));
}

function send_data () {
    $.ajax({
        url: api_polygon,
        type: 'PUT',
        data: {
            MemberID: MemberID,
            PlotNr: PlotNr,
            SurveyNr: SurveyNr,
            area: area,
            area_hectare: area_hectare
        },
    })
    .done(function() {
        // console.log("success");
    })
    .fail(function() {
        // console.log("error");
    })
    .always(function() {
        // console.log("complete");
    });
    
}

function clear_marker () {
    $("#map_area").gmap3({
        clear: {
            name: "marker"
        }
    })   
}   

function hide_polygon () {
    $("#map_area").gmap3({
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
    hide_polygon();
    markers = [];
    $.each(area, function(index, val) {
        addMarker(val[0], val[1]);
    });
}

function save_polygon () {
    area = [];
    $.each(markers, function(index, val) {
        area.push([val.lat, val.lng]);
    });
    calc_area();
    send_data();
}

function show_polygon () {   
    $.each(area, function(index, val) {
        var myLatLng = new google.maps.LatLng(val[0],val[1]);
        area_bounds.extend(myLatLng);
    });

    $('#map_area').gmap3("get")
        .fitBounds(area_bounds);    

    $("#map_area").gmap3({
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
                    contentString = 'Area : ' + area_hectare.toFixed(2) + ' ha';

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
    })
}

function addMarker(lat, lng)
{
    var mark = [];
    var id = markers.length;
    if (!lat || !lng) {
        var lat = map.getCenter().lat();
        var lng = map.getCenter().lng();
    }
    mark["lat"] = lat;
    mark["lng"] = lng;

    markers[id] = mark;

    $("#map_area").gmap3({
        marker: {
            latLng: [lat, lng],
            id: id+1,
            tag: 'area',
            options: {
                draggable: true
            },
            events: {
                dragend: function(marker, event, data) {
                    markers[data.id-1]["lat"] = marker.getPosition().lat();
                    markers[data.id-1]["lng"] = marker.getPosition().lng();
                    makePoly();
                    calc_area(markers);
                },
                click: function(marker, event, data) {
                    var button = $('#delete-marker');
                    button.text('Delete Marker ' + data.id);
                    button.data('id', data.id);
                    button.show();
                }
            }
        }
    });
    makePoly();
}

function clearMarker(id) {
    if (id) {
        delete markers[id-1];
        $("#map_area").gmap3({
            clear: {
                name: "marker",
                id: id
            }
        });
    } else {
        $("#map_area").gmap3({
             clear: {
                name: "marker"
            }
        }); 
        markers = [];
    }
    makePoly();
}

function clearMarkerByTag (tag) {
    $("#map_area").gmap3({
        clear: {
            name: "marker",
            tag: tag
        }
    }); 
}

function fitMap()
{
    var bounds = new google.maps.LatLngBounds();
    $.each(markers, function(i){
        if (markers[i]) {
            latLng = new google.maps.LatLng(parseFloat(markers[i].lat), parseFloat(markers[i].lng));
            bounds.extend(latLng);
        }
    });
    map.fitBounds(bounds); 
}

function makePoly()
{
    var tmp_marker = [];
    // $('#markers-text').html('');
    
    //var bounds = new google.maps.LatLngBounds();
    $.each(markers, function(i){
        if (markers[i]) {
            tmp_marker.push([markers[i].lat, markers[i].lng]);
            //$('#markers-text').append('['+markers[i].lat + ',' + markers[i].lng + '],<br/>');
//            latLng = new google.maps.LatLng(parseFloat(markers[i].lat), parseFloat(markers[i].lng));
//            bounds.extend(latLng);
        }
    });
    $('#polygon').val('[['+tmp_marker.join('],[')+']]');
    $("#map_area").gmap3({
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
        $("#map_area").gmap3({
            polyline: {
                options: {
                    strokeColor: "#FF0000",
                    strokeOpacity: 1.0,
                    strokeWeight: 2,
                    path: tmp_marker
                }
            }
        });
        //map.fitBounds(bounds);        
    } else if (tmp_marker.length > 2) {
        // console.log('polygon');
        tmp_marker.push(tmp_marker[0]);
        $("#map_area").gmap3({
            polygon: {
                options: {
                    strokeColor: "#FF0000",
                    strokeOpacity: 0.8,
                    strokeWeight: 2,
                    fillColor: "#FF0000",
                    fillOpacity: 0.35,
                    paths: tmp_marker                                  
                },
                events: {                            
                    click: function(polygon, event, data) {
                        var vertices = polygon.getPath();

                        var points = [];
                        for (var i = 0; i < vertices.length; i++) {
                            var xy = vertices.getAt(i);
                            points.push({'lat':xy.lat(),'lng':xy.lng()});
                        }
                        var area = PlanarPolygonAreaMeters(points);
                        area = area/10000;
                        
                        contentString = area.toFixed(2) + ' ha';

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
        //map.fitBounds(bounds);  
    }
}
var $map_canvas = $('#map_canvas');
var map         = null;
var bounds      = new google.maps.LatLngBounds();
var infowindow  = new google.maps.InfoWindow();
var width       = Math.max(document.documentElement.clientWidth, window.innerWidth || 0);
var height      = Math.max(document.documentElement.clientHeight, window.innerHeight + 300 || 0);
var icon_path   = m_base_url + 'img/maps/';
var url_kml     = m_base_url + "api/files/kml/";
// var url_kml     = 'https://app.palmoiltrace.com/' + "api/files/kml/";

if(m_sess_partner_id == 145) {
    var actors = [
        { key: 'farmer', type: 'marker', api: m_api+'/map/farmer', label: lang('Farmer'), addLabel: lang('Farmer Plantations'), icon: 'farmer.png', color: 'green' }
    ];
} else {
    var actors = [
        { key: 'farmer', type: 'marker', api: m_api+'/map/farmer', label: lang('Farmer'), addLabel: lang('Farmer Plantations'), icon: 'farmer.png', color: 'green' },
        { key: 'farmer_polygon', type: 'polygon', api: m_api+'/map/farmer_polygon', label: lang('Farm Polygons'), icon: 'farmer_polygon.png', color: 'green' },
        // { key: 'agent', type: 'marker', api: m_api+'/map/agent', label: lang('Pedagang'), icon: 'agent.png', color: 'blue' },
        // { key: 'bank', type: 'marker', api: m_api+'/map/bank', label: lang('Bank'), icon: 'bank.png', color: 'grey' },
        { key: 'sme_sta_500', type: 'marker', api: m_api+'/map/sme_sta_500/', label: lang('> 500 Farmer (Gold)'), icon: 'agent_gold.png', color: 'gold' },
        { key: 'sme_sta_200', type: 'marker', api: m_api+'/map/sme_sta_200/', label: lang('200 - 500 (Silver)'), icon: 'agent_silver.png', color: 'silver' },
        { key: 'sme_sta_100', type: 'marker', api: m_api+'/map/sme_sta_100/', label: lang('< 200 (Bronze)'), icon: 'agent_bronze.png', color: 'bronze' },
        { key: 'processing', type: 'marker', api: m_api+'/map/processing', label: lang('Mill'), icon: 'mill.png', color: 'brown' },
        { key: 'mill_plantation', type: 'marker', api: m_api+'/map/mill_plantation', label: lang('Mill Plantations'), icon: 'mill_plantation.png', color: 'blue' },
    ];
}


$(document).ready(function(){
    // set map size to fit screen
    setMapSize();
    $('#sidebar-collapse').on('click', function(event) {
        $('#sidebar-filter').hide();
        $('#sidebar-button').show();            
    });
    $('#sidebar-expand').on('click', function(event) {
        $('#sidebar-button').hide();
        $('#sidebar-filter').show();
    });
    
    getProvinces();
    $('#filter-province').on('change', function(event) {
        getDistricts();
    });
    setTimeout(function() {
        $('#filter-province').change();
    }, 1000);

    $.each(actors, function(index, val) {
        if(val.key == 'farmer') {
            var tpl = '<div class="am-checkbox" style="display: none;"><input class="check-'+val.type+'" id="check_'+val.key+'" data-type="'+val.key+'" type="checkbox"><label for="check_'+val.key+'"> <img style="width:24px;" src="'+icon_path+val.icon+'" alt=""> '+val.label+' <span class="actor-count"></span> & '+val.addLabel+' <span class="actor-count-add"></span></label></div>';

            $('#panel-farmer').append(tpl);
        } else if(val.key == 'farmer_polygon'){
            var tpl = '<div class="am-checkbox" style="display: none;"><input class="check-'+val.type+'" id="check_'+val.key+'" data-type="'+val.key+'" type="checkbox"><label for="check_'+val.key+'"> <img style="width:24px;" src="'+icon_path+val.icon+'" alt=""> '+val.label+' <span class="actor-count"></span></label></div>';
            $('#panel-farmer').append(tpl);
        } else if(val.key == 'sme_sta_500' || val.key == 'sme_sta_200' || val.key == 'sme_sta_100'){
            var tpl = '<div class="am-checkbox" style="display: none;"><input class="check-'+val.type+'" id="check_'+val.key+'" data-type="'+val.key+'" type="checkbox"><label for="check_'+val.key+'"> <img style="width:24px;" src="'+icon_path+val.icon+'" alt=""> '+val.label+' <span class="actor-count"></span></label></div>';
            $('#panel-sme').append(tpl);
        } else if(val.key == 'mill_plantation' || val.key == 'processing'){
            var tpl = '<div class="am-checkbox" style="display: none;"><input class="check-'+val.type+'" id="check_'+val.key+'" data-type="'+val.key+'" type="checkbox"><label for="check_'+val.key+'"> <img style="width:24px;" src="'+icon_path+val.icon+'" alt=""> '+val.label+' <span class="actor-count"></span></label></div>';
            $('#panel-mill').append(tpl);
        }
    });

    $('#filter-key').keypress(function (e) {
        var key = e.which;
        if(key == 13) {
            // $('#filter-search').click();
            extLoading();
            return false;  
        }
    });  

    $('#filter-search').on('click', function(e) {
        e.preventDefault();
        // fetchObject();
    });

    $('input[name="filterby"]').on('change', function(event) {
        event.preventDefault();
        var filterby = $('input[name="filterby"]:checked').val();
        if (filterby == 'district') {
            $('.filterby-farmerid').addClass('hidden');
            $('.filterby-district').removeClass('hidden');
        } else {
            $('.filterby-district').addClass('hidden');
            $('.filterby-farmerid').removeClass('hidden');
        }
    });

    $('#sidebar-filter').on('change', '.check-kml', function(event) {
        closeInfoBox();
        if ($(this).is(':checked')) {
            show_kml($(this).data('id'),$(this).data('name'),$(this).data('file'))
        } else {
            hide_kml($(this).data('id'));
        }
    });

    $('.check-age').on('change', function(event) {
        closeInfoBox();
        if ($(this).is(':checked')) {
            show_marker($(this).data('name'));
            show_polygon($(this).data('name'));
        } else {
            hide_marker($(this).data('name'));
            hide_polygon($(this).data('name'));
        }
    });

    $('.mill_distance').on('change', function(event) {
        closeInfoBox();
        hide_circle();

        if($(this).data('distance') > 0)
            draw_mills_distance($(this).data('distance')*1000);
    });

    $('.check-marker').on('change', function(event) {
        closeInfoBox();
        console.log($(this).data('type'));
        if ($(this).is(':checked')) {
            show_marker($(this).data('type'));
            show_polygon($(this).data('type'));
        } else {
            hide_marker($(this).data('type'));
            if($(this).data('type') == "farmer"){
                hide_marker("farmer_sme");
            }
            hide_polygon($(this).data('type'));
            if ($(this).data('type') == 'processing') {
                hide_circle();
            }
        }
    });

    $('.check-polygon').on('change', function(event) {
        closeInfoBox();
        if ($(this).is(':checked')) {
            show_polygon($(this).data('type'));
        } else {
            hide_polygon($(this).data('type'));
        }
    });
});

function fetchObject() {
    clear_map();

    var filterby = $('input[name="filterby"]:checked').val();
    if (filterby == 'district' && !$('#filter-province').val() /*|| !$('#filter-district').val()*/) {
        Ext.MessageBox.hide();
        Ext.Msg.alert('Info', lang('Province must be selected'));
        return false;
    } else if (filterby == 'farmerid' && $('#filter-key').val().length < 3 /*|| !$('#filter-district').val()*/) {
        Ext.MessageBox.hide();
        Ext.Msg.alert('Info', lang('Please fill search key with a minimum of three characters!'));
        return false;
    } 

    $('#panel-restricted .am-checkbox').remove();
    $('#panel-safe .am-checkbox').remove();
    $('#panel-buffer .am-checkbox').remove();
    if (m_act_landuse == 1 && filterby == 'district') {
        var ProvinceID = $('#filter-province').val();
        var DistrictID = $('#filter-district').val();
        // Restricted Area
        fetch_restricted_area(ProvinceID, DistrictID);

        // Save Area
        fetch_safe_area(ProvinceID, DistrictID);

        // Buffer Zone
        fetch_buffer_zone(ProvinceID, DistrictID);
        
    }

    $('#panel-administrative .am-checkbox').remove();
    $('#panel-land_cover .am-checkbox').remove();
    $('#panel-animal_habitat .am-checkbox').remove();
    if (filterby == 'district') {
        var ProvinceID = $('#filter-province').val();
        var DistrictID = $('#filter-district').val();
        // Administrative Area
        fetch_administrative_area(ProvinceID, DistrictID);

        // Land Cover
        fetch_land_cover(ProvinceID, DistrictID);

        // Animal Habitat
        fetch_animal_habitat(ProvinceID, DistrictID);
    }

    var activeAjaxConnections = 0;
    var actors_count = 0;    
    var DistrictIDs = [];

    $.each(actors, function(index, val) {
        $('#check_'+val.key).parent().hide();

        // if (val.key == 'farmer_polygon') {
        //     return true;
        // }

        var params = {PartnerID: m_sess_partner_id};
        if (filterby == 'district') {
            params.ProvinceID = $('#filter-province').val();
            params.DistrictID = $('#filter-district').val();
        } else {
            params.key = $('#filter-key').val();
        }

        $.ajax({
            type: "GET",
            url: m_api+'/map/'+val.key,
            data: params,
            contentType: "application/json; charset=utf-8",
            dataType: "json",
            beforeSend: function(xhr) {
                activeAjaxConnections++;
            },
            // async: false,
            success: function(list) {
                if (list) {
                    actors_count += list.length;
                    //var list     = JSON.parse(response.responseText);
                    
                    var latLngs  = [], farmAges = [], polygonCount = 0;
                    $.each(list, function(index, data) {
                        var tag = [val.key];
                        data['type']  = val.key;
                        data['label'] = val.label;
                        data['color'] = val.color;
                        var age = '';
                        if (val.key == 'farmer' || val.key == 'farmer_polygon') {
                            if (data.FarmAge >= 0 && data.FarmAge <=3) {
                                age = 'age_1';
                            } else if (data.FarmAge >= 4 && data.FarmAge <=6) {
                                age = 'age_4';
                            } else if (data.FarmAge >= 7 && data.FarmAge <=18) {
                                age = 'age_7';
                            } else if (data.FarmAge >= 19) {
                                age = 'age_19';
                            } 
                            tag.push(age);
                            if ($.inArray(age, farmAges) == -1) {
                                farmAges.push(age);
                            }
                        }
                        
                        //Garmbar Polygon
                        if(val.key == 'farmer_polygon'){
                            if (data.polygon) {
                                draw_polygon([val.key, age], data.ID+'-'+data.PlotNr, data.polygon, data);
                                polygonCount++;
                            }
                        } else {
                            // Selain Polygon
                            latLngs.push({
                                latLng: [parseFloat(data.Latitude), parseFloat(data.Longitude)],
                                data: data,
                                tag: tag,
                                options: {
                                    icon: icon_path + val.icon
                                },
                            });
    
                            if (parseFloat(data.Latitude) && parseFloat(data.Longitude)) {
                                var myLatLng = new google.maps.LatLng(parseFloat(data.Latitude), parseFloat(data.Longitude));
                                bounds.extend(myLatLng);
                            }

                            if (data.polygon) {
                                draw_polygon(val.key, data.ID+'-'+data.PlotNr, data.polygon);
                            }

                            if (filterby == 'farmerid' && parseInt(data.DistrictID) && $.inArray(data.DistrictID, DistrictIDs) == -1) {
                                console.log(data.DistrictID);
                                DistrictIDs.push(data.DistrictID);
                            }
                        }
                    });
                    
                    if (farmAges.length > 0) {
                        $.each(farmAges, function(i, farmAge) {
                            $('#check_'+farmAge).prop('checked', 'checked');
                        });
                        // $('#panel-farm-age').show();
                    } else {
                        // $('#panel-farm-age').hide();   
                    }

                    add_markers(latLngs);

                    if(val.key == 'farmer_polygon'){
                        $('#check_'+val.key).prop('checked', 'checked');
                        $('#check_'+val.key).parent().find('.actor-count').text('('+polygonCount+')');
                        $('#check_'+val.key).parent().show();
                    }else{
                        $('#check_'+val.key).prop('checked', 'checked');
                        if (val.key == 'farmer') {
                            if(list[0].petani_realcount == 'false') list[0].petani_realcount = 0;
                            $('#check_'+val.key).parent().find('.actor-count').text('('+list[0].petani_realcount+')');
                            $('#check_'+val.key).parent().find('.actor-count-add').text('('+list.length+')');
                        }else{
                            $('#check_'+val.key).parent().find('.actor-count').text('('+list.length+')');
                        }
                        $('#check_'+val.key).parent().show();
                    }

                    $map_canvas.gmap3("get").fitBounds(bounds);
                }
            },
            complete: function() {
                activeAjaxConnections--;
                if (0 == activeAjaxConnections) {
                    // this was the last Ajax connection, do the thing
                    Ext.MessageBox.hide();
                    if (0 == actors_count) {
                        Ext.Msg.alert('Info', lang('No object found.'));
                    }
                    console.log(filterby);
                    console.log(DistrictIDs);
                    if (filterby == 'farmerid' && DistrictIDs.length > 0) {
                        fetch_restricted_area('', DistrictIDs.join());
                        fetch_safe_area('', DistrictIDs.join());
                        fetch_buffer_zone('', DistrictIDs.join());
                        fetch_administrative_area('', DistrictIDs.join());
                        fetch_land_cover('', DistrictIDs.join());
                        fetch_animal_habitat('', DistrictIDs.join());
                    }
                }
            }
        });
    });
}

function fetch_restricted_area(ProvinceID, DistrictID) {
    Ext.Ajax.request({
        url: m_api+'/map/restricted_area',
        method: 'GET',
        params: {
            ProvinceID: ProvinceID,
            DistrictID: DistrictID,
        },
        success: function(response){
            if (response.responseText) {
                var data = JSON.parse(response.responseText);
                var options   = '';
                $.each(data, function(index, val) {
                    options += '<div class="am-checkbox"><input class="check-kml" id="kml_'+val.ID+'" type="checkbox" data-id="'+val.ID+'" data-name="'+val.Name+'" data-file="'+val.FileName+'"><label for="kml_'+val.ID+'"><span class="landuse-color" style="color:'+val.Color+'; background-color: '+val.Color+';">&nbsp;&nbsp;&nbsp;&nbsp;</span> '+val.Name+'</label></div>';
                });
                $('#panel-restricted').append(options);
            }
        }
    });
}

function fetch_safe_area(ProvinceID, DistrictID) {
    Ext.Ajax.request({
        url: m_api+'/map/safe_area',
        method: 'GET',
        params: {
            ProvinceID: ProvinceID,
            DistrictID: DistrictID,
        },
        success: function(response){
            if (response.responseText) {
                var data = JSON.parse(response.responseText);
                var options   = '';
                $.each(data, function(index, val) {
                    options += '<div class="am-checkbox"><input class="check-kml" id="kml_'+val.ID+'" type="checkbox" data-id="'+val.ID+'" data-name="'+val.Name+'" data-file="'+val.FileName+'"><label for="kml_'+val.ID+'"><span class="landuse-color" style="color:'+val.Color+'; background-color: '+val.Color+';">&nbsp;&nbsp;&nbsp;&nbsp;</span> '+val.Name+'</label></div>';
                });
                $('#panel-safe').append(options);
            }
        }
    });
}

function fetch_buffer_zone(ProvinceID, DistrictID) {
    Ext.Ajax.request({
        url: m_api+'/map/buffer_zone',
        method: 'GET',
        params: {
            ProvinceID: ProvinceID,
            DistrictID: DistrictID,
        },
        success: function(response){
            if (response.responseText) {
                var data = JSON.parse(response.responseText);
                var options   = '';
                $.each(data, function(index, val) {
                    options += '<div class="am-checkbox"><input class="check-kml" id="kml_'+val.ID+'" type="checkbox" data-id="'+val.ID+'" data-name="'+val.Name+'" data-file="'+val.FileName+'"><label for="kml_'+val.ID+'"><span class="landuse-color" style="color:'+val.Color+'; background-color: '+val.Color+';">&nbsp;&nbsp;&nbsp;&nbsp;</span> '+val.Name+'</label></div>';
                });
                $('#panel-buffer').append(options);
            }
        }
    });
}

function fetch_administrative_area(ProvinceID, DistrictID) {
    Ext.Ajax.request({
        url: m_api+'/map/administrative_area',
        method: 'GET',
        params: {
            ProvinceID: ProvinceID,
            DistrictID: DistrictID,
        },
        success: function(response){
            if (response.responseText) {
                var data = JSON.parse(response.responseText);
                var options   = '';
                $.each(data, function(index, val) {
                    options += '<div class="am-checkbox"><input class="check-kml" id="kml_'+val.ID+'" type="checkbox" data-id="'+val.ID+'" data-name="'+val.Name+'" data-file="'+val.FileName+'"><label for="kml_'+val.ID+'"><span class="landuse-color" style="color:'+val.Color+'; background-color: '+val.Color+';">&nbsp;&nbsp;&nbsp;&nbsp;</span> '+val.Name+'</label></div>';
                });
                $('#panel-administrative').append(options);
            }
        }
    });
}

function fetch_land_cover(ProvinceID, DistrictID) {
    Ext.Ajax.request({
        url: m_api+'/map/land_cover',
        method: 'GET',
        params: {
            ProvinceID: ProvinceID,
            DistrictID: DistrictID,
        },
        success: function(response){
            if (response.responseText) {
                var data = JSON.parse(response.responseText);
                var options   = '';
                $.each(data, function(index, val) {
                    options += '<div class="am-checkbox"><input class="check-kml" id="kml_'+val.ID+'" type="checkbox" data-id="'+val.ID+'" data-name="'+val.Name+'" data-file="'+val.FileName+'"><label for="kml_'+val.ID+'"><span class="landuse-color" style="color:'+val.Color+'; background-color: '+val.Color+';">&nbsp;&nbsp;&nbsp;&nbsp;</span> '+val.Name+'</label></div>';
                });
                $('#panel-land_cover').append(options);
            }
        }
    });
}

function fetch_animal_habitat(ProvinceID, DistrictID) {
    Ext.Ajax.request({
        url: m_api+'/map/animal_habitat',
        method: 'GET',
        params: {
            ProvinceID: ProvinceID,
            DistrictID: DistrictID,
        },
        success: function(response){
            if (response.responseText) {
                var data = JSON.parse(response.responseText);
                var options   = '';
                $.each(data, function(index, val) {
                    options += '<div class="am-checkbox"><input class="check-kml" id="kml_'+val.ID+'" type="checkbox" data-id="'+val.ID+'" data-name="'+val.Name+'" data-file="'+val.FileName+'"><label for="kml_'+val.ID+'"><span class="landuse-color" style="color:'+val.Color+'; background-color: '+val.Color+';">&nbsp;&nbsp;&nbsp;&nbsp;</span> '+val.Name+'</label></div>';
                });
                $('#panel-animal_habitat').append(options);
            }
        }
    });
}


function getProvinces(callback) {
    Ext.Ajax.request({
        url: m_api+'/map/province',
        method: 'GET',
        success: function(response){
            $('#filter-province option').remove();
            var provinces = JSON.parse(response.responseText);
            var options   = '<option value="">'+lang('All Province')+'</option>';
            $.each(provinces, function(index, val) {
                options += '<option value="'+val.id+'">'+val.label+'</option>';
            });
            $('#filter-province').append(options);
            callback;
        }
    });
}

function getDistricts() {
    Ext.Ajax.request({
        url: m_api+'/map/district',
        method: 'GET',
        params: {
            ProvinceID: $('#filter-province').val()
        },
        success: function(response){
            $('#filter-district option').remove();
            var options   = '<option value="">'+lang('All District')+'</option>';
            if (response.responseText) {
                var districts = JSON.parse(response.responseText);
                $.each(districts, function(index, val) {
                    options += '<option value="'+val.id+'">'+val.label+'</option>';
                });
            }
            $('#filter-district').append(options);
        }
    });
}

function show_kml(id, name, file) {
    var kml = $map_canvas.gmap3({
        get: {
            name: 'kmllayer',
            tag: 'kml',
            id: id
        }
    });
    if (kml) {
        kml.setMap(map);
    } else {
        $map_canvas.gmap3({
            kmllayer:{
                options:{
                    url: url_kml+file,
                    opts:{
                        // suppressInfoWindows: true
                    }
                },
                tag: 'kml',
                id: id
            },
        });
    }
}

function hide_kml(id) {
    var kml = $map_canvas.gmap3({
        get: {
            name: 'kmllayer',
            tag: 'kml',
            id: id
        }
    });
    if (kml) {
        kml.setMap(null);
    }
}

function show_marker(tag) {
    var markers = $map_canvas.gmap3({
        get: {
            name: 'marker',
            tag: tag,
            all: true
        }
    });
    $.each(markers, function (idx, marker) {
        marker.setMap(map);
    })
}

function hide_marker(tag) {
    var markers = $map_canvas.gmap3({
        get: {
            name: 'marker',
            tag: tag,
            all: true
        }
    });
    $.each(markers, function (idx, marker) {
        marker.setMap(null);
    })
}

function add_markers(latlng) {
    $map_canvas.gmap3({
        marker: {
            // tag: tag_name,
            // data: info,
            values: latlng
            , events: {
                click: function (marker, event, context) {
                    var mapObject = $(this).gmap3("get");
                    closeInfoBox();
                    getInfoBox(context).open(mapObject, marker);
                }
            }
        }
    });
}

function hide_polygon(tag) {
    var polygons = $map_canvas.gmap3({
        get: {
            name: 'polygon',
            tag: tag,
            all: true
        }
    });
    if (polygons) {
        $.each(polygons, function (idx, polygon) {
            polygon.setMap(null);
        })   
    }
}

function show_polygon(tag) {
    var polygons = $map_canvas.gmap3({
        get: {
            name: 'polygon',
            tag: tag,
            all: true
        }
    });
    if (polygons) {
        $.each(polygons, function (idx, polygon) {
            polygon.setMap(map);
        })   
    }
}

function draw_polygon(tag, id, area, data, color) {
    var polygonColor = 'red';
    if (color) {
        polygonColor = color;
    }
    $map_canvas.gmap3({
        polygon: {
            tag: tag,
            id: 'polygon_'+id,
            data: data,
            options: {
                strokeColor: polygonColor,
                strokeOpacity: 0.8,
                strokeWeight: 2,
                fillColor: polygonColor,
                fillOpacity: 0.35,
                paths: area
            }
            , events: {
                click: function (object, event, context) {
                    var map = $(this).gmap3("get");
                    var content = "";
                    content += '<p><strong>'+context.data.label+'</strong></p>';
                    content += '<table class="table table-condensed table-hover table-bordered table-striped">';
                        content += '<tbody>';
                            content += '<tr><td style="width: 100px;">'+lang('FarmerID')+'</td><td>'+context.data.MemberDisplayID+'</td></tr>';
                            content += '<tr><td>'+lang('Farmer Name')+'</td><td>'+context.data.Name+'</td></tr>';
                            content += '<tr><td>'+lang('GardenNr')+'</td><td>'+context.data.PlotNr+'</td></tr>';
                            content += '<tr><td>'+lang('SurveyNr')+'</td><td>'+context.data.SurveyNr+'</td></tr>';
                            content += '<tr><td>'+lang('Ha Survey')+'</td><td>'+context.data.GardenAreaHa+'</td></tr>';
                            content += '<tr><td>'+lang('Ha Polygon')+'</td><td>'+context.data.GardenAreaPolygon+'</td></tr>';
                        content += '</tbody>';
                    content += '</table>';
                    infowindow.setContent(content);
                    infowindow.setPosition(event.latLng);
                    infowindow.open(map);
                }
            }
        },
        // autofit:{}
    });
}

function draw_mills_distance(distance) {
    var mills = $map_canvas.gmap3({
        get: {
            name: 'marker',
            tag: 'processing',
            all: true
        }
    });
    $.each(mills, function(index, mill) {
        draw_circle(mill.position.lat(), mill.position.lng(), distance);
    });
}

function hide_circle(tag) {
    var objects = $map_canvas.gmap3({
        get: {
            name: 'circle',
            tag: tag,
            all: true
        }
    });
    if (objects) {
        $.each(objects, function (idx, object) {
            object.setMap(null);
        })   
    }
}

function draw_circle (lat, lng, radius) {
    if (!radius) {
        radius = 5000; // meters
    }
    $map_canvas.gmap3({
        circle:{
            tag: 'bank',
            options:{
                center: [lat, lng],
                radius : radius,
                fillColor : "#F46D43",
                strokeColor : "#F46D43"
            },
        },
    });
}

function init_map() {
    $map_canvas.gmap3({
        map: {
            options: {
                center: [-4.433497, 119.949203],
                // zoom: 5,
                //mapTypeControl: false,
                panControl: true,
                zoomControl: true,
                //scaleControl: false,
                streetViewControl: false,
                rotateControl: false,
                rotateControlOptions: false,
                overviewMapControl: false,
                OverviewMapControlOptions: false,
                scrollwheel: true,
                mapTypeId: google.maps.MapTypeId.ROADMAP
            },
            callback: function (map) {
                var panel_filter = $('#panel-filter')[0];
                if (panel_filter) {
                    map.controls[google.maps.ControlPosition.LEFT_TOP].push(panel_filter);
                    setTimeout(function(){
                        $(panel_filter).removeClass('hidden');
                    }, 200)
                }
            },
        },
    });
    map = $map_canvas.gmap3("get");
}
function setMapSize() {
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
function destroyMap() {
    $map_canvas.gmap3({
        clear: {
            name: ['marker', 'line', 'polyline', 'polygon']
        }
    })
    $map_canvas.gmap3('destroy');
}
function clear_map() {
    closeInfoBox();
    $map_canvas.gmap3({clear: {}});
    bounds = new google.maps.LatLngBounds();
}
function closeInfoBox() {
    $('div.infoBox').remove();
}
function getInfoBox(item) {
    var content = '';
    content = get_info_content(item);
    return new InfoBox({
        content: content,
        // disableAutoPan: true,
        maxWidth: 0,
        pixelOffset: new google.maps.Size(30, -195),
        // closeBoxMargin: '50px 200px',
        closeBoxMargin: "20px 3px 2px 2px",
        closeBoxURL: m_base_url+"img/close.gif",
        // closeBoxURL: '',
        isHidden: false,
        pane: 'floatPane',
        enableEventPropagation: true
    });
}

function show_farmer_sme(MemberID){
    hide_marker('farmer');
    hide_marker('farmer_sme');

    var actors_sme = [
        { key: 'farmer_sme', type: 'marker', api: m_api+'/map/farmer_sme', label: lang('Farmer SME'), addLabel: lang('Farmer Plantations'), icon: 'farmer.png', color: 'green' }
    ];

    var params = {MemberID: MemberID};
    var filterby = $('input[name="filterby"]:checked').val();

    var activeAjaxConnectionssme = 0;
    var actors_count_sme = 0;    
    var DistrictIDs = [];

    $.each(actors_sme, function(index, val) {
        $('#check_'+val.key).parent().hide();

        $.ajax({
            type: "GET",
            url: m_api+'/map/'+val.key,
            data: params,
            contentType: "application/json; charset=utf-8",
            dataType: "json",
            beforeSend: function(xhr) {
                activeAjaxConnectionssme++;
            },
            // async: false,
            success: function(list) {
                if (list) {
                    actors_count_sme += list.length;
                    //var list     = JSON.parse(response.responseText);
                    
                    var latLngs  = [], farmAges = [], polygonCount = 0;
                    $.each(list, function(index, data) {
                        var tag = [val.key];
                        data['type']  = val.key;
                        data['label'] = val.label;
                        data['color'] = val.color;
                        var age = '';
                        if (val.key == 'farmer' || val.key == 'farmer_polygon') {
                            if (data.FarmAge >= 0 && data.FarmAge <=3) {
                                age = 'age_1';
                            } else if (data.FarmAge >= 4 && data.FarmAge <=6) {
                                age = 'age_4';
                            } else if (data.FarmAge >= 7 && data.FarmAge <=18) {
                                age = 'age_7';
                            } else if (data.FarmAge >= 19) {
                                age = 'age_19';
                            } 
                            tag.push(age);
                            if ($.inArray(age, farmAges) == -1) {
                                farmAges.push(age);
                            }
                        }
                        
                        //Garmbar Polygon
                        if(val.key == 'farmer_polygon'){
                            if (data.polygon) {
                                draw_polygon([val.key, age], data.ID+'-'+data.PlotNr, data.polygon, data);
                                polygonCount++;
                            }
                        } else {
                            // Selain Polygon
                            latLngs.push({
                                latLng: [parseFloat(data.Latitude), parseFloat(data.Longitude)],
                                data: data,
                                tag: tag,
                                options: {
                                    icon: icon_path + val.icon
                                },
                            });
    
                            if (parseFloat(data.Latitude) && parseFloat(data.Longitude)) {
                                var myLatLng = new google.maps.LatLng(parseFloat(data.Latitude), parseFloat(data.Longitude));
                                bounds.extend(myLatLng);
                            }

                            if (data.polygon) {
                                draw_polygon(val.key, data.ID+'-'+data.PlotNr, data.polygon);
                            }

                            if (filterby == 'farmerid' && parseInt(data.DistrictID) && $.inArray(data.DistrictID, DistrictIDs) == -1) {
                                console.log(data.DistrictID);
                                DistrictIDs.push(data.DistrictID);
                            }
                        }
                    });
                    
                    if (farmAges.length > 0) {
                        $.each(farmAges, function(i, farmAge) {
                            $('#check_'+farmAge).prop('checked', 'checked');
                        });
                        // $('#panel-farm-age').show();
                    } else {
                        // $('#panel-farm-age').hide();   
                    }

                    add_markers(latLngs);

                    if(val.key == 'farmer_polygon'){
                        $('#check_'+val.key).prop('checked', 'checked');
                        $('#check_'+val.key).parent().find('.actor-count').text('('+polygonCount+')');
                        $('#check_'+val.key).parent().show();
                    }else{
                        $('#check_'+val.key).prop('checked', 'checked');
                        if (val.key == 'farmer') {
                            if(list[0].petani_realcount == 'false') list[0].petani_realcount = 0;
                            $('#check_'+val.key).parent().find('.actor-count').text('('+list[0].petani_realcount+')');
                            $('#check_'+val.key).parent().find('.actor-count-add').text('('+list.length+')');
                        }else{
                            $('#check_'+val.key).parent().find('.actor-count').text('('+list.length+')');
                        }
                        $('#check_'+val.key).parent().show();
                    }

                    $map_canvas.gmap3("get").fitBounds(bounds);
                }
            },
            complete: function() {
                activeAjaxConnectionssme--;
                if (0 == activeAjaxConnectionssme) {
                    // this was the last Ajax connection, do the thing
                    Ext.MessageBox.hide();
                    if (0 == actors_count_sme) {
                        Ext.Msg.alert('Info', lang('No object found.'));
                    }
                    console.log(filterby);
                    console.log(DistrictIDs);
                    if (filterby == 'farmerid' && DistrictIDs.length > 0) {
                        fetch_restricted_area('', DistrictIDs.join());
                        fetch_safe_area('', DistrictIDs.join());
                        fetch_buffer_zone('', DistrictIDs.join());
                        fetch_administrative_area('', DistrictIDs.join());
                        fetch_land_cover('', DistrictIDs.join());
                        fetch_animal_habitat('', DistrictIDs.join());
                    }
                }
            }
        });
    });
}

function get_info_content (context) {
    var content = '';
    var url_photo = m_base_url+'api/images/Member/'
    if (context.data.type == 'farmer' || context.data.type == 'farmer_sme') {

        //load photo
        var url_api_images = m_base_url + 'api/images/';
        var url_photo = m_base_url + 'api/images/member/';
        if (context.data.Photo !== 'null') {
            var url_load_photo = url_photo + context.data.ProvinceID + '/' + context.data.Photo;

            setTimeout(function() {$("#farmer_photo"+context.data.MemberID).attr("src",url_api_images+'rolling.gif');}, 20);
            $.get(url_load_photo)
            .done(function() {
                console.log('Done');
                image_url = url_load_photo;
                setTimeout(function() {$("#farmer_photo"+context.data.MemberID).attr("src",image_url);}, 100);
            })
            .fail(function() {
                console.log('Fail');
                setTimeout(function() {$("#farmer_photo"+context.data.MemberID).attr("src",url_api_images+'Photo/default-user.png');}, 100);
            })
        }
        image_url = url_photo + 'default-user.png';
        content = '\
        <img id="farmer_photo'+context.data.MemberID+'" align="left" width="100px" style="padding:5px" src="' + image_url + '" id="">\
        <div class="'+context.data.type+' iw-container">\
            <div class="iw-content">\
            <table border="0" width="100%"><tbody>\
            <tr><td width="100px">' + lang('ID Petani') + '</td><td>: </td><td> ' + context.data.MemberDisplayID + '</td></tr>\
            <tr><td>' + lang('Nama') + '</td><td>: </td><td> ' + context.data.Name + '</td></tr>\
            <tr><td>' + lang('Garden Nr') + '</td><td>: </td><td> ' + context.data.GardenNr + '</td></tr>\
            <tr><td>' + lang('Survey Nr') + '</td><td>: </td><td> ' + context.data.SurveyNr + '</td></tr>\
            <tr><td>' + lang('Farm Age') + '</td><td>: </td><td> ' + number_format(context.data.FarmAge,0,'.',',') + '</td></tr>\
            <tr><td>' + lang('Luas lahan') + '</td><td>: </td><td> ' + number_format(context.data.AreaHa,1,'.',',') + ' Ha</td></tr>\
            <tr><td>' + lang('Produksi') + '</td><td>: </td><td> ' + number_format(context.data.Production,0,'.',',') + ' Ton</td></tr>\
            <tr><td colspan="3">@ ' + context.data.Village +', '+ context.data.SubDistrict + '</td></tr>\
            ';
        content += '<tr><td align="center" style="text-align:center" colspan="3"><a style="line-height: 14px;" class="green_btn" onclick="preview_cetak_surat(\''+m_api+'/farmer/cetak_beneficiary_profiles/MemberID/'+context.data.ID + '\')" href="#"> ' + lang('Cetak') + ' </a>';
        content += '</td></tr>';
        content += '</tbody></table>\
            </div>\
        </div>\
        ';
    } else if (context.data.type == 'processing' || context.data.type == 'mill_plantation') {
        content = '';
        content += '<div class="'+context.data.type+' iw-container">';
            content += '<div class="iw-content">';
            content += '<table border="0" width="100%"><tbody>';
            content += '<tr><td style="width:100px; vertical-align: top;">' + lang('Name') + '</td><td style="vertical-align: top;">:</td><td> ' + context.data.Name + '</td></tr>';
            if (context.data.type == 'mill_plantation') {
                content += '<tr><td>' + lang('Garden Nr') + '</td><td>: </td><td> ' + context.data.GardenNr + '</td></tr>';
                content += '<tr><td>' + lang('Luas lahan') + '</td><td>: </td><td> ' + number_format(context.data.AreaHa,1,'.',',') + ' Ha</td></tr>'
                content += '<tr><td>' + lang('Production') + '</td><td>: </td><td> ' + number_format(context.data.Production,1,'.',',') + '</td></tr>'
            }
            if(context.data.Village){
                content += '<tr><td>' + lang('Village') + '</td><td>: </td><td> ' + context.data.Village + '</td></tr>';
            }
            if(context.data.SubDistrict){
                content += '<tr><td>' + lang('Sub District') + '</td><td>: </td><td> ' + ((context.data.SubDistrict) ? context.data.SubDistrict : '') + '</td></tr>';
            }
            content += '<tr><td>' + lang('Address') + '</td><td>: </td><td> ' + ((context.data.Address) ? context.data.Address : '') + '</td></tr>';
            if (context.data.type == 'processing') {
                var url = m_api + '/mill/cetak_mill_profiles/';
                content += '<tr><td align="center" style="text-align:center" colspan="3"><a style="line-height: 14px;" class="green_btn" onclick="preview_cetak_surat(\'' + url+'?MillID='+context.data.ID + '\')" href="#"> ' + lang('Cetak') + ' </a></td></tr>';
            }   
            content += '</tbody></table>';
            content += '</div>';
        content += '</div>';
    } else if (context.data.type == 'sme_sta_500' || context.data.type == 'sme_sta_200' || context.data.type == 'sme_sta_100') {
        content = '';
        content += '<div class="'+context.data.type+' iw-container">';
            content += '<div class="iw-content">';
            content += '<table border="0" width="100%"><tbody>';
            content += '<tr><td style="width:100px; vertical-align: top;">' + lang('Name') + '</td><td style="vertical-align: top;">:</td><td> ' + context.data.MemberName + '</td></tr>';
            content += '<tr><td style="width:100px; vertical-align: top;">' + lang("Role") + '</td><td style="vertical-align: top;">:</td><td> ' + context.data.RoleName + '</td></tr>';
            if (context.data.type == 'sme_plantation') {
                content += '<tr><td>' + lang('Garden Nr') + '</td><td>: </td><td> ' + context.data.GardenNr + '</td></tr>';
                content += '<tr><td>' + lang('Luas lahan') + '</td><td>: </td><td> ' + number_format(context.data.AreaHa,1,'.',',') + ' Ha</td></tr>'
                content += '<tr><td>' + lang('Production') + '</td><td>: </td><td> ' + number_format(context.data.Production,1,'.',',') + '</td></tr>'
            }
            if(context.data.Village){
                content += '<tr><td>' + lang('Village') + '</td><td>: </td><td> ' + context.data.Village + '</td></tr>';
            }
            if(context.data.SubDistrict){
                content += '<tr><td>' + lang('Sub District') + '</td><td>: </td><td> ' + ((context.data.SubDistrict) ? context.data.SubDistrict : '') + '</td></tr>';
            }
            content += '<tr><td>' + lang('Address') + '</td><td>: </td><td> ' + ((context.data.Address) ? context.data.Address : '') + '</td></tr>';

            // if (context.data.type == 'sme') {
                var url = m_api + '/grower/cetak_agent_profiles/MemberID/'+context.data.MemberID;
                content += '<tr><td align="center" style="text-align:center" colspan="3"><a style="line-height: 14px;" class="green_btn" onclick="preview_cetak_surat(\'' + url+'\')" href="#"> ' + lang('Cetak') + ' </a> <a style="line-height: 14px;" class="green_btn" onclick="show_farmer_sme(\'' + context.data.MemberID+'\')" href="#"> ' + lang('Show Farmer') + ' </a></td></tr>';
            // }   
            content += '</tbody></table>';
            content += '</div>';
        content += '</div>';
    }

    var info = '<div class="marker_info none" id="marker_info">' +
            '<div class="info info_'+context.data.color+'" id="info">'+
            '<h2>'+ ((context.data.type == 'farmer' || context.data.type == 'farmer_certified' || context.data.type == 'bank_farmer_1' || context.data.type == 'bank_farmer_2' || context.data.type == 'bank_farmer_3') ? 'X' : context.data.label) +'<span></span></h2>' +
            '<span>'+ content +'</span>' +
            // '<a href="'+ 'context.url_point' + '" class="green_btn">More info</a>' +
            '<span class="arrow"></span>' +
            '</div>' +
            '</div>';
    return info;
}
var $map_canvas         = $('#map_canvas');
var category_toolbar    = $('#category-toolbar')[0];
var farmer_data = {};
var farmer_edit = {};
var farmer_edit_copy = {};
var bounds          = new google.maps.LatLngBounds();
var edit_mode = false;

var Provinces = Ext.create('Ext.data.Store', {
    extend: 'Ext.data.Model',
    fields: ['id', 'label'],
    autoLoad: true,
    // pageSize: 10,
    proxy: {
        type: 'ajax',
        url: m_api+'/data_adm/polygon/province_list',
        reader: {
            type: 'json',
            root: 'data'
        }
    }
});

var Districts = Ext.create('Ext.data.Store', {
    extend: 'Ext.data.Model',
    fields: ['id', 'label'],
    autoLoad: false,
    // pageSize: 10,
    proxy: {
        type: 'ajax',
        url: m_api+'/data_adm/polygon/district_list',
        reader: {
            type: 'json',
            root: 'data'
        }
    }
});

var SubDistricts = Ext.create('Ext.data.Store', {
    extend: 'Ext.data.Model',
    fields: ['id', 'label'],
    autoLoad: false,
    // pageSize: 10,
    proxy: {
        type: 'ajax',
        url: m_api+'/data_adm/polygon/subdistrict_list',
        reader: {
            type: 'json',
            root: 'data'
        }
    }
});

Ext.onReady(function() {
    var tab = Ext.create('Ext.Panel', {
        renderTo: 'ext-content',
        height: 80,
        frame: false,
        items: [{
            xtype: 'fieldset',
            title: lang('Filter'),
            items: [{
                xtype: 'form',
                // height: 135,
                padding: 5,
                fieldDefaults: {
                    labelAlign: 'center',
                    labelWidth: 160,
                    anchor: '100%'
                },
                items: [
                {
                    layout: 'column',
                    border: false,
                    items: [
                    {
                        columnWidth: 0.25,
                        layout: 'form',
                        padding: 3,
                        border: false,
                        items: [{
                            emptyText: '-- ' + lang('Provinsi') + '--',
                            id: 'Provinsi',
                            name: 'Provinsi',
                            xtype: 'combo',
                            labelWidth: 60,
                            store: Provinces,
                            displayField: 'label',
                            valueField: 'id',
                            queryMode: 'local',
                            listeners: {
                                change: function (cb, nv, ov) {
                                    Ext.getCmp('Kabupaten').setValue('');
                                    Districts.load({params: {ProvinceID: nv}});
                                }
                            }
                        }]
                    }, {
                        columnWidth: 0.25,
                        layout: 'form',
                        padding: 3,
                        border: false,
                        items: [{
                            id: 'Kabupaten',
                            name: 'Kabupaten',
                            xtype: 'combo',
                            emptyText: '-- ' + lang('Kabupaten') + '--',
                            labelWidth: 70,
                            store: Districts,
                            displayField: 'label',
                            valueField: 'id',
                            queryMode: 'local',
                            listeners: {
                                change: function (cb, nv, ov) {
                                    Ext.getCmp('Kecamatan').setValue('');
                                    SubDistricts.load({params: {DistrictID: nv}});
                                }
                            }
                        }]
                    }, {
                        columnWidth: 0.25,
                        layout: 'form',
                        padding: 3,
                        border: false,
                        items: [{
                            id: 'Kecamatan',
                            name: 'Kecamatan',
                            emptyText: '-- ' + lang('Kecamatan') + '--',
                            xtype: 'combo',
                            labelWidth: 40,
                            store: SubDistricts,
                            displayField: 'label',
                            valueField: 'id',
                            queryMode: 'local'
                        }]
                    }, {
                        columnWidth: 0.25,
                        layout: 'form',
                        padding: 3,
                        border: false,
                        items: [{
                            id: 'Member',
                            name: 'Member',
                            emptyText: lang('Member'),
                            xtype: 'textfield',
                            labelWidth: 40,
                        }]
                    }, {
                        xtype: 'button',
                        padding: 7,
                        text : lang('View'),
                        handler: function () {
                            getFarmerPolygon();
                        }
                    }
                    ]
                },
                ],
            }]
        }
        ]
    })
});

$(function () {
    // set map size to fit screen
    setMapSize();
    init_map();
    $('#check_all').on('change', function(event) {
        closeInfoBox();
        $('.check_farmer').prop('checked', $(this).is(':checked')).change();
        $('.skop[name="farmer"]')
            .prop('checked', $(this).is(':checked'));
        $('.skop[name="farmer_polygon"]')
            .prop('checked', $(this).is(':checked'));
    });
    $('#farmer_checklist').on('change', 'input', function(event) {
        closeInfoBox();
        if ($(this).is(':checked')) {
            showObject('farmer', 'farmer_'+$(this).val());
            showObject('farmer_polygon', 'polygon_'+$(this).val());
        } else {
            hideObject('farmer', 'farmer_'+$(this).val());
            hideObject('farmer_polygon', 'polygon_'+$(this).val());
        }
    });
});

function clickOn(elm) {
    closeInfoBox();
    var map = $map_canvas.gmap3("get");
    // $('#check_all').prop('checked', elm.is(':checked')).change();
    if (elm.is(':checked')) {
        elm.prop('checked', 'checked');
        showObject(elm.attr('name'));
        // showObject(elm.attr('name')+'_polygon');
    } else {
        elm.removeProp('checked');
        hideObject(elm.attr('name'));
        // hideObject(elm.attr('name')+'_polygon');
    }
}
function set_category() {
    var categories = [
        {id: 1, key: 'farmer', label: lang('Farmer')},
        {id: 5, key: 'farmer_polygon', label: lang('Farmer Polygon')},
        {id: 2, key: 'protected_forest', label: lang('Protected Forest')},
        {id: 3, key: 'peatland', label: lang('Peatland')},
        {id: 4, key: 'conservation_area', label: lang('Conservation Area')},
    ];
    setTimeout(function(){
        $('#category-toolbar ul').html('');
        $.each(categories, function(index, val) {
            tpl = '<label><li class="list-group-item"><input type="checkbox" disabled="disabled" class="skop" name="'+val.key+'" value="'+val.id+'"> <img style="width:32px;" src="'+base_url+'img/maps/'+val.key+'.png" alt=""> '+val.label+' <span class="skop_total"></span></li></label>';
            $('#category-toolbar ul').append(tpl);
        });
        $('#category-toolbar').on('change', '.skop', function () {
            clickOn($(this));
        });
    }, 200);
}

function init_map() {
    $map_canvas.gmap3({
        map: {
            options: {
                center: [-2.0836809794977484, 113.63967449468988],
                zoom: 5,
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
                mapTypeId: google.maps.MapTypeId.HYBRID
            }
            , callback: function (map) {
                if (category_toolbar) {
                    map.controls[google.maps.ControlPosition.BOTTOM_LEFT].push(category_toolbar);
                    setTimeout(function(){
                        $(category_toolbar).removeClass('hidden');
                    }, 200)
                }
            }
        }
    }
    );
    set_category();
}
function setMapSize() {
    if (screenfull.isFullscreen) {
        height = screen.height;
    } else {
        // height = window.innerHeight - 150;
        height = window.innerHeight;
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
function clearMap() {
    $map_canvas.gmap3({
        clear: {
            name: ['marker', 'line', 'polyline', 'polygon']
        }
    })
}
function destroyMap() {
    $map_canvas.gmap3({
        clear: {
            name: ['marker', 'line', 'polyline', 'polygon']
        }
    })
    $map_canvas.gmap3('destroy');
}
function showObject(tag, id) {
    var prop = {};
    prop.tag = tag;
    prop.all = true;
    if (typeof(id) !== 'undefined') {
        prop.id = id
    }
    var map = $map_canvas.gmap3('get');
    var object = $('#map_canvas').gmap3({
        get: prop
    });
    // console.log(object);

    $.each(object, function (idx, elm) {
        elm.setMap(map);
    })
}
function hideObject(tag, id) {
    var prop = {};
    prop.tag = tag;
    prop.all = true;
    if (typeof(id) !== 'undefined') {
        prop.id = id
    }
    var object = $('#map_canvas').gmap3({
        get: prop
    });

    $.each(object, function (idx, elm) {
        elm.setMap(null);
    })
}
var farmers = [];
function getFarmerPolygon(callback) {
    farmer_data = {};
    destroyMap();
    init_map();

    var ProvinceID      = Ext.getCmp('Provinsi').getValue();
    var DistrictID      = Ext.getCmp('Kabupaten').getValue();
    var SubDistrictID   = Ext.getCmp('Kecamatan').getValue();
    var Keyword         = Ext.getCmp('Member').getValue();
    $('#farmer_checklist').html('');
    // if (!SubDistrictID) {
    //     Ext.MessageBox.alert('Warning', lang('Silahkan pilih Kecamatan'));
    //     return false;
    // }
    // reset array
    farmers = [];

    $.ajax({
        url: m_api+'/data_adm/polygon/farmer_polygon',
        type: 'GET',
        dataType: 'json',
        data: {
            ProvinceID: ProvinceID,
            DistrictID: DistrictID,
            SubDistrictID: SubDistrictID,
            Keyword: Keyword,
        },
    })
    .done(function(data) {
        // console.log("success");
        // render_list(data);
        draw_polygons(data);
    })
    .fail(function() {
        // console.log("error");
    })
    .always(function() {
        // console.log("complete");
    });
}

function set_checked(val, total) {
    $('.skop[name="' + val + '"]')
        .removeProp('disabled')
        .prop('checked', 'checked')
        .next().next('.skop_total').text(' (' + total + ')');
}

function set_disabled(val) {
    $('.skop[name="' + val + '"]')
        .removeProp('checked')
        .prop('disabled', 'disabled')
        .next().next('.skop_total').text(' (0)');
}
function render_list(farmers) {
    if (m_act_view_detail) {
        var max = Math.ceil(farmers.length/4);
        var tpl = '';
        for (var i = 0; i < 4; i++) {
            tpl += '<div class="col-sm-3">';
            for (var j = i*max; j <= (i+1)*max-1; j++) {
                if (farmers[j]) {
                    tpl += '<div class="am-checkbox">';
                    tpl += '<input id="check_'+(farmers[j].MemberID+'_'+farmers[j].PlotNr)+'" class="check_farmer" type="checkbox" checked="true" value="'+(farmers[j].MemberID+'_'+farmers[j].PlotNr)+'">';
                    tpl += '<label for="check_'+(farmers[j].MemberID+'_'+farmers[j].PlotNr)+'">['+farmers[j].MemberDisplayID+'] '+farmers[j].MemberName+' - '+farmers[j].PlotNr+'</label>';
                    tpl += '</div>';
                }
            }
            tpl += '</div>';
        }
        $('#farmer_checklist').html(tpl);
        $('#check_all').prop('checked', true);
    }
}
function draw_polygons(farmers) {
    clearMap();
    bounds          = new google.maps.LatLngBounds();
    var latLngs = [];
    var icon_path = base_url + 'img/maps/';

    $.each(farmers, function(index, val) {
        // calc area polygon

        // var point = [];
        // $.each(val.area, function(index, val) {
        //     point.push({'lat':val[0],'lng':val[1]});
        // });

        // var area_hectare = PlanarPolygonAreaMeters(point);
        // val.PolygonHa = area_hectare/10000;
        // end of calc area polygon

        var key = val.MemberID+'_'+val.PlotNr;
        farmer_data[key] = val;
        latLngs.push({
            latLng: [parseFloat(val.Latitude), parseFloat(val.Longitude)],
            data: {
                'MemberID':val.MemberID, 
                'MemberDisplayID':val.MemberDisplayID, 
                'MemberName':val.MemberName, 
                'PlotNr':val.PlotNr, 
                'SurveyNr':val.SurveyNr, 
                'GardenAreaHa':val.GardenAreaHa, 
                'PolygonHa':val.GardenAreaPolygon, 
                // 'PolygonHa':val.PolygonHa, 
                'Revision':val.Revision, 
                'Photo':val.Photo, 
                'VillageID':val.VillageID, 
            },
            tag: 'farmer',
            id: 'farmer_'+key,
            options: {
                icon: icon_path + "farmer.png"
            },
        });
        var myLatLng = new google.maps.LatLng(parseFloat(val.Latitude), parseFloat(val.Longitude));
        bounds.extend(myLatLng);
        var data = {'MemberID':val.MemberID, 'PlotNr':val.PlotNr, 'SurveyNr':val.SurveyNr, 'Revision':val.Revision};
        draw_polygon(val.area, key, null, null, 'farmer');
    });
    add_farmer_markers(latLngs);
    $('#map_canvas').gmap3("get")
            .fitBounds(bounds);
    setTimeout(function() {
        set_checked('farmer', latLngs.length);
        set_checked('farmer_polygon', latLngs.length);
    }, 1000);
}

function add_farmer_markers(latlngs) {
    $map_canvas.gmap3({
        marker: {
            values: latlngs
            , events: {
                click: function (marker, event, context) {
                    if (m_act_view_detail) {
                        var mapObject = $(this).gmap3("get");
                        closeInfoBox();
                        getInfoBox(context.data).open(mapObject, marker);
                    }
                }
            }
        }
    });
}
function draw_polygon(area, id, data, color, tag) {
    var polygonColor = 'yellow';
    if (color) {
        polygonColor = color;
    }
    $("#map_canvas").gmap3({
        polygon: {
            tag: tag+'_polygon',
            id: 'polygon_'+id,
            data: data,
            options: {
                strokeColor: polygonColor,
                strokeOpacity: 0.8,
                strokeWeight: 2,
                fillColor: polygonColor,
                fillOpacity: 0.35,
                paths: area
            },
        },
        // autofit:{}
    });
}

function closeInfoBox() {
    $('div.infoBox').remove();
}

function getInfoBox(data) {
    var content = '';
    //load photo
    var url_api_images = m_api_base_url + 'images/';
    var url_photo = m_api_base_url + 'images/member/';
    if (data.Photo !== 'null') {
        var url_load_photo = url_photo + data.VillageID.substring(0,2) + '/' + data.Photo;

        setTimeout(function() {$("#farmer_photo"+data.MemberID).attr("src",url_api_images+'rolling.gif');}, 20);
        $.get(url_load_photo)
        .done(function() {
            console.log('Done');
            image_url = url_load_photo;
            setTimeout(function() {$("#farmer_photo"+data.MemberID).attr("src",image_url);}, 100);
        })
        .fail(function() {
            console.log('Fail');
            setTimeout(function() {$("#farmer_photo"+data.MemberID).attr("src",url_api_images+'Photo/default-user.png');}, 100);
        })
    }
    content = '\
    <img id="farmer_photo'+data.MemberID+'" align="left" width="100px" style="padding:5px" src="' + m_api_base_url+'/images/Photo/default-user.png' + '" id="">\
    <div class="'+data.type+' iw-container">\
        <div class="iw-content">\
        <table border="0" width="100%"><tbody>\
        <tr><td width="100px">' + lang('ID Petani') + '</td><td>:</td><td> ' + data.MemberDisplayID + '</td></tr>\
        <tr><td>' + lang('Nama') + '</td><td>:</td><td> ' + data.MemberName + '</td></tr>\
        <tr><td>' + lang('Plantation Nr') + '</td><td>:</td><td> ' + data.PlotNr + '</td></tr>\
        <tr><td>' + lang('Survey Nr') + '</td><td>:</td><td> ' + data.SurveyNr + '</td></tr>\
        <tr><td>' + lang('Hectare Survey') + '</td><td>:</td><td> ' + data.GardenAreaHa + '</td></tr>\
        <tr><td>' + lang('Hectare Polygon') + '</td><td>:</td><td> ' + number_format(data.PolygonHa,2,'.',',') + '</td></tr>\
        ';
    content += '<tr><td align="center" style="text-align:center" colspan="3"><a id="btn_edit_polygon" style="line-height: 14px; display: '+(edit_mode?'none':'')+'" class="green_btn" onclick="$(this).hide(); $(\'#btn_save_polygon,#btn_revert_polygon\').show(); editPolygon('+data.MemberID+','+data.PlotNr+'); return false;" href="#"> ' + lang('Edit') + '</a><a id="btn_save_polygon" style="line-height: 14px; display: '+((edit_mode && data.MemberID == farmer_edit.MemberID && data.PlotNr == farmer_edit.PlotNr)?'':'none')+'" class="green_btn" onclick="$(this).hide(); $(\'#btn_revert_polygon\').hide(); $(\'#btn_edit_polygon\').show(); savePolygon('+data.MemberID+','+data.PlotNr+'); return false;" href="#"> ' + lang('Save') + '</a><a id="btn_revert_polygon" style="line-height: 14px; display: '+((edit_mode && data.MemberID == farmer_edit.MemberID && data.PlotNr == farmer_edit.PlotNr)?'':'none')+'" class="green_btn" onclick="$(this).hide(); $(\'#btn_save_polygon\').hide(); $(\'#btn_edit_polygon\').show(); revertPolygon('+data.MemberID+','+data.PlotNr+'); return false;" href="#"> ' + lang('Cancel') + '</a></td></tr>';
    // if (edit_mode === false) {
    //     $("#btn_edit_polygon").show();
    //     $("#btn_save_polygon").hide();
    // } else {
    //     $("#btn_edit_polygon").hide();
    //     $("#btn_save_polygon").hide();
    //     if (data.MemberID == farmer_edit.MemberID && data.PlotNr == farmer_edit.PlotNr) {
    //         $("#btn_save_polygon").show();
    //     }
    // }
    var info = '<div class="marker_info none" id="marker_info">' +
        '<div class="info info_green" id="info">'+
        '<h2>X<span></span></h2>' +
        '<span>'+ content +'</span>' +
        // '<a href="'+ 'context.url_point' + '" class="green_btn">More info</a>' +
        '<span class="arrow"></span>' +
        '</div>' +
        '</div>';
    // get_info_content(data);
    return new InfoBox({
        content: info,
        // disableAutoPan: true,
        maxWidth: 0,
        pixelOffset: new google.maps.Size(30, -195),
        // closeBoxMargin: '50px 200px',
        closeBoxMargin: "20px 3px 2px 2px",
        closeBoxURL: base_url+"img/close.gif",
        // closeBoxURL: '',
        isHidden: false,
        pane: 'floatPane',
        enableEventPropagation: true
    });
}

function clearObject(tag, id)
{
    $('#map_canvas').gmap3({
        clear: {
            tag: tag,
            id: id
        }
    });
}

function editPolygon(MemberID, PlotNr) {
    edit_mode = true;
    var key = MemberID+'_'+PlotNr;
    var marker = $('#map_canvas').gmap3({
        get: {
            tag: 'Farmer',
            id: 'Farmer_'+key
        }
    });
    farmer_edit_copy    = farmer_data[key];
    farmer_edit         = JSON.parse(JSON.stringify(farmer_data[key]));
    redrawPolygonEdit(key);
    return false;
}

function savePolygon(MemberID, PlotNr) {
    edit_mode = false;
    var key = MemberID+'_'+PlotNr;
    clearObject('Polygon', key);
    clearObject('FarmerEdit');
    if (JSON.stringify(farmer_edit) === JSON.stringify(farmer_edit_copy) ) {
        draw_polygon(farmer_edit.area, key, null, null, 'farmer');
        return false;
    }
    Ext.MessageBox.confirm('Message', lang('Apakah anda mau menyimpan data ini?'), function(btn) {
        if (btn == 'yes') {
            draw_polygon(farmer_edit.area, key, null, null, 'farmer');
            // send data to server
            sendFarmerData();
        } else {
            draw_polygon(farmer_edit_copy.area, key, null, null, 'farmer');
        }
    });

    return false;
}

function sendFarmerData() {
    $.post(m_api+'/data_adm/polygon/farmer_polygon', farmer_edit, function(data, textStatus, xhr) {
        if(data) {
            // set new Rev
            farmer_data[farmer_edit.MemberID+'_'+farmer_edit.PlotNr] = JSON.parse(JSON.stringify(farmer_edit));
            farmer_data[farmer_edit.MemberID+'_'+farmer_edit.PlotNr].Revision = data;
            // update HaPolygon on garden
            $.get(m_api+'geospatial/area_calc?farmer='+farmer_edit.MemberID+'&garden='+farmer_edit.PlotNr+'&survey='+farmer_edit.SurveyNr, function(data) {
                /*optional stuff to do after success */
            });
            Ext.MessageBox.alert('Info', lang('Polygon updated'));
        } else {
            Ext.MessageBox.alert('Info', lang('Failed to update polygon'));
        }
        farmer_edit = {};
        farmer_edit_copy = {};
    });
}

function revertPolygon(MemberID, PlotNr) {
    edit_mode = false;
    var key = MemberID+'_'+PlotNr;
    clearObject('Polygon', key);
    draw_polygon(farmer_edit_copy.area, key, null, null, 'farmer');
    clearObject('FarmerEdit');
    return false;
}

function redrawPolygonEdit(key) {
    clearObject('Polygon', key);
    draw_polygon(farmer_edit.area, key, null, 'red', 'farmer');
    var latLngs = [];
    $.each(farmer_edit.area, function(index, val) {
        var key = farmer_edit.MemberID+'_'+farmer_edit.PlotNr;
        latLngs.push({
            latLng: [parseFloat(val[0]), parseFloat(val[1])],
            data: {'MemberID':farmer_edit.MemberID, 'PlotNr':farmer_edit.PlotNr, 'index':index},
            tag: 'FarmerEdit',
            id: 'Farmer_Edit_'+key+'_'+index,
        });
    });
    addMarkerEdit(latLngs);
}

function addMarkerEdit(latLngs)
{
    $("#map_canvas").gmap3({
        marker: {
            values: latLngs,
            options: {
                draggable: true
            },
            events: {
                dragend: function(marker, event, context) {
                    farmer_edit.area[context.data.index][0] = marker.getPosition().lat();
                    farmer_edit.area[context.data.index][1] = marker.getPosition().lng();
                    redrawPolygonEdit(context.data.MemberID+'_'+context.data.PlotNr);
                },
            }
        }
    });
}

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
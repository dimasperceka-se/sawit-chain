var mode = '';
// var places          = [];
// var district_point  = [];
var bounds          = new google.maps.LatLngBounds();
var province, district, key;
var weather = [];

var $map_canvas     = $('#map_canvas');
var $province       = $('#province');
var $district       = $('#district');
var $date_start     = $('#date-start');
var $date_end       = $('#date-end');

var top_toolbar             = $('#top-toolbar')[0];
var bank_toolbar             = $('#bank-toolbar')[0];

var top_toolbar_supply      = $('#toolbar-supply-filter')[0];
var category_toolbar        = $('#category-toolbar')[0];
var bottom_toolbar          = $('#bottom-toolbar')[0];

var width           = Math.max(document.documentElement.clientWidth, window.innerWidth || 0);
var height          = Math.max(document.documentElement.clientHeight, window.innerHeight || 0);

var supplychain     = new Supplychain();
var infowindow      = new google.maps.InfoWindow();

$(function () {
    setMapSize();
    // $.get(url_province, function (data) {
    //     var opt = '<option value="">' + lang("Select Province") + '</option>';
    //     $.each(data.data, function (index, val) {
    //         opt += '<option value="' + val.id + '">' + lang(val.province) + '</option>';
    //     });
    //     $province.find('option').remove();
    //     $province.append(opt);
    //     $('#supply_province').find('option').remove();
    //     $('#supply_province').append(opt);
    // });

    $('#wrapper').off('change').off('click');

    $('#wrapper').on('click', '#btn_search', function () {
        closeInfoBox();
        province = $province.val();
        district = $district.val();
        if (district === '') {
            district = 'undefined'
        }

        key = $('#key').val();
        clear_map();

        if (district == 'undefined') {
            get_province_markers();
        } else {
            get_district_markers();
        }
        return false;
    });
    
    $('#wrapper').on('click', '#btn_search', function(event) {
        event.preventDefault();
        closeInfoBox();
        if (!$date_start.val() && !$date_end.val()) {
            Ext.MessageBox.show({
                title: 'Information',
                msg: lang('Please select dates!'),
                buttons: Ext.MessageBox.OK,
                animateTarget: 'mb9',
                icon: 'ext-mb-info'
            });
            return false;
        }
        get_supplychain_map();
    });
    
    $('#wrapper').on('change', '.skop:not(#all_skop)', function () {
        clickOn($(this));
    });

    $('#tab_supply').on('click', function(event) {
        event.preventDefault();
        init_map_supply();
        $('#tabs li').removeClass('active');
        $(this).parent().addClass('active');
    });

    $('#supply_tab a').click(function (e) {
        e.preventDefault()
        $(this).tab('show')
    })
    set_tooltip();
});

$(document).on(screenfull.raw.fullscreenchange, function () {
    setMapSize();
});

function set_tooltip () {
    setTimeout(function(){
        $('.tipso').tipso({
            position: 'top',
            background: '#2574A9',
            useTitle: false,
            speed: 100
        });
    }, 1000);
}

function init_map() {
    var map = '';
    if (act_default) {
        $('#button_tab_default').removeClass('hidden');
        if (map === '') map = 'default';
    }
    if (act_supplychain) {
        $('#button_tab_supply').removeClass('hidden');
        if (map === '') map = 'supply';
    }
    if (act_bank) {
        $('#button_tab_bank').removeClass('hidden');
        if (map === '') map = 'bank';
    }
    eval('init_map_'+map+'()');
    setTimeout(function() {
        // fixMap();
        setMapSize();
    }, 1000);
}

function init_map_() {
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
                mapTypeId: google.maps.MapTypeId.ROADMAP
            }
        }
    }
    )
}

function init_map_default () {
    if (mode == 'default') { return false }
    mode = 'default';
    destroyMap();
    defaultmap = new Defaultmap();
    defaultmap.init();
    categories = defaultmap.categories;
}

function init_map_supply () {
    if (mode == 'supply') { return false }
    mode = 'supply';
    destroyMap();
    supplychain     = new Supplychain();
    supplychain.init();
    setTimeout(function(){
        setMapSize();
    }, 1000);
}

function get_supplychain_map () {
    var province      = $('#supply_province').val();
    // var partner    = $('#supply_partner').val();
    var partner       = 8;
    var warehouse     = $('#supply_warehouse').val();
    var certification = $('#supply_certification').val();
    var ch            = $('#supply_ch').val();
    var start         = $date_start.val();
    var end           = $date_end.val();

    supplychain               = new Supplychain();
    supplychain.province      = province;
    supplychain.partner       = partner;
    supplychain.certification = certification;
    supplychain.warehouse     = warehouse;
    supplychain.start         = start;
    supplychain.end           = end;

    clear_map();
    $.ajax({
        url: url_supplychain,
        data: {
            province:  province,
            partner:  partner,
            warehouse:  warehouse,
            certification:  certification,
            start:  start,
            end:    end,
            ch:    ch,
        },
    })
    .done(function(data) {
        if (data.length > 0) {
            $.each(data, function(index, val) {
                if (Math.abs(parseFloat(val['1_latitude'])) > 0 && Math.abs(parseFloat(val['1_latitude'])) <= 90) {
                    var point_x = {};
                    point_x.id              = val['1_orgid'];
                    point_x.supply_id       = val['1_supplychainid'];
                    point_x.parent          = val['2_orgid']?val['2_orgid']:val['wh_orgid'];
                    point_x.level           = val['2_supplychainid']?3:2;
                    point_x.hidden          = val['2_supplychainid']?false:false;
                    point_x.detail_fetched  = false;
                    point_x.detail_shown    = false;
                    point_x.type            = val['1_orgtype'].toLowerCase().replace(' ', '_');
                    point_x.label           = lang(val['1_orgtype']);
                    point_x.name            = val['1_name'];
                    point_x.lat                  = parseFloat(val['1_latitude']);
                    point_x.lng                  = parseFloat(val['1_longitude']);
                    // point_x.bruto                = parseFloat(val.bruto);
                    // point_x.netto                = parseFloat(val.netto);
                    // point_x.transaction_count    = parseFloat(val.transaction_count);
                    // point_x.supply_count         = parseFloat(val.supply_count);
                    // point_x.batch_count          = parseFloat(val.batch_count);
                    supplychain.addPoint(point_x);

                    if (val['2_supplychainid'] && Math.abs(parseFloat(val['2_latitude'])) > 0 && Math.abs(parseFloat(val['2_latitude'])) <= 90) {
                        // lewat koperasi
                        var point_y = {};
                        point_y.id      = val['2_orgid'];
                        point_y.supply_id      = val['2_supplychainid'];
                        point_y.parent  = val['wh_orgid'];
                        point_y.level   = 2;
                        point_y.hidden          = false;
                        point_y.detail_fetched  = true;
                        point_y.detail_shown    = false;
                        point_y.type    = val['2_orgtype'].toLowerCase().replace(' ', '_');
                        point_y.label   = lang(val['2_orgtype']);
                        point_y.name    = val['2_name'];
                        point_y.lat                  = parseFloat(val['2_latitude']);
                        point_y.lng                  = parseFloat(val['2_longitude']);
                        // point_y.bruto                = parseFloat(val.bruto);
                        // point_y.netto                = parseFloat(val.netto);
                        // point_y.transaction_count    = parseFloat(val.transaction_count);
                        // point_y.supply_count         = parseFloat(val.supply_count);
                        // point_y.batch_count          = parseFloat(val.batch_count);
                        supplychain.addPoint(point_y);

                        var poly = {};
                        poly.id          = point_x.id;
                        poly.hidden      = point_x.hidden;
                        poly.type        = point_x.type;
                        poly.label       = lang(point_x.type);
                        poly.from        = point_x.name;
                        poly.to          = point_y.name;
                        poly.from_type   = point_x.type;
                        poly.to_type     = point_y.type;
                        // poly.bruto                = point_x.bruto;
                        // poly.netto                = point_x.netto;
                        // poly.transaction_count    = point_x.transaction_count;
                        // poly.supply_count         = point_x.supply_count;
                        // poly.batch_count          = point_x.batch_count;
                        poly.path = [
                            [parseFloat(point_x.lat), parseFloat(point_x.lng)],
                            [parseFloat(point_y.lat), parseFloat(point_y.lng)]
                        ];
                        supplychain.addPoly(poly);

                        point_x = point_y;
                    }
                    if (Math.abs(parseFloat(val['wh_latitude'])) > 0 && Math.abs(parseFloat(val['wh_latitude'])) <= 90) {
                        // warehouse
                        var point_y = {};
                        point_y.id      = val['wh_orgid'];
                        point_y.supply_id      = val['wh_supplychainid'];
                        point_y.parent  = null;
                        point_y.level   = 1;
                        point_y.hidden          = false;
                        point_y.detail_fetched  = true;
                        point_y.detail_shown    = false;
                        point_y.type    = 'warehouse';
                        point_y.label   = lang('Warehouse');
                        point_y.name    = val['wh_name'];
                        point_y.lat                  = parseFloat(val['wh_latitude']);
                        point_y.lng                  = parseFloat(val['wh_longitude']);
                        // point_y.bruto                = parseFloat(val.bruto);
                        // point_y.netto                = parseFloat(val.netto);
                        // point_y.transaction_count    = parseFloat(val.transaction_count);
                        // point_y.supply_count         = parseFloat(val.supply_count);
                        // point_y.batch_count          = parseFloat(val.batch_count);
                        supplychain.addPoint(point_y);

                        var poly = {};
                        poly.id          = point_x.id;
                        poly.hidden      = point_x.hidden;
                        poly.type        = point_x.type;
                        poly.label       = lang(point_x.type);
                        poly.from        = point_x.name;
                        poly.to          = point_y.name;
                        poly.from_type   = point_x.type;
                        poly.to_type     = point_y.type;
                        // poly.bruto                = point_x.bruto;
                        // poly.netto                = point_x.netto;
                        // poly.transaction_count    = point_x.transaction_count;
                        // poly.supply_count         = point_x.supply_count;
                        // poly.batch_count          = point_x.batch_count;
                        poly.path = [
                            [parseFloat(point_x.lat), parseFloat(point_x.lng)],
                            [parseFloat(point_y.lat), parseFloat(point_y.lng)]
                        ];
                        supplychain.addPoly(poly);

                        point_x = point_y;
                    }

                }

            });
            supplychain.renderPoints();
            supplychain.renderPoly();
        } else {
            $map_canvas.gmap3({
                clear: {
                    name: ['marker', 'line', 'polyline', 'polygon']
                }
                ,map: {
                    options: {
                        center: [-2.0836809794977484, 113.63967449468988],
                        zoom: 5,
                    }
                }
            })
            Ext.Msg.alert('Info', lang('Data not found'));
        }
    })
    .fail(function() {
        // console.log("error");
    })
    .always(function() {
        // console.log("complete");
    });

}

function get_province_markers() {
    district_point = [];
    $('#btn_search')
        .attr('disabled', 'disabled');
    $('#btn_search_text')
        .text('...');

    var url = '';
    if (mode === 'default') {
        url = url_districtmap;
    } else {
        url = url_districtmap_bank;
    }

    api_url = url + '/ProvinceID/' + province + '/DistrictID/' + district + '/Keyword/' + key + '/skop/1';

    var jqxhr = $.ajax(api_url)
        .done(function (data) {
            // district_point = data;
            if (data.total !== 0) {
                set_districtpoint(data);
            } else {
                Ext.Msg.alert('Info', lang('Data not found'));
            }
        })
        .fail(function () {
            // alert("error");
        })
        .always(function () {
            $('#btn_search')
                .removeAttr('disabled');
            $('#btn_search_text')
                .text(lang('Search'));
        });
}

function get_district_markers() {
    places = [];
    $('#btn_search')
        .attr('disabled', 'disabled');
    $('#btn_search_text')
        .text('...');

    var url = '';
    if (mode === 'default') {
        url = url_geospatialbydistrict;
    } else {
        url = url_geospatialbydistrict_bank;
    }

    api_url = url + '/ProvinceID/' + province + '/DistrictID/' + district + '/Keyword/' + key + '/skop/1';

    $.ajax(api_url)
    .done(function (data) {
        // console.log(data);
        if (data.total != 0) {
            set_point(data);
        } else {
            Ext.Msg.alert('Info', lang('Data not found'));
        }
    })
    .fail(function () {
        alert("error");
    })
    .always(function () {
        $('#btn_search')
        .removeAttr('disabled');
        $('#btn_search_text')
        .text(lang('Search'));
    });
}

function set_districtpoint(district_point) {
    var icon_path = base_url + 'img/maps/';
    $map_canvas.gmap3({clear: {}});
    bounds = new google.maps.LatLngBounds();

    if (district_point.total > 0) {
        // farmer
        var latLngs = [];
        $.each(categories, function(index, val) {
            data_district = district_point[val.key];
            if (data_district.length > 0) {
                var data_count = 0;
                for (var i = data_district.length - 1; i >= 0; i--) {
                    var data = data_district[i];
                    data_count += parseInt(data.count);
                    if (Math.abs(parseFloat(data.Latitude)) > 90) {
                        continue;
                    }

                    data['type'] = val.key;
                    data['label'] = val.label;
                    latLngs.push({
                        latLng: [parseFloat(data.Latitude), parseFloat(data.Longitude)],
                        data: data,
                        tag: val.key,
                        options: {
                            icon: icon_path + val.key + ".png"
                        },
                    });

                    var myLatLng = new google.maps.LatLng(parseFloat(data.Latitude), parseFloat(data.Longitude));
                    bounds.extend(myLatLng);
                }
                set_checked(val.id, data_count);
            } else {
                set_disabled(val.id);
            }
        });

        add_district_markers(latLngs);
        $('#map_canvas').gmap3("get")
            .fitBounds(bounds);
    } else {
        set_disabled(1, 0);
        set_disabled(2, 0);
        set_disabled(3, 0);
        set_disabled(4, 0);
        set_disabled(5, 0);
        set_disabled(6, 0);
        set_disabled(7, 0);
    }

}

function set_point(places) {
    var icon_path = base_url + 'img/maps/';
    $map_canvas.gmap3({clear: {}});
    bounds = new google.maps.LatLngBounds();
    if (places.total > 0) {
        var bound_count = 0;
        // farmer
        var latLngs = [];

        $.each(categories, function(index, val) {
            var data_places = places[val.key];

            if (data_places.length > 0) {
                var ids = [];
                for (var i = data_places.length - 1; i >= 0; i--) {
                    var data = data_places[i];
                    if (Math.abs(parseFloat(data.Latitude)) > 90) {
                        continue;
                    }
                    // if ($.inArray(data.id, ids) == -1) {
                        ids.push(data.id);
                    // }

                    data['type'] = val.key;
                    data['label'] = val.label;
                    latLngs.push({
                        latLng: [parseFloat(data.Latitude), parseFloat(data.Longitude)],
                        data: data,
                        tag: val.key,
                        options: {
                            icon: icon_path + val.key + ".png"
                        },
                    });

                    if (parseFloat(data.Latitude) && parseFloat(data.Longitude)) {
                        var myLatLng = new google.maps.LatLng(parseFloat(data.Latitude), parseFloat(data.Longitude));
                        bounds.extend(myLatLng);
                    }
                }
                set_checked(val.id, ids.length);
            } else {
                set_disabled(val.id);
            }
        });

        add_markers(latLngs);
        $('#map_canvas').gmap3("get")
            .fitBounds(bounds);
    } else {
        set_disabled(1, 0);
        set_disabled(2, 0);
        set_disabled(3, 0);
        set_disabled(4, 0);
        set_disabled(5, 0);
        set_disabled(6, 0);
        set_disabled(7, 0);
    }
}

function set_checked(val, total) {
    $('.skop[value="' + val + '"]')
        .removeProp('disabled')
        .prop('checked', 'checked')
        .next().next('.skop_total').text(' (' + total + ')');
}

function set_disabled(val) {
    $('.skop[value="' + val + '"]')
        .removeProp('checked')
        .prop('disabled', 'disabled')
        .next().next('.skop_total').text(' (0)');
}

function add_markers(latlng, tag_name, icon_file, info) {

    $map_canvas.gmap3({
        marker: {
            // tag: tag_name,
            // data: info,
            values: latlng
            , events: {
                click: function (marker, event, context) {
                    var mapObject = $(this).gmap3("get");
                    closeInfoBox();
                    getInfoBox('point',context).open(mapObject, marker);
                }
            }
        }
    });
}

function closeInfoBox() {
    $('div.infoBox').remove();
}

function getInfoBox(type,item) {
    var content = '';
    if (type == 'point') {
        content = get_info_content(item);
    }
    // else if(type == 'weather'){
    //     content = get_info_weather(item);
    // }
    return new InfoBox({
        content: content,
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

function get_info_content (context) {
    var content = '';
    if (context.data.type == 'farmer'
        || context.data.type == 'farmer_certified'
        || context.data.type == 'bank_farmer_1'
        || context.data.type == 'bank_farmer_2'
        || context.data.type == 'bank_farmer_3'
        ) {

        if (context.data.Photo !== 'null') {
            $.get(url_photo + context.data.Photo)
            .done(function() {
                // console.log('Exist');
                image_url = url_photo + context.data.Photo;
                setTimeout(function() {$("#farmer_photo").attr("src",image_url);}, 100)
            }).fail(function() {
                // console.log('Not Exist');
            })
        }
        image_url = url_photo + 'default-user.png';
        content = '\
        <img id="farmer_photo" align="left" width="100px" style="padding:5px" src="' + image_url + '" id="">\
        <div class="'+context.data.type+' iw-container">\
            <div class="iw-content">\
            <table border="0" width="100%"><tbody>\
            <tr><td width="100px">' + lang('ID Petani') + '</td><td>:</td><td> ' + context.data.FarmerID + '</td></tr>\
            <tr><td>' + lang('Nama') + '</td><td>:</td><td> ' + context.data.FarmerName + '</td></tr>\
            <tr><td>' + lang('Garden Nr') + '</td><td>:</td><td> ' + context.data.GardenNr + '</td></tr>\
            <tr><td>' + lang('Survey Nr') + '</td><td>:</td><td> ' + context.data.SurveyNr + '</td></tr>\
            <tr><td>' + lang('Luas lahan') + '</td><td>:</td><td> ' + ((context.data.GardenHaUnCertified) ? context.data.GardenHaUnCertified : '') + ' Ha</td></tr>\
            <tr><td>' + lang('Produksi') + '</td><td>:</td><td> ' + ((context.data.totalProduksi) ? context.data.totalProduksi : '') + ' Kg</td></tr>\
            <tr><td>' + lang('Pohon') + '</td><td>:</td><td> ' + ((context.data.Pohon) ? context.data.Pohon : '') + ' ' + lang('Pohon') + '</td></tr>\
            <tr><td>' + lang('Produktivitas') + '</td><td>:</td><td> ' + ((context.data.Produktivitas) ? number_format(context.data.Produktivitas,0,',','.') : '') + ' ('+lang('Kg/Ha/Tahun')+')</td></tr>\
            <tr><td colspan="3">@ ' + context.data.Village +', '+ context.data.SubDistrict + '</td></tr>\
            ';
        if (context.data.type == 'farmer'
        || context.data.type == 'farmer_certified') {
            content += '<tr><td align="center" style="text-align:center" colspan="3"><a style="line-height: 14px;" class="green_btn" onclick="displayBeforeCetak(' + context.data.FarmerID + ')" href="#"> ' + lang('Cetak') + ' </a>\
            ';
        } else if (context.data.type == 'bank_farmer_1'
        || context.data.type == 'bank_farmer_2'
        || context.data.type == 'bank_farmer_3') {
            content += '<tr><td align="center" style="text-align:center" colspan="3"><a style="line-height: 14px;" class="green_btn" onclick="preview_farmer_summary(' + context.data.FarmerID + ')" href="#"> ' + lang('Preview') + ' </a>\
            ';
        }
        // console.log(context.data.is_area);
        if (context.data.is_area == 1) {
            content += '<a style="line-height: 14px;" class="green_btn" onclick="display_area(' + context.data.FarmerID + ',' + context.data.GardenNr + ',' + context.data.SurveyNr + ')" href="#"> ' + lang('Area') + '</a>';
        }

        content += '</td></tr>';
        content += '</tbody></table>\
            </div>\
        </div>\
        ';
        // <tr><td align="center" style="text-align:center" colspan="3"><a style="line-height: 14px;" class="btn btn-primary" onclick="displayBeforeCetak('+context.data.FarmerID+')" href="#"> '+lang('cetak')+' </a></td></tr>\
    } else if (context.data.type == 'demoplot') {
        content = '\
        <div class="demoplot iw-container">\
            <div class="iw-content">\
            <table border="0" width="100%" height="130px"><tbody>\
            <tr><td colspan="3"><b>' + lang('Kelompok') + '</b></td></tr>\
            <tr><td width="100px">' + lang('CPG ID') + '</td><td>:</td><td> ' + context.data.CPGid + '</td></tr>\
            <tr><td>' + lang('Nama Kelompok') + '</td><td>:</td><td> ' + context.data.GroupName + '</td></tr>\
            <tr><td>' + lang('Nama Pelatihan') + '</td><td>:</td><td> ' + context.data.CpgTrainings + '</td></tr>\
            <tr><td colspan="3"><b>' + lang('demoplot') + '</b></td></tr>\
            <tr><td width="100px">' + lang('Farmer ID') + '</td><td>:</td><td> ' + context.data.FarmerID + '</td></tr>\
            <tr><td>' + lang('Nama') + '</td><td>:</td><td> ' + context.data.FarmerName + '</td></tr>\
            <tr><td>' + lang('Garden Nr') + '</td><td>:</td><td> ' + context.data.GardenNr + '</td></tr>\
            <tr><td>' + lang('Luas lahan') + '</td><td>:</td><td> ' + ((context.data.GardenHaUnCertified) ? context.data.GardenHaUnCertified : '') + ' Ha</td></tr>\
            <tr><td>' + lang('Produksi') + '</td><td>:</td><td> ' + ((context.data.totalProduksi) ? context.data.totalProduksi : '') + ' Kg</td></tr>\
            <tr><td>' + lang('Pohon') + '</td><td>:</td><td> ' + ((context.data.Pohon) ? context.data.Pohon : '') + ' ' + lang('Pohon') + '</td></tr>\
            <tr><td>' + lang('Produktivitas') + '</td><td>:</td><td> ' + ((context.data.Produktivitas) ? number_format(context.data.Produktivitas,0,',','.') : '') + ' (' + lang('Kg/Ha/Tahun') + ')</td></tr>\
            </tbody></table>\
            </div>\
        </div>\
        ';
    } else if (context.data.type == 'nursery') {
        content = '\
        <div class="nursery iw-container">\
            <div class="iw-content">\
            <table border="0" width="100%" height="130px"><tbody>\
            <tr><td width="95px">' + lang('kategori') + '</td><td>:&nbsp;</td><td> ' + context.data.ObjTypeLabel + '</td></tr>\
            <tr><td>' + lang('Owner ID') + '</td><td>:&nbsp;</td><td> ' + context.data.ObjID + '</td></tr>\
            <tr><td>' + lang('Owner Name') + '</td><td>:&nbsp;</td><td> ' + context.data.ObjNameNya + '</td></tr>\
            <tr><td>' + lang('Caretaker') + '</td><td>:&nbsp;</td><td> ' + context.data.Caretaker + '</td></tr>\
            <tr><td>' + lang('NurseryNr') + '</td><td>:&nbsp;</td><td> ' + context.data.NurseryNr + '</td></tr>\
            <tr><td>' + lang('Established') + '</td><td>:&nbsp;</td><td> ' + context.data.Established + '</td></tr>\
            <tr><td>' + lang('Panjang') + '</td><td>:&nbsp;</td><td> ' + context.data.Panjang + ' M</td></tr>\
            <tr><td>' + lang('Lebar') + '</td><td>:&nbsp;</td><td> ' + context.data.Lebar + ' ' + ' M</td></tr>\
            <tr><td>' + lang('Capacity') + '</td><td>:&nbsp;</td><td> ' + context.data.Kapasitas + '</td></tr>\
            <tr><td align="center" style="text-align:center" colspan="3"><a style="line-height: 14px;" class="green_btn" onclick="printDirectly(\''+context.data.ObjTypeNya+'\','+context.data.ObjID+','+context.data.NurseryNr+');" href="#"> ' + lang('Cetak') + ' </a>\
            </tbody></table>\
            </div>\
        </div>\
        ';
    } else if (context.data.type == 'warehouse' || context.data.type == 'farmer_organization' || context.data.type == 'trader') {
        content = '\
        <div class="'+context.data.type+' iw-container">\
            <div class="iw-content">\
            <table border="0" width="100%"><tbody>\
            <tr><td style="width:100px; vertical-align: top;">' + lang('Name') + '</td><td style="vertical-align: top;">:</td><td> ' + context.data.CoopName + '</td></tr>\
            <tr><td>' + lang('Village') + '</td><td>:</td><td> ' + context.data.Village + '</td></tr>\
            <tr><td>' + lang('Sub District') + '</td><td>:</td><td> ' + ((context.data.SubDistrict) ? context.data.SubDistrict : '') + '</td></tr>\
            <tr><td>' + lang('Staff Name') + '</td><td>:</td><td> ' + ((context.data.StaffName) ? context.data.StaffName : '') + '</td></tr>\
            </tbody></table>\
            </div>\
        </div>\
        ';
    } else if (context.data.type == 'bank') {
        content = '\
        <div class="'+context.data.type+' iw-container" style="width:350px important!;">\
            <div class="iw-content">\
            <table border="0" width="100%"><tbody>\
            <tr>\
                <td style="width:80px; vertical-align: top;">' + lang('Name') + '</td><td style="vertical-align: top;">:</td><td style="vertical-align: top;padding-left:7px;"> ' + context.data.name + '</td>\
            </tr>\
            <tr>\
                <td style="width:80px; vertical-align: top;">' + lang('Address') + '</td><td style="vertical-align: top;">:</td><td style="vertical-align: top;padding-left:7px;"> ' + context.data.address + '</td>\
            </tr>\
            </tbody></table>\
            </div>\
        </div>\
        ';
    } else if (context.data.type == 'clonal') {
        if(context.data.ObjType == 'farmer') context.data.ObjType = 'Farmer';

        if(context.data.CertificationStatus == "No"){
            showHideTr = 'style="display:none;"';
        }else{
            showHideTr = '';
        }

        content = '\
        <div class="'+context.data.type+' iw-container">\
            <div class="iw-content">\
            <table border="0" width="100%"><tbody>\
            <tr><td style="width:190px">' + lang('Owner Type') + '</td><td>:</td><td> ' + lang(context.data.ObjType) + '</td></tr>\
            <tr><td>' + lang('Owner Name') + '</td><td>:</td><td> ' + context.data.name + '</td></tr>\
            <tr><td>' + lang('Garden Nr') + '</td><td>:</td><td> ' + context.data.GardenNr + '</td></tr>\
            <tr><td>' + lang('Year Established') + '</td><td>:</td><td> ' + context.data.EstablishedYear + '</td></tr>\
            <tr><td>' + lang('Certification Provider') + '</td><td>:</td><td> ' + context.data.CertificationStatus + ' </td></tr>\
            <tr '+showHideTr+'><td>' + lang('Date Applied for Certification') + '</td><td>:</td><td> ' + ((context.data.DateAppliedCertification!=null)?context.data.DateAppliedCertification:'') + ' ' + ' </td></tr>\
            <tr '+showHideTr+'><td>' + lang('Date Received Certification') + '</td><td>:</td><td> ' + ((context.data.DateReceivedCertification!=null)?context.data.DateReceivedCertification:'') + '</td></tr>\
            <tr><td>' + lang('Land Ownership Certificate') + '</td><td>:</td><td> ' + ((context.data.LandCertificate!=null)?context.data.LandCertificate:'') + '</td></tr>\
            <tr><td colspan="3"></td></tr>\
            <tr><td>' + lang('Cocoa Clone Total') + '</td><td>:</td><td> ' + context.data.TotalClonesNr + '</td></tr>\
            <tr><td>' + lang('Total of Shade Trees') + '</td><td>:</td><td> ' + context.data.TotalShadeTreesNr + '</td></tr>';

        // console.log(context.data.is_area);
        if (context.data.is_area == 1) {
            content += '<tr><td colspan="3"><a style="line-height: 14px;" class="green_btn" onclick="display_clone_area(' + context.data.ClonalID + ',' + context.data.GardenNr + ',' + context.data.SurveyNr + ')" href="#"> ' + lang('Area') + '</a></td></tr>';
        }
        content +='\
            </tbody></table>\
            </div>\
        </div>\
        ';
    }

    var info = '<div class="marker_info none" id="marker_info">' +
            '<div class="info info_'+context.data.type+'" id="info">'+
            '<h2>'+ ((context.data.type == 'farmer' || context.data.type == 'farmer_certified' || context.data.type == 'bank_farmer_1' || context.data.type == 'bank_farmer_2' || context.data.type == 'bank_farmer_3') ? 'X' : context.data.label) +'<span></span></h2>' +
            '<span>'+ content +'</span>' +
            // '<a href="'+ 'context.url_point' + '" class="green_btn">More info</a>' +
            '<span class="arrow"></span>' +
            '</div>' +
            '</div>';
    return info;
}

function printDirectly(ObjTypeNya,CPGid,NurseryNr){
    var urlNya = url_api+'/nursery/cetak_nursery_summary/'+ObjTypeNya+'/'+CPGid+'/'+NurseryNr+'/';
    preview_cetak_surat(urlNya);
}

function add_district_markers(latlng, tag_name, icon_file, info) {
    $map_canvas.gmap3({
        marker: {
            // tag: tag_name,
            // data: info,
            values: latlng
            , events: {
                mouseover: function (marker, event, context) {
                    var map = $(this).gmap3("get"),
                        infowindow = $(this).gmap3({get: {name: "infowindow"}});
                    var info = '';
                    info = '<strong>' + context.data.District + '</strong><br/>' + context.data.label + ' (' + context.data.count + ')';
                    if (infowindow) {
                        infowindow.open(map, marker);
                        infowindow.setContent(info);
                    } else {
                        $(this).gmap3({
                            infowindow: {
                                anchor: marker,
                                options: {content: info}
                            }
                        });
                    }
                },
                mouseout: function () {
                    var infowindow = $(this).gmap3({get: {name: "infowindow"}});
                    if (infowindow) {
                        infowindow.close();
                    }
                },
                click: function (marker, event, context) {
                    district = context.data.DistrictID;
                    $district.val(district);
                    get_district_markers();
                }
            }
        }
    });
}

function get_bank_farmer_info() {
    var bank_markers = $("#map_canvas").gmap3({
        get: {
            name: "marker",
            tag: 'bank',
            all: true
        }
    });
    // console.log(bank_markers);
    var min_distance = parseInt($('#radius').val());
    if (bank_markers) {
        // fit uncertified farmer
        var fit_farmers = [];
        var farmer_1 = $("#map_canvas").gmap3({
            get: {
                name: "marker",
                tag: 'bank_farmer_1',
                all: true
            }
        });
        // console.log(farmer_1);
        if (farmer_1) {
            $.each(farmer_1, function(index, farmer) {
                farmer_lat = farmer.position.lat();
                farmer_lng = farmer.position.lng();
                var key = farmer_lat+'_'+farmer_lng;
                if ($.inArray(key, fit_farmers) == -1) {
                    $.each(bank_markers, function(i, bank) {
                        if (distance(bank.position.lat(),bank.position.lng(),farmer_lat,farmer_lng,'K') <= min_distance) {
                            fit_farmers.push(key);
                            return false;
                        }
                    });
                }
            });
        }
        // console.log(fit_farmers);
        // fit certified farmer
        var fit_farmers_cert = [];
        var farmer_2 = $("#map_canvas").gmap3({
            get: {
                name: "marker",
                tag: 'bank_farmer_2',
                all: true
            }
        });
        // console.log(farmer_2);
        if (farmer_2) {
            $.each(farmer_2, function(index, farmer) {
                farmer_lat = farmer.position.lat();
                farmer_lng = farmer.position.lng();
                var key = farmer_lat+'_'+farmer_lng;
                if ($.inArray(key, fit_farmers_cert) == -1) {
                    $.each(bank_markers, function(i, bank) {
                        if (distance(bank.position.lat(),bank.position.lng(),farmer_lat,farmer_lng,'K') <= min_distance) {
                            fit_farmers_cert.push(key);
                            return false;
                        }
                    });
                }
            });
        }
        // console.log(fit_farmers_cert);
        // unfit farmer
        var unfit_farmers = [];
        var farmer_3 = $("#map_canvas").gmap3({
            get: {
                name: "marker",
                tag: 'bank_farmer_3',
                all: true
            }
        });
        // console.log(farmer_3);
        if (farmer_3) {
            $.each(farmer_3, function(index, farmer) {
                farmer_lat = farmer.position.lat();
                farmer_lng = farmer.position.lng();
                var key = farmer_lat+'_'+farmer_lng;
                if ($.inArray(key, unfit_farmers) == -1) {
                    $.each(bank_markers, function(i, bank) {
                        if (distance(bank.position.lat(),bank.position.lng(),farmer_lat,farmer_lng,'K') <= min_distance) {
                            unfit_farmers.push(key);
                            return false;
                        }
                    });
                }
            });
        }
        // console.log(unfit_farmers);
        $('#info_farmer_1').text(fit_farmers.length);
        $('#info_farmer_2').text(fit_farmers_cert.length);
        $('#info_farmer_3').text(unfit_farmers.length);
        $('#bank_info').removeClass('hidden');
    } else {
        $('#bank_info').addClass('hidden');
    }
}

function clear_map() {
    district_point = [];
    places = [];
    $('#map_canvas').gmap3({clear: {}});
    bounds = new google.maps.LatLngBounds();
}

function clearMarkerByTag (tag) {
    $("#map_canvas").gmap3({
        clear: {
            name: "marker",
            tag: tag
        }
    });
}

function clearCirle () {
    $("#map_canvas").gmap3({
        clear: {
            name: "circle",
        }
    });
}

function clickOn(elm) {
    closeInfoBox();
    var map = $map_canvas.gmap3("get");
    if (elm.is(':checked')) {
        elm.prop('checked', 'checked');
    } else {
        elm.removeProp('checked');
    }

    // set a filter function using the closure data "checkedColors"
    if ($map_canvas.gmap3({get: "clusterer"})) {
        var checked = {};
        $(".skop:not(#all_skop):checked").each(function (i, chk) {
            checked[$(chk).attr('name')] = true;
        });

        $('#map_canvas').gmap3({get: "clusterer"}).filter(function (data) {
            return data.tag in checked;
        });
    } else {
        $(".skop:not(#all_skop)").each(function (idx, chk) {
            var tag = $(chk).attr('name');
            var markers = $('#map_canvas').gmap3({
                get: {
                    name: 'marker',
                    tag: tag,
                    all: true
                }
            });

            $.each(markers, function (idx, elm) {
                elm.setMap($(chk).is(':checked') ? map : null);
            })
            if (tag == 'bank') {
                var circle = $('#map_canvas').gmap3({
                    get: {
                        name: 'circle',
                        tag: tag,
                        all: true
                    }
                });

                $.each(circle, function (idx, elm) {
                    elm.setMap($(chk).is(':checked') ? map : null);
                })
            }
        });
    }

}

function setMapSize() {
    if (screenfull.isFullscreen) {
        height = screen.height;
    } else {
        height = window.innerHeight - 150;
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
function destroyMap() {
    $map_canvas.gmap3({
        clear: {
            name: ['marker', 'line', 'polyline', 'polygon']
        }
    })
    $map_canvas.gmap3('destroy');
}
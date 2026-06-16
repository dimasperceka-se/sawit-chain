var mode = '';
var map;
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
    // set map size to fit screen
    setMapSize();
    $.get(url_province, function (data) {
        var opt = '<option value="">' + lang("Pilih Propinsi") + '</option>';
        $.each(data.data, function (index, val) {
            opt += '<option value="' + val.id + '">' + lang(val.province) + '</option>';
        });
        $province.find('option').remove();
        $province.append(opt);
        $('#supply_province').find('option').remove();
        $('#supply_province').append(opt);
    });
    $('#wrapper').off('change').off('click');
    // handle button search click
    $('#wrapper').on('click', '#btn_search', function () {
        closeInfoBox();
        province = $province.val();
        district = $district.val();
        if (district === '') {
            district = 'undefined'
        }

        key = $('#key').val();

        if (!province) {
            alert(lang('Please select province!'))
            return false;
        }

        clear_map();

        if (district == 'undefined') {
            get_province_markers();
        } else {
            get_district_markers();
        }
        return false;
    });
    $('#wrapper').on('click', '#btn_search_supply', function(event) {
        event.preventDefault();
        closeInfoBox();
        // if (!$('#supply_province').val()) {
        //     alert(lang('Please select province!'))
        //     return false;
        // }
        // if (!$('#supply_partner').val()) {
        //     alert(lang('Please select partner!'))
        //     return false;
        // }
        if (!$('#supply_warehouse').val()) {
            alert(lang('Please select mill!'))
            return false;
        }
        if (!$date_start.val() && !$date_end.val()) {
            alert(lang('Please select dates!'));
            return false;
        }
        get_supplychain_map();
    });
    $('#wrapper').on('change', '#province', function () {
        if ($(this).val() === '') {
            return false;
        }

        $.get(url_district + '?ProvinceID=' + $(this).val(), function (data) {
            var opt = '<option value="">' + lang("Semua Kabupaten") + '</option>';
            $.each(data.data, function (index, val) {
                opt += '<option value="' + val.id + '">' + val.district + '</option>';
            });
            $district.find('option').remove();
            $district.append(opt);
        });
    });

    $.get(url_api + '/geospatial_new/warehouse' + '?PartnerID=' + partnerid, function (data) {
        var opt = '<option value="">'+lang("Pilih MIll")+'</option>';
        $.each(data, function (index, val) {
            opt += '<option value="' + val.id + '">' + val.name + '</option>';
        });
        $('#supply_warehouse').find('option').remove();
        $('#supply_warehouse').append(opt);
    });

    $('#wrapper').on('change', '#district', function () {
        if ($(this).val() === '') {
            return false;
        }

        $.get(url_bank + '?DistrictID=' + $(this).val(), function (data) {
            var opt = '<option value="">'+lang('Pilih Bank')+'</option>';
            opt += '<option value="all">'+lang('Semua')+'</option>';
            $.each(data, function (index, val) {
                opt += '<option value="' + val.id + '">' + val.name + '</option>';
            });
            $('#bank').find('option').remove();
            $('#bank').append(opt);
        });
    });
    $('#wrapper').on('change', '.skop:not(#all_skop)', function () {
        clickOn($(this));
    });
    var elem_full = $('#map_canvas')[0];
    $('#wrapper').on('click', '#btn_full', function(event) {
        $('.tipso').tipso('hide');
        if (screenfull.isFullscreen) {
            screenfull.exit();
        } else {
            screenfull.request(elem_full);
        }
        set_tooltip();
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

function init_date () {
    $('#date-start').datepicker({
        format: 'yyyy-mm-dd'
    });
    $('#date-end').datepicker({
        format: 'yyyy-mm-dd'
    });
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

function init_map_supply () {
    if (mode == 'supply') { return false }
    mode = 'supply';
    destroyMap();
    supplychain     = new Supplychain();
    supplychain.init();
    // $map_canvas.gmap3({
    //     map: {
    //         options: {
    //             center: [-2.0836809794977484, 113.63967449468988],
    //             zoom: 5,
    //             //mapTypeControl: false,
    //             panControl: true,
    //             zoomControl: true,
    //             //scaleControl: false,
    //             streetViewControl: false,
    //             rotateControl: false,
    //             rotateControlOptions: false,
    //             overviewMapControl: false,
    //             OverviewMapControlOptions: false,
    //             scrollwheel: true,
    //             mapTypeId: google.maps.MapTypeId.ROADMAP
    //         }
    //         ,callback: function (map) {
    //             if (top_toolbar_supply) {
    //                 map.controls[google.maps.ControlPosition.TOP_CENTER].push(top_toolbar_supply);
    //                 setTimeout(function(){
    //                     $(top_toolbar_supply).removeClass('hidden');
    //                     init_date ();
    //                 }, 200)
    //             }
    //             if (bottom_toolbar) {
    //                 map.controls[google.maps.ControlPosition.BOTTOM_CENTER].push(bottom_toolbar);
    //                 setTimeout(function(){
    //                     $(bottom_toolbar).removeClass('hidden');
    //                 }, 200)
    //             }
    //             // if (map_default_toolbar) {
    //             //     map.controls[google.maps.ControlPosition.BOTTOM_CENTER].push(map_default_toolbar);
    //             //     setTimeout(function(){
    //             //         $(map_default_toolbar).removeClass('hidden');
    //             //     }, 200)
    //             // }
    //         }
    //     }
    // });
    setTimeout(function(){
        setMapSize();
    }, 1000);
}


function get_supplychain_map () {
    var province              = $('#supply_province').val();
    // var partner            = $('#supply_partner').val();
    var partner               = 9;
    var warehouse             = $('#supply_warehouse').val();
    var certification         = $('#supply_certification').val();
    var start                 = $date_start.val();
    var end                   = $date_end.val();
    
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
                    point_x.label           = lang('Agent');
                    point_x.name            = val['1_name'];
                    point_x.lat                  = parseFloat(val['1_latitude']);
                    point_x.lng                  = parseFloat(val['1_longitude']);
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
                        point_y.label   = lang('DO');
                        point_y.name    = val['2_name'];
                        point_y.lat                  = parseFloat(val['2_latitude']);
                        point_y.lng                  = parseFloat(val['2_longitude']);
                        supplychain.addPoint(point_y);

                        var poly = {};
                        poly.id          = point_x.id;
                        poly.hidden      = point_x.hidden;
                        poly.type        = point_x.type;
                        poly.label       = lang('DO');
                        poly.from        = point_x.name;
                        poly.to          = point_y.name;
                        poly.from_type   = point_x.type;
                        poly.to_type     = point_y.type;
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
                        point_y.detail_shown    = true;
                        point_y.type    = 'warehouse';
                        point_y.label   = lang('Mill');
                        point_y.name    = val['wh_name'];
                        point_y.lat                  = parseFloat(val['wh_latitude']);
                        point_y.lng                  = parseFloat(val['wh_longitude']);
                        supplychain.addPoint(point_y);

                        var poly = {};
                        poly.id          = point_x.id;
                        poly.hidden      = point_x.hidden;
                        poly.type        = point_x.type;
                        poly.label       = lang('Mill');
                        poly.from        = point_x.name;
                        poly.to          = point_y.name;
                        poly.from_type   = point_x.type;
                        poly.to_type     = point_y.type;
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

function closeInfoBox() {
    $('div.infoBox').remove();
}

function printDirectly(ObjTypeNya,CPGid,NurseryNr){
    var urlNya = url_api+'/nursery/cetak_nursery_summary/'+ObjTypeNya+'/'+CPGid+'/'+NurseryNr+'/';
    preview_cetak_surat(urlNya);
}

function clear_map() {
    district_point = [];
    places = [];
    $('#map_canvas').gmap3({clear: {}});
    bounds = new google.maps.LatLngBounds();
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
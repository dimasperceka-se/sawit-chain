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

var top_toolbar_supply      = $('#toolbar-supply-filter')[0];

var width           = Math.max(document.documentElement.clientWidth, window.innerWidth || 0);
var height          = Math.max(document.documentElement.clientHeight, window.innerHeight || 0);

// var supplychain     = new Supplychain();
var infowindow      = new google.maps.InfoWindow();

var category = [];
category[1] = {name:'Farmer Plasma', icon: 'farmer_plasma.png'}
category[2] = {name:'Direct Smallholder', icon: 'direct_smallholder.png'}
category[3] = {name:'Agent / Dealer / Vendor', icon: 'agent_dealer_vendor.png'}
category[4] = {name:'Owned Estate', icon: 'owned_estate.png'}
category[5] = {name:'External Estate', icon: 'external_estate.png'}

$(function () {
    // set map size to fit screen
    setMapSize();

    $('#wrapper').off('change').off('click');
    
    $('#wrapper').on('click', '#btn_search_supply', function(event) {
        event.preventDefault();
        closeInfoBox();
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

    $.get(m_url_api + '/geospatial_new/warehouse' + '?PartnerID=' + m_partnerid, function (data) {
        var opt = '<option value="">'+lang("Pilih MIll")+'</option>';
        $.each(data, function (index, val) {
            opt += '<option value="' + val.id + '">' + val.name + '</option>';
        });
        $('#supply_warehouse').find('option').remove();
        $('#supply_warehouse').append(opt);
    });

});

function init_date () {
    $('#date-start').datepicker({
        format: 'yyyy-mm-dd'
    });
    $('#date-end').datepicker({
        format: 'yyyy-mm-dd'
    });
}

function init_map_supply () {
    destroyMap();
    
    supplychain     = new Supplychain();
    supplychain.init();

    setTimeout(function(){
        setMapSize();
    }, 1000);
}


function get_supplychain_map () {
    var partner               = m_partnerid;
    var warehouse             = $('#supply_warehouse').val();
    var start                 = $date_start.val();
    var end                   = $date_end.val();
    
    supplychain               = new Supplychain();
    supplychain.partner       = partner;
    supplychain.warehouse     = warehouse;
    supplychain.start         = start;
    supplychain.end           = end;

    clear_map();
    $.ajax({
        url: m_url_api+'/maps/supplychain_new/supplychain',
        data: {
            id: warehouse,
            start: start,
            end: end,
        },
    })
    .done(function(data) {
        if (data.length > 0) {
            $.each(data, function(index, val) {
                if (Math.abs(parseFloat(val['parent_latitude'])) > 0 && Math.abs(parseFloat(val['parent_latitude'])) <= 90) {
                    // MILL
                    var point_x = {};
                    point_x.id             = val['parent_id'];
                    point_x.parent         = null;
                    point_x.level          = 1;
                    point_x.hidden         = false;
                    point_x.detail_fetched = true;
                    point_x.detail_shown   = true;
                    point_x.type           = val['parent_type'].toLowerCase().replace(' ', '_');
                    point_x.label          = lang(val['parent_type']);
                    point_x.name           = val['parent_name'];
                    point_x.lat            = parseFloat(val['parent_latitude']);
                    point_x.lng            = parseFloat(val['parent_longitude']);
                    point_x.icon           = 'mill.png';
                    supplychain.addPoint(point_x);

                    if (val['child_id'] && Math.abs(parseFloat(val['child_latitude'])) > 0 && Math.abs(parseFloat(val['child_latitude'])) <= 90) {
                        // lewat koperasi
                        var point_y            = {};
                        point_y.id             = val['child_id'];
                        point_y.member_id      = val['child_member_id'];
                        point_y.plantation     = val['child_plantation'];
                        point_y.parent         = point_x.id;
                        point_y.level          = 2;
                        point_y.hidden         = false;
                        point_y.has_detail     = val['child_type'] == 'farmer' ? false : true;
                        point_y.detail_fetched = false;
                        point_y.detail_shown   = false;
                        point_y.type           = val['child_type'].toLowerCase().replace(' ', '_');
                        point_y.label          = lang(val['CategoryName']);
                        point_y.name           = val['child_name'];
                        point_y.lat            = parseFloat(val['child_latitude']);
                        point_y.lng            = parseFloat(val['child_longitude']);
                        point_y.icon           = category[val['SupplybaseCategoryID']].icon;
                        supplychain.addPoint(point_y);

                        var poly       = {};
                        poly.id        = point_y.id+'-'+point_x.id;
                        poly.hidden    = point_y.hidden;
                        poly.type      = point_y.type;
                        poly.label     = lang(val['CategoryName']);
                        poly.from_id   = point_y.id;
                        poly.from      = point_y.name;
                        poly.to_id     = point_x.id;
                        poly.to        = point_x.name;
                        poly.from_type = point_y.type;
                        poly.to_type   = point_x.type;
                        poly.path = [
                            [parseFloat(point_y.lat), parseFloat(point_y.lng)],
                            [parseFloat(point_x.lat), parseFloat(point_x.lng)],
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
    height = window.innerHeight - 100;
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
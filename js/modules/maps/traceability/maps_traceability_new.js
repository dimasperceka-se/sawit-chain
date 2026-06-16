var $map_canvas = $('#map_canvas');
var map         = null;
var bounds      = new google.maps.LatLngBounds();
var infowindow  = new google.maps.InfoWindow();
var width       = Math.max(document.documentElement.clientWidth, window.innerWidth || 0);
var height      = Math.max(document.documentElement.clientHeight, window.innerHeight + 300 || 0);
var icon_path   = m_base_url + 'img/maps/';
var url_kml     = m_url_awss3+"/documents/kml/";
var TcData      = new TraceabilityData();


$(function () {
    // set map size to fit screen
    setMapSize();

    // Setup Filter
    getWarehouse();
    getTier2();
    getTier1();
    $('#filterWarehouse').on('change', function(event) {
        getTier2();
        getTier1();
    });
    $('#filterTier2').on('change', function(event) {
        getTier1();
    });
    setTimeout(function() {
        $('#filter-province').change();
    }, 1000);
    
    $('#filter-key').on('keypress', function(event) {
        if(event.which == 13) {
            event.preventDefault();
            return false;
        }
        
    });

})

$('.am-checkbox').on('click', function(event){
    if($('#filter-check-all').is(':checked')) {
        $('#filter-check-all').prop('checked',false);
    } else {
        $('#filter-check-all').prop('checked',true);
    }

});

// Klik Search
$('#filter-search').on('click', function(event) {
    if($('#filter-check-all').is(':checked')) {
        var show_all = '1';
    } else {
        var show_all = '0';
    }
    if(show_all=='1'){
        if($('#filterTier1').val()==''){
            event.preventDefault();
            Ext.Msg.alert('Warning', lang('Tier 1 Supplier must be filled in when Show farmers that are not selling is checked!'));
            return false;
        }
    }


    event.preventDefault();
    clear_map();
    closeInfoBox();


    var activeAjaxConnections = 0;
    var actors_count = 0;


    $.ajax({
        type: "GET",
        url: m_api+'/traceability_api/traceability_maps/get_relation',
        data: {
            WarehouseID: $('#filterWarehouse').val(),
            Tier1: $('#filterTier1').val(),
            Tier2: $('#filterTier2').val(),
            StartDate: $('#startDate').val(),
            EndDate: $('#endDate').val(),
            key: $('#filter-key').val(),
        },
        contentType: "application/json; charset=utf-8",
        dataType: "json",
        // async: false,
        beforeSend: function(xhr) {
            activeAjaxConnections++;
        },
        success: function(data) {
            if (data) {
                // reset Line & actor
                TcData.points=[]
                TcData.polylines=[]
                
                // total actor & fill actor span
                $.each(data.total, function(index, val) {
                    actors_count += val;
                    $(`#total-${index}` ).html(`(${val})`)
                })



                // Actor (Point)
                $.each(data.actor, function(index, val) {
                    var PointID = val.Tipe+ "-" + val.LocationID
                    TcData.addPoint({PointID, ...val})
                })
                TcData.renderPoints()

                // Transaction (Line)
                $.each(data.transaction, function(index, val) {
                    var LineID = val.From.Tipe + "-" + val.From.LocationID + "-" + val.To.Tipe + "-" + val.To.LocationID
                    TcData.addPolyline({LineID, ...val})
                })
                TcData.renderPolylines()

                // console.log(TcData)
            }
        },
        complete: function() {
            if($('#filter-check-all').is(':checked')) {
                var show_all = '1';
            } else {
                var show_all = '0';
            }
            if(show_all=='1'){
                $.ajax({
                    type: "GET",
                    url: m_api+'/traceability_api/traceability_maps/get_relation_farmer_not_sales',
                    data: {
                        WarehouseID: $('#filterWarehouse').val(),
                        Tier1: $('#filterTier1').val(),
                        Tier2: $('#filterTier2').val(),
                        StartDate: $('#startDate').val(),
                        EndDate: $('#endDate').val(),
                        key: $('#filter-key').val(),
                    },
                    contentType: "application/json; charset=utf-8",
                    dataType: "json",
                    success: function(data) {
                        if (data) {
                            $.each(data.actor, function(index, val) {
                                var PointID = val.Tipe+ "-" + val.LocationID
                                TcData.addPoint({PointID, ...val})
                            })
                            TcData.renderPoints()
                        }
                    },
                    complete: function() {
                        Ext.MessageBox.hide();
                    }
                });
            }else{
                Ext.MessageBox.hide();
            }

            activeAjaxConnections--;
            if (0 == activeAjaxConnections) {
                if (0 == actors_count) {
                    Ext.Msg.alert('Info', lang('No transaction found.'));
                }
            }
        }
    });

});


// Function ===========================================================================================
function init_map() {
    $map_canvas.gmap3({
        map: {
            options: {
                zoom: 3,
                center: [-4.433497, 119.949203],
                panControl: true,
                zoomControl: true,
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
function clear_map() {
    $map_canvas.gmap3({clear: {}});
    bounds = new google.maps.LatLngBounds();
}
function closeInfoBox() {
    $('div.infoBox').remove();
}

// Sidebar collapse
$('#sidebar-collapse').on('click', function(event) {
    $('#sidebar-filter').hide();
    $('#sidebar-button').show();
});

$('#sidebar-expand').on('click', function(event) {
    $('#sidebar-button').hide();
    $('#sidebar-filter').show();
});

// Setup Filter Function
function getWarehouse(callback) {
    Ext.Ajax.request({
        url: m_api+'/traceability_api/traceability_maps/get_combo_warehouse',
        method: 'GET',
        success: function(response){
            $('#filterWarehouse option').remove();
            var warehouse = JSON.parse(response.responseText);
            var options   = '<option value="">'+lang('All')+'</option>';
            $.each(warehouse, function(index, val) {
                options += '<option value="'+val.id+'">'+val.label+'</option>';
            });
            $('#filterWarehouse').append(options);
            $('#filterWarehouse').val('').trigger('change');
            callback;
        }
    });
}

function getTier2() {
    Ext.Ajax.request({
        url: m_api+'/traceability_api/traceability_maps/get_combo_tier_2',
        method: 'GET',
        params: {
            WarehouseID: $('#filterWarehouse').val()
        },
        success: function(response){
            var tier2 = JSON.parse(response.responseText);
            if(tier2[0].id!='' && tier2[0].id!=null && tier2[0].id!=undefined){
                $('#filterTier2 option').remove();
                var tier2 = JSON.parse(response.responseText);
                var options   = '<option value="">'+lang('All')+'</option>';
                $.each(tier2, function(index, val) {
                    options += '<option value="'+val.id+'">'+val.label+'</option>';
                });
                $('#filterTier2').append(options);
            }else{
                $('#filterTier2 option').remove();
                var options   = '<option value="">'+lang('All')+'</option>';
                $('#filterTier2').append(options);
            }
        },
        failure: function(response){
            $('#filterTier2 option').remove();
            var options   = '<option value="">'+lang('All')+'</option>';
            $('#filterTier2').append(options);
        }
    });
}

function getTier1() {
    Ext.Ajax.request({
        url: m_api+'/traceability_api/traceability_maps/get_combo_tier_1',
        method: 'GET',
        params: {
            WarehouseID: $('#filterWarehouse').val(),
            Tier2: $('#filterTier2').val()
        },
        success: function(response){
            var tier1 = JSON.parse(response.responseText);
            if(tier1[0].id!='' && tier1[0].id!=null && tier1[0].id!=undefined){
                $('#filterTier1 option').remove();
                var tier1 = JSON.parse(response.responseText);
                var options   = '<option value="">'+lang('All')+'</option>';
                $.each(tier1, function(index, val) {
                    options += '<option value="'+val.id+'">'+val.label+'</option>';
                });
                $('#filterTier1').append(options);
            }else{
                $('#filterTier1 option').remove();
                var options   = '<option value="">'+lang('All')+'</option>';
                $('#filterTier1').append(options);
            }
            $('#filterTier1').val('').trigger('change');
        },
        failure: function(response){
            $('#filterTier1 option').remove();
            var options   = '<option value="">'+lang('All')+'</option>';
            $('#filterTier1').append(options);
            $('#filterTier1').val('').trigger('change');
        }
    });
}

function init_date () {
    // $('#filter-date').datepicker({
    //     format: 'yyyy-mm-dd'
    // });
    $("#startDate").datepicker({
        format: "yyyy-mm-dd",
    });

    $("#endDate").datepicker({
        format: "yyyy-mm-dd",
    });

    $('.month').on('click', function(event) {
        $("#filter-date").datepicker('hide');
    });
}


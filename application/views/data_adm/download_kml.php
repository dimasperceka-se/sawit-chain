<style>    
    .dataTables_wrapper .dataTables_filter input{
        margin-bottom: 5px;
        height: 22px
    }

    .form-control{
        height: 24px;
        padding: 0px;
    }

    .form-group{
        margin-bottom: 5px;
    }

    div.dataTables_wrapper div.dataTables_length label select {
        padding: 0px;
    }
    .dataTables_filter {
        float: left !important;
    }
    div.dt-buttons {
        float: right;
               
    }
    button.dt-button, div.dt-button, a.dt-button, input.dt-button {
        padding: 0px;
        border: 0px transparent;
        background-color: rgba(0, 0, 0, 0);
        opacity: 0.3;
    }

    button.dt-button:hover, div.dt-button:hover, a.dt-button:hover, input.dt-button:hover{
        padding: 0px;
        border: 0px transparent;
        opacity: 1;
        text-decoration:none;
    }
   
    button.dt-button:hover:not(.disabled), div.dt-button:hover:not(.disabled), a.dt-button:hover:not(.disabled), input.dt-button:hover:not(.disabled) {
        border: 0px transparent;
        background-color: rgba(255, 255, 255, 1);
        opacity: 1;
    }
    div.dt-button-collection button.dt-button, div.dt-button-collection div.dt-button, div.dt-button-collection a.dt-button {
       text-align: left;
       opacity: 1;
       
    }
    #btn-filter{
        color: white;
        border: 1px solid #D1D1D1;    
        background: #95130b;
        border-radius: 0 4px 4px 0;
        width: 75px;
        height: 27px;
        margin: 0;
        padding: 0;
        font-size: 12px;
        line-height: 14px;
    }

    #download-kml-data tbody tr.selected > *{        
        background: #95130b !important;        
        box-shadow: none;
        color: white;

    }
</style>

<div class="main-content" >
    <div class="row" >
    <div style="padding: 10px;">   

    <div class="col-md-8" >
        <div class="row" style="margin-bottom: 5px;">
        <form class="col-md-2">
            <div class="form-group">
                <label >Province</label>
                <select class="form-control" id="filter-province" style="width:170px;"></select>
            </div>
        </form>
        <form class="col-md-2">
            <div class="form-group">
                <label > District</label>
                <select class="form-control" id="filter-district" style="width:170px;"></select>
            </div>
        </form>
        <form class="col-md-2">
            <div class="form-group">
                <label >Partner</label>
                <select class="form-control" id="filter-partner" style="width:170px;"></select>
            </div>            
        </form>
        <form class="col-md-2">
            <div class="form-group">
                <label>Status Polygon</label>
                <select class="form-control" id="filter-status-polygon" style="width:170px;">
                    <option value="verified">Verified Polygon Only</option>
                    <option value="new">New Polygon</option>
                    <option value="all">All Polygon</option>            
                </select>
            </div>
        </form>
        <form class="col-md-2">
            <div class="form-group">
                <label>Farmer Status</label>
                <select class="form-control" id="filter-farmer-status" style="width:170px;">
                    <option value="">All</option>
                    <option value="active">Active</option>
                    <option value="inactive">Inactive</option>
                </select>
            </div>
        </form>
        <form class="col-md-2">
            <div class="form-group">                
                <button class="btn btn-warning" id="btn-filter" style="margin-top: 16px;height: 27px;padding: 3px;">Load Data</button> <!--style="height: 22px;padding: 0px;"-->
            </div>
        </form>
        </div>        
        <table id="download-kml-data" class="table table-striped table-bordered" style="font-size: 12px;width: 100%;padding: 0px;">
            <thead>
                <tr>
                    <th>No</th>
                    <th>FarmerID</th>
                    <th>FarmNr</th>
                    <th>Farmer Name</th>
                    <th>Status Polygon</th>
                    <th>Ha Polygon</th>
                    <th>Partner</th>
                    <th>Farmer Status</th>                    
                    <th>Province</th>
                    <th>District</th>
                    <th>Sub district</th>
                    <th>Village</th>
                    <th>Latitude</th>
                    <th>Longitude</th>
                    <th>FarmerID_FarmNr</th>
                </tr>
            </thead>
            <tbody>
                
            </tbody>
        </table>
    </div>
    
    <div class="col-md-4">    
        <div id="map" style="width:100%;height:780px;"></div>
    </div>
    
    </div>
    </div>
</div>

<script src="<?php echo base_url() ?>js/infobox.js"></script>
<script type="text/javascript">
    var SILVER_MAP_STYLE = [
			{
				"elementType": "geometry",
				"stylers": [
				{
					"color": "#f5f5f5"
				}
				]
			},
			{
				"elementType": "labels.icon",
				"stylers": [
				{
					"visibility": "off"
				}
				]
			},
			{
				"elementType": "labels.text.fill",
				"stylers": [
				{
					"color": "#616161"
				}
				]
			},
			{
				"elementType": "labels.text.stroke",
				"stylers": [
				{
					"color": "#f5f5f5"
				}
				]
			},
			{
				"featureType": "administrative.land_parcel",
				"elementType": "labels.text.fill",
				"stylers": [
				{
					"color": "#bdbdbd"
				}
				]
			},
			{
				"featureType": "poi",
				"elementType": "geometry",
				"stylers": [
				{
					"color": "#eeeeee"
				}
				]
			},
			{
				"featureType": "poi",
				"elementType": "labels.text.fill",
				"stylers": [
				{
					"color": "#757575"
				}
				]
			},
			{
				"featureType": "poi.park",
				"elementType": "geometry",
				"stylers": [
				{
					"color": "#e5e5e5"
				}
				]
			},
			{
				"featureType": "poi.park",
				"elementType": "labels.text.fill",
				"stylers": [
				{
					"color": "#9e9e9e"
				}
				]
			},
			{
				"featureType": "road",
				"elementType": "geometry",
				"stylers": [
				{
					"color": "#ffffff"
				}
				]
			},
			{
				"featureType": "road.arterial",
				"elementType": "labels.text.fill",
				"stylers": [
				{
					"color": "#757575"
				}
				]
			},
			{
				"featureType": "road.highway",
				"elementType": "geometry",
				"stylers": [
				{
					"color": "#dadada"
				}
				]
			},
			{
				"featureType": "road.highway",
				"elementType": "labels.text.fill",
				"stylers": [
				{
					"color": "#616161"
				}
				]
			},
			{
				"featureType": "road.local",
				"elementType": "labels.text.fill",
				"stylers": [
				{
					"color": "#9e9e9e"
				}
				]
			},
			{
				"featureType": "transit.line",
				"elementType": "geometry",
				"stylers": [
				{
					"color": "#e5e5e5"
				}
				]
			},
			{
				"featureType": "transit.station",
				"elementType": "geometry",
				"stylers": [
				{
					"color": "#eeeeee"
				}
				]
			},
			{
				"featureType": "water",
				"elementType": "geometry",
				"stylers": [
				{
					"color": "#c9c9c9"
				}
				]
			},
			{
				"featureType": "water",
				"elementType": "labels.text.fill",
				"stylers": [
				{
					"color": "#9e9e9e"
				}
				]
			}
	]
		

var DARK_MAP_STYLE = [
			{
				"elementType": "geometry",
				"stylers": [
				{
					"color": "#212121"
				}
				]
			},
			{
				"elementType": "labels.icon",
				"stylers": [
				{
					"visibility": "off"
				}
				]
			},
			{
				"elementType": "labels.text.fill",
				"stylers": [
				{
					"color": "#757575"
				}
				]
			},
			{
				"elementType": "labels.text.stroke",
				"stylers": [
				{
					"color": "#212121"
				}
				]
			},
			{
				"featureType": "administrative",
				"elementType": "geometry",
				"stylers": [
				{
					"color": "#757575"
				}
				]
			},
			{
				"featureType": "administrative.country",
				"elementType": "labels.text.fill",
				"stylers": [
				{
					"color": "#9e9e9e"
				}
				]
			},
			{
				"featureType": "administrative.land_parcel",
				"stylers": [
				{
					"visibility": "off"
				}
				]
			},
			{
				"featureType": "administrative.locality",
				"elementType": "labels.text.fill",
				"stylers": [
				{
					"color": "#bdbdbd"
				}
				]
			},
			{
				"featureType": "poi",
				"elementType": "labels.text.fill",
				"stylers": [
				{
					"color": "#757575"
				}
				]
			},
			{
				"featureType": "poi.park",
				"elementType": "geometry",
				"stylers": [
				{
					"color": "#181818"
				}
				]
			},
			{
				"featureType": "poi.park",
				"elementType": "labels.text.fill",
				"stylers": [
				{
					"color": "#616161"
				}
				]
			},
			{
				"featureType": "poi.park",
				"elementType": "labels.text.stroke",
				"stylers": [
				{
					"color": "#1b1b1b"
				}
				]
			},
			{
				"featureType": "road",
				"elementType": "geometry.fill",
				"stylers": [
				{
					"color": "#2c2c2c"
				}
				]
			},
			{
				"featureType": "road",
				"elementType": "labels.text.fill",
				"stylers": [
				{
					"color": "#8a8a8a"
				}
				]
			},
			{
				"featureType": "road.arterial",
				"elementType": "geometry",
				"stylers": [
				{
					"color": "#373737"
				}
				]
			},
			{
				"featureType": "road.highway",
				"elementType": "geometry",
				"stylers": [
				{
					"color": "#3c3c3c"
				}
				]
			},
			{
				"featureType": "road.highway.controlled_access",
				"elementType": "geometry",
				"stylers": [
				{
					"color": "#4e4e4e"
				}
				]
			},
			{
				"featureType": "road.local",
				"elementType": "labels.text.fill",
				"stylers": [
				{
					"color": "#616161"
				}
				]
			},
			{
				"featureType": "transit",
				"elementType": "labels.text.fill",
				"stylers": [
				{
					"color": "#757575"
				}
				]
			},
			{
				"featureType": "water",
				"elementType": "geometry",
				"stylers": [
				{
					"color": "#000000"
				}
				]
			},
			{
				"featureType": "water",
				"elementType": "labels.text.fill",
				"stylers": [
				{
					"color": "#3d3d3d"
				}
				]
			}
    ];

    var ICON_PATH;
    var table;
    var isTableCreated = false;
    var activeAjaxConnections = 0;
    var params;
    var map;
    var zoom;
    let markers= [];
    <?php if (!empty($action)): ?>
        <?php foreach ($action as $key => $value): ?>
            var m_<?php echo $key ?> = "<?php echo $value ?>";
        <?php endforeach ?>
    <?php endif ?>

    $(function(){
        ICON_PATH = m_base_url + '/images/icons/maps/';
        $('#page_title, #breadcrumb_title').text('<?php echo $titlet ?>');
        $('#first-breadcrumb').text('<?php echo $breadcrumb_1 ?>');
        $('#second-breadcrumb').text('<?php echo $breadcrumb_2 ?>');
        setupRegionFilter();
        $('#download-kml-data').DataTable({ 
            scrollY:        "650px",
            scrollCollapse: true,
            paging:         false,
            scrollX:true,
            scrollCollapse: true,            
            columnDefs: [
                { width: 5, targets: 0 },
                { width: 5, targets: 1,
                    render: function (data, type, row, meta) {                    
                        return `<span id='${data}'>${data}</span>`;
                    } 
                },
                {
                    target: 12,
                    visible: false,
                    searchable: false,
                },
                {
                    target: 13,
                    visible: false,
                    searchable: false,
                },
                {
                    target: 14,
                    visible: false,
                    searchable: true,
                }
            ],
            fixedColumns: true,
            language: {
                searchPlaceholder: "FarmerID_FarmNr"
            },
            dom: 'Bfrtip',
            buttons: [
                {                    
                    text:      `<img src="${ICON_PATH}xls-download.png" style="height: 24px;width: 24px;">`,
                    titleAttr: 'Export Excel',
                    action: function ( e, dt, node, config ) {
                        Ext.MessageBox.show({
                            title: lang('Information'),
                            msg: lang('Please load the data'),
                            buttons: Ext.MessageBox.OK,
                            animateTarget: 'mb9',
                            icon: 'ext-mb-info'
                        });
                        return false;
                        
                    } 
                },
                {
                    extend:    'collection',
                    className: 'custom-html-collection',
                    text:      `<img src="${ICON_PATH}kml-download.png" style="height: 24px;width: 24px;">`,
                    titleAttr: 'Download KML',
                    buttons: [{
                        text: 'Export Center Polygon', 
                        action: function ( e, dt, node, config ) {
                            Ext.MessageBox.show({
                                title: lang('Information'),
                                msg: lang('Please load the data'),
                                buttons: Ext.MessageBox.OK,
                                animateTarget: 'mb9',
                                icon: 'ext-mb-info'
                            });
                            return false;
                        }                       
                    },
                    {

                        text: 'Export Polygon', 
                        action: function ( e, dt, node, config ) {
                            Ext.MessageBox.show({
                                title: lang('Information'),
                                msg: lang('Please load the data'),
                                buttons: Ext.MessageBox.OK,
                                animateTarget: 'mb9',
                                icon: 'ext-mb-info'
                            });
                            return false;
                        }                       
                    }
                    ]
                }                
            ]
        });
        initMap();
    })
    $('.custom-control-input').on('click',function (event) {
        
        var chartId = $(this).data('chartid');
        console.log(chartId);
        if($('#lcp-'+chartId).is( ":checked" )){
            $('#'+chartId).show()
        }else{
            $('#'+chartId).hide()
            
        }
    });

    function initMap(){
        $('#map').gmap3('destroy');
        $('#map').gmap3({
            map: {
                options: {
                    panControl: true,
                    zoomControl: true,
                    streetViewControl: true,
                    rotateControl: false,
                    rotateControlOptions: false,
                    overviewMapControl: false,
                    OverviewMapControlOptions: false,
                    scrollwheel: true,
					mapTypeId: google.maps.MapTypeId.HYBRID,
                    mapTypeControlOptions: {
						mapTypeIds: ["roadmap", "satellite", "hybrid", "terrain", "silver_map", "dark_map"],
					},
					maxZoom:18
                },
            }
        });
        map = $('#map').gmap3("get")
        const silverMapType = new google.maps.StyledMapType(SILVER_MAP_STYLE,{ name: "Silver" })
		const darkMapType = new google.maps.StyledMapType(DARK_MAP_STYLE,{ name: "Dark" })
	
		map.mapTypes.set("silver_map", silverMapType);
		map.mapTypes.set("dark_map", darkMapType);
    }

    $('#btn-filter').on('click', function(event) {
		event.preventDefault();
        clear_map();
        
        params = {
            'ProvinceID' : $('#filter-province').val(),
            'DistrictID' : $('#filter-district').val(),
            'SubDistrictID' : $('#filter-subdistrict').val(),
            'VillageID': $('#filter-villages').val(),
            'PartnerID': $('#filter-partner').val(),
            'FarmerID': '',
            'StatusPolygon': $('#filter-status-polygon').val(),
            'FarmerStatus': $('#filter-farmer-status').val(),
            'type': $('#filter-district').val(),
            'withFarmerID': 'true'
        }
        // Cek province harus terpilih
        if( !$('#filter-province').val() ) {            
            Ext.MessageBox.show({
                title: lang('Information'),
                msg: lang('Province must be selected'),
                buttons: Ext.MessageBox.OK,
                animateTarget: 'mb9',
                icon: 'ext-mb-info'
            });
            return false;
        } 
        
        if( !$('#filter-district').val() ) {            
            Ext.MessageBox.show({
                title: lang('Information'),
                msg: lang('District must be selected'),
                buttons: Ext.MessageBox.OK,
                animateTarget: 'mb9',
                icon: 'ext-mb-info'
            });
            return false;
        } 
        
        $.ajax({
			type: "POST",
			url: m_api+'/geospatial/get_map_kml',
			data: params,			
			dataType: "json",			
			success: function(response) {
				let tag = "actors-farm-area"
				let color = 'blue'				
				$.each(response, function( i, data ) { 
					const area = [];
					$.each(data.coordinates, function(i, v) {                        
						area[i] = {lat : parseFloat(v['lat']), lng : parseFloat(v['lng'])};
					});
					
					const id = tag + "-" + data.MemberDisplayID + '-' + data.FarmNr;
					draw_polygon(tag, id, area, data, color);
				})
            }            
        });
        
        $('#download-kml-data').DataTable().destroy();
        // empty and reinitialize table
        $('#download-kml-data').empty();
        $('#download-kml-data').append(`<thead><tr>
                    <th>No</th>
                    <th>FarmerID</th>
                    <th>FarmNr</th>
                    <th>Farmer Name</th>
                    <th>Status Polygon</th>
                    <th>Ha Polygon</th>
                    <th>Partner</th>
                    <th>Farmer Status</th>                    
                    <th>Province</th>
                    <th>District</th>
                    <th>Sub district</th>
                    <th>Village</th>
                    <th>Latitude</th>
                    <th>Longitude</th>
                    <th>FarmerID_FarmNr</th>
                </tr></thead>`);

        table = $('#download-kml-data').DataTable({
            info: false,
            responsive: true,           
            fixedColumns: true,
            ajax: {
                url: m_api+'/geospatial/get_datatable_kml',
                type: 'POST',
                data: params,
                beforeSend: function(xhr) {
                    Ext.MessageBox.show({
                        msg: lang('Please wait'),
                        progressText: lang('Generating'),
                        width: 300,
                        wait: true,
                        waitConfig: {interval: 200},
                        icon: 'ext-mb-info',
                        animateTarget: 'mb9'
                    });
                },
                complete: function() {				
				    Ext.MessageBox.hide();				
			    }
            },
            scrollY: "650px",            
            paging: false,
            scrollX: true,
            scrollCollapse: true,
            scroller: true,
            language: {
                searchPlaceholder: "FarmerID_FarmNr"
            },
            
            columnDefs: [
                { width: 5, targets: 0
                },
                { width: 5, targets: 1,
                    render: function (data, type, row, meta) {                    
                    return `<span id='${data}'>${data}</span>`;
                    } 
                },
                { width: 100, targets: 6 },
                {
                    target: 12,
                    visible: false,
                    searchable: false,
                },
                {
                    target: 13,
                    visible: false,
                    searchable: false,
                },
                {
                    target: 14,
                    visible: false,
                    searchable: true,
                }
            ],
            fixedColumns: true,
            dom: 'Bfrtip',
            buttons: [
                {                    
                    text:      `<img src="${ICON_PATH}xls-download.png" style="height: 24px;width: 24px;">`,
                    titleAttr: 'Export Excel',
                    action: function ( e, dt, node, config ) {
                        Ext.MessageBox.show({
                            msg: 'Please wait...',
                            progressText: 'Generating...',
                            width: 300,
                            wait: true,
                            waitConfig: {
                                interval: 200
                            },
                            icon: 'ext-mb-info', //custom class in msg-box.html
                            animateTarget: 'mb9'
                        });
                        $.ajax({
                            type: "POST",
                            url: m_api + '/geospatial/export_excel',
                            data: params,                            
                            dataType: "json",
                            success: function(result) {
                                if (result.filenya) {
                                    window.location = result.filenya
                                } else {
                                    Ext.Msg.alert('Info', lang('Can not generate file!'));
                                }
                            },
                            complete: function() {
                                Ext.MessageBox.hide();
                            }
                        });
                    } 
                },
                {
                    extend:    'collection',
                    className: 'custom-html-collection',
                    text:      `<img src="${ICON_PATH}kml-download.png" style="height: 24px;width: 24px;">`,
                    titleAttr: 'Download KML',
                    buttons: [{
                        text: 'Export Center Polygon', 
                            action: function ( e, dt, node, config ) {                                                                
                                Ext.MessageBox.show({
                                    msg: 'Please wait...',
                                    progressText: 'Generating...',
                                    width: 300,
                                    wait: true,
                                    waitConfig: {
                                        interval: 200
                                    },
                                    icon: 'ext-mb-info', 
                                    animateTarget: 'mb9'
                                });
                                $.ajax({
                                    type: "POST",
                                    url: m_api + '/geospatial/download_kml_point',
                                    data: params,                                    
                                    dataType: "json",
                                    success: function(result) {
                                        if (result.filenya) {                                            
                                            window.location = result.filenya
                                        } else {
                                            Ext.Msg.alert('Info', lang('Can not generate file!'));
                                        }
                                    },
                                    complete: function() {
                                        Ext.MessageBox.hide();
                                    }
                                });
                            }                       
                        },
                        {

                            text: 'Export Polygon', 
                            action: function ( e, dt, node, config ) {                               
                                Ext.MessageBox.show({
                                    msg: 'Please wait...',
                                    progressText: 'Generating...',
                                    width: 300,
                                    wait: true,
                                    waitConfig: {
                                        interval: 200
                                    },
                                    icon: 'ext-mb-info', 
                                    animateTarget: 'mb9'
                                });
                                $.ajax({
                                    type: "POST",
                                    url: m_api + '/geospatial/download_kml_polygon',
                                    data: params,                                    
                                    dataType: "json",
                                    success: function(result) {
                                        if (result.filenya) {                                            
                                            window.location = result.filenya
                                        } else {
                                            Ext.Msg.alert('Info', lang('Can not generate file!'));
                                        }
                                    },
                                    complete: function() {
                                        Ext.MessageBox.hide();
                                    }
                                });
                            }                       
                        }
                    ]
                }                
            ]            
        });


        $('#download-kml-data tbody').on('click', 'tr', function () {
            var data = table.row(this).data();
            console.log('You clicked on ' + data[12] + ":" + data[13]+"'s row");
            console.log('FarmerID ' + data[1]);
            
            if ($(this).hasClass('selected')) {
                $(this).removeClass('selected');
            } else {
                table.$('tr.selected').removeClass('selected');
                $(this).addClass('selected');
            }            
            map.setZoom(15);
            map.setCenter(new google.maps.LatLng(parseFloat(data[12]), parseFloat(data[13])));

            closeInfoBox();
            setMapOnAll(null);
            var falseMarker  = new google.maps.Marker({
                map: map,
                position: new google.maps.LatLng(parseFloat(data[12]), parseFloat(data[13])),
                visible: true
            });
            markers.push(falseMarker);
            var content = "";
            
            content += '<div style="background-color:white; padding:10px">';
            content +=      '<p><strong>'+lang('Farm Polygon')+'</strong></p>';
            content +=      '<table class="table table-condensed table-hover table-bordered table-striped" style="font-size:11px">';
            content +=          '<tbody>';
            content +=              '<tr><td style="width: 100px;">'+lang('ID')+'</td><td>'+data[1]+'</td></tr>';
            content +=              '<tr><td>'+lang('Farmer Name')+'</td><td>'+data[3]+'</td></tr>';
            content +=              '<tr><td>'+lang('Farm Nr')+'</td><td>'+data[2]+'</td></tr>';            
            content +=              '<tr><td>'+lang('Ha Polygon')+'</td><td>'+data[5]+'</td></tr>';
            content +=              '<tr><td>'+lang('Status Polygon')+'</td><td>'+ data[4] +'</td></tr>';
            content +=      '</table>';
            content += '</div>';

        
            new InfoBox({
                content: content,
                maxWidth: 0,
                closeBoxMargin: "5px 5px 2px 2px",
                closeBoxURL: ICON_PATH +"close.png",
                isHidden: false,
                pane: 'floatPane',
                enableEventPropagation: true,
            }).open(map, falseMarker);
        });
       
    });

    function setupRegionFilter() {
        Ext.Ajax.request({
            url: m_api+'/geospatial/kml_province',
            method: 'GET',
            success: function(rp, o) {
                var r = Ext.decode(rp.responseText);

                $('#filter-province').find('option').remove().end().append('<option value="">'+lang('Choose Province')+'</option>');
                $.each(r, function(index, val) {
                    $('#filter-province').append('<option value="'+val.id+'">'+val.label+'</option>');
                });
            },
            failure: function(rp, o) {
                $('#filter-province').find('option').remove().end().append('<option value="">'+lang('Choose Province')+'</option>');
                $('#filter-district').find('option').remove().end().append('<option value="">'+lang('Choose District')+'</option>');
            }
        });

        $(document).on('change', '#filter-province', function(e) {
            if(e.target.value == '') {
                $('#filter-district').find('option').remove().end().append('<option value="">'+lang('Choose District')+'</option>');
            } else {
                Ext.Ajax.request({
                    url: m_api+'/geospatial/kml_district',
                    method: 'GET',
                    params: {
                        ProvinceID: e.target.value
                    },
                    success: function(rp, o) {
                        var r = Ext.decode(rp.responseText);

                        $('#filter-district').find('option').remove().end().append('<option value="">'+lang('Choose District')+'</option>');
                        $.each(r, function(index, val) {
                            $('#filter-district').append('<option value="'+val.id+'">'+val.label+'</option>');
                        });
                    },
                    failure: function(rp, o) {
                        $('#filter-district').find('option').remove().end().append('<option value="">'+lang('Choose District')+'</option>');
                    }
                });
            }
        });

        $(document).on('change', '#filter-district', function(e) {
            if(e.target.value == '') {
                $('#filter-subdistrict').find('option').remove().end().append('<option value="">'+lang('Choose Sub District')+'</option>');
            } else {
                Ext.Ajax.request({
                    url: m_api+'/geospatial/kml_subdistrict',
                    method: 'GET',
                    params: {
                        DistrictID: e.target.value
                    },
                    success: function(rp, o) {
                        var r = Ext.decode(rp.responseText);

                        $('#filter-subdistrict').find('option').remove().end().append('<option value="">'+lang('Choose Sub District')+'</option>');
                        $.each(r, function(index, val) {
                            $('#filter-subdistrict').append('<option value="'+val.id+'">'+val.label+'</option>');
                        });
                    },
                    failure: function(rp, o) {
                        $('#filter-subdistrict').find('option').remove().end().append('<option value="">'+lang('Choose Sub District')+'</option>');
                    }
                });
            }

            if(e.target.value == '') {
                $('#filter-partner').find('option').remove().end().append('<option value="">'+lang('Choose Partner')+'</option>');
            } else {
                Ext.Ajax.request({
                    url: m_api+'/geospatial/kml_partner',
                    method: 'GET',
                    params: {
                        DistrictID: e.target.value
                    },
                    success: function(rp, o) {
                        var r = Ext.decode(rp.responseText);

                        $('#filter-partner').find('option').remove().end().append('<option value="">'+lang('Choose Partner')+'</option>');
                        $.each(r, function(index, val) {
                            $('#filter-partner').append('<option value="'+val.id+'">'+val.label+'</option>');
                        });
                    },
                    failure: function(rp, o) {
                        $('#filter-partner').find('option').remove().end().append('<option value="">'+lang('Choose Partner')+'</option>');
                    }
                });
            }
        });

        $(document).on('change', '#filter-subdistrict', function(e) {
            if(e.target.value == '') {
                $('#filter-villages').find('option').remove().end().append('<option value="">'+lang('Choose Villages')+'</option>');
            } else {
                Ext.Ajax.request({
                    url: m_api+'/geospatial/kml_village',
                    method: 'GET',
                    params: {
                        SubDistrictID: e.target.value
                    },
                    success: function(rp, o) {
                        var r = Ext.decode(rp.responseText);

                        $('#filter-villages').find('option').remove().end().append('<option value="">'+lang('Choose Villages')+'</option>');
                        $.each(r, function(index, val) {
                            $('#filter-villages').append('<option value="'+val.id+'">'+val.label+'</option>');
                        });
                    },
                    failure: function(rp, o) {
                        $('#filter-villages').find('option').remove().end().append('<option value="">'+lang('Choose Villages')+'</option>');
                    }
                });
            }
        });

        $(document).on('change', '#filter-villages', function(e) {
            
        });
    }

    function draw_polygon(tag, id, area, data, color){
        var polygonColor = color ? color : 'blue';
        var infowindow  = new google.maps.InfoWindow();

        $('#map').gmap3({
            polygon: {
                tag, id, data,
                options: {
                    strokeColor: polygonColor,
                    strokeOpacity: 0.8,
                    strokeWeight: 2,
                    fillColor: polygonColor,
                    fillOpacity: 0.35,
                    paths: area
                }, 
                events: {
                    click: function (object, event, context) {
                        var map = $(this).gmap3("get");
                        closeInfoBox();                        
                        setMapOnAll(null);
                        var falseMarker  = new google.maps.Marker({
                            map: map,
                            position: event.latLng,
                            visible: true
                        });
                        markers.push(falseMarker);
                        var content         = "";                        
                        var statusCheck = context.data.StatusCheck != 'partnerverified'? context.data.StatusCheck : "Verified by " + context.data.PartnerName

                        content += '<div style="background-color:white; padding:10px">';                        
                        content +=      '<p><strong>'+context.data.MemberDisplayID+'_'+context.data.PlotNr+'</strong></p>';
                        content +=      '<table class="table table-condensed table-hover table-bordered table-striped" style="font-size:11px">';
                        content +=          '<tbody>';
                        content +=              '<tr><td style="width: 100px;">'+lang('ID')+'</td><td>'+context.data.MemberDisplayID+'</td></tr>';
                        content +=              '<tr><td>'+lang('Farmer Name')+'</td><td>'+context.data.MemberName+'</td></tr>';
                        content +=              '<tr><td>'+lang('Farm Nr')+'</td><td>'+context.data.PlotNr+'</td></tr>';                       
                        content +=              '<tr><td>'+lang('Ha Polygon')+'</td><td>'+context.data.GardenAreaHa+'</td></tr>';
                        content +=              '<tr><td>'+lang('Status')+'</td><td>'+ statusCheck[0].toUpperCase() + statusCheck.slice(1) +'</td></tr>';
                        content +=      '</table>';
                        content += '</div>';
    
                    
                        new InfoBox({
                            content: content,
                            maxWidth: 0,
                            closeBoxMargin: "5px 5px 2px 2px",
                            closeBoxURL: ICON_PATH +"close.png",
                            isHidden: false,
                            pane: 'floatPane',
                            enableEventPropagation: true,
                        }).open(map, falseMarker);
                        
                        const rows = Array.from(document.querySelectorAll('tr.selected'));
                        rows.forEach(row => {
                            row.classList.remove('selected');
                        });
                        $('#'+context.data.MemberDisplayID).parent('td').parent('tr').addClass('selected');
                        var row_index = $('#'+context.data.MemberDisplayID).parent('td').parent('tr').index();
                        
                        var $row = $(table.row(row_index).node()); 
                        
                        table.context[0].nScrollBody.scrollTo(0,($row[0].offsetTop));
                        //console.log("index : " +row_index);                        
                    }
                }
            },
        });
    }

    function closeInfoBox(){
        $('div.infoBox').remove()
    }

    function clear_map(options) {
        var options = options || {};
        closeInfoBox();
        $('#map').gmap3({clear: options});
        if (options == {}) map.overlayMapTypes.clear()
        map.data.forEach(function(feature) {
            map.data.remove(feature);
        });     
        setMapOnAll(null);   
    }

    // Sets the map on all markers in the array.
    function setMapOnAll(map) {
        for (var i = 0; i < markers.length; i++) {
            markers[i].setMap(map);
        }
    }
</script>
var map = null;
var DATA_POINT = []
var ALL_EXTENT = new google.maps.LatLngBounds();

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
]


Ext.define('Koltiva.view.DataAdm.UploadFarmPolygon.MainView' ,{
    extend: 'Ext.panel.Panel',
    id: 'Koltiva.view.DataAdm.UploadFarmPolygon.MainView',
    style:'padding:10px 15px 15px 10px;margin:7px 0 0 0;',
    viewVar: false,
    setViewVar: function(value){
        this.viewVar = value;
    },
    renderTo: 'ext-content',
    listeners: {
        afterRender: function(){
            var thisObj = this;
            var totalData = 0;
            Ext.getCmp('Koltiva.view.DataAdm.UploadFarmPolygon.MainView').initMap()
        }
    },
    initComponent: function() {
        var thisObj = this;
        //Store
        thisObj.StoreGridUploadFarmPolygon = Ext.create('Koltiva.store.DataAdm.UploadFarmPolygon.GridUploadFarmPolygon', {});
        
        thisObj.items = [{
            layout: 'column',
            border: false,
            items: [{
                columnWidth: 0.6,
                items:[{
                    xtype:'panel',                
                    title:lang('Farm List'),
                    frame:true,
                    height:790,                    
                    items:[
                        Ext.create('Ext.grid.Panel', {
                            store: thisObj.StoreGridUploadFarmPolygon,
                            width: '100%',
                            minHeight: 550,
                            style: 'border:1px solid #CCC;',                            
                            loadMask: true,
                            selType: 'rowmodel',
                            dockedItems: [        
                                {
                                    xtype: 'toolbar',
                                    items: [
                                        Ext.create('Ext.form.Panel', {
                                            fileUpload: true,
                                            enctype:'multipart/form-data',
                                            id:'upload',
                                            items: [{            
                                                xtype: 'fileuploadfield',
                                                fieldLabel: 'Upload',
                                                labelWidth: 60,
                                                id: 'file',
                                                padding : 5,
                                                name: 'file',
                                                buttonText: 'Browse',
                                                listeners: {
                                                    'change': function(fb, v){
                                                        var form = Ext.getCmp('upload').getForm();
                                                        form.submit({
                                                            url: m_api+'/data_adm/upload_farm_polygon/import_kml_tmp',
                                                            waitMsg: lang('Sending and insert data temporary...'),
                                                            success: function(fp, o) {
                                                                setTimeout(function(){                                                                
                                                                    thisObj.StoreGridUploadFarmPolygon.load();
                                                                }, 500);
                                                            },
                                                            failure: function(form, action) {
                                                                Ext.MessageBox.alert('Error', action.result.msg);
                                                            }
                                                        });
                                                    }
                                                }
                                            }]
                                        }),
                                        {
                                            xtype: 'button',
                                            id:'clear-data',
                                            text: 'Clear Data',
                                            handler: function() {
                                                Ext.Ajax.request({
                                                    url: m_api+'/data_adm/upload_farm_polygon/farm_polygon_clear_data',
                                                    method: "POST",
                                                    waitMsg: 'Mengosongkan temporary data...',
                                                    success: function(fp, o) {
                                                        Ext.MessageBox.alert('Success', 'Temporary data dikosongkan.');
                                                        setTimeout(function(){                                                        
                                                            thisObj.StoreGridUploadFarmPolygon.load();
                                                        }, 500);
                                                    },
                                                    failure: function(response, opts) {
                                                        console.log('server-side failure with status code ' + response.status);
                                                        console.log('responseText: ' + response.responseText);
                                                    }
                                                });
                                            }
                                        },
                                        {
                                            xtype: 'label',
                                            html:`<a href="https://koltiva.s3.ap-southeast-1.amazonaws.com/gis/template/TEMPLATE_UPLOAD_INTERNAL.kml"><i>File Template [KML]</i></a>`
                                        },            
                                    ]
                                },
                            ],
                            listeners: {
                                select: function( record, index, eOpts ){
                                    let zoomTo = new google.maps.LatLng(parseFloat(index.data.Lat), parseFloat(index.data.Lng))
                                    bounds = new google.maps.LatLngBounds();
                                    bounds.extend(zoomTo);
                                    $('#map').gmap3("get").fitBounds(bounds);
                                }
                            },
                            columns: [
                                {
                                    text: 'No',
                                    xtype: 'rownumberer',
                                    width: 50
                                },
                                {
                                    text: 'ID', 
                                    dataIndex: 'MemberDisplayID',
                                    width: 150
                                },
                                {
                                    text: 'Name', 
                                    flex: 2,
                                    dataIndex: 'MemberName'
                                },
                                {
                                    text: 'Farm Nr', 
                                    flex: 1,
                                    dataIndex: 'PlotNr',
                                    align: 'center'
                                },
                                {
                                    text: 'Survey Nr', 
                                    flex: 1,
                                    dataIndex: 'SurveyNr',
                                    align: 'center'
                                }, 
                                {
                                    text: 'Revision', 
                                    flex: 1,
                                    dataIndex: 'Revision',
                                    align: 'center'
                                },
                                {
                                    text: 'HA Polygon', 
                                    flex: 1,
                                    dataIndex: 'AreaHa',
                                    align: 'center'
                                },                                 
                                {       
                                    text: 'Remark', 
                                    flex: 2,
                                    dataIndex: 'Remark'
                                },
                            ],
                            viewConfig: {
                                stripeRows: false,
                                getRowClass: function (record) {
                                    return record.get('Remark') == '-' ? 'no-error' : 'error';
                                }
                            },
                            bbar: new Ext.Toolbar({
                                renderTo: document.body,
                                width: '100%',                                
                                layout: 'column',
                                items: [{
                                    id:'summaryText',
                                    columnWidth: 0.6,                                    
                                    xtype: 'tbtext',                                    
                                    scale: 'large',
                                    text: lang('0 Remarked from 0 data')                                    
                                },{
                                    xtype:'button',                                    
                                    columnWidth: 0.2,
                                    id:'asaveButtonExcel',
                                    text: lang('Export to Excel'),
                                    textAlign:'center',
                                    margin: '5px',
                                    style: 'text-align:center',
                                    listeners: {
                                        'click': function(){
                                            $.ajax({
                                                type: "GET",
                                                url: m_api + '/data_adm/upload_farm_polygon/farm_polygon_export_excel',
                                                contentType: "application/json; charset=utf-8",
                                                dataType: "json",
                                                success: function(result) {
                                                    console.log(result)
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
                                },{
                                    xtype:'button',                                    
                                    columnWidth: 0.2,
                                    id:'asaveButton',
                                    text: lang('Update Farm Polygon'),
                                    textAlign:'center',
                                    margin: '5px',
                                    handler: function() {
                                        var form = Ext.getCmp('upload').getForm();                              
                                        form.submit({
                                            url: m_api+'/data_adm/upload_farm_polygon/update_farm_polygon',
                                            waitMsg: lang('Please wait...'),
                                            success: function(fp, o) {  
                                                var jsonResp = o.result;
                                                if (jsonResp.success) {
                                                    Ext.MessageBox.show({
                                                        title: 'Information',
                                                        msg: jsonResp.message,
                                                        buttons: Ext.MessageBox.OK,
                                                        animateTarget: 'mb9',
                                                        icon: 'ext-mb-success'
                                                    });
                                                }
                                                thisObj.StoreGridUploadFarmPolygon.load();
                                            },
                                            failure: function (fp, o) {
                                                var jsonResp = JSON.parse(o.response.responseText);
                                                
                                                Ext.MessageBox.hide();
                                                Ext.MessageBox.show({
                                                    title: 'Notifications',
                                                    msg: (jsonResp.message) ? jsonResp.message : lang('Failed to Upload'),
                                                    buttons: Ext.MessageBox.OK,
                                                    animateTarget: 'mb9',
                                                    icon: 'ext-mb-error'
                                                });                    
                                            }
                                        });                               
                                    }
                                }]
                            })                            
                        })
                    ]
            
                }]
            },{
                columnWidth: 0.4,
                style:'padding-left:7px;',
                items:[{
                    xtype:'panel',
                    id:'Koltiva.view.SpatialTools.MainView-MainMap',
                    title:lang('Farm Location'),
                    frame:true,
                    height:790,                  
                    items:[
                        {
                            xtype: 'component',
                            autoEl: {
                                html: '<div id="map" style="width:100%;height:790px;background:#e1e1e1;border:1px solid #e1e1e1;"></div>',
                            }
                        }
                    ]
                }]
            }]
        }];

        this.callParent(arguments);
    },
    initMap: function () {
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
    },
    addMarker: function(values) {
        DATA_POINT = values
        $('#map').gmap3({
            marker: {values}
        });
    },
    addPolygon:function(tag, id, area, data, color) {
        var polygonColor = color ? color : 'blue';

        $('#map').gmap3({
            polygon: {
                // tag, id,
                options: {
                    strokeColor: polygonColor,
                    strokeOpacity: 0.8,
                    strokeWeight: 2,
                    fillColor: polygonColor,
                    fillOpacity: 0.35,
                    paths: area
                },
                events: {
                    click:function(){
                        console.log("polygon")
                    }
                }
            }
        });
    },
    clearMap:function(){
        ALL_EXTENT = new google.maps.LatLngBounds();
        DATA_POINT=[]
        var options = options || {};
        // closeInfoBox();
        $('#map').gmap3({clear: options});
        map.data.forEach(function(feature) {
            map.data.remove(feature);
        });
    }
})
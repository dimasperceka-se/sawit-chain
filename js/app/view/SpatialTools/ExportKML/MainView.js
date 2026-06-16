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

var mymenu = new Ext.menu.Menu({
    items: [
        {
            text: 'Export Center Polygon',
            handler: function () {                
                $.ajax({
                    type: "GET",
                    url: m_api + '/map/farm_polygon_download_export_kml_point',
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
        }, {
            text: 'Export Polygon',
            handler: function () {                
                $.ajax({
                    type: "GET",
                    url: m_api + '/map/farm_polygon_download_export_kml_polygon',
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
    ]
});

popup = new Ext.Window ({
    title:'Information Window',
    layout:'form',
    width: 1200,
    closeAction:'close',
    target : document.getElementById('buttonId'),
    plain: true,
    items: [{
        html: '<div style="width: 100%;height: 670px;overflow-y: scroll;"><video style="width: 100%;;object-fit: contain;display: inline-block" controls><source src="https://koltiva.s3.ap-southeast-1.amazonaws.com/gis/guidance/pot/POT_Guidance+Upload+Farm+Polygon+for+Partners.mp4" type="video/mp4"></video>',
    }],
    buttons: [{
        text: 'Close',
        handler: function(){
            popup.close();             
        }
    }],    
    buttonAlign: 'right',
 });

Ext.define('Koltiva.view.SpatialTools.ExportKML.MainView' ,{
    extend: 'Ext.panel.Panel',
    id: 'Koltiva.view.SpatialTools.ExportKML.MainView',
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
            Ext.getCmp('Koltiva.view.SpatialTools.ExportKML.MainView').initMap()
        }
    },
    initComponent: function() {
        var thisObj = this;
        //Store
        thisObj.StoreGridUploadFarmPolygon = Ext.create('Koltiva.store.SpatialTools.ExportKML.GridUploadFarmPolygon', {});
        
        thisObj.items = [{
            layout: 'column',
            border: false,
            items: [{
                columnWidth: 0.65,
                items:[{
                    xtype:'panel',                
                    title:lang('Farm List'),
                    frame:true,
                    height:790,                    
                    items:[
                        Ext.create('Ext.grid.Panel', {
                            store: thisObj.StoreGridUploadFarmPolygon,
                            width: '100%',
                            maxHeight: 750,
                            style: 'border:1px solid #CCC;',                            
                            loadMask: true,
                            selType: 'rowmodel',
                            id:'grid',
                            cls:'custom-gridPerformance',
                            dockedItems: [        
                                {
                                    xtype: 'toolbar',
                                    minHeight : 50,
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
                                                            url: m_api+'/map/import_farm_polygon_client_excel',
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
                                                    url: m_api+'/map/farm_polygon_client_excel_clear_data',
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
                                            html:`<a href="https://koltiva.s3.ap-southeast-1.amazonaws.com/gis/template/TEMPLATE_POLYGON_DOWNLOAD_BY_LIST_POT.xlsx"><i>File Template [Excel]</i></a>`
                                        }                                                  
                                    ]
                                },
                            ],
                            listeners: {
                                select: function( record, index, eOpts ){
                                    let zoomTo = new google.maps.LatLng(parseFloat(index.data.CenterLatitude), parseFloat(index.data.CenterLongitude))
                                    bounds = new google.maps.LatLngBounds();
                                    bounds.extend(zoomTo);
                                    $('#map').gmap3("get").fitBounds(bounds);
                                }
                            },
                            columns: [
                                {
                                    text: 'No',
                                    xtype: 'rownumberer',
                                    width: '3%',
                                },
                                {
                                    text: 'ID',                                   
                                    dataIndex: 'MemberDisplayID',
                                    width : '14%',
                                },
                                {
                                    text: 'Name',                                     
                                    dataIndex: 'MemberName',
                                    width : '14%',
                                },
                                {
                                    text: 'Status Polygon',                                    
                                    dataIndex: 'StatusCheck',
                                    width    : '7%',
                                },
                                {
                                    text: 'Farm Nr',                                     
                                    dataIndex: 'PlotNr',
                                    width    : '5%',
                                    align: 'center'
                                },
                                {
                                    text: 'Survey Nr',                                     
                                    dataIndex: 'SurveyNr',
                                    width    : '5%',
                                    align: 'center'
                                }, 
                                {
                                    text: 'HA Polygon',                                    
                                    dataIndex: 'AreaHa',
                                    width    : '7%',
                                    align: 'center'
                                },                                 
                                {       
                                    text: 'Remark',                                    
                                    dataIndex: 'Remark',
                                    width : '10%',
                                },
                                {
                                    text: 'Partner',                                    
                                    dataIndex: 'PartnerName',
                                    align: 'center',
                                    width : '10%',
                                },                                
                                {
                                    text     : 'Status Member',
                                    width    : '7%',
                                    dataIndex: 'StatusMember'
                                },
                                {
                                    text     : 'Location',
                                    width    : '16%',
                                    dataIndex: 'location',
                                    renderer: function (t, meta, record) {
                                        let {Province, District, SubDistrict, Village} = record.data;
                                        let RetVal = `${Province}, ${District}, ${SubDistrict}, ${Village}`;
                                        return RetVal;
                                    }
                                }
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
                                    columnWidth: 0.99,                                    
                                    xtype: 'tbtext',                                    
                                    scale: 'large',
                                    text: lang('0 Remarked from 0 data')                                    
                                },{
                                    xtype:'button',
                                    id:'asaveButtonExcel',
                                    text: '',
                                    textAlign:'center',
                                    margin: '5px',
                                    style: 'text-align:center',
                                    listeners: {
                                        'click': function(){
                                            $.ajax({
                                                type: "GET",
                                                url: m_api + '/map/farm_polygon_download_export_excel',
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
                                },
                                {                                     
                                    id:'buttonKML',                                    
                                    xtype: 'splitbutton',                                       
                                    menu: mymenu                                    
                                }
                            ]
                            })                            
                        })
                    ]
            
                }]
            },{
                columnWidth: 0.35,
                style:'padding-left:7px;',
                items:[{
                    xtype:'panel',
                    id:'Koltiva.view.SpatialTools.ExportKML.MainView-MainMap',
                    title:lang('Farm Location'),
                    frame:true,
                    height:790,                  
                    items:[
                        {
                            xtype: 'component',
                            autoEl: {
                                html: '<div id="map" style="width:100%;height:790px;background:#e1e1e1;border:1px solid #e1e1e1;"></div><div style="position:absolute;z-index:9;color:#F2F2F2;top:720px; padding-left:10px;text-shadow: 2px 2px gray;"> <i>Powered by KoltiGIS</i></div>',
                            }
                        }
                    ]
                },{
                    html: '<div style="position:absolute;z-index:9;color:white;top:700px">Powered by KoltiGIS</div>'
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
                    streetViewControl: false,
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
    addPolygon:function(tag, id, area, data, color, index) {
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
                    click: function (object, event, context) {
                        console.log(data);                        
                        var map = $(this).gmap3("get");                         
                        Ext.getCmp('Koltiva.view.SpatialTools.ExportKML.MainView').closeInfoBox() 
                        var falseMarker  = new google.maps.Marker({
                            map: map,
                            position: event.latLng,
                            visible: false
                        });                        
                        
                        var content         = "";
                        content += '<div style="background-color:white; padding:10px">';                        
                        content +=      '<p><strong>'+data.MemberDisplayID+'_'+data.PlotNr+'</strong></p>';
                        content +=      '<table class="table table-condensed table-hover table-bordered table-striped" style="font-size:11px">';
                        content +=          '<tbody>';
                        content +=              '<tr><td style="width: 100px;">'+lang('ID')+'</td><td>'+data.MemberDisplayID+'</td></tr>';
                        content +=              '<tr><td>'+lang('Farmer Name')+'</td><td>'+data.MemberName+'</td></tr>';
                        content +=              '<tr><td>'+lang('Farm Nr')+'</td><td>'+data.PlotNr+'</td></tr>';                       
                        content +=              '<tr><td>'+lang('Ha Polygon')+'</td><td>'+data.AreaHa+'</td></tr>';                        
                        content +=      '</table>';
                        content += '</div>';
    
                        var infowindow = new google.maps.InfoWindow({
                            content: content
                        });                        
                        infowindow.open(map, falseMarker);                        
                        
                        Ext.getCmp('grid').getSelectionModel().select(index);
                        Ext.getCmp('grid').getView().scrollRowIntoView(index);
                    }
                }
            }
        });
    },
    clearMap:function(){
        ALL_EXTENT = new google.maps.LatLngBounds();
        DATA_POINT=[]
        var options = options || {};        
        $('#map').gmap3({clear: options});
        map.data.forEach(function(feature) {
            map.data.remove(feature);
        });
    },
    closeInfoBox:function(){
        $('div.infoBox').remove()
    }
})
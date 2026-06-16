Ext.define('Koltiva.store.SpatialTools.ExportKML.GridUploadFarmPolygon', {
    extend: 'Ext.data.Store',
    storeId: 'Koltiva.store.SpatialTools.ExportKML.GridUploadFarmPolygon',
    id: 'Koltiva.store.SpatialTools.ExportKML.GridUploadFarmPolygon',
    fields: ['MemberDisplayID','MemberName','PlotNr','SurveyNr','CenterLatitude','CenterLongitude','Polygon','AreaHa','StatusCheck','Remark', 'PartnerName', 'StatusMember', 'Province', 'District', 'SubDistrict', 'Village'],
    autoLoad: true,    
    proxy: {
        type: 'ajax',
        url: m_api + '/map/farm_polygon_client_excel',
        params: {
            'X-API-KEY': '030584'
        },
        reader: {
            type: 'json',
            root: 'data',
            count: 'total'
        }
    },
    listeners: {        
        load: function(store) {        
            const totalData     = store.getTotalCount();
            const tmp_data      = store.data.items
            const remarkedData  = tmp_data.filter(({data})=> data.Remark != "-").length
            let dataPoint = []
            let tag = ""
            let color = "blue"
            bounds = new google.maps.LatLngBounds();
            Ext.getCmp('Koltiva.view.SpatialTools.ExportKML.MainView').clearMap()

            $.each(tmp_data, function( i, {data} ) {
                let {CenterLatitude, CenterLongitude, Polygon, ...attr} = data

                // add Point
                if(CenterLatitude && CenterLongitude){
                    dataPoint.push(
                        {
						    latLng: [parseFloat(CenterLatitude), parseFloat(CenterLongitude)],
                            data: attr,
                            options: {
                                icon: {
                                    url: m_base_url + '/images/icons/maps/farmer-plot-location.png', 
                                    anchor: new google.maps.Point(20, 20),
                                    scaledSize: new google.maps.Size(30, 30),
                                },
                            },
                        }
                    )
                    var myLatLng = new google.maps.LatLng(parseFloat(CenterLatitude), parseFloat(CenterLongitude));
                    bounds.extend(myLatLng);
                }
                
                // add Polygon
                if(Polygon){
                    tag = 'farm-polygon'
					const coord = JSON.parse(Polygon).coordinates[0];
                    color = data.Remark == "-" ? '#ecf39e'  :'#f508cb'
					const area = [];
					$.each(coord, function(i, v) {
						area[i] = [v[1], v[0]];
					});
					const id = tag + "-" + data.MemberDisplayID + '-' + data.PlotNr
                    Ext.getCmp('Koltiva.view.SpatialTools.ExportKML.MainView').addPolygon(tag, id, area, attr, color, i)
                }
            })

            if(store.getTotalCount()>0){
                //Store is not empty
                Ext.getCmp('file').hide();
                Ext.getCmp('clear-data').show();
                Ext.getCmp('buttonKML').show();
                Ext.getCmp('asaveButtonExcel').show()
                // remarkedData > 0 ? Ext.getCmp('asaveButtonExcel').show(): Ext.getCmp('asaveButtonExcel').hide();
                document.querySelector('#summaryText').textContent = `${remarkedData} Remarked from ${totalData} Data`;
                Ext.getCmp('Koltiva.view.SpatialTools.ExportKML.MainView').addMarker(dataPoint)
                $('#map').gmap3("get").fitBounds(bounds);

            } else{
                //Store empty
                Ext.getCmp('file').show();
                Ext.getCmp('clear-data').hide();
                Ext.getCmp('buttonKML').hide();
                Ext.getCmp('asaveButtonExcel').hide();
                document.querySelector('#summaryText').textContent = '0 Remarked from 0 Data';
            }


        }
    }
});
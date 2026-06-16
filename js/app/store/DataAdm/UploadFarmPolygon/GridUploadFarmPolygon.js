 Ext.define('Koltiva.store.DataAdm.UploadFarmPolygon.GridUploadFarmPolygon', {
    extend: 'Ext.data.Store',
    storeId: 'Koltiva.store.DataAdm.UploadFarmPolygon.GridUploadFarmPolygon',
    id: 'Koltiva.store.DataAdm.UploadFarmPolygon.GridUploadFarmPolygon',
    fields: ['MemberDisplayID','MemberName','PlotNr','SurveyNr','Revision','StatusCheck','Lat','Lng','Polygon','AreaHa','Valid','Remark'],
    autoLoad: true,    
    proxy: {
        type: 'ajax',
        url: m_api + '/data_adm/upload_farm_polygon/farm_polygon',
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
            Ext.getCmp('Koltiva.view.DataAdm.UploadFarmPolygon.MainView').clearMap()

            $.each(tmp_data, function( i, {data} ) {
                let {Lat, Lng, Polygon, ...attr} = data

                // add Point
                if(Lat && Lng){
                    dataPoint.push(
                        {
						    latLng: [parseFloat(Lat), parseFloat(Lng)],
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
                    var myLatLng = new google.maps.LatLng(parseFloat(Lat), parseFloat(Lng));
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
                    Ext.getCmp('Koltiva.view.DataAdm.UploadFarmPolygon.MainView').addPolygon(tag, id, area, attr, color)
                }
            })




            if(store.getTotalCount()>0){
                //Store is not empty
                Ext.getCmp('file').hide();
                Ext.getCmp('clear-data').show();
                Ext.getCmp('asaveButton').show();
                Ext.getCmp('asaveButtonExcel').show()
                // remarkedData > 0 ? Ext.getCmp('asaveButtonExcel').show(): Ext.getCmp('asaveButtonExcel').hide();
                document.querySelector('#summaryText').textContent = `${remarkedData} Remarked from ${totalData} Data`;

                
                Ext.getCmp('Koltiva.view.DataAdm.UploadFarmPolygon.MainView').addMarker(dataPoint)

                $('#map').gmap3("get").fitBounds(bounds);

            } else{
                //Store empty
                Ext.getCmp('file').show();
                Ext.getCmp('clear-data').hide();
                Ext.getCmp('asaveButton').hide();
                Ext.getCmp('asaveButtonExcel').hide();
                document.querySelector('#summaryText').textContent = '0 Remarked from 0 Data';
            }


        }
    }
});
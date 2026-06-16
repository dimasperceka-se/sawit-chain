/*
 * @Author: sofyan
 * @Date:   2021-11-08 
*/

Ext.define('Koltiva.store.DataAdm.FarmSummary.GridMainFarmSummary', {
    extend: 'Ext.data.Store',
    id: 'Koltiva.store.DataAdm.FarmSummary.GridMainFarmSummary',
    fields: ['MemberID','MemberDisplayID','MemberName','PlotNr','Revision','AreaHa','StatusCheck','DateCreated','Region','Location','ProvinceName','DistrictName','SubDistrictName','VillageName'],
    pageSize: 50,
    autoLoad: false,
    remoteSort: true,
    proxy: {
        type: 'ajax',
        extraParams: {
            prov: m_ProvinceID,
            kab: m_DistrictID,
            kec: m_SubDistrictID,
        },
        url: m_api + '/data_adm/farm_summary/grid_main',
        reader: {
            type: 'json',
            root: 'data',
            totalProperty: 'total'
        }
    },
    listeners: {
        load: function(store, records, success) {
            var thisObj = this;
            if(success == true){
                thisObj.loadInfoFilter(store);
            }
        },
        sort: function(store, records, success){
            if(success == true){
                Ext.Ajax.request({
                    url: m_api + '/tools/information_grid',
                    waitMsg: lang('Please Wait'),
                    success: function(data) {
                        Ext.getCmp('Koltiva.view.DataAdm.FarmSummary.GridMainFarmSummary-gridInformation').update(data.responseText);
                    }
                });
            }
        },
        beforeload: function(store, operation, options){
            var ptextSearch, CmbPolygonStatus;
            
            var patchouli_farm_summary_ls = JSON.parse(localStorage.getItem('patchouli_farm_summary_ls'));
            if(patchouli_farm_summary_ls != null){
                ptextSearch = patchouli_farm_summary_ls.ptextSearch;
                CmbPolygonStatus = patchouli_farm_summary_ls.CmbPolygonStatus;
            }else{
                ptextSearch = "";
                CmbPolygonStatus = null;

            }

            store.proxy.extraParams.prov = m_ProvinceID;
            store.proxy.extraParams.kab = m_DistrictID;
            store.proxy.extraParams.kec = m_SubDistrictID;
            store.proxy.extraParams.textSearch = ptextSearch;
            store.proxy.extraParams.CmbPolygonStatus = CmbPolygonStatus;
            
        }

    },
    loadInfoFilter: function(store) {
        Ext.Ajax.request({
            url: m_api + '/tools/information_grid',
            waitMsg: lang('Please Wait'),
            success: function(data) {
                Ext.getCmp('Koltiva.view.DataAdm.FarmSummary.GridMainFarmSummary-gridInformation').update(data.responseText);
                
                // Draw Chart (begin) ===============================================
                var dataSummary = store.proxy.reader.jsonData     

                function cekNumber(number) {
                    number === undefined && (number = 0)
                    return parseInt(number)
                }   
                
                var sNew = cekNumber(dataSummary['new'])
                var sVerified = cekNumber(dataSummary['verified'])
                var sOverlap = cekNumber(dataSummary['overlap'])
                var sRetake = cekNumber(dataSummary['retake'])
                var sIrrelevant = cekNumber(dataSummary['irrelevant']) 
                var sNullified = cekNumber(dataSummary['nullified']) 
                
                var sTotal = cekNumber(dataSummary['total'])
                var sInfo = ((sVerified / sTotal) *100 ).toFixed(2)

                Highcharts.setOptions({colors: ['#FFBC65ff','#D99E52ff','#B3803Fff','#8C632Cff','#664519ff','#402706ff']});

                StatusChart = new Highcharts.chart('chart-status', {
                    chart: {
                        type: 'bar',
                        spacingBottom: 0,
                        spacingTop: 0,
                        spacingLeft: 0,
                        spacingRight: 0,
                        marginBottom: 15,
                        marginTop: 15,
                        marginLeft: 0,
                        marginRight: 0,
                    },
                    title: {
                        text: `Farm Polygon Status (Verified : ${sInfo} %)`
                    },
                    plotOptions: {
                        series: {stacking: 'percent'},
                    },
                    xAxis: {labels: {enabled: false}},
                    yAxis: {visible: false, reversedStacks: false},
                    series: [{
                        name: `Verified [${sVerified}]`,
                        data: [sVerified]
                      }, {
                        name: `New [${sNew}]`,
                        data: [sNew]
                      }, {
                        name: `Overlap [${sOverlap}]`,
                        data: [sOverlap]
                      }, {
                        name: `Retake [${sRetake}]`,
                        data: [sRetake]
                      }, {
                         name: `Irrelevant [${sIrrelevant}]`,
                         data: [sIrrelevant]
                      }, {
                        name: `Nullified [${sNullified}]`,
                        data: [sNullified]
                      }]
                })
                // Draw Chart (End) ===============================================
            }
        });

        //render map
        Ext.Ajax.request({
            url: m_api + '/data_adm/farm_summary/render_map',
            method: 'POST',
            params: {
                ContWidth: Ext.getCmp('Koltiva.view.DataAdm.FarmSummary.GridMainFarmSummary-Map').getWidth(),
                ContHeight: Ext.getCmp('Koltiva.view.DataAdm.FarmSummary.GridMainFarmSummary-Map').getHeight(),
            },
            success: function(response){
                // Ext.MessageBox.hide();
                var MapReturn = response.responseText;
                Ext.getCmp('Koltiva.view.DataAdm.FarmSummary.GridMainFarmSummary-Map').update(MapReturn, true);
                Ext.getCmp('Koltiva.view.DataAdm.FarmSummary.GridMainFarmSummary-Map').doLayout();
            },
            failure: function(response){
                // Ext.MessageBox.hide();
                // Ext.MessageBox.show({
                //     title: 'Failed',
                //     msg: lang('Failed to render map'),
                //     buttons: Ext.MessageBox.OK,
                //     animateTarget: 'mb9',
                //     icon: 'ext-mb-error'
                // });

                //tutup popup
                // thisObj.close();
            }
        });
    }
});
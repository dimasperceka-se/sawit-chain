/******************************************
 *  Author : n1colius.lau@gmail.com   
 *  Created On : Tue May 07 2019
 *  File : PanelPlantationStatusMainGrid.js
 *******************************************/

Ext.define('Koltiva.store.PlotSurvey.PanelPlantationStatusMainGrid', {
    extend: 'Ext.data.Store',
    id: 'store.PlotSurvey.PanelPlantationStatusMainGrid',
    storeId: 'store.PlotSurvey.PanelPlantationStatusMainGrid',
    fields: ['MemberID','PlotNr','GardenAreaHa','AnnualProduction','ActiveStatus'],
    storeVar: false,
    setStoreVar: function(value){
        this.storeVar = value;
    },
    autoLoad: false,
    proxy: {
        type: 'ajax',
        url: m_api + '/plot_survey/grid_plot_status',
        reader: {
            type: 'json',
            root: 'data'
        }
    },
    listeners: {
        beforeload: function(store, operation, options){
            store.proxy.extraParams.MemberID = this.storeVar.MemberID;
            store.proxy.extraParams.CallFrom = this.storeVar.CallFrom;
        }
    }
});
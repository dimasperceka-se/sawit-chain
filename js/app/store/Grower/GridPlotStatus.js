/*
* @Author: nikolius
* @Date:   2017-05-30 18:07:31
* @Last Modified by:   nikolius
* @Last Modified time: 2017-05-30 18:36:14
*/

/*
    Store ini memerlukan parameter
        1. MemberID
*/

Ext.define('Koltiva.store.Grower.GridPlotStatus', {
    extend: 'Ext.data.Store',
    id: 'store.Grower.GridPlotStatus',
    storeId: 'store.Grower.GridPlotStatus',
    fields: ['MemberID','PlotNr','GardenAreaHa','ActiveStatus','LastSurvey', 'LastSurveyNr'],
    storeVar: false,
    setStoreVar: function(value){
        this.storeVar = value;
    },
    autoLoad: false,
    proxy: {
        type: 'ajax',
        url: m_api + '/grower/grid_plot_status',
        reader: {
            type: 'json',
            root: 'data'
        }
    },
    listeners: {
        beforeload: function(store, operation, options){
            store.proxy.extraParams.MemberID = this.storeVar.MemberID;
        }
    }
});
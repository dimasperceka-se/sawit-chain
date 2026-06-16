/*
* @Author: nikolius
* @Date:   2017-07-28 10:40:24
* @Last Modified by:   nikolius
* @Last Modified time: 2017-08-09 18:26:55
*/

Ext.define('Koltiva.store.PlotPolygon.GridPlotPolygonPanel', {
    extend: 'Ext.data.Store',
    id: 'store.PlotPolygon.GridPlotPolygonPanel',
    storeId: 'store.PlotPolygon.GridPlotPolygonPanel',
    fields: ['PlotNr','Survey','SurveyNr','DateCollection','StatusCheck','DateCreated','CreatedBy','Enumerator'],
    storeVar: false,
    setStoreVar: function(value){
        this.storeVar = value;
    },
    autoLoad: false,
    proxy: {
        type: 'ajax',
        url: m_api + '/plot_survey/grid_plot_polygon_panel',
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
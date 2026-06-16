/*
* @Author: nikolius
* @Date:   2017-05-31 11:38:59
* @Last Modified by:   nikolius
* @Last Modified time: 2017-08-09 18:24:45
*/

/*
    Store ini memerlukan parameter
        1. MemberID
*/

Ext.define('Koltiva.store.PlotSurvey.GridPlotSurveySummary', {
    extend: 'Ext.data.Store',
    id: 'store.PlotSurvey.GridPlotSurveySummary',
    storeId: 'store.PlotSurvey.GridPlotSurveySummary',
    fields: ['PlotNr','Survey','SurveyNr','DateCollection','CreatedBy','Enumerator'],
    storeVar: false,
    setStoreVar: function(value){
        this.storeVar = value;
    },
    autoLoad: false,
    proxy: {
        type: 'ajax',
        url: m_api + '/plot_survey/grid_plot_survey_summary',
        reader: {
            type: 'json',
            root: 'data'
        }
    },
    listeners: {
        beforeload: function(store, operation, options){
            store.proxy.extraParams.MemberID    = this.storeVar.MemberID;
            store.proxy.extraParams.from        = this.storeVar.from;
        }
    }
});
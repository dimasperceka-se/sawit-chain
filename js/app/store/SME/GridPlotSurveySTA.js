/*
* @Author: fikri
* @Date:   2019-10-28 17:29:59
*/

/*
    Store ini memerlukan parameter
        1. MemberID
*/

Ext.define('Koltiva.store.SME.GridPlotSurveySTA', {
    extend: 'Ext.data.Store',
    id: 'Koltiva.store.SME.GridPlotSurveySTA',
    storeId: 'Koltiva.store.SME.GridPlotSurveySTA',
    fields: ['PlotNr','Survey','SurveyNr','DateCollection','CreatedBy','Enumerator'],
    storeVar: false,
    setStoreVar: function(value){
        this.storeVar = value;
    },
    autoLoad: false,
    proxy: {
        type: 'ajax',
        url: m_api + '/sme/grid_trader_survey_sta',
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
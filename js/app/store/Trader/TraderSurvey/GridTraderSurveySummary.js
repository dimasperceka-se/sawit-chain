/*
* @Author: nikolius
* @Date:   2017-07-24 10:20:42
* @Last Modified by:   nikolius
* @Last Modified time: 2017-07-24 10:27:45
*/

/*
    Store ini memerlukan parameter
        1. MemberID
*/

Ext.define('Koltiva.store.TraderSurvey.GridTraderSurveySummary', {
    extend: 'Ext.data.Store',
    id: 'store.TraderSurvey.GridTraderSurveySummary',
    storeId: 'store.TraderSurvey.GridTraderSurveySummary',
    fields: ['BusinessNr','Survey','SurveyNr','DateCollection','CreatedBy'],
    storeVar: false,
    setStoreVar: function(value){
        this.storeVar = value;
    },
    autoLoad: false,
    proxy: {
        type: 'ajax',
        url: m_api + '/trader_survey/grid_trader_survey_summary',
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
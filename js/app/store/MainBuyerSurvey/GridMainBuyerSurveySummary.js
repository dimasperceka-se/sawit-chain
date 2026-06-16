/*
* @Author: nikolius
* @Date:   2017-06-01 13:19:11
* @Last Modified by:   nikolius
* @Last Modified time: 2017-08-09 18:31:06
*/

/*
    Store ini memerlukan parameter
        1. MemberID
*/

Ext.define('Koltiva.store.MainBuyerSurvey.GridMainBuyerSurveySummary', {
    extend: 'Ext.data.Store',
    id: 'store.MainBuyerSurvey.GridMainBuyerSurveySummary',
    storeId: 'store.MainBuyerSurvey.GridMainBuyerSurveySummary',
    fields: ['Survey','PlotNr','SurveyNr','DateCollection','CreatedBy','Enumerator'],
    storeVar: false,
    setStoreVar: function(value){
        this.storeVar = value;
    },
    autoLoad: false,
    proxy: {
        type: 'ajax',
        url: m_api + '/main_buyer_survey/grid_main_buyer_survey_summary',
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
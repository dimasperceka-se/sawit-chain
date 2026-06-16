/*
* @Author: nikolius
* @Date:   2017-11-02 16:11:55
* @Last Modified by:   nikolius
* @Last Modified time: 2017-11-02 16:17:50
*/

Ext.define('Koltiva.store.FinanceSurvey.GridFinanceSurveySummary', {
    extend: 'Ext.data.Store',
    id: 'Koltiva.store.FinanceSurvey.GridFinanceSurveySummary',
    storeId: 'Koltiva.store.FinanceSurvey.GridFinanceSurveySummary',
    fields: ['Survey','SurveyNr','DateCollection','CreatedBy','Enumerator'],
    storeVar: false,
    setStoreVar: function(value){
        this.storeVar = value;
    },
    autoLoad: false,
    proxy: {
        type: 'ajax',
        url: m_api + '/finance_survey/grid_finance_survey_summary',
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
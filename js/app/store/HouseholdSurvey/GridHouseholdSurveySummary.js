/*
* @Author: nikolius
* @Date:   2017-06-01 16:46:06
* @Last Modified by:   nikolius
* @Last Modified time: 2017-08-09 18:32:19
*/

Ext.define('Koltiva.store.HouseholdSurvey.GridHouseholdSurveySummary', {
    extend: 'Ext.data.Store',
    id: 'store.HouseholdSurvey.GridHouseholdSurveySummary',
    storeId: 'store.HouseholdSurvey.GridHouseholdSurveySummary',
    fields: ['Survey','SurveyNr','DateCollection','CreatedBy','Enumerator'],
    storeVar: false,
    setStoreVar: function(value){
        this.storeVar = value;
    },
    autoLoad: false,
    proxy: {
        type: 'ajax',
        url: m_api + '/household_survey/grid_household_survey_summary',
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
/*
* @Author: nikolius
* @Date:   2017-05-31 13:36:09
* @Last Modified by:   nikolius
* @Last Modified time: 2017-05-31 13:36:58
*/

Ext.define('Koltiva.store.PlotSurvey.CmbSurveyNr', {
    extend: 'Ext.data.Store',
    storeId: 'store.PlotSurvey.CmbSurveyNr',
    id: 'store.PlotSurvey.CmbSurveyNr',
    fields: ['id', 'label'],
    autoLoad: true,
    storeVar: false,
    setStoreVar: function(value){
        this.storeVar = value;
    },
    proxy: {
        type: 'ajax',
        url: m_api + '/plot_survey/combo_survey_nr',
        reader: {
            type: 'json'
        }
    },
    listeners: {
        beforeload: function(store, operation, options){
            store.proxy.extraParams.from = this.storeVar.from;
        }
    }
});
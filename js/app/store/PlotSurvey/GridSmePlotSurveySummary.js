/******************************************
 *  Author : n1colius.lau@gmail.com   
 *  Created On : Mon Jul 15 2019
 *  File : GridSmePlotSurveySummary.js
 *******************************************/

Ext.define('Koltiva.store.PlotSurvey.GridSmePlotSurveySummary', {
    extend: 'Ext.data.Store',
    id: 'store.PlotSurvey.GridSmePlotSurveySummary',
    storeId: 'store.PlotSurvey.GridSmePlotSurveySummary',
    fields: ['PlotNr','Survey','SurveyNr','DateCollection','CreatedBy','Enumerator'],
    storeVar: false,
    setStoreVar: function(value){
        this.storeVar = value;
    },
    autoLoad: true,
    proxy: {
        type: 'ajax',
        url: m_api + '/plot_survey/grid_sme_plot_survey_summary',
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
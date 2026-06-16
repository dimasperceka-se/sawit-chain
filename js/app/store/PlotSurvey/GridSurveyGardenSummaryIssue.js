/******************************************
 *  Author : n1colius.lau@gmail.com   
 *  Created On : Fri Jan 04 2019
 *  File : GridSurveyGardenSummaryIssue.js
 *******************************************/

Ext.define('Koltiva.store.Farmer.GridSurveyGardenSummaryIssue', {
    extend: 'Ext.data.Store',
    storeId: 'Koltiva.store.Farmer.GridSurveyGardenSummaryIssue',
    id: 'Koltiva.store.Farmer.GridSurveyGardenSummaryIssue',
    fields: ['IssueStatus','Issue'],
    autoLoad: true,
    storeVar: false,
    setStoreVar: function(value){
        this.storeVar = value;
    },
    proxy: {
        type: 'ajax',
        url: m_api + '/plot_survey/garden_grid_summary_issue',
        reader: {
            type: 'json',
            root: 'data'
        }
    },
    listeners: {
        beforeload: function(store, operation, options){
            store.proxy.extraParams.DaconID = this.storeVar.DaconID;
        }
    }
});
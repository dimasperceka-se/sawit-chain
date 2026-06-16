/******************************************
 *  Author : n1colius.lau@gmail.com   
 *  Created On : Fri Jan 04 2019
 *  File : GridSurveyGardenSummary.js
 *******************************************/

Ext.define('Koltiva.store.PlotSurvey.GridSurveyGardenSummary', {
    extend: 'Ext.data.Store',
    storeId: 'Koltiva.store.PlotSurvey.GridSurveyGardenSummary',
    id: 'Koltiva.store.PlotSurvey.GridSurveyGardenSummary',
    fields: ['DaconID','RemarkProcess','NrOfIssue'],
    autoLoad: false,
    storeVar: false,
    setStoreVar: function(value){
        this.storeVar = value;
    },
    proxy: {
        type: 'ajax',
        url: m_api + '/plot_survey/garden_grid_summary',
        reader: {
            type: 'json',
            root: 'data'
        }
    },
    listeners: {
        beforeload: function(store, operation, options){
            store.proxy.extraParams.MemberID = this.storeVar.FarmerID;
            store.proxy.extraParams.SurveyNr = this.storeVar.SurveyNr;
            store.proxy.extraParams.PlotNr = this.storeVar.GardenNr;
            store.proxy.extraParams.Certification = this.storeVar.Certification;
            store.proxy.extraParams.ICSDate = this.storeVar.ICSDate;
        }
    }
});
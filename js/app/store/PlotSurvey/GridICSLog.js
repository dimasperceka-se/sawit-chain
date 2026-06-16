/*
* @Author: nikolius
* @Date:   2018-07-09 15:29:00
* @Last Modified by:   nikolius
* @Last Modified time: 2018-07-10 11:13:48
*/

/*
    Param2 yg diperlukan ketika load View ini
    - FarmerID
    - GardenNr
    - SurveyNr
    - Certification
*/

Ext.define('Koltiva.store.PlotSurvey.GridICSLog', {
    extend: 'Ext.data.Store',
    id: 'Koltiva.store.PlotSurvey.GridICSLog',
    storeId: 'Koltiva.store.PlotSurvey.GridICSLog',
    fields: ['Certification', 'StaffName', 'CommentAudit', 'CreatedBy', 'DateCreated', 'DateRevisionAudit', 'DateUpdated', 'FarmerID', 'GardenNr', 'ICSDate', 'RecommendationAudit', 'StatusAudit', 'StatusAuditName', 'SurveyNr', 'InpectorID', 'FarmerSignature', 'InspectorSignature', 'AuditCommiteeSignature','CertProgram'],
    autoLoad: false,
    storeVar: false,
    setStoreVar: function(value){
        this.storeVar = value;
    },
    proxy: {
        type: 'ajax',
        url: m_api + '/plot_survey/grid_ics_log',
        reader: {
            type: 'json',
            root: 'data'
        }
    },
    listeners: {
        beforeload: function(store, operation, options){
            store.proxy.extraParams.MemberID = this.storeVar.FarmerID;
            store.proxy.extraParams.PlotNr = this.storeVar.GardenNr;
            store.proxy.extraParams.SurveyNr = this.storeVar.SurveyNr;
            store.proxy.extraParams.Certification = this.storeVar.Certification;
        }
    }
});
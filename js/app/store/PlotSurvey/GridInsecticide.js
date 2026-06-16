Ext.define('Koltiva.store.PlotSurvey.GridInsecticide', {
    extend: 'Ext.data.Store',
    id: 'Koltiva.store.PlotSurvey.GridInsecticide',
    storeId: 'Koltiva.store.PlotSurvey.GridInsecticide',
    fields: ['InsecticideID','MemberID', 'MemberUid','PlotNr', 'SurveyNr', 'Brand','BrandID','Frequency', 'Applying','ApplyingFor', 'ApplyingID', 'ApplyingForID', 'StatusCode','CreatedBy','DateCreated','LastModifiedBy','DateUpdated'],
    pageSize: 12,
    autoLoad: false,
    remoteSort: true,
    storeVar: false,
    setStoreVar: function(value){
        this.storeVar = value;
    },
    proxy: {
        type: 'ajax',
        url: m_api + '/plot_survey/grid_main_insecticide',
        reader: {
            type: 'json',
            root: 'data',
            totalProperty: 'total'
        }
    },
	listeners :
	{ 
		beforeload: function(store, operation, options){
            store.proxy.extraParams.MemberID = this.storeVar.MemberID;
            store.proxy.extraParams.PlotNr = this.storeVar.PlotNr;
            store.proxy.extraParams.SurveyNr = this.storeVar.SurveyNr;
        }
	}
});
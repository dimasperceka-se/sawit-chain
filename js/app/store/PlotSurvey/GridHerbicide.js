Ext.define('Koltiva.store.PlotSurvey.GridHerbicide', {
    extend: 'Ext.data.Store',
    id: 'Koltiva.store.PlotSurvey.GridHerbicide',
    storeId: 'Koltiva.store.PlotSurvey.GridHerbicide',
    fields: ['HerbicideID','MemberID', 'MemberUid','PlotNr', 'SurveyNr', 'Brand','BrandID','Frequency', 'Applying', 'ApplyingID', 'StatusCode','CreatedBy','DateCreated','LastModifiedBy','DateUpdated'],
    pageSize: 12,
    autoLoad: false,
    remoteSort: true,
    storeVar: false,
    setStoreVar: function(value){
        this.storeVar = value;
    },
    proxy: {
        type: 'ajax',
        url: m_api + '/plot_survey/grid_main_herbicide',
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
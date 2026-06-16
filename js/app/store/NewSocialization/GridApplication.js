Ext.define('Koltiva.store.NewSocialization.GridApplication', {
    extend: 'Ext.data.Store',
    id: 'Koltiva.store.NewSocialization.GridApplication',
    fields: [ 'ApplicantID','Province','District','SubDistrict','Fullname','VillageNames','GroupName'],
    pageSize: 20 ,
    autoLoad: true,
    remoteSort: true, 
	setStoreVar: function(value){
        this.storeVar = value;
    },
    proxy: { 
        type: 'ajax', 
        url: m_api + '/new_socialization/main_application_list',
        reader: {
            type: 'json',
            root: 'data',
            totalProperty: 'total'
        }
    },  
	sorters: [{
		direction:'ASC',
		property :'ApplicantID'
	}],
    listeners: { 
		beforeload: function(store, operation, options){ 
		   store.proxy.extraParams.IMSID = this.storeVar.IMSID; 
		   store.proxy.extraParams.FarmerGroupID = this.storeVar.FarmerGroupID; 
		   store.proxy.extraParams.IMSSocID = this.storeVar.IMSSocID; 
		   
        }
    }
});
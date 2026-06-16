Ext.define('Koltiva.store.NewSocialization.GridFarmerApplication', {
    extend: 'Ext.data.Store',
    id: 'Koltiva.store.NewSocialization.GridFarmerApplication',
    fields: [ 'FarmerID','FarmerName','Province','District', 'SubDistrict', 'Village'],
    pageSize: 20 ,
    autoLoad: true,
    remoteSort: true, 
	setStoreVar: function(value){
        this.storeVar = value;
    },
    proxy: { 
        type: 'ajax', 
        url: m_api + '/new_socialization/main_existingfarmer_list',
        reader: {
            type: 'json',
            root: 'data',
            totalProperty: 'total'
        }
    },  
	sorters: [{
		direction:'ASC',
		property :'FarmerID'
	}],
    listeners: { 
		beforeload: function(store, operation, options){
           store.proxy.extraParams.ProvinceID = this.storeVar.ProvinceID;
           store.proxy.extraParams.FarmerGroupID = this.storeVar.FarmerGroupID;
           store.proxy.extraParams.IMSSocID = this.storeVar.IMSSocID;
        }
    }
});
Ext.define('Koltiva.store.NewSocialization.GridSocializationStaff', {
    extend: 'Ext.data.Store',
    id: 'Koltiva.store.NewSocialization.GridSocializationStaff',
    fields: [ 'SocStaffID','IMSSocID','PersonNm', 'StaffID'],
    pageSize: 50,
    autoLoad: true,
    remoteSort: true,
	setStoreVar: function(value){
        this.storeVar = value;
    },
    proxy: { 
        type: 'ajax', 
        url: m_api + '/new_socialization/main_list_soc_staff',
        reader: {
            type: 'json',
            root: 'data',
            totalProperty: 'total'
        }
    },
    listeners: {  
		beforeload: function(store, operation, options){ 
            store.proxy.extraParams.IMSSocID = this.storeVar.IMSSocID;   
        }
    }
});
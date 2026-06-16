Ext.define('Koltiva.store.NewSocialization.GridStaffList', {
    extend: 'Ext.data.Store',
    id: 'Koltiva.store.NewSocialization.GridStaffList',
    fields: [ 'StaffID','PersonNm',{name : 'status_checked', type :'boolean'}],
    pageSize: 50,
    autoLoad: false,
    remoteSort: true,
    storeVar: false,
    setStoreVar: function(value){
        this.storeVar = value;
    },
    proxy: { 
        type: 'ajax', 
        url: m_api + '/new_socialization/main_staff',
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
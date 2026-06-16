Ext.define('Koltiva.store.NewSocialization.GridAttandanceStaffList', {
    extend: 'Ext.data.Store',
    id: 'Koltiva.store.NewSocialization.GridAttandanceStaffList',
    fields: [ 'IMSSocID', 'StaffID','PersonNm',{name :'AttendanceStatus', type : 'boolean'}, 'DayNumber'],
    pageSize: 50,
    autoLoad: false,
    remoteSort: true, 
    setStoreVar: function(value){
        this.storeVar = value;
    },
    proxy: { 
        type: 'ajax', 
        url: m_api + '/new_socialization/main_staffattandance_list',
        reader: {
            type: 'json',
            root: 'data',
            totalProperty: 'total'
        }
    },
    listeners: {  
        beforeload: function(store, operation, options){  
           store.proxy.extraParams.DayNumber = this.storeVar.DayNumber;
           store.proxy.extraParams.IMSSocID = this.storeVar.IMSSocID;  
        }
    }
});
Ext.define('Koltiva.store.Mill.ListStaffAssignment', {
    extend: 'Ext.data.Store',
    id: 'Koltiva.store.Mill.ListStaffAssignment',
    storeId: 'Koltiva.store.Mill.ListStaffAssignment',
    fields: ['value','text'],
    storeVar: false,
    setStoreVar: function(value){
        this.storeVar = value;
    },
    autoLoad: true,
    proxy: {
        type: 'ajax',
        url: m_api + '/mill/list_staff_assignment',
        reader: {
            type: 'json',
            root: 'data'
        }
    },
    listeners: {
        beforeload: function(store, operation, options){
            store.proxy.extraParams.MillID = this.storeVar.MillID;
        }
    }
});
Ext.define('Koltiva.store.Refinery.ListStaffAssignment', {
    extend: 'Ext.data.Store',
    id: 'Koltiva.store.Refinery.ListStaffAssignment',
    storeId: 'Koltiva.store.Refinery.ListStaffAssignment',
    fields: ['value','text'],
    storeVar: false,
    setStoreVar: function(value){
        this.storeVar = value;
    },
    autoLoad: true,
    proxy: {
        type: 'ajax',
        url: m_api + '/refinery/list_staff_assignment',
        reader: {
            type: 'json',
            root: 'data'
        }
    },
    listeners: {
        beforeload: function(store, operation, options){
            store.proxy.extraParams.RefineryID = this.storeVar.RefineryID;
        }
    }
});
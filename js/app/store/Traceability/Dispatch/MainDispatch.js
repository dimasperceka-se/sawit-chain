Ext.define('Koltiva.store.Traceability.Dispatch.MainDispatch', {
    extend: 'Ext.data.Store',
    id: 'Koltiva.store.Traceability.Dispatch.MainDispatch',
    storeId: 'Koltiva.store.Traceability.Dispatch.MainDispatch',
    fields: ['DespatchID','DespatchNumber','PackingDate','ShippingDate','DespatchCode','DestpatchStatusID','DestpatchNetto','DestpatchStatusName','ContainerTotal', 'ProductID'],
    pageSize: 50,
    autoLoad: true,
    remoteSort: true,
    storeVar: false,
    setStoreVar: function(value){
        this.storeVar = value;
    },
    proxy: {
        type: 'ajax',
        url: m_api + '/dispatch/transaction/fetch',
        reader: {
            type: 'json',
            root: 'data',
            totalProperty: 'total'
        }
    },
    listeners: {
        beforeload: function(store, operation, options){
        }, 
    }
});
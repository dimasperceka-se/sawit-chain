 

Ext.define('Koltiva.store.Traceability.Dispatch.ProductType', {
    extend: 'Ext.data.Store',
    storeId: 'Koltiva.store.Traceability.Dispatch.ProductType',
    id: 'Koltiva.store.Traceability.Dispatch.ProductType',
    fields: ['id','label'],
    autoLoad: true,
    proxy: {
        type: 'ajax',
        url: m_api + '/dispatch/transaction/ProductType/',
        reader: {
            type: 'json'
        }
    },
    listeners: {
        beforeload: function(store, operation, options){
            store.proxy.extraParams.ProductID = this.storeVar.ProductID;
        }, 
    }
});
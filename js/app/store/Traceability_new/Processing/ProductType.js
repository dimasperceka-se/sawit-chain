Ext.define('Koltiva.store.Traceability_new.Processing.ProductType', {
    extend: 'Ext.data.Store',
    storeId: 'Koltiva.store.Traceability_new.Processing.ProductType',
    id: 'Koltiva.store.Traceability_new.Processing.ProductType',
    fields: ['id','label'],
    autoLoad: true,
    proxy: {
        type: 'ajax',
        url: m_api + '/processing/transaction/ProductType/',
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
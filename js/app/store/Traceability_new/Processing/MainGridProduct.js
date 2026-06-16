Ext.define('Koltiva.store.Traceability_new.Processing.MainGridProduct', {
    extend: 'Ext.data.Store',
    id: 'Koltiva.store.Traceability_new.Processing.MainProcessing',
    storeId: 'Koltiva.store.Traceability_new.Processing.MainProcessing',
    fields: ['ProcessingProductID','ProcessingID', 'ProductName','ProductPercentage','ProductVolume','RemainingVolume'],
    pageSize: 100,
    autoLoad: true,
    remoteSort: true,
    storeVar: false,
    setStoreVar: function(value){
        this.storeVar = value;
    },
    proxy: {
        type: 'ajax',
        url: m_api + '/processing/transaction/fetch_product',
        reader: {
            type: 'json',
            root: 'data',
            totalProperty: 'total'
        }
    },
    listeners: {
        beforeload: function(store, operation, options){
            store.proxy.extraParams.ProcessingID = this.storeVar.ProcessingID;
        }, 
    }
});
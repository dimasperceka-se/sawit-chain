Ext.define('Koltiva.store.Traceability_new.Processing.MainGridPick', {
    extend: 'Ext.data.Store',
    id: 'Koltiva.store.Traceability_new.Processing.MainProcessing',
    storeId: 'Koltiva.store.Traceability_new.Processing.MainProcessing',
    fields: ['ProcessingTransactionID','ReceiveDate','ExternalCode','Supplier','DestTransportNumber', 'Nett'],
    pageSize: 100,
    autoLoad: true,
    remoteSort: true,
    storeVar: false,
    setStoreVar: function(value){
        this.storeVar = value;
    },
    proxy: {
        type: 'ajax',
        url: m_api + '/processing/transaction/fetchpick',
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
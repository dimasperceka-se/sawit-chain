Ext.define('Koltiva.store.Traceability_new.Transaction.GridTransactionAvailable', {
    extend: 'Ext.data.Store',
    id: 'Koltiva.store.Traceability_new.Transaction.GridTransactionAvailable',
    storeId: 'Koltiva.store.Traceability_new.Transaction.GridTransactionAvailable',
    fields: ['SupplyTransID','SupplyType','DateTransaction','FakturNumber','Name','VolumeNetto','SupplyStatus'],
    pageSize: 50,
    autoLoad: true,
    remoteSort: true,
    storeVar: false,
    setStoreVar: function(value){
        this.storeVar = value;
    },
    proxy: {
        type: 'ajax',
        //url: m_api + '/tc_transaction/transaction_available',
        reader: {
            type: 'json',
            root: 'data',
            totalProperty: 'total'
        }
    },
    listeners: {
        beforeload: function(store, operation, options){
            store.proxy.extraParams.Role = this.storeVar.Role;
            //store.proxy.extraParams.StringNameUsername = this.storeVar.StringNameUsername;
        }
    }
});
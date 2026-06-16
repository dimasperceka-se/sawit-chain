Ext.define('Koltiva.store.Traceability.Dispatch.MainGridPick', {
    extend: 'Ext.data.Store',
    id: 'Koltiva.store.Traceability.Dispatch.MainDispatch',
    storeId: 'Koltiva.store.Traceability.Dispatch.MainDispatch',
    fields: ['DespatchDetailID','ProcessingNumber','RemainingVolume','DespatchVolume','ProductType'],
    pageSize: 100,
    autoLoad: true,
    remoteSort: true,
    storeVar: false,
    setStoreVar: function(value){
        this.storeVar = value;
    },
    proxy: {
        type: 'ajax',
        url: m_api + '/dispatch/transaction/fetchpick',
        reader: {
            type: 'json',
            root: 'data',
            totalProperty: 'total'
        }
    },
    listeners: {
        beforeload: function(store, operation, options){
            store.proxy.extraParams.DespatchID = this.storeVar.DespatchID;
            store.proxy.extraParams.ProductID = this.storeVar.ProductID;
        }, 
    }
});
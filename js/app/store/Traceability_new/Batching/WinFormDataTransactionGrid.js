Ext.define('Koltiva.store.Traceability_new.Batching.WinFormDataTransactionGrid', {
    extend: 'Ext.data.Store',
    storeId: 'Koltiva.store.Traceability_new.Batching.WinFormDataTransactionGrid',
    id: 'Koltiva.store.Traceability_new.Batching.WinFormDataTransactionGrid',
    fields: [
        'TransDetailID'
        ,'TransTypeName'
        ,'SupplyTypeName'
        ,'TransSupplyID'
        ,'MemberName'
        ,'SupplyTransNumber'
        ,'DateTransaction'
        ,'GrossWeight'
        ,'NettWeight'
        ,'SupplyType'
    ],
    autoLoad: true,
    proxy: {
        type: 'ajax',
        url: m_api + '/traceability_api/batching/grid_transaction',
        reader: {
            type: 'json',
            root: 'data'
        }
    },
    listeners: {
        'beforeload': function (store, options) {
            store.proxy.extraParams.MemberName           = this.storeVar.MemberName;
            store.proxy.extraParams.StartTransactionDate = this.storeVar.StartTransactionDate;
            store.proxy.extraParams.EndTransactionDate   = this.storeVar.EndTransactionDate;
            store.proxy.extraParams.SupplyType           = this.storeVar.SupplyType;  
        }
    }
});
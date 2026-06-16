Ext.define('Koltiva.store.Traceability_new.Batching.MainGridDataPurchase', {
    extend: 'Ext.data.Store',
    storeId: 'Koltiva.store.Traceability_new.Batching.MainGridDataPurchase',
    id: 'Koltiva.store.Traceability_new.Batching.MainGridDataPurchase',
    fields: [
        'SupplyBatchTransID'
        ,'SupplyBatchID'
        ,'TransDetailID'
        ,'TransTypeName'
        ,'SupplyType'
        ,'TransSupplyID'
        ,'MemberName'
        ,'SupplyTransNumber'
        ,'DateTransaction'
        ,{name : 'GrossWeight', type: 'float'}
        ,{name : 'NettWeight', type: 'float'}
        ,'StandardName'
        ,{name : 'TotalPayment', type: 'float'}
    ],
    autoLoad: true,
    proxy: {
        type: 'ajax',
        url: m_api + '/traceability_api/batching/data_supplychain_batch_transaction_main_grid',
        reader: {
            type: 'json',
            root: 'data'
        }
    },
    listeners: {
        'beforeload': function (store, options) {
            store.proxy.extraParams.SupplyBatchID = this.storeVar.SupplyBatchID;
        }
    }
});
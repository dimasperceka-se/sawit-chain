Ext.define('Koltiva.store.Traceability_new.Batching.MainGridDataPurchaseDetail', {
    extend: 'Ext.data.Store',
    storeId: 'Koltiva.store.Traceability_new.Batching.MainGridDataPurchaseDetail',
    id: 'Koltiva.store.Traceability_new.Batching.MainGridDataPurchaseDetail',
    fields: [
        'SupplyBatchProcessingID'
        ,'StepName'
        ,'ProcessingStepID'
        ,'ProcessStartDate'
        ,'ProcessEndDate'
        ,{name : 'WeightBefore', type: 'float'}
        ,{name : 'WeightAfter', type: 'float'}
        // ,{name : 'WeightChange', type: 'float'}
        // ,'WeightChange'
        ,'Remark'
    ],
    autoLoad: true,
    proxy: {
        type: 'ajax',
        url: m_api + '/traceability_api/batching/data_supplychain_batch_processing_main_grid',
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
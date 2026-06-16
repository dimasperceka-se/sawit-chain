Ext.define('Koltiva.store.Traceability_new.Delivery.MainGridDataPurchaseDetail', {
    extend: 'Ext.data.Store',
    storeId: 'Koltiva.store.Traceability_new.Delivery.MainGridDataPurchaseDetail',
    id: 'Koltiva.store.Traceability_new.Delivery.MainGridDataPurchaseDetail',
    fields: [
        'SupplyBatchProcessingID'
        ,'StepName'
        ,'ProcessingStepID'
        ,'ProcessStartDate'
        ,'ProcessEndDate'
        ,'WeightBefore'
        ,'WeightAfter'
        ,'Remark'
    ],
    autoLoad: true,
    proxy: {
        type: 'ajax',
        url: m_api + '/traceability_api/processing/data_supplychain_batch_processing_main_grid',
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
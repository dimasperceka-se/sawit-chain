Ext.define('Koltiva.store.Traceability_new.Delivery.WinFormDataDeliveryPickGrid', {
    extend: 'Ext.data.Store',
    storeId: 'Koltiva.store.Traceability_new.Delivery.WinFormDataDeliveryPickGrid',
    id: 'Koltiva.store.Traceability_new.Delivery.WinFormDataDeliveryPickGrid',
    fields: [
        'SupplyBatchID'
        ,'SupplyBatchNumber'
        ,'SupplyDestOrgName'
        ,'DeliveryDate'
        ,{name : 'DestWeight', type: 'float'}
        ,{name : 'DestPackage', type: 'float'}
        ,{name : 'FinalWeight', type: 'float'}
        ,{name : 'Remaining', type: 'float'}
        ,'SupplyBatchStatus'
        ,'RemainingWeight'
        ,'DateCreateBatch'
    ],
    autoLoad: true,
    proxy: {
        type: 'ajax',
        url: m_api + '/traceability_api/delivery/grid_pick_delivery',
        reader: {
            type: 'json',
            root: 'data'
        }
    },
    listeners: {
        'beforeload': function (store, options) {
            store.proxy.extraParams.SupplyBatchID        = this.storeVar.SupplyBatchID;
            store.proxy.extraParams.SupplyBatchNumber    = this.storeVar.SupplyBatchNumber;
            store.proxy.extraParams.StartDateCreateBatch = this.storeVar.StartDateCreateBatch;
            store.proxy.extraParams.EndDateCreateBatch   = this.storeVar.EndDateCreateBatch;
        }
    }
});
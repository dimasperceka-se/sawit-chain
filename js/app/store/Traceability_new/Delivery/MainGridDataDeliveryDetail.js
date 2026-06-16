Ext.define('Koltiva.store.Traceability_new.Delivery.MainGridDataDeliveryDetail', {
    extend: 'Ext.data.Store',
    storeId: 'Koltiva.store.Traceability_new.Delivery.MainGridDataDeliveryDetail',
    id: 'Koltiva.store.Traceability_new.Delivery.MainGridDataDeliveryDetail',
    fields: [
        'DeliveryDetailID'
        ,'DeliveryID'
        ,'SupplyBatchNumber'
        ,{name : 'Weight', type: 'float'}
        ,'RemainingWeight'
    ],
    autoLoad: true,
    proxy: {
        type: 'ajax',
        url: m_api + '/traceability_api/delivery/data_supplychain_delivery_detail_main_grid',
        reader: {
            type: 'json',
            root: 'data'
        }
    },
    listeners: {
        'beforeload': function (store, options) {
            store.proxy.extraParams.DeliveryID = this.storeVar.DeliveryID;
        }
    }
});
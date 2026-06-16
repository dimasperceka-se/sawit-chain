Ext.define('Koltiva.store.Traceability_new.Reception.MainGridDataDeliveryDetail', {
    extend: 'Ext.data.Store',
    storeId: 'Koltiva.store.Traceability_new.Reception.MainGridDataDeliveryDetail',
    id: 'Koltiva.store.Traceability_new.Reception.MainGridDataDeliveryDetail',
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
        url: m_api + '/traceability_api/delivery/data_supplychain_reception_detail_main_grid',
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
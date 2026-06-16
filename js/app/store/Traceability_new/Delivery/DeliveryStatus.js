Ext.define('Koltiva.store.Traceability_new.Delivery.DeliveryStatus', {
    extend: 'Ext.data.Store',
    storeId: 'Koltiva.store.Traceability_new.Delivery.DeliveryStatus',
    id: 'Koltiva.store.Traceability_new.Delivery.DeliveryStatus',
    fields: ['id', 'label'],
    autoLoad: true,
    proxy: {
        type: 'ajax',
        url: m_api + '/traceability_api/delivery/delivery_status',
        reader: {
            type: 'json',
            root: 'data'
        }
    }
});
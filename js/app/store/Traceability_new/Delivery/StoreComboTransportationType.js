Ext.define('Koltiva.store.Traceability_new.Delivery.StoreComboTransportationType', {
    extend: 'Ext.data.Store',
    storeId: 'Koltiva.store.Traceability_new.Delivery.StoreComboTransportationType',
    id: 'Koltiva.store.Traceability_new.Delivery.StoreComboTransportationType',
    fields: ['id', 'label'],
    autoLoad: true,
    proxy: {
        type: 'ajax',
        url: m_api + '/traceability_api/delivery/comboTransportationType',
        reader: {
            type: 'json',
            root: 'data'
        }
    }
    
});
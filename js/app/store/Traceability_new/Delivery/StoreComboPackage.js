Ext.define('Koltiva.store.Traceability_new.Delivery.StoreComboPackage', {
    extend: 'Ext.data.Store',
    storeId: 'Koltiva.store.Traceability_new.Delivery.StoreComboPackage',
    id: 'Koltiva.store.Traceability_new.Delivery.StoreComboPackage',
    fields: ['id', 'label', 'weight'],
    autoLoad: true,
    proxy: {
        type: 'ajax',
        url: m_api + '/traceability_api/delivery/comboPackage',
        reader: {
            type: 'json',
            root: 'data'
        }
    }
    
});
Ext.define('Koltiva.store.Traceability_new.Batching.ComboRoaster', {
    extend: 'Ext.data.Store',
    storeId: 'Koltiva.store.Traceability_new.Batching.ComboRoaster',
    id: 'Koltiva.store.Traceability_new.Batching.ComboRoaster',
    fields: ['id', 'label'],
    autoLoad: true,
    proxy: {
        type: 'ajax',
        url: m_api + '/traceability_api/batching/roaster',
        reader: {
            type: 'json',
            root: 'data'
        }
    }
    
});
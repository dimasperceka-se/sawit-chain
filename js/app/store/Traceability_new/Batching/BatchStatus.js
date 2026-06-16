Ext.define('Koltiva.store.Traceability_new.Batching.BatchStatus', {
    extend: 'Ext.data.Store',
    storeId: 'Koltiva.store.Traceability_new.Batching.BatchStatus',
    id: 'Koltiva.store.Traceability_new.Batching.BatchStatus',
    fields: ['id', 'label'],
    autoLoad: true,
    proxy: {
        type: 'ajax',
        url: m_api + '/traceability_api/batching/batch_status',
        reader: {
            type: 'json',
            root: 'data'
        }
    }
});
Ext.define('Koltiva.store.Traceability_new.Batching.BatchingStep', {
    extend: 'Ext.data.Store',
    storeId: 'Koltiva.store.Traceability_new.Batching.BatchingStep',
    id: 'Koltiva.store.Traceability_new.Batching.BatchingStep',
    fields: ['id', 'label'],
    autoLoad: true,
    proxy: {
        type: 'ajax',
        url: m_api + '/traceability_api/batching/batching_step',
        reader: {
            type: 'json',
            root: 'data'
        }
    }
});
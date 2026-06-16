Ext.define('Koltiva.store.Traceability_new.Batching.BatchingStepGroup', {
    extend: 'Ext.data.Store',
    storeId: 'Koltiva.store.Traceability_new.Batching.BatchingStepGroup',
    id: 'Koltiva.store.Traceability_new.Batching.BatchingStepGroup',
    fields: ['id', 'label'],
    autoLoad: true,
    proxy: {
        type: 'ajax',
        url: m_api + '/traceability_api/batching/batching_step_group',
        reader: {
            type: 'json',
            root: 'data'
        }
    }
    
});
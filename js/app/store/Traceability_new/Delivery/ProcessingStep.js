Ext.define('Koltiva.store.Traceability_new.Delivery.ProcessingStep', {
    extend: 'Ext.data.Store',
    storeId: 'Koltiva.store.Traceability_new.Delivery.ProcessingStep',
    id: 'Koltiva.store.Traceability_new.Delivery.ProcessingStep',
    fields: ['id', 'label'],
    autoLoad: true,
    proxy: {
        type: 'ajax',
        url: m_api + '/traceability_api/processing/processing_step',
        reader: {
            type: 'json',
            root: 'data'
        }
    }
});
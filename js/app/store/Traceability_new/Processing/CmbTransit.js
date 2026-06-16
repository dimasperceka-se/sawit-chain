Ext.define('Koltiva.store.Traceability_new.Processing.CmbTransit', {
    extend: 'Ext.data.Store',
    storeId: 'Koltiva.store.Traceability_new.Processing.CmbTransit',
    id: 'Koltiva.store.Traceability_new.Processing.CmbTransit',
    fields: ['id','label'],
    autoLoad: false,
    proxy: {
        type: 'ajax',
        url: m_api + '/processing/transaction/list_transit/',
        reader: {
            type: 'json',  
            root: 'data'
        }
    }
});
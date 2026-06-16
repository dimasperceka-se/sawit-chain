Ext.define('Koltiva.store.Traceability_new.Dispatch.CmbTransit', {
    extend: 'Ext.data.Store',
    storeId: 'Koltiva.store.Traceability_new.Dispatch.CmbTransit',
    id: 'Koltiva.store.Traceability_new.Dispatch.CmbTransit',
    fields: ['id','label'],
    autoLoad: false,
    proxy: {
        type: 'ajax',
        url: m_api + '/dispatch/transaction/list_transit/',
        reader: {
            type: 'json',  
            root: 'data'
        }
    }
});
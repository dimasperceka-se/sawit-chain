 Ext.define('Koltiva.store.Traceability_new.Dispatch.OwnerStatus', {
    extend: 'Ext.data.Store',
    storeId: 'Koltiva.store.Traceability_new.Dispatch.OwnerStatus',
    id: 'Koltiva.store.Traceability_new.Dispatch.OwnerStatus',
    fields: ['OwnerID','OwnerName'],
    autoLoad: true,
    proxy: {
        type: 'ajax',
        url: m_api + '/dispatch/transaction/OwnerStatus/',
        reader: {
            type: 'json'
        }
    },
});
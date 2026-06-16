 Ext.define('Koltiva.store.Traceability.Dispatch.OwnerStatus', {
    extend: 'Ext.data.Store',
    storeId: 'Koltiva.store.Traceability.Dispatch.OwnerStatus',
    id: 'Koltiva.store.Traceability.Dispatch.OwnerStatus',
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
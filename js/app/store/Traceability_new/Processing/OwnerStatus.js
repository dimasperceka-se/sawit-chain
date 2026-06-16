Ext.define('Koltiva.store.Traceability_new.Processing.OwnerStatus', {
    extend: 'Ext.data.Store',
    storeId: 'Koltiva.store.Traceability_new.Processing.OwnerStatus',
    id: 'Koltiva.store.Traceability_new.Processing.OwnerStatus',
    fields: ['OwnerID','OwnerName'],
    autoLoad: true,
    proxy: {
        type: 'ajax',
        url: m_api + '/processing/transaction/OwnerStatus/',
        reader: {
            type: 'json'
        }
    },
});
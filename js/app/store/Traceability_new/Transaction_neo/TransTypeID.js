Ext.define('Koltiva.store.Traceability_new.Transaction_neo.TransTypeID', {
    extend: 'Ext.data.Store',
    storeId: 'Koltiva.store.Traceability_new.Transaction_neo.TransTypeID',
    id: 'Koltiva.store.Traceability_new.Transaction_neo.TransTypeID',
    fields: ['id', 'label'],
    autoLoad: true,
    proxy: {
        type: 'ajax',
        url: m_api + '/traceability/transaction_neo/trans_type_id',
        reader: {
            type: 'json'
        }
    }
});
Ext.define('Koltiva.store.Traceability_new.Transaction_neo.Units', {
    extend: 'Ext.data.Store',
    storeId: 'Koltiva.store.Traceability_new.Transaction_neo.Units',
    id: 'Koltiva.store.Traceability_new.Transaction_neo.Units',
    fields: ['id', 'label'],
    autoLoad: true,
    proxy: {
        type: 'ajax',
        url: m_api + '/traceability/transaction_neo/unit',
        reader: {
            type: 'json',
            root: 'data'
        }
    }
});
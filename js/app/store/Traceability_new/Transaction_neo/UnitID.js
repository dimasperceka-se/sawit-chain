Ext.define('Koltiva.store.Traceability_new.Transaction_neo.UnitID', {
    extend: 'Ext.data.Store',
    storeId: 'Koltiva.store.Traceability_new.Transaction_neo.UnitID',
    id: 'Koltiva.store.Traceability_new.Transaction_neo.UnitID',
    fields: ['id', 'label'],
    autoLoad: true,
    proxy: {
        type: 'ajax',
        url: m_api + '/traceability/transaction_neo/unit_id',
        reader: {
            type: 'json'
        }
    }
});
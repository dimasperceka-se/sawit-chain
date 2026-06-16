Ext.define('Koltiva.store.Traceability_new.Transaction_neo.QualityID', {
    extend: 'Ext.data.Store',
    storeId: 'Koltiva.store.Traceability_new.Transaction_neo.QualityID',
    id: 'Koltiva.store.Traceability_new.Transaction_neo.QualityID',
    fields: ['id', 'label'],
    autoLoad: true,
    proxy: {
        type: 'ajax',
        url: m_api + '/traceability/transaction_neo/quality_id',
        reader: {
            type: 'json'
        }
    }
});
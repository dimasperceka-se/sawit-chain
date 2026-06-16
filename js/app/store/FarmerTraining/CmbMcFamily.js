Ext.define('Koltiva.store.FarmerTraining.CmbMcFamily', {
    extend: 'Ext.data.Store',
    id: 'Koltiva.store.FarmerTraining.CmbMcFamily',
    fields: ['id', 'label'],
    autoLoad: false,
    pageSize: 10,
    proxy: {
        type: 'ajax',
        url: m_store_family,
        reader: {
            type: 'json',
            root: 'data',
        }
    }
});
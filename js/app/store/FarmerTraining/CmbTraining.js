Ext.define('Koltiva.store.FarmerTraining.CmbTraining', {
    extend: 'Ext.data.Store',
    id: 'Koltiva.store.FarmerTraining.CmbTraining',
    fields: ['id', 'label'],
    autoLoad: true,
    pageSize: 50,
    proxy: {
        type: 'ajax',
        url: m_store_training,
        reader: {
            type: 'json',
            root: 'data',
            totalProperty: 'totalCount'
        }
    }
});
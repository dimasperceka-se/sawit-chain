Ext.define('Koltiva.store.FarmerTraining.CmbFasilitator', {
    extend: 'Ext.data.Store',
    id: 'Koltiva.store.FarmerTraining.CmbFasilitator',
    fields: ['id', 'label'],
    autoLoad: true,
    pageSize: 10,
    proxy: {
        type: 'ajax',
        url: m_store_fasilitator,
        reader: {
            type: 'json',
            root: 'data'
        }
    }
});
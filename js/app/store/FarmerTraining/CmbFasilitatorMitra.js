Ext.define('Koltiva.store.FarmerTraining.CmbFasilitatorMitra', {
    extend: 'Ext.data.Store',
    id: 'Koltiva.store.FarmerTraining.CmbFasilitatorMitra',
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
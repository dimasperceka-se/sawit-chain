Ext.define('Koltiva.store.MasterTraining.CmbTraining', {
    extend: 'Ext.data.Store',
    id: 'Koltiva.store.MasterTraining.CmbTraining',
    storeId: 'Koltiva.store.MasterTraining.CmbTraining',
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
Ext.define('Koltiva.store.MasterTraining.CmbServiceProvider', {
    extend: 'Ext.data.Store',
    id: 'Koltiva.store.MasterTraining.CmbServiceProvider',
    storeId: 'Koltiva.store.MasterTraining.CmbServiceProvider',
    fields: ['id', 'label'],
    autoLoad: true,
    proxy: {
        type: 'ajax',
        url: m_api + '/training_master/service_provider',
        reader: {
            type: 'json',
            root: 'data'
        }
    }
});
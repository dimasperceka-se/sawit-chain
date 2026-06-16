Ext.define('Koltiva.store.MasterTraining.CmbSubTopic', {
    extend: 'Ext.data.Store',
    id: 'Koltiva.store.MasterTraining.CmbSubTopic',
    storeId: 'Koltiva.store.MasterTraining.CmbSubTopic',
    fields: ['id', 'label'],
    autoLoad: false,
    proxy: {
        type: 'ajax',
        url: m_api + '/cpg/training_subtopic',
        reader: {
            type: 'json',
            root: 'data'
        }
    },
    listeners: {
        'beforeload': function (store, options) {
            store.proxy.extraParams.CpgTrainingsID = Ext.getCmp('training').getValue();
        }
    }
});
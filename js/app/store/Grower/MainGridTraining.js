Ext.define('Koltiva.store.Grower.MainGridTraining', {
    extend: 'Ext.data.Store',
    storeId: 'Koltiva.store.Grower.MainGridTraining',
    id: 'Koltiva.store.Grower.MainGridTraining',
    fields: [
        'BatchNumber',
        'CpgTrainings', 
        'sub_topic', 
        'TrainingStart',
        'TrainingEnd', 
        'TrainingStatus'
    ],
    autoLoad: false,
    proxy: {
        type: 'ajax',
        url: m_api + '/farmer/training_main_grid',
        reader: {
            type: 'json',
            root: 'data'
        }
    },
    listeners: {
        'beforeload': function (store, options) {
            store.proxy.extraParams.MemberID = this.storeVar.MemberID;
        }
    }
});
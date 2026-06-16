Ext.define('Koltiva.store.FarmerTraining.CmbCheckSurvey', {
    extend: 'Ext.data.Store',
    id: 'Koltiva.store.FarmerTraining.CmbCheckSurvey',
    fields: ['id', 'surveya'],
    autoLoad: true,
    proxy: {
        type: 'ajax',
        url: m_CekSurvey,
        reader: {
            type: 'json',
            root: 'data'
        }
    }
});
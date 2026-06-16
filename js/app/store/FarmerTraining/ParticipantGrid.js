Ext.define('Koltiva.store.FarmerTraining.ParticipantGrid', {
    extend: 'Ext.data.Store',
    model: 'Koltiva.model.FarmerTraining.ParticipantGrid',
    id: 'Koltiva.store.FarmerTraining.ParticipantGrid',
    //pageSize: 10000,
    proxy: {
        type: 'ajax',
        url: m_store_participant + 's',
        reader: {
            type: 'json',
            root: 'data',
            totalProperty: 'total'
        }
    }
});
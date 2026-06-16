Ext.define('Koltiva.store.MasterTraining.ParticipantGrid', {
    extend: 'Ext.data.Store',
    model: 'Koltiva.model.MasterTraining.ParticipantGrid',
    id: 'Koltiva.store.MasterTraining.ParticipantGrid',
    storeId: 'Koltiva.store.MasterTraining.ParticipantGrid',
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
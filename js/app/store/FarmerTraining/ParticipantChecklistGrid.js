Ext.define('Koltiva.store.FarmerTraining.ParticipantChecklistGrid', {
    extend: 'Ext.data.Store',
    id: 'Koltiva.store.FarmerTraining.ParticipantChecklistGrid',
    fields: ['DayNumber', 'Attendance1', 'Attendance2', 'TrainingDate'],
    autoLoad: false,
    proxy: {
        type: 'ajax',
        url: m_participant_checklist + 's',
        reader: {
            type: 'json',
            root: 'data',
            totalProperty: 'total'
        }
    }
});
Ext.define('Koltiva.store.FarmerTraining.ParticipantChecklistDayGrid', {
    extend: 'Ext.data.Store',
    id: 'Koltiva.store.FarmerTraining.ParticipantChecklistDayGrid',
    fields: ['FarmerID','FamilyID','FarmerName','AnggotaName','Attendance1','Attendance2'],
    autoLoad: false,
    proxy: {
        type: 'ajax',
        url: m_participant_checklist_day,
        reader: {
            type: 'json',
            root: 'data',
            totalProperty: 'total'
        }
    }
});
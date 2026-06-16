Ext.define('Koltiva.store.FarmerTraining.CmbDistrictParticipant', {
    extend: 'Ext.data.Store',
    id: 'Koltiva.store.FarmerTraining.CmbDistrictParticipant',
    fields: ['id', 'label'],
    autoLoad: true,
    pageSize: 10,
    proxy: {
        type: 'ajax',
        url: m_store_kabupaten,
        extraParams: {prov: m_param},
        reader: {
            type: 'json',
            root: 'data'
        }
    }
});
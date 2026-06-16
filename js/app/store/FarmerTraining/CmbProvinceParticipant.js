Ext.define('Koltiva.store.FarmerTraining.CmbProvinceParticipant', {
    extend: 'Ext.data.Store',
    id: 'Koltiva.store.FarmerTraining.CmbProvinceParticipant',
    fields: ['id', 'label'],
    autoLoad: true,
    pageSize: 10,
    proxy: {
        type: 'ajax',
        url: m_store_provinsi,
        extraParams: {prov: m_param},
        reader: {
            type: 'json',
            root: 'data'
        }
    }
});
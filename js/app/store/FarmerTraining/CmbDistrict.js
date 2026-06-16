Ext.define('Koltiva.store.FarmerTraining.CmbDistrict', {
    extend: 'Ext.data.Store',
    id: 'Koltiva.store.FarmerTraining.CmbDistrict',
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
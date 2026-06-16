Ext.define('Koltiva.store.MasterTraining.CmbDistrict', {
    extend: 'Ext.data.Store',
    id: 'Koltiva.store.MasterTraining.CmbDistrict',
    storeId: 'Koltiva.store.MasterTraining.CmbDistrict',
    fields: ['id', 'district'],
    autoLoad: true,
    pageSize: 10,
    proxy: {
        type: 'ajax',
        url: m_district_data,
        reader: {
            type: 'json',
            root: 'data'
        }
    }
});
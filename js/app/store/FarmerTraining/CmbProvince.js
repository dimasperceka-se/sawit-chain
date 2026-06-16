Ext.define('Koltiva.store.FarmerTraining.CmbProvince', {
    extend: 'Ext.data.Store',
    id: 'Koltiva.store.FarmerTraining.CmbProvince',
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
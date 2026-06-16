Ext.define('Koltiva.store.MasterTraining.CmbProvince', {
    extend: 'Ext.data.Store',
    id: 'Koltiva.store.MasterTraining.CmbProvince',
    storeId: 'Koltiva.store.MasterTraining.CmbProvince',
    fields: ['id', 'label'],
    autoLoad: true,
    pageSize: 10,
    proxy: {
        type: 'ajax',
        url: m_store_provinsi,
        reader: {
            type: 'json',
            root: 'data'
        }
    }
});
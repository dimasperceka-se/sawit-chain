Ext.define('Koltiva.store.MasterTraining.CmbFilterDistrict', {
    extend: 'Ext.data.Store',
    id: 'Koltiva.store.MasterTraining.CmbFilterDistrict',
    storeId: 'Koltiva.store.MasterTraining.CmbFilterDistrict',
    fields: ['id', 'label'],
    autoLoad: true,
    pageSize: 10,
    proxy: {
        type: 'ajax',
        url: m_store_kabupaten,
        extraParams: {
            prov: m_param
        },
        reader: {
            type: 'json',
            root: 'data'
        }
    }
});
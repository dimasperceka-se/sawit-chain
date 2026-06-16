Ext.define('Koltiva.store.FarmerTraining.CmbFamily', {
    extend: 'Ext.data.Store',
    id: 'Koltiva.store.FarmerTraining.CmbFamily',
    fields: ['id', 'label'],
    autoLoad: true,
    pageSize: 10,
    proxy: {
        type: 'ajax',
        url: m_store_family,
        reader: {
            type: 'json',
            root: 'data',
            totalProperty: 'totalCount'
        }
    }
});
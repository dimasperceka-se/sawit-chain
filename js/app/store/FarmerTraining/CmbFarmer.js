Ext.define('Koltiva.store.FarmerTraining.CmbFarmer', {
    extend: 'Ext.data.Store',
    id: 'Koltiva.store.FarmerTraining.CmbFarmer',
    fields: ['id', 'label'],
    autoLoad: false,
    pageSize: 10,
    proxy: {
        type: 'ajax',
        url: m_store_farmer,
        // extraParams: {prov: m_param},
        reader: {
            type: 'json',
            root: 'data',
            totalProperty: 'totalCount'
        }
    }
});
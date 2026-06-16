Ext.define('Koltiva.store.MasterTraining.CmbFasilitator', {
    extend: 'Ext.data.Store',
    id: 'Koltiva.store.MasterTraining.CmbFasilitator',
    storeId: 'Koltiva.store.MasterTraining.CmbFasilitator',
    fields: ['id', 'label'],
    autoLoad: true,
    pageSize: 10,
    proxy: {
        type: 'ajax',
        url: m_store_fasilitator,
        extraParams: {
            workarea: m_param
        },
        reader: {
            type: 'json',
            root: 'data'
        }
    }
});
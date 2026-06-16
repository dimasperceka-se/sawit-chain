Ext.define('Koltiva.store.MasterTraining.CmbStaff', {
    extend: 'Ext.data.Store',
    id: 'Koltiva.store.MasterTraining.CmbStaff',
    storeId: 'Koltiva.store.MasterTraining.CmbStaff',
    fields: ['id', 'label'],
    autoLoad: true,
    pageSize: 10,
    proxy: {
        type: 'ajax',
        url: m_store_staff,
        reader: {
            type: 'json',
            root: 'data'
        }
    }
});
Ext.define('Koltiva.store.trainings.parent',{
    extend: 'Ext.data.Store',
    storeId: 'koltiva-trainings-parent-store',
    model: 'Koltiva.model.combo',
    autoLoad: true,
    pageSize: 50,
    proxy: {
        type: 'ajax',
        url: m_parent,
        reader: {
            type: 'json',
            root: 'data',
            // totalProperty: 'total'
        }
    }
});

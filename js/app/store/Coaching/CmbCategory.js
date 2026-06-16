Ext.define('Koltiva.store.Coaching.CmbCategory', {
    extend: 'Ext.data.Store',
    storeId: 'Koltiva.store.Coaching.CmbCategory',
    id: 'Koltiva.store.Coaching.CmbCategory',
    fields: ['id', 'label'],
    autoLoad: true,
    proxy: {
        type: 'ajax',
        url: m_api + '/coaching/cmb_category',
        reader: {
            type: 'json',
            root: 'data'
        }
    }
});
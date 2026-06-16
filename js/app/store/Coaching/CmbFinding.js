Ext.define('Koltiva.store.Coaching.CmbFinding', {
    extend: 'Ext.data.Store',
    storeId: 'Koltiva.store.Coaching.CmbFinding',
    id: 'Koltiva.store.Coaching.CmbFinding',
    fields: ['id', 'label'],
    autoLoad: false,
    proxy: {
        type: 'ajax',
        url: m_api + '/coaching/cmb_coaching_finding',
        reader: {
            type: 'json',
            root: 'data'
        }
    }
});
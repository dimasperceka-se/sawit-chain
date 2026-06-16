Ext.define('Koltiva.store.Coaching.CmbRecommendation', {
    extend: 'Ext.data.Store',
    storeId: 'Koltiva.store.Coaching.CmbRecommendation',
    id: 'Koltiva.store.Coaching.CmbRecommendation',
    fields: ['id', 'label'],
    autoLoad: false,
    proxy: {
        type: 'ajax',
        url: m_api + '/coaching/cmb_coaching_recomm',
        reader: {
            type: 'json',
            root: 'data'
        }
    }
});
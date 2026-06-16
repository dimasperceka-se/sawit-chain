Ext.define('Koltiva.store.Coaching.CmbCoachingTopic', {
    extend: 'Ext.data.Store',
    storeId: 'Koltiva.store.Coaching.CmbCoachingTopic',
    id: 'Koltiva.store.Coaching.CmbCoachingTopic',
    fields: ['id', 'label'],
    autoLoad: true,
    proxy: {
        type: 'ajax',
        url: m_api + '/coaching/cmb_coaching_topic',
        reader: {
            type: 'json',
            root: 'data'
        }
    }
});
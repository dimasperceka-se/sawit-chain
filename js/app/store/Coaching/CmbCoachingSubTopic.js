Ext.define('Koltiva.store.Coaching.CmbCoachingSubTopic', {
    extend: 'Ext.data.Store',
    storeId: 'Koltiva.store.Coaching.CmbCoachingSubTopic',
    id: 'Koltiva.store.Coaching.CmbCoachingSubTopic',
    fields: ['id', 'label'],
    autoLoad: false,
    proxy: {
        type: 'ajax',
        url: m_api + '/coaching/cmb_coaching_subtopic',
        reader: {
            type: 'json',
            root: 'data'
        }
    }
});
Ext.define('Koltiva.store.Grower.MainGridCoaching', {
    extend: 'Ext.data.Store',
    storeId: 'Koltiva.store.Grower.MainGridCoaching',
    id: 'Koltiva.store.Grower.MainGridCoaching',
    fields: [
        'CoachingRecipient'
        ,'CoachingRecipientName'
        ,'CoachingDate'
        ,'TimeStart'
        ,'TimeEnd'
    ],
    autoLoad: false,
    proxy: {
        type: 'ajax',
        url: m_api + '/farmer/coaching_main_grid',
        reader: {
            type: 'json',
            root: 'data'
        }
    },
    listeners: {
        'beforeload': function(store, options) {
            store.proxy.extraParams.MemberID = this.storeVar.MemberID;
        }
    }
});
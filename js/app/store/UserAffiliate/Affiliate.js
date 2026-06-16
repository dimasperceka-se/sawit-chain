Ext.define('Koltiva.store.UserAffiliate.Affiliate', {
    extend: 'Ext.data.Store',
    id: 'Koltiva.store.UserAffiliate.Affiliate',
    storeId: 'Koltiva.store.UserAffiliate.Affiliate',
    fields: ['UserId', 'UserRealName'],
    autoLoad: true,
    proxy: {
        type: 'ajax',
        url: m_api + '/system/other_users',
        reader: {
            type: 'json',
            root: 'data'
        }
    }
});
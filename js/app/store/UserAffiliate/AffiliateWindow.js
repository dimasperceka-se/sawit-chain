Ext.define('Koltiva.store.UserAffiliate.AffiliateWindow', {
    extend: 'Ext.data.Store',
    id: 'Koltiva.store.UserAffiliate.AffiliateWindow',
    fields: ['UserId', 'UserName', 'UserRealName', 'UserActive'],
    autoLoad: true,
    storeVar: {},
    setStoreVar: function(value){
        this.storeVar = value;
    },
    proxy: {
        type: 'ajax',
        url: m_api + '/system/other_users',
        reader: {
            type: 'json',
            root: 'data'
        }
    },
    listeners: {
        beforeload: function(store, operation, options){
            store.proxy.extraParams.UserId = this.storeVar.UserId;
            store.proxy.extraParams.key = this.storeVar.key;
        }
    }
});
Ext.define('Koltiva.store.UserAffiliate.AffiliatedGrid', {
    extend: 'Ext.data.Store',
    id: 'Koltiva.store.UserAffiliate.AffiliatedGrid',
    fields: ['UserId', 'UserName', 'UserRealName', 'UserActive'],
    autoLoad: true,
    storeVar: {},
    setStoreVar: function(value){
        this.storeVar = value;
    },
    proxy: {
        type: 'ajax',
        url: m_api + '/system/user_affiliated',
        reader: {
            type: 'json',
            root: 'data'
        }
    },
    listeners: {
        beforeload: function(store, operation, options){
            store.proxy.extraParams.UserId = this.storeVar.UserId;
        }
    }
});
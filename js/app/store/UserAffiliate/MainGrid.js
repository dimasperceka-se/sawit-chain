Ext.define('Koltiva.store.UserAffiliate.MainGrid', {
    extend: 'Ext.data.Store',
    id: 'Koltiva.store.UserAffiliate.MainGrid',
    fields: ['UserId','UserId', 'UserRealName', 'UserName', 'UserActive', 'Affiliated'],
    autoLoad: true,
    pageSize: 50,
    storeVar: false,
    setStoreVar: function(value){
        this.storeVar = value;
    },
    proxy: {
        type: 'ajax',
        url: m_crud + 's',
        reader: {
            type: 'json',
            root: 'data',
            totalProperty: 'total'
        }
    },
    listeners: {
        beforeload: function(store, operation) { 
            this.proxy.extraParams.key = Ext.getCmp('Koltiva.view.UserAffiliate.MainGrid.key').getValue();
        }
    }
});
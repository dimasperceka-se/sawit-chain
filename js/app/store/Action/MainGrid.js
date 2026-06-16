Ext.define('Koltiva.store.Action.MainGrid', {
    extend: 'Ext.data.Store',
    id: 'Koltiva.store.Action.MainGrid',
    fields: ['AksiId', 'AksiName', 'AksiFungsi'],
    autoLoad: true,
    pageSize: 50,
    storeVar: false,
    setStoreVar: function(value){
        this.storeVar = value;
    },
    proxy: {
        type: 'ajax',
        url: m_crud,
        reader: {
            type: 'json',
            root: 'data',
            totalProperty: 'total'
        }
    },
    listeners: {
        beforeload: function(store, operation) { 
            this.proxy.extraParams.key = Ext.getCmp('Koltiva.view.Action.MainGrid.key').getValue();
        }
    }
});
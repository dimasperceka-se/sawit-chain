Ext.define('Koltiva.store.ComboGeneral.ComboStatus', {
    extend: 'Ext.data.Store',
    id: 'Koltiva.store.ComboGeneral.ComboStatus',
    storeId: 'Koltiva.store.ComboGeneral.ComboStatus',
    fields: ['TransactionStatusID','TransactionStatusName'],
    autoLoad: true,
    storeVar: false,
    setStoreVar: function(value){
        this.storeVar = value;
    },
    proxy: {
        type: 'ajax', 
        url : m_api + '/common/combo_transactionstatus', 
		reader: {
            type: 'json',  
            root: 'data'
        }
    },
    pageSize: 10,
    listeners: {
		beforeload: function(store, operation, options){
            
        },  
    }
});
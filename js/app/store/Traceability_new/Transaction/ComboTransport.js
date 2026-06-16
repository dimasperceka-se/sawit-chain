Ext.define('Koltiva.store.Traceability_new.Transaction.ComboTransport', {
    extend: 'Ext.data.Store',
    id: 'Koltiva.store.Traceability_new.Transaction.ComboTransport',
    storeId: 'Koltiva.store.Traceability_new.Transaction.ComboTransport',
    fields: ['DestTransportID','DestTransportName','IsDetail'],
    autoLoad: true,
    storeVar: false,
    setStoreVar: function(value){
        this.storeVar = value;
    },
    proxy: {
        type: 'ajax', 
        url : m_api + '/reference/transport', 
		reader: {
            type: 'json',  
            root: 'data'
        }
    },
    pageSize: 10,
    listeners: {
         
    }
});
 
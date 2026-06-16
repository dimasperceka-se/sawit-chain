Ext.define('Koltiva.store.Traceability.Dispatch.CmbDestination', {
    extend: 'Ext.data.Store',
    id: 'Koltiva.store.Traceability.Dispatch.CmbDestination',
    storeId: 'Koltiva.store.Traceability.Dispatch.CmbDestination',
    fields: ['DestinationID','DestinationName'],
    autoLoad: true,
    storeVar: false,
    setStoreVar: function(value){
        this.storeVar = value;
    },
    proxy: {
        type: 'ajax', 
        url : m_api + '/dispatch/transaction/list_destination', 
		reader: {
            type: 'json',  
            root: 'data'
        }
    },
    listeners: {
		beforeload: function(store, operation, options){
            
        },  
    }
});
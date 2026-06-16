Ext.define('Koltiva.store.Traceability_new.Processing.CmbDestination', {
    extend: 'Ext.data.Store',
    id: 'Koltiva.store.Traceability_new.Processing.CmbDestination',
    storeId: 'Koltiva.store.Traceability_new.Processing.CmbDestination',
    fields: ['DestinationID','DestinationName'],
    autoLoad: true,
    storeVar: false,
    setStoreVar: function(value){
        this.storeVar = value;
    },
    proxy: {
        type: 'ajax', 
        url : m_api + '/processing/transaction/list_destination', 
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
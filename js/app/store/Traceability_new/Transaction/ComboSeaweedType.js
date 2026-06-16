Ext.define('Koltiva.store.Traceability_new.Transaction.ComboSeaweedType', {
    extend: 'Ext.data.Store',
    id: 'Koltiva.store.Traceability_new.Transaction.ComboSeaweedType',
    storeId: 'Koltiva.store.Traceability_new.Transaction.ComboSeaweedType',
    fields: ['PalmoilTypeID','SeaweedTypeName'],
    autoLoad: false,
    storeVar: false,
    setStoreVar: function(value){
        this.storeVar = value;
    },
    proxy: {
        type: 'ajax', 
        url : m_api + '/reference/seaweed-type', 
		reader: {
            type: 'json',  
            root: 'data'
        }
    },
    pageSize: 10,
    listeners: {
        beforeload: function (storeComboSeaweedType, operation) {
             
        }
    }
});
 
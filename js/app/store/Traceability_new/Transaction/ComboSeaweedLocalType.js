Ext.define('Koltiva.store.Traceability_new.Transaction.ComboSeaweedLocalType', {
    extend: 'Ext.data.Store',
    id: 'Koltiva.store.Traceability_new.Transaction.ComboSeaweedLocalType',
    storeId: 'Koltiva.store.Traceability_new.Transaction.ComboSeaweedLocalType',
    fields: ['DetailTypeID','DetailTypeCode'],
    autoLoad: false,
    storeVar: false,
    setStoreVar: function(value){
        this.storeVar = value;
    },
    proxy: {
        type: 'ajax', 
        url : m_api + '/reference/seaweed-type-detail', 
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
 
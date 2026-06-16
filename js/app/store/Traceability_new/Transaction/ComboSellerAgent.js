Ext.define('Koltiva.store.Traceability_new.Transaction.ComboSellerAgent', {
    extend: 'Ext.data.Store',
    id: 'Koltiva.store.Traceability_new.Transaction.ComboSellerAgent',
    storeId: 'Koltiva.store.Traceability_new.Transaction.ComboSellerAgent',
    fields: ['ObjID','Name'],
    autoLoad: false,
    storeVar: false,
    setStoreVar: function(value){
        this.storeVar = value;
    },
    proxy: {
        type: 'ajax', 
        url : m_api + '/web-traceability/selleragent', 
		reader: {
            type: 'json',  
            root: 'data'
        }
    }
});
 
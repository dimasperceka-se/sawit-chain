Ext.define('Koltiva.store.Traceability_new.Transaction.ComboSellerMill', {
    extend: 'Ext.data.Store',
    id: 'Koltiva.store.Traceability_new.Transaction.ComboSellerMill',
    storeId: 'Koltiva.store.Traceability_new.Transaction.ComboSellerMill',
    fields: ['ObjID','Name'],
    autoLoad: false,
    storeVar: false,
    setStoreVar: function(value){
        this.storeVar = value;
    },
    proxy: {
        type: 'ajax', 
        url : m_api + '/web-traceability/sellermill', 
		reader: {
            type: 'json',  
            root: 'data'
        }
    }
});
 
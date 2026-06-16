Ext.define('Koltiva.store.Traceability_new.Transaction.ComboSellerDO', {
    extend: 'Ext.data.Store',
    id: 'Koltiva.store.Traceability_new.Transaction.ComboSellerDO',
    storeId: 'Koltiva.store.Traceability_new.Transaction.ComboSellerDO',
    fields: ['ObjID','Name'],
    autoLoad: false,
    storeVar: false,
    setStoreVar: function(value){
        this.storeVar = value;
    },
    proxy: {
        type: 'ajax', 
        url : m_api + '/web-traceability/sellerdo', 
		reader: {
            type: 'json',  
            root: 'data'
        }
    }
});
 
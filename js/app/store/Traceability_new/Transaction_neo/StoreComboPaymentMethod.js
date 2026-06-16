Ext.define('Koltiva.store.Traceability_new.Transaction_neo.StoreComboPaymentMethod', {
    extend: 'Ext.data.Store',
    storeId:'Koltiva.store.Traceability_new.Transaction_neo.StoreComboPaymentMethod',
    fields: ['id','label'],
    autoLoad: true,
    proxy: {
        type: 'ajax',
        url: m_api + '/traceability_api/web_transaction/fetch_combo_payment_method',
        reader: {
            type: 'json',
            root: 'data'
        } 
    },
    listeners: {
        beforeload: function(store, operation, options){
            
        }
    }
});
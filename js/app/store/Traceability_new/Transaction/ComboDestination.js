Ext.define('Koltiva.store.Traceability_new.Transaction.ComboDestination', {
    extend: 'Ext.data.Store',
    id: 'Koltiva.store.Traceability_new.Transaction.ComboDestination',
    storeId: 'Koltiva.store.Traceability_new.Transaction.ComboDestination',
    fields: ['SupplychainID', 'Name'],
    autoLoad: true,
    storeVar: false,
    setStoreVar: function(value){
        this.storeVar = value;
    },
    proxy: {
        type: 'ajax',
        url: m_api + '/web-traceability/destination',
        reader: {
            type: 'json',
            root: 'data'
        }
    },
	listeners: {
        beforeload: function (store, operation) { 
			store.proxy.extraParams.SID = m_sid;
        }
    }
});
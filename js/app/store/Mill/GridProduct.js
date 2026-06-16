Ext.define('Koltiva.store.Mill.GridProduct', {
    extend: 'Ext.data.Store',
    id: 'Koltiva.store.Mill.GridProduct',
    storeId: 'Koltiva.store.Mill.GridProduct',
    fields: ['SupplychainProductID','SupplychainID', 'ProductID','ProductPercentage', 'StartDate', 'EndDate', 'StatusCode','CreatedBy','DateCreated','LastModifiedBy','DateUpdated','ProductName'],
    pageSize: 12,
    autoLoad: false,
    remoteSort: true,
    storeVar: false,
    setStoreVar: function(value){
        this.storeVar = value;
    },
    proxy: {
        type: 'ajax',
        url: m_api + '/traceability_api/Supplychain_product/fetch_mill',
        reader: {
            type: 'json',
            root: 'data',
            totalProperty: 'total'
        }
    },
	listeners :
	{ 
		beforeload: function(store, operation, options){
            store.proxy.extraParams.SupplychainID = Ext.getCmp('Koltiva.view.Mill.FormMainMill-FormBasicData-SupplychainID').getValue();  
            store.proxy.extraParams.SupplychainProductID = this.storeVar.SupplychainProductID;
        }
	}
});
 
Ext.define('Koltiva.store.Traceability_new.Reference.Supplychain_product.MainGrid', {
    extend: 'Ext.data.Store',
    id: 'Koltiva.store.Traceability_new.Reference.Supplychain_product.MainGrid',
    storeId: 'Koltiva.store.Traceability_new.Reference.Supplychain_product.MainGrid',
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
        url: m_api + '/traceability_api/Supplychain_product/fetch',
        reader: {
            type: 'json',
            root: 'data',
            totalProperty: 'total'
        }
    },
	listeners :
	{ 
		beforeload: function(store, operation, options){
            store.proxy.extraParams.SupplychainID = Ext.getCmp('Koltiva.view.Traceability_new.Reference.Supplychain_org-dataForm-SupplychainID').getValue();  
        }
	}
});
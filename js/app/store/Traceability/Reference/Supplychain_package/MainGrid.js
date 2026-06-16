 
Ext.define('Koltiva.store.Traceability.Reference.Supplychain_package.MainGrid', {
    extend: 'Ext.data.Store',
    id: 'Koltiva.store.Traceability.Reference.Supplychain_package.MainGrid',
    storeId: 'Koltiva.store.Traceability.Reference.Supplychain_package.MainGrid',
    fields: ['PackageID', 'SupplychainID', 'Obj', 'PackageType', 'PackageWeight', 'PackageCapacity', 'DefaultPackage', 'StatusCode', 'DateCreated', 'CreatedBy', 'DateUpdated', 'LastModifiedBy'],
    pageSize: 12,
    autoLoad: true,
    remoteSort: true,
    storeVar: false,
    setStoreVar: function(value){
        this.storeVar = value;
    },
    proxy: {
        type: 'ajax',
        url: m_api + '/traceability_api/Supplychain_package/fetch',
        reader: {
            type: 'json',
            root: 'data',
            totalProperty: 'total'
        }
    },
	listeners :
	{ 
		beforeload: function(store, operation, options){
            store.proxy.extraParams.SupplychainID = Ext.getCmp('Koltiva.view.Traceability.Reference.Supplychain_org-dataForm-SupplychainID').getValue();  
        }
	}
});
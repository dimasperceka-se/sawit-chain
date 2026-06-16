 
Ext.define('Koltiva.store.Traceability.Reference.Supplychain_org_rel.MainGrid', {
    extend: 'Ext.data.Store',
    id: 'Koltiva.store.Traceability.Reference.Supplychain_org_rel.MainGrid',
    storeId: 'Koltiva.store.Traceability.Reference.Supplychain_org_rel.MainGrid',
    fields: ['RelID', 'ParentID', 'Parent', 'ChildID', 'Child','StartDate','EndDate', 'StatusCode', 'CreatedBy', 'DateCreated', 'LastModifiedBy', 'DateUpdated'],
    pageSize: 12,
    autoLoad: false,
    remoteSort: true,
    storeVar: false,
    setStoreVar: function(value){
        this.storeVar = value;
    },
    proxy: {
        type: 'ajax',
        url: m_api + '/traceability_api/Supplychain_org_rel/fetch',
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
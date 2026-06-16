 
Ext.define('Koltiva.store.Traceability_new.Reference.Supplychain_org_rel.MainGrid', {
    extend: 'Ext.data.Store',
    id: 'Koltiva.store.Traceability_new.Reference.Supplychain_org_rel.MainGrid',
    storeId: 'Koltiva.store.Traceability_new.Reference.Supplychain_org_rel.MainGrid',
    fields: ['RelID', 'ParentID','ObjTypeParent', 'Parent', 'ChildID', 'Child','StartDate','EndDate', 'Status', 'StatusCode', 'CreatedBy', 'DateCreated', 'LastModifiedBy', 'DateUpdated','PartnerID','Partner','ObjType'],
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
            store.proxy.extraParams.SupplychainID = Ext.getCmp('Koltiva.view.Traceability_new.Reference.Supplychain_org-dataForm-SupplychainID').getValue();  
        }
	}
});
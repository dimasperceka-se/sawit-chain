 
Ext.define('Koltiva.store.Traceability.Reference.Supplychain_org_rel.cmbObject', {
    extend: 'Ext.data.Store',
    storeId: 'Koltiva.store.Traceability.Reference.Supplychain_org_rel.cmbObject',
    id: 'Koltiva.store.Traceability.Reference.Supplychain_org_rel.cmbObject',
    fields: ['SupplychainID', 'ObjType', 'ObjID', 'Obj', 'Name', 'PartnerID', 'IsFarmer', 'IsBatch', 'IsSent', 'StatusCode', 'DateCreated', 'CreatedBy', 'DateUpdated', 'LastModifiedBy'],
    autoLoad: true,
    proxy: {
        type: 'ajax',
        url: m_api + '/traceability_api/Supplychain_org/sid', 
        reader: {
            type: 'json',
            root: 'data',
            totalProperty: 'total'
        }
    },
	listeners :
	{ 
		beforeload: function(store, operation, options){
            store.proxy.extraParams.PartnerID = Ext.getCmp('Koltiva.view.Traceability.Reference.Supplychain_org-dataForm-PartnerID').getValue(); 
			store.proxy.extraParams.ObjID = Ext.getCmp('Koltiva.view.Traceability.Reference.Supplychain_org-dataForm-ObjID').getValue(); 
        }
	}
});
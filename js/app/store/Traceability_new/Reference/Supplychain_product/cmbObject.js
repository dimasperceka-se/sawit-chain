 
Ext.define('Koltiva.store.Traceability_new.Reference.Supplychain_product.cmbObject', {
    extend: 'Ext.data.Store',
    storeId: 'Koltiva.store.Traceability_new.Reference.Supplychain_product.cmbObject',
    id: 'Koltiva.store.Traceability_new.Reference.Supplychain_product.cmbObject',
    fields: ['SupplychainProductID', 'ProductID', 'ProductName', 'StatusCode', 'CreatedBy', 'DateCreated', 'LastModifiedBy', 'DateUpdated'],
    autoLoad: true,
    proxy: {
        type: 'ajax',
        url: m_api + '/traceability_api/Supplychain_product/sid', 
        reader: {
            type: 'json',
            root: 'data',
            totalProperty: 'total'
        }
    },
	listeners :
	{ 
		beforeload: function(store, operation, options){
            //store.proxy.extraParams.PartnerID = Ext.getCmp('Koltiva.view.Traceability_new.Reference.Supplychain_org-dataForm-PartnerID').getValue(); 
			//store.proxy.extraParams.ObjID = Ext.getCmp('Koltiva.view.Traceability_new.Reference.Supplychain_org-dataForm-ObjID').getValue(); 
        }
	}
});
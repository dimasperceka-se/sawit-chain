 
Ext.define('Koltiva.store.Refinery.GridProductProfile', {
    extend: 'Ext.data.Store',
    id: 'Koltiva.store.Refinery.GridProductProfile',
    storeId: 'Koltiva.store.Refinery.GridProductProfile',
    fields: ['SupplychainProductID','SupplychainID', 'ProductID','OilType','ProductPercentage', 'StartDate', 'EndDate', 'StatusCode','CreatedBy','DateCreated','LastModifiedBy','DateUpdated','ProductName'],
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
            store.proxy.extraParams.SupplychainID = Ext.getCmp('Koltiva.view.Refinery.FormMainRefineryProfile-FormBasicData-SupplychainID').getValue(); 
            store.proxy.extraParams.SupplychainProductID = this.storeVar.SupplychainProductID;
        }
	}
});
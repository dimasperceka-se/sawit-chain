 
Ext.define('Koltiva.store.Traceability_new.Reference.Supplychain_quality.MainGrid', {
    extend: 'Ext.data.Store',
    id: 'Koltiva.store.Traceability_new.Reference.Supplychain_quality.MainGrid',
    storeId: 'Koltiva.store.Traceability_new.Reference.Supplychain_quality.MainGrid',
    fields: ['QualityID', 'SupplychainID', 'Name', 'Obj', 'Formula', 'Order', 'Type', 'MinValue', 'MaxValue', 'StandardValue',  
             'IsPrintVisible', 'PrintVisible', 'StatusCode','StartDate', 'EndDate', 'DateCreated', 'CreatedBy', 'DateUpdated'],
    pageSize: 12,
    autoLoad: true,
    remoteSort: true,
    storeVar: false,
    setStoreVar: function(value){
        this.storeVar = value;
    },
    proxy: {
        type: 'ajax',
        url: m_api + '/traceability_api/Supplychain_quality/fetch',
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
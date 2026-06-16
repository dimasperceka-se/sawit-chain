 
Ext.define('Koltiva.store.Traceability.Reference.Supplychain_area.MainGrid', {
    extend: 'Ext.data.Store',
    id: 'Koltiva.store.Traceability.Reference.Supplychain_area.MainGrid',
    storeId: 'Koltiva.store.Traceability.Reference.Supplychain_area.MainGrid',
    fields: ['SupplychainAreaID','ProvinceID','Province', 'DistrictID','District','SupplychainID','DateStart','DateEnd'],
    pageSize: 12,
    autoLoad: false,
    remoteSort: true,
    storeVar: false,
    setStoreVar: function(value){
        this.storeVar = value;
    },
    proxy: {
        type: 'ajax',
        url: m_api + '/traceability/Supplychain_area/fetch',
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
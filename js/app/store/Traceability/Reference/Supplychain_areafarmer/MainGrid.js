 
Ext.define('Koltiva.store.Traceability.Reference.Supplychain_areafarmer.MainGrid', {
    extend: 'Ext.data.Store',
    id: 'Koltiva.store.Traceability.Reference.Supplychain_areafarmer.MainGrid',
    storeId: 'Koltiva.store.Traceability.Reference.Supplychain_areafarmer.MainGrid',
    fields: ['SupplychainFarmerID','DateStart','DateEnd', 'SupplychainID','MemberID','MemberDisplayID','MemberName','Desa','Kecamatan','Province','District'],
    pageSize: 12,
    autoLoad: false,
    remoteSort: true,
    storeVar: false,
    setStoreVar: function(value){
        this.storeVar = value;
    },
    proxy: {
        type: 'ajax',
        url: m_api + '/traceability/Supplychain_areafarmer/fetch',
        reader: {
            type: 'json',
            root: 'data',
            totalProperty: 'total'
        }
    },
	listeners :
	{ 
		beforeload: function(store, operation, options){
            store.proxy.extraParams.SupplychainID	 = Ext.getCmp('Koltiva.view.Traceability.Reference.Supplychain_org-dataForm-SupplychainID').getValue();  
        }
	}
});
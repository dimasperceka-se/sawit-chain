 
Ext.define('Koltiva.store.Traceability.Reference.Supplychain_areafarmer.winGridFarmer', {
    extend: 'Ext.data.Store',
    id: 'Koltiva.store.Traceability.Reference.Supplychain_areafarmer.winGridFarmer',
    storeId: 'Koltiva.store.Traceability.Reference.Supplychain_areafarmer.winGridFarmer',
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
        url: m_api + '/traceability/Supplychain_areafarmer/fetchallFarmer',
        reader: {
            type: 'json',
            root: 'data',
            totalProperty: 'total'
        }
    },
	listeners :
	{ 
		beforeload: function(store, operation, options){
			store.proxy.extraParams.DateStart = Ext.getCmp('Koltiva.view.Traceability.Supplychain_areafarmer.WinpilihanFarmer-DateStart').getValue();  
			store.proxy.extraParams.DateEnd = Ext.getCmp('Koltiva.view.Traceability.Supplychain_areafarmer.WinpilihanFarmer-DateEnd').getValue();  
            store.proxy.extraParams.SupplychainID = Ext.getCmp('Koltiva.view.Traceability.Reference.Supplychain_org-dataForm-SupplychainID').getValue();  
			store.proxy.extraParams.ComboProvince 	 = Ext.getCmp('Koltiva.view.Traceability.Supplychain_areafarmer.WinpilihanFarmer-gridToolbar-ComboProvince').getValue(),
		    store.proxy.extraParams.ComboDistrict 	 = Ext.getCmp('Koltiva.view.Traceability.Supplychain_areafarmer.WinpilihanFarmer-gridToolbar-ComboDistrict').getValue(), 
		    store.proxy.extraParams.ComboSubDistrict = Ext.getCmp('Koltiva.view.Traceability.Supplychain_areafarmer.WinpilihanFarmer-gridToolbar-ComboSubDistrict').getValue(),
		    store.proxy.extraParams.ComboVillage     = Ext.getCmp('Koltiva.view.Traceability.Supplychain_areafarmer.WinpilihanFarmer-gridToolbar-ComboVillage').getValue()
        }
	}
});
 
Ext.define('Koltiva.store.Traceability_new.Reference.Supplychain_areafarmer.MainGrid', {
    extend: 'Ext.data.Store',
    id: 'Koltiva.store.Traceability_new.Reference.Supplychain_areafarmer.MainGrid',
    storeId: 'Koltiva.store.Traceability_new.Reference.Supplychain_areafarmer.MainGrid',
    fields: ['SupplychainFarmerID','DateStart','DateEnd','Status','SupplychainID','MemberID','MemberDisplayID','MemberName','Desa','Kecamatan','Province','District'],
    pageSize: 12,
    autoLoad: false,
    remoteSort: true,
    storeVar: false,
    setStoreVar: function(value){
        this.storeVar = value;
    },
    proxy: {
        type: 'ajax',
        url: m_api + '/traceability_api/Supplychain_areafarmer/fetch',
        reader: {
            type: 'json',
            root: 'data',
            totalProperty: 'total'
        }
    },
	listeners :
	{ 
		beforeload: function(store, operation, options){
            var ptextSearch;

            var patchouli_grower_ls = JSON.parse(localStorage.getItem('patchouli_grower_ls'));
            if(patchouli_grower_ls != null){
                ptextSearch        = patchouli_grower_ls.ptextSearch;
            }else{
                ptextSearch        = "";
            }

            store.proxy.extraParams.textSearch = Ext.getCmp('view.Grower.GridMainGrower-textSearch').getValue();
            store.proxy.extraParams.SupplychainID	 = Ext.getCmp('Koltiva.view.Traceability_new.Reference.Supplychain_org-dataForm-SupplychainID').getValue();
        }
	}
});
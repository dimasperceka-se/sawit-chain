/*
* @Author: Fashah Darullah
* @Date:   2019-06-12 11:19:19
*/

Ext.define('Koltiva.store.FarmCloud.UserManagementGrid', {
    extend: 'Ext.data.Store',
    id: 'Koltiva.store.FarmCloud.UserManagementGrid',
    storeId: 'Koltiva.store.FarmCloud.UserManagementGrid',
    fields: ['PersonName','DateOfBirth','PersonExtID','GroupName',
        'username','Email','HandPhone','Gender','role','DistrictName','SubDistrictName'],
    pageSize: 15,
    autoLoad: true,
    storeVar: false,
    setStoreVar: function(value){
        this.storeVar = value;
    },
    remoteSort: true,
    proxy: {
        type: 'ajax',
        url: m_grid_main,
        reader: {
            type: 'json',
            root: 'data',
            totalProperty: 'total'
        }
    },
    listeners: {
        beforeload: function(store, operation, options){
            var ct_farmer_ls = JSON.parse(localStorage.getItem('ct_farmer_ls'));
            if(ct_farmer_ls != null){
                opsiCall = ct_farmer_ls.opsiCall;
                ptextSearch = ct_farmer_ls.ptextSearch;
            }else{
            	opsiCall = "simple";
                ptextSearch = "";
            }

			var ct_farmer_grid_ls = JSON.parse(localStorage.getItem('ct_farmer_grid_ls'));
			if(ct_farmer_grid_ls != null){
				opsiShow = ct_farmer_grid_ls.opsiShow;
			}else{
				opsiShow = 'default';
			}
			
            store.proxy.extraParams.opsiShow = opsiShow;
            store.proxy.extraParams.opsiCall = opsiCall;
            store.proxy.extraParams.textSearch = ptextSearch;
        }
    }
});
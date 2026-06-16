Ext.define('Koltiva.store.menu.StoreMainGridMenu', {
    extend: 'Ext.data.Store',
    id: 'Koltiva.store.menu.StoreMainGridMenu',
    storeId: 'Koltiva.store.menu.StoreMainGridMenu',
    fields: ['MenuId','MenuParentId','MenuName','MenuModule','MenuShow','MenuIcon','MenuOrder','MenuJenis','MenuParam'],
    pageSize: 25,
    autoLoad: true,
    storeVar: false,
    setStoreVar: function(value){
        this.storeVar = value;
    },
    remoteSort: true,
    proxy: {
        type: 'ajax',
        url: m_api + '/menus/menu_api/list_menu',
        reader: {
            type: 'json',
            root: 'data',
            totalProperty: 'total'
        }
    },
    listeners: {
        
        beforeload: function(store, operation, options){
            var ct_menu_ls = JSON.parse(localStorage.getItem('ct_menu_ls'));
            if(ct_menu_ls != null){
                opsiCall = ct_menu_ls.opsiCall;
                ptextSearch = ct_menu_ls.ptextSearch;
                parentMenuSearch = ct_menu_ls.parentMenuSearch;

            }else{
            	opsiCall = "simple";
                ptextSearch = "";
                parentMenuSearch = "";
               
            }

			var ct_menu_grid_ls = JSON.parse(localStorage.getItem('ct_menu_grid_ls'));
			if(ct_menu_grid_ls != null){
				opsiShow = ct_menu_grid_ls.opsiShow;
			}else{
				opsiShow = 'default';
			}

            store.proxy.extraParams.opsiShow = opsiShow;
            store.proxy.extraParams.opsiCall = opsiCall;
            store.proxy.extraParams.textSearch = ptextSearch;
            store.proxy.extraParams.ParentMenu = parentMenuSearch;
           
        }
    }
});
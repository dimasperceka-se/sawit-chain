Ext.define('Koltiva.store.Basic.NewBank.MainGrid', {
    extend: 'Ext.data.Store',
    id: 'Koltiva.store.Basic.NewBank.MainGrid',
    storeId: 'Koltiva.store.Basic.NewBank.MainGrid',
    fields: ['BankID','BankCode','BankName','BankDesc'],
    pageSize: 20,
    autoLoad: true,
    storeVar: false,
    setStoreVar: function(value){
        this.storeVar = value;
    },
    remoteSort: true,
    proxy: {
        type: 'ajax',
        url: m_api + '/bank/bangs',
        reader: {
            type: 'json',
            root: 'data',
            totalProperty: 'total'
        }
    },
    listeners: {
        beforeload: function(store, operation, options){
            var palm_newbank_ls = JSON.parse(localStorage.getItem('palm_newbank_ls'));
            if(palm_newbank_ls != null){
                opsiCall = palm_newbank_ls.opsiCall;
                ptextSearch = palm_newbank_ls.ptextSearch;
            }else{
            	opsiCall = "simple";
                ptextSearch = "";
            }

			var palm_newbank_grid_ls = JSON.parse(localStorage.getItem('palm_newbank_grid_ls'));
			if(palm_newbank_grid_ls != null){
				opsiShow = palm_newbank_grid_ls.opsiShow;
			}else{
				opsiShow = 'default';
			}
			
            store.proxy.extraParams.opsiShow = opsiShow;
            store.proxy.extraParams.opsiCall = opsiCall;
            store.proxy.extraParams.textSearch = ptextSearch;
        }
    }
});
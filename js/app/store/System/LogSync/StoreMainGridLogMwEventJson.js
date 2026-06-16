Ext.define('Koltiva.store.System.LogSync.StoreMainGridLogMwEventJson', {
    extend: 'Ext.data.Store',
    id: 'Koltiva.store.System.LogSync.StoreMainGridLogMwEventJson',
    storeId: 'Koltiva.store.System.LogSync.StoreMainGridLogMwEventJson',
    fields: ['id','event_json','program_uid','event_uid','date_created'],
    pageSize: 200,
    autoLoad: true,
    storeVar: false,
    setStoreVar: function(value){
        this.storeVar = value;
    },
    proxy: {
        type: 'ajax',
        url: m_api + '/logsync/mw2_event_json_grid',
        reader: {
            type: 'json',
            root: 'data'
        }
    },
    listeners: {
        
        beforeload: function(store, operation, options){
            var ct_mwlogprocess_ls = JSON.parse(localStorage.getItem('ct_mwlogprocess_ls'));
            if(ct_mwlogprocess_ls != null){
                var opsiCall = ct_mwlogprocess_ls.opsiCall;
                var pTextSearch = ct_mwlogprocess_ls.pTextSearch;

            }else{
            	var opsiCall = "simple";
                var pTextSearch = "";
               
            }

			var ct_mwlogprocess_grid_ls = JSON.parse(localStorage.getItem('ct_mwlogprocess_grid_ls'));
			if(ct_mwlogprocess_grid_ls != null){
				opsiShow = ct_mwlogprocess_grid_ls.opsiShow;
			}else{
				opsiShow = 'default';
			}

            store.proxy.extraParams.opsiShow = opsiShow;
            store.proxy.extraParams.opsiCall = opsiCall;
            store.proxy.extraParams.pTextSearch = Ext.getCmp('Koltiva.view.System.LogSync.PanelMwEventJson-GridLogMwEventJson-TextSearch').getValue();
           
        }
    }
});
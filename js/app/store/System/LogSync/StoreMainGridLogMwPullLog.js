Ext.define('Koltiva.store.System.LogSync.StoreMainGridLogMwPullLog', {
    extend: 'Ext.data.Store',
    id: 'Koltiva.store.System.LogSync.StoreMainGridLogMwPullLog',
    storeId: 'Koltiva.store.System.LogSync.StoreMainGridLogMwPullLog',
    fields: ['mw_log_id','eventuid','table_reff','query','err_msg','date_exec'],
    pageSize: 200,
    autoLoad: true,
    storeVar: false,
    setStoreVar: function(value){
        this.storeVar = value;
    },
    proxy: {
        type: 'ajax',
        url: m_api + '/logsync/mw_pull_log_grid',
        reader: {
            type: 'json',
            root: 'data'
        }
    },
    listeners: {
        
        beforeload: function(store, operation, options){
            var ct_mwpulllog_ls = JSON.parse(localStorage.getItem('ct_mwpulllog_ls'));
            if(ct_mwpulllog_ls != null){
                var opsiCall = ct_mwpulllog_ls.opsiCall;
                var pTextSearch = ct_mwpulllog_ls.pTextSearch;

            }else{
            	var opsiCall = "simple";
                var pTextSearch = "";
               
            }

			var ct_mwpulllog_grid_ls = JSON.parse(localStorage.getItem('ct_mwpulllog_grid_ls'));
			if(ct_mwpulllog_grid_ls != null){
				opsiShow = ct_mwpulllog_grid_ls.opsiShow;
			}else{
				opsiShow = 'default';
			}

            store.proxy.extraParams.opsiShow = opsiShow;
            store.proxy.extraParams.opsiCall = opsiCall;
            store.proxy.extraParams.pTextSearch = Ext.getCmp('Koltiva.view.System.LogSync.PanelMwPullLog-GridLogMwPullLog-TextSearch').getValue();
           
        }
    }
});
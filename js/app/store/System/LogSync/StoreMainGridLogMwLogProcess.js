Ext.define('Koltiva.store.System.LogSync.StoreMainGridLogMwLogProcess', {
    extend: 'Ext.data.Store',
    id: 'Koltiva.store.System.LogSync.StoreMainGridLogMwLogProcess',
    storeId: 'Koltiva.store.System.LogSync.StoreMainGridLogMwLogProcess',
    fields: [
        'id'
        ,'proc_name'
        ,'log'
        ,'timestamp'
        /* ,'eventuid' */
    ],
    pageSize: 100,
    autoLoad: true,
    storeVar: false,
    setStoreVar: function(value){
        this.storeVar = value;
    },
    proxy: {
        type: 'ajax',
        url: m_api + '/logsync/mw2_log_process_grid',
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
                var pDateStartMw2 = ct_mwlogprocess_ls.pDateStartMw2;
                var pDateEndMw2 = ct_mwlogprocess_ls.pDateEndMw2;

            }else{
            	var opsiCall = "simple";
                var pDateStartMw2 = "";
                var pDateEndMw2 = "";
               
            }

			var ct_mwlogprocess_grid_ls = JSON.parse(localStorage.getItem('ct_mwlogprocess_grid_ls'));
			if(ct_mwlogprocess_grid_ls != null){
				opsiShow = ct_mwlogprocess_grid_ls.opsiShow;
			}else{
				opsiShow = 'default';
			}

            store.proxy.extraParams.opsiShow = opsiShow;
            store.proxy.extraParams.opsiCall = opsiCall;
            store.proxy.extraParams.pDateStartMw2 = Ext.getCmp('Koltiva.view.System.LogSync.PanelMwLogProcess-GridLogMwLogProcess-DateStart').getValue();
            store.proxy.extraParams.pDateEndMw2 = Ext.getCmp('Koltiva.view.System.LogSync.PanelMwLogProcess-GridLogMwLogProcess-DateEnd').getValue();
           
        }
    }
});
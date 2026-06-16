Ext.define('Koltiva.store.Menu_pull_engine_check.MainGridPullEngineCheck', {
    extend: 'Ext.data.Store',
    id: 'Koltiva.store.Menu_pull_engine_check.MainGridPullEngineCheck',
    storeId: 'Koltiva.store.Menu_pull_engine_check.MainGridPullEngineCheck',
    fields: [
        'uid'
        ,'timecheck_send'
        ,'timecheck'
        ,'remark'
        ,'WorkStatus'
        ,'LastSendEmail'
    ],
    pageSize: 1,
    autoLoad: true,
    storeVar: false,
    setStoreVar: function(value){
        this.storeVar = value;
    },
    remoteSort: true,
    proxy: {
        type: 'ajax',
        url: m_api + '/menu_pull_engine_check/grid_main_pull_engine',
        reader: {
            type: 'json',
            root: 'data',
            totalProperty: 'total'
        }
    },
    listeners: {
    }
});
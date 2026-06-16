Ext.define('Koltiva.store.Menu_pull_engine_check.MainGridSysSetting', {
    extend: 'Ext.data.Store',
    id: 'Koltiva.store.Menu_pull_engine_check.MainGridSysSetting',
    storeId: 'Koltiva.store.Menu_pull_engine_check.MainGridSysSetting',
    fields: [
        'SetID'
        ,'SetName'
        ,'SetKey'
        ,'SetValue'
    ],
    pageSize: 1,
    autoLoad: true,
    storeVar: false,
    setStoreVar: function (value) {
        this.storeVar = value;
    },
    remoteSort: true,
    proxy: {
        type: 'ajax',
        url: m_api + '/menu_pull_engine_check/grid_main_sys_setting',
        reader: {
            type: 'json',
            root: 'data',
            totalProperty: 'total'
        }
    },
    listeners: {
    }
});
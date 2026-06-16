Ext.define('Koltiva.store.menu.StoreCmbSysAct', {
    extend: 'Ext.data.Store',
    id: 'Koltiva.store.menu.StoreCmbSysAct',
    storeId: 'Koltiva.store.menu.StoreCmbSysAct',
    fields: ['id','label'],
    autoLoad: true,
    storeVar: false,
    setStoreVar: function(value){
        this.storeVar = value;
    },
    proxy: {
        type: 'ajax',
        url: m_api + '/menus/sys_act/show_sys_act',
        reader: {
            type: 'json',
            root: 'data'
        }
    }
});
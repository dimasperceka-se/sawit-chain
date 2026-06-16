Ext.define('Koltiva.store.menu.StoreMenuParent', {
    extend: 'Ext.data.Store',
    id: 'Koltiva.store.menu.StoreMenuParent',
    storeId: 'Koltiva.store.menu.StoreMenuParent',
    fields: ['MenuId','MenuName'],
    autoLoad: true,
    storeVar: false,
    setStoreVar: function(value){
        this.storeVar = value;
    },
    proxy: {
        type: 'ajax',
        url: m_api + '/menus/menu_api/ShowParentMenu',
        reader: {
            type: 'json',
            root: 'data'
        }
    }
});
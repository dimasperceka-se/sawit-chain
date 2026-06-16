Ext.define('Koltiva.store.FarmCloud.UseraccManagement.MainGrid', {
    extend: 'Ext.data.Store',
    id: 'Koltiva.store.FarmCloud.UseraccManagement.MainGrid',
    storeId: 'Koltiva.store.FarmCloud.UseraccManagement.MainGrid',
    fields: ['FarmerID','MemberName','Gender','GroupName','Username','Email','HandPhone','District','SubDistrict','Partner','StatusAccount'],
    pageSize: 50,
    autoLoad: true,
    storeVar: false,
    setStoreVar: function(value){
        this.storeVar = value;
    },
    remoteSort: true,
    proxy: {
        type: 'ajax',
        url: m_api + '/farmcloud/useracc_grid_main',
        reader: {
            type: 'json',
            root: 'data',
            totalProperty: 'total'
        }
    },
    listeners: {
        beforeload: function(store, operation, options){
            store.proxy.extraParams.KeySearch = this.storeVar.KeySearch;
        }
    }
});
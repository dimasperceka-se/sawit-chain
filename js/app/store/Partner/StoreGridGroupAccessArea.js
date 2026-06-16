Ext.define('Koltiva.store.Partner.StoreGridGroupAccessArea', {
    extend: 'Ext.data.Store',
    storeId: 'Koltiva.store.Partner.StoreGridGroupAccessArea',
    id: 'Koltiva.store.Partner.StoreGridGroupAccessArea',
    fields: ['DistrictID', 'CountryName', 'ProvinceName', 'DistrictName'],
    autoLoad: false,
    pageSize: 200,
    remoteSort: true,
    storeVar: false,
    setStoreVar: function (value) {
        this.storeVar = value;
    },
    proxy: {
        type: 'ajax',
        actionMethods: {
            read: 'POST'
        },
        batchActions: false,
        api: {
            read: m_api + '/partner_new/grid_group_access_area'
        },
        reader: {
            type: 'json',
            root: 'data',
            totalProperty: 'total'
        }
    },
    listeners: {
        'beforeload': function(store, options) {
            store.proxy.extraParams.GroupId = Ext.getCmp('GroupId').getValue();
            store.proxy.extraParams.TxtSearch = Ext.getCmp('GridGroupAccessArea-TextSearch').getValue();
            var group_access_area = JSON.parse(localStorage.getItem('appkolti_group_access_area'));
            if (group_access_area != null) {
                if (group_access_area.itemAdded != null) {
                    store.proxy.extraParams.itemAdded = group_access_area.itemAdded.join(',');
                } else {
                    store.proxy.extraParams.itemAdded = null;
                }
                if (group_access_area.itemDeleted != null) {
                    store.proxy.extraParams.itemDeleted = group_access_area.itemDeleted.join(',');
                } else {
                    store.proxy.extraParams.itemDeleted = null;
                }
            } else {
                store.proxy.extraParams.itemAdded = null;
                store.proxy.extraParams.itemDeleted = null;
            }
        }
    }
});
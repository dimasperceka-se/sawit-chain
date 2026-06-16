Ext.define('Koltiva.store.Partner.GridWinSelectAccessAreaMultiple', {
    extend: 'Ext.data.Store',
    storeId: 'Koltiva.store.Partner.GridWinSelectAccessAreaMultiple',
    id: 'Koltiva.store.Partner.GridWinSelectAccessAreaMultiple',
    fields: ['DistrictID', 'CountryName', 'ProvinceName', 'DistrictName'],
    autoLoad: true,
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
            read: m_api + '/partner_new/list_district'
        },
        reader: {
            type: 'json',
            root: 'data',
            totalProperty: 'total'
        }
    },
    listeners: {
        beforeload: function (store, operation, options) {
            store.proxy.extraParams.PartnerID = this.storeVar.PartnerID;
            store.proxy.extraParams.CmbFilterCountry = this.storeVar.CmbFilterCountry;
            store.proxy.extraParams.TxtSearch = this.storeVar.TxtSearch;
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
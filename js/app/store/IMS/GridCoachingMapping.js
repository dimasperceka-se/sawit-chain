Ext.define('Koltiva.store.IMS.GridCoachingMapping', {
    extend: 'Ext.data.Store',
    storeId: 'Koltiva.store.IMS.GridCoachingMapping',
    id: 'Koltiva.store.IMS.GridCoachingMapping',
    fields: ['IMSID', 'UserName', 'UserRealName', 'FarmerID', 'FarmerName', 'Gender', 'FarmerGroup', 'Village', 'SubDistrict', 'District', 'NC_Major', 'NC_Minor'],
    autoLoad: true,
    pageSize: 25,
    remoteSort: true,
    storeVar: false,
    setStoreVar: function (value) {
        this.storeVar = value;
    },
    proxy: {
        type: 'ajax',
        url: m_api + '/ims_coaching/grid_coaching_mapping',
        reader: {
            type: 'json',
            root: 'data',
            totalProperty: 'total'
        }
    },
    listeners: {
        beforeload: function (store, operation, options) {
            store.proxy.extraParams.IMSID = this.storeVar.IMSID;
            store.proxy.extraParams.TextSearch = this.storeVar.TextSearch;
        }
    }
});
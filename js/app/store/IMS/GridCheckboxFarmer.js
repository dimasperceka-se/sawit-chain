
Ext.define('Koltiva.store.IMS.GridCheckboxFarmer', {
    extend: 'Ext.data.Store',
    storeId: 'Koltiva.store.IMS.GridCheckboxFarmer',
    id: 'Koltiva.store.IMS.GridCheckboxFarmer',
    fields: ['IMSID', 'FarmerID', 'FarmerName', 'FarmerGroup', 'Village'],
    autoLoad: true,
    storeVar: false,
    setStoreVar: function (value) {
        this.storeVar = value;
    },
    proxy: {
        type: 'ajax',
        url: m_api + '/ims_coaching/grid_checkbox_farmer',
        reader: {
            type: 'json',
            root: 'data'
        }
    },
    listeners: {
        beforeload: function (store, operation, options) {
            store.proxy.extraParams.IMSID = this.storeVar.IMSID;
            store.proxy.extraParams.UserName = this.storeVar.UserName;
            store.proxy.extraParams.TextSearch = this.storeVar.TextSearch;
        }
    }
});
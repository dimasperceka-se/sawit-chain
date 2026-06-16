Ext.define('Koltiva.store.Tools.ImportFarmerMainGrid', {
    extend: 'Ext.data.Store',
    id: 'Koltiva.store.Tools.ImportFarmerMainGrid',
    storeId: 'Koltiva.store.Tools.ImportFarmerMainGrid',
    fields: ['FarmerName', 'Birthdate', 'Gender', 'VillageID', 'Village', 'PartnerID', 'PartnerName'],
    pageSize: 2000,
    autoLoad: true,
    storeVar: false,
    setStoreVar: function (value) {
        this.storeVar = value;
    },
    remoteSort: true,
    proxy: {
        type: 'ajax',
        url: m_api + '/tools/import_farmers_grid_main',
        reader: {
            type: 'json',
            root: 'data',
            totalProperty: 'total'
        }
    },
    listeners: {
        beforeload: function (store, operation, options) {
            store.proxy.extraParams.KeySearch = Ext.getCmp('Koltiva.view.ImportFarmers.GridMainFarmers-TextSearch').getValue();
        }
    }
});

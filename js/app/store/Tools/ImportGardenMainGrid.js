Ext.define('Koltiva.store.Tools.ImportGardenMainGrid', {
    extend: 'Ext.data.Store',
    id: 'Koltiva.store.Tools.ImportGardenMainGrid',
    storeId: 'Koltiva.store.Tools.ImportGardenMainGrid',
    fields: ['MemberID', 'PlotNr', 'SurveyNr', 'Latitude', 'Longitude', 'GardenAreaHa'],
    pageSize: 2000,
    autoLoad: true,
    storeVar: false,
    setStoreVar: function (value) {
        this.storeVar = value;
    },
    remoteSort: true,
    proxy: {
        type: 'ajax',
        url: m_api + '/tools/import_gardens_grid',
        reader: {
            type: 'json',
            root: 'data',
            totalProperty: 'total'
        }
    },
    listeners: {
        beforeload: function (store, operation, options) {
            store.proxy.extraParams.KeySearch = Ext.getCmp('Koltiva.view.ImportGardens.MainGrid-TextSearch').getValue();
        }
    }
});

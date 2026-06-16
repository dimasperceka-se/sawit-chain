
Ext.define('Koltiva.store.UserDashboard.MainGrid', {
    extend: 'Ext.data.Store',
    id: 'Koltiva.store.UserDashboard.MainGrid',
    fields: ['DashID', 'DashName', 'BoardID', 'Description', 'ActiveStatus', 'StatusCode', 'Remarks', 'DateCreated', 'DateUpdated', 'CreatedBy', 'CreatedName'],
    pageSize: 25,
    autoLoad: true,
    storeVar: false,
    setStoreVar: function (value) {
        this.storeVar = value;
    },
    remoteSort: true,
    proxy: {
        type: 'ajax',
        url: m_api + '/user_dashboard/grid_main',
        reader: {
            type: 'json',
            root: 'data',
            totalProperty: 'total'
        }
    },
    listeners: {
        beforeload: function (store, operation, options) {
            // console.log(m_prov+' '+m_dist+' '+m_subdist);
            store.proxy.extraParams.KeySearch = this.storeVar.KeySearch;
        }
    }


});
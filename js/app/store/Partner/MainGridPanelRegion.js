Ext.define('Koltiva.store.Partner.MainGridPanelRegion', {
    extend: 'Ext.data.Store',
    storeId: 'Koltiva.store.Partner.MainGridPanelRegion',
    id: 'Koltiva.store.Partner.MainGridPanelRegion',
    fields: ['DistrictID','PartnerID','District','Province','CountryName'],
    autoLoad: true,
    proxy: {
        type: 'ajax',
        url: m_api + '/partner_new/main_grid_region',
        reader: {
            type: 'json',
            root: 'data'
        }
    },
    listeners: {
        'beforeload': function (store, options) {
            store.proxy.extraParams.PartnerID = this.storeVar.PartnerID;
            store.proxy.extraParams.TextSearch = this.storeVar.TextSearch;
            
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
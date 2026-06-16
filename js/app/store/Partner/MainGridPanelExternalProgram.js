Ext.define('Koltiva.store.Partner.MainGridPanelExternalProgram', {
    extend: 'Ext.data.Store',
    storeId: 'Koltiva.store.Partner.MainGridPanelExternalProgram',
    id: 'Koltiva.store.Partner.MainGridPanelExternalProgram',
    fields: ['BuInExID','BuInExName'],
    autoLoad: true,
    proxy: {
        type: 'ajax',
        url: m_api + '/partner_new/main_grid_external_program',
        reader: {
            type: 'json',
            root: 'data'
        }
    },
    listeners: {
        'beforeload': function (store, options) {
            store.proxy.extraParams.PartnerID = this.storeVar.PartnerID;
        }
    }
});
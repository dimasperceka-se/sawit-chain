Ext.define('Koltiva.store.Partner.MainGridPanelInternalProgram', {
    extend: 'Ext.data.Store',
    storeId: 'Koltiva.store.Partner.MainGridPanelInternalProgram',
    id: 'Koltiva.store.Partner.MainGridPanelInternalProgram',
    fields: ['BuInExID','BuInExName'],
    autoLoad: true,
    proxy: {
        type: 'ajax',
        url: m_api + '/partner_new/main_grid_internal_program',
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
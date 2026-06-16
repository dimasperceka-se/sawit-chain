Ext.define('Koltiva.store.ComboGeneral.CmbInternalProgramNew', {
    extend: 'Ext.data.Store',
    storeId: 'Koltiva.store.ComboGeneral.CmbInternalProgramNew',
    id: 'Koltiva.store.ComboGeneral.CmbInternalProgramNew',
    fields: ['id', 'label'],
    autoLoad: false,
    proxy: {
        type: 'ajax',
        url: m_api + '/system/combo_internal_program',
        reader: {
            type: 'json',
            root: 'data'
        }
    },
    listeners: {
        beforeload: function (store, operation, options) {
            store.proxy.extraParams.PartnerID = this.storeVar.PartnerID
        }
    }
});
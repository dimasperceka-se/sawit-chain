Ext.define('Koltiva.store.SME.CmbInternalProgram', {
    extend: 'Ext.data.Store',
    storeId: 'Koltiva.store.SME.CmbInternalProgram',
    id: 'Koltiva.store.SME.CmbInternalProgram',
    fields: ['id', 'label'],
    autoLoad: true,
    proxy: {
        type: 'ajax',
        url: m_api + '/sme/combo_internal_program',
        reader: {
            type: 'json',
            root: 'data'
        }
    }
});
Ext.define('Koltiva.store.SME.CmbExternalProgram', {
    extend: 'Ext.data.Store',
    storeId: 'Koltiva.store.SME.CmbExternalProgram',
    id: 'Koltiva.store.SME.CmbExternalProgram',
    fields: ['id', 'label'],
    autoLoad: true,
    proxy: {
        type: 'ajax',
        url: m_api + '/sme/combo_external_program',
        reader: {
            type: 'json',
            root: 'data'
        }
    }
});
Ext.define('Koltiva.store.ComboGeneral.CmbPartner', {
    extend: 'Ext.data.Store',
    storeId: 'Koltiva.store.ComboGeneral.CmbPartner',
    id: 'Koltiva.store.ComboGeneral.CmbPartner',
    fields: ['id', 'label'],
    autoLoad: true,
    proxy: {
        type: 'ajax',
        url: m_api + '/common/cmb_partner',
        reader: {
            type: 'json',
            root: 'data'
        }
    }
});
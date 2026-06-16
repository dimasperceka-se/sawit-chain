Ext.define('Koltiva.store.ComboGeneral.CmbFilterCountry', {
    extend: 'Ext.data.Store',
    storeId: 'Koltiva.store.ComboGeneral.CmbFilterCountry',
    id: 'Koltiva.store.ComboGeneral.CmbFilterCountry',
    fields: ['id', 'label'],
    autoLoad: true,
    proxy: {
        type: 'ajax',
        url: m_api + '/common/combo_filter_country',
        reader: {
            type: 'json'
        }
    }
});
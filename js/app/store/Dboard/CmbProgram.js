Ext.define('Koltiva.store.Dboard.CmbProgram', {
    extend: 'Ext.data.Store',
    storeId: 'Koltiva.store.Dboard.CmbProgram',
    id: 'Koltiva.store.Dboard.CmbProgram',
    fields: ['id', 'label'],
    autoLoad: true,
    proxy: {
        type: 'ajax',
        url: m_api + '/dboard/combo_filter_wave_sawit',
        reader: {
            type: 'json',
            root: 'data'
        }
    }
});
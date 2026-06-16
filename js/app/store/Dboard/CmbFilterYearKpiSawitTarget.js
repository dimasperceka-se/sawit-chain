Ext.define('Koltiva.store.Dboard.CmbFilterYearKpiSawitTarget', {
    extend: 'Ext.data.Store',
    storeId: 'Koltiva.store.Dboard.CmbFilterYearKpiSawitTarget',
    id: 'Koltiva.store.Dboard.CmbFilterYearKpiSawitTarget',
    fields: ['id', 'label'],
    autoLoad: false,
    proxy: {
        type: 'ajax',
        url: m_api + '/dboard/combo_filter_year_dash_kpi_sawit_terampil',
        reader: {
            type: 'json',
            root: 'data'
        }
    }
});
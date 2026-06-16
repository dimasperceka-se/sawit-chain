Ext.define('Koltiva.store.Dboard.CmbFilterYearKpiTarget', {
    extend: 'Ext.data.Store',
    storeId: 'Koltiva.store.Dboard.CmbFilterYearKpiTarget',
    id: 'Koltiva.store.Dboard.CmbFilterYearKpiTarget',
    fields: ['id', 'label'],
    autoLoad: false,
    proxy: {
        type: 'ajax',
        url: m_api + '/dboard/combo_filter_year_dash_kpi',
        reader: {
            type: 'json',
            root: 'data'
        }
    }
});
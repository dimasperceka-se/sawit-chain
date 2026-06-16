Ext.define('Koltiva.store.Traceability_new.Report.ComboReportMill', {
    extend: 'Ext.data.Store',
    storeId: 'Koltiva.store.Traceability_new.Report.ComboReportMill',
    id: 'Koltiva.store.Traceability_new.Report.ComboReportMill',
    fields: ['SupplychainID', 'Name'],
    autoLoad: true,
    proxy: {
        type: 'ajax',
        url: m_api + '/traceability_api/Report_transaction_mill/ComboMill',
        reader: {
            type: 'json',
            root: 'data'
        }
    }
});
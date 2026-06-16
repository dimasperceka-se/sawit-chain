Ext.define('Koltiva.store.Traceability_new.Report.comboMill', {
    extend: 'Ext.data.Store',
    storeId: 'Koltiva.store.Traceability_new.Report.comboMill',
    id: 'Koltiva.store.Traceability_new.Report.comboMill',
    fields: ['PartnerID', 'Name'],
    autoLoad: true,
    proxy: {
        type: 'ajax',
        url: m_api + '/traceability_api/Report_transaction/ComboMill',
        reader: {
            type: 'json',
            root: 'data'
        }
    }
});
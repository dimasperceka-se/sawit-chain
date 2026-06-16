 Ext.define('Koltiva.store.Traceability_new.Report.MainGridRecapSales', {
    extend: 'Ext.data.Store',
    id: 'Koltiva.store.Traceability_new.Report.MainGridRecapSales',
    storeId: 'Koltiva.store.Traceability_new.Report.MainGridRecapSales',
    fields: ['Tanggal_pengiriman','ObjID','Nama_agen','Tujuan_Mill','Berat_kotor_pengiriman', 'Berat_bersih_dijual','Total_harga'],
    pageSize: 50,
    autoLoad: true,
    remoteSort: true,
    storeVar: false,
    setStoreVar: function(value){
        this.storeVar = value;
    },
    proxy: {
        type: 'ajax',
        url: m_api + '/web-traceability/main-grid-report-penjualan',
        reader: {
            type: 'json',
            root: 'data',
            totalProperty: 'total'
        }
    },
    listeners: {
        beforeload: function(store, operation, options){
            store.proxy.extraParams.SID = m_sid; 
        }
    }
});
 Ext.define('Koltiva.store.Traceability_new.Report.MainGridRecapPurchase', {
    extend: 'Ext.data.Store',
    id: 'Koltiva.store.Traceability_new.Report.MainGridRecapPurchase',
    storeId: 'Koltiva.store.Traceability_new.Report.MainGridRecapPurchase',
    fields: ['Tanggal_transaksi','ID_pemasok','Nama_Pemasok','Janjang','Berat_Kotor','Presentase_pemotongan','Berat_bersih','Harga_per_kilo','Total','Pengurangan_pembayaran','Jumlah_pembayaran','Ketelusuran'],
    pageSize: 50,
    autoLoad: true,
    remoteSort: true,
    storeVar: false,
    setStoreVar: function(value){
        this.storeVar = value;
    },
    proxy: {
        type: 'ajax',
        url: m_api + '/web-traceability/main-grid-report-pembelian',
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
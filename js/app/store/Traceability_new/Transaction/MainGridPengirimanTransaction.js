//Digunakan Di grid List Pengiriman 
Ext.define('Koltiva.store.Traceability_new.Transaction.MainGridPengirimanTransaction', {
    extend: 'Ext.data.Store',
    id: 'Koltiva.store.Traceability_new.Transaction.MainGridPengirimanTransaction',
    storeId: 'Koltiva.store.Traceability_new.Transaction.MainGridPengirimanTransaction',
    fields: ['SupplyBatchID','SupplyBatchDate','SupplyBatchNumber','SupplyOrgID','SupplyDestOrgID','SupplyBatchNumber','SupplyBatchStatus','DeliveryDate','DestPO','DestWeight','DestNumberPackage','DestDriver','DestTransportID','DestTransportNumber','DestContainerNumber','SupplyDestOrgName'],
    pageSize: 50,
    autoLoad: true,
    remoteSort: true,
    storeVar: false,
    setStoreVar: function(value){
        this.storeVar = value;
    },
    proxy: {
        type: 'ajax',
        url: m_api + '/web-traceability/pengiriman-main-grid',
        reader: {
            type: 'json',
            root: 'data',
            totalProperty: 'total'
        }
    },
    listeners: {
        beforeload: function(store, operation, options){
            store.proxy.extraParams.SID = m_sid; 
			store.proxy.extraParams.PID = m_pid;
        }
    }
});
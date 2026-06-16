 
Ext.define('Koltiva.store.Traceability_new.Transaction.window.storeWindowTransactionPengiriman', {
    extend: 'Ext.data.Store',
    id: 'Koltiva.store.Traceability_new.Transaction.window.storeWindowTransactionPengiriman',
    storeId: 'Koltiva.store.Traceability_new.Transaction.window.storeWindowTransactionPengiriman',
    fields: ['SupplyTransID','SupplychainID','SupplyBatchID','TransNumber','SupplyType','DateTransaction','SupplyID','SupplierName','Certified','PackageType','VolumeBruto','VolumeNetto','PlantationNr' ,'FarmingTypeID','DetailTypeID','PackageID','PackageWeight','PackageNumber'],
    pageSize: 50,
    autoLoad: true,
    remoteSort: true,
    storeVar: false,
    setStoreVar: function(value){
        this.storeVar = value;
    },
    proxy: {
        type: 'ajax',
        url: m_api + '/web-traceability/get-transaction-window',
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
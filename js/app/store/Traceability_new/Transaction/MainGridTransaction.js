 
Ext.define('Koltiva.store.Traceability_new.Transaction.MainGridTransaction', {
    extend: 'Ext.data.Store',
    id: 'Koltiva.store.Traceability_new.Transaction.MainGridTransaction',
    storeId: 'Koltiva.store.Traceability_new.Transaction.MainGridTransaction',
    fields: ['SupplyTransID','SupplychainID','SupplyBatchID','TransNumber','SupplyType','DateTransaction','SupplyID','SupplierName','Certified','PackageType','PackageNumber','VolumeBruto','VolumeNetto','PlantationNr' ,'FarmingTypeID','DetailTypeID','PackageID','PackageWeight','SupplyStatus'],
    pageSize: 50,
    autoLoad: true,
    remoteSort: true,
    storeVar: false,
    setStoreVar: function(value){
        this.storeVar = value;
    },
    proxy: {
        type: 'ajax',
        url: m_api + '/web-traceability/main-grid',
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
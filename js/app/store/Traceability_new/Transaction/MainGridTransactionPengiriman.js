//digunakan di Form pengiriman  untuk menampung data hasil cheklist transaction di form 
Ext.define('Koltiva.store.Traceability_new.Transaction.MainGridTransactionPengiriman', {
    extend: 'Ext.data.Store',
    id: 'Koltiva.store.Traceability_new.Transaction.MainGridTransactionPengiriman',
    storeId: 'Koltiva.store.Traceability_new.Transaction.MainGridTransactionPengiriman',
    fields: ['SupplyTransID','SupplychainID','SupplyBatchID','TransNumber','SupplyType','DateTransaction','SupplyID','SupplierName','Certified','PackageType',{name:'VolumeBruto', type:'float'},{name:'VolumeNetto', type:'float'},'PlantationNr' ,'FarmingTypeID','DetailTypeID','PackageID','PackageWeight','PackageNumber','AgentOther','AgentOtherSurvey'],
    pageSize: 50,
    autoLoad: false,
    remoteSort: true,
    storeVar: false,
    setStoreVar: function(value){
        this.storeVar = value;
    },
    proxy: {
        type: 'ajax',
        url: m_api + '/web-traceability/pengiriman-transaction-grid',
        reader: {
            type: 'json',
            root: 'data',
            totalProperty: 'total'
        }
    },
    listeners: {
        beforeload: function(store, operation, options){
            //store.proxy.extraParams.status = 2; 
        }
    }
});
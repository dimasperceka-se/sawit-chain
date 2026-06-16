Ext.define('Koltiva.store.Traceability_new.Delivery.MainGridTransactionDetail', {
    extend: 'Ext.data.Store',
    id: 'Koltiva.store.Traceability_new.Delivery.MainGridTransactionDetail',
    storeId: 'Koltiva.store.Traceability_new.Delivery.MainGridTransactionDetail',
    fields: ['MemberDisplayID','SupplychainID','SupplyBatchID','SupplyBatchNumber',',TransNumber','SupplyType','DateTransaction','SupplyID','SupplierName','Certified','PackageType',{name:'VolumeBruto', type:'float'},{name:'VolumeNetto', type:'float'},'PlantationNr' ,'FarmingTypeID','DetailTypeID','PackageID','PackageWeight','PackageNumber','AgentOther','AgentOtherSurvey','DeliveryID'],
    pageSize: 50,
    autoLoad: true,
    storeVar: false,
    setStoreVar: function(value){
        this.storeVar = value;
    },
    proxy: {
        type: 'ajax',
        url: m_api + '/web-traceability/pengiriman-transaction-grid-detail',
        reader: {
            type: 'json',
            root: 'data',
            totalProperty: 'total'
        }
    },
    listeners: {
        beforeload: function(store, operation, options){
            store.proxy.extraParams.DeliveryID = this.storeVar;
        }
    }
});
Ext.define('Koltiva.store.Traceability.Reception.MainGridTransactionDetail', {
    extend: 'Ext.data.Store',
    id: 'Koltiva.store.Traceability.Reception.MainGridTransactionDetail',
    storeId: 'Koltiva.store.Traceability.Reception.MainGridTransactionDetail',
    fields: ['DespatchID','ReceptionNumber','ReceptionDate','DespatchNumber','CompanyName','ProductName','ReceptionVolume','ShippingDate','DestinationID','Dealer','SupplyBatchNumber','SupplyType','SupplierName','DateSelling','DateBuying'],
    pageSize: 50,
    autoLoad: true,
    storeVar: false,
    setStoreVar: function(value){
        this.storeVar = value;
    },
    proxy: {
        type: 'ajax',
        url: m_api + '/dispatch/refinery/grid_reception_detail',
        reader: {
            type: 'json',
            root: 'data',
            totalProperty: 'total'
        }
    },
    listeners: {
        beforeload: function(store, operation, options){
            store.proxy.extraParams.ShippingDate = this.storeVar; 
        }
    }
});
 
Ext.define('Koltiva.store.Traceability_new.Reference.Supplychain_price.MainGrid', {
    extend: 'Ext.data.Store',
    id: 'Koltiva.store.Traceability_new.Reference.Supplychain_price.MainGrid',
    storeId: 'Koltiva.store.Traceability_new.Reference.Supplychain_price.MainGrid',
    fields: ['PriceID', 'SupplychainID', 'DateStart', 'DateEnd', 'Obj', 'Price', 'StatusCode', 'CreatedBy', 'DateCreated', 'LastModifiedBy', 'DateUpdated'],
    pageSize: 12,
    autoLoad: true,
    remoteSort: true,
    storeVar: false,
    setStoreVar: function(value){
        this.storeVar = value;
    },
    proxy: {
        type: 'ajax',
        url: m_api + '/reference/supplychain-price',
        reader: {
            type: 'json',
            root: 'data',
            totalProperty: 'total'
        }
    }
});
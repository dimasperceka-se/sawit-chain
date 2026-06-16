 
Ext.define('Koltiva.store.Traceability_new.Reference.BatchType.MainGrid', {
    extend: 'Ext.data.Store',
    id: 'Koltiva.store.Traceability_new.Reference.BatchType.MainGrid',
    storeId: 'Koltiva.store.Traceability_new.Reference.BatchType.MainGrid',
    fields: ['SupplyBatchTypeID', 'SupplyBatchTypeName', 'StatusCode', 'DateCreated', 'CreatedBy', 'DateUpdated'],
    pageSize: 12,
    autoLoad: true,
    remoteSort: true,
    storeVar: false,
    setStoreVar: function(value){
        this.storeVar = value;
    },
    proxy: {
        type: 'ajax',
        url: m_api + '/reference/batch-type',
        reader: {
            type: 'json',
            root: 'data',
            totalProperty: 'total'
        }
    }
});
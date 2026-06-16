 
Ext.define('Koltiva.store.Traceability.Reference.BatchStatus.MainGrid', {
    extend: 'Ext.data.Store',
    id: 'Koltiva.store.Traceability.Reference.BatchStatus.MainGrid',
    storeId: 'Koltiva.store.Traceability.Reference.BatchStatus.MainGrid',
    fields: ['SupplyBatchStatusID', 'SupplyBatchStatusName', 'StatusCode', 'DateCreated', 'CreatedBy', 'DateUpdated'],
    pageSize: 12,
    autoLoad: true,
    remoteSort: true,
    storeVar: false,
    setStoreVar: function(value){
        this.storeVar = value;
    },
    proxy: {
        type: 'ajax',
        url: m_api + '/reference/batch-status',
        reader: {
            type: 'json',
            root: 'data',
            totalProperty: 'total'
        }
    }
});
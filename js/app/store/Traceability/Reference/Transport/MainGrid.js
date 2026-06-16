 
Ext.define('Koltiva.store.Traceability.Reference.Transport.MainGrid', {
    extend: 'Ext.data.Store',
    id: 'Koltiva.store.Traceability.Reference.Transport.MainGrid',
    storeId: 'Koltiva.store.Traceability.Reference.Transport.MainGrid',
    fields: ['DestTransportID', 'DestTransportName', 'IsDetail', 'StatusCode', 'DateCreated', 'CreatedBy', 'DateUpdated', 'LastModifiedBy'],
    pageSize: 12,
    autoLoad: true,
    remoteSort: true,
    storeVar: false,
    setStoreVar: function(value){
        this.storeVar = value;
    },
    proxy: {
        type: 'ajax',
        url: m_api + '/reference/transport',
        reader: {
            type: 'json',
            root: 'data',
            totalProperty: 'total'
        }
    }
});
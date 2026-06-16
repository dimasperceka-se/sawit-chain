Ext.define('Koltiva.store.Traceability_new.Processing.MainProcessing', {
    extend: 'Ext.data.Store',
    id: 'Koltiva.store.Traceability_new.Processing.MainProcessing',
    storeId: 'Koltiva.store.Traceability_new.Processing.MainProcessing',
    fields: [
        'ProcessingID',
        'ProcessingNumber',
        'ProcessingDate',
        'ProcessingVolume',
        'ProductVolumeCPO',
        'ProductVolumePK',
        'RemainingVolumeCPO',
        'RemainingVolumePK'
    ],
    pageSize: 50,
    autoLoad: true,
    remoteSort: true,
    storeVar: false,
    setStoreVar: function(value){
        this.storeVar = value;
    },
    proxy: {
        type: 'ajax',
        url: m_api + '/processing/transaction/fetch',
        reader: {
            type: 'json',
            root: 'data',
            totalProperty: 'total'
        }
    },
    listeners: {
        beforeload: function(store, operation, options){
        }, 
    }
});
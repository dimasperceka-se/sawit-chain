 Ext.define('Koltiva.store.Traceability_new.Transaction.StoreGridDetailTransaction', {
    extend: 'Ext.data.Store',
    id: 'Koltiva.store.Traceability_new.Transaction.StoreGridDetailTransaction',
    storeId: 'Koltiva.store.Traceability_new.Transaction.StoreGridDetailTransaction',
    fields: ['SupplyTransID'
        ,'SupplyBatchID'
        ,'SupplyType'
        ,'DateTransaction'
        ,'SupplyID'
        ,'VolumeBruto'
        ,'VolumeNetto'
        ,'MemberDisplayID'
        ,'SupplierName'],
    pageSize: 10,
    autoLoad: true,
    remoteSort: true,
    storeVar: false,
    setStoreVar: function(value){
        this.storeVar = value;
    },
    proxy: {
        type: 'ajax',
        url: m_api + '/traceability_api/web_penerimaan/fetch_detail_transaction',
        reader: {
            type: 'json',
            root: 'data',
            totalProperty: 'total'
        }
    },
    listeners: {
        beforeload: function(store, operation, options){
            
            //store.proxy.extraParams.SBID = 215;
            //store.proxy.extraParams.SID = 225;
        }
    }
});
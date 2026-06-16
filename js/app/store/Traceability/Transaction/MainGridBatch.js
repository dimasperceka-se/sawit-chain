/*
* @Author: nikolius
* @Date:   2017-10-13 13:38:07
* @Last Modified by:   nikolius
* @Last Modified time: 2017-10-13 14:04:26
*/

Ext.define('Koltiva.store.Traceability.Transaction.MainGridBatch', {
    extend: 'Ext.data.Store',
    id: 'Koltiva.store.Traceability.Transaction.MainGridBatch',
    storeId: 'Koltiva.store.Traceability.Transaction.MainGridBatch',
    fields: ['SupplyBatchID','DeliveryDate','Destination','VolumeNetto','Name','VolumeNetto','SupplyStatus'],
    pageSize: 50,
    autoLoad: true,
    remoteSort: true,
    storeVar: false,
    setStoreVar: function(value){
        this.storeVar = value;
    },
    proxy: {
        type: 'ajax',
        url: m_api + '/tc_transaction/batch_grid',
        reader: {
            type: 'json',
            root: 'data',
            totalProperty: 'total'
        }
    },
    listeners: {
        beforeload: function(store, operation, options){
            store.proxy.extraParams.Role = this.storeVar.Role;
            store.proxy.extraParams.StringNameUsername = this.storeVar.StringNameUsername;
        }
    }
});
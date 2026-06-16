/*
* @Author: nikolius
* @Date:   2017-10-13 13:38:07
 * @Last Modified by: komarudin
 * @Last Modified time: 2018-07-03 12:31:25
*/

Ext.define('Koltiva.store.Traceability.Transaction.MainGridTransaction', {
    extend: 'Ext.data.Store',
    id: 'Koltiva.store.Traceability.Transaction.MainGridTransaction',
    storeId: 'Koltiva.store.Traceability.Transaction.MainGridTransaction',
    fields: ['SupplyTransID','SupplyType','DateTransaction','AgentName','DOName','VolumeBruto','VolumeNetto','Farmers','SupplyStatus'],
   pageSize: 50,
    autoLoad: true,
    remoteSort: true,
    storeVar: false,
    setStoreVar: function(value){
        this.storeVar = value;
    },
    proxy: {
        type: 'ajax',
        url: m_api + '/tc_transaction/transaction_grid',
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
/*
* @Author: nikolius
* @Date:   2017-10-13 13:38:07
 * @Last Modified by: komarudin
 * @Last Modified time: 2018-05-25 10:50:18
*/

Ext.define('Koltiva.store.Traceability.Transaction.MainGridFarmer', {
    extend: 'Ext.data.Store',
    id: 'Koltiva.store.Traceability.Transaction.MainGridFarmer',
    storeId: 'Koltiva.store.Traceability.Transaction.MainGridFarmer',
    fields: ['DateTransaction','FarmerName','PlotNr','Village','VolumeBruto','VolumeNetto','Bjr','Tandan'],
    pageSize: 50,
    autoLoad: true,
    remoteSort: true,
    storeVar: false,
    setStoreVar: function(value){
        this.storeVar = value;
    },
    proxy: {
        type: 'ajax',
        url: m_api + '/tc_transaction/farmer_grid',
        reader: {
            type: 'json',
            root: 'data',
            totalProperty: 'total'
        }
    },
   
    listeners: {
        beforeload: function(store, operation, options){
            var sm = Ext.getCmp('Koltiva.view.Traceability.Transaction.MainGrid-gridTransaction').getSelectionModel().getSelection()[0];
            store.proxy.extraParams.TransID = sm.get('SupplyTransID');
        }
    }
});
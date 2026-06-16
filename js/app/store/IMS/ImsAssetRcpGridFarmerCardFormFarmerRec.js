/******************************************
 *  Author : n1colius.lau@gmail.com   
 *  Created On : Mon Oct 15 2018
 *  File : ImsAssetRcpGridFarmerCardFormFarmerRec.js
 *******************************************/

Ext.define('Koltiva.store.IMS.ImsAssetRcpGridFarmerCardFormFarmerRec', {
    extend: 'Ext.data.Store',
    storeId: 'Koltiva.store.IMS.ImsAssetRcpGridFarmerCardFormFarmerRec',
    id: 'Koltiva.store.IMS.ImsAssetRcpGridFarmerCardFormFarmerRec',
    fields: ['FarmerID','FarmerName','Gender','FarmerGroup','ReceiverStatus'],
    autoLoad: true,
    pageSize: 25,
    remoteSort: true,
    storeVar: false,
    setStoreVar: function(value){
        this.storeVar = value;
    },
    proxy: {
        type: 'ajax',
        url: m_api + '/ims_asset_rcp/farmer_card_farmer_rec_grid',
        reader: {
            type: 'json',
            root: 'data',
            totalProperty: 'total'
        }
    },
    listeners: {
        beforeload: function(store, operation, options){
            store.proxy.extraParams.RcpID = this.storeVar.RcpID;
        }
    }
});
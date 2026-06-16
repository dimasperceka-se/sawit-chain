/******************************************
 *  Author : n1colius.lau@gmail.com   
 *  Created On : Tue Oct 23 2018
 *  File : ImsAssetRcpGridFarmerCardRcpSummary.js
 *******************************************/

Ext.define('Koltiva.store.IMS.ImsAssetRcpGridFarmerCardRcpSummary', {
    extend: 'Ext.data.Store',
    storeId: 'Koltiva.store.IMS.ImsAssetRcpGridFarmerCardRcpSummary',
    id: 'Koltiva.store.IMS.ImsAssetRcpGridFarmerCardRcpSummary',
    fields: ['FarmerID','FarmerName','Gender','FarmerGroup','SubDistrict','Village','ReceivedStatus'],
    autoLoad: true,
    pageSize: 25,
    remoteSort: true,
    storeVar: false,
    setStoreVar: function(value){
        this.storeVar = value;
    },
    proxy: {
        type: 'ajax',
        url: m_api + '/ims_asset_rcp/farmer_card_rcp_summary',
        reader: {
            type: 'json',
            root: 'data',
            totalProperty: 'total'
        }
    },
    listeners: {
        beforeload: function(store, operation, options){
            store.proxy.extraParams.IMSID = this.storeVar.IMSID;
            store.proxy.extraParams.FilterReceivedStatus = this.storeVar.FilterReceivedStatus;
        }
    }
});
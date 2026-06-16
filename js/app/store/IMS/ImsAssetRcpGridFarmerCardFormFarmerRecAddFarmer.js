/******************************************
 *  Author : n1colius.lau@gmail.com   
 *  Created On : Mon Oct 15 2018
 *  File : ImsAssetRcpGridFarmerCardFormFarmerRecAddFarmer.js
 *******************************************/

Ext.define('Koltiva.store.IMS.ImsAssetRcpGridFarmerCardFormFarmerRecAddFarmer', {
    extend: 'Ext.data.Store',
    storeId: 'Koltiva.store.IMS.ImsAssetRcpGridFarmerCardFormFarmerRecAddFarmer',
    id: 'Koltiva.store.IMS.ImsAssetRcpGridFarmerCardFormFarmerRecAddFarmer',
    fields: ['FarmerID','FarmerName','Gender','FarmerGroup'],
    autoLoad: true,
    pageSize: 20,
    remoteSort: true,
    storeVar: false,
    setStoreVar: function(value){
        this.storeVar = value;
    },
    proxy: {
        type: 'ajax',
        url: m_api + '/ims_asset_rcp/farmer_card_farmer_rec_add_farmer_grid',
        reader: {
            type: 'json',
            root: 'data',
            totalProperty: 'total'
        }
    },
    listeners: {
        beforeload: function(store, operation, options){
            store.proxy.extraParams.RcpID = this.storeVar.RcpID;
            store.proxy.extraParams.SearchStringParam = this.storeVar.SearchStringParam;
            store.proxy.extraParams.SearchCpgParam = this.storeVar.SearchCpgParam;
        }
    }
});
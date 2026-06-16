/******************************************
 *  Author : n1colius.lau@gmail.com   
 *  Created On : Fri Oct 05 2018
 *  File : ImsAssetRcpGridFarmerAPD.js
 *******************************************/

Ext.define('Koltiva.store.IMS.ImsAssetRcpGridFarmerAPD', {
    extend: 'Ext.data.Store',
    storeId: 'Koltiva.store.IMS.ImsAssetRcpGridFarmerAPD',
    id: 'Koltiva.store.IMS.ImsAssetRcpGridFarmerAPD',
    fields: ['FarmerID','FarmerName','Gender','FarmerGroup','KotakPestisida','Masker','SarungTangan','Goggles','Boots','Mantel'],
    autoLoad: true,    
    storeVar: false,
    setStoreVar: function(value){
        this.storeVar = value;
    },
    proxy: {
        type: 'ajax',
        url: m_api + '/ims_asset_rcp/farmer_apd_main_grid',
        reader: {
            type: 'json',
            root: 'data'
        }
    },
    listeners: {
        beforeload: function(store, operation, options){
            store.proxy.extraParams.IMSID = this.storeVar.IMSID;            
        }
    }
});
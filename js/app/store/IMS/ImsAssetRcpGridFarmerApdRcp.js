/******************************************
 *  Author : n1colius.lau@gmail.com   
 *  Created On : Wed Oct 10 2018
 *  File : ImsAssetRcpGridFarmerApdRcp.js
 *******************************************/

Ext.define('Koltiva.store.IMS.ImsAssetRcpGridFarmerApdRcp', {
    extend: 'Ext.data.Store',
    storeId: 'Koltiva.store.IMS.ImsAssetRcpGridFarmerApdRcp',
    id: 'Koltiva.store.IMS.ImsAssetRcpGridFarmerApdRcp',
    fields: ['RcpID','RcpTransNumber','RcpDate','KotakPestisida','Masker','SarungTangan','Goggles','Boots','Mantel','ReceiverStatus'],
    autoLoad: true,    
    storeVar: false,
    setStoreVar: function(value){
        this.storeVar = value;
    },
    proxy: {
        type: 'ajax',
        url: m_api + '/ims_asset_rcp/farmer_apd_rcp',
        reader: {
            type: 'json',
            root: 'data'
        }
    },
    listeners: {
        beforeload: function(store, operation, options){
            store.proxy.extraParams.IMSID = this.storeVar.IMSID;
            store.proxy.extraParams.FarmerID = this.storeVar.FarmerID;
        }
    }
});
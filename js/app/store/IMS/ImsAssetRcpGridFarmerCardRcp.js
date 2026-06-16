/******************************************
 *  Author : n1colius.lau@gmail.com   
 *  Created On : Fri Oct 12 2018
 *  File : ImsAssetRcpGridFarmerCardRcp.js
 *******************************************/

Ext.define('Koltiva.store.IMS.ImsAssetRcpGridFarmerCardRcp', {
    extend: 'Ext.data.Store',
    storeId: 'Koltiva.store.IMS.ImsAssetRcpGridFarmerCardRcp',
    id: 'Koltiva.store.IMS.ImsAssetRcpGridFarmerCardRcp',
    fields: ['RcpID','RcpTransNumber','RcpDate','Remark','TotalFarmers','TotalFarmersRecCard','StatusFileUpload'],
    autoLoad: true,    
    storeVar: false,
    setStoreVar: function(value){
        this.storeVar = value;
    },
    proxy: {
        type: 'ajax',
        url: m_api + '/ims_asset_rcp/farmer_card_rcp',
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
/******************************************
 *  Author : n1colius.lau@gmail.com   
 *  Created On : Mon Oct 01 2018
 *  File : ImsAssetRcpGridRcpChBsItems.js
 *******************************************/

Ext.define('Koltiva.store.IMS.ImsAssetRcpGridRcpChBsItems', {
    extend: 'Ext.data.Store',
    storeId: 'Koltiva.store.IMS.ImsAssetRcpGridRcpChBsItems',
    id: 'Koltiva.store.IMS.ImsAssetRcpGridRcpChBsItems',
    fields: ['RcpItemID','ItemQty','ItemLabel','ItemRemark','UserAssType','UserAssLabel','UserAssLocation','Remark'],
    autoLoad: true,
    storeVar: false,
    setStoreVar: function(value){
        this.storeVar = value;
    },
    proxy: {
        type: 'ajax',
        url: m_api + '/ims_asset_rcp/rcp_ch_bs_item_main_grid',
        reader: {
            type: 'json',
            root: 'data'
        }
    },
    listeners: {
        beforeload: function(store, operation, options){
            store.proxy.extraParams.RcpID = this.storeVar.RcpID;            
        }
    }
});
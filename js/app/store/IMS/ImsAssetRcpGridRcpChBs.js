/******************************************
 *  Author : n1colius.lau@gmail.com   
 *  Created On : Mon Oct 01 2018
 *  File : ImsAssetRcpGridRcpChBs.js
 *******************************************/

Ext.define('Koltiva.store.IMS.ImsAssetRcpGridRcpChBs', {
    extend: 'Ext.data.Store',
    storeId: 'Koltiva.store.IMS.ImsAssetRcpGridRcpChBs',
    id: 'Koltiva.store.IMS.ImsAssetRcpGridRcpChBs',
    fields: ['RcpID','RcpTransNumber','RcpDate','Remark','TotalItem','ItemOwnerLabel','ReceiverStatus','ReceiverStatusRaw','CreatedBy'],
    autoLoad: true,
    pageSize: 25,
    remoteSort: true,
    storeVar: false,
    setStoreVar: function(value){
        this.storeVar = value;
    },
    proxy: {
        type: 'ajax',
        url: m_api + '/ims_asset_rcp/rcp_ch_bs_main_grid',
        reader: {
            type: 'json',
            root: 'data',
            totalProperty: 'total'
        }
    },
    listeners: {
        beforeload: function(store, operation, options){
            store.proxy.extraParams.IMSID = this.storeVar.IMSID;            
        }
    }
});
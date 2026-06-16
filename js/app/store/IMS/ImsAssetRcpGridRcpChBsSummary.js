/******************************************
 *  Author : n1colius.lau@gmail.com   
 *  Created On : Wed Oct 03 2018
 *  File : ImsAssetRcpGridRcpChBsSummary.js
 *******************************************/

Ext.define('Koltiva.store.IMS.ImsAssetRcpGridRcpChBsSummary', {
    extend: 'Ext.data.Store',
    storeId: 'Koltiva.store.IMS.ImsAssetRcpGridRcpChBsSummary',
    id: 'Koltiva.store.IMS.ImsAssetRcpGridRcpChBsSummary',
    fields: ['UserAssType','UserAssLabel','TotalItems','DetailItems'],
    autoLoad: true,    
    storeVar: false,
    setStoreVar: function(value){
        this.storeVar = value;
    },
    proxy: {
        type: 'ajax',
        url: m_api + '/ims_asset_rcp/rcp_ch_bs_summary_main_grid',
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
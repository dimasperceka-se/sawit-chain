/******************************************
 *  Author : n1colius.lau@gmail.com   
 *  Created On : Mon Oct 15 2018
 *  File : CmbFarmerCardFilterCpgByRcpID.js
 *******************************************/

Ext.define('Koltiva.store.IMS.CmbFarmerCardFilterCpgByRcpID', {
    extend: 'Ext.data.Store',
    storeId: 'Koltiva.store.IMS.CmbFarmerCardFilterCpgByRcpID',
    id: 'Koltiva.store.IMS.CmbFarmerCardFilterCpgByRcpID',
    fields: ['id','label'],
    autoLoad: true,
    storeVar: false,
    setStoreVar: function(value){
        this.storeVar = value;
    },
    proxy: {
        type: 'ajax',
        url: m_api + '/ims_asset_rcp/cmb_farmer_card_filter_cpg',
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
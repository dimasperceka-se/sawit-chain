/******************************************
 *  Author : n1colius.lau@gmail.com   
 *  Created On : Tue Oct 02 2018
 *  File : CmbIMSAssetItem.js
 *******************************************/

Ext.define('Koltiva.store.IMS.CmbIMSAssetItem', {
    extend: 'Ext.data.Store',
    storeId: 'Koltiva.store.IMS.CmbIMSAssetItem',
    id: 'Koltiva.store.IMS.CmbIMSAssetItem',
    fields: ['id','label'],
    autoLoad: true,
    storeVar: false,
    setStoreVar: function(value){
        this.storeVar = value;
    },
    proxy: {
        type: 'ajax',
        url: m_api + '/ims_asset_rcp/cmb_asset_item',
        reader: {
            type: 'json',
            root: 'data'
        }
    },
    listeners: {
        beforeload: function(store, operation, options){
            store.proxy.extraParams.UseIn = this.storeVar.UseIn;
        }
    }
});
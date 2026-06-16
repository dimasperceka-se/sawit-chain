/******************************************
 *  Author : n1colius.lau@gmail.com   
 *  Created On : Tue Oct 02 2018
 *  File : CmbAssetUser.js
 *******************************************/

Ext.define('Koltiva.store.IMS.CmbAssetUser', {
    extend: 'Ext.data.Store',
    storeId: 'Koltiva.store.IMS.CmbAssetUser',
    id: 'Koltiva.store.IMS.CmbAssetUser',
    fields: ['id','label'],
    autoLoad: true,
    storeVar: false,
    setStoreVar: function(value){
        this.storeVar = value;
    },
    proxy: {
        type: 'ajax',
        url: m_api + '/ims_asset_rcp/cmb_asset_user',
        reader: {
            type: 'json',
            root: 'data'
        }
    },
    listeners: {
        beforeload: function(store, operation, options){
            store.proxy.extraParams.Type = this.storeVar.Type;
            store.proxy.extraParams.IMSID = this.storeVar.IMSID;
        }
    }
});
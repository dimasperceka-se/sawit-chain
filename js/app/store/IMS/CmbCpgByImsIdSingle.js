/******************************************
 *  Author : n1colius.lau@gmail.com   
 *  Created On : Wed Nov 28 2018
 *  File : CmbCpgByImsIdSingle.js
 *******************************************/

Ext.define('Koltiva.store.IMS.CmbCpgByImsIdSingle', {
    extend: 'Ext.data.Store',
    storeId: 'Koltiva.store.IMS.CmbCpgByImsIdSingle',
    id: 'Koltiva.store.IMS.CmbCpgByImsIdSingle',
    fields: ['id','label'],
    autoLoad: true,
    storeVar: false,
    setStoreVar: function(value){
        this.storeVar = value;
    },
    proxy: {
        type: 'ajax',
        url: m_api + '/ims_training/cmb_cpg_by_ims_id_single',
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
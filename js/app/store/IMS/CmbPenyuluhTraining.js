/******************************************
 *  Author : n1colius.lau@gmail.com   
 *  Created On : Mon Dec 03 2018
 *  File : CmbPenyuluhTraining.js
 *******************************************/

Ext.define('Koltiva.store.IMS.CmbPenyuluhTraining', {
    extend: 'Ext.data.Store',
    storeId: 'Koltiva.store.IMS.CmbPenyuluhTraining',
    id: 'Koltiva.store.IMS.CmbPenyuluhTraining',
    fields: ['id','label'],
    autoLoad: true,
    storeVar: false,
    setStoreVar: function(value){
        this.storeVar = value;
    },
    proxy: {
        type: 'ajax',
        url: m_api + '/ims_training/cmb_penyuluh_training',
        reader: {
            type: 'json',
            root: 'data'
        }
    },
    listeners: {
        beforeload: function(store, operation, options){
        }
    }
});
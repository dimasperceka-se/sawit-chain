/******************************************
 *  Author : n1colius.lau@gmail.com   
 *  Created On : Mon Dec 03 2018
 *  File : CmbFasilitatorTraining.js
 *******************************************/

Ext.define('Koltiva.store.IMS.CmbFasilitatorTraining', {
    extend: 'Ext.data.Store',
    storeId: 'Koltiva.store.IMS.CmbFasilitatorTraining',
    id: 'Koltiva.store.IMS.CmbFasilitatorTraining',
    fields: ['id','label'],
    autoLoad: true,
    storeVar: false,
    setStoreVar: function(value){
        this.storeVar = value;
    },
    proxy: {
        type: 'ajax',
        url: m_api + '/ims_training/cmb_fasilitator_training',
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
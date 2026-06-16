/******************************************
 *  Author : n1colius.lau@gmail.com   
 *  Created On : Wed Nov 28 2018
 *  File : CmbParticipantType.js
 *******************************************/

Ext.define('Koltiva.store.IMS.CmbParticipantType', {
    extend: 'Ext.data.Store',
    storeId: 'Koltiva.store.IMS.CmbParticipantType',
    id: 'Koltiva.store.IMS.CmbParticipantType',
    fields: ['id','label'],
    autoLoad: false,
    storeVar: false,
    setStoreVar: function(value){
        this.storeVar = value;
    },
    proxy: {
        type: 'ajax',
        url: m_api + '/ims_training/cmb_participant_type',
        reader: {
            type: 'json',
            root: 'data'
        }
    },
    listeners: {
        beforeload: function(store, operation, options){
            store.proxy.extraParams.IMSID = this.storeVar.IMSID;
            store.proxy.extraParams.CPGid = this.storeVar.CPGid;
        }
    }
});
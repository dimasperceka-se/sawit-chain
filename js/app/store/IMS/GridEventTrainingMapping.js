/******************************************
 *  Author : n1colius.lau@gmail.com   
 *  Created On : Thu Feb 14 2019
 *  File : GridEventTrainingMapping.js
 *******************************************/

Ext.define('Koltiva.store.IMS.GridEventTrainingMapping', {
    extend: 'Ext.data.Store',
    storeId: 'Koltiva.store.IMS.GridEventTrainingMapping',
    id: 'Koltiva.store.IMS.GridEventTrainingMapping',
    fields: ['ActivityType','ParticipantType','TopikGAP','TopikCOC'],
    autoLoad: false,
    storeVar: false,
    setStoreVar: function(value){
        this.storeVar = value;
    },
    proxy: {
        type: 'ajax',
        url: m_api + '/ims_training/grid_event_training_mapping',
        reader: {
            type: 'json',
            root: 'data'
        }
    },
    listeners: {
        beforeload: function(store, operation, options){
            store.proxy.extraParams.IMSID = this.storeVar.IMSID;
            store.proxy.extraParams.TrainingType = this.storeVar.TrainingType;
            store.proxy.extraParams.ActivityType = this.storeVar.ActivityType;
            store.proxy.extraParams.ParticipantType = this.storeVar.ParticipantType;
        }
    }
});
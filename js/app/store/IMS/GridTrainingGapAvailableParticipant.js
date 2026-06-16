/******************************************
 *  Author : n1colius.lau@gmail.com   
 *  Created On : Thu Feb 14 2019
 *  File : GridTrainingGapAvailableParticipant.js
 *******************************************/

Ext.define('Koltiva.store.IMS.GridTrainingGapAvailableParticipant', {
    extend: 'Ext.data.Store',
    storeId: 'Koltiva.store.IMS.GridTrainingGapAvailableParticipant',
    id: 'Koltiva.store.IMS.GridTrainingGapAvailableParticipant',
    fields: ['FarmerID','FarmerName','District','SubDistrict','Village'],
    autoLoad: false,
    storeVar: false,
    setStoreVar: function(value){
        this.storeVar = value;
    },
    proxy: {
        type: 'ajax',
        url: m_api + '/ims_training/grid_training_gap_available_participant',
        reader: {
            type: 'json',
            root: 'data'
        }
    },
    listeners: {
        beforeload: function(store, operation, options){
            store.proxy.extraParams.IMSID = this.storeVar.IMSID;
            store.proxy.extraParams.TrainingType = this.storeVar.TrainingType;
            store.proxy.extraParams.EventType = this.storeVar.EventType;
            store.proxy.extraParams.ActivityType = this.storeVar.ActivityType;
            store.proxy.extraParams.ParticipantType = this.storeVar.ParticipantType;
            store.proxy.extraParams.CPGid = this.storeVar.CPGid;
        }
    }
});

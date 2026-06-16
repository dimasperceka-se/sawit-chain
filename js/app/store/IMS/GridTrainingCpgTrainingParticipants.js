/******************************************
 *  Author : n1colius.lau@gmail.com   
 *  Created On : Mon Dec 03 2018
 *  File : GridTrainingCpgTrainingParticipants.js
 *******************************************/

Ext.define('Koltiva.store.IMS.GridTrainingCpgTrainingParticipants', {
    extend: 'Ext.data.Store',
    storeId: 'Koltiva.store.IMS.GridTrainingCpgTrainingParticipants',
    id: 'Koltiva.store.IMS.GridTrainingCpgTrainingParticipants',
    fields: ['FarmerID','FarmerName','FarmerGroup','AttendancePersentase','TrainingPassed','WritingAwal','WritingAkhir'],
    autoLoad: true,
    storeVar: false,
    setStoreVar: function(value){
        this.storeVar = value;
    },
    proxy: {
        type: 'ajax',
        url: m_api + '/ims_training/cpg_training_participants_main_grid',
        reader: {
            type: 'json',
            root: 'data'
        }
    },
    listeners: {
        beforeload: function(store, operation, options){
            store.proxy.extraParams.CpgBatchTrainingID = this.storeVar.CpgBatchTrainingID;
        }
    }
});
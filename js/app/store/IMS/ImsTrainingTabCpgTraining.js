/******************************************
 *  Author : n1colius.lau@gmail.com   
 *  Created On : Fri Nov 30 2018
 *  File : ImsTrainingTabCpgTraining.js
 *******************************************/

Ext.define('Koltiva.store.IMS.ImsTrainingTabCpgTraining', {
    extend: 'Ext.data.Store',
    storeId: 'Koltiva.store.IMS.ImsTrainingTabCpgTraining',
    id: 'Koltiva.store.IMS.ImsTrainingTabCpgTraining',
    fields: ['CpgBatchTrainingID','CPGid','Groupname','TopicTraining','JumlahPeserta','TrainingStart','JumlahPertemuan','Fasilitator','EventStatus','CreatedByLabel','EventType','ParticipantType','RemidialType'],
    autoLoad: true,
    storeVar: false,
    setStoreVar: function(value){
        this.storeVar = value;
    },
    proxy: {
        type: 'ajax',
        url: m_api + '/ims_training/event_cpg_training_main_grid',
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
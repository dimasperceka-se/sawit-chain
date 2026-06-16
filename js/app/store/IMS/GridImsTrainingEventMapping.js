/**
 * Store untuk Grid IMS Training Event Mapping
 */
Ext.define('Koltiva.store.IMS.GridImsTrainingEventMapping', {
    extend: 'Ext.data.Store',
    storeId: 'Koltiva.store.IMS.GridImsTrainingEventMapping',
    id: 'Koltiva.store.IMS.GridImsTrainingEventMapping',
    fields: ['IMSID', 'TrainingType', 'ActivityType', 'ParticipantType', 'TopikGAP', 'TopikCOC'],
    autoLoad: true,
    storeVar: false,
    setStoreVar: function(value){
        this.storeVar = value;
    },
    proxy: {
        type: 'ajax',
        url: m_api + '/ims_training/training_event_mapping',
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
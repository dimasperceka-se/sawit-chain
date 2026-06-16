/******************************************
 *  Author : n1colius.lau@gmail.com   
 *  Created On : Mon Oct 29 2018
 *  File : GridTrainAttachmentFiles.js
 *******************************************/

Ext.define('Koltiva.store.Train.GridTrainAttachmentFiles', {
    extend: 'Ext.data.Store',
    storeId: 'Koltiva.store.Train.GridTrainAttachmentFiles',
    id: 'Koltiva.store.Train.GridTrainAttachmentFiles',
    fields: ['TrainAttID','TrainID','Filename','FileExist','Remark','ExtensionFile'],
    autoLoad: true,
    storeVar: false,
    setStoreVar: function(value){
        this.storeVar = value;
    },
    proxy: {
        type: 'ajax',
        url: m_api + '/train/train_attachment_files_main_grid',
        reader: {
            type: 'json',
            root: 'data'
        }
    },
    listeners: {
        beforeload: function(store, operation, options){
            store.proxy.extraParams.TrainID = this.storeVar.TrainID;
            store.proxy.extraParams.TrainType = this.storeVar.TrainType;
        }
    }
});
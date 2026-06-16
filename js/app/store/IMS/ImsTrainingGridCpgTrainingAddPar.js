/******************************************
 *  Author : n1colius.lau@gmail.com   
 *  Created On : Wed Dec 05 2018
 *  File : ImsTrainingGridCpgTrainingAddPar.js
 *******************************************/

Ext.define('Koltiva.store.IMS.ImsTrainingGridCpgTrainingAddPar', {
    extend: 'Ext.data.Store',
    storeId: 'Koltiva.store.IMS.ImsTrainingGridCpgTrainingAddPar',
    id: 'Koltiva.store.IMS.ImsTrainingGridCpgTrainingAddPar',
    fields: ['FarmerID','FarmerName','Gender','SubDistrict','Village','FarmerGroup'],
    autoLoad: true,
    pageSize: 25,
    remoteSort: true,
    storeVar: false,
    setStoreVar: function(value){
        this.storeVar = value;
    },
    proxy: {
        type: 'ajax',
        url: m_api + '/ims_training/cpg_training_add_par_main_grid',
        reader: {
            type: 'json',
            root: 'data',
            totalProperty: 'total'
        }
    },
    listeners: {
        beforeload: function(store, operation, options){
            store.proxy.extraParams.IMSID = this.storeVar.IMSID;            
            store.proxy.extraParams.CpgBatchTrainingID = this.storeVar.CpgBatchTrainingID;            
            store.proxy.extraParams.ParticipantType = this.storeVar.ParticipantType;            
            store.proxy.extraParams.SearchStringParam = this.storeVar.SearchStringParam;            
            store.proxy.extraParams.SearchCpgParam = this.storeVar.SearchCpgParam;            
        }
    }
});
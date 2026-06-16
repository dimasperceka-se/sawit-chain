/******************************************
 *  Author : n1colius.lau@gmail.com   
 *  Created On : Fri Nov 30 2018
 *  File : storeFarmerTraining.js
 *******************************************/

Ext.define('Koltiva.store.IMS.storeFarmerTraining', {
    extend: 'Ext.data.Store',
    storeId: 'Koltiva.store.IMS.storeFarmerTraining',
    id: 'Koltiva.store.IMS.storeFarmerTraining',
    fields: ['id','training','batch','partner_name','tot','JumlahPeserta','participant','start','Fasilitator','end','days','EventType','TrainingStatus'],
    autoLoad: true,
    storeVar: false,
    setStoreVar: function(value){
        this.storeVar = value;
    },
    proxy: {
        type: 'ajax',
        url: m_api + '/training_farmer/datas',
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
/******************************************
 *  Author : n1colius.lau@gmail.com   
 *  Created On : Thu Dec 26 2019
 *  File : GridCoor.js
 *******************************************/
Ext.define('Koltiva.store.DataAdm.FarmSurveyLoc.GridCoor', {
    extend: 'Ext.data.Store',
    storeId: 'Koltiva.store.DataAdm.FarmSurveyLoc.GridCoor',
    id: 'Koltiva.store.DataAdm.FarmSurveyLoc.GridCoor',
    fields: ['FarmerID', 'GardenNr','SurveyNr','GardenInfo','CoordinateLabel','Latitude','Longitude','urutanIndex'],
    autoLoad: false,
    storeVar: false,
    setStoreVar: function(value){
        this.storeVar = value;
    },
    proxy: {
        type: 'ajax',
        url: m_api + '/data_adm/farm_survey_loc/grid_coor',
        reader: {
            type: 'json',
            root: 'data'
        }
    },
    listeners: {
        beforeload: function(store, operation, options){
            store.proxy.extraParams.FarmerID = this.storeVar.FarmerID;
        }
    }
});
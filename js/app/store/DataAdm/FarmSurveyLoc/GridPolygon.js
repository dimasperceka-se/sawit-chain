/******************************************
 *  Author : n1colius.lau@gmail.com   
 *  Created On : Fri Dec 27 2019
 *  File : GridPolygon.js
 *******************************************/
Ext.define('Koltiva.store.DataAdm.FarmSurveyLoc.GridPolygon', {
    extend: 'Ext.data.Store',
    storeId: 'Koltiva.store.DataAdm.FarmSurveyLoc.GridPolygon',
    id: 'Koltiva.store.DataAdm.FarmSurveyLoc.GridPolygon',
    fields: ['FarmerID', 'GardenNr','SurveyNr','Revision','StatusPolygon','JumlahTitik','GardenInfo','UrutanIndex','ColorName','ColorCode'],
    autoLoad: false,
    storeVar: false,
    setStoreVar: function(value){
        this.storeVar = value;
    },
    proxy: {
        type: 'ajax',
        url: m_api + '/data_adm/farm_survey_loc/grid_polygon',
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
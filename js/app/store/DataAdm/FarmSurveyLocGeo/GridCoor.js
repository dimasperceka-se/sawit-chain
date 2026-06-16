/******************************************
 *  Author : sofyan.salim@koltiva.com 
 *  Created On : 08-11-2021
 *  File : GridCoor.js
 *******************************************/
Ext.define('Koltiva.store.DataAdm.FarmSurveyLocGeo.GridCoor', {
    extend: 'Ext.data.Store',
    storeId: 'Koltiva.store.DataAdm.FarmSurveyLocGeo.GridCoor',
    id: 'Koltiva.store.DataAdm.FarmSurveyLocGeo.GridCoor',
    fields: ['FarmerID', 'GardenNr','SurveyNr','GardenInfo','CoordinateLabel','Latitude','Longitude','urutanIndex'],
    autoLoad: false,
    storeVar: false,
    setStoreVar: function(value){
        this.storeVar = value;
    },
    proxy: {
        type: 'ajax',
        url: m_api + '/data_adm/farm_survey_loc_geo/grid_coor',
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
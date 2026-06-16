/******************************************
 *  Author : sofyan.salim@koltiva.com 
 *  Created On : 08-11-2021
 *  File : GridPolygon.js
 *******************************************/
Ext.define('Koltiva.store.DataAdm.FarmSurveyLocGeo.GridPolygon', {
    extend: 'Ext.data.Store',
    storeId: 'Koltiva.store.DataAdm.FarmSurveyLocGeo.GridPolygon',
    id: 'Koltiva.store.DataAdm.FarmSurveyLocGeo.GridPolygon',
    fields: ['FarmerID', 'GardenNr','SurveyNr','Revision','StatusPolygon','JumlahTitik','GardenInfo','UrutanIndex','ColorName','ColorCode'],
    autoLoad: false,
    storeVar: false,
    setStoreVar: function(value){
        this.storeVar = value;
    },
    proxy: {
        type: 'ajax',
        url: m_api + '/data_adm/farm_survey_loc_geo/grid_polygon',
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
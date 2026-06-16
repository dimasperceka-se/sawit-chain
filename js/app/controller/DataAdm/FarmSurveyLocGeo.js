/******************************************
 *  Author : sofyan.salim@koltiva.com 
 *  Created On : 08-11-2021
 *  File : FarmSurveyLocGeo.js
 *******************************************/
Ext.define('Koltiva.controller.DataAdm.FarmSurveyLocGeo', {
    extend: 'Ext.app.Controller',
    init: function() {
        this.renderView();
    },
    renderView: function() {
        if(Ext.getCmp('Koltiva.view.DataAdm.FarmSurveyLocGeo.MainView') !== undefined){
            Ext.getCmp('Koltiva.view.DataAdm.FarmSurveyLocGeo.MainView').destroy();
        }            
        var MainView = Ext.create('Koltiva.view.DataAdm.FarmSurveyLocGeo.MainView');
    }
});
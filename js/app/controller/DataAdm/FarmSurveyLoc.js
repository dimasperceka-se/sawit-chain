/******************************************
 *  Author : fikrifauzul@gmail.com   
 *  Created On : 08-01-2020
 *  File : FarmSurveyLoc.js
 *******************************************/
Ext.define('Koltiva.controller.DataAdm.FarmSurveyLoc', {
    extend: 'Ext.app.Controller',
    init: function() {
        this.renderView();
    },
    renderView: function() {
        if(Ext.getCmp('Koltiva.view.DataAdm.FarmSurveyLoc.MainView') !== undefined){
            Ext.getCmp('Koltiva.view.DataAdm.FarmSurveyLoc.MainView').destroy();
        }            
        var MainView = Ext.create('Koltiva.view.DataAdm.FarmSurveyLoc.MainView');
    }
});
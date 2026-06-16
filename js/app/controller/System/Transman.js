/******************************************
 *  Author : n1colius.lau@gmail.com   
 *  Created On : Fri Sep 18 2020
 *  File : Transman.js
 *******************************************/
Ext.define('Koltiva.controller.System.Transman', {
    extend: 'Ext.app.Controller',
    init: function() {
        this.renderView();
    },
    renderView: function() {
    	var MainLayout = [];

        if(Ext.getCmp('Koltiva.view.System.Transman.MainGrid') == undefined){
            MainLayout = Ext.create('Koltiva.view.System.Transman.MainGrid');
        }else{
            //destroy, create ulang
            Ext.getCmp('Koltiva.view.System.Transman.MainGrid').destroy();
            MainLayout = Ext.create('Koltiva.view.System.Transman.MainGrid');
        }
    }
});
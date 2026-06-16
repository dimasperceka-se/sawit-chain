/******************************************
 *  Author : n1colius.lau@gmail.com   
 *  Created On : Thu Jan 16 2020
 *  File : Farmers.js
 *******************************************/
Ext.define('Koltiva.controller.Ext_staff', {
    extend: 'Ext.app.Controller',
    init: function() {
        this.renderView();
    },
    renderView: function() {
    	var MainLayout = [];

        if(Ext.getCmp('Koltiva.view.Ext_staff.MainGrid') == undefined){
            MainLayout = Ext.create('Koltiva.view.Ext_staff.MainGrid');
        }else{
            //destroy, create ulang
            Ext.getCmp('Koltiva.view.Ext_staff.MainGrid').destroy();
            MainLayout = Ext.create('Koltiva.view.Ext_staff.MainGrid');
        }
    }
});
/******************************************
 *  Author : n1colius.lau@gmail.com   
 *  Created On : Mon Sep 17 2018
 *  File : Document.js
 *******************************************/

Ext.Loader.setConfig({enabled: true});
Ext.Loader.setPath('js/ext-4.2.0.663', '../ux');
Ext.require([
    'Ext.ux.form.ItemSelector'
]);

Ext.define('Koltiva.controller.CMS.Document', {
    extend: 'Ext.app.Controller',
    init: function() {
        this.renderView();
    },
    renderView: function() {
    	var MainLayout = [];

        if(Ext.getCmp('Koltiva.view.CMS.GridMainDocument') == undefined){
            MainLayout = Ext.create('Koltiva.view.CMS.GridMainDocument');
        }else{
            //destroy, create ulang
            Ext.getCmp('Koltiva.view.CMS.GridMainDocument').destroy();
            MainLayout = Ext.create('Koltiva.view.CMS.GridMainDocument');
        }
    }
});
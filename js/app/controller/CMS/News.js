/******************************************
 *  Author : n1colius.lau@gmail.com   
 *  Created On : Fri Sep 14 2018
 *  File : News.js
 *******************************************/

Ext.Loader.setConfig({enabled: true});
Ext.Loader.setPath('js/ext-4.2.0.663', '../ux');
Ext.require([
    'Ext.ux.form.ItemSelector'
]);

Ext.define('Koltiva.controller.CMS.News', {
    extend: 'Ext.app.Controller',
    init: function() {
        this.renderView();
    },
    renderView: function() {
    	var MainLayout = [];

        if(Ext.getCmp('Koltiva.view.CMS.GridMainNews') == undefined){
            MainLayout = Ext.create('Koltiva.view.CMS.GridMainNews');
        }else{
            //destroy, create ulang
            Ext.getCmp('Koltiva.view.CMS.GridMainNews').destroy();
            MainLayout = Ext.create('Koltiva.view.CMS.GridMainNews');
        }
    }
});
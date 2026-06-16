/******************************************
 *  Author : n1colius.lau@gmail.com   
 *  Created On : Wed Sep 12 2018
 *  File : Video.js
 *******************************************/

Ext.Loader.setConfig({enabled: true});
Ext.Loader.setPath('js/ext-4.2.0.663', '../ux');
Ext.require([
    'Ext.ux.form.ItemSelector'
]);

Ext.define('Koltiva.controller.CMS.Video', {
    extend: 'Ext.app.Controller',
    init: function() {
        this.renderView();
    },
    renderView: function() {
    	var MainLayout = [];

        if(Ext.getCmp('Koltiva.view.CMS.GridMainVideo') == undefined){
            MainLayout = Ext.create('Koltiva.view.CMS.GridMainVideo');
        }else{
            //destroy, create ulang
            Ext.getCmp('Koltiva.view.CMS.GridMainVideo').destroy();
            MainLayout = Ext.create('Koltiva.view.CMS.GridMainVideo');
        }
    }
});
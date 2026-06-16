/******************************************
 *  Author : nikolius.lau@gmail.com   
 *  Created On : Wed Sep 05 2018
 *  File : Announcement.js
 *******************************************/

Ext.Loader.setConfig({enabled: true});
Ext.Loader.setPath('js/ext-4.2.0.663', '../ux');
Ext.require([
    'Ext.ux.form.ItemSelector'
]);

Ext.define('Koltiva.controller.CMS.Announcement', {
    extend: 'Ext.app.Controller',
    init: function() {
        this.renderView();
    },
    renderView: function() {
    	var MainLayout = [];

        if(Ext.getCmp('Koltiva.view.CMS.GridMainAnnouncement') == undefined){
            MainLayout = Ext.create('Koltiva.view.CMS.GridMainAnnouncement');
        }else{
            //destroy, create ulang
            Ext.getCmp('Koltiva.view.CMS.GridMainAnnouncement').destroy();
            MainLayout = Ext.create('Koltiva.view.CMS.GridMainAnnouncement');
        }
    }
});
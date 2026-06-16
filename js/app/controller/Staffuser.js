/******************************************
 *  Author : n1colius.lau@gmail.com   
 *  Created On : Mon Jul 13 2020
 *  File : Staffuser.js
 *******************************************/
Ext.Loader.setConfig({enabled: true});
Ext.Loader.setPath('js/ext-4.2.0.663', '../ux');
Ext.require([
    'Ext.ux.form.ItemSelector'
]);

Ext.define('Koltiva.controller.Staffuser', {
    extend: 'Ext.app.Controller',
    init: function() {
        this.renderView();
    },
    renderView: function() {
        var MainLayout = [];

        if(Ext.getCmp('Koltiva.view.Staffuser.MainGrid') == undefined){
            MainLayout = Ext.create('Koltiva.view.Staffuser.MainGrid');
        }else{
            //destroy, create ulang
            Ext.getCmp('Koltiva.view.Staffuser.MainGrid').destroy();
            MainLayout = Ext.create('Koltiva.view.Staffuser.MainGrid');
        }
    }
});
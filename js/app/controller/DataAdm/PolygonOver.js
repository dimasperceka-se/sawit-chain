/******************************************
 *  Author : fikrifauzul@gmail.com
 *  Created On : 13-05-2020
 *  File : PolygonOver.js
 *******************************************/
Ext.Loader.setConfig({enabled: true});
Ext.Loader.setPath('js/ext-4.2.0.663', '../ux');
Ext.require([
    'Ext.ux.form.ItemSelector'
]);

Ext.define('Koltiva.controller.DataAdm.PolygonOver', {
    extend: 'Ext.app.Controller',
    init: function() {
        this.renderView();
    },
    renderView: function() {
    	var MainLayout = [];

        if(Ext.getCmp('Koltiva.view.DataAdm.PolygonOver.MainGrid') == undefined){
            MainLayout = Ext.create('Koltiva.view.DataAdm.PolygonOver.MainGrid');
        }else{
            //destroy, create ulang
            Ext.getCmp('Koltiva.view.DataAdm.PolygonOver.MainGrid').destroy();
            MainLayout = Ext.create('Koltiva.view.DataAdm.PolygonOver.MainGrid');
        }
    }
});
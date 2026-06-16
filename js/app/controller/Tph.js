/******************************************
 *  Author : n1colius.lau@gmail.com   
 *  Created On : Thu May 02 2019
 *  File : Tph.js
 *******************************************/
Ext.Loader.setConfig({enabled: true});
Ext.Loader.setPath('js/ext-4.2.0.663', '../ux');
Ext.require([
    'Ext.ux.form.ItemSelector'
]);

Ext.define('Koltiva.controller.Tph', {
    extend: 'Ext.app.Controller',
    init: function() {
        this.renderView();
    },
    renderView: function() {
        if(Ext.getCmp('Koltiva.view.Tph.GridMainTph') == undefined){
            var mainLayoutTph = Ext.create('Koltiva.view.Tph.GridMainTph');
        }else{
            //destroy, create ulang
            Ext.getCmp('Koltiva.view.Tph.GridMainTph').destroy();
            var mainLayoutTph = Ext.create('Koltiva.view.Tph.GridMainTph');
        }
    }
});
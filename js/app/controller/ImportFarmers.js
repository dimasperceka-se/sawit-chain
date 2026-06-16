/*
* @Author: fikri.fauzul
* @Date:   2019-09-04 16:20:51
*/

/* global Ext */

Ext.define('Koltiva.controller.ImportFarmers', {
    extend: 'Ext.app.Controller',
    init: function() {
        this.renderView();
    },
    renderView: function() {
    	var MainLayout = [];

        if(Ext.getCmp('Koltiva.view.ImportFarmers.GridMainFarmers') == undefined){
            MainLayout = Ext.create('Koltiva.view.ImportFarmers.GridMainFarmers');
        }else{
            //destroy, create ulang
            Ext.getCmp('Koltiva.view.ImportFarmers.GridMainFarmers').destroy();
            MainLayout = Ext.create('Koltiva.view.ImportFarmers.GridMainFarmers');
        }
    }
});
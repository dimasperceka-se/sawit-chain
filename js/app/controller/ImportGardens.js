/*
* @Author: fikri.fauzul
* @Date:   2019-09-04 16:20:51
*/

/* global Ext */

Ext.define('Koltiva.controller.ImportGardens', {
    extend: 'Ext.app.Controller',
    init: function() {
        this.renderView();
    },
    renderView: function() {
    	var MainLayout = [];

        if(Ext.getCmp('Koltiva.view.ImportGardens.GridMainFarmers') == undefined){
            MainLayout = Ext.create('Koltiva.view.ImportGardens.MainGrid');
        }else{
            //destroy, create ulang
            Ext.getCmp('Koltiva.view.ImportGardens.GridMainFarmers').destroy();
            MainLayout = Ext.create('Koltiva.view.ImportGardens.MainGrid');
        }
    }
});
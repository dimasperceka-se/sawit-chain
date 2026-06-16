/* 
 * *****************************************
 * Author : fikrifauzul@gmail.com
 * Created On : May 27, 2021
 * File : Bank.js
 * *****************************************
 */
Ext.define('Koltiva.controller.Basic.Bank', {
    extend: 'Ext.app.Controller',
    init: function() {
        this.renderView();
    },
    renderView: function() {
    	var MainLayout = [];

        if(Ext.getCmp('Koltiva.view.Basic.NewBank.MainGrid') == undefined){
            MainLayout = Ext.create('Koltiva.view.Basic.NewBank.MainGrid');
        }else{
            //destroy, create ulang
            Ext.getCmp('Koltiva.view.Basic.NewBank.MainGrid').destroy();
            MainLayout = Ext.create('Koltiva.view.Basic.NewBank.MainGrid');
        }
    }
});


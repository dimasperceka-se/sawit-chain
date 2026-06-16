/*
* @Author: Fashah Darullah
* @Date:   2019-07-08 11:12:36
* @Last Modified by:   
* @Last Modified time: 
*/
Ext.Loader.setConfig({enabled: true});
Ext.Loader.setPath('js/ext-4.2.0.663', '../ux');
Ext.require([
    'Ext.ux.form.ItemSelector',
    'Ext.ux.CKEditor'
]);

Ext.define('Koltiva.controller.FarmCloud.MessagesManagement', {
    extend: 'Ext.app.Controller',
    init: function() {
        this.renderView();
    },
    renderView: function() {
    	var MainLayout = [];
        if(Ext.getCmp('Koltiva.view.FarmCloud.MessagesManagementGrid') == undefined){
            MainLayout = Ext.create('Koltiva.view.FarmCloud.MessagesManagementGrid');
        }else{
            //destroy, create ulang
            Ext.getCmp('Koltiva.view.FarmCloud.MessagesManagementGrid').destroy();
            MainLayout = Ext.create('Koltiva.view.FarmCloud.MessagesManagementGrid');
        }
    }
});
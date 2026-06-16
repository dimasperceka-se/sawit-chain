Ext.define('Koltiva.controller.FarmCloud.UseraccManagement', {
    extend: 'Ext.app.Controller',
    init: function() {
        this.renderView();
    },
    renderView: function() {
    	var MainLayout = [];

        if(Ext.getCmp('Koltiva.view.FarmCloud.UseraccManagement.MainGrid') == undefined){
            MainLayout = Ext.create('Koltiva.view.FarmCloud.UseraccManagement.MainGrid');
        }else{
            //destroy, create ulang
            Ext.getCmp('Koltiva.view.FarmCloud.UseraccManagement.MainGrid').destroy();
            MainLayout = Ext.create('Koltiva.view.FarmCloud.UseraccManagement.MainGrid');
        }
    }
});
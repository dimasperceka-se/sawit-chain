
Ext.define('Koltiva.controller.UserDashboard', {
    extend: 'Ext.app.Controller',
    init: function() {
        this.renderView();
    },
    renderView: function() {
    	var MainLayout = [];

        if(Ext.getCmp('Koltiva.view.UserDashboard.MainGrid') == undefined){
            MainLayout = Ext.create('Koltiva.view.UserDashboard.MainGrid');
        }else{
            //destroy, create ulang
            Ext.getCmp('Koltiva.view.UserDashboard.MainGrid').destroy();
            MainLayout = Ext.create('Koltiva.view.UserDashboard.MainGrid');
        }
    }
});
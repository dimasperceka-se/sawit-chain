Ext.Loader.setConfig({enabled: true});
Ext.Loader.setPath('js/ext-4.2.0.663', '../ux');

Ext.define('Koltiva.controller.Menu_pull_engine_check', {
    extend: 'Ext.app.Controller',
    init: function() {
        this.renderView();
    },
    renderView: function() {
    	var MainLayout = [];

        if(Ext.getCmp('Koltiva.view.Menu_pull_engine_check.MainGrid') == undefined){
            MainLayout = Ext.create('Koltiva.view.Menu_pull_engine_check.MainGrid');
        }else{
            //destroy, create ulang
            Ext.getCmp('Koltiva.view.Menu_pull_engine_check.MainGrid').destroy();
            MainLayout = Ext.create('Koltiva.view.Menu_pull_engine_check.MainGrid');
        }
    }
});
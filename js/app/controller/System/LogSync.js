Ext.Loader.setConfig({enabled: true});
Ext.Loader.setPath('js/ext-4.2.0.663', '../ux');
Ext.require([
    'Ext.ux.DateTimeField'
]);

Ext.define('Koltiva.controller.System.LogSync', {
    extend: 'Ext.app.Controller',
    init: function() {
        this.renderView();
    },
    renderView: function() {
    	var MainLayout = [];

        if(Ext.getCmp('Koltiva.view.System.LogSync.MainGrid') == undefined){
            MainLayout = Ext.create('Koltiva.view.System.LogSync.MainGrid');
        }else{
            //destroy, create ulang
            Ext.getCmp('Koltiva.view.System.LogSync.MainGrid').destroy();
            MainLayout = Ext.create('Koltiva.view.System.LogSync.MainGrid');
        }
    }
});
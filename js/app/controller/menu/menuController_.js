Ext.Loader.setConfig({enabled: true});
Ext.Loader.setPath('js/ext-4.2.0.663', '../ux');
Ext.require([
    'Ext.ux.form.ItemSelector'
]);

Ext.define('Koltiva.controller.menu.menuController_', {
    extend: 'Ext.app.Controller',
    init: function() {
        this.renderView();
    },
    renderView: function() {
        if(Ext.getCmp('Koltiva.view.menu.MainGridMenu') == undefined){
            MainLayout = Ext.create('Koltiva.view.menu.MainGridMenu');
        }else{
            //destroy, create ulang
            Ext.getCmp('Koltiva.view.menu.MainGridMenu').destroy();
            MainLayout = Ext.create('Koltiva.view.menu.MainGridMenu');
        }
    }

});
Ext.Loader.setConfig({enabled: true});
Ext.Loader.setPath('js/ext-4.2.0.663', '../ux');
Ext.require([
    'Ext.ux.form.ItemSelector'
]);

Ext.define('Koltiva.controller.NewSocialization', {
    extend: 'Ext.app.Controller',
    init: function() {
        this.renderView();
    },
    renderView: function() {
    	var MainLayout = [];

        if(Ext.getCmp('Koltiva.view.NewSocialization.MainGrid') == undefined){
            MainLayout = Ext.create('Koltiva.view.NewSocialization.MainGrid');
        }else{
            //destroy, create ulang
            Ext.getCmp('Koltiva.view.NewSocialization.MainGrid').destroy();
            MainLayout = Ext.create('Koltiva.view.NewSocialization.MainGrid');
        }
    }
});
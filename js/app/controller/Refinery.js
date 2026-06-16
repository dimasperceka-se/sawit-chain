Ext.Loader.setConfig({enabled: true});
Ext.Loader.setPath('js/ext-4.2.0.663', '../ux');
Ext.require([
    'Ext.ux.form.ItemSelector'
]);

Ext.define('Koltiva.controller.Refinery', {
    extend: 'Ext.app.Controller',
    init: function() {
        this.renderView();
    },
    renderView: function() {
        if(Ext.getCmp('Koltiva.view.Refinery.GridMainRefinery') == undefined){
            var mainLayoutMill = Ext.create('Koltiva.view.Refinery.GridMainRefinery');
        }else{
            //destroy, create ulang
            Ext.getCmp('Koltiva.view.Refinery.GridMainRefinery').destroy();
            var mainLayoutMill = Ext.create('Koltiva.view.Refinery.GridMainRefinery');
        }
    }
});
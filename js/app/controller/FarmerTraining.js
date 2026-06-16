Ext.Loader.setConfig({enabled: true});
Ext.Loader.setPath('js/ext-4.2.0.663', '../ux');
Ext.require([
    'Ext.ux.form.ItemSelector'
]);

Ext.define('Koltiva.controller.FarmerTraining', {
    extend: 'Ext.app.Controller',
    init: function() {
        this.renderView();
    },
    renderView: function() {

        if(Ext.getCmp('Koltiva.view.FarmerTraining.MainGrid') == undefined){
            Ext.create('Koltiva.view.FarmerTraining.MainGrid');
        }else{
            //destroy, create ulang
            Ext.getCmp('Koltiva.view.FarmerTraining.MainGrid').destroy();
            Ext.create('Koltiva.view.FarmerTraining.MainGrid');
        }
    }
});
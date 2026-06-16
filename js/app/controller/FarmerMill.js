Ext.Loader.setConfig({enabled: true});
Ext.Loader.setPath('js/ext-4.2.0.663', '../ux');
Ext.require([
    'Ext.ux.form.ItemSelector'
]);

Ext.define('Koltiva.controller.FarmerMill', {
    extend: 'Ext.app.Controller',
    init: function() {
        this.renderView();
    },
    renderView: function() {
        if(Ext.getCmp('Koltiva.view.FarmerMill.GridMainFarmerMill-MainPanel') !== undefined){
            Ext.getCmp('Koltiva.view.FarmerMill.GridMainFarmerMill-MainPanel').destroy();
        }            
        var mainLayoutGrower = Ext.create('Koltiva.view.FarmerMill.GridMainFarmerMill');        
    }
});
/**************************************
 * Author : aji.alhabsyi@koltiva.com
 * Created On : Thu June 23 2022
 * File : FuelType.js
 ************************************** */
Ext.Loader.setConfig({enabled: true});
Ext.Loader.setPath('js/ext-4.2.0.663', '../ux');
Ext.require([
    'Ext.ux.form.ItemSelector'
]); 

Ext.define('Koltiva.controller.TraceabilitySetting.FuelType', {
    extend: 'Ext.app.Controller',
    init: function() {
        this.renderView();
    },
    renderView: function() {
        var MainLayout = [];

        if (Ext.getCmp('Koltiva.view.TraceabilitySetting.FuelType.MainGrid') == undefined) {
            MainLayout = Ext.create('Koltiva.view.TraceabilitySetting.FuelType.MainGrid');
        } else {
            Ext.getCmp('Koltiva.view.TraceabilitySetting.FuelType.MainGrid').destroy();
            MainLayout = Ext.create('Koltiva.view.TraceabilitySetting.FuelType.MainGrid');
        }
    }
});
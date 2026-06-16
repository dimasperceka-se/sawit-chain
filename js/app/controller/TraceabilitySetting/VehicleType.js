/*******************************************
 * Author : aji.alhabsyi@koltiva.com
 * Created On : Tue June 28 2022
 * File : VehicleType.js
********************************************/
Ext.Loader.setConfig({enabled: true});
Ext.Loader.setPath('js/ext-4.2.0.663', '../ux');
Ext.require([
    'Ext.ux.form.ItemSelector'
]); 

Ext.define('Koltiva.controller.TraceabilitySetting.VehicleType', {
    extend: 'Ext.app.Controller',
    init: function() {
        this.renderView();
    },
    renderView: function() {
        var MainLayout = [];

        if (Ext.getCmp('Koltiva.view.TraceabilitySetting.VehicleType.MainGrid') == undefined) {
            MainLayout =  Ext.create('Koltiva.view.TraceabilitySetting.VehicleType.MainGrid');
        } else {
            Ext.getCmp('Koltiva.view.TraceabilitySetting.VehicleType.MainGrid').destroy();
                MainLayout =  Ext.create('Koltiva.view.TraceabilitySetting.VehicleType.MainGrid');
        }
    }
});
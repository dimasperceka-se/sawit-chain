/*
 * @Author: sonny.fitriawan 
 * @Date: 2017-12-07 14:11:37 
 * @Last Modified by: sonny.fitriawan
 * @Last Modified time: 2017-12-08 10:20:11
 */

Ext.Loader.setConfig({enabled: true});
Ext.Loader.setPath('js/ext-4.2.0.663', '../ux');
Ext.require([
    'Ext.ux.form.ItemSelector'
]);

Ext.define('Koltiva.controller.Reference.Vehicle', {
    extend: 'Ext.app.Controller',
    init: function() {
        this.renderView();
    },
    renderView: function() {
        var mainLayoutVehicle = [];

        if(Ext.getCmp('Koltiva.view.Reference.Vehicle.MainGrid') == undefined){
            mainLayoutVehicle = Ext.create('Koltiva.view.Reference.Vehicle.MainGrid');
        }else{
            //destroy, create ulang
            Ext.getCmp('Koltiva.view.Reference.Vehicle.MainGrid').destroy();
            mainLayoutVehicle = Ext.create('Koltiva.view.Reference.Vehicle.MainGrid');
        }
    }
});


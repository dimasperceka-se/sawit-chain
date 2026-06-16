/*
* @Author: nikolius
* @Date:   2017-11-08 15:46:39
* @Last Modified by:   nikolius
* @Last Modified time: 2017-11-08 15:48:28
*/

Ext.Loader.setConfig({enabled: true});
Ext.Loader.setPath('js/ext-4.2.0.663', '../ux');
Ext.require([
    'Ext.ux.form.ItemSelector'
]);

Ext.define('Koltiva.controller.FarmerGroup', {
    extend: 'Ext.app.Controller',
    init: function() {
        this.renderView();
    },
    renderView: function() {
        var mainLayoutFarmerGroup = [];

        if(Ext.getCmp('Koltiva.view.FarmerGroup.GridMainFarmerGroup') == undefined){
            mainLayoutFarmerGroup = Ext.create('Koltiva.view.FarmerGroup.GridMainFarmerGroup');
        }else{
            //destroy, create ulang
            Ext.getCmp('Koltiva.view.FarmerGroup.GridMainFarmerGroup').destroy();
            mainLayoutFarmerGroup = Ext.create('Koltiva.view.FarmerGroup.GridMainFarmerGroup');
        }
    }
});
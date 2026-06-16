/*
 * @Author: sofyan
 * @Date:   2021-11-08 
*/

Ext.Loader.setConfig({enabled: true});
Ext.Loader.setPath('js/ext-4.2.0.663', '../ux');
Ext.require([
    'Ext.ux.form.ItemSelector'
]);

Ext.define('Koltiva.controller.DataAdm.FarmSummary', {
    extend: 'Ext.app.Controller',
    init: function() {
        this.renderView();
    },
    renderView: function() {
        if(Ext.getCmp('Koltiva.view.DataAdm.FarmSummary.GridMainFarmSummary') == undefined){
            var mainLayoutFarmSummary = Ext.create('Koltiva.view.DataAdm.FarmSummary.GridMainFarmSummary');
        }else{
            //destroy, create ulang
            Ext.getCmp('Koltiva.view.DataAdm.FarmSummary.GridMainFarmSummary').destroy();
            var mainLayoutFarmSummary = Ext.create('Koltiva.view.DataAdm.FarmSummary.GridMainFarmSummary');
        }
    }
});
/*
 * @Author: mawwatudi
 * @Date:   2018-01-03 11:18:00
 * @Last Modified by:   
 * @Last Modified time:
*/

Ext.Loader.setConfig({enabled: true});
Ext.Loader.setPath('js/ext-4.2.0.663', '../ux');
Ext.require([
    'Ext.ux.form.ItemSelector'
]);

Ext.define('Koltiva.controller.Traceability_new.Supplychain_quality', {
    extend: 'Ext.app.Controller',
    init: function() {
        this.renderGrid();
    },
    renderGrid: function() {
        var mainLayout;

        if(Ext.getCmp('Koltiva.view.Traceability_new.Supplychain_quality.MainGrid') == undefined){
            mainLayout = Ext.create('Koltiva.view.Traceability_new.Supplychain_quality.MainGrid');
        }else{
            //destroy, create ulang
            Ext.getCmp('Koltiva.view.Traceability_new.Supplychain_quality.MainGrid').destroy();
            mainLayout = Ext.create('Koltiva.view.Traceability_new.Supplychain_quality.MainGrid');
        }
    }
});
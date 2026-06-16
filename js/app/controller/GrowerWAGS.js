/*
* @Author: nikolius
* @Date:   2017-05-16 11:45:24
* @Last Modified by:   nikolius
* @Last Modified time: 2017-06-16 11:55:55
*/
Ext.Loader.setConfig({enabled: true});
Ext.Loader.setPath('js/ext-4.2.0.663', '../ux');
Ext.require([
    'Ext.ux.form.ItemSelector'
]);

Ext.define('Koltiva.controller.GrowerWAGS', {
    extend: 'Ext.app.Controller',
    init: function() {
        this.renderView();
    },
    renderView: function() {
        if(Ext.getCmp('Koltiva.view.GrowerWAGS.GridMainGrower-MainPanel') !== undefined){
            Ext.getCmp('Koltiva.view.GrowerWAGS.GridMainGrower-MainPanel').destroy();
        }            
        var mainLayoutGrower = Ext.create('Koltiva.view.GrowerWAGS.GridMainGrower');        
    }
});
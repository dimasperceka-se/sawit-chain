/*
* @Author: Gitandi Nadzari
* @Date:   2018-09-19 15:30:00
* @Last Modified by:   Gitandi Nadzari
* @Last Modified time: 2018-09-19 15:30:00
*/
Ext.Loader.setConfig({enabled: true});
Ext.Loader.setPath('js/ext-4.2.0.663', '../ux');
Ext.require([
    'Ext.ux.form.ItemSelector'
]);

Ext.define('Koltiva.controller.Report.SawitTerampil', {
    extend: 'Ext.app.Controller',
    init: function() {
        this.renderView();
    },
    renderView: function() {
        var MainLayout = [];

        if(Ext.getCmp('Koltiva.view.Report.SawitTerampilWinDetailGrid') == undefined){
            MainLayout = Ext.create('Koltiva.view.Report.SawitTerampilWinDetailGrid');
        }else{
            //destroy, create ulang
            Ext.getCmp('Koltiva.view.Report.SawitTerampilWinDetailGrid').destroy();
            MainLayout = Ext.create('Koltiva.view.Report.SawitTerampilWinDetailGrid');
        }
    }
});
/*
* @Author: nikolius
* @Date:   2017-10-11 16:35:18
* @Last Modified by:   nikolius
* @Last Modified time: 2017-10-11 16:35:43
*/

Ext.Loader.setConfig({enabled: true});
Ext.Loader.setPath('js/ext-4.2.0.663', '../ux');
Ext.require([
    'Ext.ux.form.ItemSelector'
]);

Ext.define('Koltiva.controller.DataAdm.AdcMill', {
    extend: 'Ext.app.Controller',
    init: function() {
        this.renderGrid();
    },
    renderGrid: function() {
        var mainLayout;

        if(Ext.getCmp('Koltiva.view.DataAdm.AdcMill.MainForm') == undefined){
            mainLayout = Ext.create('Koltiva.view.DataAdm.AdcMill.MainForm');
        }else{
            //destroy, create ulang
            Ext.getCmp('Koltiva.view.DataAdm.AdcMill.MainForm').destroy();
            mainLayout = Ext.create('Koltiva.view.DataAdm.AdcMill.MainForm');
        }
    }
});
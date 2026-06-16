/*
* @Author: nikolius
* @Date:   2017-10-09 16:13:59
* @Last Modified by:   nikolius
* @Last Modified time: 2017-10-10 16:11:31
*/

Ext.Loader.setConfig({enabled: true});
Ext.Loader.setPath('js/ext-4.2.0.663', '../ux');
Ext.require([
    'Ext.ux.form.ItemSelector'
]);

Ext.define('Koltiva.controller.DataAdm.AdcMember', {
    extend: 'Ext.app.Controller',
    init: function() {
        this.renderGrid();
    },
    renderGrid: function() {
        var mainLayout;

        if(Ext.getCmp('Koltiva.view.DataAdm.AdcMember.MainForm') == undefined){
            mainLayout = Ext.create('Koltiva.view.DataAdm.AdcMember.MainForm');
        }else{
            //destroy, create ulang
            Ext.getCmp('Koltiva.view.DataAdm.AdcMember.MainForm').destroy();
            mainLayout = Ext.create('Koltiva.view.DataAdm.AdcMember.MainForm');
        }
    }
});
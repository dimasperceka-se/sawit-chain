/*
* @Author: nikolius
* @Date:   2017-10-13 13:09:28
* @Last Modified by:   nikolius
* @Last Modified time: 2017-10-13 13:11:20
*/

Ext.Loader.setConfig({enabled: true});
Ext.Loader.setPath('js/ext-4.2.0.663', '../ux');
Ext.require([
    'Ext.ux.form.ItemSelector'
]);

Ext.define('Koltiva.controller.Staff.RegisterStaff', {
    extend: 'Ext.app.Controller',
    init: function() {
        this.renderGrid();
    },
    renderGrid: function() {
        var mainLayout;

        if(Ext.getCmp('Koltiva.view.Staff.RegisterStaff.MainGrid') == undefined){
            mainLayout = Ext.create('Koltiva.view.Staff.RegisterStaff.MainGrid');
        }else{
            //destroy, create ulang
            Ext.getCmp('Koltiva.view.Staff.RegisterStaff.MainGrid').destroy();
            mainLayout = Ext.create('Koltiva.view.Staff.RegisterStaff.MainGrid');
        }
    }
});
/*
* @Author: nikolius
* @Date:   2017-10-13 13:09:28
 * @Last Modified by: komarudin
 * @Last Modified time: 2018-05-28 13:24:59
*/

Ext.Loader.setConfig({enabled: true});
Ext.Loader.setPath('js/ext-4.2.0.663', '../ux');
Ext.require([
    'Ext.ux.form.ItemSelector'
]);

Ext.define('Koltiva.controller.Report.Transaction', {
    extend: 'Ext.app.Controller',
    init: function() {
        this.renderGrid();
    },
    renderGrid: function() {
        var mainLayout;

        if(Ext.getCmp('Koltiva.view.Report.Transaction.MainGrid') == undefined){
            mainLayout = Ext.create('Koltiva.view.Report.Transaction.MainGrid');
        }else{
            //destroy, create ulang
            Ext.getCmp('Koltiva.view.Report.Transaction.MainGrid').destroy();
            mainLayout = Ext.create('Koltiva.view.Report.Transaction.MainGrid');
        }
    }
});
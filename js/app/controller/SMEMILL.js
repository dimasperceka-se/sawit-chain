/*
* @Author: nikolius
* @Date:   2017-07-18 15:03:06
* @Last Modified by:   nikolius
* @Last Modified time: 2017-07-19 11:00:46
*/

Ext.Loader.setConfig({enabled: true});
Ext.Loader.setPath('js/ext-4.2.0.663', '../ux');
Ext.require([
    'Ext.ux.form.ItemSelector'
]);

Ext.define('Koltiva.controller.SMEMILL', {
    extend: 'Ext.app.Controller',
    init: function() {
        this.renderView();
    },
    renderView: function() {
        if(Ext.getCmp('Koltiva.view.SME.GridMainTraderMill') == undefined){
            var mainLayoutTrader = Ext.create('Koltiva.view.SME.GridMainTraderMill');
        }else{
            //destroy, create ulang
            Ext.getCmp('Koltiva.view.SME.GridMainTraderMill').destroy();
            var mainLayoutTrader = Ext.create('Koltiva.view.SME.GridMainTraderMill');
        }
    }
});
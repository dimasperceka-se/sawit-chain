/*
* @Author: nikolius
* @Date:   2017-08-03 15:22:31
* @Last Modified by:   nikolius
* @Last Modified time: 2017-08-04 09:53:19
*/

Ext.Loader.setConfig({enabled: true});
Ext.Loader.setPath('js/ext-4.2.0.663', '../ux');
Ext.require([
    'Ext.ux.form.ItemSelector'
]);

Ext.define('Koltiva.controller.Mill.TraceabilityDeclaration', {
    extend: 'Ext.app.Controller',
    init: function() {
        this.renderView();
    },
    renderView: function() {
        if(Ext.getCmp('Koltiva.view.Mill.FormTracebilityDeclaration') == undefined){
            var FormTracebilityDeclaration = Ext.create('Koltiva.view.Mill.FormTracebilityDeclaration', {
                opsiDisplay: 'view',
                viewVar: {
                    PartnerID: m_PartnerID
                }
            });
        }else{
            //destroy, create ulang
            Ext.getCmp('Koltiva.view.Mill.FormTracebilityDeclaration').destroy();
            var FormTracebilityDeclaration = Ext.create('Koltiva.view.Mill.FormTracebilityDeclaration', {
                opsiDisplay: 'view',
                viewVar: {
                    PartnerID: m_PartnerID
                }
            });
        }
    }
});
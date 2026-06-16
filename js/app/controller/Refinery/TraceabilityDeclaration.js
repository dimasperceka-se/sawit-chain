Ext.Loader.setConfig({enabled: true});
Ext.Loader.setPath('js/ext-4.2.0.663', '../ux');
Ext.require([
    'Ext.ux.form.ItemSelector'
]);

Ext.define('Koltiva.controller.Refinery.TraceabilityDeclaration', {
    extend: 'Ext.app.Controller',
    init: function() {
        this.renderView();
    },
    renderView: function() {
        if(Ext.getCmp('Koltiva.view.Refinery.FormTracebilityDeclaration') == undefined){
            var FormTracebilityDeclaration = Ext.create('Koltiva.view.Refinery.FormTracebilityDeclaration', {
                opsiDisplay: 'view',
                viewVar: {
                    PartnerID: m_PartnerID
                }
            });
        }else{
            //destroy, create ulang
            Ext.getCmp('Koltiva.view.Refinery.FormTracebilityDeclaration').destroy();
            var FormTracebilityDeclaration = Ext.create('Koltiva.view.Refinery.FormTracebilityDeclaration', {
                opsiDisplay: 'view',
                viewVar: {
                    PartnerID: m_PartnerID
                }
            });
        }
    }
});
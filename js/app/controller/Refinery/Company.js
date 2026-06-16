Ext.Loader.setConfig({enabled: true});
Ext.Loader.setPath('js/ext-4.2.0.663', '../ux');
Ext.require([
    'Ext.ux.form.ItemSelector'
]);

Ext.define('Koltiva.controller.Refinery.Company', {
    extend: 'Ext.app.Controller',
    init: function() {
        this.renderView();
    },
    renderView: function() {
        if(Ext.getCmp('Koltiva.view.Refinery.FormMainRefineryProfile') == undefined){
            var FormMainRefineryProfile = Ext.create('Koltiva.view.Refinery.FormMainRefineryProfile', {
                opsiDisplay: 'view',
                viewVar: {
                    PartnerID: m_PartnerID
                }
            });
        }else{
            //destroy, create ulang
            Ext.getCmp('Koltiva.view.Refinery.FormMainRefineryProfile').destroy();
            var FormMainRefineryProfile = Ext.create('Koltiva.view.Refinery.FormMainRefineryProfile', {
                opsiDisplay: 'view',
                viewVar: {
                    PartnerID: m_PartnerID
                }
            });
        }
    }
});
Ext.Loader.setConfig({enabled: true});
Ext.Loader.setPath('js/ext-4.2.0.663', '../ux');
Ext.require([
    'Ext.ux.form.ItemSelector'
]);

Ext.define('Koltiva.controller.Report.Recap_transaction', {
    extend: 'Ext.app.Controller',
    init: function() {
        this.renderGrid();
    },
    renderGrid: function() {
        var mainLayout;

        if(Ext.getCmp('Koltiva.view.Traceablility_neo.Report.MainGrid') == undefined){
            mainLayout = Ext.create('Koltiva.view.Traceability_neo.Report.MainGrid');
        }else{
            //destroy, create ulang
            Ext.getCmp('Koltiva.view.Traceablility_neo.Report.MainGrid').destroy();
            mainLayout = Ext.create('Koltiva.view.Traceability_neo.Report.MainGrid');
        }
    }
});
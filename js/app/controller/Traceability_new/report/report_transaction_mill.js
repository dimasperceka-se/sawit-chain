 

Ext.Loader.setConfig({enabled: true});
Ext.Loader.setPath('js/ext-4.2.0.663', '../ux');
Ext.require([
    'Ext.ux.form.ItemSelector'
]);

Ext.define('Koltiva.controller.Traceability_new.report.report_transaction_mill', {
    extend: 'Ext.app.Controller',
    init: function() {
        this.renderGrid();
    },
    renderGrid: function() {
        var mainLayout;

        
        if(Ext.getCmp('Koltiva.view.Traceability_new.report.MainGridTransMill') == undefined){
            mainLayout = Ext.create('Koltiva.view.Traceability_new.report.MainGridTransMill');
        }else{
            //destroy, create ulang
            Ext.getCmp('Koltiva.view.Traceability_new.report.MainGridTransMill').destroy();
            mainLayout = Ext.create('Koltiva.view.Traceability_new.report.Transaction.MainGridTransMill');
        }
    }
});
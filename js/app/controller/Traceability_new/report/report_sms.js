Ext.define('Koltiva.controller.Traceability_new.report.report_sms', {
    extend: 'Ext.app.Controller',
    init: function () {
        this.renderView();
    },
    renderView: function () {
        var MainLayout = [];

        if (Ext.getCmp('Koltiva.view.Traceability_new.report.MainGridSms') == undefined) {
            MainLayout = Ext.create('Koltiva.view.Traceability_new.report.MainGridSms');
        } else {
            //destroy, create ulang
            Ext.getCmp('Koltiva.view.Traceability_new.report.MainGridSms').destroy();
            MainLayout = Ext.create('Koltiva.view.Traceability_new.report.MainGridSms');
        }
    }
});
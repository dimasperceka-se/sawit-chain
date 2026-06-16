Ext.define('Koltiva.controller.Traceability_new.Transaction_neo', {
    extend: 'Ext.app.Controller',
    init: function () {
        this.renderView();
    },
    renderView: function () {
        var MainLayout = [];

        if (Ext.getCmp('Koltiva.view.Traceability_new.Transaction_neo.MainGrid') == undefined) {
            MainLayout = Ext.create('Koltiva.view.Traceability_new.Transaction_neo.MainGrid');
        } else {
            //destroy, create ulang
            Ext.getCmp('Koltiva.view.Traceability_new.Transaction_neo.MainGrid').destroy();
            MainLayout = Ext.create('Koltiva.view.Traceability_new.Transaction_neo.MainGrid');
        }
    }
});
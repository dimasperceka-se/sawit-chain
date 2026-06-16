Ext.define('Koltiva.controller.Traceability_new.Batching', {
    extend: 'Ext.app.Controller',
    init: function () {
        this.renderView();
    },
    renderView: function () {
        var MainLayout = [];

        if (Ext.getCmp('Koltiva.view.Traceability_new.Batching.MainGrid') == undefined) {
            MainLayout = Ext.create('Koltiva.view.Traceability_new.Batching.MainGrid');
        } else {
            //destroy, create ulang
            Ext.getCmp('Koltiva.view.Traceability_new.Batching.MainGrid').destroy();
            MainLayout = Ext.create('Koltiva.view.Traceability_new.Batching.MainGrid');
        }
    }
});
Ext.define('Koltiva.controller.Traceability_new.Delivery', {
    extend: 'Ext.app.Controller',
    init: function () {
        this.renderView();
    },
    renderView: function () {
        var MainLayout = [];

        if (Ext.getCmp('Koltiva.view.Traceability_new.Delivery.MainGrid') == undefined) {
            MainLayout = Ext.create('Koltiva.view.Traceability_new.Delivery.MainGrid');
        } else {
            //destroy, create ulang
            Ext.getCmp('Koltiva.view.Traceability_new.Delivery.MainGrid').destroy();
            MainLayout = Ext.create('Koltiva.view.Traceability_new.Delivery.MainGrid');
        }
    }
});
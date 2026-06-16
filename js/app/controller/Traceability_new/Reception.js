Ext.define('Koltiva.controller.Traceability_new.Reception', {
    extend: 'Ext.app.Controller',
    init: function () {
        this.renderView();
    },
    renderView: function () {
        var MainLayout = [];

        if (Ext.getCmp('Koltiva.view.Traceability_new.Reception.GridReception') == undefined) {
            MainLayout = Ext.create('Koltiva.view.Traceability_new.Reception.GridReception');
        } else {
            //destroy, create ulang
            Ext.getCmp('Koltiva.view.Traceability_new.Reception.GridReception').destroy();
            MainLayout = Ext.create('Koltiva.view.Traceability_new.Reception.GridReception');
        }
    }
});
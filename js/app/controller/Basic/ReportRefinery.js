Ext.require('Koltiva.view.Basic.ReportRefinery.TreeGrid');
Ext.require('Koltiva.view.Basic.ReportRefinery.Form');
Ext.define('Koltiva.controller.Basic.ReportRefinery', {
    extend: 'Ext.app.Controller',
    init: function() {
        console.log("Partner Mapping initialized...");
        this.renderGrid();
    },
    renderGrid: function() {
        grid = Ext.create('Koltiva.view.Basic.ReportRefinery.TreeGrid');
        // store_parent = Ext.create('Koltiva.store.trainings.parent');
    }
});

Ext.require('Koltiva.view.Basic.MillRefinery.TreeGrid');
Ext.require('Koltiva.view.Basic.MillRefinery.Form');
Ext.define('Koltiva.controller.Basic.MillRefinery', {
    extend: 'Ext.app.Controller',
    init: function() {
        console.log("Partner Mapping initialized...");
        this.renderGrid();
    },
    renderGrid: function() {
        grid = Ext.create('Koltiva.view.Basic.MillRefinery.TreeGrid');
        // store_parent = Ext.create('Koltiva.store.trainings.parent');
    }
});

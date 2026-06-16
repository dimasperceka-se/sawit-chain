Ext.require('Koltiva.view.Basic.PartnerMapping.TreeGrid');
Ext.require('Koltiva.view.Basic.PartnerMapping.Form');
Ext.define('Koltiva.controller.Basic.PartnerMapping', {
    extend: 'Ext.app.Controller',
    init: function() {
        console.log("Partner Mapping initialized...");
        this.renderGrid();
    },
    renderGrid: function() {
        grid = Ext.create('Koltiva.view.Basic.PartnerMapping.TreeGrid');
        // store_parent = Ext.create('Koltiva.store.trainings.parent');
    }
});

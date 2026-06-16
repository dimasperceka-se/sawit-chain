
Ext.require('Koltiva.view.trainings.treegrid');
Ext.require('Koltiva.view.trainings.form');
Ext.define('Koltiva.controller.Trainings', {
    extend: 'Ext.app.Controller',
    init: function() {
        console.log("Trainings initialized...");
        this.renderGrid();
    },
    renderGrid: function() {
        grid = Ext.create('Koltiva.view.trainings.treegrid');
        // store_parent = Ext.create('Koltiva.store.trainings.parent');
    }
});

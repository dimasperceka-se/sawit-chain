var grid,win;
Ext.define('Koltiva.controller.Basic.Kml', {
    extend: 'Ext.app.Controller',
    init: function() {
        this.renderGrid();
    },
    renderGrid: function() {
     	grid = Ext.create('Koltiva.view.Basic.Kml.list');
    }
});

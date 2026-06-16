Ext.define('Koltiva.controller.Register', {
    extend: 'Ext.app.Controller',
    init: function() {
        console.log("DHIS Register initialized...");
        this.renderGrid();
    },
    renderGrid: function() {
      var grid = Ext.create('Koltiva.view.dhis.list');
    }
});

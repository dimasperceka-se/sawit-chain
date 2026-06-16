 
 
Ext.define('Koltiva.controller.application_form.register', {
    extend: 'Ext.app.Controller',
    init: function() {
        //console.log("Application Form initialized...");
        this.renderGrid();
    },
    renderGrid: function() {
        grid = Ext.create('Koltiva.view.application_form.GridMainAppForm'); 
    }
});

Ext.define('Koltiva.controller.SpatialTools.ExportKML', {
    extend: 'Ext.app.Controller',
    init: function() {
        this.renderView();
    },
    renderView: function() {
        if(Ext.getCmp('Koltiva.view.SpatialTools.ExportKML.MainView') !== undefined){
            Ext.getCmp('Koltiva.view.SpatialTools.ExportKML.MainView').destroy();
        }
        var MainView = Ext.create('Koltiva.view.SpatialTools.ExportKML.MainView');
    }
});


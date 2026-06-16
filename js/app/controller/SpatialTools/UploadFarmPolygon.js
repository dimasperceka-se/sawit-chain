 Ext.define('Koltiva.controller.SpatialTools.UploadFarmPolygon', {
    extend: 'Ext.app.Controller',
    init: function() {
        this.renderView();
    },
    renderView: function() {
        if(Ext.getCmp('Koltiva.view.SpatialTools.MainView') !== undefined){
            Ext.getCmp('Koltiva.view.SpatialTools.MainView').destroy();
        }
        var MainView = Ext.create('Koltiva.view.SpatialTools.MainView');
    }
});


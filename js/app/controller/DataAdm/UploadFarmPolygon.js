 Ext.define('Koltiva.controller.DataAdm.UploadFarmPolygon', {
    extend: 'Ext.app.Controller',
    init: function() {
        this.renderView();
    },
    renderView: function() {
        if(Ext.getCmp('Koltiva.view.DataAdm.UploadFarmPolygon.MainView') !== undefined){
            Ext.getCmp('Koltiva.view.DataAdm.UploadFarmPolygon.MainView').destroy();
        }
        var MainView = Ext.create('Koltiva.view.DataAdm.UploadFarmPolygon.MainView');
    }
});


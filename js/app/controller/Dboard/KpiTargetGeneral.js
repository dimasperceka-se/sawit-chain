Ext.define('Koltiva.controller.Dboard.KpiTargetGeneral', {
    extend: 'Ext.app.Controller',
    init: function() {
        this.renderView();
    },
    renderView: function() {
    	var MainLayout = [];

        if(Ext.getCmp('Koltiva.view.Dboard.MainGridKpiTargetGeneral') == undefined){
            MainLayout = Ext.create('Koltiva.view.Dboard.MainGridKpiTargetGeneral');
        }else{
            //destroy, create ulang
            Ext.getCmp('Koltiva.view.Dboard.MainGridKpiTargetGeneral').destroy();
            MainLayout = Ext.create('Koltiva.view.Dboard.MainGridKpiTargetGeneral');
        }
    }
});
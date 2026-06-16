Ext.define('Koltiva.controller.Dboard.KpiTargetSawitTerampil', {
    extend: 'Ext.app.Controller',
    init: function() {
        this.renderView();
    },
    renderView: function() {
    	var MainLayout = [];

        if(Ext.getCmp('Koltiva.view.Dboard.MainGridKpiTargetSawitTerampil') == undefined){
            MainLayout = Ext.create('Koltiva.view.Dboard.MainGridKpiTargetSawitTerampil');
        }else{
            //destroy, create ulang
            Ext.getCmp('Koltiva.view.Dboard.MainGridKpiTargetSawitTerampil').destroy();
            MainLayout = Ext.create('Koltiva.view.Dboard.MainGridKpiTargetSawitTerampil');
        }
    }
});
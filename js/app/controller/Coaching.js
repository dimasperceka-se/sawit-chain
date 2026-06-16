Ext.define('Koltiva.controller.Coaching', {
    extend: 'Ext.app.Controller',
    init: function() {
        this.renderView();
    },
    renderView: function() {
    	var MainLayout = [];

        if(Ext.getCmp('Koltiva.view.Coaching.MainGrid') == undefined){
            MainLayout = Ext.create('Koltiva.view.Coaching.MainGrid');
        }else{
            //destroy, create ulang
            Ext.getCmp('Koltiva.view.Coaching.MainGrid').destroy();
            MainLayout = Ext.create('Koltiva.view.Coaching.MainGrid');
        }
    }
});
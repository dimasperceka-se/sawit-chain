Ext.Loader.setConfig({enabled: true});
Ext.Loader.setPath('js/ext-4.2.0.663', '../ux');
Ext.require([
    'Ext.ux.form.ItemSelector',
	'Ext.ux.InputTextMask'
]); 
Ext.define('Koltiva.controller.Traceability.Dispatch', {
extend: 'Ext.app.Controller',
init: function() {
	this.renderView();
},
renderView: function() {
	if(Ext.getCmp('Koltiva.view.Traceability.Dispatch.GridMainDispatch') == undefined){
		var mainLayout = Ext.create('Koltiva.view.Traceability.Dispatch.GridMainDispatch');
	}else{
		//destroy, create ulang
		Ext.getCmp('Koltiva.view.Traceability.Dispatch.GridMainDispatch').destroy();
		var mainLayout = Ext.create('Koltiva.view.Traceability.Dispatch.GridMainDispatch');
	}
}
}); 
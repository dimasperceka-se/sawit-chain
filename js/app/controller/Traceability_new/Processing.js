Ext.Loader.setConfig({enabled: true});
Ext.Loader.setPath('js/ext-4.2.0.663', '../ux');
Ext.require([
    'Ext.ux.form.ItemSelector',
	'Ext.ux.InputTextMask'
]); 
Ext.define('Koltiva.controller.Traceability_new.Processing', {
extend: 'Ext.app.Controller',
init: function() {
	this.renderView();
},
renderView: function() {
	if(Ext.getCmp('Koltiva.view.Traceability_new.Processing.GridMainProcessing') == undefined){
		var mainLayout = Ext.create('Koltiva.view.Traceability_new.Processing.GridMainProcessing');
	}else{
		//destroy, create ulang
		Ext.getCmp('Koltiva.view.Traceability_new.Processing.GridMainProcessing').destroy();
		var mainLayout = Ext.create('Koltiva.view.Traceability_new.Processing.GridMainProcessing');
	}
}
}); 
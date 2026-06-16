Ext.define('Koltiva.view.Traceability_new.Transaction.MainForm', {
    extend: 'Ext.TabPanel',
    id: 'Koltiva.view.Traceability_new.Transaction.MainForm', 
    flex: 1,
    padding: 5,
    activeTab: 0,
    plain: true,
    margin: '0 0 0 0', 
    initComponent: function () { 
         
        var thisObj = this; 
        var objFormTrans = Ext.create('Koltiva.view.Traceability_new.Transaction.FormTransaction');
        thisObj.objFormTrans = objFormTrans;
		
		var objFormPengiriman = Ext.create('Koltiva.view.Traceability_new.Transaction.FormPengiriman');
        thisObj.objFormPengiriman = objFormPengiriman;
		
		var objFormPenerimaan = Ext.create('Koltiva.view.Traceability_new.Transaction.FormPenerimaan');
        thisObj.objFormPenerimaan = objFormPenerimaan;
		
        thisObj.items = [ 
			{
                xtype: 'form',
                viewVar: false,
                setViewVar: function (value) {
                    this.viewVar = value;
                },
                frame: true,
                collapsible: true,
                margin: '0 0 0 0',
                padding: 5,
                title: lang('Farmer Transaction Form'), 
				id: 'Koltiva.view.Traceability_new.Transaction.Form-panelTransaction',
                items: [thisObj.objFormTrans]
            },
			{
                xtype: 'form',
                viewVar: false,
                setViewVar: function (value) {
                    this.viewVar = value;
                },
                frame: true,
                collapsible: true,
                margin: '0 0 0 0',
                padding: 5,
                hidden : false,
                disabled : true,
                title: lang('Batch Received Form'),
				id: 'Koltiva.view.Traceability_new.Transaction.Form-panelPenerimaan',
                items: [thisObj.objFormPenerimaan]
			},
            
			{
                xtype: 'form',
                viewVar: false,
                setViewVar: function (value) {
                    this.viewVar = value;
                },
                frame: true,
                collapsible: true,
                margin: '0 0 0 0',
                padding: 5,
                disabled : true,
                hidden : false,
                title: lang('Delivery Form'),
				id: 'Koltiva.view.Traceability_new.Transaction.Form-panelPengiriman',
                items: [thisObj.objFormPengiriman]
			}
            
			];
        this.callParent(arguments);
    }
});
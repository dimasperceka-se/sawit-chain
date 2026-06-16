Ext.define('Koltiva.view.Traceability.Reception.MainForm', {
    extend: 'Ext.TabPanel',
    id: 'Koltiva.view.Traceability.Reception.MainForm', 
    flex: 1,
    padding: 5,
    activeTab: 0,
    plain: true,
    margin: '0 0 0 0', 
    initComponent: function () { 	
        thisObj = this;	
		var objFormPenerimaan = Ext.create('Koltiva.view.Traceability.Reception.FormPenerimaan');
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
                hidden : false,
                title: lang('Dispatch Receiving Form'),
				id: 'Koltiva.view.Traceability.Reception.Form-panelPenerimaan',
                items: [thisObj.objFormPenerimaan]
			}            
        ];
        this.callParent(arguments);
    }
});
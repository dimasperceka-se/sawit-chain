 

Ext.define('Koltiva.view.Traceability.Reception.MainGrid' ,{
    extend: 'Ext.panel.Panel',
    id: 'Koltiva.view.Traceability.Reception.MainGrid',
    style:'padding:0 15px 15px 15px;margin:12px 0 0 0;',
    viewVar: false,
    setViewVar: function(value){
        this.viewVar = value;
    },
    renderTo: 'ext-content',
    initComponent: function() {
         
        var thisObj = this;
		var ReceptionList = Ext.create('Koltiva.view.Traceability.Reception.ReceptionList');
  
        var objPanelTransactionForm = Ext.create('Koltiva.view.Traceability.Reception.MainForm');
        thisObj.objPanelTransactionForm = objPanelTransactionForm;
	 
        //items
        thisObj.items = [
            {
                layout: 'column',
                border: false,
                items: [{
                    columnWidth: 0.5,
                    layout: 'form',
                    items:[{
                        xtype: 'tabpanel',
                        flex: 1,
                        padding: 5,
                        activeTab: 0,
                        plain: true,
                        id: 'sectionTab',
                        listeners: {
                        'tabchange': function(tabPanel, tab) {
                            var activeTab = tabPanel.getActiveTab();  
                            Ext.getCmp('Koltiva.view.Traceability.Reception.MainForm').setActiveTab(0);
                        },
                        'beforeshow' : function(v){
                            //alert('beforeshow');
                        }
                        },
                        items: [
                            {
                                xtype: 'panel',
                                viewVar: false,
                                setViewVar: function (value) {
                                    this.viewVar = value;
                                },
                                frame: true, 
                                collapsible: false,
                                margin: '0 0 0 0',
                                padding: 5,
                                //hidden : true,
                                title: lang('Dispatch'),
                                id: 'Koltiva.view.Traceability.Reception.MainGrid-ReceptionPanel',
                                items: [ReceptionList]
                            }                    
                        ]
                    }]
                }, 
                {
                    columnWidth: 0.5,
                    layout: 'form',
                    items:[thisObj.objPanelTransactionForm]
                }
            ]    
        }];

        this.callParent(arguments);
    }, 
    listeners: {
        afterrender: function(c){
            Ext.getCmp('sectionTab').getComponent('Koltiva.view.Traceability.Reception.MainGrid-ReceptionPanel').tab.show();
        }    
    },
});
 

Ext.define('Koltiva.view.Traceability_new.Transaction.MainGrid' ,{
    extend: 'Ext.panel.Panel',
    id: 'Koltiva.view.Traceability_new.Transaction.MainGrid',
    style:'padding:0 15px 15px 15px;margin:12px 0 0 0;',
    viewVar: false,
    setViewVar: function(value){
        this.viewVar = value;
    },
    renderTo: 'ext-content',
    initComponent: function() {
         
        var thisObj = this;
		var Grid_ListTransaction = Ext.create('Koltiva.view.Traceability_new.Transaction.List_transaction'); 
		var Grid_ListPenerimaan = Ext.create('Koltiva.view.Traceability_new.Transaction.List_penerimaan');
		var Grid_List_pengiriman = Ext.create('Koltiva.view.Traceability_new.Transaction.List_pengiriman');
  
        var objPanelTransactionForm = Ext.create('Koltiva.view.Traceability_new.Transaction.MainForm');
        thisObj.objPanelTransactionForm = objPanelTransactionForm;
	 
        //items
        thisObj.items = [{
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
							// if(tab.title == lang('List Transaksi Petani'))
							// { 
							// 	Ext.getCmp('Koltiva.view.Traceability_new.Transaction.MainForm').setActiveTab(0);
							// 	Ext.getCmp('Koltiva.view.Traceability_new.Transaction.MainForm').setActiveTab(0);
							// }
							// if(tab.title == lang('List Form Batch / Penerimaan'))
							// {
							// 	Ext.getCmp('Koltiva.view.Traceability_new.Transaction.MainForm').setActiveTab(1);  
							// }
							// if(tab.title == lang('List Pengiriman'))
							// {
							// 	Ext.getCmp('Koltiva.view.Traceability_new.Transaction.MainForm').setActiveTab(2);
                            // }
                            
							if(tab.title == lang('Farmers Transactions'))
							{ 
                                
                                Ext.getCmp('Koltiva.view.Traceability_new.Transaction.Form-panelTransaction').setDisabled(false);
                                Ext.getCmp('Koltiva.view.Traceability_new.Transaction.Form-panelPenerimaan').setDisabled(true);
                                Ext.getCmp('Koltiva.view.Traceability_new.Transaction.Form-panelPengiriman').setDisabled(true);
                                Ext.getCmp('Koltiva.view.Traceability_new.Transaction.MainForm').setActiveTab(0);
							}
							if(tab.title == lang('Batches'))
							{
                
                                Ext.getCmp('Koltiva.view.Traceability_new.Transaction.Form-panelTransaction').setDisabled(true);
                                Ext.getCmp('Koltiva.view.Traceability_new.Transaction.Form-panelPenerimaan').setDisabled(false);
                                Ext.getCmp('Koltiva.view.Traceability_new.Transaction.Form-panelPengiriman').setDisabled(true);
                                Ext.getCmp('Koltiva.view.Traceability_new.Transaction.MainForm').setActiveTab(1);
							}
							if(tab.title == lang('Deliveries'))
							{
                                
                                Ext.getCmp('Koltiva.view.Traceability_new.Transaction.Form-panelTransaction').setDisabled(true);
                                Ext.getCmp('Koltiva.view.Traceability_new.Transaction.Form-panelPenerimaan').setDisabled(true);
                                Ext.getCmp('Koltiva.view.Traceability_new.Transaction.Form-panelPengiriman').setDisabled(false);
                                Ext.getCmp('Koltiva.view.Traceability_new.Transaction.MainForm').setActiveTab(2);
							}
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
                        title: lang('Farmers Transactions'),
						id: 'Koltiva.view.Traceability_new.Transaction.MainGrid-panelTransaction',
                        items: [Grid_ListTransaction]
					},
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
                        title: lang('Batches'),
                        id: 'Koltiva.view.Traceability_new.Transaction.MainGrid-panelPenerimaan',
                        items: [Grid_ListPenerimaan]
                    },
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
                        title: lang('Deliveries'),
                        id: 'Koltiva.view.Traceability_new.Transaction.MainGrid-panelPengiriman',
                        items: [Grid_List_pengiriman]
						
					}
                    
					]
                }]
            }, 
            {
                columnWidth: 0.5,
                layout: 'form',
                items:[thisObj.objPanelTransactionForm]
            }]    
        }];

        this.callParent(arguments);
    }, 
    listeners: {
        afterrender: function(c){
            Ext.Ajax.request({
                url: m_api + '/web-traceability/check-role-transaction',
                method: 'GET',
                success: function(response, opts) {
                    var result = response.responseText;
                        result = JSON.parse(result);

                    if(result.data.TypeMill){
                        Ext.getCmp('sectionTab').getComponent('Koltiva.view.Traceability_new.Transaction.MainGrid-panelTransaction').tab.hide();
                        Ext.getCmp('Koltiva.view.Traceability_new.Transaction.MainForm').getComponent('Koltiva.view.Traceability_new.Transaction.Form-panelTransaction').tab.hide();
                        Ext.getCmp('sectionTab').setActiveTab(0);
                    }

                    if(result.data.AllTab){
                        Ext.getCmp('sectionTab').getComponent('Koltiva.view.Traceability_new.Transaction.MainGrid-panelTransaction').tab.show();
                        Ext.getCmp('sectionTab').getComponent('Koltiva.view.Traceability_new.Transaction.MainGrid-panelPenerimaan').tab.show();
                        Ext.getCmp('sectionTab').getComponent('Koltiva.view.Traceability_new.Transaction.MainGrid-panelPengiriman').tab.show();
                        
                        Ext.getCmp('Koltiva.view.Traceability_new.Transaction.MainForm').getComponent('Koltiva.view.Traceability_new.Transaction.Form-panelTransaction').tab.show();
                        Ext.getCmp('Koltiva.view.Traceability_new.Transaction.MainForm').getComponent('Koltiva.view.Traceability_new.Transaction.Form-panelPenerimaan').tab.show();
                        Ext.getCmp('Koltiva.view.Traceability_new.Transaction.MainForm').getComponent('Koltiva.view.Traceability_new.Transaction.Form-panelPengiriman').tab.show();
                    }else{
                        Ext.getCmp('sectionTab').getComponent('Koltiva.view.Traceability_new.Transaction.MainGrid-panelTransaction').tab.hide();
                        Ext.getCmp('sectionTab').getComponent('Koltiva.view.Traceability_new.Transaction.MainGrid-panelPenerimaan').tab.hide();
                        Ext.getCmp('sectionTab').getComponent('Koltiva.view.Traceability_new.Transaction.MainGrid-panelPengiriman').tab.hide();
                        
                        Ext.getCmp('Koltiva.view.Traceability_new.Transaction.MainForm').getComponent('Koltiva.view.Traceability_new.Transaction.Form-panelTransaction').tab.hide();
                        Ext.getCmp('Koltiva.view.Traceability_new.Transaction.MainForm').getComponent('Koltiva.view.Traceability_new.Transaction.Form-panelPenerimaan').tab.hide();
                        Ext.getCmp('Koltiva.view.Traceability_new.Transaction.MainForm').getComponent('Koltiva.view.Traceability_new.Transaction.Form-panelPengiriman').tab.hide();
                        
                        if(result.data.Transaction){
                            Ext.getCmp('sectionTab').getComponent('Koltiva.view.Traceability_new.Transaction.MainGrid-panelTransaction').tab.show();
                            Ext.getCmp('Koltiva.view.Traceability_new.Transaction.MainForm').getComponent('Koltiva.view.Traceability_new.Transaction.Form-panelTransaction').tab.show();
                            Ext.getCmp('sectionTab').setActiveTab(0);
                        }
                        if(result.data.Batch){
                            Ext.getCmp('sectionTab').getComponent('Koltiva.view.Traceability_new.Transaction.MainGrid-panelPenerimaan').tab.show();
                            Ext.getCmp('Koltiva.view.Traceability_new.Transaction.MainForm').getComponent('Koltiva.view.Traceability_new.Transaction.Form-panelPenerimaan').tab.show();
                            Ext.getCmp('sectionTab').setActiveTab(1);
                        }
                        if(result.data.Sent){
                            Ext.getCmp('sectionTab').getComponent('Koltiva.view.Traceability_new.Transaction.MainGrid-panelPengiriman').tab.show();
                            Ext.getCmp('Koltiva.view.Traceability_new.Transaction.MainForm').getComponent('Koltiva.view.Traceability_new.Transaction.Form-panelPengiriman').tab.show();
                            Ext.getCmp('sectionTab').setActiveTab(2);
                        }
                    }
                }
            });
        }    
    },
});
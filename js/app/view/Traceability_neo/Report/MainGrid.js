Ext.define('Koltiva.view.Traceability_neo.Report.MainGrid' ,{
    extend: 'Ext.panel.Panel',
    id: 'Koltiva.view.Traceability_neo.Report.MainGrid',
    style:'padding:0 15px 15px 15px;margin:12px 0 0 0;',
    viewVar: false,
    setViewVar: function(value){
        this.viewVar = value;
    },
    renderTo: 'ext-content',
    initComponent: function() {
         
        var thisObj = this;
		var Grid_ListPenerimaan = Ext.create('Koltiva.view.Traceability_neo.Report.List_penjualan');
		var Grid_ListPembelian = Ext.create('Koltiva.view.Traceability_neo.Report.List_pembelian');
  
        var objPanelTransactionForm = Ext.create('Koltiva.view.Traceability_new.Transaction.MainForm');
        thisObj.objPanelTransactionForm = objPanelTransactionForm;
	 
        //items
        thisObj.items = [{
            layout: 'column',
            border: false,
            items: [{
                columnWidth: 1.00,
                layout: 'form',
                items:[{
                    xtype: 'tabpanel',
                    flex: 1,
                    padding: 5,
                    activeTab: 0,
                    plain: true,
                    id: 'sectionTab',
                    items: [
					{
                        xtype: 'panel',
                        viewVar: false,
                        setViewVar: function (value) {
                            this.viewVar = value;
                        },
                        // frame: true, 
                        collapsible: false,
                        margin: '0 0 0 0',
                        padding: 5,
                        //hidden : true,
                        title: lang('Sales'),
                        id: 'Koltiva.view.Traceability_neo.Report.MainGrid.MainGrid-panelPenerimaan',
                        items: [Grid_ListPenerimaan]
                    },
					{
                        xtype: 'panel',
                        viewVar: false,
                        setViewVar: function (value) {
                            this.viewVar = value;
                     },
                        // frame: true, 
                        collapsible: false,
                        margin: '0 0 0 0',
                        padding: 5,
                        //hidden : true,
                        title: lang('Purchase'),
                        id: 'Koltiva.view.Traceability_neo.Report.MainGrid.MainGrid-panelPengiriman',
                        items: [Grid_ListPembelian]
						
					}]
                }]
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
                        Ext.getCmp('sectionTab').getComponent('Koltiva.view.Traceability_neo.Report.MainGrid.MainGrid-panelTransaction').tab.hide();
                        Ext.getCmp('Koltiva.view.Traceability_neo.Report.MainGrid.MainForm').getComponent('Koltiva.view.Traceability_neo.Report.MainGrid.Form-panelTransaction').tab.hide();
                        Ext.getCmp('sectionTab').setActiveTab(0);
                    }

                    if(result.data.AllTab){
                        Ext.getCmp('sectionTab').getComponent('Koltiva.view.Traceability_neo.Report.MainGrid.MainGrid-panelTransaction').tab.show();
                        Ext.getCmp('sectionTab').getComponent('Koltiva.view.Traceability_neo.Report.MainGrid.MainGrid-panelPenerimaan').tab.show();
                        Ext.getCmp('sectionTab').getComponent('Koltiva.view.Traceability_neo.Report.MainGrid.MainGrid-panelPengiriman').tab.show();
                        
                        Ext.getCmp('Koltiva.view.Traceability_neo.Report.MainGrid.MainForm').getComponent('Koltiva.view.Traceability_neo.Report.MainGrid.Form-panelTransaction').tab.show();
                        Ext.getCmp('Koltiva.view.Traceability_neo.Report.MainGrid.MainForm').getComponent('Koltiva.view.Traceability_neo.Report.MainGrid.Form-panelPenerimaan').tab.show();
                        Ext.getCmp('Koltiva.view.Traceability_neo.Report.MainGrid.MainForm').getComponent('Koltiva.view.Traceability_neo.Report.MainGrid.Form-panelPengiriman').tab.show();
                    }else{
                        Ext.getCmp('sectionTab').getComponent('Koltiva.view.Traceability_neo.Report.MainGrid.MainGrid-panelTransaction').tab.hide();
                        Ext.getCmp('sectionTab').getComponent('Koltiva.view.Traceability_neo.Report.MainGrid.MainGrid-panelPenerimaan').tab.hide();
                        Ext.getCmp('sectionTab').getComponent('Koltiva.view.Traceability_neo.Report.MainGrid.MainGrid-panelPengiriman').tab.hide();
                        
                        Ext.getCmp('Koltiva.view.Traceability_neo.Report.MainGrid.MainForm').getComponent('Koltiva.view.Traceability_neo.Report.MainGrid.Form-panelTransaction').tab.hide();
                        Ext.getCmp('Koltiva.view.Traceability_neo.Report.MainGrid.MainForm').getComponent('Koltiva.view.Traceability_neo.Report.MainGrid.Form-panelPenerimaan').tab.hide();
                        Ext.getCmp('Koltiva.view.Traceability_neo.Report.MainGrid.MainForm').getComponent('Koltiva.view.Traceability_neo.Report.MainGrid.Form-panelPengiriman').tab.hide();
                        
                        if(result.data.Transaction){
                            Ext.getCmp('sectionTab').getComponent('Koltiva.view.Traceability_neo.Report.MainGrid.MainGrid-panelTransaction').tab.show();
                            Ext.getCmp('Koltiva.view.Traceability_neo.Report.MainGrid.MainForm').getComponent('Koltiva.view.Traceability_neo.Report.MainGrid.Form-panelTransaction').tab.show();
                            Ext.getCmp('sectionTab').setActiveTab(0);
                        }
                        if(result.data.Batch){
                            Ext.getCmp('sectionTab').getComponent('Koltiva.view.Traceability_neo.Report.MainGrid.MainGrid-panelPenerimaan').tab.show();
                            Ext.getCmp('Koltiva.view.Traceability_neo.Report.MainGrid.MainForm').getComponent('Koltiva.view.Traceability_neo.Report.MainGrid.Form-panelPenerimaan').tab.show();
                            Ext.getCmp('sectionTab').setActiveTab(1);
                        }
                        if(result.data.Sent){
                            Ext.getCmp('sectionTab').getComponent('Koltiva.view.Traceability_neo.Report.MainGrid.MainGrid-panelPengiriman').tab.show();
                            Ext.getCmp('Koltiva.view.Traceability_neo.Report.MainGrid.MainForm').getComponent('Koltiva.view.Traceability_neo.Report.MainGrid.Form-panelPengiriman').tab.show();
                            Ext.getCmp('sectionTab').setActiveTab(2);
                        }
                    }
                }
            });
        }    
    },
});
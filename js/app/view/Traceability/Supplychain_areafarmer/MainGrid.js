


Ext.define('Koltiva.view.Traceability.Supplychain_areafarmer.MainGrid' ,{
    extend: 'Ext.panel.Panel',
    id: 'Koltiva.view.Traceability.Supplychain_areafarmer.MainGrid',  
    initComponent: function() {
        var thisObj = this;
        //store   
		var storeGridMainAreaDistrict = Ext.create('Koltiva.store.Traceability.Reference.Supplychain_areafarmer.MainGrid');
        //items
         
		var contextMenuAreaFarmGrid = Ext.create('Ext.menu.Menu',{
			cls:'Sfr_ConMenu',
            items:[  
			{
	            icon: varjs.config.base_url + 'images/icons/new/delete.png',
                text: lang('Delete'),
                cls:'Sfr_BtnConMenuWhite', 
	            handler: function(){
					     var sm = Ext.getCmp('Koltiva.view.Traceability.Supplychain_areafarmer.MainGrid-gridMainGrid').getSelectionModel().getSelection()[0];
						 Ext.Ajax.request({
								waitMsg: lang('Please Wait'),
								url:  m_api + '/traceability/Supplychain_areafarmer/del',
								method : 'POST',
								params: {
								   SupplychainFarmerID: sm.get('SupplychainFarmerID')
								},
								success: function(response, opts){
								   var obj = Ext.decode(response.responseText);  
								   Ext.getCmp('Koltiva.view.Traceability.Supplychain_areafarmer.MainGrid-gridMainGrid').getStore().load();
								}
						 });								
				}
			}]
        });
        
        thisObj.items = [{
            xtype: 'grid',
            id: 'Koltiva.view.Traceability.Supplychain_areafarmer.MainGrid-gridMainGrid',
            style: 'border:1px solid #CCC;margin-top:4px;',
            loadMask: true,
            selType: 'rowmodel',
            store: storeGridMainAreaDistrict,
            width: '100%',
            minHeight:400,
            viewConfig: {
                deferEmptyText: false,
                emptyText: lang('No data Available'),
            }, 
            dockedItems: [{
                xtype: 'pagingtoolbar',
                id: 'Koltiva.view.Traceability.Supplychain_areafarmer.MainGrid-gridToolbar',
                store: storeGridMainAreaDistrict,
                dock: 'bottom',
                displayInfo: true
            },{
                xtype: 'toolbar',
                dock:'top',
                items: [{
                    icon: varjs.config.base_url + 'images/icons/new/add.png',
                    text: lang('Add'),
                    scope: this,
                    cls:'Sfr_BtnGridGreen',
					overCls:'Sfr_BtnGridGreen-Hover',
					id :'Koltiva.view.Traceability.Supplychain_areafarmer.MainGrid-gridMainGrid-Btn',
                    handler: function () { 
						
						 var WinListFarmer = Ext.create('Koltiva.view.Traceability.Supplychain_areafarmer.WinpilihanFarmer',{
							viewVar: {
								//opsiDisplay: 'insert', 
							}
						 }); 
						 
						 if (!WinListFarmer.isVisible()) {
							 WinListFarmer.center();
							 WinListFarmer.show();
							 Ext.getCmp('Koltiva.view.Traceability.Supplychain_areafarmer.WinpilihanFarmer-grid').getStore().load();
						 } else {
							 WinListFarmer.close();
						 } 
                    }
                }]
            }],
            columns: [{
				text: lang('Action'),	
				xtype:'actioncolumn',
				width:'5%',
				items:[{
					icon: varjs.config.base_url + 'images/icons/new/action.png',
					handler: function(grid, rowIndex, colIndex, item, e, record) {
						if(Ext.getCmp('setVarParameters').getValue() != 'view'){
							contextMenuAreaFarmGrid.showAt(e.getXY());
						}
					 
					}
				}]
			},{
                text: lang('Farmer ID'),
                dataIndex: 'MemberDisplayID',
                width:'10%' 
            },
			{
                text: lang('Farmer Name'),
                dataIndex: 'MemberName',
                width:'15%' 
            },
			{
                text: lang('Village'),
                dataIndex: 'Desa',
                 flex:1, 
            },
			{
                text: lang('SubDistrict'),
                dataIndex: 'Kecamatan',
                flex:1,
            },
			{
                text: lang('District'),
                dataIndex: 'District',
                 flex:1,
            },
			{
                text: lang('Start Date'),
                dataIndex: 'DateStart',
                flex:1,
				format:'Y-m-d' 
            },
			{
                text: lang('End Date'),
                dataIndex: 'DateEnd',
                flex:1,
                format:'Y-m-d' 
            }],
            listeners: { 
				 
            }
        }];

        this.callParent(arguments);
    }
});
 

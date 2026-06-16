
/*
	Dipakai Di Form Pengiriman
*/

 
Ext.define('Koltiva.view.Traceability_new.Transaction.Grid_transact_pengiriman' ,{
    extend: 'Ext.panel.Panel',
    id: 'Koltiva.view.Traceability_new.Transaction.Grid_transact_pengiriman', 
    viewVar: false,
    setViewVar: function(value){
        this.viewVar = value;
    },
    frame: false, 
    collapsible:false, 
    margin:'0 0 0 0',
    initComponent: function() {
        var thisObj = this;
		
        var MainGridTransactionPengiriman = Ext.create('Koltiva.store.Traceability_new.Transaction.MainGridTransactionPengiriman');
		var contextMenuTransactionGridPeng = Ext.create('Ext.menu.Menu',{
            items:[ {
                icon: varjs.config.base_url + 'images/icons/new/delete.png',
                text: lang('Delete'),
                itemId: 'Koltiva.view.Traceability_new.Transaction.MainGridTransactionPengiriman-contextMenuDeleteItem', 
                handler: function() {
                    var sm = Ext.getCmp('Koltiva.view.Traceability_new.Transaction.Grid_transact_pengiriman-Grid').getSelectionModel().getSelection()[0]; 
				 
					Ext.Ajax.request({
							url: m_api + '/web-traceability/delete-transaction/',
							method: 'POST',
							waitMsg: lang('Sending data...'),
							params: { 
								STID: sm.get('SupplyTransID'),
								SBID: sm.get('SupplyBatchID'), 
							},
							success: function(response, opts) {
								var obj = Ext.decode(response.responseText);
								switch (obj.success) {
									case true:   
									Ext.getCmp('Koltiva.view.Traceability_new.Transaction.Grid_transact_pengiriman-Grid').store.reload({ params : { SBID :sm.get('SupplyBatchID')} });  
										break;
									default:
										Ext.MessageBox.alert('Warning', obj.message);
										break;
								}
							}
						});
                }
            }]
        });

		thisObj.items = [{
                            xtype: 'grid',
                            id: 'Koltiva.view.Traceability_new.Transaction.Grid_transact_pengiriman-Grid',
                            style: 'border:1px solid #CCC;margin-top:4px;',
                            loadMask: true,
                            selType: 'rowmodel',
                            store: MainGridTransactionPengiriman,
                            viewConfig: {
                                deferEmptyText: false,
                                emptyText: lang('No data Available')
                            },
                            dockedItems: [ 
							{
                                xtype: 'toolbar',
                                dock:'bottom',
                                items:[
									{
									html:'<div id="labelGross" style="width:130px; text-align:left;"> Total Gross : 0</div>'
									},{
									html:'<div id="labelNett" style="width:130px; text-align:left;"> Total Nett : 0</div>'
									},
									{
									html:'<div id="labelPackage" style="width:160px; text-align:left;"> Total Package : 0</div>'
									}]
							},
							{
                                xtype: 'toolbar',
                                dock:'top',
                                items: [{
                                    xtype: 'button',
                                    id: 'Koltiva.view.Traceability_new.Transaction.Grid_transact_pengiriman-Grid-gridToolbar-BtnAdd',
                                    icon: varjs.config.base_url + 'images/icons/new/add.png', cls:'Sfr_BtnGridGreen', overCls:'Sfr_BtnGridGreen-Hover',
                                    margin: '0px 10px 0px 6px',
                                    text: lang('Add'),
									hidden : true,
                                    handler: function() { 
											var WinTransactionPengiriman = Ext.create('Koltiva.view.Traceability_new.Transaction.window.WinTransactionPengiriman',{
												viewVar: {
													//opsiDisplay: 'insert', 
												}
											}); 
											
											if (!WinTransactionPengiriman.isVisible()) {
												WinTransactionPengiriman.center();
												WinTransactionPengiriman.show();
											} else {
												WinTransactionPengiriman.close();
											} 
                                    }
                                },{
                                    name: 'SupplyKey',
                                    id: 'Koltiva.view.Traceability_new.Transaction.Grid_transact_pengiriman-Grid-gridToolbar-SupplyKey',
                                    xtype: 'textfield',
                                    width: 150,
                                    emptyText: lang('Search by Supplier Name')
                                },{
                                    xtype: 'button',
                                    id: 'Koltiva.view.Traceability_new.Transaction.Grid_transact_pengiriman-Grid-gridToolbar-BtnSearch',
                                    icon: varjs.config.base_url + 'images/icons/silk/search.png',
                                    margin: '0px 10px 0px 6px',
                                    text: lang('Search'),
                                    handler: function() {
										var SupplyKey = Ext.getCmp('Koltiva.view.Traceability_new.Transaction.Grid_transact_pengiriman-Grid-gridToolbar-SupplyKey').getValue(); 
										var SBID = Ext.getCmp('Koltiva.view.Traceability_new.Transaction.FormPengiriman-Form-SBID').getValue();
                                        MainGridTransactionPengiriman.load({ params : { 'SBID' : SBID , 'SupplyKey' : SupplyKey } })
                                    }
                                }]
                            }],
                            columns: [{
                                text: lang('Action'),
                                xtype:'actioncolumn',
								id:'Koltiva.view.Traceability_new.Transaction.MainGridTransactionPengiriman-actioncolumn',
                                width:'10%',
                                items:[{
                                    icon: varjs.config.base_url + 'images/icons/new/action.png', 
                                    handler: function(grid, rowIndex, colIndex, item, e, record) {
                                        contextMenuTransactionGridPeng.showAt(e.getXY());
                                        var sm = record;
										 
                                    }
                                }]
                            },{
                                text: 'ID',
                                dataIndex: 'SupplyTransID',
                                hidden: true
                            },{
                                text: 'ID',
                                dataIndex: 'SupplyID',
                                hidden: true
                            },{
                                text: lang('Supply Type'),
                                dataIndex: 'SupplyType',
                                width:100,
                            },{
                                text: lang('Supplier ID'),
                                dataIndex: 'SupplyID',
                                width:100,
                            },{
                                text: lang('Supplier Name'),
                                dataIndex: 'SupplierName',
                                width:200,
                            },{
                                text: lang('Certified'),
                                dataIndex: 'Certified',
                                width:200,
                            },{
                                text: lang('Date'),
                                dataIndex: 'DateTransaction',
                                renderer: Ext.util.Format.dateRenderer('d-m-Y'),
                                width:100,
                            },{
                                text: lang('Gross'),
                                dataIndex: 'VolumeBruto',
                                width:90
                            },{
                                text: lang('Package'),
                                dataIndex: 'PackageNumber',
                                width:90
                            }, {
                                text: lang('Netto'),
                                dataIndex: 'VolumeNetto',
                                width:150
                            }, {
                                text: lang('Registered Agent'),
                                dataIndex: 'AgentOther',
                                width:150
                            }, {
                                text: lang('Survey Agent'),
                                dataIndex: 'AgentOtherSurvey',
                                width:150
                            }] 
				}];
				this.callParent(arguments);
    }
});

 
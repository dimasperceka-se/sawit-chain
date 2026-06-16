 
 
Ext.define('Koltiva.view.Traceability_new.Supplychain_quality.MainGrid' ,{
    extend: 'Ext.panel.Panel',
    id: 'Koltiva.view.Traceability_new.Supplychain_quality.MainGrid',  
    initComponent: function() {
        var thisObj = this;
        //store
        var storeGridMain = Ext.create('Koltiva.store.Traceability_new.Reference.Supplychain_quality.MainGrid');
        var cmb_status = Ext.create('Ext.data.Store', {
            fields: ['id', 'label'],
            data: [{
                "id": "active",
                "label": lang("Active")
            }, {
                "id": "inactive",
                "label": lang("Inactive")
            }, {
                "id": "nullified",
                "label": lang("Nullified")
            }]
        });

        var cmb_type = Ext.create('Ext.data.Store', {
            fields: ['id', 'label'],
            data: [{
                "id": "combo",
                "label": lang("Combo")
            }, {
                "id": "text",
                "label": lang("Text")
            }]
        });
 
        //items
        var RowEditingQuality = Ext.create('Ext.grid.plugin.RowEditing', {
            id : 'RowEditingQualityId',
            clicksToMoveEditor: 0,
            autoCancel: false,
            errorSummary: false,
            clicksToEdit: 2
        });
		
		var contextMenuQualityGrid = Ext.create('Ext.menu.Menu',{
			cls:'Sfr_ConMenu',
            items:[{
                icon: varjs.config.base_url + 'images/icons/new/view.png',
                text: lang('Set Quality Value'),
                itemId: 'Koltiva.view.Traceability_new.Transaction.List_transaction-contextMenuViewItem',
                handler: function() {
                     var sm = Ext.getCmp('Koltiva.view.Traceability_new.Supplychain_quality.MainGrid-gridMainGrid').getSelectionModel().getSelection()[0];
                     SetWindowQualityValue(sm.get('QualityID'));  
                }
            },{
                icon: varjs.config.base_url + 'images/icons/new/update.png',
                text: lang('Update'),
                itemId: 'Koltiva.view.Traceability_new.Transaction.List_transaction-contextMenuUpdateItem',
                //hidden: m_act_update,
                handler: function() {
						RowEditingQuality.cancelEdit();
                        var sm = Ext.getCmp('Koltiva.view.Traceability_new.Supplychain_quality.MainGrid-gridMainGrid').getSelectionModel().getSelection();
                        if (sm[0]) {
                            RowEditingQuality.startEdit(sm[0].index, 0);
                        }else{
                            Ext.MessageBox.alert('Warning', lang('Please select data on the grid !'));
                        }
                }
            },
			{
	           icon: varjs.config.base_url + 'images/icons/new/delete.png',
                text: lang('Delete'),
                cls:'Sfr_BtnConMenuWhite', 
	            handler: function(){
						var smb = Ext.getCmp('Koltiva.view.Traceability_new.Supplychain_quality.MainGrid-gridMainGrid').getSelectionModel().getSelection()[0];
                        RowEditingQuality.cancelEdit();

                        if (smb) {
                            Ext.MessageBox.confirm('Message', lang('Are you sure will delete this data ?'), function (btn) {
                                if (btn == 'yes') {
                                    Ext.Ajax.request({
                                        waitMsg: lang('Please Wait'),
                                        url: m_api + '/traceability_api/Supplychain_quality/del/' + smb.raw.QualityID,
                                        method: 'DELETE',
                                        success: function (response, opts) {
                                            var obj = Ext.decode(response.responseText);
                                            switch (obj.success) {
                                                case true:
                                                    Ext.getCmp('Koltiva.view.Traceability_new.Supplychain_quality.MainGrid-gridMainGrid').getStore().load();
                                                    Ext.MessageBox.alert('Warning', lang('Delete success'));
                                                    break;
                                                default:
                                                    Ext.MessageBox.alert('Warning', lang('Delete failed'));
                                                    break;
                                            }
                                        },
                                        failure: function (response, opts) {
                                            var obj = Ext.decode(response.responseText);
                                            Ext.MessageBox.alert('error', lang('Could not connect to the database. Retry later'));
                                        }
                                    });
                                }
                            });
                        }else{
                            Ext.MessageBox.alert('Warning', lang('Please select data on the grid !'));
                        }
				}
			}]
        });
        
        thisObj.items = [{
            xtype: 'grid',
            id: 'Koltiva.view.Traceability_new.Supplychain_quality.MainGrid-gridMainGrid',
            style: 'border:1px solid #CCC;margin-top:4px;',
            loadMask: true,
            selType: 'rowmodel',
            store: storeGridMain,
            height : 400,
            viewConfig: {
                deferEmptyText: false,
                emptyText: lang('No data Available'),
            },
            plugins: [RowEditingQuality],
            dockedItems: [{
                xtype: 'pagingtoolbar',
                id: 'Koltiva.view.Traceability_new.Supplychain_quality.MainGrid-gridToolbar',
                store: storeGridMain,
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
					id:'Koltiva.view.Traceability_new.Supplychain_quality.MainGrid-gridMainGrid-Btn',
                    handler: function () {
                        RowEditingQuality.cancelEdit();
                        var record = Ext.getCmp('Koltiva.view.Traceability_new.Supplychain_quality.MainGrid-gridMainGrid').getStore();

                        record.insert(0, { 
                            'Name' : "", 
                            'Formula' : "", 
                            'Order' : "", 
							'StartDate' :"",
							'EndDate' :"",
                            'Type' : "", 
                            'MinValue' : "", 
                            'MaxValue' : "", 
                            'StandardValue' : "", 
                            'IsPrintVisible' : "" 
                        });
                        RowEditingQuality.startEdit(0, 0);
                    }
                } ]
            }],
            columns: [{ 
				 text: lang('Action'),
				 xtype:'actioncolumn',
				 width:'5%',
					items:[{
						icon: varjs.config.base_url + 'images/icons/new/action.png',
						handler: function(grid, rowIndex, colIndex, item, e, record) {
						  if(Ext.getCmp('setVarParameters').getValue() != 'view'){	
							contextMenuQualityGrid.showAt(e.getXY());
						  }
						 
						}
					}]
			  }, {
                text: lang('Name'),
                dataIndex: 'Name',
                flex: 1,
                editor: {
                    xtype: 'textfield',
                    allowBlank: false
                }
            },{
                text: lang('Formula'),
                dataIndex: 'Formula',
                flex: 1,
                editor: {
                    xtype: 'textfield',
                    allowBlank: true
                }
            },{
                text: lang('Order'),
                dataIndex: 'Order',
                width :'6%',
                editor: {
                    xtype: 'textfield',
                    allowBlank: false
                }
            },
			{
                text: lang('Start Date'),
                dataIndex: 'StartDate',
                width :'10%',
                format:'Y-m-d',
                editor: {
                    xtype: 'datefield',
					format:'Y-m-d',
                    inputValue : 1,
                    allowBlank: true,
                },
            },
			{
                text: lang('End Date'),
                dataIndex: 'EndDate',
                width :'10%',
                format:'Y-m-d',
                editor: {
                    xtype: 'datefield',
					format:'Y-m-d',
                    inputValue : 1,
                    allowBlank: true,
                },
            },
			{
                text: lang('Type'),
                dataIndex: 'Type',
                width :'10%',
                editor: {
                    xtype: 'combobox',
                    store : cmb_type,
                    displayField : 'label',
                    valueField : 'id',
                    allowBlank: false,
                }
            },{
                text: lang('Min Value'),
                dataIndex: 'MinValue',
                width :'10%',
                editor: {
                    xtype: 'textfield',
                    allowBlank: false
                }
            },{
                text: lang('Max Value'),
                dataIndex: 'MaxValue',
                width :'10%',
                editor: {
                    xtype: 'textfield',
                    allowBlank: false
                }
            },{
                text: lang('Std Value'),
                dataIndex: 'StandardValue',
                flex: 1,
                editor: {
                    xtype: 'textfield',
                    allowBlank: true
                }
            },{
                text: lang('Is Print Visible ?'),
                dataIndex: 'IsPrintVisible',
                align : 'center',
                flex: 1,
                editor: {
                    xtype: 'checkbox',
                    inputValue : 1,
                    allowBlank: true,
                },
                renderer : function(value, metaData, record, row, col, store, gridView){
                    return value == "0" ? 'N' : 'Y';
                }
            }],
            listeners: {
                'canceledit': function (editor, e, eOpts) {
                    Ext.getCmp('Koltiva.view.Traceability_new.Supplychain_quality.MainGrid-gridMainGrid').getStore().load();
                },
                'edit': function (editor, e) {
                    var SupplychainID = Ext.getCmp('Koltiva.view.Traceability_new.Reference.Supplychain_org-dataForm-SupplychainID').getValue();
                    var QualityID = e.record.data.QualityID;
                    var Name = e.record.data.Name;
                    var Formula = e.record.data.Formula;
                    var Order = e.record.data.Order;
					var StartDate = e.record.data.StartDate;
					var EndDate = e.record.data.EndDate;
                    var Type = e.record.data.Type;
                    var MinValue = e.record.data.MinValue;
                    var MaxValue = e.record.data.MaxValue;
                    var StandardValue = e.record.data.StandardValue;
                    var IsPrintVisible = e.record.data.IsPrintVisible == true ? 1 : 0;
                    var StatusCode = e.record.data.StatusCode;
					
					if(Ext.getCmp('setVarParameters').getValue() != 'view'){
						Ext.Ajax.request({
							waitMsg: lang('Please wait...'),
							url: m_api + '/reference/supplychain-quality-save',
							method: 'POST',
							params: {
								SupplychainID : SupplychainID,
								QualityID : QualityID,
								Name : Name,
								Formula : Formula,
								Order : Order,
								StartDate : StartDate,
								EndDate : EndDate,
								Type : Type,
								MinValue : MinValue,
								MaxValue : MaxValue,
								StandardValue : StandardValue,
								IsPrintVisible : IsPrintVisible,
								StatusCode : StatusCode
							},
							success: function (response, opts) {
								var obj = Ext.decode(response.responseText);
								var message = QualityID != '' ? 'Update' : 'Insert';
								switch (obj.success) {
									case true:
										Ext.MessageBox.alert('Success', lang(message + ' success'));
										Ext.getCmp('Koltiva.view.Traceability_new.Supplychain_quality.MainGrid-gridMainGrid').getStore().load();
										break;
									default:
										Ext.MessageBox.alert('Warning', lang(obj.message));
										break;
								}
							},
							failure: function (response, opts) {
								var obj = Ext.decode(response.responseText);
								Ext.MessageBox.alert('error', lang('Could not connect to the database. Retry later'));
							}
						});
					}else{
						Ext.MessageBox.alert('Warning', lang('View Mode!'));
					}
                }
            }
        }];

        this.callParent(arguments);
    }
});

var MainGridQualityVal = Ext.create('Koltiva.view.Traceability_new.Supplychain_quality_value.MainGrid'); 
var winQuality = Ext.create('widget.window', {
        title: lang('Quality Value'),
        id:'Koltiva.view.Traceability_new.Reference.Supplychain_org-winQuality',
        closable: true,
        modal:true,
        closeAction: 'hide',
        autoScroll: true,
        width: '80%',
        height: '80%',
        listeners:{
            hide: function(){ 
            }
        },
        layout: {
            type: 'fit'
        },
        items: [  MainGridQualityVal ]
    });
SetWindowQualityValue = function(QualityID)
{
	Ext.getCmp('Koltiva.view.Traceability_new.Supplychain_quality_value.MainGrid-gridMainGrid').getStore().load({params : { 'QualityID' : QualityID } });
	Ext.getCmp('Koltiva.view.Traceability_new.Supplychain_quality_value.MainGrid-QualityID').setValue(QualityID);
	if(!winQuality.isVisible()){
		winQuality.show();
	} else {
		winQuality.show();
	}
}
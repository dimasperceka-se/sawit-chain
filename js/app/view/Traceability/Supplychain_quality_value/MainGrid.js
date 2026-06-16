// Define Variabel2 / Object2 / Fungsi yg diperlukan oleh view ini (end)
Ext.define('Koltiva.view.Traceability.Supplychain_quality_value.MainGrid' ,{
    extend: 'Ext.panel.Panel',
    id: 'Koltiva.view.Traceability.Supplychain_quality_value.MainGrid',  
    initComponent: function() {
        var thisObj = this;
        //store
        var storeGridMain = Ext.create('Koltiva.store.Traceability.Reference.Supplychain_quality_value.MainGrid');
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
        //items
        var RowEditing = Ext.create('Ext.grid.plugin.RowEditing', {
            id : 'roweditingId',
            clicksToMoveEditor: 0,
            autoCancel: false,
            errorSummary: false,
            clicksToEdit: 2
        });
		
		var contextMenuQualityValGrid = Ext.create('Ext.menu.Menu',{
			cls:'Sfr_ConMenu',
            items:[{
                icon: varjs.config.base_url + 'images/icons/new/update.png',
                text: lang('Update'),
                itemId: 'Koltiva.view.Traceability.Transaction.List_transaction-contextMenuUpdateItem',
                //hidden: m_act_update,
                handler: function() {
					RowEditing.cancelEdit();
					var sm = Ext.getCmp('Koltiva.view.Traceability.Supplychain_quality_value.MainGrid-gridMainGrid').getSelectionModel().getSelection();
					if (sm[0]) {
						RowEditing.startEdit(sm[0].index, 0);
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
					var smb = Ext.getCmp('Koltiva.view.Traceability.Supplychain_quality_value.MainGrid-gridMainGrid').getSelectionModel().getSelection()[0];
                        RowEditing.cancelEdit();

                        if (smb) {
                            Ext.MessageBox.confirm('Message', lang('Are you sure will delete this data ?'), function (btn) {
                                if (btn == 'yes') {
                                    Ext.Ajax.request({
                                        waitMsg: lang('Please Wait'),
                                        url: m_api + '/traceability_api/Supplychain_quality_value/del/' + smb.raw.ValueQualityID,
                                        method: 'DELETE',
                                        success: function (response, opts) {
                                            var obj = Ext.decode(response.responseText);
                                            switch (obj.success) {
                                                case true:
                                                    Ext.getCmp('Koltiva.view.Traceability.Supplychain_quality_value.MainGrid-gridMainGrid').getStore().load();
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
        
        thisObj.items = [
			{
			   xtype: 'hidden',
			   id: 'Koltiva.view.Traceability.Supplychain_quality_value.MainGrid-QualityID',  
			},
			{
            xtype: 'grid',
            id: 'Koltiva.view.Traceability.Supplychain_quality_value.MainGrid-gridMainGrid',
            style: 'border:1px solid #CCC;margin-top:4px;',
            loadMask: true,
            selType: 'rowmodel',
            store: storeGridMain,
            height: 455,
            viewConfig: {
                deferEmptyText: false,
                emptyText: lang('No data Available'),
            },
            plugins: [RowEditing],
            dockedItems: [{
                xtype: 'pagingtoolbar',
                id: 'Koltiva.view.Traceability.Supplychain_quality_value.MainGrid-gridToolbar',
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
					id:'Koltiva.view.Traceability.Supplychain_quality_value.MainGrid-gridMainGrid-Btn',
                    handler: function () {
                        RowEditing.cancelEdit();
                        var record = Ext.getCmp('Koltiva.view.Traceability.Supplychain_quality_value.MainGrid-gridMainGrid').getStore();

                        record.insert(0, {
                            'ValueQualityID' : "", 
                            'QualityID' : "", 
                            'Value' : "",
                            "StatusCode" : 'active'
                        });
                        RowEditing.startEdit(0, 0);
                    }
                }]
            }],
            columns: [
			{ 
				 text: lang('Action'),
				 xtype:'actioncolumn',
				 width:'5%',
					items:[{
						icon: varjs.config.base_url + 'images/icons/new/action.png',
						handler: function(grid, rowIndex, colIndex, item, e, record) {
							if(Ext.getCmp('setVarParameters').getValue() != 'view'){ 
							   contextMenuQualityValGrid.showAt(e.getXY());
							}
							 
						}
					}]
			},
			{
                text: lang('Value'),
                dataIndex: 'Value',
                flex: 1,
                editor: {
                    xtype: 'textfield', 
                }
            },{
                text: lang('Is Default ?'),
                dataIndex: 'is_default',
                align : 'center',
                flex: 1,
                editor: {
                    xtype: 'checkbox',
                    inputValue : 1, 
                },
                renderer : function(value, metaData, record, row, col, store, gridView){
                    return value == "0" ? 'N' : 'Y';
                }
            },{
                text: lang('Status'),
                dataIndex: 'StatusCode',
                flex: 1,
                editor: {
                    xtype: 'combobox',
                    store : cmb_status,
                    displayField : 'label',
                    valueField : 'id', 
                }
            },{
                text: lang('Date Updated'),
                dataIndex: 'DateUpdated',
                align:'right',
                flex: 1,
            }],
            listeners: {
                'canceledit': function (editor, e, eOpts) {
                    Ext.getCmp('Koltiva.view.Traceability.Supplychain_quality_value.MainGrid-gridMainGrid').getStore().load();
                },
                'edit': function (editor, e) {
                    var ValueQualityID = e.record.data.ValueQualityID;
                    var QualityID = Ext.getCmp('Koltiva.view.Traceability.Supplychain_quality_value.MainGrid-QualityID').getValue();
                    var Value = e.record.data.Value;
                    var is_default = e.record.data.is_default == true ? 1 : 0;
                    var StatusCode = e.record.data.StatusCode;
					
					if(Ext.getCmp('setVarParameters').getValue() != 'view'){
						Ext.Ajax.request({
							waitMsg: lang('Please wait...'),
							url: m_api + '/reference/supplychain-quality-value-save',
							method: 'POST',
							params: {
								ValueQualityID : ValueQualityID,
								QualityID : QualityID,
								Value : Value,
								is_default : is_default,
								StatusCode : StatusCode
							},
							success: function (response, opts) {
								var obj = Ext.decode(response.responseText);
								var message = ValueQualityID != '' ? 'Update' : 'Insert';
								switch (obj.success) {
									case true:
										Ext.MessageBox.alert('Success', lang(message + ' success'));
										Ext.getCmp('Koltiva.view.Traceability.Supplychain_quality_value.MainGrid-gridMainGrid').getStore().load();
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
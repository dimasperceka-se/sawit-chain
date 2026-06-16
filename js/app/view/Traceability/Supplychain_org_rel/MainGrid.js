

var storeGridMainRel = Ext.create('Koltiva.store.Traceability.Reference.Supplychain_org_rel.MainGrid');
Ext.define('Koltiva.view.Traceability.Supplychain_org_rel.MainGrid' ,{
    extend: 'Ext.panel.Panel',
    id: 'Koltiva.view.Traceability.Supplychain_org_rel.MainGrid',  
    initComponent: function() {
        var thisObj = this;
        //store
        
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

        var  storeSID = Ext.create('Koltiva.store.Traceability.Reference.Supplychain_org_rel.cmbObject', {
            viewVar: {
                tb: 0
            }
        });
 
        //items
        var RowEditing = Ext.create('Ext.grid.plugin.RowEditing', {
            id : 'roweditingId',
            clicksToMoveEditor: 0,
            autoCancel: false,
            errorSummary: false,
            clicksToEdit: 2
        });
		
		var contextMenuOrgRelGrid = Ext.create('Ext.menu.Menu',{
			cls:'Sfr_ConMenu',
            items:[ {
                icon: varjs.config.base_url + 'images/icons/new/update.png',
                text: lang('Update'),
                itemId: 'Koltiva.view.Traceability.Transaction.List_transaction-contextMenuUpdateItem',
                //hidden: m_act_update,
                handler: function() {
						RowEditing.cancelEdit();
                        var sm = Ext.getCmp('Koltiva.view.Traceability.Supplychain_org_rel.MainGrid-gridMainGrid').getSelectionModel().getSelection();
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
						var smb = Ext.getCmp('Koltiva.view.Traceability.Supplychain_org_rel.MainGrid-gridMainGrid').getSelectionModel().getSelection()[0];
                        RowEditing.cancelEdit();

                        if (smb) {
                            Ext.MessageBox.confirm('Message', lang('Are you sure will delete this data ?'), function (btn) {
                                if (btn == 'yes') {
                                    Ext.Ajax.request({
                                        waitMsg: lang('Please Wait'),
                                        url: m_api + '/traceability_api/Supplychain_org_rel/del/' + smb.raw.RelID,
                                        method: 'DELETE',
                                        success: function (response, opts) {
                                            var obj = Ext.decode(response.responseText);
                                            switch (obj.success) {
                                                case true:
                                                    Ext.getCmp('Koltiva.view.Traceability.Supplychain_org_rel.MainGrid-gridMainGrid').getStore().load();
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
            id: 'Koltiva.view.Traceability.Supplychain_org_rel.MainGrid-gridMainGrid',
            style: 'border:1px solid #CCC;margin-top:4px;',
            loadMask: true,
            selType: 'rowmodel',
            store: storeGridMainRel,
            width: '100%',
            minHeight:400,
            viewConfig: {
                deferEmptyText: false,
                emptyText: lang('No data Available'),
            },
            plugins: [RowEditing],
            dockedItems: [{
                xtype: 'pagingtoolbar',
                id: 'Koltiva.view.Traceability.Supplychain_org_rel.MainGrid-gridToolbar',
                store: storeGridMainRel,
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
					id :'Koltiva.view.Traceability.Supplychain_org_rel.MainGrid-gridMainGrid-Btn',
                    handler: function () { 
						
						RowEditing.cancelEdit();
                        var record = Ext.getCmp('Koltiva.view.Traceability.Supplychain_org_rel.MainGrid-gridMainGrid').getStore();

                        record.insert(0, {
                            "Parent" : '',
                            "Child" : '',
							'StartDate' :"",
							'EndDate' :"",
                        });
                        RowEditing.startEdit(0, 0);
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
							contextMenuOrgRelGrid.showAt(e.getXY());
						}
					 
					}
				}]
			},{
                text: lang('Destination'),
                dataIndex: 'Parent',
                flex: 1,
                editor: {
                    xtype: 'combobox',
                    store : storeSID,
                    displayField : 'Name',
                    valueField : 'SupplychainID',
                    allowBlank: false,
                    listeners : {
                        change : function(val){
                           
                        }
                    }
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
            }],
            listeners: {
				 
                'canceledit': function (editor, e, eOpts) {
                    Ext.getCmp('Koltiva.view.Traceability.Supplychain_org_rel.MainGrid-gridMainGrid').getStore().load();
                },
                'edit': function (editor, e) {
                    var ParentID = e.record.data.Parent; 
                    var ChildID = Ext.getCmp('Koltiva.view.Traceability.Reference.Supplychain_org-dataForm-SupplychainID').getValue();
                    var RelID = e.record.data.RelID;
                    var StatusCode = e.record.data.StatusCode;
					var StartDate = e.record.data.StartDate;
					var EndDate = e.record.data.EndDate;
					
					if(Ext.getCmp('setVarParameters').getValue() != 'view'){
						Ext.Ajax.request({
							waitMsg: lang('Please wait...'),
							url: m_api + '/traceability_api/Supplychain_org_rel/submit',
							method: 'POST',
							params: {
								RelID : RelID,
								ParentID : ParentID,
								ChildID : ChildID,
								StartDate : StartDate,
								EndDate : EndDate,
								StatusCode : StatusCode
							},
							success: function (response, opts) {
								var obj = Ext.decode(response.responseText);
								var message = RelID != '' ? 'Update' : 'Insert';
								switch (obj.success) {
									case true:
										Ext.MessageBox.alert('Success', lang(message + ' success'));
										Ext.getCmp('Koltiva.view.Traceability.Supplychain_org_rel.MainGrid-gridMainGrid').getStore().load();
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
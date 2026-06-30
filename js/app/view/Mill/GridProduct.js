Ext.define('Koltiva.view.Mill.GridProduct' ,{
    extend: 'Ext.panel.Panel',
    id: 'Koltiva.view.Mill.GridProduct',  
    initComponent: function() {
        var thisObj = this;
        //store
        var storeGridMainProduct = Ext.create('Koltiva.store.Mill.GridProduct');

        var cmb_status = Ext.create('Ext.data.Store', {
            fields: ['id', 'label'],
            data: [{
                "id": "active",
                "label": lang("Active")
            }, {
                "id": "inactive",
                "label": lang("Inactive")
            }]
        });

        var product = Ext.create('Ext.data.Store', {
            fields: ['id', 'label'],
            data: [{
                "id": "1",
                "label": lang("CPO")
            }, {
                "id": "2",
                "label": lang("PK")
            }, {
                "id": "3",
                "label": lang("CPKO")
            }, {
                "id": "4",
                "label": lang("CPKE")
            }]
        });

        var cmb_storeProduct = Ext.create('Koltiva.store.Traceability_new.Reference.Supplychain_product.cmbProduct'); 
        
        //items
        var RowEditingProduct = Ext.create('Ext.grid.plugin.RowEditing', {
            id : 'RowEditingSupplychainProductID',
            clicksToMoveEditor: 0,
            autoCancel: false,
            errorSummary: false,
            clicksToEdit: 2,
        });
		
		var contextMenuProductGrid = Ext.create('Ext.menu.Menu',{
			cls:'Sfr_ConMenu',
            items:[ {
                icon: varjs.config.base_url + 'images/icons/new/update.png',
                text: lang('Update'),
                itemId: 'Koltiva.view.Traceability_new.Transaction.List_transaction-contextMenuUpdateItem',
                //hidden: m_act_update,
                handler: function() {
						RowEditingProduct.cancelEdit();
                        var sm = Ext.getCmp('Koltiva.view.Mill.GridProduct-gridMainGrid').getSelectionModel().getSelection();
                        if (sm[0]) {
                            RowEditingProduct.startEdit(sm[0].index, 0);
                        }else{
                            Ext.MessageBox.alert('Warning', lang('Please select data on the grid !'));
                        }
                }
            },{
	            icon: varjs.config.base_url + 'images/icons/new/delete.png',
                text: lang('Delete'),
                cls:'Sfr_BtnConMenuWhite', 
	            handler: function(){
						var smb = Ext.getCmp('Koltiva.view.Mill.GridProduct-gridMainGrid').getSelectionModel().getSelection()[0];
                        RowEditingProduct.cancelEdit();

                        if (smb) {
                            Ext.MessageBox.confirm('Message', lang('Are you sure will delete this data ?'), function (btn) {
                                if (btn == 'yes') {
                                    Ext.Ajax.request({
                                        waitMsg: lang('Please Wait'),
                                        url: m_api + '/traceability_api/Supplychain_product/del/' + smb.raw.SupplychainProductID,
                                        method: 'DELETE',
                                        success: function (response, opts) {
                                            var obj = Ext.decode(response.responseText);
                                            switch (obj.success) {
                                                case true:
                                                    Ext.getCmp('Koltiva.view.Mill.GridProduct-gridMainGrid').getStore().load();
                                                    Ext.MessageBox.alert('Success', lang('Delete success'));
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
            id: 'Koltiva.view.Mill.GridProduct-gridMainGrid',
            style: 'border:1px solid #CCC;margin-top:4px;',
            loadMask: true,
            selType: 'rowmodel',
            store: storeGridMainProduct,
            autoWidth : true,
            minHeight : 200,
            viewConfig: {
                deferEmptyText: false,
                emptyText: lang('No data Available'),
            },
            plugins: [RowEditingProduct],
            dockedItems: [{
                xtype: 'pagingtoolbar',
                id: 'Koltiva.view.Mill.GridProduct-gridToolbar',
                store: storeGridMainProduct,
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
                    // disabled:true,
					id :'Koltiva.view.Mill.GridProduct-gridMainGrid-BtnAdd',
                    handler: function () { 
                        
                        if(Ext.getCmp('Koltiva.view.Mill.FormMainMill-FormBasicData-SupplychainID').getValue() == '') {
                            Ext.MessageBox.alert('Warning', lang('Please save form mill data first and setting in traceability menu !'));
                        } else {
                            RowEditingProduct.cancelEdit();
                                    
                            var record = Ext.getCmp('Koltiva.view.Mill.GridProduct-gridMainGrid').getStore();

                            record.insert(0, {
                                "SupplychainID" : '',
                                "ProductID" : '',
                                "ProductPercentage" : '',
                                'StartDate' :"",
                                'EndDate' :"",
                                'StatusCode' :"",
                            });

                            RowEditingProduct.startEdit(0,0);
                        }                       
                    }
                }]
            }],
            columns: [{
				text: lang('Action'),	
				xtype:'actioncolumn',
				width:100,
				items:[{
					icon: varjs.config.base_url + 'images/icons/new/action.png',
					handler: function(grid, rowIndex, colIndex, item, e, record) {
						if(Ext.getCmp('Koltiva.view.Mill.FormMainMill-FormBasicData-SupplychainID').getValue() != ''){
							contextMenuProductGrid.showAt(e.getXY());
						}
					 
					}
				}]
            },{
                text: lang('Product Name'),
                dataIndex: 'ProductName',
                flex:1,
                editor: {
                    xtype: 'combobox',
                    id:'ProductID',
                    store : product,
                    displayField: 'label',
                    valueField: 'id',
                    allowBlank: false,
                }
            },{
                text: lang('Percentage'),
                dataIndex: 'ProductPercentage',
                width:150,
                editor:{
                    xtype: 'textfield',
                    allowBlank: false,
                }
            },{
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
            },{
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
            },{
                text: lang('Status'),
                dataIndex: 'StatusCode',
                flex:1,
                editor: {
                    xtype: 'combobox',
                    id:"Supplychain_product_form-ComboStatusCode",
                    store : cmb_status,
                    displayField: 'label',
                    valueField: 'id',
                    allowBlank: false,
                }
            }],
            listeners: {
				 
                'canceledit': function (editor, e, eOpts) {
                    Ext.getCmp('Koltiva.view.Mill.GridProduct-gridMainGrid').getStore().load();
                },
                'beforeedit':function (editor, e, eOpts) {
                
                },
                'edit': function (editor, e) {
                    var SupplychainProductID = e.record.data.SupplychainProductID;
                    var SupplychainID = Ext.getCmp('Koltiva.view.Mill.FormMainMill-FormBasicData-SupplychainID').getValue();
                    var ProductID = e.record.data.ProductID;
                    var ProductName = e.record.data.ProductName;
                    var ProductPercentage = e.record.data.ProductPercentage;
                    var StartDate = e.record.data.StartDate;
					var EndDate = e.record.data.EndDate;
                    var StatusCode = e.record.data.StatusCode;
                    
                    if(Ext.getCmp('Koltiva.view.Mill.FormMainMill-FormBasicData-SupplychainID')){
						Ext.Ajax.request({
							waitMsg: lang('Please wait...'),
							url: m_api + '/traceability_api/Supplychain_product/submit_mill',
							method: 'POST',
							params: {
                                SupplychainProductID : SupplychainProductID,
                                SupplychainID: SupplychainID,
                                ProductID : ProductID,
								ProductName : ProductName,
								ProductPercentage : ProductPercentage,
								StartDate : StartDate,
								EndDate : EndDate,
								StatusCode : StatusCode
							},
							success: function (response, opts) {
								var obj = Ext.decode(response.responseText);
								var message = SupplychainProductID != '' ? 'Update' : 'Insert';
								switch (obj.success) {
									case true:
										Ext.MessageBox.alert('Success', lang(message + ' success'));
										Ext.getCmp('Koltiva.view.Mill.GridProduct-gridMainGrid').getStore().load();
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
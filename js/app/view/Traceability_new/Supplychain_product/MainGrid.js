Ext.define('Koltiva.view.Traceability_new.Supplychain_product.MainGrid' ,{
    extend: 'Ext.panel.Panel',
    id: 'Koltiva.view.Traceability_new.Supplychain_product.MainGrid',  
    initComponent: function() {
        var thisObj = this;
        //store
        // thisObj.storeGridMainProduct = Ext.create('Koltiva.store.Traceability_new.Reference.Supplychain_product.MainGrid');
        var storeGridMainProduct = Ext.create('Koltiva.store.Traceability_new.Reference.Supplychain_product.MainGrid');

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
                        var sm = Ext.getCmp('Koltiva.view.Traceability_new.Supplychain_product.MainGrid-gridMainGrid').getSelectionModel().getSelection();
                        if (sm[0]) {
                            RowEditingProduct.startEdit(sm[0].index, 0);
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
						var smb = Ext.getCmp('Koltiva.view.Traceability_new.Supplychain_product.MainGrid-gridMainGrid').getSelectionModel().getSelection()[0];
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
                                                    Ext.getCmp('Koltiva.view.Traceability_new.Supplychain_product.MainGrid-gridMainGrid').getStore().load();
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
            id: 'Koltiva.view.Traceability_new.Supplychain_product.MainGrid-gridMainGrid',
            style: 'border:1px solid #CCC;margin-top:4px;',
            loadMask: true,
            selType: 'rowmodel',
            store: storeGridMainProduct,
            width: 1024,
            minHeight:400,
            //autoWidth : true,
            viewConfig: {
                deferEmptyText: false,
                emptyText: lang('No data Available'),
            },
            plugins: [RowEditingProduct],
            dockedItems: [{
                xtype: 'pagingtoolbar',
                id: 'Koltiva.view.Traceability_new.Supplychain_product.MainGrid-gridToolbar',
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
					id :'Koltiva.view.Traceability_new.Supplychain_product.MainGrid-gridMainGrid-Btn',
                    handler: function () { 
						
                        RowEditingProduct.cancelEdit();
                        
                        var record = Ext.getCmp('Koltiva.view.Traceability_new.Supplychain_product.MainGrid-gridMainGrid').getStore();

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
                }]
            }],
            columns: [{
				text: lang('Action'),	
				xtype:'actioncolumn',
				width:100,
				items:[{
					icon: varjs.config.base_url + 'images/icons/new/action.png',
					handler: function(grid, rowIndex, colIndex, item, e, record) {
						if(Ext.getCmp('setVarParameters').getValue() != 'view'){
							contextMenuProductGrid.showAt(e.getXY());
						}
					 
					}
				}]
            },{
                text: lang('Product Name'),
                dataIndex: 'ProductID',
                flex:1,
                editor: {
                    xtype: 'combobox',
                    id:"Supplychain_product_form-ComboProduct",
                    store : cmb_storeProduct,
                    displayField: 'ProductName',
                    valueField: 'ProductID',
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
                    Ext.getCmp('Koltiva.view.Traceability_new.Supplychain_product.MainGrid-gridMainGrid').getStore().load();
                },
                'beforeedit':function (editor, e, eOpts) {
                
                },
                'edit': function (editor, e) {
                    var SupplychainProductID = e.record.data.SupplychainProductID;
                    var SupplychainID = Ext.getCmp('Koltiva.view.Traceability_new.Reference.Supplychain_org-dataForm-SupplychainID').getValue();
                    var ProductID = e.record.data.ProductID;
                    var ProductPercentage = e.record.data.ProductPercentage;
                    var StartDate = e.record.data.StartDate;
					var EndDate = e.record.data.EndDate;
                    var StatusCode = e.record.data.StatusCode;
					
					if(Ext.getCmp('setVarParameters').getValue() != 'view'){
						Ext.Ajax.request({
							waitMsg: lang('Please wait...'),
							url: m_api + '/traceability_api/Supplychain_product/submit',
							method: 'POST',
							params: {
                                SupplychainProductID : SupplychainProductID,
                                SupplychainID: SupplychainID,
								ProductID : ProductID,
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
										Ext.getCmp('Koltiva.view.Traceability_new.Supplychain_product.MainGrid-gridMainGrid').getStore().load();
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
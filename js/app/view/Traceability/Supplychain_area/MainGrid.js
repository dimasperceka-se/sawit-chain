


Ext.define('Koltiva.view.Traceability.Supplychain_area.MainGrid' ,{
    extend: 'Ext.panel.Panel',
    id: 'Koltiva.view.Traceability.Supplychain_area.MainGrid',  
    initComponent: function() {
        var thisObj = this;
        //store 
         var ComboProvince = Ext.create('Koltiva.store.ComboGeneral.ComboProvince'); 
		 var ComboDistrict = Ext.create('Koltiva.store.ComboGeneral.ComboDistrict');
		 var storeGridMainAreaDistrict = Ext.create('Koltiva.store.Traceability.Reference.Supplychain_area.MainGrid');
        //items
        var RowEditing = Ext.create('Ext.grid.plugin.RowEditing', {
            id : 'roweditingId',
            clicksToMoveEditor: 0,
            autoCancel: false,
            errorSummary: false,
            clicksToEdit: 2,
			listeners :{
				 
			}
        });
		
		var contextMenuAreaGrid = Ext.create('Ext.menu.Menu',{
			cls:'Sfr_ConMenu',
            items:[ {
                icon: varjs.config.base_url + 'images/icons/new/update.png',
                text: lang('Update'),
                itemId: 'Koltiva.view.Traceability.Transaction.List_transactionArea-contextMenuUpdateItem',
                //hidden: m_act_update,
                handler: function() {
						RowEditing.cancelEdit();
                        var sm = Ext.getCmp('Koltiva.view.Traceability.Supplychain_area.MainGrid-gridMainGrid').getSelectionModel().getSelection();
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
						var smb = Ext.getCmp('Koltiva.view.Traceability.Supplychain_area.MainGrid-gridMainGrid').getSelectionModel().getSelection()[0];
                        RowEditing.cancelEdit();

                        if (smb) {
                            Ext.MessageBox.confirm('Message', lang('Are you sure will delete this data ?'), function (btn) {
                                if (btn == 'yes') {
                                    Ext.Ajax.request({
                                        waitMsg: lang('Please Wait'),
                                        url: m_api + '/traceability_api/Supplychain_area/del/' + smb.raw.AreaID, 
                                        method: 'DELETE',
                                        success: function (response, opts) {
                                            var obj = Ext.decode(response.responseText);
                                            switch (obj.success) {
                                                case true:
                                                    Ext.getCmp('Koltiva.view.Traceability.Supplychain_area.MainGrid-gridMainGrid').getStore().load();
                                                    Ext.MessageBox.alert('Warning', lang('Delete success'));
													Ext.getCmp('Koltiva.view.Traceability.Supplychain_area.MainGrid-gridMainGrid').getStore().load();
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
            id: 'Koltiva.view.Traceability.Supplychain_area.MainGrid-gridMainGrid',
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
            plugins: [RowEditing],
            dockedItems: [{
                xtype: 'pagingtoolbar',
                id: 'Koltiva.view.Traceability.Supplychain_area.MainGrid-gridToolbar',
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
					id :'Koltiva.view.Traceability.Supplychain_area.MainGrid-gridMainGrid-Btn',
                    handler: function () { 
						
						RowEditing.cancelEdit();
                        var record = Ext.getCmp('Koltiva.view.Traceability.Supplychain_area.MainGrid-gridMainGrid').getStore();

                        record.insert(0, { 
                            "Province" : '',
							"District" : '',
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
							contextMenuAreaGrid.showAt(e.getXY());
						}
					 
					}
				}]
			},{
                text: lang('Province'),
                dataIndex: 'Province',
                width:'25%',
                editor: {
                    xtype: 'combobox',
					typeAhead: true, 
					queryMode: 'local',
                    store : ComboProvince,
					emptyText: lang('Select a Province...'),
                    displayField : 'label',
                    valueField : 'id',
                    allowBlank: false,
                    listeners : {
                        change: function(record) {  
						   ComboDistrict.setStoreVar({'ProvinceID':record.getValue()}); 
                           ComboDistrict.load();  
                        }
                    }
                } 
            },
			{
                text: lang('District'),
                dataIndex: 'District',
                width:'35%',
                editor: {
                    xtype: 'combobox',
					typeAhead: true, 
                    store : ComboDistrict,
					queryMode: 'local',
                    displayField : 'label',
                    valueField : 'id',
                    allowBlank: false,
                    listeners : {
                        change : function(val){
                           
                        }
                    }
                }
            },
			{
                text: lang('Start Date'),
                dataIndex: 'DateStart',
                flex:1,
				format:'Y-m-d',
                editor: {
                    xtype: 'datefield',
					format:'Y-m-d',
					submitFormat: 'Y-m-d',
                    inputValue : 1,
                    allowBlank: true,
                },
            },
			{
                text: lang('End Date'),
                dataIndex: 'DateEnd',
                flex:1,
                format:'Y-m-d',
                editor: {
                    xtype: 'datefield',
					format:'Y-m-d', 
					submitFormat: 'Y-m-d',
                    inputValue : 1,
                    allowBlank: true,
                },
            }],
            listeners: { 
				beforeedit: function(editor,e,opt){
					/*Memastikan bahwa district diload sesuai dengan provinsinya, sewaktu colom di double click*/
					ComboDistrict.setStoreVar({'ProvinceID':e.record.data.ProvinceID}); 
                    ComboDistrict.load(); 
					/*end */
				},
                'canceledit': function (editor, e, eOpts) {
                    Ext.getCmp('Koltiva.view.Traceability.Supplychain_area.MainGrid-gridMainGrid').getStore().load();
                },
                'edit': function (editor, e) { 
					//alert(e.record.data)
                    var SupplychainID = Ext.getCmp('Koltiva.view.Traceability.Reference.Supplychain_org-dataForm-SupplychainID').getValue();
                    var SupplychainAreaID = e.record.data.SupplychainAreaID;
					var ProvinceID = e.record.data.Province;
                    var DistrictID = e.record.data.District;
					var StartDate = e.record.data.DateStart;
					var EndDate = e.record.data.DateEnd;
					
					if(Ext.getCmp('setVarParameters').getValue() != 'view'){
						Ext.Ajax.request({
							waitMsg: lang('Please wait...'),
							url: m_api + '/traceability/Supplychain_area/submit',
							method: 'POST',
							params: {
								SupplychainAreaID : SupplychainAreaID, 
								SupplychainID : SupplychainID,
								DistrictID : DistrictID,
								StartDate : StartDate,
								EndDate : EndDate 
							},
							success: function (response, opts) {
								var obj = Ext.decode(response.responseText);
								var message = SupplychainAreaID != '' ? 'Update' : 'Insert';
								switch (obj.success) {
									case true:
										Ext.MessageBox.alert('Success', lang(message + ' success'));
										Ext.getCmp('Koltiva.view.Traceability.Supplychain_area.MainGrid-gridMainGrid').getStore().load();
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

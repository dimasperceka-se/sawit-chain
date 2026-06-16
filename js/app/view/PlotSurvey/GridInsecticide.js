Ext.define('Koltiva.view.PlotSurvey.GridInsecticide' ,{
    extend: 'Ext.panel.Panel',
    id: 'Koltiva.view.PlotSurvey.GridInsecticide',  
    viewVar: false,
    setViewVar: function(value){
        this.viewVar = value;
    },
    initComponent: function() {
        var thisObj = this;
        //store
        thisObj.store = Ext.create('Koltiva.store.PlotSurvey.GridInsecticide',{
        	storeVar: {
                MemberID: thisObj.viewVar.MemberID,
                PlotNr: thisObj.viewVar.PlotNr,
                SurveyNr: thisObj.viewVar.SurveyNr
            }
        });

        var applying = Ext.create('Ext.data.Store', {
            fields: ['id', 'label'],
            data: [{
                "id": "1",
                "label": lang("All Palms")
            }, {
                "id": "2",
                "label": lang("Selected Palms")
            }]
        });

        var applyingfor = Ext.create('Ext.data.Store', {
            fields: ['id', 'label'],
            data: [{
                "id": "1",
                "label": lang("Termite control")
            }, {
                "id": "2",
                "label": lang("Caterpillar Control")
            }, {
                "id": "3",
                "label": lang("Oryctes Control")
            }, {
                "id": "4",
                "label": lang("Other")
            }]
        });

        var cmb_brand_insecticide = Ext.create('Koltiva.store.PlotSurvey.CmbBrandInsecticide'); 
        
        //items
        var RowEditingProduct = Ext.create('Ext.grid.plugin.RowEditing', {
            id : 'RowEditingInsecticideID',
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
                        var sm = Ext.getCmp('Koltiva.view.PlotSurvey.GridInsecticide-gridMainGrid').getSelectionModel().getSelection();
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
						var smb = Ext.getCmp('Koltiva.view.PlotSurvey.GridInsecticide-gridMainGrid').getSelectionModel().getSelection()[0];
                        RowEditingProduct.cancelEdit();

                        if (smb) {
                            Ext.MessageBox.confirm('Message', lang('Are you sure will delete this data ?'), function (btn) {
                                if (btn == 'yes') {
                                    Ext.Ajax.request({
                                        waitMsg: lang('Please Wait'),
                                        url: m_api + '/plot_survey/data_insecticide/' + smb.raw.InsecticideID,
                                        method: 'PUT',
                                        success: function (response, opts) {
                                            var obj = Ext.decode(response.responseText);
                                            switch (obj.success) {
                                                case true:
                                                    Ext.getCmp('Koltiva.view.PlotSurvey.GridInsecticide-gridMainGrid').getStore().load();
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
            id: 'Koltiva.view.PlotSurvey.GridInsecticide-gridMainGrid',
            style: 'border:1px solid #CCC;margin-top:0px;width:98%',
            loadMask: true,
            selType: 'rowmodel',
            store: thisObj.store,
            autoWidth : true,
            minHeight : 200,
            viewConfig: {
                deferEmptyText: false,
                emptyText: lang('No data Available'),
            },
            plugins: [RowEditingProduct],
            dockedItems: [{
                xtype: 'pagingtoolbar',
                id: 'Koltiva.view.PlotSurvey.GridInsecticide-gridToolbar',
                store: thisObj.store,
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
					id :'Koltiva.view.PlotSurvey.GridInsecticide-gridMainGrid-BtnAdd',
                    handler: function () { 
                        
                        if(thisObj.viewVar.opsiDisplay == 'insert') {
                            Ext.MessageBox.alert('Warning', lang('Please save plantation data first !'));
                        } else {
                            RowEditingProduct.cancelEdit();
                                    
                            var record = Ext.getCmp('Koltiva.view.PlotSurvey.GridInsecticide-gridMainGrid').getStore();

                            record.insert(0, {
                                "InsecticideID" : '',
                                "MemberID" : '',
                                "PlotNr" : '',
                                'SurveyNr' :"",
                                'BrandID' :"",
                                'ApplyingID' :"",
                                'FrequencyID' :"",
                            });

                            RowEditingProduct.startEdit(0,0);
                        }                       
                    }
                }]
            }],
            columns: [{
				text: lang('Action'),	
				xtype:'actioncolumn',
				width:'10%',
				items:[{
					icon: varjs.config.base_url + 'images/icons/new/action.png',
					handler: function(grid, rowIndex, colIndex, item, e, record) {
						if(Ext.getCmp('Koltiva.view.PlotSurvey.WinFormPlotSurvey-Form-MemberID').getValue() != ''){
							contextMenuProductGrid.showAt(e.getXY());
						}
					 
					}
				}]
            },{
                text: lang('Brand'),
                dataIndex: 'Brand',
				width:'20%',
                editor: {
                    xtype: 'combobox',
                    id:'BrandID',
                    store : cmb_brand_insecticide,
                    displayField: 'label',
                    valueField: 'id',
                    allowBlank: false,
                }
            },{
                text: lang('Frequency (times/year)'),
                dataIndex: 'Frequency',
				width:'25%',
                editor:{
                    xtype: 'numericfield',
                    id:'FrequencyID',
                    allowBlank: false,
                    minValue: 1,
                    maxValue: 24
                }
            },{
                text: lang('Applied on'),
                dataIndex: 'Applying',
				width:'20%',
                editor: {
                    xtype: 'combobox',
                    id:'ApplyingID',
                    store : applying,
                    displayField: 'label',
                    valueField: 'id',
                    allowBlank: false,
                }
            },{
                text: lang('Applying For'),
                dataIndex: 'ApplyingFor',
				width:'23%',
                editor: {
                    xtype: 'combobox',
                    id:'ApplyingForID',
                    multiSelect: true,
                    store : applyingfor,
                    displayField: 'label',
                    valueField: 'id',
                    allowBlank: false,
                }
            },{
                text: lang('ApplyingID'),
                dataIndex: 'ApplyingID',
                hidden:true,
				width:'30%'
            },{
                text: lang('BrandID'),
                dataIndex: 'BrandID',
                hidden:true,
				width:'30%'
            },{
                text: lang('ApplyingForID'),
                dataIndex: 'ApplyingForID',
                hidden:true,
				width:'30%'
            }],
            listeners: {
				 
                'canceledit': function (editor, e, eOpts) {
                    Ext.getCmp('Koltiva.view.PlotSurvey.GridInsecticide-gridMainGrid').getStore().load();
                },
                'beforeedit':function (editor, e, eOpts) {
                
                },
                'edit': function (editor, e) {
                    // console.log(e);
                    var InsecticideID = e.record.data.InsecticideID;
                    var MemberID    = Ext.getCmp('Koltiva.view.PlotSurvey.WinFormPlotSurvey-Form-MemberID').getValue();
                    var PlotNr      = Ext.getCmp('Koltiva.view.PlotSurvey.WinFormPlotSurvey-Form-PlotNr').getValue();
                    var SurveyNr    = Ext.getCmp('Koltiva.view.PlotSurvey.WinFormPlotSurvey-Form-SurveyNr').getValue();
                    var BrandID     = e.record.data.Brand;
                    var ApplyingID  = e.record.data.Applying;
                    var FrequencyID = e.record.data.Frequency;
                    var ApplyingFor = JSON.stringify(e.record.data.ApplyingFor);
                    
                    if(MemberID){
						Ext.Ajax.request({
							waitMsg: lang('Please wait...'),
							url: m_api + '/plot_survey/submit_insecticide',
							method: 'POST',
							params: {
                                InsecticideID : InsecticideID,
                                MemberID: MemberID,
                                PlotNr : PlotNr,
								SurveyNr : SurveyNr,
								Applying : ApplyingID,
								Frequency : FrequencyID,
								Brand : BrandID,
                                ApplyingFor: ApplyingFor
							},
							success: function (response, opts) {
								var obj = Ext.decode(response.responseText);
								var message = InsecticideID != '' ? 'Update' : 'Insert';
								switch (obj.success) {
									case true:
										Ext.MessageBox.alert('Success', lang(message + ' success'));
										Ext.getCmp('Koltiva.view.PlotSurvey.GridInsecticide-gridMainGrid').getStore().load();
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
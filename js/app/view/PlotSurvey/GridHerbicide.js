Ext.define('Koltiva.view.PlotSurvey.GridHerbicide' ,{
    extend: 'Ext.panel.Panel',
    id: 'Koltiva.view.PlotSurvey.GridHerbicide',  
    viewVar: false,
    setViewVar: function(value){
        this.viewVar = value;
    },
    initComponent: function() {
        var thisObj = this;
        //store
        thisObj.store = Ext.create('Koltiva.store.PlotSurvey.GridHerbicide',{
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
                "label": lang("Blanket on The Whole Surface")
            }, {
                "id": "2",
                "label": lang("Only Circle and Harvesting Path")
            }, {
                "id": "3",
                "label": lang("Selective Area")
            }]
        });

        var cmb_brand_herbicide = Ext.create('Koltiva.store.PlotSurvey.CmbBrandHerbicide'); 
        
        //items
        var RowEditingProductHerbicide = Ext.create('Ext.grid.plugin.RowEditing', {
            id : 'RowEditingHerbicideID',
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
						RowEditingProductHerbicide.cancelEdit();
                        var sm = Ext.getCmp('Koltiva.view.PlotSurvey.GridHerbicide-gridMainGrid').getSelectionModel().getSelection();
                        if (sm[0]) {
                            RowEditingProductHerbicide.startEdit(sm[0].index, 0);
                        }else{
                            Ext.MessageBox.alert('Warning', lang('Please select data on the grid !'));
                        }
                }
            },{
	            icon: varjs.config.base_url + 'images/icons/new/delete.png',
                text: lang('Delete'),
                cls:'Sfr_BtnConMenuWhite', 
	            handler: function(){
						var smb = Ext.getCmp('Koltiva.view.PlotSurvey.GridHerbicide-gridMainGrid').getSelectionModel().getSelection()[0];
                        RowEditingProductHerbicide.cancelEdit();

                        if (smb) {
                            Ext.MessageBox.confirm('Message', lang('Are you sure will delete this data ?'), function (btn) {
                                if (btn == 'yes') {
                                    Ext.Ajax.request({
                                        waitMsg: lang('Please Wait'),
                                        url: m_api + '/plot_survey/data_herbicide/' + smb.raw.HerbicideID,
                                        method: 'PUT',
                                        success: function (response, opts) {
                                            var obj = Ext.decode(response.responseText);
                                            switch (obj.success) {
                                                case true:
                                                    Ext.getCmp('Koltiva.view.PlotSurvey.GridHerbicide-gridMainGrid').getStore().load();
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
            id: 'Koltiva.view.PlotSurvey.GridHerbicide-gridMainGrid',
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
            plugins: [RowEditingProductHerbicide],
            dockedItems: [{
                xtype: 'pagingtoolbar',
                id: 'Koltiva.view.PlotSurvey.GridHerbicide-gridToolbar',
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
					id :'Koltiva.view.PlotSurvey.GridHerbicide-gridMainGrid-BtnAdd',
                    handler: function () {
                        
                        if(thisObj.viewVar.opsiDisplay == 'insert') {
                            Ext.MessageBox.alert('Warning', lang('Please save form plantation data first !'));
                        } else {
                            RowEditingProductHerbicide.cancelEdit();
                                    
                            var record = Ext.getCmp('Koltiva.view.PlotSurvey.GridHerbicide-gridMainGrid').getStore();

                            record.insert(0, {
                                "HerbicideID" : '',
                                "MemberID" : '',
                                "PlotNr" : '',
                                'SurveyNr' :"",
                                'BrandID' :"",
                                'ApplyingID' :"",
                                'FrequencyID' :"",
                            });

                            RowEditingProductHerbicide.startEdit(0,0);
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
				width:'25%',
                editor: {
                    xtype: 'combobox',
                    id:'BrandID',
                    store : cmb_brand_herbicide,
                    displayField: 'label',
                    valueField: 'id',
                    allowBlank: false,
                }
            },{
                text: lang('Frequency (times/year)'),
                dataIndex: 'Frequency',
				width:'30%',
                editor:{
                    xtype: 'numericfield',
                    id:'FrequencyID',
                    allowBlank: false,
                    minValue: 1,
                    maxValue: 24
                }
            },{
                text: lang('How Applying'),
                dataIndex: 'Applying',
				width:'33%',
                editor: {
                    xtype: 'combobox',
                    id:'ApplyingID',
                    store : applying,
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
            }],
            listeners: {
				 
                'canceledit': function (editor, e, eOpts) {
                    Ext.getCmp('Koltiva.view.PlotSurvey.GridHerbicide-gridMainGrid').getStore().load();
                },
                'beforeedit':function (editor, e, eOpts) {
                
                },
                'edit': function (editor, e) {
                    // console.log(e);
                    var HerbicideID = e.record.data.HerbicideID;
                    var MemberID = Ext.getCmp('Koltiva.view.PlotSurvey.WinFormPlotSurvey-Form-MemberID').getValue();
                    var PlotNr = Ext.getCmp('Koltiva.view.PlotSurvey.WinFormPlotSurvey-Form-PlotNr').getValue();
                    var SurveyNr = Ext.getCmp('Koltiva.view.PlotSurvey.WinFormPlotSurvey-Form-SurveyNr').getValue();
                    var BrandID = e.record.data.Brand;
                    var ApplyingID = e.record.data.Applying;
                    var FrequencyID = e.record.data.Frequency;
                    
                    if(MemberID){
						Ext.Ajax.request({
							waitMsg: lang('Please wait...'),
							url: m_api + '/plot_survey/submit_herbicide',
							method: 'POST',
							params: {
                                HerbicideID : HerbicideID,
                                MemberID: MemberID,
                                PlotNr : PlotNr,
								SurveyNr : SurveyNr,
								Applying : ApplyingID,
								Frequency : FrequencyID,
								Brand : BrandID
							},
							success: function (response, opts) {
								var obj = Ext.decode(response.responseText);
								var message = HerbicideID != '' ? 'Update' : 'Insert';
								switch (obj.success) {
									case true:
										Ext.MessageBox.alert('Success', lang(message + ' success'));
										Ext.getCmp('Koltiva.view.PlotSurvey.GridHerbicide-gridMainGrid').getStore().load();
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
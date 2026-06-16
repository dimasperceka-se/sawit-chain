Ext.define('Koltiva.view.NewSocialization.WinParticipants' ,{
    extend: 'Ext.window.Window',
    id: 'Koltiva.view.NewSocialization.WinParticipants',
    title: 'Add Participants',
    closable: true,
    modal: true,
    closeAction: 'destroy',
    width: '85%', 
	height: '60%', 
    overflowY: 'auto',
    viewVar: false,	
    initComponent: function() {
        var thisObj = this; 
		thisObj.cmbSubDistrictGeneral = Ext.create('Koltiva.store.ComboGeneral.ComboSubDistrict');   
		thisObj.store_participant_add = Ext.create('Koltiva.store.NewSocialization.GridApplication'); 
		thisObj.cmbFarmerGroupGeneral = Ext.create('Koltiva.store.ComboGeneral.CmbFarmerGroupByDistrict');
		thisObj.cmbDistrictGeneral = Ext.create('Koltiva.store.ComboGeneral.ComboDistrict',{
				storeVar:{
					ProvinceID : Ext.getCmp('Koltiva.view.NewSocialization.MainForm-Form-ProvinceID').getValue()
				}
		});  
		thisObj.cmbDistrictGeneral.load();
			
		thisObj.items = [{
                xtype: 'gridpanel',
                id: 'Koltiva.view.NewSocialization.WinParticipants-Form-grid_participant_add', 
                store: thisObj.store_participant_add,
                loadMask: true, 				
                dockedItems: [
                        {
                            xtype: 'toolbar',
                            items: [ 
							 
                            {
									xtype: 'hidden',
									fieldLabel: lang('District') +' *)', 
									store: thisObj.cmbDistrictGeneral,
									queryMode: 'local',
									displayField: 'label',
									width: 250, 
									valueField: 'id',
									id: 'Koltiva.view.NewSocialization.WinParticipants-Form-DistrictID',
									name: 'Koltiva.view.NewSocialization.WinParticipants-Form-DistrictID',
									allowBlank: true,
									listeners: {
										change: function(cb, nv, ov) {
											Ext.getCmp('Koltiva.view.NewSocialization.WinParticipants-Form-SubDistrictID').setValue('');
											thisObj.cmbSubDistrictGeneral.setStoreVar({
												DistrictID: nv
											});
											thisObj.cmbSubDistrictGeneral.load();  
										}
									}
							}, 
							{
									xtype: 'hidden',
									fieldLabel: lang('Sub District') +' *)', 
									store: thisObj.cmbSubDistrictGeneral,
									width: 250, 
									queryMode: 'local',
									displayField: 'label',
									valueField: 'id',
									id: 'Koltiva.view.NewSocialization.WinParticipants-Form-SubDistrictID',
									name: 'Koltiva.view.NewSocialization.WinParticipants-Form-SubDistrictID',
									allowBlank: true 
							},
                            {
                                xtype: 'textfield',
                                name: 'keyAddPart',
								width: 250, 
                                id: 'keyAddPart',
                                emptyText: 'Cari berdasar nama/ID',
                                width: 150,
                                listeners: {}
                            }, {
                                xtype: 'button',
                                icon: varjs.config.base_url + 'images/icons/silk/search.png',
                                margin: '0px 0px 0px 6px',
                                text: lang('Search'),
                                handler: function() {
                                    var provinceID = Ext.getCmp('Koltiva.view.NewSocialization.MainForm-Form-ProvinceID').getValue();
									var DistrictID = Ext.getCmp('Koltiva.view.NewSocialization.WinParticipants-Form-DistrictID').getValue();
									var SubDistrictID = Ext.getCmp('Koltiva.view.NewSocialization.WinParticipants-Form-SubDistrictID').getValue();
									thisObj.store_participant_add.load({
                                        params: { 
                                            key: Ext.getCmp('keyAddPart').getValue(),
                                            ProvinceID: provinceID,
                                            DistrictID: DistrictID,
                                            SubDistrictID: SubDistrictID
                                        }
                                    });
                                }
                            }]
                    },
                    {
                        xtype: 'pagingtoolbar',
                        store: thisObj.store_participant_add,
                        dock: 'bottom',
                        displayInfo: true
                    }, 
                    ], 
				viewConfig: { deferEmptyText: false, emptyText: lang('No data Available'),stripeRows: true,
					listeners : {
						beforerefresh : function(view) {
						},
					}
				},
                selModel: {
                    selType: 'checkboxmodel',
					checkOnly: true,
					multiSelect: true,
                    mode: "MULTI",
                    headerWidth: 50,
					listeners: {
						deselect: function(model, record, index) {
							id = record.get('ApplicantID');
							IMSSocID = Ext.getCmp('Koltiva.view.NewSocialization.MainForm-Form-IMSSocID').getValue(); 
								 
									Ext.Ajax.request({
										waitMsg: lang('Please Wait'),
										 url: m_api + '/new_socialization/delSelectedparticipant',
										method: 'DELETE',
										params: {ApplicantID: id, IMSSocID : IMSSocID },
										success: function (response, opts) {
											var obj = Ext.decode(response.responseText);
											switch (obj.success) {
												case true: 
													
													Ext.getCmp('Koltiva.view.NewSocialization.GridParticipant-FormDetail').store.reload({ params : { IMSSocID: IMSSocID } });  
													break;
												default:
													Ext.MessageBox.alert('Warning', obj.message);
													break;
											}
										},
										failure: function (response, opts) {
											var obj = Ext.decode(response.responseText);
											Ext.MessageBox.alert('error', lang('Could not connect to the database. Retry later'));
										}
									 }); 
						}, 
						select: function(model, record, index) {
								id = record.get('ApplicantID'); 
								Ext.Ajax.request({
									url: m_api + '/new_socialization/save_participant',
									method: 'POST',
									waitMsg: lang('Sending data...'),
									params: {
										IMSSocID: Ext.getCmp('Koltiva.view.NewSocialization.MainForm-Form-IMSSocID').getValue(),
										ApplicantID: id 
									},
									success: function(response, opts) {
										var obj = Ext.decode(response.responseText);
										switch (obj.success) {
											case true:  
												var IMSSocID = Ext.getCmp('Koltiva.view.NewSocialization.MainForm-Form-IMSSocID').getValue();  
												Ext.getCmp('Koltiva.view.NewSocialization.GridParticipant-FormDetail').store.reload({ params : { IMSSocID: IMSSocID } });  
												break;
											default:
												Ext.MessageBox.alert('Warning', obj.message);
												break;
										}
									}
								});
						}
					}					
                }, 				 
                columns: [
                    {
                        text: lang('NAME'),
                        dataIndex: 'Fullname',
                        flex: 2,
                    },  {
                        text: lang('ID'),
                        dataIndex: 'ApplicantID',
                        flex: 1,
                    }, 
					{
                        text:  lang('Farmer Group'),
                        dataIndex: 'GroupName',
                        flex: 2 
                    }, 
					{
                        text: lang('Provinsi'),
                        dataIndex: 'Province',
                        flex: 1,
                    }, {
                        text: lang('District'),
                        dataIndex: 'District',
                        flex: 1,
                    }, {
                        text: lang('SubDistrict'),
                        dataIndex: 'SubDistrict',
                        flex: 1,
                    }, {
                        text: lang('Village'),
                        dataIndex: 'VillageNames',
                        flex: 1,
                    }, 
                ]
            }]; 
           
		thisObj.buttons = [{
            id: 'Koltiva.view.NewSocialization.WinParticipants-Form-btnSave',
            text: lang('Save'),
            margin: '5px',
            ui: 's-button',
			scale: 'large',
            cls: 's-blue',
            handler: function() { 
				thisObj.close();
				var IMSSocID = Ext.getCmp('Koltiva.view.NewSocialization.MainForm-Form-IMSSocID').getValue();  
				Ext.getCmp('Koltiva.view.NewSocialization.GridParticipant-FormDetail').store.reload({ params : { IMSSocID: IMSSocID } });  
				/*
				var participants = '';
                    Ext.each(Ext.getCmp('Koltiva.view.NewSocialization.WinParticipants-Form-grid_participant_add').getSelectionModel().getSelection(), function(row, index, value) { 
                        participants = participants + ',' + row.data.ApplicantID;
                    });
				
						
				if (participants !== '') {
					Ext.Ajax.request({
						url: m_api + '/new_socialization/save_participant',
						method: 'POST',
						waitMsg: lang('Sending data...'),
						params: {
							IMSSocID: Ext.getCmp('Koltiva.view.NewSocialization.MainForm-Form-IMSSocID').getValue(),
							ApplicantID: participants 
						},
						success: function(response, opts) {
							var obj = Ext.decode(response.responseText);
							switch (obj.success) {
								case true:
									thisObj.close();  
									var IMSSocID = Ext.getCmp('Koltiva.view.NewSocialization.MainForm-Form-IMSSocID').getValue();  
									Ext.getCmp('Koltiva.view.NewSocialization.GridParticipant-FormDetail').store.reload({ params : { IMSSocID: IMSSocID } });  
									break;
								default:
									Ext.MessageBox.alert('Warning', obj.message);
									break;
							}
						}
					});
				} else {
					Ext.Msg.alert("Warning", "Please select participants");
				} 
				*/
            }
        },{
            text: lang('Close'),
            margin: '5px',
            scale: 'large',
            ui: 's-button',
            cls: 's-grey',
            handler: function() {
                //tutup popup
                thisObj.close();
				var IMSSocID = Ext.getCmp('Koltiva.view.NewSocialization.MainForm-Form-IMSSocID').getValue();  
				Ext.getCmp('Koltiva.view.NewSocialization.GridParticipant-FormDetail').store.reload({ params : { IMSSocID: IMSSocID } });  
            }
        }];
		
		this.callParent(arguments);
	},
	listeners: {
        afterRender: function(){
			var thisObj = this;
			thisObj.store_participant_add.setStoreVar({
				IMSID: thisObj.viewVar.IMSID,
				FarmerGroupID: thisObj.viewVar.FarmerGroupID,
				IMSSocID: thisObj.viewVar.IMSSocID 
				
			}); 
			thisObj.store_participant_add.load(); 
			
			thisObj.cmbFarmerGroupGeneral.setStoreVar({
				DistrictID: Ext.getCmp('Koltiva.view.NewSocialization.MainForm-Form-DistrictID').getValue()
			});
			thisObj.cmbFarmerGroupGeneral.load();
													
		}, 
	},
	
	
});

 
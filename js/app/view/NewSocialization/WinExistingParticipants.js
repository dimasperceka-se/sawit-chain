Ext.define('Koltiva.view.NewSocialization.WinExistingParticipants' ,{
    extend: 'Ext.window.Window',
    id: 'Koltiva.view.NewSocialization.WinExistingParticipants',
    title: lang('Add Existing Participants'),
    closable: true,
    modal: true,
    closeAction: 'destroy',
    width: '85%', 
	height: '60%', 
    overflowY: 'auto',
    viewVar: false,	
    initComponent: function() {
        var thisObj = this; 
		thisObj.cmbDistrictGeneral = Ext.create('Koltiva.store.ComboGeneral.ComboDistrict',{
				storeVar:{
					ProvinceID : Ext.getCmp('Koltiva.view.NewSocialization.MainForm-Form-ProvinceID').getValue()
				}
		});  
		thisObj.cmbDistrictGeneral.load();
		thisObj.cmbSubDistrictGeneral = Ext.create('Koltiva.store.ComboGeneral.ComboSubDistrict');   
		thisObj.ext_store_participant_add = Ext.create('Koltiva.store.NewSocialization.GridFarmerApplication',{
			storeVar:{
				ProvinceID : Ext.getCmp('Koltiva.view.NewSocialization.MainForm-Form-ProvinceID').getValue(),
				FarmerGroupID : Ext.getCmp('Koltiva.view.NewSocialization.MainForm-Form-FarmerGroupID').getValue(),
				IMSSocID: Ext.getCmp('Koltiva.view.NewSocialization.MainForm-Form-IMSSocID').getValue()
			}
		});
		thisObj.cmbFarmerGroupGeneral = Ext.create('Koltiva.store.ComboGeneral.CmbFarmerGroupByDistrict');
			 
			
		thisObj.items = [{
                xtype: 'gridpanel',
                id: 'Koltiva.view.NewSocialization.WinExistingParticipants-Form-grid_participant_add', 
                store: thisObj.ext_store_participant_add,
                loadMask: false, 				
                dockedItems: [{
					xtype: 'toolbar',
					items: [ 
					{
							xtype: 'hidden',
							fieldLabel: lang('District') +' *', 
							store: thisObj.cmbDistrictGeneral, 
							queryMode: 'local',
							displayField: 'label',
							width: 250, 
							valueField: 'id',
							id: 'Koltiva.view.NewSocialization.WinExistingParticipants-Form-DistrictID',
							name: 'Koltiva.view.NewSocialization.WinExistingParticipants-Form-DistrictID',
							allowBlank: true,
							listeners: {
								change: function(cb, nv, ov) {
									Ext.getCmp('Koltiva.view.NewSocialization.WinExistingParticipants-Form-SubDistrictID').setValue('');
									thisObj.cmbSubDistrictGeneral.setStoreVar({
										DistrictID: nv
									});
									thisObj.cmbSubDistrictGeneral.load();  
								}
							}
					}, 
					{
							xtype: 'hidden',
							fieldLabel: lang('Sub District') +' *', 
							store: thisObj.cmbSubDistrictGeneral,
							width: 250, 
							queryMode: 'local',
							displayField: 'label',
							valueField: 'id',
							id: 'Koltiva.view.NewSocialization.WinExistingParticipants-Form-SubDistrictID',
							name: 'Koltiva.view.NewSocialization.WinExistingParticipants-Form-SubDistrictID',
							allowBlank: true 
					},
					{
						xtype: 'textfield',
						name: 'keyAddPart',
						width: 250, 
						id: 'keyAddPart',
						emptyText: 'Cari berdasar nama',
						width: 150,
						listeners: {}
					}, {
						xtype: 'button',
						icon: varjs.config.base_url + 'images/icons/silk/search.png',
						margin: '0px 0px 0px 6px',
						text: lang('Search'),
						handler: function() {
							var provinceID = Ext.getCmp('Koltiva.view.NewSocialization.MainForm-Form-ProvinceID').getValue();
							var DistrictID = Ext.getCmp('Koltiva.view.NewSocialization.WinExistingParticipants-Form-DistrictID').getValue();
							var SubDistrictID = Ext.getCmp('Koltiva.view.NewSocialization.WinExistingParticipants-Form-SubDistrictID').getValue(); 
							thisObj.ext_store_participant_add.load({
								params: { 
									key: Ext.getCmp('keyAddPart').getValue(),
									ProvinceID: provinceID,
									DistrictID: DistrictID,
									SubDistrictID: SubDistrictID,
									IMSSocID: Ext.getCmp('Koltiva.view.NewSocialization.MainForm-Form-IMSSocID').getValue()
								}
							});
						}
					}]
			},
			{
				xtype: 'pagingtoolbar',
				store: thisObj.ext_store_participant_add,
				dock: 'bottom',
				displayInfo: true
			}], 
				viewConfig: { deferEmptyText: false, emptyText: lang('No data Available'),stripeRows: true,
					listeners : {
						 beforerefresh : function(view) { 
							IMSSocID = Ext.getCmp('Koltiva.view.NewSocialization.MainForm-Form-IMSSocID').getValue();  
							var d ='';
							Ext.Ajax.request({ 
								url: m_api + '/new_socialization/getcheckedfromparticipant', 
								method: 'POST',
								params: { 'IMSSocID' : IMSSocID},
								success: function (response, opts) { 
									 var obj = Ext.decode(response.responseText);    
									 var store = view.getStore();
										 var model = view.getSelectionModel();
										 var s = []; 	
										 for(i=0; i<obj.data.length; i++)
										 {   
											
											 store.queryBy(function(record) {  
												if (record.get('FarmerID') === obj.data[i].MobileUID) {
													s.push(record);
												} 
											 });
											 console.log(s)
											 model.select(s);
										 }
								} 
							 })  
							 
						},
					}
				},
                selModel: {
                    selType: 'checkboxmodel',
					checkOnly: true,
					multiSelect: true,
                    mode: "MULTI",
                    headerWidth: 50,					
                }, 				 
                columns: [
                    {
                        text: lang('NAME'),
                        dataIndex: 'FarmerName',
                        flex: 2,
                    },  {
                        text: lang('ID'),
                        dataIndex: 'FarmerID',
                        flex: 1,
                    }, {
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
                        dataIndex: 'Village',
                        flex: 1,
                    }, {
                        dataIndex: 'ApplicantID',
                        hidden: true,
                    }, 
                ]
            }]; 
           
		thisObj.buttons = [{
            id: 'Koltiva.view.NewSocialization.WinExistingParticipants-Form-btnSave',
            text: lang('Save'),
            margin: '5px',
            ui: 's-button',
			scale: 'large',
            cls: 's-blue',
            handler: function() {
                let FarmerID = '';
                let ApplicantID = '';
                let selection = Ext.getCmp('Koltiva.view.NewSocialization.WinExistingParticipants-Form-grid_participant_add').getSelectionModel().getSelection();
                Ext.each(selection, function(row, index, value) {
                    FarmerID = FarmerID + ',' + row.data.FarmerID;
                    ApplicantID = ApplicantID + ',' + row.data.ApplicantID;
                });
					
				if (FarmerID !== '') {
					Ext.Ajax.request({
						url: m_api + '/new_socialization/saveexistingfarmerbyweb',
						method: 'POST',
						waitMsg: lang('Sending data...'),
						params: {
							IMSSocID: Ext.getCmp('Koltiva.view.NewSocialization.MainForm-Form-IMSSocID').getValue(),
							FarmerID: FarmerID,
							ApplicantID: ApplicantID
						},
						success: function(response, opts) {
							var obj = Ext.decode(response.responseText);
							switch (obj.success) {
								case true:
									Ext.MessageBox.alert('Message', lang(obj.message));
									var IMSSocID = Ext.getCmp('Koltiva.view.NewSocialization.MainForm-Form-IMSSocID').getValue();  
									Ext.getCmp('Koltiva.view.NewSocialization.GridParticipant-FormDetail').store.reload({ params : { IMSSocID: IMSSocID } });
									thisObj.close(); 
									break;
								default:
									Ext.MessageBox.alert('Warning', lang(obj.message));
									break;
							}
						}
					});
				} else {
					Ext.Msg.alert("Warning", "Please select participants");
				} 
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
				Ext.getCmp('Koltiva.view.socialization.GridParticipant-FormDetail').store.reload({ params : { IMSSocID: IMSSocID } });  
            }
        }];
		
		this.callParent(arguments);
	},
	listeners: {
        afterRender: function(){
			var thisObj = this; 
			thisObj.ext_store_participant_add.setStoreVar({
				ProvinceID: thisObj.viewVar.ProvinceID,
				FarmerGroupID: thisObj.viewVar.FarmerGroupID,
				IMSSocID: thisObj.viewVar.IMSSocID
			}); 
			thisObj.ext_store_participant_add.load();   
			
			thisObj.cmbFarmerGroupGeneral.setStoreVar({
				DistrictID: Ext.getCmp('Koltiva.view.NewSocialization.MainForm-Form-DistrictID').getValue()
			});
			thisObj.cmbFarmerGroupGeneral.load();
		}, 
	},
	
	
});

 
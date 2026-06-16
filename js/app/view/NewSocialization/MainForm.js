  
var cmbPropinsiGeneral = Ext.create('Koltiva.store.ComboGeneral.ComboProvince');
var cmbDistrictGeneral = Ext.create('Koltiva.store.ComboGeneral.ComboDistrict');
var cmbSubDistrictGeneral = Ext.create('Koltiva.store.ComboGeneral.ComboSubDistrict');
var cmbVillageGeneral = Ext.create('Koltiva.store.ComboGeneral.ComboVillage');
var cmbEventTypeGeneral = Ext.create('Koltiva.store.ComboGeneral.cmbEventTypeGeneral');
var cmbCertHolderGeneral = Ext.create('Koltiva.store.ComboGeneral.CmbCertHolderGeneralCargill');
var cmbCertProgramsGeneral = Ext.create('Koltiva.store.ComboGeneral.CmbCertProgramsGeneral');
var cmbImsEventGeneral = Ext.create('Koltiva.store.ComboGeneral.CmbImsEventGeneral');
var cmbBatchGeneral = Ext.create('Koltiva.store.ComboGeneral.cmbBatchGeneral');
var cmbStaffGeneral = Ext.create('Koltiva.store.ComboGeneral.cmbStaffGeneral');
var cmbFarmerGroupGeneral = Ext.create('Koltiva.store.ComboGeneral.CmbFarmerGroup');
// cmbPropinsiGeneral.setStoreVar(
// 	{'HakAkses':'Yes'},												
// );

Ext.define('Koltiva.view.NewSocialization.MainForm' ,{
    extend: 'Ext.panel.Panel',
    id: 'Koltiva.view.NewSocialization.MainForm',
    style:'padding:0 15px 15px 15px;margin:12px 0 0 0;',
    opsiDisplay: false,
    setOpsiDisplay: function(value){
        this.opsiDisplay = value;
    },
    formVar: false,
    setFormVar: function(value){
        this.formVar = value;
    },
    renderTo: 'ext-content',
    initComponent: function() {
        var thisObj = this; 
		
		thisObj.objPanelBasicData = Ext.create('Ext.form.Panel',{
            title: lang('Socialization Form'),
            frame: true,
            id: 'Koltiva.view.NewSocialization.MainForm-FormBasicData',
            //fileUpload: true,
            margin:'0 0 20 0',
            items: [{
                xtype: 'panel',
                padding: 5,
                id: 'Koltiva.view.NewSocialization.MainForm-FormBasicData-MainBasicDataForm',
                items:[{
						layout: 'column',
						border: false,
						items:[
							//COLOUMN BASIC DATA START
								{	
									columnWidth: '.5',
									padding:'5 25 5 8',
									layout:'form', 
									items:[{
											xtype: 'hiddenfield', 
											id: 'Koltiva.view.NewSocialization.MainForm-Form-IMSSocID',
											name: 'Koltiva.view.NewSocialization.MainForm-Form-IMSSocID'
										},
										{
											xtype: 'textfield',
											fieldLabel: lang('Event Name') + '*',
											labelWidth : 200, 
											id: 'Koltiva.view.NewSocialization.MainForm-Form-EventName',
											name: 'Koltiva.view.NewSocialization.MainForm-Form-EventName',
											allowBlank: false
										},
										{
											xtype: 'combobox',
											fieldLabel: lang('Event Type') + '*',
											labelWidth: 200, 
											store: cmbEventTypeGeneral,
											queryMode: 'local',
											displayField: 'label',
											valueField: 'id',
											id: 'Koltiva.view.NewSocialization.MainForm-Form-CPGtrainingsID',
											name: 'Koltiva.view.NewSocialization.MainForm-Form-CPGtrainingsID',
											allowBlank: false,
											listeners: {
											 
											}
										},
										{										
											xtype: 'combobox',
											fieldLabel: lang('Batch Number') + '*',
											labelWidth: 200, 
											store: cmbBatchGeneral,
											queryMode: 'local',
											displayField: 'label',
											valueField: 'id',
											id: 'Koltiva.view.NewSocialization.MainForm-Form-BatchID',
											name: 'Koltiva.view.NewSocialization.MainForm-Form-BatchID',
											allowBlank: false,
											listeners: {
												change: function(cb, nv, ov) {
													 
												}
											}
										}, 
										{
											xtype: 'combobox',
											fieldLabel: lang('Province') +'*',
											labelWidth: 200, 
											store: cmbPropinsiGeneral,
											queryMode: 'local',
											displayField: 'label',
											valueField: 'id',
											id: 'Koltiva.view.NewSocialization.MainForm-Form-ProvinceID',
											name: 'Koltiva.view.NewSocialization.MainForm-Form-ProvinceID',
											allowBlank: false,
											listeners: {
												change: function(cb, nv, ov) {
													cmbDistrictGeneral.setStoreVar({
														ProvinceID: nv
													});
													cmbDistrictGeneral.load();  
												},
												select :function(cb, nv, ov) {
													Ext.getCmp('Koltiva.view.NewSocialization.MainForm-Form-DistrictID').setValue(''); 
													Ext.getCmp('Koltiva.view.NewSocialization.MainForm-Form-SubDistrictID').setValue(''); 
													Ext.getCmp('Koltiva.view.NewSocialization.MainForm-Form-VillageID').setValue(''); 
													Ext.getCmp('Koltiva.view.NewSocialization.MainForm-Form-VillageName').setValue(''); 
												}
											}
										},{
											xtype: 'combobox',
											fieldLabel: lang('District') +'*',
											labelWidth: 200, 
											store: cmbDistrictGeneral,
											queryMode: 'local',
											displayField: 'label',
											valueField: 'id',
											id: 'Koltiva.view.NewSocialization.MainForm-Form-DistrictID',
											name: 'Koltiva.view.NewSocialization.MainForm-Form-DistrictID',
											allowBlank: false,
											listeners: {
												change: function(cb, nv, ov) {
													cmbSubDistrictGeneral.setStoreVar({
														DistrictID: nv
													});
													cmbSubDistrictGeneral.load();  
												},
												select :function(cb, nv, ov) {
													Ext.getCmp('Koltiva.view.NewSocialization.MainForm-Form-SubDistrictID').setValue(''); 
												}
											}
										},
										{
											xtype: 'combobox',
											fieldLabel: lang('Sub District') +'*',
											labelWidth: 200, 
											store: cmbSubDistrictGeneral,
											queryMode: 'local',
											displayField: 'label',
											valueField: 'id',
											id: 'Koltiva.view.NewSocialization.MainForm-Form-SubDistrictID',
											name: 'Koltiva.view.NewSocialization.MainForm-Form-SubDistrictID',
											allowBlank: false,
											listeners: {
												change: function(cb, nv, ov) {
													cmbVillageGeneral.setStoreVar({
														SubDistrictID: nv,
														loadAll :'Yes'
													});
													cmbVillageGeneral.load();   
												},
												select :function(cb, nv, ov) {
													Ext.getCmp('Koltiva.view.NewSocialization.MainForm-Form-VillageID').setValue(''); 
													Ext.getCmp('Koltiva.view.NewSocialization.MainForm-Form-VillageName').setValue(''); 
												}
											}
										},
										{
											xtype: 'combobox',
											fieldLabel: lang('Village') +'*',
											labelWidth: 200, 
											store: cmbVillageGeneral,
											queryMode: 'local',
											displayField: 'label',
											valueField: 'id',
											id: 'Koltiva.view.NewSocialization.MainForm-Form-VillageID',
											name: 'Koltiva.view.NewSocialization.MainForm-Form-VillageID',
											allowBlank: false,
											listeners: {
												change: function(cb, nv, ov) { 
													 
													var item =  getCBSValue (cb, 'id', 'label');
													Ext.getCmp('Koltiva.view.NewSocialization.MainForm-Form-VillageName').setValue(item);

													cmbFarmerGroupGeneral.setStoreVar({
														VillageID: nv
													});
													cmbFarmerGroupGeneral.load();
													// Ext.getCmp('Koltiva.view.NewSocialization.MainForm-Form-GroupName').setValue('');


													var item =  getCBSValue (cb, 'id', 'label');
												}
											}
										},
										{
											xtype: 'hiddenfield', 
											labelWidth : 200,
											allowBlank: true,
											id: 'Koltiva.view.NewSocialization.MainForm-Form-VillageName',
											name: 'Koltiva.view.NewSocialization.MainForm-Form-VillageName'
										},
										{
											xtype: 'textfield',
											fieldLabel: lang('Location / Building'),
											labelWidth : 200,
											id: 'Koltiva.view.NewSocialization.MainForm-Form-Location',
											name: 'Koltiva.view.NewSocialization.MainForm-Form-Location'
										},
										{
											xtype: 'combobox',
											fieldLabel: lang('Farmer Group'),
											labelWidth: 200,
											store: cmbFarmerGroupGeneral,
											queryMode: 'local',
											displayField: 'label',
											valueField: 'id',
											id: 'Koltiva.view.NewSocialization.MainForm-Form-FarmerGroupID',
											name: 'Koltiva.view.NewSocialization.MainForm-Form-FarmerGroupID',
											allowBlank: false,
											listeners: {
												change: function(cb, nv, ov) {

												}
											}
										}]
									//COLOUMN BASIC DATA END
								},
								{
								//COLOUMN BASIC DATA START
								columnWidth: '.5',
								layout:'form',
								items:[
									{
										xtype: 'combobox',
										fieldLabel: lang('Certificated Programs') + '*',
										labelWidth: 200, 
										store: cmbCertProgramsGeneral,
										queryMode: 'local',
										displayField: 'label',
										valueField: 'id',
										id: 'Koltiva.view.NewSocialization.MainForm-Form-CertProgID',
										name: 'Koltiva.view.NewSocialization.MainForm-Form-CertProgID',
										allowBlank: false,
										listeners: {
											change: function(cb, nv, ov) {
												cmbCertHolderGeneral.setStoreVar({
													CertProgID: nv
												});
												cmbCertHolderGeneral.load();  
											}
										}
									},
									{
										xtype: 'combobox',
										fieldLabel: lang('Pemegang Sertifikat') + '*',
										labelWidth: 200, 
										store: cmbCertHolderGeneral,
										queryMode: 'local',
										displayField: 'label',
										valueField: 'id',
										id: 'Koltiva.view.NewSocialization.MainForm-Form-CertHolderID',
										name: 'Koltiva.view.NewSocialization.MainForm-Form-CertHolderID',
										allowBlank: false,
										listeners: {
											change: function(cb, nv, ov) {
												cmbImsEventGeneral.setStoreVar({
													CertHolderID: nv
												});
												cmbImsEventGeneral.load();  
											}
										}
									}, 
									{
										xtype: 'combobox',
										fieldLabel: lang('IMS Event') + '*',
										labelWidth: 200, 
										store: cmbImsEventGeneral,
										queryMode: 'local',
										displayField: 'label',
										valueField: 'id',
										id: 'Koltiva.view.NewSocialization.MainForm-Form-IMSID',
										name: 'Koltiva.view.NewSocialization.MainForm-Form-IMSID',
										allowBlank: false,
										listeners: {
											change: function(cb, nv, ov) {
												var item =  getCBSValue (cb, 'id', 'IMSMasterID');
												Ext.getCmp('Koltiva.view.NewSocialization.MainForm-Form-IMSMasterID').setValue(item);  
											}
										}
									}, 
									{
										xtype: 'hiddenfield', 
										labelWidth : 200,
										id: 'Koltiva.view.NewSocialization.MainForm-Form-IMSMasterID',
										name: 'Koltiva.view.NewSocialization.MainForm-Form-IMSMasterID',
										allowBlank: false
									},
									{
										xtype: 'datefield',
										fieldLabel: lang('Start') + '*',
										labelWidth: 200,
										width : 80,
										format:'Y-m-d',
										id: 'Koltiva.view.NewSocialization.MainForm-Form-EventStart',
										name: 'Koltiva.view.NewSocialization.MainForm-Form-EventStart',
										allowBlank: false, 
									},
									{
										xtype: 'datefield',
										fieldLabel: lang('End') + '*', 
										labelWidth: 200,
										width : 80,
										format:'Y-m-d',
										id: 'Koltiva.view.NewSocialization.MainForm-Form-EventEnd',
										name: 'Koltiva.view.NewSocialization.MainForm-Form-EventEnd',
										allowBlank: false,
										listeners : {
											blur : function(c,a)
											{
												var start = Ext.getCmp('Koltiva.view.NewSocialization.MainForm-Form-EventStart').getValue();
												var end = Ext.getCmp('Koltiva.view.NewSocialization.MainForm-Form-EventEnd').getValue(); 
												var result = mydiff(start, end, 'days'); 
												Ext.getCmp('Koltiva.view.NewSocialization.MainForm-Form-EventDays').setValue(result);
												//console.log(result);
											}
										}
									},
									{
										xtype: 'textfield',
										fieldLabel: lang('Number Of Day'),
										labelWidth: 200,
										emptyText :'0',
										readOnly : true,
										id: 'Koltiva.view.NewSocialization.MainForm-Form-EventDays',
										name: 'Koltiva.view.NewSocialization.MainForm-Form-EventDays',
										allowBlank: true
									}, 
									{
										xtype: 'combobox',
										fieldLabel: lang('Staff In Command') + '*',
										labelWidth: 200, 
										store: cmbStaffGeneral,
										queryMode: 'local',
										displayField: 'label',
										valueField: 'id',
										id: 'Koltiva.view.NewSocialization.MainForm-Form-PICStaffID',
										name: 'Koltiva.view.NewSocialization.MainForm-Form-PICStaffID',
										allowBlank: false 
									},
									{
										xtype: 'textarea',
										fieldLabel: lang('Remarks'), 
										id: 'Koltiva.view.NewSocialization.MainForm-Form-Remarks',
										name: 'Koltiva.view.NewSocialization.MainForm-Form-Remarks',
										allowBlank: true, 
									}, 
									{
									xtype: 'radiogroup',
									fieldLabel: lang('Event Status'),
									labelWidth: 225,
									columns: 2,
									id:'Koltiva.view.NewSocialization.MainForm-Form-rowCpgEventStatus', 
									items:[{
												boxLabel: lang('Ongoing'),
												name: 'Koltiva.view.NewSocialization.MainForm-Form-CertEventStatus',
												inputValue: '2',
												id: 'Koltiva.view.NewSocialization.MainForm-Form-cpgEventStatus2',
												listeners:{
													change: function(){
														return false;
													}
												}
											},{
												boxLabel: lang('Completed'),
												name: 'Koltiva.view.NewSocialization.MainForm-Form-CertEventStatus',
												inputValue: '1',
												id: 'Koltiva.view.NewSocialization.MainForm-Form-cpgEventStatus1',
												listeners:{
													change: function(){
														return false;
													}
												}
											}]
									}
									]
								//COLOUMN BASIC DATA END				
								}	
							]
						}]
				}],
				buttons: [{
                    text: 'Save',
                    margin: '5 15 5 5',
                    scale: 'large',
                    ui: 's-button',
                    id: 'Koltiva.view.NewSocialization.MainForm-btnSave',
                    cls: 's-blue',
                    handler: function () {
						var status = thisObj.objPanelBasicData.getForm().getValues()['Koltiva.view.NewSocialization.MainForm-Form-CertEventStatus'];
						if (thisObj.objPanelBasicData.isValid()) {
							if(status == 1){
								Ext.MessageBox.confirm('Message', lang('After completed you cannot update data, are you sure?'), function (btn) {
									if (btn == 'yes') {										
										thisObj.objPanelBasicData.submit({
											url: m_api + '/new_socialization/savedata',
											method:'POST',
											waitMsg: 'Saving data...',
											success: function(form, action) {
												
												Ext.MessageBox.show({
													title: 'Information',
													msg: lang('Event Socialization Completed'),
													buttons: Ext.MessageBox.OK,
													animateTarget: 'mb9',
													icon: 'ext-mb-success'
												});
												
												//Reload form
												var r =  action.result.data[0];
												Ext.getCmp('Koltiva.view.NewSocialization.MainForm-Form-IMSSocID').setValue(r.IMSSocID);
												localStorage.setItem('IMSSocID', r.IMSSocID);
												if(r.SocializationStatus == 1){
													//disabled button participation
													Ext.getCmp('Koltiva.view.NewSocialization.MainForm-btnSave').setDisabled(true);
													getDisabledButtonParticipation();
												}else{
													//enabled button participation
													getEnabledButtonParticipation();
												}
												//show participant
												Ext.getCmp('objPanelDetailDataSocializ').setVisible(true);
											},
											failure: function(fp, o){
												var pesanNya;
												if(o.result.message != undefined){
													pesanNya = o.result.message;
												}else{
													pesanNya = lang('Connection error');
												}
												Ext.MessageBox.show({
													title: 'Error',
													msg: pesanNya,
													buttons: Ext.MessageBox.OK,
													animateTarget: 'mb9',
													icon: 'ext-mb-error'
												});
											}
										});
									}
								});
							}else{
								
								thisObj.objPanelBasicData.submit({
									url: m_api + '/new_socialization/savedata',
									method:'POST',
									waitMsg: 'Saving data...',
									success: function(form, action) {
										
										Ext.MessageBox.show({
											title: 'Information',
											msg: lang('Data Saved & Please Add Participant'),
											buttons: Ext.MessageBox.OK,
											animateTarget: 'mb9',
											icon: 'ext-mb-success'
										});
										
										//Reload form
										var r =  action.result.data[0];
										Ext.getCmp('Koltiva.view.NewSocialization.MainForm-Form-IMSSocID').setValue(r.IMSSocID);
										localStorage.setItem('IMSSocID', r.IMSSocID);
										//enabled button participation
										getEnabledButtonParticipation();
										//show participant
										Ext.getCmp('objPanelDetailDataSocializ').setVisible(true);
									},
									failure: function(fp, o){
										var pesanNya;
										if(o.result.message != undefined){
											pesanNya = o.result.message;
										}else{
											pesanNya = lang('Connection error');
										}
										Ext.MessageBox.show({
											title: 'Error',
											msg: pesanNya,
											buttons: Ext.MessageBox.OK,
											animateTarget: 'mb9',
											icon: 'ext-mb-error'
										});
									}
								});
							}
        
                        } else {
                            Ext.MessageBox.show({
                                title: 'Attention',
                                msg: lang('Form not valid yet'),
                                buttons: Ext.MessageBox.OK,
                                animateTarget: 'mb9',
                                icon: 'ext-mb-info'
                            });
                        }
					}
				}]
		});
		
		
		thisObj.contextMenuGridParticipant = Ext.create('Ext.menu.Menu',{
			items:[{
				icon: varjs.config.base_url + 'images/icons/new/update.png',
				text: 'Recommendation From FA',
				// hidden: !m_act_update_socializ_participant,
				id :'Recommendation_From_FA', 
				handler: function() {
					Ext.each(Ext.getCmp('Koltiva.view.NewSocialization.GridParticipant-FormDetail').getSelectionModel().getSelection(), function(row, index, value) { 
						console.log(row);
                         
							Ext.getCmp('Koltiva.view.NewSocialization.MainForm-Form-HiddenIMSSocIDRekomndasi').setValue(row.data.IMSSocID);
							Ext.getCmp('Koltiva.view.NewSocialization.MainForm-Form-HiddenApplicantIDRekomndasi').setValue(row.data.ParticipantID); 
						
							Ext.getCmp('Koltiva.view.NewSocialization.MainForm-Form-FieldAgentName').setValue(row.data.FieldAgentName); 
							var radios = Ext.getCmp("Koltiva.view.NewSocialization.MainForm-Form-GroupRecommendationStatus"); 
							radios.setValue({'Koltiva.view.NewSocialization.MainForm-Form-RecommendationStatus': row.data.RecommendationStatus });
							Ext.getCmp('Koltiva.view.NewSocialization.MainForm-Form-RecommendationDate').setValue(row.data.RecommendationDate); 
							Ext.getCmp('Koltiva.view.NewSocialization.MainForm-Form-Comments').setValue(row.data.Comments); 
							RecomenAndSelectiionPilihan.show();
							 	
                    }); 
					 
				}
			},
			{
				icon: varjs.config.base_url + 'images/icons/new/delete.png',
				text: lang('Delete'),
				id :'delete_socializ_participant',
				hidden: !m_act_delete_socializ_participant,
				handler: function(){
					var smb = Ext.getCmp('Koltiva.view.NewSocialization.GridParticipant-FormDetail').getSelectionModel().getSelection()[0]
					Ext.MessageBox.confirm('Message', lang('Apakah anda mau menghapus data ini?'), function (btn) {
						if (btn == 'yes') {
							Ext.Ajax.request({
								waitMsg: lang('Please Wait'),
								 url: m_api + '/new_socialization/appformparticipant',
								method: 'DELETE',
								params: {ParticipantID: smb.raw.ParticipantID  },
								success: function (response, opts) {
									var obj = Ext.decode(response.responseText);
									switch (obj.success) {
										case true:
											thisObj.storeGridParticipant.load();
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
						}
					});
				}
			}] 
		});
		
		/////////////////////////////////////////////////PARTTICIPANT/////////////////////////////////////////////////////////////////
		thisObj.storeGridParticipant = Ext.create('Koltiva.store.NewSocialization.GridParticipant',{
			storeVar:{
				IMSSocID: this.IMSSocID
			}
		}); 
		thisObj.Grid_Participant = 
		{
					xtype: 'grid',
					id: 'Koltiva.view.NewSocialization.GridParticipant-FormDetail',
					style: 'border:1px solid #CCC;margin-top:4px;',
					loadMask: true, 
					store: thisObj.storeGridParticipant, 
					columns: [
							{
								text: lang('Action'),
								xtype:'actioncolumn',
								width:'5%',
								items:[{
									icon: varjs.config.base_url + 'images/icons/new/action.png',
									handler: function(grid, rowIndex, colIndex, item, e, record) { 
										//Delete hide jika sudah attandance
										if(record.data.ParticipateInSocializationStatus_check == 'NO'){
											Ext.getCmp('delete_socializ_participant').show();
										}else{
											Ext.getCmp('delete_socializ_participant').hide()
										}

										// if(record.data.AttendanceStatus == 'NO'){
										// 	Ext.getCmp('delete_socializ_participant').show();
										// }else{
										// 	Ext.getCmp('delete_socializ_participant').hide()
										// }
										 
										thisObj.contextMenuGridParticipant.showAt(e.getXY());
									}
								}]
							},
							{   
								text: 'Participant ID',
								flex:1,
								dataIndex: 'DisplayID', 
							},
							{   
								text: 'Applicant Name',
								flex:1,
								dataIndex: 'Fullname', 
							},
							{   
								text: 'Farmer Group',
								flex:1,
								dataIndex: 'GroupName', 
							},
							{   
								text: 'Participate in Socialization',
								flex:1,
								dataIndex: 'ParticipateInSocializationStatus_check',
								// dataIndex: 'AttendanceStatus', 
							},
							{   
								text: lang('Recommendation'),
								flex:1,
								dataIndex: 'Recommendation', 
							},
							{   
								text: 'Selection Status',
								dataIndex: 'SelectionStatus_selected', 
								flex:1, 
							},
							{   
								text: 'Remarks',
								flex:1,
								dataIndex: 'SelectionRemarks', 
							}
						   ],	
					viewConfig: {
						deferEmptyText: false,
						emptyText: lang('No data Available')
					},
					listeners :{
						beforechange: function() { 
							var IMSSocID = Ext.getCmp('Koltiva.view.NewSocialization.MainForm-Form-IMSSocID').getValue();
							thisObj.storeGridParticipant.setStoreVar({ 
								IMSSocID: IMSSocID
							});
							thisObj.storeGridParticipant.load(); 
						}
					},
					dockedItems: [
				{	
					xtype: 'pagingtoolbar', store: thisObj.storeGridParticipant, dock: 'bottom', beforePageText: '', hiddenInputItem: true, afterPageText : '', displayInfo: true,
					listeners :{
						beforechange: function() { 
							var IMSSocID = Ext.getCmp('Koltiva.view.NewSocialization.MainForm-Form-IMSSocID').getValue();
							thisObj.storeGridParticipant.setStoreVar({ 
								IMSSocID: IMSSocID
							});
							thisObj.storeGridParticipant.load(); 
						}
					}
				},
				{
					xtype: 'toolbar',
					items: [ 
					{
						icon: varjs.config.base_url + 'images/icons/new/add.png',
						text: lang('Add from Applicant'), 
						disabled: true,	
						hidden: false, //!m_act_add_socializ_participant,						
						id : 'Koltiva.view.NewSocialization.MainForm-Form-BtnAddParticipant', 
						cls:'Sfr_BtnGridGreen',
						overCls:'Sfr_BtnGridGreen-Hover',
						handler: function() {
							var IMSID = Ext.getCmp('Koltiva.view.NewSocialization.MainForm-Form-IMSID').getValue(); 
							var IMSSocID = Ext.getCmp('Koltiva.view.NewSocialization.MainForm-Form-IMSSocID').getValue(); 
							var WinParticipantForm = Ext.create('Koltiva.view.NewSocialization.WinParticipants',{
								viewVar: {
									opsiDisplay: 'insert',
									'IMSID' : IMSID,
									'FarmerGroupID' : Ext.getCmp('Koltiva.view.NewSocialization.MainForm-Form-FarmerGroupID').getValue(),
									'IMSSocID' : IMSSocID
								}
							}); 
							
							if (!WinParticipantForm.isVisible()) {
								WinParticipantForm.center();
								WinParticipantForm.show();
							} else {
								WinParticipantForm.close();
							} 
						}
					},
					{
						icon: varjs.config.base_url + 'images/icons/new/add.png',
						text: lang('Add from Existing Farmer'), 
						disabled: true,	 						
						id : 'Koltiva.view.NewSocialization.MainForm-Form-BtnAddExistingFarmer',
						cls:'Sfr_BtnGridGreen',
						overCls:'Sfr_BtnGridGreen-Hover',
						handler: function() {
							 
							var WinExistingParticipantForm = Ext.create('Koltiva.view.NewSocialization.WinExistingParticipants',{
								viewVar: {
									opsiDisplay: 'insert',
									ProvinceID : Ext.getCmp('Koltiva.view.NewSocialization.MainForm-Form-ProvinceID').getValue(),
									FarmerGroupID : Ext.getCmp('Koltiva.view.NewSocialization.MainForm-Form-FarmerGroupID').getValue(),
									IMSSocID: Ext.getCmp('Koltiva.view.NewSocialization.MainForm-Form-IMSSocID').getValue()		
								}
							}); 
							
							if (!WinExistingParticipantForm.isVisible()) {
								WinExistingParticipantForm.center();
								WinExistingParticipantForm.show();
							} else {
								WinExistingParticipantForm.close();
							} 
						}
					},
					{
						icon: varjs.config.base_url + 'images/icons/new/update.png',
						id : 'Koltiva.view.NewSocialization.MainForm-Form-BtnAttdenceParticipant', 
						text: 'Input Attendance Checklist', 
						disabled: true,
						cls:'Sfr_BtnGridPaleBlue',
						overCls:'Sfr_BtnGridPaleBlue-Hover',					
						handler: function() { 
							var IMSSocID = Ext.getCmp('Koltiva.view.NewSocialization.MainForm-Form-IMSSocID').getValue();  
							var WinParticipantAttendanceForm = Ext.create('Koltiva.view.NewSocialization.WinParticipantAttendanceForm',{
								viewVar: {
									opsiDisplay: 'insert', 
									'IMSSocID' : IMSSocID
								}
							}); 
							if (!WinParticipantAttendanceForm.isVisible()) {
								WinParticipantAttendanceForm.center();
								WinParticipantAttendanceForm.show(); 
								Ext.getCmp('Koltiva.view.NewSocialization.WinParticipantAttendanceForm-Form-Grid_Participant_add').store.reload({ params : { IMSSocID: '' } }); 
							} else { 
								WinParticipantAttendanceForm.close();
							} 
						}
					},
					{
						xtype: 'splitbutton',
						icon: varjs.config.base_url + 'images/icons/silk/printer.png',
						id : 'Koltiva.view.NewSocialization.MainForm-Form-BtnAttdenceListParticipant', 
						text: lang('Daftar Hadir'),
						disabled: true,
						cls:'Sfr_BtnGridPaleBlue',
						overCls:'Sfr_BtnGridPaleBlue-Hover',
						menu: {
							items: [{
									text: lang('Form Kosong'),
									handler: function() {
										var IMSSocID = Ext.getCmp('Koltiva.view.NewSocialization.MainForm-Form-IMSSocID').getValue();  
										preview_cetak_surat(m_api + '/new_socialization/cetakkosong/' + IMSSocID); 
									}
								},
								{
									text: lang('Form Hasil'),
									handler: function() { 
										var IMSSocID = Ext.getCmp('Koltiva.view.NewSocialization.MainForm-Form-IMSSocID').getValue();
										ComboHariEvent.load({params:{IMSSocID: IMSSocID}}); 
										PrintPilihan.show();
									}
								}
							]
						}
					},
					{
					icon: varjs.config.base_url + 'images/icons/silk/page_excel.png',
					text: lang('Export All'),
					cls:'Sfr_BtnGridPaleBlue',
					overCls:'Sfr_BtnGridPaleBlue-Hover',
					handler: function() { 
							let IMSSocID = Ext.getCmp('Koltiva.view.NewSocialization.MainForm-Form-IMSSocID').getValue();
							let url = m_api + '/new_socialization/exportexcelsocializationParticipant/'+IMSSocID;
							window.location.href = url;
						} 	
					}]
				}
			   ],
					 
		};
		
		thisObj.contextMenuStaff = Ext.create('Ext.menu.Menu',{
			items:
			[  
				{
				icon: varjs.config.base_url + 'images/icons/new/delete.png',
				text: lang('Delete'),
				id :'delete_socializ_staff',
				hidden: !m_act_delete_socializ_staff,
				handler: function(){
						var smb = Ext.getCmp('Koltiva.view.NewSocialization.GridMainStaff-FormDetail').getSelectionModel().getSelection()[0]
						Ext.MessageBox.confirm('Message', lang('Apakah anda mau menghapus data ini?'), function (btn) {
							if (btn == 'yes') {
								Ext.Ajax.request({
									waitMsg: lang('Please Wait'),
									 url: m_api + '/new_socialization/hapusstaff',
									method: 'DELETE',
									params: { SocStaffID : smb.raw.SocStaffID},
									success: function (response, opts) {
										var obj = Ext.decode(response.responseText);
										switch (obj.success) {
											case true:
												thisObj.GridMainStaff.load();
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
							}
						});
				}
			}] 
		});
		thisObj.GridMainStaff = Ext.create('Koltiva.store.NewSocialization.GridSocializationStaff', {
			storeVar:{
				IMSSocID: this.IMSSocID
			}
		}); 
		thisObj.Grid_Staff = 
		{
					xtype: 'grid',
					id: 'Koltiva.view.NewSocialization.GridMainStaff-FormDetail',
					style: 'border:1px solid #CCC;margin-top:4px;',
					loadMask: true, 
					store: thisObj.GridMainStaff, 
					columns: [
							{
								text: lang('Action'),
								xtype:'actioncolumn',
								width:'5%',
								items:[{
									icon: varjs.config.base_url + 'images/icons/new/action.png',
										handler: function(grid, rowIndex, colIndex, item, e, record) {  
											thisObj.contextMenuStaff.showAt(e.getXY()); 
										}
									}]
							},
							{   
								text: lang('Staff Name'),
								flex:1,
								dataIndex: 'PersonNm', 
							} 
						   ],	
					viewConfig: {
						deferEmptyText: false,
						emptyText: lang('No data Available')
					}, 
					listeners :{
						beforechange: function() { 
							var IMSSocID = Ext.getCmp('Koltiva.view.NewSocialization.MainForm-Form-IMSSocID').getValue();
							thisObj.GridMainStaff.setStoreVar({ 
								IMSSocID: IMSSocID
							});
							thisObj.GridMainStaff.load(); 
						}
					},
					dockedItems: [
				{	
					xtype: 'pagingtoolbar', store: thisObj.GridMainStaff, dock: 'bottom', beforePageText: '', hiddenInputItem: true, afterPageText : '', displayInfo: true,
					listeners :{
						beforechange: function() { 
							var IMSSocID = Ext.getCmp('Koltiva.view.NewSocialization.MainForm-Form-IMSSocID').getValue();
							thisObj.GridMainStaff.setStoreVar({ 
								IMSSocID: IMSSocID
							});
							thisObj.GridMainStaff.load(); 
						}
					}
				},
				{
					xtype: 'toolbar',
					items: [{
						icon: varjs.config.base_url + 'images/icons/new/add.png',
						text: lang('Add'), 
						disabled: true,	
						hidden: !m_act_add_socializ_staff,						
						id : 'Koltiva.view.NewSocialization.MainForm-Form-BtnAddStaff',
						cls:'Sfr_BtnGridGreen',
						overCls:'Sfr_BtnGridGreen-Hover',
						handler: function() {
							var IMSID = Ext.getCmp('Koltiva.view.NewSocialization.MainForm-Form-IMSID').getValue(); 
							var IMSSocID = Ext.getCmp('Koltiva.view.NewSocialization.MainForm-Form-IMSSocID').getValue(); 
							var WinStaffForm = Ext.create('Koltiva.view.NewSocialization.WinStaff',{
								viewVar: {
									opsiDisplay: 'insert',
									'IMSID' : IMSID,
									'IMSSocID' : IMSSocID
								}
							}); 
							
							if (!WinStaffForm.isVisible()) {
								WinStaffForm.center();
								WinStaffForm.show();
							} else {
								WinStaffForm.close();
							} 
						}
					}, 
					{
						icon: varjs.config.base_url + 'images/icons/new/update.png',
						id : 'Koltiva.view.NewSocialization.MainForm-Form-BtnAttdenceStaff', 
						text: 'Input Attendance Checklist', 
						disabled: true,
						cls:'Sfr_BtnGridPaleBlue',
						overCls:'Sfr_BtnGridPaleBlue-Hover',						
						handler: function() { 
							var IMSSocID = Ext.getCmp('Koltiva.view.NewSocialization.MainForm-Form-IMSSocID').getValue();  
							var WinStaffAttendanceForm = Ext.create('Koltiva.view.NewSocialization.WinStaffAttendanceForm',{
								viewVar: {
									opsiDisplay: 'insert', 
									'IMSSocID' : IMSSocID
								}
							}); 
							if (!WinStaffAttendanceForm.isVisible()) {
								WinStaffAttendanceForm.center();
								WinStaffAttendanceForm.show(); 
								Ext.getCmp('Koltiva.view.NewSocialization.WinStaffAttendanceForm-Form-thisObj.Grid_Participant_add').store.reload({ params : { IMSSocID: '' } }); 
							} else { 
								WinStaffAttendanceForm.close();
							} 
						}
					},
					{
					icon: varjs.config.base_url + 'images/icons/silk/page_excel.png',
					text: lang('Export All'),
					cls:'Sfr_BtnGridPaleBlue',
					overCls:'Sfr_BtnGridPaleBlue-Hover',
					handler: function() { 
							let IMSSocID = Ext.getCmp('Koltiva.view.NewSocialization.MainForm-Form-IMSSocID').getValue();
							let url = m_api + '/new_socialization/exportexcelsocializationStaff/'+IMSSocID;
							window.location.href = url;
						} 	
					}
					]
				}
			   ],
					 
		};
		
		thisObj.objPanelDetailData = Ext.create('Ext.form.Panel',{ 
            frame: true, 
			id :"objPanelDetailDataSocializ",
            margin:'0 0 20 0',
            hidden: (thisObj.opsiDisplay == 'update') ? false : true,
            items: [
			{
				xtype: 'tabpanel',
				flex: 1,
				margin: 2,
				activeTab: 0,
				plain: true, 
				items:[
				{				
					   xtype:'panel',
					   title : lang('Socialization Participation'), 
					   items:[
					   { 
						xtype:'component', id:'alertmissingSyncdata', html :'' 
					   },
						thisObj.Grid_Participant
					   ]
				},
				{				
					   xtype:'panel',
					   title : lang('Staff List'), 
					   items:[thisObj.Grid_Staff]
				}
				]
			}]
		});
		
		thisObj.items = [{
            xtype: 'panel',
            border:false,
            layout:{
                type:'hbox'
            },
            items:[{
                id:'Koltiva.view.NewSocialization.MainForm-title',
                html:''
            },{
                id: 'Koltiva.view.NewSocialization.MainForm-labelInfoInsert',
                html:'',
            }]
        },{
            xtype: 'component',
            autoEl: {
                tag: 'a',
                href: '#',
                html: lang('Back to socialization List'),
                style:'text-decoration:underline;'
            },
            listeners: {
                click: {
                    element: 'el',
                    preventDefault: true,
                    fn: function(e, target){
						Ext.getCmp('Koltiva.view.NewSocialization.MainForm').destroy(); //destory current view
                        if(Ext.getCmp('Koltiva.view.NewSocialization.MainGrid') == undefined){
                            Ext.create('Koltiva.view.NewSocialization.MainGrid');
                            // Show filter region
            				$('.Sfr_BoxFilterCommonContentRegion').show();
            				Ext.getCmp('delete_socializ_participant').destroy();
            				Ext.getCmp('delete_socializ_staff').destroy();
                        }else{
                            //destroy, create ulang
                            Ext.getCmp('Koltiva.view.NewSocialization.MainForm').destroy(); 
                            Ext.create('Koltiva.view.NewSocialization.MainGrid');
                            // Show filter region
            				$('.Sfr_BoxFilterCommonContentRegion').show();
            				Ext.getCmp('delete_socializ_participant').destroy();
            				Ext.getCmp('delete_socializ_staff').destroy();
                        }
						thisObj.contextMenuGridParticipant.destroy();
						thisObj.contextMenuGridStaff.destroy();
                    }
                }
            }
        },{
            html:'<br />'
        },{
            layout: 'column',
            border: false,
            items: [{
                columnWidth: 1,
                items:[thisObj.objPanelBasicData, thisObj.objPanelDetailData] 
            }]
        }];
		
		 
		this.callParent(arguments);
	},
	listeners: {
        afterRender: function(){
			var thisObj = this;
            
            // Hide filter region
            $('.Sfr_BoxFilterCommonContentRegion').hide();

            if(thisObj.opsiDisplay == 'insert'){
                Ext.getCmp('Koltiva.view.NewSocialization.MainForm-labelInfoInsert').update('<h3 style="margin:0px;padding:0px;">Add New Socialization</h3>');
                
                //form reset
                Ext.getCmp('Koltiva.view.NewSocialization.MainForm-FormBasicData').getForm().reset();
            }
			if(thisObj.opsiDisplay == 'update'){
				Ext.getCmp('Koltiva.view.NewSocialization.MainForm-labelInfoInsert').update('<h3 style="margin:0px;padding:0px;">'+ this.IMSSocID +' - '+ this.EventName +'</h3>');
				Ext.Ajax.request({
                    url: m_api + '/new_socialization/loadappdata',
					method: 'GET',
					params: {
						IMSSocID: thisObj.IMSSocID
					},
                    success: function (fp, o) {
                        var r = Ext.decode(fp.responseText);
						Ext.getCmp('Koltiva.view.NewSocialization.MainForm-Form-IMSSocID').setValue(r.data.IMSSocID); 
						Ext.getCmp('Koltiva.view.NewSocialization.MainForm-Form-BatchID').setValue(r.data.BatchID);
						Ext.getCmp('Koltiva.view.NewSocialization.MainForm-Form-EventName').setValue(r.data.EventName);
						Ext.getCmp('Koltiva.view.NewSocialization.MainForm-Form-CertProgID').setValue(r.data.CertProgID);
						Ext.getCmp('Koltiva.view.NewSocialization.MainForm-Form-CertHolderID').setValue(r.data.CertHolderID);
						Ext.getCmp('Koltiva.view.NewSocialization.MainForm-Form-IMSMasterID').setValue(r.data.IMSMasterID);
						Ext.getCmp('Koltiva.view.NewSocialization.MainForm-Form-IMSID').setValue(r.data.IMSID); 
						Ext.getCmp('Koltiva.view.NewSocialization.MainForm-Form-CPGtrainingsID').setValue(r.data.CPGtrainingsID);
						Ext.getCmp('Koltiva.view.NewSocialization.MainForm-Form-ProvinceID').setValue(r.data.ProvinceID); 
						Ext.getCmp('Koltiva.view.NewSocialization.MainForm-Form-DistrictID').setValue(r.data.DistrictID);	
						cmbSubDistrictGeneral.setStoreVar({ DistrictID: r.data.DistrictID }); cmbSubDistrictGeneral.load();
						Ext.getCmp('Koltiva.view.NewSocialization.MainForm-Form-SubDistrictID').setValue(r.data.SubDistrictID);	
						Ext.getCmp('Koltiva.view.NewSocialization.MainForm-Form-VillageID').setValue(r.data.VillageID);
						Ext.getCmp('Koltiva.view.NewSocialization.MainForm-Form-VillageName').setValue(r.data.VillageName);
						Ext.getCmp('Koltiva.view.NewSocialization.MainForm-Form-EventStart').setValue(r.data.EventStart);
						Ext.getCmp('Koltiva.view.NewSocialization.MainForm-Form-EventEnd').setValue(r.data.EventEnd);
						Ext.getCmp('Koltiva.view.NewSocialization.MainForm-Form-EventDays').setValue(r.data.EventDays);
						Ext.getCmp('Koltiva.view.NewSocialization.MainForm-Form-Location').setValue(r.data.Location);
						Ext.getCmp('Koltiva.view.NewSocialization.MainForm-Form-PICStaffID').setValue(r.data.PICStaffID);
						Ext.getCmp('Koltiva.view.NewSocialization.MainForm-Form-Remarks').setValue(r.data.Remarks);
						Ext.getCmp('Koltiva.view.NewSocialization.MainForm-Form-FarmerGroupID').setValue(r.data.FarmerGroupID);
						
						if (r.data.SocializationStatus == 1){
							Ext.getCmp('Koltiva.view.NewSocialization.MainForm-Form-cpgEventStatus2').setValue(false);
							Ext.getCmp('Koltiva.view.NewSocialization.MainForm-Form-cpgEventStatus1').setValue(true);
							Ext.getCmp('Koltiva.view.NewSocialization.MainForm-btnSave').setDisabled(true);
							getDisabledButtonParticipation();
						}
						 
						if (r.data.SocializationStatus == 2){
							Ext.getCmp('Koltiva.view.NewSocialization.MainForm-Form-cpgEventStatus1').setValue(false);
							Ext.getCmp('Koltiva.view.NewSocialization.MainForm-Form-cpgEventStatus2').setValue(true);
							Ext.getCmp('Koltiva.view.NewSocialization.MainForm-btnSave').setDisabled(false);
							getEnabledButtonParticipation();
						}
						
						localStorage.setItem('IMSSocID', r.data.IMSSocID); 
						Ext.getCmp('Koltiva.view.NewSocialization.GridParticipant-FormDetail').store.reload();								
						
						
						
					}
                });
			}
			
		}		
	}
	
});

var ComboHariEvent = Ext.create('Koltiva.store.socialization.ComboHariEvent');
var PrintPilihan = Ext.create('Ext.window.Window', {
			title: 'Event Days',
			closable: true,
			closeAction: 'hide',
			width: 350,
			minWidth: 250,
			height: 125,
			animCollapse:false,
			border: false,
			modal: true,
			layout: {
				type: 'border',
				padding: 5
			},
			items: [{
					   xtype: 'form',
					   id : 'printoutForms',
					   items: 
							[{
								xtype: 'combobox',
								id :'printoutEventDaysInput', 
								fieldLabel : lang('Days'), 
								store : ComboHariEvent,
								displayField: 'hari',
								valueField: 'hari',
								triggerAction : 'all', 
								queryMode: 'local',  
								readOnly: false,
								allowBlank : false 
							}],
						   fbar: [{
							    text: 'Print',
							    formBind: true,
							    itemId: 'submit',
								handler: function() { 
									var IMSSocID = Ext.getCmp('Koltiva.view.NewSocialization.MainForm-Form-IMSSocID').getValue();
									var printoutEventDaysInput = Ext.getCmp('printoutEventDaysInput').getValue();
									var param = IMSSocID +'_'+ printoutEventDaysInput;
									preview_cetak_surat(m_api + '/new_socialization/cetakisi/' + param);   
								}
						   }] 
			}] 
});

var RecomenAndSelectiionPilihan = Ext.create('Ext.window.Window', {
			title: 'Recommendation From FA', 
			closable: true,
			closeAction: 'hide',
			width: 650,
			minWidth: 250,
			height: 325,
			animCollapse:false,
			border: false,
			modal: true,
			items: [
					{
					   xtype: 'form',
					   id : 'Koltiva.view.NewSocialization.MainForm-Rekomendasi', 
					   items: [ {	
									columnWidth: '.5',
									padding:'5 25 5 8',
									layout:'form',  
									items:[ {
												xtype: 'hiddenfield', 
												id: 'Koltiva.view.NewSocialization.MainForm-Form-HiddenApplicantIDRekomndasi',
												name: 'Koltiva.view.NewSocialization.MainForm-Form-HiddenApplicantIDRekomndasi', 
											},
											{
												xtype: 'hiddenfield', 
												id: 'Koltiva.view.NewSocialization.MainForm-Form-HiddenIMSSocIDRekomndasi',
												name: 'Koltiva.view.NewSocialization.MainForm-Form-HiddenIMSSocIDRekomndasi', 
											},
											
											{
												fieldLabel: 'Recomendation For Participating in the Certification event  *)',
												labelWidth: 200, 
												xtype: 'radiogroup',
												width: '100%',
												allowBlank: false,
												id:'Koltiva.view.NewSocialization.MainForm-Form-GroupRecommendationStatus',
												defaults: {xtype: "radio",name: 'Koltiva.view.NewSocialization.MainForm-Form-RecommendationStatus'},
												items: [{
													boxLabel: 'Recomended', 
													inputValue: '1' 
												}, {
													boxLabel: 'Not Recomended', 
													inputValue: '2' 
												}]
											},
											{
												xtype: 'textfield',
												fieldLabel: 'Field Agent Name', 
												labelWidth: 200,  
												id: 'Koltiva.view.NewSocialization.MainForm-Form-FieldAgentName',
												name: 'Koltiva.view.NewSocialization.MainForm-Form-FieldAgentName',
												allowBlank: false
											},	
											{
												xtype: 'datefield',
												fieldLabel: 'Date Of Recomendation', 
												labelWidth: 200,  
												id: 'Koltiva.view.NewSocialization.MainForm-Form-RecommendationDate',
												name: 'Koltiva.view.NewSocialization.MainForm-Form-RecommendationDate',
												allowBlank: false,
												format :'Y-m-d'
											},
											{
												xtype: 'textarea',
												fieldLabel: 'Comment',
												labelWidth: 200, 
												id: 'Koltiva.view.NewSocialization.MainForm-Form-Comments',
												name: 'Koltiva.view.NewSocialization.MainForm-Form-Comments',
												allowBlank: true 
											}
										]										
								}],
						   fbar: [{
							    text: lang('Save'), 
								scale: 'large',
								ui: 's-button',
								cls: 's-blue',
							    itemId: 'submitRekomendasi',
									handler: function() { 
										var formNya = Ext.getCmp('Koltiva.view.NewSocialization.MainForm-Rekomendasi').getForm(); 
										if (formNya.isValid()) {

											formNya.submit({
												url: m_api + '/new_socialization/savedata_rekomendasi',
												method:'POST',
												waitMsg: 'Saving data...',
												success: function(fp, o) {
													Ext.MessageBox.show({
														title: 'Information',
														msg: lang('Data saved'),
														buttons: Ext.MessageBox.OK,
														animateTarget: 'mb9',
														icon: 'ext-mb-success'
													});

													//form reset
													formNya.reset(); 
													//refresh store yg manggil 
													Ext.getCmp('Koltiva.view.NewSocialization.GridParticipant-FormDetail').store.reload();
													 
													//tutup popup
													RecomenAndSelectiionPilihan.close();
												},
												failure: function(fp, o){
													var pesanNya;
													if(o.result.message != undefined){
														pesanNya = o.result.message;
													}else{
														pesanNya = lang('Connection error');
													}
													Ext.MessageBox.show({
														title: 'Error',
														msg: pesanNya,
														buttons: Ext.MessageBox.OK,
														animateTarget: 'mb9',
														icon: 'ext-mb-error'
													});
												}
											});

										}else{
											Ext.MessageBox.show({
												title: 'Attention',
												msg: lang('Form not valid yet'),
												buttons: Ext.MessageBox.OK,
												animateTarget: 'mb9',
												icon: 'ext-mb-info'
											});
										}  
									}
							    },
								{
								text: lang('Close'),
								margin: '5px',
								scale: 'large',
								ui: 's-button',
								cls: 's-grey',
									handler: function() {
										//tutup popup
										RecomenAndSelectiionPilihan.close();  
									}
								}
						   ] 
			}] 
});
 
function mydiff(date1,date2,interval) {
    var second=1000, minute=second*60, hour=minute*60, day=hour*24, week=day*7;
    date1 = new Date(date1);
    date2 = new Date(date2);
    var timediff = date2 - date1;
    if (isNaN(timediff)) return NaN;
    switch (interval) {
        case "years": return date2.getFullYear() - date1.getFullYear();
        case "months": return (
            ( date2.getFullYear() * 12 + date2.getMonth() )
            -
            ( date1.getFullYear() * 12 + date1.getMonth() )
        );
        case "weeks"  : return Math.floor(timediff / week);
        case "days"   : return Math.floor(timediff / day) == 0 ? 1 : Math.floor(timediff / day) + 1 ; 
        case "hours"  : return Math.floor(timediff / hour); 
        case "minutes": return Math.floor(timediff / minute);
        case "seconds": return Math.floor(timediff / second);
        default: return undefined;
    }
}   
function getEnabledButtonParticipation(){
	Ext.getCmp('Koltiva.view.NewSocialization.MainForm-Form-BtnAttdenceListParticipant').setDisabled(false);
	Ext.getCmp('Koltiva.view.NewSocialization.MainForm-Form-BtnAttdenceParticipant').setDisabled(false);
	Ext.getCmp('Koltiva.view.NewSocialization.MainForm-Form-BtnAddParticipant').setDisabled(false); 
	Ext.getCmp('Koltiva.view.NewSocialization.MainForm-Form-BtnAddExistingFarmer').setDisabled(false); 
	Ext.getCmp('Koltiva.view.NewSocialization.MainForm-Form-BtnAddStaff').setDisabled(false);
	Ext.getCmp('Koltiva.view.NewSocialization.MainForm-Form-BtnAttdenceStaff').setDisabled(false); 
	Ext.getCmp('Recommendation_From_FA').setDisabled(false);  
	Ext.getCmp('delete_socializ_participant').setDisabled(false);
}  
function getDisabledButtonParticipation(){
	Ext.getCmp('Koltiva.view.NewSocialization.MainForm-Form-BtnAttdenceListParticipant').setDisabled(false);
	Ext.getCmp('Koltiva.view.NewSocialization.MainForm-Form-BtnAttdenceParticipant').setDisabled(true);
	Ext.getCmp('Koltiva.view.NewSocialization.MainForm-Form-BtnAddParticipant').setDisabled(true);
	Ext.getCmp('Koltiva.view.NewSocialization.MainForm-Form-BtnAddExistingFarmer').setDisabled(true); 	
	Ext.getCmp('Koltiva.view.NewSocialization.MainForm-Form-BtnAddStaff').setDisabled(true);
	Ext.getCmp('Koltiva.view.NewSocialization.MainForm-Form-BtnAttdenceStaff').setDisabled(true);  
	Ext.getCmp('Recommendation_From_FA').setDisabled(true);  
	Ext.getCmp('delete_socializ_participant').setDisabled(true);
	Ext.getCmp('Koltiva.view.NewSocialization.MainForm-btnSave').setVisible(false);
	
}
function getCBSValue(cb, nameIn, nameOut){     
     try{
          var r = cb.getStore().find(nameIn,cb.getValue());
          return cb.getStore().getAt(r).get(nameOut);
     }
     catch(err){
          return'error';
     }
}
 
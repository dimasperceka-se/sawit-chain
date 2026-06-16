
var cmbPropinsiGeneral = Ext.create('Koltiva.store.ComboGeneral.ComboProvince');
var cmbDistrictGeneral = Ext.create('Koltiva.store.ComboGeneral.ComboDistrict');
var cmbSubDistrictGeneral = Ext.create('Koltiva.store.ComboGeneral.ComboSubDistrict');
var cmbVillageGeneral = Ext.create('Koltiva.store.ComboGeneral.ComboVillage');
var cmbFarmerGroupGeneral = Ext.create('Koltiva.store.ComboGeneral.CmbFarmerGroup');
var cmbCertHolderGeneral = Ext.create('Koltiva.store.ComboGeneral.CmbCertHolderGeneralCargill');
var cmbCertProgramsGeneral = Ext.create('Koltiva.store.ComboGeneral.CmbCertProgramsGeneral');
cmbCertProgramsGeneral.load();
var cmbImsEventGeneral = Ext.create('Koltiva.store.ComboGeneral.CmbImsEventGeneral');
var cmbFarmerTypeGeneral = Ext.create('Koltiva.store.ComboGeneral.CmbFarmerTypeGeneral');


/////////////////////////////////////////////////PARTTICIPANT/////////////////////////////////////////////////////////////////
	var storeGridMain = Ext.create('Koltiva.store.application_form.GridMain');
	var storeGridParticipantHistory = Ext.create('GridMainParticipantHistory');
	var Grid_Participant_history =
	{
				xtype: 'grid',
				id: 'Koltiva.view.application_form.GridMainParticipantHistory-FormDetail',
				style: 'border:1px solid #CCC;margin-top:4px;',
				cls: 'Sfr_GridNew',
				minHeight:125,
				loadMask: true,
				store: storeGridParticipantHistory,
				columns: [
						{
							text: lang('Applicant ID'),
							flex:1,
							dataIndex: 'DisplayID',
						},
						{
							text: lang('Applicant Name'),
							flex:1,
							dataIndex: 'Fullname',
						},
						{
							text: lang('Farmer Group'),
							flex:1,
							dataIndex: 'GroupName',
						},
						{
							text: lang('Participate in Socialization'),
							flex:1,
							dataIndex: 'ParticipateInSocializationStatus_check',
						},
						{
							text: lang('Recommendation'),
							flex:1,
							dataIndex: 'Recommendation',
						},
						{
							text: lang('Selection Status'),
							dataIndex: 'SelectionStatus_selected',
							flex:1,
						},
						{
							text: lang('Remarks'),
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

					}
				},
				dockedItems: [
				{
				xtype: 'pagingtoolbar', store: storeGridParticipantHistory, dock: 'bottom', beforePageText: '', hiddenInputItem: true, afterPageText : '', displayInfo: false,
					listeners :{
							beforechange: function() {
								var ApplicantID = Ext.getCmp('Koltiva.view.application_form.WinRegisterAppForm-Form-ApplicantID').getValue();
								storeGridParticipantHistory.setStoreVar({
									ApplicantID: ApplicantID
								});
								storeGridParticipantHistory.load();
							}
					}
				},
				{
					xtype: 'toolbar',
					items: [
						{
							icon: varjs.config.base_url + 'images/icons/silk/page_excel.png',
							text: lang('Export All'),
							handler: function() {
								var ApplicantID = Ext.getCmp('Koltiva.view.application_form.WinRegisterAppForm-Form-ApplicantID').getValue();
								url = m_api + '/application_form/application_store/print_logevent/'+ApplicantID;
								if(window.open(url, 'cetak', "height=200,width=200")){
									Ext.MessageBox.hide();
								}
							} 	
						}
					]
				} 
		   ],

	};

Ext.define('Koltiva.view.application_form.WinRegisterAppForm' ,{
    extend: 'Ext.window.Window',
    id: 'Koltiva.view.application_form.WinRegisterAppForm',
    title: lang('Application Form'),
    closable: true,
    modal: true,
    closeAction: 'destroy',
    width: '90%',
    height: '90%',
    overflowY: 'auto',
    viewVar: false,
    initComponent: function() {
        var thisObj = this;
		var storeGridMain = Ext.create('Koltiva.store.application_form.GridMain');
		thisObj.items = [{
            xtype: 'form',
            id: 'Koltiva.view.application_form.WinRegisterAppForm-Form',
            padding:'5 25 5 8',
            items:
			//START
			[
			{
				xtype: 'tabpanel',
				flex: 1,
				margin: 2,
				activeTab: 0,
				plain: true,
				items:[
					{
					   xtype:'panel',
					   title : lang('Applicant Basic Data'),
					   items:[
					   {
					       layout: 'column', border: false, items:
						    [
								//COLOUMN BASIC DATA START
								{
									columnWidth: '.5',
									padding:'5 25 5 8',
									layout:'form',
									items:[{
											xtype: 'hiddenfield',
											id: 'Koltiva.view.application_form.WinRegisterAppForm-Form-ApplicantID',
											name: 'Koltiva.view.application_form.WinRegisterAppForm-Form-ApplicantID'
										},
										{
											xtype: 'textfield',
											fieldLabel: lang('Applicant ID'),
											labelWidth : 200,
											readOnly : true,
											id: 'Koltiva.view.application_form.WinRegisterAppForm-Form-DisplayID',
											name: 'Koltiva.view.application_form.WinRegisterAppForm-Form-DisplayID'
										},
										{
											xtype: 'textfield',
											fieldLabel: lang('Applicant Name')+' *)',
											labelWidth : 200,
											id: 'Koltiva.view.application_form.WinRegisterAppForm-Form-Fullname',
											name: 'Koltiva.view.application_form.WinRegisterAppForm-Form-Fullname',
											allowBlank: false
										},
										{
											xtype: 'textfield',
											fieldLabel: lang('No KTP') + ' *)',
											labelWidth : 200,
											id: 'Koltiva.view.application_form.WinRegisterAppForm-Form-NIN',
											name: 'Koltiva.view.application_form.WinRegisterAppForm-Form-NIN',
											allowBlank: true
										},
										{
											xtype: 'datefield',
											fieldLabel: lang('Date Collection')+' *)',
											labelWidth : 200,
											format: 'Y-m-d H:i:s',
											id: 'Koltiva.view.application_form.WinRegisterAppForm-Form-DateCollection',
											name: 'Koltiva.view.application_form.WinRegisterAppForm-Form-DateCollection',
											allowBlank: false
										}, 
										{
											xtype: 'combobox',
											fieldLabel: lang('Province') +' *)',
											labelWidth: 200,
											store: cmbPropinsiGeneral,
											queryMode: 'local',
											displayField: 'label',
											valueField: 'id',
											id: 'Koltiva.view.application_form.WinRegisterAppForm-Form-ProvinceID',
											name: 'Koltiva.view.application_form.WinRegisterAppForm-Form-ProvinceID',
											allowBlank: false,
											listeners: {
												change: function(cb, nv, ov) {
													cmbDistrictGeneral.setStoreVar({
														ProvinceID: nv
													});
													cmbDistrictGeneral.load();
												},
												select :function(cb, nv, ov) {
													Ext.getCmp('Koltiva.view.application_form.WinRegisterAppForm-Form-DistrictID').setValue('');
													Ext.getCmp('Koltiva.view.application_form.WinRegisterAppForm-Form-SubDistrictID').setValue('');
													Ext.getCmp('Koltiva.view.application_form.WinRegisterAppForm-Form-Form-VillageID').setValue('');
													Ext.getCmp('Koltiva.view.application_form.WinRegisterAppForm-Form-Form-VillageName').setValue('');
												}
											}
										},{
											xtype: 'combobox',
											fieldLabel: lang('District') +' *)',
											labelWidth: 200,
											store: cmbDistrictGeneral,
											queryMode: 'local',
											displayField: 'label',
											valueField: 'id',
											id: 'Koltiva.view.application_form.WinRegisterAppForm-Form-DistrictID',
											name: 'Koltiva.view.application_form.WinRegisterAppForm-Form-DistrictID',
											allowBlank: true,
											listeners: {
												change: function(cb, nv, ov) {
													cmbSubDistrictGeneral.setStoreVar({
														DistrictID: nv
													});
													cmbSubDistrictGeneral.load();
												},
												select :function(cb, nv, ov) {
													Ext.getCmp('Koltiva.view.application_form.WinRegisterAppForm-Form-SubDistrictID').setValue('');
												}
											}
										},
										{
											xtype: 'combobox',
											fieldLabel: lang('Sub District') +' *)',
											labelWidth: 200,
											store: cmbSubDistrictGeneral,
											queryMode: 'local',
											displayField: 'label',
											valueField: 'id',
											id: 'Koltiva.view.application_form.WinRegisterAppForm-Form-SubDistrictID',
											name: 'Koltiva.view.application_form.WinRegisterAppForm-Form-SubDistrictID',
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
													Ext.getCmp('Koltiva.view.application_form.WinRegisterAppForm-Form-VillageID').setValue('');
													Ext.getCmp('Koltiva.view.application_form.WinRegisterAppForm-Form-VillageName').setValue('');
												}
											}
										},
										{
											xtype: 'combobox',
											fieldLabel: lang('Village') +' *)',
											labelWidth: 200,
											store: cmbVillageGeneral,
											queryMode: 'local',
											displayField: 'label',
											valueField: 'id',
											id: 'Koltiva.view.application_form.WinRegisterAppForm-Form-VillageID',
											name: 'Koltiva.view.application_form.WinRegisterAppForm-Form-VillageID',
											allowBlank: false,
											listeners: {
												change: function(cb, nv, ov) {
													cmbFarmerGroupGeneral.setStoreVar({
														VillageID: nv
													});
													cmbFarmerGroupGeneral.load();
													Ext.getCmp('Koltiva.view.application_form.WinRegisterAppForm-Form-GroupName').setValue('');


													var item =  getCBSValue (cb, 'id', 'label');
													Ext.getCmp('Koltiva.view.application_form.WinRegisterAppForm-Form-VillageName').setValue(item);
												}
											}
										},
										{
											xtype: 'hiddenfield',
											labelWidth : 200,
											allowBlank: true,
											id: 'Koltiva.view.application_form.WinRegisterAppForm-Form-VillageName',
											name: 'Koltiva.view.application_form.WinRegisterAppForm-Form-VillageName'
										},
										{
											xtype: 'textfield',
											fieldLabel: lang('Address') ,
											labelWidth : 200,
											id: 'Koltiva.view.application_form.WinRegisterAppForm-Form-Address',
											name: 'Koltiva.view.application_form.WinRegisterAppForm-Form-Address'
										},
										{
											xtype: 'combobox',
											fieldLabel: lang('Nama Farmer Group'),
											labelWidth: 200,
											store: cmbFarmerGroupGeneral,
											queryMode: 'local',
											displayField: 'label',
											valueField: 'id',
											id: 'Koltiva.view.application_form.WinRegisterAppForm-Form-CPGid',
											name: 'Koltiva.view.application_form.WinRegisterAppForm-Form-CPGid',
											allowBlank: false,
											listeners: {
												change: function(cb, nv, ov) {

												}
											}
										},
										{
											xtype: 'textfield',
											fieldLabel: lang('Suggestion for the new farmer group'),
											labelWidth : 200,
											id: 'Koltiva.view.application_form.WinRegisterAppForm-Form-GroupName',
											name: 'Koltiva.view.application_form.WinRegisterAppForm-Form-GroupName',
											allowBlank: true
										},
										{
											layout: 'hbox',
											border: false,
											items: [{
												xtype: 'panel',
												items: [
												{
													xtype: 'datefield',
													labelWidth: 200,
													fieldLabel: lang('Tanggal Lahir'),
													id: 'Koltiva.view.application_form.WinRegisterAppForm-Form-DateOfBirth',
													name: 'Koltiva.view.application_form.WinRegisterAppForm-Form-DateOfBirth',
													allowBlank: true,
													format: 'Y-m-d',
													listeners: {
														change: function(elm, nv, ov) {
															if (nv) {
																Ext.getCmp('Koltiva.view.application_form.WinRegisterAppForm-Form-Age').setValue(getAge(nv));
															}
														}
													}
												},
												]
											}]
										},
										{
											xtype: 'textfield',
											fieldLabel: lang('Age'),
											readOnly:true,
											id: 'Koltiva.view.application_form.WinRegisterAppForm-Form-Age',
											name: 'Koltiva.view.application_form.WinRegisterAppForm-Form-Age',
											allowBlank: true
										},
										{
											fieldLabel: lang('Jenis Kelamin') + ' *)',
											xtype: 'radiogroup',
											width: '100%',
											allowBlank: false,
											id:'Koltiva.view.application_form.WinRegisterAppForm-Form-GroupGender',
											defaults: {xtype: "radio",name: 'Koltiva.view.application_form.WinRegisterAppForm-Form-Gender'},
											items: [{
												boxLabel: lang('Laki-laki'),
												inputValue: 'm'
											}, {
												boxLabel: lang('Perempuan'),
												inputValue: 'f'
											}]
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
										fieldLabel: lang('Certificated Programs'),
										labelWidth: 200,
										store: cmbCertProgramsGeneral,
										queryMode: 'local',
										displayField: 'label',
										valueField: 'id',
										id: 'Koltiva.view.application_form.WinRegisterAppForm-Form-CertProgID',
										name: 'Koltiva.view.application_form.WinRegisterAppForm-Form-CertProgID',
										allowBlank: true,
										listeners: {
											change: function(cb, nv, ov) {
												cmbCertHolderGeneral.setStoreVar({
													CertProgID: nv
												});
												cmbCertHolderGeneral.load();
											},
											select :function(cb, nv, ov) {
												Ext.getCmp('Koltiva.view.application_form.WinRegisterAppForm-Form-CertHolderID').setValue('');
												Ext.getCmp('Koltiva.view.application_form.WinRegisterAppForm-Form-IMSID').setValue('');
											}
										}
									},
									{
										xtype: 'combobox',
										fieldLabel: lang('Pemegang Sertifikat'),
										labelWidth: 200,
										store: cmbCertHolderGeneral,
										queryMode: 'local',
										displayField: 'label',
										valueField: 'id',
										id: 'Koltiva.view.application_form.WinRegisterAppForm-Form-CertHolderID',
										name: 'Koltiva.view.application_form.WinRegisterAppForm-Form-CertHolderID',
										allowBlank: true,
										listeners: {
											change: function(cb, nv, ov) {
												cmbImsEventGeneral.setStoreVar({
													CertHolderID: nv
												});
												cmbImsEventGeneral.load();
											},
											select :function(cb, nv, ov) {
												Ext.getCmp('Koltiva.view.application_form.WinRegisterAppForm-Form-IMSID').setValue('');
											}
										}
									},
									{
										xtype: 'combobox',
										fieldLabel: lang('IMS Event'),
										labelWidth: 200,
										store: cmbImsEventGeneral,
										queryMode: 'local',
										displayField: 'label',
										valueField: 'id',
										id: 'Koltiva.view.application_form.WinRegisterAppForm-Form-IMSID',
										name: 'Koltiva.view.application_form.WinRegisterAppForm-Form-IMSID',
										allowBlank: true,
										listeners: {
											change: function(cb, nv, ov) {
												var item =  getCBSValue (cb, 'id', 'IMSMasterID');
												Ext.getCmp('Koltiva.view.application_form.WinRegisterAppForm-Form-IMSMasterID').setValue(item); 
												//load farmer Type
												cmbFarmerTypeGeneral.setStoreVar({
												IMSID: cb.getValue()
												});
												cmbFarmerTypeGeneral.load();
												
											},
											select :function(cb, nv, ov) {
												Ext.getCmp('Koltiva.view.application_form.WinRegisterAppForm-Form-FarmertypeID').setValue("4");	
											}
										}
									},
									{
										xtype: 'combobox',
										fieldLabel: lang('Farmer Jenis'),
										labelWidth: 200,
										store: cmbFarmerTypeGeneral,
										queryMode: 'local',
										displayField: 'label',
										valueField: 'id',
										id: 'Koltiva.view.application_form.WinRegisterAppForm-Form-FarmertypeID',
										name: 'Koltiva.view.application_form.WinRegisterAppForm-Form-FarmertypeID',
										allowBlank: true,
										listeners: {
											change: function(cb, nv, ov) {
												var item =  getCBSValue (cb, 'id', 'PartnerID');
												Ext.getCmp('Koltiva.view.application_form.WinRegisterAppForm-Form-PatnerID').setValue(item);
											}
										}
									},
									{
										xtype: 'hiddenfield',
										labelWidth : 200,
										id: 'Koltiva.view.application_form.WinRegisterAppForm-Form-PatnerID',
										name: 'Koltiva.view.application_form.WinRegisterAppForm-Form-PatnerID',
										allowBlank: true
									},
									{
										xtype: 'hiddenfield',
										labelWidth : 200,
										id: 'Koltiva.view.application_form.WinRegisterAppForm-Form-IMSMasterID',
										name: 'Koltiva.view.application_form.WinRegisterAppForm-Form-IMSMasterID',
										allowBlank: true
									},
									{
										xtype: 'textfield',
										fieldLabel: lang('Number Of Farms'),
										labelWidth: 200,
										width : 80,
										id: 'Koltiva.view.application_form.WinRegisterAppForm-Form-NrOfFarm',
										name: 'Koltiva.view.application_form.WinRegisterAppForm-Form-NrOfFarm',
										allowBlank: true
									},
									{
										xtype: 'textfield',
										fieldLabel: lang('Size of Farms'),
										emptyText : lang('in Hectare (a)'),
										labelWidth: 200,
										width : 80,
										id: 'Koltiva.view.application_form.WinRegisterAppForm-Form-HectareOfFarm',
										name: 'Koltiva.view.application_form.WinRegisterAppForm-Form-HectareOfFarm',
										allowBlank: true
									},
									{
										xtype: 'textfield',
										fieldLabel: lang('Estimated Last year Harvest'),
										labelWidth: 200,
										emptyText : lang('in Kg (a)'),
										id: 'Koltiva.view.application_form.WinRegisterAppForm-Form-LastYearHarvest',
										name: 'Koltiva.view.application_form.WinRegisterAppForm-Form-LastYearHarvest',
										allowBlank: true
									},
									{
										xtype: 'textfield',
										fieldLabel: lang('Number Of Production Trees'),
										labelWidth: 200,
										emptyText : lang('in Tree (a)'),
										id: 'Koltiva.view.application_form.WinRegisterAppForm-Form-NrOfProductiveTrees',
										name: 'Koltiva.view.application_form.WinRegisterAppForm-Form-NrOfProductiveTrees',
										allowBlank: true
									},
									{
										xtype: 'textfield',
										fieldLabel: lang('Latitude(Dec)'),
										labelWidth: 200,
										id: 'Koltiva.view.application_form.WinRegisterAppForm-Form-Latitude',
										name: 'Koltiva.view.application_form.WinRegisterAppForm-Form-Latitude',
										allowBlank: true
									} ,
									{
										xtype: 'textfield',
										fieldLabel: lang('Latitude(Deg)'),
										labelWidth: 200,
										id: 'Koltiva.view.application_form.WinRegisterAppForm-Form-Longitude',
										name: 'Koltiva.view.application_form.WinRegisterAppForm-Form-Longitude',
										allowBlank: true
									},
									{
										fieldLabel: lang('Application status')+' *)',
										xtype: 'radiogroup',
										width: '100%',
										allowBlank: false,
										id:'Koltiva.view.application_form.WinRegisterAppForm-Form-GroupStatus',
										defaults: {xtype: "radio",name: 'Koltiva.view.application_form.WinRegisterAppForm-Form-ActiveStatus'},
										items: [{
											boxLabel: lang('Active'),
											inputValue: 'active',
											checked: true,
											listeners : {
											  change : function(a)
											  {
												  if(a.getValue() == true){ 
												     Ext.getCmp('Koltiva.view.application_form.WinRegisterAppForm-Form-InactiveReason').setDisabled(true);
													 Ext.getCmp('Koltiva.view.application_form.WinRegisterAppForm-Form-InactiveRemarks').setDisabled(true);
												  }
											  }
											}
										}, {
											boxLabel: lang('Inactive'),
											inputValue: 'inactive',
											listeners : {
											  change : function(a)
											  {
												  if(a.getValue() == true){ 
												     Ext.getCmp('Koltiva.view.application_form.WinRegisterAppForm-Form-InactiveReason').setDisabled(false);
													 Ext.getCmp('Koltiva.view.application_form.WinRegisterAppForm-Form-InactiveRemarks').setDisabled(false);
												  }
											  }
											}
										}]
									},
									{
										xtype: 'combobox',
										fieldLabel: lang('Inactive Reason'),
										labelWidth: 200,
										queryMode: 'local',
										displayField: 'text',
										valueField: 'value',
										store : new Ext.data.SimpleStore({
											data : [['1', lang('Die')], ['2', lang('moved/left the area')],
													['3', lang('stop farming')]],
											fields : ['value', 'text']
										}),
										triggerAction : 'all',
										disabled:'true',
										id: 'Koltiva.view.application_form.WinRegisterAppForm-Form-InactiveReason',
										name: 'Koltiva.view.application_form.WinRegisterAppForm-Form-InactiveReason',
										allowBlank: true,
										listeners: {
											change: function(cb, nv, ov) {

											}
										}
									},
									{
										xtype: 'textarea',
										fieldLabel: lang('Remark'),
										labelWidth: 200,
										disabled:'true',
										id: 'Koltiva.view.application_form.WinRegisterAppForm-Form-InactiveRemarks',
										name: 'Koltiva.view.application_form.WinRegisterAppForm-Form-InactiveRemarks',
										allowBlank: true
									} 
									]
								//COLOUMN BASIC DATA END
								}
							]///end
					    }
						]
					},{
						xtype:'panel',
						title : lang('File & Photo'),
						items:[
						{
							layout: 'column', border: false, items:
							[
								 //COLOUMN BASIC DATA START
								{
									columnWidth: '.33',
									padding:'5 25 5 8',
									layout:'form',
									items:[{
										xtype: 'image',
										id: 'Koltiva.view.application_form.WinRegisterAppForm-Form-Photo',
										height:'200px',
										src: m_api_base_url + '/assets/images/farmer-default.png'
									},{
										xtype: 'fileuploadfield',
										fieldLabel: lang('Contract'),
										labelAlign: 'top',
										id: 'Koltiva.view.application_form.WinRegisterAppForm-Form-PhotoInput',
										name: 'Koltiva.view.application_form.WinRegisterAppForm-Form-PhotoInput',
										buttonText: 'Browse',
										listeners: {
											'change': function (fb, v) {
												Ext.getCmp('Koltiva.view.application_form.WinRegisterAppForm-Form').submit({
													url: m_api + '/application_form/application_store/photo_applicant',
													clientValidation: false,
													params: {
														opsiDisplay: thisObj.viewVar.opsiDisplay,
														ApplicantID: Ext.getCmp('Koltiva.view.application_form.WinRegisterAppForm-Form-ApplicantID').getValue()
													},
													waitMsg: lang('Sending Photo')+'...',
													success: function (fp, o) {
														Ext.getCmp('Koltiva.view.application_form.WinRegisterAppForm-Form-Photo').setSrc(o.result.file);
														Ext.getCmp('Koltiva.view.application_form.WinRegisterAppForm-Form-PhotoOld').setValue(o.result.filepath);
													}
												});
											}
										}
									},{
										xtype: 'textfield',
										id: 'Koltiva.view.application_form.WinRegisterAppForm-Form-PhotoOld',
										name: 'Koltiva.view.application_form.WinRegisterAppForm-Form-PhotoOld',
										inputType: 'hidden'
									}]
								},{
									columnWidth: '.33',
									padding:'5 25 5 8',
									layout:'form',
									items:[{
										xtype: 'image',
										id: 'Koltiva.view.application_form.WinRegisterAppForm-Form-CertificationContractSign',
										height:'200px',
										src: m_api_base_url + '/assets/images/farmer-default.png'
									},{
										xtype: 'fileuploadfield',
										fieldLabel: lang('Signature'),
										labelAlign: 'top',
										id: 'Koltiva.view.application_form.WinRegisterAppForm-Form-CertificationContractSignInput',
										name: 'Koltiva.view.application_form.WinRegisterAppForm-Form-CertificationContractSignInput',
										buttonText: 'Browse',
										listeners: {
											'change': function (fb, v) {
												Ext.getCmp('Koltiva.view.application_form.WinRegisterAppForm-Form').submit({
													url: m_api + '/application_form/application_store/contract_applicant',
													clientValidation: false,
													params: {
														opsiDisplay: thisObj.viewVar.opsiDisplay,
														ApplicantID: Ext.getCmp('Koltiva.view.application_form.WinRegisterAppForm-Form-ApplicantID').getValue()
													},
													waitMsg: lang('Sending Certification Contract Sign')+'...',
													success: function (fp, o) {
														Ext.getCmp('Koltiva.view.application_form.WinRegisterAppForm-Form-CertificationContractSign').setSrc(o.result.file);
														Ext.getCmp('Koltiva.view.application_form.WinRegisterAppForm-Form-CertificationContractSignOld').setValue(o.result.filepath);
													}
												});
											}
										}
									},{
										xtype: 'textfield',
										id: 'Koltiva.view.application_form.WinRegisterAppForm-Form-CertificationContractSignOld',
										name: 'Koltiva.view.application_form.WinRegisterAppForm-Form-CertificationContractSignOld',
										inputType: 'hidden'
									}]
								},{
									columnWidth: '.33',
									padding:'5 25 5 8',
									layout:'form',
									items:[{
										xtype: 'image',
										id: 'Koltiva.view.application_form.WinRegisterAppForm-Form-ApplicantPhoto',
										height:'200px',
										src: m_api_base_url + '/assets/images/farmer-default.png'
									},{
										xtype: 'fileuploadfield',
										fieldLabel: lang('Applicant Photo'),
										labelAlign: 'top',
										id: 'Koltiva.view.application_form.WinRegisterAppForm-Form-ApplicantPhotoInput',
										name: 'Koltiva.view.application_form.WinRegisterAppForm-Form-ApplicantPhotoInput',
										buttonText: 'Browse',
										listeners: {
											'change': function (fb, v) {
												Ext.getCmp('Koltiva.view.application_form.WinRegisterAppForm-Form').submit({
													url: m_api + '/application_form/application_store/applicant_photo',
													clientValidation: false,
													params: {
														opsiDisplay: thisObj.viewVar.opsiDisplay,
														ApplicantID: Ext.getCmp('Koltiva.view.application_form.WinRegisterAppForm-Form-ApplicantID').getValue()
													},
													waitMsg: lang('Sending Certification Contract Sign')+'...',
													success: function (fp, o) {
														Ext.getCmp('Koltiva.view.application_form.WinRegisterAppForm-Form-ApplicantPhoto').setSrc(o.result.file);
														Ext.getCmp('Koltiva.view.application_form.WinRegisterAppForm-Form-ApplicantPhotoOld').setValue(o.result.filepath);
													}
												});
											}
										}
									},{
										xtype: 'textfield',
										id: 'Koltiva.view.application_form.WinRegisterAppForm-Form-ApplicantPhotoOld',
										name: 'Koltiva.view.application_form.WinRegisterAppForm-Form-ApplicantPhotoOld',
										inputType: 'hidden'
									}]
								}
							]
						}]
					}
				  ]

			},
			{
				xtype: 'tabpanel',
				id : 'tabpanel_farmer_recomendation_history',
				flex: 1,
				margin: 2,
				activeTab: 0,
				plain: true,
				items:[
					{
					   xtype:'panel',
					   title : lang('History'),
					   items:[Grid_Participant_history]
					}
				  ]

			}]//END
		}];


		thisObj.buttons = [{
            id: 'Koltiva.view.application_form.WinRegisterAppForm-Form-btnSave',
            text: lang('Save'),
            margin: '5px',
            scale: 'large',
            ui: 's-button',
            cls: 's-blue',
            handler: function() {
                var formNya = Ext.getCmp('Koltiva.view.application_form.WinRegisterAppForm-Form').getForm();
				if (formNya.isValid()) {

                    formNya.submit({
                        url: m_api + '/application_form/application_store/savedata',
                        method:'POST',
                        waitMsg: 'Saving data...',
                        success: function(fp, o) {
                            Ext.MessageBox.show({
                                title: lang('Information'),
                                msg: lang('Data saved'),
                                buttons: Ext.MessageBox.OK,
                                animateTarget: 'mb9',
                                icon: 'ext-mb-success'
                            });

                            //form reset
                            formNya.reset();
                            //refresh store yg manggil
							Ext.getCmp('Koltiva.view.application_form.GridMainAppForm-gridMainGrid').store.reload();

                            //tutup popup
                            thisObj.close();
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
                        title: lang('Attention'),
                        msg: lang('Form not valid yet'),
                        buttons: Ext.MessageBox.OK,
                        animateTarget: 'mb9',
                        icon: 'ext-mb-info'
                    });
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
            }
        }];

		this.callParent(arguments);
	},
	listeners: {
        afterRender: function(){
            var thisObj = this;

			if(thisObj.viewVar.opsiDisplay == 'insert'){
                AutoGenerateID();
				Ext.getCmp('tabpanel_farmer_recomendation_history').hide(); 
            }

			if(thisObj.viewVar.opsiDisplay == 'update' || thisObj.viewVar.opsiDisplay == 'view' ){

				if(thisObj.viewVar.opsiDisplay == 'view')
				{
					Ext.getCmp('Koltiva.view.application_form.WinRegisterAppForm-Form-btnSave').hide();
					getDisabledInputBasicForm()
				}


				var formNya = Ext.getCmp('Koltiva.view.application_form.WinRegisterAppForm-Form').getForm();
				//load formnya
                Ext.Ajax.request({
                            url: m_api + '/application_form/application_store/loadappdata',
							method: 'GET',
							params: {
								ApplicantID: thisObj.viewVar.ApplicantID
							},
                            success: function (fp, o) {
                                var r = Ext.decode(fp.responseText);
								Ext.getCmp('Koltiva.view.application_form.WinRegisterAppForm-Form-ApplicantID').setValue(r.data.ApplicantID);
                                Ext.getCmp('Koltiva.view.application_form.WinRegisterAppForm-Form-DisplayID').setValue(r.data.DisplayID);
								Ext.getCmp('Koltiva.view.application_form.WinRegisterAppForm-Form-Fullname').setValue(r.data.Fullname);
								Ext.getCmp('Koltiva.view.application_form.WinRegisterAppForm-Form-NIN').setValue(r.data.NIN);
								
								Ext.getCmp('Koltiva.view.application_form.WinRegisterAppForm-Form-DateCollection').setValue(r.data.DateCollection);
								Ext.getCmp('Koltiva.view.application_form.WinRegisterAppForm-Form-ProvinceID').setValue(r.data.ProvinceID);
								Ext.getCmp('Koltiva.view.application_form.WinRegisterAppForm-Form-DistrictID').setValue(r.data.DistrictID);
								cmbSubDistrictGeneral.setStoreVar({ DistrictID: r.data.DistrictID }); cmbSubDistrictGeneral.load();
								Ext.getCmp('Koltiva.view.application_form.WinRegisterAppForm-Form-SubDistrictID').setValue(r.data.SubDistrictID);
								Ext.getCmp('Koltiva.view.application_form.WinRegisterAppForm-Form-VillageID').setValue(r.data.VillageID);
								Ext.getCmp('Koltiva.view.application_form.WinRegisterAppForm-Form-VillageName').setValue(r.data.VillageName);
								Ext.getCmp('Koltiva.view.application_form.WinRegisterAppForm-Form-Address').setValue(r.data.Address);
								Ext.getCmp('Koltiva.view.application_form.WinRegisterAppForm-Form-CPGid').setValue(r.data.CPGid);
								Ext.getCmp('Koltiva.view.application_form.WinRegisterAppForm-Form-GroupName').setValue(r.data.NewGroupName);
								Ext.getCmp('Koltiva.view.application_form.WinRegisterAppForm-Form-DateOfBirth').setValue(r.data.DateOfBirth);
								Ext.getCmp('Koltiva.view.application_form.WinRegisterAppForm-Form-Age').setValue(r.data.Age);

								Ext.getCmp('Koltiva.view.application_form.WinRegisterAppForm-Form-CertHolderID').setValue(r.data.CertHolderID);
								Ext.getCmp('Koltiva.view.application_form.WinRegisterAppForm-Form-CertProgID').setValue(r.data.CertProgID);
								Ext.getCmp('Koltiva.view.application_form.WinRegisterAppForm-Form-IMSMasterID').setValue(r.data.IMSMasterID);
								Ext.getCmp('Koltiva.view.application_form.WinRegisterAppForm-Form-IMSID').setValue(r.data.IMSID);
								Ext.getCmp('Koltiva.view.application_form.WinRegisterAppForm-Form-FarmertypeID').setValue(r.data.FarmertypeID);
								Ext.getCmp('Koltiva.view.application_form.WinRegisterAppForm-Form-NrOfFarm').setValue(r.data.NrOfFarm);
								Ext.getCmp('Koltiva.view.application_form.WinRegisterAppForm-Form-HectareOfFarm').setValue(r.data.HectareOfFarm);
								Ext.getCmp('Koltiva.view.application_form.WinRegisterAppForm-Form-LastYearHarvest').setValue(r.data.LastYearHarvest);
								Ext.getCmp('Koltiva.view.application_form.WinRegisterAppForm-Form-NrOfProductiveTrees').setValue(r.data.NrOfProductiveTrees);
								Ext.getCmp('Koltiva.view.application_form.WinRegisterAppForm-Form-Latitude').setValue(r.data.Latitude);
								Ext.getCmp('Koltiva.view.application_form.WinRegisterAppForm-Form-Longitude').setValue(r.data.Longitude);
								Ext.getCmp('Koltiva.view.application_form.WinRegisterAppForm-Form-InactiveReason').setValue(r.data.InactiveReason);
								Ext.getCmp('Koltiva.view.application_form.WinRegisterAppForm-Form-InactiveRemarks').setValue(r.data.InactiveRemarks);

								Ext.getCmp('Koltiva.view.application_form.WinRegisterAppForm-Form-Photo').setSrc(r.data.PhotoSrc);
								Ext.getCmp('Koltiva.view.application_form.WinRegisterAppForm-Form-PhotoOld').setValue(r.data.PhotoSrcPath);

								Ext.getCmp('Koltiva.view.application_form.WinRegisterAppForm-Form-CertificationContractSign').setSrc(r.data.CertificationContractSignSrc);
								Ext.getCmp('Koltiva.view.application_form.WinRegisterAppForm-Form-CertificationContractSignOld').setValue(r.data.CertificationContractSignSrcPath);

								Ext.getCmp('Koltiva.view.application_form.WinRegisterAppForm-Form-ApplicantPhoto').setSrc(r.data.ApplicantPhotoSrc);
								Ext.getCmp('Koltiva.view.application_form.WinRegisterAppForm-Form-ApplicantPhotoOld').setValue(r.data.ApplicantPhotoSrcPath);

								var radios = Ext.getCmp("Koltiva.view.application_form.WinRegisterAppForm-Form-GroupStatus");
								radios.setValue({'Koltiva.view.application_form.WinRegisterAppForm-Form-ActiveStatus': r.data.ActiveStatus }); 
								
								var radios2 = Ext.getCmp("Koltiva.view.application_form.WinRegisterAppForm-Form-GroupGender"); 
								radios2.setValue({'Koltiva.view.application_form.WinRegisterAppForm-Form-Gender': r.data.Gender }); 

								storeGridParticipantHistory.setStoreVar({
									ApplicantID: r.data.ApplicantID
								});
								storeGridParticipantHistory.load();
                            }
                        });


			}
		}
	}

});

function getCBSValue(cb, nameIn, nameOut){
     try{
          var r = cb.getStore().find(nameIn,cb.getValue());
          return cb.getStore().getAt(r).get(nameOut);
     }
     catch(err){
          return'error';
     }
}

function getAge(dateString)
{
    var today = new Date();
    var birthDate = new Date(dateString);
    var age = today.getFullYear() - birthDate.getFullYear();
    var m = today.getMonth() - birthDate.getMonth();
    if (m < 0 || (m === 0 && today.getDate() < birthDate.getDate()))
    {
        age--;
    }
    return age;
}

function AutoGenerateID(){
	Ext.Ajax.request({
		url: m_api + '/application_form/application_store/appid',
		method: "POST" ,
		scope: this,
		success: function (result) {
			var response = Ext.decode(result.responseText);
			Ext.getCmp('Koltiva.view.application_form.WinRegisterAppForm-Form-DisplayID').setValue(response.data);
			//alert(response.data)
		},
		failure: function (result) {

		}
	});
}


function getDisabledInputBasicForm()
{
	Ext.getCmp('Koltiva.view.application_form.WinRegisterAppForm-Form-DisplayID').setReadOnly(true);
	Ext.getCmp('Koltiva.view.application_form.WinRegisterAppForm-Form-Fullname').setReadOnly(true);
	Ext.getCmp('Koltiva.view.application_form.WinRegisterAppForm-Form-NIN').setReadOnly(true);
	Ext.getCmp('Koltiva.view.application_form.WinRegisterAppForm-Form-DateCollection').setReadOnly(true);
	Ext.getCmp('Koltiva.view.application_form.WinRegisterAppForm-Form-DateOfBirth').setReadOnly(true);
	Ext.getCmp('Koltiva.view.application_form.WinRegisterAppForm-Form-GroupGender').setReadOnly(true);
	Ext.getCmp('Koltiva.view.application_form.WinRegisterAppForm-Form-ProvinceID').setReadOnly(true);
	Ext.getCmp('Koltiva.view.application_form.WinRegisterAppForm-Form-DistrictID').setReadOnly(true);
	Ext.getCmp('Koltiva.view.application_form.WinRegisterAppForm-Form-SubDistrictID').setReadOnly(true);
	Ext.getCmp('Koltiva.view.application_form.WinRegisterAppForm-Form-VillageID').setReadOnly(true);
	Ext.getCmp('Koltiva.view.application_form.WinRegisterAppForm-Form-Address').setReadOnly(true);
	Ext.getCmp('Koltiva.view.application_form.WinRegisterAppForm-Form-CPGid').setReadOnly(true);
	Ext.getCmp('Koltiva.view.application_form.WinRegisterAppForm-Form-GroupName').setReadOnly(true);
	Ext.getCmp('Koltiva.view.application_form.WinRegisterAppForm-Form-CertHolderID').setReadOnly(true);
	Ext.getCmp('Koltiva.view.application_form.WinRegisterAppForm-Form-CertProgID').setReadOnly(true);
	Ext.getCmp('Koltiva.view.application_form.WinRegisterAppForm-Form-IMSID').setReadOnly(true);
	Ext.getCmp('Koltiva.view.application_form.WinRegisterAppForm-Form-FarmertypeID').setReadOnly(true);
	Ext.getCmp('Koltiva.view.application_form.WinRegisterAppForm-Form-NrOfFarm').setReadOnly(true);
	Ext.getCmp('Koltiva.view.application_form.WinRegisterAppForm-Form-HectareOfFarm').setReadOnly(true);
	Ext.getCmp('Koltiva.view.application_form.WinRegisterAppForm-Form-LastYearHarvest').setReadOnly(true);
	Ext.getCmp('Koltiva.view.application_form.WinRegisterAppForm-Form-NrOfProductiveTrees').setReadOnly(true);
	Ext.getCmp('Koltiva.view.application_form.WinRegisterAppForm-Form-Latitude').setReadOnly(true);
	Ext.getCmp('Koltiva.view.application_form.WinRegisterAppForm-Form-Longitude').setReadOnly(true);
	Ext.getCmp('Koltiva.view.application_form.WinRegisterAppForm-Form-InactiveReason').setReadOnly(true);
	Ext.getCmp('Koltiva.view.application_form.WinRegisterAppForm-Form-GroupStatus').setReadOnly(true);
	Ext.getCmp('Koltiva.view.application_form.WinRegisterAppForm-Form-InactiveRemarks').setReadOnly(true);
}

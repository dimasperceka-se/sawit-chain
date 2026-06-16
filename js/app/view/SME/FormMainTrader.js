 

/*
    Param2 yg diperlukan ketika load View ini
    1. opsiDisplay
    2. viewVar
*/

// Define Variabel2 / Object2 / Fungsi yg diperlukan oleh view ini (begin)
    function checkImageExists(imageUrl, callBack) {
        var imageData = new Image();
        imageData.onload = function() {
            callBack(true);
        };
        imageData.onerror = function() {
            callBack(false);
        };
        imageData.src = imageUrl;
    }
// Define Variabel2 / Object2 / Fungsi yg diperlukan oleh view ini (end)


function init_map() {
    var lat = Ext.getCmp('Koltiva.view.SME.FormMainTrader-Latitude').getValue();
    var longs = Ext.getCmp('Koltiva.view.SME.FormMainTrader-Longitude').getValue();
	 
    if (Math.abs(lat) > 0 && Math.abs(longs)) {
        $('#map').gmap3({
            map: {
                options: {
                    center: [lat, longs],
                    zoom: 14,
                    //mapTypeControl: false,
                    panControl: true,
                    zoomControl: true,
                    //scaleControl: false,
                    streetViewControl: false,
                    rotateControl: false,
                    rotateControlOptions: false,
                    overviewMapControl: false,
                    OverviewMapControlOptions: false,
                    scrollwheel: true
                }
            },
            marker: {
                latLng:[lat, longs]
            }
        }); 
    }
}

Ext.define('Koltiva.view.SME.FormMainTrader' ,{
    extend: 'Ext.panel.Panel',
    id: 'Koltiva.view.SME.FormMainTrader',
    style:'padding:0 15px 15px 15px;margin:12px 0 0 0;',
    opsiDisplay: false,
    setOpsiDisplay: function(value){
        this.opsiDisplay = value;
    },
    viewVar: false,
    setViewVar: function(value){
        this.viewVar = value;
    },
	renderTo: 'ext-content',
	AddValidation: null,
    MsgAddValidation: null,
    initComponent: function() {
		var thisObj = this;
		var ObjPanelDinamisKanan = [];

        //store yg dipakai (begin)
        var cmb_province = Ext.create('Koltiva.store.Grower.CmbProvince');
        cmb_province.load();

        var cmb_district = Ext.create('Koltiva.store.Grower.CmbDistrict');
        var cmb_subdistrict = Ext.create('Koltiva.store.Grower.CmbSubdistrict');
        var cmb_village = Ext.create('Koltiva.store.Grower.CmbVillage');

        var cmb_education = Ext.create('Koltiva.store.Grower.CmbEducation');
        var cmb_legalstatus = Ext.create('Koltiva.store.ComboGeneral.CmbLegalStatus');
        var cmb_year_option = Ext.create('Koltiva.store.ComboGeneral.CmbYearOption');
        var cmb_handphone_type = Ext.create('Koltiva.store.ComboGeneral.CmbHandphoneType');
		var cmb_smeType = Ext.create('Koltiva.store.SME.CmbsmeType');

		var Partner = 'GAR';
		var hidden	= false;
		if(m_daerah_access.includes("73") || m_daerah_access.includes("61")){
			Partner = 'GAR';
			// hidden	= false;
		}else if(m_daerah_access.includes("43") || m_daerah_access.includes("44")){
			Partner = 'WAGS';
			// hidden	= true;
		}


		var cmb_smeRole = Ext.create('Koltiva.store.SME.CmbSmeRole');
		cmb_smeRole.proxy.extraParams = { 'Partner' : Partner };

		var cmb_WorkArea = Ext.create('Koltiva.store.SME.CmbWorkArea');
		cmb_WorkArea.proxy.extraParams = {'MemberID': thisObj.viewVar.MemberID};
		
		var cmb_handphone_type = Ext.create('Koltiva.store.ComboGeneral.CmbHandphoneType');
		//store yg dipakai (end) 

		if(m_daerah_access.includes("43") || m_daerah_access.includes("44"))
        {
            var CmbSmeType = Ext.getCmp('Koltiva.view.SME.FormMainTrader-CmbSmeType');  
			CmbSmeType = 'Collection Center';  
			
			var cmbProvince = Ext.getCmp('Koltiva.view.SME.FormMainTrader-Province');
			cmbProvince = 'Province'

        } else {
            var CmbSmeType = Ext.getCmp('Koltiva.view.SME.FormMainTrader-CmbSmeType');  
			CmbSmeType = 'SME Category';  
			
			var cmbProvince = Ext.getCmp('Koltiva.view.SME.FormMainTrader-Province');
			cmbProvince = 'Province'
		}
		
        //panel Form Basic Data ======================================================================================= (Begin)
        var objPanelBasicData = Ext.create('Ext.form.Panel',{
            title: lang('SME Basic Data'),
            frame: true,
            id: 'Koltiva.view.SME.FormMainTrader-FormBasicData',
            fileUpload: true,
            margin:'0 0 20 0',
            items: [{
                layout: 'column',
                border: false,
                padding:5,
                items:[{
                    columnWidth: 1,
                    layout:'form',
                    items:[ //tab panel
					  {
                        xtype: 'panel', //klu tab ganti aja jadi tabpanel
                        flex: 1,
                        activeTab: 0,
                        plain: false,
                        cls:'tabSce',
                        id: 'Koltiva.view.SME.FormMainTrader-FormBasicData-tab',
                        items:[{
                            xtype: 'panel',
                            id: 'Koltiva.view.SME.FormMainTrader-FormBasicData-tabTraderData',
                            items:[{
									layout: 'column',
									border: false,
									items:[{
										columnWidth: 0.495,
										layout:'form',
										style:'padding-right:25px;',
										items:[{
											xtype: 'panel',
											title: lang('Business Information'),
											frame: false,
											id: 'Koltiva.view.SME.FormMainTrader-BusinessInformationSection',
											style: 'margin-top:10px;',
											cls: 'Sfr_PanelSubLayoutFormRoundedGray',
										},{
											xtype: 'hiddenfield',
											id: 'Koltiva.view.SME.FormMainTrader-MemberID',
											name: 'Koltiva.view.SME.FormMainTrader-MemberID'
										},{
											xtype: 'textfield',
											id: 'Koltiva.view.SME.FormMainTrader-MemberDisplayID',
											name: 'Koltiva.view.SME.FormMainTrader-MemberDisplayID',
											fieldLabel: lang('SME ID'),
											labelAlign:'top',
											readOnly:true
										},{
											html:'<div></div>',
										},{
											xtype: 'datefield',
											id: 'Koltiva.view.SME.FormMainTrader-DateCollection',
											name: 'Koltiva.view.SME.FormMainTrader-DateCollection',
											fieldLabel: lang('Date Collection'),
											//labelWidth: 150,
											labelAlign:'top',
											style: 'margin-bottom:15px;',
											allowBlank: false,
											format: 'Y-m-d H:i:s'
										},{
											html:'<div></div>'
										},{
											xtype: 'boxselect',
											id: 'Koltiva.view.SME.FormMainTrader-CmbSmeType',
											name: 'Koltiva.view.SME.FormMainTrader-CmbSmeType[]',
											fieldLabel: CmbSmeType,
											labelAlign:'top',
											displayField: 'label',
											valueField: 'id',
											queryMode: 'local',
											store: cmb_smeType,
											stacked: true,
											pinList: false,
											triggerOnClick: false,
											filterPickList: true,
											allowBlank: false,
											listeners: {
												change: function() {
													// var ArrThisValue = this.value;
													// if(ArrThisValue.includes('1') == false) {
													// 	var ArrSmeRole = [];
													// 	ArrSmeRole.push('10'); //Other
													// 	Ext.getCmp('Koltiva.view.SME.FormMainTrader-CmbSmeRole').setValue(ArrSmeRole);
													// 	Ext.getCmp('Koltiva.view.SME.FormMainTrader-CmbSmeRole').setReadOnly(true);
													// } else {
													// 	Ext.getCmp('Koltiva.view.SME.FormMainTrader-CmbSmeRole').setReadOnly(false);
													// }
												}
											}
										},{
											xtype: 'boxselect',
											id: 'Koltiva.view.SME.FormMainTrader-CmbSmeRole',
											name: 'Koltiva.view.SME.FormMainTrader-CmbSmeRole[]',
											fieldLabel: lang('SME Role'),
											labelAlign:'top',
											displayField: 'label',
											valueField: 'id',
											queryMode: 'local',
											store: cmb_smeRole,
											stacked: true,
											pinList: false,
											triggerOnClick: false,
											filterPickList: true,
											allowBlank: false
										},{
											xtype: 'boxselect',
											id: 'Koltiva.view.SME.FormMainTrader-cmbSMEVillage',
											name: 'Koltiva.view.SME.FormMainTrader-cmbSMEVillage[]',
											fieldLabel: lang('Cakupan Area Kerja'),
											labelAlign:'top',
											displayField: 'label',
											valueField: 'id',
											queryMode: 'local',
											store: cmb_WorkArea,
											stacked: true,
											pinList: false,
											triggerOnClick: false,
											filterPickList: true,
											allowBlank: true,
											hidden:hidden
										},{
											xtype: 'textfield',
											id: 'Koltiva.view.SME.FormMainTrader-agCompanyName',
											name: 'Koltiva.view.SME.FormMainTrader-agCompanyName',
											fieldLabel: lang('Company Name'),
											//labelWidth: 150,
											labelAlign:'top',
											allowBlank: false
										},{
											html:'<div></div>',
										},{
											xtype: 'textfield',
											id: 'Koltiva.view.SME.FormMainTrader-agAliasName',
											name: 'Koltiva.view.SME.FormMainTrader-agAliasName',
											fieldLabel: lang('Alias'),
											labelAlign:'top',
											allowBlank: false
										},{
											html:'<div></div>',
										},{
											xtype: 'combobox',
											id: 'Koltiva.view.SME.FormMainTrader-agYearEstablished',
											name: 'Koltiva.view.SME.FormMainTrader-agYearEstablished',
											store: cmb_year_option,
											fieldLabel: lang('Year Established'),
											labelAlign:'top',
											queryMode: 'local',
											displayField: 'label',
											valueField: 'id'
										},{
											html:'<div></div>',
										},{
											xtype: 'combobox',
											id: 'Koltiva.view.SME.FormMainTrader-agLegalStatusCompany',
											name: 'Koltiva.view.SME.FormMainTrader-agLegalStatusCompany',
											store: cmb_legalstatus,
											fieldLabel: lang('Legal Status of Company'),
											labelAlign:'top',
											queryMode: 'local',
											displayField: 'label',
											valueField: 'id'
										},{
											html:'<div></div>',
										},{
											layout:'column',
											border:false,
											items:[{
												columnWidth: 1,
												border: false,
												layout:{
													type:'hbox',
													pack:'end'
												},
												items:[{
													xtype: 'image',
													id: 'Koltiva.view.SME.FormMainTrader-agCompanyLogo',
													width: '175px',
													height:'200px',
													src: m_api_base_url + '/images/default_photo/male-business.jpg'
												},{
													xtype: 'textfield',
													id: 'Koltiva.view.SME.FormMainTrader-agCompanyLogoOld',
													name: 'Koltiva.view.SME.FormMainTrader-agCompanyLogoOld',
													inputType: 'hidden'
												}]
											}]
										},{
											html:'<div></div>',
										},{
											layout:'column',
											border:false,
											style:'margin-top:-20px',
											items:[{
												columnWidth: 1,
												border: false,
												layout:'form',
												items:[{
													xtype: 'fileuploadfield',
													fieldLabel: lang('Photo of the Bussiness'),
													labelAlign: 'top',
													id: 'Koltiva.view.SME.FormMainTrader-agCompanyLogoInput',
													name: 'Koltiva.view.SME.FormMainTrader-agCompanyLogoInput',
													buttonText: 'Browse',
													listeners: {
														'change': function (fb, v) {
															objPanelBasicData.submit({
																url: m_api + '/sme/image_member_business_logo',
																clientValidation: false,
																params: {
																	opsiDisplay: thisObj.opsiDisplay,
																	MemberID: Ext.getCmp('Koltiva.view.SME.FormMainTrader-MemberID').getValue()
																},
																waitMsg: 'Sending Photo...',
																success: function (fp, o) {
																	Ext.getCmp('Koltiva.view.SME.FormMainTrader-agCompanyLogo').setSrc(o.result.file);
																	Ext.getCmp('Koltiva.view.SME.FormMainTrader-agCompanyLogoOld').setValue(o.result.filepath);
																}
															});
														}
													}
												}]
											}]
										},{
											html:'<div></div>'
										},{
											xtype: 'panel',
											title: lang('Business Location'),
											frame: false,
											id: 'Koltiva.view.SME.FormMainTrader-BusinessLocationSection',
											style: 'margin-top:10px;',
											cls: 'Sfr_PanelSubLayoutFormRoundedGray',
										},{
											xtype: 'combobox',
											id: 'Koltiva.view.SME.FormMainTrader-Province',
											name: 'Koltiva.view.SME.FormMainTrader-Province',
											store: cmb_province,
											fieldLabel: cmbProvince,
											labelAlign:'top',
											queryMode: 'local',
											displayField: 'label',
											valueField: 'id',
											listeners: {
												change: function(cb, nv, ov) {
													cmb_district.load({
														params: {
															ProvinceID: nv
														}
													});
													Ext.getCmp('Koltiva.view.SME.FormMainTrader-District').setValue('');
													Ext.getCmp('Koltiva.view.SME.FormMainTrader-Subdistrict').setValue('');
													Ext.getCmp('Koltiva.view.SME.FormMainTrader-Village').setValue('');
												}
											}
										},{
											html:'<div></div>',
										},{
											xtype: 'combobox',
											id: 'Koltiva.view.SME.FormMainTrader-District',
											name: 'Koltiva.view.SME.FormMainTrader-District',
											store: cmb_district,
											fieldLabel: lang('District'),
											labelAlign:'top',
											queryMode: 'local',
											displayField: 'label',
											valueField: 'id',
											listeners: {
												change: function(cb, nv, ov) {
													cmb_subdistrict.load({
														params: {
															DistrictID: nv
														}
													});
													Ext.getCmp('Koltiva.view.SME.FormMainTrader-Subdistrict').setValue('');
													Ext.getCmp('Koltiva.view.SME.FormMainTrader-Village').setValue('');
												}
											}
										},{
											html:'<div></div>',
										},{
											xtype: 'combobox',
											id: 'Koltiva.view.SME.FormMainTrader-Subdistrict',
											name: 'Koltiva.view.SME.FormMainTrader-Subdistrict',
											store: cmb_subdistrict,
											fieldLabel: lang('Subdistrict'),
											labelAlign:'top',
											queryMode: 'local',
											displayField: 'label',
											valueField: 'id',
											listeners: {
												change: function(cb, nv, ov) {
													cmb_village.load({
														params: {
															SubdistrictID: nv
														}
													});
													Ext.getCmp('Koltiva.view.SME.FormMainTrader-Village').setValue('');
												}
											}
										},{
											html:'<div></div>',
										},{
											xtype: 'combobox',
											id: 'Koltiva.view.SME.FormMainTrader-Village',
											name: 'Koltiva.view.SME.FormMainTrader-Village',
											store: cmb_village,
											fieldLabel: lang('Village'),
											labelAlign:'top',
											queryMode: 'local',
											displayField: 'label',
											valueField: 'id',
											allowBlank: false
										},{
											html:'<div></div>',
										},{
											xtype: 'textarea',
											fieldLabel: lang('Address'),
											labelAlign:'top',
											id: 'Koltiva.view.SME.FormMainTrader-Address',
											name: 'Koltiva.view.SME.FormMainTrader-Address',
											height: 65
										},{
											html:'<div></div>',
										},{
											xtype: 'fieldcontainer',
											fieldLabel: lang('Role'),
											hidden:true,
											labelWidth: 80,
											layout: 'vbox',
											defaultType: 'checkboxfield',
											items: [{
												boxLabel  : lang('Trader'),
												name      : 'Koltiva.view.SME.FormMainTrader-CbRoleTrader',
												id        : 'Koltiva.view.SME.FormMainTrader-CbRoleTrader',
												inputValue: '1'
											}]
										},{
											xtype: 'textfield',
											id: 'Koltiva.view.SME.FormMainTrader-Latitude',
											name: 'Koltiva.view.SME.FormMainTrader-Latitude',
											allowNegative: false,
											labelAlign:'top',
											fieldLabel: lang('Latitude')
										},{
											html:'<div></div>',
										},{
											xtype: 'textfield',
											id: 'Koltiva.view.SME.FormMainTrader-Longitude',
											name: 'Koltiva.view.SME.FormMainTrader-Longitude',
											allowNegative: false,
											labelAlign:'top',
											fieldLabel: lang('Longitude')
										},{
											html:'<div></div>',
										},{
											xtype: 'component',
											autoEl: {
												html: '<div id="map" style="width:100%;height:250px;background:#e1e1e1;border:1px solid #e1e1e1;border-radius: 1%"></div>',
												style:'width:100%;'
											}	
										}]
									},
									{
										columnWidth: 0.5,
										margin:'0 10 0 0',
										style:'padding-left:5px;',
										layout:'form',
										items:[{
											xtype: 'panel',
											title: lang('Communication and Media'),
											frame: false,
											id: 'Koltiva.view.SME.FormMainTrader-CommunicationMediaSection',
											style: 'margin-top:10px;',
											cls: 'Sfr_PanelSubLayoutFormRoundedGray',
										},{
											xtype: 'textfield',
											id: 'Koltiva.view.SME.FormMainTrader-Website',
											name: 'Koltiva.view.SME.FormMainTrader-Website',
											labelAlign: 'top',
											fieldLabel: lang('Website')
										},{
											xtype: 'textfield',
											id: 'Koltiva.view.SME.FormMainTrader-Linked',
											name: 'Koltiva.view.SME.FormMainTrader-Linked',
											labelAlign: 'top',
											fieldLabel: lang('Linked')
										},
										{
											xtype: 'textfield',
											id: 'Koltiva.view.SME.FormMainTrader-Phone',
											name: 'Koltiva.view.SME.FormMainTrader-Phone',
											fieldLabel: lang('Phone'),
											labelAlign:'top'
										},
										{
											xtype: 'textfield',
											id: 'Koltiva.view.SME.FormMainTrader-Fax',
											name: 'Koltiva.view.SME.FormMainTrader-Fax',
											fieldLabel: lang('Fax'),
											labelAlign:'top'
										},
										{
											xtype: 'textfield',
											id: 'Koltiva.view.SME.FormMainTrader-Email',
											name: 'Koltiva.view.SME.FormMainTrader-Email',
											fieldLabel: lang('Email'),
											labelAlign:'top'
										},{
											xtype: 'panel',
											title: lang('Business Owner'),
											frame: false,
											id: 'Koltiva.view.SME.FormMainTrader-BusinessOwnerSection',
											style: 'margin-top:10px;',
											cls: 'Sfr_PanelSubLayoutFormRoundedGray',
										},{
											layout:'column',
											border:false,
											items:[{
												columnWidth: 1,
												border: false,
												layout:{
													type:'hbox',
													pack:'end'
												},
												items:[{
													xtype: 'image',
													id: 'Koltiva.view.SME.FormMainTrader-MemberPhoto',
													width: '175px',
													height:'200px',
													src: m_api_base_url + '/images/default_photo/male-business.jpg'
												},{
													xtype: 'textfield',
													id: 'Koltiva.view.SME.FormMainTrader-MemberPhotoOld',
													name: 'Koltiva.view.SME.FormMainTrader-MemberPhotoOld',
													inputType: 'hidden'
												}]
											}]
										},{
											layout:'column',
											border:false,
											style:'margin-top:-20px',
											items:[{
												columnWidth: 1,
												border: false,
												layout:'form',
												items:[{
													xtype: 'fileuploadfield',
													fieldLabel: lang('Photo of the Owner'),
													labelAlign: 'top',
													id: 'Koltiva.view.SME.FormMainTrader-MemberPhotoInput',
													name: 'Koltiva.view.SME.FormMainTrader-MemberPhotoInput',
													buttonText: 'Browse',
													listeners: {
														'change': function (fb, v) {
															objPanelBasicData.submit({
																url: m_api + '/sme/image_member',
																clientValidation: false,
																params: {
																	opsiDisplay: thisObj.opsiDisplay,
																	MemberID: Ext.getCmp('Koltiva.view.SME.FormMainTrader-MemberID').getValue()
																},
																waitMsg: 'Sending Photo...',
																success: function (fp, o) {
																	Ext.getCmp('Koltiva.view.SME.FormMainTrader-MemberPhoto').setSrc(o.result.file);
																	Ext.getCmp('Koltiva.view.SME.FormMainTrader-MemberPhotoOld').setValue(o.result.filepath);
																}
															});
														}
													}
												}]
											}]
										},{
											html:'<div></div>'
										},{
											xtype: 'textfield',
											id: 'Koltiva.view.SME.FormMainTrader-Fullname',
											name: 'Koltiva.view.SME.FormMainTrader-Fullname',
											fieldLabel: lang('Owner Name'),
											//labelWidth: 150,
											labelAlign:'top',
											allowBlank: false
										},{
											html:'<div></div>',
										},{
											xtype: 'textfield',
											id: 'Koltiva.view.SME.FormMainTrader-Nin',
											name: 'Koltiva.view.SME.FormMainTrader-Nin',
											fieldLabel: lang('National Identification Number'),
											//labelWidth: 180,
											labelAlign:'top'
										},{
											html:'<div></div>',
										},{
											xtype: 'datefield',
											id: 'Koltiva.view.SME.FormMainTrader-DateOfBirth',
											name: 'Koltiva.view.SME.FormMainTrader-DateOfBirth',
											fieldLabel: lang('Date of Birth'),
											//labelWidth: 150,
											labelAlign:'top',
											allowBlank: false,
											format: 'Y-m-d'
										},{
											html:'<div></div>',
										},{
											fieldLabel: lang('Gender'),
											labelAlign:'top',
											xtype: 'radiogroup',
											allowBlank: false,
											msgTarget: 'side',
											columns: 2,
											items:[{
												boxLabel: lang('Male'),
												name: 'Koltiva.view.SME.FormMainTrader-Gender',
												inputValue: 'm',
												id: 'Koltiva.view.SME.FormMainTrader-GenderMale',
												style: 'margin-top:-10px;',
												listeners:{
													change: function(){
														return false;
													}
												}
											},{
												boxLabel: lang('Female'),
												name: 'Koltiva.view.SME.FormMainTrader-Gender',
												inputValue: 'f',
												id: 'Koltiva.view.SME.FormMainTrader-GenderFemale',
												style: 'margin-top:-10px;',
												listeners:{
													change: function(){
														return false;
													}
												}
											}]
										},{
											html:'<div></div>',
										},
										{
                                        xtype: 'combobox',
                                        id: 'Koltiva.view.SME.FormMainTrader-HandphoneType',
                                        name: 'Koltiva.view.SME.FormMainTrader-HandphoneType',
                                        store: cmb_handphone_type,
                                        fieldLabel: lang('Handphone Type'),
                                        labelAlign:'top',
                                        queryMode: 'local',
                                        displayField: 'label',
                                        valueField: 'id',
                                         listeners: {
                                            change: function(cb, nv, ov) {
                                                if(nv == '3'){
                                                    Ext.getCmp('Koltiva.view.SME.FormMainTrader-Handphone').setValue('');
                                                    Ext.getCmp('Koltiva.view.SME.FormMainTrader-Handphone').setDisabled(true);
                                                }else{
                                                    Ext.getCmp('Koltiva.view.SME.FormMainTrader-Handphone').setDisabled(false);
                                                }
												if(nv == '1'){
													Ext.getCmp('Koltiva.view.SME.FormMainTrader-AccessToSmartPhones').setDisabled(true);
												}else{
													Ext.getCmp('Koltiva.view.SME.FormMainTrader-AccessToSmartPhones').setDisabled(false);
												}
													 
                                            }
                                         }
                                      },{
                                        html:'<div></div>',
                                      },{
                                        xtype: 'textfield',
                                        id: 'Koltiva.view.SME.FormMainTrader-Handphone',
                                        name: 'Koltiva.view.SME.FormMainTrader-Handphone',
                                        fieldLabel: lang('Handphone'),
                                        labelAlign:'top',
										minLength:5,
										 listeners :{
											blur : function(s)
											{  
												if(s.getValue().length < 5 ){ 
													 Ext.MessageBox.show({
														title: 'Error',
														msg: lang('Nomor telepon anda belum benar, silahkan perbaiki'),
														buttons: Ext.MessageBox.OK,
														animateTarget: 'mb9',
														icon: 'ext-mb-error'
													});
												}
											}
										 }
                                      },{
										fieldLabel: lang('Access to Smartphone'),
										id :'Koltiva.view.SME.FormMainTrader-AccessToSmartPhones',
										xtype: 'radiogroup',
										msgTarget: 'side',
										labelAlign:'top',
										columns: 2,
										items:[{
												boxLabel: lang('Yes'),
												name: 'Koltiva.view.SME.FormMainTrader-AccessToSmartPhone',
												inputValue: '1',
												id: 'Koltiva.view.SME.FormMainTrader-AccessToSmartPhone1',
												listeners:{
													change: function(){
														return false;
													}
												}
											},{
												boxLabel: lang('No'),
												name: 'Koltiva.view.SME.FormMainTrader-AccessToSmartPhone',
												inputValue: '2',
												id: 'Koltiva.view.SME.FormMainTrader-AccessToSmartPhone2',
												listeners:{
													change: function(){
														return false;
													}
												}
											}]
										 },{
											html:'<div></div>'
										},{
											xtype: 'textfield',
											vtype: 'email',
											id: 'Koltiva.view.SME.FormMainTrader-Email',
											name: 'Koltiva.view.SME.FormMainTrader-Email',
											fieldLabel: lang('Email'),
											labelAlign:'top'
										},{
											html:'<div></div>'
										},{
											xtype: 'combobox',
											id: 'Koltiva.view.SME.FormMainTrader-Education',
											name: 'Koltiva.view.SME.FormMainTrader-Education',
											store: cmb_education,
											fieldLabel: lang('Highest education level achieved'),
											labelAlign:'top',
											queryMode: 'local',
											displayField: 'label',
											valueField: 'id'
										}]
									}]
								}]
						    } ]
					   }]//tab panel
					  
					}] 
            }],
            buttons: [{
                text: lang('Save'),
                id: 'Koltiva.view.SME.FormMainTrader-btnSave',
                icon: varjs.config.base_url + 'images/icons/new/save.png',
				cls: 'Sfr_BtnFormBlue',
				overCls: 'Sfr_BtnFormBlue-Hover',
                handler: function () {
                    if (objPanelBasicData.isValid()) {

                        //cek apakah role ada di pilih (begin)
                        var validRole = false;

                        if(Ext.getCmp('Koltiva.view.SME.FormMainTrader-CbRoleTrader').getValue() == "1" ){
                            validRole = true;
                        }
						
						//Data Control Tambahan ======================================= (Begin)
                        thisObj.AddValidation = true;
                        thisObj.MsgAddValidation = "";
                        thisObj.AddValidationBasicForm();
                        //Data Control Tambahan ======================================= (Emd)

						if(thisObj.AddValidation == true){
							objPanelBasicData.submit({
								url: m_api + '/sme/member',
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
	
									Ext.getCmp('Koltiva.view.SME.FormMainTrader').destroy(); //destory current view
									//create object View untuk FormMainTrader
									if(Ext.getCmp('Koltiva.view.SME.FormMainTrader') == undefined){
										var FormMainTrader = Ext.create('Koltiva.view.SME.FormMainTrader', {
											opsiDisplay: 'update',
											from:thisObj.from,
											viewVar: {
												MemberID: o.result.MemberIDInc,
												MemberTypeID: o.result.MemberTypeID
											}
										});
									}else{
										//destroy, create ulang
										Ext.getCmp('Koltiva.view.SME.FormMainTrader').destroy();
										var FormMainTrader = Ext.create('Koltiva.view.SME.FormMainTrader', {
											opsiDisplay: 'update',
											from:thisObj.from,
											viewVar: {
												MemberID: o.result.MemberIDInc,
												MemberTypeID: o.result.MemberTypeID
											}
										});
									}
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
                                title: 'Data Control Validation',
                                msg: thisObj.MsgAddValidation,
                                buttons: Ext.MessageBox.OK,
                                animateTarget: 'mb9',
                                icon: 'ext-mb-info'
                            });
						}

                    }else{
                        Ext.MessageBox.show({
                            title: 'Attention',
                            msg: lang('Form not complete yet'),
                            buttons: Ext.MessageBox.OK,
                            animateTarget: 'mb9',
                            icon: 'ext-mb-info'
                        });
                    }
                }
            },{
				text: lang('Print SME Profile'),
				id: 'Koltiva.view.SME.FormMainTrader-btnPrint',
				icon: varjs.config.base_url + 'images/icons/new/printout.png',
				cls: 'Sfr_BtnFormGrey',
				overCls: 'Sfr_BtnFormGrey-Hover',
				handler: function () {
					var urlNya = m_api + '/grower/cetak_agent_profiles/MemberID/' + Ext.getCmp('Koltiva.view.SME.FormMainTrader-MemberID').getValue();
					preview_cetak_surat(urlNya);
				}
			}]
        });
        //panel Form Basic Data ======================================================================================= (End)

        //panel shop Product======================================================================================= (Begin)
        // var objPanelShopProduct = Ext.create('Koltiva.view.SME.TraderShopProduct');
        // thisObj.objPanelShopProduct = objPanelShopProduct;
        //panel shop Product======================================================================================= (End)

        //panel Staff Data ======================================================================================= (Begin)
        var objPanelTraderStaff = Ext.create('Koltiva.view.SME.TraderStaffPanel');
		thisObj.objPanelTraderStaff = objPanelTraderStaff;
		ObjPanelDinamisKanan.push(objPanelTraderStaff);
        //panel Staff Data ======================================================================================= (End)
		
		//panel Warehouses Data ======================================================================================= (Begin)
        var objPanelWarehouses = Ext.create('Koltiva.view.SME.WarehousesPanel');
		thisObj.objPanelWarehouses = objPanelWarehouses;
		ObjPanelDinamisKanan.push(objPanelWarehouses);
        //panel Warehouses Data ======================================================================================= (End)

		if(thisObj.from == 'SMEMill'){	
			//panel SME Collecting Point ======================================================================================= (Begin)
			var objPanelAgentRelation = Ext.create('Koltiva.view.SME.AgentRelationPanel');
			thisObj.objPanelAgentRelation = objPanelAgentRelation;
			ObjPanelDinamisKanan.push(objPanelAgentRelation);
			//panel SME Collecting Point ======================================================================================= (End)
		}

		//panel SME Collecting Point ======================================================================================= (Begin)
        var objPanelTraderCollectingPoint = Ext.create('Koltiva.view.SME.TraderCollectingPointPanel');
		thisObj.objPanelTraderCollectingPoint = objPanelTraderCollectingPoint;
		ObjPanelDinamisKanan.push(objPanelTraderCollectingPoint);
		//panel SME Collecting Point ======================================================================================= (End)

		//panel SME Vehicle ======================================================================================= (Begin)
        var objPanelTraderVehicle = Ext.create('Koltiva.view.SME.TraderVehiclePanel');
		thisObj.objPanelTraderVehicle = objPanelTraderVehicle;
		ObjPanelDinamisKanan.push(objPanelTraderVehicle);
		//panel SME Vehicle ======================================================================================= (End)
                
        //panel Trader Survey STA ======================================================================================= (Begin)
        var objPanelTraderSTA = Ext.create('Koltiva.view.SME.TraderSurveyPanelSTA');
        thisObj.objPanelTraderSTA = objPanelTraderSTA;
        ObjPanelDinamisKanan.push(objPanelTraderSTA);
        //panel Trader Survey STA ======================================================================================= (End)
	
		//panel SP Code =======================================================// (Begin)
		ObjPanelSPCode = Ext.create('Koltiva.view.SME.SPCodePanel', {
			viewVar: {
				MemberID: thisObj.viewVar.MemberID
			}
		});
		ObjPanelDinamisKanan.push(ObjPanelSPCode);
		//panel SP Code =======================================================// (End)

		
		//Khusus yg type "Plantation" =========================================================== (Begin)
		if(thisObj.opsiDisplay == 'update' || thisObj.opsiDisplay == 'view'){
			//Proses MemberTypeID
			// console.log(thisObj.viewVar.MemberTypeID);
			if(thisObj.viewVar.MemberTypeID != null){
				var ArrMemberTypeID = thisObj.viewVar.MemberTypeID.split(",");
				if(ArrMemberTypeID.includes("5") == true) {
					//console.log('Plantation Panel');
	
					//===================== PLANTATION STATUS =======================================================//
					ObjPanelPlantationStatus = Ext.create('Koltiva.view.PlotSurvey.PanelPlantationStatus',{
						viewVar: {
							MemberID: thisObj.viewVar.MemberID,
							CallFrom: 'SME'
						}
					});
					ObjPanelDinamisKanan.push(ObjPanelPlantationStatus);
	
	
					//===================== PLANTATION SURVEY =======================================================//
					ObjPanelPlantationSurvey = Ext.create('Koltiva.view.PlotSurvey.PanelSmePlantationSurvey',{
						viewVar: {
							MemberID: thisObj.viewVar.MemberID
						}
					});
					ObjPanelDinamisKanan.push(ObjPanelPlantationSurvey);
	
					//===================== PLANTATION POLYGON =======================================================//
					ObjPanelPlotPolygon  = Ext.create('Koltiva.view.PlotPolygon.PlotPolygonPanel', {
						viewVar: {
							MemberID: thisObj.viewVar.MemberID,
							CallFrom: 'SME'
						}
					});
					ObjPanelDinamisKanan.push(ObjPanelPlotPolygon);
	
					//===================== Survey Document =======================================================//
					ObjPanelDocumentSurvey = Ext.create('Koltiva.view.DocumentSurvey.DocumentSurveyPanel', {
						viewVar: {
							MemberID: thisObj.viewVar.MemberID
						}
					});
					ObjPanelDinamisKanan.push(ObjPanelDocumentSurvey);
				}
			}
		}
		//Khusus yg type "Plantation" =========================================================== (END)

        //isi layout utama ================================================================================================= (Begin)
        thisObj.items = [{
            xtype: 'panel',
            border:false,
            layout:{
                type:'hbox'
            },
            items:[{
                html:'<h3 style="margin:0px 0 7px 0;padding:0px;">'+lang('SME Data 2')+'</h3>'
            },{
                id: 'Koltiva.view.SME.FormMainTrader-labelInfoInsert',
                html:'',
            }]
        },{
            html: '<div id="Sfr_IdBoxInfoDataGrid" class="Sfr_BoxInfoDataGrid">' +
                  '<ul class="Sft_UlListInfoDataGrid"><li class="Sft_ListInfoDataGrid">' +
                  '<a><img class="Sft_ListIconInfoDataGrid" src="' + varjs.config.base_url + 'images/icons/new/back.png" width="20" />' +
                  '&nbsp;&nbsp;' + lang('Back to SME List')  + '</a></li></ul></div>',
            listeners: {
                click: {
                    element: 'el',
                    preventDefault: true,
                    fn: function(e, target){
						Ext.getCmp('Koltiva.view.SME.FormMainTrader').destroy(); //destory current view
						if(thisObj.from == "SMEMill"){
							if(Ext.getCmp('Koltiva.view.SME.GridMainTraderMill') == undefined){
								var GridMainTraderMill = Ext.create('Koltiva.view.SME.GridMainTraderMill');
							}else{
								//destroy, create ulang
								Ext.getCmp('Koltiva.view.SME.GridMainTraderMill').destroy();
								var GridMainTraderMill = Ext.create('Koltiva.view.SME.GridMainTraderMill');
							}
						}else{
							if(Ext.getCmp('Koltiva.view.SME.GridMainTrader') == undefined){
								var GridMainTrader = Ext.create('Koltiva.view.SME.GridMainTrader');
							}else{
								//destroy, create ulang
								Ext.getCmp('Koltiva.view.SME.GridMainTrader').destroy();
								var GridMainTrader = Ext.create('Koltiva.view.SME.GridMainTrader');
							}
						}
                    }
                }
            }
        },{
            html:'<br />'
        },{
            layout: 'column',
            border: false,
            items: [{
                //LEFT CONTENT
                columnWidth:0.6,
                items:[
                    objPanelBasicData
                ]
            },{
                //RIGHT CONTENT
                columnWidth: 0.4, 
                items:ObjPanelDinamisKanan
            }]
        }];
        //isi layout utama ================================================================================================= (End)

        this.callParent(arguments);
    },
    listeners: {
        afterRender: function(){
            var thisObj = this;

            //hilangkan view Filter region
            document.getElementById('divCommonContentRegion').style.display = 'none';

            //hidden panel yg dicek by Role semua =================== (begin)
            //thisObj.objPanelTraderSurveyPanel.setVisible(false);
            //hidden panel yg dicek by Role semua =================== (end)

            //set label
            if(this.opsiDisplay == 'insert'){
                Ext.getCmp('Koltiva.view.SME.FormMainTrader-labelInfoInsert').update('<h5 style="margin:8px 0 7px 15px;padding:0px;">('+lang('Add New SME')+')</h5>');
				
				if(m_partner == 14 || m_partner == 194 || m_partner == 195){
					Ext.getCmp('Koltiva.view.SME.FormMainTrader-cmbSMEVillage').setVisible(false);
				} else {
					Ext.getCmp('Koltiva.view.SME.FormMainTrader-cmbSMEVillage').setVisible(true);
				}
				
                //form reset
                Ext.getCmp('Koltiva.view.SME.FormMainTrader-FormBasicData').getForm().reset();
                Ext.getCmp('Koltiva.view.SME.FormMainTrader-btnPrint').setVisible(false);
                Ext.getCmp('Koltiva.view.SME.FormMainTrader-MemberPhoto').setSrc(m_api_base_url + '/images/default_photo/male-business.jpg'); 

                //Trader Survey
                //thisObj.objPanelTraderSurveyPanel.collapse();
                //thisObj.objPanelTraderSurveyPanel.setViewVar({
                    //MemberID:null
                //});

                //trader vehicle
                thisObj.objPanelTraderVehicle.collapse();
                thisObj.objPanelTraderVehicle.setViewVar({
                    MemberID:null
                });

                //Trader Staff
                thisObj.objPanelTraderStaff.collapse();
                thisObj.objPanelTraderStaff.setViewVar({
                    MemberID:null
                });
				
				//warehouses
				thisObj.objPanelWarehouses.collapse();
				thisObj.objPanelWarehouses.setViewVar({
                    MemberID:null
				});

                //Trader STA
                thisObj.objPanelTraderSTA.setVisible(false);
                /*thisObj.objPanelTraderSTA.collapse();
                thisObj.objPanelTraderSTA.setViewVar({
                    MemberID:null,
                    User: 'SME'
				});*/

            }else{
                Ext.getCmp('Koltiva.view.SME.FormMainTrader-labelInfoInsert').update('');
            }

            if(this.opsiDisplay == 'update' || this.opsiDisplay == 'view'){

                //khusus view only
                if(this.opsiDisplay == 'view'){
                    Ext.getCmp('Koltiva.view.SME.FormMainTrader-btnSave').setVisible(false);
					Ext.getCmp('Koltiva.view.SME.FormMainTrader-MemberPhotoInput').setVisible(false); 

					if(m_partner == 14 || m_partner == 194 || m_partner == 195){
						Ext.getCmp('Koltiva.view.SME.FormMainTrader-cmbSMEVillage').setVisible(false);
						Ext.getCmp('Koltiva.view.SME.FormMainTrader-Subdistrict').setVisible(false);
						Ext.getCmp('Koltiva.view.SME.FormMainTrader-Village').setVisible(false);
						Ext.getCmp('Koltiva.view.SME.FormMainTrader-Address').setVisible(false);
						Ext.getCmp('Koltiva.view.SME.FormMainTrader-Latitude').setVisible(true);
						Ext.getCmp('Koltiva.view.SME.FormMainTrader-Longitude').setVisible(true);
					} else {
						Ext.getCmp('Koltiva.view.SME.FormMainTrader-cmbSMEVillage').setVisible(true);
						Ext.getCmp('Koltiva.view.SME.FormMainTrader-Subdistrict').setVisible(true);
						Ext.getCmp('Koltiva.view.SME.FormMainTrader-Village').setVisible(true);
						Ext.getCmp('Koltiva.view.SME.FormMainTrader-Address').setVisible(true);
						Ext.getCmp('Koltiva.view.SME.FormMainTrader-Latitude').setVisible(true);
						Ext.getCmp('Koltiva.view.SME.FormMainTrader-Longitude').setVisible(true);
					}
				}

				if(this.opsiDisplay == 'update'){
					if(m_partner == 14 || m_partner == 194 || m_partner == 195){
						Ext.getCmp('Koltiva.view.SME.FormMainTrader-cmbSMEVillage').setVisible(false);
						Ext.getCmp('Koltiva.view.SME.FormMainTrader-Subdistrict').setVisible(false);
						Ext.getCmp('Koltiva.view.SME.FormMainTrader-Village').setVisible(false);
						Ext.getCmp('Koltiva.view.SME.FormMainTrader-Address').setVisible(false);
						Ext.getCmp('Koltiva.view.SME.FormMainTrader-Latitude').setVisible(true);
						Ext.getCmp('Koltiva.view.SME.FormMainTrader-Longitude').setVisible(true);
					} else {
						Ext.getCmp('Koltiva.view.SME.FormMainTrader-cmbSMEVillage').setVisible(true);
						Ext.getCmp('Koltiva.view.SME.FormMainTrader-Subdistrict').setVisible(true);
						Ext.getCmp('Koltiva.view.SME.FormMainTrader-Village').setVisible(true);
						Ext.getCmp('Koltiva.view.SME.FormMainTrader-Address').setVisible(true);
						Ext.getCmp('Koltiva.view.SME.FormMainTrader-Latitude').setVisible(true);
						Ext.getCmp('Koltiva.view.SME.FormMainTrader-Longitude').setVisible(true);
					}
				}
				
                //form reset
                Ext.getCmp('Koltiva.view.SME.FormMainTrader-FormBasicData').getForm().reset();
                Ext.getCmp('Koltiva.view.SME.FormMainTrader-MemberPhoto').setSrc(m_api_base_url + '/images/user.png');

                //load data form
                Ext.getCmp('Koltiva.view.SME.FormMainTrader-FormBasicData').getForm().load({
                    url: m_api + '/sme/member_basic_data_form',
                    method: 'GET',
                    params: {
                        MemberID: this.viewVar.MemberID
                    },
                    success: function(form, action) {
                        var r = Ext.decode(action.response.responseText);
						
                        //untuk handle combo bertingkat
                        var cmb_province = Ext.data.StoreManager.lookup('store.Grower.CmbProvince');
                        var cmb_district = Ext.data.StoreManager.lookup('store.Grower.CmbDistrict');
                        var cmb_subdistrict = Ext.data.StoreManager.lookup('store.Grower.CmbSubdistrict');
						var cmb_village = Ext.data.StoreManager.lookup('store.Grower.CmbVillage');
						var SupplychainID = r.data.SupplychainID;
						// console.log(SupplychainID);
						if(SupplychainID == ''){
							Ext.getCmp('Koltiva.view.SME.SPCodePanel').destroy();
						}
                        cmb_province.load({
                            callback: function(records, operation, success){
                                Ext.getCmp('Koltiva.view.SME.FormMainTrader-Province').setValue(r.data.Province);
                                if (success == true) {
                                    cmb_district.load({
                                        params: {
                                            ProvinceID: r.data.Province
                                        },
                                        callback: function(records, operation, success){
                                            if (success == true) {
                                                Ext.getCmp('Koltiva.view.SME.FormMainTrader-District').setValue(r.data.District);
                                                cmb_subdistrict.load({
                                                    params: {
                                                        DistrictID: r.data.District
                                                    },
                                                    callback: function(records, operation, success){
                                                        if (success == true) {
                                                            Ext.getCmp('Koltiva.view.SME.FormMainTrader-Subdistrict').setValue(r.data.Subdistrict);
                                                            cmb_village.load({
                                                                params: {
                                                                    SubdistrictID: r.data.Subdistrict
                                                                },
                                                                callback: function(records, operation, success){
                                                                    if (success == true) {
                                                                        Ext.getCmp('Koltiva.view.SME.FormMainTrader-Village').setValue(r.data.Village);
                                                                    }
                                                                }
                                                            });
                                                        }
                                                    }
                                                });
                                            }
                                        }
                                    });
                                }
                            }
                        });

                        //set photo
                        if(r.data.PhotoSrc != ""){
                            var fotoUser = r.data.PhotoSrc;
                            var angkaRand = Math.floor((Math.random() * 100) + 1);
                            checkImageExists(fotoUser, function(existsImage) {
                                if (existsImage == true) {
                                    Ext.getCmp('Koltiva.view.SME.FormMainTrader-MemberPhoto').setSrc(fotoUser+'?'+angkaRand);
                                } else {
                                    if(r.data.Gender == 'f'){
                                        Ext.getCmp('Koltiva.view.SME.FormMainTrader-MemberPhoto').setSrc(m_api_base_url + '/images/default_photo/female-business.jpg');
                                    }else{
                                        Ext.getCmp('Koltiva.view.SME.FormMainTrader-MemberPhoto').setSrc(m_api_base_url + '/images/default_photo/male-business.jpg');
                                    }
                                }
                            });
                        }

                        //set photo bussiness
                        if(r.data.agCompanyLogo != ""){
                            var fotoUser2 = r.data.agCompanyLogo;
                            var angkaRand2 = Math.floor((Math.random() * 100) + 1);
                            checkImageExists(fotoUser2, function(existsImage) {
                                if (existsImage == true) {
                                    Ext.getCmp('Koltiva.view.SME.FormMainTrader-agCompanyLogo').setSrc(fotoUser2+'?'+angkaRand2);
                                } else {
                                    if(r.data.Gender == 'f'){
                                        Ext.getCmp('Koltiva.view.SME.FormMainTrader-agCompanyLogo').setSrc(m_api_base_url + '/images/default_photo/female-business.jpg');
                                    }else{
                                        Ext.getCmp('Koltiva.view.SME.FormMainTrader-agCompanyLogo').setSrc(m_api_base_url + '/images/default_photo/male-business.jpg');
                                    }
                                }
                            });
                        }

                        
                        //Trader Staff
                        thisObj.objPanelTraderStaff.expand();
                        thisObj.objPanelTraderStaff.setViewVar({
                            MemberID:thisObj.viewVar.MemberID
                        });
                        thisObj.objPanelTraderStaff.loadStoreGrid();
						
						 
						thisObj.objPanelWarehouses.expand();
                        thisObj.objPanelWarehouses.setViewVar({
                            MemberID:thisObj.viewVar.MemberID
                        });
                        thisObj.objPanelWarehouses.loadStoreGrid();

						if(thisObj.from == 'SMEMill'){	
							//trader agent relation
							thisObj.objPanelAgentRelation.expand();
							thisObj.objPanelAgentRelation.setViewVar({
								MemberID:thisObj.viewVar.MemberID
							});
							thisObj.objPanelAgentRelation.loadStoreGrid();
						}
						
                        //trader collecting point
                        thisObj.objPanelTraderCollectingPoint.expand();
                        thisObj.objPanelTraderCollectingPoint.setViewVar({
                            MemberID:thisObj.viewVar.MemberID
                        });
                        thisObj.objPanelTraderCollectingPoint.loadStoreGrid();
						
                        //trader vehicle
                        thisObj.objPanelTraderVehicle.expand();
                        thisObj.objPanelTraderVehicle.setViewVar({
                            MemberID:thisObj.viewVar.MemberID
                        });
                        thisObj.objPanelTraderVehicle.loadStoreGrid();
                        
                        //Trader Survey STA
                        var PartnerSurvey = r.data.PartnerSurvey.split(',');
                        if (PartnerSurvey.includes('159') === true) {
                            thisObj.objPanelTraderSTA.expand();
                            thisObj.objPanelTraderSTA.setViewVar({
                                MemberID: thisObj.viewVar.MemberID,
                                User: 'SME'
                            });
                            thisObj.objPanelTraderSTA.loadStoreGrid();
                        } else {
                            thisObj.objPanelTraderSTA.setVisible(false);
                        }

						init_map();//gmaps3 
                    },
                    failure: function(form, action) {
                        Ext.MessageBox.show({
                            title: 'Failed',
                            msg: 'Failed to retrieve data',
                            buttons: Ext.MessageBox.OK,
                            animateTarget: 'mb9',
                            icon: 'ext-mb-error'
                        });
                    }

                });

            }

        }
	},
	AddValidationBasicForm: function(){
        var thisObj = this;
        var ArrMsg = [];
        thisObj.AddValidation = true;
        //thisObj.MsgAddValidation = "Cihuy";

        //Cek Umur ================================================== (Begin)
        var DateBirth = Ext.Date.format(Ext.getCmp('Koltiva.view.SME.FormMainTrader-DateOfBirth').getValue(),'Y-m-d');

        var today = new Date();
        var birthDate = new Date(DateBirth);
        var age = today.getFullYear() - birthDate.getFullYear();
        var m = today.getMonth() - birthDate.getMonth();
        if (m < 0 || (m === 0 && today.getDate() < birthDate.getDate())) {
            age--;
        }

        if(age <= 16){
            thisObj.AddValidation = false;
            ArrMsg.push("Minimal Age is 16 years old");
        }
        //Cek Umur ================================================== (End)


        if(thisObj.AddValidation == false){
            var HtmlMsg = '<ul>';
            for (var index = 0; index < ArrMsg.length; index++) {
                HtmlMsg += '<li>'+ArrMsg[index]+'</li>'
            }
            HtmlMsg+='</ul>';
            thisObj.MsgAddValidation = HtmlMsg;
        }
    }
});
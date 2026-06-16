
var cmbPropinsiGeneral = Ext.create('Koltiva.store.ComboGeneral.ComboProvince');
var cmbDistrictGeneral = Ext.create('Koltiva.store.ComboGeneral.ComboDistrict');
var cmbSubDistrictGeneral = Ext.create('Koltiva.store.ComboGeneral.ComboSubDistrict');   
var cmbCertHolderGeneral = Ext.create('Koltiva.store.ComboGeneral.CmbCertHolderGeneral');
var cmbCertProgramsGeneral = Ext.create('Koltiva.store.ComboGeneral.CmbCertProgramsGeneral');
var cmbImsEventGeneral = Ext.create('Koltiva.store.ComboGeneral.CmbImsEventGeneral');
			
Ext.define('Koltiva.view.application_form.WinNotRecomended' ,{
    extend: 'Ext.window.Window',
    id: 'Koltiva.view.application_form.WinNotRecomended',
    title: 'Export Applicant',
    closable: true,
    modal: true,
    closeAction: 'destroy',
	width: 650,
	minWidth: 250,
	height: 225,
    animCollapse:false,
	border: false,
	modal: true,
    initComponent: function() {
        var thisObj = this; 
		thisObj.items = [
		{xtype: 'form',
            id: 'Koltiva.view.application_form.WinNotRecomended-Form',
            padding: '5 25 5 8',
            items:[ 
			{	
			columnWidth: '.5',
			padding:'5 25 5 8',
			layout:'form',  
			items:[
					{
					xtype: 'combobox',
					fieldLabel: 'Certificated Programs',
					labelWidth: 200, 
					store: cmbCertProgramsGeneral,
					queryMode: 'local',
					displayField: 'label',
					valueField: 'id',
					id: 'Koltiva.view.application_form-Form-CertProgID',
					name: 'Koltiva.view.application_form-Form-CertProgID',
					allowBlank: true,
					listeners: {
						change: function(cb, nv, ov) {
							cmbCertHolderGeneral.setStoreVar({
								CertProgID: nv
							});
							cmbCertHolderGeneral.load();  
						},
						select :function(cb, nv, ov) {
							Ext.getCmp('Koltiva.view.application_form-Form-CertHolderID').setValue('');
							Ext.getCmp('Koltiva.view.application_form-Form-IMSID').setValue('');
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
					id: 'Koltiva.view.application_form-Form-CertHolderID',
					name: 'Koltiva.view.application_form-Form-CertHolderID',
					allowBlank: true,
					listeners: {
						change: function(cb, nv, ov) {
							//Ext.getCmp('Koltiva.view.application_form-Form-IMSID').setValue('');
							cmbImsEventGeneral.setStoreVar({
								CertHolderID: nv
							});
							cmbImsEventGeneral.load();  
						},
						select :function(cb, nv, ov) {
							Ext.getCmp('Koltiva.view.application_form-Form-IMSID').setValue(''); 
						}
					}
				}, 
				{
					xtype: 'combobox',
					fieldLabel: 'IMS Event',
					labelWidth: 200, 
					store: cmbImsEventGeneral,
					queryMode: 'local',
					displayField: 'label',
					valueField: 'id',
					id: 'Koltiva.view.application_form-Form-IMSID',
					name: 'Koltiva.view.application_form-Form-IMSID',
					allowBlank: true,
					listeners: {
						change: function(cb, nv, ov) {
							var item =  getCBSValue (cb, 'id', 'IMSMasterID');
							Ext.getCmp('Koltiva.view.application_form.Form-Form-IMSMasterID').setValue(item);  
						}
					}
				}
				]
			}]
		}];
		
		
		thisObj.buttons = [{
            id: 'Koltiva.view.application_form.WinNotRecomended-Form-btnSave',
            text: 'Export',
            margin: '5px',
            scale: 'large',
            ui: 's-button',
            cls: 's-blue',
            handler: function() {
                var CertProgID = Ext.getCmp('Koltiva.view.application_form-Form-CertProgID').getValue();
				var CertHolderID = Ext.getCmp('Koltiva.view.application_form-Form-CertHolderID').getValue();
				var IMSID = Ext.getCmp('Koltiva.view.application_form-Form-IMSID').getValue();
				
				if( CertProgID=='' || CertHolderID == '' || IMSID == '' ){
                     Ext.MessageBox.alert('Warning', 'Please Fill the All option !');
				}else{
					Ext.MessageBox.confirm('Message', lang('Apakah anda yakin untuk download (Excel)?'), function (btn) {
						if (btn == 'yes') {
							Ext.MessageBox.show({
								msg: 'Please wait...',
								progressText: 'Exporting...',
								width: 300,
								wait: true,
								waitConfig: {
									interval: 200
								},
								icon: 'ext-mb-download', //custom class in msg-box.html
								animateTarget: 'mb7'
							});

							try {
								Ext.destroy(Ext.get('downloadIframe'));
							} catch (e) {}

							// Ext.DomHelper.append(document.body, {
							//     tag: 'iframe',
							//     id:'downloadIframe',
							//     frameBorder: 0,
							//     width: 0,
							//     height: 0,
							//     css: 'display:none;visibility:hidden;height:0px;',
							//     src: m_api+'/grower/export_farmers/'+param_string
							// });
							// Ext.MessageBox.hide();

							Ext.Ajax.request({
								url: m_api + '/application_form/application_store/print_tidaklolos/'+ CertProgID +'_' + CertHolderID +'_' + IMSID,

								method: 'GET',
								waitMsg: lang('Please Wait'),
								timeout: 360000,
								success: function (data) {
									Ext.MessageBox.hide();
									var jsonResp = JSON.parse(data.responseText);
									if(jsonResp.filenya == ''){
										Ext.MessageBox.show({
											title: 'Notifications',
											msg: lang('Data Empty.'),
											buttons: Ext.MessageBox.OK,
											animateTarget: 'mb9',
											icon: 'ext-mb-error'
										});
										return;
									}
									window.location = jsonResp.filenya;
									// window.location = jsonResp.filenya;
								},
								failure: function () {
									Ext.MessageBox.hide();
									Ext.MessageBox.show({
										title: 'Notifications',
										msg: lang('Failed to export, Please try again.'),
										buttons: Ext.MessageBox.OK,
										animateTarget: 'mb9',
										icon: 'ext-mb-error'
									});
								}
							});
						}
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
				var IMSSocID = Ext.getCmp('Koltiva.view.application_form.Form-Form-IMSSocID').getValue();  
				Ext.getCmp('Koltiva.view.application_form.GridParticipant-FormDetail').store.reload({ params : { IMSSocID: IMSSocID } });  
            }
        }];
		
		this.callParent(arguments);
	},
	listeners: {
       
	}
	
});

 
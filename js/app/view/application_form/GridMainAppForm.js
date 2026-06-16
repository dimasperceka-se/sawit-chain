var contextMenuGrid = Ext.create('Ext.menu.Menu',{
        items: [
        {
            icon: varjs.config.base_url + 'images/icons/silk/application_form.png',
            text: lang('View'),
            hidden: m_act_view,
			id :'Koltiva.view.application_form.GridMainAppForm-View',
            handler: function(){
                var sm = Ext.getCmp('Koltiva.view.application_form.GridMainAppForm-gridMainGrid').getSelectionModel().getSelection()[0];

				if(sm == undefined){
					Ext.MessageBox.show({
						title: 'Attention',
						msg: lang('No Application Data selected'),
						buttons: Ext.MessageBox.OK,
						animateTarget: 'mb9',
						icon: 'ext-mb-info'
					}); 
				}else{ 
					var WinRegisterAppForm = Ext.create('Koltiva.view.application_form.WinRegisterAppForm',{
						viewVar: {
							opsiDisplay: 'view',
							typeStatus : 'recomended',
							ApplicantID: sm.get('ApplicantID')
						}
					});
					if (!WinRegisterAppForm.isVisible()) {
						WinRegisterAppForm.center();
						WinRegisterAppForm.show();
					} else {
						WinRegisterAppForm.close();
					}
				} 
			}
        }, 
		{
            icon: varjs.config.base_url + 'images/icons/silk/pencil.png',
            text: lang('Update'),
            hidden: !m_act_update,
			id :'Koltiva.view.application_form.GridMainAppForm-update',
            handler: function(){
                var sm = Ext.getCmp('Koltiva.view.application_form.GridMainAppForm-gridMainGrid').getSelectionModel().getSelection()[0];

				if(sm == undefined){
					Ext.MessageBox.show({
						title: 'Attention',
						msg: lang('No Application Data selected'),
						buttons: Ext.MessageBox.OK,
						animateTarget: 'mb9',
						icon: 'ext-mb-info'
					});
				}else{
					var WinRegisterAppForm = Ext.create('Koltiva.view.application_form.WinRegisterAppForm',{
						viewVar: {
							opsiDisplay: 'update',
							ApplicantID: sm.get('ApplicantID')
						}
					});
					if (!WinRegisterAppForm.isVisible()) {
						WinRegisterAppForm.center();
						WinRegisterAppForm.show();
					} else {
						WinRegisterAppForm.close();
					}
				} 
			}
        }, 
        {
            icon: varjs.config.base_url + 'images/icons/silk/delete.png',
            text: lang('Delete'),
			id :'Koltiva.view.application_form.GridMainAppForm-delete',
            hidden: !m_act_delete,
            handler: function() {
                var smb = Ext.getCmp('Koltiva.view.application_form.GridMainAppForm-gridMainGrid').getSelectionModel().getSelection()[0]
								Ext.MessageBox.confirm('Message', lang('Apakah anda mau menghapus data ini?'), function (btn) {
									if (btn == 'yes') {
										Ext.Ajax.request({
											waitMsg: lang('Please Wait'),
											 url: m_api + '/application_form/application_store/appform',
											method: 'DELETE',
											params: {ApplicantID: smb.raw.ApplicantID},
											success: function (response, opts) {
												var obj = Ext.decode(response.responseText);
												switch (obj.success) {
													case true:
														storeGridMain.load();
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
        } 
        ]
    });
	
//GRID FARMER /////////////////////////////
var storeGridMain = Ext.create('Koltiva.store.application_form.GridMain'); 
var MainGrid = {
					xtype: 'grid',
					id: 'Koltiva.view.application_form.GridMainAppForm-gridMainGrid',
					style: 'border:1px solid #CCC;margin-top:4px;',
					loadMask: true, 
					minHeight:300,
					store: storeGridMain,  
					viewConfig: {
						deferEmptyText: false,
						emptyText: lang('No data Available')
					}, 
					listeners: {
						itemclick: function(view, record, item, index, e){
							/*
							if(record.data.SocStatus == 2){
								Ext.getCmp('Koltiva.view.application_form.GridMainAppForm-update').show()
								Ext.getCmp('Koltiva.view.application_form.GridMainAppForm-delete').show()
							}else{
								Ext.getCmp('Koltiva.view.application_form.GridMainAppForm-update').hide()
								Ext.getCmp('Koltiva.view.application_form.GridMainAppForm-delete').hide()
							}
							*/
						   contextMenuGrid.showAt(e.getXY());
						},
						afterRender : function(store, v)
						{
							storeGridMain.load();  
						}
					},
					dockedItems: [
					{
						xtype: 'pagingtoolbar',
						store: storeGridMain, // same store GridPanel is using
						dock: 'bottom',
						displayInfo: true
					},{
						xtype: 'toolbar',
						items: [{
							icon: varjs.config.base_url + 'images/icons/new/add.png',cls:'Sfr_BtnGridGreen', overCls:'Sfr_BtnGridGreen-Hover',
							text: lang('Add'), 
							hidden: !m_act_add,
							handler: function() {
								 
								//buka popup form
								var WinRegisterAppForm = Ext.create('Koltiva.view.application_form.WinRegisterAppForm',{
											viewVar: {
												opsiDisplay: 'insert' 
											}
									});
									
								if (!WinRegisterAppForm.isVisible()) {
									WinRegisterAppForm.center();
									WinRegisterAppForm.show();
								} else {
									WinRegisterAppForm.close();
								}
							}
						},  
						{
							icon: varjs.config.base_url + 'images/icons/silk/page_excel.png',
							xtype: 'splitbutton',
							text: lang('Export to Excel'),
							menu: {
								items: [
								{
									icon: varjs.config.base_url + 'images/icons/silk/page_excel.png',
									text: 'Export All', 
									handler: function() {
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
                                            url: m_api + '/application_form/application_store/print_afl/',

                                            method: 'GET',
                                            waitMsg: lang('Please Wait'),
                                            timeout: 360000,
                                            success: function (data) {
                                                Ext.MessageBox.hide();
                                                var jsonResp = JSON.parse(data.responseText);
                                                window.location = jsonResp.filenya;
                                            },
                                            failure: function () {
                                                Ext.MessageBox.hide();
                                                Ext.MessageBox.show({
                                                    title: 'Notifications',
                                                    msg: 'Failed to export, Please try again.',
                                                    buttons: Ext.MessageBox.OK,
                                                    animateTarget: 'mb9',
                                                    icon: 'ext-mb-error'
                                                });
                                            }
                                        });
									} 	
								},
								{
									icon: varjs.config.base_url + 'images/icons/silk/page_excel.png',
									text: 'Export Applicant Not Recommended', 
									handler: function() {
										var WinOptionForm = Ext.create('Koltiva.view.application_form.WinNotRecomended');  
										if (!WinOptionForm.isVisible()) {
											WinOptionForm.center();
											WinOptionForm.show();
										} else {
											WinOptionForm.close();
										} 
									} 	
								}
								]
							}
						}, 
						{
							icon: varjs.config.base_url + 'images/icons/silk/application_osx_get.png',
							text: lang('Upload Files'), 
							handler: function(){ 
								winUpload.show();
							}
						},
						{
							xtype: 'textfield',
							name: 'key',
							emptyText: lang('Cari berdasar nama/ID'),
							id: 'key',
							listeners: {
								specialkey: submitOnEnter
							}
						}, {
							xtype: 'button',
							margin: '0px 0px 0px 6px',
							text: lang('Search'),
							handler: function () {
								storeGridMain.load({
									params: {
										key: Ext.getCmp('key').getValue()
									}
								});
							}
						},{
							xtype:'tbspacer',
							flex:1
						},{
							icon: varjs.config.base_url + 'images/icons/new/reload.png', cls:'Sfr_BtnGridGreen', overCls:'Sfr_BtnGridGreen-Hover',
							text: lang('Generate to Member'),
							hidden: !m_act_add,
							handler: function() {
								var WinGenerateMember = Ext.create('Koltiva.view.application_form.WinGenerateMember');
								WinGenerateMember.setViewVar({
									opsiDisplay:'insert',
									callerStore: storeGridMain
								});
								if (!WinGenerateMember.isVisible()) {
									WinGenerateMember.center();
									WinGenerateMember.show();
								} else {
									WinGenerateMember.close();
								}
							}
						}]
					}
					], 
					columns: [ {
						id: 'Koltiva.view.report.GridMainAppForm-colreportID',
						dataIndex: 'MemberID',
						hidden:true
					},{
						text: 'No',
						xtype: 'rownumberer',
						width: '5%'
					},{  
						text: lang('Applicant ID'),
						flex:1,						
						dataIndex: 'DisplayID'
					},{  
						text: lang('Applicant Name'),
						flex:1,						
						dataIndex: 'Fullname'
					},{  
						
						text: lang('Date Collection'),
						flex:1,
						dataIndex: 'DateCollection',
						dateFormat: 'Y-m-d H:i:s'
					},{  
						text: lang('Gender'),
						flex:1,
						dataIndex: 'Gender',
						renderer: function(value){
							if (value === 'f') {
								return lang('Perempuan');
							}
							return lang('Laki-laki');
						}
					},{  
						text: lang('Age'),
						flex:1,
						dataIndex: 'Age'
					},{  
						text: lang('Farmer Group'),
						flex:1,
						dataIndex: 'GroupName'
					},{  
						text: lang('Village'),
						flex:1,
						dataIndex: 'VillageName'
					},{  
						text: lang('Member Status'),
						flex:1,
						dataIndex: 'MemberStatus'
					},{  
						text: lang('Date Updated'),
						flex:1,
						dataIndex: 'DateUpdated'
					} ]
		};

 
 var DataFormUpload = Ext.create('Ext.form.Panel', {
        height: 659,
        autoScroll: false, 
        id: 'Koltivaapplication_formdataFormUpload',
        fieldDefaults: {
            labelAlign: 'left',
            labelWidth: 150,
            anchor: '100%'
        },
        items: [{
            layout: 'column',
            border: false,
            items: [ 
					{
					xtype: 'form',
					fileUpload: true,
					enctype:'multipart/form-data',
					id:'Koltivaapplication_formFormUpload',
					fieldDefaults: {
						labelAlign: 'left',
						labelWidth: 150,
						anchor: '50%',
						padding: 5,
					},
					items: [
					{
						xtype: 'hiddenfield',
						id: 'upload_ApplicantID',
						name: 'ApplicantID',
					},
					{
						xtype: 'filefield',
						name: 'File',
						id: 'ApplicantIDFile[]',
						multiple: false, 
						fieldLabel: lang('File'),
						padding: 5,
						listeners:{
							afterrender:function(cmp){
								cmp.fileInputEl.set({
									multiple:'multiple'
								});
							}
						}
					} 
					]
				}]
        }
        ],
        buttons: [{
            id: 'KoltivaUploadsave_file',
            text: lang('Save'),
            margin: '5px',
            scale: 'large',
            ui: 's-button',
            cls: 's-blue ',
            handler: function() {
                var form = Ext.getCmp('Koltivaapplication_formdataFormUpload').getForm();
                form.submit({
                    url: m_api + '/application_form/application_store/importdataapplicantxls',
                    method: 'POST',
                    waitMsg: lang('Sending data...'),
                    success: function(fp, o) {
                        Ext.MessageBox.alert('Success', lang('Data saved.'));
						winUpload.hide();
						Ext.getCmp('Koltiva.view.application_form.GridMainAppForm-gridMainGrid').store.reload();
                    },
                    failure: function(form, action) {
                        var msg = action.result.msg ? action.result.msg :action.result.error;
                        Ext.Msg.alert('Failed', msg);
                    }
                });
            }
        }, {
            text: lang('Close'),
            margin: '5px',
            scale: 'large',
            ui: 's-button',
            cls: 's-grey',
            disabled: false,
            handler: function() {
                winUpload.hide();
            }
        }]
    });

var winUpload = Ext.create('widget.window', {
				title: 'Data Applicant .xlsx',
				frame: false,
				closable: true,
				id: 'Koltivaapplication_formwinupload',
				modal: true,
				closeAction: 'show',
				width: '40%',
				height: '25%',
				layout: 'fit',
				items: [DataFormUpload]
			});
			
function submitOnEnter(field, event) {
        if (event.getKey() == event.ENTER) {
            storeGridMain.load({
                params: {
                    key: Ext.getCmp('key').getValue()
                }
            });
        }
}
	
Ext.define('Koltiva.view.application_form.GridMainAppForm' ,{
    extend: 'Ext.panel.Panel',
    id: 'Koltiva.view.application_form.GridMainAppForm',
    renderTo: 'ext-content',
    listeners: {
        afterRender: function(){
            
        }
    },
    style:'padding:0 15px 15px 15px;margin:5px 0 0 0;',
    setFilterListreport: function(){
        
    },
    submitOnEnterGridreport: function(field, event){
    	 
    },
    initComponent: function() {
        var thisObj = this; 
        //items
        thisObj.items = [MainGrid];  
        this.callParent(arguments);
    }
});



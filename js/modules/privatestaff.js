Ext.onReady(function(){
   Ext.tip.QuickTipManager.init();
    Ext.define('FarmerAdd.Model', {
        extend: 'Ext.data.Model',
        fields: ['participant_id','id_staff','staf','wstart','wend','bstart','bend']
    });
    var store = Ext.create('Ext.data.Store', {
        extend: 'Ext.data.Model',
        fields: ['id','StaffName','PartnerName','OfficialCellphone','OfficialStaffEmail'],
        autoLoad: true,
        pageSize: 50,
        proxy: {
            type: 'ajax',
            url: m_crud+'s',
            reader: {
                type: 'json',
                root: 'data',
                totalProperty: 'total'
            }
        }
    });
    var mc_partner = Ext.create('Ext.data.Store', {
        extend: 'Ext.data.Model',
        fields: ['id','label'],
        autoLoad: true,
        proxy: {
            type: 'ajax',
            url: m_partner,
            reader: {
                type: 'json',
                root: 'data'
            }
        }
    });
   var mc_Provinsi = Ext.create('Ext.data.Store', {
      extend: 'Ext.data.Model',
      fields: ['id','label'],
      autoLoad: true,
      pageSize: 10,
      proxy: {
         type: 'ajax',
         url: m_Provinsi,
         reader: {
            type: 'json',
            root: 'data'
         }
      }
   });
    var mc_Kabupaten = Ext.create('Ext.data.Store', {
        extend: 'Ext.data.Model',
        fields: ['id','label'],
        autoLoad: true,
        proxy: {
            type: 'ajax',
            url: m_Kabupaten,
            reader: {
                type: 'json',
                root: 'data'
            }
        }
    });
    var mc_group = Ext.create('Ext.data.Store', {
        extend: 'Ext.data.Model',
        fields: ['GroupId','GroupName'],
        autoLoad: true,
        proxy: {
            type: 'ajax',
            url: m_group,
            reader: {
                type: 'json',
                root: 'data'
            }
        }
    });

    function displayFormWindow(){
        if(!win.isVisible()){
            DataForm.getForm().reset();
            win.show();
            Ext.getCmp('PersonNm').focus(true,true);
        } else {
            win.hide(this, function() {});
            win.toFront();
        }
    }
    var DataForm = Ext.create('Ext.form.Panel', {
        frame: false,
        height: 500,
        autoScroll: true,
        width: 1204,
        bodyPadding: 5,
        fileUpload: true,
        enctype:'multipart/form-data',
        id:'dataForm',
        fieldDefaults: {
            //labelAlign: 'left',
            //labelWidth: 120,
            //anchor: '100%'
			msgTarget: 'side',
            blankText: 'Tidak Boleh Kosong',
            labelAlign: 'left',
            labelWidth: 120,
            anchor: '100%'
        },
        xtype: 'panel',
		autoScroll: true,
        items: [{
			xtype: 'textfield',
            id: 'id',
            name: 'id',
            inputType:'hidden'
		},{
			layout: 'column',
            items: [{
				columnWidth: 0.5,
                margin: 5,
				items:[{
					xtype: 'fieldset',
					title:lang('Identitas'),
					flex: 1,
					items: [{
						xtype: 'textfield',
						fieldLabel: lang('Staff ID'),
						id: 'ExtId',
						name: 'ExtId',
						hidden:'true'
					},{
						xtype: 'textfield',
						fieldLabel: lang('Staff Name'),
						id: 'PersonNm',
						name: 'PersonNm'
					},{
						fieldLabel : lang('Jenis Kelamin'),
						xtype      : 'radiogroup',
						columns:2,
						//width: '50%',
						style:{width:"30%"},
						items: [{
							boxLabel  : lang('Laki-laki'),
							name      : 'Gender',
							inputValue: '1',
							id        : 'Gender1'
						},{
							boxLabel  : lang('Perempuan'),
							name      : 'Gender',
							inputValue: '2',
							id        : 'Gender2'
						}]
					},{
						xtype: 'datefield',
						fieldLabel: lang('Tanggal Lahir'),
						id: 'BirthDttm',
						name: 'BirthDttm',
						format:'Y-m-d'
					},{
						xtype: 'textfield',
						fieldLabel: lang('Private Phone'),
						id: 'PrivatePhone',
						name: 'PrivatePhone'
					},{
						xtype: 'textfield',
						fieldLabel: lang('Official Phone'),
						id: 'OfficialPhone',
						name: 'OfficialPhone'
					},{
						xtype: 'textfield',
						fieldLabel: lang('Private E-mail'),
						id: 'PrivateE-mail',
						name: 'PrivateE-mail'
					},{
						xtype: 'textfield',
						fieldLabel: lang('Official E-mail'),
						id: 'OfficialE-mail',
						name: 'OfficialE-mail'
					}]
				}]
			},{
				columnWidth: 0.5,
                margin: 5,
				items:[{
					xtype: 'fieldset',
					title: lang('Institusi/Partner'),
					items: [{
						id: 'PartnerId',
						name: 'PartnerId',
						xtype: 'combobox',
						fieldLabel: lang('Partner'),
						store:mc_partner,
						displayField: 'label',
						queryMode: 'local',
						valueField: 'id'
					},{
						id: 'Provinsi',
						name: 'Provinsi',
						xtype: 'combo',
						fieldLabel: lang('Provinsi'),
						store:mc_Provinsi,
						displayField: 'label',
						valueField: 'label',
						queryMode: 'local',
						listeners: {
							change: function (cb, nv, ov) {
								mc_Kabupaten.load({
									params: {
										key: Ext.getCmp('Provinsi').getValue()
									}
								});
							}
						}
					},{
						id: 'DistrictId',
						name: 'DistrictId',
						xtype: 'combobox',
						fieldLabel: lang('Location'),
						store:mc_Kabupaten,
						displayField: 'label',
						queryMode: 'local',
						valueField: 'label'
					}]
				}]
			}]
		},{ 
			xtype: 'tabpanel',
			activeTab: 0,
			items: [{
				xtype: 'panel',
				title: lang('Photo'),
				items: [{
					layout: 'column',
					items: [{
						columnWidth: 0.28,
						items:[{
							xtype: 'fileuploadfield',
							fieldLabel: lang('Photo'),
							labelWidth: 60,
							width:300,
							id: 'Photo',
							name: 'Photo',
							buttonText: 'Browse',
							listeners: {
								'change': function(fb, v){
									var form = this.up('form').getForm();
									form.submit({
										url: m_crud+'_image',
										waitMsg: lang('Sending Photo...'),
										success: function(fp, o) {
											Ext.getCmp('iphoto').setSrc(m_photo+o.result.file);
											Ext.getCmp('Photo_old').setValue(o.result.file);
										}	
									});
								}
							}
						}]
					},{
						columnWidth: 0.28,
						items:[{
							xtype:'image',
							id:'iphoto',
							height:'120px',
							style:'margin-top:3px;'
						}]
					}]                
				},{
					xtype: 'textfield',
					id: 'Photo_old',
					name: 'Photo_old',
					inputType:'hidden'
				}]
			},{
				xtype: 'panel',
				title: lang('User'),
				items: [{
                    xtype: 'textfield',
                    id: 'userid',
                    name: 'userid',
                    inputType:'hidden'
                },{
                    xtype: 'textfield',
                    fieldLabel: lang('Username'),
                    id: 'UserName',
                    name: 'UserName',
                    padding:'0 0 0 5px',
					labelWidth:90,
					width:350
                },{
                    xtype: 'textfield',
                    fieldLabel: lang('Password'),
                    id: 'UserPassword',
                    name: 'UserPassword',
                    padding:'0 0 0 5px',
					labelWidth:90,
					width:350
                },{
					xtype :'button',
                    margin: '0px 0px 0px 6px',
                    text: lang('Generate'),
                    handler: function() {
						Ext.getCmp('UserPassword').setValue(Math.random().toString(36).substring(7))
                    }
                },{
                    fieldLabel : lang('Active'),
                    xtype      : 'radiogroup',
                    columns: 3,
                    vertical: true,
                    width: '30%',
					labelWidth:90,
                    padding:'0 0 0 5px',
                    items: [{
                        boxLabel  : lang('Yes'),
                        name      : 'UserActive',
                        inputValue: 'Yes',
                        id        : 'UserActive1'
                    },{
                        boxLabel  : lang('No'),
                        name      : 'UserActive',
                        inputValue: 'No',
                        id        : 'UserActive2'
                    }]
                }]
			},{
				xtype: 'panel',
				title: lang('Role'),
				items: [{
                    id: 'UserGroupGroupId',
                    name: 'UserGroupGroupId',
                    xtype: 'combobox',
                    fieldLabel: lang('Group'),
                    store:mc_group,
                    displayField: 'GroupName',
                    valueField: 'GroupId',
                    queryMode:'local',
                    padding:'0 0 0 5px',
					labelWidth:90,
					width:350
                }]
			}]
		}],
        buttons: [{
            id:'saveButton',
            text: lang('Save'),
			margin: '5px',
            scale: 'large',
            ui: 's-button',
            cls: 's-blue',
            handler: function() {
				Ext.Ajax.request({
					url: m_cek_username,
					method: 'GET',
					params: {id:Ext.getCmp('userid').getValue(),username: Ext.getCmp('UserName').getValue()},
					success: function(fp, o){
						var r = Ext.decode(fp.responseText);
						if (r.success || Ext.getCmp('UserName').getValue()=='') {
							var form = Ext.getCmp('dataForm').getForm();
							var urle;
							if (Ext.getCmp('id').getValue()!='') urle = m_crud+'u'; else urle = m_crud;
							form.submit({
								url: urle,
								waitMsg: lang('Sending data...'),
								success: function(fp, o) {
									Ext.MessageBox.alert('Success', lang('Data saved.'));
									win.hide(this, function() {
										store.load();
									});
								}
							});
						} else Ext.MessageBox.alert('Status', lang('Username telah digunakan, silahkan ganti dengan username yang lain.'));   
					}
				});
            }
        },{
            text: lang('Close'),
            margin: '5px',
            scale: 'large',
            ui: 's-button',
            cls: 's-grey',
            disabled: false,
            handler: function() {
				win.hide();
			}
        }]
    });
    var win = Ext.create('widget.window', {
        title: lang('Data Private Staff'),
        id:'win',
        closable: true,
        modal:true,
        closeAction: 'show',
        width: '90%',
        height: '90%',
        layout: {
            type: 'fit'
        },
        items: [DataForm]
    });
    function submitOnEnter(field, event) {
    	if (event.getKey() == event.ENTER) {
           store.load({
           params: {
               key: Ext.getCmp('key').getValue()
           }});
    	}
    }    
    var grid = Ext.create('Ext.grid.Panel', {
       store: store,
       id:'grid',
       width: '100%',
       //title: lang('Staff List'),
       style: 'border:1px solid #CCC;',
       renderTo: 'ext-content',
       loadMask: true,
       selType: 'rowmodel',
       listeners : {
          itemdblclick: function(dv, record, item, index, e) {
            displayFormWindow();
            var sm = record;
            Ext.Ajax.request({
               url: m_crud,
               method: 'GET',
               params: {id: sm.get('id')},
               success: function(fp, o){
                   var r = Ext.decode(fp.responseText);
                   Ext.getCmp('id').setValue(sm.get('id'));
                   Ext.getCmp('PartnerId').setValue(r.PartnerID);
                   Ext.getCmp('ExtId').setValue(r.PrivateStaffID);
                   Ext.getCmp('PersonNm').setValue(r.StaffName);
                   Ext.getCmp('BirthDttm').setValue(r.StaffBirth);
                   Ext.getCmp('Photo_old').setValue(r.Photo);
                   Ext.getCmp('PrivatePhone').setValue(r.PrivateCellphone);
                   Ext.getCmp('OfficialPhone').setValue(r.OfficialCellphone);
                   Ext.getCmp('PrivateE-mail').setValue(r.PrivateStaffEmail);
                   Ext.getCmp('OfficialE-mail').setValue(r.OfficialStaffEmail);
                   Ext.getCmp('Provinsi').setValue(r.provinsi);
                   Ext.getCmp('DistrictId').setValue(r.kabupaten);

                   Ext.getCmp('UserName').setValue(r.UserName);
                   Ext.getCmp('userid').setValue(r.UserId);
                   //Ext.getCmp('UserPassword').setValue(r.UserPassword);
                   Ext.getCmp('UserGroupGroupId').setValue(r.UserGroupGroupId);
                   Ext.getCmp('Photo_old').setValue(r.Photo);
				   var photo = 'no-user.jpg';
					if(r.Photo != ''){
						photo = r.Photo; 
					}
					Ext.getCmp('iphoto').setSrc(m_photo+photo);
                   //Ext.getCmp('iphoto').setSrc(m_photo+r.Photo);
                   if (r.UserActive=='Yes') Ext.getCmp('UserActive1').setValue(true);
                   if (r.UserActive=='No') Ext.getCmp('UserActive2').setValue(true);
                   if (r.StaffGender=='1') Ext.getCmp('Gender1').setValue(true);
                   if (r.StaffGender=='2') Ext.getCmp('Gender2').setValue(true);
               }
            });
          }
       },       
       dockedItems: [{
              xtype: 'pagingtoolbar',
              store: store,   // same store GridPanel is using
              dock: 'bottom',
              displayInfo: true
         },{
            xtype: 'toolbar',
            items: [
            {
               icon: varjs.config.base_url + 'images/icons/new/add.png', cls:'Sfr_BtnGridGreen', overCls:'Sfr_BtnGridGreen-Hover', 
               text: lang('Add'),
               scope: this,
               handler : displayFormWindow,
               cls : m_act_add
            },{
               icon: varjs.config.base_url+'images/icons/silk/pencil.png', 
               text: lang('Update'),
               scope: this,
               handler : function(){
                  displayFormWindow();
                  var sm = Ext.getCmp('grid').getSelectionModel().getSelection()[0];
                  Ext.Ajax.request({
                     url: m_crud,
                     method: 'GET',
                     params: {id: sm.get('id')},
                     success: function(fp, o){
                         var r = Ext.decode(fp.responseText);
                         Ext.getCmp('id').setValue(sm.get('id'));
                         Ext.getCmp('PartnerId').setValue(r.PartnerID);
                         Ext.getCmp('ExtId').setValue(r.PrivateStaffID);
                         Ext.getCmp('PersonNm').setValue(r.StaffName);
                         Ext.getCmp('BirthDttm').setValue(r.StaffBirth);
                         Ext.getCmp('Photo_old').setValue(r.Photo);
                         Ext.getCmp('PrivatePhone').setValue(r.PrivateCellphone);
                         Ext.getCmp('OfficialPhone').setValue(r.OfficialCellphone);
                         Ext.getCmp('PrivateE-mail').setValue(r.PrivateStaffEmail);
                         Ext.getCmp('OfficialE-mail').setValue(r.OfficialStaffEmail);
                          console.log(sm.get('id'));
      
                         Ext.getCmp('UserName').setValue(r.UserName);
                         Ext.getCmp('userid').setValue(r.UserId);
                         //Ext.getCmp('UserPassword').setValue(r.UserPassword);
                         Ext.getCmp('UserGroupGroupId').setValue(r.UserGroupGroupId);
                         Ext.getCmp('Photo_old').setValue(r.Photo);
						 var photo = 'no-user.jpg';
						 if(r.Photo != '\\Server\@ Project Data\13 Photo Staff\Kosong.jpg'){
							photo = r.Photo; 
						 }
						 Ext.getCmp('iphoto').setSrc(m_photo+photo);
                         //Ext.getCmp('iphoto').setSrc(m_photo+r.Photo);
                         if (r.UserActive=='Yes') Ext.getCmp('UserActive1').setValue(true);
                         if (r.UserActive=='No') Ext.getCmp('UserActive2').setValue(true);
                         if (r.StaffGender=='1') Ext.getCmp('Gender1').setValue(true);
                         if (r.StaffGender=='2') Ext.getCmp('Gender2').setValue(true);
                     }
                  });               
               },
               cls : m_act_update
            },{
               itemId: 'remove',
               icon: varjs.config.base_url+'images/icons/silk/delete.png',
               cls:m_act_delete,
               text: lang('Hapus'),
               scope: this,
               handler : function(){
                 var smb = Ext.getCmp('grid').getSelectionModel().getSelection()[0];
                 Ext.MessageBox.confirm('Message', lang('Apakah anda mau menghapus data ini ?') , function(btn){
                     if(btn == 'yes'){
                        Ext.Ajax.request({
                        waitMsg: lang('Please Wait'),
                        url: m_crud,
                        method : 'DELETE',
                        params: {id:  smb.raw.id},
                        success: function(response, opts){
                           var obj = Ext.decode(response.responseText);
                           switch(obj.success){
                              case true: store.load();
                              break;
                              default: Ext.MessageBox.alert('Warning',obj.message);
                              break;
                           }
                        },
                        failure: function(response, opts){
                           var obj = Ext.decode(response.responseText);
                           Ext.MessageBox.alert('error',lang('Could not connect to the database. Retry later'));
                        }
                     });
                     }
                 });
               }
            },{
            xtype: 'textfield',
            name: 'key', baseCls:'Sfr_TxtfieldSearchGrid',
            id: 'key',
            listeners: {
        		specialkey: submitOnEnter
        	}
           },{
           xtype :'button',
           margin: '0px 0px 0px 6px',
           text: lang('Search'),
           handler: function() {
           store.load({
               params: {
                   key: Ext.getCmp('key').getValue()
               }});
           }
           }]
    }],
    columns: [
    {
        text: lang('ID'),
        dataIndex: 'id',
        width:'5%'
    },
    {
        text: lang('Name'), 
        width: '35%',
        dataIndex: 'StaffName'
    },
    {
        text: lang('Program Partner'), 
        width: '20%',
        dataIndex: 'PartnerName'
    },
    {
        text: lang('Offical Phone'),
        width: '20%',
        dataIndex: 'PrivateCellphone'
    },
    {
        text: lang('Official E-Mail'),
        width: '20%',
        dataIndex: 'OfficialStaffEmail'
    }]
   });
});

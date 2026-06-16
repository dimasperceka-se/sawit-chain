Ext.onReady(function(){
	Ext.tip.QuickTipManager.init();
    var StaffID;
    var ProvinceID;
    var DistrictID;
    Ext.define('FarmerAdd.Model', {
        extend: 'Ext.data.Model',
        fields: ['participant_id','id_staff','staf','wstart','wend','bstart','bend']
    });
    var store = Ext.create('Ext.data.Store', {
        extend: 'Ext.data.Model',
        fields: ['id','PersonNm','Gender','StaffCellphone','PartnerName'],
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
			url: m_AllProvinsi,
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
		pageSize: 10,
		proxy: {
			type: 'ajax',
			url: m_Kabupaten,
			reader: {
				type: 'json',
				root: 'data'
			}
		}
	});
	var mc_Kabupaten_area = Ext.create('Ext.data.Store', {
		extend: 'Ext.data.Model',
		fields: ['id','label'],
		autoLoad: true,
		pageSize: 10,
		proxy: {
			type: 'ajax',
			url: m_Kabupaten,
			reader: {
				type: 'json',
				root: 'data'
			}
		}
	});
	var mc_Kecamatan = Ext.create('Ext.data.Store', {
		extend: 'Ext.data.Model',
		fields: ['id','label'],
		autoLoad: true,
		pageSize: 10,
		proxy: {
			type: 'ajax',
			url: m_Kecamatan,
			reader: {
				type: 'json',
				root: 'data'
			}
		}
	});
	var mc_Desa = Ext.create('Ext.data.Store', {
		extend: 'Ext.data.Model',
		fields: ['id','label'],
		autoLoad: true,
		pageSize: 10,
		proxy: {
			type: 'ajax',
			url: m_Desa,
			reader: {
				type: 'json',
				root: 'data'
			}
		}
    });
    var store_districtInStaff = Ext.create('Ext.data.Store', {
        extend: 'Ext.data.Model',
        fields: ['id','district', 'province'],
        proxy: {
            type: 'ajax',
            url: m_districtInStaff,
            reader: {
                type: 'json',
                root: 'data'
            }
        }
    });
    var store_Province = Ext.create('Ext.data.Store', {
        extend: 'Ext.data.Model',
        fields: ['id','province'],
        proxy: {
            type: 'ajax',
            url: m_Province,
            reader: {
                type: 'json',
                root: 'data'
            }
        }
    });
    var store_District = Ext.create('Ext.data.Store', {
        extend: 'Ext.data.Model',
        fields: ['id','district'],
        proxy: {
            type: 'ajax',
            url: m_District,
            reader: {
                type: 'json',
                root: 'data'
            }
        }
    });
    function displayFormWindow(){
         store_districtInStaff.load();
         Ext.getCmp('Desa').setValue();
         Ext.getCmp('Kecamatan').setValue();
         Ext.getCmp('Kabupaten').setValue();
        if(!win.isVisible()){
            DataForm.getForm().reset();
            win.show();
            Ext.getCmp('PersonNm').focus(true,true);
        } else {
            win.hide(this, function() {});
            win.toFront();
        }
        Ext.getCmp('win').center()
    }
	
	// general panel container
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
            msgTarget: 'side',
            blankText: 'Tidak Boleh Kosong',
            labelAlign: 'left',
            labelWidth: 140,
            anchor: '100%'
        },
		xtype: 'panel',
		autoScroll: true,
		items:[{
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
					title:'Identitas',
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
						xtype: 'textfield',
						fieldLabel: lang('NIP'),
						id: 'nip',
						name: 'nip'
					},{
						fieldLabel : lang('Jenis Kelamin'),
						xtype      : 'radiogroup',
						width: '100%',
						items: [{
							boxLabel  : lang('Laki-laki'),
							name      : 'Gender',
							inputValue: 'm',
							id        : 'Gender1'
						},{
							boxLabel  : lang('Perempuan'),
							name      : 'Gender',
							inputValue: 'f',
							id        : 'Gender2'
						}]
					},{
						fieldLabel : lang('Status Perkawinan'),
						xtype      : 'radiogroup',
						width: '100%',
						items: [{
							boxLabel  : lang('Menikah'),
							name      : 'MaritalSt',
							inputValue: '1',
							id        : 'MaritalSt1'
						},{
							boxLabel  : lang('Single'),
							name      : 'MaritalSt',
							inputValue: '2',
							id        : 'MaritalSt2'
						},{
							boxLabel  : lang('Janda'),
							name      : 'MaritalSt',
							inputValue: '3',
							id        : 'MaritalSt3'
						},{
							boxLabel  : lang('Duda'),
							name      : 'MaritalSt',
							inputValue: '4',
							id        : 'MaritalSt4'
						}]
					},{
						xtype: 'datefield',
						fieldLabel: lang('Tanggal Lahir'),
						id: 'BirthDttm',
						name: 'BirthDttm',
						format:'Y-m-d'
					},{
						xtype: 'textfield',
						fieldLabel: lang('Handphone'),
						id: 'Handphone',
						name: 'Handphone',
						hidden:true
					},{
						xtype: 'textfield',
						fieldLabel: lang('Private Cell Phone'),
						id: 'PrivatePhone',
						name: 'PrivatePhone'
					},{
						xtype: 'textfield',
						fieldLabel: lang('Official Cell Phone'),
						id: 'OfficialPhone',
						name: 'OfficialPhone'
					},{
						xtype: 'textfield',
						fieldLabel: lang('Private Email'),
						id: 'PrivateEmail',
						name: 'PrivateEmail'
					},{
						xtype: 'textfield',
						fieldLabel: lang('Official Email'),
						id: 'OfficialEmail',
						name: 'OfficialEmail'
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
								Ext.getCmp('Kabupaten').enable();
							}
						}
					},{
						id: 'Kabupaten',
						name: 'Kabupaten',
						xtype: 'combo',
						fieldLabel: lang('Kabupaten'),
						store:mc_Kabupaten,
						displayField: 'label',
						valueField: 'label',
						queryMode: 'local',
						disabled:'true',
						listeners: {
							change: function (cb, nv, ov) {
								mc_Kecamatan.load({
									params: {
										key: Ext.getCmp('Kabupaten').getValue()
									}
								});
								Ext.getCmp('Kecamatan').enable();
							}
						}
					},{
						id: 'Kecamatan',
						name: 'Kecamatan',
						xtype: 'combo',
						fieldLabel: lang('Kecamatan'),
						store:mc_Kecamatan,
						displayField: 'label',
						valueField: 'label',
						queryMode: 'local',
						disabled: 'true',
						listeners: {
							change: function (cb, nv, ov) {
								mc_Desa.load({
									params: {
										key: Ext.getCmp('Kecamatan').getValue()
									}
								});
								Ext.getCmp('Desa').enable();
								Ext.getCmp('Desa').setValue(false);
							}
						}
					},{
						id: 'Desa',
						name: 'Desa',
						xtype: 'combo',
						fieldLabel: lang('Desa'),
						store:mc_Desa,
						displayField: 'label',
						disabled: 'true',
						valueField: 'id',
						queryMode: 'local'
					},{
						xtype: 'textareafield',
						fieldLabel: lang('Alamat'),
						id: 'Address',
						name: 'Address'
					}]
				}]
			},{
				columnWidth: 0.5,
                margin: 5,
				items:[{
					xtype: 'fieldset',
					title: 'Institusi/Partner',
					flex: 1,
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
						xtype: 'radiogroup',
						fieldLabel: lang('Position'),
						columns:2,
						items: [{
							boxLabel  : lang('Field Fasilitator'),
							name      : 'Position',
							inputValue: '1',
							id        : 'Position'
						},{
							boxLabel  : lang('District Coordinator'),
							name      : 'Position',
							inputValue: '2',
							id        : 'Position2'
						},{
							boxLabel  : lang('Program Ofiicer'),
							name      : 'Position',
							inputValue: '3',
							id        : 'Position3'
						},{
							boxLabel  : lang('Area Manager'),
							name      : 'Position',
							inputValue: '4',
							id        : 'Position4'
						},{
							boxLabel  : lang('GIS Officer'),
							name      : 'Position',
							inputValue: '5',
							id        : 'Position5'
						},{
							boxLabel  : lang('Monitoring and Evaluation'),
							name      : 'Position',
							inputValue: '6',
							id        : 'Position6'
						}]
					},{
						id: 'AreaKerja',
						name: 'AreaKerja',
						xtype: 'combo',
						fieldLabel: lang('Provinsi Area Kerja'),
						store:mc_Provinsi,
						displayField: 'label',
						valueField: 'label',
						queryMode: 'local',
						listeners: {
							change: function (cb, nv, ov) {
								mc_Kabupaten_area.load({
									params: {
										key: Ext.getCmp('AreaKerja').getValue()
									}
								});
							}
						}
					},{
						id: 'DistrictId',
						name: 'DistrictId',
						xtype: 'combobox',
						fieldLabel: lang('Area Kerja'),
						store:mc_Kabupaten_area,
						displayField: 'label',
						queryMode: 'local',
						valueField: 'label'
					}]
				}]
			}]
		},{
			xtype: 'tabpanel',
			minHeight:220,
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
							id: 'Photo',
							name: 'Photo',
							labelWidth: 60,
							width:300,
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
					labelWidth:90,
                    padding:'0 0 0 5px',
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
					labelWidth:90,
                    width: '30%',
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
					labelWidth:90,
                    store:mc_group,
                    displayField: 'GroupName',
                    valueField: 'GroupId',
                    queryMode:'local',
                    padding:'0 0 0 5px',
					width:350
				}]
			},{
                xtype: 'panel',
                title: lang('Access'),
                items: [{
                    xtype: 'gridpanel',
                    id:'gaccess',
                    store: store_districtInStaff,
                    width: '100%',
                    loadMask: true,
                    selType: 'rowmodel',
                    dockedItems: [{
                        xtype: 'toolbar',
						items: [{
                            id: 'province',
                            name: 'province',
                            xtype: 'combo',
                            store: store_Province,
                            displayField: 'province',
                            valueField: 'id',
                            queryMode: 'local',
                            listeners: {
                                change: function (cb, nv, ov) {
                                    ProvinceID = nv
                                    store_District.load({
                                        params: {
                                            id: nv
										}
									});
                                    Ext.getCmp('district').reset();
								}
							}
						},{
                            id: 'district',
                            name: 'district',
                            xtype: 'combo',
                            store: store_District,
                            displayField: 'district',
                            valueField: 'id',
                            queryMode: 'local',
                            listeners: {
                                change: function (cb, nv, ov) {
                                    DistrictID = nv
                                }
							}
						},{
                            icon: varjs.config.base_url + 'images/icons/new/add.png', cls:'Sfr_BtnGridGreen', overCls:'Sfr_BtnGridGreen-Hover',
                            text: lang('Add'),
                            scope: this,
                            hidden : m_act_update,
                            handler : function() {
                                Ext.Ajax.request({
									waitMsg: lang('Please Wait'),
                                    url: m_crud+'addDistrict',
                                    method : 'PUT',
                                    params: {StaffID : StaffID, DistrictID : DistrictID },
                                    success: function(response, opts){
                                        var obj = Ext.decode(response.responseText);
                                        switch(obj.success){
                                            case true:
                                                store_districtInStaff.load({
                                                    params: {
                                                        id: StaffID
													}
												});
                                                break;
                                            default: Ext.MessageBox.alert('Warning',obj.message);
                                                break;
										}
									}
								})
							}
						},{
                            icon: varjs.config.base_url + 'images/icons/new/add.png', cls:'Sfr_BtnGridGreen', overCls:'Sfr_BtnGridGreen-Hover',
                            text: lang('All Access'),
                            scope: this,
                            hidden : m_act_update,
								handler : function() {
									Ext.Ajax.request({
                                        waitMsg: lang('Please Wait'),
                                        url: m_crud+'_districts',
                                        method : 'GET',
                                        success: function(response, opts){
                                            var obj = Ext.decode(response.responseText);
                                            //console.log(obj);
											//console.log('-');
                                            Ext.Array.each(obj.data, function(name, index, object) {
												console.log(object[index].id);
                                                Ext.Ajax.request({
                                                    waitMsg: lang('Please Wait'),
                                                    url: m_crud+'addDistrict',
                                                    method : 'PUT',
                                                    params: {StaffID : StaffID, DistrictID : object[index].id },
                                                    success: function(response, opts){
														/*var obj = Ext.decode(response.responseText);
                                                        switch(obj.success){
                                                        case true:
                                                        store_districtInStaff.load({
															params: {
																id: StaffID
															}
														});
                                                        break;
                                                        default: Ext.MessageBox.alert('Warning',obj.message);
                                                            break;
                                                        }
                                                        */
													}
												})
											});
                                                store_districtInStaff.load({
                                                    params: {
                                                        id: StaffID
                                                    }
												});
										}
									})
								}
						}]
					}],
                    columns: [{
                        text: lang('No'),
                        xtype: 'rownumberer',
                        width:'10%'
                    },{
                        text: lang('District'),
						dataIndex: 'district',
                        width:'35%'
					},{
                        text: lang('Province'),
                        dataIndex: 'province',
                        width:'35%'
					},{
                        text: lang('Action'),
                        xtype: 'actioncolumn',
                        width: '20%',
                        items: [{
							icon: varjs.config.base_url+'images/icons/silk/delete.png',
                            tooltip: 'Delete',
                            hidden : m_act_update,
                            handler : function(grid, rowIndex, colIndex){
								var sma = grid.getStore().getAt(rowIndex);
                                DistrictID =  sma.get('id');
                                Ext.MessageBox.confirm('Message', lang('Apakah anda mau menghapus data ini ?') , function(btn){
                                              if(btn == 'yes') {
                                                  Ext.Ajax.request({
                                                      waitMsg: lang('Please Wait'),
                                                      url: m_crud+'deldist',
                                                      method : 'PUT',
                                                      params: {StaffID : StaffID, DistrictID : DistrictID},
                                                      success: function(response, opts){
                                                          var obj = Ext.decode(response.responseText);
                                                          switch(obj.success){
                                                              case true:
                                                                  store_districtInStaff.load({
                                                                      params: {
                                                                          id: StaffID
                                                                      }});
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
                                  }]
                              }]
                      }
                  ]
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
						} else Ext.MessageBox.alert('Status', 'Username telah digunakan, silahkan ganti dengan username yang lain.');   
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
        title: lang('Data Staff'),
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
				   //var photo = 'no-user.jpeg';
				   //if(r.Photo != ''){
				   //	   photo = r.Photo;
				   //} 
                   Ext.getCmp('id').setValue(sm.get('id'));
                   Ext.getCmp('PartnerId').setValue(r.PartnerId);
                   Ext.getCmp('PersonNm').setValue(r.PersonNm);
                   Ext.getCmp('BirthDttm').setValue(r.BirthDttm);
                   Ext.getCmp('Photo_old').setValue(r.Photo);
                   Ext.getCmp('Address').setValue(r.Address);
                   if (r.MaritalSt=='1') Ext.getCmp('MaritalSt1').setValue(true);
                   if (r.MaritalSt=='2') Ext.getCmp('MaritalSt2').setValue(true);
                   if (r.MaritalSt=='3') Ext.getCmp('MaritalSt3').setValue(true);
                   if (r.MaritalSt=='4') Ext.getCmp('MaritalSt4').setValue(true);
                   if (r.Position=='1') Ext.getCmp('Position').setValue(true);
                   if (r.Position=='2') Ext.getCmp('Position2').setValue(true);
                   if (r.Position=='3') Ext.getCmp('Position3').setValue(true);
                   if (r.Position=='4') Ext.getCmp('Position4').setValue(true);
                   if (r.Position=='5') Ext.getCmp('Position5').setValue(true);
                   if (r.Position=='6') Ext.getCmp('Position6').setValue(true);
                   Ext.getCmp('AreaKerja').setValue(r.waprovince);
                   Ext.getCmp('DistrictId').setValue(r.WorkArea);
                   Ext.getCmp('Handphone').setValue(r.Handphone);
                   Ext.getCmp('PrivatePhone').setValue(r.StaffCellphone);
                   Ext.getCmp('OfficialPhone').setValue(r.StaffCellphone2);
                   Ext.getCmp('PrivateEmail').setValue(r.StaffEmail);
                   Ext.getCmp('OfficialEmail').setValue(r.StaffEmail2);
                   Ext.getCmp('nip').setValue(r.Nip);
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
                   if (r.UserActive=='Yes') Ext.getCmp('UserActive1').setValue(true);
                   if (r.UserActive=='No') Ext.getCmp('UserActive2').setValue(true);
                   if (r.Gender=='m') Ext.getCmp('Gender1').setValue(true);
                   if (r.Gender=='f') Ext.getCmp('Gender2').setValue(true);
                   if (r.RegionalCd!='') {
                       Ext.getCmp('Provinsi').setValue(r.Provinsi);
                       Ext.getCmp('Kabupaten').setValue(r.Kabupaten);
                       Ext.getCmp('Kecamatan').setValue(r.Kecamatan);
                       Ext.getCmp('Desa').setValue(r.RegionalCd);
                   }
               }
            });
               store_districtInStaff.load({
                  params: {
                      id: sm.get('id')
                  }});
              store_Province.load();
              StaffID = sm.get('id');
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
                         Ext.getCmp('PartnerId').setValue(r.PartnerId);
                         Ext.getCmp('PersonNm').setValue(r.PersonNm);
                         Ext.getCmp('BirthDttm').setValue(r.BirthDttm);
                         Ext.getCmp('Photo_old').setValue(r.Photo);
                         Ext.getCmp('Address').setValue(r.Address);
                         if (r.MaritalSt=='1') Ext.getCmp('MaritalSt1').setValue(true);
                         if (r.MaritalSt=='2') Ext.getCmp('MaritalSt2').setValue(true);
                         if (r.MaritalSt=='3') Ext.getCmp('MaritalSt3').setValue(true);
                         if (r.MaritalSt=='4') Ext.getCmp('MaritalSt4').setValue(true);
                         if (r.Position=='1') Ext.getCmp('Position').setValue(true);
                         if (r.Position=='2') Ext.getCmp('Position2').setValue(true);
                         if (r.Position=='3') Ext.getCmp('Position3').setValue(true);
                         if (r.Position=='4') Ext.getCmp('Position4').setValue(true);
                         if (r.Position=='5') Ext.getCmp('Position5').setValue(true);
                         if (r.Position=='6') Ext.getCmp('Position6').setValue(true);
                         Ext.getCmp('AreaKerja').setValue(r.WorkArea);
                         Ext.getCmp('Handphone').setValue(r.Handphone);
                         Ext.getCmp('PrivatePhone').setValue(r.StaffCellphone);
                         Ext.getCmp('OfficialPhone').setValue(r.StaffCellphone2);
                         Ext.getCmp('PrivateEmail').setValue(r.StaffEmail);
                         Ext.getCmp('OfficialEmail').setValue(r.StaffEmail2);
                         Ext.getCmp('nip').setValue(r.Nip);
                         Ext.getCmp('UserName').setValue(r.UserName);
                         Ext.getCmp('userid').setValue(r.UserId);
                         //Ext.getCmp('UserPassword').setValue(r.UserPassword);
                         Ext.getCmp('UserGroupGroupId').setValue(r.UserGroupGroupId);
                         Ext.getCmp('Photo_old').setValue(r.Photo);
						 // Test foto
						 var photo = 'no-user.jpg';
						 if(r.Photo != ''){
							photo = r.Photo; 
						 }
						 Ext.getCmp('iphoto').setSrc(m_photo+photo);
                         //Ext.getCmp('iphoto').setSrc(m_photo+r.Photo);
                         if (r.UserActive=='Yes') Ext.getCmp('UserActive1').setValue(true);
                         if (r.UserActive=='No') Ext.getCmp('UserActive2').setValue(true);
                         if (r.Gender=='m') Ext.getCmp('Gender1').setValue(true);
                         if (r.Gender=='f') Ext.getCmp('Gender2').setValue(true);
                         if (r.RegionalCd!='') {
                             Ext.getCmp('Provinsi').setValue(r.Provinsi);
                             Ext.getCmp('Kabupaten').setValue(r.Kabupaten);
                             Ext.getCmp('Kecamatan').setValue(r.Kecamatan);
                             Ext.getCmp('Desa').setValue(r.RegionalCd);
                         }
                     }
                  });
                     store_districtInStaff.load({
                        params: {
                            id: sm.get('id')
                        }});
                    store_Province.load();
                    StaffID = sm.get('id');               
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
        dataIndex: 'PersonNm'
    },
    {
        text: lang('Gender'), 
        width: '10%',
        dataIndex: 'Gender'
    },
    {
        text: lang('Cellphone'), 
        width: '20%',
        dataIndex: 'StaffCellphone'
    },
    {
        text: lang('Program Partner'), 
        width: '30%',
        dataIndex: 'PartnerName'
    }]
   });
});

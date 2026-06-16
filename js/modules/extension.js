Ext.onReady(function(){
   Ext.tip.QuickTipManager.init();
    var store = Ext.create('Ext.data.Store', {
        extend: 'Ext.data.Model',
        fields: ['id','PersonNm','district','InstitutionName','PositionName'],
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
        },
      listeners: {
         beforeload: function (store, operation) {
            store.proxy.extraParams.key=Ext.getCmp('key').getValue();
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
    function displayFormWindow(){
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
    }
	// general panel container
    var DataForm = Ext.create('Ext.form.Panel', {
        frame: false,
        height: 500,
        autoScroll: true,
        width: 1204,
        bodyPadding: 5,
        id:'dataForm',
        fileUpload: true,
        enctype:'multipart/form-data',
        fieldDefaults: {
            //labelAlign: 'left',
            //labelWidth: 120,
            //anchor: '100%'
			msgTarget: 'side',
            blankText: 'Tidak Boleh Kosong',
            labelAlign: 'left',
            labelWidth: 140,
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
					title:'Identitas',
					flex: 1,
					items: [{
						xtype: 'textfield',
						fieldLabel: lang('Staff Name'),
						id: 'PersonNm',
						name: 'PersonNm'
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
						fieldLabel : lang('Status Pernikahan'),
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
						name: 'Handphone'
					},{
						xtype: 'textfield',
						fieldLabel: lang('KTP'),
						id: 'KTP',
						name: 'KTP'
					},{
						xtype: 'textfield',
						fieldLabel: lang('Email'),
						id: 'email',
						name: 'email'
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
								//Ext.getCmp('Desa').setValue();
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
					},{
						xtype: 'radiogroup',
						fieldLabel: lang('Pendidikan'),
						columns:2,
						items: [{
							xtype: 'radiofield',
							boxLabel: lang('Belum pernah sekolah'),
							id: 'Education',
							name: 'Education',
							inputValue:'1'
						},{
							xtype: 'radiofield',
							boxLabel: lang('Tidak tamat SD'),
							id: 'Education2',
							name: 'Education',
							inputValue:'2'
						},{
							xtype: 'radiofield',
							boxLabel: lang('Tamat SD, tidak melanjutkan'),
							id: 'Education3',
							name: 'Education',
							inputValue:'3'
						},{
							xtype: 'radiofield',
							boxLabel: lang('Tamat SMP'),
							id: 'Education4',
							name: 'Education',
							inputValue:'4'
						},{
							xtype: 'radiofield',
							boxLabel: lang('Tamat SMA/SMK'),
							id: 'Education5',
							name: 'Education',
							inputValue:'5'
						},{
							xtype: 'radiofield',
							boxLabel: lang('Tamat perguruan tinggi'),
							id: 'Education6',
							name: 'Education',
							inputValue:'6'
						}]
					},{
						xtype: 'panel',
						items: [{
							layout: 'column',
							items: [{
								columnWidth: 0.52,
								items:[{
									xtype: 'fileuploadfield',
									fieldLabel: lang('Photo'),
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
								columnWidth: 0.48,
								items:[{
									xtype:'image',
									id:'iphoto',
									height:'120px'
								}]
							}]                
						}]
					},{
						xtype: 'textfield',
						id: 'Photo_old',
						name: 'Photo_old',
						inputType:'hidden'
					}]
				}]
			},{
				xtype: 'fieldset',
				title: lang('Extension Staff Tests'),
				hidden:true,
				flex: 1,
				items: [{
					layout: 'column',
					items: [{
						columnWidth: 0.5,
						layout: 'form',
						items:[{
							xtype:'label',
							text:'/'
						},{
							xtype:'label',
							text:'Writing'
						},{
							xtype:'label',
							text:'Ballot Box'
						}]
					},{
						columnWidth: 0.2,
						items:[{
							xtype:'label',
							text:'Awal'
						},{
							xtype: 'textfield',
							id: 'WritingAwal',
							padding: 5,
							name: 'WritingAwal'
						},{
							xtype: 'textfield',
							padding: 5,
							id: 'WritingAkhir',
							name: 'WritingAkhir'
						}]
					},{
						columnWidth: 0.2,
						items:[{
							xtype:'label',
							text:'Akhir'
						},{
							xtype: 'textfield',
							padding: 5,
							id: 'BallotAwal',
							name: 'BallotAwal'
						},{
							xtype: 'textfield',
							padding: 5,
							id: 'BallotAkhir',
							name: 'BallotAkhir'
						}]
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
						xtype: 'radiogroup',
						fieldLabel: lang('Institution'),
						columns: 1,
						items: [{
							boxLabel: lang('Dinas Perkebunan dan Kehutanan'),
							id: 'GovInstitute',
							name: 'GovInstitute',
							inputValue:'1'
						},{
							boxLabel: lang('Dinas Kesehatan'),
							id: 'GovInstitute2',
							name: 'GovInstitute',
							inputValue:'2'
						},{
							boxLabel: lang('Dinas Koperasi'),
							id: 'GovInstitute3',
							name: 'GovInstitute',
							inputValue:'3'
						},{
							boxLabel: lang('Badan Penyuluhan'),
							id: 'GovInstitute4',
							name: 'GovInstitute',
							inputValue:'4'
						},{
							boxLabel: lang('Balai Proteksi Tanaman'),
							id: 'GovInstitute5',
							name: 'GovInstitute',
							inputValue:'5'
						}]
					},{
						xtype: 'radiogroup',
						fieldLabel: lang('Position'),
						columns: 1,
						items: [{
							boxLabel: lang('Penyuluh'),
							id: 'StaffPosition',
							name: 'StaffPosition',
							inputValue:'1'
						},{
							boxLabel: lang('Petugas Teknis'),
							id: 'StaffPosition2',
							name: 'StaffPosition',
							inputValue:'2'
						},{
							boxLabel: lang('Petugas Administratif'),
							id: 'StaffPosition3',
							name: 'StaffPosition',
							inputValue:'3'
						},{
							boxLabel: lang('Kepala Balai/unit/Dinas'),
							id: 'StaffPosition4',
							name: 'StaffPosition',
							inputValue:'4'
						}]
					}]
				}]
			}]
		}],
        buttons: [{
            itemId: 'cetak',
            //icon: varjs.config.base_url+'images/icons/silk/printer.png',
            //cls:m_act_,
            text: lang('Cetak'),
            margin: '5px',
            scale: 'large',
            ui: 's-button',
            cls: 's-blue',
            scope: this,
            handler : function(){
                preview_cetak_surat(m_cetak+Ext.getCmp('id').getValue());
            }
		},{
            id:'saveButton',
            text: lang('Save'),
            margin: '5px',
            scale: 'large',
            ui: 's-button',
            cls: 's-blue',
            handler: function() {
                var form = this.up('form').getForm();
                var urle;
                if (Ext.getCmp('id').getValue()!='') urle = m_crud+'u'; else urle = m_crud;
                form.submit({
                    url: urle,
                    waitMsg: lang('Sending data...'),
                    success: function(fp, o) {
                       store.load();
                       Ext.MessageBox.alert('Success', 'Data saved.');
                    }
                });
                win.hide(this, function() {
                    store.load();
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
        title: lang('Data Extension'),
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
       width: '100%',
       id:'grid',
       //title: lang('Extension List'),
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
                   Ext.getCmp('PersonNm').setValue(r.PersonNm);
                   Ext.getCmp('BirthDttm').setValue(r.BirthDttm);
                   Ext.getCmp('Photo_old').setValue(r.Photo);
                   Ext.getCmp('Address').setValue(r.Address);
                   if (r.MaritalSt=='1') Ext.getCmp('MaritalSt1').setValue(true);
                   if (r.MaritalSt=='2') Ext.getCmp('MaritalSt2').setValue(true);
                   if (r.MaritalSt=='3') Ext.getCmp('MaritalSt3').setValue(true);
                   if (r.MaritalSt=='4') Ext.getCmp('MaritalSt4').setValue(true);
                   Ext.getCmp('Handphone').setValue(r.Handphone);
                   Ext.getCmp('KTP').setValue(r.KTP);
                   Ext.getCmp('email').setValue(r.Email);
                   Ext.getCmp('Photo_old').setValue(r.Photo);
                   Ext.getCmp('iphoto').setSrc(m_photo+r.Photo);
                   if (r.UserActive=='Yes') Ext.getCmp('UserActive1').setValue(true);
                   if (r.UserActive=='No') Ext.getCmp('UserActive2').setValue(true);
                   if (r.Gender=='m') Ext.getCmp('Gender1').setValue(true);
                   if (r.Gender=='f') Ext.getCmp('Gender2').setValue(true);
                   if (r.VillageID!='') {
                       Ext.getCmp('Provinsi').setValue(r.Provinsi);
                       Ext.getCmp('Kabupaten').setValue(r.Kabupaten);
                       Ext.getCmp('Kecamatan').setValue(r.Kecamatan);
                       console.log(r.VillageID);
                       Ext.getCmp('Desa').setValue(r.VillageID);
                   }
                  if (r.Education=='1') Ext.getCmp('Education').setValue(true);
                  if (r.Education=='2') Ext.getCmp('Education2').setValue(true);
                  if (r.Education=='3') Ext.getCmp('Education3').setValue(true);
                  if (r.Education=='4') Ext.getCmp('Education4').setValue(true);
                  if (r.Education=='5') Ext.getCmp('Education5').setValue(true);
                  if (r.Education=='6') Ext.getCmp('Education6').setValue(true);
                  //Ext.getCmp('InstitutionID').setValue(r.InstitutionID);
                  if (r.StaffPosition=='1') Ext.getCmp('StaffPosition').setValue(true);
                  if (r.StaffPosition=='2') Ext.getCmp('StaffPosition2').setValue(true);
                  if (r.StaffPosition=='3') Ext.getCmp('StaffPosition3').setValue(true);
                  if (r.StaffPosition=='4') Ext.getCmp('StaffPosition4').setValue(true);
                  if (r.GovInstitute=='1') Ext.getCmp('GovInstitute').setValue(true);
                  if (r.GovInstitute=='2') Ext.getCmp('GovInstitute2').setValue(true);
                  if (r.GovInstitute=='3') Ext.getCmp('GovInstitute3').setValue(true);
                  if (r.GovInstitute=='4') Ext.getCmp('GovInstitute4').setValue(true);
                  if (r.GovInstitute=='5') Ext.getCmp('GovInstitute5').setValue(true);
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
               handler : function(){
				   Ext.getCmp('iphoto').setSrc('');
				   displayFormWindow();
			   },
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
                         Ext.getCmp('PersonNm').setValue(r.PersonNm);
                         Ext.getCmp('BirthDttm').setValue(r.BirthDttm);
                         Ext.getCmp('Photo_old').setValue(r.Photo);
                         Ext.getCmp('Address').setValue(r.Address);
                         if (r.MaritalSt=='1') Ext.getCmp('MaritalSt1').setValue(true);
                         if (r.MaritalSt=='2') Ext.getCmp('MaritalSt2').setValue(true);
                         if (r.MaritalSt=='3') Ext.getCmp('MaritalSt3').setValue(true);
                         if (r.MaritalSt=='4') Ext.getCmp('MaritalSt4').setValue(true);
                         Ext.getCmp('Handphone').setValue(r.Handphone);
                         Ext.getCmp('KTP').setValue(r.KTP);
                         Ext.getCmp('email').setValue(r.Email);
                         Ext.getCmp('Photo_old').setValue(r.Photo);
                         Ext.getCmp('iphoto').setSrc(m_photo+r.Photo);
                         if (r.UserActive=='Yes') Ext.getCmp('UserActive1').setValue(true);
                         if (r.UserActive=='No') Ext.getCmp('UserActive2').setValue(true);
                         if (r.Gender=='m') Ext.getCmp('Gender1').setValue(true);
                         if (r.Gender=='f') Ext.getCmp('Gender2').setValue(true);
                         if (r.VillageID!='') {
                             Ext.getCmp('Provinsi').setValue(r.Provinsi);
                             Ext.getCmp('Kabupaten').setValue(r.Kabupaten);
                             Ext.getCmp('Kecamatan').setValue(r.Kecamatan);
                             console.log(r.VillageID);
                             Ext.getCmp('Desa').setValue(r.VillageID);
                         }
                        if (r.Education=='1') Ext.getCmp('Education').setValue(true);
                        if (r.Education=='2') Ext.getCmp('Education2').setValue(true);
                        if (r.Education=='3') Ext.getCmp('Education3').setValue(true);
                        if (r.Education=='4') Ext.getCmp('Education4').setValue(true);
                        if (r.Education=='5') Ext.getCmp('Education5').setValue(true);
                        if (r.Education=='6') Ext.getCmp('Education6').setValue(true);
                        //Ext.getCmp('InstitutionID').setValue(r.InstitutionID);
                        if (r.StaffPosition=='1') Ext.getCmp('StaffPosition').setValue(true);
                        if (r.StaffPosition=='2') Ext.getCmp('StaffPosition2').setValue(true);
                        if (r.StaffPosition=='3') Ext.getCmp('StaffPosition3').setValue(true);
                        if (r.StaffPosition=='4') Ext.getCmp('StaffPosition4').setValue(true);
                        if (r.GovInstitute=='1') Ext.getCmp('GovInstitute').setValue(true);
                        if (r.GovInstitute=='2') Ext.getCmp('GovInstitute2').setValue(true);
                        if (r.GovInstitute=='3') Ext.getCmp('GovInstitute3').setValue(true);
                        if (r.GovInstitute=='4') Ext.getCmp('GovInstitute4').setValue(true);
                        if (r.GovInstitute=='5') Ext.getCmp('GovInstitute5').setValue(true);
                     }
                  });               
               },
               cls : m_act_update
            },{
               itemId: 'cetak',
               icon: varjs.config.base_url+'images/icons/silk/printer.png',
               //cls:m_act_,
               text: lang('Cetak'),
               scope: this,
               handler : function(){
                  preview_cetak_surat(m_cetak);
               }
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

        fields: ['id','PersonNm','district','InstitutionName','PositionName'],

    columns: [
    {
        text: lang('ID'),
        dataIndex: 'id',
        width:'5%'
    },
    {
        text: lang('Name'), 
        width: '25%',
        dataIndex: 'PersonNm'
    },
    {
        text: lang('District'), 
        width: '20%',
        dataIndex: 'district'
    },
    {
        text: lang('Institution'), 
        width: '25%',
        dataIndex: 'InstitutionName'
    },
    {
        text: lang('Position'), 
        width: '25%',
        dataIndex: 'PositionName'
    }]
   });
});

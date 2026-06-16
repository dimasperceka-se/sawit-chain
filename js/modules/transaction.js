Ext.onReady(function(){
    //add batch
    var myMask = new Ext.LoadMask(Ext.getBody(), {msg:lang("Please wait...")});
    var yourApp = {
        'showLoading' : function() {
            Ext.MessageBox.show({
               msg: 'Loading...',
               progressText: 'Loading...',
               width:300,
               activeOnTop: true,
               closable: false,
               id: 'showloading'
            });
        },
        'hideLoading' : function() {
            Ext.MessageBox.hide();
        }
    };
    function add_batch() {
        winFarmer.hide()
        win.hide()
        var ids = Ext.getCmp('SupplyBatchID').getValue()
        displayFormWindow()
        var today = new Date();
        var mm = today.getMonth()+1;
        if (mm<10) mm = '0'+mm;
        Ext.getCmp('SupplyBatchDate').setValue(m_now);
        Ext.Ajax.request({
            url: m_crud+'_number',
            method: 'GET',
            success: function(fp, o){
                 var r = Ext.decode(fp.responseText);console.log('r1:'); console.log(r);
                 //Ext.getCmp('SupplyBatchNumber').setValue(r.number);
                 Ext.getCmp('SupplychainID').setValue(r.id);
                 Ext.getCmp('unitto').setValue(r.id+' - '+r.nama)
                 //Ext.getCmp('add_batch').hide()
            }
        });
        Ext.getCmp('unitfrom').show()
        Ext.getCmp('SupplyDestOrgID').hide()
        Ext.getCmp('unitto').show()
        Ext.getCmp('nama').hide()
        Ext.getCmp('saveButton').setText(lang('Create Batch'));
        Ext.getCmp('saveButton').show()
        Ext.getCmp('closeBatchButton').hide()
        Ext.getCmp('SupplyDestStatus').setValue('Open');
        Ext.getCmp('SupplyBatchID_old').setValue(ids)
        //**//
        Ext.getCmp('AddBatchBUFromCoop').setValue(1);
        //**//
   }

    function jam24(amPmString) { 
        /*var d = new Date("1/1/2013 " + amPmString); 
        if(d.getHours() < 10){
            var jam = "0"+d.getHours();
        }else{
            var jam = d.getHours();
        }
        if(d.getMinutes() < 10){
            var mnt = "0"+d.getMinutes();
        }else{
            var mnt = d.getMinutes();
        }
        return  jam + ':' + mnt + ':00';*/
        return amPmString+':00';
    }

    var contextMenu = Ext.create('Ext.menu.Menu', {
        items: [{
            icon: varjs.config.base_url + 'images/icons/new/update.png',
            text: lang('Update'),
            handler: function() {
                var sm = Ext.getCmp('grid').getSelectionModel().getSelection()[0];
                if (sm.get('no_edit')!='2') {
                   displayFormWindow();
                   tset(sm.get('SupplyBatchID'))
                }
            }
        }, {
            icon: varjs.config.base_url + 'images/icons/new/delete.png',
            text: lang('Delete'),
            handler: function() {
                var smb = Ext.getCmp('grid').getSelectionModel().getSelection()[0];
                if (smb.raw.SupplyDestStatus=='Delivered' || smb.raw.SupplyDestStatus=='Sent' || smb.raw.SupplyDestStatus=='Closed' ||
                     smb.raw.SupplyDestStatus=='Other')
                     Ext.MessageBox.alert('Warning','Data tidak bisa dihapus');
                else {
                    Ext.MessageBox.confirm('Message', lang('Apakah anda mau menghapus data batch ini ?') , function(btn){
                        if(btn == 'yes'){
                            Ext.Ajax.request({
                                waitMsg: lang('Please Wait'),
                                url: m_crud+'_batch',
                                method : 'DELETE',
                                params: {id:  smb.raw.SupplyBatchID},
                                success: function(response, opts){
                                    var obj = Ext.decode(response.responseText);
                                    switch(obj.success){
                                        case true:
                                        store.load();
                                        Ext.MessageBox.alert('Warning',obj.message);
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
            }
        }]
    });

    var contextMenuBatch = Ext.create('Ext.menu.Menu', {
        items: [{
            icon: varjs.config.base_url + 'images/icons/silk/application_view_detail.png',
            text: lang('Detail'),
            id: 'toolbar_detail_update_detail',
            handler: function() {
                var sm = Ext.getCmp('grid_detail').getSelectionModel().getSelection()[0];
                displayFormFarmer(sm.get('SupplyType'),'','',function(){
                    fset(sm);
                });
            }
        },  {
            icon: varjs.config.base_url + 'images/icons/new/update.png',
            text: lang('Update'),
            id: 'toolbar_detail_update',
            handler: function() {
                var sm = Ext.getCmp('grid_detail').getSelectionModel().getSelection()[0];
                displayFormFarmer(sm.get('SupplyType'),'','',function(){
                    fset(sm);
                });
            }
        }, {
            icon: varjs.config.base_url + 'images/icons/silk/printer.png',
            text: lang('Print'),
            id: 'cetakButtonTrans',
            handler: function() {
                var sm = Ext.getCmp('grid_detail').getSelectionModel().getSelection()[0];
                //alert(sm.get('SupplyTransID'));
                preview_cetak_surat(m_cetak_kuitansi+sm.get('SupplyTransID'));
            }
        }, {
            icon: varjs.config.base_url + 'images/icons/new/delete.png',
            text: lang('Delete'),
            id: 'toolbar_detail_remove',
            handler: function() {
                hapus_trans();
            }
        }]
    });

    var contextMenuTransaction = Ext.create('Ext.menu.Menu', {
        items: [{
            icon: varjs.config.base_url + 'images/icons/new/update.png',
            text: lang('Update'),
            id:'toolbar_farmer2',
            handler: function() {
                pRowEditing.cancelEdit();
                var sm = Ext.getCmp('grid_farmer').getSelectionModel().getSelection();
                pRowEditing.startEdit(sm[0].index, 0);
            }
        }, {
            icon: varjs.config.base_url + 'images/icons/new/delete.png',
            text: lang('Delete'),
            id:'toolbar_farmer3',
            handler: function() {
                var smb = Ext.getCmp('grid_farmer').getSelectionModel().getSelection()[0];
                pRowEditing.cancelEdit();
                Ext.MessageBox.confirm('Message', lang('Apakah anda mau menghapus transaksi ini ?') , function(btn){
                    if(btn == 'yes'){
                       Ext.Ajax.request({
                          waitMsg: lang('Please Wait'),
                          url: m_crud+'_farmer',
                          method : 'DELETE',
                          params: {
                             id:  smb.raw.DetailID
                          },
                          success: function(response, opts){
                             var obj = Ext.decode(response.responseText);
                             switch(obj.success){
                                case true:
                                   store_farmer.load({
                                      params: {
                                         id: Ext.getCmp('fSupplyTransID').getValue(),
                                         frombatchid: Ext.getCmp('frombatchid').getValue()
                                   }});
                                break;
                                default:
                                    Ext.MessageBox.alert('Warning',obj.message);
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
    });
    
    var contextMenuFiles = Ext.create('Ext.menu.Menu', {
        items: [{
            icon: varjs.config.base_url + 'images/icons/silk/application_view_detail.png',
            text: lang('View'),
            hidden: true,
            handler: function() {
                var smcf = Ext.getCmp('gridUploadFiles').getSelectionModel().getSelection()[0];
                preview_cetak_surat(m_preview_file+'files/supplychain/'+smcf.raw.path);
            }
        },  {
            icon: varjs.config.base_url + 'images/icons/silk/disk_download.png',
            text: lang('Download'),
            handler: function() {
                var smcf = Ext.getCmp('gridUploadFiles').getSelectionModel().getSelection()[0];
                window.open(m_preview_file+'files/supplychain/'+smcf.raw.path);
            }
        }, {
            icon: varjs.config.base_url + 'images/icons/new/delete.png',
            text: lang('Delete'),
            handler: function() {
                hapus_file();
            }
        }]
    });

   //packing list
    function ebsearch(jenis) {
        if(jenis=='check'){
            Ext.Ajax.request({
                url: m_crud+'_check_batch',
                method: 'GET',
                params: {
                    id: Ext.getCmp('BatchID').getValue(),
                    parentID : Ext.getCmp('SupplyBatchNumber').getValue(),
                    orgid: Ext.getCmp('SupplychainID').getValue()
                },
                success: function(fp, o){

                    var r = Ext.decode(fp.responseText);
                    if(r.statusnya=='sama' || s_partner=='9'){
                        Ext.Ajax.request({
                            url: m_crud+'_batch',
                            method: 'GET',
                            params: {
                                id: Ext.getCmp('BatchID').getValue(),
                                orgid: Ext.getCmp('SupplychainID').getValue()
                            },
                            success: function(fp, o){
                                if (fp.responseText!='') {
                                    var r = Ext.decode(fp.responseText);
                                    Ext.getCmp('BatchFromName').setValue(r.name);
                                    Ext.getCmp('frombatchid').setValue(r.SupplyBatchID);
                                    store_farmer.load({
                                        params: {
                                            id: '',frombatchid: r.SupplyBatchID, tipe: 'batch'
                                        }
                                    });
                                } else Ext.MessageBox.alert('Warning',lang('Data batch tidak ditemukan'));
                            }
                        })
                    }else{
                        if(Ext.getCmp('BatchID').getValue()!=""&&Ext.getCmp('SupplyBatchNumber').getValue()!=""){
                            Ext.MessageBox.alert('Warning',lang('Data batch tidak boleh digabung.'));
                            Ext.getCmp('BatchID').setValue('');
                            var proses = 0;
                            return;
                        }
                    }
                }
            })
        }else{
            Ext.Ajax.request({
                url: m_crud+'_batch',
                method: 'GET',
                params: {
                    id: Ext.getCmp('BatchID').getValue(),
                    orgid: Ext.getCmp('SupplychainID').getValue()
                },
                success: function(fp, o){
                    if (fp.responseText!='') {
                        var r = Ext.decode(fp.responseText);
                        Ext.getCmp('BatchFromName').setValue(r.name);
                        Ext.getCmp('frombatchid').setValue(r.SupplyBatchID);
                        store_farmer.load({
                            params: {
                                id: '',frombatchid: r.SupplyBatchID
                            }
                        });
                    } else Ext.MessageBox.alert('Warning',lang('Data batch tidak ditemukan'));
                }
            })
        }
    }
    Ext.define('packing_list.Model', {
        extend: 'Ext.data.Model',
        fields: [{name:'id'},{name:'jumlah'}],
    });
    var store_packing_list = Ext.create('Ext.data.Store', {
        model: 'packing_list.Model',
        autoLoad: false,
        pageSize: 50,
        proxy: {
            type: 'ajax',
            url: m_crud+'s_packing',
            reader: {
                type: 'json',
                root: 'data',
                totalProperty: 'total'
            }
        },
        listeners : {
            load: function(store, record){
                var total = 0;
                store.each(function(record){
                  total += parseFloat(record.get('jumlah'));
                });
                Ext.getCmp('Jumlah').setValue(total)
            }
        }
    });
    var store_doublePack = Ext.create('Ext.data.Store', {
        fields: ['label'],
        data: [
            {"label": lang('Berat')},
            {"label": lang('Karung')},
            {"label": lang('Transaksi')}
        ]
    });
   var packingRowEditing = Ext.create('Ext.grid.plugin.RowEditing', {
      id: 'packingRowEditing',
      clicksToMoveEditor: 0,
      autoCancel: false,
      errorSummary : false,
      clicksToEdit: 2
   });
    var DataFormPackingList = Ext.create('Ext.form.Panel', {
        frame: false,
        autoScroll: true,
        bodyPadding: 5,
        id:'dataFormPakingList',
        fieldDefaults: {
            labelAlign: 'left',
            labelWidth: 120,
            anchor: '100%'
        },
        items: [{
            xtype: 'gridpanel',
            id:'grid_packing_list',
            store: store_packing_list,
            width: '100%',
            loadMask: true,
            selType: 'rowmodel',
            dockedItems: [{
               xtype: 'toolbar',
               id:'toolbar_packing',
               items: [{
                  icon: varjs.config.base_url + 'images/icons/new/add.png', cls:'Sfr_BtnGridGreen', overCls:'Sfr_BtnGridGreen-Hover',
                  text: lang('Add'),
                  scope: this,
                  handler :function(){
                       packingRowEditing.cancelEdit();
                       var r = Ext.create('packing_list.Model', {
                           id:'', jumlah:''
                       });
                       store_packing_list.insert(0, r);
                       packingRowEditing.startEdit(0, 0);
                  }
               },{
                  icon: varjs.config.base_url+'images/icons/silk/pencil.png',
                  cls:m_act_save,
                  text: lang('Edit'),
                  scope: this,
                  handler : function() {
                    packingRowEditing.cancelEdit();
                    var sm = Ext.getCmp('grid_packing_list').getSelectionModel().getSelection();
                    packingRowEditing.startEdit(sm[0].index, 0);
                  }
               },{
                  icon: varjs.config.base_url+'images/icons/silk/delete.png',
                  text: lang('Hapus'),
                  scope: this,
                  handler : function(){
                    var smb = Ext.getCmp('grid_packing_list').getSelectionModel().getSelection()[0];
                    packingRowEditing.cancelEdit();
                    Ext.MessageBox.confirm('Message', lang('Apakah anda mau menghapus data ini ?') , function(btn){
                        if(btn == 'yes'){
                           Ext.Ajax.request({
                              waitMsg: lang('Please Wait'),
                              url: m_crud+'_packing',
                              method : 'DELETE',
                              params: {
                                 id:  smb.raw.id
                              },
                              success: function(response, opts){
                                 var obj = Ext.decode(response.responseText);
                                 switch(obj.success){
                                    case true:
                                       store_packing_list.load({
                                          params: {
                                             id: Ext.getCmp('SupplyBatchID').getValue()
                                       }});
                                        Ext.MessageBox.alert('Warning',obj.message);
                                    break;
                                    default:
                                        Ext.MessageBox.alert('Warning',obj.message);
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
            }],
            columns: [{
              text: lang('No'),
              xtype: 'rownumberer',
              width:'10%'
            },{
              text: lang('Jumlah'),
              dataIndex: 'jumlah',
              width:'90%',
              editor: {
                 xtype:'textfield',
                 allowBlank:false
              },
              listeners : {
                  change: function(store, record){
                      if (parseFloat(Ext.getCmp('Jumlah').getValue())==parseFloat(Ext.getCmp('DestWeight').getValue()))
                        Ext.getCmp('cetak_packing_list').show()
                        if(Ext.getCmp('IsCetakSuratJalan').getValue()=='1'){
                            Ext.getCmp('cetakSuratJalan').show()
                        }

                  }
              }
            }],
            plugins: [packingRowEditing],
            listeners: {
                'canceledit':function(editor,e,eOpts){
                     store_packing_list.load({
                        params: {
                           id: Ext.getCmp('SupplyBatchID').getValue()
                     }});
                },
                'edit': function(editor, e) {
                  if(e.record.data.id==''){
                     Ext.Ajax.request({
                         waitMsg: lang('Please wait...'),
                         url: m_crud+'_packing',
                         method : 'POST',
                         params: {
                            BatchID      : Ext.getCmp('SupplyBatchID').getValue(),
                            jumlah   : e.record.data.jumlah,
                            destid: Ext.getCmp('SupplyDestOrgID').getValue()
                         },
                         success: function(response, opts){
                             var obj = Ext.decode(response.responseText);
                             switch(obj.success){
                                 case true:
                                    Ext.MessageBox.alert('Success',obj.message);
                                    store_packing_list.load({
                                       params: {
                                          id: Ext.getCmp('SupplyBatchID').getValue()
                                    }});
                                    break;
                                 default:
                                    Ext.MessageBox.alert('Warning',obj.message);
                                 break;
                             }
                         },
                         failure: function(response, opts){
                             var obj = Ext.decode(response.responseText);
                             Ext.MessageBox.alert('error',lang('Could not connect to the database. Retry later'));
                         }
                     });
                  } else {
                     Ext.MessageBox.confirm('Message', lang('Update data ini ?') , function(btn){
                        if(btn == 'yes') {
                           Ext.Ajax.request({
                              waitMsg: lang('Please wait...'),
                              url: m_crud+'_packing',
                              method : 'PUT',
                              params: {
                                  BatchID      : Ext.getCmp('SupplyBatchID').getValue(),
                                  id      : e.record.data.id,
                                  jumlah   : e.record.data.jumlah
                              },
                              success: function(response, opts){
                                  var obj = Ext.decode(response.responseText);
                                  switch(obj.success){
                                      case true:
                                         Ext.MessageBox.alert('Success',obj.message);
                                         store_packing_list.load({
                                             params: {
                                                id: Ext.getCmp('SupplyBatchID').getValue()
                                         }});
                                         break;
                                      default:
                                         Ext.MessageBox.alert('Warning',obj.message);
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
               }
            }
         },{
         layout: 'column',
         items: [{
          columnWidth: 0.6,
          layout: 'form',
          padding:5,
          items:[{}]
         },{
          columnWidth: 0.4,
          layout: 'form',
          padding:5,
          items:[{
            xtype: 'textfield',
            fieldLabel: lang('Jumlah'),
            id: 'Jumlah',
            name: 'Jumlah',
            readOnly:true,
             listeners: {
                 change: function(cb, nv, ov) {
//                     if (parseFloat(Ext.getCmp('DestWeight').getValue())>0 && parseFloat(Ext.getCmp('DestWeight').getValue())!=parseFloat(this.value))
  //                   Ext.getCmp('saveButton').hide(); else Ext.getCmp('saveButton').show();
                 }
             }
        }]}]}],
        buttons: [{
            text: lang('Cetak'),
               margin: '5px',
               scale: 'large',
            ui: 's-button',
            cls: 's-blue',
            id:'cetak_packing_list',
            handler: function() {
               preview_cetak_surat(m_cetak_packing_list+Ext.getCmp('SupplyBatchID').getValue());
            }
        },{
            text: lang('Close'),
               margin: '5px',
               scale: 'large',
            ui: 's-button',
            cls: 's-grey',
            disabled: false,
            handler: function() {

                winPackingList.hide();
            }
        }]
    });
    var winPackingList = Ext.create('widget.window', {
        title: 'Packing List',
        id:'winplay',
        closable: false,
        modal:true,
        closeAction: 'show',
        width: '80%',
        height: '80%',
        layout: {
            type: 'fit'
        },
        items: [DataFormPackingList]
    });
    function displayFormPackingList(){
      set_hide(['cetak_packing_list','toolbar_packing']);
      if (parseFloat(Ext.getCmp('Jumlah').getValue())==parseFloat(Ext.getCmp('DestWeight').getValue())) {
         set_show(['cetak_packing_list']);
      }
        if(Ext.getCmp('SupplyDestStatus').getValue()=='Close Batch'){
            set_show(['toolbar_packing']);
        }else{
            set_show(['cetak_packing_list']);
            if(Ext.getCmp('IsCetakSuratJalan').getValue()=='1'){
                set_show(['cetakSuratJalan']);
            }
        }
        if(!winPackingList.isVisible()){
            DataFormPackingList.getForm().reset();
            winPackingList.show();
        } else {
            winPackingList.hide(this, function() {});
            winPackingList.toFront();
        }
    }
    
    var store_upload_files = Ext.create('Ext.data.Store', {
        extend: 'Ext.data.Model',
        fields: ['SupplyBatchFileID', 'tipe', 'name', 'path', 'date'],
        autoLoad: false,
        proxy: {
            type: 'ajax',
            url: m_crud+'_files',
            reader: {
                type: 'json',
                root: 'data'
            }
        }
    });
    
    function displayFormUploadFiles() {
        if (!winUploadFiles.isVisible()) {
            //DataFormClonalGardenPolygonCoop.getForm().reset();
            winUploadFiles.show();
        } else {
            winUploadFiles.hide(this, function() {});
            winUploadFiles.toFront();
        }
    }
    
    function displayUploadFilesFormWindow() {
        if (!winUploadFilesForm.isVisible()) {
            UploadFilesForm.getForm().reset();
            winUploadFilesForm.show();
        } else {
            winUploadFilesForm.hide(this, function() {
            });
            winUploadFilesForm.toFront();
        }
    }
    
    var UploadFilesForm = Ext.widget('form', {
        frame: false,
        height: 180,
        id: 'UploadFilesForm',
        autoScroll: true,
        width: 600,
        bodyPadding: 15,
        fileUpload: true,
        enctype: 'multipart/form-data',
        fieldDefaults: {
            labelAlign: 'left',
            labelWidth: 150
        },
        items: [{
                xtype: 'textfield',
                id: 'UploadFilesSupplyBatchID',
                name: 'UploadFilesSupplyBatchID',
                inputType: 'hidden'
            }, {
                xtype: 'fileuploadfield',
                fieldLabel: lang('File'),
                labelWidth: 100,
                id: 'File',
                name: 'File',
                width: '100%',
                buttonText: 'Browse',
                listeners: {
                    'change': function(fb, v) {
                        var form = Ext.getCmp('UploadFilesForm').getForm();
                        form.submit({
                            url: m_crud + '_files',
                            waitMsg: lang('Sending Photo...'),
                            success: function(fp, o) {
                                Ext.MessageBox.alert(o.result.status, o.result.message);
                                store_upload_files.load({
                                    params: {
                                        batchid: Ext.getCmp('SupplyBatchID').getValue()
                                    }
                                });
                                //Ext.getCmp('iphoto').setSrc(m_photo + o.result.file);
                                //if (Ext.getCmp('Photo_old').setValue(Ext.getCmp('Photo_name').getValue())) {
                                    //Ext.getCmp('Photo_name').setValue(o.result.file);
                            }, 
                            failure: function(fp, o){
                                Ext.MessageBox.alert(o.result.status, o.result.message);
                                store_upload_files.load({
                                    params: {
                                        batchid: Ext.getCmp('SupplyBatchID').getValue()
                                    }
                                });
                            }
                        });
                    }
                }
            }],
        buttons: [{
            text: 'Save',
            margin: '5px',
            scale: 'large',
            hidden: true,
            ui: 's-button',
            cls: 's-blue',
            handler: function() {
                /*var form = this.up('form').getForm();
                var methode;
                if (Ext.getCmp('InfrastructureID').getValue() == '')
                    methode = 'POST';
                else
                    methode = 'PUT';

                form.submit({
                    url: m_crudInfrastructure,
                    method: methode,
                    waitMsg: 'Sending data...',
                    success: function(fp, o) {
                        Ext.MessageBox.alert('Success', 'Data saved.');
                                                    winInfrastructureForm.hide(this, function() {
                                                            var vl = Ext.getCmp('VillageList').getSelectionModel().getSelection()[0];
                                                            storeInfrastructure.load({
                                                                    params: {
                                                                            id: vl.get('id')
                                                                    }
                                                            });
                                                    });
                    },
                                            failure : function(fp, o) {
                        Ext.MessageBox.alert('Warning', 'Please check your input data.');
                                            }
                });*/
            }
        }, {
            text: 'Close',
            margin: '5px',
            scale: 'large',
            ui: 's-button',
            cls: 's-grey',
            disabled: false,
            handler: function() {
                winUploadFilesForm.hide();
            }
        }]
    });

    var winUploadFilesForm = Ext.create('widget.window', {
        title: 'Upload File Form',
        id: 'winUploadFilesForm',
        closable: true,
        modal: true,
        closeAction: 'hide',
        width: 600,
        frame: false,
        minWidth: 370,
        height: 180,
        layout: {
            type: 'fit'
        },
        items: [UploadFilesForm]
    });
    
    var DataFormUploadFiles = Ext.create('Ext.form.Panel', {
        autoScroll: true,
        bodyPadding: 5,
        id: 'DataFormUploadFiles',
        fieldDefaults: {
            labelAlign: 'left',
            labelWidth: 120,
            anchor: '95%'
        },
        items: [{
            xtype: 'gridpanel',
            id: 'gridUploadFiles',
            style: 'border:1px solid #CCC;',
            store: store_upload_files,
            width: '100%',
            loadMask: true,
            dockedItems: [{
                xtype: 'toolbar',
                items: [{
                    icon: varjs.config.base_url + 'images/icons/new/add.png', cls:'Sfr_BtnGridGreen', overCls:'Sfr_BtnGridGreen-Hover',
                    cls: m_act_save,
                    hidden: !m_act_add,
                    text: lang('Add'),
                    scope: this,
                    handler: function() {
                        displayUploadFilesFormWindow();
                        //var vl = Ext.getCmp('VillageList').getSelectionModel().getSelection()[0];
                        Ext.getCmp('UploadFilesSupplyBatchID').setValue(Ext.getCmp('SupplyBatchID').getValue());
                    }
                }]
            }],
            columns: [{
                text: lang('ID'),
                dataIndex: 'SupplyBatchFileID',
                align: 'center',
                hidden: true
            }, {
                text: lang('No'),
                xtype: 'rownumberer',
                width: '10%'
            }, {
                text: lang('Date'),
                dataIndex: 'date',
                width: '20%',
            }, {
                text: lang('Type'),
                dataIndex: 'tipe',
                width: '30%',
            }, {
                text: lang('File Name'),
                dataIndex: 'name',
                width: '40%',
            }],
            listeners : {
                itemclick: function(view, record, item, index, e){
                    contextMenuFiles.showAt(e.getXY());
                }
             }
        }],
        buttons: [{
            text: lang('Close'),
            margin: '5px',
            scale: 'large',
            ui: 's-button',
            cls: 's-grey',
            disabled: false,
            handler: function() {
                winUploadFiles.hide();
            }
        }]
    });
    
    var winUploadFiles = Ext.create('widget.window', {
        title: lang('Files'),
        id: 'winUploadFiles',
        closable: true,
        modal: true,
        closeAction: 'show',
        width: '50%',
        height: 400,
        layout: {
            type: 'fit'
        },
        items: [DataFormUploadFiles]
    });

   //end packing list
   // close batch
    function cbsave(){
        var form = Ext.getCmp('dataForm').getForm();
        form.submit({
            url: m_crud+'_close_batch',
            method: 'PUT',
            params: {
                IsGeneratePo: s_isgeneratepo,
                SupplychainID : Ext.getCmp('SupplychainID').getValue()
            },
            waitMsg: lang('Sending data...'),
            success: function(fp, o) {
                set_setting('Close Batch',function(){
                    Ext.getCmp('DestWeight').setValue(Ext.getCmp('VolumeNetto').getValue());
                    var id = Ext.getCmp('SupplyBatchID').getValue();
                    tset(id,function(){
                        store.load();
                    });
                });
           }
       });
    }
    // end close batch
    var change_cbatch = true;
    function fset(sm) {
      if (Ext.getCmp('SupplyDestStatus').getValue()=='Open') set_show(['toolbar_farmer','toolbar_farmer2','toolbar_farmer3']);
      Ext.Ajax.request({
         url: m_crud+'_data_trans',
         method: 'GET',
         params: {id: sm.get('SupplyTransID')},
         success: function(fp, o){
              var r = Ext.decode(fp.responseText);
               store_farmer.load({
                  params: {
                     id: sm.get('SupplyTransID'),frombatchid:r.frombatchid
               }});
              Ext.getCmp('fSupplyTransID').setValue(sm.get('SupplyTransID'));
              updateQuality(function(){
                Ext.getCmp('fSupplyBatchID').setValue(r.SupplyBatchID);
                Ext.getCmp('fDateTransaction').setValue(r.DateTransaction);
                Ext.getCmp('FakturNumber').setValue(r.FakturNumber);
                Ext.getCmp('FAQBeratBersihSetara').setValue(r.FAQBeratBersihSetara);
                Ext.getCmp('FFBeratBersihSetara').setValue(r.FFBeratBersihSetara);
                Ext.getCmp('FAQVerifikasi').setValue(r.FAQVerifikasi);
                Ext.getCmp('FFVerifikasi').setValue(r.FFVerifikasi);
                Ext.getCmp('SupplyType').setValue(r.SupplyType);
                Ext.getCmp('FarmerID').setValue(r.FarmerID);
                Ext.getCmp('fcol').setValue(r.FarmerID);
                Ext.getCmp('FarmerName').setValue(r.FarmerName);
                Ext.getCmp('GroupName').setValue(r.GroupName);
                Ext.getCmp('District').setValue(r.District);
                Ext.getCmp('BatasFarmerID').setValue(r.batas_atas);
                Ext.getCmp('BatasJual').setValue(r.jual);

                Ext.getCmp('fncol').setValue(r.FarmerName);
                Ext.getCmp('NonFarmerID').setValue(r.NonFarmerID);
                Ext.getCmp('NonIdentity').setValue(r.FarmerIdentity);
                Ext.getCmp('NonBirthdate').setValue(r.FarmerBirthdate);
                Ext.getCmp('NonVillageID').setValue(r.FarmerVillageID);

                Ext.getCmp('FFContractPrice').setValue(r.FFContractPrice);
                //Ext.getCmp('FFRewardBruto').setValue(r.FFRewardBruto);
                //Ext.getCmp('FFRewardBonus').setValue(r.FFRewardBonus);
                //Ext.getCmp('FFReward').setValue(r.FFReward);
                Ext.getCmp('FFTotalPayment').setValue(r.FFTotalPayment);

                Ext.getCmp('FAQContractPrice').setValue(r.FAQContractPrice);
                //Ext.getCmp('FAQRewardBruto').setValue(r.FAQRewardBruto);
                //Ext.getCmp('FAQRewardBonus').setValue(r.FAQRewardBonus);
                //Ext.getCmp('FAQReward').setValue(r.FAQReward);
                Ext.getCmp('FAQTotalPayment').setValue(r.FAQTotalPayment);


                Ext.getCmp('FFVolumeNetto2').setValue(r.FFVolumeNetto);
                Ext.getCmp('FAQVolumeNetto2').setValue(r.FAQVolumeNetto);
                change_cbatch = false
                Ext.getCmp('BatchID').setValue(r.SupplyBatchNumber);
                change_cbatch = true;
                Ext.getCmp('BatchFromName').setValue(r.Name);
                Ext.getCmp('frombatchid').setValue(r.frombatchid);
                Ext.getCmp('DpTotalPayment').setValue(r.Dp);
                Ext.getCmp('FAQNetPrice').setValue(r.FAQNetPrice);
              Ext.getCmp('FFNetPrice').setValue(r.FFNetPrice);
              });

         }
      });
    }
    //hapus trans
    function hapus_trans() {
        var smcc = Ext.getCmp('grid_detail').getSelectionModel().getSelection()[0];
        Ext.MessageBox.confirm('Message', lang('Apakah anda mau menghapus transaksi ini ?') , function(btn){
            if(btn == 'yes'){
               Ext.Ajax.request({
                  waitMsg: lang('Please Wait'),
                  url: m_crud+'_detail',
                  method : 'DELETE',
                  params: {
                     id:  smcc.raw.SupplyTransID
                  },
                  success: function(response, opts){
                     var obj = Ext.decode(response.responseText);
                     switch(obj.success){
                        case true:
                           store_detail.load({
                              params: {
                                 id: Ext.getCmp('SupplyBatchID').getValue()
                           }});
                           Ext.MessageBox.alert('Warning',obj.message);
                        break;
                        default:
                            Ext.MessageBox.alert('Warning',obj.message);
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
    
    function hapus_file() {
        var smcf = Ext.getCmp('gridUploadFiles').getSelectionModel().getSelection()[0];
        Ext.MessageBox.confirm('Message', lang('Apakah anda mau menghapus transaksi ini ?') , function(btn){
            if(btn == 'yes'){
               Ext.Ajax.request({
                  waitMsg: lang('Please Wait'),
                  url: m_crud+'_files',
                  method : 'DELETE',
                  params: {
                     id:  smcf.raw.SupplyBatchFileID
                  },
                  success: function(response, opts){
                     var obj = Ext.decode(response.responseText);
                     switch(obj.success){
                        case true:
                           store_upload_files.load({
                                params: {
                                    batchid: Ext.getCmp('SupplyBatchID').getValue()
                                }
                            });
                           Ext.MessageBox.alert('Success',obj.message);
                        break;
                        default:
                            Ext.MessageBox.alert('Warning',obj.message);
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
    
    //end hapus trans
    function fsave(callback){
       var form = Ext.getCmp('dataFormFarmer').getForm();
       var methode;
       if (Ext.getCmp('fSupplyTransID').getValue()!='') methode = 'PUT'; else methode = 'POST';
       form.submit({
           url: m_crud+'_trans',
           method: methode,
           waitMsg: lang('Sending data...'),
           success: function(fp, o) {
              if (o.result.id>0) {
                  Ext.getCmp('fSupplyTransID').setValue(o.result.id);
                  updateQuality();
                  if (o.result.NonFarmerID!='') Ext.getCmp('NonFarmerID').setValue(o.result.NonFarmerID)
                  Ext.getCmp('toolbar_farmer').show()
                  Ext.getCmp('toolbar_farmer2').show()
                  Ext.getCmp('toolbar_farmer3').show()
              } else {
                 //**//Ext.MessageBox.alert('Success', lang('Data saved.'));
              }
               if (parseInt(Ext.getCmp('FFVolumeNetto2').getValue())>0 || parseInt(Ext.getCmp('FAQVolumeNetto2').getValue())>0) winFarmer.hide();
               store_detail.load({
                  params: {
                     id: Ext.getCmp('fSupplyBatchID').getValue()
               }});
               //**//
               Ext.MessageBox.alert('Success', lang('Data saved.'));
                if (typeof callback === "function") {
                    callback();
                }
               //**//
           }
       });
    }
   //show-hide
   function hide_all(show) {
      var hide = ['pengiriman','SupplyBatchResponsible','perwakilan','SupplyDestOrgID','unitto','DestICS','DestNoPolisi','DestTransport','DestDriver','PanelDriver','DestDriverJabatan','DestDriverAddress','unitfrom',
         'toolbar_detail_farmer','toolbar_add_batch','toolbar_detail_farmer_cert','toolbar_detail_non_farmer','toolbar_detail_batch',
         'toolbar_detail_update','toolbar_detail_update_detail','toolbar_detail_remove','SupplyDestStatus','DestPO',
         'cetakPackingList','btnUploadFile','cetakSuratJalan','cetakButton','closeBatchButton','saveButton','tombol_generate',
         'DestWeight','DestJumlahKarung','DestWeightPerKarung','tombol_generate','ColDoublePack'];
      for(var i=0;i<hide.length;i++) {
         Ext.getCmp(hide[i]).hide();
      }
   }
   function set_show(show) {
      if(show!=undefined) {
         for(var i=0;i<show.length;i++) {
            Ext.getCmp(show[i]).show();
         }
      }
   }
   function set_hide(hide) {
      if(hide!=undefined) {
         for(var i=0;i<hide.length;i++) {//console.log(hide[i])
            Ext.getCmp(hide[i]).hide();
         }
      }
   }
   //end show-hide
   function setReward(len) {
      var ffreward=faqreward=0;
      for(var i=1;i<len+1;i++){
         ffreward += parseFloat(Ext.getCmp('ffreward'+i).getValue());
         faqreward += parseFloat(Ext.getCmp('faqreward'+i).getValue());
      }
      Ext.getCmp('FFRewardBruto').setValue(ffreward)
      Ext.getCmp('FFRewardBonus').setValue(0)
      Ext.getCmp('FFReward').setValue(Ext.getCmp('FFRewardBruto').getValue())
      Ext.getCmp('FAQRewardBruto').setValue(faqreward)
      Ext.getCmp('FAQRewardBonus').setValue(0)
      Ext.getCmp('FAQReward').setValue(Ext.getCmp('FAQRewardBruto').getValue())
   }
    function updateQuality(callback) {

        set_hide(['setQuality'])
        Ext.Ajax.request({
            url: m_crud+'_ff',
            params: {id: Ext.getCmp('SupplychainID').getValue()},
            method: 'GET',
            success: function(fp, o){
                var r = Ext.decode(fp.responseText);
                Ext.getCmp('FAQContractPrice').setValue(r.FAQPrice);
                Ext.getCmp('FFContractPrice').setValue(r.FFPrice);
                //**//
                //update quality
                Ext.Ajax.request({
                    url: m_crud+'_setquality/'+Ext.getCmp('SupplychainID').getValue()+'/'+Ext.getCmp('fSupplyTransID').getValue(),
                    method: 'GET',
                    success: function(fp, o){
                        var r = Ext.decode(fp.responseText);
                        //alert(r.data[0].isClaim);
                        if(r.data.length>0){
                            set_show(['setQuality']);
                            var quality = Ext.getCmp('quality-pnl');
                            quality.removeAll();

                            var cont = Ext.create('Ext.Container',{
                                xtype:'container',
                                height:25,
                                layout:{
                                    type:'hbox'
                                },
                                defaults:{
                                    width:150,
                                    height:25,
                                    style:'padding-top:3px;',
                                    margin:2
                                },
                                items:[
                                    {
                                        xtype:'label',
                                        text:'Penilaian'
                                    }
                                ]
                            });

                            if(s_isff == '1') {
                                cont.add({
                                        xtype:'label',
                                        text:s_labelff+' Result'
                                    },
                                    {
                                        xtype:'label',
                                        text:s_labelff+' Standard'
                                    },
                                    {
                                        xtype:'label',
                                        text:s_labelff+' Reward',
                                        id: 'QFFReward'
                                });
                            }

                            if(s_isfaq == '1') {
                                cont.add({
                                        xtype:'label',
                                        text:s_labelfaq+' Result'
                                    },
                                    {
                                        xtype:'label',
                                        text:s_labelfaq+' Standard'
                                    },
                                    {
                                        xtype:'label',
                                        text:s_labelfaq+' Reward',
                                        id: 'QFAQReward'
                                });
                            }



                            quality.add(cont);

                            Ext.each(r.data,function(one,idx,all){
                                console.log(one);
                                if( one.Name.indexOf('Brix') >= 0){
                                    var labelid = 'Brix';
                                    var tfield = 'textfield';
                                    var tformat = '';
                                }else if( one.Name.indexOf('Waste') >= 0){
                                    var labelid = 'Waste';
                                    var tfield = 'textfield';
                                    var tformat = '';
                                }else if( one.Name.indexOf('Time') >= 0){
                                    var labelid = 'Time';
                                    var tfield = 'timefield'; //format try 'H:i'
                                    var tformat = 'H:i';
                                }else{
                                    var labelid = one.DetailID;
                                    var tfield = 'textfield';
                                    var tformat = '';
                                }
                                var cont2 = Ext.create('Ext.Container',{
                                    xtype:'container',
                                    layout:{
                                        type:'hbox'
                                    },
                                    defaults:{
                                        width:150,
                                        margin:2
                                    },
                                    items:[
                                        {
                                            xtype:'label',
                                            style:'padding-top:10px;',
                                            text:one.Name
                                        }
                                    ]
                                });
                                if((one.IsClaim=='0'&&one.IsReward=='0') ||(one.IsClaim==''&&one.IsReward=='') || (one.IsClaim==null&&one.IsReward==null)){
                                    //var hid = true;
                                    var hid = false;
                                }else{
                                    var hid = false;
                                }

                                if(s_isff == '1') {
                                    if( one.Name.indexOf('Time') >= 0){
                                        var qres2 = one.FFResult;
                                    }else{
                                        var qres2 = isNaN(parseFloat(one.FFResult))?0:parseFloat(one.FFResult);
                                    }
                                    var qstd2 = isNaN(parseFloat(one.FFValue))?0:parseFloat(one.FFValue);
                                    cont2.add({
                                        xtype:tfield,
                                        format:tformat,
                                        name: 'QFFResult[]',
                                        id: 'QFFResult'+labelid,
                                        value: qres2,
                                        listeners:{
                                            change: function(c,v){
                                                if(s_partner=='9'){
                                                    if( one.Name.indexOf('Brix') >= 0){

                                                    }else if( one.Name.indexOf('Waste') >= 0){

                                                    }
                                                }else{
                                                    var std = c.nextSibling();
                                                    var stv = std.getValue();
                                                    var reward = std.nextSibling();
                                                    var str = one.FFFormula;
                                                    var str2 = str.replace("[R]","v");
                                                    var str2 = str2.replace("[S]","stv");
                                                    var formula = eval(str2);
                                                    if(one.IsClaim=='1'&&one.IsReward=='0'){
                                                        if(parseFloat(formula) < 0){
                                                            var formula = 0;
                                                        }else if(parseFloat(formula) > 0){
                                                            var formula = 0 - parseFloat(formula);
                                                        }
                                                    }else if(one.IsClaim=='0'&&one.IsReward=='1'){
                                                        if(parseFloat(formula) < 0){
                                                            var formula = Math.abs(parseFloat(formula));
                                                        }else if(parseFloat(formula) > 0){
                                                            var formula = 0;
                                                        }
                                                    }else  if(one.IsClaim=='1'&&one.IsReward=='1'){
                                                        if(parseFloat(formula) < 0){
                                                            var formula = Math.abs(parseFloat(formula));
                                                        }else if(parseFloat(formula) > 0){
                                                            var formula = 0 - parseFloat(formula);
                                                        }
                                                    }
                                                    reward.setValue(formula);
                                                    if(one.FormulaNettoPrice!='' || one.FormulaNettoAkhir!=''){
                                                        var selects = Ext.ComponentQuery.query('textfield[test="QFFHasilReward"]');
                                                        var hasil = 0;
                                                        Ext.Array.each(selects,function(ones,index,all){
                                                            hasil = parseFloat(hasil) + parseFloat(ones.getValue());
                                                        });
                                                        var hasil2 = Math.abs(hasil);
                                                        if(one.FormulaNettoPrice!=''){
                                                            var strFNP = one.FormulaNettoPrice;
                                                            var FNP = Ext.getCmp('FFContractPrice').getValue();
                                                            var str2 = strFNP.replace(new RegExp("\\[R\\]", 'g'), "hasil2");
                                                            var str3 = str2.replace(new RegExp("\\[C\\]", 'g'), "FNP");
                                                            Ext.getCmp('FFNetPrice').setValue(eval(str3));
                                                        }
                                                        if(one.FormulaNettoAkhir!=''){
                                                            var strFNA = one.FormulaNettoAkhir;
                                                            var FNA = Ext.getCmp('FFVolumeNettoTrans').getValue();
                                                            var str2 = strFNA.replace(new RegExp("\\[R\\]", 'g'), "hasil2");
                                                            var str3 = str2.replace(new RegExp("\\[N\\]", 'g'), "FNA");
                                                            Ext.getCmp('FFVolumeNetto2').setValue(eval(str3));
                                                        }

                                                    }
                                                }
                                            }
                                        }
                                    },
                                    {
                                        xtype:'textfield',
                                        readOnly:true,
                                        value:qstd2,
                                        name: 'QFFFStandard[]',
                                        id: 'QFFStandard'+labelid,
                                    },
                                    {
                                        xtype:'textfield',
                                        readOnly:true,
                                        name: 'QFFReward[]',
                                        test: 'QFFHasilReward',
                                        id: 'QFFReward'+labelid,
                                        value: one.FFReward,
                                        hidden: hid
                                    });
                                    Ext.getCmp('QFFResult'+labelid).setValue(qres2);
                                }


                                if(s_isfaq == '1') {
                                    if( one.Name.indexOf('Time') >= 0){
                                        var qres = one.FAQResult;
                                    }else{
                                        var qres = isNaN(parseFloat(one.FAQResult))?0:parseFloat(one.FAQResult);
                                    }
                                    var qstd = isNaN(parseFloat(one.FAQValue))?0:parseFloat(one.FAQValue);
                                    cont2.add({
                                        xtype:tfield,
                                        name: 'QFAQResult[]',
                                        id: 'QFAQResult'+labelid,
                                        value: qres,
                                        format:tformat,
                                        listeners:{
                                            change: function(c,v){
                                                if(Ext.isDefined(Ext.getCmp('QFAQResultTime'))){
                                                    var QFAQResultTime = Ext.getCmp('QFAQResultTime').getRawValue();
                                                }else{
                                                    var QFAQResultTime = 0;
                                                }
                                                if(Ext.isDefined(Ext.getCmp('QFAQResultBrix'))){
                                                    var QFAQResultBrix = isNaN(parseFloat(Ext.getCmp('QFAQResultBrix').getValue()))?0:parseFloat(Ext.getCmp('QFAQResultBrix').getValue());
                                                }else{
                                                    var QFAQResultBrix = 0;
                                                }
                                                if(Ext.isDefined(Ext.getCmp('QFAQResultWaste'))){
                                                    var QFAQResultWaste = isNaN(parseFloat(Ext.getCmp('QFAQResultWaste').getValue()))?0:parseFloat(Ext.getCmp('QFAQResultWaste').getValue());
                                                }else{
                                                    var QFAQResultWaste = 0;
                                                }
                                                
                                                var FAQContractPrice = isNaN(parseFloat(Ext.getCmp('FAQContractPrice').getValue()))?0:parseFloat(Ext.getCmp('FAQContractPrice').getValue());
                                                if(s_partner=='9'){
                                                    if(QFAQResultWaste<=2.5){
                                                        var pengurang = 2.5;
                                                    }else if(QFAQResultWaste>6){
                                                        var pengurang = 6;
                                                    }
                                                    var ContractPrice = FAQContractPrice;
                                                    if(QFAQResultWaste > 2.5 && QFAQResultWaste <=6){
                                                        var rew = 0;
                                                        var NetPrice = ContractPrice;
                                                    }else{
                                                        var rew = pengurang - QFAQResultWaste;
                                                        var NetPrice = ((rew / 100) * ContractPrice) + ContractPrice;
                                                    }
                                                    Ext.getCmp('QFAQRewardWaste').setValue(rew);

                                                    if(Ext.isDefined(Ext.getCmp('QFAQResultBrix')) && Ext.isDefined(Ext.getCmp('QFAQResultTime'))){
                                                        if(QFAQResultBrix >= 15 && QFAQResultWaste <= 6 && new Date('1990-01-01 '+jam24(QFAQResultTime)) >= new Date('1990-01-01 10:00:00') && new Date('1990-01-01 '+jam24(QFAQResultTime)) <= new Date('1990-01-01 22:00:00') ){
                                                            NetPrice = NetPrice + 200;
                                                        }    
                                                    }
                                                    Ext.getCmp('FAQNetPrice').setValue(NetPrice);
                                                    if(Ext.isDefined(Ext.getCmp('QFAQResultBrix'))){
                                                        Ext.getCmp('QFAQRewardBrix').setValue(QFAQResultBrix);
                                                    }

                                                    if(Ext.isDefined(Ext.getCmp('QFAQResultTime'))){
                                                        Ext.getCmp('QFAQRewardTime').setValue(0);
                                                    }
                                                }else{
                                                    var std = c.nextSibling();
                                                    var stv = std.getValue();
                                                    var reward = std.nextSibling();
                                                    var str = one.FAQFormula;
                                                    var str2 = str.replace("[R]","v");
                                                    var str2 = str2.replace("[S]","stv");
                                                    var formula = eval(str2);
                                                    if(one.IsClaim=='1'&&one.IsReward=='0'){
                                                        if(parseFloat(formula) < 0){
                                                            var formula = 0;
                                                        }else if(parseFloat(formula) > 0){
                                                            var formula = 0 - parseFloat(formula);
                                                        }
                                                    }else if(one.IsClaim=='0'&&one.IsReward=='1'){
                                                        if(parseFloat(formula) < 0){
                                                            var formula = Math.abs(parseFloat(formula));
                                                        }else if(parseFloat(formula) > 0){
                                                            var formula = 0;
                                                        }
                                                    }else  if(one.IsClaim=='1'&&one.IsReward=='1'){
                                                        if(parseFloat(formula) < 0){
                                                            var formula = Math.abs(parseFloat(formula));
                                                        }else if(parseFloat(formula) > 0){
                                                            var formula = 0 - parseFloat(formula);
                                                        }
                                                    }
                                                    reward.setValue(formula);
                                                    if(one.FormulaNettoPrice!='' || one.FormulaNettoAkhir!=''){
                                                        var selects = Ext.ComponentQuery.query('textfield[test="QFAQHasilReward"]');
                                                        var hasil = 0;
                                                        Ext.Array.each(selects,function(ones,index,all){
                                                            hasil = parseFloat(hasil) + parseFloat(ones.getValue());
                                                        });
                                                        var hasil2 = Math.abs(hasil);
                                                        if(one.FormulaNettoPrice!=''){
                                                            var strFNP = one.FormulaNettoPrice;
                                                            var FNP = Ext.getCmp('FAQContractPrice').getValue();
                                                            var str2 = strFNP.replace(new RegExp("\\[R\\]", 'g'), "hasil2");
                                                            var str3 = str2.replace(new RegExp("\\[C\\]", 'g'), "FNP");
                                                            Ext.getCmp('FAQNetPrice').setValue(eval(str3));
                                                        }
                                                        if(one.FormulaNettoAkhir!=''){
                                                            var strFNA = one.FormulaNettoAkhir;
                                                            var FNA = Ext.getCmp('FAQVolumeNettoTrans').getValue();
                                                            var str2 = strFNA.replace(new RegExp("\\[R\\]", 'g'), "hasil2");
                                                            var str3 = str2.replace(new RegExp("\\[N\\]", 'g'), "FNA");
                                                            Ext.getCmp('FAQVolumeNetto2').setValue(eval(str3));
                                                        }

                                                    }
                                                }
                                            }
                                        }
                                    },
                                    {
                                        xtype:'textfield',
                                        readOnly:true,
                                        name: 'QFAQStandard[]',
                                        id: 'QFAQStandard'+labelid,
                                        value:qstd
                                    },{
                                        xtype:'textfield',
                                        readOnly:true,
                                        name: 'QFAQReward[]',
                                        test : 'QFAQHasilReward',
                                        id: 'QFAQReward'+labelid,
                                        value: one.FAQReward,
                                        hidden: hid
                                    });
                                    Ext.getCmp('QFAQResult'+labelid).setValue(qres);
                                }



                                cont2.add({
                                    xtype:'textfield',
                                    readOnly:true,
                                    name: 'QDetailID[]',
                                    id: 'QDetailID'+labelid,
                                    value: one.DetailID,
                                    hidden: true
                                },{
                                    xtype:'textfield',
                                    readOnly:true,
                                    name: 'QStandardID[]',
                                    id: 'QStandardID'+labelid,
                                    value: one.StandardID,
                                    hidden: true
                                });

                                quality.add(cont2);
                            });

                            if (typeof callback === "function") {
                                callback();
                            }
                        }else{
                            if (typeof callback === "function") {
                                callback();
                            }
                            return;
                        }
                        //untuk fieldset aja nih yak
                        //if(r.data.length>0) { set_show(['setQuality']); }

                    }
                })
                //**//
         }
        })
    }
    function addDriver(rDestDriver,rDestDriverJabatan,rDestDriverAddress,rDestNoPolisi,rDestTransport) {
        //var parent = Ext.getCmp('driver-pnl');
        //var selects = Ext.ComponentQuery.query('textfield[forfield="DestDriver"]',parent);
        //var i = parseInt(selects.length) + 1;
        if(rDestDriver==null || rDestDriver== undefined){rDestDriver='';}
        if(rDestDriverJabatan==null || rDestDriverJabatan== undefined){rDestDriverJabatan='';}
        if(rDestDriverAddress==null || rDestDriverAddress== undefined){rDestDriverAddress='';}
        if(rDestNoPolisi==null || rDestNoPolisi== undefined){rDestNoPolisi='';}
        if(rDestTransport==null || rDestTransport== undefined){rDestTransport='';}
        var i = parseInt(Ext.getCmp('iDriver').getValue()) + 1;
        Ext.getCmp('iDriver').setValue(i); 
        
        if(s_isdriver!='1'){ var isdriver=true; }else{ var isdriver=false; }
        if(s_isdriverpostition!='1'){ var isdriverpostition=true; }else{ var isdriverpostition=false; }
        if(s_isdriveraddress!='1'){ var isdriveraddress=true; }else{ var isdriveraddress=false; }
        if(s_isvehicletype!='1'){ var isvehicletype=true; }else{ var isvehicletype=false; }
        if(s_ispolicenumber!='1'){ var ispolicenumber=true; }else{ var ispolicenumber=false; }

        if(Ext.getCmp('SupplyDestStatus').getValue()=='Close Batch'){
            var rh = false;
        }else{
            var rh = true;
        }

        //
        var driver = Ext.getCmp('driver-pnl');
        //driver.removeAll();
        var cont = Ext.create('Ext.Container',{
            layout: 'column',
            id: 'panel_driver'+i,
            items: [{
                columnWidth: 0.5,
                layout: 'form',
                padding:5,
                items:[{
                    xtype: 'textfield',
                    forfield: 'DestDriver',
                    fieldLabel: lang('Driver'),
                    id: 'DestDriver'+i,
                    value: rDestDriver,
                    hidden : isdriver,
                    name: 'DestDriver[]'
                }, {
                    xtype: 'textfield',
                    fieldLabel: lang('Jabatan'),
                    id: 'DestDriverJabatan'+i,
                    value: rDestDriverJabatan,
                    hidden : isdriverpostition,
                    name: 'DestDriverJabatan[]'
                }, {
                    xtype: 'textfield',
                    fieldLabel: lang('Alamat'),
                    id: 'DestDriverAddress'+i,
                    value: rDestDriverAddress,
                    hidden : isdriveraddress,
                    name: 'DestDriverAddress[]'
                }]
            }, {
                columnWidth: 0.5,
                layout: 'form',
                padding:5,
                items:[{
                    xtype: 'textfield',
                    fieldLabel: lang('No. Kendaraan'),
                    id: 'DestNoPolisi'+i,
                    value: rDestNoPolisi,
                    hidden : ispolicenumber,
                    name: 'DestNoPolisi[]'
                }, {
                    xtype: 'textfield',
                    fieldLabel: lang('Kendaraan'),
                    id: 'DestTransport'+i,
                    value: rDestTransport,
                    hidden : isvehicletype,
                    name: 'DestTransport[]'
                }, {
                    xtype :'button',
                    icon: varjs.config.base_url+'images/icons/silk/delete.png',
                    text: lang('Remove'),
                    hidden: rh,
                    handler: function() {
                        RemoveDriver(i);
                    }
                }]
            }]
        });
        driver.add(cont);
    }
    function RemoveDriver(i){
        var r = Ext.getCmp('panel_driver'+i);
        r.removeAll();
    }
   //update quality
   //add trans(farmer cert,farmer non cert,non farmer,batch)
    Ext.define('farmer.Model', {
        extend: 'Ext.data.Model',
        fields: [{name:'DetailID'},{name:'SupplyTransID'},{name:'PackageType'},{name:'Type'},{name:'Weight',type:'float'},{name:'defaultWeight',type:'float'},{name:'DetailID',type:'float'},
            {name:'PackageWeight',type:'float'},{name:'MoistureStandard',type:'float'},{name:'Moisture',type:'float'},
            {name:'PackageID'},{name:'Netto'}],
    });
    //farmer
    var store_farmer = Ext.create('Ext.data.Store', {
        model: 'farmer.Model',
        autoLoad: false,
        pageSize: 50,
        proxy: {
            type: 'ajax',
            url: m_crud+'s_farmer',
            reader: {
                type: 'json',
                root: 'data',
                totalProperty: 'total'
            }
        },
        listeners : {
            load: function(store, record){
               var FFBruto = FAQBruto = FFPackage = FAQPackage = FFMoisture = FAQMoisture = 0;
               store.each(function(record){
                    if (record.get('Type')=='FF') {
                       FFBruto += record.get('Weight');
                       FFPackage += record.get('PackageWeight');
                       if (record.get('Moisture')>record.get('MoistureStandard') && record.get('MoistureStandard')>0)
                         FFMoisture += (record.get('Moisture')-record.get('MoistureStandard'))/100*(record.get('Weight')-record.get('PackageWeight'));
                    } else {
                       FAQBruto += record.get('Weight');
                       FAQPackage += record.get('PackageWeight');
                       //console.log(record)
                       if (record.get('Moisture')>record.get('MoistureStandard') && record.get('MoistureStandard')>0)
                         FAQMoisture += (record.get('Moisture')-record.get('MoistureStandard'))/100*(record.get('Weight')-record.get('PackageWeight'));
                    }
                    console.log(record)
               });
               //console.log(s_formulakarung)
               Ext.getCmp('FFVolumeBrutoTrans').setValue(Math.round(FFBruto * 100) / 100)
               Ext.getCmp('FFPackageTrans').setValue(FFPackage)
               if (FFMoisture>0) Ext.getCmp('FFMoistureTrans').setValue(FFMoisture)
               else Ext.getCmp('FFMoistureTrans').setValue(0)
               if(s_formulakarung!=''&&s_formulakarung!=null&&s_formulakarung!='null'&&s_formulakarung!='undefined'&&s_formulakarung!=undefined){
                var str = s_formulakarung.replace(/\[B\]/g, parseFloat(FFBruto));
                str = str.replace(/\[P\]/g, parseFloat(FFPackage));
                str = str.replace(/\[M\]/g, parseFloat(FFMoisture));
                Ext.getCmp('FFVolumeNettoTrans').setValue(eval(str))
               }
               Ext.getCmp('FAQVolumeBrutoTrans').setValue(Math.round(FAQBruto * 100) / 100)
               Ext.getCmp('FAQPackageTrans').setValue(FAQPackage)
               if (FAQMoisture>0) Ext.getCmp('FAQMoistureTrans').setValue(FAQMoisture)
               else Ext.getCmp('FAQMoistureTrans').setValue(0)
               if(s_formulakarung!=''&&s_formulakarung!=null&&s_formulakarung!='null'&&s_formulakarung!='undefined'&&s_formulakarung!=undefined){
                var qstr = s_formulakarung.replace(/\[B\]/g, parseFloat(FAQBruto));
                qstr = qstr.replace(/\[P\]/g, parseFloat(FAQPackage));
                qstr = qstr.replace(/\[M\]/g, parseFloat(FAQMoisture));
                Ext.getCmp('FAQVolumeNettoTrans').setValue(eval(qstr))
               }
               Ext.getCmp('FFVolumeNetto2').setValue(Ext.getCmp('FFVolumeNettoTrans').getValue())
               Ext.getCmp('FAQVolumeNetto2').setValue(Ext.getCmp('FAQVolumeNettoTrans').getValue())
            }
        }
    });
   var cvillage = Ext.create('Ext.data.Store', {
      extend: 'Ext.data.Model',
      fields: ['id','label'],
      pageSize: 10,
      autoLoad: false,
      proxy: {
         type: 'ajax',
         url: m_crud+'_village',
         reader: {
            type: 'json',
            root: 'data'
         }
      }
   });
   var cpackage = Ext.create('Ext.data.Store', {
      extend: 'Ext.data.Model',
      fields: ['id','label','berat'],
      pageSize: 10,
      proxy: {
         type: 'ajax',
         url: m_crud+'s_package',
         reader: {
            type: 'json',
            root: 'data'
         }
      }
   });
   var ctype = Ext.create('Ext.data.Store', {
       fields: ['label'],
       data : [
           {"label":s_labelff},
           {"label":s_labelfaq}
       ]
   });
   var pRowEditing = Ext.create('Ext.grid.plugin.RowEditing', {
      id: 'pRowEditing',
      clicksToMoveEditor: 0,
      autoCancel: false,
      errorSummary : false,
      clicksToEdit: 2
   });
    var c_batch = Ext.create('Ext.data.Store', {
        extend: 'Ext.data.Model',
        fields: ['id','label','DestPO','nama','VolumeNetto','TotalPayment'],
        autoLoad: false,
        pageSize: 25,
        proxy: {
            type: 'ajax',
            url: m_crud+'_batch_sent',
            reader: {
                type: 'json',
                root: 'data'
            }
        }
    });
    Ext.define("NonPost", {
        extend: 'Ext.data.Model',
        proxy: {
            type: 'ajax',
            url : m_crud+'_non_farmers',
            reader: {
                type: 'json',
                root: 'data',
                totalProperty: 'total'
            }
        },
        fields: [
            {name: 'id', mapping: 'id'},
            {name: 'name', mapping: 'name'},
            {name: 'FarmerIdentity', mapping: 'FarmerIdentity'},
            {name: 'FarmerVillageID', mapping: 'FarmerVillageID'},
            {name: 'FarmerBirthdate', mapping: 'FarmerBirthdate'}
        ]
    });
    var ds_non = Ext.create('Ext.data.Store', {
        pageSize: 10,
        model: 'NonPost'
    });
   Ext.define("Post", {
        extend: 'Ext.data.Model',
        proxy: {
            type: 'ajax',
            url : m_crud+'_farmers',
            reader: {
                type: 'json',
                root: 'data',
                totalProperty: 'total'
            }
        },
        fields: [
            {name: 'id', mapping: 'id'},
            {name: 'name', mapping: 'name'},
            {name: 'grup', mapping: 'grup'},
            {name: 'district', mapping: 'district'},
            {name: 'batas_atas', mapping: 'batas_atas'},
            {name: 'jual', mapping: 'jual'},
            {name: 'sisa', mapping: 'sisa'},
        ]
    });

    var ds = Ext.create('Ext.data.Store', {
        pageSize: 10,
        model: 'Post'
    });
    //**//
    var DataFormHidden = Ext.create('Ext.form.Panel', {
      frame: false,
      autoScroll: true,
      hidden: true,
      bodyPadding: 5,
      id:'dataFormHidden',
      fieldDefaults: {
         labelAlign: 'left',
         labelWidth: 120,
         anchor: '100%'
      },
      items: [{
         layout: 'column',
         items: [{
            columnWidth: 0.5,
            layout: 'form',
            padding:5,
            items:[{
               xtype: 'textfield',
               id: 'AddBatchBUFromCoop',
               name: 'AddBatchBUFromCoop',
               hidden:true
           },{
               xtype: 'textfield',
               id: 'SupplyBatchNumber1',
               name: 'SupplyBatchNumber1',
               hidden:true
           },{
               xtype: 'textfield',
               id: 'SupplyBatchNumber2',
               name: 'SupplyBatchNumber2',
               hidden:true
           },{
               xtype: 'textfield',
               id: 'SupplyBatchID1',
               name: 'SupplyBatchID1',
               hidden:true
           },{
               xtype: 'textfield',
               id: 'SupplyBatchID2',
               name: 'SupplyBatchID2',
               hidden:true
           },{
               xtype: 'textfield',
               id: 'SupplychainID1',
               name: 'SupplychainID1',
               hidden:true
           },{
               xtype: 'textfield',
               id: 'SupplychainID2',
               name: 'SupplychainID2',
               hidden:true
           },{
               xtype: 'textfield',
               id: 'fcol1',
               name: 'fcol1',
               hidden:true
           },{
               xtype: 'textfield',
               id: 'BatchID1',
               name: 'BatchID1',
               hidden:true
           },{
               xtype: 'textfield',
               id: 'PemisahanBatch1',
               name: 'PemisahanBatch1',
               hidden:true
           },{
               xtype: 'textfield',
               id: 'IsCetakSuratJalan',
               name: 'IsCetakSuratJalan',
               hidden:true
           },{
               xtype: 'textfield',
               id: 'noncert',
               name: 'noncert',
               hidden:true
           }]
         }]
      }]
   });
    //**//
    var DataFormFarmer = Ext.create('Ext.form.Panel', {
        frame: false,
        autoScroll: true,
        bodyPadding: 5,
        id:'dataFormFarmer',
        fieldDefaults: {
            labelAlign: 'left',
            labelWidth: 120,
            anchor: '100%'
        },
        items: [{
         layout: 'column',
         id:'header_trans',
         items: [{
          columnWidth: 0.5,
          layout: 'form',
          padding:5,
          items:[{
               xtype: 'textfield',
               fieldLabel: lang('Transaction ID'),
               id: 'fSupplyTransID',
               name: 'SupplyTransID',
               readOnly:true
           },
           {
               xtype: 'textfield',
               id: 'fSupplyBatchID',
               name: 'SupplyBatchID',
               hidden:true
           },{
               xtype: 'textfield',
               id: 'SupplyType',
               name: 'SupplyType',
               hidden:true
           },{
               xtype: 'combo',
               store: ds,
               id:'fcol',
               name:'fcol',
               displayField: 'id',
               fieldLabel: lang('Farmer'),
               typeAhead: false,
               hideTrigger:true,
               queryCaching:false,
               emptyText: lang('Search by Name/ID'),
               anchor: '100%',
               listConfig: {
                   loadingText: 'Searching...',
                   emptyText: 'No matching farmer found.',
                   // Custom rendering template for each item
                   getInnerTpl: function() {
                       return '<div class="search-item">' +
                           '{id} - {name} ({grup})' +
                           '{excerpt}' +
                           '<br>Kuota : <b>{batas_atas}</b> | Sisa : <b>{sisa}</b>'
                       '</div>';
                   }
               },
               pageSize: 10,
               // override default onSelect to do redirect
               listeners: {
                   select: function(combo, selection) {
                       var post = selection[0];
                       if (post) {
                           Ext.getCmp('FarmerID').setValue(post.get('id'));
                           Ext.getCmp('FarmerName').setValue(post.get('name'));
                           Ext.getCmp('GroupName').setValue(post.get('grup'));
                           Ext.getCmp('District').setValue(post.get('district'));
                           Ext.getCmp('BatasFarmerID').setValue(post.get('batas_atas'));
                           Ext.getCmp('BatasJual').setValue(post.get('jual'));
                           var dijual = (post.get('jual')-0);
                           var maxjual = Math.round((post.get('batas_atas')-0)*100/100);
                           var sisa = parseInt(maxjual)-parseInt(dijual);
                           //Ext.getCmp('SisaPenjualan').setValue(sisa);
                           //Ext.MessageBox.alert('Warning','Farmer sudah menjual sebanyak '+dijual+' Kg dari '+ maxjual+' Kg yang diijinkan');
                           Ext.getCmp('saveButtonTrans').show();
                       }
                   }
               }
           },{
               xtype: 'textfield',
               id: 'NonFarmerID',
               name: 'NonFarmerID',
               hidden:true
           },{
               xtype: 'textfield',
               id: 'FarmerID',
               name: 'FarmerID',
               hidden:true
           },{
               xtype: 'textfield',
               id: 'BatasFarmerID',
               name: 'BatasFarmerID',
               hidden:true
           },{
               xtype: 'textfield',
               id: 'BatasWeightOld',
               name: 'BatasWeightOld',
               hidden:true
           },{
               xtype: 'textfield',
               id: 'BatasJual',
               name: 'BatasJual',
               hidden:true
           },{
               xtype: 'combo',
               store: ds_non,
               id:'fncol',
               name:'NonFarmerName',
               displayField: 'name',
               fieldLabel: lang('Nama'),
               typeAhead: false,
               hideTrigger:true,
               anchor: '100%',
               listConfig: {
                   loadingText: 'Searching...',
                   emptyText: 'No matching non farmer found.',
                   // Custom rendering template for each item
                   getInnerTpl: function() {
                       return '<div class="search-item">' +
                           '{id} - {name} ({grup})' +
                           '{excerpt}' +
                       '</div>';
                   }
               },
               pageSize: 10,
               // override default onSelect to do redirect
               listeners: {
                   select: function(combo, selection) {
                       var post = selection[0];
                       if (post) {
                           console.log(post.get('FarmerIdentity'));
                           Ext.getCmp('NonFarmerID').setValue(post.get('id'));
                           //Ext.getCmp('NonFarmerName').setValue(post.get('name'));
                           Ext.getCmp('NonIdentity').setValue(post.get('FarmerIdentity'));
                           Ext.getCmp('NonBirthdate').setValue(post.get('FarmerBirthdate'));
                           Ext.getCmp('NonVillageID').setValue(post.get('FarmerVillageID'));
                       }
                   }
               }
           },{
               xtype: 'textfield',
               fieldLabel: lang('No Identitas'),
               id: 'NonIdentity',
               name: 'NonIdentity'
           },{
            layout: 'column',
            id:'bcol',
            items: [{
             columnWidth: 0.85,
             layout: 'form',
             items:[{
               xtype: 'combo',
               store: c_batch,
               id:'BatchID',
               name:'BatchID',
               displayField: 'id',
               valueField: 'id',
               fieldLabel: lang('Batch Number'),
               typeAhead: false,
               hideTrigger:true,
               queryCaching:false,
               emptyText: lang(''),
               anchor: '100%',
               listConfig: {
                   loadingText: 'Searching...',
                   emptyText: 'No matching farmer found.',
                   // Custom rendering template for each item
                   getInnerTpl: function() {
                       return '<div class="search-item">' +
                           '<b>[{id}] | {nama}</b><br>' +
                           'No. PO : {DestPO}<br>' +
                           lang('Weight') + ' : {VolumeNetto} | ' + lang('Total Payment') + ' : {TotalPayment}' +
                           '<hr style="margin-top:-2px;margin-bottom:-2px;"/>' +
                       '</div>';
                   }
               },
               pageSize: 3,
               // override default onSelect to do redirect
               listeners: {
                   change: function(combo, selection) {
                        if (change_cbatch) ebsearch('check')
                        c_batch.load()
                   },
                   focus :{scope:this, fn:function(field) {
                              field.onTriggerClick();
                        }
                   }

               }
             }]
            },{
             columnWidth: 0.15,
             items:[{
                    xtype :'button',
                    icon: varjs.config.base_url + 'images/icons/new/add.png', cls:'Sfr_BtnGridGreen', overCls:'Sfr_BtnGridGreen-Hover',
                    margin: '0px 0px 0px 6px',
                    text: lang('Batch'),
                    id:'toolbar_add_batch',
                    handler: function() {
                        //**//
                        set_hide(['toolbar_detail_farmer','toolbar_add_batch','toolbar_detail_farmer_cert','toolbar_detail_non_farmer','toolbar_detail_batch','toolbar_detail_update','toolbar_detail_remove']);
                        set_show(['toolbar_detail_update_detail']);
                        //**//
                        Ext.getCmp('win').setTitle(lang('Batch Buying Unit'));
                        add_batch();

                    }
               }]
             }/*,{
             columnWidth: 0.15,
             items:[{
                    xtype :'button',
                    hidden: true,
                    icon: varjs.config.base_url+'images/icons/silk/application_view_detail.png',
                    margin: '0px 0px 0px 6px',
                    text: lang('View'),
                    hide:true,
                    handler: function() {
                    }
               }]
             }*/]
           },{
               xtype: 'textfield',
               fieldLabel: lang('Farmer Name'),
               id: 'FarmerName',
               name: 'FarmerName',
               readOnly:true
           },{
               xtype: 'textfield',
               fieldLabel: lang('Nomor Faktur'),
               id: 'FakturNumber',
               name: 'FakturNumber'
           }]
          },{
          columnWidth: 0.5,
          layout: 'form',
          padding:5,
          items:[{
               xtype: 'datefield',
               fieldLabel: lang('Tanggal Lahir'),
               id: 'NonBirthdate',
               name: 'NonBirthdate',
               format:'Y-m-d'
           },{
               xtype      : 'combo',
               fieldLabel: lang('Village'),
               store : cvillage,
               id:'NonVillageID',
               name:'NonVillageID',
               queryMode: 'local',
               displayField: 'label',
               valueField: 'id'
           },{
               xtype: 'textfield',
               fieldLabel: lang('Group Name'),
               id: 'GroupName',
               name: 'GroupName',
               readOnly:true
           },{
               xtype: 'textfield',
               fieldLabel: lang('District'),
               id: 'District',
               name: 'District',
               readOnly:true
           },{
               xtype: 'textfield',
               fieldLabel: lang('Batch From'),
               id: 'BatchFromName',
               name: 'BatchFromName',
               readOnly:true
           },{
               xtype: 'textfield',
               id: 'frombatchid',
               name: 'frombatchid',
               hidden:true
           },{
               xtype: 'datefield',
               fieldLabel: lang('Date Transaction'),
               id: 'fDateTransaction',
               name: 'DateTransaction',
               format:'Y-m-d'
           }]
          }]
        },{
            xtype: 'gridpanel',
            id:'grid_farmer',
            store: store_farmer,
            width: '100%',
            minHeight: 200,
            loadMask: true,
            selType: 'rowmodel',
            dockedItems: [{
               xtype: 'toolbar',
               id:'toolbar_farmer',
               items: [{
                  icon: varjs.config.base_url + 'images/icons/new/add.png', cls:'Sfr_BtnGridGreen', overCls:'Sfr_BtnGridGreen-Hover',
                  text: lang('Add'),
                  cls : m_act_save,
                  scope: this,
                  handler :function(){
                       pRowEditing.cancelEdit();
                       var r = Ext.create('farmer.Model', {
                           DetailID:'', SupplyTransID:'', PackageID:'', Weight:'', PackageWeight:'',MoistureStandard:'',
                           Moisture:'',Netto:''
                       });
                       store_farmer.insert(0, r);
                       pRowEditing.startEdit(0, 0);
                  }
               }/*,{
                  icon: varjs.config.base_url+'images/icons/silk/pencil.png',
                  cls:m_act_save,
                  text: lang('Edit'),
                  scope: this,
                  handler : function() {
                    pRowEditing.cancelEdit();
                    var sm = Ext.getCmp('grid_farmer').getSelectionModel().getSelection();
                    pRowEditing.startEdit(sm[0].index, 0);
                  }
               },{
                  itemId: 'remove_trans',
                  icon: varjs.config.base_url+'images/icons/silk/delete.png',
                  text: lang('Hapus'),
                  scope: this,
                  handler : function(){
                    var smb = Ext.getCmp('grid_farmer').getSelectionModel().getSelection()[0];
                    pRowEditing.cancelEdit();
                    Ext.MessageBox.confirm('Message', lang('Apakah anda mau menghapus transaksi ini ?') , function(btn){
                        if(btn == 'yes'){
                           Ext.Ajax.request({
                              waitMsg: lang('Please Wait'),
                              url: m_crud+'_farmer',
                              method : 'DELETE',
                              params: {
                                 id:  smb.raw.DetailID
                              },
                              success: function(response, opts){
                                 var obj = Ext.decode(response.responseText);
                                 switch(obj.success){
                                    case true:
                                       store_farmer.load({
                                          params: {
                                             id: Ext.getCmp('fSupplyTransID').getValue(),
                                             frombatchid: Ext.getCmp('frombatchid').getValue()
                                       }});
                                    break;
                                    default:
                                        Ext.MessageBox.alert('Warning',obj.message);
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
               }*/]
            }],
            columns: [{
              text: lang('No'),
              xtype: 'rownumberer',
              width:'5%'
            },{
              text: lang('DetailID'),
              dataIndex: 'DetailID',
              id:'DetailID',
              width:'10%',
              hidden : true,
              editor: {
                 xtype:'textfield',
                 id:'DetailID',
                 hidden: true,
              }
            },{
              text: lang('defaultWeight'),
              dataIndex: 'defaultWeight',
              id:'defaultWeight',
              width:'10%',
              hidden : true,
              editor: {
                 xtype:'textfield',
                 id:'defaultWeight',
                 hidden: true,
              }
            },{
              text: lang('Package'),
              dataIndex: 'PackageType',
              id:'setPackageType',
              width:'38%',
              editor: {
                  xtype      : 'combo',
                  store : cpackage,
                  id:'PackageI',
                  queryMode: 'local',
                  displayField: 'label',
                  valueField: 'id',
                  allowBlank: false
              }
            },{
              text: lang('Type'),
              dataIndex: 'Type',
              id:'setKarungType',
              width:'10%',
              editor: {
                  xtype : 'combo',
                  store : ctype,
                  id    :'type',
                  queryMode: 'local',
                  displayField: 'label',
                  valueField: 'label'
              }
            },{
              text: lang('Weight'),
              dataIndex: 'Weight',
              id:'setWeight',
              width:'10%',
              editor: {
                 xtype:'numberfield',
                 id:'Weight',
                 allowBlank:false,
                 minValue: 0.001,
                  listeners: {
                     change: function (cb, nv, ov) {
                        if (parseFloat(Ext.getCmp('MoistureRst').getValue())>parseFloat(Ext.getCmp('MoistureStd').getValue()) &&
                           parseFloat(Ext.getCmp('MoistureStd').getValue())>0) {
                           Ext.getCmp('Netto').setValue((100-(Ext.getCmp('MoistureRst').getValue()-Ext.getCmp('MoistureStd').getValue()))/100*Ext.getCmp('Weight').getValue())
                        } else Ext.getCmp('Netto').setValue(Ext.getCmp('Weight').getValue())
                     }
                  }
              }
            },{
              text: lang('Moisture Standar'),
              dataIndex: 'MoistureStandard',
              id:'setMoistureStd',
              width:'10%',
              editor: {
                 xtype:'textfield',
                 id:'MoistureStd',
                 readOnly:true
              }
            },{
              text: lang('Moisture Result'),
              dataIndex: 'Moisture',
              id:'setMoistureRst',
              width:'10%',
              editor: {
                 xtype:'textfield',
                 id:'MoistureRst',
                  listeners: {
                     change: function (cb, nv, ov) {
                        if (parseFloat(Ext.getCmp('MoistureRst').getValue())>parseFloat(Ext.getCmp('MoistureStd').getValue()) &&
                           parseFloat(Ext.getCmp('MoistureStd').getValue())>0) {
                           Ext.getCmp('Netto').setValue((100-(Ext.getCmp('MoistureRst').getValue()-Ext.getCmp('MoistureStd').getValue()))/100*Ext.getCmp('Weight').getValue())
                        } else Ext.getCmp('Netto').setValue(Ext.getCmp('Weight').getValue())
                     }
                  }
              }
            },{
              text: lang('Netto'),
              dataIndex: 'Netto',
              id:'setNetto',
              width:'10%',
              editor: {
                 xtype:'textfield',
                 id:'Netto',
                 readOnly:true
              }
            }],
            plugins: [pRowEditing],
            listeners: {
                itemclick: function(view, record, item, index, e){
                    contextMenuTransaction.showAt(e.getXY());
                },
               'beforeedit':function( editor, e, eOpts ) {
//                  Ext.getCmp('BatasWeightOld').setValue(Ext.getCmp('Weight').getValue());
                  if (Ext.getCmp('SupplyDestStatus').getValue()=='Delivered' || Ext.getCmp('SupplyDestStatus').getValue()=='Closed' ||
                     Ext.getCmp('SupplyDestStatus').getValue()=='Other' || Ext.getCmp('SupplyDestStatus').getValue()=='Sent') return false;
               },
                'canceledit':function(editor,e,eOpts){
                     store_farmer.load({
                        params: {
                           id: Ext.getCmp('fSupplyTransID').getValue(),
                           frombatchid: Ext.getCmp('frombatchid').getValue()
                     }});
                },
                'edit': function(editor, e) {
                  if(e.record.data.DetailID==''){
                    //**//
                    Ext.Ajax.request({
                        url: m_crud+'_check_farmer_quota',
                        method: 'GET',
                        params: {
                           query : Ext.getCmp('fcol').getValue(),
                           SupplyBatchID: Ext.getCmp('SupplyBatchID').getValue(),
                           SupplyTransID: Ext.getCmp('fSupplyTransID').getValue(),
                           page : 1,
                           limit: 1,
                           noncert : Ext.getCmp('noncert').getValue(),
                           start: 0
                        },
                        success: function(fp, o){
                            var r = Ext.decode(fp.responseText);
                            if(parseFloat(e.record.data.Weight) > parseFloat(r.hasil)){
                                Ext.MessageBox.alert('Warning',lang('Penjualan farmer melebihi batas yang diperbolehkan. Maksimal '+r.maks+'Kg'));
                                e.record.data.Weight = r.maks;
                                e.record.data.Netto = r.maks-e.record.data.PackageWeight;
                                store_farmer.load({
                                    params: {
                                        id: Ext.getCmp('fSupplyTransID').getValue(),
                                        frombatchid: Ext.getCmp('frombatchid').getValue()
                                    }
                                });
                                return;
                            }else{
                                //**//
                                /*if (e.record.data.Weight+Ext.getCmp('BatasJual').getValue()>0 && Ext.getCmp('BatasFarmerID').getValue()>0) {
                                    if(Ext.getCmp('BatasJual').getValue()==Ext.getCmp('BatasFarmerID').getValue()) {
                                        Ext.MessageBox.alert('Warning','Penjualan farmer sudah sama dengan batas yang diperbolehkan');
                                        return;
                                    } else if (parseFloat(e.record.data.Weight)+parseFloat(Ext.getCmp('BatasJual').getValue())>parseFloat(Ext.getCmp('BatasFarmerID').getValue())) {
                                        var min = Ext.getCmp('BatasFarmerID').getValue()-Ext.getCmp('BatasJual').getValue();
                                        console.log(e.record.data)
                                        e.record.data.Weight = min;
                                        e.record.data.Netto = min-e.record.data.PackageWeight;
                                        Ext.MessageBox.alert('Warning','Penjualan farmer maksimal adalah '+min+' Kg');
                                        return;
                                    }
                                }*/
                                Ext.getCmp('BatasJual').setValue(parseFloat(e.record.data.Weight)+parseFloat(Ext.getCmp('BatasJual').getValue()));
                                Ext.Ajax.request({
                                    waitMsg: lang('Please wait...'),
                                    url: m_crud+'_farmer',
                                    method : 'POST',
                                    params: {
                                        SupplyTransID      : Ext.getCmp('fSupplyTransID').getValue(),
                                        PackageType    : e.record.data.PackageType,
                                        Type    : e.record.data.Type,
                                        PackageID    : e.record.data.PackageI,
                                        Weight   : e.record.data.Weight,
                                        MoistureStandard   : s_standardmoisturekarung,
                                        Moisture   : e.record.data.Moisture,
                                        Netto   : e.record.data.Netto
                                    },
                                    success: function(response, opts){
                                        var obj = Ext.decode(response.responseText);
                                        switch(obj.success){
                                            case true:
                                                //Ext.MessageBox.alert('Success',obj.message);
                                                store_farmer.load({
                                                    params: {
                                                        id: Ext.getCmp('fSupplyTransID').getValue(),
                                                        frombatchid: Ext.getCmp('frombatchid').getValue()
                                                    }});
                                            break;
                                            default:
                                                Ext.MessageBox.alert('Warning',obj.message);
                                            break;
                                        }
                                    },
                                    failure: function(response, opts){
                                        var obj = Ext.decode(response.responseText);
                                        Ext.MessageBox.alert('error',lang('Could not connect to the database. Retry later'));
                                    }
                                });
                            }

                        }
                    });
                  } else {
                     Ext.MessageBox.confirm('Message', lang('Update data ini ?') , function(btn){
                        if(btn == 'yes') {
                            //**//
                            Ext.Ajax.request({
                                url: m_crud+'_check_farmer_quota',
                                method: 'GET',
                                params: {
                                   query : Ext.getCmp('fcol').getValue(),
                                   DetailID: e.record.data.DetailID,
                                   Weight: e.record.data.Weight,
                                   defaultWeight: e.record.data.defaultWeight,
                                   SupplyBatchID: Ext.getCmp('SupplyBatchID').getValue(),
                                   SupplyTransID: Ext.getCmp('fSupplyTransID').getValue(),
                                   noncert : Ext.getCmp('noncert').getValue(),
                                   page : 1,
                                   limit: 1,
                                   start: 0
                                },
                                success: function(fp, o){
                                    var r = Ext.decode(fp.responseText);

                                    //if(false){ //sementara aja ya, batasan packing list
                                    if(parseFloat(r.hasil)<0){
                                        Ext.MessageBox.alert('Warning',lang('Penjualan farmer melebihi batas yang diperbolehkan. Maksimal '+r.maks+'Kg'));
                                        e.record.data.Weight = r.maks;
                                        e.record.data.Netto = r.maks-e.record.data.PackageWeight;
                                        store_farmer.load({
                                            params: {
                                                id: Ext.getCmp('fSupplyTransID').getValue(),
                                                frombatchid: Ext.getCmp('frombatchid').getValue()
                                            }
                                        });
                                        return;
                                    }else{
                                        Ext.Ajax.request({
                                            waitMsg: lang('Please wait...'),
                                            url: m_crud+'_farmer',
                                            method : 'PUT',
                                            params: {
                                                SupplyTransID      : Ext.getCmp('fSupplyTransID').getValue(),
                                                DetailID    : e.record.data.DetailID,
                                                PackageType    : e.record.data.PackageType,
                                                PackageID    : e.record.data.PackageID,
                                                Type    : e.record.data.Type,
                                                Weight   : e.record.data.Weight,
                                                MoistureStandard   : s_standardmoisturekarung,
                                                Moisture   : e.record.data.Moisture,
                                                Netto   : e.record.data.Netto
                                            },
                                            success: function(response, opts){
                                                var obj = Ext.decode(response.responseText);
                                                switch(obj.success){
                                                    case true:
                                                        //Ext.MessageBox.alert('Success',obj.message);
                                                        store_farmer.load({
                                                            params: {
                                                                id: Ext.getCmp('fSupplyTransID').getValue(),
                                                                frombatchid: Ext.getCmp('frombatchid').getValue()
                                                            }
                                                        });
                                                    break;
                                                    default:
                                                        Ext.MessageBox.alert('Warning',obj.message);
                                                    break;
                                                }
                                            },
                                            failure: function(response, opts){
                                                var obj = Ext.decode(response.responseText);
                                                    Ext.MessageBox.alert('error',lang('Could not connect to the database. Retry later'));
                                            }
                                        });
                                    }

                                }
                            });
                            //**//
                           /*if (e.record.data.Weight+Ext.getCmp('BatasJual').getValue()>0 && Ext.getCmp('BatasFarmerID').getValue()>0) {
                           if(Ext.getCmp('BatasJual').getValue()==Ext.getCmp('BatasFarmerID').getValue()) {
                              Ext.MessageBox.alert('Warning','Penjualan farmer sudah sama dengan batas yang diperbolehkan');
                              return;
                           } else if (e.record.data.Weight+Ext.getCmp('BatasJual').getValue()-Ext.getCmp('BatasWeightOld').getValue()>Ext.getCmp('BatasFarmerID').getValue()) {
                              var min = Ext.getCmp('BatasFarmerID').getValue()-Ext.getCmp('BatasWeightOld').getValue()-Ext.getCmp('BatasJual').getValue();
                              e.record.data.Weight = min;
                              e.record.data.Netto = min-e.record.data.PackageWeight;
                              Ext.MessageBox.alert('Warning','Penjualan farmer maksimal adalah1 '+min+' Kg');
                              return;
                           }
                           }
                           Ext.getCmp('BatasJual').setValue(parseFloat(e.record.data.Weight)+parseFloat(Ext.getCmp('BatasJual').getValue()));*/

                        }
                     });
                  }
               }
            }
         },{
         layout: 'column',
         items: [{
          columnWidth: 0.6,
          layout: 'form',
          id:'trans_col',
          //padding:5,
          items:[{}]
         },{
          columnWidth: 0.2,
          layout: 'form',
          //padding:5,
          items:[
         {
            xtype: 'tbspacer',
            height:10
        },{
            xtype: 'label',cls:'x-form-item-label',
            text: lang('Bruto')
        },{
            xtype: 'label',cls:'x-form-item-label',
            text: lang('Package'),
        },{
            xtype: 'label',cls:'x-form-item-label',
            text: lang('Moisture'),
            id:'setLabelMoisture'
        },{
            xtype: 'label',cls:'x-form-item-label',
            text: lang('Netto'),
        }]},{
          columnWidth: 0.1,
          layout: 'form',
          padding:5,
          id:'ff',
          items:[
         {
            xtype: 'label',cls:'x-form-item-label',
            text: lang(s_labelff),
        },{
            xtype: 'textfield',
            id: 'FFVolumeBrutoTrans',
            name: 'FFVolumeBrutoTrans',
            //readOnly:true
        },{
            xtype: 'textfield',
            id: 'FFPackageTrans',
            name: 'FFPackageTrans',
            readOnly:true
        },{
            xtype: 'textfield',
            id: 'FFMoistureTrans',
            name: 'FFMoistureTrans'
            //readOnly:true
        },{
            xtype: 'textfield',
            id: 'FFVolumeNettoTrans',
            name: 'FFVolumeNettoTrans',
            //readOnly:true
        }]},{
          columnWidth: 0.1,
          layout: 'form',
          id:'faq',
          padding:5,
          items:[
         {
            xtype: 'label',cls:'x-form-item-label',
            text: lang(s_labelfaq),
        },{
            xtype: 'textfield',
            id: 'FAQVolumeBrutoTrans',
            name: 'FAQVolumeBrutoTrans',
            //readOnly:true
        },{
            xtype: 'textfield',
            id: 'FAQPackageTrans',
            name: 'FAQPackageTrans',
            readOnly:true
        },{
            xtype: 'textfield',
            id: 'FAQMoistureTrans',
            name: 'FAQMoistureTrans'
            //readOnly:true
        },{
            xtype: 'textfield',
            id: 'FAQVolumeNettoTrans',
            name: 'FAQVolumeNettoTrans',
            //readOnly:true
        }]}]},{
                xtype: 'fieldset',
                title: 'Quality',
                id:'setQuality',
                layout:{
                    type:'hbox',
                    pack:'end'
                },
                items: [
                    {
                        xtype:'container',
                        id:'quality-pnl'
                    }
                ]
            },{
            xtype: 'fieldset',
            id:'payment_trans',
            title: 'Payment',
            fieldDefaults: {
               labelAlign: 'left',
               labelWidth: 600,
               anchor: '100%'
           },
            items: [{
               layout: 'column',
               items: [{
                columnWidth: 0.6,
                layout: 'form',
                id:'width_kiri',
                padding:5,
                items:[{}]
               },{
                columnWidth: 0.2,
                layout: 'form',
                //padding:4,
                items:[{
                     xtype: 'tbspacer',
                     height:10
                },{
                  xtype: 'label',cls:'x-form-item-label label-height',
                  text: lang('Contract Price')
                },/*{
                  xtype: 'label',cls:'x-form-item-label label-height',
                  text: lang('Reward Bruto'),id:'RewardBruto'
                },{
                  xtype: 'label',cls:'x-form-item-label label-height',
                  text: lang('Reward Bonus'),id:'RewardBonus'
                },{
                  xtype: 'label',cls:'x-form-item-label label-height',
                  text: lang('Reward Netto(%)'),id:'RewardNetto'
                },*/{
                  xtype: 'label',cls:'x-form-item-label label-height',
                  text: lang('Net Price')
                },{
                  xtype: 'label',cls:'x-form-item-label label-height',
                  text: lang('Volume(Kg)')
                },{
                  xtype: 'label',cls:'x-form-item-label label-height',
                  text: lang('Total')
                },{
                  xtype: 'label',cls:'x-form-item-label label-height',
                  text: lang('Berat Bersih Setara'),id:'BeratBersihSetara'
                },{
                  xtype: 'label',cls:'x-form-item-label label-height',
                  text: lang('Verifikasi'),id:'Verifikasi'
                },{
                  xtype: 'label',cls:'x-form-item-label label-height',
                  text: lang('Total Payment')
                },{
                  xtype: 'label',cls:'x-form-item-label label-height',
                  text: lang('Down Payment'),id:'labeldp'
                },{
                  xtype: 'label',cls:'x-form-item-label label-height',
                  text: lang('Kekurangan Pembayaran'),id:'labeltotal'
                }]
               },{
                columnWidth: 0.1,
                layout: 'form',
                padding:5,
                id:'bawah_ff',
                items:[{
                  xtype: 'label',cls:'x-form-item-label',
                  text: lang(s_labelff)
                },{
                  xtype: 'textfield',
                  id: 'FFContractPrice',
                  name: 'FFContractPrice',
                  readOnly:true,
                  listeners: {
                     change: function (cb, nv, ov) {
                        //if (Ext.getCmp('FFReward').getValue()=='') Ext.getCmp('FFReward').setValue(0);
                     }
                  }
                },/*{
                  xtype: 'textfield',
                  id: 'FFRewardBruto',
                  name: 'FFRewardBruto',
                  readOnly:true
                },{
                  xtype: 'textfield',
                  id: 'FFRewardBonus',
                  name: 'FFRewardBonus',
                  listeners: {
                     change: function (cb, nv, ov) {
                        Ext.getCmp('FFReward').setValue((parseFloat(Ext.getCmp('FFRewardBruto').getValue())+parseFloat(Ext.getCmp('FFRewardBonus').getValue())).toFixed(2))
                     }
                  }
                },{
                  xtype: 'textfield',
                  id: 'FFReward',
                  name: 'FFReward',
                  listeners: {
                     change: function (cb, nv, ov) {
                        if(s_formulaakhir!='' && s_formulaakhir!=null){
                            var qstr = s_formulaakhir.replace(/\[R\]/g, parseFloat(Ext.getCmp('FFReward').getValue()));
                            qstr = qstr.replace(/\[N\]/g, parseFloat(Ext.getCmp('FFVolumeNettoTrans').getValue()));
                            Ext.getCmp('FFVolumeNetto2').setValue((eval(qstr)).toFixed(2));
                        }
                        if(s_formulaprice!='' && s_formulaprice!=null){
                            var qstr = s_formulaprice.replace(/\[R\]/g, parseFloat(Ext.getCmp('FFReward').getValue()));
                            qstr = qstr.replace(/\[C\]/g, parseFloat(Ext.getCmp('FFContractPrice').getValue()));
                            Ext.getCmp('FFNetPrice').setValue((eval(qstr)).toFixed(2))
                        }
                     }
                  }
                },*/{
                  xtype: 'textfield',
                  id: 'FFNetPrice',
                  name: 'FFNetPrice',
                  listeners: {
                     change: function (cb, nv, ov) {
                        if (Ext.getCmp('FFVolumeNetto2').getValue()=='') Ext.getCmp('FFVolumeNetto2').setValue(0);
                        Ext.getCmp('FFTotalPayment').setValue((parseFloat(Ext.getCmp('FFNetPrice').getValue())*
                           parseFloat(Ext.getCmp('FFVolumeNetto2').getValue())).toFixed(2))
                     }
                  }
                },{
                  xtype: 'textfield',
                  id: 'FFVolumeNetto2',
                  name: 'FFVolumeNetto2',
                  readOnly:true,
                  listeners: {
                     change: function (cb, nv, ov) {
                        if(parseFloat(Ext.getCmp('FFNetPrice').getValue())>0) {
                           Ext.getCmp('FFTotalPayment').setValue((parseFloat(Ext.getCmp('FFNetPrice').getValue())*
                              parseFloat(Ext.getCmp('FFVolumeNetto2').getValue())).toFixed(2))
                           Ext.getCmp('AllTotalPayment').setValue((parseFloat(Ext.getCmp('FFTotalPayment').getValue())).toFixed(2))
                           if(parseFloat(Ext.getCmp('FFTotalPayment').getValue())>0)
                           Ext.getCmp('AllTotalPayment').setValue((parseFloat(Ext.getCmp('FFTotalPayment').getValue())+
                              parseFloat(Ext.getCmp('FAQTotalPayment').getValue())).toFixed(2))
                        }
                     }
                  }
                },{
                  xtype: 'textfield',
                  id: 'FFTotalPayment',
                  name: 'FFTotalPayment',
                  readOnly:true,
                  listeners: {
                     change: function (cb, nv, ov) {
                        Ext.getCmp('AllTotalPayment').setValue((isNaN(parseFloat(Ext.getCmp('FFTotalPayment').getValue()))?0:parseFloat(Ext.getCmp('FFTotalPayment').getValue())).toFixed(2))
                        if (parseFloat(Ext.getCmp('FAQTotalPayment').getValue())>0)
                        Ext.getCmp('AllTotalPayment').setValue((isNaN(parseFloat(Ext.getCmp('FFTotalPayment').getValue()))?0:parseFloat(Ext.getCmp('FFTotalPayment').getValue())+
                           isNaN(parseFloat(Ext.getCmp('FAQTotalPayment').getValue()))?0:parseFloat(Ext.getCmp('FAQTotalPayment').getValue())).toFixed(2))
                     }
                  }
                },{
                  xtype: 'textfield',
                  id: 'FFBeratBersihSetara',
                  name: 'FFBeratBersihSetara',
                  listeners: {
                     change: function (cb, nv, ov) {
                        Ext.getCmp('FFVerifikasi').setValue(this.value*parseFloat(Ext.getCmp('FFContractPrice').getValue()));
                     }
                  }
                },{
                  xtype: 'textfield',
                  id: 'FFVerifikasi',
                  readOnly:true,
                  name: 'FFVerifikasi'
                }]
               },{
                columnWidth: 0.1,
                layout: 'form',
                id:'bawah_faq',
                padding:5,
                items:[{
                  xtype: 'label',cls:'x-form-item-label',
                  text: lang(s_labelfaq)
                },{
                  xtype: 'textfield',
                  id: 'FAQContractPrice',
                  name: 'FAQContractPrice',
                  readOnly:true,
                  listeners: {
                     change: function (cb, nv, ov) {
                        //if (Ext.getCmp('FAQReward').getValue()=='') Ext.getCmp('FAQReward').setValue(0);
                     }
                  }
                },/*{
                  xtype: 'textfield',
                  id: 'FAQRewardBruto',
                  name: 'FAQRewardBruto',
                  readOnly:true
                },{
                  xtype: 'textfield',
                  id: 'FAQRewardBonus',
                  name: 'FAQRewardBonus',
                  listeners: {
                     change: function (cb, nv, ov) {
                        Ext.getCmp('FAQReward').setValue((parseFloat(Ext.getCmp('FAQRewardBruto').getValue())+parseFloat(Ext.getCmp('FAQRewardBonus').getValue())).toFixed(2))
                     }
                  }
                },{
                  xtype: 'textfield',
                  id: 'FAQReward',
                  name: 'FAQReward',
                  listeners: {
                     change: function (cb, nv, ov) {
                        if(s_formulaakhir!='' && s_formulaakhir!=null){
                            var qstr = s_formulaakhir.replace(/\[R\]/g, parseFloat(Ext.getCmp('FAQReward').getValue()));
                            qstr = qstr.replace(/\[N\]/g, parseFloat(Ext.getCmp('FAQVolumeNettoTrans').getValue()));
                            Ext.getCmp('FAQVolumeNetto2').setValue((eval(qstr)).toFixed(2));
                        }
                        if(s_formulaprice!='' && s_formulaprice!=null){
                            var qstr = s_formulaprice.replace(/\[R\]/g, parseFloat(Ext.getCmp('FAQReward').getValue()));
                            qstr = qstr.replace(/\[C\]/g, parseFloat(Ext.getCmp('FAQContractPrice').getValue()));
                            Ext.getCmp('FAQNetPrice').setValue((eval(qstr)).toFixed(2));
                        }
                     }
                  }
                },*/{
                  xtype: 'textfield',
                  id: 'FAQNetPrice',
                  name: 'FAQNetPrice',
                  listeners: {
                     change: function (cb, nv, ov) {
                        if (Ext.getCmp('FAQVolumeNetto2').getValue()=='') Ext.getCmp('FAQVolumeNetto2').setValue(0);
                        Ext.getCmp('FAQTotalPayment').setValue((parseFloat(Ext.getCmp('FAQNetPrice').getValue())*
                           parseFloat(Ext.getCmp('FAQVolumeNetto2').getValue())).toFixed(2))
                     }
                  }
                },{
                  xtype: 'textfield',
                  id: 'FAQVolumeNetto2',
                  name: 'FAQVolumeNetto2',
                  readOnly:true,
                  listeners: {
                     change: function (cb, nv, ov) {
                        if (parseFloat(Ext.getCmp('FAQNetPrice').getValue())>0) {
                           Ext.getCmp('FAQTotalPayment').setValue((parseFloat(Ext.getCmp('FAQNetPrice').getValue())*
                              parseFloat(Ext.getCmp('FAQVolumeNetto2').getValue())).toFixed(2))
                           Ext.getCmp('AllTotalPayment').setValue((parseFloat(Ext.getCmp('FAQTotalPayment').getValue())).toFixed(2))
                           if (parseFloat(Ext.getCmp('FFTotalPayment').getValue())>0)
                           Ext.getCmp('AllTotalPayment').setValue((parseFloat(Ext.getCmp('FFTotalPayment').getValue())+
                              parseFloat(Ext.getCmp('FAQTotalPayment').getValue())).toFixed(2))
                        }
                     }
                  }
                },{
                  xtype: 'textfield',
                  id: 'FAQTotalPayment',
                  name: 'FAQTotalPayment',
                  readOnly:true,
                  listeners: {
                     change: function (cb, nv, ov) {
                        Ext.getCmp('AllTotalPayment').setValue((isNaN(parseFloat(Ext.getCmp('FAQTotalPayment').getValue()))?0:parseFloat(Ext.getCmp('FAQTotalPayment').getValue())).toFixed(2))
                        if (parseFloat(Ext.getCmp('FFTotalPayment').getValue())>0)
                        Ext.getCmp('AllTotalPayment').setValue((isNaN(parseFloat(Ext.getCmp('FFTotalPayment').getValue()))?0:parseFloat(Ext.getCmp('FFTotalPayment').getValue())+
                           isNaN(parseFloat(Ext.getCmp('FAQTotalPayment').getValue()))?0:parseFloat(Ext.getCmp('FAQTotalPayment').getValue())).toFixed(2))
                     }
                  }
                },{
                  xtype: 'textfield',
                  id: 'FAQBeratBersihSetara',
                  name: 'FAQBeratBersihSetara',
                  listeners: {
                     change: function (cb, nv, ov) {
                        Ext.getCmp('FAQVerifikasi').setValue(this.value*parseFloat(Ext.getCmp('FAQContractPrice').getValue()));
                     }
                  }
                },{
                  xtype: 'textfield',
                  id: 'FAQVerifikasi',
                  readOnly:true,
                  name: 'FAQVerifikasi'
                },{
                  xtype: 'textfield',
                  id: 'AllTotalPayment',
                  name: 'AllTotalPayment'
                },{
                  xtype: 'textfield',
                  id: 'DpTotalPayment',
                  name: 'DpTotalPayment',
                  listeners: {
                     change: function (cb, nv, ov) {
                        Ext.getCmp('MinTotalPayment').setValue((parseFloat(Ext.getCmp('AllTotalPayment').getValue())-parseFloat(Ext.getCmp('DpTotalPayment').getValue())).toFixed(2))
                     }
                  }
                },{
                  xtype: 'textfield',
                  id: 'MinTotalPayment',
                  name: 'MinTotalPayment'
                }]
               }]
           }]
         }],
        buttons: [{
            id:'cetakBatchButtonTrans',
            text: lang('Cetak Batch'),
               margin: '5px',
               scale: 'large',
            ui: 's-button',
            cls: 's-blue',
            handler: function() {
               preview_cetak_surat(m_cetak_kuitansi_batch+Ext.getCmp('BatchID').getValue()+'/'+Ext.getCmp('id').getValue()+'/'+
                  Ext.getCmp('SupplyBatchID').getValue()+'/'+Ext.getCmp('fSupplyTransID').getValue());
            }
        },{
            id:'cetakButtonTrans2',
            hidden: true,
            text: lang('Cetak'),
               margin: '5px',
               scale: 'large',
            ui: 's-button',
            cls: 's-blue',
            handler: function() {
               preview_cetak_surat(m_cetak_kuitansi+Ext.getCmp('fSupplyTransID').getValue());
            }
        },{
            id:'saveButtonTrans',
            text: lang('Save'),
               margin: '5px',
               scale: 'large',
            ui: 's-button',
            cls: 's-blue',
            handler: function() {
                if((Ext.getCmp('fcol1').getValue()=='1') && (Ext.getCmp('fcol').getValue()==null || Ext.getCmp('fcol').getValue()=="" || Ext.getCmp('FarmerID').getValue()=="" || Ext.getCmp('FarmerID').getValue()==null)){
                    var proses = 0;
                    Ext.MessageBox.alert('Warning', lang('Petani tidak boleh kosong!'));
                    return;
                }else{
                    if(Ext.getCmp('BatchID1').getValue()=='1' && (Ext.getCmp('BatchID').getValue()=='' || Ext.getCmp('BatchID').getValue()==null)){
                        var proses = 0;
                        Ext.MessageBox.alert('Warning', lang('Batch Number')+' tidak boleh kosong!');
                    }else{
                        fsave(function(){
                            //**//
                            c_batch.load();
                            store.load();
                            //**//
                        });
                    }
                }
            }
        },{
            text: lang('Close'),
               margin: '5px',
               scale: 'large',
            ui: 's-button',
            cls: 's-grey',
            disabled: false,
            handler: function() {
                winFarmer.hide();
            }
        }]
    });
    function displayFormFarmer(type,fromcoop,batchid,callback){

      if(!winFarmer.isVisible()){
         DataFormFarmer.getForm().reset();
         winFarmer.show();
      } else {
         winFarmer.hide(this, function() {});
         winFarmer.toFront();
      }
      cvillage.load();
      Ext.getCmp('SupplyType').setValue(type);
      set_hide(['toolbar_farmer','toolbar_farmer2','toolbar_farmer3','BatchFromName',
         'fcol','FarmerName','GroupName','District',
         'fncol','NonIdentity','NonBirthdate','NonVillageID',   'bcol',
         'setKarungType','ff','faq','setNetto',
         'setMoistureStd','setMoistureRst','setLabelMoisture','FFMoistureTrans','FAQMoistureTrans',
         /*'RewardBruto','RewardBonus','RewardNetto',*/'BeratBersihSetara','Verifikasi','labeldp','labeltotal',
            'DpTotalPayment','MinTotalPayment',
         'bawah_ff','bawah_faq','FakturNumber',
         /*'FFRewardBruto','FFRewardBonus','FFReward',*/'FFBeratBersihSetara','FFVerifikasi',
         /*'FAQRewardBruto','FAQRewardBonus','FAQReward',*/'FAQBeratBersihSetara','FAQVerifikasi',
         'saveButtonTrans','cetakBatchButtonTrans','cetakButtonTrans'])
      var width_karung = 83;
      var width_trans = 0.6;
      if(s_isff=='1' && s_isfaq=='1') {
         set_show(['setKarungType','ff','faq','bawah_ff','bawah_faq']);
         width_karung -= 10;
      } else {
         width_trans += 0.1;
         if (s_isff=='1') {
            //Ext.getCmp('type').setValue('FF')
            set_show(['ff','bawah_ff']);
         } else if (s_isfaq=='1') {
            //Ext.getCmp('type').setValue('FAQ')
            set_show(['faq','bawah_faq']);
         }
      }
      if(s_moisturekarung=='1') {
         set_show(['setMoistureRst']);
         width_karung -= 10;
      }
      if(s_calculatemoisturekarung=='1') {
         set_show(['setNetto','setMoistureStd','setLabelMoisture','FFMoistureTrans','FAQMoistureTrans']);
         width_karung -= 20;
      }
      if(s_isfaktur=='1') set_show(['FakturNumber']);
      Ext.getCmp('fcol1').setValue('0');
      Ext.getCmp('BatchID1').setValue('0');
      if (type=='Batch') {
         //**//set_show(['SupplyDestOrgID','BatchFromName','bcol','cetakBatchButtonTrans']);
         set_show(['BatchFromName','bcol','saveButtonTrans']);
         Ext.getCmp('SupplyDestOrgID').hide();
         if(Ext.getCmp('SupplyBatchID1').getValue()!=''){
            set_show(['SupplyDestOrgID']);
         }
         Ext.getCmp('BatchID1').setValue('1');
         //alert(fromcoop);
         if(fromcoop!='' && fromcoop!='undefined' && fromcoop!=undefined){
            set_hide(['setMoistureRst','FakturNumber']);
            c_batch.load({
                callback: function() {
                    Ext.getCmp('BatchID').setValue(fromcoop);
                }
            });
         }else{
            c_batch.load();
         }
         if(batchid!='' && batchid!='undefined' && batchid!=undefined){
            Ext.getCmp('SupplyBatchID').setValue(batchid);
            Ext.getCmp('fSupplyBatchID').setValue(batchid);
         }

      } else if (type=='NonFarmer') {
         set_show(['fncol','NonIdentity','NonBirthdate','NonVillageID','cetakButtonTrans']);
         set_hide(['tombol_generate','ColDoublePack']);
      } else {
            if(type=='Farmer'){
                ds.getProxy().setExtraParam("noncert", '');
                Ext.getCmp('noncert').setValue('');
            }else if(type=='FarmerNonCert'){
                ds.getProxy().setExtraParam("noncert", '1');
                Ext.getCmp('noncert').setValue('1');
            }
         ds.load();
         set_show(['fcol','FarmerName','GroupName','District','cetakButtonTrans']);
         Ext.getCmp('fcol1').setValue('1');
         set_hide(['tombol_generate','ColDoublePack']);
      }
      Ext.getCmp('MoistureStd').setValue(s_standardmoisturekarung);
      cpackage.load({params: {id: Ext.getCmp('SupplychainID').getValue()}});
      store_farmer.load();
      Ext.getCmp('fSupplyBatchID').setValue(Ext.getCmp('SupplyBatchID').getValue());
      Ext.getCmp('fDateTransaction').setValue(m_now);
      Ext.getCmp('setPackageType').setWidth(width_karung/10*Ext.getCmp('setWeight').getWidth());
      Ext.getCmp('trans_col').columnWidth=width_trans;
      Ext.getCmp('trans_col').doLayout();
      Ext.getCmp('width_kiri').columnWidth=width_trans;
      Ext.getCmp('width_kiri').doLayout();
      if (Ext.getCmp('SupplyDestStatus').getValue()=='Open'){
        set_show(['saveButtonTrans']);
        Ext.getCmp('SupplyDestOrgID').hide();
      } 
      if (Ext.getCmp('SupplyBatchID_old').getValue()!='') {
         Ext.getCmp('unitfrom').hide()
         Ext.getCmp('nama').show()
      }
        updateQuality(callback);

    }
    var winFarmer = Ext.create('widget.window', {
        title: 'Transaction',
        id:'winTraining',
        closable: false,
        modal:true,
        closeAction: 'show',
        width: '80%',
        height: '80%',
        layout: {
            type: 'fit'
        },
        items: [DataFormFarmer]
    });
   //end add trans
   //update batch
    function tset(sma,callback) {
      Ext.Ajax.request({
         url: m_crud,
         method: 'GET',
         params: {id: sma},
         success: function(fp, o){
            var r = Ext.decode(fp.responseText);
            Ext.getCmp('SupplyBatchID').setValue(r.SupplyBatchID);
            Ext.getCmp('SupplychainID').setValue(r.SupplyOrgID);
            set_setting(r.SupplyDestStatus,function(){
                Ext.getCmp('SupplyBatchNumber').setValue(r.SupplyBatchNumber);
                Ext.getCmp('nama').setValue(r.id+' - '+r.Name);
                Ext.getCmp('SupplyDestOrgID').setValue(r.SupplyDestOrgID);
                Ext.getCmp('SupplyDestStatus').setValue(r.SupplyDestStatus);
                if(r.SupplyDestStatus!='Close Batch'){
                    Ext.getCmp('add_sopir').hide();
                }else{
                    Ext.getCmp('add_sopir').show();
                }
                Ext.getCmp('SupplyBatchDate').setValue(r.SupplyBatchDate);
                Ext.getCmp('perwakilan').setValue(r.PerwakilanOrgID);
                Ext.getCmp('pengiriman').setValue(r.DeliveryDate);
                Ext.getCmp('SupplyBatchResponsible').setValue(r.SupplyBatchResponsible);
                Ext.getCmp('VolumeBruto').setValue(r.VolumeBruto);
                Ext.getCmp('Package').setValue(r.VolumeBruto-r.VolumeNetto);
                Ext.getCmp('VolumeNetto').setValue(r.VolumeNetto);
                Ext.getCmp('DestWeight').setValue(r.DestWeight>0?r.DestWeight:r.VolumeNetto);
                Ext.getCmp('DestPO').setValue(r.DestPO);
                Ext.getCmp('DestICS').setValue(r.DestICS);
                //**//
                Ext.getCmp('iDriver').setValue(0); 
                var driver = Ext.getCmp('driver-pnl');
                driver.removeAll();
                if(r.DestDriver==null) r.DestDriver='';
                if(r.DestDriverJabatan==null) r.DestDriverJabatan='';
                if(r.DestDriverAddress==null) r.DestDriverAddress='';
                if(r.DestNoPolisi==null) r.DestNoPolisi='';
                if(r.DestTransport==null) r.DestTransport='';

                var rDestDriver = r.DestDriver.split('|');
                var rDestDriverJabatan = r.DestDriverJabatan.split('|');
                var rDestDriverAddress = r.DestDriverAddress.split('|');
                var rDestNoPolisi = r.DestNoPolisi.split('|');
                var rDestTransport = r.DestTransport.split('|');
                for (dr = 0; dr < rDestDriver.length; dr++) { 
                    if(dr==0){
                        Ext.getCmp('DestDriver').setValue(rDestDriver[dr]);
                        Ext.getCmp('DestDriverJabatan').setValue(rDestDriverJabatan[dr]);
                        Ext.getCmp('DestDriverAddress').setValue(rDestDriverAddress[dr]);
                        Ext.getCmp('DestNoPolisi').setValue(rDestNoPolisi[dr]);
                        Ext.getCmp('DestTransport').setValue(rDestTransport[dr]);
                    }else{
                        addDriver(rDestDriver[dr],rDestDriverJabatan[dr],rDestDriverAddress[dr],rDestNoPolisi[dr],rDestTransport[dr]);
                    } 
                }
                //**//
                Ext.getCmp('DestJumlahKarung').setValue(r.DestJumlahKarung);
                if(r.SupplyDestOrgID!="" && r.SupplyDestOrgID!=null){
                    Ext.getCmp('SupplyDestOrgID').setReadOnly(true);
                }else{
                    Ext.getCmp('SupplyDestOrgID').setReadOnly(false);
                }
                //**//
                store_detail.load({params: {id: sma}});
                sdest.load({params: {query: r.SupplyOrgID}});
                if (r.SupplyDestStatus=='Closed' || r.SupplyDestStatus=='Close Batch' || r.SupplyDestStatus=='Other' || r.SupplyDestStatus=='Sent' || r.SupplyDestStatus=='Delivered') {
                   set_show(['cetakPackingList','btnUploadFile','SupplyDestOrgID','DestPO','DestWeight','pengiriman','toolbar_detail_update_detail']);
                   set_hide(['toolbar_detail_batch','toolbar_detail_farmer','toolbar_add_batch','toolbar_detail_farmer_cert','toolbar_detail_non_farmer',
                      'toolbar_detail_update','toolbar_detail_remove','closeBatchButton','saveButton']);
                   if (r.SupplyDestStatus=='Close Batch'){
                       Ext.getCmp('saveButton').setText(lang('Sent'));
                       set_show(['saveButton']);
                   }

                   if(s_generateweightorpackage=='Weight'){
                    set_show(['DestWeightPerKarung']);
                   }else if(s_generateweightorpackage=='Package'){
                    set_show(['DestJumlahKarung']);
                }else if(s_generateweightorpackage=='Transaction'){

                    // @author ardiantoro@koltiva.com
                    // jika generate packing list yang ada di setting buyingunit adalah transaksi, maka
                    // muncul tombol untuk melakukan ajax request generate packing list berdasarkan karung transaksi yang dilakukan
                    // sekarang yang pake ini cuma mamuju


                    Ext.getCmp('DestWeightPerKarung').hide();
                    Ext.getCmp('DestJumlahKarung').hide();
                    Ext.getCmp('DestWeightPerKarung').setValue('');
                    Ext.getCmp('DestJumlahKarung').setValue('');

                     }else{
                    if(Ext.getCmp('SupplyDestStatus').getValue()=='Close Batch'){
                        set_show(['ColDoublePack']);
                    }
                   }
                    if(Ext.getCmp('AddBatchBUFromCoop').getValue()!=''){
                        set_hide(['SupplyDestOrgID']);
                    }
                   if(s_isgeneratepacking=='1' && Ext.getCmp('SupplyDestStatus').getValue()=='Close Batch') set_show(['tombol_generate'])
                }
                if (typeof callback === "function") {
                    callback();
                }
            });
         }
      });
    }
   //end update batch
   //add batch
    var s_nonfarmer,s_farmer,s_farmer_cert,s_batch,s_pemisahan_batch,s_perwakilan_koperasi,s_org_type,s_label,s_isff,
      s_isfaq,s_moisturekarung,s_calculatemoisturekarung,s_formulakarung,s_formulaprice,s_formulaakhir,s_labelfarmercert,s_labelfaq,s_labelff,
      s_labelfarmer,s_labelnonfarmer,s_isfaktur,s_isgeneratepacking,s_isdriver,s_isdriverpostition,s_isdriveraddress,s_isresponsible,s_ispolicenumber,s_isvehicletype,s_issuratjalan,s_quota,
      s_generateweightorpackage,s_islockdestweigh,s_isgeneratepo,s_partner
    var sperwakilan = Ext.create('Ext.data.Store', {
        extend: 'Ext.data.Model',
        fields: ['id','label'],
        autoLoad: false,
        proxy: {
            type: 'ajax',
            url: m_perwakilan,
            reader: {
                type: 'json',
                root: 'data'
            }
        }
    });
    function set_setting(stat,callback) {
       hide_all();
       Ext.Ajax.request({
         url: m_crud+'_add_batch',
         params: {orgid: Ext.getCmp('SupplychainID').getValue()},
         method: 'GET',
         success: function(fp, o){
            var r = Ext.decode(fp.responseText);
            s_perwakilan_koperasi = r.setting['PerwakilanKoperasi'];
               s_isff = r.setting['IsFF'];
               s_isfaq = r.setting['IsFAQ'];
            s_farmer = r.setting['PembelianFarmer'];
            s_farmer_cert = r.setting['PembelianFarmerCert'];
            s_nonfarmer = r.setting['PembelianNonFarmer'];
            s_batch = r.setting['PembelianBatch'];
            s_generate_batch = r.setting['IsGenerateBuyingUnitBatch'];

            s_isfaktur = r.setting['IsFakturNumber'];
            s_label = r.setting['LabelKarung'];//di cetaknya
            s_labelfaq = r.setting['LabelFAQ'];
            s_labelff = r.setting['LabelFF'];
            s_moisturekarung = r.setting['IsMoistureKarung'];
            s_standardmoisturekarung = r.setting['StandardMoistureKarung'];
            s_isgeneratepacking = r.setting['IsGeneratePacking'];
            s_generateweightorpackage = r.setting['GenerateWeightOrPackage'];
            s_isdriver = r.setting['IsDriver'];
            s_isdriverpostition = r.setting['IsDriverPosition'];
            s_isdriveraddress = r.setting['IsDriverAddress'];
            s_isresponsible = r.setting['IsResponsible'];
            s_ispolicenumber = r.setting['IsPoliceNumber'];
            s_isvehicletype = r.setting['IsVehicleType'];
            s_pemisahan_batch = r.setting['PemisahanBatch'];
            s_issuratjalan = r.setting['IsSuratJalan'];//di cetaknya
            s_islockdestweigh = r.setting['IsLockDestWeigh'];
            s_isgeneratepo = r.setting['IsGeneratePo'];
            s_partner = r.setting['PartnerID'];
            Ext.getCmp('IsCetakSuratJalan').setValue(r.setting['IsSuratJalan']);
            s_org_type = r.setting['OrgType'];

               s_formulakarung = r.setting['FormulaNettoKarung'];
               s_formulaprice = r.setting['FormulaNettoPrice'];
               s_formulaakhir = r.setting['FormulaNettoAkhir'];
               s_quota = r.setting['IsQuota'];

            Ext.getCmp('toolbar_detail_farmer').setText(lang(r.setting['LabelFarmerCertified']))
            Ext.getCmp('toolbar_detail_farmer_cert').setText(lang(r.setting['LabelFarmerNonCertified']))
            Ext.getCmp('toolbar_detail_non_farmer').setText(lang(r.setting['LabelNonFarmer']))
            Ext.getCmp('PemisahanBatch1').setValue(r.setting['PemisahanBatch']);
            if(s_islockdestweigh=='1'){
                Ext.getCmp('DestWeight').setReadOnly(true);
            }else{
                Ext.getCmp('DestWeight').setReadOnly(true);
            }

            if(s_perwakilan_koperasi=='1') {
               set_show(['perwakilan']);
               sperwakilan.load({params: {id: r.setting['SupplychainID']}});
            }
            //show hide

            if(Ext.getCmp('SupplyBatchID').getValue()==''){
                Ext.getCmp('saveButton').setText(lang('Create Batch'));
                //if (s_quota=='0') Ext.getCmp('SupplyDestOrgID').show()
                set_show(['saveButton']);
            }
            else {
               if(stat=='Open') {
                  if (s_batch=='1') Ext.getCmp('toolbar_detail_batch').show()
                  if (s_farmer=='1') Ext.getCmp('toolbar_detail_farmer').show()
                  if (s_farmer_cert=='1') Ext.getCmp('toolbar_detail_farmer_cert').show()
                  if (s_nonfarmer=='1') Ext.getCmp('toolbar_detail_non_farmer').show()
                  if (s_generate_batch=='1') Ext.getCmp('toolbar_add_batch').show()
                  set_show(['closeBatchButton','toolbar_detail_update','toolbar_detail_remove']);
                  set_hide(['toolbar_detail_update_detail']);
               } else if (stat=='Close Batch') {
                  Ext.getCmp('saveButton').setText(lang('Save Batch'));
                  set_show(['saveButton']);
               } else {
                    set_show(['cetak_packing_list']);//'cetakButton',
                    if(Ext.getCmp('IsCetakSuratJalan').getValue()=='1'){
                        set_show(['cetakSuratJalan']);//'cetakButton',
                    }
                  //**//
                  //set_show(['cetakButton']);
                  //**//
               }
               if(stat!='Open') {
                  set_show(['cetakPackingList','btnUploadFile','SupplyDestOrgID','DestPO','pengiriman','DestWeight']);
                  sdest.load({params: {query: Ext.getCmp('SupplychainID').getValue()}});
                  if(s_isdriver=='1') set_show(['DestDriver','PanelDriver']);
                  if(s_isdriverpostition=='1') set_show(['DestDriverJabatan']);
                  if(s_isdriveraddress=='1') set_show(['DestDriverAddress']);
                  if(s_isresponsible=='1') set_show(['SupplyBatchResponsible']);
                  if(s_ispolicenumber=='1') set_show(['DestNoPolisi','DestTransport']);
                  if(s_isvehicletype=='1') set_show(['DestTransport']);
                  if(s_isgeneratepacking=='1' && Ext.getCmp('SupplyDestStatus').getValue()=='Close Batch') set_show(['tombol_generate']);
                  if(s_generateweightorpackage=='Weight') set_show(['DestWeightPerKarung']);
                  else if(s_generateweightorpackage=='Package') set_show(['DestJumlahKarung']);
                  else{
                    if(Ext.getCmp('SupplyDestStatus').getValue()=='Close Batch'){
                        set_show(['ColDoublePack']);
                    }

                  }
                  if (r.SupplyDestStatus=='Close Batch'){
                      Ext.getCmp('saveButton').setText(lang('Save Batch'));
                      set_show(['saveButton']);
                  }
                  if(Ext.getCmp('SupplyBatchID_old').getValue()!='') set_hide(['SupplyDestOrgID']);
               }
            }
            if(Ext.getCmp('SupplyBatchID_old').getValue()!=''){
                set_show(['unitfrom','unitto']);
                set_hide(['SupplyDestOrgID']);
            }
            if(Ext.getCmp('SupplyBatchNumber2').getValue()!='') set_hide(['unitfrom']);
            if (typeof callback === "function") {
                callback();
            }
         }
      })
   }
    function tsave(){
        var form = Ext.getCmp('dataForm').getForm();
        yourApp.showLoading();
        //**//
        Ext.Ajax.request({
            url: m_crud+'_packing_check',
            method: 'POST',
            params: {
               id      : Ext.getCmp('SupplyBatchID').getValue(),
               SupplychainID : Ext.getCmp('SupplychainID').getValue()
            },
            success: function(fp, o){
                var r = Ext.decode(fp.responseText);
                var proses = 0;
                if((r.jumlah==null&&r.IsGeneratePacking=='0')||Ext.getCmp('SupplyBatchID').getValue()==''){
                    var proses = 1;
                }else{
                    if(new Date(Ext.getCmp('SupplyBatchDate').getValue()) > new Date(Ext.getCmp('pengiriman').getValue())){
                        var proses = 0;
                        yourApp.hideLoading();
                        Ext.MessageBox.alert('Warning', lang('Tanggal Transaksi dan Tanggal Pengiriman tidak sesuai.'));
                        return;
                    }
                    if(r.jumlah!=Ext.getCmp('DestWeight').getValue()){
                        yourApp.hideLoading();
                        if(r.jumlah==null){
                            Ext.MessageBox.alert('Warning', lang('Packing List belum dibuat. Silahkan Buat Packing List terlebih dahulu.'));
                        }else{
                            //Ext.MessageBox.alert('Warning', lang('Jumlah berat tidak sesuai dengan berat di packing list.'));
                            var proses = 1;
                        }
                        Ext.getCmp('DestWeight').focus();
                    }else{
                        var proses = 1;
                    }
                }
                if((Ext.getCmp('nama').getValue()==null || Ext.getCmp('nama').getValue()=="") && (Ext.getCmp('unitfrom').getValue()==null || Ext.getCmp('unitfrom').getValue()=="")){
                    yourApp.hideLoading();
                    var proses = 0;
                    Ext.MessageBox.alert('Warning', lang('Unit tidak boleh kosong!'));
                    return;
                }

                if(proses==1){
                    var penjualan = Ext.getCmp('SupplyDestOrgID').getValue();
                    var stats = 0;
                    if(penjualan!='' && penjualan!=null){
                        if(Ext.getCmp('pengiriman').getValue()==null && Ext.getCmp('SupplyBatchID').getValue()!=''){
                            yourApp.hideLoading();
                            Ext.MessageBox.alert('Warning', lang('Tanggal Pengiriman tidak boleh kosong.'));
                        }else{
                            var stats = 1;
                        }
                    }else{
                        var stats = 1;
                    }
                    if(stats==1){//**//
                        var methode;
                        if (Ext.getCmp('SupplyBatchID').getValue()!='') methode = 'PUT'; else methode = 'POST';
                        form.submit({
                           url: m_crud+'_batch',
                           method: methode,
                           waitMsg: lang('Sending data...'),
                           success: function(fp, o) {
                              if (o.result.id>0) {
                                  Ext.getCmp('SupplyBatchID').setValue(o.result.id);
                                  Ext.getCmp('SupplyBatchNumber').setValue(o.result.batch_number);
                                  set_setting(Ext.getCmp('SupplyDestStatus').getValue(),function(){
                                        tset(o.result.id,function(){
                                            store.load();
                                            if(Ext.getCmp('SupplyDestStatus').getValue()!='Close Batch'){
                                                set_hide(['tombol_generate','ColDoublePack']);
                                            }
                                            yourApp.hideLoading();
                                        });
                                  })
                              } else {
                                  if (Ext.getCmp('SupplyBatchID_old').getValue()!='') {
                                     win.hide();
                                     var id = Ext.getCmp('SupplyBatchID').getValue()
                                     var bnum = Ext.getCmp('SupplyBatchNumber').getValue()
                                     var id_t = Ext.getCmp('SupplychainID').getValue()
                                     Ext.getCmp('BatchID').setValue(bnum);
                                     displayFormWindow();
                                     tset(id,function(){
                                         ebsearch();
                                        if(Ext.getCmp('SupplyDestStatus').getValue()!='Close Batch'){
                                            set_hide(['tombol_generate','ColDoublePack']);
                                        }
                                        if(Ext.getCmp('AddBatchBUFromCoop').getValue()!='' && Ext.getCmp('SupplyBatchID1').getValue()!=''){
                                            Ext.getCmp('win').setTitle(lang('Batch'));
                                            winFarmer.hide();
                                            win.hide();
                                            var id = Ext.getCmp('SupplyBatchID1').getValue();
                                            Ext.getCmp('SupplyBatchID').setValue(Ext.getCmp('SupplyBatchID1').getValue());
                                            Ext.getCmp('SupplychainID').setValue(Ext.getCmp('SupplychainID1').getValue());
                                            Ext.getCmp('SupplyBatchNumber').setValue(Ext.getCmp('SupplyBatchNumber1').getValue());
                                            displayFormWindow();
                                            tset(id,function(){
                                                displayFormFarmer('Batch',Ext.getCmp('SupplyBatchNumber2').getValue(),Ext.getCmp('SupplyBatchID1').getValue(),function(){
                                                    Ext.getCmp('BatchID').setValue(Ext.getCmp('SupplyBatchNumber2').getValue());
                                                    Ext.getCmp('SupplyBatchID').setValue(Ext.getCmp('SupplyBatchID1').getValue());
                                                    Ext.getCmp('fSupplyBatchID').setValue(Ext.getCmp('SupplyBatchID1').getValue());
                                                    Ext.getCmp('AddBatchBUFromCoop').setValue();
                                                    Ext.getCmp('SupplyBatchID1').setValue();
                                                    Ext.getCmp('SupplyBatchNumber1').setValue();
                                                    Ext.getCmp('SupplyBatchID2').setValue();
                                                    Ext.getCmp('SupplyBatchNumber2').setValue();
                                                    Ext.getCmp('SupplychainID1').setValue();
                                                    Ext.getCmp('SupplychainID2').setValue();
                                                    yourApp.hideLoading();
                                                });
                                            });
                                        }
                                        yourApp.hideLoading();
                                     });
                                     //**//displayFormBatch()
                                    }else{
                                        win.hide();
                                        var id = Ext.getCmp('SupplyBatchID').getValue()
                                        var bnum = Ext.getCmp('SupplyBatchNumber').getValue()
                                        var id_t = Ext.getCmp('SupplychainID').getValue()
                                        Ext.getCmp('BatchID').setValue(bnum);
                                        displayFormWindow();
                                        tset(id,function(){
                                            ebsearch();
                                            if(Ext.getCmp('SupplyDestStatus').getValue()!='Close Batch'){
                                                set_hide(['tombol_generate','ColDoublePack']);
                                            }
                                            cari();
                                            yourApp.hideLoading();
                                        });
                                    }
                                 //**//Ext.MessageBox.alert('Success', lang('Data saved.'));
                              }
                              //**//

                                Ext.MessageBox.alert('Success', lang('Data saved.'));
                                //var id = Ext.getCmp('SupplyBatchID').getValue();

                                if(Ext.getCmp('SupplyBatchNumber1').getValue()==""){
                                    Ext.getCmp('SupplyBatchNumber1').setValue(Ext.getCmp('SupplyBatchNumber').getValue());
                                    Ext.getCmp('SupplyBatchID1').setValue(Ext.getCmp('SupplyBatchID').getValue());
                                    Ext.getCmp('SupplychainID1').setValue(Ext.getCmp('SupplychainID').getValue());
                                }else{
                                    if(Ext.getCmp('SupplyBatchNumber').getValue()!=''){
                                        Ext.getCmp('SupplyBatchNumber2').setValue(Ext.getCmp('SupplyBatchNumber').getValue());
                                        Ext.getCmp('SupplyBatchID2').setValue(Ext.getCmp('SupplyBatchID').getValue());
                                        Ext.getCmp('SupplychainID2').setValue(Ext.getCmp('SupplychainID').getValue());
                                    }
                                }
                              //**//


                           }
                        });
                    }//**//
                }
            }
        });
        //**//
    }
    function displayFormWindow(){
        if(!win.isVisible()){
            DataForm.getForm().reset();
            win.show();
        } else {
            win.hide(this, function() {});
            win.toFront();
        }
        store_detail.load()
    }
    function check_batch(jenis,cert){
        Ext.Ajax.request({
            url: m_crud+'_check_farmer_batch',
            method: 'GET',
            params: {
                id: Ext.getCmp('BatchID').getValue(),
                parentID : Ext.getCmp('SupplyBatchNumber').getValue(),
                orgid: Ext.getCmp('SupplychainID').getValue(),
                isCert: cert
            },
            success: function(fp, o){
                var r = Ext.decode(fp.responseText);
                if(r.statusnya!='sama'){
                    Ext.MessageBox.alert('Warning',lang('Data Petani tidak boleh digabung.'));
                    return;
                }else{
                    displayFormFarmer(jenis);
                }
            }
        })
    }
    //Grid Detail
    var store_detail = Ext.create('Ext.data.Store', {
        extend: 'Ext.data.Model',
        storeId:'store_detail',
        fields: [{name:'SupplyTransID'},{name:'SupplyBatchID'},{name:'SupplyType'},{name:'SupplyTypeLabel'},{name:'Name'},{name:'DateTransaction'},
            {name:'VolumeBruto',type:'float'},{name:'VolumeNetto',type:'float'},{name:'NetPrice'},{name:'packing'}],
        autoLoad: false,
        pageSize: 50,
        proxy: {
            type: 'ajax',
            url: m_crud+'s_detail',
            reader: {
                type: 'json',
                root: 'data',
                totalProperty: 'total'
            }
        },
        listeners: {
         load: function(store, operation) {
            if (s_pemisahan_batch=='1' && Ext.getStore('store_detail').getCount()>0) {
               set_hide(['toolbar_detail_farmer','toolbar_detail_farmer_cert','toolbar_detail_non_farmer']);
               if (Ext.getCmp('SupplyDestStatus').getValue()=='Open') {
                  if (Ext.getStore('store_detail').getAt(0).get('SupplyType')=='Farmer' && s_farmer=='1') {
                     set_show(['toolbar_detail_farmer']);
                  } else if (Ext.getStore('store_detail').getAt(0).get('SupplyType')=='FarmerNonCert' && s_farmer_cert=='1') {
                     set_show(['toolbar_detail_farmer_cert']);
                  } else if (Ext.getStore('store_detail').getAt(0).get('SupplyType')=='NonFarmer' && s_nonfarmer=='1') {
                     set_show(['toolbar_detail_non_farmer']);
                  }
               }
                //**//
                if(Ext.getCmp('fSupplyTransID').getValue()==''){
                    Ext.getCmp('fSupplyTransID').setValue(Ext.getStore('store_detail').getAt(0).get('SupplyTransID'));
                    updateQuality();
                }
                //**//
            }
         }
        }
    });
    //Batch
   var sstatus = Ext.create('Ext.data.Store', {
       fields: ['id'],
       data : [
           {"id":"Open"},
           {"id":"Closed"},
           {"id":"Other"},
       ]
   });
    var sdest = Ext.create('Ext.data.Store', {
        extend: 'Ext.data.Model',
        fields: ['id','label'],
        autoLoad: false,
        proxy: {
            type: 'ajax',
            url: m_crud+'_dest',
            reader: {
                type: 'json',
                root: 'data'
            }
        }
    });
    var sperwakilan = Ext.create('Ext.data.Store', {
        extend: 'Ext.data.Model',
        fields: ['id','label'],
        autoLoad: false,
        proxy: {
            type: 'ajax',
            url: m_perwakilan,
            reader: {
                type: 'json',
                root: 'data'
            }
        }
    });
    var sunitfrom = Ext.create('Ext.data.Store', {
        extend: 'Ext.data.Model',
        fields: ['id','label'],
        autoLoad: false,
        proxy: {
            type: 'ajax',
            url: m_crud+'_unitfrom',
            reader: {
                type: 'json',
                root: 'data'
            }
        }
    });
   var DataForm = Ext.create('Ext.form.Panel', {
      frame: false,
      autoScroll: true,
      bodyPadding: 5,
      id:'dataForm',
      fieldDefaults: {
         labelAlign: 'left',
         labelWidth: 120,
         anchor: '100%'
      },
      items: [{
         layout: 'column',
         items: [{
            columnWidth: 0.5,
            layout: 'form',
            padding:5,
            items:[{
               xtype: 'textfield',
               id: 'SupplyBatchID',
               name: 'SupplyBatchID',
               hidden:true
            },{
               xtype: 'textfield',
               id: 'SupplyBatchID_old',
               name: 'SupplyBatchID_old',
               hidden:true
            },{
               xtype: 'textfield',
               id: 'SupplychainID',
               name: 'SupplychainID',
               hidden:true
            },{
               xtype: 'combo',
               fieldLabel: lang('Unit'),
               id: 'unitfrom',
               name: 'unitfrom',
               displayField: 'label',
               valueField: 'id',
               store : sunitfrom,
               listeners : {
                   change: function(dv, record, item, index, e) {
                        if (this.value!=''){
                            Ext.getCmp('saveButton').setText(lang('Create Batch'));
                            Ext.getCmp('saveButton').show();
                        }
                   }
               },
           },{
               xtype: 'textfield',
               fieldLabel: lang('Batch Number'),
               id: 'SupplyBatchNumber',
               name: 'SupplyBatchNumber',
               readOnly:true
            },{
               xtype: 'textfield',
               fieldLabel: lang('Unit'),
               id: 'nama',
               name: 'nama',
               readOnly:true
            },{
               xtype: 'datefield',
               fieldLabel: lang('Tanggal Transaksi'),
               id: 'SupplyBatchDate',
               name: 'SupplyBatchDate',
               format:'Y-m-d',
               value:m_now
            },{
               xtype: 'combo',
               store : sperwakilan,
               id: 'perwakilan',
               name: 'PerwakilanOrgID',
               queryMode: 'local',
               displayField: 'label',
               valueField: 'id',
               fieldLabel: lang('Perwakilan')
            },{
               xtype: 'datefield',
               fieldLabel: lang('Tanggal Pengiriman'),
               id: 'pengiriman',
               name: 'DeliveryDate',
               format:'Y-m-d'
            }/*,{
               xtype: 'textfield',
               fieldLabel: lang('Driver'),
               id: 'DestDriver',
               name: 'DestDriver'
            },{
               xtype: 'textfield',
               fieldLabel: lang('Jabatan'),
               id: 'DestDriverJabatan',
               name: 'DestDriverJabatan'
            },{
               xtype: 'textfield',
               fieldLabel: lang('Alamat'),
               id: 'DestDriverAddress',
               name: 'DestDriverAddress'
            }*/]
         },{
            columnWidth: 0.5,
            layout: 'form',
            padding:5,
            items:[{
               xtype: 'combo',
               store : sstatus,
               id: 'SupplyDestStatus',
               name: 'SupplyDestStatus',
               queryMode: 'local',
               displayField: 'id',
               valueField: 'id',
               fieldLabel: lang('Status')
            },{
               xtype: 'combo',
               hidden: true,
               fieldLabel: lang('Penjualan'),
               id: 'SupplyDestOrgID',
               name: 'SupplyDestOrgID',
               displayField: 'label',
               valueField: 'id',
               queryMode: 'local',
               store : sdest
            },{
               xtype: 'textfield',
               fieldLabel: lang('Penjualan'),
               id: 'unitto',
               name: 'unitto',
               readOnly:true,
            },{
               xtype: 'textfield',
               fieldLabel: lang('Nomor PO'),
               id: 'DestPO',
               name: 'DestPO'
            },
            //**//
            {
               layout: 'column',
               id:'ColDoublePack',
               items: [{
                   columnWidth: 0.7,
                   margin : '0 5 0 0',
                   width:'70%',
                   items:[{
                        xtype: 'combo',
                        fieldLabel: lang('Packing'),
                        store: store_doublePack,
                        id:'jenisPack',
                        name:'jenisPack',
                        queryMode: 'local',
                        displayField: 'label',
                        valueField: 'label',
                        listeners: {
                            change: function (cb, nv, ov) {
                                var jenisPack = Ext.getCmp('jenisPack').getValue();


                                if(jenisPack=='Berat'){
                                    set_show(['DestWeightPerKarung']);
                                    Ext.getCmp('DestJumlahKarung').setValue('')
                                    set_hide(['DestJumlahKarung','Gelondongan']);
                                } else if(jenisPack == 'Transaksi' || jenisPack == 'Transaction') {
                                    Ext.getCmp('DestWeightPerKarung').hide();
                                    Ext.getCmp('DestJumlahKarung').hide();
                                } else{
                                    set_hide(['DestWeightPerKarung']);
                                    Ext.getCmp('DestWeightPerKarung').setValue('')
                                    set_show(['DestJumlahKarung','Gelondongan']);
                                }

                            }
                        }
                   }]
                },{
                    xtype: 'checkboxfield',
                    id: 'Gelondongan',
                    name: 'Gelondongan',
                    fieldLabel: '',
                    hideLabel: true,
                    margin: ' 0 0 0 10',
                    boxLabel: 'Detail',
                    listeners: {
                        click: function(dv, record, item, index, e){
                            alert(Ext.getCmp('Gelondongan').getChecked());
                        }
                    }
                }]
            },
            //**//
            {
               layout: 'column',
               id:'ColDestWeight',
               items: [{
                    columnWidth: 0.8,
                    layout:'hbox',
                    items:[{
                        xtype: 'textfield',
                        fieldLabel: lang('Destination Weight'),
                        id: 'DestWeight',
                        width:200,
                        name: 'DestWeight',
                    },{
                        xtype: 'textfield',
                        //fieldLabel: lang('WeightPerKarung'),
                        margin: 2,
                        width:50,
                        id: 'DestWeightPerKarung',
                        name: 'DestWeightPerKarung',
                        emptyText:'Berat per Karung',
                        value:62.5
                   },{
                        xtype: 'textfield',
                        margin: 2,
                        width:75,
                        id: 'DestJumlahKarung',
                        name: 'DestJumlahKarung',
                        emptyText:'Jumlah Karung'
                   }]
                },{
                    xtype :'button',
                    id:'tombol_generate',
                    //icon: varjs.config.base_url+'images/icons/silk/search.png',
                    text: lang('Buat Packing Lists'),
                    handler: function() {
                         if(s_generateweightorpackage == 'Both') { packingtype = Ext.getCmp('jenisPack').getValue(); } else { packingtype = s_generateweightorpackage; }
                         if(s_generateweightorpackage=='Weight') Ext.getCmp('DestJumlahKarung').setValue('')
                         else if(s_generateweightorpackage=='Package') Ext.getCmp('DestWeightPerKarung').setValue('')
                         Ext.Ajax.request({
                                 waitMsg: lang('Please wait...'),
                                 url: m_crud+'_packing',
                                 method : 'POST',
                                 params: {
                                        type                    : packingtype,
                                        BatchID      : Ext.getCmp('SupplyBatchID').getValue(),
                                        DestWeight   : Ext.getCmp('DestWeight').getValue(),
                                        DestWeightPerKarung :Ext.getCmp('DestWeightPerKarung').getValue(),
                                        DestJumlahKarung   : Ext.getCmp('DestJumlahKarung').getValue(),
                                        destid: Ext.getCmp('SupplyDestOrgID').getValue(),
                                        unitto: Ext.getCmp('unitto').getValue(),
                                        Gelondongan: Ext.getCmp('Gelondongan').getValue(),
                                 },
                                 success: function(response, opts){
                                         var obj = Ext.decode(response.responseText);
                                         switch(obj.success){
                                                 case true:
                                                        Ext.MessageBox.alert('Success',obj.message);
                                                        break;
                                                 default:
                                                        Ext.MessageBox.alert('Warning',obj.message);
                                                 break;
                                         }
                                 },
                                 failure: function(response, opts){
                                         var obj = Ext.decode(response.responseText);
                                         Ext.MessageBox.alert('error',lang('Could not connect to the database. Retry later'));
                                 }
                         });
                    }
             },{
                   columnWidth: 0.25,
                   width:'90%',
                             layout:'hbox',
                   items:[]
                },{
                   columnWidth: 0.25,
                   width:'90%',
                   items:[]
                }]
            },{
               xtype: 'textfield',
               fieldLabel: lang('ICS'),
               id: 'DestICS',
               name: 'DestICS'
            },{
               xtype: 'textfield',
               fieldLabel: lang('Penanggung Jawab Unit Pembeli'),
               id: 'SupplyBatchResponsible',
               name: 'SupplyBatchResponsible'
            }/*,{
               xtype: 'textfield',
               fieldLabel: lang('Kendaraan'),
               id: 'DestTransport',
               name: 'DestTransport'
            },{
               xtype: 'textfield',
               fieldLabel: lang('No. Kendaraan'),
               id: 'DestNoPolisi',
               name: 'DestNoPolisi'
            }*/]
         }]
        }, {
            xtype: 'fieldset',
            title: lang('Data Sopir'),
            id : 'PanelDriver',
            items: [{
                layout: 'column',
                items: [{
                    columnWidth: 0.5,
                    layout: 'form',
                    padding:5,
                    items:[{
                        xtype: 'textfield',
                        fieldLabel: lang('Driver'),
                        forfield: 'DestDriver',
                        id: 'DestDriver',
                        name: 'DestDriver[]'
                    }, {
                        xtype: 'textfield',
                        fieldLabel: lang('Jabatan'),
                        id: 'DestDriverJabatan',
                        name: 'DestDriverJabatan[]'
                    }, {
                        xtype: 'textfield',
                        fieldLabel: lang('Alamat'),
                        id: 'DestDriverAddress',
                        name: 'DestDriverAddress[]'
                    }]
                }, {
                    columnWidth: 0.5,
                    layout: 'form',
                    padding:5,
                    items:[{
                        xtype: 'textfield',
                        fieldLabel: lang('No. Kendaraan'),
                        id: 'DestNoPolisi',
                        name: 'DestNoPolisi[]'
                    }, {
                        xtype: 'textfield',
                        fieldLabel: lang('Kendaraan'),
                        id: 'DestTransport',
                        name: 'DestTransport[]'
                    }, {
                        xtype :'button',
                        id:'add_sopir',
                        icon: varjs.config.base_url + 'images/icons/new/add.png', cls:'Sfr_BtnGridGreen', overCls:'Sfr_BtnGridGreen-Hover',
                        text: lang('Tambah Sopir'),
                        handler: function() {
                            addDriver();
                        }
                    }, {
                        xtype: 'textfield',
                        fieldLabel: lang('i Kendaraan'),
                        id: 'iDriver',
                        name: 'iDriver',
                        value: 0,
                        hidden: true
                    }]
                }]
            }, {
                xtype:'container',
                id:'driver-pnl'
            }]
        }, {
         xtype: 'gridpanel',
         id:'grid_detail',
         features: [{
              ftype: 'summary'
         }],
         store: store_detail,
         width: '100%',
             minHeight: 100,
         loadMask: true,
         selType: 'rowmodel',
         listeners : {
            itemclick: function(view, record, item, index, e){
                contextMenuBatch.showAt(e.getXY());
            }
         },
         dockedItems: [{
            xtype: 'toolbar',
            items: [{
               icon: varjs.config.base_url + 'images/icons/new/add.png', cls:'Sfr_BtnGridGreen', overCls:'Sfr_BtnGridGreen-Hover',
               id:'toolbar_detail_farmer',
               cls : m_act_save,
               text: lang('Farmer Certification'),
               scope: this,
               handler : function(){
                    if(Ext.getCmp('PemisahanBatch1').getValue()=='2'){
                        check_batch('Farmer','Cert');
                    }else{
                        displayFormFarmer('Farmer');
                    }
               },
            },{
               icon: varjs.config.base_url + 'images/icons/new/add.png', cls:'Sfr_BtnGridGreen', overCls:'Sfr_BtnGridGreen-Hover',
               id:'toolbar_detail_farmer_cert',
               cls : m_act_save,
               text: lang('Farmer Non Certification'),
               scope: this,
               handler : function(){
                    if(Ext.getCmp('PemisahanBatch1').getValue()=='2'){
                        check_batch('FarmerNonCert','NonCert');
                    }else{
                        displayFormFarmer('FarmerNonCert');
                    }
                  //displayFormFarmer('FarmerNonCert')
               },
            },{
               icon: varjs.config.base_url + 'images/icons/new/add.png', cls:'Sfr_BtnGridGreen', overCls:'Sfr_BtnGridGreen-Hover',
               id:'toolbar_detail_non_farmer',
               cls : m_act_save,
               text: lang('Non Farmer'),
               scope: this,
               handler : function(){
                  displayFormFarmer('NonFarmer')
               }
            },{
               icon: varjs.config.base_url + 'images/icons/new/add.png', cls:'Sfr_BtnGridGreen', overCls:'Sfr_BtnGridGreen-Hover',
               text: lang('Batch'),
               scope: this,
               id:'toolbar_detail_batch',
               cls : m_act_save,
               handler : function(){
                  displayFormFarmer('Batch')
               }
            },{
               id:'toolbar_detail_update2',
               cls : m_act_update,
               icon: varjs.config.base_url+'images/icons/silk/pencil.png',
               text: lang('Update'),
               scope: this,
               hidden: true,
               handler : function(){
                  var sm = Ext.getCmp('grid_detail').getSelectionModel().getSelection()[0];
                  displayFormFarmer(sm.get('SupplyType'),'','',function(){
                    fset(sm);
                  });

               }
            },{
               id:'toolbar_detail_remove2',
               itemId: 'remove',
               hidden: true,
               icon: varjs.config.base_url+'images/icons/silk/delete.png',
               text: lang('Hapus'),
               scope: this,
               handler : function(){
                  hapus_trans()
               }
            }]
         }],
         columns: [{
            text: lang('No'),
            xtype: 'rownumberer',
            width:'3%'
         },{
            text: lang('Type'),
            dataIndex: 'SupplyTypeLabel',
            width:'12%'
         },{
            text: lang('Name/Nomor'),
            dataIndex: 'Name',
            width:'25%'
         },{
            text: lang('Tanggal'),
            dataIndex: 'DateTransaction',
            width:'10%',
            format:'Y-m-d'
         },{
            text: lang('Bruto'),
            dataIndex: 'VolumeBruto',
            width:'10%',
            summaryType: 'sum',
            summaryRenderer: function(value, summaryData, dataIndex) {
               var total = value;
               Ext.getCmp('VolumeBruto').setValue(total.toFixed(2));
            }
         },{
            text: lang('Netto'),
            dataIndex: 'VolumeNetto',
            width:'10%',
            summaryType: 'sum',
            summaryRenderer: function(value, summaryData, dataIndex) {
               var total = value;
               Ext.getCmp('VolumeBruto').setValue(total.toFixed(2));
               Ext.getCmp('VolumeNetto').setValue(total.toFixed(2));
               if (Ext.getCmp('DestWeight').getValue()=='')Ext.getCmp('DestWeight').setValue(value);
               Ext.getCmp('Package').setValue((parseFloat(Ext.getCmp('VolumeBruto').getValue())-
                  parseFloat(Ext.getCmp('VolumeNetto').getValue())).toFixed(2));
            }
         },{
            text: lang('Packing List'),
            dataIndex: 'packing',
            width:'29%'
         }]
      },{
         layout: 'column',
         items: [{
            columnWidth: 0.6,
            layout: 'form',
            padding:5,
            items:[{}]
         },{
            columnWidth: 0.4,
            layout: 'form',
            padding:5,
            items:[{
               xtype: 'textfield',
               fieldLabel: lang('Bruto'),
               id: 'VolumeBruto',
               name: 'VolumeBruto',
               readOnly:true
            },{
               xtype: 'textfield',
               fieldLabel: lang('Package/Moisture'),
               id: 'Package',
               name: 'Package',
               readOnly:true
            },{
               xtype: 'textfield',
               fieldLabel: lang('Netto'),
               id: 'VolumeNetto',
               name: 'VolumeNetto',
               readOnly:true
            }]
         }]
      }],
      buttons: [{
         id:'btnUploadFile',
         text: lang('Upload Files'),
         margin: '5px',
         scale: 'large',
         ui: 's-button',
         cls: 's-blue',
         handler: function() {
            store_upload_files.load({
                params: {
                    batchid: Ext.getCmp('SupplyBatchID').getValue()
                }
            });
            displayFormUploadFiles();
         }
        },{
         id:'cetakPackingList',
         text: lang('Detail Packing List'),
         margin: '5px',
         scale: 'large',
         ui: 's-button',
         cls: 's-blue',
         handler: function() {
            displayFormPackingList();
            store_packing_list.load({params: {id: Ext.getCmp('SupplyBatchID').getValue()}});
         }
        },{
            id:'cetakSuratJalan',
            text: lang('Cetak Surat Jalan'),
               margin: '5px',
               scale: 'large',
            ui: 's-button',
            cls: 's-blue',
            handler: function() {
               preview_cetak_surat(m_cetak_packing_list+Ext.getCmp('SupplyBatchID').getValue());
            }
        },{
         id:'cetakButton',
         text: lang('Cetak'),
         margin: '5px',
         scale: 'large',
         ui: 's-button',
         cls: 's-blue',
         handler: function() {
            preview_cetak_surat(m_cetak_kuitansi+Ext.getCmp('fSupplyTransID').getValue());
         }
      },{
         id:'closeBatchButton',
         text: lang('Close Batch'),
         margin: '5px',
         scale: 'large',
         ui: 's-button',
         cls: 's-blue',
         handler: function() {
            if (Ext.getCmp('VolumeBruto').getValue()<1) {
               Ext.MessageBox.alert('Warning','Silahkan tambahkah transaksi terlebih dahulu');
               return;
            }
            cbsave()
         }
      },{
         id:'saveButton',
         text: lang('Create Batch'),
         margin: '5px',
         scale: 'large',
         ui: 's-button',
         cls: 's-blue',
         handler: function() {
            if (Ext.getCmp('SupplyDestOrgID').getValue()!=null && parseFloat(Ext.getCmp('DestWeight').getValue())==0 && s_quota=='1') {
                Ext.MessageBox.alert('Warning','Silahkan isi Berat pengiriman');
                return;
            }else{
                tsave();
            }
         }
      },{
         text: lang('Cancel'),
         id:'close_transaction',
         margin: '5px',
         scale: 'large',
         ui: 's-button',
         cls: 's-grey',
         disabled: false,
         handler: function() {
            var addBacth = Ext.getCmp('AddBatchBUFromCoop').getValue();
            if(addBacth!=''){
                Ext.MessageBox.confirm('Message', lang('Transaksi belum selesai. Apakah anda yakin mau membatalkan?') , function(btn){
                    if(btn == 'yes'){
                        Ext.getCmp('win').setTitle(lang('Batch'));
                        yourApp.showLoading();
                        winFarmer.hide();
                        win.hide();
                        var id = Ext.getCmp('SupplyBatchID1').getValue();
                        var id_old = Ext.getCmp('SupplyBatchID_old').getValue();
                        //**//

                        if(addBacth!=''){
                            if(Ext.getCmp('SupplyBatchID1').getValue()!=""){
                                Ext.getCmp('SupplyBatchID').setValue(Ext.getCmp('SupplyBatchID1').getValue());
                                Ext.getCmp('SupplychainID').setValue(Ext.getCmp('SupplychainID1').getValue());
                                Ext.getCmp('SupplyBatchNumber').setValue(Ext.getCmp('SupplyBatchNumber1').getValue());
                                displayFormWindow();
                                tset(id,function(){
                                    displayFormFarmer('Batch',Ext.getCmp('SupplyBatchNumber2').getValue(),Ext.getCmp('SupplyBatchID1').getValue(),function(){
                                        Ext.getCmp('BatchID').setValue(Ext.getCmp('SupplyBatchNumber2').getValue());
                                        Ext.getCmp('SupplyBatchID').setValue(Ext.getCmp('SupplyBatchID1').getValue());
                                        Ext.getCmp('fSupplyBatchID').setValue(Ext.getCmp('SupplyBatchID1').getValue());
                                        Ext.getCmp('AddBatchBUFromCoop').setValue();
                                        Ext.getCmp('SupplyBatchID1').setValue();
                                        Ext.getCmp('SupplyBatchNumber1').setValue();
                                        Ext.getCmp('SupplyBatchID2').setValue();
                                        Ext.getCmp('SupplyBatchNumber2').setValue();
                                        Ext.getCmp('SupplychainID1').setValue();
                                        Ext.getCmp('SupplychainID2').setValue();
                                        yourApp.hideLoading();
                                    });
                                });
                            }else{
                                displayFormWindow();
                                tset(id_old,function(){
                                    displayFormFarmer(Ext.getCmp('SupplyType').getValue());
                                    Ext.getCmp('AddBatchBUFromCoop').setValue();
                                    Ext.getCmp('SupplyBatchID1').setValue();
                                    Ext.getCmp('SupplyBatchNumber1').setValue();
                                    Ext.getCmp('SupplyBatchID2').setValue();
                                    Ext.getCmp('SupplyBatchNumber2').setValue();
                                    Ext.getCmp('SupplychainID1').setValue();
                                    Ext.getCmp('SupplychainID2').setValue();
                                    yourApp.hideLoading();
                                });
                            }
                        }else{
                            DataForm.getForm().reset();
                            DataFormHidden.getForm().reset();
                            DataFormFarmer.getForm().reset();
                            DataFormPackingList.getForm().reset();
                            yourApp.hideLoading();
                        }
                    }
                });
            }else{
                winFarmer.hide();
                win.hide();
                DataForm.getForm().reset();
                DataFormHidden.getForm().reset();
                DataFormFarmer.getForm().reset();
                DataFormPackingList.getForm().reset();
            }
            //**//
         }
      }]
   });
   var win = Ext.create('widget.window', {
      title: 'Batch',
      id:'win',
      closable: false,
      modal:true,
      closeAction: 'show',
      width: '80%',
      height: '80%',
      layout: {
         type: 'fit'
      },
      items: [DataForm]
   });
   function addFormWindow() {
      if(!win.isVisible()){
         DataForm.getForm().reset();
         win.show();
      } else {
         win.hide(this, function() {});
         win.toFront();
      }
   }
   //end add batch
   //cari
   function cari() {
      store.load({
      params: {
         key: Ext.getCmp('key').getValue(),
         tgl: Ext.getCmp('tgl').getValue(),
         start:0
      }});
   }
   function submitOnEnterCari(field, event) {
        if (event.getKey() == event.ENTER) cari()
   }
   //end cari
   //grid-depan
   var store = Ext.create('Ext.data.Store', {
      fields: ['SupplyBatchID','SupplyBatchNumber','DestPO','jenis','Name','SupplyDestStatus','NameDest','SupplyBatchDate','VolumeBruto',
         'VolumeNetto','PembelianNonFarmer','PembelianFarmer','PembelianFarmerCert','PembelianBatch','MekanismeReward','no_edit', 'DestWeight', 'Tandan', 'Brondol'],
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
   var grid = Ext.create('Ext.grid.Panel', {
      store: store,
      width: '100%',
      id:'grid',
      style: 'border:1px solid #CCC;',
      renderTo: 'ext-content',
      loadMask: true,
      selType: 'rowmodel',
       listeners : {
          /*itemdblclick: function(dv, record, item, index, e) {
            var sm = record;
            if (sm.get('no_edit')!='2') {
               displayFormWindow();
               tset(sm.get('SupplyBatchID'))
            }
          }*/
            itemclick: function(view, record, item, index, e){
                contextMenu.showAt(e.getXY());
            }

       },
      dockedItems: [{
         xtype: 'pagingtoolbar',
         store: store,
         dock: 'bottom',
         displayInfo: true
      },{
         xtype: 'toolbar',
         items: [{
            icon: varjs.config.base_url + 'images/icons/new/add.png', cls:'Sfr_BtnGridGreen', overCls:'Sfr_BtnGridGreen-Hover',
            text: lang('Add Batch'),
            cls : m_act_add,
            scope: this,
            handler : function(){

                displayFormWindow();
                set_setting('Open',function(){
                    Ext.Ajax.request({
                        url: m_crud+'_number',
                        method: 'GET',
                        success: function(fp, o){
                            var r = Ext.decode(fp.responseText); console.log('r:'); console.log(r);
                            Ext.getCmp('SupplyBatchNumber').setValue(r.number);
                            Ext.getCmp('SupplychainID').setValue(r.id);
                            Ext.getCmp('nama').setValue(r.id+' - '+r.nama);
                            Ext.getCmp('SupplyDestStatus').setValue('Open');
                            Ext.getCmp('SupplyDestStatus').setReadOnly();
                            sdest.load({params: {query: r.id}});
                            if(s_quota=='0'){
                                Ext.getCmp('SupplyDestOrgID').setReadOnly(false);
                            }
                        }
                    });
                });
                var today = new Date();
                var mm = today.getMonth()+1;
                if (mm<10) mm = '0'+mm;
                Ext.getCmp('SupplyBatchDate').setValue(m_now);
                Ext.getCmp('saveButton').setText(lang('Create Batch'));
            }
         },/*{
            icon: varjs.config.base_url+'images/icons/silk/pencil.png',
            text: lang('Update'),
            cls : m_act_update,
            scope: this,
            handler : function(){
               var sm = Ext.getCmp('grid').getSelectionModel().getSelection()[0];
               if (sm.get('no_edit')!='2') {
                  displayFormWindow();
                  tset(sm.get('SupplyBatchID'))
               }
            },
         },{
            itemId: 'remove',
            icon: varjs.config.base_url+'images/icons/silk/delete.png',
            cls:m_act_delete,
            text: lang('Hapus'),
            scope: this,
            handler : function(){
                 var smb = Ext.getCmp('grid').getSelectionModel().getSelection()[0];
                 if (smb.raw.SupplyDestStatus=='Delivered' || smb.raw.SupplyDestStatus=='Sent' || smb.raw.SupplyDestStatus=='Closed' ||
                     smb.raw.SupplyDestStatus=='Other')
                     Ext.MessageBox.alert('Warning','Data tidak bisa dihapus');
                 else {
                  Ext.MessageBox.confirm('Message', lang('Apakah anda mau menghapus data batch ini ?') , function(btn){
                     if(btn == 'yes'){
                        Ext.Ajax.request({
                        waitMsg: lang('Please Wait'),
                        url: m_crud+'_batch',
                        method : 'DELETE',
                        params: {id:  smb.raw.SupplyBatchID},
                        success: function(response, opts){
                           var obj = Ext.decode(response.responseText);
                           switch(obj.success){
                              case true:
                              store.load();
                              Ext.MessageBox.alert('Warning',obj.message);
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
            }
         },*/{
            xtype: 'textfield',
            name: 'key', baseCls:'Sfr_TxtfieldSearchGrid',
            id: 'key',
            emptyText:lang('Cari berdasar Batch/PO'),
            listeners: {
               specialkey: submitOnEnterCari
            }
         },{
            xtype: 'datefield',
            name: 'tgl',
            id: 'tgl',
            emptyText:lang('Tanggal'),
            format: 'Y-m-d',
            listeners: {
               specialkey: submitOnEnterCari
            }
         },{
            xtype :'button',
            icon: varjs.config.base_url+'images/icons/silk/search.png',
            margin: '0px 0px 0px 6px',
            text: lang('Search'),
            handler: function() {
               cari()
            }
         }]
      }],
      columns: [{
         text: lang('No'),
         xtype: 'rownumberer',
         width:'5%'
      },{
         text: lang('Batch Number'),
         dataIndex: 'SupplyBatchNumber',
         width:'10%'
      },{
         text: lang('PO'),
         dataIndex: 'DestPO',
         width:'10%'
      },{
         text: lang('Jenis'),
         dataIndex: 'jenis',
         width:'13%'
      },{
         text: lang('Unit'),
         width: '15%',
         dataIndex: 'Name'
      },{
         text: lang('Tanggal'),
         width: '8%',
         dataIndex: 'SupplyBatchDate'
      },{
         text: lang('Bruto'),
         width: '7%',
         dataIndex: 'VolumeBruto',
         renderer: function(value, metaData, record, rowIndex, colIndex, store) {
            return Ext.util.Format.number(value, '0,000.00');
         }
      },{
         text: lang('Netto'),
         width: '7%',
         dataIndex: 'VolumeNetto',
         renderer: function(value, metaData, record, rowIndex, colIndex, store) {
            return Ext.util.Format.number(value, '0,000.00');
         }
      },{
         text: lang('Destination Weight'),
         width: '7%',
         dataIndex: 'DestWeight',
         renderer: function(value, metaData, record, rowIndex, colIndex, store) {
            return Ext.util.Format.number(value, '0,000.00');
         }
      },{
         text: lang('Tandan'),
         width: '7%',
         dataIndex: 'Tandan'
      },{
         text: lang('Brondol'),
         width: '7%',
         dataIndex: 'Brondol'
      },{
         text: lang('Status'),
         width: '10%',
         dataIndex: 'SupplyDestStatus'
      },{
         text: lang('Destination'),
         width: '14%',
         dataIndex: 'NameDest'
      }]
   });

   //end-grid
});
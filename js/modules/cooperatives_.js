Ext.Loader.setConfig({enabled: true});
Ext.Loader.setPath('Ext.ux', varjs.config.base_url+'js/'+varjs.config.extjs_version+'/ux');
Ext.require([
    'Ext.grid.*',
    'Ext.data.*',
    'Ext.panel.*',
    'Ext.ux.grid.FiltersFeature',
    'Ext.form.Panel',
    'Ext.tab.*',
    'Ext.window.*',
    'Ext.tip.*',
    'Ext.layout.container.Border'
]);

Ext.onReady(function(){
    Ext.tip.QuickTipManager.init();
    var store = Ext.create('Ext.data.Store', {
        extend: 'Ext.data.Model',
        fields: ['id','CoopName','Chairman','SubDistrict','Village'],
        autoLoad: true,
        pageSize: 50,
        proxy: {
            type: 'ajax',
            url: m_crud+'s',
            extraParams: {prov: m_param},
            reader: {
                type: 'json',
                root: 'data',
                totalProperty: 'total'
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
         extraParams: {prov: m_param},
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
         extraParams: {prov: m_param},
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
      fields: ['label','id'],
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
        Ext.Ajax.request({
            url: m_label_provinsi,
            method: 'GET',
            params: {id: m_param},
            success: function(fp, o){
               var r = Ext.decode(fp.responseText);
               Ext.getCmp('Provinsi').setValue(r.id);
            }
         });
        if(!win.isVisible()){
            DataForm.getForm().reset();
            win.show();
        } else {
            win.hide(this, function() {});
            win.toFront();
        }
    }
    //staff
   Ext.define('staff.Model', {
        extend: 'Ext.data.Model',
        fields: ['StaffID','CoopID','FarmerID','FarmerName','Position'],
   });
   var store_staff = Ext.create('Ext.data.Store', {
      model: 'staff.Model',
      autoLoad: false,
      pageSize: 10,
      proxy: {
         type: 'ajax',
         url: m_staff+'s',
         reader: {
            type: 'json',
            root: 'data',
            totalProperty: 'total'
         }
      }
   });
   var RowEditing = Ext.create('Ext.grid.plugin.RowEditing', {
      id: 'RowEditing',
      clicksToMoveEditor: 0,
      autoCancel: false,
      errorSummary : false,
      clicksToEdit: 2
   });
   var cposition = Ext.create('Ext.data.Store', {
       fields: ['label'],
       data : [
           {"label":"Chairman"},
           {"label":"Secretary"},
           {"label":"Cashier"}
       ]
   });
    //end staff

    var DataForm = Ext.create('Ext.form.Panel', {
        frame: false,
        height: 500,
        autoScroll: true,
        width: 1010,
        bodyPadding: 5,
        id:'dataForm',
        fieldDefaults: {
            labelAlign: 'left',
            labelWidth: 140,
            anchor: '100%'
        },
        items: [{
               xtype: 'textfield',
               id: 'CoopName',
               name: 'CoopName',
               labelWidth: 180,
               fieldLabel: 'Nama'
         },{
             id: 'Provinsi',
             name: 'Provinsi',
             xtype: 'combo',
             fieldLabel: 'Provinsi',
             store:mc_Provinsi,
             displayField: 'label',
             valueField: 'label',
             queryMode: 'local',
             readOnly:true,
             listeners: {
               change: function (cb, nv, ov) {
                  mc_Kabupaten.load({
                     params: {
                        key: Ext.getCmp('Provinsi').getValue()
                  }});
                  Ext.getCmp('Kabupaten').enable();
               }
             }
         },{
             id: 'Kabupaten',
             name: 'Kabupaten',
             xtype: 'combo',
             fieldLabel: 'Kabupaten',
             disabled:'true',
             store:mc_Kabupaten,
             displayField: 'label',
             valueField: 'label',
             queryMode: 'local',
             listeners: {
               change: function (cb, nv, ov) {
                  mc_Kecamatan.load({
                     params: {
                        key: Ext.getCmp('Kabupaten').getValue()
                  }});
                  Ext.getCmp('Kecamatan').enable();
               }
             }
         },{
             id: 'Kecamatan',
             name: 'Kecamatan',
             xtype: 'combo',
             fieldLabel: 'Kecamatan',
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
                  }});
                  Ext.getCmp('Desa').enable();
               }
             }
         },{
             id: 'Desa',
             name: 'Desa',
             xtype: 'combo',
             fieldLabel: 'Desa',
             store:mc_Desa,
             displayField: 'label',
             disabled: 'true',
             valueField: 'id',
             queryMode: 'local'
         },{
               xtype: 'textfield',
               id: 'Address',
               name: 'Address',
               labelWidth: 180,
               fieldLabel: 'Alamat'
         },{
               xtype: 'textfield',
               id: 'Latitude',
               name: 'Latitude',
               labelWidth: 180,
               fieldLabel: 'Latitude'
         },{
               xtype: 'textfield',
               id: 'Longitude',
               name: 'Longitude',
               labelWidth: 180,
               fieldLabel: 'Longitude'
         },{
            xtype: 'panel',
            autoScroll: true,
            id:'panel_staff',
            disabled:true,
            title: 'Staff',
            padding:5,
            style: 'border:2px solid #ADD2ED',
            items: [{
               xtype: 'gridpanel',
               id:'grid_staff',
               store: store_staff,
               width: '100%',
               loadMask: true,
               selType: 'rowmodel',
               dockedItems: [{
                  xtype: 'toolbar',
                  items: [{
                     icon: varjs.config.base_url + 'images/icons/new/add.png', cls:'Sfr_BtnGridGreen', overCls:'Sfr_BtnGridGreen-Hover',
                     text: 'Add',
                     cls : m_act_save,
                     scope: this,
                     handler :function(){
                          RowEditing.cancelEdit();
                          var r = Ext.create('staff.Model', {
                              StaffID:'',CoopID:'', FarmerID:'', FarmerName:'', Position:''
                          });
                          store_staff.insert(0, r);
                          RowEditing.startEdit(0, 0);
                     }
                  },{
                     icon: varjs.config.base_url+'images/icons/silk/pencil.png',
                     cls:m_act_save,
                     text: 'Edit',
                     scope: this,
                     handler : function() {
                       RowEditing.cancelEdit();
                       var sm = Ext.getCmp('grid_staff').getSelectionModel().getSelection();
                       Ext.getCmp('')
                       RowEditing.startEdit(sm[0].index, 0);
                     }
                  },{
                     itemId: 'remove',
                     icon: varjs.config.base_url+'images/icons/silk/delete.png',
                     text: 'Hapus',
                     scope: this,
                     handler : function(){
                       var smb = Ext.getCmp('grid_staff').getSelectionModel().getSelection()[0];
                       RowEditing.cancelEdit();
                       Ext.MessageBox.confirm('Message', 'Apakah anda mau menghapus staff ini ?' , function(btn){
                           if(btn == 'yes'){
                              Ext.Ajax.request({
                                 waitMsg: 'Please Wait',
                                 url: m_staff,
                                 method : 'DELETE',
                                 params: {
                                    id:  smb.raw.StaffID
                                 },
                                 success: function(response, opts){
                                    var obj = Ext.decode(response.responseText);
                                    switch(obj.success){
                                       case true:
                                          store_staff.load({
                                             params: {
                                                id: Ext.getCmp('CoopID').getValue()
                                          }});
                                       break;
                                       default:
                                           Ext.MessageBox.alert('Warning',obj.message);
                                       break;
                                    }
                                 },
                                 failure: function(response, opts){
                                    var obj = Ext.decode(response.responseText);
                                    Ext.MessageBox.alert('error','Could not connect to the database. Retry later');
                                 }
                              });
                           }
                       });
                     }
                  }]
               }],
               columns: [{
                 text: 'No',
                 xtype: 'rownumberer',
                 width:'5%'
               },{
                 text: 'Nama Staff',
                 dataIndex: 'StaffName',
                 width:'10%',
                 editor: {
                     xtype      : 'combo',
                     store : cfarmer,
                     queryMode: 'local',
                     id: 'FarmerID',
                     displayField: 'label',
                     valueField: 'id'
                 }
               },{
                 text: 'Position',
                 dataIndex: 'Position',
                 width:'10%',
                 editor: {
                     xtype      : 'combo',
                     store : cposition,
                     id:'Position',
                     queryMode: 'local',
                     displayField: 'label',
                     valueField: 'label'
                 }
               }],
               plugins: [RowEditing],
               listeners: {
                   'canceledit':function(editor,e,eOpts){
                        store_staff.load({
                           params: {
                              id: Ext.getCmp('CoopID').getValue()
                        }});
                   },
                   'edit': function(editor, e) {
                     if(e.record.data.TraderStaffID==''){
                        Ext.Ajax.request({
                            waitMsg: 'Please wait...',
                            url: m_staff,
                            method : 'POST',
                            params: {
                               CoopID:            Ext.getCmp('CoopID').getValue(),
                               StaffID:            Ext.getCmp('StaffID').getValue(),
                               FarmerID:         e.record.data.FarmerID,
                               Position:       e.record.data.Position
                            },
                            success: function(response, opts){
                                var obj = Ext.decode(response.responseText);
                                switch(obj.success){
                                    case true:
                                       Ext.MessageBox.alert('Success',obj.message);
                                       store_staff.load({
                                          params: {
                                             id: Ext.getCmp('TraderID').getValue()
                                       }});
                                       break;
                                    default:
                                       Ext.MessageBox.alert('Warning',obj.message);
                                    break;
                                }
                            },
                            failure: function(response, opts){
                                var obj = Ext.decode(response.responseText);
                                Ext.MessageBox.alert('error','Could not connect to the database. Retry later');
                            }
                        });
                     } else {
                        Ext.MessageBox.confirm('Message', 'Update data staff ini ?' , function(btn){
                           if(btn == 'yes') {
                              Ext.Ajax.request({
                                 waitMsg: 'Please wait...',
                                 url: m_staff,
                                 method : 'PUT',
                                 params: {
                                     CoopID:            Ext.getCmp('CoopID').getValue(),
                                     StaffID:            Ext.getCmp('StaffID').getValue(),
                                     FarmerID:         e.record.data.FarmerID,
                                     Position:       e.record.data.Position
                                 },
                                 success: function(response, opts){
                                     var obj = Ext.decode(response.responseText);
                                     switch(obj.success){
                                         case true:
                                            Ext.MessageBox.alert('Success',obj.message);
                                            store_staff.load({
                                                params: {
                                                   id: Ext.getCmp('CoopID').getValue()
                                            }});
                                            break;
                                         default:
                                            Ext.MessageBox.alert('Warning',obj.message);
                                         break;
                                     }
                                },
                                failure: function(response, opts){
                                    var obj = Ext.decode(response.responseText);
                                       Ext.MessageBox.alert('error','Could not connect to the database. Retry later');
                                    }
                                });
                           }
                        });
                     }
                  }
               }
            }]
         },{
            xtype: 'panel',
            autoScroll: true,
            id:'panel_kualitas',
            disabled:true,
            title: 'Kualitas',
            padding:5,
            style: 'border:2px solid #ADD2ED',
            items: [{
               xtype: 'gridpanel',
               id:'grid_quality',
               store: store_quality,
               width: '100%',
               loadMask: true,
               selType: 'rowmodel',
               dockedItems: [{
                  xtype: 'toolbar',
                  items: [{
                     icon: varjs.config.base_url + 'images/icons/new/add.png', cls:'Sfr_BtnGridGreen', overCls:'Sfr_BtnGridGreen-Hover',
                     text: 'Add',
                     cls : m_act_save,
                     scope: this,
                     handler :function(){
                          qRowEditing.cancelEdit();
                          var r = Ext.create('quality.Model', {
                              QualityID:'', QualityTraderID:'', QualityDate:'', StandardName:'', Moisture:'',BeanCount:'',Waste:'',
                              Mouldy:'',Insect:'',Slaty:'',StandardID:''
                          });
                          store_quality.insert(0, r);
                          qRowEditing.startEdit(0, 0);
                           store_standard.load({
                              params: {
                                 id: Ext.getCmp('TraderID').getValue()
                           }});
                     }
                  },{
                     icon: varjs.config.base_url+'images/icons/silk/pencil.png',
                     cls:m_act_save,
                     text: 'Edit',
                     scope: this,
                     handler : function() {
                       qRowEditing.cancelEdit();
                       var sm = Ext.getCmp('grid_quality').getSelectionModel().getSelection();
                        store_standard.load({
                           params: {
                              id: Ext.getCmp('TraderID').getValue()
                        }});
                       qRowEditing.startEdit(sm[0].index, 0);
                     }
                  },{
                     itemId: 'remove',
                     icon: varjs.config.base_url+'images/icons/silk/delete.png',
                     text: 'Hapus',
                     scope: this,
                     handler : function(){
                       var smb = Ext.getCmp('grid_quality').getSelectionModel().getSelection()[0];
                       qRowEditing.cancelEdit();
                       Ext.MessageBox.confirm('Message', 'Apakah anda mau menghapus data kualitas ini ?' , function(btn){
                           if(btn == 'yes'){
                              Ext.Ajax.request({
                                 waitMsg: 'Please Wait',
                                 url: m_quality,
                                 method : 'DELETE',
                                 params: {
                                    id:  smb.raw.QualityID
                                 },
                                 success: function(response, opts){
                                    var obj = Ext.decode(response.responseText);
                                    switch(obj.success){
                                       case true:
                                          store_quality.load({
                                             params: {
                                                id: Ext.getCmp('TraderID').getValue()
                                          }});
                                       break;
                                       default:
                                           Ext.MessageBox.alert('Warning',obj.message);
                                       break;
                                    }
                                 },
                                 failure: function(response, opts){
                                    var obj = Ext.decode(response.responseText);
                                    Ext.MessageBox.alert('error','Could not connect to the database. Retry later');
                                 }
                              });
                           }
                       });
                     }
                  }]
               }],
               columns: [{
                 text: 'No',
                 xtype: 'rownumberer',
                 width:'5%'
               },{
                 text: 'Tanggal',
                 dataIndex: 'QualityDate',
                 width:'15%',
                 editor: {
                    xtype:'datefield',
                    allowBlank:false,
                    format:'Y-m-d'                    
                 }
               },{
                 text: 'Nama',
                 dataIndex: 'StandardName',
                 width:'20%',
                 editor: {
                     xtype      : 'combo',
                     store : store_standard,
                     id:'StandardID',
                     queryMode: 'local',
                     displayField: 'label',
                     valueField: 'id',
                      listeners: {
                        change: function (cb, nv, ov) {
                           Ext.Ajax.request({
                              url: m_quality_standard,
                              method: 'GET',
                              params: {id: this.value},
                              success: function(fp, o){
                                 var r = Ext.decode(fp.responseText);
                                 Ext.getCmp('iMoisture').setValue(r.Moisture);
                                 Ext.getCmp('iBeanCount').setValue(r.BeanCount);
                                 Ext.getCmp('iWaste').setValue(r.Waste);
                                 Ext.getCmp('iMouldy').setValue(r.Mouldy);
                                 Ext.getCmp('iInsect').setValue(r.Insect);
                                 Ext.getCmp('iSlaty').setValue(r.Slaty);
                              }
                           });
                        }
                      }
                 }
               },{
                 text: 'Moisture',
                 dataIndex: 'Moisture',
                 width:'10%',
                 editor: {
                    xtype:'textfield',
                    allowBlank:false,
                    id: 'iMoisture',
                    readOnly:true
                 }
               },{
                 text: 'BeanCount',
                 dataIndex: 'BeanCount',
                 width:'10%',
                 editor: {
                    xtype:'textfield',
                    allowBlank:false,
                    id: 'iBeanCount',
                    readOnly:true
                 }
               },{
                 text: 'Waste',
                 dataIndex: 'Waste',
                 width:'10%',
                 editor: {
                    xtype:'textfield',
                    allowBlank:false,
                    id: 'iWaste',
                    readOnly:true
                 }
               },{
                 text: 'Mouldy',
                 dataIndex: 'Mouldy',
                 width:'10%',
                 editor: {
                    xtype:'textfield',
                    allowBlank:false,
                    id: 'iMouldy',
                    readOnly:true
                 }
               },{
                 text: 'Insect',
                 dataIndex: 'Insect',
                 width:'10%',
                 editor: {
                    xtype:'textfield',
                    allowBlank:false,
                    id: 'iInsect',
                    readOnly:true
                 }
               },{
                 text: 'Slaty',
                 dataIndex: 'Slaty',
                 width:'10%',
                 editor: {
                    xtype:'textfield',
                    allowBlank:false,
                    id: 'iSlaty',
                    readOnly:true
                 }
               }],
               plugins: [qRowEditing],
               listeners: {
                   itemdblclick: function(dv, record, item, index, e) {
                       store_standard.load({
                           params: {
                              id: Ext.getCmp('TraderID').getValue()
                        }});
                   },               
                   'canceledit':function(editor,e,eOpts){
                        store_quality.load({
                           params: {
                              id: Ext.getCmp('TraderID').getValue()
                        }});
                   },
                   'edit': function(editor, e) {
                     if(e.record.data.QualityID==''){
                        Ext.Ajax.request({
                            waitMsg: 'Please wait...',
                            url: m_quality,
                            method : 'POST',
                            params: {
                               QualityTraderID      : Ext.getCmp('TraderID').getValue(),
                               QualityDate    : e.record.data.QualityDate,
                               StandardID      : Ext.getCmp('StandardID').getValue(),
                               Moisture   : e.record.data.Moisture,
                               BeanCount   : e.record.data.BeanCount,
                               Waste   : e.record.data.Waste,
                               Mouldy   : e.record.data.Mouldy,
                               Insect   : e.record.data.Insect,
                               Slaty   : e.record.data.Slaty,
                            },
                            success: function(response, opts){
                                var obj = Ext.decode(response.responseText);
                                switch(obj.success){
                                    case true:
                                       Ext.MessageBox.alert('Success',obj.message);
                                       store_quality.load({
                                          params: {
                                             id: Ext.getCmp('TraderID').getValue()
                                       }});
                                       break;
                                    default:
                                       Ext.MessageBox.alert('Warning',obj.message);
                                    break;
                                }
                            },
                            failure: function(response, opts){
                                var obj = Ext.decode(response.responseText);
                                Ext.MessageBox.alert('error','Could not connect to the database. Retry later');
                            }
                        });
                     } else {
                        Ext.MessageBox.confirm('Message', 'Update data quality ini ?' , function(btn){
                           if(btn == 'yes') {
                              Ext.Ajax.request({
                                 waitMsg: 'Please wait...',
                                 url: m_quality,
                                 method : 'PUT',
                                 params: {
                                     QualityTraderID      : Ext.getCmp('TraderID').getValue(),
                                     QualityID    : e.record.data.QualityID,
                                     QualityDate    : e.record.data.QualityDate,
                                     StandardID      : Ext.getCmp('StandardID').getValue(),
                                     Moisture   : e.record.data.Moisture,
                                     BeanCount   : e.record.data.BeanCount,
                                     Waste   : e.record.data.Waste,
                                     Mouldy   : e.record.data.Mouldy,
                                     Insect   : e.record.data.Insect,
                                     Slaty   : e.record.data.Slaty,
                                 },
                                 success: function(response, opts){
                                     var obj = Ext.decode(response.responseText);
                                     switch(obj.success){
                                         case true:
                                            Ext.MessageBox.alert('Success',obj.message);
                                            store_quality.load({
                                                params: {
                                                   id: Ext.getCmp('TraderID').getValue()
                                            }});
                                            break;
                                         default:
                                            Ext.MessageBox.alert('Warning',obj.message);
                                         break;
                                     }
                                },
                                failure: function(response, opts){
                                    var obj = Ext.decode(response.responseText);
                                       Ext.MessageBox.alert('error','Could not connect to the database. Retry later');
                                    }
                                });
                           }
                        });
                     }
                  }
               }
            }]
         },{
            xtype: 'panel',
            autoScroll: true,
            id:'panel_harga',
            disabled:true,
            title: 'Harga',
            padding:5,
            style: 'border:2px solid #ADD2ED',
            items: [{
               xtype: 'gridpanel',
               id:'grid_price',
               store: store_price,
               width: '100%',
               loadMask: true,
               selType: 'rowmodel',
               dockedItems: [{
                  xtype: 'toolbar',
                  items: [{
                     icon: varjs.config.base_url + 'images/icons/new/add.png', cls:'Sfr_BtnGridGreen', overCls:'Sfr_BtnGridGreen-Hover',
                     text: 'Add',
                     cls : m_act_save,
                     scope: this,
                     handler :function(){
                          pRowEditing.cancelEdit();
                          var r = Ext.create('price.Model', {
                              PriceID:'', PriceTraderID:'', PriceDate:'', Price:'',District:''
                          });
                          store_price.insert(0, r);
                          pRowEditing.startEdit(0, 0);
                     }
                  },{
                     icon: varjs.config.base_url+'images/icons/silk/pencil.png',
                     cls:m_act_save,
                     text: 'Edit',
                     scope: this,
                     handler : function() {
                       pRowEditing.cancelEdit();
                       var sm = Ext.getCmp('grid_price').getSelectionModel().getSelection();
                       pRowEditing.startEdit(sm[0].index, 0);
                     }
                  },{
                     itemId: 'remove',
                     icon: varjs.config.base_url+'images/icons/silk/delete.png',
                     text: 'Hapus',
                     scope: this,
                     handler : function(){
                       var smb = Ext.getCmp('grid_price').getSelectionModel().getSelection()[0];
                       pRowEditing.cancelEdit();
                       Ext.MessageBox.confirm('Message', 'Apakah anda mau menghapus harga ini ?' , function(btn){
                           if(btn == 'yes'){
                              Ext.Ajax.request({
                                 waitMsg: 'Please Wait',
                                 url: m_price,
                                 method : 'DELETE',
                                 params: {
                                    id:  smb.raw.PriceID
                                 },
                                 success: function(response, opts){
                                    var obj = Ext.decode(response.responseText);
                                    switch(obj.success){
                                       case true:
                                          store_price.load({
                                             params: {
                                                id: Ext.getCmp('TraderID').getValue()
                                          }});
                                       break;
                                       default:
                                           Ext.MessageBox.alert('Warning',obj.message);
                                       break;
                                    }
                                 },
                                 failure: function(response, opts){
                                    var obj = Ext.decode(response.responseText);
                                    Ext.MessageBox.alert('error','Could not connect to the database. Retry later');
                                 }
                              });
                           }
                       });
                     }
                  }]
               }],
               columns: [{
                 text: 'No',
                 xtype: 'rownumberer',
                 width:'5%'
               },{
                 text: 'Tanggal',
                 dataIndex: 'PriceDate',
                 width:'55%',
                 editor: {
                    xtype:'datefield',
                    allowBlank:false,
                    format:'Y-m-d'
                 }
               },{
                 text: 'District',
                 dataIndex: 'District',
                 width:'20%',
                 editor: {
                     xtype      : 'combo',
                     store : mc_Kabupaten,
                     queryMode: 'local',
                     id: 'District',
                     displayField: 'label',
                     valueField: 'label'
                 }
               },{
                 text: 'Harga',
                 dataIndex: 'Price',
                 width:'20%',
                 editor: {
                    xtype:'textfield',
                    allowBlank:false
                 }
               }],
               plugins: [pRowEditing],
               listeners: {
                   'canceledit':function(editor,e,eOpts){
                        store_price.load({
                           params: {
                              id: Ext.getCmp('TraderID').getValue()
                        }});
                   },
                   'edit': function(editor, e) {
                     if(e.record.data.PriceID==''){
                        Ext.Ajax.request({
                            waitMsg: 'Please wait...',
                            url: m_price,
                            method : 'POST',
                            params: {
                               PriceTraderID      : Ext.getCmp('TraderID').getValue(),
                               PriceDate    : e.record.data.PriceDate,
                               Price   : e.record.data.Price,
                               District   : e.record.data.District
                            },
                            success: function(response, opts){
                                var obj = Ext.decode(response.responseText);
                                switch(obj.success){
                                    case true:
                                       Ext.MessageBox.alert('Success',obj.message);
                                       store_price.load({
                                          params: {
                                             id: Ext.getCmp('TraderID').getValue()
                                       }});
                                       break;
                                    default:
                                       Ext.MessageBox.alert('Warning',obj.message);
                                    break;
                                }
                            },
                            failure: function(response, opts){
                                var obj = Ext.decode(response.responseText);
                                Ext.MessageBox.alert('error','Could not connect to the database. Retry later');
                            }
                        });
                     } else {
                        Ext.MessageBox.confirm('Message', 'Update data price ini ?' , function(btn){
                           if(btn == 'yes') {
                              Ext.Ajax.request({
                                 waitMsg: 'Please wait...',
                                 url: m_price,
                                 method : 'PUT',
                                 params: {
                                     PriceTraderID      : Ext.getCmp('TraderID').getValue(),
                                     PriceID    : e.record.data.PriceID,
                                     PriceDate    : e.record.data.PriceDate,
                                     Price   : e.record.data.Price,
                                     District  : e.record.data.District
                                 },
                                 success: function(response, opts){
                                     var obj = Ext.decode(response.responseText);
                                     switch(obj.success){
                                         case true:
                                            Ext.MessageBox.alert('Success',obj.message);
                                            store_price.load({
                                                params: {
                                                   id: Ext.getCmp('TraderID').getValue()
                                            }});
                                            break;
                                         default:
                                            Ext.MessageBox.alert('Warning',obj.message);
                                         break;
                                     }
                                },
                                failure: function(response, opts){
                                    var obj = Ext.decode(response.responseText);
                                       Ext.MessageBox.alert('error','Could not connect to the database. Retry later');
                                    }
                                });
                           }
                        });
                     }
                  }
               }
            }]
         },{
            xtype: 'panel',
            autoScroll: true,
            id:'panel_kemasan',
            disabled:true,
            title: 'Kemasan',
            padding:5,
            style: 'border:2px solid #ADD2ED',
            items: [{
               xtype: 'gridpanel',
               id:'grid_package',
               store: store_package,
               width: '100%',
               loadMask: true,
               selType: 'rowmodel',
               dockedItems: [{
                  xtype: 'toolbar',
                  items: [{
                     icon: varjs.config.base_url + 'images/icons/new/add.png', cls:'Sfr_BtnGridGreen', overCls:'Sfr_BtnGridGreen-Hover',
                     text: 'Add',
                     cls : m_act_save,
                     scope: this,
                     handler :function(){
                          paRowEditing.cancelEdit();
                          var r = Ext.create('package.Model', {
                              PackageID:'', PackageTraderID:'', PackageType:'', PackageWeight:''
                          });
                          store_package.insert(0, r);
                          paRowEditing.startEdit(0, 0);
                     }
                  },{
                     icon: varjs.config.base_url+'images/icons/silk/pencil.png',
                     cls:m_act_save,
                     text: 'Edit',
                     scope: this,
                     handler : function() {
                       paRowEditing.cancelEdit();
                       var sm = Ext.getCmp('grid_package').getSelectionModel().getSelection();
                       paRowEditing.startEdit(sm[0].index, 0);
                     }
                  },{
                     itemId: 'remove',
                     icon: varjs.config.base_url+'images/icons/silk/delete.png',
                     text: 'Hapus',
                     scope: this,
                     handler : function(){
                       var smp = Ext.getCmp('grid_package').getSelectionModel().getSelection()[0];
                       paRowEditing.cancelEdit();
                       Ext.MessageBox.confirm('Message', 'Apakah anda mau menghapus package ini ?' , function(btn){
                           if(btn == 'yes'){
                              Ext.Ajax.request({
                                 waitMsg: 'Please Wait',
                                 url: m_package,
                                 method : 'DELETE',
                                 params: {
                                    id:  smp.raw.PackageID
                                 },
                                 success: function(response, opts){
                                    var obj = Ext.decode(response.responseText);
                                    switch(obj.success){
                                       case true:
                                          store_package.load({
                                             params: {
                                                id: Ext.getCmp('TraderID').getValue()
                                          }});
                                       break;
                                       default:
                                           Ext.MessageBox.alert('Warning',obj.message);
                                       break;
                                    }
                                 },
                                 failure: function(response, opts){
                                    var obj = Ext.decode(response.responseText);
                                    Ext.MessageBox.alert('error','Could not connect to the database. Retry later');
                                 }
                              });
                           }
                       });
                     }
                  }]
               }],
               columns: [{
                 text: 'No',
                 xtype: 'rownumberer',
                 width:'5%'
               },{
                 text: 'Nama',
                 dataIndex: 'PackageType',
                 width:'75%',
                 editor: {
                    xtype:'textfield',
                    allowBlank:false
                 }
               },{
                 text: 'Berat Pemotongan',
                 dataIndex: 'PackageWeight',
                 width:'20%',
                 editor: {
                    xtype:'textfield',
                    allowBlank:false
                 }
               }],
               plugins: [paRowEditing],
               listeners: {
                   'canceledit':function(editor,e,eOpts){
                        store_package.load({
                           params: {
                              id: Ext.getCmp('id').getValue()
                        }});
                   },
                   'edit': function(editor, e) {
                     if(e.record.data.PackageID==''){
                        Ext.Ajax.request({
                            waitMsg: 'Please wait...',
                            url: m_package,
                            method : 'POST',
                            params: {
                               PackageTraderID      : Ext.getCmp('TraderID').getValue(),
                               PackageType    : e.record.data.PackageType,
                               PackageWeight  : e.record.data.PackageWeight
                            },
                            success: function(response, opts){
                                var obj = Ext.decode(response.responseText);
                                switch(obj.success){
                                    case true:
                                       Ext.MessageBox.alert('Success',obj.message);
                                       store_package.load({
                                          params: {
                                             id: Ext.getCmp('TraderID').getValue()
                                       }});
                                       break;
                                    default:
                                       Ext.MessageBox.alert('Warning',obj.message);
                                    break;
                                }
                            },
                            failure: function(response, opts){
                                var obj = Ext.decode(response.responseText);
                                Ext.MessageBox.alert('error','Could not connect to the database. Retry later');
                            }
                        });
                     } else {
                        Ext.MessageBox.confirm('Message', 'Update data package ini ?' , function(btn){
                           if(btn == 'yes') {
                              Ext.Ajax.request({
                                 waitMsg: 'Please wait...',
                                 url: m_package,
                                 method : 'PUT',
                                 params: {
                                     PackageTraderID      : Ext.getCmp('TraderID').getValue(),
                                     PackageID    : e.record.data.PackageID,
                                     PackageType    : e.record.data.PackageType,
                                     PackageWeight  : e.record.data.PackageWeight
                                 },
                                 success: function(response, opts){
                                     var obj = Ext.decode(response.responseText);
                                     switch(obj.success){
                                         case true:
                                            Ext.MessageBox.alert('Success',obj.message);
                                            store_package.load({
                                                params: {
                                                   id: Ext.getCmp('TraderID').getValue()
                                            }});
                                            break;
                                         default:
                                            Ext.MessageBox.alert('Warning',obj.message);
                                         break;
                                     }
                                },
                                failure: function(response, opts){
                                    var obj = Ext.decode(response.responseText);
                                       Ext.MessageBox.alert('error','Could not connect to the database. Retry later');
                                    }
                                });
                           }
                        });
                     }
                  }
               }
            }]
         }]
        }],
        /*
        {layout: 'column',
            items: [{
                columnWidth: 0.5,
                items:[{
                  xtype: 'fieldset',
                  title: 'Data Pedagang',
                  items: [{
                         xtype: 'textfield',
                         id: 'TraderID',
                         name: 'TraderID',
                         fieldLabel: 'ID Pedagang',
                         readOnly:true
                     },{
                         xtype: 'textfield',
                         id: 'TraderName',
                         name: 'TraderName',
                         fieldLabel: 'Nama Pedagang'
                     },{
                        fieldLabel : 'Jenis Kelamin',
                        xtype      : 'radiogroup',
                        width: '100%',
                        items: [{
                           boxLabel  : 'Laki-laki',
                           name      : 'Sex',
                           inputValue: 'm',
                           id        : 'Sex'
                        }, {
                           boxLabel  : 'Perempuan',
                           name      : 'Sex',
                           inputValue: 'f',
                           id        : 'Sex2'
                        }]
                     },{
                         xtype: 'textfield',
                         id: 'FamilyMembers',
                         name: 'FamilyMembers',
                         fieldLabel: 'Jumlah anggota keluarga'
                     },{
                         xtype: 'textfield',
                         id: 'IdentityNumber',
                         name: 'IdentityNumber',
                         fieldLabel: 'Nomor KTP'
                     },{
                         xtype: 'textfield',
                         id: 'Handphone',
                         name: 'Handphone',
                         fieldLabel: 'Handphone'
                     },{
                        xtype: 'radiogroup',
                        fieldLabel: 'Pendidikan Terakhir',
                        columns: 2,
                        items: [{
                            xtype: 'radiofield',
                            boxLabel: 'Belum pernah sekolah',
                            id: 'Education',
                            name: 'Education',
                            inputValue:'1'
                        },{
                            xtype: 'radiofield',
                            boxLabel: 'Tidak tamat SD',
                            id: 'Education2',
                            name: 'Education',
                            inputValue:'2'
                        },{
                            xtype: 'radiofield',
                            boxLabel: 'Tamat SD, tidak melanjutkan',
                            id: 'Education3',
                            name: 'Education',
                            inputValue:'3'
                        },{
                            xtype: 'radiofield',
                            boxLabel: 'Tamat SMP',
                            id: 'Education4',
                            name: 'Education',
                            inputValue:'4'
                        },{
                            xtype: 'radiofield',
                            boxLabel: 'Tamat SMA/SMK',
                            id: 'Education5',
                            name: 'Education',
                            inputValue:'5'
                        },{
                            xtype: 'radiofield',
                            boxLabel: 'Tamat perguruan tinggi',
                            id: 'Education6',
                            name: 'Education',
                            inputValue:'6'
                        }]
                     }]
                 },]
            },{
             columnWidth: 0.45,
             margin: 5,
             items:[]
                }]
        }],*/
        buttons: [{
            id:'saveButton',
            text: 'Save',
               margin: '5px',
               scale: 'large',
            ui: 's-button',
            cls: 's-blue',
            handler: function() {
                var form = this.up('form').getForm();
                var urle;
                if (Ext.getCmp('TraderID').getValue()!='') urle = m_crud+'u'; else urle = m_crud;
                form.submit({
                    url: urle,
                    waitMsg: 'Sending data...',
                    success: function(fp, o) {
                        Ext.MessageBox.alert('Success', 'Data saved.');
                    }
                });
                win.hide(this, function() {
                    store.load();
                });
            }
        },{
            text: 'Close',
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
        title: 'Trader',
        closable: true,
        modal:true,
        closeAction: 'show',
        autoScroll: true,
        width: '90%',
        height: '90%',
        layout: {
            type: 'fit'
        },
        items: [DataForm]
    });
    function fset(r) {
      Ext.getCmp('panel_staff').enable()
      store_staff.load({
         params: {
            id: Ext.getCmp('TraderID').getValue()
      }});
      Ext.getCmp('panel_kualitas_standard').enable()
      store_quality_standard.load({
         params: {
            id: Ext.getCmp('TraderID').getValue()
      }});
      store_standard.load({
         params: {
            id: Ext.getCmp('TraderID').getValue()
      }});
      Ext.getCmp('panel_kualitas').enable()
      store_quality.load({
         params: {
            id: Ext.getCmp('TraderID').getValue()
      }});
      Ext.getCmp('panel_harga').enable()
      store_price.load({
         params: {
            id: Ext.getCmp('TraderID').getValue()
      }});
      Ext.getCmp('panel_kemasan').enable()
      store_package.load({
         params: {
            id: Ext.getCmp('TraderID').getValue()
      }});

        /*Ext.getCmp('TraderName').setValue(r.TraderName);
        if (r.Sex=='m') Ext.getCmp('Sex').setValue(true);
        if (r.Sex=='f') Ext.getCmp('Sex2').setValue(true);
        //Ext.getCmp('Birthdate').setValue(r.Birthdate);
        Ext.getCmp('IdentityNumber').setValue(r.IdentityNumber);
      if (r.Education=='1') Ext.getCmp('Education').setValue(true);
      if (r.Education=='2') Ext.getCmp('Education2').setValue(true);
      if (r.Education=='3') Ext.getCmp('Education3').setValue(true);
      if (r.Education=='4') Ext.getCmp('Education4').setValue(true);
      if (r.Education=='5') Ext.getCmp('Education5').setValue(true);
      if (r.Education=='6') Ext.getCmp('Education6').setValue(true);
        Ext.getCmp('FamilyMembers').setValue(r.FamilyMembers);
        */
        Ext.getCmp('Address').setValue(r.Address);
        Ext.getCmp('Handphone').setValue(r.Handphone);
        if (r.VillageID!='') {
             Ext.getCmp('Provinsi').setValue(r.Provinsi);
             Ext.getCmp('Kabupaten').setValue(r.Kabupaten);
             Ext.getCmp('Kecamatan').setValue(r.Kecamatan);
             Ext.getCmp('Desa').setValue(r.VillageID);
        }
        Ext.getCmp('Company').setValue(r.Company);
      if (r.CompanyStatus=='UD') Ext.getCmp('CompanyStatus').setValue(true);
      if (r.CompanyStatus=='Firma') Ext.getCmp('CompanyStatus2').setValue(true);
      if (r.CompanyStatus=='CV') Ext.getCmp('CompanyStatus3').setValue(true);
      if (r.CompanyStatus=='Koperasi') Ext.getCmp('CompanyStatus4').setValue(true);
      if (r.CompanyStatus=='PT') Ext.getCmp('CompanyStatus5').setValue(true);
      if (r.CompanyStatus=='Tidak Berbadan Hukum') Ext.getCmp('CompanyStatus6').setValue(true);
        Ext.getCmp('CompanyYear').setValue(r.CompanyYear);
        Ext.getCmp('CompanyAlias').setValue(r.CompanyAlias);
        Ext.getCmp('PermanentEmployeeMale').setValue(r.PermanentEmployeeMale);
        Ext.getCmp('PermanentEmployeeFemale').setValue(r.PermanentEmployeeFemale);
        Ext.getCmp('TemporaryEmployeeMale').setValue(r.TemporaryEmployeeMale);
        Ext.getCmp('TemporaryEmployeeFemale').setValue(r.TemporaryEmployeeFemale);
        Ext.getCmp('LatDeg').setValue(r.LatDeg);
        //Ext.getCmp('LatMin').setValue(r.LatMin);
        Ext.getCmp('LatSec').setValue(r.LatSec);
        Ext.getCmp('LongDeg').setValue(r.LongDeg);
        //Ext.getCmp('LongMin').setValue(r.LongMin);
        Ext.getCmp('LongSec').setValue(r.LongSec);
        Ext.getCmp('Elevation').setValue(r.Elevation);
      Ext.getCmp('Photo_old').setValue(r.Photo);
      Ext.getCmp('iphoto').setSrc(m_photo+'/'+r.Photo);
    }

    var grid = Ext.create('Ext.grid.Panel', {
        store: store,
        width: '100%',
        minHeight: 250,
        id:'grid',
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
                    Ext.getCmp('TraderID').setValue(sm.get('id'));
                    fset(r)
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
            items: [{
                icon: varjs.config.base_url + 'images/icons/new/add.png', cls:'Sfr_BtnGridGreen', overCls:'Sfr_BtnGridGreen-Hover',
                text: 'Add',
                scope: this,
                handler : function(){
                  Ext.getCmp('panel_staff').disable()
                  Ext.getCmp('panel_kualitas').disable()
                  Ext.getCmp('panel_harga').disable()
                  Ext.getCmp('panel_kemasan').disable()
                  displayFormWindow();
                  Ext.getCmp('iphoto').setSrc('');
                  Ext.getCmp('Kabupaten').setValue('');
                  Ext.getCmp('Kecamatan').disable()
                  Ext.getCmp('Desa').disable()
                },
                cls : m_act_add
            },{
                icon: varjs.config.base_url+'images/icons/silk/pencil.png',
                text: 'Update',
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
                          Ext.getCmp('TraderID').setValue(sm.get('TraderID'));
                          fset(r)
                     }
                  });                
                },
                cls : m_act_update
            },{
               itemId: 'remove',
               icon: varjs.config.base_url+'images/icons/silk/delete.png',
               cls:m_act_delete,
               text: 'Hapus',
               scope: this,
               handler : function(){
                 var smb = Ext.getCmp('grid').getSelectionModel().getSelection()[0];
                 Ext.MessageBox.confirm('Message', 'Apakah anda mau menghapus data ini ?' , function(btn){
                     if(btn == 'yes'){
                        Ext.Ajax.request({
                        waitMsg: 'Please Wait',
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
                           Ext.MessageBox.alert('error','Could not connect to the database. Retry later');
                        }
                     });
                     }
                 });
               }
            },{
            name: 'key', baseCls:'Sfr_TxtfieldSearchGrid',
            id: 'key',
            xtype:'textfield',
            emptyText:'Cari berdasar nama/ID'
        },{
          id: 'sProvinsi',
          name: 'sProvinsi',
          xtype: 'combo',
          store:mc_Provinsi,
          displayField: 'label',
          valueField: 'label',
          queryMode: 'local',
          hidden:true,
          listeners: {
            change: function (cb, nv, ov) {
               mc_Kabupaten.load({
                  params: {
                     key: Ext.getCmp('sProvinsi').getValue()
               }});
               Ext.getCmp('sKabupaten').enable();
            }
          }
        },{
          id: 'sKabupaten',
          name: 'sKabupaten',
          xtype: 'combo',
          store:mc_Kabupaten,
          displayField: 'label',
          valueField: 'label',
          queryMode: 'local'
        },{
           xtype :'button',
           icon: varjs.config.base_url+'images/icons/silk/search.png',
           margin: '0px 0px 0px 6px',
           text: 'Search',
           handler: function() {
             store.load({
               params: {
                   key: Ext.getCmp('key').getValue(),
                   kab: Ext.getCmp('sKabupaten').getValue(),
                   prov: Ext.getCmp('sProvinsi').getValue()
             }});
           }
      }]
        }],
        columns: [{
            text: 'ID',
            dataIndex: 'id',
            hidden:true
        },{
                text: 'No',
                xtype: 'rownumberer',
                width:'5%'
            },{
                text: 'Nama Perusahaan',
                width: '20%',
                dataIndex: 'Company'
            },{
                text: 'Nama Alias',
                width: '10%',
                dataIndex: 'CompanyAlias'
            },{
                text: 'District',
                width: '20%',
                dataIndex: 'District'
            },{
                text: 'Status Hukum',
                width: '20%',
                dataIndex: 'CompanyStatus'
            },{
                text: 'Tahun Berdiri',
                width: '10%',
                dataIndex: 'CompanyYear'
            },{
                text: 'Jumlah Pegawai',
                width: '15%',
                dataIndex: 'TotalPegawai'
            }]
    });
});

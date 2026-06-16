Ext.onReady(function(){
    Ext.tip.QuickTipManager.init();
    var store = Ext.create('Ext.data.Store', {
        extend: 'Ext.data.Model',
        fields: ['SupplychainID','Name','Address','District'],
        autoLoad: true,
        pageSize: 50,
        proxy: {
            type: 'ajax',
            url: m_crud+'data/cooperation',
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
        fields: ['StaffID','UserId','StaffSupplychainID','StaffName','PrivateCellphone','OfficialCellphone','PrivateStaffEmail',
            'OfficialStaffEmail','StaffBirth','StaffGender',
            'Educatio','StaffGende','IdentityNumber','Education','FamilyMembers','Address','Position'],
   });
   var store_staff = Ext.create('Ext.data.Store', {
      model: 'staff.Model',
      autoLoad: false,
      pageSize: 10,
      proxy: {
         type: 'ajax',
         url: m_staff,
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
           {"label":"Staff"},
           {"label":"Coordinator"}
       ]
   });
   var cfarmer = Ext.create('Ext.data.Store', {
       fields: ['label'],
       data : [
           {"label":"Farmer"},
           {"label":"Non Farmer"}
       ]
   });
   var ckelamin = Ext.create('Ext.data.Store', {
       fields: ['id', 'label'],
       data : [
           {"id":"1", "label":"Laki-laki"},
           {"id":"2", "label":"Perempuan"}
       ]
   });
   var ceducation = Ext.create('Ext.data.Store', {
       fields: ['id', 'label'],
       data : [
           {"id":"1", "label":"Belum pernah sekolah"},
           {"id":"2", "label":"Tidak tamat SD"},
           {"id":"3", "label":"Tamat SD, tidak melanjutkan"},
           {"id":"4", "label":"Tamat SMP"},
           {"id":"5", "label":"Tamat SMA/SMK"},
           {"id":"6", "label":"Tamat perguruan tinggi"}
       ]
   });
    //end staff

    //quality standard
   Ext.define('quality_standard.Model', {
        extend: 'Ext.data.Model',
        fields: ['StandardID','StandardSupplychainID','StandardName','Moisture','BeanCount','Waste','Mouldy','Insect','Slaty'],
   });
   var store_quality_standard = Ext.create('Ext.data.Store', {
      model: 'quality_standard.Model',
      autoLoad: false,
      pageSize: 10,
      proxy: {
         type: 'ajax',
         url: m_quality_standard+'s',
         reader: {
            type: 'json',
            root: 'data',
            totalProperty: 'total'
         }
      }
   });
   var qsRowEditing = Ext.create('Ext.grid.plugin.RowEditing', {
      id: 'qsRowEditing',
      clicksToMoveEditor: 0,
      autoCancel: false,
      errorSummary : false,
      clicksToEdit: 2
   });
    //end quality standard

    //quality
   Ext.define('quality.Model', {
        extend: 'Ext.data.Model',
        fields: ['QualityID','QualitySupplychainID','QualityDate','StandardName','Moisture','BeanCount','Waste','Mouldy','Insect','Slaty','StandardID'],
   });
   var store_quality = Ext.create('Ext.data.Store', {
      model: 'quality.Model',
      autoLoad: false,
      pageSize: 10,
      proxy: {
         type: 'ajax',
         url: m_quality,
         reader: {
            type: 'json',
            root: 'data',
            totalProperty: 'total'
         }
      }
   });
   var qRowEditing = Ext.create('Ext.grid.plugin.RowEditing', {
      id: 'qRowEditing',
      clicksToMoveEditor: 0,
      autoCancel: false,
      errorSummary : false,
      clicksToEdit: 2
   });
    var store_standard = Ext.create('Ext.data.Store', {
        extend: 'Ext.data.Model',
        fields: ['id','label'],
        proxy: {
            type: 'ajax',
            url: m_standard,
            reader: {
                type: 'json',
                root: 'data'
            }
        }
    });
    //end quality    
    
    //price
   Ext.define('price.Model', {
        extend: 'Ext.data.Model',
        fields: ['PriceID','PriceSupplychainID','PriceDate','Price','District'],
   });
   var store_price = Ext.create('Ext.data.Store', {
      model: 'price.Model',
      autoLoad: false,
      pageSize: 10,
      proxy: {
         type: 'ajax',
         url: m_price,
         reader: {
            type: 'json',
            root: 'data',
            totalProperty: 'total'
         }
      }
   });
   var pRowEditing = Ext.create('Ext.grid.plugin.RowEditing', {
      id: 'pRowEditing',
      clicksToMoveEditor: 0,
      autoCancel: false,
      errorSummary : false,
      clicksToEdit: 2
   });
    //end price  

    //package
   Ext.define('package.Model', {
        extend: 'Ext.data.Model',
        fields: ['PackageID','PackageSupplychainID','PackageType','PackageWeight'],
   });
   var store_package = Ext.create('Ext.data.Store', {
      model: 'package.Model',
      autoLoad: false,
      pageSize: 10,
      proxy: {
         type: 'ajax',
         url: m_package,
         reader: {
            type: 'json',
            root: 'data',
            totalProperty: 'total'
         }
      }
   });
   var paRowEditing = Ext.create('Ext.grid.plugin.RowEditing', {
      id: 'paRowEditing',
      clicksToMoveEditor: 0,
      autoCancel: false,
      errorSummary : false,
      clicksToEdit: 2
   });
    //end package  
   Ext.define("Post", {
        extend: 'Ext.data.Model',
        proxy: {
            type: 'ajax',
            url : m_staff+'_farmers',
            reader: {
                type: 'json',
                root: 'data',
                totalProperty: 'total'
            }
        },
        fields: [
            {name: 'id', mapping: 'id'},
            {name: 'name', mapping: 'name'},
            {name: 'handphone', mapping: 'hp'},
            {name: 'email', mapping: 'email'},
            {name: 'birtday', mapping: 'birthday', type: 'date', dateFormat: 'timestamp'},
            {name: 'kelamin', mapping: 'kelamin'}
        ]
    });

    var ds = Ext.create('Ext.data.Store', {
        pageSize: 10,
        model: 'Post'
    });
    var DataForm = Ext.create('Ext.form.Panel', {
        frame: false,
        height: 500,
        autoScroll: true,
        width: 1010,
        bodyPadding: 5,
        fileUpload: true,
        enctype:'multipart/form-data',
        id:'dataForm',
        fieldDefaults: {
            labelAlign: 'left',
            labelWidth: 140,
            anchor: '100%'
        },
        items: [{
         xtype: 'tabpanel',
         flex: 1,
         margin:2,
         activeTab: 0,
         plain: true,
         items: [{
            xtype: 'panel',
            autoScroll: true,
            title: 'Data Umum',
            padding:5,
            style: 'border:2px solid #D6EDA4',
            items: [{
                  xtype: 'textfield',
                  id: 'SupplychainID',
                  name: 'SupplychainID',
                  hidden:true
              },{
               layout: 'column',
               items: [{
                columnWidth: 0.5,
                items:[{
                  xtype: 'fieldset',
                  title: 'Data Perusahaan',
                  items: [{
                     xtype: 'textfield',
                     id: 'Name',
                     name: 'Name',
                     fieldLabel: 'Nama Perusahaan'
                 },{
                     xtype: 'textfield',
                     id: 'Alias',
                     name: 'Alias',
                     fieldLabel: 'Nama Alias'
                 },{
                     xtype: 'textfield',
                     id: 'Handphone',
                     name: 'Handphone',
                     fieldLabel: 'No Telepon'
                 },{
                     xtype: 'radiogroup',
                     fieldLabel: 'Status Hukum Perusahaan',
                     columns: 3,       
                     items: [{
                         xtype: 'radiofield',
                         boxLabel: 'UD',
                         id: 'Status',
                         name: 'Status',
                         inputValue:'UD'
                     },{
                         xtype: 'radiofield',
                         boxLabel: 'Firma',
                         id: 'Status2',
                         name: 'Status',
                         inputValue:'Firma'
                     },{
                         xtype: 'radiofield',
                         boxLabel: 'CV',
                         id: 'Status3',
                         name: 'Status',
                         inputValue:'CV'
                     },{
                         xtype: 'radiofield',
                         boxLabel: 'Koperasi',
                         id: 'Status4',
                         name: 'Status',
                         inputValue:'Koperasi'
                     },{
                         xtype: 'radiofield',
                         boxLabel: 'PT',
                         id: 'Status5',
                         name: 'Status',
                         inputValue:'PT'
                     },{
                         xtype: 'radiofield',
                         boxLabel: 'Tidak berbadan hukum',
                         id: 'Status6',
                         name: 'Status',
                         inputValue:'Tidak Berbadan Hukum'
                     }]
                  },{
                     xtype: 'textfield',
                     id: 'Year',
                     name: 'Year',
                     fieldLabel: 'Tahun Berdiri'
                 },{
                  xtype: 'fieldset',
                  title: 'Status Karyawan',
                  items: [{
                     xtype:'label',
                     text:'Karyawan Tetap'
                  },{
                     xtype: 'textfield',
                     id: 'PermanentEmployeeMale',
                     name: 'PermanentEmployeeMale',
                     fieldLabel: 'Laki-laki'
                 },{
                     xtype: 'textfield',
                     id: 'PermanentEmployeeFemale',
                     name: 'PermanentEmployeeFemale',
                     fieldLabel: 'Perempuan'
                 },{
                     xtype:'label',
                     text:'Karyawan Tidak Tetap'
                 },{
                     xtype: 'textfield',
                     id: 'TemporaryEmployeeMale',
                     name: 'TemporaryEmployeeMale',
                     fieldLabel: 'Laki-laki'
                 },{
                     xtype: 'textfield',
                     id: 'TemporaryEmployeeFemale',
                     name: 'TemporaryEmployeeFemale',
                     fieldLabel: 'Perempuan'
                 }]
                 }]
                 }]
               },{
                columnWidth: 0.5,
                margin: 5,
                items:[{
                  layout: 'column',
                  border:true,
                  hidden:true,
                  items: [{
                   columnWidth: 0.6,
                  padding: 10,
                   items:[{
                         xtype: 'textfield',
                         id: 'Photo_old',
                         name: 'Photo_old',
                         inputType:'hidden'
                  },{
                    xtype: 'fileuploadfield',
                    fieldLabel: 'Icon',
                    labelWidth: 50,
                    id: 'Photo',
                    name: 'Photo',
                    buttonText: 'Browse',
                    listeners: {
                        'change': function(fb, v){
                            var form = this.up('form').getForm();
                            form.submit({
                                url: m_crud+'data_image',
                                waitMsg: 'Sending Photo...',
                                success: function(fp, o) {
                                    Ext.getCmp('iphoto').setSrc(m_photo+o.result.file);
                                    Ext.getCmp('Photo_old').setValue(o.result.file);
                                }
                            });
                        }
                    }
                   }]
                  },{
                   columnWidth: 0.4,
                   items:[{
                    xtype:'image',
                    id:'iphoto',
                    height:'120px'
                  }]
                 }]
                },{
                  xtype: 'fieldset',
                  title: 'Lokasi',
                  items: [{
                      id: 'Provinsi',
                      name: 'Provinsi',
                      xtype: 'combo',
                      fieldLabel: 'Provinsi',
                      store:mc_Provinsi,
                      displayField: 'label',
                      valueField: 'label',
                      queryMode: 'local',
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
                      fieldLabel: 'Alamat',
                      id: 'Address', 
                      name: 'Address'
                  },{
                     xtype: 'textfield',
                     id: 'LatSec',
                     name: 'LatSec',
                     fieldLabel: 'Latitude(Dec)'
                 },{
                     xtype: 'textfield',
                     id: 'LongSec',
                     name: 'LongSec',
                     fieldLabel: 'Longitude(Dec)'
                 },{
                     xtype: 'textfield',
                     id: 'LatDeg',
                     name: 'LatDeg',
                     fieldLabel: 'Latitude(Deg)'
                 },{
                     xtype: 'textfield',
                     id: 'LongDeg',
                     name: 'LongDeg',
                     fieldLabel: 'Longitude(Deg)'
                 },{
                     xtype: 'textfield',
                     id: 'Elevation',
                     name: 'Elevation',
                     fieldLabel: 'Elevation(Meter)'
                 }]
                 }]
               }]
            }]
         },{
            xtype: 'panel',
            autoScroll: true,
            id:'panel_staff',
            disabled:true,
            title: 'Staff',
            padding:5,
            style: 'border:2px solid #D6EDA4',
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
                              StaffID:'',UserId:'',StaffSupplychainID:'', StaffName:'',FarmerID:'', PrivateCellphone:'', 
                              OfficialCellphone:'',PrivateStaffEmail:'',OfficialStaffEmail:'',
                              StaffBirth:'', StaffGender:'',Educatio:'',StaffGende:'', IdentityNumber:'', Education:'', FamilyMembers:'',Address:'',
                              Position:''
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
                                    id:  smb.raw.StaffID,
                                    userid: smb.raw.UserId
                                 },
                                 success: function(response, opts){
                                    var obj = Ext.decode(response.responseText);
                                    switch(obj.success){
                                       case true:
                                          store_staff.load({
                                             params: {
                                                id: Ext.getCmp('SupplychainID').getValue()
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
                 text: 'Farmer',
                 id:'lid',
                 dataIndex: 'FarmerID',
                 width:'35%',
                 editor: {
                     xtype: 'combo',
                     store: ds,
                     displayField: 'name',
                     typeAhead: false,
                     hideLabel: true,
                     hideTrigger:true,
                     anchor: '100%',
                     listConfig: {
                         loadingText: 'Searching...',
                         emptyText: 'No matching farmer found.',
                         // Custom rendering template for each item
                         getInnerTpl: function() {
                             return '<div class="search-item">' +
                                 '{id} - {name}' +
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
                                 Ext.getCmp('ltnama').setValue(post.get('id'))
                                 Ext.getCmp('lhp').setValue(post.get('handphone'))
                                 Ext.getCmp('lhp').setReadOnly()
                                 Ext.getCmp('lemail').setValue(post.get('email'))
                                 Ext.getCmp('lemail').setReadOnly()
                                 Ext.getCmp('StaffGender').setValue(post.get('kelamin'))
                                 Ext.getCmp('StaffGender').setReadOnly()
                                 Ext.getCmp('lbirthday').setValue(post.get('birthday'))
                                 Ext.getCmp('lbirthday').setReadOnly()
                             }
                         }
                     }
                 }
               },{
                 text: 'Nama Staff',
                 id:'lnama',
                 dataIndex: 'StaffName',
                 width:'35%',
                 hidden:true,
                 editor: {
                    id:'ltnama',
                    xtype:'textfield'
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
               },{
                 text: 'Handphone',
                 dataIndex: 'PrivateCellphone',
                 width:'10%',
                 editor: {
                    xtype:'textfield',
                    allowBlank:false
                 }
               },{
                 text: 'Official Handphone',
                 dataIndex: 'OfficialCellphone',
                 width:'5%',
                 hidden:true,
                 editor: {
                    xtype:'textfield'
                 }
               },{
                 text: 'Email',
                 dataIndex: 'PrivateStaffEmail',
                 width:'10%',
                 editor: {
                    xtype:'textfield',
                    allowBlank:false
                 }
               },{
                 text: 'Official Email',
                 dataIndex: 'OfficialStaffEmail',
                 width:'10%',
                 hidden:true,
                 editor: {
                    xtype:'textfield'
                 }
               },{
                 text: 'Address',
                 dataIndex: 'Address',
                 width:'15%',
                 hidden:true,
                 editor: {
                    xtype:'textfield'
                 }
               },{
                 text: 'Identity Number',
                 dataIndex: 'IdentityNumber',
                 hidden:true,
                 width:'10%',
                 editor: {
                    xtype:'textfield'
                 }
               },{
                 text: 'Birthday',
                 dataIndex: 'StaffBirth',
                 width:'10%',
                 editor: {
                    xtype:'datefield',
                    allowBlank:false,
                    format:'Y-m-d'
                 }
               },{
                 text: 'Kelamin',
                 dataIndex: 'StaffGende',
                 width:'10%',
                 editor: {
                     xtype      : 'combo',
                     store : ckelamin,
                     queryMode: 'local',
                     id: 'StaffGender',
                     displayField: 'label',
                     valueField: 'id'
                 }
               },{
                 text: 'Education',
                 dataIndex: 'Educatio',
                 hidden:true,
                 width:'15%',
                 editor: {
                     xtype      : 'combo',
                     store : ceducation,
                     queryMode: 'local',
                     id:'Education',
                     displayField: 'label',
                     valueField: 'id'
                 }
               }],
               plugins: [RowEditing],
               listeners: {
                   'canceledit':function(editor,e,eOpts){
                        store_staff.load({
                           params: {
                              id: Ext.getCmp('id').getValue()
                        }});
                   },
                   'edit': function(editor, e) {
                     if(e.record.data.StaffID==''){
                        Ext.Ajax.request({
                            waitMsg: 'Please wait...',
                            url: m_staff,
                            method : 'POST',
                            params: {
                               SupplychainID:            Ext.getCmp('SupplychainID').getValue(),
                               StaffName:         e.record.data.StaffName,
                               FarmerID:         e.record.data.FarmerID,
                               Status:         e.record.data.Status,
                               PrivateCellphone:    e.record.data.PrivateCellphone,
                               PrivateStaffEmail:          e.record.data.PrivateStaffEmail,
                               OfficialCellphone:    e.record.data.OfficialCellphone,
                               OfficialStaffEmail:          e.record.data.OfficialStaffEmail,
                               StaffBirth:       e.record.data.StaffBirth,
                               StaffGender:       e.record.data.StaffGende,
                               IdentityNumber:       e.record.data.IdentityNumber,
                               Education:       e.record.data.Educatio,
                               Position:       e.record.data.Position,
                               Address:       e.record.data.Address
                            },
                            success: function(response, opts){
                                var obj = Ext.decode(response.responseText);
                                switch(obj.success){
                                    case true:
                                       Ext.MessageBox.alert('Success',obj.message);
                                       store_staff.load({
                                          params: {
                                             id: Ext.getCmp('SupplychainID').getValue()
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
                                     SupplychainID:            Ext.getCmp('SupplychainID').getValue(),
                                     StaffID:         e.record.data.StaffID,
                                     StaffName:         e.record.data.StaffName,
                                     FarmerID:         e.record.data.FarmerID,
                                     Status:         e.record.data.Status,
                                     PrivateCellphone:    e.record.data.PrivateCellphone,
                                     PrivateStaffEmail:          e.record.data.PrivateStaffEmail,
                                     OfficialCellphone:    e.record.data.OfficialCellphone,
                                     OfficialStaffEmail:          e.record.data.OfficialStaffEmail,
                                     StaffBirth:       e.record.data.StaffBirth,
                                     StaffGender:       e.record.data.StaffGender,
                                     IdentityNumber:       e.record.data.IdentityNumber,
                                     Education:       e.record.data.Education,
                                     Position:       e.record.data.Position,
                                     Address:       e.record.data.Address,
                                     
                                     StaffGende:       e.record.data.StaffGende,
                                     Educatio:       e.record.data.Educatio
                                 },
                                 success: function(response, opts){
                                     var obj = Ext.decode(response.responseText);
                                     switch(obj.success){
                                         case true:
                                            Ext.MessageBox.alert('Success',obj.message);
                                            store_staff.load({
                                                params: {
                                                   id: Ext.getCmp('SupplychainID').getValue()
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
            id:'panel_kualitas_standard',
            disabled:true,
            title: 'Standar Kualitas',
            padding:5,
            style: 'border:2px solid #D6EDA4',
            items: [{
               xtype: 'gridpanel',
               id:'grid_quality_standard',
               store: store_quality_standard,
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
                          qsRowEditing.cancelEdit();
                          var r = Ext.create('quality_standard.Model', {
                              StandardID:'', StandardSupplychainID:'', StandardName:'', Moisture:'',BeanCount:'',Waste:'',
                              Mouldy:'',Insect:'',Slaty:''
                          });
                          store_quality_standard.insert(0, r);
                          qsRowEditing.startEdit(0, 0);
                     }
                  },{
                     icon: varjs.config.base_url+'images/icons/silk/pencil.png',
                     cls:m_act_save,
                     text: 'Edit',
                     scope: this,
                     handler : function() {
                       qsRowEditing.cancelEdit();
                       var sm = Ext.getCmp('grid_quality_standard').getSelectionModel().getSelection();
                       qsRowEditing.startEdit(sm[0].index, 0);
                     }
                  },{
                     itemId: 'remove',
                     icon: varjs.config.base_url+'images/icons/silk/delete.png',
                     text: 'Hapus',
                     scope: this,
                     handler : function(){
                       var smb = Ext.getCmp('grid_quality_standard').getSelectionModel().getSelection()[0];
                       qsRowEditing.cancelEdit();
                       Ext.MessageBox.confirm('Message', 'Apakah anda mau menghapus data kualitas ini ?' , function(btn){
                           if(btn == 'yes'){
                              Ext.Ajax.request({
                                 waitMsg: 'Please Wait',
                                 url: m_quality_standard,
                                 method : 'DELETE',
                                 params: {
                                    id:  smb.raw.StandardID
                                 },
                                 success: function(response, opts){
                                    var obj = Ext.decode(response.responseText);
                                    switch(obj.success){
                                       case true:
                                          store_quality_standard.load({
                                             params: {
                                                id: Ext.getCmp('SupplychainID').getValue()
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
                 dataIndex: 'StandardName',
                 width:'35%',
                 editor: {
                    xtype:'textfield',
                    allowBlank:false                  
                 }
               },{
                 text: 'Moisture',
                 dataIndex: 'Moisture',
                 width:'10%',
                 editor: {
                    xtype:'textfield',
                    allowBlank:false
                 }
               },{
                 text: 'BeanCount',
                 dataIndex: 'BeanCount',
                 width:'10%',
                 editor: {
                    xtype:'textfield',
                    allowBlank:false
                 }
               },{
                 text: 'Waste',
                 dataIndex: 'Waste',
                 width:'10%',
                 editor: {
                    xtype:'textfield',
                    allowBlank:false
                 }
               },{
                 text: 'Mouldy',
                 dataIndex: 'Mouldy',
                 width:'10%',
                 editor: {
                    xtype:'textfield',
                    allowBlank:false
                 }
               },{
                 text: 'Insect',
                 dataIndex: 'Insect',
                 width:'10%',
                 editor: {
                    xtype:'textfield',
                    allowBlank:false
                 }
               },{
                 text: 'Slaty',
                 dataIndex: 'Slaty',
                 width:'10%',
                 editor: {
                    xtype:'textfield',
                    allowBlank:false
                 }
               }],
               plugins: [qsRowEditing],
               listeners: {
                   'canceledit':function(editor,e,eOpts){
                        store_quality_standard.load({
                           params: {
                              id: Ext.getCmp('id').getValue()
                        }});
                   },
                   'edit': function(editor, e) {
                     if(e.record.data.StandardID==''){
                        Ext.Ajax.request({
                            waitMsg: 'Please wait...',
                            url: m_quality_standard,
                            method : 'POST',
                            params: {
                               StandardSupplychainID      : Ext.getCmp('SupplychainID').getValue(),
                               StandardName    : e.record.data.StandardName,
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
                                       store_quality_standard.load({
                                          params: {
                                             id: Ext.getCmp('SupplychainID').getValue()
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
                        Ext.MessageBox.confirm('Message', 'Update data quality standard ini ?' , function(btn){
                           if(btn == 'yes') {
                              Ext.Ajax.request({
                                 waitMsg: 'Please wait...',
                                 url: m_quality_standard,
                                 method : 'PUT',
                                 params: {
                                     StandardSupplychainID      : Ext.getCmp('SupplychainID').getValue(),
                                     StandardID    : e.record.data.StandardID,
                                     StandardName    : e.record.data.StandardName,
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
                                            store_quality_standard.load({
                                                params: {
                                                   id: Ext.getCmp('SupplychainID').getValue()
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
            style: 'border:2px solid #D6EDA4',
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
                              QualityID:'', QualitySupplychainID:'', QualityDate:'', StandardName:'', Moisture:'',BeanCount:'',Waste:'',
                              Mouldy:'',Insect:'',Slaty:'',StandardID:''
                          });
                          store_quality.insert(0, r);
                          qRowEditing.startEdit(0, 0);
                           store_standard.load({
                              params: {
                                 id: Ext.getCmp('SupplychainID').getValue()
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
                              id: Ext.getCmp('SupplychainID').getValue()
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
                                                id: Ext.getCmp('SupplychainID').getValue()
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
                              id: Ext.getCmp('SupplychainID').getValue()
                        }});
                   },               
                   'canceledit':function(editor,e,eOpts){
                        store_quality.load({
                           params: {
                              id: Ext.getCmp('SupplychainID').getValue()
                        }});
                   },
                   'edit': function(editor, e) {
                     if(e.record.data.QualityID==''){
                        Ext.Ajax.request({
                            waitMsg: 'Please wait...',
                            url: m_quality,
                            method : 'POST',
                            params: {
                               QualitySupplychainID      : Ext.getCmp('SupplychainID').getValue(),
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
                                             id: Ext.getCmp('SupplychainID').getValue()
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
                                     QualitySupplychainID      : Ext.getCmp('SupplychainID').getValue(),
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
                                                   id: Ext.getCmp('SupplychainID').getValue()
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
            style: 'border:2px solid #D6EDA4',
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
                              PriceID:'', PriceSupplychainID:'', PriceDate:'', Price:'',District:''
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
                                                id: Ext.getCmp('SupplychainID').getValue()
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
                              id: Ext.getCmp('SupplychainID').getValue()
                        }});
                   },
                   'edit': function(editor, e) {
                     if(e.record.data.PriceID==''){
                        Ext.Ajax.request({
                            waitMsg: 'Please wait...',
                            url: m_price,
                            method : 'POST',
                            params: {
                               PriceSupplychainID      : Ext.getCmp('SupplychainID').getValue(),
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
                                             id: Ext.getCmp('SupplychainID').getValue()
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
                                     PriceSupplychainID      : Ext.getCmp('SupplychainID').getValue(),
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
                                                   id: Ext.getCmp('SupplychainID').getValue()
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
            style: 'border:2px solid #D6EDA4',
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
                              PackageID:'', PackageSupplychainID:'', PackageType:'', PackageWeight:''
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
                                                id: Ext.getCmp('SupplychainID').getValue()
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
                               PackageSupplychainID      : Ext.getCmp('SupplychainID').getValue(),
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
                                             id: Ext.getCmp('SupplychainID').getValue()
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
                                     PackageSupplychainID      : Ext.getCmp('SupplychainID').getValue(),
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
                                                   id: Ext.getCmp('SupplychainID').getValue()
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
        buttons: [{
            id:'saveButton',
            text: 'Save',
               margin: '5px',
               scale: 'large',
            ui: 's-button',
            cls: 's-blue',
            handler: function() {
                var form = this.up('form').getForm();
                var urle = m_crud+'data/cooperation';
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
        title: 'Cooperative',
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
            id: Ext.getCmp('SupplychainID').getValue()
      }});
      Ext.getCmp('panel_kualitas_standard').enable()
      store_quality_standard.load({
         params: {
            id: Ext.getCmp('SupplychainID').getValue()
      }});
      store_standard.load({
         params: {
            id: Ext.getCmp('SupplychainID').getValue()
      }});
      Ext.getCmp('panel_kualitas').enable()
      store_quality.load({
         params: {
            id: Ext.getCmp('SupplychainID').getValue()
      }});
      Ext.getCmp('panel_harga').enable()
      store_price.load({
         params: {
            id: Ext.getCmp('SupplychainID').getValue()
      }});
      Ext.getCmp('panel_kemasan').enable()
      store_package.load({
         params: {
            id: Ext.getCmp('SupplychainID').getValue()
      }});
        Ext.getCmp('Address').setValue(r.Address);
        Ext.getCmp('Handphone').setValue(r.Handphone);
        if (r.VillageID!='') {
             Ext.getCmp('Provinsi').setValue(r.Provinsi);
             Ext.getCmp('Kabupaten').setValue(r.Kabupaten);
             Ext.getCmp('Kecamatan').setValue(r.Kecamatan);
             Ext.getCmp('Desa').setValue(r.VillageID);
        }
        Ext.getCmp('Name').setValue(r.Name);
      if (r.Status=='UD') Ext.getCmp('Status').setValue(true);
      if (r.Status=='Firma') Ext.getCmp('Status2').setValue(true);
      if (r.Status=='CV') Ext.getCmp('Status3').setValue(true);
      if (r.Status=='Koperasi') Ext.getCmp('Status4').setValue(true);
      if (r.Status=='PT') Ext.getCmp('Status5').setValue(true);
      if (r.Status=='Tidak Berbadan Hukum') Ext.getCmp('Status6').setValue(true);
        Ext.getCmp('Year').setValue(r.Year);
        Ext.getCmp('Alias').setValue(r.Alias);
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
               url: m_crud+'data/cooperation',
               method: 'GET',
               params: {id: sm.get('SupplychainID')},
               success: function(fp, o){
                  var r = Ext.decode(fp.responseText);
                  Ext.getCmp('SupplychainID').setValue(sm.get('SupplychainID'));
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
               Ext.getCmp('Provinsi').setValue('');
               Ext.getCmp('Kabupaten').disable()
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
                  url: m_crud+'data/cooperation',
                  method: 'GET',
                  params: {id: sm.get('SupplychainID')},
                  success: function(fp, o){
                     var r = Ext.decode(fp.responseText);
                     Ext.getCmp('SupplychainID').setValue(sm.get('SupplychainID'));
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
                        url: m_crud+'data',
                        method : 'DELETE',
                        params: {id:  smb.raw.SupplychainID},
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
          text: 'ID',
          width: '10%',
          dataIndex: 'SupplychainID'
      },{
          text: 'Nama',
          width: '30%',
          dataIndex: 'Name'
      },{
          text: 'Alamat',
          width: '30%',
          dataIndex: 'Address'
      },{
          text: 'District',
          width: '25%',
          dataIndex: 'District'
      }]
   });
});

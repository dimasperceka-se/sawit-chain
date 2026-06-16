Ext.onReady(function(){
    var store = Ext.create('Ext.data.Store', {
        extend: 'Ext.data.Model',
        fields: ['CustomerID','Name','Email', 'Phone', 'Address', 'Note'],
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
    function set_update(sm) {
       Ext.Ajax.request({
           url: m_crud,
           method: 'GET',
           params: {CustomerID: sm.get('CustomerID')},
           success: function(fp, o){
               var r = Ext.decode(fp.responseText);console.log(r)
               Ext.getCmp('CustomerID').setValue(sm.get('CustomerID'));
               Ext.getCmp('OrgID').setValue(r.sce.SceID);
               Ext.getCmp('Name').setValue(r.data.Name);
               Ext.getCmp('Email').setValue(r.data.Email);
               Ext.getCmp('Phone').setValue(r.data.Phone);
               Ext.getCmp('Address').setValue(r.data.Address);
               Ext.getCmp('Note').setValue(r.data.Note);
               Ext.getCmp('Provinsi').setValue(r.data.Provinsi);
               Ext.getCmp('Kabupaten').setValue(r.data.Kabupaten);
               Ext.getCmp('Kecamatan').setValue(r.data.Kecamatan);
               Ext.getCmp('Desa').setValue(r.data.Desa);
               Ext.getCmp('FarmerID').setValue(r.data.FarmerName);
           }
       });
    }
    var mc_Provinsi = Ext.create('Ext.data.Store', {
        extend: 'Ext.data.Model',
        fields: ['id', 'label'],
        autoLoad: true,
        pageSize: 10,
        proxy: {
            type: 'ajax',
            url: m_api+'/farmer/Provinsis',
            reader: {
                type: 'json',
                root: 'data'
            }
        }
    });
    var mc_Kabupaten = Ext.create('Ext.data.Store', {
        extend: 'Ext.data.Model',
        fields: ['id', 'label'],
        autoLoad: true,
        pageSize: 10,
        proxy: {
            type: 'ajax',
            url: m_api+'/farmer/Kabupatens',
            reader: {
                type: 'json',
                root: 'data'
            }
        }
    });
    var mc_Kecamatan = Ext.create('Ext.data.Store', {
        extend: 'Ext.data.Model',
        fields: ['id', 'label'],
        autoLoad: false,
        pageSize: 10,
        proxy: {
            type: 'ajax',
            url: m_api+'/farmer/Kecamatans',
            reader: {
                type: 'json',
                root: 'data'
            }
        }
    });
    var mc_Desa = Ext.create('Ext.data.Store', {
        extend: 'Ext.data.Model',
        fields: ['label', 'id'],
        autoLoad: false,
        pageSize: 10,
        proxy: {
            type: 'ajax',
            url: m_api+'/farmer/Desas',
            reader: {
                type: 'json',
                root: 'data'
            }
        }
    }); 
    var mc_org_type = Ext.create('Ext.data.Store', {
        fields: ['label'],
        data: [{'label': 'sce'}, {'label': 'koperasi'}],
        autoLoad: true
    });
    var mc_org_id = Ext.create('Ext.data.Store', {
        extend: 'Ext.data.Model',
        fields: ['id', 'label'],
        autoLoad: false,
        pageSize: 10,
        proxy: {
            type: 'ajax',
            url: m_api+'/bussiness/penjualan_org',
            reader: {
                type: 'json',
                root: 'data'
            }
        }
    });
   Ext.define("Post", {
        extend: 'Ext.data.Model',
        proxy: {
            type: 'ajax',
            url : m_api+'/bussiness/penjualan_farmer',
            reader: {
                type: 'json',
                root: 'data',
                totalProperty: 'total'
            }
        },
        fields: [
            {name: 'FarmerID', mapping: 'FarmerID'},
            {name: 'FarmerName', mapping: 'FarmerName'},
            {name: 'Email', mapping: 'Email'},
            {name: 'Phone', mapping: 'Phone'},
            {name: 'Address', mapping: 'Address'},
            {name: 'Provinsi', mapping: 'Provinsi'},
            {name: 'Kabupaten', mapping: 'Kabupaten'},
            {name: 'Kecamatan', mapping: 'Kecamatan'},
            {name: 'VillageID', mapping: 'VillageID'}
        ]
    });
    var mc_farmer = Ext.create('Ext.data.Store', {
        pageSize: 10,
        model: 'Post'
    });
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
                set_update(sm);
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
                text: lang('Add'),
                scope: this,
                handler : function(){
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
                  set_update(sm)
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
                        params: {CustomerID:  smb.raw.CustomerID},
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
            }]
        }],
        columns: [{
            text: lang('No'),
            xtype: 'rownumberer',
            width:'5%'
        },{
            text: lang('Name'),
            width: '20%',
            dataIndex: 'Name'
        },{
            text: lang('Email'),
            width: '20%',
            dataIndex: 'Email',
           // xtype:'Email',
        },{
            text: lang('Phone'),
            width: '15%',
            dataIndex: 'Phone'
        },{
            text: lang('Address'),
            width: '20%',
            dataIndex: 'Address'
        },{
            text: lang('Note'),
            width: '20%',
            dataIndex: 'Note'
        }]
    });
    
    function displayFormWindow(){
        if(!win.isVisible()){
            DataForm.getForm().reset();
            Ext.getCmp('OrgType').setValue('sce');
            win.show();
        } else {
            win.hide(this, function() {});
            win.toFront();
        }
    }
    
    var DataForm = Ext.create('Ext.form.Panel', {
        frame: false,
        height: 350,
        width: '100%',
        bodyPadding: 5,
        autoScroll:true,
        id:'dataForm',
        fieldDefaults: {
            labelAlign: 'left',
            labelWidth: 150,
            anchor: '100%'
        },
        items: [{
            layout: 'column',
            border: false,
            items: [{
                 columnWidth: .48,
                 layout: 'form',
                 padding: 10,
                 xtype: 'fieldset',
                 title: 'Data',
                 height: '100%',
                 items: [{
                    xtype: 'textfield',
                    id: 'CustomerID',
                    name: 'CustomerID',
                    hidden:true
                },{
                   fieldLabel: lang('Org Type'),
                   id: 'OrgType',
                   name: 'OrgType',
                   xtype: 'combo',
                   store: mc_org_type,
                   displayField: 'label',
                   valueField: 'label',
                   queryMode: 'local',
                   hidden:true,
                   listeners: {
                     change: function (cb, nv, ov) {
                        if (Ext.getCmp('OrgType').getValue()!=null)
                        mc_org_id.load({
                             params: {
                                 'OrgType': Ext.getCmp('OrgType').getValue()
                             }
                        });
                     }
                   }
               },{
                  xtype: 'combo',
                  store: mc_farmer,
                  fieldLabel: lang('Farmer'),
                  id:'FarmerID',
                  name:'FarmerID',
                  displayField: 'FarmerName',
                  valueField: 'FarmerID',
                  typeAhead: false,
                  hideTrigger:true,
                  anchor: '100%',
                  listConfig: {
                      loadingText: 'Searching...',
                      emptyText: lang('No matching data found.'),
                      getInnerTpl: function() {
                          return '<div class="search-item">' +
                              '{FarmerID} - {FarmerName}' +
                              '{excerpt}' +
                          '</div>';
                      }
                  },
                  pageSize: 10,
                  listeners: {
                      select: function(combo, selection) {
                          var post = selection[0];
                          if (post) {
                              Ext.getCmp('Name').setValue(post.get('FarmerName'))
                              Ext.getCmp('Email').setValue(post.get('Email'))
                              Ext.getCmp('Phone').setValue(post.get('Phone'))
                              Ext.getCmp('Address').setValue(post.get('Address'))
                              Ext.getCmp('Provinsi').setValue(post.get('Provinsi'))
                              Ext.getCmp('Kabupaten').setValue(post.get('Kabupaten'))
                              Ext.getCmp('Kecamatan').setValue(post.get('Kecamatan'))
                              Ext.getCmp('Desa').setValue(post.get('VillageID').toString())
                          }
                      }
                  }
               },{
                   fieldLabel: lang('Org'),
                   id: 'OrgID',
                   name: 'OrgID',
                   xtype: 'combo',
                   store: mc_org_id,
                   displayField: 'label',
                   valueField: 'id',
                   queryMode: 'local',
                  listeners   : {
                      beforequery: function(record){  
                          record.query = new RegExp(record.query, 'i');
                          record.forceAll = true;
                      }
                  }               
               },{
                    xtype: 'textfield',
                    fieldLabel: lang('Name'),
                    id: 'Name',
                    name: 'Name',
                },{
                    xtype: 'textfield',
                    fieldLabel: lang('Email'),
                    id: 'Email',
                    name: 'Email'
                },{
                    xtype: 'textfield',
                    fieldLabel: lang('Phone'),
                    id: 'Phone',
                    name: 'Phone',
                }]
            },{ // right fieldset
                columnWidth: .52,
                height: '100%',
                layout: 'form',
                xtype: 'fieldset',
                title: 'Detail',
                style:'margin-left:12px',
                items: [{
                       fieldLabel: lang('Address'),
                       xtype: 'textarea',
                       height: 40,
                       id: 'Address',
                       name: 'Address'
                   },{
                       fieldLabel: lang('Note'),
                       xtype: 'textarea',
                       height: 40,
                       id: 'Note',
                       name: 'Note'
                  },{
                       id: 'Provinsi',
                       name: 'Provinsi',
                       xtype: 'combo',
                       fieldLabel: lang('Provinsi'),
                       store: mc_Provinsi,
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
                   }, {
                       id: 'Kabupaten',
                       name: 'Kabupaten',
                       xtype: 'combo',
                       fieldLabel: lang('Kabupaten'),
                       store: mc_Kabupaten,
                       displayField: 'label',
                       valueField: 'label',
                       queryMode: 'local',
                       disabled: 'true',
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
                   }, {
                       id: 'Kecamatan',
                       name: 'Kecamatan',
                       xtype: 'combo',
                       fieldLabel: lang('Kecamatan'),
                       store: mc_Kecamatan,
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
                           }
                       }
                   }, {
                       id: 'Desa',
                       name: 'Desa',
                       xtype: 'combo',
                       fieldLabel: lang('Desa'),
                       store: mc_Desa,
                       displayField: 'label',
                       disabled: 'true',
                       valueField: 'id',
                       queryMode: 'local'
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
                var form = this.up('form').getForm();
                var method;
                if (Ext.getCmp('CustomerID').getValue()!='') method = 'PUT'; else method = 'POST';
                form.submit({
                    url: m_crud,
                    method:method,
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
        title: lang('Customer'),
        id:'win',
        closable: true,
        modal:true,
        closeAction: 'show',
        width: '90%',
        height: '90%',
        layout: {
            type: 'fit'
        },
        items: [DataForm],
        listeners:{
            'close':function(){
                //clearItemselector();
            }
        }
    });

});

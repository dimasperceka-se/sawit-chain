Ext.onReady(function(){
    var store = Ext.create('Ext.data.Store', {
        extend: 'Ext.data.Model',
        fields: ['PurchaseID','OrgID','OrgType','Date','Number','SupplierID','Name','Total','Pembayaran'],
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
    //supp
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
    //end supp
    function set_update(sm) {
       //if (sm.get('Pembayaran')>0) return;
       Ext.Ajax.request({
           url: m_crud,
           method: 'GET',
           params: {PurchaseID: sm.get('PurchaseID')},
           success: function(fp, o){
               var r = Ext.decode(fp.responseText);
               Ext.getCmp('Date').setValue(sm.get('Date'));
               Ext.getCmp('PurchaseID').setValue(sm.get('PurchaseID'));
               Ext.getCmp('OrgType').setValue(sm.get('OrgType'));
               Ext.getCmp('OrgID').setValue(sm.get('OrgID'));
               Ext.getCmp('SupplierID').setValue(sm.get('SupplierID'));
               Ext.getCmp('Total').setValue(sm.get('Total'));
               Ext.getCmp('Pay').setValue(sm.get('Pembayaran'));
               store_detail.load({
                    params: {
                        'id': Ext.getCmp('PurchaseID').getValue()
                    }
               });
           }
       });
    }
    //detail
    Ext.define('detail.Model', {
        extend: 'Ext.data.Model',
        fields: ['DetailID','PurchaseID','name','InventoryID',{name:'Qty',type:'float'},{name:'Price',type:'float'},
            {name:'Total',type:'float'}], 
    });
    var store_detail = Ext.create('Ext.data.Store', {
        model: 'detail.Model',
        autoLoad: false,
        pageSize: 10,
        proxy: {
            type: 'ajax',
            url: m_crud + '_detail',
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
        errorSummary: false,
        clicksToEdit: 2
    });
    //end detail
    //store combo
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
    var mc_supplier = Ext.create('Ext.data.Store', {
        extend: 'Ext.data.Model',
        fields: ['id', 'label'],
        autoLoad: false,
        pageSize: 10,
        proxy: {
            type: 'ajax',
            url: m_api+'/bussiness/supplier',
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
            url : m_api+'/bussiness/penjualan_barang',
            reader: {
                type: 'json',
                root: 'data',
                totalProperty: 'total'
            }
        },
        fields: [
            {name: 'id', mapping: 'id'},
            {name: 'name', mapping: 'name'},
            {name: 'label', mapping: 'label'},
            {name: 'Price', mapping: 'Price'}
        ]
    });
    var mc_barang = Ext.create('Ext.data.Store', {
        pageSize: 10,
        model: 'Post'
    });
    //end store combo
    function save_detail(e) {
        Ext.Ajax.request({
            waitMsg: 'Please wait...',
            url: m_crud+'_detail',
            method: 'POST',
            params: {
                PurchaseID: Ext.getCmp('PurchaseID').getValue(),
                name: e.record.data.name,
                InventoryID: Ext.getCmp('InventoryID').getValue(),
                Qty: e.record.data.Qty,
                Price: e.record.data.Price
            },
            success: function (response, opts) {
                var obj = Ext.decode(response.responseText);
                switch (obj.success) {
                    case true:
                        Ext.MessageBox.alert('Success', obj.message);
                        store_detail.load({
                            params: {
                                id: Ext.getCmp('PurchaseID').getValue()
                            }
                        });
                        break;
                    default:
                        Ext.MessageBox.alert('Warning', obj.message);
                        break;
                }
            },
            failure: function (response, opts) {
                var obj = Ext.decode(response.responseText);
                Ext.MessageBox.alert('error', 'Could not connect to the database. Retry later');
            }
        });
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
                    store_detail.load();
                    Ext.getCmp('OrgType').setValue()
                    Ext.getCmp('OrgID').setValue()
                    Ext.getCmp('SupplierID').setValue()
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
                        params: {id:  smb.raw.PurchaseID},
                        success: function(response, opts){
                           var obj = Ext.decode(response.responseText);
                           switch(obj.success){
                              case true: 
                                 store.load({
                                     params: {
                                         Awal: Ext.getCmp('searchAwal').getSubmitValue(),
                                         Akhir: Ext.getCmp('searchAkhir').getSubmitValue(),
                                         OrgType: Ext.getCmp('searchOrgType').getValue(),
                                         OrgID: Ext.getCmp('searchOrgID').getValue(),
                                     }
                                 });
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
                icon: varjs.config.base_url+'images/icons/silk/pencil.png',
                text: lang('Retur'),
                scope: this,
                handler : function(){
                  var sm = Ext.getCmp('grid').getSelectionModel().getSelection()[0];
                  link(m_retur+sm.get('PurchaseID'));
                },
                cls : m_act_update
            },{              emptyText: lang('Org Type'),
                   id: 'searchOrgType',
                   name: 'searchOrgType',
                   xtype: 'combo',
                   store: mc_org_type,
                   displayField: 'label',
                   width:100,
                   valueField: 'label',
                   queryMode: 'local',
                   listeners: {
                     change: function (cb, nv, ov) {
                        if (Ext.getCmp('searchOrgType').getValue()!=null)
                        mc_org_id.load({
                             params: {
                                 'OrgType': Ext.getCmp('searchOrgType').getValue()
                             }
                        });
                     }
                   }
               },{emptyText: lang('Org ID'),
                   id: 'searchOrgID',
                   name: 'searchOrgID',
                   xtype: 'combo',
                   width:250,
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
              xtype: 'datefield',
              name: 'searchAwal',
                   width:100,
              id: 'searchAwal',
              format: 'Y-m-d',
              emptyText: lang('Awal'),
          }, {
              xtype: 'label',
              text: ' s.d '
          },{
                   width:100,
              xtype: 'datefield',
              name: 'searchAkhir',
              id: 'searchAkhir',
              format: 'Y-m-d',
              emptyText: lang('Akhir'),
          }, {
              xtype: 'button',
              margin: '0px 0px 0px 6px',
              text: 'Search',
              handler: function () {
                  store.load({
                      params: {
                          Awal: Ext.getCmp('searchAwal').getSubmitValue(),
                          Akhir: Ext.getCmp('searchAkhir').getSubmitValue(),
                          OrgType: Ext.getCmp('searchOrgType').getValue(),
                          OrgID: Ext.getCmp('searchOrgID').getValue(),
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
            text: lang('Number'),
            width: '15%',
            dataIndex: 'Number'
        },{
            text: lang('Org Type'),
            width: '10%',
            dataIndex: 'OrgType'
        },{
            text: lang('Org ID'),
            width: '10%',
            dataIndex: 'OrgID'
        },{
            text: lang('Date'),
            width: '15%',
            dataIndex: 'Date'
        },{
            text: lang('Supplier'),
            width: '20%',
            dataIndex: 'Name'
        },{
            text: lang('Total'),
            width: '25%',
            dataIndex: 'Total',
            xtype:'numbercolumn',
            format:'0,000.00'
        }]
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
    
    var DataForm = Ext.create('Ext.form.Panel', {
        frame: false,
        height: 350,
        width: '100%',
        bodyPadding: 5,
        autoScroll:true,
        id:'dataForm',
        fieldDefaults: {
            labelAlign: 'left',
            labelWidth: 100,
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
                 title: 'Pembelian',
                 height: '100%',
                 items: [{
                    xtype: 'textfield',
                    id: 'PurchaseID',
                    name: 'PurchaseID',
                    hidden:true
                },{
                  xtype: 'datefield',
                  fieldLabel: lang('Tanggal'),
                  id: 'Date',
                  name: 'Date',
                  format: 'Y-m-d',
                  value: new Date()
              },{
                   fieldLabel: lang('Org Type'),
                   id: 'OrgType',
                   name: 'OrgType',
                   xtype: 'combo',
                   store: mc_org_type,
                   displayField: 'label',
                   valueField: 'label',
                   queryMode: 'local',
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
                   fieldLabel: lang('Org'),
                   id: 'OrgID',
                   name: 'OrgID',
                   xtype: 'combo',
                   store: mc_org_id,
                   displayField: 'label',
                   valueField: 'id',
                   queryMode: 'local',
                   listeners: {
                      beforequery: function(record){  
                          record.query = new RegExp(record.query, 'i');
                          record.forceAll = true;
                      },
                     change: function (cb, nv, ov) {
                        if (Ext.getCmp('OrgType').getValue()!=null) {
                           mc_supplier.load({
                                params: {
                                    'OrgType': Ext.getCmp('OrgType').getValue(),
                                    'OrgID': Ext.getCmp('OrgID').getValue()
                                }
                           });
                           mc_barang.proxy.extraParams.OrgType = Ext.getCmp('OrgType').getValue();
                           mc_barang.proxy.extraParams.OrgID = Ext.getCmp('OrgID').getValue();
                        }
                     }
                   }
               },{
                   fieldLabel: lang('Supplier'),
                   id: 'SupplierID',
                   name: 'SupplierID',
                   xtype: 'combo',
                   store: mc_supplier,
                   displayField: 'label',
                   valueField: 'id',
                   queryMode: 'local',
                   listeners: {
                      beforequery: function(record){  
                          record.query = new RegExp(record.query, 'i');
                          record.forceAll = true;
                      },
                     change: function (cb, nv, ov) {
                        if (this.value=='-1') {
                           displayFormSupplier();
                           Ext.getCmp('SuppOrgType').setValue(Ext.getCmp('OrgType').getValue());
                           Ext.getCmp('SuppOrgID').setValue(Ext.getCmp('OrgID').getValue());
                        }
                     }
                   }
               }]
            },{ // right fieldset
                columnWidth: .52,
                height: '100%',
                layout: 'form',
                xtype: 'fieldset',
                title: 'Pembayaran',
                style:'margin-left:12px',
                items: [{
                       xtype: 'numericfield',
                       fieldLabel: lang('Total'),
                       id: 'Total',
                       name: 'Total',
                       readOnly: true,
                       labelCls: 'biggertext',
                        fieldCls:'biggertext'
                   },{
                       xtype: 'numericfield',
                       fieldLabel: lang('Pembayaran'),
                       id: 'Pay',
                       name: 'Pay',
                       labelCls: 'biggertext',
                        fieldCls:'biggertext',
                        hideTrigger: true,
                        keyNavEnabled: false,
                        mouseWheelEnabled: false,
                               listeners: {
                        change: function (cb, nv, ov) {
                           Ext.getCmp('Kekurangan').setValue(Ext.getCmp('Total').getValue()-Ext.getCmp('Pay').getValue())
                        },
                        specialkey: function(f,e){
                            if (e.getKey() == e.ENTER) {
                               var form = this.up('form').getForm();
                               var method;
                               if (Ext.getCmp('PurchaseID').getValue()!='') method = 'PUT'; else method = 'POST';
                               form.submit({
                                   url: m_crud,
                                   method:method,
                                   waitMsg: 'Sending data...',
                                   success: function(fp, o) {
                                        win.hide(this, function() {
                                       store.load({
                                           params: {
                                               Awal: Ext.getCmp('searchAwal').getSubmitValue(),
                                               Akhir: Ext.getCmp('searchAkhir').getSubmitValue(),
                                               OrgType: Ext.getCmp('searchOrgType').getValue(),
                                               OrgID: Ext.getCmp('searchOrgID').getValue(),
                                           }
                                       });
                                        });
                                        preview_cetak_surat(m_crud+'_cetak/'+Ext.getCmp('PurchaseID').getValue());
                                        win.hide();
                                   }
                               });                                 
                            }
                        }
                       }
                   },{
                       xtype: 'numericfield',
                       fieldLabel: lang('Kekurangan'),
                       id: 'Kekurangan',
                       name: 'Kekurangan',
                       labelCls: 'biggertext',
                        fieldCls:'biggertext',
                       readOnly: true
                }]
            }]                  
        },{
           xtype: 'gridpanel',
           id: 'grid_detail',
           features: [{
               ftype: 'summary'
           }],
           store: store_detail,
           width: '100%',
           loadMask: true,
           selType: 'rowmodel',
           dockedItems: [{
               xtype: 'toolbar',
               items: [{
                   icon: varjs.config.base_url + 'images/icons/new/add.png', cls:'Sfr_BtnGridGreen', overCls:'Sfr_BtnGridGreen-Hover',
                   text: lang('Add'),
                   cls: m_act_add,
                   scope: this,
                   handler: function () {
                       RowEditing.cancelEdit();
                       var r = Ext.create('detail.Model', {DetailID: '',PurchaseID: '',name:'',InventoryID: '',
                           Qty: '',Price: '',Total:''});
                       store_detail.insert(0, r);
                       RowEditing.startEdit(0, 0);
                   }
               }, {
                   icon: varjs.config.base_url + 'images/icons/new/update.png',
                   cls: m_act_update,
                   text: lang('Edit'),
                   scope: this,
                   handler: function () {
                       RowEditing.cancelEdit();
                       var sm = Ext.getCmp('grid_detail').getSelectionModel().getSelection();
                       RowEditing.startEdit(sm[0].index, 0);
                   }
               }, {
                   itemId: 'remove',
                   icon: varjs.config.base_url + 'images/icons/new/delete.png',
                   text: lang('Hapus'),
                   scope: this,
                   handler: function () {
                       var smb = Ext.getCmp('grid_detail').getSelectionModel().getSelection()[0];
                       RowEditing.cancelEdit();
                       Ext.MessageBox.confirm('Message', 'Apakah anda mau menghapus data ini ?', function (btn) {
                           if (btn == 'yes') {
                                Ext.Ajax.request({
                                   waitMsg: 'Please Wait',
                                   url: m_crud+'_detail',
                                   method: 'DELETE',
                                   params: {
                                       id: smb.raw.DetailID
                                   },
                                   success: function(response, opts) {
                                      var obj = Ext.decode(response.responseText);
                                      switch (obj.success) {
                                      case true:
                                      store_detail.load({
                                         params: {
                                             id: Ext.getCmp('PurchaseID').getValue()
                                         }
                                      });
                                      break;
                                      default:
                                      Ext.MessageBox.alert('Warning', obj.message);
                                      break;
                                      }
                                   },
                                   failure: function(response, opts) {
                                      var obj = Ext.decode(response.responseText);
                                      Ext.MessageBox.alert('error', 'Could not connect to the database. Retry later');
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
               width: '5%'
           }, {
               dataIndex: 'InventoryID',
               hidden:true,
               editor: {
                   xtype: 'textfield',
                   id:'InventoryID',
                   name:'InventoryID',
               }
           }, {
               text: lang('Barang'),
               dataIndex: 'name',
               width: '48%',
               editor: {
                  xtype: 'combo',
                  store: mc_barang,
                  id:'name',
                  displayField: 'label',
                  valueField: 'id',
                  typeAhead: false,
                  hideLabel: true,
                  hideTrigger:true,
                  anchor: '100%',
                  listConfig: {
                      loadingText: 'Searching...',
                      emptyText: lang('No matching data found.'),
                      getInnerTpl: function() {
                          return '<div class="search-item">' +
                              '{id} - {name}' +
                              '{excerpt}' +
                          '</div>';
                      }
                  },
                  pageSize: 10,
                  listeners: {
                      select: function(combo, selection) {
                          var post = selection[0];
                          if (post) {
                              Ext.getCmp('harga').setValue(post.get('Price'))
                              Ext.getCmp('InventoryID').setValue(post.get('id'))
                          }
                      }
                  }
               }
           }, {
               text: lang('Qty'),
               dataIndex: 'Qty',
               width: '15%',
               editor: {
                   xtype: 'textfield',
                   allowBlank: false,
                   listeners: {
                       change: function (cb, nv, ov) {
                           Ext.getCmp('total').setValue(this.value*Ext.getCmp('harga').getValue())
                       }
                   }
               }
           }, {
               text: lang('Price'),
               dataIndex: 'Price',
               width: '15%',
               editor: {
                   xtype: 'textfield',
                   id:'harga',
                   allowBlank: false
               }
           }, {
               text: lang('Total'),
               dataIndex: 'Total',
               width: '15%',
               summaryType: 'sum',
               summaryRenderer: function(value, summaryData, dataIndex) {
                  var total = value;
                  Ext.getCmp('Total').setValue(total);
               },
               editor: {
                   xtype: 'textfield',
                   allowBlank: false,
                   id:'total',
               }
           }],
           plugins: [RowEditing],
           listeners: {
               'canceledit': function (editor, e, eOpts) {
                   store_detail.load({
                       params: {
                           id: Ext.getCmp('PurchaseID').getValue()
                       }
                   });
               },
               'edit': function (editor, e) {
                   if (e.record.data.DetailID == '') {
                       if (Ext.getCmp('PurchaseID').getValue()=='') {
                         var form = this.up('form').getForm();
                         form.submit({
                             url: m_crud,
                             method:'POST',
                             waitMsg: 'Sending data...',
                             success: function(fp, o) {
                                 Ext.getCmp('PurchaseID').setValue(o.result.PurchaseID)
                                 save_detail(e)
                             }
                         });
                       } else save_detail(e)
                   } else {
                       Ext.MessageBox.confirm('Message', 'Update data ini ?', function (btn) {
                           if (btn == 'yes') {
                               Ext.Ajax.request({
                                   waitMsg: 'Please wait...',
                                   url: m_crud+'_detail',
                                   method: 'PUT',
                                   params: {
                                     DetailID: e.record.data.DetailID,
                                     name: e.record.data.name,
                                     InventoryID: Ext.getCmp('InventoryID').getValue(),
                                     Qty: e.record.data.Qty,
                                     Price: e.record.data.Price
                                   },
                                   success: function (response, opts) {
                                       var obj = Ext.decode(response.responseText);
                                       switch (obj.success) {
                                           case true:
                                               Ext.MessageBox.alert('Success', obj.message);
                                               store_detail.load({
                                                   params: {
                                                       id: Ext.getCmp('PurchaseID').getValue()
                                                   }
                                               });
                                               break;
                                           default:
                                               Ext.MessageBox.alert('Warning', obj.message);
                                               break;
                                       }
                                   },
                                   failure: function (response, opts) {
                                       var obj = Ext.decode(response.responseText);
                                       Ext.MessageBox.alert('error', 'Could not connect to the database. Retry later');
                                   }
                               });
                           }
                       });
                   }
               }
           }
        }],
        buttons: [{
            id:'printButton',
            text: lang('Cetak'),
            margin: '5px',
            scale: 'large',
            ui: 's-button',
            cls: 's-blue',
            hidden:true,
            handler: function() {
               preview_cetak_surat(m_crud+'_cetak/'+Ext.getCmp('PurchaseID').getValue());
            }
        },{
            id:'saveButton',
            text: lang('Bayar'),
            margin: '5px',
            scale: 'large',
            ui: 's-button',
            cls: 's-blue',
            handler: function() {
                var form = this.up('form').getForm();
                var method;
                if (Ext.getCmp('PurchaseID').getValue()!='') method = 'PUT'; else method = 'POST';
                form.submit({
                    url: m_crud,
                    method:method,
                    waitMsg: 'Sending data...',
                    success: function(fp, o) {
                        Ext.MessageBox.alert('Success', 'Data saved.');
                    }
                });
                win.hide(this, function() {
                  store.load({
                      params: {
                          Awal: Ext.getCmp('searchAwal').getSubmitValue(),
                          Akhir: Ext.getCmp('searchAkhir').getSubmitValue(),
                          OrgType: Ext.getCmp('searchOrgType').getValue(),
                          OrgID: Ext.getCmp('searchOrgID').getValue(),
                      }
                  });
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
    //supplier
    function displayFormSupplier(){
        if(!winDetail.isVisible()){
            DataFormSupplier.getForm().reset();
            winDetail.show();
        } else {
            winDetail.hide(this, function() {});
            winDetail.toFront();
        }
    }
   Ext.define("Post", {
        extend: 'Ext.data.Model',
        proxy: {
            type: 'ajax',
            url : m_crud+'_farmer',
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
    var DataFormSupplier = Ext.create('Ext.form.Panel', {
        frame: false,
        height: 300,
        width: '70%',
        bodyPadding: 5,
        autoScroll:true,
        id:'dataFormSupplier',
        fieldDefaults: {
            labelAlign: 'left',
            labelWidth: 100,
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
                    id: 'SuppOrgType',
                    name: 'SuppOrgType',
                    hidden:true
               },{
                    xtype: 'textfield',
                    id: 'SuppOrgID',
                    name: 'SuppOrgID',
                    hidden:true
               },{
                  xtype: 'combo',
                  store: mc_farmer,
                  hidden:true,
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
                              //Ext.getCmp('SupplierName').setValue(post.get('FarmerName'))
                              Ext.getCmp('SuppEmail').setValue(post.get('Email'))
                              Ext.getCmp('SuppPhone').setValue(post.get('Phone'))
                              Ext.getCmp('SuppAddress').setValue(post.get('Address'))
                              Ext.getCmp('SuppNote').setValue(post.get('Note'))
                              Ext.getCmp('Provinsi').setValue(post.get('Provinsi'))
                              Ext.getCmp('Kabupaten').setValue(post.get('Kabupaten'))
                              Ext.getCmp('Kecamatan').setValue(post.get('Kecamatan'))
                              Ext.getCmp('Desa').setValue(post.get('VillageID').toString())
                          }
                      }
                  }
               },{
                    fieldLabel: lang('Name'),
                    xtype: 'textfield',
                    id: 'SuppName',
                    name: 'SuppName'
               },{
                    fieldLabel: lang('Email'),
                    xtype: 'textfield',
                    id: 'SuppEmail',
                    name: 'SuppEmail'
               },{
                    fieldLabel: lang('Phone'),
                    xtype: 'textfield',
                    id: 'SuppPhone',
                    name: 'SuppPhone'
               },{
                    fieldLabel: lang('Note'),
                    xtype: 'textareafield',
                    id: 'SuppNote',
                    name: 'SuppNote',
                     height: 50,
                     width: 200,
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
                    xtype: 'textfield',
                    id: 'SupplierAddress',
                    name: 'SupplierAddress'
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
            id:'saveButtonSupplier',
            text: lang('Save'),
            margin: '5px',
            scale: 'large',
            ui: 's-button',
            cls: 's-blue',
            handler: function() {
                var form = this.up('form').getForm();
                form.submit({
                    url: m_api+'/bussiness/barang_supplier',
                    method:'POST',
                    waitMsg: 'Sending data...',
                    success: function(fp, o) {
                        winDetail.hide()
                        mc_supplier.load({
                           params: {
                              'OrgType': Ext.getCmp('OrgType').getValue(),
                              'OrgID': Ext.getCmp('OrgID').getValue()
                           },
                           scope: this,
                           callback: function(records, operation, success) {
                              Ext.getCmp('SupplierID').setValue(o.result.SupplierID)
                           }
                        });
                    }
                });
                /*winDetail.hide(this, function() {
                  mc_supplier.load({
                       params: {
                           'OrgType': Ext.getCmp('OrgType').getValue(),
                           'OrgID': Ext.getCmp('OrgID').getValue()
                       }
                  });
                  //Ext.getCmp('SupplierID').setValue()
                });*/
                
            }
        },{
            text: lang('Close'),
            margin: '5px',
            scale: 'large',
            ui: 's-button',
            cls: 's-grey',
            disabled: false,
            handler: function() {
                winDetail.hide();
            }
        }]
    });
    var winDetail = Ext.create('widget.window', {
        title: lang('Supplier'),
        id:'winDetail',
        closable: true,
        modal:true,
        closeAction: 'show',
        width: '70%',
        height: 310,
        layout: {
            type: 'fit'
        },
        items: [DataFormSupplier],
        listeners:{
            'close':function(){
                //clearItemselector();
            }
        }
    });
    //Supplier    
    var win = Ext.create('widget.window', {
        title: lang('Pembelian'),
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

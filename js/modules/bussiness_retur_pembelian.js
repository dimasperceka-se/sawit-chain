Ext.onReady(function(){
    var store = Ext.create('Ext.data.Store', {
        extend: 'Ext.data.Model',
        fields: ['PurchaseID','OrgID','OrgType','Date','PurchaseNumber','Number','SupplierID','Name','Total','Pembayaran'],
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
       //if (sm.get('Pembayaran')>0) return;
       Ext.Ajax.request({
           url: m_crud,
           method: 'GET',
           params: {PurchaseID: sm.get('PurchaseID')},
           success: function(fp, o){
               var r = Ext.decode(fp.responseText);
               Ext.getCmp('Date').setValue(sm.get('Date'));
               Ext.getCmp('PurchaseID').setValue(sm.get('PurchaseID'));
               Ext.getCmp('name_purchase').setValue(sm.get('PurchaseNumber'));
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
   Ext.define("Post_purchase", {
        extend: 'Ext.data.Model',
        proxy: {
            type: 'ajax',
            url : m_crud+'_pembelian',
            reader: {
                type: 'json',
                root: 'data',
                totalProperty: 'total'
            }
        },
        fields: [
            {name: 'PurchaseID', mapping: 'PurchaseID'},
            {name: 'SupplierID', mapping: 'SupplierID'},
            {name: 'Total', mapping: 'Total'},
            {name: 'Number', mapping: 'Number'}
        ]
    });
    var mc_purchase = Ext.create('Ext.data.Store', {
        pageSize: 10,
        model: 'Post_purchase'
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
            text: lang('Purchase Number'),
            width: '15%',
            dataIndex: 'PurchaseNumber'
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
            width: '15%',
            dataIndex: 'Name'
        },{
            text: lang('Total'),
            width: '15%',
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
                 title: 'Retur Pembelian',
                 height: '100%',
                 items: [{
                    xtype: 'textfield',
                    id: 'PurchaseID',
                    name: 'PurchaseID',
                    hidden:true
                },{
                    xtype: 'textfield',
                    id: 'ReturPurchaseID',
                    name: 'ReturPurchaseID',
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
                           mc_purchase.proxy.extraParams.OrgType = Ext.getCmp('OrgType').getValue();
                           mc_purchase.proxy.extraParams.OrgID = Ext.getCmp('OrgID').getValue();
                        }
                     }
                   }
               },{
                  fieldLabel: lang('Purchase'),
                  xtype: 'combo',
                  store: mc_purchase,
                  id:'name_purchase',
                  name:'PurchaseNumber',
                  displayField: 'Number',
                  valueField: 'Number',
                  typeAhead: false,
                  hideTrigger:true,
                  anchor: '100%',
                  listConfig: {
                      loadingText: 'Searching...',
                      emptyText: lang('No matching data found.'),
                      getInnerTpl: function() {
                          return '<div class="search-item">' +
                              '{Number}' +
                              '{excerpt}' +
                          '</div>';
                      }
                  },
                  pageSize: 10,
                  listeners: {
                      select: function(combo, selection) {
                          var post = selection[0];
                          if (post) {
                              Ext.getCmp('ReturPurchaseID').setValue(post.get('PurchaseID'))
                              Ext.getCmp('SupplierID').setValue(post.get('SupplierID'))
                              store_detail.load({
                                   params: {
                                       'ReturPurchaseID': post.get('PurchaseID')
                                   }
                              });                              
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
                       hidden:true,
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
            text: lang('Save'),
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
  
    var win = Ext.create('widget.window', {
        title: lang('Retur Pembelian'),
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

Ext.onReady(function(){
    var store = Ext.create('Ext.data.Store', {
        extend: 'Ext.data.Model',
        fields: ['SaleID','OrgID','OrgType','Date','Number','CustomerID','Name','Total','Pembayaran'],
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
           params: {SaleID: sm.get('SaleID')},
           success: function(fp, o){
               var r = Ext.decode(fp.responseText);//$ConsultationProblem,$ConsultationSolution,$ConsultationDateEnd,$Total,$SaleID
               //console.log(sm)
               Ext.getCmp('Date').setValue(sm.get('Date'));
               Ext.getCmp('SaleID').setValue(sm.get('SaleID'));
               Ext.getCmp('OrgType').setValue(sm.get('OrgType'));
               Ext.getCmp('OrgID').setValue(sm.get('OrgID'));
               Ext.getCmp('CustomerID').setValue(sm.get('CustomerID'));
               Ext.getCmp('Total').setValue(sm.get('Total'));
               Ext.getCmp('Pay').setValue(sm.get('Pembayaran'));
               store_detail.load({
                    params: {
                        'id': Ext.getCmp('SaleID').getValue()
                    }
               });
           }
       });
    }
    //detail
    Ext.define('detail.Model', {
        extend: 'Ext.data.Model',
        fields: ['DetailID','SaleID','name','InventoryID',{name:'Qty',type:'float'},{name:'Price',type:'float'},
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
            url: m_crud+'_org',
            reader: {
                type: 'json',
                root: 'data'
            }
        }
    });
    var mc_customer = Ext.create('Ext.data.Store', {
        extend: 'Ext.data.Model',
        fields: ['id', 'label'],
        autoLoad: false,
        pageSize: 10,
        proxy: {
            type: 'ajax',
            url: m_crud+'_customer',
            reader: {
                type: 'json',
                root: 'data'
            }
        }
    });
    var mc_barang = Ext.create('Ext.data.Store', {
        extend: 'Ext.data.Model',
        fields: ['id', 'label'],
        autoLoad: false,
        pageSize: 10,
        proxy: {
            type: 'ajax',
            url: m_crud+'_barang',
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
            url : m_crud+'_barang',
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
                SaleID: Ext.getCmp('SaleID').getValue(),
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
                                id: Ext.getCmp('SaleID').getValue()
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
                    Ext.getCmp('CustomerID').setValue()
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
                icon: varjs.config.base_url+'images/icons/silk/pencil.png',
                text: lang('Retur'),
                scope: this,
                handler : function(){
                  var sm = Ext.getCmp('grid').getSelectionModel().getSelection()[0];
                  link(m_retur+sm.get('SaleID'));
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
                        params: {id:  smb.raw.SaleID},
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
            text: lang('Customer'),
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
                 title: 'Penjualan',
                 height: '100%',
                 items: [{
                    xtype: 'textfield',
                    id: 'SaleID',
                    name: 'SaleID',
                    hidden:true
                },{
                  xtype: 'datefield',
                  fieldLabel: lang('Tanggal'),
                  id: 'Date',
                  name: 'Date',
                  format: 'Y-m-d H:i:s',
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
                           mc_customer.load({
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
                   fieldLabel: lang('Customer'),
                   id: 'CustomerID',
                   name: 'CustomerID',
                   xtype: 'combo',
                   store: mc_customer,
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
                           displayFormCust();
                           Ext.getCmp('CustOrgType').setValue(Ext.getCmp('OrgType').getValue());
                           Ext.getCmp('CustOrgID').setValue(Ext.getCmp('OrgID').getValue());
                        }
                     }
                   }
               },{
                    fieldLabel: lang('Problem'),
                    xtype: 'textarea',
                    height: 40,
                    id: 'ConsultationProblem',
                    name: 'ConsultationProblem',
                    hidden:true
               },{
                    fieldLabel: lang('Solution'),
                    xtype: 'textarea',
                    height: 40,
                    id: 'ConsultationSolution',
                    name: 'ConsultationSolution',
                    hidden:true
               },{
                    xtype: 'datefield',
                    fieldLabel: lang('Tanggal Akhir'),
                    id: 'ConsultationDateEnd',
                    name: 'ConsultationDateEnd',
                    format: 'Y-m-d',
                    hidden:true
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
                           Ext.getCmp('Kembalian').setValue(Ext.getCmp('Pay').getValue()-Ext.getCmp('Total').getValue())
                        },
                        specialkey: function(f,e){
                            if (e.getKey() == e.ENTER) {
                               var form = this.up('form').getForm();
                               var method;
                               if (Ext.getCmp('SaleID').getValue()!='') method = 'PUT'; else method = 'POST';
                               form.submit({
                                   url: m_crud,
                                   method:method,
                                   waitMsg: 'Sending data...',
                                   success: function(fp, o) {
                                        win.hide(this, function() {
                                            store.load();
                                        });
                                        preview_cetak_surat(m_crud+'_cetak/'+Ext.getCmp('SaleID').getValue());
                                        win.hide();
                                   }
                               });                                 
                            }
                        }
                       }
                   },{
                       xtype: 'numericfield',
                       fieldLabel: lang('Kembalian'),
                       id: 'Kembalian',
                       name: 'Kembalian',
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
                       var r = Ext.create('detail.Model', {DetailID: '',SaleID: '',name:'',InventoryID: '',
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
                                             id: Ext.getCmp('SaleID').getValue()
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
                           id: Ext.getCmp('SaleID').getValue()
                       }
                   });
               },
               'edit': function (editor, e) {
                   if (e.record.data.DetailID == '') {
                       if (Ext.getCmp('SaleID').getValue()=='') {
                         var form = this.up('form').getForm();
                         form.submit({
                             url: m_crud,
                             method:'POST',
                             waitMsg: 'Sending data...',
                             success: function(fp, o) {
                                 Ext.getCmp('SaleID').setValue(o.result.SaleID)
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
                                                       id: Ext.getCmp('SaleID').getValue()
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
               preview_cetak_surat(m_crud+'_cetak/'+Ext.getCmp('SaleID').getValue());
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
                if (Ext.getCmp('SaleID').getValue()!='') method = 'PUT'; else method = 'POST';
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
    //cust
    function displayFormCust(){
        if(!winDetail.isVisible()){
            DataFormCust.getForm().reset();
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
    var DataFormCust = Ext.create('Ext.form.Panel', {
        frame: false,
        height: 300,
        width: '70%',
        bodyPadding: 5,
        autoScroll:true,
        id:'dataFormCust',
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
                    id: 'CustOrgType',
                    name: 'CustOrgType',
                    hidden:true
               },{
                    xtype: 'textfield',
                    id: 'CustOrgID',
                    name: 'CustOrgID',
                    hidden:true
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
                              Ext.getCmp('CustName').setValue(post.get('FarmerName'))
                              Ext.getCmp('CustEmail').setValue(post.get('Email'))
                              Ext.getCmp('CustPhone').setValue(post.get('Phone'))
                              Ext.getCmp('CustAddress').setValue(post.get('Address'))
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
                    id: 'CustName',
                    name: 'CustName'
               },{
                    fieldLabel: lang('Email'),
                    xtype: 'textfield',
                    id: 'CustEmail',
                    name: 'CustEmail'
               },{
                    fieldLabel: lang('Phone'),
                    xtype: 'textfield',
                    id: 'CustPhone',
                    name: 'CustPhone'
               },{
                    fieldLabel: lang('Note'),
                    xtype: 'textareafield',
                    id: 'CustNote',
                    name: 'CustNote',
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
                    id: 'CustAddress',
                    name: 'CustAddress'
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
            id:'saveButtonCust',
            text: lang('Save'),
            margin: '5px',
            scale: 'large',
            ui: 's-button',
            cls: 's-blue',
            handler: function() {
                var form = this.up('form').getForm();
                form.submit({
                    url: m_crud+'_customer',
                    method:'POST',
                    waitMsg: 'Sending data...',
                    success: function(fp, o) {
                        winDetail.hide()
                        mc_customer.load({
                           params: {
                              'OrgType': Ext.getCmp('OrgType').getValue(),
                              'OrgID': Ext.getCmp('OrgID').getValue()
                           },
                           scope: this,
                           callback: function(records, operation, success) {
                              Ext.getCmp('CustomerID').setValue(o.result.CustomerID)
                           }
                        });
                    }
                });
                /*winDetail.hide(this, function() {
                  mc_customer.load({
                       params: {
                           'OrgType': Ext.getCmp('OrgType').getValue(),
                           'OrgID': Ext.getCmp('OrgID').getValue()
                       }
                  });
                  //Ext.getCmp('CustomerID').setValue()
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
        title: lang('Customer'),
        id:'winDetail',
        closable: true,
        modal:true,
        closeAction: 'show',
        width: '70%',
        height: 310,
        layout: {
            type: 'fit'
        },
        items: [DataFormCust],
        listeners:{
            'close':function(){
                //clearItemselector();
            }
        }
    });
    //Cust    
    var win = Ext.create('widget.window', {
        title: lang('Penjualan'),
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

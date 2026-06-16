Ext.require('Ext.tab.*');

Ext.onReady(function(){

      Ext.define('detail.Model', {
        extend: 'Ext.data.Model',
        fields: ['DetailID','PurchaseID','name',{name:'InventoryID',type:'int'},{name:'Qty',type:'float'},{name:'Price',type:'float'},
            {name:'Total',type:'float'}], 
    });

    var store_detail = Ext.create('Ext.data.Store', {
        model: 'detail.Model',
        autoLoad: false,
        pageSize: 10,
        proxy: {
            type: 'ajax',
            url: m_api + 'cooperatives/purchase_items',
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
            url: m_api+'/bussiness/penjualan_org/',
            reader: {
                type: 'json',
                root: 'data'
            }
        }
    });

    var mc_Supplier = Ext.create('Ext.data.Store', {
        extend: 'Ext.data.Model',
        fields: ['id', 'Name','Address'],
        autoLoad: false,
        pageSize: 10,
        proxy: {
            type: 'ajax',
            url: m_api+'/cooperatives/Suppliers',
            reader: {
                type: 'json',
                root: 'data'
            }
        }
    });

    var mc_barang = Ext.create('Ext.data.Store', {
        extend: 'Ext.data.Model',
        fields: ['InventoryID','Number', 'Name'],
        autoLoad: false,
        pageSize: 10,
        proxy: {
            type: 'ajax',
            url: m_api+'/cooperatives/getDataInventorys',
            reader: {
                type: 'json',
                root: 'data'
            }
        }
    });

//POP UP Supplier LIST
var storeCoaList = Ext.create('Ext.data.Store', {
    extend: 'Ext.data.Model',
    fields: ['id', 'label'],
//    autoLoad: true,
    pageSize: 50,
    proxy: {
        type: 'ajax',
        url: m_crud+'_Supplier',
        reader: {
            type: 'json',
            root: 'data',
            totalProperty: 'total'
        }
    }
});
    
// Ext.define('GridSupplierList', {
//     itemId: 'GridSupplierList',
//     id: 'GridSupplierList',
//     extend: 'Ext.grid.Panel',
//     alias: 'widget.GridSupplierList',
//     store: mc_Supplier,
//     loadMask: true,
//     columns: [
//     {
//             text: 'Pilih',
//             width: 65,
//             xtype: 'actioncolumn',
//             tooltip: 'Select',
//             align: 'center',
//             icon: varjs.config.base_url + '/images/icons/silk/add.png',
//             handler: function(grid, rowIndex, colIndex, actionItem, event, selectedRecord, row) {
//                 // console.log(Ext.getCmp('typeSupplier').getValue().typeSupplierRb);
//                     Ext.getCmp('SupplierID').setValue(selectedRecord.data.id);
//                     Ext.getCmp('SupplierName').setValue(selectedRecord.data.Name);
//                     // Ext.getCmp('SupplierTypeID').setValue(Ext.getCmp('typeSupplier').getValue().typeSupplierRb);
//                     Ext.getCmp('wSupplierPopup').hide();
//             }
//         },
//         { text: 'id', dataIndex: 'id', hidden: true },
//         { text: 'Nama', flex:1, width: '25%', dataIndex: 'Name' },
//         { text: 'Alamat', width: '75%', dataIndex: 'Address' }        
//     ]
//     , dockedItems: [
//         {
//                xtype: 'toolbar',
//                items: [{
//                         xtype: 'radiogroup',
//                         fieldLabel: 'Jenis Supplier',
//                         hidden:true,
//                         id:'typeSupplier',
//                         items: [
//                             {boxLabel: 'Member', name: 'typeSupplierRb', inputValue: 1, width:100, checked:true},
//                             {boxLabel: 'Non Member', name: 'typeSupplierRb', inputValue: 2, width:100}
//                         ],
//                         listeners: {
//                             change: function(radiogroup, radio){
//                                 mc_Supplier.load( {
//                                         params: {
//                                             Type: Ext.getCmp('typeSupplier').getValue().typeSupplierRb,
//                                             Name: Ext.getCmp('CariNamaSupplier').getValue()
//                                         }
//                                     });        
//                             }
//                         }
//                     },{
//                         xtype:'textfield',
//                         margin:'0px 0px 0px 115px',
//                         id:'CariNamaSupplier',
//                         fieldLabel:'Cari Nama',
//                         listeners: {
//                           specialkey: function(f,e){
//                             if (e.getKey() == e.ENTER) {
//                                 // console.log()
//                                  mc_Supplier.load(
//                                     {
//                                         params: {
//                                             Type: Ext.getCmp('typeSupplier').getValue().typeSupplierRb,//member
//                                             Name: this.value
//                                         }
//                                     }
//                                  );
//                             }
//                           }
//                         },
//                         handler:function()
//                         {
                          
//                         }
//                     },
//                     {
//                         xtype:'button',
//                         text:'Cari'
//                     },'->',
//                     {
//                         xtype:'button',
//                         hidden:true,
//                         text:'Tambah Supplier'
//                     }]
//         }
//     ]
// });

var typeSupplierID = Ext.id();
var CariNamaSupplierID = Ext.id();

var gridSupplier = Ext.create('Ext.grid.Panel', {
        itemId: 'markGrid',
        store: mc_Supplier,
        loadMask: true,
        width: 400,
        columns: [
          {
              text: 'Pilih',
              width: 65,
              xtype: 'actioncolumn',
              tooltip: 'Select',
              align: 'center',
              icon: varjs.config.base_url + '/images/icons/silk/add.png',
              handler: function(grid, rowIndex, colIndex, actionItem, event, selectedRecord, row) {
                  // console.log(Ext.getCmp('typeCustomerSales').getValue().typeCustomerSalesRb);
                      Ext.getCmp('SupplierID').setValue(selectedRecord.data.id);
                      Ext.getCmp('SupplierName').setValue(selectedRecord.data.Name);
                    // Ext.getCmp('SupplierTypeID').setValue(Ext.getCmp('typeSupplier').getValue().typeSupplierRb);
                      Ext.getCmp('wSupplierPopup').hide();
              }
          },
          { text: 'id', dataIndex: 'id', hidden: true },
          { text: 'Nama', flex:1, width: '25%', dataIndex: 'Name' },
          { text: 'Alamat', width: '75%', dataIndex: 'Address' }        
        ],
        dockedItems: [
        {
               xtype: 'toolbar',
               items: [{
                        xtype: 'radiogroup',
                        fieldLabel: 'Jenis Supplier',
                        hidden:true,
                        id:typeSupplierID,
                        items: [
                            {boxLabel: 'Member', name: 'typeSupplierRb', inputValue: 1, width:100, checked:true},
                            {boxLabel: 'Non Member', name: 'typeSupplierRb', inputValue: 2, width:100}
                        ],
                        listeners: {
                            change: function(radiogroup, radio){
                                mc_Supplier.load( {
                                        params: {
                                            Type: Ext.getCmp(typeSupplierID).getValue().typeSupplierRb,
                                            Name: Ext.getCmp(CariNamaSupplierID).getValue()
                                        }
                                    });        
                            }
                        }
                    },{
                        xtype:'textfield',
                        margin:'0px 0px 0px 115px',
                        id:CariNamaSupplierID,
                        fieldLabel:'Cari Nama',
                        listeners: {
                          specialkey: function(f,e){
                            if (e.getKey() == e.ENTER) {
                                // console.log()
                                 mc_Supplier.load(
                                    {
                                        params: {
                                            Type: Ext.getCmp(typeSupplierID).getValue().typeSupplierRb,//member
                                            Name: this.value
                                        }
                                    }
                                 );
                            }
                          }
                        },
                        handler:function()
                        {
                          
                        }
                    },
                    {
                        xtype:'button',
                        text:'Cari'
                    },'->',
                    {
                        xtype:'button',
                        hidden:true,
                        text:'Tambah Pelanggan'
                    }]
        }
    ]
});

    var wSupplierPopup = Ext.create('widget.window', {
        id: 'wSupplierPopup',
        title: 'Pilih Supplier',
        closable: true,
        closeAction: 'hide',
    //    autoWidth: true,
         width: 970,
        modal:true,
        height: 430,
        layout: 'fit',
        border: false,
        items: [gridSupplier]
    });
//END POP UP Supplier LIST

////GRID purchase
var storepurchase = Ext.create('Ext.data.Store', {
        extend: 'Ext.data.Model',
        fields: ['PurchaseID','OrgID','OrgType','Date','Number','SupplierID','Name','Total','Pembayaran','SisaBayar','Pajak','SupplierName','JournalMemo','MemberID','Diskon','DueDate'],
        autoLoad: true,
        pageSize: 50,
        proxy: {
            type: 'ajax',
            url: m_api+'/cooperatives/purchase_list',
            reader: {
                type: 'json',
                root: 'data',
                totalProperty: 'total'
            }
        }
    });

var storeHutang = Ext.create('Ext.data.Store', {
        extend: 'Ext.data.Model',
        fields: ['PurchaseID','OrgID','OrgType','Date','Number','SupplierID','Name','Total','Pembayaran','SisaBayar','Pajak','SupplierName','JournalMemo','MemberID','Diskon','DueDate'],
        autoLoad: true,
        pageSize: 50,
        proxy: {
            type: 'ajax',
            url: m_api+'/cooperatives/Hutang_list',
            reader: {
                type: 'json',
                root: 'data',
                totalProperty: 'total'
            }
        }
});

function displayFormWindow(){
    if(!win.isVisible()){
        // DataFormpurchase.getForm().reset();
        win.show();
    } else {
        win.hide(this, function() {});
        win.toFront();
    }
}

    var DataFormpurchase = Ext.create('Ext.form.Panel', {
        frame: false,
        height: 330,
        width: '100%',
        bodyPadding: 5,
        autoScroll:true,
        id:'DataFormpurchase',
        fieldDefaults: {
            labelAlign: 'left',
            labelWidth: 100,
            anchor: '100%'
        },
        items: [{
            layout: 'hbox',
            items: [{
                    items: [
                        {
                            xtype:'datefield',
                            labelWidth:150,
                            name:'Date',
                            id:'tglpurchase',
                            fieldLabel:'Tanggal Pembelian'       
                        },
                        {
                            xtype:'textfield',
                            labelWidth:150,
                            minWidth:600,
                            name:'memo',
                            id:'memo',
                            fieldLabel:'Memo'       
                        }
                    ]
                },
                {
                    margin: '0px 0px 0px 20px',
                    items: [
                        {
                            xtype:'hiddenfield',
                            id:'PurchaseID',
                            name:'PurchaseID'
                        },
                        {
                            xtype:'hiddenfield',
                            id:'SupplierID',
                            name:'SupplierID'
                        },
                        {
                            xtype:'hiddenfield',
                            id:'SupplierTypeID',
                            name:'SupplierTypeID'
                        },
                         {
                            // 
                            xtype:'textfield',
                            labelWidth:100,
                            id:'SupplierName',
                            name:'SupplierName',
                            fieldLabel:'Supplier',
                               listeners: {
                                   render: function(component) {
                                       component.getEl().on('click', function(event, el) {
                                            wSupplierPopup.show();
                                            mc_Supplier.load(
                                                {
                                                    params: {
                                                        Type: Ext.getCmp('searchOrgType').getValue(),//member
                                                        OrgID: Ext.getCmp('searchOrgID').getValue(),
                                                        Name: this.value
                                                    }
                                                }
                                             );
                                       });
                                   }
                               }
                        }
                    ]
                }
            ]
        },{
           xtype: 'gridpanel',
           id: 'grid_detail',
           features: [{
               ftype: 'summary'
           }],
           store: store_detail,
           width: '100%',
           height:260,
           loadMask: true,
           selType: 'rowmodel',
           viewConfig: {
               markDirty: false
           },
           dockedItems: [{
               xtype: 'toolbar',
               id:'ToolbarDetailpurchase',
               items: [{
                   icon: varjs.config.base_url + 'images/icons/new/add.png', cls:'Sfr_BtnGridGreen', overCls:'Sfr_BtnGridGreen-Hover',
                   text: lang('Add'),
                   cls: m_act_add,
                   scope: this,
                   handler: function () {
                       RowEditing.cancelEdit();
                       var r = Ext.create('detail.Model', {DetailID: '',PurchaseID: '',name:'',InventoryID: '',Qty: '',Price: '',Total:''});
                       store_detail.insert(0, r);
                       RowEditing.startEdit(0, 0);
                   }
               }, {
                   icon: varjs.config.base_url + 'images/icons/new/update.png',
                   cls: m_act_update,
                   hidden:true,
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
                   hidden:true,
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
                  displayField: 'Name',
                  valueField: 'Name',
                  typeAhead: false,
                  hideLabel: true,
                  hideTrigger:true,
                  anchor: '100%',
                  listConfig: {
                      loadingText: 'Searching...',
                      emptyText: lang('No matching data found.'),
                      getInnerTpl: function() {
                          return '<div class="search-item">' +
                              '{Number} - {Name}' +
                              '{excerpt}' +
                          '</div>';
                      }
                  },
                  pageSize: 10,
                  listeners: {
                      select: function(combo, selection) {
                          var post = selection[0];
                          if (post) {
                              // Ext.getCmp('harga').setValue(post.get('sellingprice'))
                              Ext.getCmp('InventoryID').setValue(post.get('InventoryID'))
                          }
                      }
                  }
               }
           },  {
               text: lang('Harga Satuan'),
               dataIndex: 'Price',
               align:'right',
               width: '15%',
               editor: {
                   xtype: 'textfield',
                   id:'harga',
                   allowBlank: false,
                   listeners: {
                       change: function (cb, nv, ov) {
                           Ext.getCmp('total').setValue(this.value*Ext.getCmp('qty').getValue())
                           calcSubtotal()
                       }
                   }
               }
           },{
               text: lang('Qty'),
               dataIndex: 'Qty',
               width: '15%',
               align:'right',
               editor: {
                   xtype: 'textfield',
                   id:'qty',
                   allowBlank: false,
                   listeners: {
                       change: function (cb, nv, ov) {
                           Ext.getCmp('total').setValue(this.value*Ext.getCmp('harga').getValue())
                           calcSubtotal()
                       }
                   }
               }
           }, {
               text: lang('Total'),
               dataIndex: 'Total',
               align:'right',
               width: '15%',
               style: 'text-align: right',
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
                // console.log(e)
                    calcSubtotal(e.store.data.items);
                   // if (e.record.data.DetailID == '') {
                   //     if (Ext.getCmp('PurchaseID').getValue()=='') {
                   //       var form = this.up('form').getForm();
                   //       form.submit({
                   //           url: m_crud,
                   //           method:'POST',
                   //           waitMsg: 'Sending data...',
                   //           success: function(fp, o) {
                   //               Ext.getCmp('PurchaseID').setValue(o.result.PurchaseID)
                   //               save_detail(e)
                   //           }
                   //       });
                   //     } else save_detail(e)
                   // } else {
                   //     Ext.MessageBox.confirm('Message', 'Update data ini ?', function (btn) {
                   //         if (btn == 'yes') {
                   //             Ext.Ajax.request({
                   //                 waitMsg: 'Please wait...',
                   //                 url: m_crud+'_detail',
                   //                 method: 'PUT',
                   //                 params: {
                   //                   DetailID: e.record.data.DetailID,
                   //                   name: e.record.data.name,
                   //                   InventoryID: Ext.getCmp('InventoryID').getValue(),
                   //                   Qty: e.record.data.Qty,
                   //                   Price: e.record.data.Price
                   //                 },
                   //                 success: function (response, opts) {
                   //                     var obj = Ext.decode(response.responseText);
                   //                     switch (obj.success) {
                   //                         case true:
                   //                             Ext.MessageBox.alert('Success', obj.message);
                   //                             store_detail.load({
                   //                                 params: {
                   //                                     id: Ext.getCmp('PurchaseID').getValue()
                   //                                 }
                   //                             });
                   //                             break;
                   //                         default:
                   //                             Ext.MessageBox.alert('Warning', obj.message);
                   //                             break;
                   //                     }
                   //                 },
                   //                 failure: function (response, opts) {
                   //                     var obj = Ext.decode(response.responseText);
                   //                     Ext.MessageBox.alert('error', 'Could not connect to the database. Retry later');
                   //                 }
                   //             });
                   //         }
                   //     });
                   // }
               }
           }
        },
        {
            layout: 'hbox',
            items: [{
                    items: [
                       {
                          xtype: 'radiogroup',
                          fieldLabel: 'Pembayaran',
                          labelWidth:150,
                          id:'typeBayar',
                          items: [
                              {boxLabel: 'Tunai', name: 'typeBayarRb', inputValue: 1, width:100, checked:true},
                              {boxLabel: 'Kredit', name: 'typeBayarRb', inputValue: 2, width:100}
                          ],
                          listeners: {
                              change: function(radiogroup, radio){
                                   if(Ext.getCmp('typeBayar').getValue().typeBayarRb==1)
                                   {
                                      Ext.getCmp('duedate').hide();
                                   } else {
                                      Ext.getCmp('duedate').show();
                                   }
                              }
                          }
                      },
                        {
                          xtype:'datefield',
                          labelWidth:150,
                          format: 'd/m/Y',
                          id:'duedate',
                          name:'duedate',
                          hidden:true,
                          fieldLabel:'Tanggal Pelunasan'
                        },
                         {
                            xtype:'textareafield',
                            labelWidth:150,
                            minWidth:500,
                            name:'remark',
                            fieldLabel:'Catatan'
                        }
                    ]
                },
                {
                    margin: '0px 0px 0px 290px',
                    items: [
                         {
                            
                            xtype:'numberfield',
                            hideTrigger:true,
                            readOnly:true,
                            fieldStyle: 'text-align: right;',
                            id:'Subtotal',
                            labelWidth:100,
                            name:'subtotal',
                            fieldLabel:'Subtotal'      
                        },{
                            
                             xtype:'numberfield',
                            hideTrigger:true,
                           fieldStyle: 'text-align: right;',
                            labelWidth:100,
                            id:'pajak',
                            fieldLabel:'Pajak',
                            name:'pajak',
                            listeners: {
                               change: function (cb, nv, ov) {
                                   calcGrandTotal()
                               }
                            }      
                        },{
                            
                             xtype:'numberfield',
                            hideTrigger:true,
                           fieldStyle: 'text-align: right;',
                            labelWidth:100,
                            id:'diskon',
                            fieldLabel:'Diskon',
                            name:'diskon',
                            listeners: {
                               change: function (cb, nv, ov) {
                                   calcGrandTotal()
                               }
                            }      
                        },{
                            
                             xtype:'numberfield',
                            hideTrigger:true,
                            readOnly:true,
                           fieldStyle: 'text-align: right;',
                            id:'grandtotal',
                            labelWidth:100,
                            name:'grandtotal',
                            fieldLabel:'Grand Total'      
                        },{
                            
                             xtype:'numberfield',
                            hideTrigger:true,
                            fieldStyle: 'text-align: right;',
                            labelWidth:100,
                            id:'totalbayar',
                            fieldLabel:'Total Bayar',
                            name:'totalbayar',
                            listeners: {
                               change: function (cb, nv, ov) {
                                   calcSisa()
                               }
                            }       
                        },
                        {
                            
                             xtype:'numberfield',
                            hideTrigger:true,
                            fieldStyle: 'text-align: right;',
                            labelWidth:100,
                            id:'pelunasan',
                            fieldLabel:'Pelunasan',
                            name:'pelunasan',
                            listeners: {
                               change: function (cb, nv, ov) {
                                   calcPelunasan()
                               }
                            }       
                        },{
                            
                            xtype:'numberfield',
                            hideTrigger:true,
                            fieldStyle: 'text-align: right;',
                            id:'sisabayar',
                            readOnly:true,
                            labelWidth:100,
                            name:'sisabayar',
                            fieldLabel:'Sisa Bayar'      
                        },
                        {
                          xtype:'hiddenfield',
                          id:'sisabayartmp'
                        },
                        {
                          xtype:'hiddenfield',
                          name:'formtype',
                          id:'formtype'
                        }
                    ]
                }
            ]
        }],
        buttons: [{
            id:'saveButton',
            text: lang('Simpan'),
            margin: '5px',
            scale: 'large',
            ui: 's-button',
            cls: 's-blue',
            handler: function() {
                var griditem = Ext.encode(Ext.pluck(store_detail.data.items, 'data'));
                var form = this.up('form').getForm();
                var method;
                if (Ext.getCmp('PurchaseID').getValue()!='') method = 'PUT'; else method = 'POST';
                form.submit({
                    url: m_crud,
                    method:method,
                    params: {
                            griditem: griditem,
                            OrgType:Ext.getCmp('searchOrgType').getValue(),
                            OrgID:Ext.getCmp('searchOrgID').getValue()
                    },
                    waitMsg: 'Sending data...',
                    success: function(fp, o) {
                        Ext.MessageBox.alert('Success', 'Data saved.');
                        storepurchase.load();
                        storeHutang.load();
                    }
                });

                win.hide(this, function() {
                    // store.load();
                });
                
            }
        },{
            id:'printButton',
            text: lang('Simpan dan Cetak'),
            margin: '5px',
            hidden:true,
            scale: 'large',
            ui: 's-button',
            cls: 's-blue',
            handler: function() {
               // preview_cetak_surat(m_crud+'_cetak/'+Ext.getCmp('PurchaseID').getValue());
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
                    store.load();
                });
            }
        },{
            text: lang('Cancel'),
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
        title: lang('Pembelian'),
        id:'win',
        closable: true,
        modal:true,
        closeAction: 'show',
        width: 1100,
        height: 630,
        layout: {
            type: 'fit'
        },
        items: [DataFormpurchase],
        listeners:{
            'close':function(){
                //clearItemselector();
            }
        }
    });

//end form purchase

//GRID purchase
var gridpurchase = Ext.create('Ext.grid.Panel', {
        store: storepurchase,
        width: '100%',
        title:'Pembelian',
        minHeight: 450,
        id:'gridpurchase',
        // style: 'border:1px solid #CCC;',
        loadMask: true,
        selType: 'rowmodel',
        listeners : {
            itemdblclick: function(dv, record, item, index, e) {
                displayFormWindow();
                var sm = record;
                set_data_purchase(sm);
            }
        },
        dockedItems: [{
            xtype: 'pagingtoolbar',
            store: storepurchase,   
            dock: 'bottom',
            displayInfo: true
        },{
            xtype: 'toolbar',
            items: [{
                icon: varjs.config.base_url + 'images/icons/new/add.png', cls:'Sfr_BtnGridGreen', overCls:'Sfr_BtnGridGreen-Hover',
                text: lang('Add'),
                scope: this,
                handler : function(){
                   Ext.getCmp('DataFormpurchase').getForm().reset();

                    displayFormWindow();
                    store_detail.removeAll();
                    Ext.getCmp('pelunasan').hide();
                    Ext.getCmp('pajak').setReadOnly(false);
                    Ext.getCmp('diskon').setReadOnly(false);
                    Ext.getCmp('totalbayar').setReadOnly(false);

                    Ext.getCmp('Subtotal').show();
                    Ext.getCmp('ToolbarDetailpurchase').show();

                    Ext.getCmp('formtype').setValue('inputbaru');
                    // Ext.getCmp('OrgType').setValue()
                    // Ext.getCmp('OrgID').setValue()
                    // Ext.getCmp('SupplierID').setValue()
                },
                cls : m_act_add
            },{
                icon: varjs.config.base_url+'images/icons/silk/pencil.png',
                text: lang('Update'),
                scope: this,
                hidden:true,
                handler : function(){
                  displayFormWindow();
                  var sm = Ext.getCmp('gridpurchase').getSelectionModel().getSelection()[0];
                  set_data_purchase(sm)
                   Ext.getCmp('ToolbarDetailpurchase').show();
                },
                cls : m_act_update
            },{
                icon: varjs.config.base_url+'images/icons/silk/pencil.png',
                text: lang('Retur'),
                hidden:true,
                scope: this,
                handler : function(){
                  var sm = Ext.getCmp('grid').getSelectionModel().getSelection()[0];
                  link(m_retur+sm.get('PurchaseID'));
                },
                cls : m_act_update
            },{
               itemId: 'remove',
               icon: varjs.config.base_url+'images/icons/silk/delete.png',
               cls:m_act_delete,
               text: lang('Hapus'),
               scope: this,
               handler : function(){
                 var smb = Ext.getCmp('gridpurchase').getSelectionModel().getSelection()[0];
                 Ext.MessageBox.confirm('Message', lang('Apakah anda mau menghapus data ini ?') , function(btn){
                  // console.log(smb.data)
                     if(btn == 'yes'){
                        Ext.Ajax.request({
                        waitMsg: lang('Please Wait'),
                        url: m_crud,
                        method : 'DELETE',
                        params: {id:  smb.data.PurchaseID},
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
                    emptyText: lang('Org Type'),
                   id: 'searchOrgType',
                   name: 'searchOrgType',
                   xtype: 'combo',
                   hidden:true,
                   // value:'koperasi',
                   store: mc_org_type,
                   displayField: 'label',
                   width:120,
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
                   width:280,
                   store: mc_org_id,
                   displayField: 'label',
                   readOnly:true,
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
                  storepurchase.load({
                      params: {
                          Awal: Ext.getCmp('searchAwal').getSubmitValue(),
                          Akhir: Ext.getCmp('searchAkhir').getSubmitValue(),
                          // OrgType: Ext.getCmp('searchOrgType').getValue(),
                          // OrgID: Ext.getCmp('searchOrgID').getValue(),
                      }
                  });
              }
          }]
        }],        
        columns: [
        {
          text:'PurchaseID',
          dataIndex:'PurchaseID',
          hidden:true,
        },
        {
          text:'JournalMemo',
          dataIndex:'JournalMemo',
          hidden:true,
        },    
        {
          text:'MemberID',
          dataIndex:'MemberID',
          hidden:true,
        },             
        {
            text: lang('No'),
            xtype: 'rownumberer',
            width:'5%'
        },{
            text: lang('Number'),
            width: '15%',
            dataIndex: 'Number'
        },{
            text: lang('Org Type'),
            hidden:true,
            width: '10%',
            dataIndex: 'OrgType'
        },{
            text: lang('Org ID'),
            hidden:true,
            width: '10%',
            dataIndex: 'OrgID'
        },{
            text: lang('Date'),
            width: '10%',
            dataIndex: 'Date'
        },{
            text: lang('Supplier'),
            flex:2,
            width: '20%',
            dataIndex: 'SupplierName'
        },{
            text: lang('Total'),
            width: '13%',
            dataIndex: 'Total',
            align:'right',
            xtype:'numbercolumn',
            format:'0,000.00'
        },{
            text: lang('Dibayar'),
            width: '13%',
            dataIndex: 'Pembayaran',
            align:'right',
            xtype:'numbercolumn',
            format:'0,000.00'
        },{
            text: lang('Sisa Bayar'),
            width: '13%',
            dataIndex: 'SisaBayar',
            align:'right',
            xtype:'numbercolumn',
            format:'0,000.00'
        }]
    });


////END GRID purchase

//GRID Hutang
var gridHutang = Ext.create('Ext.grid.Panel', {
        store: storeHutang,
        width: '100%',
        title:'Hutang',
        minHeight: 450,
        id:'gridHutang',
        // style: 'border:1px solid #CCC;',
        loadMask: true,
        selType: 'rowmodel',
        listeners : {
            itemdblclick: function(dv, record, item, index, e) {
                displayFormWindow();
                var sm = record;
                set_data_purchase(sm);
            }
        },
        dockedItems: [{
            xtype: 'pagingtoolbar',
            store: storeHutang,   
            dock: 'bottom',
            displayInfo: true
        },{
            xtype: 'toolbar',
            items: [
            {
                icon: varjs.config.base_url+'images/icons/silk/pencil.png',
                text: lang('Bayar'),
                scope: this,
                handler : function(){
                  displayFormWindow();
                  var sm = Ext.getCmp('gridHutang').getSelectionModel().getSelection()[0];
                  set_data_purchase(sm)
                  Ext.getCmp('ToolbarDetailpurchase').hide();

                  Ext.getCmp('formtype').setValue('pelunasan');
                  
                },
                cls : m_act_update
            },{
                  emptyText: lang('Org ID'),
                   id: 'searchOrgIDHutang',
                   name: 'searchOrgID',
                   xtype: 'combo',
                   width:280,
                   store: mc_org_id,
                   displayField: 'label',
                   readOnly:true,
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
              id: 'searchAwalHutang',
              format: 'Y-m-d',
              emptyText: lang('Awal'),
          }, {
              xtype: 'label',
              text: ' s.d '
          },{
                   width:100,
              xtype: 'datefield',
              name: 'searchAkhir',
              id: 'searchAkhirHutang',
              format: 'Y-m-d',
              emptyText: lang('Akhir'),
          }, {
              xtype: 'button',
              margin: '0px 0px 0px 6px',
              text: 'Search',
              handler: function () {
                  storepurchase.load({
                      params: {
                          Awal: Ext.getCmp('searchAwalHutang').getSubmitValue(),
                          Akhir: Ext.getCmp('searchAkhirHutang').getSubmitValue(),
                          // OrgType: Ext.getCmp('searchOrgType').getValue(),
                          // OrgID: Ext.getCmp('searchOrgID').getValue(),
                      }
                  });
              }
          }]
        }],        
        columns: [
        {
          text:'PurchaseID',
          dataIndex:'PurchaseID',
          hidden:true,
        },{
            text: lang('No'),
            xtype: 'rownumberer',
            width:'5%'
        },{
            text: lang('Number'),
            width: '15%',
            dataIndex: 'Number'
        },{
            text: lang('Org Type'),
            hidden:true,
            width: '10%',
            dataIndex: 'OrgType'
        },{
            text: lang('Org ID'),
            hidden:true,
            width: '10%',
            dataIndex: 'OrgID'
        },{
            text: lang('Date'),
            width: '10%',
            dataIndex: 'Date'
        },{
            text: lang('Due Date'),
            width: '10%',
            dataIndex: 'DueDate'
        },{
            text: lang('Supplier'),
            flex:2,
            width: '20%',
            dataIndex: 'SupplierName'
        },{
            text: lang('Total'),
            width: '13%',
            dataIndex: 'Total',
            align:'right',
            xtype:'numbercolumn',
            format:'0,000.00'
        },{
            text: lang('Dibayar'),
            width: '13%',
            dataIndex: 'Pembayaran',
            align:'right',
            xtype:'numbercolumn',
            format:'0,000.00'
        },{
            text: lang('Sisa Bayar'),
            width: '13%',
            dataIndex: 'SisaBayar',
            align:'right',
            xtype:'numbercolumn',
            format:'0,000.00'
        }]
    });
//END GRID Hutang

mc_org_id.load({
         params: {
             'OrgType': Ext.getCmp('searchOrgType').getValue()
         }
    });


 var tabpurchase = Ext.widget('tabpanel', {
        renderTo: 'ext-content',
        plain:true,
        autoWidth:true,
        activeTab: 0,
        defaults :{
            // bodyPadding: 10
        },
        items: [gridpurchase,gridHutang]
    });

///functiin 
function calcSubtotal(d)
{
    // console.log(d)
    // Ext.getCmp('Subtotal').setValue(12312321);
    var SubTotal = 0;
    // d.each(function(d){
    //     // data.push(rec.get('field'));
    //     console.log(d)
    //     SubTotal+=d.data.Total;
    // });

    d.forEach(function(v) {
        console.log(v);
        SubTotal+=v.data.Total;
    });
    Ext.getCmp('Subtotal').setValue(SubTotal);
}

function calcGrandTotal()
{
    var Subtotal = Ext.getCmp('Subtotal').getValue()*1;
    var pajak = Ext.getCmp('pajak').getValue()*1;
    var diskon = Ext.getCmp('diskon').getValue()*1;
    
    Ext.getCmp('grandtotal').setValue((Subtotal+pajak)-diskon);
}

function calcSisa()
{
    var grandtotal = Ext.getCmp('grandtotal').getValue()*1;
    var totalbayar = Ext.getCmp('totalbayar').getValue()*1;

    Ext.getCmp('sisabayar').setValue(grandtotal-totalbayar);
}

function calcPelunasan()
{
    var sisabayar = Ext.getCmp('sisabayartmp').getValue()*1;
    var pelunasan = Ext.getCmp('pelunasan').getValue()*1;

    Ext.getCmp('sisabayar').setValue(sisabayar-pelunasan);
}

function set_data_purchase(data)
{
  // console.log(data);
  Ext.getCmp('pelunasan').show();
  Ext.getCmp('Subtotal').hide();

  Ext.getCmp('SupplierName').setValue(data.data.SupplierName);
  Ext.getCmp('tglpurchase').setValue(data.data.Date);
  Ext.getCmp('memo').setValue(data.data.JournalMemo);
  Ext.getCmp('PurchaseID').setValue(data.data.PurchaseID);
  Ext.getCmp('SupplierID').setValue(data.data.MemberID);
  Ext.getCmp('SupplierTypeID').setValue(1);

  // Ext.getCmp('Subtotal').setValue(data.data.MemberID);
  Ext.getCmp('pajak').setValue(data.data.Pajak);
  Ext.getCmp('pajak').setReadOnly(true);
  Ext.getCmp('diskon').setValue(data.data.Diskon);
  Ext.getCmp('diskon').setReadOnly(true);
  Ext.getCmp('grandtotal').setValue(data.data.Total);
  Ext.getCmp('totalbayar').setValue(data.data.Pembayaran);
  Ext.getCmp('totalbayar').setReadOnly(true);
  Ext.getCmp('sisabayar').setValue(data.data.SisaBayar);
  Ext.getCmp('sisabayartmp').setValue(data.data.SisaBayar);
  
  if(data.data.DueDate!==null)
  {
    var duedate = Ext.getCmp('duedate');
    duedate.show();
    if(data.data.DueDate!==null)
    {
      var strdd = data.data.DueDate;
      var dd = strdd.split('-');
      var tglnya = dd[2]+'/'+dd[1]+'/'+dd[0];
      duedate.setValue(tglnya);

      // Ext.getCmp('typeBayar').setValue(2);
      // var val = {rb : 2};
      Ext.getCmp('typeBayar').setValue({typeBayarRb : 2});
    } else {
      Ext.getCmp('typeBayar').setValue({typeBayarRb : 1});
    }
   
  } 

  Ext.getCmp('pelunasan').setValue(0);

  store_detail.load({
                  params:{
                    PurchaseID:data.data.PurchaseID
                  }
                });
}

function disable_field(opt)
{
  Ext.getCmp('tglpurchase').setDisabled(opt);
  Ext.getCmp('memo').setDisabled(opt);
  Ext.getCmp('SupplierName').setDisabled(opt);
  Ext.getCmp('tglpurchase').setDisabled(opt);
  Ext.getCmp('tglpurchase').setDisabled(opt);
  Ext.getCmp('tglpurchase').setDisabled(opt);
}

if(m_OrgType!==null) { Ext.getCmp('searchOrgType').setValue(m_OrgType); }
if(m_OrgID!==null) { Ext.getCmp('searchOrgID').setValue(m_OrgID); }
if(m_OrgID!==null) { Ext.getCmp('searchOrgIDHutang').setValue(m_OrgID); }


});
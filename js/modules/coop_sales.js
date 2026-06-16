Ext.require('Ext.tab.*');

Ext.onReady(function(){

      Ext.define('detail.Model', {
        extend: 'Ext.data.Model',
        fields: ['DetailID','SaleID','name',{name:'InventoryID',type:'int'},{name:'Qty',type:'float'},{name:'Price',type:'float'},
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

    var mc_customer = Ext.create('Ext.data.Store', {
        extend: 'Ext.data.Model',
        fields: ['id', 'name','address'],
        autoLoad: false,
        pageSize: 10,
        proxy: {
            type: 'ajax',
            url: m_api+'/cooperatives/customers',
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

//POP UP CUSTOMER LIST
var storeCoaList = Ext.create('Ext.data.Store', {
    extend: 'Ext.data.Model',
    fields: ['id', 'label'],
//    autoLoad: true,
    pageSize: 50,
    proxy: {
        type: 'ajax',
        url: m_crud+'_customer',
        reader: {
            type: 'json',
            root: 'data',
            totalProperty: 'total'
        }
    }
});
    
// Ext.define('GridCustomerList', {
//     itemId: 'GridCustomerList',
//     id: 'GridCustomerList',
//     extend: 'Ext.grid.Panel',
//     alias: 'widget.GridCustomerList',
//     store: mc_customer,
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
//                 console.log(Ext.getCmp('typeCustomerSales').getValue().typeCustomerSalesRb);
//                     Ext.getCmp('CustomerID').setValue(selectedRecord.data.id);
//                     Ext.getCmp('CustomerName').setValue(selectedRecord.data.name);
//                     Ext.getCmp('CustomerTypeID').setValue(Ext.getCmp('typeCustomerSales').getValue().typeCustomerSalesRb);
//                     wCustomerPopup.hide();
//             }
//         },
//         { text: 'id', dataIndex: 'id', hidden: true },
//         { text: 'Nama', flex:1, width: '25%', dataIndex: 'name' },
//         { text: 'Alamat', width: '75%', dataIndex: 'address' }        
//     ]
//     , dockedItems: [
//         {
//                xtype: 'toolbar',
//                items: [{
//                         xtype: 'radiogroup',
//                         fieldLabel: 'Jenis Pelanggan',
//                         id:'typeCustomerSales',
//                         items: [
//                             {boxLabel: 'Member', name: 'typeCustomerSalesRb', inputValue: 1, width:100, checked:true},
//                             {boxLabel: 'Non Member', name: 'typeCustomerSalesRb', inputValue: 2, width:100}
//                         ],
//                         listeners: {
//                             change: function(radiogroup, radio){
//                                 mc_customer.load( {
//                                         params: {
//                                             Type: Ext.getCmp('typeCustomerSales').getValue(),
//                                             Name: Ext.getCmp('CariNama').getValue()
//                                         }
//                                     });        
//                             }
//                         }
//                     },{
//                         xtype:'textfield',
//                         margin:'0px 0px 0px 115px',
//                         // id:'CariNama',
//                         fieldLabel:'Cari Nama',
//                         listeners: {
//                           specialkey: function(f,e){
//                             if (e.getKey() == e.ENTER) {
//                                 // console.log()
//                                  mc_customer.load(
//                                     {
//                                         params: {
//                                             Type: Ext.getCmp('typeCustomerSales').getValue(),//member
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
//                         text:'Tambah Pelanggan'
//                     }]
//         }
//     ]
// });
var cmbTypeCustomer = Ext.id();
var CariNamaID = Ext.id();

var gridCustomer = Ext.create('Ext.grid.Panel', {
        itemId: 'markGrid',
        store: mc_customer,
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
                      Ext.getCmp('CustomerID').setValue(selectedRecord.data.id);
                      Ext.getCmp('CustomerName').setValue(selectedRecord.data.name);
                      Ext.getCmp('CustomerTypeID').setValue(Ext.getCmp(cmbTypeCustomer).getValue().typeCustomerSalesRb);
                      wCustomerPopup.hide();
              }
          },
          { text: 'id', dataIndex: 'id', hidden: true },
          { text: 'Nama', flex:1, width: '25%', dataIndex: 'name' },
          { text: 'Alamat', width: '75%', dataIndex: 'address' }  
        ],
        dockedItems: [
        {
               xtype: 'toolbar',
               items: [{
                        xtype: 'radiogroup',
                        fieldLabel: 'Jenis Pelanggan',
                        id:cmbTypeCustomer,
                        items: [
                            {boxLabel: 'Member', name: 'typeCustomerSalesRb', inputValue: 1, width:100, checked:true},
                            {boxLabel: 'Non Member', name: 'typeCustomerSalesRb', inputValue: 2, width:100}
                        ],
                        listeners: {
                            change: function(radiogroup, radio){
                                mc_customer.load( {
                                        params: {
                                            Type: Ext.getCmp(cmbTypeCustomer).getValue(),
                                            Name: Ext.getCmp(CariNamaID).getValue()
                                        }
                                    });        
                            }
                        }
                    },{
                        xtype:'textfield',
                        margin:'0px 0px 0px 115px',
                        id:CariNamaID,
                        fieldLabel:'Cari Nama',
                        listeners: {
                          specialkey: function(f,e){
                            if (e.getKey() == e.ENTER) {
                                // console.log()
                                 mc_customer.load(
                                    {
                                        params: {
                                            Type: Ext.getCmp(cmbTypeCustomer).getValue(),//member
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

    var wCustomerPopup = Ext.create('widget.window', {
        title: 'Pilih Pelanggan',
        closable: true,
        closeAction: 'hide',
    //    autoWidth: true,
         width: 970,
        modal:true,
        height: 430,
        layout: 'fit',
        border: false,
        items:[gridCustomer]
        // items: [{
        //         xtype:'GridCustomerList'
        // }]
    });

function displayFormWindowCustomerSales(){
    if(!wCustomerPopup.isVisible()){
        wCustomerPopup.show();
    } else {
        wCustomerPopup.hide(this, function() {});
        wCustomerPopup.toFront();
    }
}
//END POP UP CUSTOMER LIST

////GRID SALES
var storeSales = Ext.create('Ext.data.Store', {
        extend: 'Ext.data.Model',
        fields: ['SaleId','OrgID','OrgType','Date','Number','CustomerID','Name','Total','Pembayaran','SisaBayar','Pajak','CustomerName','JournalMemo','MemberID','Diskon'],
        autoLoad: true,
        pageSize: 50,
        proxy: {
            type: 'ajax',
            url: m_api+'/cooperatives/sales_list',
            reader: {
                type: 'json',
                root: 'data',
                totalProperty: 'total'
            }
        }
    });

var storePiutang = Ext.create('Ext.data.Store', {
        extend: 'Ext.data.Model',
        fields: ['SaleId','OrgID','OrgType','Date','Number','CustomerID','Name','Total','Pembayaran','SisaBayar','Pajak','CustomerName','JournalMemo','MemberID','Diskon'],
        autoLoad: true,
        pageSize: 50,
        proxy: {
            type: 'ajax',
            url: m_api+'/cooperatives/piutang_list',
            reader: {
                type: 'json',
                root: 'data',
                totalProperty: 'total'
            }
        }
});

function displayFormWindow(){
    if(!win.isVisible()){
        DataFormSales.getForm().reset();
        win.show();
    } else {
        win.hide(this, function() {});
        win.toFront();
    }
}

    var DataFormSales = Ext.create('Ext.form.Panel', {
        frame: false,
        height: 330,
        width: '100%',
        bodyPadding: 5,
        autoScroll:true,
        id:'DataFormSales',
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
                            id:'tglSales',
                            fieldLabel:'Tanggal Penjualan'       
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
                            id:'SaleID',
                            name:'SaleID'
                        },
                        {
                            xtype:'hiddenfield',
                            id:'CustomerID',
                            name:'CustomerID'
                        },
                        {
                            xtype:'hiddenfield',
                            id:'CustomerTypeID',
                            name:'CustomerTypeID'
                        },
                         {
                            // 
                            xtype:'textfield',
                            labelWidth:100,
                            id:'CustomerName',
                            name:'CustomerName',
                            fieldLabel:'Pelanggan',
                               listeners: {
                                   render: function(component) {
                                       component.getEl().on('click', function(event, el) {
                                            // wCustomerPopup.show();
                                            displayFormWindowCustomerSales();
                                            mc_customer.load(
                                                {
                                                    params: {
                                                        Type: Ext.getCmp(cmbTypeCustomer).getValue(),//member
                                                        Name: Ext.getCmp(CariNamaID).getValue()
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
               id:'ToolbarDetailSales',
               items: [{
                   icon: varjs.config.base_url + 'images/icons/new/add.png', cls:'Sfr_BtnGridGreen', overCls:'Sfr_BtnGridGreen-Hover',
                   text: lang('Add'),
                   cls: m_act_add,
                   scope: this,
                   handler: function () {
                       RowEditing.cancelEdit();
                       var r = Ext.create('detail.Model', {DetailID: '',SaleID: '',name:'',InventoryID: '',Qty: '',Price: '',Total:''});
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

                        console.log(smb.raw.DetailID)
                       if(Ext.getCmp('SaleID').getValue()===null)
                       {
                        //masih baru input
                       } else {
                        //saat edit
                       }
                       RowEditing.cancelEdit();
                       // Ext.MessageBox.confirm('Message', 'Apakah anda mau menghapus data ini ?', function (btn) {
                       //     if (btn == 'yes') {
                       //          Ext.Ajax.request({
                       //             waitMsg: 'Please Wait',
                       //             url: m_crud+'_detail',
                       //             method: 'DELETE',
                       //             params: {
                       //                 id: smb.raw.DetailID
                       //             },
                       //             success: function(response, opts) {
                       //                var obj = Ext.decode(response.responseText);
                       //                switch (obj.success) {
                       //                case true:
                       //                store_detail.load({
                       //                   params: {
                       //                       id: Ext.getCmp('SaleID').getValue()
                       //                   }
                       //                });
                       //                break;
                       //                default:
                       //                Ext.MessageBox.alert('Warning', obj.message);
                       //                break;
                       //                }
                       //             },
                       //             failure: function(response, opts) {
                       //                var obj = Ext.decode(response.responseText);
                       //                Ext.MessageBox.alert('error', 'Could not connect to the database. Retry later');
                       //             }
                       //          });
                       //     }
                       // });
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
               width: '7%',
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
           },
           {
                        menuDisabled: true,
                        id:'actkolomsales',
                        sortable: false,
                        xtype: 'actioncolumn',
                        width: 80,
                        align: 'center',
                        items: [
                           {
                                icon: m_baseurl + '/images/delete.png',
                                tooltip: lang('Delete'),
                                handler: function(grid, rowIndex, colIndex) {

                                    if(Ext.getCmp('SaleID').getValue()===null)
                                    {

                                    } else {
                                      var selection = Ext.getCmp('grid_detail').getView().getSelectionModel().getSelection()
                                      store_detail.remove(selection);  
                                    }
                                    
                                    

                                }
                            },
                        ]
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
                // console.log(e)
                    calcSubtotal(e.store.data.items);
                   // if (e.record.data.DetailID == '') {
                   //     if (Ext.getCmp('SaleID').getValue()=='') {
                   //       var form = this.up('form').getForm();
                   //       form.submit({
                   //           url: m_crud,
                   //           method:'POST',
                   //           waitMsg: 'Sending data...',
                   //           success: function(fp, o) {
                   //               Ext.getCmp('SaleID').setValue(o.result.SaleID)
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
                   //                                     id: Ext.getCmp('SaleID').getValue()
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
                            xtype:'textareafield',
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
                if (Ext.getCmp('SaleID').getValue()!='') method = 'PUT'; else method = 'POST';
                form.submit({
                    url: m_crud,
                    method:method,
                    params: {
                            griditem: griditem,
                            searchOrgType:Ext.getCmp('searchOrgTypePiutang').getValue(),
                            searchOrgID:Ext.getCmp('searchOrgIDPiutang').getValue()
                    },
                    waitMsg: 'Sending data...',
                    success: function(fp, o) {
                        Ext.MessageBox.alert('Success', 'Data saved.');
                        storeSales.load();
                        storePiutang.load();
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
               // preview_cetak_surat(m_crud+'_cetak/'+Ext.getCmp('SaleID').getValue());
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
        title: lang('Penjualan'),
        id:'win',
        closable: true,
        modal:true,
        closeAction: 'show',
        width: 1100,
        height: 630,
        layout: {
            type: 'fit'
        },
        items: [DataFormSales],
        listeners:{
            'close':function(){
                //clearItemselector();
            }
        }
    });

//end form sales

//GRID SALES
var gridSales = Ext.create('Ext.grid.Panel', {
        store: storeSales,
        width: '100%',
        title:'Penjualan',
        minHeight: 450,
        id:'gridSales',
        // style: 'border:1px solid #CCC;',
        loadMask: true,
        selType: 'rowmodel',
        listeners : {
            itemdblclick: function(dv, record, item, index, e) {
                displayFormWindow();
                var sm = record;
                set_data_sales(sm);
            }
        },
        dockedItems: [{
            xtype: 'pagingtoolbar',
            store: storeSales,   
            dock: 'bottom',
            displayInfo: true
        },{
            xtype: 'toolbar',
            items: [{
                icon: varjs.config.base_url + 'images/icons/new/add.png', cls:'Sfr_BtnGridGreen', overCls:'Sfr_BtnGridGreen-Hover',
                text: lang('Add'),
                scope: this,
                handler : function(){
                    Ext.getCmp('DataFormSales').getForm().reset();

                    displayFormWindow();
                    store_detail.removeAll();
                    Ext.getCmp('pelunasan').hide();
                    Ext.getCmp('pajak').setReadOnly(false);
                    Ext.getCmp('diskon').setReadOnly(false);
                    Ext.getCmp('totalbayar').setReadOnly(false);

                    Ext.getCmp('Subtotal').show();
                    Ext.getCmp('ToolbarDetailSales').show();

                    Ext.getCmp('formtype').setValue('inputbaru');
                    // Ext.getCmp('OrgType').setValue()
                    // Ext.getCmp('OrgID').setValue()
                    // Ext.getCmp('CustomerID').setValue()
                },
                cls : m_act_add
            },{
                icon: varjs.config.base_url+'images/icons/silk/pencil.png',
                text: lang('Update'),
                scope: this,
                hidden:true,
                handler : function(){
                  displayFormWindow();
                  var sm = Ext.getCmp('gridSales').getSelectionModel().getSelection()[0];
                  set_data_sales(sm)
                   Ext.getCmp('ToolbarDetailSales').show();
                },
                cls : m_act_update
            },{
                icon: varjs.config.base_url+'images/icons/silk/pencil.png',
                text: lang('Retur'),
                hidden:true,
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
                 var smb = Ext.getCmp('gridSales').getSelectionModel().getSelection()[0];
                 Ext.MessageBox.confirm('Message', lang('Apakah anda mau menghapus data ini ?') , function(btn){
                  // console.log(smb.data)
                     if(btn == 'yes'){
                        Ext.Ajax.request({
                        waitMsg: lang('Please Wait'),
                        url: m_crud,
                        method : 'DELETE',
                        params: {id:  smb.data.SaleId},
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
                   value:'koperasi',
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
                  storeSales.load({
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
          text:'SaleId',
          dataIndex:'SaleId',
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
            text: lang('Customer'),
            flex:2,
            width: '20%',
            dataIndex: 'CustomerName'
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


////END GRID SALES

//GRID PIUTANG
var gridPiutang = Ext.create('Ext.grid.Panel', {
        store: storePiutang,
        width: '100%',
        title:'Piutang',
        minHeight: 450,
        id:'gridPiutang',
        // style: 'border:1px solid #CCC;',
        loadMask: true,
        selType: 'rowmodel',
        listeners : {
            itemdblclick: function(dv, record, item, index, e) {
                displayFormWindow();
                var sm = record;
                set_data_sales(sm);
            }
        },
        dockedItems: [{
            xtype: 'pagingtoolbar',
            store: storePiutang,   
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
                  var sm = Ext.getCmp('gridPiutang').getSelectionModel().getSelection()[0];
                  set_data_sales(sm)
                  Ext.getCmp('ToolbarDetailSales').hide();

                  Ext.getCmp('formtype').setValue('pelunasan');
                },
                cls : m_act_update
            },{              
                    emptyText: lang('Org Type'),
                   id: 'searchOrgTypePiutang',
                   name: 'searchOrgType',
                   xtype: 'combo',
                   hidden:true,
                   value:m_OrgType,
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
                   id: 'searchOrgIDPiutang',
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
              id: 'searchAwalPiutang',
              format: 'Y-m-d',
              emptyText: lang('Awal'),
          }, {
              xtype: 'label',
              text: ' s.d '
          },{
                   width:100,
              xtype: 'datefield',
              name: 'searchAkhir',
              id: 'searchAkhirPiutang',
              format: 'Y-m-d',
              emptyText: lang('Akhir'),
          }, {
              xtype: 'button',
              margin: '0px 0px 0px 6px',
              text: 'Search',
              handler: function () {
                  storeSales.load({
                      params: {
                          Awal: Ext.getCmp('searchAwalPiutang').getSubmitValue(),
                          Akhir: Ext.getCmp('searchAkhirPiutang').getSubmitValue(),
                          // OrgType: Ext.getCmp('searchOrgType').getValue(),
                          // OrgID: Ext.getCmp('searchOrgID').getValue(),
                      }
                  });
              }
          }]
        }],        
        columns: [
        {
          text:'SaleId',
          dataIndex:'SaleId',
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
            text: lang('Customer'),
            flex:2,
            width: '20%',
            dataIndex: 'CustomerName'
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
//END GRID PIUTANG

mc_org_id.load({
         params: {
             'OrgType': Ext.getCmp('searchOrgType').getValue()
         }
    });


 var tabSales = Ext.widget('tabpanel', {
        renderTo: 'ext-content',
        plain:true,
        autoWidth:true,
        activeTab: 0,
        defaults :{
            // bodyPadding: 10
        },
        items: [gridSales,gridPiutang]
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

function set_data_sales(data)
{
  console.log(data);
  Ext.getCmp('pelunasan').show();
  Ext.getCmp('Subtotal').hide();

  Ext.getCmp('CustomerName').setValue(data.data.CustomerName);
  Ext.getCmp('tglSales').setValue(data.data.Date);
  Ext.getCmp('memo').setValue(data.data.JournalMemo);
  Ext.getCmp('SaleID').setValue(data.data.SaleId);
  Ext.getCmp('CustomerID').setValue(data.data.MemberID);
  Ext.getCmp('CustomerTypeID').setValue(1);

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
  

  Ext.getCmp('pelunasan').setValue(0);
}

if(m_OrgID!==null) { Ext.getCmp('searchOrgID').setValue(m_OrgID); }
if(m_OrgID!==null) { Ext.getCmp('searchOrgIDPiutang').setValue(m_OrgID); }


});
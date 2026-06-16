Ext.onReady(function() {
    Ext.tip.QuickTipManager.init();
    var store = Ext.create('Ext.data.Store', {
        extend: 'Ext.data.Model',
        fields: ['OpnameID','Periode','Notes','CreatedDate','TotalAS','TotalCS','Difference'],
        autoLoad: true,
        pageSize: 50,
        proxy: {
            type: 'ajax',
            url: m_api + 'cooperatives/opname_list',
            reader: {
                type: 'json',
                root: 'data',
                totalProperty: 'total'
            }
        }
    });

    //detail
    Ext.define('detail.Model', {
        extend: 'Ext.data.Model',
        fields: ['InventoryID','Name','Number','Stock','CheckedStock'], 
    });
   
   // Ext.create('Ext.data.Store', {
   //      storeId:'inventory_items',
   //      fields:['InventoryID', 'Name', 'Stock'],
   //      data: [
   //          {"InventoryID":"Lisa", "Name":"lisa@simpsons.com", "Stock":1},
   //          {"InventoryID":"Bart", "Name":"bart@simpsons.com", "Stock":2},
   //          {"InventoryID":"Homer", "Name":"home@simpsons.com", "Stock":3},
   //          {"InventoryID":"Marge", "Name":"marge@simpsons.com", "Stock":4}
   //      ]
   //  });
   // var mc_barang = Ext.data.StoreManager.lookup('inventory_items');

    var mc_barang = Ext.create('Ext.data.Store', {
        model: 'detail.Model',
        autoLoad: false,
        pageSize: 10,
        proxy: {
            type: 'ajax',
            url: m_api + '/cooperatives/items_opname',
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

    function displayFormWindow() {
        if (!win.isVisible()) {
            DataForm.getForm().reset();
            win.show();
        } else {
            win.hide(this, function() {
            });
            win.toFront();
        }
    }

Ext.define('dataForm', {
        extend: 'Ext.form.Panel',
        id: 'dataForm',
//        title:'Inventory Form',
        alias: 'widget.dataForm',
        initComponent: function () {
            var frm = this;
            frm.bodyStyle = 'padding:5px';
           frm.width = 1050;
            // frm.autoWidth = true;
            frm.autoScroll = true;
           frm.height = 500;
            // frm.autoHeight = true;
            frm.fieldDefaults = {
                msgTarget: 'side',
                blankText: 'Tidak Boleh Kosong',
                labelWidth: 180,
                width: 460
            };
             frm.buttons = [
                 {
                id: 'saveButton',
                text: 'Save',
                margin: '5px',
                scale: 'large',
                ui: 's-button',
                cls: 's-blue',
                handler: function() {

                     var grid = Ext.getCmp('grid_detail');
                          selected = [];
                          Ext.each(grid.getStore().data.items, function(item) {
                              selected.push(item.data);
                      });


                    var form = this.up('form').getForm();
                    var methode;
                    if (Ext.getCmp('id').getValue() == '')
                        methode = 'POST';
                    else
                        methode = 'PUT';
                    form.submit({
                        url: m_api+'cooperatives/opname',
                        method: methode,
                        waitMsg: 'Sending data...',
                        params: {
                           data: Ext.encode(selected)
                        },
                        success: function(fp, o) {
                            Ext.MessageBox.alert('Success', 'Data saved.');
                            Ext.getCmp('WFormCheckStock').hide();
                            store.load();
                        }});
                }
            }, {
                text: 'Close',
                margin: '5px',
                scale: 'large',
                ui: 's-button',
                cls: 's-grey',
                disabled: false, handler: function() {
                    Ext.getCmp('WFormCheckStock').hide();
                }
            }];
        
            frm.items = [
                {
                    xtype: 'textfield',
                    id: 'OpnameID',
                    name: 'OpnameID',
                    inputType: 'hidden'
                },
                {
                  xtype: 'datefield',
                  fieldLabel: 'Periode',
                  name: 'periode',
                  id: 'periode',
                  format: 'd-m-Y'
                },{
                    xtype: 'textarea',
                    height:50,
                    allowBlank: false,
                    fieldLabel: 'Catatan',
                    name: 'notes'
                },
                {
                   xtype: 'gridpanel',
                   id: 'grid_detail',
                   store: mc_barang,
                   width: '100%',
                   minHeight:100,
                   loadMask: true,
                   selType: 'rowmodel',
                   columns: [{
                       text: lang('No'),
                       xtype: 'rownumberer',
                       width: '5%'
                   },
                   {
                       hidden:true,
                       dataIndex: 'InventoryID'
                   }, {
                       text: lang('Nama Barang'),
                       dataIndex: 'Name',
                       flex:1,
                       width: '30%'
                   },{
                       text: lang('Kode Barang'),
                       dataIndex: 'Number',
                       width: '12%'
                   },  {
                       text: lang('Stok Aktual'),
                       id:'Stock',
                       dataIndex: 'Stock'
                   }, {
                       text: lang('Stok Fisik'),
                       dataIndex: 'CheckedStock',
                       width: '10%',
                       editor: {
                           xtype: 'numberfield',
                           id:'CheckedStock',
                           allowBlank: false,
                           listeners: {
                               change: function (cb, nv, ov) {
                                   // Ext.getCmp('Difference').setValue(this.value*Ext.getCmp('Stock').getValue()*1)
                               }
                           }
                       }
                   }, {
                       text: lang('Selisih'),
                       id:'Difference',
                       hidden:true,
                       dataIndex: 'Difference'
                   },{
                       text: lang('Catatan'),
                       hidden:true,
                       flex:1,
                       dataIndex: 'Notes',
                       width: '15%',
                       editor: {
                           xtype: 'textfield',
                           id:'Notes',
                           // allowBlank: false
                       }
                   }],
                   dockedItems: [
                   {
                          xtype: 'pagingtoolbar',
                          store: mc_barang, 
                          dock: 'bottom',
                          displayInfo: true
                      }
                   ],
                  // {
                  //   xtype: 'toolbar',
                  //   dock: 'top',
                  //   items: [
                  //      {
                  //         xtype: 'pagingtoolbar',
                  //         store: mc_barang, 
                  //         dock: 'bottom',
                  //         displayInfo: true
                  //     }
                  //   ]
                  // },                 
                   plugins: [RowEditing],
                   listeners: {
                       'canceledit': function (editor, e, eOpts) {
                           // store_detail.load({
                           //     params: {
                           //         InventoryID: Ext.getCmp('InventoryID').getValue()
                           //     }
                           // });
                       },
                       'edit': function (editor, e) {
                           if (e.record.data.PaketID == '') {
                               if (Ext.getCmp('InventoryID').getValue()=='') {
                                 var form = this.up('form').getForm();
                                 form.submit({
                                     url: m_crud,
                                     method:'POST',
                                     waitMsg: 'Sending data...',
                                     success: function(fp, o) {
                                         Ext.getCmp('InventoryID').setValue(o.result.InventoryID)
                                         save_detail(e)
                                     }
                                 });
                               } else save_detail(e)
                           } else {
                               // Ext.MessageBox.confirm('Message', 'Update data ini ?', function (btn) {
                               //     if (btn == 'yes') {
                               //         Ext.Ajax.request({
                               //             waitMsg: 'Please wait...',
                               //             url: m_crud+'_detail',
                               //             method: 'PUT',
                               //             params: {
                               //               PaketID: e.record.data.PaketID,
                               //               name: e.record.data.name,
                               //               ChildInventoryID: Ext.getCmp('ChildInventoryID').getValue(),
                               //               Qty: e.record.data.Qty
                               //             },
                               //             success: function (response, opts) {
                               //                 var obj = Ext.decode(response.responseText);
                               //                 switch (obj.success) {
                               //                     case true:
                               //                         Ext.MessageBox.alert('Success', obj.message);
                               //                         store_detail.load({
                               //                             params: {
                               //                                 InventoryID: Ext.getCmp('InventoryID').getValue()
                               //                             }
                               //                         });
                               //                         break;
                               //                     default:
                               //                         Ext.MessageBox.alert('Warning', obj.message);
                               //                         break;
                               //                 }
                               //             },
                               //             failure: function (response, opts) {
                               //                 var obj = Ext.decode(response.responseText);
                               //                 Ext.MessageBox.alert('error', 'Could not connect to the database. Retry later');
                               //             }
                               //         });
                               //     }
                               // });
                           }
                       }
                   }
                }
        ];

            frm.callParent();
        },
        afterRender: function ()
        {
            this.superclass.afterRender.apply(this);
            this.doLayout();
        }
});
    
    
//    var win = Ext.create('widget.window', {
//        title: 'Input Form Supplier',
//        frame: false,
//        closable: true,
//        id: 'win',
//        modal: true,
//        closeAction: 'show',
//        width: '50%',
//        minWidth: 370,
//        height: '50%',
//        layout: 'fit',
//        items: [DataForm]
//    });
//    function submitOnEnter(field, event) {
//        if (event.getKey() == event.ENTER) {
//            store.load({
//                params: {
//                    key: Ext.getCmp('key').getValue()
//                }});
//        }
//    }
    var grid = Ext.create('Ext.grid.Panel', {
        store: store,
        width: '100%',
        id: 'grid',
        minHeight: 350,
        style: 'border:1px solid #CCC;',
        renderTo: 'ext-content',
        loadMask: true,
        selType: 'rowmodel',
        dockedItems: [{
                xtype: 'pagingtoolbar',
                store: store, // same store GridPanel is using
                dock: 'bottom',
                displayInfo: true
            }, {
                xtype: 'toolbar',
                items: [
                    {
                        icon: varjs.config.base_url + 'images/icons/new/add.png', cls:'Sfr_BtnGridGreen', overCls:'Sfr_BtnGridGreen-Hover',
                        text: 'Opname Baru',
                        scope: this,
                        handler: function () {
                            
                            var win = Ext.getCmp('WFormCheckStock');
//                            var win = Ext.getCmp('WindowInventory');
//
                            if (!win) {
//                                
                                win = new Ext.Window({
                                    id: 'WFormCheckStock',
                                    modal: true,
                                    title: 'Form Cek Stok',
                                    resizable: false,
                                    plain: true,
                                    items: [
                                        {
                                            xtype:'dataForm'
                                        }
                                    ]
                                });
//                                
                            }
                            win.show();

                            mc_barang.load();
                        }
                    }, {
                        icon: varjs.config.base_url + 'images/icons/new/update.png',
                        text: lang('Update'),
                        hidden:true,
                        scope: this,
                        cls: m_act_update,
                        handler: function() {
                            var sm = grid.getSelectionModel().getSelection()[0];
                            if (!sm) {
                                Ext.MessageBox.alert(lang('Error'), lang('Please select data'));
                                return false;
                            } else {
                                // var id = sm.get('id');
                                var id = sm.data.id;
                                
                                var win = Ext.getCmp('WFormCheckStock');
//                            var win = Ext.getCmp('WindowInventory');
//
                                if (!win) {
    //                                
                                    win = new Ext.Window({
                                        id: 'WFormCheckStock',
                                        modal: true,
                                        title: 'Form Supplier',
                                        resizable: false,
                                        plain: true,
                                        listeners:{
                                            beforerender:function(c){
                                               
                                            }
                                        },
                                        items: [
                                            {
                                                xtype:'dataForm'
                                            }
                                        ]
                                    });
    //                                
                                }
                                win.show();
                                Ext.getCmp('dataForm').getForm().load({
                                    url:m_crud,
                                    method:'GET',
                                    params:{id:id}
                                });
                            }
                        }
                    }, {
                        itemId: 'remove',
                        hidden:true,
                        icon: varjs.config.base_url + 'images/icons/new/delete.png',
                        cls: m_act_delete,
                        text: 'Hapus',
                        scope: this,
                        handler: function() {
                            var smb = Ext.getCmp('grid').getSelectionModel().getSelection()[0];
                            Ext.MessageBox.confirm('Message', 'Apakah anda mau menghapus data ini ?', function(btn) {
                                if (btn == 'yes') {
                                    Ext.Ajax.request({
                                        waitMsg: 'Please Wait',
                                        url: m_crud,
                                        method: 'DELETE',
                                        params: {id: smb.raw.id},
                                        success: function(response, opts) {
                                            var obj = Ext.decode(response.responseText);
                                            switch (obj.success) {
                                                case true:
                                                    store.load();
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
                    }, {
                        xtype: 'textfield',
                        name: 'key', baseCls:'Sfr_TxtfieldSearchGrid',
                        id: 'key',
                        hidden:true,
                        listeners: {
//                            specialkey: submitOnEnter
                        }
                    }, {
                        xtype: 'button',
                        hidden:true,
                        margin: '0px 0px 0px 6px',
                        text: 'Search',
                        handler: function() {
                            store.load({
                                params: {
                                    key: Ext.getCmp('key').getValue()
                                }});
                        }
                    }]
            }],
        columns: [
        // 'OpnameID','Periode','Notes','CreatedDate','TotalAS','TotalCS','Difference'
            {
                text: 'OpnameID',
                dataIndex: 'OpnameID',
                hidden: true
            }, {
                text: 'No',
                xtype: 'rownumberer',
                width: '5%'
            },
            {
                text: 'Periode',
                width: '10%',
                dataIndex: 'Periode'
            },
            {
                text: 'Catatan',
                flex:1,
                dataIndex: 'Notes'
            },
            {
                text: 'Total Aktual',
                align:'right',
                width: '15%',
                dataIndex: 'TotalAS'
            },
            {
                text: 'Total Fisik',
                align:'right',
                width: '15%',
                dataIndex: 'TotalCS'
            },
            {
                text: 'Total Selisih',
                align:'right',
                width: '15%',
                dataIndex: 'Difference'
            }
        ]
    });
});

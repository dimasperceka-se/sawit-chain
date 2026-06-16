Ext.onReady(function() {
    Ext.tip.QuickTipManager.init();



//START AKUN PERKIRAAN LIST
var storeBuyCoaList = Ext.create('Ext.data.Store', {
    extend: 'Ext.data.Model',
    fields: ['id', 'code', 'title'],
//    autoLoad: true,
    pageSize: 50,
    proxy: {
        type: 'ajax',
        url: m_coadatas,
        reader: {
            type: 'json',
            root: 'data',
            totalProperty: 'total'
        }
    }
});

Ext.define('GridCoaBuyList', {
    itemId: 'GridCoaBuyList',
    id: 'GridCoaBuyList',
    extend: 'Ext.grid.Panel',
    alias: 'widget.GridCoaBuyList',
    store: storeBuyCoaList,
    loadMask: true,
    columns: [
    {
            text: 'Select',
            width: 65,
            xtype: 'actioncolumn',
            tooltip: 'Select',
            align: 'center',
            icon: m_baseurl + '/images/icons/silk/add.png',
            handler: function(grid, rowIndex, colIndex, actionItem, event, selectedRecord, row) {
                    Ext.getCmp('BuyCoaID').setValue(selectedRecord.data.id);
                    Ext.getCmp('CoaTitleBuy').setValue(selectedRecord.data.title);
                    Ext.getCmp('BuyCoaCode').setValue(selectedRecord.data.code);
                    Ext.getCmp('wCoaBuyPopup').hide();
            }
        },
        { text: 'id', dataIndex: 'id', hidden: true },
        { text: 'COA Code', flex:1, width: '25%', dataIndex: 'code' },
        { text: 'COA Name', width: '75%', dataIndex: 'title' }
    ]
    , dockedItems: [{
            xtype: 'pagingtoolbar',
            store: storeBuyCoaList, // same store GridPanel is using
            dock: 'bottom',
            displayInfo: true
                    // pageSize:20
        }
    ]
});

  var wCoaBuyPopup = Ext.create('widget.window', {
      id: 'wCoaBuyPopup',
      title: 'Choose Chart of Account',
      header: {
          titlePosition: 2,
          titleAlign: 'center'
      },
      closable: true,
      closeAction: 'hide',
  //    autoWidth: true,
       width: 770,
      height: 330,
      layout: 'fit',
      border: false,
      items: [{
              xtype:'GridCoaBuyList'
      }]
  });

//

var storeSellCoaList = Ext.create('Ext.data.Store', {
    extend: 'Ext.data.Model',
    fields: ['id', 'code', 'title'],
//    autoLoad: true,
    pageSize: 50,
    proxy: {
        type: 'ajax',
        url: m_coadatas,
        reader: {
            type: 'json',
            root: 'data',
            totalProperty: 'total'
        }
    }
});

Ext.define('GridCoaSellList', {
    itemId: 'GridCoaSellList',
    id: 'GridCoaSellList',
    extend: 'Ext.grid.Panel',
    alias: 'widget.GridCoaSellList',
    store: storeSellCoaList,
    loadMask: true,
    columns: [
    {
            text: 'Select',
            width: 65,
            xtype: 'actioncolumn',
            tooltip: 'Select',
            align: 'center',
            icon: m_baseurl + '/images/icons/silk/add.png',
            handler: function(grid, rowIndex, colIndex, actionItem, event, selectedRecord, row) {
                    Ext.getCmp('SellCoaID').setValue(selectedRecord.data.id);
                    Ext.getCmp('CoaTitleSell').setValue(selectedRecord.data.title);
                    Ext.getCmp('SellCoaCode').setValue(selectedRecord.data.code);
                    Ext.getCmp('wCoaSellPopup').hide();
            }
        },
        { text: 'id', dataIndex: 'id', hidden: true },
        { text: 'COA Code', flex:1, width: '25%', dataIndex: 'code' },
        { text: 'COA Name', width: '75%', dataIndex: 'title' }
    ]
    , dockedItems: [{
            xtype: 'pagingtoolbar',
            store: storeSellCoaList, // same store GridPanel is using
            dock: 'bottom',
            displayInfo: true
                    // pageSize:20
        }
    ]
});

  var wCoaSellPopup = Ext.create('widget.window', {
      id: 'wCoaSellPopup',
      title: 'Choose Chart of Account',
      header: {
          titlePosition: 2,
          titleAlign: 'center'
      },
      closable: true,
      closeAction: 'hide',
  //    autoWidth: true,
       width: 770,
      height: 330,
      layout: 'fit',
      border: false,
      items: [{
              xtype:'GridCoaSellList'
      }]
  });


//END COA

    var store = Ext.create('Ext.data.Store', {
        extend: 'Ext.data.Model',
        fields: ['id', 'namecat', 'Description','SellCoaID','BuyCoaID', 'CreatedBy', 'CreatedDate', 'UpdatedBy', 'UpdatedDate'],
        autoLoad: true,
        pageSize: 50,
        proxy: {
            type: 'ajax',
            url: m_crud + 's',
            reader: {
                type: 'json',
                root: 'data',
                totalProperty: 'total'
            }
        }
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

    function submitOnEnter(field, event) {
        if (event.getKey() == event.ENTER) {
            store.load({
                params: {
                    key: Ext.getCmp('key').getValue()
                }
            });
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
//            frm.width = 1050;
            frm.autoWidth = true;
            frm.autoScroll = true;
//            frm.height = 500;
            frm.autoHeight = true;
            frm.fieldDefaults = {
                msgTarget: 'side',
                blankText: 'Tidak Boleh Kosong',
                labelWidth: 110,
                width: 460
            };
             frm.buttons = [
                 {
                // id: 'saveButton',
                text: 'Save',
                margin: '5px',
                scale: 'large',
                ui: 's-button',
                cls: 's-blue',
                handler: function() {
                    var form = this.up('form').getForm();
                    var methode;
                    if (Ext.getCmp('id').getValue() == '')
                        methode = 'POST';
                    else
                        methode = 'PUT';
                    form.submit({
                        url: m_crud,
                        method: methode,
                        waitMsg: 'Sending data...',
                        success: function(fp, o) {
                            Ext.MessageBox.alert('Success', 'Data saved.');
                            Ext.getCmp('WFormInvCategory').hide();
                            store.reload();
                            // Ext.getCmp('grid').getSelectionModel().clearSelections();
                        }});
                }
            }, {
                text: 'Close',
                margin: '5px',
                scale: 'large',
                ui: 's-button',
                cls: 's-grey',
                disabled: false, handler: function() {
                    Ext.getCmp('WFormInvCategory').hide();
                }
            }];

            frm.items = [
                {
                xtype: 'hiddenfield',
                id: 'id',
                name: 'id',
                // inputType: 'hidden'
            },{
                xtype: 'textfield',
                allowBlank: false,
                fieldLabel: 'Category Name',
                name: 'namecat'
            },
            {
                xtype: 'fieldcontainer',
                fieldLabel: 'Selling COA',
                combineErrors: true,
                msgTarget : 'side',
                layout: 'hbox',
                defaults: {
                    // flex: 1,
                    hideLabel: true
                },
                items: [

                    {
                        xtype: 'hiddenfield',
                        id: 'SellCoaID',
                        name: 'SellCoaID'
                    }, {
                        xtype: 'textfield',
                        allowBlank:false,
                        margin: '0 5 0 0',
                        width:210,
                        name: 'CoaTitleSell',
                        hideLabel:true,
                        id: 'CoaTitleSell',
                        listeners: {
                            render: function(component) {
                                component.getEl().on('click', function(event, el) {
                                    wCoaSellPopup.show();
                                    storeSellCoaList.load({
                                         params: {
                                                type: 'class',
                                                id:1
                                            }
                                    });
                                });
                            }
                        }
                    },
                    {
                        xtype     : 'textfield',
                        readOnly:true,
                        width:110,
                        hideLabel:true,
                        name:'SellCoaCode',
                        id:'SellCoaCode'
                    }
                ]
            },
            {
                xtype: 'fieldcontainer',
                fieldLabel: 'Buying COA',
                combineErrors: true,
                msgTarget : 'side',
                layout: 'hbox',
                defaults: {
                    // flex: 1,
                    hideLabel: true
                },
                items: [
                    {
                        xtype: 'hiddenfield',
                        id: 'BuyCoaID',
                        name: 'BuyCoaID'
                    },
                    {
                        xtype: 'textfield',
                        allowBlank:false,
                        margin: '0 5 0 0',
                        width:210,
                        name: 'CoaTitleBuy',
                        id: 'CoaTitleBuy',
                        listeners: {
                            render: function(component) {
                                component.getEl().on('click', function(event, el) {
                                    wCoaBuyPopup.show();
                                    storeBuyCoaList.load({
                                         params: {
                                                type: 'class',
                                                id:1
                                            }
                                    });
                                });
                            }
                        }
                    },
                    {
                        xtype: 'textfield',
                        readOnly:true,
                        width:110,
                        hideLabel:true,
                        name:'BuyCoaCode',
                        id:'BuyCoaCode'
                    }
                ]
            },
            {
                xtype: 'textarea',
                allowBlank: false,
                fieldLabel: 'Description',
                name: 'Description'
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


    var grid = Ext.create('Ext.grid.Panel', {
        store: store,
        width: '100%',
        id: 'grid',
        minHeight: 250,
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
                        text: 'Add',
                        cls: m_act_add,
                        scope: this,
                        handler: function () {

                            var win = Ext.getCmp('WFormInvCategory');
//                            var win = Ext.getCmp('WindowInventory');
//
                            if (!win) {
//
                                win = new Ext.Window({
                                    modal: true,
                                    id: 'WFormInvCategory',
                                    title: 'Form Inventory Category',
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
                            Ext.getCmp('dataForm').getForm().reset();
                        }
                    }, {
                        icon: varjs.config.base_url + 'images/icons/new/update.png',
                        text: lang('Update'),
                        scope: this,
                        cls: m_act_update,
                        handler: function() {
                            var sm = grid.getSelectionModel().getSelection()[0];
                            console.log(sm.data.id)
                            if (!sm) {
                                Ext.MessageBox.alert(lang('Error'), lang('Please select data'));
                                return false;
                            } else {
                                // var id = sm.get('id');
                                var id = sm.data.id;

                                var win = Ext.getCmp('WFormInvCategory');
//                            var win = Ext.getCmp('WindowInventory');
//
                                if (!win) {


                                    win = new Ext.Window({
                                        id: 'WFormInvCategory',
                                        modal: true,
                                        title: 'Form Inventory Category',
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
                    },'->', {
                        xtype: 'textfield',
                        name: 'key', baseCls:'Sfr_TxtfieldSearchGrid',
                        id: 'key',
                        listeners: {
                           specialkey: submitOnEnter
                        }
                    }, {
                        xtype: 'button',
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

            {
                text: 'id',
                dataIndex: 'id',
                hidden: true
            }, {
                text: 'No',
                xtype: 'rownumberer',
                width: '5%'
            },
            {
                text: 'Category Name',
                width: '25%',
                dataIndex: 'namecat'
            },
            {
                text: 'Description',
                width: '35%',
                dataIndex: 'Description'
            }
        ]
    });
});

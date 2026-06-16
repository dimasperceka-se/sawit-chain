Ext.onReady(function() {
    Ext.tip.QuickTipManager.init();
    var store = Ext.create('Ext.data.Store', {
        extend: 'Ext.data.Model',
        fields: ['id', 'code', 'namesupplier', 'companyaddress', 'companyaddress2', 'telephone', 'fax', 'city', 'email','country'],
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
                            Ext.getCmp('WFormSupplier').hide();
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
                    Ext.getCmp('WFormSupplier').hide();
                }
            }];
        
            frm.items = [
                {
                xtype: 'textfield',
                id: 'id',
                name: 'id',
                inputType: 'hidden'
            },  {
                xtype: 'textfield',
                allowBlank: false,
                width:320,
                fieldLabel: 'Supplier Code',
                id: 'code',
                name: 'code'
            },{
                xtype: 'textfield',
                allowBlank: false,
                fieldLabel: 'Supplier Name',
                id: 'namesupplier',
                name: 'namesupplier'
            },{
                xtype: 'textarea',
                height:50,
                allowBlank: false,
                fieldLabel: 'Address',
                id: 'companyaddress',
                name: 'companyaddress'
            },{
                xtype: 'textfield',
                allowBlank: false,
                width:320,
                fieldLabel: 'No Telephone',
//                id: 'telephone',
                name: 'telephone'
            },{
                xtype: 'textfield',
                width:320,
                allowBlank: false,
                fieldLabel: 'fax',
//                id: 'fax',
                name: 'fax'
            },{
                xtype: 'textfield',
                allowBlank: false,
                fieldLabel: 'Email',
                name: 'email'
            },{
                xtype: 'textareafield',
                                            height:103,
                allowBlank: false,
                fieldLabel: 'Address',
                id: 'companyaddress',
                name: 'companyaddress'
            },{
                xtype: 'textfield',
                allowBlank: false,
                width:320,
                fieldLabel: 'City',
                name: 'city'
            },{
                xtype: 'textfield',
                hidden:true,
                // allowBlank: false,
                width:320,
                fieldLabel: 'Country',
//                id: 'companyaddress',
                name: 'country'
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
                        scope: this,
                        handler: function () {
                            
                            var win = Ext.getCmp('WFormSupplier');
//                            var win = Ext.getCmp('WindowInventory');
//
                            if (!win) {
//                                
                                win = new Ext.Window({
                                    id: 'WFormSupplier',
                                    modal: true,
                                    title: 'Form Supplier',
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
                        }
                    }, {
                        icon: varjs.config.base_url + 'images/icons/new/update.png',
                        text: lang('Update'),
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
                                
                                var win = Ext.getCmp('WFormSupplier');
//                            var win = Ext.getCmp('WindowInventory');
//
                                if (!win) {
    //                                
                                    win = new Ext.Window({
                                        id: 'WFormSupplier',
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
        columns: [{
                text: 'ID',
                dataIndex: 'id',
                hidden: true
            }, {
                text: 'No',
                xtype: 'rownumberer',
                width: '5%'
            },
            {
                text: 'Supplier Code',
                width: '15%',
                dataIndex: 'code'
            },
            {
                text: 'Supplier Name',
                width: '15%',
                dataIndex: 'namesupplier'
            },
            {
                text: 'Address',
                width: '25%',
                dataIndex: 'companyaddress'
            },
            {
                text: 'No Telephone',
                width: '15%',
                dataIndex: 'telephone'
            },
            {
                text: 'City',
                width: '15%',
                dataIndex: 'telephone'
            },
            {
                text: 'Country',
                width: '15%',
                dataIndex: 'country'
            }
        ]
    });
});

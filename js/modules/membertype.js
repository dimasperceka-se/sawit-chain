Ext.onReady(function() {
    Ext.tip.QuickTipManager.init();

    var storeCoaList = Ext.create('Ext.data.Store', {
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



    var store = Ext.create('Ext.data.Store', {
        extend: 'Ext.data.Model',
        fields: ['id', 'coopID', 'typeName', 'typeCode', 'typeMaxProfit', 'typeSimPokokAmount', 'typeSimWajibAmount', 'typeSimPokokPeriod', 'typeSimWajibPeriod'],
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

    var mc_period = Ext.create('Ext.data.Store', {
        extend: 'Ext.data.Model',
        fields: ['id', 'label'],
        autoLoad: true,
        data: [
            {"id": "1", "label": "Monthly"},
            {"id": "2", "label": "Yearly"}
        ]

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

    var DataForm = Ext.create('Ext.form.Panel', {
        frame: false,
        bodyPadding: 5,
        autoScroll: true,
        id: 'dataForm',
        fieldDefaults: {
            labelAlign: 'left',
            labelWidth: 150,
            anchor: '100%'
        },
        items: [
            {
                xtype: 'textfield',
                id: 'id',
                name: 'id',
                inputType: 'hidden'
            }, {
                xtype: 'textfield',
                id: 'coopID',
                name: 'coopID',
                inputType: 'hidden'
            }, {
                xtype: 'textfield',
                allowBlank: false,
                fieldLabel: lang('Code') + " *",
                id: 'typeCode',
                name: 'typeCode'
            }, {
                xtype: 'textfield',
                allowBlank: false,
                fieldLabel: lang('Name') + " *",
                id: 'typeName',
                name: 'typeName'
            }, {
                xtype: 'textfield',
                allowBlank: false,
                fieldLabel: lang('Max Profit') + " *",
                id: 'typeMaxProfit',
                name: 'typeMaxProfit'
            }, {
                xtype: 'fieldcontainer',
                hidden:true,
                fieldLabel: lang('Simpanan Pokokx') + " *",
                layout: 'hbox',
                items: [{
                        xtype: 'textfield',
                        // allowBlank: false,
                        margin: '0 5 0 0',
                        id: 'typeSimPokokAmount',
                        name: 'typeSimPokokAmount'
                    }, {
                        xtype: 'textfield',
                        // allowBlank: false,
                        id: 'typeSimPokokPeriod',
                        name: 'typeSimPokokPeriod',
                        hidden: true,
                        value: '1'
                    }]
            }, {
                xtype: 'fieldcontainer',
                hidden:true,
                fieldLabel: lang('Simpanan Wajib') + " *",
                layout: 'hbox',
                items: [{
                        xtype: 'textfield',
                        width:100,
                        // allowBlank: false,
                        margin: '0 5 0 0',
                        id: 'typeSimWajibAmount',
                        name: 'typeSimWajibAmount'
                    }, {
                        id: 'typeSimWajibPeriod',
                        name: 'typeSimWajibPeriod',
                        xtype: 'combobox',
                        store: mc_period,
                        displayField: 'label',
                        valueField: 'id',
                        queryMode: 'local'
                    }]
            }],
        buttons: [{
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
                        }});
                    win.hide(this, function() {
                        store.load();
                    });
                }
            }, {
                text: 'Close',
                margin: '5px',
                scale: 'large',
                ui: 's-button',
                cls: 's-grey',
                disabled: false, handler: function() {
                    win.hide();
                }
            }]
    });
    var win = Ext.create('widget.window', {
        title: 'Input Form Member Type',
        frame: false,
        closable: true,
        id: 'win',
        modal: true,
        closeAction: 'show',
        width: '50%',
        minWidth: 400,
        height: '50%',
        layout: 'fit',
        items: [DataForm]
    });
    function submitOnEnter(field, event) {
        if (event.getKey() == event.ENTER) {
            store.load({
                params: {
                    key: Ext.getCmp('key').getValue()
                }});
        }
    }
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

                                Ext.define('GridCoaRegCostList', {
                                    itemId: 'GridCoaRegCostList',
                                    id: 'GridCoaRegCostList',
                                    extend: 'Ext.grid.Panel',
                                    alias: 'widget.GridCoaRegCostList',
                                    store: storeCoaList,
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
                                                    Ext.getCmp('CoaRegMemberTypeID').setValue(selectedRecord.data.id);
                                                    Ext.getCmp('CoaRegMemberTypeName').setValue(selectedRecord.data.title);
                                                    Ext.getCmp('wCoaRegCostPopup').hide();
                                            }
                                        },
                                        { text: 'id', dataIndex: 'id', hidden: true },
                                        { text: 'COA Code', flex:1, width: '25%', dataIndex: 'code' },
                                        { text: 'COA Name', width: '75%', dataIndex: 'title' }
                                    ]
                                    , dockedItems: [{
                                            xtype: 'pagingtoolbar',
                                            store: storeCoaList, // same store GridPanel is using
                                            dock: 'bottom',
                                            displayInfo: true
                                                    // pageSize:20
                                        }
                                    ]
                                });

                                var wCoaRegCostPopup = Ext.create('widget.window', {
                                    id: 'wCoaRegCostPopup',
                                    title: 'Choose Chart of Account Registration Cost',
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
                                            xtype:'GridCoaRegCostList'
                                    }]
                                });

                            var win = Ext.create('widget.window', {
                                title: 'Add Member Type',
                                id: 'win-member-type',
                                modal: true,
                                width: 480,
                                layout: 'fit',
                                items: Ext.create('Ext.form.Panel', {
                                    bodyPadding: 5,
                                    autoScroll: true,
                                    id: 'frm-add-member-type',
                                    fieldDefaults: {
                                        labelAlign: 'left',
                                        labelWidth: 140
                                    },
                                    items: [
                                        {
                                            xtype: 'textfield',
                                            allowBlank: false,
                                            anchor:'60%',
                                            fieldLabel: lang('Code') + " <span style='color:red;font-weight:bold'>*</span>",
                                            id: 'typeCode',
                                            maxLength:5,
                                            enforceMaxLength:true,
                                            name: 'typeCode'
                                        }, {
                                            xtype: 'textfield',
                                            allowBlank: false,
                                            anchor:'100%',
                                            fieldLabel: lang('Name') + " <span style='color:red;font-weight:bold'>*</span>",
                                            id: 'typeName',
                                            name: 'typeName'
                                        },
                                        // , {
                                        //     xtype: 'fieldcontainer',
                                        //     fieldLabel: lang('Max Profitz') + " <span style='color:red;font-weight:bold'>*</span>",
                                        //     layout: 'hbox',
                                        //     items: [{
                                        //             xtype: 'textfield',
                                        //             allowBlank: false,
                                        //             width:50,
                                        //             margin: '0 5 0 0',
                                        //             id: 'typeMaxProfit',
                                        //             name: 'typeMaxProfit'
                                        //         }, {
                                        //             xtype: 'displayfield',
                                        //             allowBlank: false,
                                        //             value: ' % '
                                        //         }]
                                        // },
                                         {
                                            xtype: 'fieldcontainer',
                                            hidden:true,
                                            fieldLabel: lang('Simpanan Pokok') + " <span style='color:red;font-weight:bold'>*</span>",
                                            layout: 'hbox',
                                            items: [{
                                                    xtype: 'numericfield',
                                                    // allowBlank: false,
                                                    margin: '0 5 0 0',
                                                    id: 'typeSimPokokAmount',
                                                    name: 'typeSimPokokAmount'
                                                }, {
                                                    xtype: 'textfield',
                                                    // allowBlank: false,
                                                    id: 'typeSimPokokPeriod',
                                                    name: 'typeSimPokokPeriod',
                                                    hidden: true,
                                                    value: '1'
                                                }]
                                        }, {
                                            xtype: 'fieldcontainer',
                                            hidden:true,
                                            fieldLabel: lang('Simpanan Wajib') + " <span style='color:red;font-weight:bold'>*</span>",
                                            layout: 'hbox',
                                            items: [{
                                                    xtype: 'numericfield',
                                                    // allowBlank: false,
                                                    margin: '0 5 0 0',
                                                    id: 'typeSimWajibAmount',
                                                    name: 'typeSimWajibAmount'
                                                }, {
                                                    id: 'typeSimWajibPeriod',
                                                    name: 'typeSimWajibPeriod',
                                                    xtype: 'combobox',
                                                    width:80,
                                                    store: mc_period,
                                                    displayField: 'label',
                                                    valueField: 'id',
                                                    queryMode: 'local'
                                                }]
                                        },
                                        // {
                                        //     xtype: 'numericfield',
                                        //     hideTrigger:true,
                                        //     allowBlank: false,
                                        //     fieldLabel: 'Uang Pendaftaran',
                                        //     name:'RegistrationFee'
                                        // },
                                        {
                                            xtype:'hiddenfield',
                                            name:'CoaRegMemberTypeID',
                                            id:'CoaRegMemberTypeID'
                                        },
                                        {
                                            xtype: 'textfield',
                                            hidden:true,
                                            // allowBlank: false,
                                            fieldLabel: 'Account Jurnal Uang Pendaftaran',
                                            name:'CoaRegMemberTypeName',
                                            id:'CoaRegMemberTypeName',
                                            listeners: {
                                                        render: function(component) {
                                                            component.getEl().on('click', function(event, el) {
                                                                wCoaRegCostPopup.show();
                                                                storeCoaList.load({
                                                                    params: {
                                                                        type: 'class',
                                                                        id:1
                                                                    }
                                                                });
                                                   });

                                                }
                                            }
                                        }
                                    ],
                                    buttons: [{
                                            id: 'saveButton',
                                            text: 'Save',
                                            margin: '5px',
                                            scale: 'large',
                                            ui: 's-button',
                                            cls: 's-blue',
                                            handler: function() {
                                                var form = this.up('form').getForm();
                                                form.submit({
                                                    url: m_crud,
                                                    method: 'POST',
                                                    waitMsg: 'Sending data...',
                                                    success: function(fp, o) {
                                                        Ext.MessageBox.alert('Success', 'Data saved.');
                                                        win.close(this, function() {

                                                        });
                                                        store.load();
                                                    }
                                                });

                                            }
                                        }, {
                                            text: 'Close',
                                            margin: '5px',
                                            scale: 'large',
                                            ui: 's-button',
                                            cls: 's-grey',
                                            disabled: false, handler: function() {
                                                win.close();
                                            }
                                        }]
                                })
                            }).show();
                        }
                    }, {
                        icon: varjs.config.base_url + 'images/icons/new/update.png',
                        text: lang('Update'),
                        scope: this,
                        cls: m_act_update,
                        handler: function() {
                            var sm = Ext.getCmp('grid').getSelectionModel().getSelection()[0];
                            if (!sm) {
                                Ext.MessageBox.alert(lang('Error'), lang('Please select data'));
                                return false;
                            } else {
                                var id = sm.get('id');

                                 Ext.define('GridCoaRegCostEditList', {
                                    itemId: 'GridCoaRegCostEditList',
                                    id: 'GridCoaRegCostEditList',
                                    extend: 'Ext.grid.Panel',
                                    alias: 'widget.GridCoaRegCostEditList',
                                    store: storeCoaList,
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
                                                    Ext.getCmp('CoaRegMemberTypeIDEdit').setValue(selectedRecord.data.id);
                                                    Ext.getCmp('CoaRegMemberTypeNameEdit').setValue(selectedRecord.data.title);
                                                    Ext.getCmp('wCoaRegCostPopupEdit').hide();
                                            }
                                        },
                                        { text: 'id', dataIndex: 'id', hidden: true },
                                        { text: 'COA Code', flex:1, width: '25%', dataIndex: 'code' },
                                        { text: 'COA Name', width: '75%', dataIndex: 'title' }
                                    ]
                                    , dockedItems: [{
                                            xtype: 'pagingtoolbar',
                                            store: storeCoaList, // same store GridPanel is using
                                            dock: 'bottom',
                                            displayInfo: true
                                                    // pageSize:20
                                        }
                                    ]
                                });

                                var wCoaRegCostPopupEdit = Ext.create('widget.window', {
                                    id: 'wCoaRegCostPopupEdit',
                                    title: 'Choose Chart of Account Registration Cost',
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
                                            xtype:'GridCoaRegCostEditList'
                                    }]
                                });

                                var win = Ext.create('widget.window', {
                                    title: 'Edit Member Type',
                                    id: 'win-member-type',
                                    modal: true,
                                    width: 480,
                                    layout: 'fit',
                                    items: Ext.create('Ext.form.Panel', {
                                        bodyPadding: 5,
                                        autoScroll: true,
                                        id: 'frm-edit-member-type',
                                        fieldDefaults: {
                                            labelAlign: 'left',
                                            labelWidth: 140
                                        },
                                        listeners:{
                                            beforerender:function(c){
                                                c.getForm().load({
                                                    url:m_crud,
                                                    method:'GET',
                                                    params:{id:id}
                                                });
                                            }
                                        },
                                        items: [
                                            {
                                                xtype: 'textfield',
                                                name: 'typeID',
                                                inputType: 'hidden'
                                            }, {
                                                xtype: 'textfield',
                                                allowBlank: false,
                                                anchor:'60%',
                                                maxLength:5,
                                                enforceMaxLength:true,
                                                fieldLabel: lang('Code') + " <span style='color:red;font-weight:bold'>*</span>",
                                                name: 'typeCode'
                                            }, {
                                                xtype: 'textfield',
                                                allowBlank: false,
                                                anchor:'100%',
                                                fieldLabel: lang('Name') + " <span style='color:red;font-weight:bold'>*</span>",
                                                name: 'typeName'
                                            }, 
                                            // {
                                            //     xtype: 'fieldcontainer',
                                            //     fieldLabel: lang('Max Profit') + " <span style='color:red;font-weight:bold'>*</span>",
                                            //     layout: 'hbox',
                                            //     items: [{
                                            //             xtype: 'textfield',
                                            //             allowBlank: false,
                                            //             width:50,
                                            //             margin: '0 5 0 0',
                                            //             id: 'typeMaxProfit',
                                            //             name: 'typeMaxProfit'
                                            //         }, {
                                            //             xtype: 'displayfield',
                                            //             allowBlank: false,
                                            //             value: ' % '
                                            //         }]
                                            // }, 
                                            {
                                                xtype: 'fieldcontainer',
                                                hidden:true,
                                                fieldLabel: lang('Simpanan Pokok') + " <span style='color:red;font-weight:bold'>*</span>",
                                                layout: 'hbox',
                                                items: [{
                                                        xtype: 'numericfield',
                                                        // allowBlank: false,
                                                        margin: '0 5 0 0',
                                                        id: 'typeSimPokokAmount',
                                                        name: 'typeSimPokokAmount'
                                                    }, {
                                                        xtype: 'textfield',
                                                        // allowBlank: false,
                                                        id: 'typeSimPokokPeriod',
                                                        name: 'typeSimPokokPeriod',
                                                        hidden: true,
                                                        value: '1'
                                                    }]
                                            }, {
                                                xtype: 'fieldcontainer',
                                                hidden:true,
                                                fieldLabel: lang('Simpanan Wajib') + " <span style='color:red;font-weight:bold'>*</span>",
                                                layout: 'hbox',
                                                items: [{
                                                        xtype: 'numericfield',
                                                        allowBlank: false,
                                                        margin: '0 5 0 0',
                                                        id: 'typeSimWajibAmount',
                                                        name: 'typeSimWajibAmount'
                                                    }, {
                                                        id: 'typeSimWajibPeriod',
                                                        name: 'typeSimWajibPeriod',
                                                        xtype: 'combobox',
                                                        width:80,
                                                        store: mc_period,
                                                        displayField: 'label',
                                                        valueField: 'id',
                                                        queryMode: 'local'
                                                    }]
                                            },
                                            // {
                                            //     xtype: 'numericfield',
                                            //     hideTrigger:true,
                                            //     allowBlank: false,
                                            //     fieldLabel: 'Uang Pendaftaran',
                                            //     name:'RegistrationFee'
                                            // },
                                            {
                                                    xtype:'hiddenfield',
                                                    name:'CoaRegMemberTypeID',
                                                    id:'CoaRegMemberTypeIDEdit'
                                                },
                                                {
                                                    xtype: 'textfield',
                                                    anchor:'100%',
                                                    // allowBlank: false,
                                                    hidden:true,
                                                    fieldLabel: 'Account Jurnal Uang Pendaftaran',
                                                    name:'CoaRegMemberTypeName',
                                                    id:'CoaRegMemberTypeNameEdit',
                                                    listeners: {
                                                                render: function(component) {
                                                                    component.getEl().on('click', function(event, el) {
                                                                        wCoaRegCostPopupEdit.show();
                                                                        storeCoaList.load({
                                                                            params: {
                                                                                type: 'class',
                                                                                id:1
                                                                            }
                                                                        });
                                                           });

                                                        }
                                                    }
                                                }
                                            ],
                                        buttons: [{
                                                id: 'saveButton',
                                                text: 'Save',
                                                margin: '5px',
                                                scale: 'large',
                                                ui: 's-button',
                                                cls: 's-blue',
                                                handler: function() {
                                                    var form = this.up('form').getForm();
                                                    form.submit({
                                                        url: m_crud,
                                                        method: 'PUT',
                                                        waitMsg: 'Sending data...',
                                                        success: function(fp, o) {
                                                            Ext.MessageBox.alert('Success', 'Data saved.');
                                                            win.close(this, function() {

                                                            });
                                                            store.load();
                                                        }
                                                    });

                                                }
                                            }, {
                                                text: 'Close',
                                                margin: '5px',
                                                scale: 'large',
                                                ui: 's-button',
                                                cls: 's-grey',
                                                disabled: false, handler: function() {
                                                    win.close();
                                                }
                                            }]
                                    })
                                }).show();
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
        columns: [{
                text: 'ID',
                dataIndex: 'id',
                hidden: true
            }, {
                text: 'No',
                xtype: 'rownumberer',
                width: '5%'
            }, {
                text: lang('Code'),
                width: '15%',
                dataIndex: 'typeCode'
            }, {
                text: lang('Name'),
                width: '100%',
                dataIndex: 'typeName'
            },
            //  {
            //     text: lang('Max Profitz'),
            //     width: '15%',
            //     dataIndex: 'typeMaxProfit'
            // },
             {
                text: lang('Simpanan Pokok'),
                hidden:true,
                renderer:  Ext.util.Format.numberRenderer('0,000'),
                width: '20%',
                xtype: 'numbercolumn',
                format:'0,000.00',
                dataIndex: 'typeSimPokokAmount'
            }, {
                text: lang('Simpanan Wajib'),
                hidden:true,
                renderer:  Ext.util.Format.numberRenderer('0,000'),
                width: '20%',
                xtype: 'numbercolumn',
                format:'0,000.00',
                dataIndex: 'typeSimWajibAmount'
            }
        ]
    });
});

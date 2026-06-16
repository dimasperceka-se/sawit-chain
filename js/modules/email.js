Ext.onReady(function() {
    Ext.tip.QuickTipManager.init();
    var store = Ext.create('Ext.data.Store', {
        extend: 'Ext.data.Model',
        fields: ['id', 'EmailSubject', 'EmailTo', 'EmailFrom', 'EmailBody', 'EmailAddTime'],
        autoLoad: true,
        pageSize: 50,
        proxy: {
            type: 'ajax',
            url: m_crud + "s",
            reader: {
                type: 'json',
                root: 'data',
                totalProperty: 'total'
            }
        }
    });

    function displayFormWindow() {
        if (!win.isVisible()) {
            resetForm();
            win.show();
            //Ext.getCmp('name').focus(true,true);
        } else {
            win.hide(this, function() {
            });
            win.toFront();
        }
    }
    function resetForm() {
        Ext.getCmp('id').setValue('');
        Ext.getCmp('EmailSubject').setValue('');
        Ext.getCmp('EmailTo').setValue('');
        Ext.getCmp('EmailBody').setValue('');
    }
    var mc_email = Ext.create('Ext.data.Store', {
        extend: 'Ext.data.Model',
        fields: ['id', 'label'],
        autoLoad: true,
        pageSize: 10,
        proxy: {
            type: 'ajax',
            url: m_email,
            reader: {
                type: 'json',
                root: 'data'
            }
        }
    });
//    var multiCombo = Ext.create('Ext.form.field.ComboBox', {
//        fieldLabel: 'Select multiple states',
//        renderTo: 'multiSelectCombo',
//        multiSelect: true,
//        displayField: 'name',
//        width: 500,
//        labelWidth: 130,
//        store: store,
//        queryMode: 'local'
//    });
    var DataForm = Ext.create('Ext.form.Panel', {
        frame: false,
        height: 550,
        width: 800,
        bodyPadding: 5,
        id: 'dataForm',
        fieldDefaults: {
            labelAlign: 'left',
            labelWidth: 100,
            anchor: '100%'
        },
        items: [
            {
                xtype: 'textfield',
                id: 'id',
                name: 'id',
                inputType: 'hidden'
            },
            {
                id: 'EmailTo',
                name: 'EmailTo[]',
                xtype: 'combo',
                emptyText: '-- To --',
                fieldLabel: 'To',
                multiSelect: true,
                store: mc_email,
                displayField: 'label',
                valueField: 'id',
                queryMode: 'local'
            },
            {
                xtype: 'textfield',
                fieldLabel: 'Subject',
                id: 'EmailSubject',
                name: 'EmailSubject'
            },
            {
                xtype: 'textarea',
                fieldLabel: 'Body',
                id: 'EmailBody',
                name: 'EmailBody'
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
                        }
                    });
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
                disabled: false,
                handler: function() {
                    win.hide();
                }
            }]
    });
    var win = Ext.create('widget.window', {
        title: 'Data Email',
        frame: false,
        closable: true,
        id: 'win',
        modal: true,
        closeAction: 'show',
        width: 630,
        minWidth: 370,
        height: 400,
        layout: 'fit',
        items: [DataForm]
    });
    function submitOnEnter(field, event) {
        if (event.getKey() == event.ENTER) {
            store.load({
                params: {
                    key: Ext.getCmp('key').getValue()
                }
            });
        }
    }

    var grid = Ext.create('Ext.grid.Panel', {
        store: store,
        width: '100%',
        id: 'grid',
        minHeight: 250,
        //title: 'Email List',
        style: 'border:1px solid #CCC;',
        renderTo: 'ext-content',
        loadMask: true,
        selType: 'rowmodel',
        listeners: {
            itemdblclick: function(dv, record, item, index, e) {
                displayFormWindow();
                var sm = record;
                Ext.Ajax.request({
                    url: m_crud,
                    method: 'GET',
                    params: {id: sm.get('id')},
                    success: function(fp, o) {
                        var r = Ext.decode(fp.responseText);
                        Ext.getCmp('id').setValue(sm.get('id'));
                        Ext.getCmp('EmailSubject').setValue(r.EmailSubject);
                        Ext.getCmp('EmailTo').setValue(r.EmailTo);
                        Ext.getCmp('EmailBody').setValue(r.EmailBody);
                    }
                });
            }
        },
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
                        handler: displayFormWindow,
                        cls: m_act_add
                    }, {
                        itemId: 'remove',
                        hidden: true,
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
                        xtype: 'datefield',
                        name: 'date',
                        id: 'date',
                        format: 'd M Y',
                        altFormats: 'Y-m-d',
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
                                    key: Ext.getCmp('key').getValue(),
                                    date: Ext.getCmp('date').getValue()
                                }});
                        }
                    }]
            }],
        columns: [
            {
                text: 'ID',
                dataIndex: 'id',
                hidden: true
            },
            {
                text: 'No',
                xtype: 'rownumberer',
                width: '5%'
            },
            {
                text: 'Subject',
                width: '35%',
                dataIndex: 'EmailSubject'
            },
            {
                text: 'To',
                width: '25%',
                dataIndex: 'EmailTo'
            },
            {
                text: 'From',
                width: '25%',
                dataIndex: 'EmailFrom'
            },
            {
                text: 'Date',
                dataIndex: 'EmailAddTime'
            }
        ]
    });
});

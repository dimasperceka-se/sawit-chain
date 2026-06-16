Ext.onReady(function() {
    Ext.tip.QuickTipManager.init();

    Ext.Ajax.request({
        url: m_header,
        success: function(response) {
            var column = [];
            var field = [];
            var data = Ext.JSON.decode(response.responseText);
            var column = data.header;
            var lang_list = data.lang;

            Ext.each(data.header, function(one, idx, all) {
                field.push(one.dataIndex);
            });

            var store = Ext.create('Ext.data.Store', {
                fields: field,
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
                },
                listeners: {
                    'beforeload': function(store, options) {
                        store.proxy.extraParams.key = Ext.getCmp('key').getValue();
                    }
                }
            });

            var grid = Ext.create('Ext.grid.Panel', {
                store: store,
                id: 'grid',
                minHeight: 250,
                style: 'border:1px solid #CCC;',
                renderTo: 'ext-content',
                loadMask: true,
                selType: 'rowmodel',
                dockedItems: [{
                        xtype: 'pagingtoolbar',
                        store: store,
                        dock: 'bottom',
                        displayInfo: true
                    }, {
                        xtype: 'toolbar',
                        items: [
                            {
                                icon: varjs.config.base_url + 'images/icons/new/add.png', cls:'Sfr_BtnGridGreen', overCls:'Sfr_BtnGridGreen-Hover',
                                text: lang('Add'),
                                cls:m_act_add,
                                scope: this,
                                handler: function() {
                                    var win = Ext.create('widget.window', {
                                        title: lang('Add') + ' ' + lang('Translation'),
                                        id: 'win-translation',
                                        modal: true,
                                        width: 600,
                                        layout: 'fit',
                                        items: Ext.create('Ext.form.Panel', {
                                            height: 300,
                                            width: '100%',
                                            bodyPadding: 5,
                                            autoScroll: true,
                                            id: 'frm-add-translation',
                                            listeners: {
                                                beforerender: function() {
                                                    var lang_count = lang_list.length;
                                                    for (var i = 0; i < lang_count; i++) {
                                                        Ext.getCmp('frm-add-translation').add({
                                                            xtype: 'textfield',
                                                            fieldLabel: lang_list[i]['text'],
                                                            name: lang_list[i]['dataIndex'],
                                                            allowBlank: false,
                                                            labelAlign: 'left',
                                                            labelWidth: 30,
                                                            width: '100%'
                                                                    //margin: '0 0 0 30'
                                                        }
                                                        );
                                                    }
                                                }
                                            },
                                            items: [{
                                                    xtype: 'textfield',
                                                    name: 'key', baseCls:'Sfr_TxtfieldSearchGrid',
                                                    fieldLabel: lang('Key'),
                                                    allowBlank: false,
                                                    width: '100%',
                                                    labelAlign: 'left',
                                                    labelWidth: 30,
                                                    validFlag: true,
                                                    validator: function() {
                                                        return this.validFlag;
                                                    },
                                                    listeners: {
                                                        'change': function(textfield, newValue, oldValue) {
                                                            var text = this;
                                                            Ext.Ajax.request({
                                                                url: m_validate,
                                                                method: 'GET',
                                                                params: 'key=' + newValue + '&id=',
                                                                success: function(response) {
                                                                    var status = Ext.decode(response.responseText);
                                                                    if (status.key_data == 'true')
                                                                        text.validFlag = true;
                                                                    else
                                                                        text.validFlag = lang('key_already_exist');
                                                                }
                                                            });
                                                        }
                                                    }
                                                }],
                                            buttons: [
                                                {
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
                                                                win.close();
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
                                cls:m_act_update,
                                scope: this,
                                handler: function() {
                                    var sm = Ext.getCmp('grid').getSelectionModel().getSelection()[0];
                                    if (!sm) {
                                        Ext.MessageBox.alert(lang('Error'), lang('Please select data'));
                                        return false;
                                    } else {
                                        var id = sm.get('key');

                                        var win = Ext.create('widget.window', {
                                            title: lang('Edit') + ' ' + lang('Translation'),
                                            id: 'win-translation',
                                            modal: true,
                                            width: 600,
                                            layout: 'fit',
                                            items: Ext.create('Ext.form.Panel', {
                                                height: 300,
                                                width: '100%',
                                                bodyPadding: 5,
                                                autoScroll: true,
                                                id: 'frm-edit-translation',
                                                listeners: {
                                                    beforerender: function(c) {
                                                        c.getForm().load({
                                                            url: m_crud,
                                                            method: 'GET',
                                                            params: {id: id},
                                                            success: function(form, action) {
                                                                var r = Ext.decode(action.response.responseText);
                                                                var lang_exist = r.data;
                                                                var lang_exist_count = lang_exist.length;
                                                                for (var i = 0; i < lang_exist_count; i++) {
                                                                    Ext.getCmp('frm-edit-translation').add([{
                                                                            xtype: 'textfield',
                                                                            fieldLabel: lang_exist[i]['name'],
                                                                            name: lang_exist[i]['dataIndex'],
                                                                            allowBlank: false,
                                                                            value: lang_exist[i]['text'],
                                                                            width: '100%',
                                                                            labelAlign: 'left',
                                                                            labelWidth: 30
                                                                        }, {
                                                                            xtype: 'textfield',
                                                                            name: 'trans_id_' + lang_exist[i]['dataIndex'],
                                                                            value: lang_exist[i]['trans_id'],
                                                                            inputType: 'hidden'
                                                                        }]);
                                                                }
                                                            }

                                                        });
                                                    }
                                                },
                                                items: [
                                                    {
                                                        xtype: 'textfield',
                                                        id: 'key_old',
                                                        name: 'key_old',
                                                        value: id,
                                                        hidden: true
                                                    },
                                                    {
                                                        xtype: 'textfield',
                                                        name: 'key', baseCls:'Sfr_TxtfieldSearchGrid',
                                                        fieldLabel: lang('Key'),
                                                        allowBlank: false,
                                                        value: id,
                                                        width: '100%',
                                                        labelAlign: 'left',
                                                        labelWidth: 30,
                                                        validFlag: true,
                                                        validator: function() {
                                                            return this.validFlag;
                                                        },
                                                        listeners: {
                                                            'change': function(textfield, newValue, oldValue) {
                                                                var text = this;
                                                                Ext.Ajax.request({
                                                                    url: m_validate,
                                                                    method: 'GET',
                                                                    params: 'key=' + newValue + '&id=' + Ext.getCmp('key_old').getValue(),
                                                                    success: function(response) {
                                                                        var status = Ext.decode(response.responseText);
                                                                        if (status.key_data == 'true')
                                                                            text.validFlag = true;
                                                                        else
                                                                            text.validFlag = lang('key_already_exist');
                                                                    }
                                                                });
                                                            }
                                                        }
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
                                                            form.submit({
                                                                url: m_crud,
                                                                method: 'PUT',
                                                                waitMsg: 'Sending data...',
                                                                success: function(fp, o) {
                                                                    Ext.MessageBox.alert('Success', 'Data Updated.');
                                                                    win.close();
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
                                text: lang('Delete'),
                                cls:m_act_delete,
                                scope: this,
                                handler: function() {
                                    var smb = Ext.getCmp('grid').getSelectionModel().getSelection()[0];
                                    if (!smb) {
                                        Ext.MessageBox.alert(lang('error'), lang('please_select_data'));
                                        return false;
                                    } else {
                                        Ext.MessageBox.confirm('Message', lang('are_you_sure_to_delete_this_data?'), function(btn) {
                                            if (btn == 'yes') {
                                                Ext.Ajax.request({
                                                    waitMsg: 'Please Wait',
                                                    url: m_crud,
                                                    method: 'DELETE',
                                                    params: {id: smb.raw.key},
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
                                }
                            }, {
                                xtype: 'textfield',
                                name: lang('Key'),
                                id: 'key',
                                width: '30%',
                                labelAlign: 'left',
                                labelWidth: 30,
                                listeners: {
                                    specialkey: submitOnEnter
                                }
                            }, {
                                xtype: 'button',
                                margin: '0px 0px 0px 6px',
                                text: lang('Search'),
                                handler: function() {
                                    store.load({
                                        params: {
                                            page: 1,
                                            start: 0,
                                            limit: 50
                                        }
                                    });
                                }
                            }
                        ]
                    }],
                columns: column
            });

            function submitOnEnter(field, event) {
                if (event.getKey() == event.ENTER) {
                    store.load({
                        params: {
                            page: 1,
                            start: 0,
                            limit: 50
                        }
                    });
                }
            }
        }
    });

    /*function validateKey(v, callback) {
     Ext.Ajax.request({
     url: m_validate,
     method: 'GET',
     params: 'key=' + v + '&id=',
     success: function(o) {
     var get_data = Ext.decode(o.responseText);
     var ret = get_data.key_data;
     callback(ret);
     },
     failure: function(o) {
     callback(false);
     }
     });
     }*/

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
});



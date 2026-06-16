Ext.onReady(function () {
    Ext.tip.QuickTipManager.init();
    var store = Ext.create('Ext.data.Store', {
        extend: 'Ext.data.Model',
        fields: ['id', 'UserRealName', 'UserName', 'UserActive', 'GroupId', 'GroupName', 'UserType'],
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
    store.on('beforeload', function() {
      var proxy = store.getProxy();
      proxy.setExtraParam('key', Ext.getCmp('key').getValue());
      proxy.setExtraParam('groupId', Ext.getCmp('sGroupId').getValue());
   });

    var mc_group = Ext.create('Ext.data.Store', {
        extend: 'Ext.data.Model',
        fields: ['GroupId', 'GroupName'],
        autoLoad: true,
        proxy: {
            type: 'ajax',
            url: m_group,
            reader: {
                type: 'json',
                root: 'data'
            }
        }
    });

    var mc_group_search = Ext.create('Ext.data.Store', {
        extend: 'Ext.data.Model',
        fields: ['id','label'],
        autoLoad: true,
        proxy: {
            type: 'ajax',
            url: m_group_search,
            reader: {
                type: 'json',
                root: 'data'
            }
        }
    });

    function displayFormWindow() {
        if (!win.isVisible()) {
            resetForm();
            win.show();
            //Ext.getCmp('name').focus(true,true);
        } else {
            win.hide(this, function () {
            });
            win.toFront();
        }
    }

    function resetForm() {
        Ext.getCmp('id').setValue('');
        Ext.getCmp('UserRealName').setValue('');
        Ext.getCmp('UserName').setValue('');
        Ext.getCmp('UserPassword').setValue('');
        Ext.getCmp('UserActive1').setValue(false);
        Ext.getCmp('UserActive2').setValue(false);
        Ext.getCmp('UserGroupGroupId').setValue('');
    }

    var DataForm = Ext.create('Ext.form.Panel', {
        frame: false,
        height: 250,
        width: 400,
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
                xtype: 'textfield',
                fieldLabel: 'Real Name',
                id: 'UserRealName',
                name: 'UserRealName'
            },
            {
                xtype: 'textfield',
                fieldLabel: 'User Name',
                id: 'UserName',
                name: 'UserName'
            },
            {
                xtype: 'textfield',
                //inputType:'password',
                fieldLabel: 'Password',
                id: 'UserPassword',
                name: 'UserPassword'
            }, {
                xtype: 'button',
                margin: '0px 0px 0px 6px',
                text: 'Generate',
                handler: function () {
                    Ext.getCmp('UserPassword').setValue(Math.random().toString(36).substring(7))
                }
            }, {
                xtype: 'radiogroup',
                fieldLabel: 'Active',
                defaultType: 'radiofield',
                defaults: {
                    flex: 1
                },
                layout: 'hbox',
                items: [
                    {
                        boxLabel: 'Yes',
                        name: 'UserActive',
                        id: 'UserActive1',
                        inputValue: 'Yes'
                    }, {
                        boxLabel: 'No',
                        name: 'UserActive',
                        id: 'UserActive2',
                        inputValue: 'No'
                    }
                ]
            },
            {
                id: 'UserGroupGroupId',
                name: 'UserGroupGroupId',
                xtype: 'combobox',
                fieldLabel: 'Group',
                store: mc_group,
                displayField: 'GroupName',
                valueField: 'GroupId',
                queryMode: 'local'
            }
        ],
        buttons: [{
            id: 'saveButton',
            text: 'Save',
            margin: '5px',
            scale: 'large',
            ui: 's-button',
            cls: m_act_update,
            handler: function () {
                var form = this.up('form').getForm();
                var methode;
                if (Ext.getCmp('id').getValue() == '') methode = 'POST'; else methode = 'PUT';
                form.submit({
                    url: m_crud,
                    method: methode,
                    waitMsg: 'Sending data...',
                    success: function (fp, o) {
                        Ext.MessageBox.alert('Success', 'Data saved.');
                    }
                });
                win.hide(this, function () {
                    store.load({
                            params: {
                                key: Ext.getCmp('key').getValue(),
                                groupId: Ext.getCmp('sGroupId').getValue()
                            }
                        });
                });
            }
        }, {
            text: 'Close',
            margin: '5px',
            scale: 'large',
            ui: 's-button',
            cls: 's-grey',
            disabled: false,
            handler: function () {
                win.hide();
            }
        }]
    });
    var win = Ext.create('widget.window', {
        title: 'Data User',
        frame: false,
        closable: true,
        id: 'win',
        modal: true,
        closeAction: 'show',
        width: 430,
        minWidth: 370,
        height: 300,
        layout: 'fit',
        items: [DataForm]
    });

    function submitOnEnter(field, event) {
        if (event.getKey() == event.ENTER) {
            store.load({
                            params: {
                                key: Ext.getCmp('key').getValue(),
                                groupId: Ext.getCmp('sGroupId').getValue()
                            }
                        });
        }
    }

    var grid = Ext.create('Ext.grid.Panel', {
        store: store,
        width: '100%',
        id: 'grid',
        minHeight: 250,
        //title: 'User List',
        style: 'border:1px solid #CCC;',
        renderTo: 'ext-content',
        loadMask: true,
        selType: 'rowmodel',
        listeners: {
            itemdblclick: function (dv, record, item, index, e) {
                displayFormWindow();
                var sm = record;
                Ext.Ajax.request({
                    url: m_crud,
                    method: 'GET',
                    params: {id: sm.get('id')},
                    success: function (fp, o) {
                        var r = Ext.decode(fp.responseText);
                        Ext.getCmp('id').setValue(sm.get('id'));
                        Ext.getCmp('UserRealName').setValue(r.UserRealName);
                        Ext.getCmp('UserName').setValue(r.UserName);
                        Ext.getCmp('UserGroupGroupId').setValue(r.GroupId);
                        if (r.UserActive == 'Yes') Ext.getCmp('UserActive1').setValue(true);
                        if (r.UserActive == 'No') Ext.getCmp('UserActive2').setValue(true);
                    }
                });
            }
        },
        dockedItems: [{
            xtype: 'pagingtoolbar',
            store: store,   // same store GridPanel is using
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
                    icon: varjs.config.base_url + 'images/icons/new/delete.png',
                    cls: m_act_delete,
                    text: 'Hapus',
                    scope: this,
                    handler: function () {
                        var smb = Ext.getCmp('grid').getSelectionModel().getSelection()[0];
                        Ext.MessageBox.confirm('Message', 'Apakah anda mau menghapus data ini ?', function (btn) {
                            if (btn == 'yes') {
                                Ext.Ajax.request({
                                    waitMsg: 'Please Wait',
                                    url: m_crud,
                                    method: 'DELETE',
                                    params: {id: smb.raw.id},
                                    success: function (response, opts) {
                                        var obj = Ext.decode(response.responseText);
                                        switch (obj.success) {
                                            case true:
                                                store.load({
                                                    params: {
                                                        key: Ext.getCmp('key').getValue(),
                                                        groupId: Ext.getCmp('sGroupId').getValue()
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
                }, {
                   xtype: 'textfield',
                    name: 'key', baseCls:'Sfr_TxtfieldSearchGrid',
                    id: 'key',
                    listeners: {
                        specialkey: submitOnEnter
                    }
               },{
                   id: 'sGroupId',
                   name: 'sGroupId',
                   xtype: 'combo',
                   store:mc_group_search,
                   displayField: 'label',
                   valueField: 'id',
                   queryMode: 'local',
                   width:350
                }, {
                    xtype: 'button',
                    margin: '0px 0px 0px 6px',
                    text: 'Search',
                    handler: function () {
                        store.load({
                            params: {
                                key: Ext.getCmp('key').getValue(),
                                groupId: Ext.getCmp('sGroupId').getValue(),
                                page:1,
                                start:0,
                                limit:50
                            }
                        });
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
                text: 'Real Name',
                width: '20%',
                dataIndex: 'UserRealName'
            },
            {
                text: 'User Name',
                width: '20%',
                dataIndex: 'UserName'
            },
            {
                text: 'Active',
                width: '10%',
                dataIndex: 'UserActive'
            },
            {
                text: 'User Type',
                width: '15%',
                dataIndex: 'UserType'
            },
            {
                text: 'Group',
                width: '25%',
                dataIndex: 'GroupName'
            }]
    });
});
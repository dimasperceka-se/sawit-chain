/*
 * @Author: nikolius
 * @Date:   2016-03-17 17:39:46
 * @Last Modified by:   nikolius
 * @Last Modified time: 2017-03-09 10:55:03
 */
Ext.onReady(function() {
    Ext.tip.QuickTipManager.init();
    Ext.define('Scpp.Model', {
        extend: 'Ext.data.Model',
        fields: ['PositionID', 'PositionCode','PositionName', 'Category', 'StatusCode'],
    });
    var store = Ext.create('Ext.data.Store', {
        extend: 'Ext.data.Model',
        fields: ['PositionID', 'PositionCode', 'PositionName', 'Category', 'StatusCode'],
        autoLoad: true,
        pageSize: 50,
        remoteSort: true,
        proxy: {
            type: 'ajax',
            url: m_crud + 's',
            params: {
                'X-API-KEY': '030584'
            },
            reader: {
                type: 'json',
                root: 'data',
                totalProperty: 'total'
            }
        }
    });

    var cmbCategory = Ext.create('Ext.data.Store', {
        extend: 'Ext.data.Model',
        fields: ['id', 'label'],
        autoLoad: true,
        proxy: {
            type: 'ajax',
            url: m_combo_role,
            reader: {
                type: 'json',
                root: 'data'
            }
        }
    });
    var cmbStatusCode = Ext.create('Ext.data.Store', {
        fields: ['id', 'label'],
        data: [{
            "id": "active",
            "label": "ACTIVE"
        }, {
            "id": "inactive",
            "label": "INACTIVE"
        }]
    });
    var RowEditing = Ext.create('Ext.grid.plugin.RowEditing', {
        id: 'RowEditing',
        clicksToMoveEditor: 0,
        autoCancel: false,
        errorSummary: false,
        clicksToEdit: 2
    });
    var grid = Ext.create('Ext.grid.Panel', {
        store: store,
        width: '100%',
        minHeight: 250,
        id: 'grid',
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
            minHeight: 38,
            items: [{
                icon: varjs.config.base_url + 'images/icons/new/add.png', cls:'Sfr_BtnGridGreen', overCls:'Sfr_BtnGridGreen-Hover',
                text: lang('Add'),
                scope: this,
                cls: m_act_add,
                handler: function() {
                    RowEditing.cancelEdit();
                    var r = Ext.create('Scpp.Model', {
                        PositionID: '',
                        PositionName: '',
                        Category: '',
                        StatusCode: ''
                    });
                    store.insert(0, r);
                    RowEditing.startEdit(0, 0);
                }
            }, {
                itemId: 'remove',
                icon: varjs.config.base_url + 'images/icons/new/delete.png',
                cls: m_act_delete,
                text: lang('Delete'),
                scope: this,
                handler: function() {
                    var smb = Ext.getCmp('grid').getSelectionModel().getSelection()[0];
                    RowEditing.cancelEdit();
                    Ext.MessageBox.confirm('Message', 'Are you sure want to delete this data ?', function(btn) {
                        if (btn == 'yes') {
                            Ext.Ajax.request({
                                waitMsg: 'Please Wait',
                                url: m_crud,
                                method: 'DELETE',
                                params: {
                                    PositionID: smb.raw.PositionID
                                },
                                success: function(response, opts) {
                                    var obj = Ext.decode(response.responseText);
                                    switch (obj.success) {
                                        case true:
                                            Ext.MessageBox.alert('Success', obj.message);
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
            },{
                name: 'key', baseCls:'Sfr_TxtfieldSearchGrid',
                id: 'key',
                xtype: 'textfield',
                width: 250,
                emptyText: lang('Cari berdasar Kode, Nama or Rule'),
                listeners: {
                    specialkey: submitOnEnter
                }
            }, {
                xtype: 'button',
                icon: varjs.config.base_url + 'images/icons/silk/search.png',
                margin: '0px 0px 0px 6px',
                text: lang('Search'),
                handler: function() {
                    store.getProxy().extraParams = {
                        'key': Ext.getCmp('key').getValue()
                      };
                    store.load({
                        params: {
                            key: Ext.getCmp('key').getValue(),
                        }
                    });
                }
            }]
        }],
        columns: [{
            dataIndex: 'PositionID',
            hidden: true
        }, {
            text: 'No',
            xtype: 'rownumberer',
            width: '3%'
        }, {
            text: lang('Position Code'),
            width: '20%',
            dataIndex: 'PositionCode',
            editor: {
                xtype: 'textfield',
                allowBlank: false
            }
        },{
            text: lang('position_name'),
            width: '37%',
            dataIndex: 'PositionName',
            editor: {
                xtype: 'textfield',
                allowBlank: false
            }
        }, {
            text: lang('Role'),
            width: '20%',
            dataIndex: 'Category',
            editor: {
                allowBlank: false,
                xtype: 'combo',
                store: cmbCategory,
                id: 'Category',
                queryMode: 'local',
                displayField: 'label',
                valueField: 'id',
                editable: false
            }
        }, {
            text: lang('Status'),
            width: '20%',
            dataIndex: 'StatusCode',
            renderer: Ext.util.Format.uppercase,
            editor: {
                xtype: 'combo',
                store: cmbStatusCode,
                id: 'StatusCode',
                queryMode: 'local',
                displayField: 'label',
                valueField: 'id',
                editable: false
            }
        }],
        plugins: [RowEditing],
        listeners: {
            'canceledit': function(editor, e, eOpts) {
                store.load();
            },
            'edit': function(editor, e) {
                var PositionID = e.record.data.PositionID;
                var PositionCode = e.record.data.PositionCode;
                var PositionName = e.record.data.PositionName;
                var Category = e.record.data.Category;
                var StatusCode = e.record.data.StatusCode;
                if (PositionID.trim() === '') {
                    console.log('insert');
                    Ext.Ajax.request({
                        waitMsg: 'Please wait...',
                        url: m_crud,
                        method: 'POST',
                        params: {
                            PositionCode: PositionCode,
                            PositionName: PositionName,
                            Category: Category,
                            StatusCode: StatusCode
                        },
                        success: function(response, opts) {
                            console.log(response);
                            var obj = Ext.decode(response.responseText);
                            console.log(obj);
                            switch (obj.success) {
                                case true:
                                    Ext.MessageBox.alert('Success', obj.message);
                                    store.load();
                                    break;
                                default:
                                    Ext.MessageBox.alert('Warning', obj.message);
                                    break;
                            }
                        },
                        failure: function(response, opts) {
                            var obj = Ext.decode(response.responseText);
                            console.log(obj);
                            Ext.MessageBox.alert('error', 'Could not connect to the database. Retry later');
                        }
                    });
                } else {
                    Ext.MessageBox.confirm('Message', 'Do you want to update ?', function(btn) {
                        if (btn == 'yes') {
                            Ext.Ajax.request({
                                waitMsg: 'Please wait...',
                                url: m_crud,
                                method: 'PUT',
                                params: {
                                    PositionID: PositionID,
                                    PositionCode: PositionCode,
                                    PositionName: PositionName,
                                    Category: Category,
                                    StatusCode: StatusCode
                                },
                                success: function(response, opts) {
                                    var obj = Ext.decode(response.responseText);
                                    console.log(obj);
                                    switch (obj.success) {
                                        case true:
                                            Ext.MessageBox.alert('Success', obj.message);
                                            store.load();
                                            break;
                                        default:
                                            Ext.MessageBox.alert('Warning', obj.message);
                                            break;
                                    }
                                },
                                failure: function(response, opts) {
                                    var obj = Ext.decode(response.responseText);
                                    console.log(obj);
                                    Ext.MessageBox.alert('error', 'Could not connect to the database. Retry later');
                                }
                            });
                        }
                    });
                }
            }
        }
    });

    function submitOnEnter(field, event) {
        if (event.getKey() == event.ENTER) {
            store.getProxy().extraParams = {
                'key': Ext.getCmp('key').getValue()
            };
            store.load({
                params: {
                    key: Ext.getCmp('key').getValue()
                }
            });
        }
    }
});


var height = window.innerHeight - 100;
var parent_id = null;
var parent_type = null;
var rule = '';
Ext.onReady(function () {
    var treeStore = Ext.create('Ext.data.TreeStore', {
        autoLoad: true,
        proxy: {
            type: 'ajax',
            url: m_tree
        }
    });
    Ext.define('Scpp.model.Region', {
        extend: 'Ext.data.Model',
        fields: ['id', 'name'],
    });
    var gridStore = Ext.create('Ext.data.Store', {
        storeId: 'regionStore',
        model: 'Scpp.model.Region',
        autoLoad: true,
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

    function showWindowForm(data) {
        switch (parent_type) {
            case 'province':
                rule = new RegExp("^" + parent_id + "[0-9]{2}$");
                title = lang('District');
                type = 'district';
                break;
            case 'district':
                rule = new RegExp("^" + parent_id + "[0-9]{3}$");
                title = lang('Sub District');
                type = 'subdistrict';
                break;
            case 'subdistrict':
                rule = new RegExp("^" + parent_id + "[0-9]{3}$");
                title = lang('Village');
                type = 'village';
                break;
            default:
                rule = new RegExp("^[0-9]{2}$");
                title = lang('Province');
                type = 'province';
                break;
        }

        form = itemForm.getForm();
        field_id = form.findField('id');
        form.findField('parent_id').setValue(parent_id);
        form.findField('type').setValue(type);
        if (data) {
            act = lang('Edit');
            field_id.setValue(data.id);
            field_id.setDisabled(true);
            form.findField('name').setValue(data.name);
            form.findField('mode').setValue('edit');
            form.findField('old_id').setValue(data.id);
        } else {
            act = lang('Add');
            field_id.setValue(parent_id);
            field_id.setDisabled(false);
            form.findField('name').setValue('');
            form.findField('mode').setValue('add');
        }
        field_id.regex = rule;
        windowForm.setTitle(lang(act) + ' ' + title);
        if (!windowForm.isVisible()) {
            windowForm.show();
        } else {
            windowForm.hide(this, function () {
            });
            windowForm.toFront();
        }
        field_id.focus();
    }

    var itemForm = Ext.create('Ext.form.Panel', {
        frame: false,
        height: 130,
        autoScroll: true,
        width: 580,
        bodyPadding: 5,
        id: 'dataForm',
        fieldDefaults: {
            labelAlign: 'left',
            labelWidth: 100,
            anchor: '100%'
        },
        items: [
            {
                xtype: 'hiddenfield',
                id: 'type',
                name: 'type'
            },
            {
                xtype: 'hiddenfield',
                id: 'mode',
                name: 'mode'
            },
            {
                xtype: 'hiddenfield',
                id: 'old_id',
                name: 'old_id'
            },
            {
                xtype: 'hiddenfield',
                id: 'parent_id',
                name: 'parent_id'
            },
            {
                xtype: 'textfield',
                id: 'id',
                name: 'id',
                fieldLabel: lang('ID'),
                allowBlank: false,
                regex: rule
            },
            {
                xtype: 'textfield',
                id: 'name',
                name: 'name',
                fieldLabel: lang('Name'),
                allowBlank: false
            },
        ],
        buttons: [
            {
                id: 'saveButton',
                text: lang('Save'),
                margin: '5px',
                scale: 'large',
                ui: 's-button',
                cls: 's-blue',
                handler: function () {
                    var form = Ext.getCmp('dataForm').getForm();
                    form.submit({
                        url: m_crud,
                        waitMsg: lang('Saving....'),
                        success: function (form, action) {
                            treeStore.load();
                            treePanel = Ext.getCmp('treeRegion');
                            var record = treePanel.getStore().getNodeById(parent_id);
                            treePanel.getSelectionModel().select(record);

                            gridStore.load({
                                params: {
                                    type: parent_type,
                                    id: parent_id
                                }
                            });
                            windowForm.hide();
                            if (action.result.msg)
                                Ext.Msg.alert('Success', action.result.msg);
                        },
                        failure: function (form, action) {
                            switch (action.failureType) {
                                case Ext.form.action.Action.CLIENT_INVALID:
                                    Ext.Msg.alert('Failure', 'Form fields may not be submitted with invalid values');
                                    break;
                                case Ext.form.action.Action.CONNECT_FAILURE:
                                    Ext.Msg.alert('Failure', 'Ajax communication failed');
                                    break;
                                case Ext.form.action.Action.SERVER_INVALID:
                                    Ext.Msg.alert('Failure', action.result.msg);
                            }
                        }
                    });
                }
            }
            , {
                text: lang('Close'),
                margin: '5px',
                scale: 'large',
                ui: 's-button',
                cls: 's-grey',
                disabled: false,
                handler: function () {
                    windowForm.hide();
                }
            }
        ]
    });
    var windowForm = Ext.create('widget.window', {
        title: 'Form',
        id: 'win',
        closable: true,
        modal: true,
        closeAction: 'hide',
        width: 600,
        height: 180,
        layout: {
            type: 'border',
            padding: 5
        },
        items: [itemForm]
    });
    var resultsPanel = Ext.create('Ext.panel.Panel', {
        // title: 'Results',
        // width: 600,
        height: height,
        renderTo: 'ext-content',
        layout: {
            type: 'hbox',       // Arrange child items horizontally
            align: 'stretch',    // Each takes up full width
            padding: 5
        },
        items: [{
            id: 'treeRegion',
            xtype: 'treepanel',
            title: lang('Region'),
            style: 'border:1px solid #CCC;',
            width: 200,
            height: '100%',
            store: treeStore,
            rootVisible: false,
            flex: 1,
            listeners: {
                itemClick: function (view, record, item, index, e, eOpts) {
                    parent_id = record.data.id;
                    parent_type = record.data.cls;
                    if (record.data.cls != 'village') {
                        switch (record.data.cls) {
                            case 'province':
                                title = lang('District');
                                break;
                            case 'district':
                                title = lang('Sub District');
                                break;
                            case 'subdistrict':
                                title = lang('Village');
                                break;
                            default:
                                title = lang('Province');
                                break;
                        }
                        Ext.getCmp('gridDetails').setTitle(title);
                        gridStore.load({
                            params: {
                                type: record.data.cls,
                                id: record.data.id
                            }
                        });
                    }
                    ;
                },
            }
        }, {
            xtype: 'splitter'
        }, {
            id: 'gridDetails',
            xtype: 'gridpanel',
            title: lang('Province'),
            style: 'border:1px solid #CCC;',
            store: gridStore,
            loadMask: true,
            selType: 'rowmodel',
            columns: [
                {text: lang('ID'), dataIndex: 'id', flex: 1},
                {text: lang('Name'), dataIndex: 'name', flex: 4},
                {
                    menuDisabled: true,
                    sortable: false,
                    xtype: 'actioncolumn',
                    width: 80,
                    align: 'center',
                    items: [
                        {
                            icon: m_url + '/images/icons/silk/page_white_edit.png',
                            tooltip: lang('Edit'),
                            handler: function (grid, rowIndex, colIndex) {
                                var rec = grid.getStore().getAt(rowIndex);
                                showWindowForm(rec.data);
                            }
                        }
                        , {
                            icon: m_url + '/images/delete.png',
                            tooltip: lang('Delete'),
                            handler: function (grid, rowIndex, colIndex) {
                                var rec = grid.getStore().getAt(rowIndex);
                                switch (parent_type) {
                                    case 'province':
                                        type = 'district';
                                        break;
                                    case 'district':
                                        type = 'subdistrict';
                                        break;
                                    case 'subdistrict':
                                        type = 'village';
                                        break;
                                    default:
                                        type = 'province';
                                        break;
                                }
                                Ext.MessageBox.confirm('Message', rec.data.name + '<br/>' + lang('Apakah anda mau menghapus data ini?'), function (btn) {
                                    if (btn == 'yes') {
                                        Ext.Ajax.request({
                                            waitMsg: lang('Please Wait'),
                                            url: m_crud,
                                            method: 'DELETE',
                                            params: {
                                                id: rec.data.id,
                                                type: type
                                            },
                                            success: function (response, opts) {
                                                var obj = Ext.decode(response.responseText);
                                                if (obj.success) {
                                                    gridStore.load({
                                                        params: {
                                                            type: parent_type,
                                                            id: parent_id
                                                        }
                                                    });

                                                    treeStore.load();
                                                    treePanel = Ext.getCmp('treeRegion');
                                                    var record = treePanel.getStore().getNodeById(parent_id);
                                                    treePanel.getSelectionModel().select(record);
                                                } else {
                                                    Ext.MessageBox.alert('Warning', obj.message);
                                                }
                                            },
                                            failure: function (response, opts) {
                                                // var obj = Ext.decode(response.responseText);
                                                Ext.MessageBox.alert('error', 'Could not connect to the database. Retry later');
                                            }
                                        });
                                    }
                                });

                            }
                        },
                    ]
                }
            ],
            height: '100%',
            flex: 3,
            dockedItems: [
                {
                    xtype: 'toolbar',
                    items: [
                        {
                            xtype: 'button',
                            icon: varjs.config.base_url + 'images/icons/new/add.png', cls:'Sfr_BtnGridGreen', overCls:'Sfr_BtnGridGreen-Hover',
                            text: lang('Add'),
                            handler: function () {
                                showWindowForm();
                            }
                        }
                    ]
                }
            ],
        }]
    });
})
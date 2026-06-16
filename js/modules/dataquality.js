Ext.onReady(function () {
    Ext.tip.QuickTipManager.init();
    Ext.define('Scpp.Model', {
        extend: 'Ext.data.Model',
        fields: ['dq_id', 'dq_name', 'dq_result', 'dqvalue_value', 'dqvalue_date', 'program'],
    });
    var store = Ext.create('Ext.data.Store', {
        model: 'Scpp.Model',
        autoLoad: true,
        pageSize: 50,
        groupField: 'program',
        proxy: {
            type: 'ajax',
            url: m_crud + "s",
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
    store.loadPage(1);

    var mc_program = Ext.create('Ext.data.Store', {
        extend: 'Ext.data.Model',
        fields: ['id', 'label'],
        autoLoad: true,
        pageSize: 10,
        proxy: {
            type: 'ajax',
            url: m_program,
            reader: {
                type: 'json',
                root: 'data'
            }
        }
    });

    var mc_programsection = Ext.create('Ext.data.Store', {
        extend: 'Ext.data.Model',
        fields: ['id', 'label'],
        autoLoad: false,
        pageSize: 10,
        proxy: {
            type: 'ajax',
            url: m_programsection,
            reader: {
                type: 'json',
                root: 'data'
            }
        }
    });

    var contextAction = Ext.create('Ext.menu.Menu', {
        items: [{
                icon: varjs.config.base_url + 'images/icons/silk/script_start.png',
                text: 'Show Data',
                itemId: 'contextMenuRunQueryItem',
//                hidden: m_run_query,
                handler: function () {
                    var sm = Ext.getCmp('grid').getSelectionModel().getSelection()[0];
                    displayRunQuery(sm.get('dq_id'));
                }
            }, {
                icon: varjs.config.base_url + 'images/icons/silk/arrow_refresh.png',
                text: 'Calculate',
                handler: function () {
                    var sm = Ext.getCmp('grid').getSelectionModel().getSelection()[0];
                    Ext.Ajax.request({
                        waitMsg: lang('Please Wait'),
                        url: m_calculate,
                        method: 'GET',
                        params: {id: sm.get('dq_id')},
                        success: function (response, opts) {
                            var obj = Ext.decode(response.responseText);
                            switch (obj.success) {
                                case true:
                                    Ext.MessageBox.alert('Success', 'Data calculated.');
                                    store.load();
                                    break;
                                default:
                                    Ext.MessageBox.alert('Warning', obj.message);
                                    break;
                            }
                        },
                        failure: function (response, opts) {
                            var obj = Ext.decode(response.responseText);
                            Ext.MessageBox.alert('error', lang('Could not connect to the database. Retry later'));
                        }
                    });
                }
            }, {
                icon: varjs.config.base_url + 'images/icons/new/update.png',
                text: 'Edit',
//                hidden: m_act_update,
                handler: function () {
                    var dq = Ext.getCmp('grid').getSelectionModel().getSelection()[0];
                    displayMainForm('update', store, dq.get('dq_id'), false);
                }
            }, {
                icon: varjs.config.base_url + 'images/icons/new/delete.png',
                text: 'Delete',
                handler: function () {
                    Ext.MessageBox.confirm('Message', lang('Apakah anda mau menghapus data ini ?'), function (btn) {
                        if (btn == 'yes') {
                            var sm = Ext.getCmp('grid').getSelectionModel().getSelection()[0];
                            Ext.Ajax.request({
                                waitMsg: lang('Please Wait'),
                                url: m_delete,
                                method: 'DELETE',
                                params: {id: sm.get('dq_id')},
                                success: function (response, opts) {
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
                                failure: function (response, opts) {
                                    var obj = Ext.decode(response.responseText);
                                    Ext.MessageBox.alert('error', lang('Could not connect to the database. Retry later'));
                                }
                            });
                        }
                    });
                }
            }
        ]
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
        listeners: {
            itemclick: function (view, record, item, index, e) {
                contextAction.showAt(e.getXY());
            }
        },
        dockedItems: [{
                xtype: 'pagingtoolbar',
                store: store, // same store GridPanel is using
                dock: 'bottom',
                displayInfo: true
            }, {
                xtype: 'toolbar',
                items: [{
                        icon: varjs.config.base_url + 'images/icons/new/add.png', cls:'Sfr_BtnGridGreen', overCls:'Sfr_BtnGridGreen-Hover',
                        text: lang('Add'),
                        scope: this,
//                        hidden: m_act_add,
                        handler: function () {
                            displayMainForm('add', store, null, false);
                        }
                    }, {
                        xtype: 'label',
                        text: 'Search'
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
                        handler: function () {
                            store.load({
                                params: {
                                    key: Ext.getCmp('key').getValue()
                                }});
                        }
                    }]
            }],
        columns: [{
                text: 'Name',
                width: '60%',
                dataIndex: 'dq_name'
            }, {
                text: 'Total',
                width: '20%',
                dataIndex: 'dqvalue_value'
            }, {
                text: 'Status',
                width: '10%',
                dataIndex: 'dq_result'
            }, {
                text: 'Last Calculated',
                width: '10%',
                dataIndex: 'dqvalue_date'
            }
        ],
        viewConfig: {
            stripeRows: false,
            getRowClass: function (record, rowIndex, rowParams, store) {
                if (record.get('dq_result') === 'invalid' && record.get('dqvalue_value') > 0)
                    return 'rowInvalid';
                else if (record.get('dq_result') === 'valid')
                    return 'rowValid';
            }
        },
        features: [{
                id: 'group',
                ftype: 'grouping',
                groupHeaderTpl: '{name}',
                hideGroupedHeader: true,
//                remoteRoot: 'summaryData',
                enableGroupingMenu: true
            }]
    });

    function displayMainForm(displayMethod, store, dq_id, viewOnly) {
        var winMainForm = Ext.create('widget.window', {
            title: 'Data Quality Form',
            id: 'winMainForm',
            closable: true,
            modal: true,
            closeAction: 'destroy',
            width: '65%',
            height: '90%',
            overflowY: 'auto',
            bodyStyle: {"background-color": "#F0F0F0"},
            style: 'background-color:#F0F0F0;',
            padding: 6,
            scrollOffset: 20,
            items: [{
                    xtype: 'form',
                    id: 'mainForm',
                    fileUpload: true,
                    padding: '5 20 5 8',
                    items: [{
                            layout: 'column',
                            border: false,
                            items: [{
                                    columnWidth: 1,
                                    padding: 4,
                                    layout: 'form',
                                    items: [{
                                            xtype: 'hiddenfield',
                                            id: 'dq_id',
                                            name: 'dq_id'
                                        }, {
                                            xtype: 'textfield',
                                            fieldLabel: lang('Name'),
                                            labelWidth: 150,
                                            allowBlank: false,
                                            id: 'dq_name',
                                            name: 'dq_name'
                                        }, {
                                            xtype: 'textfield',
                                            fieldLabel: lang('Description'),
                                            labelWidth: 150,
                                            id: 'dq_description',
                                            name: 'dq_description'
                                        }, {
                                            id: 'dq_program',
                                            name: 'dq_program',
                                            xtype: 'combo',
                                            emptyText: '-- Program --',
                                            fieldLabel: 'Program',
                                            multiSelect: false,
                                            store: mc_program,
                                            displayField: 'label',
                                            valueField: 'id',
                                            queryMode: 'local',
                                            listeners: {
                                                change: function (cb, nv, ov) {
                                                    mc_programsection.load({
                                                        params: {
                                                            dqprogram_id: Ext.getCmp('dq_program').getValue()
                                                        }
                                                    });
                                                }
                                            }
                                        }, {
                                            id: 'dq_programsection',
                                            name: 'dq_programsection',
                                            xtype: 'combo',
                                            emptyText: '-- Program Section --',
                                            fieldLabel: 'Program Section',
                                            multiSelect: false,
                                            store: mc_programsection,
                                            displayField: 'label',
                                            valueField: 'id',
                                            queryMode: 'local'
                                        }, {
                                            xtype: 'fieldcontainer',
                                            fieldLabel: lang('Status'),
                                            defaults: {
                                                flex: 1
                                            },
                                            layout: 'hbox',
                                            items: [{
                                                    boxLabel: 'Active',
                                                    xtype: 'radio',
                                                    name: 'dq_status',
                                                    inputValue: 'active',
                                                    checked: true,
                                                    id: 'status1'
                                                }, {
                                                    boxLabel: 'Inactive',
                                                    xtype: 'radio',
                                                    name: 'dq_status',
                                                    inputValue: 'inactive',
                                                    id: 'status2'
                                                }]
                                        }, {
                                            xtype: 'textareafield',
                                            grow: true,
                                            anchor: '100%',
                                            height: 500,
                                            fieldLabel: 'Query',
                                            labelWidth: 150,
                                            allowBlank: false,
                                            msgTarget: 'side',
                                            id: 'dq_query',
                                            name: 'dq_query'
                                        }]
                                }]
                        }]
                }],
            buttons: [{
                    text: 'Save',
                    margin: '5px',
                    scale: 'large',
                    ui: 's-button',
                    cls: 's-blue',
                    hidden: viewOnly,
                    handler: function () {
                        var form = Ext.getCmp('mainForm').getForm();

                        if (form.isValid()) {
                            form.submit({
                                url: m_api + '/dataquality/dataqualities',
                                method: 'POST',
                                waitMsg: 'Saving data...',
                                success: function (fp, o) {
                                    var obj = o.result;
                                    switch (obj.success) {
                                        case true:
                                            Ext.MessageBox.alert('Success', obj.message);
                                            winMainForm.close();
                                            store.load();
                                            break;
                                        default:
                                            Ext.MessageBox.show({
                                                title: 'Failed',
                                                msg: obj.message,
                                                buttons: Ext.MessageBox.OK,
                                                animateTarget: 'mb9',
                                                icon: 'ext-mb-error'
                                            });
                                            break;
                                    }
                                },
                                failure: function (fp, o) {
                                    Ext.MessageBox.show({
                                        title: 'Failed',
                                        msg: 'Failed to save data',
                                        buttons: Ext.MessageBox.OK,
                                        animateTarget: 'mb9',
                                        icon: 'ext-mb-error'
                                    });
                                }
                            });
                        } else {
                            Ext.MessageBox.show({
                                title: 'Failed',
                                msg: 'Form not valid yet',
                                buttons: Ext.MessageBox.OK,
                                animateTarget: 'mb9',
                                icon: 'ext-mb-error'
                            });
                        }
                    }
                }, {
                    text: lang('Close'),
                    margin: '5px',
                    scale: 'large',
                    ui: 's-button',
                    cls: 's-grey',
                    disabled: false,
                    handler: function () {
                        winMainForm.close();
                    }
                }]
        });

        if (displayMethod == 'add') {
            //tambah
        } else if (displayMethod == 'update') {
            //update
            Ext.getCmp('mainForm').getForm().load({
                url: m_api + '/dataquality/form_data_quality',
                method: 'GET',
                params: {
                    dq_id: dq_id
                },
                success: function (form, action) {
                    var r = Ext.decode(action.response.responseText);
                },
                failure: function (form, action) {
                    Ext.MessageBox.show({
                        title: 'Failed',
                        msg: 'Failed to retrieve data',
                        buttons: Ext.MessageBox.OK,
                        animateTarget: 'mb9',
                        icon: 'ext-mb-error'
                    });
                }
            });
        }

        //show windows
        if (!winMainForm.isVisible()) {
            winMainForm.show();
        } else {
            winMainForm.close();
        }
    }


    function displayRunQuery(dq_id) {
        Ext.MessageBox.show({
            msg: 'Please wait...',
            progressText: 'Exporting...',
            width: 300,
            wait: true,
            waitConfig: {
                interval: 200
            },
            icon: 'ext-mb-download', //custom class in msg-box.html
            animateTarget: 'mb7'
        });

        Ext.Ajax.request({
            url: m_api + '/dataquality/prep_run_query',
            method: 'GET',
            params: {
                dq_id: dq_id
            },
            success: function (response, action) {
                Ext.MessageBox.hide();
//                if (!testJSON(response.responseText)) {
//                    Ext.MessageBox.hide();
//                    Ext.MessageBox.show({
//                        title: 'Failed',
//                        msg: 'Connection Failed',
//                        buttons: Ext.MessageBox.OK,
//                        animateTarget: 'mb9',
//                        icon: 'ext-mb-error'
//                    });
//                    return false;
//                }

                var obj = Ext.decode(response.responseText);
                switch (obj.success) {
                    case true:
                        Ext.define('dinamisPartGridModel.Model', {
                            extend: 'Ext.data.Model',
                            fields: obj.fieldNya
                        });

                        var store_sql_view = Ext.create('Ext.data.Store', {
                            model: 'dinamisPartGridModel.Model',
                            autoLoad: true,
                            pageSize: 50,
                            proxy: {
                                type: 'ajax',
                                url: m_api + '/dataquality/data_quality_list',
                                reader: {
                                    type: 'json',
                                    root: 'data',
                                    totalProperty: 'total'
                                },
                                extraParams: {
                                    dq_id: dq_id
                                }
                            }
                        });

                        var winSqlView = Ext.create('widget.window', {
                            title: obj.sqlViewName,
                            id: 'winSqlView',
                            closable: true,
                            modal: true,
                            closeAction: 'destroy',
                            width: '78%',
                            height: '95%',
                            overflowY: 'auto',
                            bodyStyle: {"background-color": "#F0F0F0"},
                            style: 'background-color:#F0F0F0;padding:4px;',
                            items: [{
                                    layout: 'column',
                                    border: false,
                                    padding: '0 20 0 0',
                                    items: [{
                                            columnWidth: 1,
                                            layout: 'form',
                                            items: [{
                                                    items: [{
                                                            layout: 'fit',
                                                            items: [{
                                                                    xtype: 'gridpanel',
                                                                    title: lang('Data List'),
                                                                    id: 'gridSqlView',
                                                                    style: 'border:1px solid #CCC;',
                                                                    store: store_sql_view,
                                                                    //width: '100%',
                                                                    autoScroll: true,
                                                                    loadMask: true,
                                                                    selType: 'rowmodel',
                                                                    height: '90%',
                                                                    columns: obj.gridColumnNya,
                                                                    dockedItems: [{
                                                                            xtype: 'pagingtoolbar',
                                                                            store: store_sql_view, // same store GridPanel is using
                                                                            dock: 'bottom',
                                                                            displayInfo: true
                                                                        }, {
                                                                            xtype: 'toolbar',
                                                                            items: [{
                                                                                    icon: varjs.config.base_url + 'images/icons/new/export.png', cls:'Sfr_BtnGridPaleBlue',
                                                                                    text: lang('Export to Excel'),
//                                                                                    hidden: m_sql_view_export_excel,
                                                                                    scope: this,
                                                                                    handler: function () {
                                                                                        Ext.MessageBox.show({
                                                                                            msg: 'Please wait...',
                                                                                            progressText: 'Exporting...',
                                                                                            width: 300,
                                                                                            wait: true,
                                                                                            waitConfig: {
                                                                                                interval: 200
                                                                                            },
                                                                                            icon: 'ext-mb-download', //custom class in msg-box.html
                                                                                            animateTarget: 'mb7'
                                                                                        });

                                                                                        Ext.Ajax.request({
                                                                                            url: m_api + '/dataquality/sql_view_export_excel',
                                                                                            method: 'POST',
                                                                                            waitMsg: lang('Please Wait'),
                                                                                            params: {
                                                                                                dq_id: dq_id
                                                                                            },
                                                                                            success: function (data) {
                                                                                                Ext.MessageBox.hide();
                                                                                                if (!testJSON(data.responseText)) {
                                                                                                    Ext.MessageBox.show({
                                                                                                        title: 'Failed',
                                                                                                        msg: 'Connection Failed',
                                                                                                        buttons: Ext.MessageBox.OK,
                                                                                                        animateTarget: 'mb9',
                                                                                                        icon: 'ext-mb-error'
                                                                                                    });
                                                                                                    return false;
                                                                                                }

                                                                                                var jsonResp = JSON.parse(data.responseText);
                                                                                                window.location = jsonResp.filenya;
                                                                                            },
                                                                                            failure: function () {
                                                                                                Ext.MessageBox.hide();
                                                                                                Ext.MessageBox.show({
                                                                                                    title: 'Notifications',
                                                                                                    msg: 'Failed to export, Please try again.',
                                                                                                    buttons: Ext.MessageBox.OK,
                                                                                                    animateTarget: 'mb9',
                                                                                                    icon: 'ext-mb-error'
                                                                                                });
                                                                                            }
                                                                                        });

                                                                                    }
                                                                                }]
                                                                        }]
                                                                }]
                                                        }]
                                                }]
                                        }]
                                }],
                            buttons: [{
                                    text: lang('Close'),
                                    margin: '5px',
                                    scale: 'large',
                                    ui: 's-button',
                                    cls: 's-grey',
                                    disabled: false,
                                    handler: function () {
                                        winSqlView.close();
                                    }
                                }]
                        });

                        //show windows
                        if (!winSqlView.isVisible()) {
                            winSqlView.show();
                        } else {
                            winSqlView.close();
                        }

                        break;
                    case false:
                        Ext.MessageBox.show({
                            title: 'Failed',
                            msg: 'Query syntax error',
                            buttons: Ext.MessageBox.OK,
                            animateTarget: 'mb9',
                            icon: 'ext-mb-error'
                        });
                        break;
                }
            },
            failure: function (response, action) {
                Ext.MessageBox.hide();
                Ext.MessageBox.show({
                    title: 'Failed',
                    msg: 'Failed to connect',
                    buttons: Ext.MessageBox.OK,
                    animateTarget: 'mb9',
                    icon: 'ext-mb-error'
                });
            }
        });
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
});

var height = window.innerHeight - 100;
var parent_id = null;
var parent_type = null;
var rule = '';
Ext.onReady(function() {
    var treeStore = Ext.create('Ext.data.TreeStore', {
        autoLoad: true,
        proxy: {
            type: 'ajax',
            url: m_tree
        },
        extraParams: {
            
        },
        listeners: {
            beforeLoad: function() {
                
            }
        }
    });
    Ext.define('Fin.model.Coa', {
        extend: 'Ext.data.Model',
        fields: ['id', 'code', 'title', 'name', 'coaType','CoaStatus','CoaForReceived','CoaForSpent','CoaOrder','CoaReportDisplay','coaBalanceAmount','CoaForCash','CoaForNonCash'],
    });
    var gridStore = Ext.create('Ext.data.Store', {
        storeId: 'coaStore',
        model: 'Fin.model.Coa',
        autoLoad: true,
        proxy: {
            type: 'ajax',
            url: m_crud + 's',
            reader: {
                type: 'json',
                root: 'data',
                totalProperty: 'total'
            }
        },
        extraParams: {
            closedDate: ''
        },
        listeners: {
            load: function(sender, node, records) {
               // var selected = [];
               //      Ext.each(gridStore.data.items, function(item) {
               //          selected.push(item.data);
               //  });
               //  console.log(selected);
               //  console.log('cihuy');
            }
        }
    });

    //////////////////COA PARENT
var storeCoaList = Ext.create('Ext.data.Store', {
    extend: 'Ext.data.Model',
    fields: ['id', 'code', 'title'],
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

    Ext.define('GridCoaParentList', {
        itemId: 'GridCoaParentList',
        id: 'GridCoaParentList',
        extend: 'Ext.grid.Panel',
        alias: 'widget.GridCoaParentList',
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
                        Ext.getCmp('parent').setValue(selectedRecord.data.id);
                        Ext.getCmp('parentCoaName').setValue(selectedRecord.data.title);
                        Ext.getCmp('wCoaParentPopup').hide();
                }
            },
            { text: 'id', dataIndex: 'id', hidden: true },
            { text: 'Account Code', flex:1, width: '25%', dataIndex: 'code' },
            { text: 'Account Name', width: '75%', dataIndex: 'title' }        
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

    var wCoaParentPopup = Ext.create('widget.window', {
        id: 'wCoaParentPopup',
        title: 'Choose Parent Chart of Account',
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
                xtype:'GridCoaParentList'
        }]
    });
    //////////////////COA PARENT

    function showWindowForm(data) {
        var rule = '';
        var title = '';
        var type = '';
        var parent_type = Ext.getCmp('parent_type').getValue();
        var parent_id = Ext.getCmp('parent_id').getValue();
        switch (parent_type) {
            case 'group':
                title = lang('Chart of Accounts Group');
                type = 'group';
                Ext.getCmp('code').show();
                Ext.getCmp('coaType').hide();
                Ext.getCmp('parent').hide();
                Ext.getCmp('coaBalanceAmount').hide();
                Ext.getCmp('journalClosedDate').hide();
                break;
            case 'coa':
                title = lang('Chart of Accounts');
                type = 'coa';
                Ext.getCmp('code').show();
                Ext.getCmp('coaType').show();
                Ext.getCmp('parent').hide();
                Ext.getCmp('coaBalanceAmount').show();
                // Ext.getCmp('journalClosedDate').show();
                break;
            case 'coa_parent':
                title = lang('Chart of Accounts');
                type = 'coa_parent';
                Ext.getCmp('parent').show();
                Ext.getCmp('code').show();
                Ext.getCmp('coaType').show();
                Ext.getCmp('coaBalanceAmount').show();
                // Ext.getCmp('journalClosedDate').show();
                break;
            default:
                title = lang('Class');
                type = 'class';
                Ext.getCmp('code').hide();
                Ext.getCmp('coaType').hide();
                Ext.getCmp('parent').hide();
                Ext.getCmp('coaBalanceAmount').hide();
                Ext.getCmp('journalClosedDate').hide();
                break;
        }

        var form = itemForm.getForm();
        form.findField('source_id').setValue(parent_id);
        form.findField('type').setValue(type);
        if (data) {
            var act = lang('Edit');
            form.findField('code').setValue(data.code);
            form.findField('name').setValue(data.title);
            form.findField('mode').setValue('edit');
            form.findField('old_id').setValue(data.id);
            form.findField('coaBalanceAmount').setValue(data.coaBalanceAmount);
            console.log(Ext.getCmp('closingDate').getValue());
            form.findField('journalClosedDate').setValue(Ext.getCmp('closingDate').getRawValue());
            Ext.getCmp('parent').setValue(data.code + ' - ' + data.title);
            Ext.getCmp('parentCode').setValue(data.code);
            if (data.coaType) {
                form.findField('coaType').setValue(data.coaType);
            }
        } else {
            act = lang('Add');
            form.findField('name').setValue('');
            form.findField('code').setValue('');
            form.findField('coaType').setValue('');
            form.findField('mode').setValue('add');
            form.findField('coaBalanceAmount').setValue('');
            form.findField('journalClosedDate').setValue(Ext.getCmp('closingDate').getRawValue());
            if (type == 'coa_parent') {
                Ext.Ajax.request({
                    url: m_crud,
                    method: 'GET',
                    params: {id: Ext.getCmp('parent_id').getValue()},
                    success: function(fp, o) {
                        var r = Ext.decode(fp.responseText);
                        Ext.getCmp('parent').setValue(r.code);
                        Ext.getCmp('parentCode').setValue(r.coaCode);
                    }
                });
            }
        }
        windowForm.setTitle(lang(act) + ' ' + title);
        if (!windowForm.isVisible()) {
            windowForm.show();
        } else {
            windowForm.hide(this, function() {
            });
            windowForm.toFront();
        }
        Ext.getCmp('parentCoaName').setValue(Ext.getCmp('tmpNameParent').getValue());
    }


    var mc_coatype = Ext.create('Ext.data.Store', {
        extend: 'Ext.data.Model',
        fields: ['id', 'label'],
        data: [
            {"id": "1", "label": "Debit"},
            {"id": "2", "label": "Kredit"}
        ]
    });

    var mc_closingdate = Ext.create('Ext.data.Store', {
        extend: 'Ext.data.Model',
        fields: ['id', 'label'],
        autoLoad: true,
        proxy: {
            type: 'ajax',
            url: m_closingdate,
            reader: {
                type: 'json',
                root: 'data'
            }
        }
    });



    var itemForm = Ext.create('Ext.form.Panel', {
        frame: false,
        autoScroll: true,
        bodyPadding: 5,
        id: 'dataForm',
        fieldDefaults: {
            labelAlign: 'left',
            labelWidth: 150,
            anchor: '100%'
        },
        items: [{
                xtype: 'hiddenfield',
                id: 'type',
                name: 'type'
            }, {
                xtype: 'hiddenfield',
                id: 'mode',
                name: 'mode'
            }, {
                xtype: 'hiddenfield',
                id: 'old_id',
                name: 'old_id'
            }, {
                xtype: 'hiddenfield',
                id: 'source_id',
                name: 'source_id'
            }, {
                xtype: 'textfield',
                id: 'parentCode',
                name: 'parentCode',
                hidden: true
            }, {
                xtype: 'textfield',
                id: 'parent',
                name: 'parent',
                readOnly: true,
                hidden: true,
                fieldLabel: lang('Parent')
            }, {
                xtype: 'textfield',
                fieldLabel: 'Parent Account',
                name: 'parentCoaName',
                id: 'parentCoaName',
                readOnly:true,
                listeners: {
                    render: function(component) {
                        component.getEl().on('click', function(event, el) {
                           // wCoaParentPopup.show();
                           //  storeCoaList.load({
                           //       params: {
                           //              type: 'class',
                           //              id:1
                           //          }
                           //  });
                        });
                    }
                }
            },
             {
                xtype: 'textfield',
                id: 'code',
                name: 'code',
                fieldLabel: lang('Code')
            }, {
                xtype: 'textfield',
                id: 'name',
                name: 'name',
                fieldLabel: lang('Name'),
                allowBlank: false
            }, {
                id: 'coaType',
                name: 'coaType',
                xtype: 'combo',
                emptyText: '-- Select --',
                fieldLabel: 'Chart of Accounts Type',
                multiSelect: false,
                store: mc_coatype,
                displayField: 'label',
                valueField: 'id',
                queryMode: 'local'
            }, {
                id: 'journalClosedDate',
                hidden:true,
                name: 'journalClosedDate',
                xtype: 'combo',
                emptyText: '-- Select --',
                fieldLabel: lang('Journal Closed Date'),
                multiSelect: false,
                store: mc_closingdate,
                displayField: 'label',
                valueField: 'id',
                queryMode: 'local'
            }, {
                id: 'coaBalanceAmount',
                fieldStyle:'text-align:right;',
                // readOnly:true,
                hideTrigger:true,
                name: 'coaBalanceAmount',
                xtype: 'numericfield',
                fieldLabel: lang('Balance')
            }

        ],
        buttons: [
            {
                id: 'saveButton',
                text: lang('Save'),
                margin: '5px',
                scale: 'large',
                ui: 's-button',
                cls: 's-blue',
                handler: function() {
                    var form = Ext.getCmp('dataForm').getForm();
                    form.submit({
                        url: m_crud,
                        waitMsg: lang('Saving....'),
                        success: function(form, action) {
                            treeStore.load();
                            treePanel = Ext.getCmp('treeCoa');
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
                        failure: function(form, action) {
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
                handler: function() {
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
        height: 300,
        layout: {
            type: 'fit',
            padding: 5
        },
        items: [itemForm]
    });

    var resultsPanel = Ext.create('Ext.panel.Panel', {
        height: height,
        renderTo: 'ext-content',
        layout: {
            type: 'hbox', // Arrange child items horizontally
            align: 'stretch', // Each takes up full width
            padding: 5
        },
        items: [{
                id: 'treeCoa',
                xtype: 'treepanel',
                title: lang('Class'),
                style: 'border:1px solid #CCC;',
                width: 200,
                height: '100%',
                store: treeStore,
                rootVisible: false,
                flex: 1,
                listeners: {
                    itemClick: function(view, record, item, index, e, eOpts) {
                        var parent_id = record.data.id;
                        var parent_code = parent_id.split('|')[2];
                        var parent_id = parent_id.split('|')[1];
                        var parent_type = record.data.cls;
                        if (record.data.cls != '') {
                            var title = '';
                            Ext.getCmp('parent_id').setValue(parent_id);
                            switch (record.data.cls) {
                                case 'root':
                                    title = lang('Chart of Accounts Class');
                                    Ext.getCmp('parent_type').setValue('class');
                                    visibleColCoa(false);
                                    break;
                                case 'group':
                                    title = lang('Chart of Accounts');
                                    Ext.getCmp('parent_type').setValue('coa');
                                    visibleColCoa(true);
                                    break;
                                case 'coa':
                                    title = lang('Chart of Accounts');
                                    Ext.getCmp('parent_type').setValue('coa_parent');
                                    break;
                                default:
                                    title = lang('Chart of Accounts Group');
                                    Ext.getCmp('parent_type').setValue('group');
                                    visibleColCoa(false);
                                    break;
                            }
                            Ext.getCmp('gridDetails').setTitle(title);
                            gridStore.load({
                                params: {
                                    type: record.data.cls,
                                    id: parent_id,
                                    code: parent_code
                                }
                            });
                            Ext.getCmp('tmpIdParent').setValue(parent_id);
                            Ext.getCmp('tmpNameParent').setValue(record.data.text);
                        }
                        ;
                    },
                }
            }, {
                xtype: 'splitter'
            }, {
                id: 'gridDetails',
                xtype: 'gridpanel',
                plugins: [new Ext.grid.plugin.CellEditing({
                            clicksToEdit: 1
                        })],
                title: lang('Chart of Accounts Class'),
                style: 'border:1px solid #CCC;',
                store: gridStore,
                loadMask: true,
                selType: 'rowmodel',
                columns: [
                        {header: 'id', dataIndex: 'id', width: '5%',hidden:true},
                        {text: lang('No'), xtype: 'rownumberer', width: '5%'},
                        {text: lang('Name'), dataIndex: 'name', flex: 1,minWidth:120},
                        {xtype: 'numbercolumn', align:'right', hidden:true, header: 'Balance',dataIndex: 'coaBalanceAmount',width: 60},
                        {xtype: 'checkcolumn', header: 'Active',dataIndex: 'CoaStatus',width: 60,hidden:true,menuDisabled:true},
                        {xtype: 'checkcolumn', header: 'Receive',dataIndex: 'CoaForReceived',width: 60,hidden:true,menuDisabled:true},
                        {xtype: 'checkcolumn', header: 'Spent',dataIndex: 'CoaForSpent',width: 60,hidden:true,menuDisabled:true},
                        {xtype: 'checkcolumn', header: 'Cash',dataIndex: 'CoaForCash',width: 60,hidden:true,menuDisabled:true},
                        {xtype: 'checkcolumn', header: 'Non-cash',dataIndex: 'CoaForNonCash',width: 70,hidden:true,menuDisabled:true},
                    {
                        header: 'Sequence',
                        width:70,
                        hidden:true,
                        dataIndex: 'CoaOrder',
                        align: 'right',
                        editor: {
                            xtype: 'numberfield',
                            allowBlank: false,
                            minValue: 0,
                            maxValue: 100000
                        }
                    },
                    {
                        header: 'Display',
                        dataIndex: 'CoaReportDisplay',
                        width: 130,
                        hidden:true,
                        editor: new Ext.form.field.ComboBox({
                            typeAhead: true,
                            displayField: 'CoaReportDisplay',
                            valueField: 'CoaReportDisplay',
                            name: 'CoaReportDisplay',
                            triggerAction: 'all',
                            fields: ['CoaReportDisplay','CoaReportDisplayName'],
                            store: new Ext.data.ArrayStore({
                                fields: ['CoaReportDisplay'],
                                data: [
                                    ['Balance Sheet','Balance Sheet'],
                                    ['Profit and Loss','Profit and Loss']
                                ]
                            })
                        })
                    },
                    {
                        menuDisabled: true,
                        sortable: false,
                        xtype: 'actioncolumn',
                        width: 80,
                        align: 'center',
                        items: [
                            {
                                icon: m_baseurl + '/images/icons/silk/page_white_edit.png',
                                tooltip: lang('Edit'),
                                handler: function(grid, rowIndex, colIndex) {
                                    var rec = grid.getStore().getAt(rowIndex);
                                    showWindowForm(rec.data);
                                    Ext.getCmp('coaBalanceAmount').setReadOnly(true);
                                }
                            }, {
                                icon: m_baseurl + '/images/delete.png',
                                tooltip: lang('Delete'),
                                handler: function(grid, rowIndex, colIndex) {
                                    var rec = grid.getStore().getAt(rowIndex);
                                    var parent_type = Ext.getCmp('parent_type').getValue();
                                    switch (parent_type) {
                                        case 'group':
                                            type = 'group';
                                            break;
                                        case 'coa':
                                            type = 'coa';
                                            break;
                                        case 'coa_parent':
                                            type = 'coa_parent';
                                            break;
                                        default:
                                            type = 'class';
                                            break;
                                    }
                                    Ext.MessageBox.confirm('Message', rec.data.name + '<br/>' + lang('Apakah anda mau menghapus data ini?'), function(btn) {
                                        if (btn == 'yes') {
                                            Ext.Ajax.request({
                                                waitMsg: lang('Please Wait'),
                                                url: m_crud,
                                                method: 'DELETE',
                                                params: {
                                                    id: rec.data.id,
                                                    type: type
                                                },
                                                success: function(response, opts) {
                                                    var obj = Ext.decode(response.responseText);
                                                    if (obj.success) {
                                                        // gridStore.load({
                                                        //     params: {
                                                        //         type: parent_type,
                                                        //         id: parent_id
                                                        //     }
                                                        // });
                                                        gridStore.load({
                                                            params: {
                                                                type: 'root'
                                                            }
                                                        });
                                                        visibleColCoa(false);
                                                        
                                                        treeStore.load();
                                                        treePanel = Ext.getCmp('treeCoa');
                                                        var record = treePanel.getStore().getNodeById(parent_id);
                                                        treePanel.getSelectionModel().select(record);

                                                    } else {
                                                        Ext.MessageBox.alert('Warning', obj.message);
                                                    }
                                                },
                                                failure: function(response, opts) {
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
                                handler: function() {
                                    showWindowForm();
                                    Ext.getCmp('parentCoaName').setValue(Ext.getCmp('tmpNameParent').getValue());
                                    Ext.getCmp('parent').setValue(Ext.getCmp('tmpIdParent').getValue());

                                    Ext.getCmp('coaBalanceAmount').setReadOnly(false);
                                }
                            }, {
                                id: 'closingDate',
                                name: 'closingDate',
                                hidden:true,
                                xtype: 'combo',
                                emptyText: '-- Select --',
                                fieldLabel: 'Closing Date',
                                multiSelect: false,
                                store: mc_closingdate,
                                displayField: 'label',
                                valueField: 'id',
                                queryMode: 'local'
                            }, {
                                xtype: 'hiddenfield',
                                name: 'parent_type',
                                id: 'parent_type',
                                value: 'class',
                                hidden: false
                            }, {
                                xtype: 'hiddenfield',
                                name: 'parent_id',
                                id: 'parent_id',
                                hidden: true
                            },
                            {
                                xtype:'hiddenfield',
                                name:'tmpIdParent',
                                id:'tmpIdParent'
                            },{
                                xtype:'hiddenfield',
                                name:'tmpNameParent',
                                id:'tmpNameParent'
                            },'->',
                            {
                                xtype: 'button',
                                icon: varjs.config.base_url + 'images/icons/silk/disk.png',
                                text: 'Save',
                                handler: function() {
                                    var grid = Ext.getCmp('gridDetails');
                                        selected = [];
                                        Ext.each(grid.getStore().data.items, function(item) {
                                            selected.push(item.data);
                                    });
                                        // console.log(selected);
                                    Ext.Ajax.request({
                                        url: m_crudcellgrid,
                                        method: 'PUT',
                                        params: {
                                           data: Ext.encode(selected)
                                        },
                                        success: function(form, action) {
                                             var d = Ext.decode(form.responseText);
                                             Ext.Msg.alert("Info",d.message);
                                           gridStore.load();
                                        },
                                        failure: function(form, action) {
                                           var d = Ext.decode(form.responseText);
                                             Ext.Msg.alert("Info",d.message);
                                           gridStore.load();
                                        }
                                    });
                                }
                            }

                        ]
                    }
                ],
            }]
    });
})

function visibleColCoa(opt)
{ 
    var gridDetails = Ext.getCmp('gridDetails');
    gridDetails.columns[4].setVisible(opt);
    gridDetails.columns[5].setVisible(opt);
    gridDetails.columns[6].setVisible(opt);
    gridDetails.columns[7].setVisible(opt);
    gridDetails.columns[8].setVisible(opt);
    gridDetails.columns[9].setVisible(opt);
    gridDetails.columns[10].setVisible(opt);

    gridDetails.getStore().each(function(rec){  
         // rec.set('CoaStatus', false); 
         rec.data.check = false; 
    });
}
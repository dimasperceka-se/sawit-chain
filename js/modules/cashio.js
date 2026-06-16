Ext.onReady(function() {
    
    Ext.tip.QuickTipManager.init();
    
    Ext.define('Core.Module.Accounting.model.Cashio', {
        extend: 'Ext.data.Model',
        fields: [
            {name: 'DEBET', type:'decimal'},
            {name: 'KREDIT', type:'decimal'},
            {name: 'journalID', type:'int'},
            {name: 'journalTypeCode', type:'string'},
            {name: 'journalMemo', type:'string'},
            {name: 'journalDate', type:'string'},
            {name: 'journalIsPosted', type:'int'},
            {name: 'journalPostedDate', type:'string'}
        ],
        idProperty:'journalID'
    });
    
    Ext.define('Core.Module.Accounting.model.CashDetail', {
        extend: 'Ext.data.Model',
        fields: [
            {name: 'coaCode', type:'string'},
            {name: 'coaTitle', type:'string'},
            {name: 'SIDE', type:'string'},
            {name: 'DEBET', type:'decimal'},
            {name: 'KREDIT', type:'decimal'},
            {name: 'journalID', type:'int'},
            {name: 'journalDetailType', type:'string'},
            {name: 'journalDetailDesc', type:'string'},
            {name: 'currencyID', type:'int'},
            {name: 'currencyName', type:'string'},
            {name: 'journalDetailOrig', type:'decimal'},
            {name: 'journalDetailSum', type:'decimal'},
            {name: 'journalDetailExRate', type:'decimal'}
        ],
        idProperty:'journalDetailID'
    });

    var jstore = Ext.create('Ext.data.Store', {
        model: 'Core.Module.Accounting.model.Cashio',
        autoSync: false,
        autoLoad: true,
        remoteSort: true,
        proxy: {
            type: 'rest',
            url: m_crud + '/getdata', // url that will load data with respect to start and limit params
            reader: {
                type: 'json',
                root: 'data',
                totalProperty: 'total',
                idProperty: 'cashTransID'
            },
            writer: {
                type: 'json'
            },
            api: {
                destroy: m_crud + 'api/cashio/delete'
            },
            appendId: true
        }
    });
    
    var rowedit = Ext.create('Ext.grid.plugin.CellEditing', {
        listeners: {
            edit:function(editor,e){

            }
        }
    });
    
    var me = Ext.create('Ext.grid.Panel', {
        style:'border: 1px solid #CCCCCC',
        renderTo: 'ext-content',
        autoScroll: true,
        id: 'grid-cash-trans',
        store: jstore,
        height:800,
        columns: [{
            header: 'Transaction No.',
            dataIndex: 'journalTypeCode',
            width:85,
            renderer:function(v){
                return '<span style="font-family:courier new">' + v + '</span>';
            }
        }, {
            header: 'Type',
            dataIndex: 'cashTransType',
            flex: 1,
            renderer:function(v){
                return '<span style="font-family:courier new">' + v + '</span>';
            }
        }, {
            header: 'Date',
            dataIndex: 'journalDate',
            width:100,
            renderer:function(v){
                return '<span style="font-family:courier new">' + Ext.util.Format.date(v,'d/m/Y') + '</span>';
            }
        }, {
            header: 'Amount',
            align:'right',
            dataIndex: 'cashTransAmount',
            width:200,
            renderer:function(v){
                return '<span style="font-family:courier new">' + Ext.util.Format.number(v,'0,000.00') + '</span>';
            }
        }, {
            header: 'Remark',
            dataIndex: 'cashTransRemark',
            flex: 1,
            renderer:function(v){
                return '<span style="font-family:courier new">' + v + '</span>';
            }
        }],
        selModel: new Ext.selection.CheckboxModel({
            listeners: {
                selectionchange: function(c, selected, o) {
                    if (selected.length > 0) {
                        if (selected.length > 1) {
                            Ext.getCmp('btn-edit-journal').disable();
                            Ext.getCmp('btn-del-journal').enable();
                        } else {
                            Ext.getCmp('btn-del-journal').enable();
                            Ext.getCmp('btn-edit-journal').enable();
                        }
                    } else {
                        Ext.getCmp('btn-edit-journal').disable();
                        Ext.getCmp('btn-del-journal').disable();
                    }
                }
            }
        }),
        dockedItems: [{
            xtype: 'pagingtoolbar',
            dock: 'bottom',
            store: jstore,
            displayInfo: true,
            displayMsg: 'Displaying topics {0} - {1} of {2}',
            emptyMsg: "No topics to display"
        },{
            xtype: 'toolbar',
            dock: 'top',
            items: [{
            xtype: 'button',
            text:'Cash-In',
            iconCls: 'add',
            id: 'btn-add-cash-in',
            handler: function() {
                
                var win = Ext.create('Ext.Window',{
                    title:'Cash-In',
                    items:[
                        Ext.create('Ext.form.Panel' ,{
                            id:'frm-Journal',
                            region:'center',
                            layout:{
                                type:'vbox',
                                align:'stretch'
                            },
                            bodyStyle:'background:#FAFAFA',
                            items: [
                                {
                                    xtype:'hidden',
                                    name:'journalID'
                                },
                                {
                                    xtype:'container',
                                    layout:{
                                        type:'table',
                                        columns:3
                                    },
                                    defaults:{
                                        labelAlign:'top',
                                        margin:'0 10 0 10',
                                        labelSeparator:'',
                                        labelStyle:'font-weight:bold'
                                    },
                                    items:[
                                        {
                                            xtype:'textfield',
                                            readOnly:true,
                                            width:200,
                                            fieldLabel:'Transaction No.',
                                            name:'cashTransNo'
                                        },
                                        {
                                            xtype:'datefield',
                                            name:'cashTransDate',
                                            fieldLabel: 'Date <b style="color:red">*</b>',
                                            value:new Date()
                                        },
                                        {
                                            xtype:'textarea',
                                            width:250,
                                            fieldLabel:'Remark',
                                            rowspan:2,
                                            name:'cashTransRemark'
                                        },
                                        {
                                            xtype:'textfield',
                                            fieldLabel:'From',
                                            width:200,
                                            name:'cashTransFrom'
                                        },
                                        {
                                            xtype: 'combo',
                                            width:200,
                                            fieldLabel:'Cash-In to',
                                            store: Ext.create('Ext.data.Store', {
                                                fields: ['coaCode', 'coaTitle'],
                                                autoLoad: true,
                                                proxy: {
                                                    type: 'rest',
                                                    url: m_api + '/common/getcombo', // url that will load data with respect to start and limit params
                                                    extraParams: {
                                                        table: 'accounting_coa',
                                                        name: 'coaCode',
                                                        id: 'coaTitle'
                                                    },
                                                    reader: {
                                                        type: 'json',
                                                        root: 'data',
                                                        totalProperty: 'total'
                                                    }
                                                }
                                            }),
                                            listConfig: {
                                                loadingText: 'Searching...',
                                                minWidth:450,
                                                getInnerTpl: function() {
                                                    return '{coaCode} - {coaTitle}';
                                                }
                                            },
                                            displayField: 'coaTitle',
                                            valueField: 'coaCode',
                                            name: 'coaTitle'
                                        }
                                    ]
                                },
                                {
                                    xtype:'grid',
                                    id:'grid-cash-detail',
                                    flex:1,
                                    tbar:[
                                            {
                                            xtype:'button',
                                            iconCls:'add',
                                            text:'Add Row',
                                            handler:function(c){
                                                var grid = c.up('.grid');
                                                var last = grid.store.getCount();
                                                // empty record
                                                grid.store.insert(last, Ext.create('Core.Module.Accounting.model.CashDetail',{
                                                    coaCode:'',
                                                    cashDetailAmount:''
                                                }));

                                                rowedit.startEdit(last, 0);
                                            }
                                        },
                                        {
                                            xtype:'button',
                                            iconCls:'add',
                                            text:'Remove Row',
                                            handler:function(c){
                                                var grid = Ext.getCmp('grid-cash-detail');
                                                var sm = grid.getSelectionModel();
                                                var sel = sm.getSelection();
                                                grid.store.remove(sel);
                                            }
                                        }
                                    ],
                                    bbar:[
                                        '->',
                                        '<b>Total</b>',
                                        { xtype: 'textfield', width: 200, readOnly:true, fieldStyle:'font-weight:bold;text-align:right' }
                                    ],
                                    store:Ext.create('Ext.data.Store', {
                                        model: 'Core.Module.Accounting.model.CashDetail',
                                        autoLoad: true,
                                        remoteSort: true,
                                        proxy: {
                                            type: 'rest',
                                            url: m_api + '/jurnal/getdetail', // url that will load data with respect to start and limit params
                                            reader: {
                                                type: 'json',
                                                root: 'data',
                                                totalProperty: 'total',
                                                idProperty: 'cashDetailID'
                                            },
                                            writer: {
                                                type: 'json'
                                            },
                                            api: {
                                                destroy: window.location + 'api/cashio/delete'
                                            },
                                            appendId: true
                                        }
                                    }),
                                    plugins:[rowedit],
                                    selType: 'cellmodel',
                                    columns:[{
                                        header: 'COA Code',
                                        dataIndex: 'coaCode',
                                        width:200,
                                        renderer:function(v){
                                            return '<span style="font-family:courier new">' + v + '</span>';
                                        },
                                        field: {
                                            xtype: 'combo',
                                            disabledCls: 'disabled',
                                            id:'cmb-coa-journal-detail',
                                            listeners:{
                                                select:function(c,r){
                                                    var grid = Ext.getCmp('grid-cash-detail');
                                                    var sm = grid.getSelectionModel();
                                                    var sel = sm.getSelection();

                                                    var value = r[0].data.coaTitle;
                                                    sel[0].set('coaTitle',value);
                                                }
                                            },
                                            store: Ext.create('Ext.data.Store', {
                                                fields: ['coaCode', 'coaTitle'],
                                                autoLoad: true,
                                                proxy: {
                                                    type: 'rest',
                                                    url: m_api + '/common/getcombo', // url that will load data with respect to start and limit params
                                                    extraParams: {
                                                        table: 'accounting_coa',
                                                        name: 'coaTitle',
                                                        id: 'coaCode'
                                                    },
                                                    reader: {
                                                        type: 'json',
                                                        root: 'data',
                                                        totalProperty: 'total'
                                                    }
                                                }
                                            }),
                                            listConfig: {
                                                loadingText: 'Searching...',
                                                minWidth:350,
                                                getInnerTpl: function() {
                                                    return '{coaCode} - {coaTitle}';
                                                }
                                            },
                                            minChars:1,
                                            displayField: 'coaCode',
                                            valueField: 'coaCode',
                                            name: 'coaCode'

                                        }
                                    },{
                                        header: 'Coa Title',
                                        dataIndex: 'coaTitle',
                                        flex:true,
                                        renderer:function(v){
                                            return '<span style="font-family:courier new">' + v + '</span>';
                                        }
                                    },{
                                        header: 'Amount',
                                        dataIndex: 'cashDetailAmount',
                                        width:200,
                                        align:'right',
                                        renderer:function(v){
                                            return '<span style="font-family:courier new">' + Ext.util.Format.number(v,'0,000.00') + '</span>';
                                        },
                                        editor: {
                                            xtype: 'numberfield'
                                        }
                                    }]
                                }
                            ]
                        })
                    ],
                    width:720,
                    height:500,
                    modal:true,
                    layout:{
                        type:'border'
                    },
                    buttons: [
                        {
                            xtype:'button',
                            text:'Save',
                            id:'btn-save-frm-Journal',
                            margin: '5px',
                            scale: 'large',
                            ui: 's-button',
                            cls: 's-blue',
                            handler:function(c){
                                var form = Ext.getCmp('frm-Journal');
                                var frm = form.getForm();
                                var id = frm.getValues();
                                if(id.JOURNAL_ID === ''){
                                    var url = window.location + 'api/journal/add';
                                    var method = 'POST';

                                } else {
                                    var url = window.location + 'api/journal/edit/'+id.jouralID;
                                    var method = 'PUT';

                                }
                                var data_grid = Ext.getCmp('grid-cash-detail').store;
                                var detail = [];

                                data_grid.each(function(value, index, rec){
                                    detail.push(value.data);
                                });

                                frm.submit({
                                    url: url,
                                    method: method,
                                    params:{data:Ext.JSON.encode(detail)},
                                    success: function(f, resp) {
                                        Ext.Msg.alert('Success', 'Journal successfully saved');
                                        win.close();
                                        jtore.loadPage(1);
                                    },
                                    failure: function(f, resp) {
                                        Ext.Msg.alert('Failed', 'Cannot save journal');
                                    }
                                });
                            }
                        },
                        {
                            xtype:'button',
                            text:'Cancel',
                            margin: '5px',
                            scale: 'large',
                            ui: 's-button',
                            cls: 's-grey',
                            id:'btn-cancel-frm-Journal',
                            handler:function(){
                                var form = Ext.getCmp('frm-Journal');
                                form.getForm().reset();
                                win.close();
                            }
                        }
                    ]
                }).show();

            }
        }, {
            xtype: 'button',
            text:'Cash-Out',
            iconCls: 'add',
            id: 'btn-add-cash-out',
            handler: function() {
                
                var win = Ext.create('Ext.Window',{
                    title:'Cash-Out',
                    items:[
                        Ext.create('Ext.form.Panel' ,{
                            id:'frm-Journal',
                            region:'center',
                            layout:{
                                type:'vbox',
                                align:'stretch'
                            },
                            bodyStyle:'background:#FAFAFA',
                            items: [
                                {
                                    xtype:'hidden',
                                    name:'journalID'
                                },
                                {
                                    xtype:'container',
                                    layout:{
                                        type:'table',
                                        columns:3
                                    },
                                    defaults:{
                                        labelAlign:'top',
                                        margin:'0 10 0 10',
                                        labelSeparator:'',
                                        labelStyle:'font-weight:bold'
                                    },
                                    items:[
                                        {
                                            xtype:'textfield',
                                            readOnly:true,
                                            width:200,
                                            fieldLabel:'Transaction No.',
                                            name:'cashTransNo'
                                        },
                                        {
                                            xtype:'datefield',
                                            name:'cashTransDate',
                                            fieldLabel: 'Date <b style="color:red">*</b>',
                                            value:new Date()
                                        },
                                        {
                                            xtype:'textarea',
                                            width:250,
                                            fieldLabel:'Remark',
                                            rowspan:2,
                                            name:'cashTransRemark'
                                        },
                                        {
                                            xtype:'textfield',
                                            fieldLabel:'Pay to',
                                            width:200,
                                            name:'cashTransFrom'
                                        },
                                        {
                                            xtype: 'combo',
                                            width:200,
                                            fieldLabel:'Cash-Out from',
                                            store: Ext.create('Ext.data.Store', {
                                                fields: ['coaCode', 'coaTitle'],
                                                autoLoad: true,
                                                proxy: {
                                                    type: 'rest',
                                                    url: m_api + '/common/getcombo', // url that will load data with respect to start and limit params
                                                    extraParams: {
                                                        table: 'accounting_coa',
                                                        name: 'coaCode',
                                                        id: 'coaTitle'
                                                    },
                                                    reader: {
                                                        type: 'json',
                                                        root: 'data',
                                                        totalProperty: 'total'
                                                    }
                                                }
                                            }),
                                            listConfig: {
                                                loadingText: 'Searching...',
                                                minWidth:450,
                                                getInnerTpl: function() {
                                                    return '{coaCode} - {coaTitle}';
                                                }
                                            },
                                            displayField: 'coaTitle',
                                            valueField: 'coaCode',
                                            name: 'coaTitle'
                                        }
                                    ]
                                },
                                {
                                    xtype:'grid',
                                    id:'grid-cash-detail',
                                    flex:1,
                                    tbar:[
                                            {
                                            xtype:'button',
                                            iconCls:'add',
                                            text:'Add Row',
                                            handler:function(c){
                                                var grid = c.up('.grid');
                                                var last = grid.store.getCount();
                                                // empty record
                                                grid.store.insert(last, Ext.create('Core.Module.Accounting.model.CashDetail',{
                                                    coaCode:'',
                                                    cashDetailAmount:''
                                                }));

                                                rowedit.startEdit(last, 0);
                                            }
                                        },
                                        {
                                            xtype:'button',
                                            iconCls:'add',
                                            text:'Remove Row',
                                            handler:function(c){
                                                var grid = Ext.getCmp('grid-cash-detail');
                                                var sm = grid.getSelectionModel();
                                                var sel = sm.getSelection();
                                                grid.store.remove(sel);
                                            }
                                        }
                                    ],
                                    bbar:[
                                        '->',
                                        '<b>Total</b>',
                                        { xtype: 'textfield', width: 200, readOnly:true, fieldStyle:'font-weight:bold;text-align:right' }
                                    ],
                                    store:Ext.create('Ext.data.Store', {
                                        model: 'Core.Module.Accounting.model.CashDetail',
                                        autoLoad: true,
                                        remoteSort: true,
                                        proxy: {
                                            type: 'rest',
                                            url: m_api + '/jurnal/getdetail', // url that will load data with respect to start and limit params
                                            reader: {
                                                type: 'json',
                                                root: 'data',
                                                totalProperty: 'total',
                                                idProperty: 'cashDetailID'
                                            },
                                            writer: {
                                                type: 'json'
                                            },
                                            api: {
                                                destroy: window.location + 'api/cashio/delete'
                                            },
                                            appendId: true
                                        }
                                    }),
                                    plugins:[rowedit],
                                    selType: 'cellmodel',
                                    columns:[{
                                        header: 'COA Code',
                                        dataIndex: 'coaCode',
                                        width:200,
                                        renderer:function(v){
                                            return '<span style="font-family:courier new">' + v + '</span>';
                                        },
                                        field: {
                                            xtype: 'combo',
                                            disabledCls: 'disabled',
                                            id:'cmb-coa-journal-detail',
                                            listeners:{
                                                select:function(c,r){
                                                    var grid = Ext.getCmp('grid-cash-detail');
                                                    var sm = grid.getSelectionModel();
                                                    var sel = sm.getSelection();

                                                    var value = r[0].data.coaTitle;
                                                    sel[0].set('coaTitle',value);
                                                }
                                            },
                                            store: Ext.create('Ext.data.Store', {
                                                fields: ['coaCode', 'coaTitle'],
                                                autoLoad: true,
                                                proxy: {
                                                    type: 'rest',
                                                    url: m_api + '/common/getcombo', // url that will load data with respect to start and limit params
                                                    extraParams: {
                                                        table: 'accounting_coa',
                                                        name: 'coaTitle',
                                                        id: 'coaCode'
                                                    },
                                                    reader: {
                                                        type: 'json',
                                                        root: 'data',
                                                        totalProperty: 'total'
                                                    }
                                                }
                                            }),
                                            listConfig: {
                                                loadingText: 'Searching...',
                                                minWidth:350,
                                                getInnerTpl: function() {
                                                    return '{coaCode} - {coaTitle}';
                                                }
                                            },
                                            minChars:1,
                                            displayField: 'coaCode',
                                            valueField: 'coaCode',
                                            name: 'coaCode'

                                        }
                                    },{
                                        header: 'Coa Title',
                                        dataIndex: 'coaTitle',
                                        flex:true,
                                        renderer:function(v){
                                            return '<span style="font-family:courier new">' + v + '</span>';
                                        }
                                    },{
                                        header: 'Amount',
                                        dataIndex: 'cashDetailAmount',
                                        width:200,
                                        align:'right',
                                        renderer:function(v){
                                            return '<span style="font-family:courier new">' + Ext.util.Format.number(v,'0,000.00') + '</span>';
                                        },
                                        editor: {
                                            xtype: 'numberfield'
                                        }
                                    }]
                                }
                            ]
                        })
                    ],
                    width:720,
                    height:500,
                    modal:true,
                    layout:{
                        type:'border'
                    },
                    buttons: [
                        {
                            xtype:'button',
                            text:'Save',
                            id:'btn-save-frm-Journal',
                            margin: '5px',
                            scale: 'large',
                            ui: 's-button',
                            cls: 's-blue',
                            handler:function(c){
                                var form = Ext.getCmp('frm-Journal');
                                var frm = form.getForm();
                                var id = frm.getValues();
                                if(id.JOURNAL_ID === ''){
                                    var url = window.location + 'api/journal/add';
                                    var method = 'POST';

                                } else {
                                    var url = window.location + 'api/journal/edit/'+id.jouralID;
                                    var method = 'PUT';

                                }
                                var data_grid = Ext.getCmp('grid-cash-detail').store;
                                var detail = [];

                                data_grid.each(function(value, index, rec){
                                    detail.push(value.data);
                                });

                                frm.submit({
                                    url: url,
                                    method: method,
                                    params:{data:Ext.JSON.encode(detail)},
                                    success: function(f, resp) {
                                        Ext.Msg.alert('Success', 'Journal successfully saved');
                                        win.close();
                                        jtore.loadPage(1);
                                    },
                                    failure: function(f, resp) {
                                        Ext.Msg.alert('Failed', 'Cannot save journal');
                                    }
                                });
                            }
                        },
                        {
                            xtype:'button',
                            text:'Cancel',
                            margin: '5px',
                            scale: 'large',
                            ui: 's-button',
                            cls: 's-grey',
                            id:'btn-cancel-frm-Journal',
                            handler:function(){
                                var form = Ext.getCmp('frm-Journal');
                                form.getForm().reset();
                                win.close();
                            }
                        }
                    ]
                }).show();

            }
        }, {
            xtype: 'button',
            disabled: true,
            id: 'btn-edit-journal',
            text:'Update Transaction',
            iconCls: 'edit',
            handler: function() {

                var sm = me.getSelectionModel();
                var sel = sm.getSelection();

                var id = sel[0].data.journalID;
                
                var win = Ext.create('Ext.Window',{
                    title:'Cash-In',
                    items:[
                        Ext.create('Ext.form.Panel' ,{
                            id:'frm-Journal',
                            region:'center',
                            layout:{
                                type:'vbox',
                                align:'stretch'
                            },
                            bodyStyle:'background:#FAFAFA',
                            items: [
                                {
                                    xtype:'hidden',
                                    name:'journalID'
                                },
                                {
                                    xtype:'container',
                                    layout:{
                                        type:'table',
                                        columns:3
                                    },
                                    defaults:{
                                        labelAlign:'top',
                                        margin:'0 10 0 10',
                                        labelSeparator:'',
                                        labelStyle:'font-weight:bold'
                                    },
                                    items:[
                                        {
                                            xtype:'textfield',
                                            readOnly:true,
                                            width:200,
                                            fieldLabel:'Transaction No.',
                                            name:'cashTransNo'
                                        },
                                        {
                                            xtype:'datefield',
                                            name:'cashTransDate',
                                            fieldLabel: 'Date <b style="color:red">*</b>',
                                            value:new Date()
                                        },
                                        {
                                            xtype:'textarea',
                                            width:250,
                                            fieldLabel:'Remark',
                                            rowspan:2,
                                            name:'cashTransRemark'
                                        },
                                        {
                                            xtype:'textfield',
                                            fieldLabel:'From',
                                            width:200,
                                            name:'cashTransFrom'
                                        },
                                        {
                                            xtype: 'combo',
                                            width:200,
                                            fieldLabel:'Cash-In to',
                                            store: Ext.create('Ext.data.Store', {
                                                fields: ['coaCode', 'coaTitle'],
                                                autoLoad: true,
                                                proxy: {
                                                    type: 'rest',
                                                    url: m_api + '/common/getcombo', // url that will load data with respect to start and limit params
                                                    extraParams: {
                                                        table: 'accounting_coa',
                                                        name: 'coaCode',
                                                        id: 'coaTitle'
                                                    },
                                                    reader: {
                                                        type: 'json',
                                                        root: 'data',
                                                        totalProperty: 'total'
                                                    }
                                                }
                                            }),
                                            listConfig: {
                                                loadingText: 'Searching...',
                                                minWidth:450,
                                                getInnerTpl: function() {
                                                    return '{coaCode} - {coaTitle}';
                                                }
                                            },
                                            displayField: 'coaTitle',
                                            valueField: 'coaCode',
                                            name: 'coaTitle'
                                        }
                                    ]
                                },
                                {
                                    xtype:'grid',
                                    id:'grid-cash-detail',
                                    flex:1,
                                    tbar:[
                                            {
                                            xtype:'button',
                                            iconCls:'add',
                                            text:'Add Row',
                                            handler:function(c){
                                                var grid = c.up('.grid');
                                                var last = grid.store.getCount();
                                                // empty record
                                                grid.store.insert(last, Ext.create('Core.Module.Accounting.model.CashDetail',{
                                                    coaCode:'',
                                                    cashDetailAmount:''
                                                }));

                                                rowedit.startEdit(last, 0);
                                            }
                                        },
                                        {
                                            xtype:'button',
                                            iconCls:'add',
                                            text:'Remove Row',
                                            handler:function(c){
                                                var grid = Ext.getCmp('grid-cash-detail');
                                                var sm = grid.getSelectionModel();
                                                var sel = sm.getSelection();
                                                grid.store.remove(sel);
                                            }
                                        }
                                    ],
                                    bbar:[
                                        '->',
                                        '<b>Total</b>',
                                        { xtype: 'textfield', width: 200, readOnly:true, fieldStyle:'font-weight:bold;text-align:right' }
                                    ],
                                    store:Ext.create('Ext.data.Store', {
                                        model: 'Core.Module.Accounting.model.CashDetail',
                                        autoLoad: true,
                                        remoteSort: true,
                                        proxy: {
                                            type: 'rest',
                                            url: m_api + '/jurnal/getdetail', // url that will load data with respect to start and limit params
                                            reader: {
                                                type: 'json',
                                                root: 'data',
                                                totalProperty: 'total',
                                                idProperty: 'cashDetailID'
                                            },
                                            writer: {
                                                type: 'json'
                                            },
                                            api: {
                                                destroy: window.location + 'api/cashio/delete'
                                            },
                                            appendId: true
                                        }
                                    }),
                                    plugins:[rowedit],
                                    selType: 'cellmodel',
                                    columns:[{
                                        header: 'COA Code',
                                        dataIndex: 'coaCode',
                                        width:200,
                                        renderer:function(v){
                                            return '<span style="font-family:courier new">' + v + '</span>';
                                        },
                                        field: {
                                            xtype: 'combo',
                                            disabledCls: 'disabled',
                                            id:'cmb-coa-journal-detail',
                                            listeners:{
                                                select:function(c,r){
                                                    var grid = Ext.getCmp('grid-cash-detail');
                                                    var sm = grid.getSelectionModel();
                                                    var sel = sm.getSelection();

                                                    var value = r[0].data.coaTitle;
                                                    sel[0].set('coaTitle',value);
                                                }
                                            },
                                            store: Ext.create('Ext.data.Store', {
                                                fields: ['coaCode', 'coaTitle'],
                                                autoLoad: true,
                                                proxy: {
                                                    type: 'rest',
                                                    url: m_api + '/common/getcombo', // url that will load data with respect to start and limit params
                                                    extraParams: {
                                                        table: 'accounting_coa',
                                                        name: 'coaTitle',
                                                        id: 'coaCode'
                                                    },
                                                    reader: {
                                                        type: 'json',
                                                        root: 'data',
                                                        totalProperty: 'total'
                                                    }
                                                }
                                            }),
                                            listConfig: {
                                                loadingText: 'Searching...',
                                                minWidth:350,
                                                getInnerTpl: function() {
                                                    return '{coaCode} - {coaTitle}';
                                                }
                                            },
                                            minChars:1,
                                            displayField: 'coaCode',
                                            valueField: 'coaCode',
                                            name: 'coaCode'

                                        }
                                    },{
                                        header: 'Coa Title',
                                        dataIndex: 'coaTitle',
                                        flex:true,
                                        renderer:function(v){
                                            return '<span style="font-family:courier new">' + v + '</span>';
                                        }
                                    },{
                                        header: 'Amount',
                                        dataIndex: 'cashDetailAmount',
                                        width:200,
                                        align:'right',
                                        renderer:function(v){
                                            return '<span style="font-family:courier new">' + Ext.util.Format.number(v,'0,000.00') + '</span>';
                                        },
                                        editor: {
                                            xtype: 'numberfield'
                                        }
                                    }]
                                }
                            ]
                        })
                    ],
                    width:720,
                    height:500,
                    modal:true,
                    layout:{
                        type:'border'
                    },
                    buttons: [
                        {
                            xtype:'button',
                            text:'Save',
                            id:'btn-save-frm-Journal',
                            margin: '5px',
                            scale: 'large',
                            ui: 's-button',
                            cls: 's-blue',
                            handler:function(c){
                                var form = Ext.getCmp('frm-Journal');
                                var frm = form.getForm();
                                var id = frm.getValues();
                                if(id.JOURNAL_ID === ''){
                                    var url = window.location + 'api/journal/add';
                                    var method = 'POST';

                                } else {
                                    var url = window.location + 'api/journal/edit/'+id.jouralID;
                                    var method = 'PUT';

                                }
                                var data_grid = Ext.getCmp('grid-cash-detail').store;
                                var detail = [];

                                data_grid.each(function(value, index, rec){
                                    detail.push(value.data);
                                });

                                frm.submit({
                                    url: url,
                                    method: method,
                                    params:{data:Ext.JSON.encode(detail)},
                                    success: function(f, resp) {
                                        Ext.Msg.alert('Success', 'Journal successfully saved');
                                        win.close();
                                        jtore.loadPage(1);
                                    },
                                    failure: function(f, resp) {
                                        Ext.Msg.alert('Failed', 'Cannot save journal');
                                    }
                                });
                            }
                        },
                        {
                            xtype:'button',
                            text:'Cancel',
                            margin: '5px',
                            scale: 'large',
                            ui: 's-button',
                            cls: 's-grey',
                            id:'btn-cancel-frm-Journal',
                            handler:function(){
                                var form = Ext.getCmp('frm-Journal');
                                form.getForm().reset();
                                win.close();
                            }
                        }
                    ]
                }).show();
                var form = Ext.getCmp('frm-Journal');
                form.getForm().load({
                    url:window.location + 'api/journal/get/' + id,
                    method:'GET',
                    success:function(c,r){
                        
                    }
                });

                Ext.getCmp('grid-cash-detail').store.proxy.url = m_api + '/jurnal/getdetail/'+id;
                Ext.getCmp('grid-cash-detail').store.load();
            }
        }, {
            xtype: 'button',
            disabled: true,
            id: 'btn-del-journal',
            text:'Delete Transaction',
            iconCls: 'delete',
            handler: function() {
                var sm = me.getSelectionModel();
                var sel = sm.getSelection();

                Ext.MessageBox.show({
                    title: 'Delete Data',
                    msg: 'Are You Sure?',
                    width: 300,
                    buttons: Ext.MessageBox.YESNO,
                    fn: del,
                    animateTarget: 'mb3'
                });

                function del(btn) {
                    if (btn === "yes") {
                        me.store.remove(sel);
                        me.store.sync();
                    }
                }

              }
            }]
        }]
    });
});

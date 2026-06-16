Ext.onReady(function() {

    Ext.tip.QuickTipManager.init();

    Ext.define('Core.Module.Journal.model.Journal', {
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

    Ext.define('Core.Module.Journal.model.Detail', {
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
        model: 'Core.Module.Journal.model.Journal',
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
                idProperty: 'journalID'
            },
            writer: {
                type: 'json'
            },
            api: {
                destroy: window.location + 'api/jurnal/delete'
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
        alias: 'widget.Journal-grid',
        style:'border: 1px solid #CCCCCC',
        renderTo: 'ext-content',
        autoScroll: true,
        id: 'grid-Journal',
        store: jstore,
        height:800,
        columns: [{
            header: 'Code',
            dataIndex: 'journalTypeCode',
            width:85,
            renderer:function(v){
                return '<span style="font-family:courier new">' + v + '</span>';
            }
        }, {
            header: 'Date',
            dataIndex: 'journalDate',
            width:100,
            renderer:function(v){
                if(v === '0000-00-00') { return ''; } else { return '<span style="font-family:courier new">' + Ext.util.Format.date(v,'d/m/Y') + '</span>'; }

            }
        },{
            header: 'Memo',
            dataIndex: 'journalMemo',
            flex: 1,
            renderer:function(v){
                return '<span style="font-family:courier new">' + v + '</span>';
            }
        },{
            header: 'Debet',
            align:'right',
            dataIndex: 'DEBET',
            width:200,
            renderer:function(v){
                return '<span style="font-family:courier new">' + Ext.util.Format.number(v,'0,000.00') + '</span>';
            }
        },{
            header: 'Kredit',
            align:'right',
            dataIndex: 'KREDIT',
            width:200,
            renderer:function(v){
                return '<span style="font-family:courier new">' + Ext.util.Format.number(v,'0,000.00') + '</span>';
            }
        },{
            header: 'Posted',
            dataIndex: 'journalIsPosted',
            width:100,
            renderer:function(v){
                if(v === 1){
                    v = 'Yes';
                } else {
                    v = 'No';
                }
                return '<span style="font-family:courier new">' + v + '</span>';
            }
        },{
            header: 'Post Date',
            dataIndex: 'journalPostedDate',
            width:100,
            renderer:function(v){
                return '<span style="font-family:courier new">' + Ext.util.Format.date(v,'d/m/Y') + '</span>';
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
                xtype:'button',
                text:'Pull Transaction Journal',
                iconCls:'add',
                handler : function(){

                    var win = Ext.create('Ext.Window',{
                        title:'Pull Transaction Data',
                        modal:true,
                        items:[
                            {
                                xtype:'form',
                                padding:5,
                                layout:{
                                    type:'table',
                                    columns:2
                                },
                                defaults:{
                                    labelAlign:'top',
                                    margin:5
                                },
                                items:[
                                    {
                                        xtype:'datefield',
                                        fieldLabel:'Start Date'
                                    },
                                    {
                                        xtype:'datefield',
                                        fieldLabel:'End Date'
                                    }
                                ]
                            }
                        ],
                        buttons:[
                            {
                                xtype:'button',
                                margin: '5px',
                                scale: 'large',
                                ui: 's-button',
                                cls: 's-blue ',
                                text:'Pull Data'
                            },
                            {
                                xtype:'button',
                                margin: '5px',
                                scale: 'large',
                                ui: 's-button',
                                cls: 's-grey ',
                                text:'Close'
                            }
                        ]
                    }).show();
                }
            },{
            xtype: 'button',
            text:'Add Journal',
            iconCls: 'add',
            id: 'btn-add-journal',
            handler: function() {

                var win = Ext.create('Ext.Window',{
                    title:'Add Journal',
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
                                        type:'hbox'
                                    },
                                    defaults:{
                                        labelAlign:'top',
                                        margin:10,
                                        labelSeparator:'',
                                        labelStyle:'font-weight:bold'
                                    },
                                    items:[
                                        {
                                            xtype:'datefield',
                                            name:'journalDate',
                                            fieldLabel: 'Date <b style="color:red">*</b>',
                                            value:new Date()
                                        },
                                        {
                                            xtype: 'combo',
                                            disabledCls: 'disabled',
                                            fieldLabel: 'Type <b style="color:red">*</b>',
                                            allowBlank: false,
                                            store: Ext.create('Ext.data.Store', {
                                                fields: ['journalTypeCode', 'journalTypeDesc'],
                                                autoLoad: true,
                                                proxy: {
                                                    type: 'rest',
                                                    url: m_api + '/common/getcombo', // url that will load data with respect to start and limit params
                                                    extraParams: {
                                                        table: 'accounting_journal_type',
                                                        name: 'journalTypeDesc',
                                                        id: 'journalTypeCode'
                                                    },
                                                    reader: {
                                                        type: 'json',
                                                        root: 'data',
                                                        totalProperty: 'total'
                                                    }
                                                }
                                            }),
                                            displayField: 'journalTypeDesc',
                                            valueField: 'journalTypeCode',
                                            name: 'journalTypeCode'

                                        },
                                        {
                                            xtype:'textfield',
                                            fieldLabel:'Memo',
                                            flex:1,
                                            name:'journalMemo'
                                        }
                                    ]
                                },
                                {
                                    xtype:'grid',
                                    id:'grid-journal-detail',
                                    flex:1,
                                    features: [{
                                        ftype: 'summary',
                                        dock: 'bottom'
                                    }],
                                    tbar:[
                                            {
                                            xtype:'button',
                                            iconCls:'add',
                                            text:'Add Row',
                                            handler:function(c){
                                                var grid = c.up('.grid');
                                                var last = grid.store.getCount();
                                                // empty record
                                                grid.store.insert(last, Ext.create('Core.Module.Journal.model.Detail',{
                                                    coaCode:'',
                                                    DEBET:'',
                                                    KREDIT:'',
                                                    journalDetailType:'',
                                                    journalDetailDesc:'',
                                                    currencyName:'',
                                                    journalDetailOrig:'',
                                                    journalDetailSum:'',
                                                    journalDetailExRate:1
                                                }));

                                                rowedit.startEdit(last, 0);
                                            }
                                        },
                                        {
                                            xtype:'button',
                                            iconCls:'add',
                                            text:'Remove Row',
                                            handler:function(c){
                                                var grid = Ext.getCmp('grid-journal-detail');
                                                var sm = grid.getSelectionModel();
                                                var sel = sm.getSelection();
                                                grid.store.remove(sel);
                                            }
                                        }
                                    ],
                                    store:Ext.create('Ext.data.Store', {
                                        model: 'Core.Module.Journal.model.Detail',
                                        autoLoad: true,
                                        remoteSort: true,
                                        proxy: {
                                            type: 'rest',
                                            url: m_api + '/jurnal/getdetail', // url that will load data with respect to start and limit params
                                            reader: {
                                                type: 'json',
                                                root: 'data',
                                                totalProperty: 'total',
                                                idProperty: 'journalDetailID'
                                            },
                                            writer: {
                                                type: 'json'
                                            },
                                            api: {
                                                destroy: window.location + 'api/jurnal/delete'
                                            },
                                            appendId: true
                                        }
                                    }),
                                    plugins:[rowedit],
                                    selType: 'cellmodel',
                                    columns:[{
                                        header: 'COA',
                                        dataIndex: 'coaCode',
                                        width:150,
                                        renderer:function(v){
                                            return '<span style="font-family:courier new">' + v + '</span>';
                                        },
                                        field: {
                                            xtype: 'combo',
                                            disabledCls: 'disabled',
                                            id:'cmb-coa-journal-detail',
                                            listeners:{
                                                select:function(c,r){
                                                    var grid = Ext.getCmp('grid-journal-detail');
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
                                                    url: m_api + '/jurnal/getcombo', // url that will load data with respect to start and limit params
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
                                        width:300,
                                        renderer:function(v){
                                            return '<span style="font-family:courier new">' + v + '</span>';
                                        }
                                    },{
                                        header: 'Curr',
                                        dataIndex: 'currencyName',
                                        width:50,
                                        renderer:function(v,r){
                                            return '<span style="font-family:courier new">' + v + '</span>';
                                        },
                                        editor: {
                                            xtype: 'combo',
                                            disabledCls: 'disabled',
                                            id:'cmb-currency-journal-detail',
                                            listeners:{
                                                select:function(c,r){
                                                    var grid = Ext.getCmp('grid-journal-detail');
                                                    var sm = grid.getSelectionModel();
                                                    var sel = sm.getSelection();

                                                    var value = r[0].data.currencyID;

                                                    sel[0].set('currencyID',value);
                                                }
                                            },
                                            store: Ext.create('Ext.data.Store', {
                                                fields: ['currencyID', 'currencyName'],
                                                autoLoad: true,
                                                proxy: {
                                                    type: 'rest',
                                                    url: m_api + '/common/getcombo', // url that will load data with respect to start and limit params
                                                    extraParams: {
                                                        table: 'accounting_currency',
                                                        name: 'currencyID',
                                                        id: 'currencyName'
                                                    },
                                                    reader: {
                                                        type: 'json',
                                                        root: 'data',
                                                        totalProperty: 'total'
                                                    }
                                                }
                                            }),
                                            displayField: 'currencyName',
                                            valueField: 'currencyName',
                                            name: 'currencyName'
                                        }
                                    },{
                                        header: 'Original Amount',
                                        dataIndex: 'journalDetailOrig',
                                        width:170,
                                        align:'right',
                                        renderer:function(v){
                                            return '<span style="font-family:courier new">' + Ext.util.Format.number(v,'0,000.00') + '</span>';
                                        },
                                        editor: {
                                            xtype: 'numericfield'
                                        }
                                    },{
                                        header: 'Exchange Rate',
                                        dataIndex: 'journalDetailExRate',
                                        width:70,
                                        align:'right',
                                        renderer:function(v){
                                            return '<span style="font-family:courier new">' + Ext.util.Format.number(v,'0,000.00') + '</span>';
                                        },
                                        editor: {
                                            xtype: 'numericfield'
                                        }
                                    },{
                                        header: 'D/K',
                                        dataIndex: 'journalDetailType',
                                        width:80,
                                        renderer:function(v,r){
                                            return '<span style="font-family:courier new">' + v + '</span>';
                                        },
                                        editor: {
                                            xtype: 'combo',
                                            disabledCls: 'disabled',
                                            id:'cmb-side-journal-detail',
                                            listeners:{
                                                select:function(c,r){
                                                    var grid = Ext.getCmp('grid-journal-detail');
                                                    var sm = grid.getSelectionModel();
                                                    var sel = sm.getSelection();

                                                    var value = r[0].data.SIDE;

                                                    sel[0].set('SIDE',value);

                                                    if(value === 'DEBET'){
                                                        sel[0].set('DEBET',(sel[0].get('journalDetailExRate') * sel[0].get('journalDetailOrig')));
                                                        sel[0].set('KREDIT',0);
                                                    }

                                                    if(value === 'KREDIT'){
                                                        sel[0].set('KREDIT',(sel[0].get('journalDetailExRate') * sel[0].get('journalDetailOrig')));
                                                        sel[0].set('DEBET',0);
                                                    }
                                                }
                                            },
                                            store: Ext.create('Ext.data.Store', {
                                                fields: ['SIDE'],
                                                autoLoad: true,
                                                data: [
                                                    {SIDE: 'DEBET'},
                                                    {SIDE: 'KREDIT'}
                                                ]
                                            }),
                                            displayField: 'SIDE',
                                            valueField: 'SIDE',
                                            name: 'SIDE'
                                        }
                                    },{
                                        header: 'Debet',
                                        align:'right',
                                        dataIndex: 'DEBET',
                                        width:170,
                                        summaryType: 'sum',
                                        summaryRenderer: function(value, summaryData, dataIndex) {
                                            return '<span style="font-family:courier new; font-weight:bold">' + Ext.util.Format.number(value,'0,000.00') + '</span>';
                                        },
                                        renderer:function(v){
                                            return '<span style="font-family:courier new">' + Ext.util.Format.number(v,'0,000.00') + '</span>';
                                        }
                                    },{
                                        header: 'Kredit',
                                        align:'right',
                                        dataIndex: 'KREDIT',
                                        width:170,
                                        summaryType: 'sum',
                                        summaryRenderer: function(value, summaryData, dataIndex) {
                                            return '<span style="font-family:courier new; font-weight:bold">' + Ext.util.Format.number(value,'0,000.00') + '</span>';
                                        },
                                        renderer:function(v){
                                            return '<span style="font-family:courier new">' + Ext.util.Format.number(v,'0,000.00') + '</span>';
                                        }
                                    },{
                                        header: 'Description',
                                        dataIndex: 'journalDetailDesc',
                                        width:180,
                                        renderer:function(v){
                                            return '<span style="font-family:courier new">' + v + '</span>';
                                        },
                                        editor: {
                                            type: 'textfield'
                                        }
                                    }]
                                }
                            ]
                        })
                    ],
                    width:'100%',
                    height:500,
                    modal:true,
                    layout:{
                        type:'border'
                    },
                    bbar: [
                        '->',

                        {
                            xtype:'button',
                            text:'Save',
                            id:'btn-save-frm-Journal',
                            iconCls:'save',
                            handler:function(c){
                                var form = Ext.getCmp('frm-Journal');
                                var frm = form.getForm();
                                var id = frm.getValues();
                                if(id.journalID === ''){
                                    var url = m_api + '/jurnal/add';
                                    var method = 'POST';

                                } else {
                                    var url = m_api + '/jurnal/edit/'+id.jouralID;
                                    var method = 'PUT';

                                }
                                var data_grid = Ext.getCmp('grid-journal-detail').store;
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
                                        jstore.loadPage(1);
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
                            iconCls:'cancel',
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
            text:'Edit Journal',
            iconCls: 'edit',
            handler: function() {

                var sm = me.getSelectionModel();
                var sel = sm.getSelection();

                var id = sel[0].data.journalID;

                var win = Ext.create('Ext.Window',{
                    title:'Edit Journal',
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
                                    name:'JournalID'
                                },
                                {
                                    xtype:'container',
                                    layout:{
                                        type:'hbox'
                                    },
                                    defaults:{
                                        labelAlign:'top',
                                        margin:10,
                                        labelSeparator:'',
                                        labelStyle:'font-weight:bold'
                                    },
                                    items:[
                                        {
                                            xtype:'datefield',
                                            name:'JournalDate',
                                            fieldLabel: 'Date <b style="color:red">*</b>',
                                            value:new Date()
                                        },
                                        {
                                            xtype: 'combo',
                                            disabledCls: 'disabled',
                                            fieldLabel: 'Type <b style="color:red">*</b>',
                                            allowBlank: false,
                                            store: Ext.create('Ext.data.Store', {
                                                fields: ['journalTypeCode', 'journalTypeDesc'],
                                                autoLoad: true,
                                                proxy: {
                                                    type: 'rest',
                                                    url: m_api + '/common/getcombo', // url that will load data with respect to start and limit params
                                                    extraParams: {
                                                        table: 'accounting_journal_type',
                                                        name: 'journalTypeDesc',
                                                        id: 'journalTypeCode'
                                                    },
                                                    reader: {
                                                        type: 'json',
                                                        root: 'data',
                                                        totalProperty: 'total'
                                                    }
                                                }
                                            }),
                                            displayField: 'journalTypeDesc',
                                            valueField: 'journalTypeCode',
                                            name: 'JournalTypeCode'

                                        },
                                        {
                                            xtype:'textfield',
                                            fieldLabel:'Memo',
                                            flex:1,
                                            name:'JournalMemo'
                                        }
                                    ]
                                },
                                {
                                    xtype:'grid',
                                    id:'grid-journal-detail',
                                    flex:1,
                                    features: [{
                                        ftype: 'summary',
                                        dock: 'bottom'
                                    }],
                                    tbar:[
                                            {
                                            xtype:'button',
                                            iconCls:'add',
                                            text:'Add Row',
                                            handler:function(c){
                                                var grid = c.up('.grid');
                                                var last = grid.store.getCount();
                                                // empty record
                                                grid.store.insert(last, Ext.create('Core.Module.Journal.model.Detail',{
                                                    coaCode:'',
                                                    DEBET:'',
                                                    KREDIT:'',
                                                    journalDetailType:'',
                                                    journalDetailDesc:'',
                                                    currencyName:'',
                                                    journalDetailOrig:'',
                                                    journalDetailSum:'',
                                                    journalDetailExRate:1
                                                }));

                                                rowedit.startEdit(last, 0);
                                            }
                                        },
                                        {
                                            xtype:'button',
                                            iconCls:'add',
                                            text:'Remove Row',
                                            handler:function(c){
                                                var grid = Ext.getCmp('grid-journal-detail');
                                                var sm = grid.getSelectionModel();
                                                var sel = sm.getSelection();
                                                grid.store.remove(sel);
                                            }
                                        }
                                    ],
                                    store:Ext.create('Ext.data.Store', {
                                        model: 'Core.Module.Journal.model.Detail',
                                        autoLoad: true,
                                        remoteSort: true,
                                        proxy: {
                                            type: 'rest',
                                            url: m_api + '/jurnal/getdetail', // url that will load data with respect to start and limit params
                                            reader: {
                                                type: 'json',
                                                root: 'data',
                                                totalProperty: 'total',
                                                idProperty: 'journalDetailID'
                                            },
                                            writer: {
                                                type: 'json'
                                            },
                                            api: {
                                                destroy: window.location + 'api/jurnal/delete'
                                            },
                                            appendId: true
                                        }
                                    }),
                                    plugins:[rowedit],
                                    selType: 'cellmodel',
                                    columns:[{
                                        header: 'COA',
                                        dataIndex: 'coaCode',
                                        width:150,
                                        renderer:function(v){
                                            return '<span style="font-family:courier new">' + v + '</span>';
                                        },
                                        field: {
                                            xtype: 'combo',
                                            disabledCls: 'disabled',
                                            id:'cmb-coa-journal-detail',
                                            listeners:{
                                                select:function(c,r){
                                                    var grid = Ext.getCmp('grid-journal-detail');
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
                                        width:300,
                                        renderer:function(v){
                                            return '<span style="font-family:courier new">' + v + '</span>';
                                        }
                                    },{
                                        header: 'Curr',
                                        dataIndex: 'currencyName',
                                        width:50,
                                        renderer:function(v,r){
                                            return '<span style="font-family:courier new">' + v + '</span>';
                                        },
                                        editor: {
                                            xtype: 'combo',
                                            disabledCls: 'disabled',
                                            id:'cmb-currency-journal-detail',
                                            listeners:{
                                                select:function(c,r){
                                                    var grid = Ext.getCmp('grid-journal-detail');
                                                    var sm = grid.getSelectionModel();
                                                    var sel = sm.getSelection();

                                                    var value = r[0].data.currencyID;

                                                    sel[0].set('currencyID',value);
                                                }
                                            },
                                            store: Ext.create('Ext.data.Store', {
                                                fields: ['currencyID', 'currencyName'],
                                                autoLoad: true,
                                                proxy: {
                                                    type: 'rest',
                                                    url: m_api + '/common/getcombo', // url that will load data with respect to start and limit params
                                                    extraParams: {
                                                        table: 'accounting_currency',
                                                        name: 'currencyID',
                                                        id: 'currencyName'
                                                    },
                                                    reader: {
                                                        type: 'json',
                                                        root: 'data',
                                                        totalProperty: 'total'
                                                    }
                                                }
                                            }),
                                            displayField: 'currencyName',
                                            valueField: 'currencyName',
                                            name: 'currencyName'
                                        }
                                    },{
                                        header: 'Original Amount',
                                        dataIndex: 'journalDetailOrig',
                                        width:170,
                                        align:'right',
                                        renderer:function(v){
                                            return '<span style="font-family:courier new">' + Ext.util.Format.number(v,'0,000.00') + '</span>';
                                        },
                                        editor: {
                                            xtype: 'numericfield'
                                        }
                                    },{
                                        header: 'Exchange Rate',
                                        dataIndex: 'journalDetailExRate',
                                        width:70,
                                        align:'right',
                                        renderer:function(v){
                                            return '<span style="font-family:courier new">' + Ext.util.Format.number(v,'0,000.00') + '</span>';
                                        },
                                        editor: {
                                            xtype: 'numericfield'
                                        }
                                    },{
                                        header: 'D/K',
                                        dataIndex: 'journalDetailType',
                                        width:80,
                                        renderer:function(v,r){
                                            return '<span style="font-family:courier new">' + v + '</span>';
                                        },
                                        editor: {
                                            xtype: 'combo',
                                            disabledCls: 'disabled',
                                            id:'cmb-side-journal-detail',
                                            listeners:{
                                                select:function(c,r){
                                                    var grid = Ext.getCmp('grid-journal-detail');
                                                    var sm = grid.getSelectionModel();
                                                    var sel = sm.getSelection();

                                                    var value = r[0].data.SIDE;

                                                    sel[0].set('SIDE',value);

                                                    if(value === 'DEBET'){
                                                        sel[0].set('DEBET',(sel[0].get('journalDetailExRate') * sel[0].get('journalDetailOrig')));
                                                        sel[0].set('KREDIT',0);
                                                    }

                                                    if(value === 'KREDIT'){
                                                        sel[0].set('KREDIT',(sel[0].get('journalDetailExRate') * sel[0].get('journalDetailOrig')));
                                                        sel[0].set('DEBET',0);
                                                    }
                                                }
                                            },
                                            store: Ext.create('Ext.data.Store', {
                                                fields: ['SIDE'],
                                                autoLoad: true,
                                                data: [
                                                    {SIDE: 'DEBET'},
                                                    {SIDE: 'KREDIT'}
                                                ]
                                            }),
                                            displayField: 'SIDE',
                                            valueField: 'SIDE',
                                            name: 'SIDE'
                                        }
                                    },{
                                        header: 'Debet',
                                        align:'right',
                                        dataIndex: 'DEBET',
                                        width:170,
                                        summaryType: 'sum',
                                        summaryRenderer: function(value, summaryData, dataIndex) {
                                            return '<span style="font-family:courier new; font-weight:bold">' + Ext.util.Format.number(value,'0,000.00') + '</span>';
                                        },
                                        renderer:function(v){
                                            return '<span style="font-family:courier new">' + Ext.util.Format.number(v,'0,000.00') + '</span>';
                                        }
                                    },{
                                        header: 'Kredit',
                                        align:'right',
                                        dataIndex: 'KREDIT',
                                        width:170,
                                        summaryType: 'sum',
                                        summaryRenderer: function(value, summaryData, dataIndex) {
                                            return '<span style="font-family:courier new; font-weight:bold">' + Ext.util.Format.number(value,'0,000.00') + '</span>';
                                        },
                                        renderer:function(v){
                                            return '<span style="font-family:courier new">' + Ext.util.Format.number(v,'0,000.00') + '</span>';
                                        }
                                    },{
                                        header: 'Description',
                                        dataIndex: 'journalDetailDesc',
                                        width:180,
                                        renderer:function(v){
                                            return '<span style="font-family:courier new">' + v + '</span>';
                                        },
                                        editor: {
                                            type: 'textfield'
                                        }
                                    }]
                                }
                            ]
                        })
                    ],
                    width:'100%',
                    height:500,
                    modal:true,
                    layout:{
                        type:'border'
                    },
                    bbar: [
                        '->',

                        {
                            xtype:'button',
                            text:'Save',
                            id:'btn-save-frm-Journal',
                            iconCls:'save',
                            handler:function(c){
                                var form = Ext.getCmp('frm-Journal');
                                var frm = form.getForm();
                                var id = frm.getValues();
                                if(id.journalID === ''){
                                    var url = m_api + 'jurnal/add';
                                    var method = 'POST';

                                } else {
                                    var url = m_api + 'jurnal/edit/'+id.JournalID;
                                    var method = 'PUT';

                                }
                                var data_grid = Ext.getCmp('grid-journal-detail').store;
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
                                        jstore.loadPage(1);

                                    },
                                    failure: function(f, resp) {
                                        Ext.Msg.alert('Success', 'Cannot save journal');
                                    }
                                });
                            }
                        },
                        {
                            xtype:'button',
                            text:'Cancel',
                            iconCls:'cancel',
                            id:'btn-cancel-frm-Journal',
                            handler:function(){
                                win.close();
                            }
                        }
                    ]
                }).show();
                var form = Ext.getCmp('frm-Journal');
                form.getForm().load({
                    url:'/api/index.php/jurnal/get/' + id,
                    method:'GET',
                    success:function(c,r){

                    }
                });

                Ext.getCmp('grid-journal-detail').store.proxy.url = m_api + '/jurnal/getdetail/'+id;
                Ext.getCmp('grid-journal-detail').store.load();
            }
        }, {
            xtype: 'button',
            disabled: true,
            id: 'btn-del-journal',
            text:'Delete Journal',
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
            },{
                xtype:'button',
                text:'Post Selected Journal',
                iconCls:'edit',
                handler:function(){
                    var sm = me.getSelectionModel();
                    var sel = sm.getSelection();

                    Ext.MessageBox.show({
                        title: 'Post Selected Data',
                        msg: 'Are You Sure?',
                        width: 300,
                        buttons: Ext.MessageBox.YESNO,
                        fn: del,
                        animateTarget: 'mb3'
                    });

                    function del(btn) {
                        if (btn === "yes") {
                            var output = [];

                            Ext.each(sel,function(val,index,data){
                                output.push(val.get('journalID'));
                            });

                            Ext.Ajax.request({
                                url: window.location + 'api/jurnal/post/',
                                method: 'PUT',
                                params:{id:Ext.JSON.encode(output)},
                                success: function(resp) {
                                    me.store.load();
                                },
                                failure: function() {
                                    Ext.MessageBox.show({
                                        title: 'Failed Posting',
                                        msg: 'Failed Posting Journal, please check your journal detail',
                                        width: 300,
                                        buttons: Ext.MessageBox.OK,
                                        animateTarget: 'mb3'
                                    });
                                }
                            });
                        }
                    }
                }
            }]
        }]
    });
});

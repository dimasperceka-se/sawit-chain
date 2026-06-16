Ext.onReady(function() {
    
    Ext.tip.QuickTipManager.init();
    
    Ext.create('Ext.panel.Panel', {
        layout: 'fit',
        autoScroll: true,
        id: 'panel-ledger',
        renderTo:'ext-content',
        dockedItems:[
            {
                xtype:'toolbar',
                dock:'top',
                items:[
                    {
                        xtype:'form',
                        id:'frm-ledger',
                        layout:{
                            type:'hbox'
                        },
                        defaults:{
                            margin:'3 3 3 3'
                        },
                        items:[
                            {
                                xtype:'datefield',
                                value:m_startdate,
                                name:'START_DATE',
                                allowBlank:false,
                                labelAlign:'top',
                                submitFormat:'Y-m-d',
                                fieldLabel:'Start Date <span style="color:red;font-weight:bold">*</span>'
                            },{
                                xtype:'datefield',
                                value:m_enddate,
                                name:'END_DATE',
                                allowBlank:false,
                                labelAlign:'top',
                                submitFormat:'Y-m-d',
                                fieldLabel:'End Date <span style="color:red;font-weight:bold">*</span>'
                            },{
                                xtype: 'combo',
                                width:300,
                                // hidden:true,
                                fieldLabel: 'COA <b style="color:red">*</b>',
                                store: Ext.create('Ext.data.Store', {
                                    fields: ['COA_CODE', 'COA_TITLE'],
                                    autoLoad: true,
                                    proxy: {
                                        type: 'rest',
                                        url: m_api + 'coa/fin_coas?&type=all', // url that will load data with respect to start and limit params
                                        extraParams: {
                                            table: 'r_coa',
                                            name: 'COA_TITLE',
                                            id: 'COA_CODE'
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
                                        return '{COA_CODE} - {COA_TITLE}';
                                    }
                                },
                                labelAlign:'top',
                                displayField: 'COA_TITLE',
                                valueField: 'COA_CODE',
                                name: 'COA_CODE'

                            },{
                                xtype: 'combo',
                                hidden:true,
                                disabledCls: 'disabled',
                                fieldLabel: 'Status',
                                labelAlign:'top',
                                store: Ext.create('Ext.data.Store', {
                                    fields: ['STATUS_ID', 'STATUS_NAME'],
                                    autoLoad: true,
                                    data: [
                                        {STATUS_ID: '1', STATUS_NAME: 'Unposted'},
                                        {STATUS_ID: '2', STATUS_NAME: 'Posted'},
                                        {STATUS_ID: '3', STATUS_NAME: 'All'}
                                    ]
                                }),
                                displayField: 'STATUS_NAME',
                                valueField: 'STATUS_ID',
                                name: 'JOURNAL_STATUS'
                            },{
                                xtype:'button',
                                margin:'32 3 3 3',
                                text:'Generate Ledger',
                                handler:function(){
                                    var params = Ext.getCmp('frm-ledger').getForm().getValues();
                                    var loader = Ext.getCmp('panel-ledger').getLoader();
                                    loader.load({
                                        params:params
                                    });
                                }
                            }
                        ]
                    }
                ]
            }
        ],
        loader: {
            url: m_crud + '/ledger',
            autoLoad: false,
            renderer: function(loader, response, active) { 
                editJurnalLink = function(val){
                var rowedit = Ext.create('Ext.grid.plugin.CellEditing', {
                    listeners: {
                        edit:function(editor,e){

                        }
                    }
                });
                var win = new Ext.Window({
                    title:'Journal - Edit',
                    align:'center',
                    constrain:true,
                    modal:true,
                    width:'100%',
                    height:500,
                    y:100,
                    id:'win-edit-journal',
                    buttonAlign:'center',
                    bodyStyle:'background:#ffffff',
                    items:[
                        {
                            xtype:'form',
                            items:[
                                {
                                    xtype:'hidden',
                                    name:'JOURNAL_ID'
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
                                            name:'JOURNAL_DATE',
                                            fieldLabel: 'Date <b style="color:red">*</b>',
                                            value:new Date()
                                        },
                                        {
                                            xtype: 'combo',
                                            disabledCls: 'disabled',
                                            fieldLabel: 'Type <b style="color:red">*</b>',
                                            allowBlank: false,
                                            store: Ext.create('Ext.data.Store', {
                                                fields: ['JOURNAL_TYPE_CODE', 'JOURNAL_TYPE_DESC'],
                                                autoLoad: true,
                                                proxy: {
                                                    type: 'rest',
                                                    url: window.location + 'api/common/get-combo', // url that will load data with respect to start and limit params
                                                    extraParams: {
                                                        table: 'r_journal_type',
                                                        name: 'JOURNAL_TYPE_DESC',
                                                        id: 'JOURNAL_TYPE_CODE'
                                                    },
                                                    reader: {
                                                        type: 'json',
                                                        root: 'data',
                                                        totalProperty: 'total'
                                                    }
                                                }
                                            }),
                                            displayField: 'JOURNAL_TYPE_DESC',
                                            valueField: 'JOURNAL_TYPE_CODE',
                                            name: 'JOURNAL_TYPE_CODE'

                                        },
                                        {
                                            xtype:'textfield',
                                            fieldLabel:'Memo',
                                            flex:1,
                                            name:'JOURNAL_MEMO'
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
                                                    COA_CODE:'',
                                                    DEBET:'',
                                                    KREDIT:'',
                                                    JOURNAL_DETAIL_TYPE:'',
                                                    JOURNAL_DETAIL_DESC:'',
                                                    CURRENCY_NAME:'',
                                                    JOURNAL_DETAIL_ORIG:'',
                                                    JOURNAL_DETAIL_SUM:'',
                                                    JOURNAL_DETAIL_EX_RATE:1
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
                                            url: window.location + 'api/journal/get-detail/'+val, // url that will load data with respect to start and limit params
                                            reader: {
                                                type: 'json',
                                                root: 'data',
                                                totalProperty: 'total',
                                                idProperty: 'JOURNAL_DETAIL_ID'
                                            },
                                            writer: {
                                                type: 'json'
                                            },
                                            api: {
                                                destroy: window.location + 'api/journal/delete'
                                            },
                                            appendId: true
                                        }
                                    }),
                                    plugins:[rowedit],
                                    selType: 'cellmodel',
                                    columns:[{
                                        header: 'COA',
                                        dataIndex: 'COA_CODE',
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

                                                    var value = r[0].data.COA_TITLE;
                                                    sel[0].set('COA_TITLE',value);
                                                }
                                            },
                                            store: Ext.create('Ext.data.Store', {
                                                fields: ['COA_CODE', 'COA_TITLE'],
                                                autoLoad: true,
                                                proxy: {
                                                    type: 'rest',
                                                    url: window.location + 'api/journal/get-combo', // url that will load data with respect to start and limit params
                                                    extraParams: {
                                                        table: 'r_coa',
                                                        name: 'COA_TITLE',
                                                        id: 'COA_CODE'
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
                                                    return '{COA_CODE} - {COA_TITLE}';
                                                }
                                            },
                                            minChars:1,
                                            displayField: 'COA_CODE',
                                            valueField: 'COA_CODE',
                                            name: 'COA_CODE'

                                        }
                                    },{
                                        header: 'Coa Title',
                                        dataIndex: 'COA_TITLE',
                                        width:300,
                                        renderer:function(v){
                                            return '<span style="font-family:courier new">' + v + '</span>';
                                        }
                                    },{
                                        header: 'Curr',
                                        dataIndex: 'CURRENCY_NAME',
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

                                                    var value = r[0].data.CURRENCY_ID;

                                                    sel[0].set('CURRENCY_ID',value);
                                                }
                                            },
                                            store: Ext.create('Ext.data.Store', {
                                                fields: ['CURRENCY_ID', 'CURRENCY_NAME'],
                                                autoLoad: true,
                                                proxy: {
                                                    type: 'rest',
                                                    url: window.location + 'api/common/get-combo', // url that will load data with respect to start and limit params
                                                    extraParams: {
                                                        table: 'r_currency',
                                                        name: 'CURRENCY_ID',
                                                        id: 'CURRENCY_NAME'
                                                    },
                                                    reader: {
                                                        type: 'json',
                                                        root: 'data',
                                                        totalProperty: 'total'
                                                    }
                                                }
                                            }),
                                            displayField: 'CURRENCY_NAME',
                                            valueField: 'CURRENCY_NAME',
                                            name: 'CURRENCY_NAME'
                                        }
                                    },{
                                        header: 'Original Amount',
                                        dataIndex: 'JOURNAL_DETAIL_ORIG',
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
                                        dataIndex: 'JOURNAL_DETAIL_EX_RATE',
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
                                        dataIndex: 'JOURNAL_DETAIL_TYPE',
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
                                                        sel[0].set('DEBET',(sel[0].get('JOURNAL_DETAIL_EX_RATE') * sel[0].get('JOURNAL_DETAIL_ORIG')));
                                                        sel[0].set('KREDIT',0);
                                                    }

                                                    if(value === 'KREDIT'){
                                                        sel[0].set('KREDIT',(sel[0].get('JOURNAL_DETAIL_EX_RATE') * sel[0].get('JOURNAL_DETAIL_ORIG')));
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
                                        dataIndex: 'JOURNAL_DETAIL_DESC',
                                        width:180,
                                        renderer:function(v){
                                            return '<span style="font-family:courier new">' + Ext.util.Format.number(v,'0,000.00') + '</span>';
                                        },
                                        editor: {
                                            type: 'textfield'
                                        }
                                    }]
                                }
                            ]
                        }
                    ],
                    bbar: [
                        '->',

                        {
                            xtype:'button',
                            text:'Save',
                            id:'btn-save-frm-Journal',
                            iconCls:'save',
                            handler:function(c){
                                var frm = form.getForm();
                                var id = frm.getValues();
                                if(id.JOURNAL_ID === ''){
                                    var url = window.location + 'api/journal/add';
                                    var method = 'POST';

                                } else {
                                    var url = window.location + 'api/journal/edit/'+id.JOURNAL_ID;
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
                                        var params = Ext.getCmp('frm-ledger').getForm().getValues();
                                        var loader = Ext.getCmp('panel-ledger').getLoader();
                                        loader.load({
                                            params:params
                                        });
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
                                form.getForm().reset();
                                win.close();
                            }
                        }
                    ]
                });
                win.show();
                var form = win.down('form');

                form.getForm().load({
                    url:window.location + 'api/journal/get/' + val,
                    method:'GET',
                    success:function(c,r){

                    }
                });
            }
                var text = response.responseText;
                loader.getTarget().update(text);
                return true;
            }
        },
        
        editJurnalLink: function(val){
                var rowedit = Ext.create('Ext.grid.plugin.CellEditing', {
                    listeners: {
                        edit:function(editor,e){

                        }
                    }
                });
                var win = new Ext.Window({
                    title:'Journal - Edit',
                    align:'center',
                    constrain:true,
                    modal:true,
                    width:'100%',
                    height:500,
                    y:100,
                    id:'win-edit-journal',
                    buttonAlign:'center',
                    bodyStyle:'background:#ffffff',
                    items:[
                        {
                            xtype:'form',
                            items:[
                                {
                                    xtype:'hidden',
                                    name:'JOURNAL_ID'
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
                                            name:'JOURNAL_DATE',
                                            fieldLabel: 'Date <b style="color:red">*</b>',
                                            value:new Date()
                                        },
                                        {
                                            xtype: 'combo',
                                            disabledCls: 'disabled',
                                            fieldLabel: 'Type <b style="color:red">*</b>',
                                            allowBlank: false,
                                            store: Ext.create('Ext.data.Store', {
                                                fields: ['JOURNAL_TYPE_CODE', 'JOURNAL_TYPE_DESC'],
                                                autoLoad: true,
                                                proxy: {
                                                    type: 'rest',
                                                    url: window.location + 'api/common/get-combo', // url that will load data with respect to start and limit params
                                                    extraParams: {
                                                        table: 'r_journal_type',
                                                        name: 'JOURNAL_TYPE_DESC',
                                                        id: 'JOURNAL_TYPE_CODE'
                                                    },
                                                    reader: {
                                                        type: 'json',
                                                        root: 'data',
                                                        totalProperty: 'total'
                                                    }
                                                }
                                            }),
                                            displayField: 'JOURNAL_TYPE_DESC',
                                            valueField: 'JOURNAL_TYPE_CODE',
                                            name: 'JOURNAL_TYPE_CODE'

                                        },
                                        {
                                            xtype:'textfield',
                                            fieldLabel:'Memo',
                                            flex:1,
                                            name:'JOURNAL_MEMO'
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
                                                    COA_CODE:'',
                                                    DEBET:'',
                                                    KREDIT:'',
                                                    JOURNAL_DETAIL_TYPE:'',
                                                    JOURNAL_DETAIL_DESC:'',
                                                    CURRENCY_NAME:'',
                                                    JOURNAL_DETAIL_ORIG:'',
                                                    JOURNAL_DETAIL_SUM:'',
                                                    JOURNAL_DETAIL_EX_RATE:1
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
                                            url: window.location + 'api/journal/get-detail/'+val, // url that will load data with respect to start and limit params
                                            reader: {
                                                type: 'json',
                                                root: 'data',
                                                totalProperty: 'total',
                                                idProperty: 'JOURNAL_DETAIL_ID'
                                            },
                                            writer: {
                                                type: 'json'
                                            },
                                            api: {
                                                destroy: window.location + 'api/journal/delete'
                                            },
                                            appendId: true
                                        }
                                    }),
                                    plugins:[rowedit],
                                    selType: 'cellmodel',
                                    columns:[{
                                        header: 'COA',
                                        dataIndex: 'COA_CODE',
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

                                                    var value = r[0].data.COA_TITLE;
                                                    sel[0].set('COA_TITLE',value);
                                                }
                                            },
                                            store: Ext.create('Ext.data.Store', {
                                                fields: ['COA_CODE', 'COA_TITLE'],
                                                autoLoad: true,
                                                proxy: {
                                                    type: 'rest',
                                                    url: window.location + 'api/journal/get-combo', // url that will load data with respect to start and limit params
                                                    extraParams: {
                                                        table: 'r_coa',
                                                        name: 'COA_TITLE',
                                                        id: 'COA_CODE'
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
                                                    return '{COA_CODE} - {COA_TITLE}';
                                                }
                                            },
                                            minChars:1,
                                            displayField: 'COA_CODE',
                                            valueField: 'COA_CODE',
                                            name: 'COA_CODE'

                                        }
                                    },{
                                        header: 'Coa Title',
                                        dataIndex: 'COA_TITLE',
                                        width:300,
                                        renderer:function(v){
                                            return '<span style="font-family:courier new">' + v + '</span>';
                                        }
                                    },{
                                        header: 'Curr',
                                        dataIndex: 'CURRENCY_NAME',
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

                                                    var value = r[0].data.CURRENCY_ID;

                                                    sel[0].set('CURRENCY_ID',value);
                                                }
                                            },
                                            store: Ext.create('Ext.data.Store', {
                                                fields: ['CURRENCY_ID', 'CURRENCY_NAME'],
                                                autoLoad: true,
                                                proxy: {
                                                    type: 'rest',
                                                    url: window.location + 'api/common/get-combo', // url that will load data with respect to start and limit params
                                                    extraParams: {
                                                        table: 'r_currency',
                                                        name: 'CURRENCY_ID',
                                                        id: 'CURRENCY_NAME'
                                                    },
                                                    reader: {
                                                        type: 'json',
                                                        root: 'data',
                                                        totalProperty: 'total'
                                                    }
                                                }
                                            }),
                                            displayField: 'CURRENCY_NAME',
                                            valueField: 'CURRENCY_NAME',
                                            name: 'CURRENCY_NAME'
                                        }
                                    },{
                                        header: 'Original Amount',
                                        dataIndex: 'JOURNAL_DETAIL_ORIG',
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
                                        dataIndex: 'JOURNAL_DETAIL_EX_RATE',
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
                                        dataIndex: 'JOURNAL_DETAIL_TYPE',
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
                                                        sel[0].set('DEBET',(sel[0].get('JOURNAL_DETAIL_EX_RATE') * sel[0].get('JOURNAL_DETAIL_ORIG')));
                                                        sel[0].set('KREDIT',0);
                                                    }

                                                    if(value === 'KREDIT'){
                                                        sel[0].set('KREDIT',(sel[0].get('JOURNAL_DETAIL_EX_RATE') * sel[0].get('JOURNAL_DETAIL_ORIG')));
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
                                        dataIndex: 'JOURNAL_DETAIL_DESC',
                                        width:180,
                                        renderer:function(v){
                                            return '<span style="font-family:courier new">' + Ext.util.Format.number(v,'0,000.00') + '</span>';
                                        },
                                        editor: {
                                            type: 'textfield'
                                        }
                                    }]
                                }
                            ]
                        }
                    ],
                    bbar: [
                        '->',

                        {
                            xtype:'button',
                            text:'Save',
                            id:'btn-save-frm-Journal',
                            iconCls:'save',
                            handler:function(c){
                                var frm = form.getForm();
                                var id = frm.getValues();
                                if(id.JOURNAL_ID === ''){
                                    var url = window.location + 'api/journal/add';
                                    var method = 'POST';

                                } else {
                                    var url = window.location + 'api/journal/edit/'+id.JOURNAL_ID;
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
                                        var params = Ext.getCmp('frm-ledger').getForm().getValues();
                                        var loader = Ext.getCmp('panel-ledger').getLoader();
                                        loader.load({
                                            params:params
                                        });
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
                                form.getForm().reset();
                                win.close();
                            }
                        }
                    ]
                });
                win.show();
                var form = win.down('form');

                form.getForm().load({
                    url:window.location + 'api/journal/get/' + val,
                    method:'GET',
                    success:function(c,r){

                    }
                });
            }
    });

});

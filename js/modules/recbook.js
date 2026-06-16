Ext.onReady(function() {
    Ext.tip.QuickTipManager.init();
    
    function displayFormWindow() {
        if (!win.isVisible()) {
//            resetForm();
            DataForm.getForm().reset();
            win.show();
        } else {
            win.hide(this, function() {
            });
            win.toFront();
        }
    }

    function submitOnEnter(field, event) {
        if (event.getKey() == event.ENTER) {
            store.load({
                params: {
                    key: Ext.getCmp('key').getValue(),
                    status: Ext.getCmp('filterStatus').getValue()
                }});
        }
    }

    function generateSavingType() {
        Ext.Ajax.request({
            url: m_crud + '_savingtype',
            method: 'GET',
            params: {id: Ext.getCmp('savingTypeID').getValue()},
            success: function(fp, o) {
                var r = Ext.decode(fp.responseText);
                Ext.getCmp('savingTypeInterestRate').setValue(r.savingTypeInterestRate);
                Ext.getCmp('savingTypeMinAmount').setValue(r.savingTypeMinAmount);
                Ext.getCmp('savingTypeMinTrans').setValue(r.savingTypeMinTrans);
            }
        });
    }

    function generateMemberData() {
        Ext.Ajax.request({
            url: m_crud + '_member',
            method: 'GET',
            params: {id: Ext.getCmp('memberID').getValue()},
            success: function(fp, o) {
                var r = Ext.decode(fp.responseText);
                Ext.getCmp('name').setValue(r.name);
                Ext.getCmp('address').setValue(r.address);
            }
        });
    }
    
    var form_teller = Ext.create('Ext.Container',{
        width: '100%',
        height: '100%',
        renderTo:'ext-content',
        items:[
            {
                xtype:'container',
                layout:{
                    type:'column'
                },
                defaults:{
                    margin:5
                },
                items:[
                    {
                        xtype:'container',
                        columnWidth:.05
                    },
                    {
                        xtype:'container',
                        columnWidth:.4,
                        layout:{
                            type:'fit'
                        },
                        items:[
                            {
                                xtype:'button',
                                flex:true,
                                ui: 's-button',
                                cls: 's-green',
                                padding:10,
                                disabled:true,
                                pressed:true,
                                scale:'large',
                                text:'CASH-IN',
                                id:'btn-toggle-add-deposit',
                                enableToggle:true,
                                toggleHandler: function(button,state){
                                    if(state === true){
                                        button.removeCls('s-green');
                                        button.addCls('s-grey');
                                        button.disable();
                                        Ext.getCmp('btn-toggle-add-withdrawal').addCls('s-red');
                                        Ext.getCmp('btn-toggle-add-withdrawal').enable();
                                        Ext.getCmp('frm-add-deposit').getForm().reset();
                                        Ext.getCmp('btn-toggle-add-withdrawal').toggle();
                                        Ext.getCmp('pnl-card-coop-transaction').getLayout().setActiveItem(0);
                                    }
                                }
                            }
                        ]
                    },
                    {
                        xtype:'container',
                        columnWidth:.1
                    },
                    {
                        xtype:'panel',
                        columnWidth:.4,
                        layout:{
                            type:'fit'
                        },
                        items:[
                            {
                                xtype:'button',
                                scale:'large',
                                text:'CASH-OUT',
                                ui: 's-button',
                                cls: 's-red',
                                padding:10,
                                id:'btn-toggle-add-withdrawal',
                                enableToggle:true,
                                toggleHandler: function(button,state){
                                    if(state === true){
                                        button.removeCls('s-red');
                                        button.addCls('s-grey');
                                        button.disable();
                                        Ext.getCmp('btn-toggle-add-deposit').addCls('s-green');
                                        Ext.getCmp('btn-toggle-add-deposit').enable();
                                        Ext.getCmp('frm-add-withdrawal').getForm().reset();
                                        Ext.getCmp('btn-toggle-add-deposit').toggle();
                                        Ext.getCmp('pnl-card-coop-transaction').getLayout().setActiveItem(1);
                                    }
                                }
                            }
                        ]
                    },
                    {
                        xtype:'panel',
                        columnWidth:.05,
                        layout:{
                            type:'fit'
                        },
                        items:[
                            {
                                xtype:'button',
                                scale:'large',
                                hidden:true,
                                text:'AUTHORIZED',
                                ui: 's-button',
                                cls: 's-red',
                                padding:10,
                                id:'btn-authorized-add-withdrawal',
                                handler: function(button,state){
                                    
                                    var win = Ext.create('Ext.Window',{
                                        title:'Transaction List',
                                        width:1000,
                                        height:500,
                                        modal:true,
                                        layout:{
                                            type:'fit'
                                        },
                                        items:[
                                            {
                                                xtype:'grid',
                                                store:Ext.create('Ext.data.Store', {
                                                    storeId:'store-grid-authorized-trans-list',
                                                    fields: ['id', 'memberTransactionNumber', 'memberTransactionType', 'memberTransactionDate', 'cashSourceName', 'memberSavingNo', 'memberTransactionAmount', 'memberTransactionRemark', 'debet', 'credit'],
                                                    autoLoad: true,
                                                    pageSize: 10,
                                                    proxy: {
                                                        type: 'ajax',
                                                        url: 'api/transaction/coop_transactions',
                                                        reader: {
                                                            type: 'json',
                                                            root: 'data'
                                                        }
                                                    }
                                                }),
                                                dockedItems: [{
                                                    xtype: 'pagingtoolbar',
                                                    store: Ext.data.StoreManager.lookup('store-grid-authorized-trans-list'),
                                                    dock: 'bottom',
                                                    displayInfo: true
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
                                                        text: lang('Transaction Number'),
                                                        width: '15%',
                                                        dataIndex: 'memberTransactionNumber'
                                                    },
                                                    {
                                                        text: lang('Name'),
                                                        flex:true,
                                                        dataIndex: 'memberSavingNo'
                                                    },
                                                    {
                                                        text: lang('Trans Type'),
                                                        width: 100,
                                                        dataIndex: 'memberTransactionType'
                                                    },
                                                    {
                                                        text: lang('Date [d/m/Y]'),
                                                        width: 100,
                                                        xtype:'datecolumn',
                                                        format:'d/m/Y',
                                                        dataIndex: 'memberTransactionDate'
                                                    },
                                                    {
                                                        text: lang('Debet'),
                                                        width: 200,
                                                        align:'right',
                                                        style:'font-family:courier new',
                                                        xtype: 'numbercolumn',
                                                        format:'0,000.00',
                                                        dataIndex: 'debet'
                                                    },
                                                    {
                                                        text: lang('Kredit'),
                                                        width: 200,
                                                        style:'font-family:courier new',
                                                        align:'right',
                                                        xtype: 'numbercolumn',
                                                        format:'0,000.00',
                                                        dataIndex: 'credit'
                                                    },
                                                    {
                                                        xtype:'actioncolumn',
                                                        width:40,
                                                        items: [{
                                                            icon: varjs.config.base_url + 'images/icons/silk/check_error.png',  // Use a URL in the icon config
                                                            tooltip: 'Cancel this transaction',
                                                            handler: function(grid, rowIndex, colIndex) {
                                                                var rec = grid.getStore().getAt(rowIndex);
                                                                
                                                                Ext.MessageBox.confirm('Cancel', 'Apakah anda mau membatalkan transaksi ini ?', function(btn) {
                                                                    if (btn == 'yes') {
                                                                        
                                                                        Ext.Ajax.request({
                                                                            waitMsg: 'Please Wait',
                                                                            url: 'api/transaction/cancel_trans',
                                                                            method: 'POST',
                                                                            params: {id: rec.get('id')},
                                                                            success: function(response, opts) {
                                                                                
                                                                            },
                                                                            failure: function(response, opts) {
                                                                                
                                                                            }
                                                                        });
                                                                    }
                                                                });
                                                            }
                                                        }]
                                                    }
                                                ]
                                            }
                                        ]
                                    }).show();
                                }
                            }
                        ]
                    }
                ]
            },
            {
                xtype:'container',
                id:'pnl-card-coop-transaction',
                layout:{
                    type:'card'
                },
                items:[
                    {
                        xtype:'panel',
                        frame:true,
                        hidden:true,
                        id:'pnl-add-deposit',
                        style:'border:6px solid #799143',
                        bodyStyle:'background:#799143;',
                        header:{
                            style:'background:#799143;border-color:#799143;text-align:center; font-size:25px'
                        },
                        title:'C A S H - I N',
                        items:[
                            Ext.create('Ext.form.Panel', {
                                bodyPadding: 5,
                                id: 'frm-add-deposit',
                                fieldDefaults: {
                                    labelAlign: 'left',
                                    labelWidth: 120
                                },
                                layout: {
                                    type:'column'
                                },
                                getInvalidFields: function() {
                                    var invalidFields = [];
                                    Ext.suspendLayouts();
                                    this.form.getFields().filterBy(function(field) {
                                        if (field.validate()) return;
                                        invalidFields.push(field);
                                    });
                                    Ext.resumeLayouts(true);
                                    return invalidFields;
                                },
                                items: [
                                    {
                                        xtype:'panel',
                                        columnWidth: .4,
                                        layout:{
                                            type:'fit'
                                        },
                                        height:350,
                                        items:[
                                            {
                                                xtype:'fieldset',
                                                title:'Cash-In Info',
                                                style:'padding-bottom:2px',
                                                items:[
                                                    {
                                                        xtype:'textfield',
                                                        id:'member-name-add-trans',
                                                        fieldLabel:'From <b style="color:red">*</b>',
                                                        allowBlank:false,
                                                        width:400,
                                                        name:'name'
                                                    },
                                                    {
                                                        xtype:'container',
                                                        layout:{
                                                            type:'table',
                                                            columns:2
                                                        },
                                                        items:[{
                                                            xtype: 'combo',
                                                            id:'cmb-add-source-fund',
                                                            fieldLabel: 'Source of fund <b style="color:red">*</b>',
                                                            allowBlank: false,
                                                            width:350,
                                                            store: Ext.create('Ext.data.Store', {
                                                                fields: ['id', 'label'],
                                                                autoLoad: true,
                                                                proxy: {
                                                                    type: 'rest',
                                                                    url: m_api + 'transaction/combo_cashsource', // url that will load data with respect to start and limit params
                                                                    reader: {
                                                                        type: 'json',
                                                                        root: 'data',
                                                                        totalProperty: 'total'
                                                                    }
                                                                }
                                                            }),
                                                            displayField: 'label',
                                                            valueField: 'id',
                                                            name: 'source'
                                                        },{
                                                            xtype:'checkbox',
                                                            boxLabel:'Cash',
                                                            name:'source',
                                                            inputValue:'cash',
                                                            fieldStyle:'margin-left:5px;',
                                                            listeners:{
                                                                change:function(c,v){
                                                                    if(v === true){
                                                                        Ext.getCmp('cmb-add-source-fund').disable();
                                                                    } else {
                                                                        Ext.getCmp('cmb-add-source-fund').enable();
                                                                    }
                                                                }
                                                            }
                                                        }]
                                                    },
                                                    {
                                                        xtype: 'combo',
                                                        id:'cmb-add-to-fund',
                                                        fieldLabel: 'To Account <b style="color:red">*</b>',
                                                        allowBlank: false,
                                                        width:350,
                                                        store: Ext.create('Ext.data.Store', {
                                                            fields: ['id', 'label'],
                                                            autoLoad: true,
                                                            proxy: {
                                                                type: 'rest',
                                                                url: m_api + 'transaction/combo_cashsource', // url that will load data with respect to start and limit params
                                                                reader: {
                                                                    type: 'json',
                                                                    root: 'data',
                                                                    totalProperty: 'total'
                                                                }
                                                            }
                                                        }),
                                                        displayField: 'label',
                                                        valueField: 'id',
                                                        name: 'to'
                                                    },
                                                    {
                                                        xtype: 'numericfield',
                                                        width:300,
                                                        id:'txt-deposit-amount',
                                                        hideTrigger:true,
                                                        name:'amount',
                                                        fieldStyle:'text-align:right;font-family:Courier New;',
                                                        fieldLabel:'AMOUNT<b style="color:red"> *</b>'
                                                    },
                                                    {
                                                        xtype: 'textarea',
                                                        fieldLabel: 'Remark',
                                                        width: 550,
                                                        height:75,
                                                        name: 'remark',
                                                        allowBlank: true
                                                    }
                                                ]
                                            }
                                        ],
                                        dockedItems:[
                                            {
                                                xtype:'toolbar',
                                                dock:'bottom',
                                                items: ['->',{
                                                    id: 'saveButtonDeposit',
                                                    text: 'Save Transaction',
                                                    margin: '5px',
                                                    padding:'10px',
                                                    scale: 'medium',
                                                    ui: 's-button',
                                                    cls: 's-blue',
                                                    handler: function() {
                                                        var form = Ext.getCmp('frm-add-deposit').getForm();
                                                        if(form.isValid()){
                                                            form.submit({
                                                                url: m_api + '/transaction/add_deposit_recbook',
                                                                method:'POST',
                                                                waitMsg: 'Sending data...',
                                                                success: function(fp, o) {
                                                                    Ext.MessageBox.alert('Success', 'Data saved.');
                                                                    form.reset();
                                                                    Ext.getCmp('grid-add-deposit-saving-trans').store.load();
                                                                }
                                                            });
                                                        } else {
                                                            var fieldNames = [];                
                                                            var fields = Ext.getCmp('frm-add-deposit').getInvalidFields();
                                                            for(var i=0; i <  fields.length; i++){
                                                                var field = fields[i];
                                                                fieldNames.push(field.getName());
                                                            }
                                                            Ext.MessageBox.alert('Invalid Fields', 'The following fields are invalid: ' + fieldNames.join(', '));
                                                        }
                                                    }
                                                }, {
                                                    text: 'Cancel',
                                                    margin: '5px',
                                                    padding:'10px 15px',
                                                    scale: 'medium',
                                                    ui: 's-button',
                                                    cls: 's-grey',
                                                    disabled: false,
                                                    handler: function() {
                                                        var form = Ext.getCmp('frm-add-deposit').getForm();
                                                        form.reset();
                                                    }
                                                }]
                                            }
                                        ]
                                    },
                                    Ext.create('Ext.grid.Panel', {
                                        id:'grid-add-deposit-saving-trans',
                                        columnWidth:.6,
                                        style:'margin-right: 10px;margin-left: 10px; margin-top:10px;border-top: 1px solid #999;border-bottom: 0px solid #999;',
                                        store: Ext.create('Ext.data.Store', {
                                            fields:['number', 'name', 'account', 'amount'],
                                            autoLoad:true,
                                            proxy: {
                                                type: 'ajax',
                                                url: 'api/transaction/todayrec/' + '1',
                                                reader: {
                                                    type: 'json',
                                                    root: 'data'
                                                }
                                            }
                                        }),
                                        dockedItems:[
                                            {
                                                xtype:'toolbar',
                                                plain:true,
                                                items:[
                                                    '<span><b>Today\'s Deposit</b></span>'
                                                ]
                                            }
                                        ],
                                        columns: [
                                            { text: 'Name',  dataIndex: 'name', flex:true },
                                            { text: 'Account',  dataIndex: 'account', width:200 },
                                            { text: 'Amount', align:'right', dataIndex: 'amount', width: 150, renderer: function(v){
                                                return '<span style="font-family:courier new">' + Ext.util.Format.number(v) + '</span>';
                                            } }
                                        ],
                                        height: 350
                                    })
                                ]
                            })
                        ]
                    },
                    {
                        xtype:'panel',
                        frame:true,
                        hidden:true,
                        id:'pnl-add-withdrawal',
                        style:'border:5px solid #799143',
                        bodyStyle:'background:#799143;',
                        header:{
                            style:'background:#799143;border-color:#799143;text-align:center; font-size:25px'
                        },
                        title:'C A S H - O U T',
                        items: Ext.create('Ext.form.Panel', {
                            bodyPadding: 5,
                            id: 'frm-add-withdrawal',
                            fieldDefaults: {
                                labelAlign: 'left',
                                labelWidth: 120
                            },
                            layout: {
                                type:'column'
                            },
                            items: [
                                {
                                    xtype:'panel',
                                    columnWidth: .4,
                                    height:350,
                                    layout:{
                                        type:'fit'
                                    },
                                    items:[
                                        {
                                            xtype:'fieldset',
                                            title:'Cash-Out Info',
                                            style:'padding-bottom:2px',
                                            items:[
                                                {
                                                    xtype:'textfield',
                                                    id:'member-name-add-with',
                                                    fieldLabel:'Name <b style="color:red">*</b>',
                                                    allowBlank:false,
                                                    width:400,
                                                    name:'name'
                                                },
                                                {
                                                    xtype: 'combo',
                                                    id:'cmb-add-source-fund-with',
                                                    fieldLabel: 'Source of Fund <b style="color:red">*</b>',
                                                    allowBlank: false,
                                                    width:350,
                                                    store: Ext.create('Ext.data.Store', {
                                                        fields: ['id', 'label'],
                                                        autoLoad: true,
                                                        proxy: {
                                                            type: 'rest',
                                                            url: m_api + '/transaction/combo_cashsource', // url that will load data with respect to start and limit params
                                                            reader: {
                                                                type: 'json',
                                                                root: 'data',
                                                                totalProperty: 'total'
                                                            }
                                                        }
                                                    }),
                                                    displayField: 'label',
                                                    valueField: 'id',
                                                    name: 'source'
                                                },
                                                {
                                                    xtype:'container',
                                                    layout:{
                                                        type:'table',
                                                        columns:2
                                                    },
                                                    items:[{
                                                        xtype: 'combo',
                                                        id:'cmb-add-to-with',
                                                        fieldLabel: 'To Account <b style="color:red">*</b>',
                                                        allowBlank: false,
                                                        width:350,
                                                        store: Ext.create('Ext.data.Store', {
                                                            fields: ['id', 'label'],
                                                            autoLoad: true,
                                                            proxy: {
                                                                type: 'rest',
                                                                url: m_api + '/transaction/combo_cashsource', // url that will load data with respect to start and limit params
                                                                reader: {
                                                                    type: 'json',
                                                                    root: 'data',
                                                                    totalProperty: 'total'
                                                                }
                                                            }
                                                        }),
                                                        displayField: 'label',
                                                        valueField: 'id',
                                                        name: 'to'
                                                    },{
                                                        xtype:'checkbox',
                                                        boxLabel:'Cash',
                                                        name: 'to',
                                                        fieldStyle:'margin-left:5px;',
                                                        listeners:{
                                                            change:function(c,v){
                                                                if(v === true){
                                                                    Ext.getCmp('cmb-add-to-with').disable();
                                                                } else {
                                                                    Ext.getCmp('cmb-add-to-with').enable();
                                                                }
                                                            }
                                                        }
                                                    }]
                                                },
                                                {
                                                    xtype: 'numericfield',
                                                    width:300,
                                                    id:'txt-with-amount',
                                                    hideTrigger:true,
                                                    name:'amount',
                                                    fieldStyle:'text-align:right;font-family:Courier New;',
                                                    fieldLabel:'AMOUNT<b style="color:red"> *</b>'
                                                },
                                                {
                                                    xtype: 'textarea',
                                                    fieldLabel: 'Remark',
                                                    width: 550,
                                                    height:75,
                                                    name: 'remark',
                                                    allowBlank: true
                                                }
                                            ]
                                        }
                                    ],
                                    dockedItems:[
                                        {
                                            xtype:'toolbar',
                                            dock:'bottom',
                                            items: ['->',{
                                                id: 'saveButtonWithdraw',
                                                text: 'Save Transaction',
                                                margin: '5px',
                                                padding:'10px',
                                                scale: 'medium',
                                                ui: 's-button',
                                                cls: 's-red',
                                                handler: function() {
                                                    var form = Ext.getCmp('frm-add-withdrawal').getForm();
                                                    if(form.isValid()){
                                                        form.submit({
                                                            url: m_api + '/transaction/add_withdrawal_recbook',
                                                            method:'POST',
                                                            waitMsg: 'Sending data...',
                                                            success: function(fp, o) {
                                                                Ext.MessageBox.alert('Success', 'Data saved.');
                                                                form.reset();
                                                                Ext.getCmp('grid-add-with-saving-trans').store.load();
                                                            }
                                                        });
                                                    } else {
                                                        var fieldNames = [];                
                                                        var fields = Ext.getCmp('frm-add-withdrawal').getInvalidFields();
                                                        for(var i=0; i <  fields.length; i++){
                                                            var field = fields[i];
                                                            fieldNames.push(field.getName());
                                                         }
                                                       
                                                        Ext.MessageBox.alert('Invalid Fields', 'The following fields are invalid: ' + fieldNames.join(', '));
                                                    }
                                                }
                                            }, {
                                                text: 'Cancel',
                                                margin: '5px',
                                                padding:'10px 15px',
                                                scale: 'medium',
                                                ui: 's-button',
                                                cls: 's-grey',
                                                disabled: false,
                                                handler: function() {
                                                    var form = Ext.getCmp('frm-add-withdrawal').getForm();
                                                    form.reset();
                                                }
                                            }]
                                        }
                                    ]
                                },
                                Ext.create('Ext.grid.Panel', {
                                    id:'grid-add-with-saving-trans',
                                    columnWidth:.6,
                                    style:'margin-right: 10px;margin-left: 10px; margin-top:10px;border-top: 1px solid #999;border-bottom: 0px solid #999;',
                                    store: Ext.create('Ext.data.Store', {
                                        fields:['name', 'account', 'amount'],
                                        autoLoad:true,
                                        proxy: {
                                            type: 'ajax',
                                            url: 'api/transaction/todayrec/' + '2',
                                            reader: {
                                                type: 'json',
                                                root: 'data'
                                            }
                                        }
                                    }),
                                    dockedItems:[
                                        {
                                            xtype:'toolbar',
                                            plain:true,
                                            items:[
                                                '<span><b>Today\'s Withdrawal</b></span>'
                                            ]
                                        }
                                    ],
                                    columns: [
                                        { text: 'Name',  dataIndex: 'name', flex:true },
                                        { text: 'Account',  dataIndex: 'account', width:200 },
                                        { text: 'Amount', align:'right', dataIndex: 'amount', width: 150, renderer: function(v){
                                            return '<span style="font-family:courier new">' + Ext.util.Format.number(v) + '</span>';
                                        } }
                                    ],
                                    height: 350
                                })
                            ]
                        })
                    }
                ]
            }
        ]
    });
    
    
});

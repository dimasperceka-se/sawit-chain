Ext.onReady(function() {
    Ext.tip.QuickTipManager.init();

var storeCoaList = Ext.create('Ext.data.Store', {
    extend: 'Ext.data.Model',
    fields: ['id', 'code', 'title'],
//    autoLoad: true,
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
    
Ext.define('fromCoaRecbookList', {
    itemId: 'fromCoaRecbookList',
    id: 'fromCoaRecbookList',
    extend: 'Ext.grid.Panel',
    alias: 'widget.fromCoaRecbookList',
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
                    // Ext.getCmp('coaIDAsset').setValue(selectedRecord.data.id);
                    Ext.getCmp('fromCoaRecbook').setValue(selectedRecord.data.code+' '+selectedRecord.data.title);
                    Ext.getCmp('wfromCoaRecbookPopup').hide();
            }
        },
        { text: 'id', dataIndex: 'id', hidden: true },
        { text: 'COA Code', flex:1, width: '25%', dataIndex: 'code' },
        { text: 'COA Name', width: '75%', dataIndex: 'title' }        
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

    var wfromCoaRecbookPopup = Ext.create('widget.window', {
        id: 'wfromCoaRecbookPopup',
        title: 'Choose Chart of Account',
        modal:true,
        closable: true,
        closeAction: 'hide',
    //    autoWidth: true,
         width: 770,
        height: 330,
        layout: 'fit',
        border: false,
        items: [{
                xtype:'fromCoaRecbookList'
        }]
});
    //////////////////////////////////
Ext.define('toCoaRecbookList', {
    itemId: 'toCoaRecbookList',
    id: 'toCoaRecbookList',
    extend: 'Ext.grid.Panel',
    alias: 'widget.toCoaRecbookList',
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
                    // Ext.getCmp('coaIDAsset').setValue(selectedRecord.data.id);
                    Ext.getCmp('toCoaRecbook').setValue(selectedRecord.data.code+' '+selectedRecord.data.title);
                    Ext.getCmp('wtoCoaRecbookPopup').hide();
            }
        },
        { text: 'id', dataIndex: 'id', hidden: true },
        { text: 'COA Code', flex:1, width: '25%', dataIndex: 'code' },
        { text: 'COA Name', width: '75%', dataIndex: 'title' }        
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

    var wtoCoaRecbookPopup = Ext.create('widget.window', {
        id: 'wtoCoaRecbookPopup',
        title: 'Choose Chart of Account',
        closable: true,
        closeAction: 'hide',
        modal:true,
         width: 770,
        height: 330,
        layout: 'fit',
        border: false,
        items: [{
                xtype:'toCoaRecbookList'
        }]
});

    ///////////////////

var storeAssetList = Ext.create('Ext.data.Store', {
    extend: 'Ext.data.Model',
    fields: ['InventoryID', 'Number', 'Name'],
//    autoLoad: true,
    pageSize: 50,
    proxy: {
        type: 'ajax',
        url: m_assetdatas,
        reader: {
            type: 'json',
            root: 'data',
            totalProperty: 'total'
        }
    }
});
    

Ext.define('AssetList', {
    itemId: 'AssetList',
    id: 'AssetList',
    extend: 'Ext.grid.Panel',
    alias: 'widget.AssetList',
    store: storeAssetList,
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
                    // Ext.getCmp('coaIDAsset').setValue(selectedRecord.data.id);
                    Ext.getCmp('NameAsset').setValue(selectedRecord.data.Number+' '+selectedRecord.data.Name);
                    Ext.getCmp('wAssetListPopup').hide();
            }
        },
        { text: 'id', dataIndex: 'InventoryID', hidden: true },
        { text: 'Code', flex:1, width: '25%', dataIndex: 'Number' },
        { text: 'Name', width: '75%', dataIndex: 'Name' }        
    ]
    , dockedItems: [{
            xtype: 'pagingtoolbar',
            store: storeAssetList, // same store GridPanel is using
            dock: 'bottom',
            displayInfo: true
                    // pageSize:20
        }
    ]
});

    var wAssetListPopup = Ext.create('widget.window', {
        id: 'wAssetListPopup',
        title: 'Choose Asset',
        closable: true,
        closeAction: 'hide',
        modal:true,
         width: 770,
        height: 330,
        layout: 'fit',
        border: false,
        items: [{
                xtype:'AssetList'
        }]
});

    
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
                        title:'RECORD BOOKING',
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
                                        height:450,
                                        items:[
                                            {
                                                xtype:'fieldset',
                                                title:'Info',
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
                                                        xtype: 'radiogroup',
                                                        fieldLabel: 'Type Transaction <b style="color:red">*</b>',
                                                        items: [
                                                            {boxLabel: 'Cash', name: 'rb-auto', inputValue: 1},
                                                            {boxLabel: 'Non Cash', name: 'rb-auto', inputValue: 2}
                                                        ]
                                                    },
                                                    {
                                                        xtype: 'textfield',
                                                        fieldLabel: 'From COA <b style="color:red">*</b>',
                                                        name: 'fromCoaRecbook',
                                                        id: 'fromCoaRecbook',
                                                        listeners: {
                                                            render: function(component) {
                                                                component.getEl().on('click', function(event, el) {
                                                                    wfromCoaRecbookPopup.show();
                                                                    storeCoaList.load({
                                                                        params: {
                                                                            type: 'class',
                                                                            id:1
                                                                        }
                                                                    });
                                                                });
                                                            }
                                                        }
                                                    },
                                                    {
                                                        xtype: 'radiogroup',
                                                        fieldLabel: 'Type Payment <b style="color:red">*</b>',
                                                        items: [
                                                            {boxLabel: 'Transfer', name: 'rb-auto', inputValue: 1},
                                                            {boxLabel: 'Cheque', name: 'rb-auto', inputValue: 2}
                                                        ]
                                                    },
                                                    {
                                                        xtype: 'textfield',
                                                        fieldLabel: 'To COA <b style="color:red">*</b>',
                                                        name: 'toCoaRecbook',
                                                        id: 'toCoaRecbook',
                                                        listeners: {
                                                            render: function(component) {
                                                                component.getEl().on('click', function(event, el) {
                                                                    wtoCoaRecbookPopup.show();
                                                                    storeCoaList.load({
                                                                        params: {
                                                                            type: 'class',
                                                                            id:1
                                                                        }
                                                                    });
                                                                });
                                                            }
                                                        }
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
                                                        width: 400,
                                                        height:75,
                                                        name: 'remark',
                                                        allowBlank: true
                                                    },
                                                    {
                                                        xtype: 'checkbox',
                                                        fieldLabel: 'Buy Asset',
                                                        name: 'cbBuyAsset',
                                                        listeners:{
                                                            change: function(c,v){
                                                                if(v === true){
                                                                    Ext.getCmp('NameAsset').setDisabled(false);
                                                                } else {
                                                                    Ext.getCmp('NameAsset').setDisabled(true);
                                                                }
                                                            }
                                                        }
                                                    },
                                                    {
                                                        xtype: 'textfield',
                                                        fieldLabel: 'Asset Name',
                                                        disabled:true,
                                                        name: 'NameAsset',
                                                        id: 'NameAsset',
                                                        listeners: {
                                                            render: function(component) {
                                                                component.getEl().on('click', function(event, el) {
                                                                    wAssetListPopup.show();
                                                                    storeAssetList.load();
                                                                });
                                                            }
                                                        }
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
                                                    '<span><b>Today\'s Transaction</b></span>'
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
                    }
                ]
            }
        ]
    });
    
    
});

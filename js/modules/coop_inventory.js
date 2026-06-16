Ext.onReady(function () {

    Ext.tip.QuickTipManager.init();

    //START AKUN PERKIRAAN LIST
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

Ext.define('GridCoaAssetList', {
    itemId: 'GridSavingTypeList',
    id: 'GridCoaAssetList',
    extend: 'Ext.grid.Panel',
    alias: 'widget.GridCoaAssetList',
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
                    Ext.getCmp('coaIDAsset').setValue(selectedRecord.data.id);
                    Ext.getCmp('coaNameAsset').setValue(selectedRecord.data.title);
                    Ext.getCmp('wCoaAssetPopup').hide();
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

    var wCoaAssetPopup = Ext.create('widget.window', {
        id: 'wCoaAssetPopup',
        title: 'Choose Chart of Account Asset',
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
                xtype:'GridCoaAssetList'
        }]
    });

    ////////////////////////////////////////////
    Ext.define('GridCoaAkumDeprecList', {
    itemId: 'GridCoaAkumDeprecList',
    id: 'GridCoaAkumDeprecList',
    extend: 'Ext.grid.Panel',
    alias: 'widget.GridCoaAkumDeprecList',
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
                    Ext.getCmp('coaIDAkumDepres').setValue(selectedRecord.data.id);
                    Ext.getCmp('coaNameAkumDepres').setValue(selectedRecord.data.title);
                    Ext.getCmp('wCoaAkumDeprecPopup').hide();
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

    var wCoaAkumDeprecPopup = Ext.create('widget.window', {
        id: 'wCoaAkumDeprecPopup',
        title: 'Choose Accumulated Depreciation Chart of Account',
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
                xtype:'GridCoaAkumDeprecList'
        }]
    });

    //////////////////////////////////////////////////////////

    Ext.define('GridCoaBebanDeprecList', {
    itemId: 'GridCoaBebanDeprecList',
    id: 'GridCoaBebanDeprecList',
    extend: 'Ext.grid.Panel',
    alias: 'widget.GridCoaBebanDeprecList',
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
                    Ext.getCmp('coaIDBebanDepres').setValue(selectedRecord.data.id);
                    Ext.getCmp('coaNameBebanDepres').setValue(selectedRecord.data.title);
                    Ext.getCmp('wCoaBebanDeprecPopup').hide();
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

    var wCoaBebanDeprecPopup = Ext.create('widget.window', {
        id: 'wCoaBebanDeprecPopup',
        title: 'Choose Expense Depreciation Chart of Account',
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
                xtype:'GridCoaBebanDeprecList'
        }]
    });

    ///AKUN PERKIRAAN LIST END

    Ext.define('fotoinvthumb', {
        extend: 'Ext.Component',
        alias: 'widget.fotoinvthumb',
        fieldLabel: 'Foto',
        autoEl: {
            tag: 'img',
            width: 80,
            style:'margin-left:90px;',
            height: 50
        }
    });


    var storeInterestDuration = new Ext.data.ArrayStore({
        fields: ['loanTypeInterestDuration', 'loanTypeInterestDurationName'],
        data: [[1, 'Per Month'], [2, 'Per Year'], [3, 'One Time']]
    });

    Ext.define('comboxInterestDuration', {
        extend: 'Ext.form.ComboBox',
        alias: 'widget.comboxInterestDuration',
//    fieldLabel: 'Status Perencanaan',
        editable: false,
        triggerAction: 'all',
        displayField: 'loanTypeInterestDurationName',
        valueField: 'loanTypeInterestDuration',
        name: 'loanTypeInterestDuration',
        store: storeInterestDuration
    });

    var storeCatInv = new Ext.data.ArrayStore({
        fields: ['catinventoryid', 'catinventoryname'],
        data: [[1, 'Cat 1'], [2, 'Cat 2'], [3, 'Cat 3']]
    });

    Ext.define('comboxCatInv', {
        extend: 'Ext.form.ComboBox',
        alias: 'widget.comboxCatInv',
        fieldLabel: 'Category',
//        editable: false,
        triggerAction: 'all',
        displayField: 'catinventoryname',
        valueField: 'catinventoryid',
        name: 'idinventorycat',
        store: storeCatInv
    });

    var storeTax = new Ext.data.ArrayStore({
        fields: ['taxid', 'taxname'],
        data: [[1, 'Non PPN'], [2, 'PPN']]
    });

    Ext.define('comboxTax', {
        extend: 'Ext.form.ComboBox',
        alias: 'widget.comboxTax',
        fieldLabel: 'Tax',
//        editable: false,
        triggerAction: 'all',
        displayField: 'taxname',
        valueField: 'taxid',
        name: 'taxid',
        store: storeTax
    });

    Ext.define('comboxEvaluateType', {
        extend: 'Ext.form.ComboBox',
        alias: 'widget.comboxEvaluateType',
        fieldLabel: 'Status',
//        editable: false,
        triggerAction: 'all',
        displayField: 'StatusName',
        valueField: 'StatusName',
        name: 'StatusName',
        store: new Ext.data.ArrayStore({
            fields: ['StatusName'],
            data: [['Active'],['Sold'], ['Destroyed'], ['Not Used']]
        })
    });

//    var storeSupplier = new Ext.data.ArrayStore({
//        fields: ['supplierid', 'suppliername'],
//        data: [[1, 'Sup 1'], [2, 'Sup 2']]
//    });
//
//    Ext.define('comboxSupplier', {
//        extend: 'Ext.form.ComboBox',
//        alias: 'widget.comboxSupplier',
//        fieldLabel: 'Supplier',
////        editable: false,
//        triggerAction: 'all',
//        displayField: 'suppliername',
//        valueField: 'supplierid',
//        name: 'supplierid',
//        store: storeSupplier
//    });

     var storeInvCatList = Ext.create('Ext.data.ArrayStore', {
        fields: [{
                name: 'id',
                type: 'int'
            }, {
                name: 'namecat',
                type: 'string'
            }],
        proxy: {
            type: 'ajax',
            url: m_invcatlist,
            reader: {
                type: 'json',
                root: 'data'
            }
        },
        autoLoad: true
    });

    Ext.define('comboxInvCategory', {
        extend: 'Ext.form.ComboBox',
        alias: 'widget.comboxInvCategory',
        fieldLabel: 'Category',
//        editable: false,
        triggerAction: 'all',
        displayField: 'namecat',
        valueField: 'id',
        name: 'idinventorycat',
        store: storeInvCatList
    });

    var storeSupplierList = Ext.create('Ext.data.ArrayStore', {
        fields: [{
                name: 'id',
                type: 'int'
            }, {
                name: 'namesupplier',
                type: 'string'
            }],
        proxy: {
            type: 'ajax',
            url: m_supplierlist,
            reader: {
                type: 'json',
                root: 'data'
            }
        },
        autoLoad: true
    });

    Ext.define('comboxSupplier', {
        extend: 'Ext.form.ComboBox',
        alias: 'widget.comboxSupplier',
        fieldLabel: 'Supplier',
//        editable: false,
        triggerAction: 'all',
        displayField: 'namesupplier',
        valueField: 'id',
        name: 'idsupplier',
        store: storeSupplierList
    });


    Ext.define('FormInventory', {
        extend: 'Ext.form.Panel',
        id: 'FormInventory',
        title:'Inventory Data',
        alias: 'widget.FormInventory',
        initComponent: function () {
            var frm = this;
            frm.bodyStyle = 'padding:5px';
            // frm.width = 1000;
            frm.autoWidth = true,
            frm.autoScroll = true;
            // frm.height = 600;
            frm.autohight = true,
            frm.fieldDefaults = {
                msgTarget: 'side',
                blankText: 'Tidak Boleh Kosong',
                labelWidth: 180,
                width: 460
            };
            frm.items = [
                {
                    layout: 'column',
                    defaults: {
                        padding: '5 5 5 5',
                        width: '50%',
                    },
                    items: [
                        {
                            items: [
                                {
                                    xtype: 'hiddenfield',
                                    name: 'InventoryID',
                                    id: 'idinventoryInv'
                                },
                                // {
                                //     xtype:'hiddenfield',
                                //     id:'IsRemoved',
                                //     name:'IsRemoved'
                                // },
                                {
                                    xtype: 'fieldset',
//                                    bodyStyle: 'margin: 10px;',
                                    title: 'Profil',
                                    // collapsible: true,
                                    items: [
                                        {
                                            xtype: 'fotoinvthumb',
                                            id: 'fotoinvthumb',
                                            fieldLabel: 'Foto Inventory',
                                            anchor: '70%',
                                            width: 80,
                                            height: 150,
                                        }, {
                                            xtype: 'filefield',
                                            emptyText: 'Upload Foto',
                                            fieldLabel: 'Photo',
                                            name: 'Images',
                                            buttonText: '',
                                            buttonConfig: {
                                                iconCls: 'imgupload-icon'
                                            }
                                        }, {
                                            xtype: 'textfield',
                                            fieldLabel: 'No Barang/SKU',
                                            allowBlank: false,
                                            id:'NumberInv',
                                            name: 'Number'
                                        },
                                        {
                                            xtype: 'textfield',
                                            fieldLabel: 'Serial Number',
                                            allowBlank: false,
                                            id: 'SerialNumber',
                                            name: 'SerialNumber'
                                        },  {
                                            xtype: 'textfield',
                                            fieldLabel: 'Nama Barang',
                                            allowBlank: false,
                                            id:'NameInv',
                                            name: 'Name'
                                        },
                                         {
                                            xtype: 'textfield',
                                            fieldLabel: 'Lokasi',
                                            hidden:true,
                                            allowBlank: false,
                                            id: 'LocationInv',
                                            name: 'Location'
                                        },
                                        {
                                          xtype:'comboxInvCategory',
                                          id:'comboxCatInv',
                                          name:'CategoryID',
                                          allowBlank:false
                                        },
//                                        {
//                                            xtype: 'comboxCatInv',
//                                            id:'comboxCatInv',
//                                            hidden:true,
//                                            allowBlank: false
//                                        },
                                        {
                                            xtype: 'textareafield',
                                            height:103,
                                            fieldLabel: 'Deskripsi',
                                            id: 'DescriptionInv',
                                            name: 'Description'
                                        },
                                        {
                                            xtype: 'comboxEvaluateType',
                                            fieldLabel: 'Status Persediaan',
                                            id:'statusPersediaan',
                                            name: 'Status',
                                            listeners: {
                                                change: function(txt, The, eOpts) {
                                                    if(The!='Active')
                                                    {
                                                        Ext.getCmp('EvaluateReason').show();
                                                    } else {
                                                        Ext.getCmp('EvaluateReason').hide();
                                                    }

                                                    if(The=='Sold')
                                                    {
                                                        //sold
                                                        Ext.getCmp('EvaluateSoldPrice').show();
                                                    } else {
                                                        Ext.getCmp('EvaluateSoldPrice').hide();
                                                    }
                                                }
                                            }
                                        },
                                        {
                                            xtype:'hiddenfield',
                                            id:'IsRemoved',
                                            name:'IsRemoved'
                                        },

                                        {
                                            xtype     : 'numericfield',
                                            emptyText:'Masukkan Harga...',
                                            hideTrigger:true,
                                            // width:170,
                                            hidden:true,
                                            id:'EvaluateSoldPrice',
                                            name      : 'EvaluateSoldPrice',
                                            fieldLabel: 'Harga Jual'
                                        },
                                         {
                                            xtype: 'textareafield',
                                            fieldLabel: 'Alasan',
                                            // height:103,
                                            id:'EvaluateReason',
                                            // width:272,
                                            // fieldStyle: 'padding: 0px 0px 0px 0px; margin-left:188px;',
                                            name: 'EvaluateReason'
                                        },
                                        // {
                                        //     xtype: 'fieldcontainer',
                                        //     id:'cntReasonInventory',
                                        //     combineErrors: true,
                                        //     hidden:true,
                                        //     hideLabel: true,
                                        //     msgTarget : 'side',
                                        //     // layout: 'hbox'
                                        //     anchor:'30%',
                                        //     defaults: {
                                        //         flex: 1
                                        //         // hideLabel: true
                                        //     },
                                        //     items: [
                                        //         {
                                        //             xtype: 'textareafield',
                                        //             height:103,
                                        //             id:'EvaluateReason',
                                        //             width:272,
                                        //             fieldStyle: 'padding: 0px 0px 0px 0px; margin-left:188px;',
                                        //             name: 'EvaluateReason'
                                        //         }
                                        //     ]
                                        // },
                                        //   {
                                        //     xtype: 'fieldcontainer',
                                        //     id:'EvaluateSoldPrice',
                                        //     combineErrors: true,
                                        //     hidden:true,
                                        //     hideLabel: true,
                                        //     msgTarget : 'side',
                                        //     // layout: 'hbox'
                                        //     anchor:'30%',
                                        //     defaults: {
                                        //         flex: 1
                                        //         // hideLabel: true
                                        //     },
                                        //     items: [
                                        //         {
                                        //             xtype: 'numericfield',
                                        //             fieldStyle: 'padding: 0px 0px 0px 0px; margin-left:188px;',
                                        //             name: 'EvaluateSoldPrice'
                                        //         }
                                        //     ]
                                        // },
                                        {
                                            hidden:true,
                                            xtype: 'checkboxgroup',
//                                            anchor: '100%',
                                            columns: 3,
                                            items: [{
                                                    xtype: 'checkboxfield',
                                                    name: 'cbdijual',
                                                    hidden:true,
                                                    id: 'cbdijual',
                                                    boxLabel: 'Dijual',
                                                    listeners: {
                                                        change: function () {
                                                            if (this.getValue()) {
                                                                Ext.getCmp('fieldsetInvSell').setDisabled(false);
                                                            } else {
                                                                Ext.getCmp('fieldsetInvSell').setDisabled(true);
                                                            }
                                                        }
                                                    }
                                                }, {
                                                    xtype: 'checkboxfield',
                                                    name: 'cbdibeli',
                                                    id: 'cbdibeli',
                                                    padding: '0 0 0 -320',
                                                    boxLabel: 'Dibeli',
                                                    listeners: {
                                                        change: function () {
                                                            // var val = Ext.getCmp('cbdibeli').getValue();
                                                            if (this.getValue()) {
                                                                Ext.getCmp('fieldsetInvBuy').setDisabled(false);
                                                            } else {
                                                                Ext.getCmp('fieldsetInvBuy').setDisabled(true);
                                                            }
                                                        }
                                                    }
                                                }, {
                                                    xtype: 'checkboxfield',
                                                    name: 'cbpersediaan',
                                                    id: 'cbpersediaan',
                                                    padding: '0 0 0 -320',
                                                    boxLabel: 'Disusutkan',
                                                    listeners: {
                                                        change: function () {
                                                            // var val = Ext.getCmp('cbpersediaan').getValue();
                                                            if (this.getValue()) {
//                                                                Ext.getCmp('fieldsetInvPersediaan').setDisabled(false);

//                                                                Ext.getCmp('qtystockInv').setDisabled(false);
                                                                fieldPenyusutan(false)
                                                            } else {
//                                                                Ext.getCmp('fieldsetInvPersediaan').setDisabled(true);

//                                                                 Ext.getCmp('qtystockInv').setDisabled(false);
                                                               fieldPenyusutan(true)
                                                            }

                                                        }
                                                    }
                                                }, {
                                                    xtype: 'checkboxfield',
                                                    name: 'nonaktif',
                                                    id: 'nonaktif',
//                                                    padding: '0 0 0 -120',
                                                    boxLabel: 'Tidak Aktif',
                                                    listeners: {
                                                        change: function () {
                                                            //                                var val = Ext.getCmp('nonaktif').getValue();
                                                            // var formProfile = Ext.ComponentQuery.query('FormProfile')[0];
                                                            // if (this.getValue()) {
                                                            //     formProfile.getForm().findField('cbdibeli').setValue(false);
                                                            //     formProfile.getForm().findField('cbdijual').setValue(false);
                                                            //     formProfile.getForm().findField('cbpersediaan').setValue(false);
                                                            //     Ext.getCmp('idFormBuy').setDisabled(true);
                                                            //     Ext.getCmp('idFormSell').setDisabled(true);
                                                            //     Ext.getCmp('idFormInventoried').setDisabled(true);
                                                            //     //                                        Ext.getCmp('idTabItemInventoryHistory').setDisabled(true);
                                                            // } else {
                                                            //     Ext.getCmp('idFormBuy').setDisabled(false);
                                                            //     Ext.getCmp('idFormSell').setDisabled(false);
                                                            //     Ext.getCmp('idFormInventoried').setDisabled(false);
                                                            //     Ext.getCmp('idTabItemInventoryHistory').setDisabled(false);
                                                            // }
                                                        }
                                                    }
                                                }
                                                // {boxLabel: 'Item 1', name: 'cb-horiz-1'},
                                                // {boxLabel: 'Item 2', name: 'cb-horiz-2',padding:'0 0 0 -120'},
                                                // {boxLabel: 'Item 3', name: 'cb-horiz-3'},
                                                // {boxLabel: 'Item 4', name: 'cb-horiz-4'}
                                            ]
                                        }]
                                },
                                {
                                    xtype: 'fieldset',
                                    id: 'fieldsetInvSell',
                                    hidden:true,
                                    title: 'Penjualan',
                                    // collapsible: true,
                                    items: [{
                                            xtype: 'hiddenfield',
                                            name: 'incomeaccount',
                                            id: 'incomeaccountSellID'
                                        },
                                        {
                                            xtype: 'textfield',
                                            anchor: '100%',
                                            fieldLabel: 'Harga Dasar Penjualan',
                                            name: 'sellingprice'
                                        }, {
                                            xtype: 'comboxTax',
                                            anchor: '100%',
                                            name: 'taxsellid',
                                            id:'idselingtax',
                                            fieldLabel: 'Pajak Penjualan'
                                        }, {
                                            xtype: 'textfield',
                                            anchor: '50%',
                                            fieldLabel: 'Satuan',
                                            name: 'unitmeasuresell'
                                        }
                                    ]
                                }
                            ]

                        },
                        {
                            items: [
                                {
                                    xtype: 'container',
                                    // padding: '0 0 0 10',
                                    layout: 'anchor',
                                    defaultType: 'textfield',
                                    items: [
                                        {
                                            xtype: 'fieldset',
                                            id: 'fieldsetInvBuy',
                                            title: 'Pembelian',
                                            // collapsible: true,
                                            items: [{
                                                    xtype: 'hiddenfield',
                                                    name: 'cosaccount',
                                                    id: 'cosaccountBuy'
                                                }, {
                                                    xtype: 'numericfield',
                                                    hideTrigger:true,
                                                    fieldLabel: 'Harga Beli',
                                                    id: 'costInventory',
                                                    name: 'Cost',
                                                    listeners: {
                                                        'render': function(c) {
                                                            c.getEl().on('keyup', function() {
                                                                CalcPenyusutan();
                                                            }, c);
                                                        },
                                                        change: function(txt, The, eOpts) {
                                                            CalcPenyusutan();
                                                        }
                                                    }
                                                }, {
                                                    xtype: 'textfield',
                                                    fieldLabel: 'Satuan',
                                                    anchor: '70%',
                                                    id: 'UnitMeasure',
                                                    name: 'UnitMeasure'
                                                }, {
                                                    xtype: 'comboxTax',
                                                    id:'idbuytax',
                                                    name: 'idbuytax',
                                                    hidden:true,
                                                    anchor: '70%',
                                                    fieldLabel: 'Pajak Pembelian'
                                                }, {
                                                    xtype: 'comboxSupplier',
                                                    hidden:true,
                                                    id:'comboxSupplier'
                                                },
                                                {
                                                     xtype: 'textfield',
                                                    fieldLabel: 'Supplier',
                                                    anchor: '70%',
                                                    id: 'SupplierName',
                                                    name: 'SupplierName'
                                                },
                                                {
                                                    xtype: 'datefield',
                                                    format: 'Y-m-d',
                                                    anchor: '70%',
                                                    fieldLabel: 'Tgl Pembelian Terakhir',
                                                    id: 'datebuy',
                                                    name: 'DateBuy'
                                                }]
                                        },
                                        {
                                            xtype: 'fieldset',
                                            title: 'Persediaan',
                                            id:'fieldsetInvPersediaan',
                                            // collapsible: true,
                                            items: [{
                                                xtype: 'numberfield',
                                                anchor: '60%',
                                                readOnly:true,
                                                fieldLabel: 'Jumlah Stok',
                                                id: 'qtystockInv',
                                                name: 'Stock'
                                            }, {
                                                xtype: 'numberfield',
                                                anchor: '98%',
                                                fieldLabel: 'Nilai Residu',
                                                listeners: {
                                                    'render': function(c) {
                                                        c.getEl().on('keyup', function() {
                                                            CalcPenyusutan();
                                                        }, c);
                                                    },
                                                    change: function(txt, The, eOpts) {
                                                        CalcPenyusutan();
                                                    }
                                                },
                                                id: 'residu',
                                                name: 'Residu'
                                            }, {
                                                xtype: 'numberfield',
                                                anchor: '98%',
                                                fieldLabel: 'Umur Ekonomis (tahun)',
                                                allowBlank: false,
                                                id: 'umurEkonomis',
                                                name: 'Umur',
                                                listeners: {
                                                    'render': function(c) {
                                                        c.getEl().on('keyup', function() {
                                                            CalcPenyusutan();
                                                        }, c);
                                                    },
                                                    change: function(txt, The, eOpts) {
                                                        CalcPenyusutan();
                                                    }
                                                },
                                            }, {
                                                xtype: 'textfield',
                                                readOnly:true,
                                                style: 'text-align: right;',
                                                anchor: '98%',
                                                labelStyle: 'text-align:left;',
                                                // anchor: '9',
                                                fieldLabel: 'Akumulasi Beban Berjalan',
                                                id: 'akumulasibeban',
                                                allowBlank: false,
                                                name: 'AkumulasiBeban',
                                                // listeners: {
                                                //     change: function(txt, The, eOpts){
                                                //       this.setRawValue(renderNomor(this.getValue()));
                                                //     }
                                                // }
                                            }, {
                                                xtype: 'textfield',
                                                readOnly:true,
                                                anchor: '98%',
                                                style: 'text-align: right',
                                                labelStyle: 'text-align:left',
                                                fieldLabel: 'Beban Tahun Berjalan',
                                                id: 'bebanberjalan',
                                                allowBlank: false,
                                                name: 'BebanBerjalan'
                                            }, {
                                                xtype: 'textfield',
                                                readOnly:true,
                                                anchor: '98%',
                                                style: 'text-align: right',
                                                labelStyle: 'text-align:left',
                                                fieldLabel: 'Nilai Buku Berjalan',
                                                id: 'nilaibuku',
//                                                readOnly: true,
                                                allowBlank: false,
                                                name: 'NilaiBuku'
                                            }, {
                                                xtype: 'textfield',
                                                readOnly:true,
                                                anchor: '98%',
                                                style: 'text-align: right',
                                                labelStyle: 'text-align:left',
                                                fieldLabel: 'Beban Perbulan',
                                                id: 'bebanperbulan',
                                                allowBlank: false,
//                                                readOnly: true,
                                                name: 'BebanPerBulan'
                                            }, {
                                                xtype: 'textfield',
                                                readOnly:true,
                                                anchor: '98%',
                                                style: 'text-align: right',
                                                labelStyle: 'text-align:left',
                                                fieldLabel: 'Penyusutan Setelah Habis Usia',
                                                id: 'akumulasiAkhir',
                                                allowBlank: false,
//                                                readOnly: true,
                                                name: 'AkumulasiAkhir'
                                            }]
                                        },
                                        // new Ext.Button({
                                        //     text    : 'Re-evaluasi',
                                        //     id:'reEvaluasiBtn',
                                        //     anchor:'100%',
                                        //     handler : function(btn)
                                        //     {
                                        //         if(Ext.getCmp('reEvaluasiBtnOpt').getValue()=='1')
                                        //         {
                                        //             Ext.getCmp('reEvaluasiBtn').setText('Re-evaluasi');
                                        //             Ext.getCmp('reEvaluasiBtnOpt').setValue('0');
                                        //             Ext.getCmp('fieldsetEvaluasi').hide();
                                        //             readOnlyFieldInventory(false);

                                        //         } else {
                                        //             Ext.getCmp('reEvaluasiBtn').setText('Batalkan Re-evaluasi');
                                        //             Ext.getCmp('reEvaluasiBtnOpt').setValue('1');
                                        //             showWindowReEvaluate(Ext.getCmp('idinventoryInv').getValue());
                                        //             // Ext.getCmp('EvaluateType').setValue(1);
                                        //         }

                                        //     }
                                        // }),
                                    ]
                                }
                            ]
                        }
                    ]
                }
            ];

            frm.callParent();
        },
        afterRender: function ()
        {
            this.superclass.afterRender.apply(this);
            this.doLayout();
        }
    });

//    Ext.define('FormCoaInventory', {
//        extend: 'Ext.form.Panel',
//        title:'Chart of Accounts',
//        id: 'FormCoaInventory',
//        alias: 'widget.FormCoaInventory',
//        initComponent: function () {
//            var frm = this;
//            frm.bodyStyle = 'padding:5px';
//            frm.width = 1050;
//            frm.autoScroll = true;
//            frm.height = 500;
//            frm.fieldDefaults = {
//                msgTarget: 'side',
//                blankText: 'Tidak Boleh Kosong',
//                labelWidth: 180,
//                width: 460
//            };
//            frm.items = [{
//                                        xtype: 'textfield',
//                                        fieldLabel: 'Akun Asset (Harta)',
//                                        name: 'accname',
//                                        id: 'accnameAsset',
//                                        listeners: {
//                                            render: function(component) {
//                                                component.getEl().on('click', function(event, el) {
//                                                    AccLinkedInventoryAsset.show();
//                                                    storeAccountAktive.load({
//                                                        params: {
//                                                            'idunit': Ext.getCmp('idunitAccInventory').getValue()
//                                                        }
//                                                    });
//                                                });
//                                            }
//                                        }
//                                    }, {
//                                        xtype: 'hiddenfield',
//                                        id: 'akumpenyusutaccount',
//                                        name: 'akumpenyusutaccount',
//                                        readOnly: true
//                                    }, {
//                                        xtype: 'textfield',
//                                        fieldLabel: 'Akun Akumulasi Depresiasi',
//                                        name: 'accnamePenyusutan',
//                                        id: 'accnamePenyusutan',
//                                        listeners: {
//                                            render: function(component) {
//                                                component.getEl().on('click', function(event, el) {
//                                                    AccLinkedInventoryAkumulasi.show();
//                                                    storeAccountAktive.load({
//                                                        params: {
//                                                            'idunit': Ext.getCmp('idunitAccInventory').getValue()
//                                                        }
//                                                    });
//                                                });
//                                            }
//                                        }
//                                    }, {
//                                        xtype: 'hiddenfield',
//                                        id: 'depresiasiaccount',
//                                        name: 'depresiasiaccount',
//                                        readOnly: true
//                                    }, {
//                                        xtype: 'textfield',
//                                        fieldLabel: 'Akun Beban Depresiasi',
//                                        name: 'accnameDepresiasi',
//                                        id: 'accnameDepresiasi',
//                                        listeners: {
//                                            render: function(component) {
//                                                component.getEl().on('click', function(event, el) {
//                                                    AccLinkedInventoryBeban.show();
//                                                    storeAccountAktive.load({
//                                                        params: {
//                                                            'idunit': Ext.getCmp('idunitAccInventory').getValue()
//                                                        }
//                                                    });
//                                                });
//                                            }
//                                        }
//                                    }];
//            frm.callParent();
//        },
//        afterRender: function ()
//        {
//            this.superclass.afterRender.apply(this);
//            this.doLayout();
//        }
//    });
//

    Ext.define('GridDepresiasiInventoryModel', {
    extend: 'Ext.data.Model',
    fields: ['idinventory','month','year','penyusutan','idunit'],
    idProperty: 'id'
});

var storeGridDepresiasiInventory = Ext.create('Ext.data.Store', {
    pageSize: 100,
    model: 'GridDepresiasiInventoryModel',
    //remoteSort: true,
    // autoload:true,
    proxy: {
        type: 'ajax',
        url: m_depreciatedinv,
        actionMethods: 'POST',
        reader: {
            root: 'rows',
            totalProperty: 'results'
        },
        //simpleSortMode: true
    },
    sorters: [{
            property: 'menu_name',
            direction: 'DESC'
        }]
});


Ext.define('MY.searchGridDepresiasiInventoryAll', {
    extend: 'Ext.ux.form.SearchField',
    alias: 'widget.searchGridDepresiasiInventory',
    store: storeGridDepresiasiInventory,
    width: 180
});

var smGridDepresiasiInventory = Ext.create('Ext.selection.CheckboxModel', {
    allowDeselect: true,
    mode: 'SINGLE',
    listeners: {
        deselect: function(model, record, index) {
            var selectedLen = smGridInventoryAll.getSelection().length;
            if (selectedLen == 0) {
                console.log(selectedLen);
                // Ext.getCmp('btnDeleteInventoryAll').disable();
            }
        },
        select: function(model, record, index) {
            // Ext.getCmp('btnDeleteInventoryAll').enable();
        }
    }
});

Ext.define('GridDepresiasiInventoryTab', {
    title: 'Chart of Acc & Depreciated',
    width: 1050,
    height:400,
    itemId: 'GridDepresiasiInventoryTab',
    id: 'GridDepresiasiInventoryTab',
    extend: 'Ext.grid.Panel',
    alias: 'widget.GridDepresiasiInventoryTab',
    store: storeGridDepresiasiInventory,
    loadMask: true,
    columns: [
    {
        header: 'idunit',
        dataIndex: 'idunit',
        hidden: true
    }, {
        header: 'idinventory',
        dataIndex: 'idinventory',
        hidden: true
    }, {
        header: 'Bulan',
        dataIndex: 'month',
        minWidth: 150
    }, {
        header: 'Tahun',
        dataIndex: 'year',
        minWidth: 200
    }, {
        header: 'Depresiasi',
        align:'right',
        xtype:'numbercolumn',
        dataIndex: 'penyusutan',
        minWidth: 200
    }],
    dockedItems: [
        {
                xtype:'toolbar',
                dock:'top',
                items:[
                    {
                        xtype: 'hiddenfield',
                        id: 'coaIDAsset',
                        name: 'coaIDAsset',
                        readOnly: true
                    },
                    {
                        xtype: 'textfield',
                        fieldLabel: 'Akun Asset (Harta)',
                        name: 'coaNameAsset',
                        labelWidth:160,
                        id: 'coaNameAsset',
                        listeners: {
                            render: function(component) {
                                component.getEl().on('click', function(event, el) {
                                    wCoaAssetPopup.show();
                                    storeCoaList.load({
                                        params: {
                                            type: 'class',
                                            id:1
                                        }
                                    });
                                });
                            }
                        }
                    }, {
                        xtype: 'hiddenfield',
                        id: 'coaIDAkumDepres',
                        name: 'coaIDAkumDepres',
                        readOnly: true
                    }, {
                        xtype: 'textfield',
                        fieldLabel: 'Akun Akumulasi Depresiasi',
                        labelWidth:160,
                        name: 'coaNameAkumDepres',
                        id: 'coaNameAkumDepres',
                        listeners: {
                            render: function(component) {
                                component.getEl().on('click', function(event, el) {
                                    wCoaAkumDeprecPopup.show();
                                    storeCoaList.load({
                                         params: {
                                                type: 'class',
                                                id:1
                                            }
                                    });
                                });
                            }
                        }
                    }, {
                        xtype: 'hiddenfield',
                        id: 'coaIDBebanDepres',
                        name: 'coaIDBebanDepres',
                        readOnly: true
                    }, {
                        xtype: 'textfield',
                        fieldLabel: 'Akun Beban Depresiasi',
                        labelWidth:160,
                        name: 'coaNameBebanDepres',
                        id: 'coaNameBebanDepres',
                        listeners: {
                            render: function(component) {
                                component.getEl().on('click', function(event, el) {
                                   wCoaBebanDeprecPopup.show();
                                    storeCoaList.load({
                                         params: {
                                                type: 'class',
                                                id:5
                                            }
                                    });
                                });
                            }
                        }
                    }
                ]
        },
        {
                xtype:'toolbar',
                dock:'top',
                items:[{
                        xtype:'displayfield',
                        hideLabel:true,
                        value:'<b>Riwayat Penysutan</b>'
                }]
        },
        {
                xtype: 'pagingtoolbar',
                store: storeGridDepresiasiInventory, // same store GridPanel is using
                dock: 'bottom',
                displayInfo: true
                        // pageSize:20
        }
    ]
});


    var store = Ext.create('Ext.data.Store', {
        storeId: 'inventoryStore',
        autoLoad: true,
        fields: ['InventoryID','Number','Name','Stock','UnitMeasure','Cost','SellingPrice','MinStock','YearBuy'],
        proxy: {
            type: 'rest',
            url: m_datas, // url that will load data with respect to start and limit params
            reader: {
                type: 'json',
                root: 'data',
                totalProperty: 'total',
                idProperty: 'idinventory'
            },
            writer: {
                type: 'json'
            },
            api: {
                destroy: '/loan/deletetype'
            },
            appendId: true
        }
    });

    var grid = Ext.create('Ext.grid.Panel', {
        width: '100%',
        id:'grid',
        loadMask: true,
        style: 'border: 1px solid #CCCCCC',
        store: Ext.data.StoreManager.lookup('inventoryStore'),
        columns: [
            {header: 'InventoryID', dataIndex: 'InventoryID', hidden: true},
            {header: 'No Inventory/SKU', dataIndex: 'Number', minWidth: 150},
            // {header: 'Unit', dataIndex: 'namaunit', minWidth: 100},
            {header: 'Nama', dataIndex: 'Name', minWidth: 300, flex: 1},
            {header: 'Jumlah Persediaan', dataIndex: 'Stock', minWidth: 150, align: 'right'},
            {header: 'Satuan', dataIndex: 'UnitMeasure', minWidth: 100},
            {header: 'Harga Beli', dataIndex: 'Cost', minWidth: 100, xtype: 'numbercolumn', align: 'right'},
            {header: 'Harga Jual', dataIndex: 'SellingPrice', minWidth: 100, xtype: 'numbercolumn', align: 'right'},
//            {header: 'Stok Minimum', dataIndex: 'minstock', minWidth: 110},
            {header: 'Tahun Pembelian', dataIndex: 'YearBuy', minWidth: 130}
        ],
        height: 500,
        renderTo: 'ext-content',
        dockedItems: [
            {
                xtype: 'pagingtoolbar',
                dock: 'bottom',
                store: store,
                displayInfo: true,
                displayMsg: 'Displaying topics {0} - {1} of {2}',
                emptyMsg: "No topics to display"
            }, {
                xtype: 'toolbar',
                dock: 'top',
                items: [
                    {
                        xtype: 'button',
                        iconCls: 'add',
                        text: 'Add',
                        handler: function () {

                            var win = Ext.getCmp('WFormInventory');
//                            var win = Ext.getCmp('WindowInventory');
//
                            if (!win) {
//
                                win = new Ext.Window({
                                    id: 'WFormInventory',
                                    title: 'Form Inventory',
                                    modal: true,
                                    resizable: false,
                                    plain: true,
                                    items: [
                                        {
                                            xtype:'tabpanel',
                                            style:'margin-top:5px;',
                                            plain:true,
                                            tabBar:{
                                                style:'padding-left:5px;background:#fff;'
                                            },
                                            height: 550,
                                            width:1009,
                                            id:'tab-inventory-detail',
                                            items:[{
                                                    xtype: 'FormInventory'
                                                },
//                                                {
//                                                    xtype: 'FormCoaInventory'
//                                                },
                                                {
                                                    xtype:'GridDepresiasiInventoryTab'
                                                }]
                                            }
                                    ],
                                    buttons: [

                                        {
                                            text: 'Save',
                                            margin: '5px',
                                            scale: 'large',
                                            ui: 's-button',
                                            cls: 's-blue',
                                            handler: function () {
                                                var form = Ext.getCmp('FormInventory').getForm();

                                                if (form.isValid()) {
                                                    //var params = Ext.getCmp('menuu').getValue();
                                                    var methode;
                                                    if (Ext.getCmp('idinventoryInv').getValue() == '')
                                                    {
                                                        methode = 'POST';
                                                        var url = m_add;
                                                    } else {
                                                        methode = 'PUT';
                                                        var url = m_edit;
                                                    }

                                                    form.submit({
                                                        //url: m_crud+'?'+ Ext.urlEncode(params),
                                                        url: url,
                                                        method: methode,
                                                        params: {
                                                            coaIDAsset: Ext.getCmp('coaIDAsset').getValue(),
                                                            coaIDAkumDepres: Ext.getCmp('coaIDAkumDepres').getValue(),
                                                            coaIDBebanDepres: Ext.getCmp('coaIDBebanDepres').getValue()
                                                        },
                                                        waitMsg: 'Sending data...',
                                                        success: function (fp, o) {
                                                            Ext.MessageBox.alert('Success', 'Data saved.');
                                                            Ext.getCmp('WFormInventory').hide(this, function () {
                                                                store.load();
                                                            });
                                                        },
                                                        failure: function (form, action) {
                                                            var d = Ext.decode(action.response.responseText);
                                                             Ext.MessageBox.alert('Failed', d.message);
                                                        }
                                                    });


                                                } else {
                                                    Ext.Msg.alert("Error!", "Your form is invalid!");
                                                }
                                            }
                                        }, {
                                            text: 'Close',
                                            margin: '5px',
                                            scale: 'large',
                                            ui: 's-button',
                                            cls: 's-grey',
                                            disabled: false,
                                            handler: function () {
                                                Ext.getCmp('WFormInventory').hide();
                                            }
                                        }
                                    ]
                                });
//
                            }
                            win.show();
                            readOnlyFieldInventory(false);

                            Ext.getCmp('fotoinvthumb').el.dom.src = m_baseurl + '/images/inventory.png';
                            fieldPenyusutan(false)
                            // Ext.getCmp('fieldsetInvBuy').setDisabled(true);
                            Ext.getCmp('FormInventory').getForm().reset();

                            // Ext.getCmp('cntReasonInventoryLabel').hide();
                            // Ext.getCmp('cntReasonInventory').hide();
                            Ext.getCmp('IsRemoved').setValue(0);
                            Ext.getCmp('qtystockInv').setValue(1);
                            // Ext.getCmp('fieldsetEvaluasi').hide(); //hilangkan fieldset evaluasi

                            // Ext.getCmp('reEvaluasiBtn').hide();

                            Ext.getCmp('statusPersediaan').setValue('Active');
                            Ext.getCmp('statusPersediaan').setReadOnly(true);

                            Ext.getCmp('EvaluateReason').hide();
                        }
                    },
                    {
                        xtype: 'button',
                        iconCls: 'edit',
                        text: 'Edit',
                        handler: function () {
                            var win = Ext.getCmp('WFormInventory');



                            var sm = grid.getSelectionModel();
                            var sel = sm.getSelection();

                            if (sel.length > 0) {
                                var id = sel[0].get('InventoryID');

                                if (!win) {
                                win = new Ext.Window({
                                    id: 'WFormInventory',
                                    title: 'Form Inventory',
                                    modal: true,
                                    resizable: false,
                                    plain: true,
                                    items: [
                                       {
                                            xtype:'tabpanel',
                                            style:'margin-top:5px;',
                                            plain:true,
                                            tabBar:{
                                                style:'padding-left:5px;background:#fff;'
                                            },
                                            height: 550,
                                            width:1009,
                                            id:'tab-inventoryedit-detail',
                                            items:[{
                                                    xtype: 'FormInventory'
                                                },
                                                {
                                                    xtype:'GridDepresiasiInventoryTab'
                                                }]
                                            }
                                    ],
                                    buttons: [
                                        {
                                            text: 'Save',
                                            margin: '5px',
                                            scale: 'large',
                                            ui: 's-button',
                                            cls: 's-blue',
                                            handler: function () {
                                               var form = Ext.getCmp('FormInventory').getForm();

                                                if (form.isValid()) {
                                                    //var params = Ext.getCmp('menuu').getValue();
                                                    var methode;
                                                    if (Ext.getCmp('idinventoryInv').getValue() == '')
                                                    {
                                                        methode = 'POST';
                                                        var url = m_add;
                                                    } else {
                                                        methode = 'PUT';
                                                        var url = m_edit;
                                                    }

                                                    form.submit({
                                                        //url: m_crud+'?'+ Ext.urlEncode(params),
                                                        url: url,
                                                        params: {
                                                            coaIDAsset: Ext.getCmp('coaIDAsset').getValue(),
                                                            coaIDAkumDepres: Ext.getCmp('coaIDAkumDepres').getValue(),
                                                            coaIDBebanDepres: Ext.getCmp('coaIDBebanDepres').getValue()
                                                        },
                                                        method: methode,
                                                        waitMsg: 'Sending data...',
                                                        success: function (fp, o) {
                                                                Ext.MessageBox.alert('Success', 'Data saved.');
                                                                Ext.getCmp('WFormInventory').hide(this, function () {
                                                                    store.load();
                                                                });
                                                            },
                                                            failure: function (form, action) {
                                                                var d = Ext.decode(action.response.responseText);
                                                                 Ext.MessageBox.alert('Failed', d.message);
                                                            }
                                                        });

                                                } else {
                                                    Ext.Msg.alert("Error!", "Your form is invalid!");
                                                }
                                            }
                                        }, {
                                            text: 'Close',
                                            margin: '5px',
                                            scale: 'large',
                                            ui: 's-button',
                                            cls: 's-grey',
                                            disabled: false,
                                            handler: function () {
                                                Ext.getCmp('WFormInventory').hide();
                                            }
                                        }
                                    ]
                                });
                            }
                            win.show();
                            readOnlyFieldInventory(true);

                                //alert(id);
                                var WFormInventory = Ext.getCmp('FormInventory');
//                                var formProductType = Ext.ComponentQuery.query('formProductType')[0];

                                WFormInventory.getForm().load({
                                    url: m_data,
                                    method: 'GET',
                                    params: {
                                        id: id
                                    },
                                    success: function (form, action) {
                                        var d = Ext.decode(action.response.responseText);

                                        if(d.data.Images!=='')
                                        {
                                            // Ext.getCmp('fotoinvthumb').el.dom.src = d.data.Images;
                                            Ext.getCmp('fotoinvthumb').el.dom.src = d.data.fotoinventory;
                                        } else {
                                            Ext.getCmp('fotoinvthumb').el.dom.src = m_baseurl + '/images/inventory.png';
                                        }

                                         Ext.getCmp('comboxCatInv').setValue(d.data.CategoryID*1);

                                         Ext.getCmp('coaIDAsset').setValue(d.data.coaIDAsset);
                                         Ext.getCmp('coaNameAsset').setValue(d.data.coaNameAsset);
                                         Ext.getCmp('coaIDAkumDepres').setValue(d.data.coaIDAkumDepres);
                                         Ext.getCmp('coaNameAkumDepres').setValue(d.data.coaNameAkumDepres);
                                         Ext.getCmp('coaIDBebanDepres').setValue(d.data.coaIDBebanDepres);
                                         Ext.getCmp('coaNameBebanDepres').setValue(d.data.coaNameBebanDepres);
                                         // alert(d.data.IsRemoved)

                                         if(d.data.Status=='Active')
                                         {
                                            Ext.getCmp('EvaluateReason').hide();
                                         } else {
                                            Ext.getCmp('EvaluateReason').show();
                                         }

                                       Ext.getCmp('statusPersediaan').setReadOnly(true);
                                       Ext.getCmp('statusPersediaan').setFieldStyle('background-color: white; border:none; background-image: none;');
                                       Ext.getCmp('EvaluateSoldPrice').setReadOnly(true);
                                    },
                                    failure: function (form, action) {
                                        Ext.Msg.alert("Load failed", action.result.errorMessage);
                                    }
                                })

                            } else {
                                Ext.MessageBox.show({
                                    title: '',
                                    msg: 'Please select data to update',
                                    width: 300,
                                    buttons: Ext.MessageBox.OK,
                                    animateTarget: 'mb3'
                                });
                            }


//                           fieldPenyusutan(false)
                           // Ext.getCmp('fieldsetInvBuy').setDisabled(false);

                        }
                    },
                    {
                        xtype: 'button',
                        iconCls: 'edit',
                        text: 'Re-evaluasi',
                        handler: function () {
                            var win = Ext.getCmp('WFormInventory');



                            var sm = grid.getSelectionModel();
                            var sel = sm.getSelection();

                            if (sel.length > 0) {
                                var id = sel[0].get('InventoryID');

                                if (!win) {
                                win = new Ext.Window({
                                    id: 'WFormInventory',
                                    title: 'Form Inventory',
                                    modal: true,
                                    resizable: false,
                                    plain: true,
                                    items: [
                                       {
                                            xtype:'tabpanel',
                                            style:'margin-top:5px;',
                                            plain:true,
                                            tabBar:{
                                                style:'padding-left:5px;background:#fff;'
                                            },
                                            height: 550,
                                            id:'tab-inventoryedit-detail',
                                            items:[{
                                                    xtype: 'FormInventory'
                                                },
                                                {
                                                    xtype:'GridDepresiasiInventoryTab'
                                                }]
                                            }
                                    ],
                                    buttons: [
                                        {
                                            text: 'Save',
                                            margin: '5px',
                                            scale: 'large',
                                            ui: 's-button',
                                            cls: 's-blue',
                                            handler: function () {
                                               var form = Ext.getCmp('FormInventory').getForm();

                                                if (form.isValid()) {
                                                    //var params = Ext.getCmp('menuu').getValue();
                                                    var methode;
                                                    if (Ext.getCmp('idinventoryInv').getValue() == '')
                                                    {
                                                        methode = 'POST';
                                                        var url = m_add;
                                                    } else {
                                                        methode = 'PUT';
                                                        var url = m_edit;
                                                    }

                                                    form.submit({
                                                        //url: m_crud+'?'+ Ext.urlEncode(params),
                                                        url: url,
                                                        params: {
                                                            coaIDAsset: Ext.getCmp('coaIDAsset').getValue(),
                                                            coaIDAkumDepres: Ext.getCmp('coaIDAkumDepres').getValue(),
                                                            coaIDBebanDepres: Ext.getCmp('coaIDBebanDepres').getValue()
                                                        },
                                                        method: methode,
                                                        waitMsg: 'Sending data...',
                                                        success: function (fp, o) {
                                                                Ext.MessageBox.alert('Success', 'Data saved.');
                                                                Ext.getCmp('WFormInventory').hide(this, function () {
                                                                    store.load();
                                                                });
                                                            },
                                                            failure: function (form, action) {
                                                                var d = Ext.decode(action.response.responseText);
                                                                 Ext.MessageBox.alert('Failed', d.message);
                                                            }
                                                        });

                                                } else {
                                                    Ext.Msg.alert("Error!", "Your form is invalid!");
                                                }
                                            }
                                        }, {
                                            text: 'Close',
                                            margin: '5px',
                                            scale: 'large',
                                            ui: 's-button',
                                            cls: 's-grey',
                                            disabled: false,
                                            handler: function () {
                                                Ext.getCmp('WFormInventory').hide();
                                            }
                                        }
                                    ]
                                });
                            }
                            win.show();

                                //alert(id);
                                var WFormInventory = Ext.getCmp('FormInventory');
//                                var formProductType = Ext.ComponentQuery.query('formProductType')[0];

                                WFormInventory.getForm().load({
                                    url: m_data,
                                    method: 'GET',
                                    params: {
                                        id: id
                                    },
                                    success: function (form, action) {
                                        var d = Ext.decode(action.response.responseText);

                                        if(d.data.Images!=='')
                                        {
                                            // Ext.getCmp('fotoinvthumb').el.dom.src = d.data.Images;
                                            Ext.getCmp('fotoinvthumb').el.dom.src = d.data.fotoinventory;
                                        } else {
                                            Ext.getCmp('fotoinvthumb').el.dom.src = m_baseurl + '/images/inventory.png';
                                        }

                                         Ext.getCmp('comboxCatInv').setValue(d.data.CategoryID*1);

                                         Ext.getCmp('coaIDAsset').setValue(d.data.coaIDAsset);
                                         Ext.getCmp('coaNameAsset').setValue(d.data.coaNameAsset);
                                         Ext.getCmp('coaIDAkumDepres').setValue(d.data.coaIDAkumDepres);
                                         Ext.getCmp('coaNameAkumDepres').setValue(d.data.coaNameAkumDepres);
                                         Ext.getCmp('coaIDBebanDepres').setValue(d.data.coaIDBebanDepres);
                                         Ext.getCmp('coaNameBebanDepres').setValue(d.data.coaNameBebanDepres);

                                        if(d.data.Status=='Active')
                                         {
                                            // Ext.getCmp('cntReasonInventoryLabel').hide();
                                         } else {
                                            // Ext.getCmp('cntReasonInventoryLabel').show();
                                         }

                                        readOnlyFieldInventoryReEvaluasi();
                                    },
                                    failure: function (form, action) {
                                        Ext.Msg.alert("Load failed", action.result.errorMessage);
                                    }
                                })

                            } else {
                                Ext.MessageBox.show({
                                    title: '',
                                    msg: 'Please select data to update',
                                    width: 300,
                                    buttons: Ext.MessageBox.OK,
                                    animateTarget: 'mb3'
                                });
                            }


//                           fieldPenyusutan(false)
                           // Ext.getCmp('fieldsetInvBuy').setDisabled(false);

                        }
                    },
                    {
                        xtype: 'button',
                        hidden:true,
                        iconCls: 'edit',
                        text: 'Re-evaluasi',
                        handler: function () {


                            var sm = grid.getSelectionModel();
                            var sel = sm.getSelection();

                            if (sel.length > 0) {
                                var id = sel[0].get('InventoryID');

                                var win = Ext.getCmp('WFormInventory');

                            if (!win) {
                                win = new Ext.Window({
                                    id: 'WFormInventory',
                                    title: 'Form Inventory',
                                    modal: true,
                                    resizable: false,
                                    plain: true,
                                    items: [
                                       {
                                            xtype:'tabpanel',
                                            style:'margin-top:5px;',
                                            plain:true,
                                            tabBar:{
                                                style:'padding-left:5px;background:#fff;'
                                            },
                                            height: 550,
                                            id:'tab-inventoryedit-detail',
                                            items:[{
                                                    xtype: 'FormInventory'
                                                },
                                                {
                                                    xtype:'GridDepresiasiInventoryTab'
                                                }]
                                            }
                                    ],
                                    buttons: [
                                        {
                                            text: 'Save',
                                            margin: '5px',
                                            scale: 'large',
                                            ui: 's-button',
                                            cls: 's-blue',
                                            handler: function () {
                                               var form = Ext.getCmp('FormInventory').getForm();

                                                if (form.isValid()) {
                                                    //var params = Ext.getCmp('menuu').getValue();
                                                    var methode;
                                                    if (Ext.getCmp('idinventoryInv').getValue() == '')
                                                    {
                                                        methode = 'POST';
                                                        var url = m_add;
                                                    } else {
                                                        methode = 'PUT';
                                                        var url = m_edit;
                                                    }

                                                    form.submit({
                                                        //url: m_crud+'?'+ Ext.urlEncode(params),
                                                        url: url,
                                                        params: {
                                                            coaIDAsset: Ext.getCmp('coaIDAsset').getValue(),
                                                            coaIDAkumDepres: Ext.getCmp('coaIDAkumDepres').getValue(),
                                                            coaIDBebanDepres: Ext.getCmp('coaIDBebanDepres').getValue()
                                                        },
                                                        method: methode,
                                                        waitMsg: 'Sending data...',
                                                        success: function (fp, o) {
                                                                Ext.MessageBox.alert('Success', 'Data saved.');
                                                                Ext.getCmp('WFormInventory').hide(this, function () {
                                                                    store.load();
                                                                });
                                                            },
                                                            failure: function (form, action) {
                                                                var d = Ext.decode(action.response.responseText);
                                                                 Ext.MessageBox.alert('Failed', d.message);
                                                            }
                                                        });

                                                } else {
                                                    Ext.Msg.alert("Error!", "Your form is invalid!");
                                                }
                                            }
                                        }, {
                                            text: 'Close',
                                            margin: '5px',
                                            scale: 'large',
                                            ui: 's-button',
                                            cls: 's-grey',
                                            disabled: false,
                                            handler: function () {
                                                Ext.getCmp('WFormInventory').hide();
                                            }
                                        }
                                    ]
                                });
                            }
                            win.show();
                            readOnlyFieldInventory(true);

                                //alert(id);
                                var WFormInventory = Ext.getCmp('FormInventory');
//                                var formProductType = Ext.ComponentQuery.query('formProductType')[0];

                                WFormInventory.getForm().load({
                                    url: m_data,
                                    method: 'GET',
                                    params: {
                                        id: id
                                    },
                                    success: function (form, action) {
                                        var d = Ext.decode(action.response.responseText);
//                                        console.log(d.data);
//                                        Ext.getCmp('loanTypeInterestDurationMargin').setValue(d.data.loanTypeInterestDuration * 1);
                                         Ext.getCmp('fotoinvthumb').el.dom.src = d.data.Images;

                                         Ext.getCmp('comboxCatInv').setValue(d.data.idinventorycat*1);

                                         Ext.getCmp('EvaluateSoldPrice').hide();

                                         // Ext.getCmp('idbuytax').setValue(d.data.idbuytax*1);
                                         // Ext.getCmp('comboxSupplier').setValue(d.data.idsupplier*1);

                                         // if(d.data.issell*1==1)
                                         // {
                                         //     Ext.getCmp('cbdijual').setValue(true);
                                         // } else {
                                         //     Ext.getCmp('cbdijual').setValue(false);
                                         // }

                                         // if(d.data.isbuy*1==1)
                                         // {
                                         //     Ext.getCmp('cbdibeli').setValue(true);
                                         //     Ext.getCmp('fieldsetInvBuy').setDisabled(false);
                                         // } else {
                                         //     Ext.getCmp('cbdibeli').setValue(false);
                                         //     Ext.getCmp('fieldsetInvBuy').setDisabled(true);
                                         // }

                                         // if(d.data.isinventory*1==1)
                                         // {
                                         //     Ext.getCmp('cbpersediaan').setValue(true);
                                         //     fieldPenyusutan(false)
                                         // } else {
                                         //     Ext.getCmp('cbpersediaan').setValue(false);
                                         //     fieldPenyusutan(true)
                                         // }

                                         Ext.getCmp('coaIDAsset').setValue(d.data.coaIDAsset);
                                         Ext.getCmp('coaNameAsset').setValue(d.data.coaNameAsset);
                                         Ext.getCmp('coaIDAkumDepres').setValue(d.data.coaIDAkumDepres);
                                         Ext.getCmp('coaNameAkumDepres').setValue(d.data.coaNameAkumDepres);
                                         Ext.getCmp('coaIDBebanDepres').setValue(d.data.coaIDBebanDepres);
                                         Ext.getCmp('coaNameBebanDepres').setValue(d.data.coaNameBebanDepres);

                                         //  if(d.data.IsRemoved*1==1)
                                         // {
                                         //    Ext.getCmp('EvaluateReasonLabel').show();
                                         //    Ext.getCmp('EvaluateReason').show();
                                         //    // Ext.getCmp('btnRemoveInventory').setText('Removed');
                                         // } else {
                                         //    Ext.getCmp('EvaluateReasonLabel').hide();
                                         //    Ext.getCmp('EvaluateReason').hide();
                                         //    // Ext.getCmp('btnRemoveInventory').setText('Remove');
                                         // }

                                        // Ext.getCmp('fieldsetEvaluasi').show();
                                        // Ext.getCmp('EvaluateType').setValue(d.data.EvaluateType*1);
                                        //  if(d.data.EvaluateType*1==1)
                                        //  {
                                        //     //sold
                                        //     Ext.getCmp('EvaluateSoldPrice').show();
                                        //  } else {
                                        //     Ext.getCmp('EvaluateSoldPrice').hide();
                                        //  }


                                    },
                                    failure: function (form, action) {
                                        Ext.Msg.alert("Load failed", action.result.errorMessage);
                                    }
                                });

                            } else {
                                Ext.MessageBox.show({
                                    title: '',
                                    msg: 'Please select data to update',
                                    width: 300,
                                    buttons: Ext.MessageBox.OK,
                                    animateTarget: 'mb3'
                                });
                            }


//                           fieldPenyusutan(false)
                           // Ext.getCmp('fieldsetInvBuy').setDisabled(false);

                        }
                    },
                    {
                        itemId: 'remove',
                        icon: varjs.config.base_url + 'images/icons/new/delete.png',
//                        cls: m_act_delete,
                        text: 'Hapus',
                        scope: this,
                        handler: function() {
                            var smb = grid.getSelectionModel().getSelection()[0];
                            Ext.MessageBox.confirm('Message', 'Apakah anda mau menghapus data ini ?', function(btn) {
                                if (btn == 'yes') {
                                    Ext.Ajax.request({
                                        waitMsg: 'Please Wait',
                                        url: m_delete,
                                        method: 'DELETE',
                                        params: {id: smb.raw.InventoryID},
                                        success: function(response, opts) {
                                            var obj = Ext.decode(response.responseText);
                                            switch (obj.success) {
                                                case true:
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
                    },'->',
                    {
                        xtype:'comboxEvaluateType',
                        value:'Active',
                        listeners: {
                            change: function(txt, The, eOpts) {
                                store.load({
                                    params: {Status:The}
                                });
                            }
                        }
                    }
                ]
            }
        ]
    });

});


function CalcPenyusutan(prefix) {
    // var FormBuy = Ext.ComponentQuery.query('FormBuy')[0];
//    if (prefix === undefined)
//    {
        prefix = '';
//    }
//    console.log(prefix)
//    if (prefix == 'Opening')
//    {
//        //input persediaan awal
//        var FormBuy = Ext.getCmp('formInventoryOpening');
//        var costInventory = Ext.getCmp('costInventory' + prefix).getValue();
//    } else {
//        var FormBuy = Ext.getCmp('FormInventory');
//        var costInventory = Ext.getCmp('costInventory').getValue();
//    }

    var FormBuy = Ext.getCmp('FormInventory');
    var costInventory = Ext.getCmp('costInventory').getValue();

    var dt = FormBuy.getForm().findField('datebuy').getSubmitValue();
    var residu = FormBuy.getForm().findField('residu' + prefix).getValue();
    var umurEkonomis = FormBuy.getForm().findField('umurEkonomis' + prefix).getValue();
    var dtArr = dt.split('-');
    var tgl = dtArr[0] + '-' + dtArr[1] + '-' + dtArr[2];

    // var FormInventoried = Ext.ComponentQuery.query('FormInventoried')[0];
    if (costInventory != '' && residu != null && umurEkonomis != null)
    {
        Ext.Ajax.request({
            url: m_apiurl+'/cooperatives/countdepreciate/',
            method: 'GET',
             params: {
                 costInventory: costInventory,
                 residu:residu,
                 umurEkonomis:umurEkonomis,
                 tgl:tgl,
                 tahun:dtArr[0]
             },
            success: function (form, action) {
                var d = Ext.decode(form.responseText);

                FormBuy.getForm().findField('bebanperbulan' + prefix).setValue(d.penyusutanBulan);
                FormBuy.getForm().findField('akumulasibeban' + prefix).setValue(d.akumulasiPenyusutan);
                FormBuy.getForm().findField('bebanberjalan' + prefix).setValue(d.bebanBerjalan);
                FormBuy.getForm().findField('nilaibuku' + prefix).setValue(d.nilaiBuku);
                FormBuy.getForm().findField('akumulasiAkhir' + prefix).setValue(d.akumulasiPenyusutanAkhir);

            },
            failure: function (form, action) {
                Ext.Msg.alert('Failed', action.result ? action.result.message : 'No response');
            }
        });
    }
}

function fieldPenyusutan(opsi)
{
    Ext.getCmp('residu').setDisabled(opsi);
    Ext.getCmp('umurEkonomis').setDisabled(opsi);
    Ext.getCmp('akumulasibeban').setDisabled(opsi);
    Ext.getCmp('bebanberjalan').setDisabled(opsi);
    Ext.getCmp('nilaibuku').setDisabled(opsi);
    Ext.getCmp('bebanperbulan').setDisabled(opsi);
    Ext.getCmp('akumulasiAkhir').setDisabled(opsi);
}

function readOnlyFieldInventory(opt)
{
    // Ext.getCmp('NumberInv').setReadOnly(opt);
    // Ext.getCmp('SerialNumber').setReadOnly(opt);
    // Ext.getCmp('NameInv').setReadOnly(opt);
    // Ext.getCmp('LocationInv').setReadOnly(opt);
    // Ext.getCmp('DescriptionInv').setReadOnly(opt);
    Ext.getCmp('IsRemoved').setReadOnly(opt);
    // Ext.getCmp('EvaluateReasonLabel').setReadOnly(opt);
    Ext.getCmp('EvaluateReason').setReadOnly(opt);
    Ext.getCmp('costInventory').setReadOnly(opt);
    Ext.getCmp('UnitMeasure').setReadOnly(opt);
    Ext.getCmp('SupplierName').setReadOnly(opt);
    Ext.getCmp('datebuy').setReadOnly(opt);
    Ext.getCmp('qtystockInv').setReadOnly(opt);
    Ext.getCmp('residu').setReadOnly(opt);
    Ext.getCmp('umurEkonomis').setReadOnly(opt);
    Ext.getCmp('akumulasibeban').setReadOnly(opt);
    Ext.getCmp('bebanberjalan').setReadOnly(opt);
    Ext.getCmp('nilaibuku').setReadOnly(opt);
    Ext.getCmp('bebanperbulan').setReadOnly(opt);
    Ext.getCmp('akumulasiAkhir').setReadOnly(opt);
    // Ext.getCmp('comboxCatInv').setDisabled(opt);
}

function readOnlyFieldInventoryReEvaluasi()
{
    Ext.getCmp('NumberInv').setReadOnly(true);
    Ext.getCmp('SerialNumber').setReadOnly(true);
    Ext.getCmp('NameInv').setReadOnly(true);
    Ext.getCmp('LocationInv').setReadOnly(true);
    Ext.getCmp('DescriptionInv').setReadOnly(true);

    Ext.getCmp('IsRemoved').setReadOnly(false);
    // Ext.getCmp('EvaluateReasonLabel').setReadOnly(false);
    Ext.getCmp('EvaluateReason').setReadOnly(false);
    Ext.getCmp('costInventory').setReadOnly(false);
    Ext.getCmp('UnitMeasure').setReadOnly(false);
    Ext.getCmp('SupplierName').setReadOnly(false);
    Ext.getCmp('datebuy').setReadOnly(false);
    Ext.getCmp('qtystockInv').setReadOnly(true);
    Ext.getCmp('residu').setReadOnly(false);
    Ext.getCmp('umurEkonomis').setReadOnly(false);
    Ext.getCmp('akumulasibeban').setReadOnly(false);
    Ext.getCmp('bebanberjalan').setReadOnly(false);
    Ext.getCmp('nilaibuku').setReadOnly(false);
    Ext.getCmp('bebanperbulan').setReadOnly(false);
    Ext.getCmp('akumulasiAkhir').setReadOnly(false);

    Ext.getCmp('comboxCatInv').setReadOnly(true);

    Ext.getCmp('statusPersediaan').setReadOnly(false);
    Ext.getCmp('EvaluateSoldPrice').setReadOnly(false);
}

function showWindowReEvaluate(id)
{
    // var id = sel[0].get('InventoryID');

    var win = Ext.getCmp('WFormInventory');

    if (!win) {
        win = new Ext.Window({
            id: 'WFormInventory',
            title: 'Form Inventory',
            modal: true,
            resizable: false,
            plain: true,
            items: [
               {
                    xtype:'tabpanel',
                    style:'margin-top:5px;',
                    plain:true,
                    tabBar:{
                        style:'padding-left:5px;background:#fff;'
                    },
                    height: 550,
                    id:'tab-inventoryedit-detail',
                    items:[{
                            xtype: 'FormInventory'
                        },
                        {
                            xtype:'GridDepresiasiInventoryTab'
                        }]
                    }
            ],
            buttons: [
                {
                    text: 'Save',
                    margin: '5px',
                    scale: 'large',
                    ui: 's-button',
                    cls: 's-blue',
                    handler: function () {
                       var form = Ext.getCmp('FormInventory').getForm();

                        if (form.isValid()) {
                            //var params = Ext.getCmp('menuu').getValue();
                            var methode;
                            if (Ext.getCmp('idinventoryInv').getValue() == '')
                            {
                                methode = 'POST';
                                var url = m_add;
                            } else {
                                methode = 'PUT';
                                var url = m_edit;
                            }

                            form.submit({
                                //url: m_crud+'?'+ Ext.urlEncode(params),
                                url: url,
                                params: {
                                    coaIDAsset: Ext.getCmp('coaIDAsset').getValue(),
                                    coaIDAkumDepres: Ext.getCmp('coaIDAkumDepres').getValue(),
                                    coaIDBebanDepres: Ext.getCmp('coaIDBebanDepres').getValue()
                                },
                                method: methode,
                                waitMsg: 'Sending data...',
                                success: function (fp, o) {
                                        Ext.MessageBox.alert('Success', 'Data saved.');
                                        Ext.getCmp('WFormInventory').hide(this, function () {
                                            store.load();
                                        });
                                    },
                                    failure: function (form, action) {
                                        var d = Ext.decode(action.response.responseText);
                                         Ext.MessageBox.alert('Failed', d.message);
                                    }
                                });

                        } else {
                            Ext.Msg.alert("Error!", "Your form is invalid!");
                        }
                    }
                }, {
                    text: 'Close',
                    margin: '5px',
                    scale: 'large',
                    ui: 's-button',
                    cls: 's-grey',
                    disabled: false,
                    handler: function () {
                        Ext.getCmp('WFormInventory').hide();
                    }
                }
            ]
        });
    }
    win.show();
    readOnlyFieldInventory(true);

                                //alert(id);
    var WFormInventory = Ext.getCmp('FormInventory');
//                                var formProductType = Ext.ComponentQuery.query('formProductType')[0];

    WFormInventory.getForm().load({
        url: m_data,
        method: 'GET',
        params: {
            id: id
        },
        success: function (form, action) {
            var d = Ext.decode(action.response.responseText);
//                                        console.log(d.data);
//                                        Ext.getCmp('loanTypeInterestDurationMargin').setValue(d.data.loanTypeInterestDuration * 1);
             Ext.getCmp('fotoinvthumb').el.dom.src = d.data.Images;

             Ext.getCmp('comboxCatInv').setValue(d.data.idinventorycat*1);

             Ext.getCmp('EvaluateSoldPrice').hide();

             Ext.getCmp('coaIDAsset').setValue(d.data.coaIDAsset);
             Ext.getCmp('coaNameAsset').setValue(d.data.coaNameAsset);
             Ext.getCmp('coaIDAkumDepres').setValue(d.data.coaIDAkumDepres);
             Ext.getCmp('coaNameAkumDepres').setValue(d.data.coaNameAkumDepres);
             Ext.getCmp('coaIDBebanDepres').setValue(d.data.coaIDBebanDepres);
             Ext.getCmp('coaNameBebanDepres').setValue(d.data.coaNameBebanDepres);

              if(d.data.IsRemoved*1==1)
             {
                // Ext.getCmp('EvaluateReasonLabel').show();
                Ext.getCmp('EvaluateReason').show();
                // Ext.getCmp('btnRemoveInventory').setText('Removed');
             } else {
                // Ext.getCmp('EvaluateReasonLabel').hide();
                Ext.getCmp('EvaluateReason').hide();
                // Ext.getCmp('btnRemoveInventory').setText('Remove');
             }


        },
        failure: function (form, action) {
            Ext.Msg.alert("Load failed", action.result.errorMessage);
        }
    });
}

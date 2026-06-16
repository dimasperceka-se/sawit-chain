Ext.onReady(function () {

    Ext.tip.QuickTipManager.init();

    var store = Ext.create('Ext.data.Store', {
        storeId: 'loanStore',
        autoLoad:true,
        fields:['loan','term', 'from', 'date', 'amount', 'due', 'installment', 'interest'],
        data:{'items':[
            { 'term': '1',  "from":"Cash",  "loan":"5.0", "amount":"1.000.000", "interest":"500.000", "installment":"1.500.000", "date": "01/04/2015", "due": "05/04/2015" },
            { 'term': '2',  "from":"Cash",  "loan":"4.0", "amount":"1.000.000", "interest":"500.000", "installment":"1.500.000", "date": "01/05/2015", "due": "05/05/2015" },
            { 'term': '3',  "from":"Cash",  "loan":"3.0", "amount":"1.000.000", "interest":"500.000", "installment":"1.500.000", "date": "01/06/2015", "due": "05/06/2015" },
            { 'term': '4',  "from":"Cash",  "loan":"2.0", "amount":"1.000.000", "interest":"500.000", "installment":"1.500.000", "date": "01/07/2015", "due": "05/07/2015" },
            { 'term': '5',  "from":"Cash",  "loan":"1.0", "amount":"1.000.000", "interest":"500.000", "installment":"1.500.000", "date": "01/08/2015", "due": "05/08/2015" }

        ]},
        proxy: {
            type: 'memory',
            reader: {
                type: 'json',
                root: 'items'
            }
        }
    });
    
    var mc_district = Ext.create('Ext.data.Store', {
        extend: 'Ext.data.Model',
        fields: ['id', 'label'],
        autoLoad: true,
        pageSize: 10,
        proxy: {
            type: 'ajax',
            url: m_district,
            reader: {
                type: 'json',
                root: 'data'
            }
        }
    });
    var mc_subdistrict = Ext.create('Ext.data.Store', {
        extend: 'Ext.data.Model',
        fields: ['id', 'label'],
        autoLoad: false,
        pageSize: 10,
        proxy: {
            type: 'ajax',
            url: m_subdistrict,
            reader: {
                type: 'json',
                root: 'data'
            }
        }
    });
    var mc_village = Ext.create('Ext.data.Store', {
        extend: 'Ext.data.Model',
        fields: ['id', 'label'],
        autoLoad: false,
        pageSize: 10,
        proxy: {
            type: 'ajax',
            url: m_village,
            reader: {
                type: 'json',
                root: 'data'
            }
        }
    });
    
    var grid = Ext.create('Ext.grid.Panel', {
        width: '100%',
        loadMask: true,
        style:'border: 1px solid #CCCCCC',
        store: Ext.data.StoreManager.lookup('loanStore'),
        columns: [
            { text: 'No',  dataIndex: 'term', width:50 },
            { text: 'Photo', dataIndex: 'term', width:120, xtype:'templatecolumn', tpl: '<img src="" style="height:100px; width:100px;">' },
            { text: 'Member', dataIndex: 'term', flex:true, xtype:'templatecolumn', tpl: '<div style="font-weight:bold; font-size: 1.2em; vertical-align:top;">Zainnudin Usnur</div><div><i class="fa fa-map"></i><strong class="jsCountry">Alur Sungai Pinang</strong><span class="text-muted"> - <span class="jsTests"> Jeumpa </span></span></span></div>' },
            { text: 'Overall Score', dataIndex: 'loan', width:100, align:"center", xtype:'templatecolumn', tpl:'<div style="font-weight:bold; font-size: 24px;">{loan}</div>' }
        ],
        listeners:{
            itemdblclick: function(grid, record, item, index, e, eOpts) {
                
                var store_loan = Ext.create('Ext.data.Store', {
                    extend: 'Ext.data.Model',
                    fields: [],
                    autoLoad: true,
                    pageSize: 50,
                    proxy: {
                        type: 'ajax',
                        url: m_crud + "s",
                        reader: {
                            type: 'json',
                            root: 'data',
                            totalProperty: 'total'
                        }
                    }
                });

                var DataDetail = Ext.create('Ext.form.Panel', {
                    autoScroll: true,
                    bodyPadding: 5,
                    id: 'DataDetail',
                    fieldDefaults: {
                        labelAlign: 'left',
                        labelWidth: 120,
                        anchor: '95%'
                    },
                    items: [{
                            layout: 'column',
                            border: false,
                            items: [{
                                    xtype: 'fieldset',
                                    title: lang('Data Umum'),
                                    columnWidth: .5,
                                    layout: 'form',
                                    margin: '5px',
                                    items: [{
                                            xtype: 'displayfield',
                                            fieldLabel: 'No. Member',
                                            id: 'primaryNoDetail'
                                        },
                                        {
                                            xtype: 'displayfield',
                                            fieldLabel: 'Nama Member',
                                            id: 'nameDetail'
                                        },
                                        {
                                            xtype: 'displayfield',
                                            fieldLabel: lang('Member Type'),
                                            id: 'typeIDDetail'
                                        },
                                        {
                                            xtype: 'fieldcontainer',
                                            fieldLabel: lang('Identitas'),
                                            layout: 'hbox',
                                            items: [{
                                                    xtype: 'displayfield',
                                                    id: 'identityTypeDetail'
                                                }, {
                                                    xtype: 'displayfield',
                                                    id: 'identityNumberDetail'
                                                }]
                                        },
                                        {
                                            xtype: 'displayfield',
                                            fieldLabel: lang('Gender'),
                                            id: 'genderDetail',
                                            renderer: function(value) {
                                                if (value == '1') {
                                                    return lang('Male');
                                                } else {
                                                    return lang('Female');
                                                }
                                            }
                                        },
                                        {
                                            xtype: 'fieldcontainer',
                                            fieldLabel: 'Tempat, Tgl. Lahir',
                                            layout: 'hbox',
                                            items: [{
                                                    xtype: 'displayfield',
                                                    id: 'placeOfBirthDetail',
                                                    margin: '0 5 0 0'
                                                }, {
                                                    xtype: 'displayfield',
                                                    id: 'dateOfBirthDetail'
                                                }]
                                        },
                                        {
                                            xtype: 'displayfield',
                                            fieldLabel: 'Address',
                                            id: 'addressDetail'
                                        },
                                        {
                                            xtype: 'fieldcontainer',
                                            fieldLabel: 'Village',
                                            layout: 'hbox',
                                            items: [{
                                                    xtype: 'displayfield',
                                                    id: 'districtIDDetail'
                                                }, {
                                                    xtype: 'displayfield',
                                                    id: 'subdistrictIDDetail'
                                                }, {
                                                    xtype: 'displayfield',
                                                    id: 'villageIDDetail'
                                                }]
                                        },
                                        {
                                            xtype: 'displayfield',
                                            fieldLabel: 'Phone',
                                            id: 'phoneDetail'
                                        },
                                        {
                                            xtype: 'displayfield',
                                            fieldLabel: lang('Marital Status'),
                                            id: 'maritalStatusDetail',
                                            renderer: function(value) {
                                                if (value == '1') {
                                                    return lang('Lajang');
                                                } else if (value == '2') {
                                                    return lang('Menikah');
                                                } else if (value == '3') {
                                                    return lang('Cerai');
                                                }
                                            }
                                        },
                                        {
                                            xtype: 'displayfield',
                                            fieldLabel: lang('Education'),
                                            id: 'educationDetail',
                                            renderer: function(value) {
                                                if (value == '1') {
                                                    return lang('Belum Pernah Sekolah');
                                                } else if (value == '2') {
                                                    return lang('Tidak Tamat SD');
                                                } else if (value == '3') {
                                                    return lang('Tamat SD Tidak Dilanjutkan');
                                                } else if (value == '4') {
                                                    return lang('Tamat SMP');
                                                } else if (value == '5') {
                                                    return lang('Tamat SMK/SMA');
                                                } else if (value == '6') {
                                                    return lang('Tamat Perguruan Tinggi');
                                                }
                                            }

                                        }]
                                }, {
                                    xtype: 'fieldset',
                                    title: lang('Photo'),
                                    columnWidth: .5,
                                    layout: {
                                        align: 'center'
                                    },
                                    padding: 5,
                                    items: [{
                                            xtype: 'image',
                                            id: 'photoDetail',
                                            height: '100px'
                                        }]
                                }, {
                                    xtype: 'fieldset',
                                    title: lang('Current Balance'),
                                    columnWidth: .5,
                                    layout: 'form',
                                    padding: 5,
                                    items: [{
                                            xtype: 'displayfield',
                                            fieldLabel: lang('Simpanan Pokok'),
                                            id: 'simpananPokok'
                                        }, {
                                            xtype: 'displayfield',
                                            fieldLabel: lang('Simpanan Wajib'),
                                            id: 'simpananWajib'
                                        }, {
                                            xtype: 'displayfield',
                                            fieldLabel: lang('Simpanan Sukarela'),
                                            id: 'simpananSukarela'
                                        }]
                                }, {
                                    xtype: 'fieldset',
                                    title: lang('Farmer Data'),
                                    columnWidth: .5,
                                    layout: 'fit',
                                    padding: 5,
                                    items: []
                                }]
                        }, {
                            xtype: 'gridpanel',
                            id: 'grid_loan',
                            title: lang('Loan Activity'),
                            store: store_loan,
                            style: 'border:1px solid #CCC;',
                            width: '100%',
                            minHeight: 350,
                            loadMask: true,
                            selType: 'rowmodel',
                            listeners: {},
                            dockedItems: [{
                                    xtype: 'pagingtoolbar',
                                    store: store_loan, // same store GridPanel is using
                                    dock: 'bottom',
                                    displayInfo: true
                                }],
                            columns: [{
                                    text: 'ID',
                                    dataIndex: 'id',
                                    hidden: true
                                }, {
                                    text: 'No',
                                    xtype: 'rownumberer',
                                    width: '5%'
                                }, {
                                    text: lang('Transaction Number'),
                                    width: '25%',
                                    dataIndex: ''
                                }, {
                                    text: lang('Transaction Date'),
                                    width: '20%',
                                    dataIndex: ''
                                }, {
                                    text: lang('Transaction Type'),
                                    width: '15%',
                                    dataIndex: ''
                                }, {
                                    text: lang('Transaction Amount'),
                                    width: '15%',
                                    dataIndex: ''
                                }, {
                                    text: lang('Transaction Remark'),
                                    width: '15%',
                                    dataIndex: ''
                                }]
                        }],
                    buttons: [{
                            text: 'Close',
                            margin: '5px',
                            scale: 'large',
                            ui: 's-button',
                            cls: 's-grey',
                            disabled: false,
                            handler: function() {
                                winDetail.close();
                            }
                        }]
                });
    
                var winDetail = Ext.create('widget.window', {
                    title: 'Detail Member',
                    closable: true,
                    id: 'winDetail',
                    modal: true,
                    width: '90%',
                    minWidth: 1000,
                    height: '90%',
                    layout: {
                        type: 'fit'
                    },
                    items: [DataDetail]
                }).show();
            }
        },
        height: 550,
        renderTo: 'ext-content',
        dockedItems:[
            {
                xtype: 'pagingtoolbar',
                dock: 'bottom',
                store: store,
                displayInfo: true,
                displayMsg: 'Displaying topics {0} - {1} of {2}',
                emptyMsg: "No topics to display"
            },{
                xtype:'form',
                layout:{
                    type:'table',
                    columns:7
                },
                defaults:{
                    margin:'0 5',
                    labelAlign:'top'
                },
                id:'loan-simulator',
                padding:5,
                items:[
                    {
                        xtype:'textfield',
                        fieldLabel:'Member No.'
                    },
                    {
                        xtype: 'combo',
                        fieldLabel: 'Member Type <b style="color:red">*</b>',
                        allowBlank: false,
                        width:200,
                        store: Ext.create('Ext.data.Store', {
                            fields: ['typeID', 'typeName'],
                            autoLoad: true,
                            proxy: {
                                type: 'rest',
                                url: m_crud + '/common/getcombo', // url that will load data with respect to start and limit params
                                extraParams: {
                                    table: 'coop_member_type_id',
                                    name: 'typeID',
                                    id: 'typeName'
                                },
                                reader: {
                                    type: 'json',
                                    root: 'data',
                                    totalProperty: 'total'
                                }
                            }
                        }),
                        displayField: 'typeName',
                        valueField: 'typeID',
                        name: 'typeID'
                    },
                    {
                        id: 'districtID',
                        name: 'districtID',
                        xtype: 'combo',
                        fieldLabel:'District',
                        emptyText: '-- District --',
                        multiSelect: false,
                        store: mc_district,
                        displayField: 'label',
                        valueField: 'id',
                        margin: '0 5 0 0',
                        queryMode: 'local',
                        listeners: {
                            change: function(cb, nv, ov) {
                                mc_subdistrict.load({params: {district: Ext.getCmp('districtID').getValue()}});
                            }
                        }
                    }, {
                        id: 'subdistrictID',
                        name: 'subdistrictID',
                        xtype: 'combo',
                        fieldLabel:'Sub District',
                        emptyText: '-- Subdistrict --',
                        multiSelect: false,
                        store: mc_subdistrict,
                        displayField: 'label',
                        valueField: 'id',
                        margin: '0 5 0 0',
                        queryMode: 'local',
                        listeners: {
                            change: function(cb, nv, ov) {
                                mc_village.load({params: {sub_district: Ext.getCmp('subdistrictID').getValue()}});
                            }
                        }
                    }, {
                        id: 'villageID',
                        name: 'villageID',
                        xtype: 'combo',
                        fieldLabel:'Village',
                        emptyText: '-- Village --',
                        multiSelect: false,
                        store: mc_village,
                        displayField: 'label',
                        valueField: 'id',
                        queryMode: 'local',
                        listConfig:{
                            width:250
                        }
                    },
                    {
                        xtype:'button',
                        text:'Search',
                        margin: '30 58 5 58',
                        scale: 'large',
                        ui: 's-button',
                        cls: 's-blue ',
                        buttonAlign: 'left',
                        handler: function() {
                            
                        }
                    }
                ]
            }
        ]
    });

});

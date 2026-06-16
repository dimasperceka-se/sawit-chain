    
Ext.Loader.setConfig({enabled: true});
Ext.Loader.setPath('js/ext-4.2.0.663', '../ux');
//Ext.Loader.setPath('js/ext-4.2.0.663/ux/form');
Ext.require([
    //'Ext.form.Panel',
    //'Ext.ux.form.MultiSelect',
    'Ext.ux.form.ItemSelector'
]);

Ext.onReady(function () {

Ext.tip.QuickTipManager.init();

var storeInterestDuration = new Ext.data.ArrayStore({
    fields: ['loanTypeInterestDuration','loanTypeInterestDurationName'],
    data: [[1,'Per Month'], [2,'Per Year'], [3,'One Time']]
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


var storeMemberTypeList = Ext.create('Ext.data.ArrayStore', {
        fields: [{
            name:'id',
            type:'int'
        },{
            name:'typeName',
            type:'string'
        }],
        proxy: {
            type: 'ajax',
            url: m_membertype_list,
            reader: {
                type:'json',
                root:'data'
            }
        },
        autoLoad: true
    });

Ext.define('FormLoanProductType', {
    extend: 'Ext.form.Panel',
    id:'formProductType',
    alias: 'widget.FormLoanProductType',
    initComponent: function() {
        var frm = this;
        frm.bodyStyle = 'padding:5px';
        frm.width = 950;
        frm.height = 500;
        frm.fieldDefaults = {
            msgTarget: 'side',
            blankText: 'Tidak Boleh Kosong',
            labelWidth: 130,
            width:460
        };
        frm.items = [
            {
                layout: 'hbox',
                defaults: {
                    padding: '5 10 5 5',
                    width:'50%',
                },
                items: [
                    {
                        items: [
                                {
                                    xtype:'hiddenfield',
                                    id:'loanTypeID',
                                    name:'LoanTypeID'
                                },
                                 {
                                    xtype: 'fieldcontainer',	
                                    hidelabel:true,
                                    allowBlank:false,
                                    combineErrors: true,
                                    msgTarget : 'side',
                                    items: [
                                        {
                                            xtype: 'checkbox',
                                            hidelabel:true,
                                            boxLabel:'Activated',
                                            id:'activeStatusLoanType',
                                            allowBlank:false,
                                            margin: '0 5 0 135',
//                                            fieldLabel: ' ',
                                            name:'active'
                                        }
                                    ]
                                },
                                 {
                                    xtype: 'fieldcontainer',											
                                    allowBlank:false,
                                    fieldLabel: 'Product Type',
                                    combineErrors: true,
                                    msgTarget : 'side',
                                    layout: 'hbox',
                                    items: [
                                        {
    //                                    labelWidth: 110,
                                            xtype: 'radio',
                                            boxLabel: 'Islamic Finance',
                                            name: 'loanTypeCode',
                                            id:'loanTypeCode',
                                            disabled: true,
                                            width: 202,
                                            allowBlank: false,
                                            inputValue:1,
                                            listeners: {
                                                change: function(field, newValue, oldValue) {
                                                      if(newValue)
                                                      {
                                                          Ext.getCmp('contIntRate').hide();
                                                          Ext.getCmp('contIntRate').setDisabled(true);
                                                          Ext.getCmp('loanTypeInterestAmount').setDisabled(true);
                                                          Ext.getCmp('loanTypeInterestAmount').allowBlank=true;
                                                          
                                                          Ext.getCmp('comboxIntType').hide();
                                                          Ext.getCmp('comboxIntType').setDisabled(true);
                                                          
                                                          Ext.getCmp('fcFixMargin').show();
                                                          Ext.getCmp('loanTypeInterestAmountMargin').setDisabled(false);
                                                          Ext.getCmp('loanTypeInterestDurationMargin').setDisabled(false);
                                                          Ext.getCmp('fcProfitShare').show();
                                                          Ext.getCmp('loanTypeClientSharing').setDisabled(false);
                                                          Ext.getCmp('loanTypeCooperativeSharing').setDisabled(false);
                                                          
                                                      } else {
                                                          Ext.getCmp('contIntRate').show();
                                                          Ext.getCmp('contIntRate').setDisabled(false);
                                                          Ext.getCmp('loanTypeInterestAmount').setDisabled(false);
                                                          Ext.getCmp('loanTypeInterestAmount').allowBlank=false;
                                                          Ext.getCmp('comboxIntType').show();
                                                          Ext.getCmp('comboxIntType').setDisabled(false);
                                                          
                                                          Ext.getCmp('fcFixMargin').hide();
                                                          
                                                          Ext.getCmp('loanTypeInterestAmountMargin').setDisabled(true);
                                                          Ext.getCmp('loanTypeInterestDurationMargin').setDisabled(true);
                                                          Ext.getCmp('fcProfitShare').hide();
                                                          Ext.getCmp('loanTypeClientSharing').setDisabled(true);
                                                          Ext.getCmp('loanTypeCooperativeSharing').setDisabled(true);
                                                      }
                                                }
                                            }
                                        },  
                                        {
                                            xtype: 'radio',
                                            labelWidth: 202,
                                            boxLabel:'Loan',
                                            name: 'loanTypeCode',
                                            width: 130,
                                            allowBlank: false,
                                            inputValue:2,
                                            listeners: {
                                                change: function(field, newValue, oldValue) {
                                                     // console.log(newValue);
                                                }
                                            }
                                        }
                                    ]
                                 },
                                {
                                    xtype: 'textfield',
                                    allowBlank:false,
                                    fieldLabel: 'Name',
                                    name:'LoanTypeName',
                                    width: 410
                                },{
                                    xtype: 'numberfield',
                                    hideTrigger: true,
                                    allowBlank:false,
                                    fieldLabel: 'Min Amount',
                                    name:'LoanTypeMinAmount',
                                    width: 410
                                },{
                                    xtype: 'numberfield',
                                    hideTrigger: true,
                                    allowBlank:false,
                                    fieldLabel: 'Max Amount',
                                    name:'LoanTypeMaxAmount',
                                    width: 410
                                },
                                {
                                    xtype: 'fieldcontainer',											
                                    allowBlank:false,
                                    fieldLabel:'Tenor',
                                    combineErrors: true,
                                    msgTarget : 'side',
                                    layout: 'hbox',
                                    width: 410,
                                    items: [
                                        {
    //                                    labelWidth: 110,
                                            xtype: 'textfield',
                                            fieldLabel: 'Min',
                                            name: 'LoanTypeMinTenor',
                                            labelWidth: 25,
                                            width: 80,
                                            allowBlank: false
                                        },  {
                                            xtype: 'textfield',
                                            labelWidth: 25,
                                            margin: '0 5 0 20',
                                            fieldLabel:'Max',
                                            name: 'LoanTypeMaxTenor',
                                            width: 80,
                                            allowBlank: false,
                                        }
                                    ]
                                },
                                {
                                    xtype: 'fieldcontainer',                                            
                                    allowBlank:false,
                                    fieldLabel: 'Grace Periode',
                                    combineErrors: true,
                                    msgTarget : 'side',
                                    layout: 'hbox',
                                    width: 410,
                                    defaults: {
                                            hideLabel: true
                                    },
                                    items: [{
                                                xtype: 'numberfield',
                                                name:'LoanTypeGracePeriod',
                                                id:'periodePengajuan',
                                                width:'30%'
                                        }, {
                                                xtype: 'displayfield',
                                                margin: '0 5 0 5',
                                                value: '/ Month'
                                        }
                                    ]
                                },
                                {
                                xtype: 'fieldcontainer',
                                id:'fcFixMargin',
                                hidden:true,
                                msgTarget : 'side',
                                layout: 'hbox',
                                width: 410,
                                items: [
                                    {
                                        xtype: 'radio',
                                        // allowBlank:false,
                                        boxLabel: 'Fixed Margin',
                                        width: 132,
                                        name:'LoanTypeProfitType',
                                        id:'loanTypeProfitTypeMargin',
                                        inputValue:2,
                                        listeners: {
                                            change: function(field, newValue, oldValue) {
                                                  if(newValue)
                                                  {
                                                      Ext.getCmp('loanTypeClientSharing').setDisabled(true);
                                                      Ext.getCmp('loanTypeCooperativeSharing').setDisabled(true);
                                                      
                                                      Ext.getCmp('loanTypeInterestAmount').setDisabled(false);
                                                      Ext.getCmp('loanTypeInterestAmountMargin').setDisabled(false);
                                                      Ext.getCmp('loanTypeInterestDurationMargin').setDisabled(false);
                                                  } else {
                                                      Ext.getCmp('loanTypeClientSharing').setDisabled(false);
                                                      Ext.getCmp('loanTypeCooperativeSharing').setDisabled(false);
                                                      
                                                      Ext.getCmp('loanTypeInterestAmount').setDisabled(true);
                                                      Ext.getCmp('loanTypeInterestAmountMargin').setDisabled(true);
                                                      Ext.getCmp('loanTypeInterestDurationMargin').setDisabled(true);
                                                  }
                                            }
                                        }
                                    },
                                    {
                                        xtype: 'textfield',
                                        labelWidth: 80,
                                        id: 'loanTypeInterestAmountMargin',
                                        name: 'LoanTypeInterestAmount',
                                        width: 80,
                                        // allowBlank: false,
                                    },
                                    {
                                        xtype: 'displayfield',
                                        margin: '0 5 0 5',
                                        labelWidth: 50,
                                        width: 12,
                                        value: '%'
                                    }, {
                                        xtype: 'comboxInterestDuration',
//                                        emptyText:'Per Month',
                                        id: 'loanTypeInterestDurationMargin',
                                        name: 'LoanTypeInterestDuration',
                                        width: 160,
                                        // allowBlank: false,
                                    }
                                ]
                             },
                             {
                                xtype: 'fieldcontainer',
                                hidden:true,
                                id:'fcProfitShare',
                                msgTarget : 'side',
                                layout: 'hbox',
                                items: [
                                    {
                                        xtype: 'radio',
                                        // allowBlank:false,
                                        boxLabel: 'Profit Sharing',
                                        width: 132,
                                        name:'LoanTypeProfitType',
                                        id:'loanTypeProfitTypeSharing',
                                        inputValue:1
                                    },
                                    {
                                        xtype: 'textfield',
                                        fieldLabel:'Client',
                                        labelWidth: 40,
                                        name: 'LoanTypeClientSharing',
                                        id: 'loanTypeClientSharing',
                                        width: 120,
                                        // allowBlank: false
                                    },
                                    {
                                        xtype: 'displayfield',
                                        margin: '0 5 0 5',
                                        labelWidth: 50,
                                        width: 12,
                                        value: '%'
                                    },
                                    {
                                        xtype: 'textfield',
                                        fieldLabel:'Coop',
                                        labelWidth: 40,
                                        name: 'LoanTypeCooperativeSharing',
                                        id: 'loanTypeCooperativeSharing',
                                        width: 120,
                                        // allowBlank: false,
                                    },
                                    {
                                        xtype: 'displayfield',
                                        margin: '0 0 0 5',
                                        labelWidth: 50,
                                        width: 12,
                                        value: '%'
                                    },{
                                        xtype: 'fieldcontainer',                                            
                                        // allowBlank:false,
                                        fieldLabel: 'Grace Periode',
                                        combineErrors: true,
                                        msgTarget : 'side',
                                        layout: 'hbox',
                                        width: 410,
                                        defaults: {
                                                hideLabel: true
                                        },
                                        items: [{
                                                    xtype: 'numberfield',
                                                    name:'LoanTypeGracePeriod',
                                                    id:'periodePengajuan',
                                                    width:'20%'
                                            }, {
                                                    xtype: 'displayfield',
                                                    margin: '0 5 0 5',
                                                    value: '/ Month'
                                            }
                                        ]
                                     }
                                ]
                             }
                        ]
                    },
                     {
                        items: [
                            {
                                xtype: 'combo',
                                // hidden:true,
                                fieldLabel: 'Interest Type',
                                allowBlank: false,
                                width:300,
                                store: Ext.create('Ext.data.Store', {
                                    fields: ['interestTypeID', 'interestTypeName'],
                                    autoLoad: true,
                                    proxy: {
                                        type: 'rest',
                                        url: m_crud + '/common/getcombo', // url that will load data with respect to start and limit params
                                        extraParams: {
                                            table: 'coop_interest_type',
                                            name: 'interestTypeName',
                                            id: 'interestTypeID'
                                        },
                                        reader: {
                                            type: 'json',
                                            root: 'data',
                                            totalProperty: 'total'
                                        }
                                    }
                                }),
                                listeners:{
                                    select: function(c,v){
//                                        var id = v[0].get('loanTypeID');
//
//                                        Ext.getCmp('frm-loan').getForm().load({
//                                            url:m_crud + '/loan/getLoanType',
//                                            method:'GET',
//                                            params:{
//                                                id:id
//                                            }
//                                        });
                                    }
                                },
                                displayField: 'interestTypeName',
                                valueField: 'interestTypeID',
                                id:'comboxIntType',
                                name: 'InterestTypeID'
                            },
                            {
                                xtype: 'container',
                                id:'contIntRate',
                                // hidden:true,
                                layout: 'hbox',
                                margin: '0 0 5 0',
                                items: [{
//                                  labelWidth: 110,
                                    xtype: 'textfield',
                                    fieldLabel: 'Interest Rate',
                                    name: 'LoanTypeInterestAmount',
                                    id: 'loanTypeInterestAmount',
                                    width: 252,
                                    allowBlank: false
                                }, 
                                {
                                    xtype: 'displayfield',
                                    labelWidth: 50,
                                    margin: '0 5 0 5',
                                    width: 12,
                                    value: '%'
                                }, 
                                {
                                    xtype: 'comboxInterestDuration',
                                    margin: '0 5 0 5',
//                                  emptyText:'Per Month',
                                    name: 'LoanTypeInterestDuration',
                                    id: 'loanTypeInterestDuration',
                                    width: 160,
                                    allowBlank: false,
                                }
                                ]
                            },
                            {
                                xtype: 'textfield',
                                allowBlank:false,
                                fieldLabel: 'Administration Fee',
                                name:'LoanTypeFee',
                                width: 400
                            },
                            {
                                xtype: 'fieldcontainer',											
                                allowBlank:false,
                                combineErrors: true,
                                msgTarget : 'side',
                                layout: 'hbox',
                                items: [
                                    {
                                        xtype: 'textfield',
                                        fieldLabel: 'Due Date Penalty',
                                        name: 'LoanTypePenaltyPercent',
                                        width: 195,
                                        // allowBlank: false
                                    }, 
                                    {
                                        xtype: 'displayfield',
                                        margin: '0 5 0 7',
                                        labelWidth: 50,
                                        width: 8,
                                        value: '%'
                                    }, {
                                        xtype: 'textfield',
                                        margin: '0 0 0 10',
                                        labelWidth: 75,
                                        fieldLabel:'Fix Amount',
                                        name: 'LoanTypePenaltyAmount',
                                        billingFieldName: 'billingPostalCode',
                                        width: 215,
                                        // allowBlank: false,
                                    }
                                ]
                             },
                             {
                                xtype: 'combo',
                                fieldLabel: lang('Cashbox'),
                                allowBlank: false,
                                width:300,
                                store: Ext.create('Ext.data.Store', {
                                    fields: ['cashSourceID', 'cashSourceName'],
                                    autoLoad: true,
                                    proxy: {
                                        type: 'rest',
                                        url: m_crud + '/common/getcombo', // url that will load data with respect to start and limit params
                                        extraParams: {
                                            table: 'coop_cash_source',
                                            name: 'cashSourceName',
                                            id: 'cashSourceID'
                                        },
                                        reader: {
                                            type: 'json',
                                            root: 'data',
                                            totalProperty: 'total'
                                        }
                                    }
                                }),
                                listeners:{
                                    select: function(c,v){
                                    }
                                },
                                displayField: 'cashSourceName',
                                valueField: 'cashSourceID',
                                name: 'CashSourceID'
                            },
                            {
                                    xtype: 'datefield',
                                    width:300,
                                    allowBlank:false,
                                    emptyText:'DD-MM-YYY',
                                    format: 'd-m-Y',
                                    name:'LoanTypeActiveDate',
                                    fieldLabel: 'Active Since'
                            },
                            {
                                xtype: 'textarea',
                                // allowBlank:false,
                                fieldLabel: 'Remark',
                                name:'LoanTypeRemark',
                                width: 400,
                            }
                        ]
                    }
                ]
            },
            {
                xtype: 'itemselector',
                name: 'itemselector',
               // fieldLabel:'Select roles',
                id: 'itemselector-membertypeloan',
                anchor: '100%',
                height:200,
                store: storeMemberTypeList,
                displayField: 'typeName',
                valueField: 'id',
//                value: [],
                allowBlank: true,
                msgTarget: 'side',
                fromTitle: 'Available Member Type',
                toTitle: 'Selected Member Type'
            }
        ];
//        frm.buttons = ['->', 
//            {
//                text: 'Save',
//                margin: '5px',
//                scale: 'large',
//                ui: 's-button',
//                cls: 's-blue',
//                handler: function() {
//                    var form = this.up('form').getForm();
//                    if (form.isValid()) {
//                         //var params = Ext.getCmp('menuu').getValue();
//                        var methode;
//                        if (Ext.getCmp('loanTypeID').getValue()=='') 
//                        {
//                            methode = 'POST';
//                            var url =  m_crud+'/loan/addloantype';
//                        } else {
//                            methode = 'PUT';
//                            var url =  m_crud+'/loan/editloantype';
//                        }
//
//                        form.submit({
//                            //url: m_crud+'?'+ Ext.urlEncode(params),
//                            url: url,
//                            method : methode,
//                            waitMsg: 'Sending data...',
//                            success: function(fp, o) {
//                                Ext.MessageBox.alert('Success', 'Data saved.');
//                            }
//                        });
//
//                        Ext.getCmp('WFormProductType').hide(this, function() {
//                            store.load();
//                        });
//                    } else {
//                         Ext.Msg.alert("Error!", "Your form is invalid!");
//                    }               
//                }
//            },{
//                text: 'Close',
//                margin: '5px',
//                scale: 'large',
//                ui: 's-button',
//                cls: 's-grey',
//                disabled: false,
//                handler: function() {
//                    Ext.getCmp('WFormProductType').hide();
//                }
//            }
//        ];

        frm.callParent();
    },
    afterRender: function()
    {
        this.superclass.afterRender.apply(this);
        this.doLayout();
    }
});

    var store = Ext.create('Ext.data.Store', {
        storeId: 'loanStore',
        autoLoad:true,
        fields: ['loanTypeID', 'loanTypeName', 'loanTypeMinAmount', 'loanTypeMaxAmount', 'tenorRange', 'interestRate', 'interestTypeName'],
        proxy: {
            type: 'rest',
            url: m_crud + '/loan/getdatatype', // url that will load data with respect to start and limit params
            reader: {
                type: 'json',
                root: 'data',
                totalProperty: 'total',
                idProperty: 'loanTypeID'
            },
            writer: {
                type: 'json'
            },
            api: {
                destroy: m_crud + '/loan/deletetype'
            },
            appendId: true
        }
    });

    var grid = Ext.create('Ext.grid.Panel', {
        width: '100%',
        loadMask: true,
        style:'border: 1px solid #CCCCCC',
        store: Ext.data.StoreManager.lookup('loanStore'),
        columns: [
            {text: 'Loan Name', dataIndex: 'loanTypeName', flex: 1},
            {text: 'Min Amount', dataIndex: 'loanTypeMinAmount', width: 170, 
                renderer:  Ext.util.Format.numberRenderer('0,000'),
                align:'right',
                xtype:'numbercolumn'
            },
            {text: 'Max Amount', dataIndex: 'loanTypeMaxAmount', width: 170, 
                renderer:  Ext.util.Format.numberRenderer('0,000'),
                align:'right',
                xtype:'numbercolumn'
            },
            {text: 'Tenor', dataIndex: 'tenorRange', width: '9%'},
            {text: 'Interest Type', dataIndex: 'interestTypeName', width: 130},
            {text: 'Interest Rate', dataIndex: 'interestRate', width: 170}
        ],
        height: 500,
        renderTo: 'ext-content',
        dockedItems:[
            {
                xtype: 'pagingtoolbar',
                dock: 'bottom',
                store: store,
                displayInfo: true,
                displayMsg: 'Displaying item(s) {0} - {1} of {2}',
                emptyMsg: "No item to display"
            },{
                xtype:'toolbar',
                dock:'top',
                items:[
                    {
                        xtype:'button',
                        iconCls:'add',
                        text:'Add',
                        handler:function(){
                            var win = Ext.getCmp('WFormProductType');

                            if (!win) {
                                win = new Ext.Window({
                                    id: 'WFormProductType',
                                    title:'Form Loan Type',
                                    resizable    : false,
                                    plain        : true,
                                    modal        : true,
                                    items        : [
                                        {
                                            xtype:'FormLoanProductType'
                                        }
                                    ],
                                    buttons:[
                                         {
                                                text: 'Save',
                                                margin: '5px',
                                                scale: 'large',
                                                ui: 's-button',
                                                cls: 's-blue',
                                                handler: function() {
                                                    var form = Ext.getCmp('formProductType').getForm();
                                                    if(Ext.getCmp('itemselector-membertypeloan').getValue()=='')
                                                    {
                                                        Ext.Msg.alert("Error!", "Jenis Anggota/Member Type belum dipilih!");
                                                    } else  if (form.isValid()) {
                                                            //var params = Ext.getCmp('menuu').getValue();
                                                           var methode;
                                                           if (Ext.getCmp('loanTypeID').getValue()=='') 
                                                           {
                                                               methode = 'POST';
                                                               var url =  m_crud+'/loan/addloantype';
                                                           } else {
                                                               methode = 'PUT';
                                                               var url =  m_crud+'/loan/editloantype';
                                                           }

                                                           form.submit({
                                                               //url: m_crud+'?'+ Ext.urlEncode(params),
                                                               url: url,
                                                               method : methode,
                                                               waitMsg: 'Sending data...',
                                                               success: function(fp, o) {
                                                                   Ext.MessageBox.alert('Success', 'Data saved.');
                                                               }
                                                           });

                                                           Ext.getCmp('WFormProductType').hide(this, function() {
                                                               store.load();
                                                           });
                                                       } else {
                                                                Ext.Msg.alert("Error!", "Your form is invalid!");
                                                           }               
                                                }
                                            },{
                                                text: 'Close',
                                                margin: '5px',
                                                scale: 'large',
                                                ui: 's-button',
                                                cls: 's-grey',
                                                disabled: false,
                                                handler: function() {
                                                    Ext.getCmp('WFormProductType').hide();
                                                }
                                            }
                                    ]
                                });
                            }
                            win.show();
//                            storeMemberTypeList.load();
                            Ext.getCmp('itemselector-membertypeloan').setValue(null);
                            Ext.getCmp('formProductType').getForm().reset();
                            Ext.getCmp('loanTypeCode').setValue(2);
                            Ext.getCmp('loanTypeProfitTypeMargin').setValue(2);
                            
//                            var win = Ext.create('Ext.Window',{
//                                title:'Add Loan Type',
//                                width:450,
//                                items:[
//                                    Ext.create('Ext.form.Panel' ,{
//                                        id:'frm-loan-type',
//                                        padding:5,
//                                        items: [
//                                            {
//                                                xtype:'hidden',
//                                                name:'loanTypeID'
//                                            },
//                                            {
//                                                xtype:'textfield',
//                                                width:400,
//                                                fieldLabel:'Name <b style="color:red">*</b>',
//                                                name:'loanTypeName'
//                                            },
//                                            {
//                                                xtype:'numberfield',
//                                                hideTrigger:true,
//                                                width:150,
//                                                fieldLabel:'Rate <b style="color:red">*</b>',
//                                                name:'loanTypeInterestRate'
//                                            },
//                                            {
//                                                xtype: 'combo',
//                                                fieldLabel: 'Interest Type <b style="color:red">*</b>',
//                                                allowBlank: false,
//                                                width:300,
//                                                store: Ext.create('Ext.data.Store', {
//                                                    fields: ['interestTypeID', 'interestTypeName'],
//                                                    autoLoad: true,
//                                                    proxy: {
//                                                        type: 'rest',
//                                                        url: m_crud + '/common/getcombo', // url that will load data with respect to start and limit params
//                                                        extraParams: {
//                                                            table: 'coop_interest_type',
//                                                            name: 'interestTypeID',
//                                                            id: 'interestTypeName'
//                                                        },
//                                                        reader: {
//                                                            type: 'json',
//                                                            root: 'data',
//                                                            totalProperty: 'total'
//                                                        }
//                                                    }
//                                                }),
//                                                displayField: 'interestTypeName',
//                                                valueField: 'interestTypeID',
//                                                name: 'interestTypeID'
//                                            },
//                                            {
//                                                xtype:'textfield',
//                                                width:150,
//                                                name:'loanMinTenor',
//                                                fieldLabel:'Min Tenor'
//                                            },
//                                            {
//                                                xtype:'textfield',
//                                                width:150,
//                                                name:'loanMaxTenor',
//                                                fieldLabel:'Max Tenor'
//                                            }
//                                        ]
//                                    })
//                                ],
//                                buttons: [
//                                    {
//                                        xtype:'button',
//                                        text:'Save',
//                                        margin: '5px',
//                                        scale: 'large',
//                                        ui: 's-button',
//                                        cls: 's-blue ',
//                                        buttonAlign: 'left',
//                                        id:'btn-save-frm-loan-type',
//                                        handler:function(c){
//                                            var form = Ext.getCmp('frm-loan-type').getForm();
//                                            var id = form.getValues();
//                                            var url = m_crud + '/loan/addtype';
//                                            var method = 'POST';
//                                            var data_grid = grid.store;
//                                            var detail = [];
//
//                                            data_grid.each(function(value, index, rec){
//                                                detail.push(value.data);
//                                            });
//
//                                            form.submit({
//                                                url: url,
//                                                method: method,
//                                                success: function(f, resp) {
//                                                    Ext.Msg.alert('Success', 'Data successfully saved');
//                                                    win.close();
//                                                    store.loadPage(1);
//                                                },
//                                                failure: function(f, resp) {
//                                                    Ext.Msg.alert('Failed', 'Cannot save data');
//                                                }
//                                            });
//                                        }
//                                    },
//                                    {
//                                        xtype:'button',
//                                        text:'Close',
//                                        id:'btn-cancel-frm-Journal',
//                                        margin: '5px',
//                                        scale: 'large',
//                                        ui: 's-button',
//                                        cls: 's-grey',
//                                        handler:function(){
//                                            var form = Ext.getCmp('frm-loan-type');
//                                            form.getForm().reset();
//                                            win.close();
//                                        }
//                                    }
//                                ],
//                                modal:true
//                            }).show();
                        }
                    },
                    {
                        xtype:'button',
                        iconCls:'edit',
                        text:'Edit',
                        handler:function(){
                            var win = Ext.getCmp('WFormProductType');

                            if (!win) {
                                win = new Ext.Window({
                                    id: 'WFormProductType',
                                    title:'Form Loan Type',
                                    resizable    : false,
                                    plain       : true,
                                    modal       : true,
                                    items        : [
                                        {
                                            xtype:'FormLoanProductType'
                                        }
                                    ],
                                    buttons:[
                                         {
                                                text: 'Save',
                                                margin: '5px',
                                                scale: 'large',
                                                ui: 's-button',
                                                cls: 's-blue',
                                                handler: function() {
                                                    var form = Ext.getCmp('formProductType').getForm();
                                                    if (form.isValid()) {
                                                         //var params = Ext.getCmp('menuu').getValue();
                                                        var methode;
                                                        if (Ext.getCmp('loanTypeID').getValue()=='') 
                                                        {
                                                            methode = 'POST';
                                                            var url =  m_crud+'/loan/addloantype';
                                                        } else {
                                                            methode = 'PUT';
                                                            var url =  m_crud+'/loan/editloantype';
                                                        }

                                                        form.submit({
                                                            //url: m_crud+'?'+ Ext.urlEncode(params),
                                                            url: url,
                                                            method : methode,
                                                            waitMsg: 'Sending data...',
                                                            success: function(fp, o) {
                                                                Ext.MessageBox.alert('Success', 'Data saved.');
                                                            }
                                                        });

                                                        Ext.getCmp('WFormProductType').hide(this, function() {
                                                            store.load();
                                                        });
                                                    } else {
                                                         Ext.Msg.alert("Error!", "Your form is invalid!");
                                                    }               
                                                }
                                            },{
                                                text: 'Close',
                                                margin: '5px',
                                                scale: 'large',
                                                ui: 's-button',
                                                cls: 's-grey',
                                                disabled: false,
                                                handler: function() {
                                                    Ext.getCmp('WFormProductType').hide();
                                                }
                                            }
                                    ]
                                });
                            }
                            win.show();
                            
                            var sm = grid.getSelectionModel();
                            var sel = sm.getSelection();
                            
                            if(sel.length > 0){
                                var id = sel[0].get('loanTypeID');
                                //alert(id);
                                var formProductType = Ext.getCmp('formProductType');
//                                var formProductType = Ext.ComponentQuery.query('formProductType')[0];
                                
                                formProductType.getForm().load({
                                    url: m_crud + '/loan/getTypeLoanByID',
                                    method:'GET',
                                    params: {
                                        id: id
                                    },
                                    success: function(form, action) {
                                        var d = Ext.decode(action.response.responseText);
                                        console.log(d.data);
                                        if(d.data.Active==1)
                                        {
                                            Ext.getCmp('activeStatusLoanType').setValue(1);
                                        } else {
                                            Ext.getCmp('activeStatusLoanType').setValue(null);
                                        }
                                        
                                        if(d.data.LoanTypeCode*1===1)
                                        {

                                            //islamic
                                            Ext.getCmp('loanTypeInterestAmount').setValue(null);
                                            Ext.getCmp('loanTypeInterestAmount').setDisabled(true);
                                            Ext.getCmp('loanTypeInterestDuration').setValue(null);
                                            Ext.getCmp('comboxIntType').setValue(null);
                                            Ext.getCmp('loanTypeCode').setValue(1);
                                            
                                            Ext.getCmp('loanTypeInterestAmountMargin').setValue(d.data.LoanTypeInterestAmount);
                                            Ext.getCmp('loanTypeInterestDurationMargin').setValue(d.data.LoanTypeInterestDuration*1);

                                            if(d.data.LoanTypeProfitType*1==2)
                                            {
                                                //fixed margin
                                                Ext.getCmp('loanTypeInterestAmountMargin').setDisabled(false);
                                                Ext.getCmp('loanTypeInterestDurationMargin').setDisabled(false);

                                                Ext.getCmp('loanTypeClientSharing').setDisabled(true);
                                                Ext.getCmp('loanTypeCooperativeSharing').setDisabled(true);
                                            } else {
                                                //profit sharing
                                                 Ext.getCmp('loanTypeInterestAmountMargin').setDisabled(true);
                                                Ext.getCmp('loanTypeInterestDurationMargin').setDisabled(true);

                                                Ext.getCmp('loanTypeClientSharing').setDisabled(false);
                                                Ext.getCmp('loanTypeCooperativeSharing').setDisabled(false);
                                            }
                                        } else {
                                            Ext.getCmp('loanTypeCode').setValue(2);

                                            Ext.getCmp('contIntRate').show();
                                            Ext.getCmp('contIntRate').setDisabled(false);
                                            Ext.getCmp('loanTypeInterestAmount').setDisabled(false);
                                            Ext.getCmp('comboxIntType').show();
                                            Ext.getCmp('comboxIntType').setDisabled(false);
                                                          
                                            Ext.getCmp('loanTypeInterestAmountMargin').setValue(null);
                                            Ext.getCmp('loanTypeInterestDurationMargin').setValue(null);
                                            
                                            Ext.getCmp('loanTypeInterestAmount').setDisabled(false);
                                            Ext.getCmp('loanTypeInterestAmount').setValue(d.data.LoanTypeInterestAmount);
                                            Ext.getCmp('loanTypeInterestDuration').setValue(d.data.LoanTypeInterestDuration*1);
                                            
//                                            Ext.getCmp('fcFixMargin').hide();
                                            Ext.getCmp('loanTypeInterestAmountMargin').setDisabled(true);
                                            Ext.getCmp('loanTypeInterestDurationMargin').setDisabled(true);
//                                            Ext.getCmp('fcProfitShare').hide();
                                            Ext.getCmp('loanTypeClientSharing').setDisabled(true);
                                            Ext.getCmp('loanTypeCooperativeSharing').setDisabled(true);
                                        }
                                        // console.log(action)
                                        Ext.Ajax.request({
                                            url: m_crud + '/loan/getLoanTypeMemberByID',
                                            method:'GET',
                                            params: {
                                                id: id
                                            },
                                            success: function(form, action) {
                                                var d = Ext.decode(form.responseText);
                                                
                                                Ext.getCmp('itemselector-membertypeloan').setValue(d.data);
                                            },
                                            failure: function(form, action) {
//                                                Ext.Msg.alert("Load failed",Ext.decode(action.responseText));
                                            }
                                        });
                                    },
                                    failure: function(form, action) {
                                             var d = Ext.decode(action.response.responseText);
                                        console.log(d.data);
                                    }
                                })

//                                WFormProductType.show();
//                                var win = Ext.create('Ext.Window',{
//                                    title:'Edit Loan Type',
//                                    width:450,
//                                    items:[
//                                        Ext.create('Ext.form.Panel' ,{
//                                            id:'frm-loan-type',
//                                            padding:5,
//                                            items: [
//                                                {
//                                                    xtype:'hidden',
//                                                    name:'loanTypeID'
//                                                },
//                                                {
//                                                    xtype:'textfield',
//                                                    width:400,
//                                                    fieldLabel:'Name <b style="color:red">*</b>',
//                                                    name:'loanTypeName'
//                                                },
//                                                {
//                                                    xtype:'numberfield',
//                                                    hideTrigger:true,
//                                                    width:150,
//                                                    fieldLabel:'Rate <b style="color:red">*</b>',
//                                                    name:'loanTypeInterestRate'
//                                                },
//                                                {
//                                                    xtype: 'combo',
//                                                    fieldLabel: 'Interest Type <b style="color:red">*</b>',
//                                                    allowBlank: false,
//                                                    width:300,
//                                                    store: Ext.create('Ext.data.Store', {
//                                                        fields: ['interestTypeID', 'interestTypeName'],
//                                                        autoLoad: true,
//                                                        proxy: {
//                                                            type: 'rest',
//                                                            url: m_crud + '/common/getcombo', // url that will load data with respect to start and limit params
//                                                            extraParams: {
//                                                                table: 'coop_interest_type',
//                                                                name: 'interestTypeID',
//                                                                id: 'interestTypeName'
//                                                            },
//                                                            reader: {
//                                                                type: 'json',
//                                                                root: 'data',
//                                                                totalProperty: 'total'
//                                                            }
//                                                        }
//                                                    }),
//                                                    displayField: 'interestTypeName',
//                                                    valueField: 'interestTypeID',
//                                                    name: 'interestTypeID'
//                                                },
//                                                {
//                                                    xtype:'textfield',
//                                                    width:150,
//                                                    name:'loanMinTenor',
//                                                    fieldLabel:'Min Tenor'
//                                                },
//                                                {
//                                                    xtype:'textfield',
//                                                    width:150,
//                                                    name:'loanMaxTenor',
//                                                    fieldLabel:'Max Tenor'
//                                                }
//                                            ]
//                                        })
//                                    ],
//                                    buttons: [
//                                        {
//                                            xtype:'button',
//                                            text:'Save',
//                                            margin: '5px',
//                                            scale: 'large',
//                                            ui: 's-button',
//                                            cls: 's-blue ',
//                                            buttonAlign: 'left',
//                                            id:'btn-save-frm-loan-type',
//                                            handler:function(c){
//                                                var form = Ext.getCmp('frm-loan-type').getForm();
//                                                var id = form.getValues();
//                                                var url = m_crud + '/loan/edittype/'+id.loanTypeID;
//                                                var method = 'PUT';
//                                                var data_grid = grid.store;
//                                                var detail = [];
//
//                                                data_grid.each(function(value, index, rec){
//                                                    detail.push(value.data);
//                                                });
//
//                                                form.submit({
//                                                    url: url,
//                                                    method: method,
//                                                    success: function(f, resp) {
//                                                        Ext.Msg.alert('Success', 'Data successfully saved');
//                                                        win.close();
//                                                        store.loadPage(1);
//                                                    },
//                                                    failure: function(f, resp) {
//                                                        Ext.Msg.alert('Failed', 'Cannot save data');
//                                                    }
//                                                });
//                                            }
//                                        },
//                                        {
//                                            xtype:'button',
//                                            text:'Close',
//                                            id:'btn-cancel-frm-Journal',
//                                            margin: '5px',
//                                            scale: 'large',
//                                            ui: 's-button',
//                                            cls: 's-grey',
//                                            handler:function(){
//                                                var form = Ext.getCmp('frm-loan-type');
//                                                form.getForm().reset();
//                                                win.close();
//                                            }
//                                        }
//                                    ],
//                                    modal:true
//                                }).show();
                                  
                                  
                                
//                                Ext.getCmp('frm-loan-type').getForm().load({
//                                    url:m_crud + '/loan/gettypebyid',
//                                    method:'GET',
//                                    params:{
//                                        id:id
//                                    }
//                                });
                                
                            } else {
                                win.hide();
                                
                                Ext.MessageBox.show({
                                    title: '',
                                    msg: 'Please select data to update',
                                    width: 300,
                                    buttons: Ext.MessageBox.OK,
                                    animateTarget: 'mb3'
                                });
                            }
                            
                        }
                    },
                    {
                        xtype:'button',
                        iconCls:'delete',
                        text:'Delete',
                        handler:function(){
                            
                            var sm = grid.getSelectionModel();
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
                                    grid.store.remove(sel);
                                    grid.store.sync();
                                }
                            }
                        }
                    }
                ]
            }
        ]
    });

});

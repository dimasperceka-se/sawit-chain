Ext.onReady(function () {

    Ext.tip.QuickTipManager.init();

    var store = Ext.create('Ext.data.Store', {
        storeId: 'loanStore',
//        autoLoad:true,
        fields:['loan','term', 'from', 'date', 'amount', 'due', 'installment', 'interest'],
//        data:{'items':[
//            { 'term': '1',  "from":"Cash",  "loan":"5.000.000", "amount":"1.000.000", "interest":"500.000", "installment":"1.500.000", "date": "01/04/2015", "due": "05/04/2015" },
//            { 'term': '2',  "from":"Cash",  "loan":"4.000.000", "amount":"1.000.000", "interest":"500.000", "installment":"1.500.000", "date": "01/05/2015", "due": "05/05/2015" },
//            { 'term': '3',  "from":"Cash",  "loan":"3.000.000", "amount":"1.000.000", "interest":"500.000", "installment":"1.500.000", "date": "01/06/2015", "due": "05/06/2015" },
//            { 'term': '4',  "from":"Cash",  "loan":"2.000.000", "amount":"1.000.000", "interest":"500.000", "installment":"1.500.000", "date": "01/07/2015", "due": "05/07/2015" },
//            { 'term': '5',  "from":"Cash",  "loan":"1.000.000", "amount":"1.000.000", "interest":"500.000", "installment":"1.500.000", "date": "01/08/2015", "due": "05/08/2015" }
//
//        ]},
        proxy: {
            type: 'memory',
            reader: {
                type: 'json',
                root: 'items'
            }
        }
    });

    var grid = Ext.create('Ext.grid.Panel', {
        width: '100%',
        loadMask: true,
        style:'border: 1px solid #CCCCCC',
        store: Ext.data.StoreManager.lookup('loanStore'),
        columns: [
            { text: 'Term',  dataIndex: 'term', width:50 },           
            { text: 'Amount Principal', dataIndex: 'amount', align:'right', width:250 },
            { text: 'Amount Interest', dataIndex: 'interest', align:'right', width:200 },
            { text: 'Amount Installment', dataIndex: 'installment', align:'right', width:250},
            { text: 'Remaining Debt', dataIndex: 'loan', align:'right', width:250 },
            { text: 'Due Date', dataIndex: 'due' }
        ],
        height: 550,
        renderTo: 'ext-content',
        dockedItems:[
           
//            {
//                xtype: 'pagingtoolbar',
//                dock: 'bottom',
//                store: store,
////                displayInfo: true,
//                displayMsg: 'Displaying topics {0} - {1} of {2}',
//                emptyMsg: "No topics to display"
//            },
            {
                xtype:'form',
                anchor:'100%',
                layout:{
                    type:'table',
                    columns:9
                },
                defaults:{
                    margin:'0 5',
                    labelAlign:'top'
                },
                id:'loan-simulator',
                padding:5,
                items:[
                    {
                        xtype:'datefield',
                        format: 'd-m-Y',
                        width:120,
                        id:'duedateLoan',
                        fieldLabel:'Due',
                        value: new Date()
                    },
                    {
                        xtype: 'combo',
                        fieldLabel: 'Loan Type <b style="color:red">*</b>',
                        allowBlank: false,
                        width:200,
                        store: Ext.create('Ext.data.Store', {
                            fields: ['loanTypeID', 'loanTypeName'],
                            autoLoad: true,
                            proxy: {
                                type: 'rest',
                                url: m_crud + '/common/getcombo', // url that will load data with respect to start and limit params
                                extraParams: {
                                    table: 'coop_loan_type',
                                    name: 'loanTypeID',
                                    id: 'loanTypeName'
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
                                var id = v[0].get('loanTypeID');

                                Ext.getCmp('loan-simulator').getForm().load({
                                    url:m_crud + '/loan/getLoanType',
                                    method:'GET',
                                    params:{
                                        id:id
                                    },
                                    success: function(form, action) {
//                                        console.log(action);
                                        var d = Ext.decode(action.response.responseText);
//                                        console.log(Ext.getCmp('interestTypeNameSimulator'));
                                        Ext.getCmp('interestTypeNameSimulator').show();
                                        if(d.data.loanTypeIslamic==1)
                                        {
                                            //syariah
                                            if(d.data.loanTypeProfitType==1)
                                            {
                                                //Profit Sharing
                                                Ext.getCmp('interestTypeNameSimulator').hide();
                                                Ext.getCmp('loanTypeInterestAmountSimulator').hide();
                                                
                                                var clientProfitShare = Ext.getCmp('clientProfitShare').show();
                                                clientProfitShare.setValue(d.data.loanTypeClientSharing);
                                                var coopProfitShare = Ext.getCmp('coopProfitShare').show();
                                                coopProfitShare.setValue(d.data.loanTypeCooperativeSharing);
                                            } else {
                                                //Fixed Margin
                                                Ext.getCmp('interestTypeNameSimulator').hide();
                                                var loanTypeInterestAmountSimulator = Ext.getCmp('loanTypeInterestAmountSimulator');
                                                loanTypeInterestAmountSimulator.show();
                                                loanTypeInterestAmountSimulator.setValue(d.data.loanTypeInterestAmount);
                                                
                                                Ext.getCmp('clientProfitShare').hide();
                                                Ext.getCmp('coopProfitShare').hide();
                                            }
                                            
                                        } else {
                                            Ext.getCmp('interestTypeNameSimulator').show();
                                            Ext.getCmp('loanTypeInterestAmountSimulator').hide();
                                            
                                            Ext.getCmp('clientProfitShare').hide();
                                                Ext.getCmp('coopProfitShare').hide();
                                        }
                                        // Ext.Msg.alert("Load failed", action.result.errorMessage);
                                    },
                                    failure: function(form, action) {
                                        Ext.Msg.alert("Load failed", action.result.errorMessage);
                                    }
                                });
                            }
                        },
                        displayField: 'loanTypeName',
                        valueField: 'loanTypeID',
                        id:'loanTypeIDSimulator',
                        name: 'loanTypeID'
                    },
                    {
                       xtype:'textfield',
                       submitValue:false,
                       width:85,
                       fieldLabel:'Client Profit %',
                       id:'clientProfitShare'  
                    },
                    {
                       xtype:'textfield',
                       submitValue:false,
                       width:85,
                       fieldLabel:'Coop Profit %',
                       id:'coopProfitShare'  
                    },
                    {
                       xtype:'textfield',
                       submitValue:false,
                       width:150,
                       fieldLabel:'Margin (%)',
                       id:'loanTypeInterestAmountSimulator'  
                    },
                    {
                        xtype:'textfield',
//                         hidden:true,
                        name:'interestTypeName',
                        id:'interestTypeNameSimulator',
                        submitValue:false,
                        width:150,
                        fieldLabel:'Interest'
                    },
                    {
                        xtype:'textfield',
                        name:'loanMemberAmount',
                        id:'loanMemberAmountSimulator',
                        submitValue:false,
                        width:150,
                        fieldLabel:'Loan Amount'
                    },
                    {
                        xtype:'textfield',
                        name:'loanMemberTotalTenor',
                        id:'loanMemberTotalTenorSimulator',
                        submitValue:false,
                        width:70,
                        fieldLabel:'Term'
                    },
                    {
                        xtype:'displayfield',
                        value:'Months',
                        id:'MonthsSimulator',
                        labelSeparator:'',
                        fieldLabel:'&nbsp;'
                    },
                    {
                        xtype:'button',
                        text:'Calculate Credit',
                        margin: '30 5 5 28',
                        scale: 'large',
                        ui: 's-button',
                        cls: 's-blue ',
                        buttonAlign: 'left',
                        handler: function() {
                            store.removeAll();
                            Ext.Ajax.request({
                                    waitMsg: 'Please wait...',
                                    url: m_crud+'/loan/runSimulator',
                                    method: 'POST',
                                    params: {
                                        loanTypeIDSimulator: Ext.getCmp('loanTypeIDSimulator').getValue(),
                                        loanMemberAmountSimulator: Ext.getCmp('loanMemberAmountSimulator').getValue(),
                                        loanMemberTotalTenorSimulator: Ext.getCmp('loanMemberTotalTenorSimulator').getValue(),
                                        MonthsSimulator: Ext.getCmp('MonthsSimulator').getValue(),
                                        duedateLoan: Ext.getCmp('duedateLoan').getSubmitValue()
                                    },
                                    success: function (response, opts) {
                                        var obj = Ext.decode(response.responseText);
                                         console.log(obj)
                                        if(obj.data==null)
                                        {
                                            Ext.MessageBox.alert('Failed', obj.message);
                                        } else {
                                            Ext.each(obj.data, function(v, i) {
                                                console.log(v)
//                                                var pajak = (obj.data.amount * 1 / 100) * obj.data.ratetax;
//                                                totalPajak += pajak;
//                                                subtotalReceive += obj.data.amount * 1;
                                                  store.add(v);
                                            });
                                            
                                        }
                                        
                                        if(obj.margin*1!=0)
                                        {
                                            Ext.getCmp('marginSimulator').show();
                                            Ext.getCmp('marginSimulator').setValue(obj.margin)
                                        } else {
                                            Ext.getCmp('marginSimulator').hide();
                                        }
                                       
//                                        switch (obj.success) {
//                                            case true:
//                                                Ext.MessageBox.alert('Success', obj.message);
//                                                store_keluarga.load({
//                                                    params: {
//                                                        id: Ext.getCmp('id').getValue()
//                                                    }
//                                                });
//                                                break;
//                                            default:
//                                                Ext.MessageBox.alert('Warning', obj.message);
//                                                break;
//                                        }
                                    },
                                    failure: function (response, opts) {
                                        var obj = Ext.decode(response.responseText);
                                        //console.log(obj);
                                        Ext.MessageBox.alert('error', 'Could not connect to the database. Retry later');
                                    }
                                });
                        }
                    },
                    
                    {
                        xtype:'button',
                        text:'Print',
                        margin: '30 5 5 5',
                        scale: 'large',
                        ui: 's-button',
                        cls: 's-blue ',
                        buttonAlign: 'right',
                        handler: function() {
                             var url = m_baseurl+'index.php/loan/print_simulator/cetak/'+Ext.getCmp('loanTypeIDSimulator').getValue()+'/'+Ext.getCmp('loanMemberAmountSimulator').getValue()+'/'+Ext.getCmp('loanMemberTotalTenorSimulator').getValue()+'/'+Ext.getCmp('MonthsSimulator').getValue()+'/'+Ext.getCmp('duedateLoan').getSubmitValue();
                             win = window.open(url, '_blank');
                             win.focus();
                        }
                    }
                ]
            },
             {
                xtype: 'toolbar',
                dock: 'top',
                items: [{
                        xtype:'displayfield',
                        id:'marginSimulator',
                        fieldLabel:'Profit Margin'                        
                }]
            }
        ]
    });

    Ext.getCmp('loanTypeInterestAmountSimulator').hide();
    Ext.getCmp('interestTypeNameSimulator').hide();
    Ext.getCmp('marginSimulator').hide();
    Ext.getCmp('clientProfitShare').hide();
    Ext.getCmp('coopProfitShare').hide();
});

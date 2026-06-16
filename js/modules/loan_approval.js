Ext.onReady(function () {

    Ext.tip.QuickTipManager.init();

    var store = Ext.create('Ext.data.Store', {
        storeId: 'loanStore',
        autoLoad:true,
        fields: ['loanTypeName', 'name', 'memberLoanNo','interestTypeName','memberLoanApprovedAmount','memberLoanProposedAmount','memberLoanTotalTenor'],
        proxy: {
            type: 'rest',
            url: m_crud + '/loan/getproposedmemberloan', // url that will load data with respect to start and limit params
            reader: {
                type: 'json',
                root: 'data',
                totalProperty: 'total',
                idProperty: 'memberLoanID'
            },
            writer: {
                type: 'json'
            },
            api: {
                destroy: m_crud + '/loan/delete'
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
            {text: 'Loan No.', dataIndex: 'memberLoanNo', flex: 1},
            {text: 'Member Name', dataIndex: 'name', flex: 1},
            {text: 'Loan Type', dataIndex: 'loanTypeName', width: 250},
            {text: 'Interest Type', dataIndex: 'loanTypeInterestRate', width: 130},
            {text: 'Loan Amount', dataIndex: 'memberLoanProposedAmount', width: 130},
            {text: 'Terms', dataIndex: 'memberLoanTotalTenor', width: 130}
        ],
        height: 500,
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
                xtype:'toolbar',
                dock:'top',
                items:[
                    {
                        xtype:'button',
                        text:'Loan Appraisal & Approval',
                        iconCls:'edit',
                        handler: function(){
                            var sm = grid.getSelectionModel();
                            var sel = sm.getSelection();
                            
                            if(sel.length > 0){
                                var id = sel[0].get('memberLoanID');

                                var win = Ext.create('Ext.Window',{
                                    title:'Credit Approval',
                                    width:480,
                                    height:500,
                                    y:10,
                                    items:[
                                        {
                                            xtype:'panel',
                                            padding:5,
                                            loader: {
                                                url: '/api/loan/loadDetailMemberLoan',
                                                params:{id:id},
                                                autoLoad: true
                                            }
                                        },
                                        {
                                            xtype:'form',
                                            padding:5,
                                            id:'frm-loan',
                                            layout:{
                                                type:'table',
                                                columns:3
                                            },
                                            listeners:{
                                                beforerender: function(c){
                                                    c.getForm().load({
                                                        url:'/api/loan/loadDetailMemberLoan',
                                                        params:{id:id, plain:true}
                                                    });
                                                }
                                            },
                                            items:[
                                                {
                                                    xtype:'hidden',
                                                    name:'memberLoanID'
                                                },
                                                {
                                                    xtype:'hidden',
                                                    id:'hidden-proposed-loan-amount',
                                                    submitValue:false,
                                                    name:'memberLoanProposedAmount'
                                                },
                                                {
                                                    xtype: 'numericfield',
                                                    height:50,
                                                    width:250,
                                                    id:'txt-deposit-amount',
                                                    hideTrigger:true,
                                                    name:'memberLoanApprovedAmount',
                                                    fieldStyle:'text-align:right;font-size:15px;font-family:Courier New;',
                                                    fieldLabel:'<b>APPROVED AMOUNT</b>',
                                                    allowBlank: false,
                                                    labelAlign:'top',
                                                    validator: function (val) {
                                                        var maxi = Ext.getCmp('hidden-proposed-loan-amount').getValue();

                                                        if(parseInt(val) > parseInt(maxi)) {
                                                            return 'Approved amount can\'t be higher than proposed amount';
                                                        } else {
                                                            return true;
                                                        }
                                                    }
                                                },
                                                {
                                                    xtype:'numericfield',
                                                    width:80,
                                                    height:50,
                                                    hideTrigger:true,
                                                    style:'margin-left:5px;',
                                                    name:'memberLoanTotalTenor',
                                                    fieldStyle:'text-align:right;font-size:15px;font-family:Courier New;',
                                                    fieldLabel:'<b>TOTAL TERM</b>',
                                                    allowBlank: false,
                                                    labelAlign:'top'
                                                },
                                                {
                                                    xtype:'numericfield',
                                                    fieldLabel:'<b>APPRISAL</b>',
                                                    labelAlign:'top',
                                                    hideTrigger:true,
                                                    name:'memberLoanApprisal',
                                                    fieldStyle:'font-size:15px;',
                                                    style:'margin-left:5px;',
                                                    height:50,
                                                    width:110
                                                }
                                            ]
                                        }
                                    ],
                                    buttons: [
                                        {
                                            xtype:'button',
                                            text:'Approve',
                                            margin: '5px',
                                            scale: 'large',
                                            ui: 's-button',
                                            cls: 's-green ',
                                            buttonAlign: 'left',
                                            id:'btn-save-frm-loan',
                                            handler:function(c){
                                                var form = Ext.getCmp('frm-loan').getForm();
                                                var id = form.getValues();
                                                var url = m_crud + '/loan/approve';
                                                var method = 'POST';
                                                var data_grid = grid.store;
                                                var detail = [];

                                                data_grid.each(function(value, index, rec){
                                                    detail.push(value.data);
                                                });

                                                form.submit({
                                                    url: url,
                                                    method: method,
                                                    success: function(f, resp) {
                                                        Ext.Msg.alert('Success', 'Data successfully saved');
                                                        win.close();
                                                        store.loadPage(1);
                                                    },
                                                    failure: function(f, resp) {
                                                        Ext.Msg.alert('Success', 'Data Failed to be submitted');
                                                    }
                                                });
                                            }
                                        },
                                        {
                                            xtype:'button',
                                            text:'Reject',
                                            id:'btn-reject-frm-Journal',
                                            margin: '5px',
                                            scale: 'large',
                                            ui: 's-button',
                                            cls: 's-red',
                                            handler:function(){
                                                var form = Ext.getCmp('frm-loan').getForm();
                                                var id = form.getValues();
                                                var url = m_crud + '/loan/reject';
                                                var method = 'POST';
                                                var data_grid = grid.store;
                                                var detail = [];

                                                data_grid.each(function(value, index, rec){
                                                    detail.push(value.data);
                                                });

                                                form.submit({
                                                    url: url,
                                                    method: method,
                                                    success: function(f, resp) {
                                                        Ext.Msg.alert('Success', 'Data successfully saved');
                                                        win.close();
                                                        store.loadPage(1);
                                                    },
                                                    failure: function(f, resp) {
                                                        Ext.Msg.alert('Success', 'Data Failed to be submitted');
                                                    }
                                                });
                                            }
                                        },
                                        {
                                            xtype:'button',
                                            text:'Close',
                                            id:'btn-cancel-frm-Journal',
                                            margin: '5px',
                                            scale: 'large',
                                            ui: 's-button',
                                            cls: 's-grey',
                                            handler:function(){
                                                win.close();
                                            }
                                        }
                                    ],
                                    modal:true
                                }).show();
                            } else {
                                Ext.MessageBox.show({
                                    title: 'Approve Failed',
                                    msg: 'Please select the loan you want to approve',
                                    width: 300,
                                    buttons: Ext.MessageBox.OK,
                                    animateTarget: 'mb3'
                                });
                            }
                            
                        }
                    }
                ]
            }
        ]
    });

});

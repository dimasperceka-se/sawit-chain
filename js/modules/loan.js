Ext.onReady(function () {

    Ext.tip.QuickTipManager.init();

    var store = Ext.create('Ext.data.Store', {
        storeId: 'loanStore',
        autoLoad:true,
        fields: ['MemberLoanStatus','LoanTypeName', 'name', 'MemberLoanNo','InterestTypeName','memberLoanApprovedAmount','MemberLoanProposedAmount','MemberLoanTotalTenor'],
        proxy: {
            type: 'rest',
            url: m_api + '/loan/getmemberloan', // url that will load data with respect to start and limit params
            reader: {
                type: 'json',
                root: 'data',
                totalProperty: 'total',
                idProperty: 'MemberLoanID'
            },
            writer: {
                type: 'json'
            },
            api: {
                destroy: m_api + '/loan/delete'
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
            {text: 'Loan No.', dataIndex: 'MemberLoanNo', flex: 1},
            {text: 'Member Name', dataIndex: 'name', flex: 1},
            {text: 'Loan Type', dataIndex: 'LoanTypeName', width: 250},
            {text: 'Interest Type', dataIndex: 'InterestTypeName', width: 130},
            {text: 'Loan Amount', dataIndex: 'MemberLoanProposedAmount', width: 130, xtype:'numbercolumn',align:'right'},
            {text: 'Terms', dataIndex: 'MemberLoanTotalTenor', width: 130},
            {text: 'Status', dataIndex: 'MemberLoanStatus', width: 130, renderer: function(v){
                switch(v){
                    case null:
                        return 'Awaiting Approval';
                    case '1':
                        return 'Completed';
                        break;
                    case '2':
                        return 'Approved';
                        break;
                    case '3':
                        return 'Proposed';
                        break;
                    case '4':
                        return 'Written Off';
                        break;
                }
            }}
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
                        iconCls:'add',
                        text:'Add',
                        handler:function(){
                            var win = Ext.create('Ext.Window',{
                                title:'Add Member Loan Application',
                                width:500,
                                items:[
                                    Ext.create('Ext.form.Panel' ,{
                                        id:'frm-loan',
                                        padding:5,
                                        layout:{
                                            type:'column'
                                        },
                                        items:[
                                            {
                                                xtype:'container',
                                                items: [
                                                    {
                                                        xtype:'fieldset',
                                                        title:'Member Detail',
                                                        items:[
                                                            {
                                                                xtype: 'combo',
                                                                fieldLabel: 'Member No <b style="color:red">*</b>',
                                                                allowBlank: false,
                                                                width:400,
                                                                store: Ext.create('Ext.data.Store', {
                                                                    fields: ['memberID', 'primaryNo', 'name'],
                                                                    autoLoad: true,
                                                                    proxy: {
                                                                        type: 'rest',
                                                                        url: m_api+'/loan/getcombomember', // url that will load data with respect to start and limit params
                                                                        reader: {
                                                                            type: 'json',
                                                                            root: 'data',
                                                                            totalProperty: 'total'
                                                                        }
                                                                    }
                                                                }),
                                                                tpl: Ext.create('Ext.XTemplate',
                                                                    '<ul class="x-list-plain"><tpl for=".">',
                                                                        '<li role="option" class="x-boundlist-item">{primaryNo} - {name}</li>',
                                                                    '</tpl></ul>'
                                                                ),
                                                                // template for the content inside text field
                                                                displayTpl: Ext.create('Ext.XTemplate',
                                                                    '<tpl for=".">',
                                                                        '{primaryNo} - {name}',
                                                                    '</tpl>'
                                                                ),
                                                                listeners:{
                                                                    select: function(c,v){
                                                                        
                                                                        var id = v[0].get('memberID');

                                                                        Ext.getCmp('frm-loan').getForm().load({
                                                                            url:m_api + '/loan/getMemberData',
                                                                            method:'GET',
                                                                            params:{
                                                                                id:id
                                                                            }
                                                                        });
                                                                    }
                                                                },
                                                                displayField: 'primaryNo',
                                                                valueField: 'memberID',
                                                                name: 'MemberID'
                                                            },
                                                            {
                                                                xtype:'textfield',
                                                                name:'TypeName',
                                                                submitValue:false,
                                                                fieldLabel:'Member Type',
                                                                readOnly:true,
                                                                width:300
                                                            },
                                                            {
                                                                xtype:'textfield',
                                                                name:'Name',
                                                                submitValue:false,
                                                                fieldLabel:'Member Name',
                                                                readOnly:true,
                                                                width:450
                                                            },
                                                            {
                                                                xtype:'textarea',
                                                                name:'Address',
                                                                height:60,
                                                                submitValue:false,
                                                                fieldLabel:'Member Address',
                                                                readOnly:true,
                                                                width:450
                                                            }
                                                        ]
                                                    },
                                                    {
                                                        xtype:'fieldset',
                                                        title:'Loan Type',
                                                        items:[
                                                            {
                                                                xtype: 'combo',
                                                                fieldLabel: 'Loan Type <b style="color:red">*</b>',
                                                                allowBlank: false,
                                                                width:400,
                                                                store: Ext.create('Ext.data.Store', {
                                                                    fields: ['loanTypeID', 'loanTypeName'],
                                                                    autoLoad: true,
                                                                    proxy: {
                                                                        type: 'rest',
                                                                        url: m_api + '/loan/getcombotype', // url that will load data with respect to start and limit params
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

                                                                        Ext.getCmp('frm-loan').getForm().load({
                                                                            url:m_api + '/loan/getLoanType',
                                                                            method:'GET',
                                                                            params:{
                                                                                id:id
                                                                            },
                                                                             success: function (form, action) {
                                                                                var obj = Ext.decode(action.response.responseText);
                                                                                
                                                                                console.log(obj);

                                                                                if(obj.LoanTypeIslamic*1==1)
                                                                                {
                                                                                    Ext.getCmp('interestTypeName').setValue('Syariah');
                                                                                } else {
                                                                                     Ext.getCmp('interestTypeName').setValue('Konvensional');
                                                                                }
                                                                                


                                                                                Ext.getCmp('add-loan-min-tenor').setValue(obj.data.LoanTypeMinTenor);
                                                                                Ext.getCmp('add-loan-max-tenor').setValue(obj.data.LoanTypeMaxTenor);
                                                                                // Ext.Msg.alert('Success');
                                                                                // console.log(action);
                                                                            },
                                                                            failure: function (form, action) {
                                                                                Ext.Msg.alert('Failure');
                                                                            }          
                                                                        });
                                                                    }
                                                                },
                                                                displayField: 'loanTypeName',
                                                                valueField: 'loanTypeID',
                                                                name: 'loanTypeID'
                                                            },
                                                            {
                                                                xtype:'textfield',
                                                                id:'interestTypeName',
                                                                name:'interestTypeName',
                                                                fieldLabel:'Interest Type',
                                                                readOnly:true,
                                                                submitValue:false,
                                                                width:300
                                                            },
                                                            {
                                                                xtype:'container',
                                                                layout:{
                                                                    type:'table',
                                                                    columns:3
                                                                },
                                                                items:[
                                                                    {
                                                                        xtype:'displayfield',
                                                                        fieldLabel:'Terms'
                                                                    },
                                                                    {
                                                                        xtype:'textfield',
                                                                        readOnly:true,
                                                                        labelWidth:25,
                                                                        fieldLabel:'Min',
                                                                        width:75,
                                                                        id:'add-loan-min-tenor',
                                                                        submitValue:false,
                                                                        name:'loanTypeMinTenor',
                                                                        labelAlign:'right'
                                                                    },
                                                                    {
                                                                        xtype:'textfield',
                                                                        readOnly:true,
                                                                        width:95,
                                                                        id:'add-loan-max-tenor',
                                                                        submitValue:false,
                                                                        labelAlign:'right',
                                                                        labelWidth:45,
                                                                        name:'loanTypeMaxTenor',
                                                                        fieldLabel:'Max'
                                                                    }
                                                                ]
                                                            }
                                                        ]
                                                    },
                                                    {
                                                        xtype:'fieldset',
                                                        title:'Proposed Loan Detail',
                                                        layout:{
                                                            type:'table',
                                                            columns:2
                                                        },
                                                        items:[
                                                            {
                                                                xtype: 'numericfield',
                                                                height:55,
                                                                width:300,
                                                                id:'txt-deposit-amount',
                                                                hideTrigger:true,
                                                                name:'MemberLoanProposedAmount',
                                                                fieldStyle:'text-align:right;font-size:20px;font-family:Courier New;',
                                                                fieldLabel:'<b>PROPOSED AMOUNT</b>',
                                                                allowBlank: false,
                                                                labelAlign:'top'
                                                            },
                                                            {
                                                                xtype:'numericfield',
                                                                width:150,
                                                                height:55,
                                                                hideTrigger:true,
                                                                style:'margin-left:5px;',
                                                                name:'MemberLoanTotalTenor',
                                                                fieldStyle:'text-align:right;font-size:20px;font-family:Courier New;',
                                                                fieldLabel:'<b>TOTAL TERM</b>',
                                                                allowBlank: false,
                                                                labelAlign:'top',
                                                                validator: function (val) {
                                                                    var mini = Ext.getCmp('add-loan-min-tenor').getValue();
                                                                    var maxi = Ext.getCmp('add-loan-max-tenor').getValue();
                                                                    
                                                                    if(parseInt(val) < parseInt(mini)){
                                                                        return 'This field is invalid, please check minimum terms';
                                                                    } else if(parseInt(val) > parseInt(maxi)) {
                                                                        return 'This field is invalid, please check maximum terms';
                                                                    } else {
                                                                        return true;
                                                                    }
                                                                }
                                                            },
                                                            {
                                                                xtype:'textarea',
                                                                width:400,
                                                                height:50,
                                                                hidden:true,
                                                                name:'memberLoanRemark',
                                                                fieldLabel:'Remark'
                                                            }
                                                        ]
                                                    }
                                                ]
                                            },
                                            {
                                                xtype:'container',
                                                items:[
                                                    {
                                                        xtype:'fieldset',
                                                        margin:'0 5',
                                                        title:'Guarantee',
                                                        hidden:true,
                                                        items:[
                                                            {
                                                                xtype: 'combo',
                                                                disabledCls: 'disabled',
                                                                fieldLabel: 'Type ',
                                                                store: Ext.create('Ext.data.Store', {
                                                                    fields: ['GUARANTEE_TYPE_ID', 'GUARANTEE_TYPE_NAME'],
                                                                    autoLoad: true,
                                                                    data: [
                                                                        {GUARANTEE_TYPE_ID: '1', GUARANTEE_TYPE_NAME: 'Vehicle'},
                                                                        {GUARANTEE_TYPE_ID: '2', GUARANTEE_TYPE_NAME: 'Property'}
                                                                    ]
                                                                }),
                                                                displayField: 'GUARANTEE_TYPE_NAME',
                                                                valueField: 'GUARANTEE_TYPE_ID',
                                                                name: 'loanGuaranteeID'
                                                            },
                                                            {
                                                                xtype:'textfield',
                                                                fieldLabel:'Certificate No.',
                                                                width:300
                                                            },
                                                            {
                                                                xtype:'textfield',
                                                                fieldLabel:'Certificate Owner',
                                                                width:300
                                                            },
                                                            {
                                                                xtype:'numberfield',
                                                                hideTrigger:true,
                                                                fieldLabel:'Guarantee Value',
                                                                width:250
                                                            }
                                                        ]
                                                    }
                                                ]
                                            }
                                        ]
                                    })
                                ],
                                buttons: [
                                    {
                                        xtype:'button',
                                        text:'Save',
                                        margin: '5px',
                                        scale: 'large',
                                        ui: 's-button',
                                        cls: 's-blue ',
                                        buttonAlign: 'left',
                                        id:'btn-save-frm-loan',
                                        handler:function(c){
                                            
                                            var form = Ext.getCmp('frm-loan').getForm();
                                            var id = form.getValues();
                                            var url = m_api + '/loan/add';
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
                                                    Ext.Msg.alert('Failed', 'Cannot save data');
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
                                            var form = Ext.getCmp('frm-loan');
                                            form.getForm().reset();
                                            win.close();
                                        }
                                    }
                                ],
                                modal:true
                            }).show();
                        }
                    },
                    {
                        xtype:'button',
                        iconCls:'edit',
                        text:'Edit',
                        hidden:true,
                        handler:function(){
                            
                            var sm = grid.getSelectionModel();
                            var sel = sm.getSelection();
                            
                            if(sel.length > 0){
                                var win = Ext.create('Ext.Window',{
                                title:'Add Member Loan',
                                width:1000,
                                items:[
                                    Ext.create('Ext.form.Panel' ,{
                                        id:'frm-loan',
                                        padding:5,
                                        layout:{
                                            type:'table',
                                            columns:2
                                        },
                                        items:[
                                            {
                                                xtype:'container',
                                                items: [
                                                    {
                                                        xtype:'fieldset',
                                                        title:'Loan Type',
                                                        items:[
                                                            {
                                                                xtype: 'combo',
                                                                fieldLabel: 'Loan Type <b style="color:red">*</b>',
                                                                allowBlank: false,
                                                                width:300,
                                                                store: Ext.create('Ext.data.Store', {
                                                                    fields: ['loanTypeID', 'LoanTypeName'],
                                                                    autoLoad: true,
                                                                    proxy: {
                                                                        type: 'rest',
                                                                        url: m_api + '/common/getcombo', // url that will load data with respect to start and limit params
                                                                        extraParams: {
                                                                            table: 'coop_loan_type',
                                                                            name: 'loanTypeID',
                                                                            id: 'LoanTypeName'
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

                                                                        Ext.getCmp('frm-loan').getForm().load({
                                                                            url:m_api + '/loan/getLoanType',
                                                                            method:'GET',
                                                                            params:{
                                                                                id:id
                                                                            }
                                                                        });
                                                                    }
                                                                },
                                                                displayField: 'LoanTypeName',
                                                                valueField: 'loanTypeID',
                                                                name: 'loanTypeID'
                                                            },
                                                            {
                                                                xtype:'textfield',
                                                                name:'interestTypeName',
                                                                fieldLabel:'Interest Type',
                                                                readOnly:true,
                                                                submitValue:false,
                                                                width:300
                                                            },
                                                            {
                                                                xtype:'container',
                                                                layout:{
                                                                    type:'table',
                                                                    columns:3
                                                                },
                                                                items:[
                                                                    {
                                                                        xtype:'displayfield',
                                                                        fieldLabel:'Terms'
                                                                    },
                                                                    {
                                                                        xtype:'textfield',
                                                                        readOnly:true,
                                                                        labelWidth:25,
                                                                        fieldLabel:'Min',
                                                                        width:75,
                                                                        submitValue:false,
                                                                        name:'loanMinTenor',
                                                                        labelAlign:'right'
                                                                    },
                                                                    {
                                                                        xtype:'textfield',
                                                                        readOnly:true,
                                                                        width:95,
                                                                        submitValue:false,
                                                                        labelAlign:'right',
                                                                        labelWidth:45,
                                                                        name:'loanMaxTenor',
                                                                        fieldLabel:'Max'
                                                                    }
                                                                ]
                                                            }
                                                        ]
                                                    },
                                                    {
                                                        xtype:'fieldset',
                                                        title:'Member Detail',
                                                        height:242,
                                                        items:[
                                                            {
                                                                xtype: 'combo',
                                                                fieldLabel: 'Member No <b style="color:red">*</b>',
                                                                allowBlank: false,
                                                                width:400,
                                                                store: Ext.create('Ext.data.Store', {
                                                                    fields: ['memberID', 'primaryNo'],
                                                                    autoLoad: true,
                                                                    proxy: {
                                                                        type: 'rest',
                                                                        url: m_api + '/common/getcombo', // url that will load data with respect to start and limit params
                                                                        extraParams: {
                                                                            table: 'coop_member',
                                                                            name: 'primaryNo',
                                                                            id: 'memberID'
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
                                                                        var id = v[0].get('memberID');

                                                                        Ext.getCmp('frm-loan').getForm().load({
                                                                            url:m_api + '/loan/getMemberData',
                                                                            method:'GET',
                                                                            params:{
                                                                                id:id
                                                                            }
                                                                        });
                                                                    }
                                                                },
                                                                displayField: 'primaryNo',
                                                                valueField: 'memberID',
                                                                name: 'memberID'
                                                            },
                                                            {
                                                                xtype:'textfield',
                                                                name:'typeName',
                                                                submitValue:false,
                                                                fieldLabel:'Member Type',
                                                                readOnly:true,
                                                                width:300
                                                            },
                                                            {
                                                                xtype:'textfield',
                                                                name:'name',
                                                                submitValue:false,
                                                                fieldLabel:'Member Name',
                                                                readOnly:true,
                                                                width:450
                                                            },
                                                            {
                                                                xtype:'textarea',
                                                                name:'address',
                                                                submitValue:false,
                                                                fieldLabel:'Member Address',
                                                                readOnly:true,
                                                                width:450
                                                            }
                                                        ]
                                                    }
                                                ]
                                            },
                                            {
                                                xtype:'container',
                                                width:500,
                                                items:[
                                                    {
                                                        xtype:'fieldset',
                                                        margin:'0 5',
                                                        title:'Loan Detail',
                                                        items:[
                                                            {
                                                                xtype:'container',
                                                                layout:{
                                                                    type:'table',
                                                                    columns:3
                                                                },
                                                                items:[
                                                                    {
                                                                        xtype:'displayfield',
                                                                        fieldLabel:'Proposed'
                                                                    },
                                                                    {
                                                                        xtype:'datefield',
                                                                        labelWidth:25,
                                                                        fieldLabel:'Date',
                                                                        width:150,
                                                                        name:'memberLoanProposedDate',
                                                                        submitFormat:'Y-m-d',
                                                                        value: new Date(),
                                                                        style:'margin-right:5px',
                                                                        labelAlign:'right'
                                                                    },
                                                                    {
                                                                        xtype:'textfield',
                                                                        width:190,
                                                                        labelAlign:'right',
                                                                        labelWidth:45,
                                                                        value:0,
                                                                        name:'MemberLoanProposedAmount',
                                                                        fieldLabel:'Amount'
                                                                    }
                                                                ]
                                                            },
                                                            {
                                                                xtype:'container',
                                                                layout:{
                                                                    type:'table',
                                                                    columns:3
                                                                },
                                                                items:[
                                                                    {
                                                                        xtype:'displayfield',
                                                                        fieldLabel:'Approved'
                                                                    },
                                                                    {
                                                                        xtype:'datefield',
                                                                        labelWidth:25,
                                                                        fieldLabel:'Date',
                                                                        width:150,
                                                                        submitFormat:'Y-m-d',
                                                                        value: new Date(),
                                                                        name:'memberLoanApprovedDate',
                                                                        style:'margin-right:5px',
                                                                        labelAlign:'right'
                                                                    },
                                                                    {
                                                                        xtype:'textfield',
                                                                        width:190,
                                                                        labelAlign:'right',
                                                                        labelWidth:45,
                                                                        value:0,
                                                                        name:'memberLoanApprovedAmount',
                                                                        fieldLabel:'Amount'
                                                                    }
                                                                ]
                                                            },
                                                            {
                                                                xtype:'textfield',
                                                                width:150,
                                                                name:'MemberLoanTotalTenor',
                                                                fieldLabel:'Total Terms'
                                                            },
                                                            {
                                                                xtype:'textarea',
                                                                width:400,
                                                                height:50,
                                                                name:'memberLoanRemark',
                                                                fieldLabel:'Remark'
                                                            },
                                                            {
                                                                xtype: 'combo',
                                                                disabledCls: 'disabled',
                                                                fieldLabel: 'Status',
                                                                store: Ext.create('Ext.data.Store', {
                                                                    fields: ['STATUS_ID', 'STATUS_NAME'],
                                                                    autoLoad: true,
                                                                    data: [
                                                                        {STATUS_ID: '1', STATUS_NAME: 'Active'},
                                                                        {STATUS_ID: '2', STATUS_NAME: 'Completed'}
                                                                    ]
                                                                }),
                                                                displayField: 'STATUS_NAME',
                                                                valueField: 'STATUS_ID',
                                                                name: 'MemberLoanStatus'
                                                            }
                                                        ]
                                                    },
                                                    {
                                                        xtype:'fieldset',
                                                        margin:'0 5',
                                                        title:'Guarantee',
                                                        items:[
                                                            {
                                                                xtype: 'combo',
                                                                disabledCls: 'disabled',
                                                                fieldLabel: 'Type ',
                                                                store: Ext.create('Ext.data.Store', {
                                                                    fields: ['GUARANTEE_TYPE_ID', 'GUARANTEE_TYPE_NAME'],
                                                                    autoLoad: true,
                                                                    data: [
                                                                        {GUARANTEE_TYPE_ID: '1', GUARANTEE_TYPE_NAME: 'Vehicle'},
                                                                        {GUARANTEE_TYPE_ID: '2', GUARANTEE_TYPE_NAME: 'Property'}
                                                                    ]
                                                                }),
                                                                displayField: 'GUARANTEE_TYPE_NAME',
                                                                valueField: 'GUARANTEE_TYPE_ID',
                                                                name: 'loanGuaranteeID'
                                                            },
                                                            {
                                                                xtype:'textfield',
                                                                fieldLabel:'Certificate No.',
                                                                width:300
                                                            },
                                                            {
                                                                xtype:'textfield',
                                                                fieldLabel:'Certificate Owner',
                                                                width:300
                                                            },
                                                            {
                                                                xtype:'numberfield',
                                                                hideTrigger:true,
                                                                fieldLabel:'Guarantee Value',
                                                                width:250
                                                            }
                                                        ]
                                                    }
                                                ]
                                            }
                                        ]
                                    })
                                ],
                                buttons: [
                                    {
                                        xtype:'button',
                                        text:'Save',
                                        margin: '5px',
                                        scale: 'large',
                                        ui: 's-button',
                                        cls: 's-blue ',
                                        buttonAlign: 'left',
                                        id:'btn-save-frm-loan',
                                        handler:function(c){
                                            var form = Ext.getCmp('frm-loan').getForm();
                                            var id = form.getValues();
                                            var url = m_api + '/loan/add';
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
                                                    Ext.Msg.alert('Failed', 'Cannot save data');
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
                                            var form = Ext.getCmp('frm-loan');
                                            form.getForm().reset();
                                            win.close();
                                        }
                                    }
                                ],
                                modal:true
                            }).show();
                                var id = sel[0].get('loanTypeID');
                                
                                Ext.getCmp('frm-loan-type').getForm().load({
                                    url:m_api + '/loan/gettypebyid',
                                    method:'GET',
                                    params:{
                                        id:id
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
                            
                            
                            
                            
                        }
                    },
                    {
                        xtype:'button',
                        iconCls:'delete',
                        text:'Delete',
                        hidden:true,
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
                    },
                    {
                        xtype:'button',
                        hidden:true,
                        text:'Prepare to write off loan',
                        iconCls:'edit',
                        handler: function(){
                            var sm = grid.getSelectionModel();
                            var sel = sm.getSelection();
                            
                            if(sel.length > 0){
                                var id = sel[0].get('MemberLoanID');

                                Ext.MessageBox.confirm('Message', 'Do you want to write off this loan ?', function(btn) {
                                    if (btn == 'yes') {
                                        Ext.Ajax.request({
                                            waitMsg: 'Please Wait',
                                            url: m_api + '/loan/setloanwrittenoff',
                                            method: 'POST',
                                            params: {id: id},
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
                    },
                    {
                        xtype:'button',
                        text:'Loan Appraisal & Approval',
                        iconCls:'edit',
                        handler: function(){
                            var sm = grid.getSelectionModel();
                            var sel = sm.getSelection();

                            // console.log(sel[0].data.id);
                            
                            if(sel.length > 0){
                                // var id = sel[0].get('MemberLoanID');
                                var id = sel[0].data.id;
                                Ext.getCmp('MemberLoanID_approval').setValue(id);

                                var win = Ext.create('Ext.Window',{
                                    title:'Credit Approval',
                                    width:480,
                                    y:10,
                                    items:[
                                        {
                                            xtype:'panel',
                                            padding:5,
                                            loader: {
                                                url: m_api+'/loan/loaddetailmemberloan',
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
                                                
                                            },
                                            items:[
                                                {
                                                    xtype:'hidden',
                                                    id:'MemberLoanID_approval',
                                                    name:'MemberLoanID'
                                                },
                                                {
                                                    xtype:'hidden',
                                                    id:'hidden-proposed-loan-amount',
                                                    submitValue:false,
                                                    name:'MemberLoanProposedAmount'
                                                },
                                                {
                                                    xtype: 'numericfield',
                                                    height:50,
                                                    width:250,
                                                    id:'txt-deposit-amount',
                                                    hideTrigger:true,
                                                    name:'MemberLoanApprovedAmount',
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
                                                // {
                                                //     xtype:'numericfield',
                                                //     width:80,
                                                //     height:50,
                                                //     hideTrigger:true,
                                                //     style:'margin-left:5px;',
                                                //     name:'MemberLoanTotalTenor',
                                                //     fieldStyle:'text-align:right;font-size:15px;font-family:Courier New;',
                                                //     fieldLabel:'<b>TOTAL TERM</b>',
                                                //     allowBlank: false,
                                                //     labelAlign:'top'
                                                // },

                                                {
                                                    xtype:'numericfield',
                                                    hideTrigger:true,
                                                    fieldLabel:'<b>TOTAL TERM</b>',
                                                    labelAlign:'top',
                                                    name:'MemberLoanTotalTenor',
                                                    fieldStyle:'font-size:15px;',
                                                    style:'margin-left:5px;',
                                                    height:50,
                                                    width:100
                                                },
                                                {
                                                    xtype:'numericfield',
                                                    fieldLabel:'<b>APPRISAL</b>',
                                                    labelAlign:'top',
                                                    name:'MemberLoanApprisal',
                                                    fieldStyle:'font-size:15px;',
                                                    style:'margin-left:5px;',
                                                    height:50,
                                                    width:100
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
                                                var url = m_api + '/loan/approve';
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
                                                var url = m_api + '/loan/reject';
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
                    },
                    {
                        xtype:'button',
                        hidden:true,
                        text:'Loan Payment',
                        iconCls:'edit',
                        handler: function(){
                            
                            var sm = grid.getSelectionModel();
                            var sel = sm.getSelection();
                            
                            Ext.create('Ext.data.Store', {
                                storeId:'loanInstallment',
                                fields:['term', 'from', 'date', 'amount', 'due', 'arrear'],
                                data:{'items':[
                                    { 'term': '1',  "from":"Cash",  "amount":"5.000.000", "date": "01/04/2015", "due": "05/04/2015", "arrear": 0 },
                                    { 'term': '2',  "from":"Cash",  "amount":"5.000.000", "date": "01/05/2015", "due": "05/05/2015", "arrear": 0 },
                                    { 'term': '3',  "from":"Cash",  "amount":"5.000.000", "date": "01/06/2015", "due": "05/06/2015", "arrear": 0 },
                                    { 'term': '4',  "from":"Cash",  "amount":"5.000.000", "date": "01/07/2015", "due": "05/07/2015", "arrear": 0 },
                                    { 'term': '5',  "from":"Cash",  "amount":"5.000.000", "date": "13/08/2015", "due": "05/08/2015", "arrear": 5 },
                                    { 'term': '6',  "from":"Cash",  "amount":"5.000.000", "date": "01/09/2015", "due": "05/09/2015", "arrear": 0 },
                                    { 'term': '7',  "from":"Cash",  "amount":"5.000.000", "date": "01/10/2015", "due": "05/10/2015", "arrear": 0 },
                                    { 'term': '8',  "from":"Cash",  "amount":"5.000.000", "date": "01/11/2015", "due": "05/11/2015", "arrear": 0 }
                                    
                                ]},
                                proxy: {
                                    type: 'memory',
                                    reader: {
                                        type: 'json',
                                        root: 'items'
                                    }
                                }
                            });


                            var win = Ext.create('Ext.Window',{
                                title:'Loan Payment',
                                width:900,
                                items:[
                                    {
                                        xtype:'form',
                                        layout:{
                                            type:'table',
                                            columns:3
                                        },
                                        defaults:{
                                            margin:'0 5',
                                            labelAlign:'top'
                                        },
                                        padding:5,
                                        items:[
                                            {
                                                xtype:'textfield',
                                                name:'loanMemberNo',
                                                submitValue:false,
                                                fieldLabel:'Loan No.',
                                                width:250,
                                                readOnly:true
                                            },
                                            {
                                                xtype:'textfield',
                                                name:'name',
                                                width:250,
                                                submitValue:false,
                                                fieldLabel:'Member Name',
                                                readOnly:true
                                            },
                                            {
                                                xtype:'textarea',
                                                name:'loanMemberRemark',
                                                submitValue:false,
                                                fieldLabel:'Remark',
                                                readOnly:true,
                                                width:350,
                                                height:102,
                                                rowspan: 2
                                            },
                                            {
                                                xtype:'textfield',
                                                name:'loanMemberAmount',
                                                submitValue:false,
                                                width:250,
                                                fieldLabel:'Loan Amount',
                                                readOnly:true
                                            },
                                            {
                                                xtype:'textfield',
                                                name:'interestTypeName',
                                                submitValue:false,
                                                width:250,
                                                fieldLabel:'Interest Type',
                                                readOnly:true
                                            },
                                            
                                        ]
                                    },
                                    {
                                        xtype:'grid',
                                        style:'border-top:1px solid #CCC',
                                        store: Ext.data.StoreManager.lookup('loanInstallment'),
                                        columns: [
                                            { text: 'Term',  dataIndex: 'term', width:50 },
                                            { text: 'Amount', dataIndex: 'amount', align:'right', width:150 },
                                            { text: 'Received From', dataIndex: 'from', flex: 1 },
                                            { text: 'Received Date', dataIndex: 'date' },
                                            { text: 'Due Date', dataIndex: 'due', summaryRenderer: function() { return 'Total' }},
                                            { text: 'Arrear', dataIndex: 'arrear', summaryType: 'sum' }
                                        ],
                                        features: [{
                                            id: 'sum',
                                            ftype: 'summary',
                                            groupHeaderTpl: '{arrear}'
                                        }],
                                        height:300,
                                        dockedItems:[
                                            {
                                                xtype:'toolbar',
                                                dock:'top',
                                                items:[
                                                    {
                                                        xype:'button',
                                                        text:'Add',
                                                        iconCls:'add',
                                                        handler: function(){
                                                            var winterm = Ext.create('Ext.Window',{
                                                                title:'Add Payment',
                                                                items:[
                                                                    {
                                                                        xtype:'form',
                                                                        layout:{
                                                                            type:'table',
                                                                            columns:1
                                                                        },
                                                                        defaults:{
                                                                            margin:'0 5',
                                                                            labelWidth:130,
                                                                            labelAlign:'left'
                                                                        },
                                                                        padding:5,
                                                                        items:[
                                                                            {
                                                                                xtype:'datefield',
                                                                                submitValue:false,
                                                                                fieldLabel:'Date',
                                                                                value:new Date(),
                                                                                width:250,
                                                                                readOnly:true
                                                                            },
                                                                            {
                                                                                xtype:'textfield',
                                                                                fieldLabel:'Term',
                                                                                width:200,
                                                                                value:9
                                                                            },
                                                                            {
                                                                                xtype:'textfield',
                                                                                fieldLabel:'From',
                                                                                width:400
                                                                            },
                                                                            {
                                                                                xtype:'numberfield',
                                                                                name:'name',
                                                                                hideTrigger:true,
                                                                                width:400,
                                                                                labelWidth:200,
                                                                                fieldStyle:'text-align:right',
                                                                                value:3500000,
                                                                                submitValue:false,
                                                                                fieldLabel:'Installment Amount',
                                                                                readOnly:true
                                                                            },
                                                                            {
                                                                                xtype:'numberfield',
                                                                                hideTrigger:true,
                                                                                width:400,
                                                                                labelWidth:200,
                                                                                fieldStyle:'text-align:right',
                                                                                value:1500000,
                                                                                submitValue:false,
                                                                                fieldLabel:'Interest Amount',
                                                                                readOnly:true
                                                                            },
                                                                            {
                                                                                xtype:'numberfield',
                                                                                hideTrigger:true,
                                                                                width:400,
                                                                                labelWidth:200,
                                                                                fieldStyle:'text-align:right',
                                                                                value:5000000,
                                                                                fieldStyle:'font-weight:bold; text-align:right',
                                                                                submitValue:false,
                                                                                fieldLabel:'<b>Total</b>',
                                                                                readOnly:true
                                                                            },
                                                                            {
                                                                                xtype:'numberfield',
                                                                                name:'name',
                                                                                hideTrigger:true,
                                                                                width:400,
                                                                                labelWidth:200,
                                                                                fieldStyle:'text-align:right',
                                                                                value:5000000,
                                                                                margin:'15 5 0 5',
                                                                                fieldLabel:'Payment Amount'
                                                                            }

                                                                        ]
                                                                    }
                                                                ],
                                                                buttons: [
                                                                    {
                                                                        xtype:'button',
                                                                        text:'Save',
                                                                        margin: '5px',
                                                                        scale: 'large',
                                                                        ui: 's-button',
                                                                        cls: 's-blue ',
                                                                        buttonAlign: 'left',
                                                                        handler:function(c){
                                                                            
                                                                        }
                                                                    },
                                                                    {
                                                                        xtype:'button',
                                                                        text:'Close',
                                                                        margin: '5px',
                                                                        scale: 'large',
                                                                        ui: 's-button',
                                                                        cls: 's-grey',
                                                                        handler:function(){
                                                                            winterm.close();
                                                                        }
                                                                    }
                                                                ],
                                                                modal:true
                                                            }).show();
                                                        }
                                                    },
                                                    {
                                                        xype:'button',
                                                        text:'Update',
                                                        iconCls:'edit',
                                                        handler: function() {
                                                            var winterm = Ext.create('Ext.Window',{
                                                                title:'Add Payment',
                                                                items:[
                                                                    {
                                                                        xtype:'form',
                                                                        layout:{
                                                                            type:'table',
                                                                            columns:1
                                                                        },
                                                                        defaults:{
                                                                            margin:'0 5',
                                                                            labelWidth:130,
                                                                            labelAlign:'left'
                                                                        },
                                                                        padding:5,
                                                                        items:[
                                                                            {
                                                                                xtype:'datefield',
                                                                                submitValue:false,
                                                                                fieldLabel:'Date',
                                                                                value:new Date(),
                                                                                width:250,
                                                                                readOnly:true
                                                                            },
                                                                            {
                                                                                xtype:'textfield',
                                                                                fieldLabel:'Term',
                                                                                width:200,
                                                                                value:9
                                                                            },
                                                                            {
                                                                                xtype:'textfield',
                                                                                fieldLabel:'From',
                                                                                width:400
                                                                            },
                                                                            {
                                                                                xtype:'numberfield',
                                                                                name:'name',
                                                                                hideTrigger:true,
                                                                                width:400,
                                                                                labelWidth:200,
                                                                                fieldStyle:'text-align:right',
                                                                                value:3500000,
                                                                                submitValue:false,
                                                                                fieldLabel:'Installment Amount',
                                                                                readOnly:true
                                                                            },
                                                                            {
                                                                                xtype:'numberfield',
                                                                                hideTrigger:true,
                                                                                width:400,
                                                                                labelWidth:200,
                                                                                fieldStyle:'text-align:right',
                                                                                value:1500000,
                                                                                submitValue:false,
                                                                                fieldLabel:'Interest Amount',
                                                                                readOnly:true
                                                                            },
                                                                            {
                                                                                xtype:'numberfield',
                                                                                hideTrigger:true,
                                                                                width:400,
                                                                                labelWidth:200,
                                                                                fieldStyle:'text-align:right',
                                                                                value:5000000,
                                                                                submitValue:false,
                                                                                fieldLabel:'Total',
                                                                                readOnly:true
                                                                            }

                                                                        ]
                                                                    }
                                                                ],
                                                                buttons: [
                                                                    {
                                                                        xtype:'button',
                                                                        text:'Save',
                                                                        margin: '5px',
                                                                        scale: 'large',
                                                                        ui: 's-button',
                                                                        cls: 's-blue ',
                                                                        buttonAlign: 'left',
                                                                        handler:function(c){
                                                                            
                                                                        }
                                                                    },
                                                                    {
                                                                        xtype:'button',
                                                                        text:'Close',
                                                                        margin: '5px',
                                                                        scale: 'large',
                                                                        ui: 's-button',
                                                                        cls: 's-grey',
                                                                        handler:function(){
                                                                            winterm.close();
                                                                        }
                                                                    }
                                                                ],
                                                                modal:true
                                                            }).show();
                                                        }
                                                    },
                                                    {
                                                        xype:'button',
                                                        text:'Delete',
                                                        iconCls:'delete',
                                                        handler: function() {
                                                            Ext.MessageBox.show({
                                                                title: 'Delete Data',
                                                                msg: 'Are You Sure?',
                                                                width: 300,
                                                                buttons: Ext.MessageBox.YESNO,
                                                                animateTarget: 'mb3'
                                                            });
                                                        }
                                                    }
                                                ]
                                            }
                                        ],
                                        width: '100%'
                                    }
                                ],
                                buttons: [
                                    {
                                        xtype:'button',
                                        text:'Save',
                                        margin: '5px',
                                        scale: 'large',
                                        ui: 's-button',
                                        cls: 's-blue ',
                                        buttonAlign: 'left',
                                        id:'btn-save-frm-loan',
                                        handler:function(c){
                                            var form = Ext.getCmp('frm-loan').getForm();
                                            var id = form.getValues();
                                            var url = m_api + '/loan/add';
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
                                                    Ext.Msg.alert('Failed', 'Cannot save data');
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
                        }
                    }
                ]
            }
        ]
    });


function showAproval(MemberLoanID)
{
//    var sm = grid.getSelectionModel();
//                            var sel = sm.getSelection();
                            
                            if(MemberLoanID!=null && MemberLoanID!=''){
//                                var id = sel[0].get('MemberLoanID');
                                var id = MemberLoanID;

                                var win = Ext.create('Ext.Window',{
                                    title:'Credit Approval',
                                    width:480,
                                    y:10,
                                    items:[
                                        {
                                            xtype:'panel',
                                            padding:5,
                                            loader: {
                                                url: '/api/index.php/loan/loaddetailmemberloan',
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
                                                
                                            },
                                            items:[
                                                {
                                                    xtype:'hidden',
                                                    name:'MemberLoanID'
                                                },
                                                {
                                                    xtype:'hidden',
                                                    id:'hidden-proposed-loan-amount',
                                                    submitValue:false,
                                                    name:'MemberLoanProposedAmount'
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
                                                    name:'MemberLoanTotalTenor',
                                                    fieldStyle:'text-align:right;font-size:15px;font-family:Courier New;',
                                                    fieldLabel:'<b>TOTAL TERM</b>',
                                                    allowBlank: false,
                                                    labelAlign:'top'
                                                },
                                                {
                                                    xtype:'numericfield',
                                                    fieldLabel:'<b>APPRISAL</b>',
                                                    labelAlign:'top',
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
                                                var url = m_api + '/loan/approve';
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
                                                var url = m_api + '/loan/reject';
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
//                                Ext.MessageBox.show({
//                                    title: 'Approve Failed',
//                                    msg: 'Please select the loan you want to approve',
//                                    width: 300,
//                                    buttons: Ext.MessageBox.OK,
//                                    animateTarget: 'mb3'
//                                });
                            }
}

// showAproval(m_MemberLoanID);

});

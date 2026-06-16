Ext.onReady(function(){

	Ext.tip.QuickTipManager.init();

	var loanStatus = Ext.create('Ext.data.Store', {
		fields: ['val', 'label'],
		data : [
			{'val':'', 'label':'Awaiting Approval'},
			{'val':'1', 'label':'Completed'},
			{'val':'2', 'label':'Approved'},
			{'val':'3', 'label':'Written Off'},
		]
	});

    var store = Ext.create('Ext.data.Store', {
        storeId: 'loanMemberProposalStore',
        autoLoad:true,
        fields: ['MemberLoanID', 'MemberId', 'MemberLoanNo', 'LoanTypeID', 'MemberLoanProposedAmount', 'MemberLoanApprovedAmount', 'MemberLoanTotalTenor', 'MemberLoanStatus', 'name', 'LoanTypeName', 'InterestTypeName' ],
        proxy: {
            type: 'rest',
            url: m_api + '/loan/getmemberloanproposal', // url that will load data with respect to start and limit params
            reader: {
                type: 'json',
                root: 'data',
                totalProperty: 'total',
                idProperty: 'MemberLoanID'
            },
            writer: {
                type: 'json'
            },
            /*api: {
                destroy: m_api + '/loan/delete'
            },*/
            appendId: true
        }
    });

    var grid = Ext.create('Ext.grid.Panel', {
        width: '100%',
        loadMask: true,
        style:'border: 1px solid #CCCCCC',
        store: Ext.data.StoreManager.lookup('loanMemberProposalStore'),
        columns: [
        	{text: 'Loan No.', dataIndex: 'MemberLoanNo', flex: 1},
            {text: 'Member Name', dataIndex: 'name', flex: 1},
            {text: 'Loan Type', dataIndex: 'LoanTypeName', width: 170},
            {text: 'Interest Type', dataIndex: 'InterestTypeName', width: 100},
            {text: 'Proposed Amount', dataIndex: 'MemberLoanProposedAmount', width: 150, xtype:'numbercolumn',align:'right'},
            {text: 'Approved Amount', dataIndex: 'MemberLoanProposedAmount', width: 150, xtype:'numbercolumn',align:'right'},
            {text: 'Terms', dataIndex: 'MemberLoanTotalTenor', width: 70},
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
                displayMsg: 'Displaying item(s) {0} - {1} of {2}',
                emptyMsg: "No item(s) to display"
            },{
                xtype:'toolbar',
                dock:'top',
                padding: '6px 7px',
                items:[ '->',
                	{
                		xtype: 'text',
                		text: 'Filter by',
                	},{
                		xtype: 'textfield',
		        		id: 'filterMemberName',
		        		emptyText: 'Member name'
                	},{
		        		xtype: 'combobox',
		        		id: 'filterLoanStatus',
		        		emptyText: 'Loan status',
		        		store: loanStatus,
					    queryMode: 'local',
					    valueField: 'val',
					    displayField: 'label',
		        	},{
		        		xtype: 'combobox',
		        		id: 'filterLoanType',
		        		emptyText: 'Loan type',
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
					    valueField: 'loanTypeID',
					    displayField: 'loanTypeName',
		        	},{
		        		xtype: 'button',
		        		margin: '0px 0px 0px 6px',
						text: 'Apply',
						handler: function(){
							var sMemberName = Ext.getCmp('filterMemberName').getValue();
							var sLoanStatus = Ext.getCmp('filterLoanStatus').getValue();
							var sLoanType = Ext.getCmp('filterLoanType').getValue();
							// console.log(sMemberName + ',' + sLoanStatus  + ',' +  sLoanType);

							grid.store.load({
								params: {
									memberName: sMemberName,
									loanStatus: sLoanStatus,
									loanType: sLoanType,
								}
							});
						}
		        	},{
		        		xtype: 'button',
		        		margin: '0px 0px 0px 6px',
		        		text: 'Reset',
		        		handler: function(){
		        			Ext.getCmp('filterMemberName').reset();
		        			Ext.getCmp('filterLoanStatus').reset();
		        			Ext.getCmp('filterLoanType').reset();
		        			grid.store.load();
		        		}
		        	}
                ]
            }
        ]
    }).show();

}); //end of Ext.onReady
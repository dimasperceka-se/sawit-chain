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
                        xtype:'textfield',
                        id:'no_pinjaman',
                        fieldLabel:'No Pinjaman'
                    },
                    {
                        xtype:'button',
                        text:'Search',
                        ui: 's-button',
                        cls: 's-blue ',
                        scale: 'medium',
                        handler: function() {
                            var store = Ext.data.StoreManager.lookup('loanStore');
                            store.on('beforeload', function (store, operation, eOpts) {
                                operation.params = {
                                    'no_pinjaman': Ext.getCmp('no_pinjaman').getValue()
                                };
                            });
                            store.load();
                        }
                    }
                ]
            }
        ]
    });

});

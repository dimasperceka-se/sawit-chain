Ext.onReady(function() {
    Ext.tip.QuickTipManager.init();
    
    var grid = Ext.create('Ext.grid.Panel', {
        store:Ext.create('Ext.data.Store', {
            storeId:'store-grid-authorized-trans-list',
            fields: ['id', 'memberTransactionNumber', 'memberTransactionType', 'memberTransactionDate', 'cashSourceName', 'memberSavingNo', 'memberTransactionAmount', 'memberTransactionRemark', 'debet', 'credit'],
            autoLoad: true,
            remoteSort:true,
            pageSize: 25,
            proxy: {
                type: 'rest',
                url: 'api/transaction/coop_transactions',
                reader: {
                    type: 'json',
                    root: 'data',
                    totalProperty: 'total'
                }
            }
        }),
        width: '100%',
        id: 'grid',
        minHeight: 350,
        style: 'border:1px solid #CCC;',
        renderTo: 'ext-content',
        loadMask: true,
        selType: 'rowmodel',
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
                                        // console.log(response);
                                        Ext.data.StoreManager.lookup('store-grid-authorized-trans-list').load();
                                    },
                                    failure: function(response, opts) {
                                        console.log(response)
                                    }
                                });
                            }
                        });
                    }
                }]
            }
        ]
    });
});

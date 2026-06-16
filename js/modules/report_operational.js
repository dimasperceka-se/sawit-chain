Ext.onReady(function(){

	Ext.tip.QuickTipManager.init();

    var store = Ext.create('Ext.data.Store', {
        storeId: 'reportOperational',
        autoLoad:true,
        fields: ['transactionID', 'transactionNumber', 'transactionDate', 'transactionName', 'transactionAmount', 'cashSourceName', 'CoaTitle'],
        proxy: {
            type: 'rest',
            url: m_rpt + '/operationaltransactions', // url that will load data with respect to start and limit params
            reader: {
                type: 'json',
                root: 'data',
                totalProperty: 'total',
                idProperty: 'transactionID'
            },
            writer: {
                type: 'json'
            },
            appendId: true
        }
    });

    var trxType = Ext.create('Ext.data.Store', {
        fields: ['val', 'label'],
        data : [
            {'val':'1', 'label':'Setoran'},
            {'val':'2', 'label':'Penarikan'}
        ]
    });

    var coa = Ext.create('Ext.data.Store',{
        fields: ['CoaCode', 'CoaTitle'],
        proxy: {
            type: 'rest',
            url: m_rpt + '/combo_coa', // url that will load data with respect to start and limit params
            reader: {
                type: 'json',
                root: 'data',
                totalProperty: 'total',
                idProperty: 'CoaID'
            },
            writer: {
                type: 'json'
            },
            appendId: true
        }
    });

    var filterPanel = Ext.create('Ext.panel.Panel', {
        width: '100%',
        title: 'Filter',
        id: 'gridFilterPanel',
        renderTo: 'ext-content',
        style:'border: 1px solid #CCCCCC',
        layout: { type: 'table', columns: 6},
        items: [
            {
                xtype:'datefield',
                id:'filterTrxDate',
                emptyText: lang('Tgl Transaksi'),
                format: 'Y-m-d',
                padding: '10px 11px 0'
            },{
                xtype: 'textfield',
                id: 'filterTrxNo',
                emptyText: lang('No. Transaksi'),
                padding: '10px 11px 0'
            },{
                xtype: 'combobox',
                id: 'filterTrxType',
                emptyText: lang('Jenis transaksi'),
                store: trxType,
                queryMode: 'local',
                valueField: 'val',
                displayField: 'label',
                padding: '10px 11px 0'
            },{
                xtype: 'textfield',
                id: 'filterCashboxName',
                emptyText: lang('Cashbox'),
                padding: '10px 11px 0'
            },{
                colspan: 1,
                items: [
                    {
                        xtype: 'button',
                        text: lang('Apply'),
                        margin: '20px 0px 20px 11px',
                        handler: function(){
                            var sTrxDate = Ext.getCmp('filterTrxDate').getRawValue();
                            var sTrxNo = Ext.getCmp('filterTrxNo').getValue();
                            var sTrxType = Ext.getCmp('filterTrxType').getValue();
                            var sCashboxName = Ext.getCmp('filterCashboxName').getValue();
                            var sCOA = Ext.getCmp('filterCOA').getValue();
                            var sTrxName = Ext.getCmp('filterTrxName').getValue();

                            grid.store.load({
                                params:{
                                    trxDate : sTrxDate,
                                    trxNo : sTrxNo,
                                    trxType : sTrxType,
                                    cashboxName : sCashboxName,
                                    coa : sCOA,
                                    trxName : sTrxName
                                }
                            });
                        }
                    },{
                        xtype: 'button',
                        text: lang('Reset'),
                        margin: '20px 0px 20px 6px',
                        handler: function(){
                            Ext.getCmp('filterTrxDate').reset();
                            Ext.getCmp('filterTrxNo').reset();
                            Ext.getCmp('filterTrxType').reset();
                            Ext.getCmp('filterCashboxName').reset();
                            Ext.getCmp('filterCOA').reset();
                            Ext.getCmp('filterTrxName').reset();
                            grid.store.load();
                        }
                    }
                ]
            },{
                xtype: 'button',
                text: lang('Export to xls'),
                ui: 's-button',
                scale: 'medium',
                margin: '20px 0px 20px 11px',
                handler: function(){
                    alert('Xport 2 .xls');
                }
            },{
                colspan: 2,
                items: [
                    {
                        xtype: 'combobox',
                        width: 300,
                        id: 'filterCOA',
                        emptyText: lang('Tujuan (COA)'),
                        store: coa,
                        valueField: 'CoaCode',
                        displayField: 'CoaTitle',
                        padding: '0 11px 20px 11px'
                    }
                ]
            },{
                xtype: 'textfield',
                id: 'filterTrxName',
                emptyText: lang('Nama Penerima/Penyetor'),
                padding: '0 11px 20px 11px',
            }
        ]
    });

    var grid = Ext.create('Ext.grid.Panel', {
        width: '100%',
        loadMask: true,
        style:'border: 1px solid #CCCCCC',
        store: Ext.data.StoreManager.lookup('reportOperational'),
        columns: [
        	{ text: 'No.', xtype: 'rownumberer', width: '3%' },
            {
                text: lang('Tgl Transaksi'),
                dataIndex: 'transactionDate',
                width: 120,
            },{
                text: lang('No Transaksi'),
                dataIndex: 'transactionNumber',
                width: 190,
            },{
                text: lang('Jenis Transaksi'),
                dataIndex: 'transactionType',
                width: 120,
                renderer: function(val){
                    var txt = (val === '1') ? 'Setoran': 'Penarikan';
                    return txt;
                }
            },{
                text: lang('Cashbox'),
                dataIndex: 'cashSourceName',
                flex: 1,
            },{
                text: lang('Tujuan (COA)'),
                dataIndex: 'CoaTitle',
                flex: 1,
            },{
                text: lang('Nama Penerima/Penyetor'),
                dataIndex: 'transactionName',
                flex: 1,
            },{
                xtype:'numbercolumn',
                format: '0,000',
                align:'right',
                text: lang('Jumlah'),
                width: 170,
                dataIndex: 'transactionAmount',
            }
            
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
            },
        ]
    }).show();

}); //end of Ext.onReady
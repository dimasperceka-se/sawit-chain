Ext.onReady(function(){

	Ext.tip.QuickTipManager.init();

    var store = Ext.create('Ext.data.Store', {
        storeId: 'reportSaving',
        autoLoad:true,
        fields: ['MemberTransactionID', 'MemberTransactionType', 'MemberTransactionNumber', 'MemberTransactionDate', 'MemberTransactionAmount', 'primaryNo', 'cashSourceName', 'savingTypeName'],
        proxy: {
            type: 'rest',
            url: m_rpt + '/membertransactions', // url that will load data with respect to start and limit params
            reader: {
                type: 'json',
                root: 'data',
                totalProperty: 'total',
                idProperty: 'MemberTransactionID'
            },
            writer: {
                type: 'json'
            },
            appendId: true
        }
    });

    var savingType = Ext.create('Ext.data.Store',{
        fields: ['savingTypeID', 'savingTypeName'],
        proxy: {
            type: 'rest',
            url: m_rpt + '/combo_savingtype', // url that will load data with respect to start and limit params
            reader: {
                type: 'json',
                root: 'data',
                totalProperty: 'total',
                idProperty: 'savingTypeID'
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
                padding: '10px 11px 0',
            },{
                xtype: 'textfield',
                id: 'filterTrxNo',
                emptyText: lang('No. Transaksi'),
                padding: '10px 11px 0',
            },{
                xtype: 'textfield',
                id: 'filterMemberNo',
                emptyText: lang('Member no.'),
                padding: '10px 11px 0',
            },{
                xtype: 'combobox',
                id: 'filterSavingType',
                emptyText: lang('Jenis simpanan'),
                store: savingType,
                valueField: 'savingTypeID',
                displayField: 'savingTypeName',
                padding: '10px 11px 0',
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
                            var sMemberNo = Ext.getCmp('filterMemberNo').getValue();
                            var sSavingType = Ext.getCmp('filterSavingType').getValue();
                            var sTrxType = Ext.getCmp('filterTrxType').getValue();
                            var sCashboxName = Ext.getCmp('filterCashboxName').getValue();

                            grid.store.load({
                                params:{
                                    trxDate : sTrxDate,
                                    trxNo : sTrxNo,
                                    memberNo : sMemberNo,
                                    savingType : sSavingType,
                                    trxType : sTrxType,
                                    cashboxName : sCashboxName
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
                            Ext.getCmp('filterMemberNo').reset();
                            Ext.getCmp('filterSavingType').reset();
                            Ext.getCmp('filterTrxType').reset();
                            Ext.getCmp('filterCashboxName').reset();
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
                xtype: 'combobox',
                id: 'filterTrxType',
                emptyText: lang('Jenis transaksi'),
                store: trxType,
                queryMode: 'local',
                valueField: 'val',
                displayField: 'label',
                padding: '0 11px 20px 11px',
            },{
                xtype: 'textfield',
                id: 'filterCashboxName',
                emptyText: lang('Cashbox'),
                padding: '0 11px 20px 11px',
            }
        ]
    });

    var grid = Ext.create('Ext.grid.Panel', {
        width: '100%',
        loadMask: true,
        style:'border: 1px solid #CCCCCC',
        store: Ext.data.StoreManager.lookup('reportSaving'),
        columns: [
        	{ text: 'No.', xtype: 'rownumberer', width: '3%' },
            {
                text: lang('Tgl Transaksi'),
                dataIndex: 'MemberTransactionDate',
                width: 120
            },
            {
                text: lang('No Transaksi'),
                dataIndex: 'MemberTransactionNumber',
                width: 190
            },{
                text: lang('No Anggota'),
                dataIndex: 'primaryNo',
                width: 190
            },{
                text: lang('Jenis Transaksi'),
                dataIndex: 'MemberTransactionType',
                width: 140,
                renderer: function(val){
                    var txt = (val === '1') ? 'Setoran': 'Penarikan';
                    return txt;
                }
            },{
                text: lang('Jenis Simpanan'),
                dataIndex: 'savingTypeName',
                flex: 1
            },{
                text: lang('Cashbox'),
                dataIndex: 'cashSourceName',
                flex: 1
            },{
                xtype:'numbercolumn',
                format: '0,000',
                align:'right',
                text: lang('Jumlah'),
                width: 170,
                dataIndex: 'MemberTransactionAmount'
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
                emptyMsg: lang("No item(s) to display")
            },
        ]
    }).show();

}); //end of Ext.onReady
Ext.onReady(function(){

	Ext.tip.QuickTipManager.init();

	var store = Ext.create('Ext.data.Store',{
            storeId: 'JournalClosedStore',
            autoLoad:true,
            fields: ['JournalClosedID', 'JournalClosedDate', 'JournalClosedRemark'],
            proxy: {
                type: 'rest',
                url: m_api + '/coop_tutup_tahun/getclosedbook', // url that will load data with respect to start and limit params
                reader: {
                    type: 'json',
                    root: 'data',
                    totalProperty: 'total',
                    idProperty: 'JournalClosedID'
                },
                writer: {
                    type: 'json'
                },
                appendId: true
            }
	});

	var grid = Ext.create('Ext.grid.Panel', {
        width: '100%',
        loadMask: true,
        style:'border: 1px solid #CCCCCC',
        store: Ext.data.StoreManager.lookup('JournalClosedStore'),
        columns: [
        	{text: 'Tahun', dataIndex: 'JournalClosedDate', flex: 1, xtype: 'datecolumn',   format:'Y'},
        	{text: 'Laba/Rugi', dataIndex: 'pnl', flex: 1},
        	{text: 'Neraca', dataIndex: 'balance', flex: 1},
        	{text: 'Remark', dataIndex: 'JournalClosedRemark', flex: 1}
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
                items:[{
                    xtype:'button',
                    iconCls:'add',
                    text:'Tutup Tahun',
                    cls: m_act_add,
                    scope: this,
                    handler: function(){
                        generateWin();
                    }
                },{
                    xtype:'button',
                    iconCls:'edit',
                    text:'Hitung Sisa Hasil Usaha',
                    cls: m_act_update,
                    scope: this,
                    handler: function(){
                        
                    }
                },{
                    xtype:'button',
                    iconCls:'delete',
                    text:'Print',
                    scope: this,
                    handler: function(){
                        
                    }
                }]
            }
        ]
    }).show();

    function generateWin(){
    	Ext.create('Ext.window.Window', {
            title: '',
            id: 'winTutupTahun',
            modal: true,
            width: 580,
            layout: 'fit',
            items: Ext.create('Ext.form.Panel',{
            	bodyPadding: 11,
                autoScroll: true,
                id: 'frmTutupTahun',
                fieldDefaults: {
                    labelAlign: 'right',
                    labelWidth: 150
                },
                items: [
                    {
                        xtype: 'combo',
                        labelAlign:'right',
                        width:250,
                        id:'cmb-tahun-tutup-buku',
                        fieldLabel: 'Tahun Tutup Buku',
                        store: Ext.create('Ext.data.Store', {
                            fields: ['YEAR_ID', 'YEAR_NAME'],
                            autoLoad: true,
                            proxy: {
                                type: 'rest',
                                url: m_api + '/common/getyear', // url that will load data with respect to start and limit params
                                reader: {
                                    type: 'json',
                                    root: 'data',
                                    totalProperty: 'total'
                                }
                            }
                        }),
                        listeners:{
                            change: function(c,v) {
                                Ext.getCmp('juml-laba-rugi-tutup-buku').setValue(123456);
                            }
                        },
                        displayField: 'YEAR_NAME',
                        valueField: 'YEAR_ID',
                        name: 'YEAR_ID'

                    },
                    {
                        xtype:'textfield',
                        width:500,
                        readOnly:true,
                        name:'SHU_AMOUNT',
                        id:'juml-laba-rugi-tutup-buku',
                        fieldLabel:'Jumlah SHU'
                    },
                    {
                        xtype:'fieldset',
                        title:'Komponen',
                        items:[
                            {
                                xtype:'container',
                                layout:{
                                    type:'hbox'
                                },
                                items:[
                                    {
                                        xtype:'numberfield',
                                        width:200,
                                        hideTrigger:true,
                                        labelAlign:'right',
                                        fieldLabel:'Jasa Modal',
                                        listeners:{
                                            change:function(c,v){
                                                
                                                var pnl = Ext.getCmp('juml-laba-rugi-tutup-buku').getValue();
                                                Ext.getCmp('nominal-jasa-modal-tutup-buku').setValue((v/100) * pnl);
                                            }
                                        }
                                    },
                                    {
                                        xtype:'displayfield',
                                        margin:5,
                                        value:'%'
                                    },
                                    {
                                        xtype:'displayfield',
                                        width:300,
                                        margin:5,
                                        id:'nominal-jasa-modal-tutup-buku',
                                        style:'text-align:right',
                                        value:'0,00'
                                    }
                                ]
                            },
                            {
                                xtype:'container',
                                layout:{
                                    type:'hbox'
                                },
                                items:[
                                    {
                                        xtype:'numberfield',
                                        width:200,
                                        hideTrigger:true,
                                        labelAlign:'right',
                                        fieldLabel:'Jasa Usaha',
                                        listeners:{
                                            change:function(c,v){
                                                
                                                var pnl = Ext.getCmp('juml-laba-rugi-tutup-buku').getValue();
                                                Ext.getCmp('nominal-jasa--usaha-tutup-buku').setValue((v/100) * pnl);
                                            }
                                        }
                                    },
                                    {
                                        xtype:'displayfield',
                                        margin:5,
                                        value:'%'
                                    },
                                    {
                                        xtype:'displayfield',
                                        width:300,
                                        margin:5,
                                        id:'nominal-jasa-usaha-tutup-buku',
                                        style:'text-align:right',
                                        value:'0,00'
                                    }
                                ]
                            },
                            {
                                xtype:'container',
                                layout:{
                                    type:'hbox'
                                },
                                items:[
                                    {
                                        xtype:'numberfield',
                                        width:200,
                                        hideTrigger:true,
                                        labelAlign:'right',
                                        fieldLabel:'Dana Cadangan',
                                        listeners:{
                                            change:function(c,v){
                                                
                                                var pnl = Ext.getCmp('juml-laba-rugi-tutup-buku').getValue();
                                                Ext.getCmp('nominal-dana-cadangan-tutup-buku').setValue((v/100) * pnl);
                                            }
                                        }
                                    },
                                    {
                                        xtype:'displayfield',
                                        margin:5,
                                        value:'%'
                                    },
                                    {
                                        xtype:'displayfield',
                                        width:300,
                                        margin:5,
                                        id:'nominal-dana-cadangan-tutup-buku',
                                        style:'text-align:right',
                                        value:'0,00'
                                    }
                                ]
                            },
                            {
                                xtype:'container',
                                layout:{
                                    type:'hbox'
                                },
                                items:[
                                    {
                                        xtype:'numberfield',
                                        width:200,
                                        hideTrigger:true,
                                        labelAlign:'right',
                                        fieldLabel:'Dana Pengurus'
                                    },
                                    {
                                        xtype:'displayfield',
                                        margin:5,
                                        value:'%'
                                    },
                                    {
                                        xtype:'displayfield',
                                        width:300,
                                        margin:5,
                                        id:'nominal-dana-pengurus-tutup-buku',
                                        style:'text-align:right',
                                        value:'0,00'
                                    }
                                ]
                            },
                            {
                                xtype:'container',
                                layout:{
                                    type:'hbox'
                                },
                                items:[
                                    {
                                        xtype:'numberfield',
                                        width:200,
                                        hideTrigger:true,
                                        labelAlign:'right',
                                        fieldLabel:'Dana Karyawan'
                                    },
                                    {
                                        xtype:'displayfield',
                                        margin:5,
                                        value:'%'
                                    },
                                    {
                                        xtype:'displayfield',
                                        width:300,
                                        margin:5,
                                        id:'nominal-dana-karyawan-tutup-buku',
                                        style:'text-align:right',
                                        value:'0,00'
                                    }
                                ]
                            },
                            {
                                xtype:'container',
                                layout:{
                                    type:'hbox'
                                },
                                items:[
                                    {
                                        xtype:'numberfield',
                                        width:200,
                                        hideTrigger:true,
                                        labelAlign:'right',
                                        fieldLabel:'Dana Lain-lain'
                                    },
                                    {
                                        xtype:'displayfield',
                                        margin:5,
                                        value:'%'
                                    },
                                    {
                                        xtype:'displayfield',
                                        width:300,
                                        margin:5,
                                        id:'nominal-dana-lain-tutup-buku',
                                        style:'text-align:right',
                                        value:'0,00'
                                    }
                                ]
                            }
                        ]
                    }
                ],
                buttons: [{
                    text:'Simpan',
                    iconCls:'save',
                    handler:function() {
                        var frm = Ext.getCmp('frmTutupTahun').getForm();
                        if(frm.isValid()) {
                            frm.submit({
                                url:m_api + '/coop_tutup_tahun/closebook',
                                success: function() {
                                    Ext.getCmp('winTutupTahun').close();
                                }
                            });
                        }
                        
                        
                    }
                },{
                    text:'Batal',
                    iconCls:'cancel',
                    handler:function() {
                        Ext.getCmp('winTutupTahun').close();
                    }
                }]
            })
    	}).show();
    }

    function editData(){
    	var grid = Ext.getCmp('gridCashbox');
    	var slct = grid.getSelectionModel().getSelection();
    	
    	if(slct.length >= 1){
    		Ext.Ajax.request({
    			url: m_crud + '/getbyid',
    			method: 'POST',
    			params: { cashboxID: slct[0].data.CashSourceID },
    			success: function(response){
    				var q = JSON.parse(response.responseText);

    				if(q.total >= 1){
    					generateWin();
				        var win = Ext.getCmp('winCashbox');

				        //set form values
				        Ext.getCmp('cashboxID').setValue(q.data.CashSourceID);
				        Ext.getCmp('cashboxName').setValue(q.data.CashSourceName);
				        if(q.data.BankID != 0) { Ext.getCmp('selectBank').setValue(q.data.BankID); }
				        Ext.getCmp('bankAccNo').setValue(q.data.CashSourceNo);
				        if(q.data.CoaCode != 0) { Ext.getCmp('selectCOA').setValue(q.data.CoaCode); }

				        win.setTitle('Edit Cashbox');
				        win.show();
    				}else{
    					Ext.Msg.alert('System Error', 'Some error occurred while retrieving data, please contact Administrator');
    				}
    			}
    		});
    	}else{
    		Ext.Msg.alert('Error', 'Please select a record to edit');
    	}
    }

    function saveData(){
    	var form = Ext.getCmp('frmAddCashbox');

    	if(!form.isValid()){
    		return false;
    	}

    	Ext.Ajax.request({
			url: m_crud + '/savedata',
			method: 'POST',
			params: {
				cashboxID: Ext.getCmp('cashboxID').getValue(),
				cashboxName: Ext.getCmp('cashboxName').getValue(),
				bank: Ext.getCmp('selectBank').getValue(),
				bankAccNo: Ext.getCmp('bankAccNo').getValue(),
				coa: Ext.getCmp('selectCOA').getValue(),
			},
			success: function(response){
				Ext.getCmp('winCashbox').close();
				Ext.getCmp('gridCashbox').store.load();
				
				var rsp = JSON.parse(response.responseText);
				Ext.Msg.alert('Success', rsp.data.msg);
			},
			failure: function(response, opts) {
                var obj = Ext.decode(response.responseText);
                Ext.MessageBox.alert('System error', 'Could not connect to the database. Please retry later');
            }
		});
    }

    function deleteData(){
    	var grid = Ext.getCmp('gridCashbox');
    	var slct = grid.getSelectionModel().getSelection();
    	
    	if(slct.length >= 1){
    		Ext.MessageBox.confirm('Delete Confirmation', 'Delete selected data, please confirm to proceed', function(r) {
                if (r == 'yes') {
                    Ext.Ajax.request({
                        waitMsg: 'Please Wait',
                        url: m_crud + '/deletecashbox',
                        method: 'POST',
                        params: {did: slct[0].data.cashSourceID},
                        success: function(response, opts) {
                            Ext.getCmp('gridCashbox').store.load();

                            var obj = Ext.decode(response.responseText);
                            switch (obj.success) {
                                case true:
                                    Ext.MessageBox.alert('Success', 'Data has been deleted');
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
    	}else{
    		Ext.Msg.alert('Error', 'Please select a record to delete');
    	}
    }

}); //end of Ext.onReady
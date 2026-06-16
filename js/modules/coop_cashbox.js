Ext.onReady(function(){

	Ext.tip.QuickTipManager.init();

	var store = Ext.create('Ext.data.Store',{
		storeId: 'cashSourceStore',
        autoLoad:true,
        fields: ['CashSourceID', 'CashSourceName', 'CashSourceNo', 'CoaCode', 'CoopID', 'BankID', 'BankName', 'CoaTitle'],
        proxy: {
        	type: 'rest',
        	url: m_api + '/coop_cashbox/getcashsourcelist', // url that will load data with respect to start and limit params
            reader: {
                type: 'json',
                root: 'data',
                totalProperty: 'total',
                idProperty: 'CashSourceID'
            },
            writer: {
                type: 'json'
            },
            appendId: true
        }
	});

	var grid = Ext.create('Ext.grid.Panel', {
        width: '100%',
        id: 'gridCashbox',
        loadMask: true,
        style:'border: 1px solid #CCCCCC',
        store: Ext.data.StoreManager.lookup('cashSourceStore'),
        columns: [
        	{text: 'Name', dataIndex: 'CashSourceName', flex: 1},
        	{text: 'Bank Name', dataIndex: 'BankName', width: 120},
        	{text: 'Bank Acc. Number', dataIndex: 'CashSourceNo', flex: 1},
        	{text: 'COA Code', dataIndex: 'CoaCode', width: 90},
        	{text: 'COA Name', dataIndex: 'CoaTitle', flex: 1},
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
                items:[
                	{
                		xtype:'button',
                        iconCls:'add',
                        text:'Add',
                        cls: m_act_add,
                        scope: this,
                        handler: function(){
				           	generateWin();
				           	var win = Ext.getCmp('winCashbox');

				           	win.setTitle('Add Cashbox');
				           	win.show();
                        }
                	},{
                		xtype:'button',
                        iconCls:'edit',
                        text:'Edit',
                        cls: m_act_update,
                        scope: this,
                        handler: function(){
                        	editData();
                        }
                	},{
                		xtype:'button',
                        iconCls:'delete',
                        text:'Delete',
                        cls: m_act_delete,
                        scope: this,
                        handler: function(){
                        	deleteData();
                        }
                	}, '->',{
                		xtype: 'text',
                		text: 'Filter by',
                	},{
                		xtype: 'textfield',
		        		id: 'filterCashboxName',
		        		emptyText: 'Cashbox name'
                	},{
                        xtype: 'combobox',
		        		id: 'filterCOACode',
		        		emptyText: 'COA name',
                        width: 320,
                        store: Ext.create('Ext.data.Store', {
                            fields: ['CoaID', 'CoaCode', 'CoaDisplay'],
                            autoLoad: true,
                            proxy: {
                                type: 'rest',
                                url: m_api + '/coop_cashbox/getcombocoa', 
                                reader: {
                                    type: 'json',
                                    root: 'data',
                                    totalProperty: 'total'
                                }
                            }
                        }),
                        valueField: 'CoaCode',
                        displayField: 'CoaDisplay',
                	},{
                		xtype: 'textfield',
		        		id: 'filterBankNo',
		        		emptyText: 'Bank account no'
                	},{
		        		xtype: 'button',
		        		margin: '0px 0px 0px 6px',
						text: 'Apply',
						handler: function(){
							var sCashboxName = Ext.getCmp('filterCashboxName').getValue();
							var sCOACode = Ext.getCmp('filterCOACode').getValue();
							var sBankNo = Ext.getCmp('filterBankNo').getValue();

							grid.store.load({
								params: {
									cashboxName: sCashboxName,
									COACode: sCOACode,
									bankNo: sBankNo,
								}
							});
						}
					},{
		        		xtype: 'button',
		        		margin: '0px 0px 0px 6px',
		        		text: 'Reset',
		        		handler: function(){
		        			Ext.getCmp('filterCashboxName').reset();
		        			Ext.getCmp('filterCOACode').reset();
		        			Ext.getCmp('filterBankNo').reset();
		        			grid.store.load();
		        		}
		        	}
		        ]
            }
        ]
    }).show();

    function generateWin(){
    	Ext.create('Ext.window.Window', {
    		title: '',
            id: 'winCashbox',
            modal: true,
            width: 580,
            layout: 'fit',
            items: Ext.create('Ext.form.Panel',{
            	bodyPadding: 11,
                autoScroll: true,
                id: 'frmAddCashbox',
                fieldDefaults: {
                    labelAlign: 'left',
                    labelWidth: 150
                },
                items: [
                	{
                		xtype: 'textfield',
                        id: 'cashboxID',
                        hidden: true,
                	},{
                		xtype: 'textfield',
                        fieldLabel: 'Cashbox Name <span style="color:red;font-weight:bold">*</span>',
                		width: 520,
                        id: 'cashboxName',
                        allowBlank: false,
                	},{
                		xtype: 'combobox',
                        fieldLabel: 'Bank',
                		width: 450,
		        		id: 'selectBank',
		        		store: Ext.create('Ext.data.Store', {
		        			fields: ['BankID', 'BankName'],
                            autoLoad: true,
                            proxy: {
                                type: 'rest',
                                url: m_api + '/coop_cashbox/getcombobank', 
					            reader: {
					                type: 'json',
					                root: 'data',
					                totalProperty: 'total'
					            }
                            }
		        		}),
					    valueField: 'BankID',
					    displayField: 'BankName',
                	},{
                		xtype: 'textfield',
                        fieldLabel: 'Bank Acc. Number',
                        width: 520,
                        id: 'bankAccNo',
                	},{
                		xtype: 'combobox',
                        fieldLabel: 'COA',
                        width: 450,
		        		id: 'selectCOA',
		        		store: Ext.create('Ext.data.Store', {
		        			fields: ['CoaID', 'CoaCode', 'CoaDisplay'],
                            autoLoad: true,
                            proxy: {
                                type: 'rest',
                                url: m_api + '/coop_cashbox/getcombocoa', 
					            reader: {
					                type: 'json',
					                root: 'data',
					                totalProperty: 'total'
					            }
                            }
		        		}),
					    valueField: 'CoaCode',
					    displayField: 'CoaDisplay',
                	}
                ],
                buttons: [
                	{
                		xtype: 'button',
                        text:'Save',
                        margin: '5px',
                        scale: 'large',
                        ui: 's-button',
                        cls: 's-blue ',
                        handler: function(){
                        	saveData();
                        }
                	},{
                		xtype:'button',
                        text:'Close',
                        margin: '5px',
                        scale: 'large',
                        ui: 's-button',
                        handler: function(){
                        	Ext.getCmp('winCashbox').close();
                        }
                	}
                ]
            })
    	});
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
                        params: {did: slct[0].data.CashSourceID},
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
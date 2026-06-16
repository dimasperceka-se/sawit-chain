Ext.onReady(function () {

	Ext.tip.QuickTipManager.init();

	var storeCoaList = Ext.create('Ext.data.Store', {
	    extend: 'Ext.data.Model',
	    fields: ['id', 'code', 'title'],
	    autoLoad: true,
	    pageSize: 50,
	    proxy: {
	        type: 'ajax',
	        url: m_api + 'savingtype/getCOA',
	        reader: {
	            type: 'json',
	            root: 'data',
	            totalProperty: 'total'
	        }
	    }
	});

	Ext.define('GridCoaSavingTypeList', {
	    itemId: 'GridCoaSavingTypeList',
	    id: 'GridCoaSavingTypeList',
	    extend: 'Ext.grid.Panel',
	    alias: 'widget.GridCoaSavingTypeList',
	    store: storeCoaList,
	    loadMask: true,
	    columns: [
	    {
	            text: 'Select',
	            width: 65,
	            xtype: 'actioncolumn',
	            tooltip: 'Select',
	            align: 'center',
	            icon: m_baseurl + '/images/icons/silk/add.png',
	            handler: function(grid, rowIndex, colIndex, actionItem, event, selectedRecord, row) {
	                    Ext.getCmp('coaNameSavingTypeID').setValue(selectedRecord.data.id);
	                    Ext.getCmp('coaNameSavingType').setValue(selectedRecord.data.title);
	                    Ext.getCmp('wCoaSavingTypePopup').hide();
	            }
	        },
	        { text: 'id', dataIndex: 'id', hidden: true },
	        { text: 'COA Code', flex:1, width: '25%', dataIndex: 'code' },
	        { text: 'COA Name', width: '75%', dataIndex: 'title' }        
	    ]
	    , dockedItems: [{
	            xtype: 'pagingtoolbar',
	            store: storeCoaList, // same store GridPanel is using
	            dock: 'bottom',
	            displayInfo: true
	        }
	    ]
	});

    var mainGridStore = Ext.create('Ext.data.Store',{
		storeId: 'mainGridStore',
        fields: ['SavingTypeID', 'SavingTypeName', 'SavingTypeMinAmount', 'SavingTypeMinTrans', 'SavingTypeInterestRate', 'SavingRemark', 'SavingTypeStatus'],
        autoLoad:true,
        proxy: {
        	type: 'rest',
        	url: m_crud + 's',
            reader: {
                type: 'json',
                root: 'data',
                totalProperty: 'total',
            },
            writer: {
                type: 'json'
            },
            appendId: true
        }
	});

    var mainGrid = Ext.create('Ext.grid.Panel', {
        id: 'mainGrid',
        width: '100%',
        height: 560,
        style: 'border:1px solid #CCC;',
        loadMask: true,
    	store: Ext.data.StoreManager.lookup('mainGridStore'),
        columns: [
        	{
                text: 'No',
                xtype: 'rownumberer',
                width: '5%'
            },{
                text: lang('Saving Type Name'),
                dataIndex: 'SavingTypeName',
                flex: 1
            },{
                text: lang('Min Payment'),
                renderer:  Ext.util.Format.numberRenderer('0,000'),
                align:'right',
                xtype:'numbercolumn',
                width: '15%',
                dataIndex: 'SavingTypeMinAmount'
            },{
                text: lang('Min Transaction'),
                renderer:  Ext.util.Format.numberRenderer('0,000'),
                align:'right',
                width: '15%',
                dataIndex: 'SavingTypeMinTrans'
            },{
                text: lang('Interest Rate'),
                renderer:  Ext.util.Format.numberRenderer('0,000'),
                align:'right',
                width: '15%',
                dataIndex: 'SavingTypeInterestRate'
            },{
                text: lang('Status'),
                width: 80,
                dataIndex: 'SavingTypeStatus',
                renderer: function(v){
                	var stat;
                	return stat = (v === '1') ? 'Active' : 'Inactive';
                }
            },{
                text: lang('Remark'),
                width: '21%',
                dataIndex: 'SavingRemark'
            }
        ],
        dockedItems: [
        	{
                xtype: 'pagingtoolbar',
                store: mainGridStore, 
                dock: 'bottom',
                displayInfo: true
            },{
                xtype:'toolbar',
                dock:'top',
                padding: '6px 7px',
                items:[
                	{
                		text: 'Add',
                        iconCls: 'add',
                        handler: function () {
                        	generateWin();
                        	var win = Ext.getCmp('winMgmt');

                        	win.setTitle(lang('Add Saving Type'));
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
                	},'->',
		            {
		        		xtype: 'textfield',
		        		id: 'filterSavingTypeName',
		        		emptyText: lang('Search by name')
		        	},{
		        		xtype: 'button',
		        		margin: '0px 0px 0px 6px',
						text: lang('Apply'),
						handler: function(){
							mainGrid.store.load({
								params: {

								}
							});
						}
					},{
		        		xtype: 'button',
		        		margin: '0px 0px 0px 6px',
		        		text: lang('Reset'),
		        		handler: function(){
		        			Ext.getCmp('filterSavingTypeName').reset();
		        			mainGrid.store.load();
		        		}
		        	}
                ]
            }
        ],
        renderTo: 'ext-content'
    }).show();

    function generateWin(){
    	Ext.create('Ext.window.Window', {
    		title: '',
            id: 'winMgmt',
            modal: true,
            width: 1200,
            layout: 'fit',
            items: [{
            	xtype: 'form',
           		id: 'formSavingType',
            	layout: 'column',
           		items: [
           			{
           				xtype: 'container',
           				columnWidth: 0.5,
           				items: [
           					{
		           				xtype: 'fieldset',
		           				style: 'margin: 5px; padding: 4px 7px;',
		           				fieldDefaults: {
				                    labelAlign: 'left',
				                    labelWidth: 120
				                },
		           				items: [
		           					{
				                		xtype: 'textfield',
				                        id: 'fieldSavingTypeID',
				                        hidden: true
				                	},{
				                		xtype: 'checkbox',
				                		id: 'fieldSavingTypeStatus',
				                		fieldLabel: lang('Active'),
				                		boxLabel: '',
	                                    checked: true,
	                                    inputValue: 1,
	                                    uncheckedValue: 0
				                	},{
				                		xtype: 'checkbox',
				                		id: 'fieldSavingTypeDefault',
				                		fieldLabel: lang('Default Saving'),
				                		boxLabel: 'Note: Automatically created on member activated',
	                                    checked: false,
	                                    inputValue: 1,
	                                    uncheckedValue: 0
				                	}
		           				]
           					},{
           						xtype: 'fieldset',
		           				style: 'margin: 5px; padding: 4px 7px;',
		           				fieldDefaults: {
				                    labelAlign: 'left',
				                    labelWidth: 120
				                },
		           				items: [
		           					{
	                                    xtype: 'textfield',
	                                    id: 'fieldSavingTypeCode',
	                                    width: 515,
	                                    allowBlank: false,
	                                    fieldLabel: lang('Code') + '  <span style="color:red">*</span>',
	                                },{
				                		xtype: 'textfield',
				                        id: 'fieldSavingTypeName',
				                        fieldLabel:  lang('Name') + ' <span style="color:red;font-weight:bold">*</span>',
				                        allowBlank: false,
				                        width: 515
				                	},{
	                                    xtype: 'radiogroup',
	                                    id: 'fieldSavingTypeSHU',
	                                    columns: 2,
	                                    allowBlank: false,
	                                    msgTarget: 'side',
	                                    fieldLabel: lang('Type/SHU') + ' <span style="color:red">*</span>',
	                                    vertical: true,
	                                    items: [
	                                        {boxLabel: 'Simpanan Pokok', name: 'savingTypeSHUOpt', inputValue: '1', disabled: true},
	                                        {boxLabel: 'Simpanan Wajib', name: 'savingTypeSHUOpt', inputValue: '2', disabled: true},
	                                        {boxLabel: 'Simpanan Sukarela Saham', name: 'savingTypeSHUOpt', inputValue: '3'},
	                                        {boxLabel: 'Simpanan Sukarela Non-Saham', name: 'savingTypeSHUOpt', inputValue: '4'}
	                                    ]
	                                },{
                                        xtype: 'radiogroup',
                                        id: 'fieldSavingTypeSHUPayment',
                                        columns: 2,
                                        allowBlank: false,
                                        msgTarget: 'side',
                                        fieldLabel: lang('SHU Payment') + ' <span style="color:red">*</span>',
                                        vertical: true,
                                        items: [
                                            {boxLabel: '1 day after RAT', name: 'savingTypeSHUPayment', inputValue: '1'},
                                            {boxLabel: 'End of year', name: 'savingTypeSHUPayment', inputValue: '2'}
                                        ]
                                    }
		           				]
           					},{
           						xtype: 'fieldset',
           						style: 'margin: 5px; padding: 4px 7px;',
		           				fieldDefaults: {
				                    labelAlign: 'left',
				                    labelWidth: 120
				                },
				                items: [
				                	{
				                		xtype: 'checkbox',
				                		id: 'isDeposito',
				                		fieldLabel: lang('Deposito'),
				                		boxLabel: '',
	                                    checked: false,
	                                    inputValue: 1,
	                                    uncheckedValue: 0,
	                                    listeners: {
	                                    	change: function(t, nv){
	                                    		toggleDepositoFields(nv);
	                                    	}
	                                    }
				                	},{
                                        xtype: 'numericfield',
                                        hidden:true,
                                        id:'fieldDepositoMinAmount',
                                        width: 400,
				                    	labelWidth: 160,
                                        hideTrigger:true,
                                        fieldStyle:'text-align:right;',
                                        fieldLabel: lang('Minimum Amount'),
                                    },{
                                        xtype: 'numericfield',
                                        id: 'fieldDepositoLength',
                                        hidden:true,
                                        width: 400,
				                    	labelWidth: 160,
                                        hideTrigger:true,
                                        fieldStyle:'text-align:right;',
                                        fieldLabel: lang('Period'),
                                    },{
                                        xtype: 'radiogroup',
                                        id:'fieldDepositoMethod',
                                        columns: 1,
                                        hidden:true,
				                    	labelWidth: 160,
                                        fieldLabel: lang('Deposito Method'),
                                        vertical: true,
                                        items: [
                                            {boxLabel: 'Metode Masa Simpan Bulanan', name: 'metodeDepositoOpt', inputValue: '1'},
                                            {boxLabel: 'Metode Masa Simpan Harian', name: 'metodeDepositoOpt', inputValue: '2'}
                                        ]
                                    },{
                                        xtype: 'radiogroup',
                                        hidden:true,
				                    	labelWidth: 160,
                                        columns: 2,
                                        id:'fieldDepositoTax',
                                        fieldLabel: lang('Deposito Tax'),
                                        vertical: true,
                                        items: [
                                            {boxLabel: 'Tanpa Pajak', name: 'fieldDepositoTaxOpt', inputValue: '1'},
                                            {boxLabel: 'Dengan Pajak', name: 'fieldDepositoTaxOpt', inputValue: '2'}
                                        ]
                                    }
				                ]
           					}
           				]
           			},{
           				xtype: 'container',
           				columnWidth: 0.5,
           				items:[
           					{
           						xtype: 'fieldset',
           						style: 'margin: 5px; padding: 4px 7px;',
		           				fieldDefaults: {
				                    labelAlign: 'left',
				                    labelWidth: 140
				                },
				                items: [
				                	{
	                                    xtype: 'numericfield',
	                                    id: 'fieldMonthlyFee',
	                                    allowBlank: false,
	                                    hideTrigger: true,
	                                    fieldLabel: lang('Monthly Fee') + ' <span style="color:red">*</span>',
	                                    fieldStyle: 'text-align:right;',
	                                    width: 400
	                                },{
	                                	xtype: 'datefield',
                                        id: 'fieldSavingTypeActiveDate',
                                        allowBlank: false,
                                        submitFormat: 'Y-m-d',
                                        fieldLabel: lang('Active Since') + ' <span style="color:red">*</span>'
	                                }
				                ]
           					},{
           						xtype: 'fieldset',
           						style: 'margin: 5px; padding: 4px 7px;',
		           				fieldDefaults: {
				                    labelAlign: 'left',
				                    labelWidth: 140
				                },
				                items: [
				                	{
                                        xtype: 'numericfield',
                                        id: 'fieldSavingTypeMinTrans',
                                        allowBlank:false,
                                        hideTrigger:true,
                                        fieldLabel: lang('Min Transaction') + ' <span style="color:red">*</span>',
                                        fieldStyle:'text-align:right;',
                                        width: 400
                                    },{
                                        xtype: 'numericfield',
                                        id: 'fieldsavingTypeMinAmount',
                                        hideTrigger: true,
                                        allowBlank:false,
                                        fieldLabel: lang('Min Balance') + ' <span style="color:red">*</span>',
                                        fieldStyle:'text-align:right;',
                                        width: 400
                                    },{
                                        xtype: 'numericfield',
                                        id: 'fieldSavingTypeInterestRate',
                                        hideTrigger:true,
                                        allowBlank:false,
                                        fieldLabel: lang('Interest Rate') + ' <span style="color:red">*</span>',
                                        width:200
                                    },{
                                        xtype: 'radiogroup',
                                        id:'fieldSavingTypeInterestCalc',
                                        columns: 1,
                                        allowBlank:false,
                                        msgTarget: 'side',
                                        fieldLabel: lang('Interest Calc') + '<span style="color:red">*</span>',
                                        vertical: true,
                                        items: [
                                            {boxLabel: 'Daily Balance', name: 'savingTypeInterestCalcOpt', inputValue: '1'},
                                            {boxLabel: 'Average Monthly Balance', name: 'savingTypeInterestCalcOpt', inputValue: '2'},
                                            {boxLabel: 'End of Month Balance', name: 'savingTypeInterestCalcOpt', inputValue: '3'}
                                        ]
                                    },{
                                        xtype: 'radiogroup',
                                        id:'fieldSavingTypeInterestPayment',
                                        columns: 2,
                                        allowBlank:false,
                                        msgTarget: 'side',
                                        fieldLabel:'Interest Payment <span style="color:red">*</span>',
                                        vertical: true,
                                        items: [
                                            {boxLabel: 'Yearly', name: 'savingTypeInterestPaymentOpt', inputValue: '1'},
                                            {boxLabel: 'Monthly', name: 'savingTypeInterestPaymentOpt', inputValue: '2'}
                                        ]
                                    }
				                ]
				            },{
				            	xtype: 'fieldset',
           						style: 'margin: 5px; padding: 4px 7px;',
		           				fieldDefaults: {
				                    labelAlign: 'left',
				                    labelWidth: 140
				                },
				                items: [
				                	{
                                        xtype: 'boxselect',
                                        itemId: 'valuesSelect',
                                        id:'valuesSelect',
                                        fieldLabel: 'Used by',
                                        displayField: 'TypeName',
                                        hidden: false,
                                        anchor:'100%',
                                        name:'usedby[]',
                                        store: Ext.create('Ext.data.Store', {
                                            fields: ['TypeID', 'TypeName'],
                                            autoLoad: true,
                                            proxy: {
                                                type: 'rest',
                                                url: m_api + '/savingtype/getComboMemberType', // url that will load data with respect to start and limit params
                                                reader: {
                                                    type: 'json',
                                                    root: 'data',
                                                    totalProperty: 'total'
                                                }
                                            }
                                        }),
                                        valueField: 'TypeID'
                                    },{
                                        xtype:'hiddenfield',
                                        id:'coaNameSavingTypeID',
                                        name:'CoaID'
                                    },{
                                        xtype: 'textfield',
                                        anchor:'100%',
                                        fieldLabel: 'Chart of Account',
                                        // name: 'coaNameSavingType',
                                        id: 'coaNameSavingType',
                                        listeners: {
                                            render: function(component) {
                                                component.getEl().on('click', function(event, el) {
                                                   displayCoaWindow();
                                                });
                                            }
                                        }
                                    }
				                ]
				            }
           				]
           			}
           		]
            }], //winitems
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
                    	Ext.getCmp('winMgmt').close();
                    }
            	}
            ]
        });
    }

    function toggleDepositoFields(val){
    	var fMinAmount = Ext.getCmp('fieldDepositoMinAmount');
    	var fMethod = Ext.getCmp('fieldDepositoMethod');
    	var fLength = Ext.getCmp('fieldDepositoLength');
    	var fTax = Ext.getCmp('fieldDepositoTax');

    	fMinAmount.reset();
		fMethod.reset();
		fLength.reset();
		fTax.reset();

    	if(val === true){	
    		fMinAmount.show();
    		fMethod.show();
    		fLength.show();
    		fTax.show();
    	}else{
    		fMinAmount.hide();
    		fMethod.hide();
    		fLength.hide();
    		fTax.hide();
    	}
    }

    function displayCoaWindow(){
    	Ext.create('Ext.window.Window', {
	        id: 'wCoaSavingTypePopup',
	        title: 'Choose Chart of Account',
	    //    autoWidth: true,
	        width: 770,
	        height: 330,
	        layout: 'fit',
	        border: false,
	        items: [{
	                xtype:'GridCoaSavingTypeList'
	        }]
	    }).show();
    }

    function deleteData(){
    	var grid = Ext.getCmp('mainGrid');
    	var slct = grid.getSelectionModel().getSelection();

    	if(slct.length >= 1){
    		Ext.MessageBox.confirm('Delete Confirmation', 'Delete selected data, please confirm to proceed', function(r) {
                if (r == 'yes') {
                	Ext.Ajax.request({
                        waitMsg: 'Please Wait',
                		url: m_crud,
    					method: 'DELETE',
    					params: { id: slct[0].data.SavingTypeID },
    					success: function(response){
	    					grid.store.load();

	    					var obj = Ext.decode(response.responseText);
	                        switch (obj.success) {
	                            case true:
	                                Ext.MessageBox.alert('Success', 'Data has been deleted');
	                                break;
	                            default:
	                                Ext.MessageBox.alert('Warning', obj.message);
	                                break;
	                        }
                        }
    				});
                }
            });
    	}else{
    		Ext.Msg.alert('Error', 'Please select a record to delete');
    	}
    }

    function editData(){
    	var grid = Ext.getCmp('mainGrid');
    	var slct = grid.getSelectionModel().getSelection();
    	// console.log(slct);

    	if(slct.length >= 1){
    		Ext.Ajax.request({
    			url: m_crud,
    			method: 'GET',
    			params: { id: slct[0].data.SavingTypeID },
    			success: function(response){
    				var q = JSON.parse(response.responseText);
    				console.log('edit');
    				if(q.total >= 1){
    					generateWin();
    					var win = Ext.getCmp('winMgmt');

    					//set form values
    					Ext.getCmp('fieldSavingTypeID').setValue(q.data.savingTypeID);
				    	
				    	var isAct = (q.data.savingTypeStatus === '1') ? true : false;
				    	Ext.getCmp('fieldSavingTypeStatus').setValue(isAct);
				    	
				    	var isDef = (q.data.savingTypeDefault === '1') ? true : false;
				    	Ext.getCmp('fieldSavingTypeDefault').setValue(isDef);
				    	
				    	Ext.getCmp('fieldSavingTypeCode').setValue(q.data.savingTypeCode);
				    	Ext.getCmp('fieldSavingTypeName').setValue(q.data.savingTypeName);
				    	Ext.getCmp('fieldSavingTypeSHU').setValue({savingTypeSHUOpt: q.data.savingTypeSHU});
				    	Ext.getCmp('fieldSavingTypeSHUPayment').setValue({savingTypeSHUPayment: q.data.savingTypeSHUPayment});
				    	
				    	var dma = q.data.MinAmountDepositLimit, dl = q.data.LengthDeposito, dm = q.data.MetodeDeposito, dt = q.data.PajakDeposito;
				    	if(dma != 0 || dl != 0 || dm != 0 || dt != 0){
				    		Ext.getCmp('isDeposito').setValue(true);
					    	Ext.getCmp('fieldDepositoMinAmount').setValue(dma);
					    	Ext.getCmp('fieldDepositoLength').setValue(dl);
					    	Ext.getCmp('fieldDepositoMethod').setValue({metodeDepositoOpt: dm});
					    	Ext.getCmp('fieldDepositoTax').setValue({fieldDepositoTaxOpt: dt});
				    	}
				    	
				    	Ext.getCmp('fieldMonthlyFee').setValue(q.data.savingTypeMonthlyFee);
				    	Ext.getCmp('fieldSavingTypeActiveDate').setValue(q.data.savingTypeActiveDate);
				    	Ext.getCmp('fieldSavingTypeMinTrans').setValue(q.data.savingTypeMinTrans);
				    	Ext.getCmp('fieldsavingTypeMinAmount').setValue(q.data.savingTypeMinAmount);
				    	Ext.getCmp('fieldSavingTypeInterestRate').setValue(q.data.savingTypeInterestRate);
				    	Ext.getCmp('fieldSavingTypeInterestCalc').setValue({savingTypeInterestCalcOpt: q.data.savingTypeInterestCalc});
				    	Ext.getCmp('fieldSavingTypeInterestPayment').setValue({savingTypeInterestPaymentOpt: q.data.savingTypeInterestPayment});
				    	Ext.getCmp('valuesSelect').setValue(q.data.usedby);
				    	Ext.getCmp('coaNameSavingTypeID').setValue(q.data.CoaID);
				    	Ext.getCmp('coaNameSavingType').setValue(q.data.coaNameSavingType);

    					win.setTitle('Edit Saving Type');
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
    	//simple validators with form
    	var form = Ext.getCmp('formSavingType');
    	if(!form.isValid()){
    		//terminate the process
    		return false;
    	}

    	var stID = Ext.getCmp('fieldSavingTypeID').getValue();
    	var isActive = Ext.getCmp('fieldSavingTypeStatus').getValue();
    	var isDefault = Ext.getCmp('fieldSavingTypeDefault').getValue();
    	var code = Ext.getCmp('fieldSavingTypeCode').getValue();
    	var name = Ext.getCmp('fieldSavingTypeName').getValue();
    	var SHU = Ext.getCmp('fieldSavingTypeSHU').getValue().savingTypeSHUOpt;
    	var SHUPayment = Ext.getCmp('fieldSavingTypeSHUPayment').getValue().savingTypeSHUPayment;
    	var depoMinAmount = Ext.getCmp('fieldDepositoMinAmount').getValue();
    	var depoLength = Ext.getCmp('fieldDepositoLength').getValue();
    	var depoMethod = Ext.getCmp('fieldDepositoMethod').getValue().metodeDepositoOpt;
    	var depoTax = Ext.getCmp('fieldDepositoTax').getValue().fieldDepositoTaxOpt;
    	var monthlyFee = Ext.getCmp('fieldMonthlyFee').getValue();
    	var activeDate = Ext.getCmp('fieldSavingTypeActiveDate').getValue();
    	var minTrx = Ext.getCmp('fieldSavingTypeMinTrans').getValue();
    	var minAmount = Ext.getCmp('fieldsavingTypeMinAmount').getValue();
    	var intRate = Ext.getCmp('fieldSavingTypeInterestRate').getValue();
    	var intCalc = Ext.getCmp('fieldSavingTypeInterestCalc').getValue().savingTypeInterestCalcOpt;
    	var intPayment = Ext.getCmp('fieldSavingTypeInterestPayment').getValue().savingTypeInterestPaymentOpt;
    	var usedBy = Ext.getCmp('valuesSelect').getValue();
    	var coa = Ext.getCmp('coaNameSavingTypeID').getValue();

    	console.log(usedBy);

    	Ext.Ajax.request({
			url: m_crud,
			method: 'POST',
			params: {
				SavingTypeID: stID,
				SavingTypeDefault: (isDefault === true) ? 1 : 2,
				SavingTypeCode: code,
				CoaID: coa,
				SavingTypeSHU: SHU,
				SavingTypeName: name,
				SavingTypeMinAmount: minAmount,
				SavingTypeMinTrans: minTrx,
				SavingTypeInterestRate: intRate,
				SavingTypeSHUPayment: SHUPayment,
				SavingTypeInterestCalc: intCalc,
				SavingTypeActiveDate: activeDate,
				SavingTypeMonthlyFee: monthlyFee,
				SavingTypeInterestPayment: intPayment,
				SavingTypeStatus: (isActive === true) ? 1 : 2,
				MinAmountDepositLimit: depoMinAmount,
				LengthDeposito: depoLength,
				MetodeDeposito: depoMethod,
				PajakDeposito: depoTax,
				UsedBy: Ext.encode(usedBy)
			},
			success: function(response){
				Ext.getCmp('winMgmt').close();
				Ext.getCmp('mainGrid').store.load();

				var rsp = JSON.parse(response.responseText);
				Ext.Msg.alert('Success', rsp.data.msg);
			},
			failure: function(response, opts){
				var obj = Ext.decode(response.responseText);
                Ext.MessageBox.alert('System error', 'Could not connect to the database. Please retry later');
			}
		});
    }

}); //end of onReady
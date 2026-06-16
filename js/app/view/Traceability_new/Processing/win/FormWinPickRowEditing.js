Ext.define('Koltiva.view.Traceability_new.Processing.win.FormWinPickRowEditing' ,{
    extend: 'Ext.window.Window',
    id: 'Koltiva.view.Traceability_new.Processing.win.FormWinPickRowEditing',
    title: lang('Selection'),
    closable: true,
    modal: true,
    closeAction: 'destroy',
    width: '70%',
    overflowY: 'auto',
    // formVar: false,
    viewVar: false,
    // setFormVar: function(value){
    //     this.formVar = value;
    // },
    setViewVar: function(value){
        this.viewVar = value;
    },
    initComponent: function() {
        var thisObj = this;
  
        var storeGridDesPathPickRow = Ext.create('Koltiva.store.Traceability_new.Processing.RefProcessing', {
            storeVar: { 
                ProductID : thisObj.viewVar.ProductID 
            } 
        });

		var RowEditingDespatchPick = Ext.create('Ext.grid.plugin.RowEditing', {
            id : 'RowEditingBeenTypeId',
            clicksToMoveEditor: 0,
            autoCancel: false,
            errorSummary: false,
            clicksToEdit: 2,
           
        });
		
		var contextMenuProcessingPick = Ext.create('Ext.menu.Menu',{
			cls:'Sfr_ConMenu',
            items:[
                {
                    icon: varjs.config.base_url + 'images/icons/new/update.png',
                    text: lang('Pick Weight'),
                    itemId: 'Koltiva.view.Traceability_new.Processing.win.FormWinPickRowEditing-contextMenuUpdateItem',
                    //hidden: m_act_update,
                    handler: function() {
                            RowEditingDespatchPick.cancelEdit();
                            var sm = Ext.getCmp('Koltiva.view.Traceability_new.Processing.win.FormWinPickRowEditing-MainGrid').getSelectionModel().getSelection()[0];
                            if (sm.raw.ProcessingProductID) {
                                RowEditingDespatchPick.startEdit(sm.index, 0);
                            }else{
                                Ext.MessageBox.alert(lang('Warning'), lang('Please select data on the grid !'));
                            }
                            
                    }
                }]
        });

        // var cmb_product_type = Ext.create('Koltiva.store.Traceability_new.Processing.ProductType');
        
        thisObj.items = [{
            xtype: 'grid',
            id: 'Koltiva.view.Traceability_new.Processing.win.FormWinPickRowEditing-MainGrid',
            style: 'border:1px solid #CCC;margin-top:4px;',
            loadMask: true,
            selType: 'rowmodel',
            store: storeGridDesPathPickRow,
            width: '100%',
            minHeight:350,
            //autoWidth : true,
            viewConfig: {
                deferEmptyText: false,
                emptyText: lang('No data Available'),
            },
            plugins: [RowEditingDespatchPick],
            dockedItems: [{
                xtype: 'pagingtoolbar',
                id: 'Koltiva.view.Traceability_new.Processing.win.FormWinPickRowEditing-MainGrid-gridToolbar',
                store: storeGridDesPathPickRow,
                dock: 'bottom',
                displayInfo: true
            },{
                xtype: 'toolbar',
                dock:'top',
                items: [{
                    icon: varjs.config.base_url + 'images/icons/new/reload.png',
                    cls:'Sfr_BtnGridGreen',
                    overCls:'Sfr_BtnGridGreen-Hover',
                    text: lang('Generate Processing'),
                    handler: function() {
                        let haveOer = Ext.getCmp('view.Traceability_new.Processing.win.FormWinPickRowEditing.HaveOer').getValue()
                        console.log(haveOer);
                        if (parseInt(haveOer) == 2) {
                            let flagCpo              = Ext.getCmp('view.Traceability_new.Processing.win.FormWinPickRowEditing.flagCpo').getValue()
                            let flagPk               = Ext.getCmp('view.Traceability_new.Processing.win.FormWinPickRowEditing.flagPk').getValue()
                            let ProductPercentageCpo = Ext.getCmp('view.Traceability_new.Processing.win.FormWinPickRowEditing.ProductPercentageCpo').getValue()
                            let ProductPercentagePk  = Ext.getCmp('view.Traceability_new.Processing.win.FormWinPickRowEditing.ProductPercentagePk').getValue()

                            if (parseInt(haveOer) == 2) {
                                if (thisObj.viewVar.ProductID == 1) {
                                    if (flagCpo == 1) {
                                        if (ProductPercentageCpo == "" || parseInt(ProductPercentageCpo) == 0) {
                                            Ext.MessageBox.show({
                                                title: 'Error',
                                                msg: lang('Please Input Percentage CPO'),
                                                buttons: Ext.MessageBox.OK,
                                                animateTarget: 'mb9',
                                                icon: 'ext-mb-error'
                                            });

                                            return;
                                        }
                                    }
                                } else if (thisObj.viewVar.ProductID == 2) {
                                    if (flagPk == 1) {
                                        if (ProductPercentagePk == "" || parseInt(ProductPercentagePk) == 0) {
                                            Ext.MessageBox.show({
                                                title: 'Error',
                                                msg: lang('Please Input Percentage PK'),
                                                buttons: Ext.MessageBox.OK,
                                                animateTarget: 'mb9',
                                                icon: 'ext-mb-error'
                                            });

                                            return;
                                        }
                                    }
                                } else {
                                    if (flagCpo == 1 && flagPk == 1) {
                                        if ((ProductPercentagePk == "" || parseInt(ProductPercentagePk) == 0) && (ProductPercentageCpo == "" || parseInt(ProductPercentageCpo) == 0)) {
                                            Ext.MessageBox.show({
                                                title: 'Error',
                                                msg: lang('Please Input Percentage CPO & PK'),
                                                buttons: Ext.MessageBox.OK,
                                                animateTarget: 'mb9',
                                                icon: 'ext-mb-error'
                                            });

                                            return;
                                        }
                                    }
                                }
                                
                            }

                            Ext.MessageBox.show({
                                msg: 'Please wait...',
                                progressText: 'Generating...',
                                width: 300,
                                wait: true,
                                waitConfig: {
                                    interval: 200
                                },
                                icon: 'ext-mb-download', //custom class in msg-box.html
                                animateTarget: 'mb7'
                            });

                            Ext.Ajax.request({
                                waitMsg: 'Please Wait',
                                url: m_api + '/scheduler/generate_processing_manual',
                                params: {
                                    ProductPercentageCpo : Ext.getCmp('view.Traceability_new.Processing.win.FormWinPickRowEditing.ProductPercentageCpo').getValue(),
                                    ProductPercentagePk:Ext.getCmp('view.Traceability_new.Processing.win.FormWinPickRowEditing.ProductPercentagePk').getValue(),
                                    HaveOer: Ext.getCmp('view.Traceability_new.Processing.win.FormWinPickRowEditing.HaveOer').getValue(),
                                    ProductID : thisObj.viewVar.ProductID
                                },
                                method: 'GET',
                                success: function(response, opts) {
                                    var r = Ext.decode(response.responseText);

                                    Ext.MessageBox.hide();
                                    Ext.MessageBox.show({
                                        title: 'Information',
                                        msg: r.success == false ? lang(r.message) : lang('Data Generated'),
                                        buttons: Ext.MessageBox.OK,
                                        animateTarget: 'mb9',
                                        icon: r.success == false ? 'ext-mb-error' : 'ext-mb-success'
                                    });

                                    //refresh store
                                    storeGridDesPathPickRow.load();
                                },
                                failure: function(response, opts) {
                                    Ext.MessageBox.show({
                                        title: 'Error',
                                        msg: lang('Failed to Generated'),
                                        buttons: Ext.MessageBox.OK,
                                        animateTarget: 'mb9',
                                        icon: 'ext-mb-error'
                                    });
                                }
                            });
                        } else {
                            if (Ext.getCmp('Koltiva.view.Traceability_new.Processing.win.FormWinPickRowEditingHaveOer') == undefined){
                                var FormPickHaveOer = Ext.create('Koltiva.view.Traceability_new.Processing.win.FormWinPickRowEditingHaveOer', {
                                    viewVar: { 
                                        ProductID : thisObj.viewVar.ProductID
                                    }
                                });
                            } else{
                                var FormPickHaveOer = Ext.create('Koltiva.view.Traceability_new.Processing.win.FormWinPickRowEditingHaveOer', {
                                    viewVar: { 
                                        ProductID : thisObj.viewVar.ProductID
                                    }
                                });
                            }

                            if (!FormPickHaveOer.isVisible()) {
                                FormPickHaveOer.center();
                                FormPickHaveOer.show();
                            } else {
                                FormPickHaveOer.close();
                            }
                        }
                    }
                }, {
                    xtype: 'tbspacer',
                    flex: 0.5
                },
                {
                    xtype: 'textfield',
                    id: 'view.Traceability_new.Processing.win.FormWinPickRowEditing.HaveOer',
                    name: 'HaveOer',
                    hidden:true
                },
                {
                    xtype: 'textfield',
                    id: 'view.Traceability_new.Processing.win.FormWinPickRowEditing.flagCpo',
                    name: 'flagCpo',
                    hidden:true
                },
                {
                    xtype: 'textfield',
                    id: 'view.Traceability_new.Processing.win.FormWinPickRowEditing.flagPk',
                    name: 'flagPk',
                    hidden:true
                },
                {
                    xtype: 'textfield',
                    id: 'view.Traceability_new.Processing.win.FormWinPickRowEditing.ProductPercentageCpo',
                    name: 'ProductPercentageCpo',
                    fieldLabel: lang('OER CPO')+' (%)',
                },
                {
                    xtype: 'textfield',
                    id: 'view.Traceability_new.Processing.win.FormWinPickRowEditing.ProductPercentagePk',
                    name: 'ProductPercentagePk',
                    fieldLabel: lang('OER PK')+' (%)',
                },
                // {
                //     id: 'view.Traceability_new.Processing.win.FormWinPickRowEditing.ProductTypeSearch',
                //     xtype: 'combobox',
                //     baseCls: 'Sfr_ComboSearchGrid',
                //     store: cmb_product_type,
                //     emptyText: lang('Filter Product Type'),
                //     displayField: 'label',
                //     valueField: 'id',
                //     queryMode: 'local',
                //     width: 200,
                //     listeners: {
                //         change: function (cb, nv, ov) {
                //         }
                //     }
                // },{
                //     icon: varjs.config.base_url + 'images/icons/new/search_white.png',
                //     cls:'Sfr_BtnGridBlue',
                //     overCls:'Sfr_BtnGridBlue-Hover',
                //     text: lang('Apply'),
                //     handler: function() {
                //         setFilterLs();
                //         Ext.getCmp('Koltiva.view.Traceability_new.Processing.win.FormWinPickRowEditing-MainGrid').getStore().loadPage(1);
                //     }
                // }
                ]
            },{
                text: lang('Transaction Type'),
            }
            ],
            columns: [{
                text: lang('Action'),	
                xtype:'actioncolumn',
				width:65,
				items:[{
					icon: varjs.config.base_url + 'images/icons/new/action.png',
					handler: function(grid, rowIndex, colIndex, item, e, record) {
						//if(Ext.getCmp('setVarParameters').getValue() != 'view'){
							contextMenuProcessingPick.showAt(e.getXY());
						//}
					 
					}
				}]
            },
            {
                text: lang('ID'),
                dataIndex: '',
                hidden:true,
                width:40,
                editor: {
                    xtype: 'textfield',
                    id:'Koltiva.view.Traceability_new.Processing.win.FormWinPickRowEditing-MainGrid-DespatchID',
                    readOnly : true,
                }
            },
            {
                text: lang('ProcessingID'),
                dataIndex: 'ProcessingProductID',
                hidden:true,
                width:40,
                editor: {
                    xtype: 'textfield',
                    id:'Koltiva.view.Traceability_new.Processing.win.FormWinPickRowEditing-MainGrid-ProcessingProductID',
                    readOnly : true,
                }
            },
            {
                text: lang('ProductVolume'),
                dataIndex: 'ProductVolume',
                hidden:true,
                width:40,
                editor: {
                    xtype: 'textfield',
                    id:'Koltiva.view.Traceability_new.Processing.win.FormWinPickRowEditing-MainGrid-ProductVolume',
                    readOnly : true,
                }
            },
            {
                text: lang('Product ID'),
                dataIndex: 'ProductID',
                hidden:true,
                width:40,
                editor: {
                    xtype: 'textfield',
                    id:'Koltiva.view.Traceability_new.Processing.win.FormWinPickRowEditing-MainGrid-ProductID',
                    readOnly : true,
                }
            },
            {
                text: lang('Processing Number'),
                dataIndex: 'ProcessingNumber',
                flex:1,
                editor: {
                    xtype: 'textfield',
                    id:'Koltiva.view.Traceability_new.Processing.win.FormWinPickRowEditing-MainGrid-ProcessingNumber',
                    allowBlank: false
                    
                }
            },
            {
                text: lang('Product'),
                dataIndex: 'ProductName',
                flex:1,                
                editor: {
                    xtype: 'textfield',
                    id:'Koltiva.view.Traceability_new.Processing.win.FormWinPickRowEditing-MainGrid-ProductName',
                    readOnly : true,
                }
            },
            {
                text: lang('Processing Date'),
                dataIndex: 'ProcessingDate',
                flex:1,
                editor: {
                    xtype: 'textfield',
                    id:'Koltiva.view.Traceability_new.Processing.win.FormWinPickRowEditing-MainGrid-ProcessingDate',
                    allowBlank: false
                    
                }
            },
            {
                text: lang('Total Picked Weight (kg)'),
                dataIndex: 'PickedVolume',
                flex:1
            },
            {
                text: lang('Remaining Weight (kg)'),
                dataIndex: 'RemainingVolume',
                flex:1,
                editor: {
                    xtype: 'numericfield',
                    id:'Koltiva.view.Traceability_new.Processing.win.FormWinPickRowEditing-MainGrid-RemainingVolume',
                    allowBlank: false                    
                }
            },
            {
                text: lang('Pick Weight (kg)'),
                dataIndex: '......',
                flex:1,
                editor: {
                    xtype: 'numberfield',
                    id:'Koltiva.view.Traceability_new.Processing.win.FormWinPickRowEditing-MainGrid-SelectedWeight',
                    allowBlank: false,
                    listeners : {
                        change : function(val){
                           
                           var RemainingVolume = Ext.getCmp('Koltiva.view.Traceability_new.Processing.win.FormWinPickRowEditing-MainGrid-RemainingVolume').getValue();
                           if(val.value>RemainingVolume){
                                Ext.MessageBox.alert(lang('Warning'), lang('Selected Weight should be less than remaining weight'));
                                Ext.getCmp('Koltiva.view.Traceability_new.Processing.win.FormWinPickRowEditing-MainGrid-SelectedWeight').setValue('');
                           }


                        }
                    }
                    
                }
            },
            {
                text: lang('Despatch Date'),
                dataIndex: '.....',
                flex:1,
                hidden:true,
                editor: {
                    xtype: 'datefield',
                    id:'Koltiva.view.Traceability_new.Processing.win.FormWinPickRowEditing-MainGrid-DespatchDate',
                    allowBlank: true
                    
                }
            },
           
			],
            listeners: {
				 
                'canceledit': function (editor, e, eOpts) {
                    Ext.getCmp('Koltiva.view.Traceability_new.Processing.win.FormWinPickRowEditing-MainGrid').getStore().load();
                },
                'beforeedit': function (editor, e){
                   
                    Ext.getCmp('Koltiva.view.Traceability_new.Processing.win.FormWinPickRowEditing-MainGrid-ProcessingNumber').setReadOnly(true);
                    Ext.getCmp('Koltiva.view.Traceability_new.Processing.win.FormWinPickRowEditing-MainGrid-ProcessingDate').setReadOnly(true);
                    Ext.getCmp('Koltiva.view.Traceability_new.Processing.win.FormWinPickRowEditing-MainGrid-RemainingVolume').setReadOnly(true);
                    Ext.getCmp('Koltiva.view.Traceability_new.Processing.win.FormWinPickRowEditing-MainGrid-RemainingVolume').setReadOnly(true);

                },
                'edit': function (editor, e) {
                    var ProcessingID        = Ext.getCmp('Koltiva.view.Traceability_new.Processing.win.FormWinPickRowEditing-MainGrid-ProcessingProductID').getValue();
                    var ProductID           = Ext.getCmp('Koltiva.view.Traceability_new.Processing.win.FormWinPickRowEditing-MainGrid-ProductID').getValue();
                    var DespatchDate        = Ext.getCmp('Koltiva.view.Traceability_new.Processing.win.FormWinPickRowEditing-MainGrid-DespatchDate').getValue()
                    var ProcessingNumber    = Ext.getCmp('Koltiva.view.Traceability_new.Processing.win.FormWinPickRowEditing-MainGrid-ProcessingNumber').getValue(); 
                    var ProcessingDate      = Ext.getCmp('Koltiva.view.Traceability_new.Processing.win.FormWinPickRowEditing-MainGrid-ProcessingDate').getValue(); 

                    var ProductVolume       = Ext.getCmp('Koltiva.view.Traceability_new.Processing.win.FormWinPickRowEditing-MainGrid-ProductVolume').getValue();
                
                    var RemainingVolume     = Ext.getCmp('Koltiva.view.Traceability_new.Processing.win.FormWinPickRowEditing-MainGrid-RemainingVolume').getValue(); 
                    var SelectedWeight      = Ext.getCmp('Koltiva.view.Traceability_new.Processing.win.FormWinPickRowEditing-MainGrid-SelectedWeight').getValue(); 
                    
                    var Remaining = parseFloat(RemainingVolume)-parseFloat(SelectedWeight);
                     
                    Ext.Ajax.request({
                        waitMsg: lang('Please wait...'),
                        url: m_api + '/processing/transaction/save_pick',
                        method: 'POST',
                        params: {
                            DispatchID          : thisObj.viewVar.DespatchID,
                            ProcessingProductID : ProcessingID,
                            DespatchDate        : DespatchDate,
                            PickedQty           : SelectedWeight,
                            Remaining           : Remaining,
                            ProductID           : ProductID
                            
                        },
                        success: function (response, opts) {
                            var obj = Ext.decode(response.responseText);
                            var message =  'Insert';
                            switch (obj.success) {
                                case true:
                                    Ext.MessageBox.alert('Success', lang(message + ' success'));
                                    Ext.getCmp('Koltiva.view.Traceability_new.Processing.win.FormWinPickRowEditing-MainGrid').getStore().load();
                                    Ext.getCmp('Koltiva.view.Traceability_new.Processing.GridPick-Grid').getStore().load();

                                    Ext.getCmp('Koltiva.view.Traceability_new.Processing.win.FormWinPickRowEditing-MainGrid-SelectedWeight').setValue("");
                                    break;
                                default:
                                    Ext.MessageBox.alert('Warning', lang(obj.message));

                                    Ext.getCmp('Koltiva.view.Traceability_new.Processing.win.FormWinPickRowEditing-MainGrid-SelectedWeight').setValue("");
                                    break;
                            }
                        },
                        failure: function (response, opts) {
                            var obj = Ext.decode(response.responseText);
                            Ext.MessageBox.alert('error', lang('Could not connect to the database. Retry later'));
                        }
                    });					
                }
            }
        }];

        thisObj.buttons = [ 
        // {
        //     xtype:'label',
        //     cls: 'x-form-item-label',
        //     id:'FFBUnproccesed',
        //     text: lang('FFB Unprocesed: ')
        // },
        {
            xtype:'label',
            cls: 'x-form-item-label',
            id:'CPOTotal',
            text: lang('CPO Remaining : ')
        },{
            xtype:'label',
            cls: 'x-form-item-label',
            id:'PKOTotal',
            text: lang('PKO Remaining: ')
        },
        {
            xtype:'tbspacer',
            flex:1
        },
        {
            text: lang('Close'),
            margin: '5px',
            scale: 'large',
            ui: 's-button',
            cls: 's-grey',
            handler: function() {
                //Ext.getCmp('Koltiva.view.Processing.GridProcPick-Grid').getStore().load();
				thisObj.close();
            }
        }];
		
        this.callParent(arguments);
    },
    listeners: {
        afterRender: function(c){
            var thisObj = this;

            if (thisObj.viewVar.ProductID == 1) {
                Ext.getCmp('view.Traceability_new.Processing.win.FormWinPickRowEditing.ProductPercentagePk').hide();
                // Ext.getCmp('PKOTotal').hide();
            } else if (thisObj.viewVar.ProductID == 2) {
                Ext.getCmp('view.Traceability_new.Processing.win.FormWinPickRowEditing.ProductPercentageCpo').hide();
                // Ext.getCmp('CPOTotal').hide()
            } else {
                Ext.getCmp('view.Traceability_new.Processing.win.FormWinPickRowEditing.ProductPercentagePk').show();
                Ext.getCmp('view.Traceability_new.Processing.win.FormWinPickRowEditing.ProductPercentageCpo').show();
                // Ext.getCmp('PKOTotal').show();
                // Ext.getCmp('CPOTotal').show();
            }

			/*if(thisObj.opsiDisplay == 'view'){
				Ext.getCmp('Koltiva.view.Traceability_new.Processing.win.FormWinPickRowEditing-GoodReceiptNumber').setReadOnly(true)
				Ext.getCmp('Koltiva.view.Traceability_new.Processing.win.FormWinPickRowEditing-PickedQty').setReadOnly(true)
				Ext.getCmp('Koltiva.view.Traceability_new.Processing.win.FormWinPickRowEditing-BtnSave').hide(true)
			}*/
        }
    }
});
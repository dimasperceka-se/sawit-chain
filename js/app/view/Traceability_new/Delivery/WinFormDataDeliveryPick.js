Ext.define('Koltiva.view.Traceability_new.Delivery.WinFormDataDeliveryPick' ,{
    extend: 'Ext.window.Window',
    id: 'Koltiva.view.Traceability_new.Delivery.WinFormDataDeliveryPick',
    title: lang('List Data'),
    closable: true,
    modal: true,
    closeAction: 'destroy',
    width: '80%',
    height: 555,
    overflowY: 'auto',
    style:'padding:2px;',
    viewVar: false,
    setViewVar: function(value){
        this.viewVar = value;
    },
    listeners: {
        afterRender: function () {
            var thisObj = this;
        }
    },
    initComponent: function() {
        var thisObj = this;
        
        thisObj.ArrCheckSupplyTransID = [];

        thisObj.StoreGridMain = Ext.create('Koltiva.store.Traceability_new.Delivery.WinFormDataDeliveryPickGrid',{
            storeVar: {
                SupplyBatchID : thisObj.viewVar.SupplyBatchID,
                TransTypeName: null,
                SupplyBatchNumber: null,
                StartDateCreateBatch: null,
                EndDateCreateBatch: null
            }
        });
        //Store ========================= (End)

        thisObj.items = [{
            xtype: 'grid',
            id: 'Koltiva.view.Traceability_new.Delivery.WinFormDataDeliveryPick-MainGrid',
            style: 'border:1px solid #CCC;margin-top:4px;',
            cls:'Sfr_GridNew',
            loadMask: true,
            selType: 'rowmodel',
            store: thisObj.StoreGridMain,
            enableColumnHide: false,
            height:450,
            viewConfig: {
                deferEmptyText: false,
                emptyText: GetDefaultContentNoData()
            },
            dockedItems: [{
                xtype: 'toolbar',
                dock: 'top',
            },{
                layout: 'column',
                hidden:false,
                border: false,
                items:  [{
                    columnWidth: 0.435,
                    layout: 'form',
                    style: 'padding:10px 5px 10px 20px;',
                    defaults: {
                        labelAlign: 'left',
                        labelWidth: 150
                    },
                    items: [{
                        xtype: 'textfield',
                        id: 'Koltiva.view.Traceability_new.Delivery.WinFormDataDeliveryPick-SupplyBatchNumber',
                        name: 'Koltiva.view.Traceability_new.Delivery.WinFormDataDeliveryPick-SupplyBatchNumber',
                        fieldLabel: lang('Supply Batch Number')
                    }]
                }, {
                    columnWidth: 0.435,
                    layout: 'form',
                    style: 'padding:10px 0px 10px 20px;',
                    defaults: {
                        labelAlign: 'left',
                        labelWidth: 150
                    },
                    items: [{
                        xtype: 'datefield',
                        id: 'Koltiva.view.Traceability_new.Delivery.WinFormDataDeliveryPick-StartDateCreateBatch',
                        name: 'Koltiva.view.Traceability_new.Delivery.WinFormDataDeliveryPick-StartDateCreateBatch',
                        format: 'Y-m-d',
                        fieldLabel: lang('Start Date Create Batch'),
                        enableKeyEvents: true,
                        listeners: {
                            keydown : function (field_, e_  )  {
                                e_.stopEvent();
                                return false;
                            }
                        }
                    },{
                        xtype: 'datefield',
                        id: 'Koltiva.view.Traceability_new.Delivery.WinFormDataDeliveryPick-EndDateCreateBatch',
                        name: 'Koltiva.view.Traceability_new.Delivery.WinFormDataDeliveryPick-EndDateCreateBatch',
                        format: 'Y-m-d',
                        fieldLabel: lang('End Date Create Batch'),
                        enableKeyEvents: true,
                        listeners: {
                            keydown : function (field_, e_  )  {
                                e_.stopEvent();
                                return false;
                            }
                        }
                    }]
                }, 
                {
                    columnWidth: 0.13,
                    layout: 'form',
                    style: 'padding:10px 0px 10px 20px;',
                    items: [{
                        xtype:'button',
                        icon: varjs.config.base_url + 'images/icons/silk/search.png',
                        text: lang('Search'),
                        cls:'Sfr_BtnGridBlue',
                        overCls:'Sfr_BtnGridBlue-Hover',
                        handler: function() {
                            thisObj.AddValidation = true;
                            thisObj.MsgAddValidation = "";
                            thisObj.AddValidationBasicForm();
                            if(thisObj.AddValidation == true) {

                                let StartDateCreateBatch = Ext.Date.format(Ext.getCmp('Koltiva.view.Traceability_new.Delivery.WinFormDataDeliveryPick-StartDateCreateBatch').getValue(), 'Y-m-d');
                                let EndDateCreateBatch = Ext.Date.format(Ext.getCmp('Koltiva.view.Traceability_new.Delivery.WinFormDataDeliveryPick-EndDateCreateBatch').getValue(), 'Y-m-d');

                                thisObj.StoreGridMain.storeVar.SupplyBatchID        = thisObj.viewVar.SupplyBatchID;
                                thisObj.StoreGridMain.storeVar.SupplyBatchNumber    = Ext.getCmp('Koltiva.view.Traceability_new.Delivery.WinFormDataDeliveryPick-SupplyBatchNumber').getValue();
                                thisObj.StoreGridMain.storeVar.StartDateCreateBatch = StartDateCreateBatch;
                                thisObj.StoreGridMain.storeVar.EndDateCreateBatch   = EndDateCreateBatch;

                                thisObj.StoreGridMain.load();
                            } else {
                                Ext.MessageBox.show({
                                    title: lang('Information'),
                                    msg: thisObj.MsgAddValidation,
                                    buttons: Ext.MessageBox.OK,
                                    animateTarget: 'mb9',
                                    icon: 'ext-mb-info'
                                });
                            }
                        }
                    },{
                        xtype:'button',
                        icon: varjs.config.base_url + 'images/icons/new/delete.svg',
                        text: lang('Reset'),
                        cls: 'Sfr_BtnFormRed',
                        overCls: 'Sfr_BtnFormRed-Hover',
                        handler: function () {
        
                            Ext.getCmp('Koltiva.view.Traceability_new.Delivery.WinFormDataDeliveryPick-SupplyBatchNumber').setValue('');
                            Ext.getCmp('Koltiva.view.Traceability_new.Delivery.WinFormDataDeliveryPick-StartDateCreateBatch').setValue('');
                            Ext.getCmp('Koltiva.view.Traceability_new.Delivery.WinFormDataDeliveryPick-EndDateCreateBatch').setValue('');
                            
                            thisObj.StoreGridMain.load();
                            thisObj.close();
                        }
                    }]
                }]
            }],
            columns: [{
                xtype: 'actioncolumn',
                width: 50,
                items: [{
                    icon: varjs.config.base_url + 'images/icons/silk/control_add_blue.png',
                    tooltip: lang('Select'),
                    handler: function(grid, rowIndex, colIndex) {
                        var rec = grid.getStore().getAt(rowIndex);

                        thisObj.WinFormDataDeliveryDetail = Ext.create('Koltiva.view.Traceability_new.Delivery.WinFormDataDeliveryDetail', {
                            viewVar: {
                                OpsiDisplay: 'update',
                                SupplyBatchID : rec.data.SupplyBatchID
                            }
                        });

                        if (!thisObj.WinFormDataDeliveryDetail.isVisible()) {
                            thisObj.WinFormDataDeliveryDetail.center();
                            thisObj.WinFormDataDeliveryDetail.show();
                        } else {
                            thisObj.WinFormDataDeliveryDetail.close();
                        }
                    }
                }]
            }, {
                text: lang('Status'),
                dataIndex: 'SupplyBatchStatus',
                width:120
            },{
                text: lang('Nomor Siklus'),
                dataIndex: 'SupplyBatchNumber',
                width:300
            },{
                text: lang('Total Weight'),
                dataIndex: 'DestWeight',
                width:150
            },
            {
                text: lang('Remaining'),
                dataIndex: 'RemainingWeight',
                width:150
            },
            {
                text: lang('Date Create Batch'),
                dataIndex: 'DateCreateBatch',
                width:150
            }
            ]
        }];

        this.callParent(arguments);
    },
    AddValidationBasicForm: function() {
        var thisObj = this;
        var ArrMsg = [];
        thisObj.AddValidation = true;

        if(thisObj.AddValidation == false){
            var HtmlMsg = '<ul>';
            for (var index = 0; index < ArrMsg.length; index++) {
                HtmlMsg += '<li>'+ArrMsg[index]+'</li>'
            }
            HtmlMsg+='</ul>';
            thisObj.MsgAddValidation = HtmlMsg;
        }
    }
});
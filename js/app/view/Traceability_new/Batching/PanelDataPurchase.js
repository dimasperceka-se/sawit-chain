Ext.define('Koltiva.view.Traceability_new.Batching.PanelDataPurchase', {
    extend: 'Ext.panel.Panel',
    id: 'Koltiva.view.Traceability_new.Batching.PanelDataPurchase',
    title: lang('Batch Pick'),
    viewVar: false,
    setViewVar: function (value) {
        this.viewVar = value;
    },
    listeners: {
        afterRender: function () {
            var thisObj = this;
        }
    },
    initComponent: function () {
        var thisObj = this;

        thisObj.MainGrid = Ext.create('Koltiva.store.Traceability_new.Batching.MainGridDataPurchase', {
            storeVar: {
                SupplyBatchID : thisObj.viewVar.SupplyBatchID
            }
        });

        thisObj.ContextMenu = Ext.create('Ext.menu.Menu', {
            cls: 'Sfr_ConMenu',
            items: [{
                icon: varjs.config.base_url + 'images/icons/new/delete.png',
                text: lang('Delete'),
                cls: 'Sfr_BtnConMenuWhite',
                hidden: m_act_delete,
                handler: function () {
                    var sm = Ext.getCmp('Koltiva.view.Traceability_new.Batching.PanelDataPurchase-MainGrid').getSelectionModel().getSelection()[0];
                    
                    Ext.MessageBox.confirm('Message', lang('Do you want to delete this data ?'), function (btn) {
                        if (btn == 'yes') {
                            Ext.Ajax.request({
                                waitMsg: 'Please Wait',
                                url: m_api + '/traceability_api/batching/data_supplychain_batch_transaction',
                                method: 'DELETE',
                                params: {
                                    TransSupplyID : sm.get('TransSupplyID'),
                                    SupplyBatchID : sm.get('SupplyBatchID')
                                },
                                success: function (response, opts) {
                                  
                                    var r = Ext.decode(response.responseText);

                                    if (r.status == 1) {

                                        Ext.MessageBox.show({
                                            title: 'Information',
                                            msg: lang('Data deleted'),
                                            buttons: Ext.MessageBox.OK,
                                            animateTarget: 'mb9',
                                            icon: 'ext-mb-success',
                                            fn: function (btn) {
                                                if (btn == 'ok') {

                                                    thisObj.close();

                                                    Ext.getCmp('Koltiva.view.Traceability_new_new.Batching.MainForm').destroy();
                                                    var MainForm = [];
                                                    if (Ext.getCmp('Koltiva.view.Traceability_new_new.Batching.MainForm') == undefined) {
                                                        MainForm = Ext.create('Koltiva.view.Traceability_new_new.Batching.MainForm', {
                                                            viewVar: {
                                                                OpsiDisplay: 'update',
                                                                SupplyBatchID: thisObj.viewVar.SupplyBatchID
                                                            }
                                                        });
                                                    } else {
                                                        Ext.getCmp('Koltiva.view.Traceability_new_new.Batching.MainForm').destroy();
                                                        MainForm = Ext.create('Koltiva.view.Traceability_new_new.Batching.MainForm', {
                                                            viewVar: {
                                                                OpsiDisplay: 'update',
                                                                SupplyBatchID: thisObj.viewVar.SupplyBatchID
                                                            }
                                                        });
                                                    }
                                                }
                                            }
                                        });

                                    } else {

                                        Ext.MessageBox.show({
                                            title: 'Information',
                                            msg: lang('Data deleted'),
                                            buttons: Ext.MessageBox.OK,
                                            animateTarget: 'mb9',
                                            icon: 'ext-mb-success'
                                        });

                                        //refresh store
                                        thisObj.MainGrid.load();

                                    }

                                },
                                failure: function (rp, o) {
                                    try {
                                        var r = Ext.decode(rp.responseText);
                                        Ext.MessageBox.show({
                                            title: lang('Error'),
                                            msg: r.message,
                                            buttons: Ext.MessageBox.OK,
                                            animateTarget: 'mb9',
                                            icon: 'ext-mb-error'
                                        });
                                    } catch (err) {
                                        Ext.MessageBox.show({
                                            title: lang('Error'),
                                            msg: lang('Connection Error'),
                                            buttons: Ext.MessageBox.OK,
                                            animateTarget: 'mb9',
                                            icon: 'ext-mb-error'
                                        });
                                    }
                                }
                            });
                        }
                    });
                }
            }]
        });

        thisObj.items = [{
            xtype: 'grid',
            id: 'Koltiva.view.Traceability_new.Batching.PanelDataPurchase-MainGrid',
            cls: 'Sfr_GridNew',
            loadMask: true,
            height: 300,
            selType: 'rowmodel',
            style: 'border:1px solid #CCC;',
            store: thisObj.MainGrid,
            features: [{
                ftype: 'summary'
            }],
            viewConfig: {
                deferEmptyText: false,
                emptyText: GetDefaultContentNoData()
            },
            dockedItems: [{
                xtype: 'toolbar',
                dock: 'top',
                items: [{
                    xtype: 'button',
                    icon: varjs.config.base_url + 'images/icons/new/add.png',
                    text: lang('Add Request'),
                    hidden: m_act_add,
                    cls: 'Sfr_BtnGridGreen',
                    overCls: 'Sfr_BtnGridGreen-Hover',
                    id: 'Koltiva.view.Traceability_new.Batching.PanelDataPurchase-BtnAdd',
                    handler: function () {
                        thisObj.AddValidation = true;
                        thisObj.MsgAddValidation = "";
                        thisObj.AddValidationBasicForm();
                        if(thisObj.AddValidation == true) {

                            thisObj.WinFormDataTransaction = Ext.create('Koltiva.view.Traceability_new.Batching.WinFormDataTransaction', {
                                viewVar: {
                                    OpsiDisplay: 'insert',
                                    StoreGridMain: thisObj.MainGrid,
                                    SupplyBatchID: thisObj.viewVar.SupplyBatchID
                                }
                            });

                            if (!thisObj.WinFormDataTransaction.isVisible()) {
                                thisObj.WinFormDataTransaction.center();
                                thisObj.WinFormDataTransaction.show();
                            } else {
                                thisObj.WinFormDataTransaction.close();
                            }
                            
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
                    xtype: 'button',
                    icon: varjs.config.base_url + 'images/icons/new/save.png',
                    text: lang('Close'),
                    hidden: m_act_add,
                    cls: 'Sfr_BtnGridBlue',
                    overCls: 'Sfr_BtnGridBlue-Hover',
                    id: 'Koltiva.view.Traceability_new.Batching.PanelDataPurchase-BtnClose',
                    handler: function () {
                        var sm = Ext.getCmp('Koltiva.view.Traceability_new.Batching.PanelDataPurchase-MainGrid').getSelectionModel().getSelection()[0];
                        console.log(sm);

                        if(thisObj.viewVar.SupplyBatchID) {
                            
                            Ext.Ajax.request({
                                waitMsg: 'Please Wait',
                                url: m_api + '/traceability_api/batching/data_supplychain_batch_close',
                                method: 'POST',
                                params: {
                                    SupplyBatchID: thisObj.viewVar.SupplyBatchID
                                },
                                success: function (response, opts) {
                                  
                                    Ext.MessageBox.show({
                                        title: 'Information',
                                        msg: lang('Data saved.'),
                                        buttons: Ext.MessageBox.OK,
                                        animateTarget: 'mb9',
                                        icon: 'ext-mb-success',
                                        fn: function (btn) {
                                            if (btn == 'ok') {
                                                Ext.getCmp('Koltiva.view.Traceability_new.Batching.MainForm-FormBasicData-SupplyBatchStatusID').setValue('2');
                                                Ext.getCmp('Koltiva.view.Traceability_new.Batching.PanelDataPurchase-BtnClose').setVisible(false);
                                            }
                                        }
                                    });

                                    Ext.getCmp('Koltiva.view.Traceability_new.Batching.PanelDataPurchase-BtnAdd').setVisible(false)
                                },
                                failure: function (rp, o) {
                                    try {
                                        var r = Ext.decode(rp.responseText);
                                        Ext.MessageBox.show({
                                            title: lang('Error'),
                                            msg: r.message,
                                            buttons: Ext.MessageBox.OK,
                                            animateTarget: 'mb9',
                                            icon: 'ext-mb-error'
                                        });
                                    } catch (err) {
                                        Ext.MessageBox.show({
                                            title: lang('Error'),
                                            msg: lang('Connection Error'),
                                            buttons: Ext.MessageBox.OK,
                                            animateTarget: 'mb9',
                                            icon: 'ext-mb-error'
                                        });
                                    }
                                }
                            });

                        } else {

                            Ext.MessageBox.show({
                                title: lang('Information'),
                                msg: lang("No transaction data"),
                                buttons: Ext.MessageBox.OK,
                                animateTarget: 'mb9',
                                icon: 'ext-mb-error'
                            });
                        }


                    }
                }]
            }],
            columns: [{
                text: ' ',
                xtype: 'actioncolumn',
                width: '10%',
                id: 'Koltiva.view.Traceability_new.Batching.PanelDataPurchase-ActionColumn',
                items: [{
                    icon: varjs.config.base_url + 'images/icons/new/action.png',
                    handler: function (grid, rowIndex, colIndex, item, e, record) {
                        thisObj.ContextMenu.showAt(e.getXY());
                    }
                }]
            },{
                text: lang('Supply Type Name'),
                dataIndex: 'SupplyType',
                flex:50
            },{
                text: lang('Trans Supply ID'),
                dataIndex: 'TransSupplyID',
                flex:50,
            },{
                text: lang('Supplier Name'),
                dataIndex: 'MemberName',
                flex:50
            },{
                text: lang('Supply Trans Number'),
                dataIndex: 'SupplyTransNumber',
                flex:60
            },{
                text: lang('Transaction Date'),
                dataIndex: 'DateTransaction',
                flex:50,
                summaryRenderer: function(value, summaryData, dataIndex) {
                    return "TOTAL";
                }
            },{
                text: lang('Total Weight'),
                dataIndex: 'GrossWeight',
                renderer: Ext.util.Format.numberRenderer('0,000.00'),
                flex:50,
                summaryType: 'sum'
            }]
        }];

        // mendapatkan value terbesar pada StandardName
        thisObj.MainGrid.on('load', function(store, records){
            sessionStorage.removeItem('totalPaymentBatch');
            sessionStorage.removeItem('maxpalue');
            sessionStorage.removeItem('nettWeight');

            let maxpalue;

        }, this);

        this.callParent(arguments);
    },
    AddValidationBasicForm: function() {
        var thisObj = this;
        var ArrMsg = [];
        thisObj.AddValidation = true;

        if(Ext.getCmp('Koltiva.view.Traceability_new.Batching.MainForm-FormBasicData-SupplyBatchDate').getValue() == null) {
            thisObj.AddValidation = false;
            ArrMsg.push(lang('Supply BatchDate must be filled in first'));
        }

        // if(Ext.getCmp('Koltiva.view.Traceability_new.Batching.MainForm-FormBasicData-ExternalBatchCode').getValue() == "") {
        //     thisObj.AddValidation = false;
        //     ArrMsg.push(lang('External Batch Code must be filled in first'));
        // }

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
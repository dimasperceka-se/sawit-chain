Ext.define('Koltiva.view.Traceability_new.Batching.PanelDataPurchaseDetail', {
    extend: 'Ext.panel.Panel',
    id: 'Koltiva.view.Traceability_new.Batching.PanelDataPurchaseDetail',
    title: lang('Batch Step'),
    style:'margin-top:10px',
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
        
        thisObj.MainGrid = Ext.create('Koltiva.store.Traceability_new.Batching.MainGridDataPurchaseDetail', {
            storeVar: {
                SupplyBatchID : thisObj.viewVar.SupplyBatchID
            }
        });

        //ContextMenu
        thisObj.ContextMenu = Ext.create('Ext.menu.Menu', {
            cls: 'Sfr_ConMenu',
            items: [{
                icon: varjs.config.base_url + 'images/icons/new/update.png',
                text: lang('Update'),
                cls: 'Sfr_BtnConMenuWhite',
                hidden: m_act_update,
                handler: function () {
                    var sm = Ext.getCmp('Koltiva.view.Traceability_new.Batching.PanelDataPurchaseDetail-MainGrid').getSelectionModel().getSelection()[0];

                    thisObj.WinFormDataBatchingStep = Ext.create('Koltiva.view.Traceability_new.Batching.WinFormDataBatchingStep', {
                        viewVar: {
                            OpsiDisplay: 'update',
                            StoreGridMain: thisObj.MainGrid,
                            SupplyBatchID : thisObj.viewVar.SupplyBatchID,
                            SupplyBatchBatchingID: sm.get('SupplyBatchBatchingID')
                        }
                    });

                    if (!thisObj.WinFormDataBatchingStep.isVisible()) {
                        thisObj.WinFormDataBatchingStep.center();
                        thisObj.WinFormDataBatchingStep.show();
                    } else {
                        thisObj.WinFormDataBatchingStep.close();
                    }

                }
            }, {
                icon: varjs.config.base_url + 'images/icons/new/delete.png',
                text: lang('Delete'),
                cls: 'Sfr_BtnConMenuWhite',
                hidden: m_act_delete,
                handler: function () {
                    var sm = Ext.getCmp('Koltiva.view.Traceability_new.Batching.PanelDataPurchaseDetail-MainGrid').getSelectionModel().getSelection()[0];

                    Ext.MessageBox.confirm('Message', lang('Do you want to delete this data ?'), function (btn) {
                        if (btn == 'yes') {

                            Ext.Ajax.request({
                                waitMsg: 'Please Wait',
                                url: m_api + '/traceability_api/batching/data_supplychain_batch_batching',
                                method: 'DELETE',
                                params: {
                                    SupplyBatchBatchingID: sm.get('SupplyBatchBatchingID'),
                                    SupplyBatchID : thisObj.viewVar.SupplyBatchID
                                },
                                success: function (response, opts) {
                                    Ext.MessageBox.show({
                                        title: 'Information',
                                        msg: lang('Data deleted'),
                                        buttons: Ext.MessageBox.OK,
                                        animateTarget: 'mb9',
                                        icon: 'ext-mb-success'
                                    });
                                    
                                    //refresh store
                                    thisObj.MainGrid.load();
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
            id: 'Koltiva.view.Traceability_new.Batching.PanelDataPurchaseDetail-MainGrid',
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
                    text: lang('Add'),
                    hidden: m_act_add,
                    cls: 'Sfr_BtnGridGreen',
                    overCls: 'Sfr_BtnGridGreen-Hover',
                    id: 'Koltiva.view.Traceability_new.Batching.PanelDataPurchaseDetail-BtnAdd',
                    handler: function () {

                        thisObj.WinFormDataBatchingStep = Ext.create('Koltiva.view.Traceability_new.Batching.WinFormDataBatchingStep', {
                            viewVar: {
                                OpsiDisplay: 'insert',
                                StoreGridMain: thisObj.MainGrid,
                                SupplyBatchID: thisObj.viewVar.SupplyBatchID
                            }
                        });

                        if (!thisObj.WinFormDataBatchingStep.isVisible()) {
                            thisObj.WinFormDataBatchingStep.center();
                            thisObj.WinFormDataBatchingStep.show();
                        } else {
                            thisObj.WinFormDataBatchingStep.close();
                        }
                    }
                },{
                    xtype: 'button',
                    icon: varjs.config.base_url + 'images/icons/new/save.png',
                    text: lang('Complete'),
                    // hidden: m_act_add,
                    hidden: true,
                    cls: 'Sfr_BtnGridBlue',
                    overCls: 'Sfr_BtnGridBlue-Hover',
                    id: 'Koltiva.view.Traceability_new.Batching.PanelDataPurchaseDetail-BtnComplete',
                    handler: function () {

                        if(thisObj.viewVar.SupplyBatchID) {
                            
                            Ext.Ajax.request({
                                waitMsg: 'Please Wait',
                                url: m_api + '/traceability_api/batching/data_supplychain_batch_complete',
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

                                                Ext.getCmp('Koltiva.view.Traceability_new.Batching.MainForm').destroy();
                                                var MainForm = [];
                                                if (Ext.getCmp('Koltiva.view.Traceability_new.Batching.MainForm') == undefined) {
                                                    MainForm = Ext.create('Koltiva.view.Traceability_new.Batching.MainForm', {
                                                        viewVar: {
                                                            OpsiDisplay: 'update',
                                                            SupplyBatchID: thisObj.viewVar.SupplyBatchID,
                                                            SupplyBatchStatusID : "5"//complete
                                                        }
                                                    });
                                                } else {
                                                    Ext.getCmp('Koltiva.view.Traceability_new.Batching.MainForm').destroy();
                                                    MainForm = Ext.create('Koltiva.view.Traceability_new.Batching.MainForm', {
                                                        viewVar: {
                                                            OpsiDisplay: 'update',
                                                            SupplyBatchID: thisObj.viewVar.SupplyBatchID,
                                                            SupplyBatchStatusID : "5"//complete
                                                        }
                                                    });
                                                }
                                            }
                                        }
                                    });

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
                },
                {
                    xtype: 'button',
                    icon: varjs.config.base_url + 'images/icons/new/reload.png',
                    cls: 'Sfr_BtnGridBlue',
                    overCls: 'Sfr_BtnGridBlue-Hover',
                    hidden : true,
                    handler: function () {
                        Ext.getCmp('Koltiva.view.Traceability_new.Batching.PanelDataPurchaseDetail-MainGrid').getStore().loadPage(1);
                    }
                }
                ]
            }],
            columns: [
                {
                text: ' ',
                xtype: 'actioncolumn',
                width: '40',
                id: 'Koltiva.view.Traceability_new.Batching.PanelDataPurchaseDetail-ActionColumn',
                items: [{
                    icon: varjs.config.base_url + 'images/icons/new/action.png',
                    handler: function (grid, rowIndex, colIndex, item, e, record) {
                        thisObj.ContextMenu.showAt(e.getXY());
                    }
                }]
                },
                {
                    text: lang('Step Name'),
                    dataIndex: 'StepName',
                    flex:1
                },
                {
                    text: lang('Start Date'),
                    dataIndex: 'ProcessStartDate',
                    width:130
                },
                {
                    text: lang('End Date'),
                    dataIndex: 'ProcessEndDate',
                    width:130,
                    // summaryRenderer: function(value, summaryData, dataIndex) {
                    //     return "TOTAL";
                    // }
                },
                {
                    text: lang('Total Weight'),
                    dataIndex: 'WeightBefore',
                    width:150
                },
                {
                    text: lang('Weight After Batching'),
                    dataIndex: 'WeightAfter',
                    width:170
                },
                {
                    text: lang('Note'),
                    dataIndex: 'Remark',
                    flex:1,
                }
            ]
        }];

        // mendapatkan value terbesar pada StandardName
        thisObj.MainGrid.on('load', function(store, records){
            sessionStorage.removeItem('recordPanelDataPurchaseDetail');
            sessionStorage.removeItem('maxpaluePanelDataPurchaseDetail');

            let maxpalue;

            // if (records.length > 0) {
            //     let arrayValue = [];
            //     records.forEach(function(k){
            //         let weightAfter = k.data.WeightAfter;
                    
            //         arrayValue.push(weightAfter);
            //     })

            //     maxpalue = arrayValue[0];
            //     // for (a of arrayValue) {
            //     //     if (a > maxpalue) {
            //     //         maxpalue = a;
            //     //     }
            //     // }
                
            //     sessionStorage.setItem('recordPanelDataPurchaseDetail', records.length);
            //     sessionStorage.setItem('maxpaluePanelDataPurchaseDetail', maxpalue);
            // } else {
            //     maxpalue = null;

            //     sessionStorage.setItem('recordPanelDataPurchaseDetail', 0);
            //     sessionStorage.setItem('maxpaluePanelDataPurchaseDetail', 0);
            // }

        }, this);


        this.callParent(arguments);
    }
});
Ext.define('Koltiva.view.Traceability_new.Delivery.PanelDataPurchaseDetail', {
    extend: 'Ext.panel.Panel',
    id: 'Koltiva.view.Traceability_new.Delivery.PanelDataPurchaseDetail',
    title: lang('Processing Step'),
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
        
        // thisObj.MainGrid = Ext.create('Koltiva.store.Traceability_new.Delivery.MainGridDataPurchaseDetail', {
        //     storeVar: {
        //         SupplyBatchID : thisObj.viewVar.SupplyBatchID
        //     }
        // });

        //ContextMenu
        thisObj.ContextMenu = Ext.create('Ext.menu.Menu', {
            cls: 'Sfr_ConMenu',
            items: [{
                icon: varjs.config.base_url + 'images/icons/new/update.png',
                text: lang('Update'),
                cls: 'Sfr_BtnConMenuWhite',
                hidden: m_act_update,
                handler: function () {
                    var sm = Ext.getCmp('Koltiva.view.Traceability_new.Delivery.PanelDataPurchaseDetail-MainGrid').getSelectionModel().getSelection()[0];

                    thisObj.WinFormDataProcessingStep = Ext.create('Koltiva.view.Traceability_new.Delivery.WinFormDataProcessingStep', {
                        viewVar: {
                            OpsiDisplay: 'update',
                            StoreGridMain: thisObj.MainGrid,
                            SupplyBatchID : thisObj.viewVar.SupplyBatchID,
                            SupplyBatchProcessingID: sm.get('SupplyBatchProcessingID')
                        }
                    });

                    if (!thisObj.WinFormDataProcessingStep.isVisible()) {
                        thisObj.WinFormDataProcessingStep.center();
                        thisObj.WinFormDataProcessingStep.show();
                    } else {
                        thisObj.WinFormDataProcessingStep.close();
                    }

                }
            }, {
                icon: varjs.config.base_url + 'images/icons/new/delete.png',
                text: lang('Delete'),
                cls: 'Sfr_BtnConMenuWhite',
                hidden: m_act_delete,
                handler: function () {
                    var sm = Ext.getCmp('Koltiva.view.Traceability_new.Delivery.PanelDataPurchaseDetail-MainGrid').getSelectionModel().getSelection()[0];

                    Ext.MessageBox.confirm('Message', lang('Do you want to delete this data ?'), function (btn) {
                        if (btn == 'yes') {

                            Ext.Ajax.request({
                                waitMsg: 'Please Wait',
                                url: m_api + '/traceability_api/processing/data_supplychain_batch_processing',
                                method: 'DELETE',
                                params: {
                                    SupplyBatchProcessingID: sm.get('SupplyBatchProcessingID'),
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
            id: 'Koltiva.view.Traceability_new.Delivery.PanelDataPurchaseDetail-MainGrid',
            cls: 'Sfr_GridNew',
            loadMask: true,
            height: 300,
            selType: 'rowmodel',
            style: 'border:1px solid #CCC;',
            store: thisObj.MainGrid,
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
                    id: 'Koltiva.view.Traceability_new.Delivery.PanelDataPurchaseDetail-BtnAdd',
                    handler: function () {

                        thisObj.WinFormDataProcessingStep = Ext.create('Koltiva.view.Traceability_new.Delivery.WinFormDataProcessingStep', {
                            viewVar: {
                                OpsiDisplay: 'insert',
                                StoreGridMain: thisObj.MainGrid,
                                SupplyBatchID: thisObj.viewVar.SupplyBatchID,
                            }
                        });

                        if (!thisObj.WinFormDataProcessingStep.isVisible()) {
                            thisObj.WinFormDataProcessingStep.center();
                            thisObj.WinFormDataProcessingStep.show();
                        } else {
                            thisObj.WinFormDataProcessingStep.close();
                        }
                    }
                },{
                    xtype: 'button',
                    icon: varjs.config.base_url + 'images/icons/new/save.png',
                    text: lang('Complete'),
                    hidden: m_act_add,
                    cls: 'Sfr_BtnGridBlue',
                    overCls: 'Sfr_BtnGridBlue-Hover',
                    id: 'Koltiva.view.Traceability_new.Delivery.PanelDataPurchaseDetail-BtnComplete',
                    handler: function () {

                        if(thisObj.viewVar.SupplyBatchID) {
                            
                            Ext.Ajax.request({
                                waitMsg: 'Please Wait',
                                url: m_api + '/traceability_api/processing/data_supplychain_batch_complete',
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

                                                Ext.getCmp('Koltiva.view.Traceability_new.Delivery.MainForm').destroy();
                                                var MainForm = [];
                                                if (Ext.getCmp('Koltiva.view.Traceability_new.Delivery.MainForm') == undefined) {
                                                    MainForm = Ext.create('Koltiva.view.Traceability_new.Delivery.MainForm', {
                                                        viewVar: {
                                                            OpsiDisplay: 'update',
                                                            SupplyBatchID: thisObj.viewVar.SupplyBatchID,
                                                            SupplyBatchStatusID : "5"//complete
                                                        }
                                                    });
                                                } else {
                                                    Ext.getCmp('Koltiva.view.Traceability_new.Delivery.MainForm').destroy();
                                                    MainForm = Ext.create('Koltiva.view.Traceability_new.Delivery.MainForm', {
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
                                msg: lang("No Selling data"),
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
                id: 'Koltiva.view.Traceability_new.Delivery.PanelDataPurchaseDetail-ActionColumn',
                items: [{
                    icon: varjs.config.base_url + 'images/icons/new/action.png',
                    handler: function (grid, rowIndex, colIndex, item, e, record) {
                        thisObj.ContextMenu.showAt(e.getXY());
                    }
                }]
            },{
                text: lang('Step Name'),
                dataIndex: 'StepName',
                flex:50
            },{
                text: lang('Process Start Date'),
                dataIndex: 'ProcessStartDate',
                flex:50
            },{
                text: lang('Process End Date'),
                dataIndex: 'ProcessEndDate',
                flex:50,
            },{
                text: lang('Weight Before'),
                dataIndex: 'WeightBefore',
                flex:50
            },{
                text: lang('Weight After'),
                dataIndex: 'WeightAfter',
                flex:50
            },{
                text: lang('Remark'),
                dataIndex: 'Remark',
                flex:50,
            }]
        }];


        this.callParent(arguments);
    }
});
Ext.define('Koltiva.view.Traceability_new.Batching.MainGrid', {
    extend: 'Ext.panel.Panel',
    id: 'Koltiva.view.Traceability_new.Batching.MainGrid',
    renderTo: 'ext-content',
    style: 'padding:0 7px 7px 7px;margin:2px 0 0 0;',
    listeners: {
        afterRender: function (component, eOpts) {
            var thisObj = this;
        }
    },
    initComponent: function () {
        var thisObj = this;
        //Store
        thisObj.StoreGridMain = Ext.create('Koltiva.store.Traceability_new.Batching.MainGrid');

        thisObj.items = [{
                xtype: 'grid',
                id: 'Koltiva.view.Traceability_new.Batching.MainGrid-Grid',
                style: 'border:1px solid #CCC;margin-top:4px;',
                minHeight:600,
                cls: 'Sfr_GridNew',
                loadMask: true,
                selType: 'rowmodel',
                store: thisObj.StoreGridMain,
                enableColumnHide: false,
                viewConfig: {
                    deferEmptyText: false,
                    emptyText: GetDefaultContentNoData()
                },
                dockedItems: [{
                        xtype: 'pagingtoolbar',
                        store: thisObj.StoreGridMain,
                        dock: 'bottom',
                        displayInfo: true,
                        displayMsg: lang('Showing') + ' {0} ' + lang('to') + ' {1} ' + lang('of') + ' {2} ' + lang('data')
                    }, {
                        xtype: 'toolbar',
                        dock: 'top',
                        items: [{
                                xtype: 'button',
                                icon: varjs.config.base_url + 'images/icons/new/add.png',
                                text: lang('Add'),
                                hidden: m_act_add,
                                cls: 'Sfr_BtnGridGreen',
                                overCls: 'Sfr_BtnGridGreen-Hover',
                                handler: function () {
                                    Ext.getCmp('Koltiva.view.Traceability_new.Batching.MainGrid').destroy(); //destory current view
                                    var MainFormBatch = [];
                                    //create object View untuk FormMainGrower
                                    if (Ext.getCmp('Koltiva.view.Traceability_new.Batching.MainForm') == undefined) {
                                        MainFormBatch = Ext.create('Koltiva.view.Traceability_new.Batching.MainForm', {
                                            viewVar: {
                                                OpsiDisplay: 'insert'
                                            }
                                        });
                                    } else {
                                        //destroy, create ulang
                                        Ext.getCmp('Koltiva.view.Traceability_new.Batching.MainForm').destroy();
                                        MainFormBatch = Ext.create('Koltiva.view.Traceability_new.Batching.MainForm', {
                                            viewVar: {
                                                OpsiDisplay: 'insert'
                                            }
                                        });
                                    }
                                }
                            }, 
                            {
                                xtype: 'button',
                                id: 'Koltiva.view.Traceability_new.Batching.MainGrid-gridToolbar-BtnExport',
                                icon: varjs.config.base_url + 'images/icons/new/export.png', cls:'Sfr_BtnGridPaleBlue',
                                margin: '0px 10px 0px 6px',
                                text: lang('Export'),
                                cls:'Sfr_BtnGridGreen',
                                overCls:'Sfr_BtnGridGreen-Hover',
                                handler: function() {

                                    Ext.MessageBox.show({
                                        msg: 'Please wait...',
                                        progressText: 'Exporting...',
                                        width: 300,
                                        wait: true,
                                        waitConfig: {
                                            interval: 200
                                        },
                                        icon: 'ext-mb-download', //custom class in msg-box.html
                                        animateTarget: 'mb7'
                                    });

                                    var param_string    = '?sid=';

                                    try {
                                        Ext.destroy(Ext.get('downloadIframe'));
                                    }
                                    catch(e) {}
    
                                    Ext.Ajax.request({
                                        url: m_api+'/traceability_api/web_penerimaan/export_batch/'+param_string,
                                    
                                        method: 'GET',
                                        waitMsg: lang('Please Wait'),
                                        timeout: 360000,
                                        success: function(data) {
                                            Ext.MessageBox.hide();
                                            var jsonResp = JSON.parse(data.responseText);
                                            window.location = jsonResp.filenya;
                                        },
                                        failure: function() {
                                            Ext.MessageBox.hide();
                                            Ext.MessageBox.show({
                                                title: 'Notifications',
                                                msg: 'Failed to export, Please try again.',
                                                buttons: Ext.MessageBox.OK,
                                                animateTarget: 'mb9',
                                                icon: 'ext-mb-error'
                                            });
                                        }
                                    });
                                    
                                }
                            },
                            {
                                xtype: 'tbspacer',
                                flex: 1
                            },{
                                xtype: 'button',
                                icon: varjs.config.base_url + 'images/icons/new/add-filter.png',
                                text: lang('Apply Filter'),
                                cls: 'Sfr_BtnGridPaleBlue',
                                overCls: 'Sfr_BtnGridPaleBlue-Hover',
                                handler: function () {
                                    var WinApplyFilter = Ext.create('Koltiva.view.Traceability_new.Batching.WinApplyFilter', {
                                        viewVar: {
                                            StoreGridMain: thisObj.StoreGridMain
                                        }
                                    });
                                    if (!WinApplyFilter.isVisible()) {
                                        WinApplyFilter.center();
                                        WinApplyFilter.show();
                                    } else {
                                        WinApplyFilter.close();
                                    }
                                }
                            },{
                                xtype: 'button',
                                icon: varjs.config.base_url + 'images/icons/new/reload.png',
                                cls: 'Sfr_BtnGridBlue',
                                overCls: 'Sfr_BtnGridBlue-Hover',
                                handler: function () {
                                    Ext.getCmp('Koltiva.view.Traceability_new.Batching.MainGrid-Grid').getStore().loadPage(1);
                                }
                            }]
                    }],
                columns: [{
                        text: '',
                        xtype: 'actioncolumn',
                        width: '5%',
                        items: [{
                                icon: varjs.config.base_url + 'images/icons/new/action.png',
                                handler: function (grid, rowIndex, colIndex, item, e, record) {
                                    if (Ext.isDefined(Ext.getCmp('ContextMenuMainGrid'))){
                                        Ext.getCmp('ContextMenuMainGrid').destroy();
                                    }

                                    //ContextMenu
                                    thisObj.ContextMenuGrid = Ext.create('Ext.menu.Menu', {
                                        cls: 'Sfr_ConMenu',
                                        id:"ContextMenuMainGrid",
                                        items: [{
                                                icon: varjs.config.base_url + 'images/icons/new/view.png',
                                                text: lang('View'),
                                                cls: 'Sfr_BtnConMenuWhite',
                                                handler: function () {
                                                    var sm = Ext.getCmp('Koltiva.view.Traceability_new.Batching.MainGrid-Grid').getSelectionModel().getSelection()[0];
                                                    Ext.getCmp('Koltiva.view.Traceability_new.Batching.MainGrid').destroy(); //destory current view

                                                    var MainFormBatch = [];
                                                    //create object View untuk FormMainGrower
                                                    if (Ext.getCmp('Koltiva.view.Traceability_new.Batching.MainForm') == undefined) {
                                                        MainFormBatch = Ext.create('Koltiva.view.Traceability_new.Batching.MainForm', {
                                                            viewVar: {
                                                                OpsiDisplay: 'view',
                                                                SupplyBatchID: sm.get('SupplyBatchID'),
                                                                SupplyBatchStatusID: sm.get('SupplyBatchStatusID')
                                                            }
                                                        });
                                                    } else {
                                                        //destroy, create ulang
                                                        Ext.getCmp('Koltiva.view.Traceability_new.Batching.MainForm').destroy();
                                                        MainFormBatch = Ext.create('Koltiva.view.Traceability_new.Batching.MainForm', {
                                                            viewVar: {
                                                                OpsiDisplay: 'view',
                                                                SupplyBatchID: sm.get('SupplyBatchID'),
                                                                SupplyBatchStatusID: sm.get('SupplyBatchStatusID')
                                                            }
                                                        });
                                                    }
                                                }
                                            },{
                                                icon: varjs.config.base_url + 'images/icons/new/update.png',
                                                text: lang('Update'),
                                                cls: 'Sfr_BtnConMenuWhite',
                                                id: 'Koltiva.view.Traceability_new.Batching.MainGrid.ContextMenu-ButtonUpdate',
                                                hidden: m_act_update,
                                                handler: function () {
                                                    var sm = Ext.getCmp('Koltiva.view.Traceability_new.Batching.MainGrid-Grid').getSelectionModel().getSelection()[0];

                                                    Ext.getCmp('Koltiva.view.Traceability_new.Batching.MainGrid').destroy(); //destory current view

                                                    var MainFormBatch = [];
                                                    //create object View untuk FormMainGrower
                                                    if (Ext.getCmp('Koltiva.view.Traceability_new.Batching.MainForm') == undefined) {
                                                        MainFormBatch = Ext.create('Koltiva.view.Traceability_new.Batching.MainForm', {
                                                            viewVar: {
                                                                OpsiDisplay: 'update',
                                                                SupplyBatchID: sm.get('SupplyBatchID'),
                                                                SupplyBatchStatusID: sm.get('SupplyBatchStatusID'),
                                                            }
                                                        });
                                                    } else {
                                                        //destroy, create ulang
                                                        Ext.getCmp('Koltiva.view.Traceability_new.Batching.MainForm').destroy();
                                                        MainFormBatch = Ext.create('Koltiva.view.Traceability_new.Batching.MainForm', {
                                                            viewVar: {
                                                                OpsiDisplay: 'update',
                                                                SupplyBatchID: sm.get('SupplyBatchID'),
                                                                SupplyBatchStatusID: sm.get('SupplyBatchStatusID'),
                                                            }
                                                        });
                                                    }
                                                }
                                            },{
                                                icon: varjs.config.base_url + 'images/icons/new/delete.png',
                                                text: lang('Delete'),
                                                id: 'Koltiva.view.Traceability_new.Batching.MainGrid.ContextMenu-ButtonDelete',
                                                cls: 'Sfr_BtnConMenuWhite',
                                                // hidden: m_act_delete,
                                                hidden: true,
                                                handler: function () {
                                                    var sm = Ext.getCmp('Koltiva.view.Traceability_new.Batching.MainGrid-Grid').getSelectionModel().getSelection()[0];
                                                    Ext.MessageBox.confirm('Message', lang('Do you want to delete this data ?'), function (btn) {
                                                        if (btn == 'yes') {

                                                            Ext.Ajax.request({
                                                                waitMsg: 'Please Wait',
                                                                url: m_api + '/traceability_api/batching/data_supplychain_batch',
                                                                method: 'DELETE',
                                                                params: {
                                                                    SupplyBatchID: sm.get('SupplyBatchID'),
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
                                                                    thisObj.StoreGridMain.load();
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

                                    thisObj.ContextMenuGrid.showAt(e.getXY());

                                    if (record.get('Status') == "Closed" || record.get('Status') == "Delivered") {
                                        Ext.getCmp('Koltiva.view.Traceability_new.Batching.MainGrid.ContextMenu-ButtonUpdate').hide();
                                    }
                                }
                            }]
                    }, {
                        text: 'No',
                        width: '5%',
                        xtype: 'rownumberer'
                    }, {
                        text: lang('Batch Number'),
                        dataIndex: 'SupplyBatchNumber',
                        width: '17%',
                    }, {
                        text: lang('Name'),
                        dataIndex: 'AgentName',
                        width: '17%',
                    }, {
                        text: lang('Weight'),
                        dataIndex: 'VolumeBruto',
                        width: '17%',
                    },{
                        text: lang('Supply Batch Status'),
                        dataIndex: 'Status',
                        width: '17%',
                    },{
                        text: lang('Date Created'),
                        dataIndex: 'DateCreated',
                        width: '20%',
                    }]
            }];
        this.callParent(arguments);
    }
});
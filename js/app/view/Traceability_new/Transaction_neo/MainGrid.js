Ext.define('Koltiva.view.Traceability_new.Transaction_neo.MainGrid', {
    extend: 'Ext.panel.Panel',
    id: 'Koltiva.view.Traceability_new.Transaction_neo.MainGrid',
    renderTo: 'ext-content',
    style: 'padding:0 7px 7px 7px;margin:2px 0 0 0;',
    listeners: {
        afterRender: function (component, eOpts) {
            var thisObj = this;
            // document.getElementById('divCommonContentRegion2').style.display = 'block';
        }
    },
    initComponent: function () {
        var thisObj = this;
        //Store
        thisObj.StoreGridMain = Ext.create('Koltiva.store.Traceability_new.Transaction_neo.MainGrid');
        //ContextMenu
        thisObj.items = [{
                xtype: 'grid',
                id: 'Koltiva.view.Traceability_new.Transaction_neo.MainGrid-Grid',
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
                                // hidden: m_act_add,
                                cls: 'Sfr_BtnGridGreen',
                                overCls: 'Sfr_BtnGridGreen-Hover',
                                handler: function () {
                                    Ext.getCmp('Koltiva.view.Traceability_new.Transaction_neo.MainGrid').destroy(); //destory current view
                                    var MainFormBatch = [];
                                    //create object View untuk FormMainGrower
                                    if (Ext.getCmp('Koltiva.view.Traceability_new.Transaction_neo.MainForm') == undefined) {
                                        MainFormBatch = Ext.create('Koltiva.view.Traceability_new.Transaction_neo.MainForm', {
                                            viewVar: {
                                                OpsiDisplay: 'insert'
                                            }
                                        });
                                    } else {
                                        //destroy, create ulang
                                        Ext.getCmp('Koltiva.view.Traceability_new.Transaction_neo.MainForm').destroy();
                                        MainFormBatch = Ext.create('Koltiva.view.Traceability_new.Transaction_neo.MainForm', {
                                            viewVar: {
                                                OpsiDisplay: 'insert'
                                            }
                                        });
                                    }
                                }
                            },
                            {
                                xtype: 'button',
                                id: 'Koltiva.view.Traceability_new.Transaction.MainGrid-gridToolbar-BtnExport',
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

                                    var filter = getFilterLs();
                                    var keys = Object.keys(filter);
                                    var param_string = '?';
                                    $.each(keys, function (index, val) {
                                        param_string += '' +'&' + val + '=' + filter[val];
                                    });
                                    
                                    try {
                                        Ext.destroy(Ext.get('downloadIframe'));
                                    }
                                    catch(e) {}
    
                                    Ext.Ajax.request({
                                        url: m_api+'/traceability_api/web_transaction/export_excel/'+param_string,
                                    
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
                            }, {
                                xtype: 'button',
                                icon: varjs.config.base_url + 'images/icons/new/add-filter.png',
                                text: lang('Apply Filter'),
                                cls: 'Sfr_BtnGridPaleBlue',
                                overCls: 'Sfr_BtnGridPaleBlue-Hover',
                                handler: function () {
                                    var WinApplyFilter = Ext.create('Koltiva.view.Traceability_new.Transaction_neo.WinApplyFilter', {
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
                            }, {
                                xtype: 'button',
                                icon: varjs.config.base_url + 'images/icons/new/reload.png',
                                cls: 'Sfr_BtnGridBlue',
                                overCls: 'Sfr_BtnGridBlue-Hover',
                                handler: function () {
                                    Ext.getCmp('Koltiva.view.Traceability_new.Transaction_neo.MainGrid-Grid').getStore().loadPage(1);
                                }
                            }]
                    }],
                columns: [{
                        text: '',
                        xtype: 'actioncolumn',
                        flex: 5,
                        items: [{
                                icon: varjs.config.base_url + 'images/icons/new/action.png',
                                handler: function (grid, rowIndex, colIndex, item, e, record) {
                                    if(Ext.isDefined(Ext.getCmp('ContextMenuMainGrid'))){
                                        Ext.getCmp('ContextMenuMainGrid').destroy();
                                    }

                                    thisObj.ContextMenuGrid = Ext.create('Ext.menu.Menu', {
                                        cls: 'Sfr_ConMenu',
                                        id:"ContextMenuMainGrid",
                                        items: [{
                                                icon: varjs.config.base_url + 'images/icons/new/view.png',
                                                text: lang('View'),
                                                cls: 'Sfr_BtnConMenuWhite',
                                                handler: function () {
                                                    var sm = Ext.getCmp('Koltiva.view.Traceability_new.Transaction_neo.MainGrid-Grid').getSelectionModel().getSelection()[0];
                                                    Ext.getCmp('Koltiva.view.Traceability_new.Transaction_neo.MainGrid').destroy(); //destory current view

                                                    var MainFormBatch = [];
                                                    //create object View untuk FormMainGrower
                                                    if (Ext.getCmp('Koltiva.view.Traceability_new.Transaction_neo.MainForm') == undefined) {
                                                        MainFormBatch = Ext.create('Koltiva.view.Traceability_new.Transaction_neo.MainForm', {
                                                            viewVar: {
                                                                OpsiDisplay: 'view',
                                                                SupplyTransID: sm.get('SupplyTransID'),
                                                                SupplychainID: sm.get('SupplychainID')
                                                            }
                                                        });
                                                    } else {
                                                        //destroy, create ulang
                                                        Ext.getCmp('Koltiva.view.Traceability_new.Transaction_neo.MainForm').destroy();
                                                        MainFormBatch = Ext.create('Koltiva.view.Traceability_new.Transaction_neo.MainForm', {
                                                            viewVar: {
                                                                OpsiDisplay: 'view',
                                                                SupplyTransID: sm.get('SupplyTransID'),
                                                                SupplychainID: sm.get('SupplychainID')
                                                            }
                                                        });
                                                    }
                                                }
                                            }, {
                                                icon: varjs.config.base_url + 'images/icons/new/update.png',
                                                text: lang('Update'),
                                                cls: 'Sfr_BtnConMenuWhite',
                                                id: 'Koltiva.view.Traceability_new.Transaction_neo.MainGrid.ContextMenu-ButtonUpdate',
                                                hidden: m_act_update,
                                                handler: function () {
                                                    var sm = Ext.getCmp('Koltiva.view.Traceability_new.Transaction_neo.MainGrid-Grid').getSelectionModel().getSelection()[0];

                                                    Ext.getCmp('Koltiva.view.Traceability_new.Transaction_neo.MainGrid').destroy(); //destory current view

                                                    var MainFormBatch = [];
                                                    //create object View untuk FormMainGrower
                                                    if (Ext.getCmp('Koltiva.view.Traceability_new.Transaction_neo.MainForm') == undefined) {
                                                        MainFormBatch = Ext.create('Koltiva.view.Traceability_new.Transaction_neo.MainForm', {
                                                            viewVar: {
                                                                OpsiDisplay: 'update',
                                                                SupplyTransID: sm.get('SupplyTransID'),
                                                                SupplychainID: sm.get('SupplychainID')
                                                            }
                                                        });
                                                    } else {
                                                        //destroy, create ulang
                                                        Ext.getCmp('Koltiva.view.Traceability_new.Transaction_neo.MainForm').destroy();
                                                        MainFormBatch = Ext.create('Koltiva.view.Traceability_new.Transaction_neo.MainForm', {
                                                            viewVar: {
                                                                OpsiDisplay: 'update',
                                                                SupplyTransID: sm.get('SupplyTransID'),
                                                                SupplychainID: sm.get('SupplychainID')
                                                            }
                                                        });
                                                    }
                                                }
                                            }, {   
                                                //generate qrcode
                                                icon: varjs.config.base_url + 'images/icons/new/refresh1.png',
                                                id: 'Koltiva.view.Traceability_new.Transaction_neo.MainGrid.ContextMenu-CheckPaymentStatus',
                                                text: lang('Check Payment Status '),
                                                //hidden:true,
                                                handler: function() {
                 
                                                    var sm = Ext.getCmp('Koltiva.view.Traceability_new.Transaction_neo.MainGrid-Grid').getSelectionModel().getSelection();

                                                    if(sm.length > 0) {
                                                        Ext.Ajax.request({
                                                            url: m_api + '/traceability_api/web_transaction/check_payment_status',
                                                            method: 'GET',
                                                            params: {
                                                                SupplyTransID : sm[0].get('SupplyTransID'),
                                                                PaymentMethodID : sm[0].get('PaymentMethodID'),
                                                                uid : sm[0].get('uid'),
                                                            
                                                            },
                                                            success: function(fp, o){
                                                                var r = Ext.decode(fp.responseText);
                                                                
                                                                Ext.MessageBox.show({
                                                                    title: lang('Information'),
                                                                    msg: lang('Check Payment Success'),
                                                                    buttons: Ext.MessageBox.OK,
                                                                    animateTarget: 'mb9',
                                                                    icon: 'ext-mb-success'
                                                                });
                 
                                                                Ext.getCmp('Koltiva.view.Traceability_new.Transaction_neo.MainGrid-Grid').getStore().loadPage(1);
                                                                // Ext.getCmp('Koltiva.view.Traceability_new.Transaction_neo.MainGrid-Grid').getSelectionModel().getSelection()[1];
                 
                                                            },
                                                            failure: function(fp, o){
                                                                var r = Ext.decode(fp.responseText);
                                                                
                                                                Ext.MessageBox.show({
                                                                    title: lang('Error'),
                                                                    msg: "Check Payment Status Failed",
                                                                    buttons: Ext.MessageBox.OK,
                                                                    animateTarget: 'mb9',
                                                                    icon: 'ext-mb-error'
                                                                });
                                                            } 
                                                        });
                 
                                                    } else {
                                                        Ext.MessageBox.show({
                                                            title: 'Information',
                                                            msg: lang('Please select data'),
                                                            buttons: Ext.MessageBox.OK,
                                                            animateTarget: 'mb9',
                                                            icon: 'ext-mb-success'
                                                        });
                                                    }
                 
                                                }
                                             },
                                            {
                                                icon: varjs.config.base_url + 'images/icons/silk/printer.png',
                                                text: lang('Print'),
                                                itemId: 'Koltiva.view.Traceability_new.Transaction.List_transaction-contextMenuCetakKwitansiItem',
                                                //hidden: m_act_update,
                                                handler: function() {
                                                    var sm = Ext.getCmp('Koltiva.view.Traceability_new.Transaction_neo.MainGrid-Grid').getSelectionModel().getSelection()[0];
                                                    preview_cetak_surat(m_api + '/web-traceability/cetak-kuitansi/' +  sm.get('SupplyTransID') + '/' + sm.get('SupplychainID') );  
                                                }
                                            }
                                        ]
                                    });
                                    
                                    thisObj.ContextMenuGrid.showAt(e.getXY());
                                    
                                    if (parseInt(record.get('SupplychainID')) == 2) {
                                        Ext.getCmp('Koltiva.view.Traceability_new.Transaction_neo.MainGrid.ContextMenu-ButtonUpdate').hide();
                                        Ext.getCmp('Koltiva.view.Traceability_new.Transaction_neo.MainGrid.ContextMenu-ButtonDelete').hide();
                                    }
                                    
                                    if (record.get('Status') == "Complete") {
                                        Ext.getCmp('Koltiva.view.Traceability_new.Transaction_neo.MainGrid.ContextMenu-ButtonUpdate').hide();
                                    }

                                    if (record.get('PaymentStatus') !== "Waiting Payment") {
                                        Ext.getCmp('Koltiva.view.Traceability_new.Transaction_neo.MainGrid.ContextMenu-CheckPaymentStatus').hide();
                                    }
                                }
                            }]
                    }, {
                        text: 'No',
                        width: '5%',
                        xtype: 'rownumberer'
                    }, {
                        text: lang('Status'),
                        dataIndex: 'SupplyStatus',
                        flex:20
                    }, {
                        text: 'Transaction ID',
                        dataIndex: 'SupplyTransID',
                        flex:20
                    }, {
                        text: lang('Trans Number'),
                        dataIndex: 'TransNumber',
                        flex:20
                    }, {
                        text: 'ID',
                        dataIndex: 'SupplyID',
                        hidden: true,
                    }, {
                        text: lang('Supply Type'),
                        dataIndex: 'SupplyType',
                        flex:20
                    }, {
                        text: lang('Farmer ID'),
                        dataIndex: 'MemberDisplayID',
                        flex:20
                    }, {
                        text: lang('Supplier Name'),
                        dataIndex: 'SupplierName',
                        flex:20
                    },{
                        text: lang('Transaction Type'),
                        dataIndex: 'SalesType',
                        flex:20
                    },{
                        text: lang('Certified'),
                        dataIndex: 'Certified',
                        flex:20
                    },{
                        text: lang('Date'),
                        dataIndex: 'DateTransaction',
                        renderer: Ext.util.Format.dateRenderer('d-m-Y'),
                        flex:20
                    },{
                        text: lang('Janjang'),
                        dataIndex: 'Bunches',
                        flex:20
                    },{
                        text: lang('Gross'),
                        dataIndex: 'VolumeBruto',
                        flex:20
                    },{
                        text: lang('Netto'),
                        dataIndex: 'VolumeNetto',
                        flex:20
                    },{
                        id: 'Koltiva.view.Traceability_new.Transaction_neo.MainGrid-Grid-PaymentAmount',
                        text: lang('Amount'),
                        dataIndex: 'PaymentAmount',
                        flex:20
                    },
                    {
                        id: 'Koltiva.view.Traceability_new.Transaction_neo.MainGrid-Grid-PaymentStatus',
                        text: lang('Payment Status'),
                        dataIndex: 'PaymentStatus',
                        flex:20
                    },{
                        text: lang('Traceable'),
                        dataIndex: 'isTraceable',
                        flex:20
                    }],
                    listeners: {
                        afterRender: function(data, r) {
    
                            if(m_IsPaymentMethod != 1){
                                Ext.getCmp('Koltiva.view.Traceability_new.Transaction_neo.MainGrid-Grid-PaymentStatus').hide();
                                Ext.getCmp('Koltiva.view.Traceability_new.Transaction_neo.MainGrid-Grid-PaymentAmount').hide();
                                // Ext.getCmp('Koltiva.view.Traceability_new.Transaction_neo.MainGrid-Grid').columnManager.getColumns()[10].setVisible(false);
                                // Ext.getCmp('Koltiva.view.Traceability_new.Transaction_neo.MainGrid-Grid').doLayout();
                            }
                            else{
                                Ext.getCmp('Koltiva.view.Traceability_new.Transaction_neo.MainGrid-Grid-PaymentStatus').show();
                                Ext.getCmp('Koltiva.view.Traceability_new.Transaction_neo.MainGrid-Grid-PaymentAmount').show();
                                // Ext.getCmp('Koltiva.view.Traceability_new.Transaction_neo.MainGrid-Grid').columnManager.getColumns()[10].setVisible(true);
                                // Ext.getCmp('Koltiva.view.Traceability_new.Transaction_neo.MainGrid-Grid').doLayout();
                            }
                        }
                    }
            }];
        this.callParent(arguments);
    }
});

function getFilterLs() {
    var filters = {};

    //ngeload filter parameters
    var cof_gridtransaction_params = JSON.parse(localStorage.getItem('cof_gridtransaction_params'));

    if (cof_gridtransaction_params != null) {
        filters.ArrFilter                      = cof_gridtransaction_params.ArrFilter.join(',');
        filters.TextFilterTransTypeName        = cof_gridtransaction_params.TextFilterTransTypeName;
        filters.TextFilterTransSupplyID        = cof_gridtransaction_params.TextFilterTransSupplyID;
        filters.TextFilterMemberName           = cof_gridtransaction_params.TextFilterMemberName;
        filters.TextFilterStartDateTransaction = cof_gridtransaction_params.TextFilterStartDateTransaction;
        filters.TextFilterEndDateTransaction   = cof_gridtransaction_params.TextFilterEndDateTransaction;
    } else {
        //reset params
        filters.ArrFilter                      = null;
        filters.TextFilterTransTypeName        = null;
        filters.TextFilterTransSupplyID        = null;
        filters.TextFilterMemberName           = null;
        filters.TextFilterStartDateTransaction = null;
        filters.TextFilterEndDateTransaction   = null;
    }
    
    return filters;
}
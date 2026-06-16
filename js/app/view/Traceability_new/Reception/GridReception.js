Ext.define('Koltiva.view.Traceability_new.Reception.GridReception', {
    extend: 'Ext.panel.Panel',
    id: 'Koltiva.view.Traceability_new.Reception.GridReception',
    renderTo: 'ext-content',
    style: 'padding:0 7px 7px 7px;margin:2px 0 0 0;',
    listeners: {
        afterRender: function (component, eOpts, record) {
            var thisObj = this;

        }
    },
    initComponent: function () {
        var thisObj = this;
        //Store
        thisObj.StoreGridMain = Ext.create('Koltiva.store.Traceability_new.Reception.StoreGridReception');

        thisObj.items = [{
                xtype: 'grid',
                id: 'Koltiva.view.Traceability_new.Reception.GridReception-Grid',
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
                        items: [
                            {
                                xtype: 'button',
                                id: 'Koltiva.view.Traceability_new.Reception.GridReception-Grid-BtnExport',
                                icon: varjs.config.base_url + 'images/icons/new/export.png',
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

                                    Ext.Ajax.request({
                                        url: m_api + '/traceability_api/web_penerimaan/export_reception'+ param_string,
                                        method: 'GET',
                                        waitMsg: lang('Export data...'),
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
                                    var WinApplyFilter = Ext.create('Koltiva.view.Traceability_new.Reception.WinApplyFilter', {
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
                                    Ext.getCmp('Koltiva.view.Traceability_new.Reception.GridReception-Grid').getStore().loadPage(1);
                                }
                            }]
                    }],
                columns: [{
                        text: '',
                        xtype: 'actioncolumn',
                        width: 35,
                        items: [{
                                icon: varjs.config.base_url + 'images/icons/new/action.png',
                                handler: function (grid, rowIndex, colIndex, item, e, record) {
                                    // console.log(record);
                                     let textLabel
                                     let iconLabel

                                     if (parseInt(record.get('DeliveryStatusID')) == 4)  {
                                         textLabel = 'View Reception & Payment'
                                         iconLabel = 'images/icons/new/view.png'
                                     } else {
                                         textLabel = 'Edit Reception'
                                         iconLabel = 'images/icons/new/update.png'
                                     }

                                     //ContextMenu
                                        if(Ext.isDefined(Ext.getCmp('ContextMenuGridReception'))){
                                            Ext.getCmp('ContextMenuGridReception').destroy();
                                        }

                                        thisObj.ContextMenuGrid = Ext.create('Ext.menu.Menu', {
                                            cls: 'Sfr_ConMenu',
                                            id:"ContextMenuGridReception",
                                            items: [{
                                                    icon: varjs.config.base_url + iconLabel,
                                                    text: lang(textLabel),
                                                    id: 'Koltiva.view.Traceability_new.Reception.GridReception.ContextMenu-View',
                                                    cls: 'Sfr_BtnConMenuWhite',
                                                    handler: function () {
                                                        var sm = Ext.getCmp('Koltiva.view.Traceability_new.Reception.GridReception-Grid').getSelectionModel().getSelection()[0];
                                                        Ext.getCmp('Koltiva.view.Traceability_new.Reception.GridReception').destroy(); //destory current view

                                                        var MainFormBatch = [];
                                                        //create object View untuk FormMainGrower
                                                        if (Ext.getCmp('Koltiva.view.Traceability_new.Reception.FormReception') == undefined) {
                                                            MainFormBatch = Ext.create('Koltiva.view.Traceability_new.Reception.FormReception', {
                                                                viewVar: {
                                                                    OpsiDisplay: 'view',
                                                                    DeliveryID: sm.get('DeliveryID'),
                                                                    SupplyTransID: sm.get('SupplyTransID'),
                                                                    DestinationID: sm.get('DestinationID'),
                                                                }
                                                            });
                                                        } else {
                                                            //destroy, create ulang
                                                            Ext.getCmp('Koltiva.view.Traceability_new.Reception.FormReception').destroy();
                                                            MainFormBatch = Ext.create('Koltiva.view.Traceability_new.Reception.FormReception', {
                                                                viewVar: {
                                                                    OpsiDisplay: 'view',
                                                                    DeliveryID: sm.get('DeliveryID'),
                                                                    SupplyTransID: sm.get('SupplyTransID'),
                                                                    DestinationID: sm.get('DestinationID'),
                                                                }
                                                            });
                                                        }
                                                    }
                                                }, 
                                                {
                                                    icon: varjs.config.base_url + 'images/icons/new/update.png',
                                                    text: lang('Receiving'),
                                                    id: 'Koltiva.view.Traceability_new.Reception.GridReception.ContextMenu-ReceivingNew',
                                                    cls: 'Sfr_BtnConMenuWhite',
                                                    hidden: m_act_update,
                                                    handler: function () {
                                                        var sm = Ext.getCmp('Koltiva.view.Traceability_new.Reception.GridReception-Grid').getSelectionModel().getSelection()[0];

                                                        Ext.getCmp('Koltiva.view.Traceability_new.Reception.GridReception').destroy(); //destory current view

                                                        var MainFormBatch = [];
                                                        //create object View untuk FormMainGrower
                                                        if (Ext.getCmp('Koltiva.view.Traceability_new.Reception.FormReception') == undefined) {
                                                            MainFormBatch = Ext.create('Koltiva.view.Traceability_new.Reception.FormReception', {
                                                                viewVar: {
                                                                    OpsiDisplay: 'update',
                                                                    DeliveryID: sm.get('DeliveryID'),
                                                                    SupplyTransID: sm.get('SupplyTransID'),
                                                                    DestinationID: sm.get('DestinationID'),
                                                                }
                                                            });
                                                        } else {
                                                            //destroy, create ulang
                                                            Ext.getCmp('Koltiva.view.Traceability_new.Reception.FormReception').destroy();
                                                            MainFormBatch = Ext.create('Koltiva.view.Traceability_new.Reception.FormReception', {
                                                                viewVar: {
                                                                    OpsiDisplay: 'update',
                                                                    DeliveryID: sm.get('DeliveryID'),
                                                                    SupplyTransID: sm.get('SupplyTransID'),
                                                                    DestinationID: sm.get('DestinationID'),
                                                                }
                                                            });
                                                        }
                                                    }
                                                },
                                                {
                                                    icon: varjs.config.base_url + 'images/icons/new/document_link.png',
                                                    id: 'Koltiva.view.Traceability_new.Reception.MainGrid.ContextMenu-Detail',
                                                    text: lang('Transaction Detail'),
                                                    cls: 'Sfr_BtnConMenuWhite',
                                                    handler: function () {
                                                        var sm = Ext.getCmp('Koltiva.view.Traceability_new.Reception.GridReception-Grid').getSelectionModel().getSelection()[0];
                                                        // console.log(sm)
                                                        Ext.getCmp('Koltiva.view.Traceability_new.Reception.GridReception').destroy(); //destory current view
                                
                                                        var MainFormBatch = [];
                                                        if (Ext.getCmp('Koltiva.view.Traceability_new.Reception.MainGridTransaction') == undefined) {
                                                            MainFormBatch = Ext.create('Koltiva.view.Traceability_new.Reception.MainGridTransaction', {
                                                                viewVar: {
                                                                    DeliveryID: sm.raw.DeliveryID
                                                                }
                                                            });
                                                        } else {
                                                            MainFormBatch = Ext.create('Koltiva.view.Traceability_new.Reception.MainGridTransaction', {
                                                                viewVar: {
                                                                    DeliveryID: sm.raw.DeliveryID
                                                                }
                                                            });
                                                        }
                                                    },
                                                },
                                                {   
                                                    //generate qrcode
                                                    icon: varjs.config.base_url + 'images/icons/new/refresh1.png',
                                                    id: 'Koltiva.view.Traceability_new.Transaction_neo.MainGrid.ContextMenu-CheckPaymentStatus',
                                                    text: lang('Check Payment Status '),
                                                    //hidden:true,
                                                    handler: function() {
                     
                                                        var sm = Ext.getCmp('Koltiva.view.Traceability_new.Reception.GridReception-Grid').getSelectionModel().getSelection();
    
                                                        if(sm.length > 0) {
                                                            Ext.Ajax.request({
                                                                url: m_api + '/traceability_api/reception/check_payment_status',
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
                     
                                                                    Ext.getCmp('Koltiva.view.Traceability_new.Reception.GridReception-Grid').getStore().loadPage(1);
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
                                                 }
                                            ]
                                        });
                                    
                                    thisObj.ContextMenuGrid.showAt(e.getXY());

                                    // console.log(record.get);

                                    if (record.get('PaymentStatusID') == 2) {
                                        Ext.getCmp('Koltiva.view.Traceability_new.Transaction_neo.MainGrid.ContextMenu-CheckPaymentStatus').show();
                                    }
                                    else {
                                        Ext.getCmp('Koltiva.view.Traceability_new.Transaction_neo.MainGrid.ContextMenu-CheckPaymentStatus').hide();
                                    }

                                    if (record.get('SupplychainIDSelf') != record.get('DestinationID')) {
                                        Ext.getCmp('Koltiva.view.Traceability_new.Reception.GridReception.ContextMenu-ReceivingNew').hide();
                                    }

                                    if (parseInt(record.get('DeliveryStatusID')) == 6) {
                                        Ext.getCmp('Koltiva.view.Traceability_new.Reception.GridReception.ContextMenu-ReceivingNew').hide();
                                    }
                                }
                            }]
                    }, {
                        text: 'No',
                        align:'center',
                        width: 50,
                        xtype: 'rownumberer'
                    }, {
                        text: lang('Shipment Date'),
                        dataIndex: 'DeliveryDate',
                        width: 120,
                    },{
                        text: lang('EXT Code'),
                        dataIndex: 'ExternalCode',
                        flex:1,
                    },{
                        text: lang('Transport Number'),
                        dataIndex: 'DestTransportNumber',
                        flex:1,
                    },{
                        text: lang('Supplier'),
                        dataIndex: 'AgentName',
                        width:'10%'
                    },{
                        text: lang('Nett Weight'),
                        dataIndex: 'TotalWeight',
                        renderer: Ext.util.Format.numberRenderer('0,000.00'),
                        width: 100,
                    },{
                        text: lang('Receive Date'),
                        dataIndex: 'DateReceipt',
                        width:100,
                        align:'center'
                    },{
                        text: lang('Shipment Status'),
                        dataIndex: 'DeliveryStatus',
                        width: 120,
                        align:'center'
                    },{
                        text: lang('Destination'),
                        dataIndex: 'DestinationName',
                        flex:1,
                    }
                    // {
                    //     id: 'Koltiva.view.Traceability_new.Reception.GridReception-Grid-PaymentStatus',
                    //     text: lang('Payment Status'),
                    //     dataIndex: 'PaymentStatus',
                    //     flex:1,
                    // },
                    // {
                    //     id: 'Koltiva.view.Traceability_new.Reception.GridReception-Grid-PaymentAmount',
                    //     text: lang('Amount'),
                    //     dataIndex: 'PaymentAmount',
                    //     flex:1,
                    // }
                ],
                listeners: {
                    afterRender: function(data, r) {

                        if(m_IsPaymentMethod != 1){
                            // Ext.getCmp('Koltiva.view.Traceability_new.Reception.GridReception-Grid-PaymentStatus').hide();
                            // Ext.getCmp('Koltiva.view.Traceability_new.Reception.GridReception-Grid-PaymentAmount').hide();
                            // Ext.getCmp('Koltiva.view.Traceability_new.Reception.GridReception-Grid').columnManager.getColumns()[10].setVisible(false);
                            // Ext.getCmp('Koltiva.view.Traceability_new.Reception.GridReception-Grid').doLayout();
                        }
                        else{
                            // Ext.getCmp('Koltiva.view.Traceability_new.Reception.GridReception-Grid-PaymentStatus').show();
                            // Ext.getCmp('Koltiva.view.Traceability_new.Reception.GridReception-Grid-PaymentAmount').show();
                            // Ext.getCmp('Koltiva.view.Traceability_new.Reception.GridReception-Grid').columnManager.getColumns()[10].setVisible(true);
                            // Ext.getCmp('Koltiva.view.Traceability_new.Reception.GridReception-Grid').doLayout();
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
    var cof_gridreception_params = JSON.parse(localStorage.getItem('cof_gridreception_params'));

    if (cof_gridreception_params != null) {

        filters.TextFilterStartShipmentDate = cof_gridreception_params.TextFilterStartShipmentDate;
        filters.TextFilterEndShipmentDate   = cof_gridreception_params.TextFilterEndShipmentDate;
    }
    
    return filters;
}
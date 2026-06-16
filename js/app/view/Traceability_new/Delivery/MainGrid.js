Ext.define('Koltiva.view.Traceability_new.Delivery.MainGrid', {
    extend: 'Ext.panel.Panel',
    id: 'Koltiva.view.Traceability_new.Delivery.MainGrid',
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
        thisObj.StoreGridMain = Ext.create('Koltiva.store.Traceability_new.Delivery.MainGrid');

        //ContextMenu       
        thisObj.items = [{
                xtype: 'grid',
                minHeight:500,
                id: 'Koltiva.view.Traceability_new.Delivery.MainGrid-Grid',
                style: 'border:1px solid #CCC;margin-top:4px;',
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
                                    Ext.getCmp('Koltiva.view.Traceability_new.Delivery.MainGrid').destroy(); //destory current view
                                    var MainFormBatch = [];
                                    //create object View untuk FormMainGrower
                                    if (Ext.getCmp('Koltiva.view.Traceability_new.Delivery.MainForm') == undefined) {
                                        MainFormBatch = Ext.create('Koltiva.view.Traceability_new.Delivery.MainForm', {
                                            viewVar: {
                                                OpsiDisplay: 'insert'
                                            }
                                        });
                                    } else {
                                        //destroy, create ulang
                                        Ext.getCmp('Koltiva.view.Traceability_new.Delivery.MainForm').destroy();
                                        MainFormBatch = Ext.create('Koltiva.view.Traceability_new.Delivery.MainForm', {
                                            viewVar: {
                                                OpsiDisplay: 'insert'
                                            }
                                        });
                                    }
                                }
                            }, 
                            {
                                xtype: 'button',
                                id: 'Koltiva.view.Traceability_new.Transaction.MainGridPengiriman-gridToolbar-BtnExport',
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

                                    // var param_string    = '?sid=';
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
                                        url: m_api+'/traceability_api/delivery/export_excel_delivery/'+param_string,
                                    
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
                                    var WinApplyFilter = Ext.create('Koltiva.view.Traceability_new.Delivery.WinApplyFilter', {
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
                                    Ext.getCmp('Koltiva.view.Traceability_new.Delivery.MainGrid-Grid').getStore().loadPage(1);
                                }
                            }]
                    }],
                columns: [{
                        text: '',
                        xtype: 'actioncolumn',
                        width: 30,
                        items: [{
                                icon: varjs.config.base_url + 'images/icons/new/action.png',
                                handler: function (grid, rowIndex, colIndex, item, e, record) {
                                    if(Ext.isDefined(Ext.getCmp('ContextMenuGridDelivery'))){
                                        Ext.getCmp('ContextMenuGridDelivery').destroy();
                                    }
                                    thisObj.ContextMenuGrid = Ext.create('Ext.menu.Menu', {
                                        cls: 'Sfr_ConMenu',
                                        id:"ContextMenuGridDelivery",
                                        items: [{
                                                icon: varjs.config.base_url + 'images/icons/new/view.png',
                                                text: lang('View'),
                                                id:'Koltiva.view.Traceability_new.Delivery.btnView',
                                                cls: 'Sfr_BtnConMenuWhite',
                                                handler: function () {
                                                    var sm = Ext.getCmp('Koltiva.view.Traceability_new.Delivery.MainGrid-Grid').getSelectionModel().getSelection()[0];
                                                    Ext.getCmp('Koltiva.view.Traceability_new.Delivery.MainGrid').destroy(); //destory current view
                            
                                                    var MainFormBatch = [];
                                                    //create object View untuk FormMainGrower
                                                    if (Ext.getCmp('Koltiva.view.Traceability_new.Delivery.MainForm') == undefined) {
                                                        MainFormBatch = Ext.create('Koltiva.view.Traceability_new.Delivery.MainForm', {
                                                            viewVar: {
                                                                OpsiDisplay: 'view',
                                                                DeliveryID: sm.get('DeliveryID'),
                                                                DeliveryStatusID: sm.get('DeliveryStatusID')
                                                            }
                                                        });
                                                    } else {
                                                        //destroy, create ulang
                                                        Ext.getCmp('Koltiva.view.Traceability_new.Delivery.MainForm').destroy();
                                                        MainFormBatch = Ext.create('Koltiva.view.Traceability_new.Delivery.MainForm', {
                                                            viewVar: {
                                                                OpsiDisplay: 'view',
                                                                DeliveryID: sm.get('DeliveryID'),
                                                                DeliveryStatusID: sm.get('DeliveryStatusID'),
                                                            }
                                                        });
                                                    }
                                                }
                                            }, 
                                            {
                                                icon: varjs.config.base_url + 'images/icons/new/update.png',
                                                id: 'Koltiva.view.Traceability_new.Delivery.MainGrid.ContextMenu-Update',
                                                text: lang('Update'),
                                                cls: 'Sfr_BtnConMenuWhite',
                                                hidden: m_act_update,
                                                handler: function () {
                                                    var sm = Ext.getCmp('Koltiva.view.Traceability_new.Delivery.MainGrid-Grid').getSelectionModel().getSelection()[0];
                            
                                                    Ext.getCmp('Koltiva.view.Traceability_new.Delivery.MainGrid').destroy(); //destory current view
                            
                                                    var MainFormBatch = [];
                                                    //create object View untuk FormMainGrower
                                                    if (Ext.getCmp('Koltiva.view.Traceability_new.Delivery.MainForm') == undefined) {
                                                        MainFormBatch = Ext.create('Koltiva.view.Traceability_new.Delivery.MainForm', {
                                                            viewVar: {
                                                                OpsiDisplay: 'update',
                                                                DeliveryID: sm.get('DeliveryID'),
                                                                DeliveryStatusID: sm.get('DeliveryStatusID'),
                                                            }
                                                        });
                                                    } else {
                                                        //destroy, create ulang
                                                        Ext.getCmp('Koltiva.view.Traceability_new.Delivery.MainForm').destroy();
                                                        MainFormBatch = Ext.create('Koltiva.view.Traceability_new.Delivery.MainForm', {
                                                            viewVar: {
                                                                OpsiDisplay: 'update',
                                                                DeliveryID: sm.get('DeliveryID'),
                                                                DeliveryStatusID: sm.get('DeliveryStatusID'),
                                                            }
                                                        });
                                                    }
                                                },
                                            },
                                            {
                                                icon: varjs.config.base_url + 'images/icons/new/document_link.png',
                                                id: 'Koltiva.view.Traceability_new.Delivery.MainGrid.ContextMenu-Detail',
                                                text: lang('Buying Detail'),
                                                cls: 'Sfr_BtnConMenuWhite',
                                                handler: function () {
                                                    var sm = Ext.getCmp('Koltiva.view.Traceability_new.Delivery.MainGrid-Grid').getSelectionModel().getSelection()[0];
                                                    console.log(sm);
                                                    Ext.getCmp('Koltiva.view.Traceability_new.Delivery.MainGrid').destroy(); //destory current view
                            
                                                    var MainFormBatch = [];
                                                    if (Ext.getCmp('Koltiva.view.Traceability_new.Delivery.MainGridTransaction') == undefined) {
                                                        MainFormBatch = Ext.create('Koltiva.view.Traceability_new.Delivery.MainGridTransaction', {
                                                            viewVar: {
                                                                DeliveryID : sm.raw.DeliveryID
                                                            }
                                                        });
                                                    } else {
                                                        MainFormBatch = Ext.create('Koltiva.view.Traceability_new.Delivery.MainGridTransaction', {
                                                            viewVar: {
                                                                DeliveryID : sm.raw.DeliveryID
                                                            }
                                                        });
                                                    }
                                                },
                                            },
                                            {
                                                icon: varjs.config.base_url + 'images/icons/silk/printer.png',
                                                text: lang('Delivery Order'),
                                                id: 'Koltiva.view.Traceability_new.Delivery.MainGrid.ContextMenu-DeliverOrder', 
                                                cls: 'Sfr_BtnConMenuWhite',
                                                handler: function() {
                                                    var sm = Ext.getCmp('Koltiva.view.Traceability_new.Delivery.MainGrid-Grid').getSelectionModel().getSelection()[0];

                                                    var DeliveryID = sm.raw.DeliveryID;
                                                    
                                                    var SID = sm.raw.SupplyChainID;
                                                    
                                                    preview_cetak_surat(m_api + '/traceability_api/web_pengiriman/cetak_suratdo/' + DeliveryID, + SID, '/');  
                                                }
                                            },
                                            {
                                                icon: varjs.config.base_url + 'images/icons/new/delete.png',
                                                id: 'Koltiva.view.Traceability_new.Delivery.MainGrid.ContextMenu-Delete',
                                                text: lang('Delete'),
                                                cls: 'Sfr_BtnConMenuWhite',
                                                //hidden: m_act_delete,
                                                hidden: true,
                                                handler: function () {
                                                    var sm = Ext.getCmp('Koltiva.view.Traceability_new.Delivery.MainGrid-Grid').getSelectionModel().getSelection()[0];
                                                    Ext.MessageBox.confirm('Message', lang('Do you want to delete this data ?'), function (btn) {
                                                        if (btn == 'yes') {
                            
                                                            Ext.Ajax.request({
                                                                waitMsg: 'Please Wait',
                                                                // url: m_api + '/traceability_api/processing/data_supplychain_batch',
                                                                method: 'DELETE',
                                                                params: {
                                                                    DeliveryID: sm.get('DeliveryID'),
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
                                            }
                                        ]
                                    });


                                    thisObj.ContextMenuGrid.showAt(e.getXY());
                                    
                                    if(record.get('Status') == 'Open'){
                                        Ext.getCmp('Koltiva.view.Traceability_new.Delivery.MainGrid.ContextMenu-Detail').hide();
                                    }
                                    
                                    if (record.get('Status') == 'Sent' || record.get('Status') == 'Delivered' ){
                                        Ext.getCmp('Koltiva.view.Traceability_new.Delivery.MainGrid.ContextMenu-Update').show();
                                        Ext.getCmp('Koltiva.view.Traceability_new.Delivery.MainGrid.ContextMenu-Detail').show();
                                    }

                                    if(record.get('Status') == 'Delivered') {
                                        Ext.getCmp('Koltiva.view.Traceability_new.Delivery.MainGrid.ContextMenu-DeliverOrder').hide();
                                    }
                                    
                                    if(record.get('Status') == 'Finish') {
                                        Ext.getCmp('Koltiva.view.Traceability_new.Delivery.MainGrid.ContextMenu-Update').hide();
                                    }

                                }
                        }],
                    }, {
                        text: 'No',
                        width: 45,
                        align:'center',
                        xtype: 'rownumberer'
                    },
                    {
                        text: lang('Delivery Date'),
                        dataIndex: 'DeliveryDate',
                        width: 100,
                    },
                    {
                        text: lang('External Code'),
                        dataIndex: 'ExternalCode',
                        flex:1,
                    },
                    {
                        text: lang('Delivery Number'),
                        dataIndex: 'DeliveryNumber',
                        flex:1,
                    },
                    {
                        text: lang('Date Created'),
                        dataIndex: 'DateCreated',
                        width: 100,
                    },
                    {
                        text: lang('Total Weight'),
                        dataIndex: 'SellingWeight',
                        width: 100,
                    },
                    {
                        text: lang('Arrival Estimation'),
                        dataIndex: 'ArrivalEstimation',
                        width: 100,
                    },
                     {
                        text: lang('Delivery Status'),
                        dataIndex: 'Status',
                        width: 100,
                    }]
            }];

        this.callParent(arguments);
    }
});

function getFilterLs() {
    var filters = {};

    var cof_griddelivery_params = JSON.parse(localStorage.getItem('cof_griddelivery_params'));
    if (cof_griddelivery_params != null) {
        filters.ArrFilter                       = cof_griddelivery_params.ArrFilter.join(',');
        filters.TextFilterDeliveryNumber        = cof_griddelivery_params.TextFilterDeliveryNumber;
        filters.TextFilterExernalCode           = cof_griddelivery_params.TextFilterExernalCode;
        // filters.TextFilterDestinationID = cof_griddelivery_params.TextFilterDestinationID;
        filters.TextFilterDeliveryStatusID      = cof_griddelivery_params.TextFilterDeliveryStatusID;
        filters.TextFilterStartDeliveryDate     = cof_griddelivery_params.TextFilterStartDeliveryDate;
        filters.TextFilterEndDeliveryDate       = cof_griddelivery_params.TextFilterEndDeliveryDate;
    } else {
        //reset params
        filters.ArrFilter                       = null;
        filters.TextFilterDeliveryNumber        = null;
        filters.TextFilterExernalCode           = null;
        // filters.TextFilterDestinationID         = null;
        filters.TextFilterDeliveryStatusID      = null;
        filters.TextFilterStartDeliveryDate     = null;
        filters.TextFilterEndDeliveryDate       = null;
    }

    return filters;
}
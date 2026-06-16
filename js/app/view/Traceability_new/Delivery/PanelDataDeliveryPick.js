Ext.define('Koltiva.view.Traceability_new.Delivery.PanelDataDeliveryPick', {
    extend: 'Ext.panel.Panel',
    id: 'Koltiva.view.Traceability_new.Delivery.PanelDataDeliveryPick',
    title: lang('Selling Pick'),
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

        thisObj.MainGrid = Ext.create('Koltiva.store.Traceability_new.Delivery.MainGridDataDeliveryDetail', {
            storeVar: {
                DeliveryID : thisObj.viewVar.DeliveryID
            }
        });

        thisObj.ContextMenu = Ext.create('Ext.menu.Menu', {
            cls: 'Sfr_ConMenu',
            items: [{
                icon: varjs.config.base_url + 'images/icons/new/delete.png',
                text: lang('Delete'),
                id:'Koltiva.view.Traceability_new.Delivery.PanelDataDeliveryPick-CtxDelete',
                cls: 'Sfr_BtnConMenuWhite',
                hidden: m_act_delete,
                //hidden : thisObj.delete,
                handler: function () {
                    var sm = Ext.getCmp('Koltiva.view.Traceability_new.Delivery.PanelDataDeliveryPick-MainGrid').getSelectionModel().getSelection()[0];
                    console.log(sm);
                    Ext.MessageBox.confirm('Message', lang('Do you want to delete this data ?'), function (btn) {
                        if (btn == 'yes') {
                            Ext.Ajax.request({
                                waitMsg: 'Please Wait',
                                url: m_api + '/traceability_api/delivery/del_transaction',
                                method: 'DELETE',
                                params: {
                                    DeliveryDetailID: sm.get('DeliveryDetailID'),
                                    Weight : sm.get('Weight'),
                                    SupplyBatchNumber : sm.get('SupplyBatchNumber')
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

                                                    Ext.getCmp('Koltiva.view.Traceability_new.Delivery.MainForm').destroy();
                                                    var MainForm = [];
                                                    if (Ext.getCmp('Koltiva.view.Traceability_new.Delivery.MainForm') == undefined) {
                                                        MainForm = Ext.create('Koltiva.view.Traceability_new.Delivery.MainForm', {
                                                            viewVar: {
                                                                OpsiDisplay: 'update',
                                                                DeliveryID: thisObj.viewVar.DeliveryID
                                                            }
                                                        });
                                                    } else {
                                                        Ext.getCmp('Koltiva.view.Traceability_new.Delivery.MainForm').destroy();
                                                        MainForm = Ext.create('Koltiva.view.Traceability_new.Delivery.MainForm', {
                                                            viewVar: {
                                                                OpsiDisplay: 'update',
                                                                DeliveryID: thisObj.viewVar.DeliveryID
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
            id: 'Koltiva.view.Traceability_new.Delivery.PanelDataDeliveryPick-MainGrid',
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
                    id: 'Koltiva.view.Traceability_new.Delivery.PanelDataDeliveryPick-BtnAdd',
                    handler: function () {

                        thisObj.AddValidation = true;
                        thisObj.MsgAddValidation = "";
                        thisObj.AddValidationBasicForm();
                        if(thisObj.AddValidation == true) {

                            thisObj.WinFormDataDeliveryPick = Ext.create('Koltiva.view.Traceability_new.Delivery.WinFormDataDeliveryPick', {
                                viewVar: {
                                    OpsiDisplay: 'insert',
                                    StoreGridMain: thisObj.MainGrid,
                                    // PalmoilTypeID: Ext.getCmp('Koltiva.view.Traceability_new.Delivery.MainForm-FormBasicData-PalmoilTypeID').getValue()
                                }
                            });

                            if (!thisObj.WinFormDataDeliveryPick.isVisible()) {
                                thisObj.WinFormDataDeliveryPick.center();
                                thisObj.WinFormDataDeliveryPick.show();
                            } else {
                                thisObj.WinFormDataDeliveryPick.close();
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
                    icon: varjs.config.base_url + 'images/icons/new/close.png',
                    text: lang('Close'),
                    hidden: m_act_add,
                    cls: 'Sfr_BtnGridRed',
                    overCls: 'Sfr_BtnGridRed-Hover',
                    id: 'Koltiva.view.Traceability_new.Delivery.PanelDataDeliveryPick-BtnClose',
                    handler: function () {

                        if(thisObj.viewVar.DeliveryID) {
                            
                            Ext.Ajax.request({
                                waitMsg: 'Please Wait',
                                url: m_api + '/traceability_api/delivery/close_delivery_pick',
                                method: 'POST',
                                params: {
                                    DeliveryID: thisObj.viewVar.DeliveryID
                                },
                                success: function (response, opts) {
                                    
                                    Ext.getCmp('Koltiva.view.Traceability_new.Delivery.PanelDataDeliveryPick-BtnAdd').hide();
                                    Ext.getCmp('Koltiva.view.Traceability_new.Delivery.PanelDataDeliveryPick-BtnClose').hide();
                                    
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
                                                            DeliveryID: thisObj.viewVar.DeliveryID
                                                        }
                                                    });
                                                } else {
                                                    Ext.getCmp('Koltiva.view.Traceability_new.Delivery.MainForm').destroy();
                                                    MainForm = Ext.create('Koltiva.view.Traceability_new.Delivery.MainForm', {
                                                        viewVar: {
                                                            OpsiDisplay: 'update',
                                                            DeliveryID: thisObj.viewVar.DeliveryID
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
                },
                {
                    xtype: 'button',
                    icon: varjs.config.base_url + 'images/icons/new/reload.png',
                    cls: 'Sfr_BtnGridBlue',
                    hidden:true,
                    overCls: 'Sfr_BtnGridBlue-Hover',
                    id: 'Koltiva.view.Traceability_new.Delivery.PanelDataDeliveryPick-reload',
                    handler: function () {
                        Ext.getCmp('Koltiva.view.Traceability_new.Delivery.PanelDataDeliveryPick-MainGrid').getStore().loadPage(1);
                    }
                }
                ]
            }],
            columns: [{
                text: ' ',
                xtype: 'actioncolumn',
                width: '10%',
                id: 'Koltiva.view.Traceability_new.Delivery.PanelDataDeliveryPick-ActionColumn',
                items: [{
                    icon: varjs.config.base_url + 'images/icons/new/action.png',
                    handler: function (grid, rowIndex, colIndex, item, e, record) {
                        thisObj.ContextMenu.showAt(e.getXY());

                        // if(thisObj.viewVar.DeliveryStatusID=='1'){
                        //     Ext.getCmp('Koltiva.view.Traceability_new.Delivery.PanelDataDeliveryPick-CtxDelete').show();
                        //     thisObj.delete = false;
                        // } else{
                        //     Ext.getCmp('Koltiva.view.Traceability_new.Delivery.PanelDataDeliveryPick-CtxDelete').hide();
                        //     thisObj.delete = true;
                        // }

                    }
                }]
            },{
                text: lang('Selling ID'),
                dataIndex: 'DeliveryID',
                flex:50
            },
            {
                text: lang('Batch ID'),
                dataIndex: 'SupplyBatchNumber',
                flex:50
            },
            {
                text: lang('Gross weight farmer'),
                dataIndex: 'Weight',
                flex:50,
            }]
        }];

        // thisObj.MainGrid.on('load', function(store, records){
        //     if (records.length > 0) {
        //         Ext.getCmp('Koltiva.view.Traceability_new.Delivery.MainForm-FormBasicData-DeliveryID').setReadOnly(true);
        //         Ext.getCmp('Koltiva.view.Traceability_new.Delivery.MainForm-FormBasicData-DeliveryID');
        //     } else {
        //         Ext.getCmp('Koltiva.view.Traceability_new.Delivery.MainForm-FormBasicData-DeliveryID').setReadOnly(false);
        //     }
        // }, this);

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
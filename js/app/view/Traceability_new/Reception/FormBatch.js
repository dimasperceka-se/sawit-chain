 Ext.define('Koltiva.view.Traceability_new.Reception.FormBatch' ,{
    extend: 'Ext.panel.Panel',
    id: 'Koltiva.view.Traceability_new.Reception.FormBatch', 
    viewVar: false,
    setViewVar: function(value){
        this.viewVar = value;
    },
    frame: false, 
    collapsible:false, 
    margin:'0 0 0 0',
    initComponent: function() {
        var thisObj = this;

        thisObj.MainGridDataDeliveryDetail = Ext.create('Koltiva.store.Traceability_new.Reception.MainGridDataDeliveryDetail', {
            storeVar: {
                DeliveryID : thisObj.viewVar.DeliveryID
            }
        });

        thisObj.MainGridDataDeliveryReceiving = Ext.create('Koltiva.store.Traceability_new.Reception.MainGridDataDeliveryReceiving', {
            storeVar: {
                SupplyTransID: thisObj.viewVar.SupplyTransID
            }
        });


        thisObj.ContextMenu = Ext.create('Ext.menu.Menu', {
            cls: 'Sfr_ConMenu',
            items: [{
                icon: varjs.config.base_url + 'images/icons/new/update.png',
                text: lang('Update'),
                scope: this,
                cls: 'Sfr_BtnConMenuWhite',
                id: 'Koltiva.view.Traceability_new.Reception.FormBatch.PanelDataDeliveryDetail.ContextMenu-update',
                // hidden: m_act_update,
                handler: function () {

                    var sm = Ext.getCmp('PanelDataDeliveryReceiving').getSelectionModel().getSelection()[0];
                    var WinFormDeliveryReceiving = Ext.create('Koltiva.view.Traceability_new.Reception.WinFormDeliveryReceiving');
                    WinFormDeliveryReceiving.setViewVar({
                        OpsiDisplay: 'update',
                        StoreGridMain: thisObj.MainGridDataDeliveryReceiving,
                        SupplyTransID: Ext.getCmp('Koltiva.view.Traceability_new.Reception.FormReception-form-SupplyTransID').getValue(),
                        TransDetailID: sm.get('TransDetailID')
                    });
                    if (!WinFormDeliveryReceiving.isVisible()) {
                        WinFormDeliveryReceiving.center();
                        WinFormDeliveryReceiving.show();
                    } else {
                        WinFormDeliveryReceiving.close();
                    }
                }
            },{
                icon: varjs.config.base_url + 'images/icons/new/delete.png',
                text: lang('Delete'),
                cls: 'Sfr_BtnConMenuWhite',
                id: 'Koltiva.view.Traceability_new.Reception.FormBatch.PanelDataDeliveryDetail.ContextMenu-delete',
                // hidden: m_act_delete,
                handler: function () {
                    var sm = Ext.getCmp('PanelDataDeliveryReceiving').getSelectionModel().getSelection()[0];

                    Ext.MessageBox.confirm('Message', lang('Do you want to delete this data ?'), function (btn) {
                        if (btn == 'yes') {
                            Ext.Ajax.request({
                                waitMsg: 'Please Wait',
                                url: m_api + '/traceability_api/reception/data_delivery_receiving',
                                method: 'DELETE',
                                params: {
                                    SupplyTransID: thisObj.viewVar.SupplyTransID,
                                    TransDetailID: sm.get('TransDetailID')
                                },
                                success: function (response, opts) {
                                    Ext.MessageBox.show({
                                        title: 'Information',
                                        msg: lang('Data deleted'),
                                        buttons: Ext.MessageBox.OK,
                                        animateTarget: 'mb9',
                                        icon: 'ext-mb-success'
                                    });

                                    var r = Ext.decode(response.responseText);

                                    thisObj.MainGridDataDeliveryReceiving.load({
                                        params: {
                                            SupplyTransID: r.SupplyTransID
                                        }
                                    });

                                    Ext.getCmp('Koltiva.view.Traceability_new.Reception.FormReception-form-SupplyTransID').setValue(r.SupplyTransID)
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
            layout: 'column',
            border: false,
            items:[{
                columnWidth: 1,
                layout:'form',
                style: 'padding: 0 10px 0 10px;margin-top:10px;',
                items:[{
                    xtype: 'panel',
                    title: lang('Storage Selling Data'),
                    items: [{
                        layout: 'form',
                        items: [{
                            // columnWidth: 0.5,
                            layout: 'form',
                            padding:5,
                            items:[{
                                xtype: 'hiddenfield',
                                labelWidth: 150,
                                hidden: true,
                                id: 'Koltiva.view.Traceability_new.Reception.FormBatch-form-DeliveryNumber',
                                name: 'SupplyBatchNumber'
                            }, 
                            {
                                xtype: 'hiddenfield',
                                labelWidth: 150,
                                readOnly: true,
                                id: 'Koltiva.view.Traceability_new.Reception.FormBatch-form-DeliveryID',
                                name: 'DeliveryID'
                            }, 
                            {
                                xtype: 'textfield',
                                labelWidth: 150,
                                readOnly: true,
                                fieldLabel: lang('Selling Date'),
                                id: 'Koltiva.view.Traceability_new.Reception.FormBatch-form-DeliveryDate',
                                name: 'DeliveryDate'
                            }, {
                                xtype: 'textfield',
                                labelWidth: 150,
                                readOnly: true,
                                fieldLabel: lang('Ext Code'),
                                id: 'Koltiva.view.Traceability_new.Reception.FormBatch-form-ExtCode',
                                name: 'ExtCode'
                            },
                            {
                                xtype: 'textfield',
                                labelWidth: 150,
                                readOnly: true,
                                fieldLabel: lang('Arrival Estimation'),
                                id: 'Koltiva.view.Traceability_new.Reception.FormBatch-form-ArrivalEstimation',
                                name: 'ArrivalEstimation'
                            },
                             {
                                xtype: 'textfield',
                                labelWidth: 150,
                                readOnly: true,
                                fieldLabel: lang('Collector'),
                                id: 'Koltiva.view.Traceability_new.Reception.FormBatch-form-BatchName',
                                name: 'BatchName'
                            },{
                                xtype: 'hiddenfield',
                                labelWidth: 150,
                                readOnly: true,
                                fieldLabel: lang('Weight'),
                                id: 'Koltiva.view.Traceability_new.Reception.FormBatch-form-Weight',
                                name: 'Weight'
                            },{
                                xtype: 'hiddenfield',
                                labelWidth: 150,
                                readOnly: true,
                                fieldLabel: lang('Weight'),
                                id: 'Koltiva.view.Traceability_new.Reception.FormBatch-form-TransNumber',
                                name: 'TransNumber'
                            }]
                        }]
                    }]
                }]
            },{
                columnWidth: 1,
                layout:'form',
                style: 'padding: 0 10px 0 10px;margin-top:10px;',
                items:[{
                    xtype: 'panel',
                    title: lang('Data Selling'),
                    id : 'PanelDriver',
                    items: [{
                                layout: 'column',
                                items: [{
                                    columnWidth: 0.5,
                                    layout: 'form',
                                    padding:5,
                                    items:[{
                                        xtype: 'textfield',
                                        readOnly: true,
                                        labelWidth: 150,
                                        fieldLabel: lang('Driver Name'),
                                        forfield: 'DestDriver',
                                        id: 'FormBatch-form-DestDriver',
                                        name: 'DestDriver'
                                    }]
                                }, {
                                    columnWidth: 0.5,
                                    layout: 'form',
                                    padding:5,
                                    items:[ {
                                        xtype: 'textfield',
                                        readOnly: true,
                                        labelWidth: 150,
                                        fieldLabel: lang('license Plate'),
                                        id: 'FormBatch-form-DestTransportNumber',
                                        name: 'DestTransportNumber'
                                    }, {
                                        xtype: 'textfield',
                                        readOnly: true,
                                        labelWidth: 150,
                                        fieldLabel: lang('Transportation Type'),
                                        id: 'FormBatch-form-TransportationType',
                                        name: 'DestTransportID'
                                    }]
                                }]
                        }]
                }]
            },{
                columnWidth: 1,
                layout:'form',
                style: 'padding: 0 10px 0 10px;margin-top:10px;',
                items:[{
                    xtype: 'grid',
                    title: lang('Data Selling Detail'),
                    id : 'PanelDataDeliveryDetail',
                    cls: 'Sfr_GridNew',
                    loadMask: true,
                    height: 300,
                    selType: 'rowmodel',
                    style: 'border:1px solid #CCC;',
                    store: thisObj.MainGridDataDeliveryDetail,
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
                        items: [
                            // {
                            //     xtype: 'button',
                            //     icon: varjs.config.base_url + 'images/icons/new/reload.png',
                            //     cls: 'Sfr_BtnGridBlue',
                            //     overCls: 'Sfr_BtnGridBlue-Hover',
                            //     handler: function () {
                            //         Ext.getCmp('PanelDataDeliveryDetail').getStore().loadPage(1);
                            //     }
                            // }
                        ]
                    }],
                    columns: [
                        {
                            text: lang('Selling Detail ID'),
                            dataIndex: 'DeliveryDetailID',
                            flex:50,
                            hidden:true
                        },
                        {
                            text: lang('Selling ID'),
                            dataIndex: 'DeliveryID',
                            flex:50
                        },
                        {
                            text: lang('Selling Number'),
                            dataIndex: 'SupplyBatchNumber',
                            flex:50
                        },
                        {
                            text: lang('Total Weight'),
                            dataIndex: 'Weight',
                            flex:50,
                        }
                    ]
                }]
            },{
                columnWidth: 1,
                layout:'form',
                style: 'padding: 0 10px 0 10px;margin-top:10px;',
                items:[{
                    xtype: 'grid',
                    title: lang('Data Selling Receiving'),
                    id : 'PanelDataDeliveryReceiving',
                    cls: 'Sfr_GridNew',
                    loadMask: true,
                    height: 300,
                    selType: 'rowmodel',
                    style: 'border:1px solid #CCC;',
                    store: thisObj.MainGridDataDeliveryReceiving,
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
                        items: [
                            {
                                id: 'Koltiva.view.Traceability_new.Reception.FormBatch-form-ButtonAdd',
                                xtype: 'button',
                                icon: varjs.config.base_url + 'images/icons/new/add.png',
                                text: lang('Add'),
                                cls: 'Sfr_BtnGridGreen',

                                overCls: 'Sfr_BtnGridGreen-Hover',
                                handler: function () {

                                    let WinFormDeliveryReceiving = Ext.create('Koltiva.view.Traceability_new.Reception.WinFormDeliveryReceiving');
                                    WinFormDeliveryReceiving.setViewVar({
                                        OpsiDisplay: 'insert',
                                        StoreGridMain: thisObj.MainGridDataDeliveryReceiving,
                                        SupplyTransID: Ext.getCmp('Koltiva.view.Traceability_new.Reception.FormReception-form-SupplyTransID').getValue(),
                                        MemberID: thisObj.viewVar.DeliveryID
                                    });
                                    if (!WinFormDeliveryReceiving.isVisible()) {
                                        WinFormDeliveryReceiving.center();
                                        WinFormDeliveryReceiving.show();
                                    } else {
                                        WinFormDeliveryReceiving.close();
                                    }
                                }
                            }
                        ]
                    }],
                    columns: [
                        {
                            text: lang('Selling Detail ID'),
                            dataIndex: 'DeliveryDetailID',
                            flex:50
                        },
                        {
                            text: lang('Detail Number'),
                            dataIndex: 'DetailNumber',
                            flex:50
                        },
                        {
                            text: lang('Detail SupplyChain ID'),
                            dataIndex: 'SupplyTransID',
                            flex:50
                        },
                        {
                            text: lang('Buying Detail ID'),
                            dataIndex: 'TransDetailID',
                            flex:50
                        },
                        {
                            text: lang('Weight'),
                            dataIndex: 'Weight',
                            flex:50
                        },
                        {
                            text: lang('Total capacity '),
                            dataIndex: 'TotalCapacity',
                            flex:50
                        },
                        {
                            text: lang('Status'),
                            dataIndex: 'statusWeight',
                            flex:50
                        }
                    ]
                }]
            }],
            listeners:{
                afterrender: function(c){
                    var SupplyTransID = thisObj.viewVar.SupplyTransID;
                    var DeliveryID = thisObj.viewVar.DeliveryID;
                    
                    Ext.Ajax.request({
                        url: m_api + '/traceability_api/reception/fetch_batch_data',
                        method: 'GET',
                        params: {
                            SupplyTransID: SupplyTransID, 
                            DeliveryID: DeliveryID
                        },
                        success: function(fp, o){
                            var r = Ext.decode(fp.responseText);
                            // console.log(r);
                            let DestTransportName;

                            if(r.DestDriver==null) r.DestDriver='';
                            if(r.DestTransportNumber==null) r.DestTransportNumber='';
                            if(r.DestTransportID==null) r.DestTransportID='';
                            Ext.getCmp('Koltiva.view.Traceability_new.Reception.FormBatch-form-DeliveryID').setValue(r.DeliveryID);
                            Ext.getCmp('Koltiva.view.Traceability_new.Reception.FormBatch-form-DeliveryDate').setValue(r.DeliveryDate);
                            Ext.getCmp('Koltiva.view.Traceability_new.Reception.FormBatch-form-DeliveryNumber').setValue(r.DeliveryNumber);
                            Ext.getCmp('Koltiva.view.Traceability_new.Reception.FormBatch-form-ExtCode').setValue(r.ExternalCode);
                            Ext.getCmp('Koltiva.view.Traceability_new.Reception.FormBatch-form-BatchName').setValue(r.Collector);
                            Ext.getCmp('Koltiva.view.Traceability_new.Reception.FormBatch-form-ArrivalEstimation').setValue(r.ArrivalEstimation);
                            Ext.getCmp('Koltiva.view.Traceability_new.Reception.FormBatch-form-Weight').setValue(r.Weight);

                            Ext.getCmp('FormBatch-form-DestDriver').setValue(r.DestDriver);
                            Ext.getCmp('FormBatch-form-DestTransportNumber').setValue(r.DestTransportNumber);

                            Ext.getCmp('Koltiva.view.Traceability_new.Reception.FormBatch-form-TransNumber').setValue(r.TransNumber);

                            if (r.DestTransportID == "1") {
                                DestTransportName = "Mobil";
                            } else if (r.DestTransportID == "2") {
                                DestTransportName = "Truck Container";
                            } else if (r.DestTransportID == "4") {
                                DestTransportName = "Bak Truk";
                            } else if(r.DestTransportID == "5"){
                                DestTransportName = "Pickup";
                            } else if(r.DestTransportID == "6"){
                                DestTransportName = "Bak Terbuka";
                            } else if(r.DestTransportID == "7"){
                                DestTransportName = "Motor";
                            } else {
                                DestTransportName = "-";
                            }

                            if(r.DeliveryStatusID == '4'){
                                Ext.getCmp('Koltiva.view.Traceability_new.Reception.FormBatch-form-ButtonAdd').hide();
                            }

                            Ext.getCmp('FormBatch-form-TransportationType').setValue(DestTransportName);
                            
                        }
                     });
                    
                    
                }
            }
        }];

        thisObj.MainGridDataDeliveryReceiving.on('load', function(store, records){

            Ext.getCmp('Koltiva.view.Traceability_new.Reception.FormReception-form-TotalCapacity').setValue(parseFloat(store.sum('TotalCapacity')));

            if(records.length > 0){
                // Ext.getCmp('Koltiva.view.Traceability_new.Reception.FormReception-form-PaymentPaid').setValue(records[0].data.PaymentPaid);
                // Ext.getCmp('Koltiva.view.Traceability_new.Reception.FormReception-form-PaymentStatusID').setValue(records[0].data.PaymentStatusID);
                // Ext.getCmp('Koltiva.view.Traceability_new.Reception.FormReception-form-PaymentMethodID').setValue(records[0].data.PaymentMethodID);
                // Ext.getCmp('Koltiva.view.Traceability_new.Reception.FormReception-form-BankCode').setValue(records[0].data.BankCode);
                // Ext.getCmp('Koltiva.view.Traceability_new.Reception.FormReception-form-BankName').setValue(records[0].data.BankName);
                // Ext.getCmp('Koltiva.view.Traceability_new.Reception.FormReception-form-AccountNumber').setValue(records[0].data.AccountNumber);
                // Ext.getCmp('Koltiva.view.Traceability_new.Reception.FormReception-form-AccountName').setValue(records[0].data.AccountName);
            }

        }, this);


		this.callParent(arguments);
    }
});
Ext.define('Koltiva.view.Traceability_new.Delivery.WinFormDataDeliveryDetail', {
    extend: 'Ext.window.Window',
    id: 'Koltiva.view.Traceability_new.Delivery.WinFormDataDeliveryDetail',
    cls: 'Sfr_LayoutPopupWindows',
    title: lang('Form Data Selling'),
    closable: true,
    modal: true,
    closeAction: 'destroy',
    width: '30%',
    overflowY: 'auto',
    viewVar: false,
    setViewVar: function (value) {
        this.viewVar = value;
    },
    listeners: {
        afterRender: function () {
            var thisObj = this;

            if (thisObj.viewVar.OpsiDisplay == 'update') {
                //form reset
                var FormNya = Ext.getCmp('Koltiva.view.Traceability_new.Delivery.WinFormDataDeliveryDetail-Form');

                FormNya.getForm().load({
                    url: m_api + '/traceability_api/delivery/data_supplychain_batch_form_open',
                    method: 'GET',
                    params: {
                        SupplyBatchID: thisObj.viewVar.SupplyBatchID
                    },
                    success: function (form, action) {
                        var r = Ext.decode(action.response.responseText);
                        
                        // let totalPayment = Ext.getCmp('Koltiva.view.Traceability_new.Delivery.WinFormDataDeliveryDetail-Form-SupplyDestOrgName').getValue()

                        // if (Ext.getCmp('Koltiva.view.Traceability_new.Delivery.WinFormDataDeliveryDetail-Form-SupplyDestOrgName').getValue() === null) {
                        //     Ext.getCmp('Koltiva.view.Traceability_new.Delivery.WinFormDataDeliveryDetail-Form-SupplyDestOrgName').setValue(SupplyDestOrgName)
                        // }
                    },
                    failure: function (form, action) {
                        Ext.MessageBox.show({
                            title: lang('Failed'),
                            msg: lang('Failed to retrieve data'),
                            buttons: Ext.MessageBox.OK,
                            animateTarget: 'mb9',
                            icon: 'ext-mb-error'
                        });
                    }
                });
            }

        }
    },
    initComponent: function () {

        var thisObj = this;
        var labelWidth = 150;

        //Store ==================================== (Begin)
        

        //Store ==================================== (End)
    
        //items -------------------------------------------------------------- (begin)
        thisObj.items = [{
            xtype: 'form',
            id: 'Koltiva.view.Traceability_new.Delivery.WinFormDataDeliveryDetail-Form',
            padding: '5 15 5 8',
            items: [{
                layout: 'column',
                border: false,
                items: [{
                    columnWidth: 1,
                    layout: 'form',
                    style: 'padding-bottom:10px;',
                    items: [{
                        xtype: 'hiddenfield',
                        id: 'Koltiva.view.Traceability_new.Delivery.WinFormDataDeliveryDetail-Form-SupplyBatchID',
                        name: 'Koltiva.view.Traceability_new.Delivery.WinFormDataDeliveryDetail-Form-SupplyBatchID'
                    },{
                        xtype: 'textfield',
                        id: 'Koltiva.view.Traceability_new.Delivery.WinFormDataDeliveryDetail-Form-SupplyBatchNumber',
                        name: 'Koltiva.view.Traceability_new.Delivery.WinFormDataDeliveryDetail-Form-SupplyBatchNumber',
                        labelWidth: labelWidth,
                        fieldLabel: lang('Number'),
                        readOnly : true
                    },
                    {
                        xtype: 'textfield',
                        id: 'Koltiva.view.Traceability_new.Delivery.WinFormDataDeliveryDetail-Form-RemainingWeight',
                        name: 'Koltiva.view.Traceability_new.Delivery.WinFormDataDeliveryDetail-Form-RemainingWeight',
                        labelWidth: labelWidth,
                        fieldLabel: lang('Remaining'),
                        readOnly : true
                    },
                    {
                        xtype: 'numberfield',
                        id: 'Koltiva.view.Traceability_new.Delivery.WinFormDataDeliveryDetail-Form-Weight',
                        name: 'Koltiva.view.Traceability_new.Delivery.WinFormDataDeliveryDetail-Form-Weight',
                        fieldLabel: lang('Selling Weight'),
                        labelWidth: labelWidth,
                        allowBlank: false,
                        baseCls: 'Sfr_FormInputMandatory',
                        minValue:0,
                        listeners: {
                            change : function(record){
                                var Remaining = Ext.getCmp('Koltiva.view.Traceability_new.Delivery.WinFormDataDeliveryDetail-Form-RemainingWeight').getValue();
                                var weight = record.getValue();
                                if(parseFloat(weight)>parseFloat(Remaining)){
                                    Ext.getCmp('Koltiva.view.Traceability_new.Delivery.WinFormDataDeliveryDetail-Form-RemainingWeight').getValue();
                                    Ext.MessageBox.show({
                                        title: 'Warning',
                                        msg: 'Weight should not more than remaining!',
                                        buttons: Ext.MessageBox.OK,
                                        animateTarget: 'mb9',
                                        icon: 'ext-mb-error'
                                    });
                                }
                            }
                        }
                    }
                ]
                }]
            }]
        }];
        //items -------------------------------------------------------------- (end)


        thisObj.buttons = [{
            icon: varjs.config.base_url + 'images/icons/new/save.png',
            cls: 'Sfr_BtnFormBlue',
            overCls: 'Sfr_BtnFormBlue-Hover',
            text: lang('Save'),
            id: 'Koltiva.view.Traceability_new.Delivery.WinFormDataDeliveryDetail-Form-BtnSave',
            handler: function () {

                var Formnya = Ext.getCmp('Koltiva.view.Traceability_new.Delivery.MainForm-FormBasicData').getForm();
                var FormnyaWinFormDataDeliveryDetail = Ext.getCmp('Koltiva.view.Traceability_new.Delivery.WinFormDataDeliveryDetail-Form').getForm();

                if (FormnyaWinFormDataDeliveryDetail.isValid() === false) {
                    Ext.MessageBox.show({
                        title: lang('Attention'),
                        msg: lang('Form not complete yet'),
                        buttons: Ext.MessageBox.OK,
                        animateTarget: 'mb9',
                        icon: 'ext-mb-info'
                    });

                    return;
                }

                if (Formnya.isValid()) {
                    thisObj.AddValidation = true;
                    thisObj.MsgAddValidation = "";
                    thisObj.AddValidationBasicForm();
                    if(thisObj.AddValidation == true) {

                        Formnya.submit({
                            url: m_api + '/traceability_api/delivery/data_supplychain_delivery_detail',
                            method: 'POST',
                            waitMsg: lang('Saving data'),
                            params: {
                                SupplyBatchID : Ext.getCmp('Koltiva.view.Traceability_new.Delivery.WinFormDataDeliveryDetail-Form-SupplyBatchID').getValue(),
                                Weight: Ext.getCmp('Koltiva.view.Traceability_new.Delivery.WinFormDataDeliveryDetail-Form-Weight').getValue(),
                                SupplyBatchNumber: Ext.getCmp('Koltiva.view.Traceability_new.Delivery.WinFormDataDeliveryDetail-Form-SupplyBatchNumber').getValue()
                            },
                            success: function (response, opts) {
                                
                                Ext.MessageBox.show({
                                    title: 'Information',
                                    msg: lang('Data saved'),
                                    buttons: Ext.MessageBox.OK,
                                    animateTarget: 'mb9',
                                    icon: 'ext-mb-success',
                                    fn: function (btn) {
                                        if (btn == 'ok') {

                                            thisObj.close();
                                            Ext.getCmp('Koltiva.view.Traceability_new.Delivery.WinFormDataDeliveryPick').close();

                                            Ext.getCmp('Koltiva.view.Traceability_new.Delivery.MainForm').destroy();
                                            var MainForm = [];
                                            if (Ext.getCmp('Koltiva.view.Traceability_new.Delivery.MainForm') == undefined) {
                                                MainForm = Ext.create('Koltiva.view.Traceability_new.Delivery.MainForm', {
                                                    viewVar: {
                                                        OpsiDisplay: 'update',
                                                        DeliveryID: opts.result.DeliveryID
                                                    }
                                                });
                                            } else {
                                                Ext.getCmp('Koltiva.view.Traceability_new.Delivery.MainForm').destroy();
                                                MainForm = Ext.create('Koltiva.view.Traceability_new.Delivery.MainForm', {
                                                    viewVar: {
                                                        OpsiDisplay: 'update',
                                                        DeliveryID: opts.result.DeliveryID
                                                    }
                                                });
                                            }
                                        }
                                    }
                                });
                            },
                            failure: function (rp, o) {
                                try {
                                    var r = Ext.decode(o.response.responseText);
                                    Ext.MessageBox.show({
                                        title: lang('Error'),
                                        msg: r.message,
                                        buttons: Ext.MessageBox.OK,
                                        animateTarget: 'mb9',
                                        icon: 'ext-mb-error',
                                        fn: function (btn) {
                                            if (btn == 'ok') {

                                                thisObj.close();
                                                Ext.getCmp('Koltiva.view.Traceability_new.Delivery.WinFormDataDeliveryPick').close();

                                                Ext.getCmp('Koltiva.view.Traceability_new.Delivery.MainForm').destroy();
                                                var MainForm = [];
                                                if (Ext.getCmp('Koltiva.view.Traceability_new.Delivery.MainForm') == undefined) {
                                                    MainForm = Ext.create('Koltiva.view.Traceability_new.Delivery.MainForm', {
                                                        viewVar: {
                                                            OpsiDisplay: 'update',
                                                            DeliveryID: r.DeliveryID
                                                        }
                                                    });
                                                } else {
                                                    Ext.getCmp('Koltiva.view.Traceability_new.Delivery.MainForm').destroy();
                                                    MainForm = Ext.create('Koltiva.view.Traceability_new.Delivery.MainForm', {
                                                        viewVar: {
                                                            OpsiDisplay: 'update',
                                                            DeliveryID: r.DeliveryID
                                                        }
                                                    });
                                                }
                                            }
                                        }
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
                            msg: thisObj.MsgAddValidation,
                            buttons: Ext.MessageBox.OK,
                            animateTarget: 'mb9',
                            icon: 'ext-mb-info'
                        });
                    }
                } else {
                    Ext.MessageBox.show({
                        title: lang('Attention'),
                        msg: lang('Form not complete yet'),
                        buttons: Ext.MessageBox.OK,
                        animateTarget: 'mb9',
                        icon: 'ext-mb-info'
                    });
                }
            }
        },{
            icon: varjs.config.base_url + 'images/icons/new/close.png',
            text: lang('Close'),
            cls: 'Sfr_BtnFormGrey',
            overCls: 'Sfr_BtnFormGrey-Hover',
            handler: function () {
                thisObj.close();
            }
        }];

        this.callParent(arguments);
    },
    AddValidationBasicForm: function() {
        var thisObj = this;
        var ArrMsg = [];
        thisObj.AddValidation = true;

        // var remaining       = Ext.getCmp('Koltiva.view.Traceability_new.Delivery.WinFormDataDeliveryDetail-Form-RemainingWeight').getValue();
        var delivery_weight = Ext.getCmp('Koltiva.view.Traceability_new.Delivery.WinFormDataDeliveryDetail-Form-Weight').getValue();

        // if(parseInt(delivery_weight) > parseInt(remaining)) {
        //     thisObj.AddValidation = false;
        //     ArrMsg.push(lang('Exceed the number remaining'));
        // }

        if(parseInt(delivery_weight) == 0) {
            thisObj.AddValidation = false;
            ArrMsg.push(lang('Should not be 0'));
        }

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
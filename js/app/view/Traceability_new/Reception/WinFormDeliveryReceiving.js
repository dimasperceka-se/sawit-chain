Ext.define('Koltiva.view.Traceability_new.Reception.WinFormDeliveryReceiving', {
    extend: 'Ext.window.Window',
    id: 'Koltiva.view.Traceability_new.Reception.WinFormDeliveryReceiving',
    cls: 'Sfr_LayoutPopupWindows',
    title: lang('Data Selling Receiving'),
    closable: true,
    modal: true,
    closeAction: 'destroy',
    width: '35%',
    height: 400,
    overflowY: 'auto',
    viewVar: false,
    setViewVar: function (value) {
        this.viewVar = value;
    },
    initComponent: function () {
        var thisObj = this;
        var labelWidth = 105;

        let setWeightOriginal = 0;
        let setValueLabel = ""; 

        //items -------------------------------------------------------------- (begin)
        thisObj.items = [{
            xtype: 'form',
            id: 'Koltiva.view.Traceability_new.Reception.WinFormDeliveryReceiving-Form',
            padding: '1 15 5 8',
            items: [{
                layout: 'column',
                border: false,
                items: [{
                    columnWidth: 1,
                    layout: 'form',
                    style: 'padding-bottom:10px;',
                    items: [{
                        xtype: 'hiddenfield',
                        id: 'Koltiva.view.Traceability_new.Reception.WinFormDeliveryReceiving-Form-TransDetailID',
                        name: 'Koltiva.view.Traceability_new.Reception.WinFormDeliveryReceiving-Form-TransDetailID',
                        listeners: {
                            afterrender: function(c){
                                let DeliveryID     = Ext.getCmp("Koltiva.view.Traceability_new.Reception.FormBatch-form-DeliveryID").getValue();
                                
                                Ext.Ajax.request({
                                    url: m_api + '/traceability_api/reception/fetch_batch_data',
                                    method: 'GET',
                                    params: {
                                        DeliveryID: DeliveryID
                                    },
                                    success: function(fp, o){
                                    var r = Ext.decode(fp.responseText);
                                        
                                    Ext.getCmp('Koltiva.view.Traceability_new.Reception.WinFormDeliveryReceiving-Form-Weight').setValue(r.Weight);
                            
                                    }
                                 });
                            }
                        },
                    },
                    {
                        xtype: 'textfield',
                        id: 'Koltiva.view.Traceability_new.Reception.WinFormDeliveryReceiving-Form-DetailNumber',
                        name: 'Koltiva.view.Traceability_new.Reception.WinFormDeliveryReceiving-Form-DetailNumber',
                        fieldLabel: lang('Detail Number'),
                        labelWidth: labelWidth,
                        allowBlank: false,
                        baseCls: 'Sfr_FormInputMandatory',
                    },
                    {
                        xtype: 'numberfield',
                        id: 'Koltiva.view.Traceability_new.Reception.WinFormDeliveryReceiving-Form-Weight',
                        name: 'Koltiva.view.Traceability_new.Reception.WinFormDeliveryReceiving-Form-Weight',
                        fieldLabel: lang('Weight'),
                        labelWidth: labelWidth,
                        allowBlank: false,
                        readOnly: true,
                        baseCls: 'Sfr_FormInputMandatory',
                        minValue: 0,
                        listeners : { 
                            change: function(record){
                                let value = parseFloat(record.getValue());

                                if (!isNaN(value)) {
                                    if (value != 0) {
                                        if (parseFloat(setWeightOriginal) > 0) {
                                            if (parseFloat(value) != parseFloat(setWeightOriginal)) {
                                                Ext.getCmp("Koltiva.view.Traceability_new.Reception.WinFormDeliveryReceiving-Form-labelCheckWeight").hide();
                                            } else {
                                                Ext.getCmp("Koltiva.view.Traceability_new.Reception.WinFormDeliveryReceiving-Form-labelCheckWeight").show();
                                            }
                                        }
                                    } else {
                                        Ext.getCmp("Koltiva.view.Traceability_new.Reception.WinFormDeliveryReceiving-Form-labelCheckWeight").hide();
                                    }
                                } else {
                                    Ext.getCmp("Koltiva.view.Traceability_new.Reception.WinFormDeliveryReceiving-Form-labelCheckWeight").hide();
                                }
                            }                       
                        }
                    },
                    {
                        xtype: 'label',
                        id: 'Koltiva.view.Traceability_new.Reception.WinFormDeliveryReceiving-Form-labelCheckWeight', 
                        margin:0, 
                        padding:0,
                        hidden:true
                    },
                    {
                        xtype: 'numberfield',
                        id: 'Koltiva.view.Traceability_new.Reception.WinFormDeliveryReceiving-Form-TotalCapacity',
                        name: 'Koltiva.view.Traceability_new.Reception.WinFormDeliveryReceiving-Form-TotalCapacity',
                        fieldLabel: lang('Total Capacity'),
                        labelWidth: labelWidth,
                        allowBlank: false,
                        baseCls: 'Sfr_FormInputMandatory',
                    },
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
            id: 'Koltiva.view.Traceability_new.Reception.WinFormDeliveryReceiving-Form-BtnSave',
            handler: function () {

                let TotalWeight     = Ext.getCmp("Koltiva.view.Traceability_new.Reception.FormBatch-form-Weight").getValue();
                let Weight          = Ext.getCmp("Koltiva.view.Traceability_new.Reception.WinFormDeliveryReceiving-Form-Weight").getValue();

                let TotalCapacity = Ext.getCmp("Koltiva.view.Traceability_new.Reception.WinFormDeliveryReceiving-Form-TotalCapacity").getValue();
                console.log(TotalCapacity);
                if (isNaN(parseFloat(TotalCapacity)) || parseFloat(TotalCapacity) == 0) {
                    Ext.MessageBox.show({
                        title: lang('Warning'),
                        msg: lang("Please input weight correctly"),
                        buttons: Ext.MessageBox.OK,
                        animateTarget: 'mb9',
                        icon: 'ext-mb-error'
                    });
                    return;
                } else {
                    
                    let TotalWeight     = Ext.getCmp("Koltiva.view.Traceability_new.Reception.FormBatch-form-Weight").getValue();
                    let Weight          = Ext.getCmp("Koltiva.view.Traceability_new.Reception.WinFormDeliveryReceiving-Form-Weight").getValue();

                    let TotalCapacity = Ext.getCmp("Koltiva.view.Traceability_new.Reception.WinFormDeliveryReceiving-Form-TotalCapacity").getValue();
                    
                    let Persen20        = parseFloat(TotalCapacity) - parseFloat((20/100) * TotalCapacity);
					let Persen20plus    = parseFloat(TotalCapacity) + parseFloat((20/100) * TotalCapacity);
                
                    let message 

                    var Formnya = Ext.getCmp('Koltiva.view.Traceability_new.Reception.WinFormDeliveryReceiving-Form').getForm();

                    if (Formnya.isValid()) {
                        if(parseFloat(Weight) < parseFloat(Persen20)){
                        
                            message = lang("Total capacity couldn't more than 20% Total Weight estimation");
                            
                            if (Ext.getCmp("Koltiva.view.Traceability_new.Reception.WinFormDeliveryReceiving-Form-labelCheckWeight").isVisible() == false) {
                                Ext.MessageBox.show({
                                    title: 'Warning',
                                    msg: message + '<br/>' + lang('Are you sure to continue?'),
                                    buttons: Ext.MessageBox.OKCANCEL,
                                    icon: Ext.MessageBox.WARNING,
                                    fn: function(btn){
                                        if (btn == 'ok'){
                                            Ext.getCmp("Koltiva.view.Traceability_new.Reception.WinFormDeliveryReceiving-Form-labelCheckWeight").show();

                                            setWeightOriginal = parseFloat(Weight);
                                            setValueLabel     = lang('More than 20%');

                                            Ext.getCmp("Koltiva.view.Traceability_new.Reception.WinFormDeliveryReceiving-Form-labelCheckWeight").update(`<div style="margin-left:110px;color:#ED2F0D;">${lang(setValueLabel)}</div>`);

                                            return;
                                        } else {
                                            return;
                                        }
                                    }
                                });
                            } else {
                                Formnya.submit({
                                    url: m_api + '/traceability_api/reception/data_receiving_input',
                                    method: 'POST',
                                    waitMsg: lang('Saving data'),
                                    params: {
                                        OpsiDisplay: thisObj.viewVar.OpsiDisplay,
                                        SupplyTransID: thisObj.viewVar.SupplyTransID,
                                        MemberID : thisObj.viewVar.MemberID,
                                        TotalWeight : TotalWeight,
                                        TotalCapacity : TotalCapacity
                                    },
                                    success: function (fp, o) {
                                        var r = Ext.decode(o.response.responseText);
                                
                                        Ext.MessageBox.show({
                                            title: 'Information',
                                            msg: r.message,
                                            buttons: Ext.MessageBox.OK,
                                            animateTarget: 'mb9',
                                            icon: 'ext-mb-success'
                                        });
                                
                                        Ext.getCmp('Koltiva.view.Traceability_new.Reception.FormBatch-form-ButtonAdd').hide(); 
                                        
                                        thisObj.close();
                                
                                        thisObj.viewVar.StoreGridMain.load({
                                            params: {
                                                SupplyTransID: r.SupplyTransID
                                            }
                                        });
                                
                                        Ext.getCmp('Koltiva.view.Traceability_new.Reception.FormReception-form-SupplyTransID').setValue(r.SupplyTransID)
                                    },
                                    failure: function (fp, o) {
                                        try {
                                            var r = Ext.decode(o.response.responseText);
                                            Ext.MessageBox.show({
                                                title: 'Error',
                                                msg: r.message,
                                                buttons: Ext.MessageBox.OK,
                                                animateTarget: 'mb9',
                                                icon: 'ext-mb-error'
                                            });
                                        }
                                        catch(err) {
                                            Ext.MessageBox.show({
                                                title: 'Error',
                                                msg: 'Connection Error',
                                                buttons: Ext.MessageBox.OK,
                                                animateTarget: 'mb9',
                                                icon: 'ext-mb-error'
                                            });
                                        }
                                    }
                                });
                            }
                        } else if (parseFloat(Weight) > parseFloat(Persen20plus)) {

                            message = lang("Total capacity couldn't less then 20% Total Weight estimation");

                            if (Ext.getCmp("Koltiva.view.Traceability_new.Reception.WinFormDeliveryReceiving-Form-labelCheckWeight").isVisible() == false) {
                                Ext.MessageBox.show({
                                    title: 'Warning',
                                    msg: message + '<br/>' + lang('Are you sure to continue?'),
                                    buttons: Ext.MessageBox.OKCANCEL,
                                    icon: Ext.MessageBox.WARNING,
                                    fn: function(btn){
                                        if (btn == 'ok'){
                                            Ext.getCmp("Koltiva.view.Traceability_new.Reception.WinFormDeliveryReceiving-Form-labelCheckWeight").show();

                                            setWeightOriginal = parseFloat(Weight);
                                            setValueLabel     = lang('Less than 20%');

                                            Ext.getCmp("Koltiva.view.Traceability_new.Reception.WinFormDeliveryReceiving-Form-labelCheckWeight").update(`<div style="margin-left:110px;color:#ED2F0D;">${lang(setValueLabel)}</div>`);

                                            return;
                                        } else {
                                            return;
                                        }
                                    }
                                });
                            } else {
                                Formnya.submit({
                                    url: m_api + '/traceability_api/reception/data_receiving_input',
                                    method: 'POST',
                                    waitMsg: lang('Saving data'),
                                    params: {
                                        OpsiDisplay: thisObj.viewVar.OpsiDisplay,
                                        SupplyTransID: thisObj.viewVar.SupplyTransID,
                                        MemberID : thisObj.viewVar.MemberID,
                                        TotalWeight : TotalWeight,
                                        TotalCapacity : TotalCapacity
                                    },
                                    success: function (fp, o) {
                                        var r = Ext.decode(o.response.responseText);
                                
                                        Ext.MessageBox.show({
                                            title: 'Information',
                                            msg: r.message,
                                            buttons: Ext.MessageBox.OK,
                                            animateTarget: 'mb9',
                                            icon: 'ext-mb-success'
                                        });
                                
                                        Ext.getCmp('Koltiva.view.Traceability_new.Reception.FormBatch-form-ButtonAdd').hide(); 
                                        
                                        thisObj.close();
                                
                                        thisObj.viewVar.StoreGridMain.load({
                                            params: {
                                                SupplyTransID: r.SupplyTransID
                                            }
                                        });
                                
                                        Ext.getCmp('Koltiva.view.Traceability_new.Reception.FormReception-form-SupplyTransID').setValue(r.SupplyTransID)
                                    },
                                    failure: function (fp, o) {
                                        try {
                                            var r = Ext.decode(o.response.responseText);
                                            Ext.MessageBox.show({
                                                title: 'Error',
                                                msg: r.message,
                                                buttons: Ext.MessageBox.OK,
                                                animateTarget: 'mb9',
                                                icon: 'ext-mb-error'
                                            });
                                        }
                                        catch(err) {
                                            Ext.MessageBox.show({
                                                title: 'Error',
                                                msg: 'Connection Error',
                                                buttons: Ext.MessageBox.OK,
                                                animateTarget: 'mb9',
                                                icon: 'ext-mb-error'
                                            });
                                        }
                                    }
                                });
                            }
                            
                        } else {

                            Formnya.submit({
                                url: m_api + '/traceability_api/reception/data_receiving_input',
                                method: 'POST',
                                waitMsg: lang('Saving data'),
                                params: {
                                    OpsiDisplay: thisObj.viewVar.OpsiDisplay,
                                    SupplyTransID: thisObj.viewVar.SupplyTransID,
                                    MemberID : thisObj.viewVar.MemberID,
                                    TotalWeight : TotalWeight
                                },
                                success: function (fp, o) {
                                    var r = Ext.decode(o.response.responseText);
                            
                                    Ext.MessageBox.show({
                                        title: 'Information',
                                        msg: r.message,
                                        buttons: Ext.MessageBox.OK,
                                        animateTarget: 'mb9',
                                        icon: 'ext-mb-success'
                                    });
                            
                                    Ext.getCmp('Koltiva.view.Traceability_new.Reception.FormBatch-form-ButtonAdd').hide(); 
                                    
                                    thisObj.close();
                            
                                    thisObj.viewVar.StoreGridMain.load({
                                        params: {
                                            SupplyTransID: r.SupplyTransID
                                        }
                                    });
                            
                                    Ext.getCmp('Koltiva.view.Traceability_new.Reception.FormReception-form-SupplyTransID').setValue(r.SupplyTransID)
                                },
                                failure: function (fp, o) {
                                    try {
                                        var r = Ext.decode(o.response.responseText);
                                        Ext.MessageBox.show({
                                            title: 'Error',
                                            msg: r.message,
                                            buttons: Ext.MessageBox.OK,
                                            animateTarget: 'mb9',
                                            icon: 'ext-mb-error'
                                        });
                                    }
                                    catch(err) {
                                        Ext.MessageBox.show({
                                            title: 'Error',
                                            msg: 'Connection Error',
                                            buttons: Ext.MessageBox.OK,
                                            animateTarget: 'mb9',
                                            icon: 'ext-mb-error'
                                        });
                                    }
                                }
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
    }
});
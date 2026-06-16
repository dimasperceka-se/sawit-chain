Ext.define('Koltiva.view.Traceability_new.Transaction_neo.WinFormDataUnit', {
    extend: 'Ext.window.Window',
    id: 'Koltiva.view.Traceability_new.Transaction_neo.WinFormDataUnit',
    cls: 'Sfr_LayoutPopupWindows',
    title: lang('Weight & Deduction'),
    closable: true,
    modal: true,
    closeAction: 'destroy',
    width: '45%',
    height: 500,
    overflowY: 'auto',
    viewVar: false,
    setViewVar: function (value) {
        this.viewVar = value;
    },
    listeners: {
        afterRender: function () {
            var thisObj = this;
            var SupplyTransID = thisObj.viewVar.SupplyTransID

            if (thisObj.viewVar.OpsiDisplay == 'update') {
                //form reset
                var FormNya = Ext.getCmp('Koltiva.view.Traceability_new.Transaction_neo.WinFormDataUnit-Form');

                FormNya.getForm().load({
                    url: m_api + '/traceability_api/web_transaction/data_weight_unit_form_open',
                    method: 'GET',
                    params: {
                        SupplyTransID: thisObj.viewVar.SupplyTransID
                    },
                    success: function (form, action) {
                        var r = Ext.decode(action.response.responseText);

                        Ext.getCmp('Koltiva.view.Traceability_new.Transaction_neo.WinFormDataUnit-Form-SupplyTransID').setReadOnly(true);

                        Ext.getCmp('Koltiva.view.Traceability_new.Transaction_neo.WinFormDataUnit-Form-ContractPrice').getValue()

                        var totalPayment  = Ext.getCmp('Koltiva.view.Traceability_new.Transaction_neo.WinFormDataUnit-Form-TotalPayment').getValue()

                        Ext.getCmp('Koltiva.view.Traceability_new.Transaction_neo.WinFormDataUnit-Form-TotalPayment').setValue(totalPayment)

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
        var labelWidth = 135;
        
        var SalesType          = Ext.getCmp('Koltiva.view.Traceability_new.Transaction_neo.MainForm-FormBasicData-SalesType').getValue();
        var DateTransaction    = Ext.getCmp('Koltiva.view.Traceability_new.Transaction_neo.MainForm-FormBasicData-DateTransaction').getValue();

        var SellerType         = Ext.getCmp('Koltiva.view.Traceability_new.Transaction_neo.MainForm-FormBasicData-SellerType').getValue();

        var Mill               = Ext.getCmp('Koltiva.view.Traceability_new.Transaction.FormTransaction-Mill').getValue();
        var OtherMillName      = Ext.getCmp('Koltiva.view.Traceability_new.Transaction.FormTransaction-OtherMillName').getValue();

        var Do                 = Ext.getCmp('Koltiva.view.Traceability_new.Transaction.FormTransaction-DO').getValue();
        var Agent              = Ext.getCmp('Koltiva.view.Traceability_new.Transaction.FormTransaction-Agent').getValue();

        var OtherDOName        = Ext.getCmp('Koltiva.view.Traceability_new.Transaction.FormTransaction-OtherDOName').getValue();
        var OtherAgentName     = Ext.getCmp('Koltiva.view.Traceability_new.Transaction.FormTransaction-OtherAgentName').getValue();
        var OtherAgentNin      = Ext.getCmp('Koltiva.view.Traceability_new.Transaction.FormTransaction-OtherAgentNin').getValue();
        var OtherAgentSurvey   = Ext.getCmp('Koltiva.view.Traceability_new.Transaction.FormTransaction-OtherAgentSurvey').getValue();

        SupplyTransID : Ext.getCmp('Koltiva.view.Traceability_new.Transaction_neo.MainForm-FormBasicData-SupplyTransID').getValue();

        //items -------------------------------------------------------------- (begin)
        thisObj.items = [{
            xtype: 'form',
            id: 'Koltiva.view.Traceability_new.Transaction_neo.WinFormDataUnit-Form',
            padding: '1 15 5 8',
            items: [{
                layout: 'column',
                border: false,
                items: [{
                    columnWidth: 1,
                    layout: 'form',
                    style: 'padding-bottom:10px;',
                    items: [{
                        xtype: 'panel',
                        title: lang('Weight and Price'),
                        frame: false,
                        style: 'margin-top:15px;',
                        cls: 'Sfr_PanelSubLayoutFormRoundedGray',
                    },{
                        xtype: 'hiddenfield',
                        id: 'Koltiva.view.Traceability_new.Transaction_neo.WinFormDataUnit-Form-SupplyTransID',
                        name: 'Koltiva.view.Traceability_new.Transaction_neo.WinFormDataUnit-Form-SupplyTransID'
                    },
                    
                    {
                        xtype: 'numberfield',
                        id: 'Koltiva.view.Traceability_new.Transaction_neo.WinFormDataUnit-Form-Bunches',
                        name: 'Koltiva.view.Traceability_new.Transaction_neo.WinFormDataUnit-Form-Bunches',
                        fieldLabel: lang('Bunches (pcs)'),
                        labelWidth: labelWidth,
                        allowBlank: false,
                        baseCls: 'Sfr_FormInputMandatory',
                        minValue: 0
                    },{
                        xtype: 'numberfield',
                        id: 'Koltiva.view.Traceability_new.Transaction_neo.WinFormDataUnit-Form-VolumeBruto',
                        name: 'Koltiva.view.Traceability_new.Transaction_neo.WinFormDataUnit-Form-VolumeBruto',
                        fieldLabel: lang('Gross Weight (kg)'),
                        labelWidth: labelWidth,
                        allowBlank: false,
                        baseCls: 'Sfr_FormInputMandatory',
                        minValue: 0,
                        listeners: {
                            change : function(record){
                                var value               = parseFloat(record.getValue())
                                var DeductionPercentage = Ext.getCmp('Koltiva.view.Traceability_new.Transaction_neo.WinFormDataUnit-Form-DeductionPercentage').getValue();
                                
                                Ext.getCmp('Koltiva.view.Traceability_new.Transaction_neo.WinFormDataUnit-Form-VolumeNetto').setValue(value);
                                if (Object.is(DeductionPercentage, null) == false) {
                                    Ext.getCmp('Koltiva.view.Traceability_new.Transaction_neo.WinFormDataUnit-Form-DeductionPercentage').setValue('0');
                                }

                            }
                        }
                    },{
                        xtype: 'numberfield',
                        id: 'Koltiva.view.Traceability_new.Transaction_neo.WinFormDataUnit-Form-ContractPrice',
                        name: 'Koltiva.view.Traceability_new.Transaction_neo.WinFormDataUnit-Form-ContractPrice',
                        fieldLabel: lang('Price per Kilo'),
                        labelWidth: labelWidth,
                        allowBlank: false,
                        baseCls: 'Sfr_FormInputMandatory',
                        minValue: 0,
                        listeners: {
                            change : function(record){
                                var value               = parseFloat(record.getValue())

                                var price = Ext.getCmp('Koltiva.view.Traceability_new.Transaction_neo.WinFormDataUnit-Form-ContractPrice').getValue(value);
                                var netto   = Ext.getCmp('Koltiva.view.Traceability_new.Transaction_neo.WinFormDataUnit-Form-VolumeNetto').getValue();

                                var payment = parseFloat(netto) * parseFloat(price);

                                Ext.getCmp('Koltiva.view.Traceability_new.Transaction_neo.WinFormDataUnit-Form-TotalPayment').setValue(payment);

                            }
                        }
                    },{
                        xtype: 'numberfield',
                        forcePrecision: true,   
                        decimalPrecision: 0,
                        id: 'Koltiva.view.Traceability_new.Transaction_neo.WinFormDataUnit-Form-TotalPayment',
                        name: 'Koltiva.view.Traceability_new.Transaction_neo.WinFormDataUnit-Form-TotalPayment',
                        fieldLabel: lang('Total Payment'),
                        labelWidth: labelWidth,
                        readOnly: true
                    },{
                        xtype: 'panel',
                        title: lang('Deduction'),
                        frame: false,
                        id: 'Koltiva.view.Traceability_new.Transaction_neo.WinFormDataUnit-Form-Deduction',
                        style: 'margin-top:15px;',
                        cls: 'Sfr_PanelSubLayoutFormRoundedGray',
                    },
                    {
                        xtype: 'fieldcontainer', 
                        width : 350, 
                        fieldLabel: lang('Deduction By Percentage'),									
                        defaults: {
                            hideLabel: true,
                            allowBlank: true, 
                            readOnly:true,
                        }, 
                        layout: 'hbox',
                        msgTarget: 'side',
                        items: [
                            { 
                            labelAlign:'top',
                            xtype: 'radiogroup',
                            id : 'Koltiva.view.Traceability_new.Transaction_neo.WinFormDataUnit-Form-IsWeight',
                            allowBlank: false,
                            msgTarget: 'side',
                            columns :2, 
                            padding :'8 10 0 0',
                            items:[{
                                boxLabel: lang('Yes'),
                                name: 'Koltiva.view.Traceability_new.Transaction_neo.WinFormDataUnit-Form-IsWeight',
                                id: 'Koltiva.view.Traceability_new.Transaction_neo.WinFormDataUnit-Form-IsWeightYes',
                                style: 'margin-top:-10px;',
                                listeners:{
                                    change: function(record){
                                        var checked = record.getValue()
                                        if(checked == true){
                                            Ext.getCmp('Koltiva.view.Traceability_new.Transaction_neo.WinFormDataUnit-Form-DeductionPercentage').setVisible(true);
                                            Ext.getCmp('Koltiva.view.Traceability_new.Transaction_neo.WinFormDataUnit-Form-DeductionWeight').setVisible(false);
                                        } 

                                        return false;
                                    }
                                }
                            },{
                                boxLabel: lang('No'),
                                name: 'Koltiva.view.Traceability_new.Transaction_neo.WinFormDataUnit-Form-IsWeight',
                                id: 'Koltiva.view.Traceability_new.Transaction_neo.WinFormDataUnit-Form-IsWeightNo',
                                style: 'margin-top:-10px; margin-left:20px;',
                                width : 160,
                                listeners:{
                                    change: function(record){
                                        var checked = record.getValue()
                                        
                                        if(checked == true){
                                            
                                            Ext.getCmp('Koltiva.view.Traceability_new.Transaction_neo.WinFormDataUnit-Form-DeductionWeight').setVisible(true);

                                            Ext.getCmp('Koltiva.view.Traceability_new.Transaction_neo.WinFormDataUnit-Form-DeductionPercentage').setVisible(false);

                                            let VolumeBruto = Ext.getCmp('Koltiva.view.Traceability_new.Transaction_neo.WinFormDataUnit-Form-VolumeBruto').getValue();

                                            let VolumeNetto2 = Ext.getCmp('Koltiva.view.Traceability_new.Transaction_neo.WinFormDataUnit-Form-VolumeNetto').getValue();
                                            
                                            let DeductionWeight = VolumeBruto - VolumeNetto2 

                                            Ext.getCmp('Koltiva.view.Traceability_new.Transaction_neo.WinFormDataUnit-Form-DeductionWeight').setValue(DeductionWeight);
                                        } 

                                        return false;
                                    }
                                }
                            }]
                        }
                    ]
                    },
                    {
                        xtype: 'numberfield',
                        id: 'Koltiva.view.Traceability_new.Transaction_neo.WinFormDataUnit-Form-DeductionPercentage',
                        name: 'Koltiva.view.Traceability_new.Transaction_neo.WinFormDataUnit-Form-DeductionPercentage',
                        style: 'margin-top:15px;margin-left:10px;margin-right:10px',
                        fieldLabel: lang('By Percentage (%)'),
                        labelWidth: labelWidth,
                        minValue: 0,
                        allowBlank: true,
                        // readOnly: false,
                        baseCls: 'Sfr_FormInputMandatory',
                        listeners: {
                            change : function(record){
                                
                                let Price = Ext.getCmp('Koltiva.view.Traceability_new.Transaction_neo.WinFormDataUnit-Form-ContractPrice').getValue();

                                let VolumeBruto = Ext.getCmp('Koltiva.view.Traceability_new.Transaction_neo.WinFormDataUnit-Form-VolumeBruto').getValue();
                            
                                let DeductionPercentage   = Ext.getCmp('Koltiva.view.Traceability_new.Transaction_neo.WinFormDataUnit-Form-DeductionPercentage').getValue();

                                let percent = DeductionPercentage / 100;
                                
                                if (Object.is(VolumeBruto, null) == false) {
                                    if (Object.is(DeductionPercentage, null) == false) {
                                        let TotalNetto = parseFloat(VolumeBruto) * parseFloat(percent);

                                        let TotalNetto2 = parseFloat(VolumeBruto) - parseFloat(TotalNetto) ;
                                        
                                        Ext.getCmp('Koltiva.view.Traceability_new.Transaction_neo.WinFormDataUnit-Form-VolumeNetto').setValue(TotalNetto2);

                                        let countTotalPayment = parseFloat(TotalNetto2) * Price

                                        Ext.getCmp('Koltiva.view.Traceability_new.Transaction_neo.WinFormDataUnit-Form-TotalPayment').setValue(countTotalPayment)
                                    }
                                } 
                            }
                        }
                    },{
                        xtype: 'numberfield',
                        id: 'Koltiva.view.Traceability_new.Transaction_neo.WinFormDataUnit-Form-DeductionWeight',
                        name: 'Koltiva.view.Traceability_new.Transaction_neo.WinFormDataUnit-Form-DeductionWeight',
                        style: 'margin-top:15px;margin-left:10px;margin-right:10px',
                        fieldLabel: lang('By Weight (kg)'),
                        labelWidth: labelWidth,
                        allowBlank: true,
                        baseCls: 'Sfr_FormInputMandatory',
                        // readOnly: false,
                        minValue: 0,
                        listeners: {
                            change : function(record){
                                let value                   = parseFloat(record.getValue())
                                
                                let DeductionWeightValue    = value.toFixed(0);

                                let DeductionWeight         = Ext.getCmp('Koltiva.view.Traceability_new.Transaction_neo.WinFormDataUnit-Form-DeductionWeight').setValue(DeductionWeightValue);
                                
                                if (Object.is(DeductionWeight, null) == false) {

                                    if(value == '0'){
                                        
                                        Ext.getCmp('Koltiva.view.Traceability_new.Transaction_neo.WinFormDataUnit-Form-DeductionPercentage').getValue();

                                    } else {
                                        
                                        let VolumeBruto             = parseFloat(Ext.getCmp('Koltiva.view.Traceability_new.Transaction_neo.WinFormDataUnit-Form-VolumeBruto').getValue());
                                   
                                        let DeductionWeight         = DeductionWeightValue;
                                        
                                        let DeductionPercentage     = DeductionWeight / VolumeBruto * 100;
                                        
                                        Ext.getCmp('Koltiva.view.Traceability_new.Transaction_neo.WinFormDataUnit-Form-DeductionPercentage').setValue(DeductionPercentage);

                                        let TotalNetto2             = parseFloat(VolumeBruto) - parseFloat(DeductionWeight) ;
                                    
                                        Ext.getCmp('Koltiva.view.Traceability_new.Transaction_neo.WinFormDataUnit-Form-VolumeNetto').setValue(TotalNetto2);

                                        let Price = Ext.getCmp('Koltiva.view.Traceability_new.Transaction_neo.WinFormDataUnit-Form-ContractPrice').getValue();
                                        
                                        let TotalPayment = parseFloat(TotalNetto2) * Price

                                        Ext.getCmp('Koltiva.view.Traceability_new.Transaction_neo.WinFormDataUnit-Form-TotalPayment').setValue(TotalPayment);
                                    }
                                }
                            }
                        }
                    },{
                        xtype: 'numberfield',
                        forcePrecision: true,   
                        decimalPrecision: 0,
                        id: 'Koltiva.view.Traceability_new.Transaction_neo.WinFormDataUnit-Form-VolumeNetto',
                        name: 'Koltiva.view.Traceability_new.Transaction_neo.WinFormDataUnit-Form-VolumeNetto',
                        style: 'margin-top:15px;margin-left:10px;margin-right:10px',
                        fieldLabel: lang('Nett Weight (Kg)'),
                        labelWidth: labelWidth,
                        readOnly: true
                    }]
                }]
            }]
        }];
        //items -------------------------------------------------------------- (end)


        thisObj.buttons = [{
            icon: varjs.config.base_url + 'images/icons/new/save.png',
            cls: 'Sfr_BtnFormBlue',
            overCls: 'Sfr_BtnFormBlue-Hover',
            text: lang('Save'),
            id: 'Koltiva.view.Traceability_new.Transaction_neo.WinFormDataUnit-Form-BtnSave',
            handler: function () {
                var Formnya = Ext.getCmp('Koltiva.view.Traceability_new.Transaction_neo.WinFormDataUnit-Form').getForm();
                if (Formnya.isValid()) {
                    Formnya.submit({
                        url: m_api + '/traceability_api/web_transaction/data_transaction_detail_input',
                        params: {
                            OpsiDisplay: thisObj.viewVar.OpsiDisplay,
                            SupplyTransID: thisObj.viewVar.SupplyTransID,
                            MemberID : thisObj.viewVar.MemberID,
                            SalesType : SalesType,
                            SellerType : SellerType,
                            Mill   : Mill,
                            Do     : Do,
                            Agent  : Agent,
                            OtherDOName : OtherDOName,
                            OtherMillName : OtherMillName,
                            OtherAgentName : OtherAgentName,
                            OtherAgentNin : OtherAgentNin,
                            OtherAgentSurvey : OtherAgentSurvey,
                            DateTransaction : DateTransaction
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

                            thisObj.close();

                            thisObj.viewVar.StoreGridMain.load({
                                params: {
                                    SupplyTransID: r.SupplyTransID
                                }
                            });

                            sessionStorage.setItem('setSupplyTransID', r.SupplyTransID);

                            Ext.getCmp('Koltiva.view.Traceability_new.Transaction_neo.MainForm-FormBasicData-MemberID').setReadOnly(true);
                            Ext.getCmp('Koltiva.view.Traceability_new.Transaction_neo.MainForm-FormBasicData-SupplyTransID').setValue(r.SupplyTransID);

                            Ext.getCmp('Koltiva.view.Traceability_new.Transaction_neo.MainForm-FormBasicData-BtnSave').setVisible(true);
                            Ext.getCmp('Koltiva.view.Traceability_new.Transaction_neo.MainForm-FormBasicData-SalesType').setReadOnly(true);
                            Ext.getCmp('Koltiva.view.Traceability_new.Transaction_neo.MainForm-FormBasicData-DateTransaction').setReadOnly(true);
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
    }
});
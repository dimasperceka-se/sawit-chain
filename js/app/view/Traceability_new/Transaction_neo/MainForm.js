function getCertifiedFarmer(MemberID){
	Ext.Ajax.request({
		url: m_api + '/traceability_api/web_transaction/getFarmerCertified',
		waitMsg: lang('Please Wait'),
		method : 'GET',
		params: {
			MemberID:  MemberID
		},
		success: function(result, opts) {
			var r = JSON.parse(result.responseText);
		
			Ext.getCmp('Koltiva.view.Traceability_new.Transaction_neo.MainForm-FormBasicData-Certified').setValue(r.data.Certification);
		}
	});
}

Ext.define('Koltiva.view.Traceability_new.Transaction_neo.MainForm', {
    extend: 'Ext.panel.Panel',
    id: 'Koltiva.view.Traceability_new.Transaction_neo.MainForm',
    style: 'padding:0 15px 15px 15px;margin:5px 0 0 0;',
    viewVar: false,
    setViewVar: function (value) {
        this.viewVar = value;
    },
    renderTo: 'ext-content',
    listeners: {
        afterRender: function () {
            var thisObj = this;

            if(m_IsPaymentMethod != 1){
                thisObj.ObjPanelPayment.hide();
                thisObj.ObjPanelPaymentNonFarmer.hide();
                thisObj.ObjPanelPaymentDirectBatch.hide();
            }
            else{
                thisObj.ObjPanelPayment.hide();
                thisObj.ObjPanelPaymentNonFarmer.hide();
                thisObj.ObjPanelPaymentDirectBatch.hide();
            }

            // console.log(r.Status);

            if (thisObj.viewVar.OpsiDisplay == 'view' || thisObj.viewVar.OpsiDisplay == 'update') {
                if (thisObj.viewVar.OpsiDisplay == 'view') {
                    Ext.getCmp('Koltiva.view.Traceability_new.Transaction_neo.MainForm-FormBasicData-BtnSave').setVisible(false);
                    // Ext.getCmp('Koltiva.view.Traceability_new.Transaction_neo.MainForm-FormBasicData-btnBayar').setVisible(false);
                    // Ext.getCmp('Koltiva.view.Traceability_new.Transaction_neo.MainForm-FormBasicData-btnPaymentInstruction').setVisible(true);
                    Ext.getCmp('Koltiva.view.Traceability_new.Transaction_neo.PanelDataUnit-ButtonAdd').setVisible(false);
                    Ext.getCmp('Koltiva.view.Traceability_new.Transaction_neo.PanelDataUnit-ButtonActionGrid').setVisible(false);
                    // Ext.getCmp('Koltiva.view.Traceability_new.Transaction_neo.MainForm-FormBasicData-btnBayar').setVisible(false);

                    Ext.getCmp('Koltiva.view.Traceability_new.Transaction_neo.MainForm-FormBasicData-InvoiceNumber').setReadOnly(true)
                    Ext.getCmp('Koltiva.view.Traceability_new.Transaction_neo.MainForm-FormBasicData-InvoiceNumberNonFarmer').setReadOnly(true)
                } else {
                    Ext.getCmp('Koltiva.view.Traceability_new.Transaction_neo.MainForm-FormBasicData-BtnSave').setVisible(true);
                }

                //load formnya
                Ext.getCmp('Koltiva.view.Traceability_new.Transaction_neo.MainForm-FormBasicData').getForm().load({
                    url: m_api + '/traceability_api/web_transaction/transaction_form_open',
                    method: 'GET',
                    params: {
                        SupplyTransID: sessionStorage.getItem('setSupplyTransID') == null ? this.viewVar.SupplyTransID : sessionStorage.getItem('setSupplyTransID'),
                        SupplychainID: this.viewVar.SupplychainID,
                        page : 1,
                        start : 0,
                        limit :1
                    },
                    success: function (form, action) {
                        Ext.MessageBox.hide();
                        var r = Ext.decode(action.response.responseText);

                        console.log('cowik');

                        //Title
                        Ext.getCmp('Koltiva.view.Traceability_new.Transaction_neo.MainForm-labelInfoInsert').update('<div id="header_title_farmer">' + Ext.getCmp('Koltiva.view.Traceability_new.Transaction_neo.MainForm-FormBasicData-MemberDisplayID').getValue() + ' - <strong>' + Ext.getCmp('Koltiva.view.Traceability_new.Transaction_neo.MainForm-FormBasicData-MemberName').getValue() + '</strong></div>');
                        Ext.getCmp('Koltiva.view.Traceability_new.Transaction_neo.MainForm-labelInfoInsert').doLayout();


                        var photo      = Ext.getCmp('Koltiva.view.Traceability_new.Transaction_neo.MainForm-FormBasicData-Photo').getValue();
                        var provinceID = Ext.getCmp('Koltiva.view.Traceability_new.Transaction_neo.MainForm-FormBasicData-ProvinceID').getValue();
                        var status     = Ext.getCmp('Koltiva.view.Traceability_new.Transaction_neo.MainForm-FormBasicData-Status').getValue();

                        var paymentmethodid = Ext.getCmp('Koltiva.view.Traceability_new.Transaction_neo.MainForm-FormBasicData-PaymentMethodID').getValue();
                        var paymentmethodidnonfarmer = Ext.getCmp('Koltiva.view.Traceability_new.Transaction_neo.MainForm-FormBasicData-PaymentMethodIDNonFarmer').getValue();
                        var paymentmethodiddirectbatch = Ext.getCmp('Koltiva.view.Traceability_new.Transaction_neo.MainForm-FormBasicData-PaymentMethodIDDirectBatch').getValue();
                        var salestype = Ext.getCmp('Koltiva.view.Traceability_new.Transaction_neo.MainForm-FormBasicData-SalesType').getValue();

                        Ext.getCmp('Koltiva.view.Traceability_new.Transaction_neo.MainForm-FormBasicData-btnBayar').setVisible(true);

                        if(status=='Submitted'){
                            Ext.getCmp('Koltiva.view.Traceability_new.Transaction_neo.MainForm-FormBasicData-btnPaymentInstruction').show();
                            Ext.getCmp('Koltiva.view.Traceability_new.Transaction_neo.MainForm-FormBasicData-btnBayar').hide();
                        }

                        if(paymentmethodid==null){
                            Ext.getCmp('Koltiva.view.Traceability_new.Transaction_neo.MainForm-FormBasicData-btnBayar').hide();
                        }

                        if(paymentmethodidnonfarmer==null){
                            Ext.getCmp('Koltiva.view.Traceability_new.Transaction_neo.MainForm-FormBasicData-btnBayarNonFarmer').hide();
                        }

                        if(paymentmethodiddirectbatch==null){
                            Ext.getCmp('Koltiva.view.Traceability_new.Transaction_neo.MainForm-FormBasicData-btnBayarDirectBatch').hide();
                        }

                        if(photo != ""){
                            var fotoUser = m_api_base_url + '/images/member/'+provinceID+'/'+ photo;
                            checkImageExists(fotoUser, function(existsImage) {
                                    if (existsImage == true) {
                                        Ext.getCmp('Koltiva.view.Traceability_new.Transaction_neo.MainForm-FormBasicData-PhotoShow').update('<img src="' + fotoUser + '" style="height:150px;margin:0px 5px 5px 0px;float:left;" />');
                                    } else {
                                        Ext.getCmp('Koltiva.view.Traceability_new.Transaction_neo.MainForm-FormBasicData-PhotoShow').update('<img src="' + m_api_base_url + '/assets/images/farmer-default.png" style="height:150px;margin:0px 5px 5px 0px;float:left;" />');
                                    }
                            });
                        } else {
                            Ext.getCmp('Koltiva.view.Traceability_new.Transaction_neo.MainForm-FormBasicData-PhotoShow').update('<img src="' + m_api_base_url + '/assets/images/farmer-default.png" style="height:150px;margin:0px 5px 5px 0px;float:left;" />');
                        }

                        if (status == 1) {
                            Ext.getCmp('Koltiva.view.Traceability_new.Transaction_neo.PanelDataUnit-ButtonAdd').setVisible(false);
                            Ext.getCmp('Koltiva.view.Traceability_new.Transaction_neo.PanelDataUnit-ButtonActionGrid').setVisible(false);
                        }

                        Ext.getCmp('Koltiva.view.Traceability_new.Transaction_neo.MainForm-FormBasicData-MemberDisplayID').setReadOnly(true)

                        if (Ext.getCmp('Koltiva.view.Traceability_new.Transaction_neo.MainForm-FormBasicData-TotalPayment').getValue() === null || Ext.getCmp('Koltiva.view.Traceability_new.Transaction_neo.MainForm-FormBasicData-TotalPayment').getValue() == 0) {
                            Ext.getCmp('Koltiva.view.Traceability_new.Transaction_neo.MainForm-FormBasicData-TotalPayment').setValue(sessionStorage.getItem('TotalPayment'));
                        }

                        if (Ext.getCmp('Koltiva.view.Traceability_new.Transaction_neo.MainForm-FormBasicData-TotalPaymentNonFarmer').getValue() === null || Ext.getCmp('Koltiva.view.Traceability_new.Transaction_neo.MainForm-FormBasicData-TotalPaymentNonFarmer').getValue() == 0) {
                            Ext.getCmp('Koltiva.view.Traceability_new.Transaction_neo.MainForm-FormBasicData-TotalPaymentNonFarmer').setValue(sessionStorage.getItem('TotalPaymentNonFarmer'));
                        }

                        if (Ext.getCmp('Koltiva.view.Traceability_new.Transaction_neo.MainForm-FormBasicData-TotalPaymentDirectBatch').getValue() === null || Ext.getCmp('Koltiva.view.Traceability_new.Transaction_neo.MainForm-FormBasicData-TotalPaymentDirectBatch').getValue() == 0) {
                            Ext.getCmp('Koltiva.view.Traceability_new.Transaction_neo.MainForm-FormBasicData-TotalPaymentDirectBatch').setValue(sessionStorage.getItem('TotalPaymentDirectBatch'));
                        }

                        Ext.getCmp('Koltiva.view.Traceability_new.Transaction_neo.MainForm-FormBasicData-SalesType').setReadOnly(true);
                        Ext.getCmp('Koltiva.view.Traceability_new.Transaction_neo.MainForm-FormBasicData-DateTransaction').setReadOnly(true);

                        // console.log(salestype);
                        if(m_IsPaymentMethod != 1 || salestype != 1){
                            thisObj.ObjPanelPayment.hide();
                            thisObj.ObjPanelPaymentNonFarmer.hide();
                            thisObj.ObjPanelPaymentDirectBatch.hide();
                        }
                        else{
                            thisObj.ObjPanelPayment.show();
                            thisObj.ObjPanelPaymentNonFarmer.hide();
                            thisObj.ObjPanelPaymentDirectBatch.hide();
                        }
                    },
                    failure: function (form, action) {
                        Ext.MessageBox.hide();
                        Ext.MessageBox.show({
                            title: 'Failed',
                            msg: 'Failed to retrieve data',
                            buttons: Ext.MessageBox.OK,
                            animateTarget: 'mb9',
                            icon: 'ext-mb-error'
                        });
                    }
                });

            } else {
                // Ext.getCmp('Koltiva.view.Traceability_new.Transaction_neo.MainForm-FormBasicData-DateTransaction').setVisible(false);
                Ext.getCmp('Koltiva.view.Traceability_new.Transaction_neo.MainForm-FormBasicData-BtnSave').setVisible(false);
                // Ext.getCmp('Koltiva.view.Traceability_new.Transaction_neo.MainForm-FormBasicData-btnBayar').setVisible(false);
            }
        },
        beforerender: function () {
            var thisObj = this;

            if (thisObj.viewVar.OpsiDisplay != 'insert') {
                Ext.MessageBox.show({
                    msg: 'Please wait...',
                    progressText: 'Loading...',
                    width: 300,
                    wait: true,
                    waitConfig: {
                        interval: 200
                    },
                    icon: 'ext-mb-info', //custom class in msg-box.html
                    animateTarget: 'mb9'
                });
            }
        }
    },
    initComponent: function () {
        var thisObj = this;
        
        sessionStorage.removeItem('setSupplyTransID');

        //Store 
        thisObj.StoreFarmers = Ext.create('Koltiva.store.Traceability_new.Transaction_neo.Farmers');
        var ComboPlantation = Ext.create('Koltiva.store.Traceability_new.Transaction_neo.ComboPlantation');

        thisObj.TransTypeID = Ext.create('Koltiva.store.Traceability_new.Transaction_neo.TransTypeID');
        thisObj.cmb_partner  = Ext.create('Koltiva.store.ComboGeneral.ComboPartner');

        thisObj.StoreComboPaymentMethod     = Ext.create('Koltiva.store.Traceability_new.Transaction_neo.StoreComboPaymentMethod');

        thisObj.ObjPanelDataUnit = Ext.create('Koltiva.view.Traceability_new.Transaction_neo.PanelDataUnit', {
            viewVar: {
                SupplyTransID: thisObj.viewVar.SupplyTransID,
                MemberID : thisObj.viewVar.MemberID
            }
        });

        thisObj.ObjPanelDataUnitNonFarmer = Ext.create('Koltiva.view.Traceability_new.Transaction_neo.PanelDataUnitNonFarmer', {
            viewVar: {
                SupplyTransID: thisObj.viewVar.SupplyTransID,
                MemberID : thisObj.viewVar.MemberID
            }
        });

        thisObj.ObjPanelDataUnitDirectBatch = Ext.create('Koltiva.view.Traceability_new.Transaction_neo.PanelDataUnitDirectBatch', {
            viewVar: {
                SupplyTransID: thisObj.viewVar.SupplyTransID,
                MemberID : thisObj.viewVar.MemberID
            }
        });

        //Panel Payment ==================================== (Begin)
        thisObj.ObjPanelPayment = Ext.create('Ext.panel.Panel', {
            xtype: 'panel',
            title: lang('Payment'),
            frame: false,
            hidden: true,
            id: 'Koltiva.view.Traceability_new.Transaction_neo.MainForm-FormBasicData-SectionTransactionInfoData',
            style: 'margin-left:10px',
            cls: 'Sfr_PanelSubLayoutFormRoundedGray',
            items: [{
                layout: 'column',
                border: false,
                items: [{
                    columnWidth: 1,
                    layout: 'form',
                    style: 'margin-left:10px;margin-right:10px;',
                    items: [{
                        xtype: 'textfield',
                        id: 'Koltiva.view.Traceability_new.Transaction_neo.MainForm-FormBasicData-TransID',
                        name: 'Koltiva.view.Traceability_new.Transaction_neo.MainForm-FormBasicData-TransID',
                        inputType: 'hidden'
                    },
                    {
                        xtype: 'textfield',
                        id: 'Koltiva.view.Traceability_new.Transaction_neo.MainForm-FormBasicData-TransNumber',
                        name: 'Koltiva.view.Traceability_new.Transaction_neo.MainForm-FormBasicData-TransNumber',
                        inputType: 'hidden'
                    },
                    {
                        xtype: 'textfield',
                        id: 'Koltiva.view.Traceability_new.Transaction_neo.MainForm-FormBasicData-InvoiceNumber',
                        name: 'Koltiva.view.Traceability_new.Transaction_neo.MainForm-FormBasicData-InvoiceNumber',
                        fieldLabel: lang('Invoice Number'),
                        allowBlank: true,
                        baseCls: 'Sfr_FormInputMandatory'
                    },
                    {
                        xtype: 'numericfield',
                        id: 'Koltiva.view.Traceability_new.Transaction_neo.MainForm-FormBasicData-TotalPayment',
                        name: 'Koltiva.view.Traceability_new.Transaction_neo.MainForm-FormBasicData-TotalPayment',
                        fieldLabel: lang('Total Payment'),
                        allowBlank: true,
                        readOnly: true,
                        listeners: {
                            change : function(record){

                                let TotalPayment = record.getValue();
                                
                                if(parseFloat(TotalPayment)){
                                    let valuePaymentPaid = Ext.getCmp('Koltiva.view.Traceability_new.Transaction_neo.MainForm-FormBasicData-TotalPayment').getValue();
                                    Ext.getCmp('Koltiva.view.Traceability_new.Transaction_neo.MainForm-FormBasicData-PaymentPaid').setValue(valuePaymentPaid);
                                } 
                            }
                        }
                    },
                    {
                        xtype: 'numericfield',
                        id: 'Koltiva.view.Traceability_new.Transaction_neo.MainForm-FormBasicData-PaymentReduction',
                        name: 'Koltiva.view.Traceability_new.Transaction_neo.MainForm-FormBasicData-PaymentReduction',
                        fieldLabel: lang('Payment Reduction'),
                        value:0,
                        listeners: {
                            change : function(record){
                                let Payment          = Ext.getCmp('Koltiva.view.Traceability_new.Transaction_neo.MainForm-FormBasicData-TotalPayment').getValue();
                                let PaymentReduction = Ext.getCmp('Koltiva.view.Traceability_new.Transaction_neo.MainForm-FormBasicData-PaymentReduction').getValue();
                                
                                let value            = Payment - PaymentReduction;
                                
                                Ext.getCmp('Koltiva.view.Traceability_new.Transaction_neo.MainForm-FormBasicData-PaymentPaid').setValue(value);
                            }
                        }
                    },
                    {
                        xtype: 'numericfield',
                        id: 'Koltiva.view.Traceability_new.Transaction_neo.MainForm-FormBasicData-PaymentPaid',
                        name: 'Koltiva.view.Traceability_new.Transaction_neo.MainForm-FormBasicData-PaymentPaid',
                        fieldLabel: lang('Payment Amount'),
                        readOnly: true
                    },
                    // {
                    //     xtype: 'numericfield',
                    //     id: 'Koltiva.view.Traceability_new.Transaction_neo.MainForm-FormBasicData-PaymentStatusID',
                    //     name: 'Koltiva.view.Traceability_new.Transaction_neo.MainForm-FormBasicData-PaymentStatusID',
                    //     fieldLabel: lang('Payment Status'),
                    //     value:0,
                    //     readOnly: true
                    // },
                    // {
                    //     xtype: 'textfield',
                    //     id: 'Koltiva.view.Traceability_new.Transaction_neo.MainForm-FormBasicData-Status',
                    //     name: 'Koltiva.view.Traceability_new.Transaction_neo.MainForm-FormBasicData-Status',
                    //     fieldLabel: lang('Status'),
                    //     value:'Draft',
                    //     readOnly: true,
                    // },
                    // {
                    //     xtype: 'combo',
                    //     id: 'Koltiva.view.Traceability_new.Transaction_neo.MainForm-FormBasicData-PaymentMethodID',
                    //     name: 'Koltiva.view.Traceability_new.Transaction_neo.MainForm-FormBasicData-PaymentMethodID',
                    //     fieldLabel: lang('Payment Method'),
                    //     store: thisObj.StoreComboPaymentMethod ,
                    //     displayField: 'label',
                    //     valueField: 'id',
                    //     queryMode: 'local',
                    //     listeners: {
                    //         change : function(){
                    //             // Ext.getCmp('Koltiva.view.Traceability_new.Transaction_neo.MainForm-FormBasicData-btnBayar').show();
                    //         }
                    //     }
                    //     // valueField:id
                    // },
                    // {
                    //     xtype: 'textfield',
                    //     id: 'Koltiva.view.Traceability_new.Transaction_neo.MainForm-FormBasicData-BankCode',
                    //     name: 'Koltiva.view.Traceability_new.Transaction_neo.MainForm-FormBasicData-BankCode',
                    //     fieldLabel: lang('Bank Code')
                    // },
                    // {
                    //     xtype: 'textfield',
                    //     id: 'Koltiva.view.Traceability_new.Transaction_neo.MainForm-FormBasicData-BankName',
                    //     name: 'Koltiva.view.Traceability_new.Transaction_neo.MainForm-FormBasicData-BankName',
                    //     fieldLabel: lang('Bank Name')
                    // },
                    // {
                    //     xtype: 'textfield',
                    //     id: 'Koltiva.view.Traceability_new.Transaction_neo.MainForm-FormBasicData-AccountNumber',
                    //     name: 'Koltiva.view.Traceability_new.Transaction_neo.MainForm-FormBasicData-AccountNumber',
                    //     fieldLabel: lang('Account Number')
                    // },
                    // {
                    //     xtype: 'textfield',
                    //     id: 'Koltiva.view.Traceability_new.Transaction_neo.MainForm-FormBasicData-AccountName',
                    //     name: 'Koltiva.view.Traceability_new.Transaction_neo.MainForm-FormBasicData-AccountName',
                    //     fieldLabel: lang('Account Name')
                    // },
                    // {
                    //     xtype:'button',
                    //     icon: varjs.config.base_url + 'images/icons/new/script_link_white.png',
                    //     hidden:true,
                    //     text: lang('Payment Instruction'),
                    //     id:'Koltiva.view.Traceability_new.Transaction_neo.MainForm-FormBasicData-btnPaymentInstruction',
                    //     margin: '5px',
                    //     cls:'Sfr_BtnFormGreen',
                    //     // overCls:'Sfr_BtnFormGreen-Hover',
                    //     handler: function() {
                    //        //preview_cetak_surat(m_api+'/disburse/disburse_premium/print_invoice/'+thisObj.viewVar.PremiumID);
                    //        var SupplyTransID = Ext.getCmp('Koltiva.view.Traceability_new.Transaction_neo.MainForm-FormBasicData-SupplyTransID').getValue(); 
                           
                    //        if(Ext.getCmp('Koltiva.view.Traceability_new.Transaction_neo.PaymentInstuction') == undefined){
                    //             var PanelPaymentIntruction = Ext.create('Koltiva.view.Traceability_new.Transaction_neo.PaymentInstuction', {
                    //                 opsiDisplay: 'insert',
                    //                 viewVar: { 
                    //                    SupplyTransID : SupplyTransID ,
                    //                    // SupplyBatchID  : thisObj.viewVar.SupplyBatchID,
                    //                    // SupplyTransID  : SupplyTransID,
                    //                    btnSave: true
                    //                 }
                    //             });
                    //         }else{
                    //             //destroy, create ulang
                    //             Ext.getCmp('Koltiva.view.Traceability_new.Transaction_neo.PaymentInstuction').destroy();
                    //             var PanelPaymentIntruction = Ext.create('Koltiva.view.Traceability_new.Transaction_neo.PaymentInstuction', {
                    //                 opsiDisplay: 'insert',
                    //                 viewVar: { 
                    //                    SupplyTransID : SupplyTransID ,
                    //                    btnSave: true
                    //                 }
                    //             });
                    //         }
                    //         if (!PanelPaymentIntruction.isVisible()) {
                    //            PanelPaymentIntruction.center();
                    //            PanelPaymentIntruction.show();
                    //         } else {
                    //            PanelPaymentIntruction.close();
                    //         } 
                    //     }
                    // },
                    // {
                    //     xtype:'button',
                    //     icon: varjs.config.base_url + 'images/icons/new/script_link_white.png',
                    //     // hidden:true,
                    //     text: lang('Bayar'),
                    //     id: 'Koltiva.view.Traceability_new.Transaction_neo.MainForm-FormBasicData-btnBayar',
                    //     margin: '5px',
                    //     cls:'Sfr_BtnFormGreen',
                    //     // overCls:'Sfr_BtnFormGreen-Hover',
                    //     handler: function () {
                    //         Ext.MessageBox.confirm(lang('Confirmation'), lang('Are you sure ?'), function(btn) {
                    //             if (btn == 'yes') {
                    //                 var FormPayment = Ext.getCmp('Koltiva.view.Traceability_new.Transaction_neo.MainForm-FormBasicData').getForm();
                    //                 if (FormPayment.isValid()) {
                    //                     var method = 'POST';
                    //                     FormPayment.submit({
                    //                             url: m_api + '/traceability_api/web_transaction/submit_payment',
                    //                             method:method,
                    //                             waitMsg: lang('Processing ...'),
                    //                             params: {
                    //                                 SupplyTransID : Object.is(sessionStorage.getItem('setSupplyTransID'), null) == false ? sessionStorage.getItem('setSupplyTransID') : thisObj.viewVar.SupplyTransID
                    //                             },
                    //                             success: function (fp, o) {
                    //                                 var r = Ext.decode(fp.responseText);
                    //                                 Ext.MessageBox.show({
                    //                                     title: 'Information',
                    //                                     msg: lang('Submit Payment Success'),
                    //                                     buttons: Ext.MessageBox.OK,
                    //                                     animateTarget: 'mb9',
                    //                                     icon: 'ext-mb-success',
                                                        
                    //                                 });

                    //                                 Ext.getCmp('Koltiva.view.Traceability_new.Transaction_neo.MainForm-FormBasicData-btnBayar').hide();
                    //                                 Ext.getCmp('Koltiva.view.Traceability_new.Transaction_neo.MainForm-FormBasicData-btnPaymentInstruction').show();
                    //                                 Ext.getCmp('Koltiva.view.Traceability_new.Transaction_neo.MainForm-FormBasicData-btnPaymentInstruction').el.dom.click();
                    //                             },
                    //                             failure: function (fp, o) {
                    //                                 try {
                    //                                     var r = Ext.decode(o.response.responseText);
                    //                                     Ext.MessageBox.show({
                    //                                         title: 'Error',
                    //                                         msg: (r.error) ? r.error : r.message,
                    //                                         buttons: Ext.MessageBox.OK,
                    //                                         animateTarget: 'mb9',
                    //                                         icon: 'ext-mb-error'
                    //                                     });
                    //                                 } catch (err) {
                    //                                     Ext.MessageBox.show({
                    //                                         title: 'Error',
                    //                                         msg: 'Connection Error',
                    //                                         buttons: Ext.MessageBox.OK,
                    //                                         animateTarget: 'mb9',
                    //                                         icon: 'ext-mb-error'
                    //                                     });
                    //                                 }
                    //                             }
                    //                         });
                    //                 } else {
                    //                     Ext.MessageBox.show({
                    //                         title: 'Attention',
                    //                         msg: lang('Form not valid yet'),
                    //                         buttons: Ext.MessageBox.OK,
                    //                         animateTarget: 'mb9',
                    //                         icon: 'ext-mb-info'
                    //                     });
                    //                 }
                    //             }
                    //         });
                    //     }
                    // }
                    ]
                },
            ]
            }],
                     
        });
        //Panel Payment ==================================== (End)

        //Panel Payment NonFarmer ==================================== (Begin)
        thisObj.ObjPanelPaymentNonFarmer = Ext.create('Ext.panel.Panel', {
            xtype: 'panel',
            title: lang('Payment'),
            frame: false,
            hidden: false,
            id: 'Koltiva.view.Traceability_new.Transaction_neo.MainForm-FormBasicData-SectionTransactionInfoDataNonFarmer',
            style: 'margin-left:10px',
            cls: 'Sfr_PanelSubLayoutFormRoundedGray',
            items: [{
                layout: 'column',
                border: false,
                items: [{
                    columnWidth: 1,
                    layout: 'form',
                    style: 'margin-left:10px;margin-right:10px;',
                    items: [{
                        xtype: 'textfield',
                        id: 'Koltiva.view.Traceability_new.Transaction_neo.MainForm-FormBasicData-TransID',
                        name: 'Koltiva.view.Traceability_new.Transaction_neo.MainForm-FormBasicData-TransID',
                        inputType: 'hidden'
                    },
                    {
                        xtype: 'textfield',
                        id: 'Koltiva.view.Traceability_new.Transaction_neo.MainForm-FormBasicData-InvoiceNumberNonFarmer',
                        name: 'Koltiva.view.Traceability_new.Transaction_neo.MainForm-FormBasicData-InvoiceNumberNonFarmer',
                        fieldLabel: lang('Invoice Number'),
                        allowBlank: true,
                        baseCls: 'Sfr_FormInputMandatory'
                    },{
                        xtype: 'numericfield',
                        id: 'Koltiva.view.Traceability_new.Transaction_neo.MainForm-FormBasicData-TotalPaymentNonFarmer',
                        name: 'Koltiva.view.Traceability_new.Transaction_neo.MainForm-FormBasicData-TotalPaymentNonFarmer',
                        fieldLabel: lang('Total Payment'),
                        allowBlank: true,
                        readOnly: true,
                        listeners: {
                            change : function(record){

                                let TotalPayment = record.getValue();
                                
                                if(parseFloat(TotalPayment)){
                                    let valuePaymentPaidNonFarmer = Ext.getCmp('Koltiva.view.Traceability_new.Transaction_neo.MainForm-FormBasicData-TotalPaymentNonFarmer').getValue();
                                    Ext.getCmp('Koltiva.view.Traceability_new.Transaction_neo.MainForm-FormBasicData-PaymentPaidNonFarmer').setValue(valuePaymentPaidNonFarmer);
                                } 
                            }
                        }
                    },{
                        xtype: 'numericfield',
                        id: 'Koltiva.view.Traceability_new.Transaction_neo.MainForm-FormBasicData-PaymentReductionNonFarmer',
                        name: 'Koltiva.view.Traceability_new.Transaction_neo.MainForm-FormBasicData-PaymentReductionNonFarmer',
                        fieldLabel: lang('Payment Reduction'),
                        value:0,
                        listeners: {
                            change : function(record){
                                let Payment          = Ext.getCmp('Koltiva.view.Traceability_new.Transaction_neo.MainForm-FormBasicData-TotalPaymentNonFarmer').getValue();
                                let PaymentReduction = Ext.getCmp('Koltiva.view.Traceability_new.Transaction_neo.MainForm-FormBasicData-PaymentReductionNonFarmer').getValue();
                                
                                let value            = Payment - PaymentReduction;
                                
                                Ext.getCmp('Koltiva.view.Traceability_new.Transaction_neo.MainForm-FormBasicData-PaymentPaidNonFarmer').setValue(value);
                            }
                        }
                    },
                    {
                        xtype: 'numericfield',
                        id: 'Koltiva.view.Traceability_new.Transaction_neo.MainForm-FormBasicData-PaymentPaidNonFarmer',
                        name: 'Koltiva.view.Traceability_new.Transaction_neo.MainForm-FormBasicData-PaymentPaid',
                        fieldLabel: lang('Payment Amount'),
                        readOnly: true
                    },
                    {
                        xtype: 'numericfield',
                        id: 'Koltiva.view.Traceability_new.Transaction_neo.MainForm-FormBasicData-PaymentStatusIDNonFarmer',
                        name: 'Koltiva.view.Traceability_new.Transaction_neo.MainForm-FormBasicData-PaymentStatusIDNonFarmer',
                        fieldLabel: lang('Payment Status'),
                        value:0,
                        readOnly: true
                    },{
                        xtype: 'combo',
                        id: 'Koltiva.view.Traceability_new.Transaction_neo.MainForm-FormBasicData-PaymentMethodIDNonFarmer',
                        name: 'Koltiva.view.Traceability_new.Transaction_neo.MainForm-FormBasicData-PaymentMethodIDNonFarmer',
                        fieldLabel: lang('Payment Method'),
                        store: thisObj.StoreComboPaymentMethod ,
                        displayField: 'label',
                        valueField: 'id',
                        queryMode: 'local',
                        listeners: {
                            change : function(){
                                // Ext.getCmp('Koltiva.view.Traceability_new.Transaction_neo.MainForm-FormBasicData-btnBayarNonFarmer').show();
                            }
                        }
                        // valueField:id
                    },
                    {
                        xtype: 'textfield',
                        id: 'Koltiva.view.Traceability_new.Transaction_neo.MainForm-FormBasicData-BankCodeNonFarmer',
                        name: 'Koltiva.view.Traceability_new.Transaction_neo.MainForm-FormBasicData-BankCodeNonFarmer',
                        fieldLabel: lang('Bank Code')
                    },
                    {
                        xtype: 'textfield',
                        id: 'Koltiva.view.Traceability_new.Transaction_neo.MainForm-FormBasicData-BankNameNonFarmer',
                        name: 'Koltiva.view.Traceability_new.Transaction_neo.MainForm-FormBasicData-BankNameNonFarmer',
                        fieldLabel: lang('Bank Name')
                    },
                    {
                        xtype: 'textfield',
                        id: 'Koltiva.view.Traceability_new.Transaction_neo.MainForm-FormBasicData-AccountNumberNonFarmer',
                        name: 'Koltiva.view.Traceability_new.Transaction_neo.MainForm-FormBasicData-AccountNumberNonFarmer',
                        fieldLabel: lang('Account Number')
                    },
                    {
                        xtype: 'textfield',
                        id: 'Koltiva.view.Traceability_new.Transaction_neo.MainForm-FormBasicData-AccountNameNonFarmer',
                        name: 'Koltiva.view.Traceability_new.Transaction_neo.MainForm-FormBasicData-AccountNameNonFarmer',
                        fieldLabel: lang('Account Name')
                    },
                    {
                        xtype:'button',
                        icon: varjs.config.base_url + 'images/icons/new/script_link_white.png',
                        hidden:true,
                        text: lang('Payment Instruction'),
                        id:'Koltiva.view.Traceability_new.Transaction_neo.MainForm-FormBasicData-btnPaymentInstructionNonFarmer',
                        margin: '5px',
                        cls:'Sfr_BtnFormGreen',
                        // overCls:'Sfr_BtnFormGreen-Hover',
                        handler: function() {
                           //preview_cetak_surat(m_api+'/disburse/disburse_premium/print_invoice/'+thisObj.viewVar.PremiumID);
                           var SupplyTransID = Ext.getCmp('Koltiva.view.Traceability_new.Transaction_neo.MainForm-FormBasicData-SupplyTransID').getValue(); 
                           
                           if(Ext.getCmp('Koltiva.view.Traceability_new.Transaction_neo.PaymentInstuction') == undefined){
                                var PanelPaymentIntruction = Ext.create('Koltiva.view.Traceability_new.Transaction_neo.PaymentInstuction', {
                                    opsiDisplay: 'insert',
                                    viewVar: { 
                                       SupplyTransID : SupplyTransID ,
                                       // SupplyBatchID  : thisObj.viewVar.SupplyBatchID,
                                       // SupplyTransID  : SupplyTransID,
                                       btnSave: true
                                    }
                                });
                            }else{
                                //destroy, create ulang
                                Ext.getCmp('Koltiva.view.Traceability_new.Transaction_neo.PaymentInstuction').destroy();
                                var PanelPaymentIntruction = Ext.create('Koltiva.view.Traceability_new.Transaction_neo.PaymentInstuction', {
                                    opsiDisplay: 'insert',
                                    viewVar: { 
                                       SupplyTransID : SupplyTransID ,
                                       btnSave: true
                                    }
                                });
                            }
                            if (!PanelPaymentIntruction.isVisible()) {
                               PanelPaymentIntruction.center();
                               PanelPaymentIntruction.show();
                            } else {
                               PanelPaymentIntruction.close();
                            } 
                        }
                    },
                    {
                        xtype:'button',
                        icon: varjs.config.base_url + 'images/icons/new/script_link_white.png',
                        // hidden:true,
                        text: lang('Bayar'),
                        id: 'Koltiva.view.Traceability_new.Transaction_neo.MainForm-FormBasicData-btnBayarNonFarmer',
                        margin: '5px',
                        cls:'Sfr_BtnFormGreen',
                        // overCls:'Sfr_BtnFormGreen-Hover',
                        handler: function () {
                            Ext.MessageBox.confirm(lang('Confirmation'), lang('Are you sure ?'), function(btn) {
                                if (btn == 'yes') {
                                    var FormPayment = Ext.getCmp('Koltiva.view.Traceability_new.Transaction_neo.MainForm-FormBasicData').getForm();
                                    if (FormPayment.isValid()) {
                                        var method = 'POST';
                                        FormPayment.submit({
                                                url: m_api + '/traceability_api/web_transaction/submit_payment',
                                                method:method,
                                                waitMsg: lang('Processing ...'),
                                                params: {
                                                    SupplyTransID : Object.is(sessionStorage.getItem('setSupplyTransID'), null) == false ? sessionStorage.getItem('setSupplyTransID') : thisObj.viewVar.SupplyTransID
                                                },
                                                success: function (fp, o) {
                                                    var r = Ext.decode(fp.responseText);
                                                    Ext.MessageBox.show({
                                                        title: 'Information',
                                                        msg: lang('Submit Payment Success'),
                                                        buttons: Ext.MessageBox.OK,
                                                        animateTarget: 'mb9',
                                                        icon: 'ext-mb-success',
                                                        
                                                    });

                                                    Ext.getCmp('Koltiva.view.Traceability_new.Transaction_neo.MainForm-FormBasicData-btnBayarNonFarmer').hide();
                                                    Ext.getCmp('Koltiva.view.Traceability_new.Transaction_neo.MainForm-FormBasicData-btnPaymentInstructionNonFarmer').show();
                                                    Ext.getCmp('Koltiva.view.Traceability_new.Transaction_neo.MainForm-FormBasicData-btnPaymentInstructionNonFarmer').el.dom.click();
                                                },
                                                failure: function (fp, o) {
                                                    try {
                                                        var r = Ext.decode(o.response.responseText);
                                                        Ext.MessageBox.show({
                                                            title: 'Error',
                                                            msg: (r.error) ? r.error : r.message,
                                                            buttons: Ext.MessageBox.OK,
                                                            animateTarget: 'mb9',
                                                            icon: 'ext-mb-error'
                                                        });
                                                    } catch (err) {
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
                                            title: 'Attention',
                                            msg: lang('Form not valid yet'),
                                            buttons: Ext.MessageBox.OK,
                                            animateTarget: 'mb9',
                                            icon: 'ext-mb-info'
                                        });
                                    }
                                }
                            });
                        }
                    }]
                }]
            }]                       
        });
        //Panel Payment Nonfarmar ==================================== (End)

        //Panel Payment Direct Batch ==================================== (Begin)
        thisObj.ObjPanelPaymentDirectBatch = Ext.create('Ext.panel.Panel', {
            xtype: 'panel',
            title: lang('Payment'),
            frame: false,
            hidden: false,
            id: 'Koltiva.view.Traceability_new.Transaction_neo.MainForm-FormBasicData-SectionTransactionInfoDataDirectBatch',
            style: 'margin-left:10px',
            cls: 'Sfr_PanelSubLayoutFormRoundedGray',
            items: [{
                layout: 'column',
                border: false,
                items: [{
                    columnWidth: 1,
                    layout: 'form',
                    style: 'margin-left:10px;margin-right:10px;',
                    items: [{
                        xtype: 'textfield',
                        id: 'Koltiva.view.Traceability_new.Transaction_neo.MainForm-FormBasicData-TransID',
                        name: 'Koltiva.view.Traceability_new.Transaction_neo.MainForm-FormBasicData-TransID',
                        inputType: 'hidden'
                    },
                    {
                        xtype: 'textfield',
                        id: 'Koltiva.view.Traceability_new.Transaction_neo.MainForm-FormBasicData-InvoiceNumberDirectBatch',
                        name: 'Koltiva.view.Traceability_new.Transaction_neo.MainForm-FormBasicData-InvoiceNumberDirectBatch',
                        fieldLabel: lang('Invoice Number'),
                        allowBlank: true,
                        baseCls: 'Sfr_FormInputMandatory'
                    },{
                        xtype: 'numericfield',
                        id: 'Koltiva.view.Traceability_new.Transaction_neo.MainForm-FormBasicData-TotalPaymentDirectBatch',
                        name: 'Koltiva.view.Traceability_new.Transaction_neo.MainForm-FormBasicData-TotalPaymentDirectBatch',
                        fieldLabel: lang('Total Payment'),
                        allowBlank: true,
                        readOnly: true,
                        listeners: {
                            change : function(record){

                                let TotalPayment = record.getValue();
                                
                                if(parseFloat(TotalPayment)){
                                    let valuePaymentPaidDirectBatch = Ext.getCmp('Koltiva.view.Traceability_new.Transaction_neo.MainForm-FormBasicData-TotalPaymentDirectBatch').getValue();
                                    Ext.getCmp('Koltiva.view.Traceability_new.Transaction_neo.MainForm-FormBasicData-PaymentPaidDirectBatch').setValue(valuePaymentPaidDirectBatch);
                                } 
                            }
                        }
                        // baseCls: 'Sfr_FormInputMandatory'
                    },{
                        xtype: 'numericfield',
                        id: 'Koltiva.view.Traceability_new.Transaction_neo.MainForm-FormBasicData-PaymentReductionDirectBatch',
                        name: 'Koltiva.view.Traceability_new.Transaction_neo.MainForm-FormBasicData-PaymentReductionDirectBatch',
                        fieldLabel: lang('Payment Reduction'),
                        value:0,
                        listeners: {
                            change : function(record){
                                let Payment          = Ext.getCmp('Koltiva.view.Traceability_new.Transaction_neo.MainForm-FormBasicData-TotalPaymentDirectBatch').getValue();
                                let PaymentReduction = Ext.getCmp('Koltiva.view.Traceability_new.Transaction_neo.MainForm-FormBasicData-PaymentReductionDirectBatch').getValue();
                                
                                let value            = Payment - PaymentReduction;
                                
                                Ext.getCmp('Koltiva.view.Traceability_new.Transaction_neo.MainForm-FormBasicData-PaymentPaidDirectBatch').setValue(value);
                            }
                        }
                    },
                    {
                        xtype: 'numericfield',
                        id: 'Koltiva.view.Traceability_new.Transaction_neo.MainForm-FormBasicData-PaymentPaidDirectBatch',
                        name: 'Koltiva.view.Traceability_new.Transaction_neo.MainForm-FormBasicData-PaymentPaidDirectBatch',
                        fieldLabel: lang('Payment Amount'),
                        value:0,
                        readOnly: true
                    },{
                        xtype: 'numericfield',
                        id: 'Koltiva.view.Traceability_new.Transaction_neo.MainForm-FormBasicData-PaymentStatusIDDirectBatch',
                        name: 'Koltiva.view.Traceability_new.Transaction_neo.MainForm-FormBasicData-PaymentStatusIDDirectBatch',
                        fieldLabel: lang('Payment Status'),
                        value:0,
                        readOnly: true
                    },{
                        xtype: 'combo',
                        id: 'Koltiva.view.Traceability_new.Transaction_neo.MainForm-FormBasicData-PaymentMethodIDDirectBatch',
                        name: 'Koltiva.view.Traceability_new.Transaction_neo.MainForm-FormBasicData-PaymentMethodIDDirectBatch',
                        fieldLabel: lang('Payment Method'),
                        store: thisObj.StoreComboPaymentMethod ,
                        displayField: 'label',
                        valueField: 'id',
                        queryMode: 'local',
                        listeners: {
                            change : function(){
                                // Ext.getCmp('Koltiva.view.Traceability_new.Transaction_neo.MainForm-FormBasicData-btnBayarDirectBatch').show();
                            }
                        }
                        // valueField:id
                    },
                    {
                        xtype: 'textfield',
                        id: 'Koltiva.view.Traceability_new.Transaction_neo.MainForm-FormBasicData-BankCodeDirectBatch',
                        name: 'Koltiva.view.Traceability_new.Transaction_neo.MainForm-FormBasicData-BankCodeDirectBatch',
                        fieldLabel: lang('Bank Code')
                    },
                    {
                        xtype: 'textfield',
                        id: 'Koltiva.view.Traceability_new.Transaction_neo.MainForm-FormBasicData-BankNameDirectBatch',
                        name: 'Koltiva.view.Traceability_new.Transaction_neo.MainForm-FormBasicData-BankNameDirectBatch',
                        fieldLabel: lang('Bank Name')
                    },
                    {
                        xtype: 'textfield',
                        id: 'Koltiva.view.Traceability_new.Transaction_neo.MainForm-FormBasicData-AccountNumberDirectBatch',
                        name: 'Koltiva.view.Traceability_new.Transaction_neo.MainForm-FormBasicData-AccountNumberDirectBatch',
                        fieldLabel: lang('Account Number')
                    },
                    {
                        xtype: 'textfield',
                        id: 'Koltiva.view.Traceability_new.Transaction_neo.MainForm-FormBasicData-AccountNameDirectBatch',
                        name: 'Koltiva.view.Traceability_new.Transaction_neo.MainForm-FormBasicData-AccountNameDirectBatch',
                        fieldLabel: lang('Account Name')
                    },
                    {
                        xtype:'button',
                        icon: varjs.config.base_url + 'images/icons/new/script_link_white.png',
                        hidden:true,
                        text: lang('Payment Instruction'),
                        id:'Koltiva.view.Traceability_new.Transaction_neo.MainForm-FormBasicData-btnPaymentInstructionDirectBatch',
                        margin: '5px',
                        cls:'Sfr_BtnFormGreen',
                        // overCls:'Sfr_BtnFormGreen-Hover',
                        handler: function() {
                           //preview_cetak_surat(m_api+'/disburse/disburse_premium/print_invoice/'+thisObj.viewVar.PremiumID);
                           var SupplyTransID = Ext.getCmp('Koltiva.view.Traceability_new.Transaction_neo.MainForm-FormBasicData-SupplyTransID').getValue(); 
                           
                           if(Ext.getCmp('Koltiva.view.Traceability_new.Transaction_neo.PaymentInstuction') == undefined){
                                var PanelPaymentIntruction = Ext.create('Koltiva.view.Traceability_new.Transaction_neo.PaymentInstuction', {
                                    opsiDisplay: 'insert',
                                    viewVar: { 
                                       SupplyTransID : SupplyTransID ,
                                       // SupplyBatchID  : thisObj.viewVar.SupplyBatchID,
                                       // SupplyTransID  : SupplyTransID,
                                       btnSave: true
                                    }
                                });
                            }else{
                                //destroy, create ulang
                                Ext.getCmp('Koltiva.view.Traceability_new.Transaction_neo.PaymentInstuction').destroy();
                                var PanelPaymentIntruction = Ext.create('Koltiva.view.Traceability_new.Transaction_neo.PaymentInstuction', {
                                    opsiDisplay: 'insert',
                                    viewVar: { 
                                       SupplyTransID : SupplyTransID ,
                                       btnSave: true
                                    }
                                });
                            }
                            if (!PanelPaymentIntruction.isVisible()) {
                               PanelPaymentIntruction.center();
                               PanelPaymentIntruction.show();
                            } else {
                               PanelPaymentIntruction.close();
                            } 
                        }
                    },
                    {
                        xtype:'button',
                        icon: varjs.config.base_url + 'images/icons/new/script_link_white.png',
                        hidden:true,
                        text: lang('Bayar'),
                        id: 'Koltiva.view.Traceability_new.Transaction_neo.MainForm-FormBasicData-btnBayarDirectBatch',
                        margin: '5px',
                        cls:'Sfr_BtnFormGreen',
                        // overCls:'Sfr_BtnFormGreen-Hover',
                        handler: function () {
                            Ext.MessageBox.confirm(lang('Confirmation'), lang('Are you sure ?'), function(btn) {
                                if (btn == 'yes') {
                                    var FormPayment = Ext.getCmp('Koltiva.view.Traceability_new.Transaction_neo.MainForm-FormBasicData').getForm();
                                    if (FormPayment.isValid()) {
                                        var method = 'POST';
                                        FormPayment.submit({
                                                url: m_api + '/traceability_api/web_transaction/submit_payment',
                                                method:method,
                                                waitMsg: lang('Processing ...'),
                                                params: {
                                                    SupplyTransID : Object.is(sessionStorage.getItem('setSupplyTransID'), null) == false ? sessionStorage.getItem('setSupplyTransID') : thisObj.viewVar.SupplyTransID
                                                },
                                                success: function (fp, o) {
                                                    var r = Ext.decode(fp.responseText);
                                                    Ext.MessageBox.show({
                                                        title: 'Information',
                                                        msg: lang('Submit Payment Success'),
                                                        buttons: Ext.MessageBox.OK,
                                                        animateTarget: 'mb9',
                                                        icon: 'ext-mb-success',
                                                        
                                                    });

                                                    Ext.getCmp('Koltiva.view.Traceability_new.Transaction_neo.MainForm-FormBasicData-btnBayarDirectBatch').hide();
                                                    Ext.getCmp('Koltiva.view.Traceability_new.Transaction_neo.MainForm-FormBasicData-btnPaymentInstructionDirectBatch').show();
                                                    Ext.getCmp('Koltiva.view.Traceability_new.Transaction_neo.MainForm-FormBasicData-btnPaymentInstructionDirectBatch').el.dom.click();
                                                },
                                                failure: function (fp, o) {
                                                    try {
                                                        var r = Ext.decode(o.response.responseText);
                                                        Ext.MessageBox.show({
                                                            title: 'Error',
                                                            msg: (r.error) ? r.error : r.message,
                                                            buttons: Ext.MessageBox.OK,
                                                            animateTarget: 'mb9',
                                                            icon: 'ext-mb-error'
                                                        });
                                                    } catch (err) {
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
                                            title: 'Attention',
                                            msg: lang('Form not valid yet'),
                                            buttons: Ext.MessageBox.OK,
                                            animateTarget: 'mb9',
                                            icon: 'ext-mb-info'
                                        });
                                    }
                                }
                            });
                        }
                    }]
                }]
            }]                       
        });
        //Panel Payment DirectBatch ==================================== (End)
        
        if (thisObj.viewVar.OpsiDisplay == 'view' || thisObj.viewVar.OpsiDisplay == 'update') {
            thisObj.ObjPanelDataUnit.show();
            thisObj.ObjPanelPayment.show();

            thisObj.ObjPanelDataUnitNonFarmer.hide();
            thisObj.ObjPanelPaymentNonFarmer.hide();

            thisObj.ObjPanelDataUnitDirectBatch.hide();
            thisObj.ObjPanelPaymentDirectBatch.hide();
        }  else {
            thisObj.ObjPanelDataUnitNonFarmer.show();
            thisObj.ObjPanelPaymentNonFarmer.hide();

            thisObj.ObjPanelDataUnitDirectBatch.show();
            thisObj.ObjPanelPaymentDirectBatch.hide();
        }

        var ComboSalesType = Ext.create('Ext.data.Store', {
            fields: ['id', 'label'],
            data : [
                {"label":lang('Farmer'), "id":'1'},
                {"label":lang('Non Farmer'), "id":'2'},
                {"label":lang('Direct Batch'), "id":'3'}
            ]
        });

		var ComboPlantationNonFarmer = Ext.create('Koltiva.store.Traceability_new.Transaction_neo.ComboPlantationNonFarmer');
		var ComboSellerMill = Ext.create('Koltiva.store.Traceability_new.Transaction.ComboSellerMill');
		var ComboSellerDO 	= Ext.create('Koltiva.store.Traceability_new.Transaction.ComboSellerDO');
		var ComboSellerAgent = Ext.create('Koltiva.store.Traceability_new.Transaction.ComboSellerAgent');

        var ComboSellerType = Ext.create('Ext.data.Store', {
            fields: ['id', 'label'],
            data : [
                {"label":lang('External Estate'), "id":'external'},
                {"label":lang('Other Supplier'), "id":'other'}
            ]
        });

        //Panel Basic ==================================== (Begin)
        thisObj.ObjPanelBasicData = Ext.create('Ext.panel.Panel', {
            title: lang('Buying Data'),
            frame: true,
            cls: 'Sfr_PanelLayoutForm',
            id: 'Koltiva.view.Traceability_new.Transaction_neo.MainForm-FormGeneralData',
            collapsible: true,
            items: [{
                    xtype: 'form',
                    id: 'Koltiva.view.Traceability_new.Transaction_neo.MainForm-FormBasicData',
                    buttonAlign: 'right',
                    cls: 'Sfr_PanelSubLayoutForm',
                    items: [{
                                xtype: 'panel',
                                title: lang('Basic Information'),
                                frame: false,
                                id: 'Koltiva.view.Traceability_new.Transaction_neo.MainForm-FormBasicData-Information',
                                style: 'margin-top:15px;margin-left:10px;margin-right:10px',
                                cls: 'Sfr_PanelSubLayoutFormRoundedGray',
                            },{
                                xtype: 'combobox',
                                id: 'Koltiva.view.Traceability_new.Transaction_neo.MainForm-FormBasicData-SalesType',
                                name: 'Koltiva.view.Traceability_new.Transaction_neo.MainForm-FormBasicData-SalesType',
                                store: ComboSalesType, 
                                // labelWidth:200, 
                                fieldLabel: lang('Sales Type'), 
                                style: 'margin-top:15px;margin-left:10px;margin-right:10px',
                                queryMode: 'local',
                                displayField: 'label',
                                labelAlign:'left',
                                width: 500,
                                valueField: 'id',
                                typeAhead: true, 
                                disableKeyFilter : true,
                                triggerAction : 'all', 
                                listeners:{
                                    select: function(combo, records, eOpts) {
                                        if(records[0].data.id == 1){
                                            
                                            Ext.getCmp('Koltiva.view.Traceability_new.Transaction_neo.MainForm-FormBasicData-SectionFarmerProfile').setVisible(true);
                                            Ext.getCmp('Koltiva.view.Traceability_new.Transaction_neo.MainForm-FormBasicData-SectionNonFarmer').setVisible(false);
                                            Ext.getCmp('Koltiva.view.Traceability_new.Transaction_neo.MainForm-FormBasicData-DirectBatch').setVisible(false); 
											Ext.getCmp('Koltiva.view.Traceability_new.Transaction_neo.MainForm-FormBasicData-SellerType').allowBlank = true;

                                            Ext.getCmp('Koltiva.view.Traceability_new.Transaction_neo.MainForm-FormBasicData-PlantationNrNonFarmer').allowBlank = true;

                                            thisObj.ObjPanelDataUnitNonFarmer.hide();
                                            thisObj.ObjPanelPaymentNonFarmer.hide();

                                            thisObj.ObjPanelDataUnitDirectBatch.hide();
                                            thisObj.ObjPanelPaymentDirectBatch.hide();
                                        } else if (records[0].data.id == 2) {
                                            
                                            Ext.getCmp('Koltiva.view.Traceability_new.Transaction_neo.MainForm-FormBasicData-SectionFarmerProfile').setVisible(false);
                                            Ext.getCmp('Koltiva.view.Traceability_new.Transaction_neo.MainForm-FormBasicData-SectionNonFarmer').setVisible(true);
                                            Ext.getCmp('Koltiva.view.Traceability_new.Transaction_neo.MainForm-FormBasicData-DirectBatch').setVisible(false); 
                                            Ext.getCmp('Koltiva.view.Traceability_new.Transaction_neo.MainForm-FormBasicData-PlantationNrNonFarmer').allowBlank = true;

                                            ComboPlantationNonFarmer.load({params : {'SupplychainID' : m_sid } });
                                            Ext.getCmp('Koltiva.view.Traceability_new.Transaction_neo.MainForm-FormBasicData-PlantationNrNonFarmer').setReadOnly(false);
                                            Ext.getCmp('Koltiva.view.Traceability_new.Transaction_neo.MainForm-FormBasicData-SellerType').allowBlank = true;

                                            thisObj.ObjPanelDataUnitNonFarmer.show();
                                            thisObj.ObjPanelPaymentNonFarmer.hide();

                                            thisObj.ObjPanelDataUnitDirectBatch.hide();
                                            thisObj.ObjPanelPaymentDirectBatch.hide();

                                            thisObj.ObjPanelDataUnit.hide();
                                            thisObj.ObjPanelPayment.hide();
                                        } else {
                                            
                                            Ext.getCmp('Koltiva.view.Traceability_new.Transaction_neo.MainForm-FormBasicData-SectionFarmerProfile').setVisible(false);
                                            Ext.getCmp('Koltiva.view.Traceability_new.Transaction_neo.MainForm-FormBasicData-SectionNonFarmer').setVisible(false);
                                            Ext.getCmp('Koltiva.view.Traceability_new.Transaction_neo.MainForm-FormBasicData-DirectBatch').setVisible(true); 
                                            // Ext.getCmp('Koltiva.view.Traceability_new.Transaction_neo.MainForm-FormBasicData-PlantationNr').allowBlank = true;
                                            Ext.getCmp('Koltiva.view.Traceability_new.Transaction_neo.MainForm-FormBasicData-SellerType').setReadOnly(false);
                                            Ext.getCmp('Koltiva.view.Traceability_new.Transaction_neo.MainForm-FormBasicData-SellerType').allowBlank = false;

                                            Ext.getCmp('Koltiva.view.Traceability_new.Transaction_neo.MainForm-FormBasicData-PlantationNrNonFarmer').allowBlank = true;

                                            thisObj.ObjPanelDataUnitNonFarmer.hide();
                                            thisObj.ObjPanelPaymentNonFarmer.hide();

                                            thisObj.ObjPanelDataUnitDirectBatch.show();
                                            thisObj.ObjPanelPaymentDirectBatch.hide();

                                            thisObj.ObjPanelDataUnit.hide();
                                            thisObj.ObjPanelPayment.hide();
                                        }
                                        return false;
                                    }
                                }
                            },
                            {
                                xtype: 'datefield',
                                fieldLabel: lang('Buying Date'),
                                style: 'margin-top:15px;margin-left:10px;margin-right:10px',
                                width: 500,
                                labelAlign:'left',
                                format: 'Y-m-d H:i:s',
                                id: 'Koltiva.view.Traceability_new.Transaction_neo.MainForm-FormBasicData-DateTransaction',
                                name: 'Koltiva.view.Traceability_new.Transaction_neo.MainForm-FormBasicData-DateTransaction',
                                value: m_now,
                            },
                            {
                                xtype: 'panel',
                                title: lang('Farmer Buying'),
                                frame: false,
                                id: 'Koltiva.view.Traceability_new.Transaction_neo.MainForm-FormBasicData-SectionFarmerProfile',
                                style: 'margin-top:15px;margin-left:10px;margin-right:10px',
                                cls: 'Sfr_PanelSubLayoutFormRoundedGray',
                                items: [{
                                    layout: 'column',
                                    border: false,
                                    items: [{
                                        columnWidth: 0.13,
                                        layout: 'form',
                                        style: 'padding:10px 0px 10px 5px;',
                                        items: [{
                                                xtype: 'panel',
                                                id: 'Koltiva.view.Traceability_new.Transaction_neo.MainForm-FormBasicData-PhotoShow',
                                                html: '<img src="' + m_api_base_url + '/assets/images/farmer-default.png" style="height:150px;margin:0px 5px 5px 0px;float:left;" />'
                                            }, {
                                                xtype: 'textfield',
                                                id: 'Koltiva.view.Traceability_new.Transaction_neo.MainForm-FormBasicData-Photo',
                                                name: 'Koltiva.view.Traceability_new.Transaction_neo.MainForm-FormBasicData-Photo',
                                                inputType: 'hidden'
                                            },
                                            {
                                                xtype: 'textfield',
                                                id: 'Koltiva.view.Traceability_new.Transaction_neo.MainForm-FormBasicData-ProvinceID',
                                                name: 'Koltiva.view.Traceability_new.Transaction_neo.MainForm-FormBasicData-ProvinceID',
                                                inputType: 'hidden'
                                            },
                                            {
                                                xtype: 'textfield',
                                                id: 'Koltiva.view.Traceability_new.Transaction_neo.MainForm-FormBasicData-Status',
                                                name: 'Koltiva.view.Traceability_new.Transaction_neo.MainForm-FormBasicData-Status',
                                                inputType: 'hidden'
                                            }]
                                        }, {
                                            columnWidth: 0.435,
                                            layout: 'form',
                                            style: 'padding:10px 5px 10px 20px;',
                                            defaults: {
                                                labelAlign: 'left',
                                                labelWidth: 150
                                            },
                                            items: [
                                            {
                                                xtype: 'textfield',
                                                id: 'Koltiva.view.Traceability_new.Transaction_neo.MainForm-FormBasicData-SupplyTransID',
                                                name: 'Koltiva.view.Traceability_new.Transaction_neo.MainForm-FormBasicData-SupplyTransID',
                                                inputType: 'hidden'
                                            },{
                                                xtype: 'textfield',
                                                id: 'Koltiva.view.Traceability_new.Transaction_neo.MainForm-FormBasicData-MemberID',
                                                name: 'Koltiva.view.Traceability_new.Transaction_neo.MainForm-FormBasicData-MemberID',
                                                inputType: 'hidden'
                                            },{
                                                xtype: 'combo',
                                                store: thisObj.StoreFarmers,
                                                id: 'Koltiva.view.Traceability_new.Transaction_neo.MainForm-FormBasicData-MemberDisplayID',
                                                name: 'Koltiva.view.Traceability_new.Transaction_neo.MainForm-FormBasicData-MemberDisplayID',
                                                displayField: 'MemberDisplayID',
                                                fieldLabel: lang('Member ID'),
                                                typeAhead: false,
                                                hideTrigger:true,
                                                queryCaching:false,
                                                minChars: 1,
                                                emptyText: lang('Search by Name/ID'),
                                                anchor: '100%',
                                                allowBlank: true,
                                                baseCls: 'Sfr_FormInputMandatory',
                                                listConfig: {
                                                    loadingText: 'Searching...',
                                                    emptyText: 'No matching farmer found.',
                                                    // Custom rendering template for each item
                                                    getInnerTpl: function() {
                                                    return '<div class="search-item">' +
                                                        '{MemberDisplayID} - <b>{MemberName}</b><br>' +
                                                        'District : <b>{District}</b><br>' +
                                                        'Location : <b>{Village}, {SubDistrict}</b><br><hr>' +
                                                    '</div>';
                                                    }
                                                },
                                                pageSize: 10,
                                                // override default onSelect to do redirect
                                                listeners: {
                                                    change : function(val)
                                                    {
                                                        if(val.getValue()!= null){  
                                                            ComboPlantation.load({params : {'MemberID' : val.getValue() } });
                                                        }
                                                    },				
                                                    select: function(combo, selection) {

                                                        var post = selection[0];
                                                        if (post) {
                                                                
                                                            Ext.getCmp('Koltiva.view.Traceability_new.Transaction_neo.MainForm-FormBasicData-MemberID').setValue(post.raw.MemberID);
                                                            Ext.getCmp('Koltiva.view.Traceability_new.Transaction_neo.MainForm-FormBasicData-MemberDisplayID').setValue(post.raw.MemberDisplayID);
                                                            Ext.getCmp('Koltiva.view.Traceability_new.Transaction_neo.MainForm-FormBasicData-MemberName').setValue(post.raw.MemberName);
                                                            Ext.getCmp('Koltiva.view.Traceability_new.Transaction_neo.MainForm-FormBasicData-isCertified').setValue(post.raw.isCertified);
                                                            Ext.getCmp('Koltiva.view.Traceability_new.Transaction_neo.MainForm-FormBasicData-Gender').setValue(post.raw.Gender);
                                                            Ext.getCmp('Koltiva.view.Traceability_new.Transaction_neo.MainForm-FormBasicData-Age').setValue(post.raw.Age);
                                                            Ext.getCmp('Koltiva.view.Traceability_new.Transaction_neo.MainForm-FormBasicData-FarmerCategory').setValue(post.raw.FarmerCategory);
                                                            Ext.getCmp('Koltiva.view.Traceability_new.Transaction_neo.MainForm-FormBasicData-PlantationNr').setValue('1');
                                                            Ext.getCmp('Koltiva.view.Traceability_new.Transaction_neo.MainForm-FormBasicData-Latitude').setValue(post.raw.Latitude);
                                                            Ext.getCmp('Koltiva.view.Traceability_new.Transaction_neo.MainForm-FormBasicData-Longitude').setValue(post.raw.Longitude);
                                                            Ext.getCmp('Koltiva.view.Traceability_new.Transaction_neo.MainForm-FormBasicData-PartnerID').setValue(post.raw.PartnerID);
                                                            Ext.getCmp('Koltiva.view.Traceability_new.Transaction_neo.MainForm-FormBasicData-Region').setValue('Country: ' + post.raw.CountryName + ', Province: ' + post.raw.Province + ', District: ' + post.raw.District +', Sub District: ' + post.raw.SubDistrict +', Village: '+ post.raw.Village);
                                                            Ext.getCmp('Koltiva.view.Traceability_new.Transaction_neo.MainForm-FormBasicData-Address').setValue(post.raw.Address);

                                                            if(post.raw.Photo != ""){
                                                                var fotoUser = m_api_base_url + '/images/member/'+post.raw.ProvinceID+'/'+ post.raw.Photo;
                                                                checkImageExists(fotoUser, function(existsImage) {
                                                                        if (existsImage == true) {
                                                                            Ext.getCmp('Koltiva.view.Traceability_new.Transaction_neo.MainForm-FormBasicData-PhotoShow').update('<img src="' + fotoUser + '" style="height:150px;margin:0px 5px 5px 0px;float:left;" />');
                                                                        } else {
                                                                            Ext.getCmp('Koltiva.view.Traceability_new.Transaction_neo.MainForm-FormBasicData-PhotoShow').update('<img src="' + m_api_base_url + '/assets/images/farmer-default.png" style="height:150px;margin:0px 5px 5px 0px;float:left;" />');
                                                                        }
                                                                });
                                                            } else {
                                                                Ext.getCmp('Koltiva.view.Traceability_new.Transaction_neo.MainForm-FormBasicData-PhotoShow').update('<img src="' + m_api_base_url + '/assets/images/farmer-default.png" style="height:150px;margin:0px 5px 5px 0px;float:left;" />');
                                                            }

                                                            thisObj.ObjPanelDataUnit.setViewVar({
                                                                SupplyTransID : null,
                                                                MemberID : post.raw.MemberID
                                                            });

                                                            if(m_IsPaymentMethod != 1){
                                                                thisObj.ObjPanelPayment.hide();
                                                            }
                                                            else{
                                                                thisObj.ObjPanelPayment.show();
                                                            }

                                                            thisObj.ObjPanelDataUnit.show();
                                                            
                                                        }
                                                    }
                                                }, 
                                            },
                                            {
                                                xtype: 'textfield',
                                                id: 'Koltiva.view.Traceability_new.Transaction_neo.MainForm-FormBasicData-MemberName',
                                                name: 'Koltiva.view.Traceability_new.Transaction_neo.MainForm-FormBasicData-MemberName',
                                                readOnly: true,
                                                fieldLabel: lang('Name')
                                            },
                                            {
                                                xtype: 'combo',
                                                id: 'Koltiva.view.Traceability_new.Transaction_neo.MainForm-FormBasicData-PlantationNr',
                                                name: 'Koltiva.view.Traceability_new.Transaction_neo.MainForm-FormBasicData-PlantationNr',
                                                allowBlank : true,
                                                readOnly:false,
                                                store:  ComboPlantation,
                                                labelWidth:200, 
                                                fieldLabel: lang('Garden'), 
                                                queryMode: 'local',
                                                displayField: 'PlantationNr',
                                                valueField: 'PlantationNr',
                                                typeAhead: true, 
                                                disableKeyFilter : true,
                                                triggerAction : 'all',
                                                listeners : { 
                                                    change: function(combo, /* Array */ value){ 
                                                        // var records = combo.store.findRecord('PlantationNr', value); 
                                                        //alert(rec.get('FarmingType'))
                                                    } 							
                                                },onFocus: function() {
                                                    var me = this;
                                                
                                                    if (!me.isExpanded) {
                                                        me.expand()
                                                    }
                                                    me.getPicker().focus();
                                                }
                                            },
                                            {
                                                xtype: 'textfield',
                                                id: 'Koltiva.view.Traceability_new.Transaction_neo.MainForm-FormBasicData-isCertified',
                                                name: 'Koltiva.view.Traceability_new.Transaction_neo.MainForm-FormBasicData-isCertified',
                                                readOnly: true,
                                                fieldLabel: lang('Certified')
                                            },
                                            {
                                                xtype: 'textfield',
                                                id: 'Koltiva.view.Traceability_new.Transaction_neo.MainForm-FormBasicData-Gender',
                                                name: 'Koltiva.view.Traceability_new.Transaction_neo.MainForm-FormBasicData-Gender',
                                                readOnly: true,
                                                fieldLabel: lang('Gender')
                                            },
                                            {
                                                xtype: 'textfield',
                                                id: 'Koltiva.view.Traceability_new.Transaction_neo.MainForm-FormBasicData-Age',
                                                name: 'Koltiva.view.Traceability_new.Transaction_neo.MainForm-FormBasicData-Age',
                                                readOnly: true,
                                                fieldLabel: lang('Age')
                                            },
                                            {
                                                xtype: 'textfield',
                                                id: 'Koltiva.view.Traceability_new.Transaction_neo.MainForm-FormBasicData-FarmerCategory',
                                                name: 'Koltiva.view.Traceability_new.Transaction_neo.MainForm-FormBasicData-FarmerCategory',
                                                inputType: 'hidden'
                                            },
                                            {
                                                xtype: 'textfield',
                                                id: 'Koltiva.view.Traceability_new.Transaction_neo.MainForm-FormBasicData-Latitude',
                                                name: 'Koltiva.view.Traceability_new.Transaction_neo.MainForm-FormBasicData-Latitude',
                                                inputType: 'hidden'
                                            }, 
                                            {
                                                xtype: 'textfield',
                                                id: 'Koltiva.view.Traceability_new.Transaction_neo.MainForm-FormBasicData-Longitude',
                                                name: 'Koltiva.view.Traceability_new.Transaction_neo.MainForm-FormBasicData-Longitude',
                                                inputType: 'hidden'
                                            }, 
                                            {
                                                xtype: 'combobox',
                                                id: 'Koltiva.view.Traceability_new.Transaction_neo.MainForm-FormBasicData-PartnerID',
                                                name: 'Koltiva.view.Traceability_new.Transaction_neo.MainForm-FormBasicData-PartnerID',
                                                store: thisObj.cmb_partner,
                                                fieldLabel: lang('Partner'),
                                                queryMode: 'local',
                                                displayField: 'label',
                                                valueField: 'id',
                                                readOnly: true,
                                                hidden : true
                                            }]
                                        }, {
                                            columnWidth: 0.435,
                                            layout: 'form',
                                            style: 'padding:10px 0px 10px 20px;',
                                            items: [{
                                                xtype: 'textareafield',
                                                id: 'Koltiva.view.Traceability_new.Transaction_neo.MainForm-FormBasicData-Region',
                                                name: 'Koltiva.view.Traceability_new.Transaction_neo.MainForm-FormBasicData-Region',
                                                readOnly: true,
                                                fieldLabel: lang('Region')
                                            },  {
                                                xtype: 'textareafield',
                                                id: 'Koltiva.view.Traceability_new.Transaction_neo.MainForm-FormBasicData-Address',
                                                name: 'Koltiva.view.Traceability_new.Transaction_neo.MainForm-FormBasicData-Address',
                                                readOnly: true,
                                                fieldLabel: lang('Address')
                                            }]
                                        }
                                    ]
                                }, 
                                {
                                    layout: 'column',
                                    border: false,
                                    items: [{
                                        //LEFT CONTENT
                                        columnWidth: 0.40,
                                        items: [
                                            thisObj.ObjPanelDataUnit
                                        ]
                                    }, {
                                        //RIGHT CONTENT
                                        columnWidth: 0.60,
                                        items: [
                                            thisObj.ObjPanelPayment
                                        ]
                                    }]
                                }
                            ]
                            },
                            {
                                columnWidth:1,
                                layout: 'form',
                                style: 'padding:10px 5px 10px 20px;',
                                defaults: {
                                    labelAlign: 'left',
                                    labelWidth: 150
                                },
                                id: 'Koltiva.view.Traceability_new.Transaction_neo.MainForm-FormBasicData-DirectBatch',	
                                style: 'margin-top:15px;margin-left:10px;margin-right:10px',
                                hidden:true,
                                items:[{
                                    xtype: 'label', 
                                    html:'<div class="companyLabel" style="background-color: #848D98 !important;">'+lang('Batch')+'</div>'
                                },{
                                    xtype: 'combobox',
                                    id: 'Koltiva.view.Traceability_new.Transaction_neo.MainForm-FormBasicData-SellerType',
                                    name: 'Koltiva.view.Traceability_new.Transaction_neo.MainForm-FormBasicData-SellerType',
                                    allowBlank : true,
                                    readOnly:true,
                                    store: ComboSellerType, 
                                    fieldLabel: lang('Seller Type'), 
                                    queryMode: 'local',
                                    displayField: 'label',
                                    labelAlign:'top',
                                    valueField: 'id',
                                    typeAhead: true, 
                                    disableKeyFilter : true,
                                    triggerAction : 'all', 
                                    listeners : { 
                                        change: function(combo, /* Array */ value){ 
                                            if (typeof combo.store.findRecord('id', value) !== 'undefined') {
                                                var records = combo.store.findRecord('id', value);
                                                if(records){
                                                    Ext.getCmp('Koltiva.view.Traceability_new.Transaction_neo.PanelDataUnit').show()
                                                    Ext.getCmp('Koltiva.view.Traceability_new.Transaction_neo.MainForm-FormBasicData-SectionTransactionInfoData').show()

                                                    Ext.getCmp('Koltiva.view.Traceability_new.Transaction_neo.PanelDataUnitNonFarmer').show()
                                                    Ext.getCmp('Koltiva.view.Traceability_new.Transaction_neo.MainForm-FormBasicData-SectionTransactionInfoDataNonFarmer').show()
                                                    
                                                    if(records.data.id == "external"){
                                                        Ext.getCmp('Koltiva.view.Traceability_new.Transaction.FormTransaction-Mill').setVisible(true);
                                                        Ext.getCmp('OtherMill').setVisible(true);
                                                        ComboSellerMill.load({params : {'SupplychainID' : m_sid } });
                                                        Ext.getCmp('Koltiva.view.Traceability_new.Transaction.FormTransaction-DO').setVisible(false);
                                                        Ext.getCmp('OtherDO').setVisible(false);
                                                        Ext.getCmp('Koltiva.view.Traceability_new.Transaction.FormTransaction-Agent').setVisible(false);
                                                        Ext.getCmp('Koltiva.view.Traceability_new.Transaction.FormTransaction-OtherDOName').setVisible(false);
                                                        Ext.getCmp('Koltiva.view.Traceability_new.Transaction.FormTransaction-OtherAgentName').setVisible(false);
                                                        Ext.getCmp('Koltiva.view.Traceability_new.Transaction.FormTransaction-OtherAgentNin').setVisible(false);
                                                        Ext.getCmp('OtherAgentSurvey').setVisible(false);
                                                        Ext.getCmp('OtherAgent').setVisible(false);
                                                    }else{
                                                        Ext.getCmp('Koltiva.view.Traceability_new.Transaction.FormTransaction-Mill').setVisible(false);
                                                        Ext.getCmp('Koltiva.view.Traceability_new.Transaction.FormTransaction-OtherMillName').setVisible(false);
                                                        Ext.getCmp('OtherMill').setVisible(false);
                                                        Ext.getCmp('Koltiva.view.Traceability_new.Transaction.FormTransaction-DO').setVisible(true);
                                                        Ext.getCmp('OtherDO').setVisible(true);
                                                        ComboSellerDO.load({params : {'SupplychainID' : m_sid } });
                                                        Ext.getCmp('Koltiva.view.Traceability_new.Transaction.FormTransaction-Agent').setVisible(true);
                                                        Ext.getCmp('OtherAgent').setVisible(true);
                                                    }
                                                }
                                            }
                                        } 							
                                    },onFocus: function() {
                                        var me = this;
                                    
                                        if (!me.isExpanded) {
                                            me.expand()
                                        }
                                        me.getPicker().focus();
                                    }
                                },{
                                    layout: 'column',
                                    border: false,
                                    columnWidth:1,
                                    items:[{
                                        columnWidth: 0.6,
                                        layout:'form',
                                        style:'padding-right:25px;',
                                        items:[{
                                            xtype: 'combobox',
                                            id: 'Koltiva.view.Traceability_new.Transaction.FormTransaction-Mill',
                                            name: 'Koltiva.view.Traceability_new.Transaction.FormTransaction-Mill',
                                            allowBlank : true,
                                            hidden:true,
                                            labelAlign:'top',
                                            store: ComboSellerMill, 
                                            labelWidth:200, 
                                            fieldLabel: lang('Mill'), 
                                            queryMode: 'local',
                                            displayField: 'Name',
                                            valueField: 'ObjID',
                                            typeAhead: true, 
                                            disableKeyFilter : true,
                                            triggerAction : 'all', 
                                            listeners : { 
                                                change: function(combo, /* Array */ value){ 
                                                    // var records = combo.store.findRecord('id', value); 
                                                    // console.log(records.data.id);
                                                
                                                } 							
                                            },onFocus: function() {
                                                var me = this;
                                            
                                                if (!me.isExpanded) {
                                                    me.expand()
                                                }
                                                me.getPicker().focus();
                                            }
                                        }]
                                    },
                                    {
                                        columnWidth:0.38,
                                        layout:'column',			
                                        // style: 'padding-right:10px;',
                                        items:[{
                                            xtype: 'fieldcontainer',
                                            fieldLabel: lang('Other MIll'),
                                            defaultType: 'checkboxfield',
                                            labelAlign:'top',
                                            id:'OtherMill',
                                            hidden: true,
                                            items: [
                                                {
                                                    boxLabel  : lang('Yes'),
                                                    name      : 'Koltiva.view.Traceability_new.Transaction.FormTransaction-OtherMill',
                                                    inputValue: '1',
                                                    id        : 'Koltiva.view.Traceability_new.Transaction.FormTransaction-OtherMill',
                                                    listeners:{
                                                        change: function(checkbox, newValue, oldValue, eOpts) {
                                                            if(newValue){
                                                                Ext.getCmp('Koltiva.view.Traceability_new.Transaction.FormTransaction-OtherMillName').setVisible(true);
                                                                Ext.getCmp('Koltiva.view.Traceability_new.Transaction.FormTransaction-Mill').setReadOnly(true);
                                                                Ext.getCmp('Koltiva.view.Traceability_new.Transaction.FormTransaction-Mill').setValue('');
                                                            }else{
                                                                Ext.getCmp('Koltiva.view.Traceability_new.Transaction.FormTransaction-OtherMillName').setVisible(false);
                                                                Ext.getCmp('Koltiva.view.Traceability_new.Transaction.FormTransaction-Mill').setReadOnly(false);
                                                                Ext.getCmp('Koltiva.view.Traceability_new.Transaction.FormTransaction-Mill').setValue('');
                                                            }
                                                        }
                                                    }
                                                }
                                            ]
                                        }]
                                    }]
                                },
                                {
                                    xtype: 'textfield',
                                    id: 'Koltiva.view.Traceability_new.Transaction.FormTransaction-OtherMillName',
                                    name: 'Koltiva.view.Traceability_new.Transaction.FormTransaction-OtherMillName',
                                    fieldLabel: lang('Other MIll Name'),
                                    labelAlign:'top',
                                    hidden:true,
                                    listeners :{
                                        change:function(val){
                                                
                                        }
                                    } 
                                    
                                },{
                                    layout: 'column',
                                    border: false,
                                    items:[{
                                        columnWidth: 0.6,
                                        layout:'form',
                                        style:'padding-right:25px;',
                                        items:[{
                                            xtype: 'combobox',
                                            id: 'Koltiva.view.Traceability_new.Transaction.FormTransaction-DO',
                                            name: 'Koltiva.view.Traceability_new.Transaction.FormTransaction-DO',
                                            allowBlank : true,
                                            hidden:true,
                                            labelAlign:'top',
                                            store: ComboSellerDO, 
                                            labelWidth:200, 
                                            fieldLabel: lang('DO'), 
                                            queryMode: 'local',
                                            displayField: 'Name',
                                            valueField: 'ObjID',
                                            typeAhead: true, 
                                            disableKeyFilter : true,
                                            triggerAction : 'all', 
                                            listeners : { 
                                                change: function(combo, /* Array */ value){ 
                                                    if (typeof combo.store.findRecord('ObjID', value) !== 'undefined') {
                                                        var records = combo.store.findRecord('ObjID', value); 
                                                        // console.log(records.data);
                                                        
                                                        ComboSellerAgent.load({params : {'SupplychainID' : records.data.ObjID } });
                                                    }												
                                                } 							
                                            },onFocus: function() {
                                                var me = this;
                                            
                                                if (!me.isExpanded) {
                                                    me.expand()
                                                }
                                                me.getPicker().focus();
                                            }
                                        }]
                                    },{
                                        columnWidth:0.38,
                                        layout:'form',				
                                        // style: 'padding-right:10px;',
                                        items:[{
                                            xtype: 'fieldcontainer',
                                            fieldLabel: lang('Other DO'),
                                            defaultType: 'checkboxfield',
                                            labelAlign:'top',
                                            id:'OtherDO',
                                            hidden: true,
                                            items: [
                                                {
                                                    boxLabel  : lang('Yes'),
                                                    name      : 'Koltiva.view.Traceability_new.Transaction.FormTransaction-OtherDO',
                                                    inputValue: '1',
                                                    id        : 'Koltiva.view.Traceability_new.Transaction.FormTransaction-OtherDO',
                                                    listeners:{
                                                        change: function(checkbox, newValue, oldValue, eOpts) {
                                                            if(newValue){
                                                                Ext.getCmp('Koltiva.view.Traceability_new.Transaction.FormTransaction-OtherDOName').setVisible(true);
                                                                Ext.getCmp('Koltiva.view.Traceability_new.Transaction.FormTransaction-DO').setReadOnly(true);
                                                                Ext.getCmp('Koltiva.view.Traceability_new.Transaction.FormTransaction-DO').setValue('');
                                                                Ext.getCmp('Koltiva.view.Traceability_new.Transaction.FormTransaction-OtherAgent').setValue("1");
                                                            }else{
                                                                Ext.getCmp('Koltiva.view.Traceability_new.Transaction.FormTransaction-OtherDOName').setVisible(false);
                                                                Ext.getCmp('Koltiva.view.Traceability_new.Transaction.FormTransaction-DO').setReadOnly(false);
                                                                Ext.getCmp('Koltiva.view.Traceability_new.Transaction.FormTransaction-DO').setValue('');
                                                            }
                                                        }
                                                    }
                                                }
                                            ]
                                        }]
                                    }]
                                },
                                {
                                    xtype: 'textfield',
                                    id: 'Koltiva.view.Traceability_new.Transaction.FormTransaction-OtherDOName',
                                    name: 'Koltiva.view.Traceability_new.Transaction.FormTransaction-OtherDOName',
                                    fieldLabel: lang('Other DO Name'),
                                    labelAlign:'top',
                                    hidden:true,
                                    listeners :{
                                        change:function(val){
                                            
                                        }
                                    } 
                                    
                                },{
                                    layout: 'column',
                                    border: false,
                                    items:[{
                                        columnWidth: 0.6,
                                        layout:'form',
                                        style:'padding-right:25px;',
                                        items:[{
                                            xtype: 'combobox',
                                            id: 'Koltiva.view.Traceability_new.Transaction.FormTransaction-Agent',
                                            name: 'Koltiva.view.Traceability_new.Transaction.FormTransaction-Agent',
                                            allowBlank : true,
                                            hidden:true,
                                            labelAlign:'top',
                                            store: ComboSellerAgent, 
                                            labelWidth:200, 
                                            fieldLabel: lang('Agent'), 
                                            queryMode: 'local',
                                            displayField: 'Name',
                                            valueField: 'ObjID',
                                            typeAhead: true, 
                                            disableKeyFilter : true,
                                            triggerAction : 'all', 
                                            listeners : { 
                                                change: function(combo, /* Array */ value){ 
                                                    // var records = combo.store.findRecord('id', value); 
                                                    // console.log(records.data.id);
                                                
                                                } 							
                                            },onFocus: function() {
                                                var me = this;
                                            
                                                if (!me.isExpanded) {
                                                    me.expand()
                                                }
                                                me.getPicker().focus();
                                            }
                                        }]
                                    },{
                                        columnWidth:0.38,
                                        layout:'form',		
                                        // style: 'padding-right:10px;',
                                        items:[{
                                            xtype: 'fieldcontainer',
                                            fieldLabel: lang('Other Agent'),
                                            defaultType: 'checkboxfield',
                                            labelAlign:'top',
                                            id:'OtherAgent',
                                            hidden: true,
                                            items: [
                                                {
                                                    boxLabel  : lang('Yes'),
                                                    name      : 'Koltiva.view.Traceability_new.Transaction.FormTransaction-OtherAgent',
                                                    inputValue: '1',
                                                    id        : 'Koltiva.view.Traceability_new.Transaction.FormTransaction-OtherAgent',
                                                    listeners:{
                                                        change: function(checkbox, newValue, oldValue, eOpts) {
                                                            if(newValue){
                                                                Ext.getCmp('Koltiva.view.Traceability_new.Transaction.FormTransaction-OtherAgentName').setVisible(true);
                                                                Ext.getCmp('Koltiva.view.Traceability_new.Transaction.FormTransaction-OtherAgentNin').setVisible(true);
                                                                Ext.getCmp('OtherAgentSurvey').setVisible(true);
                                                                Ext.getCmp('Koltiva.view.Traceability_new.Transaction.FormTransaction-Agent').setReadOnly(true);
                                                                Ext.getCmp('Koltiva.view.Traceability_new.Transaction.FormTransaction-Agent').setValue('');
                                                            }else{
                                                                Ext.getCmp('Koltiva.view.Traceability_new.Transaction.FormTransaction-OtherAgentName').setVisible(false);
                                                                Ext.getCmp('Koltiva.view.Traceability_new.Transaction.FormTransaction-OtherAgentNin').setVisible(false);
                                                                Ext.getCmp('OtherAgentSurvey').setVisible(false);
                                                                Ext.getCmp('Koltiva.view.Traceability_new.Transaction.FormTransaction-Agent').setReadOnly(false);
                                                                Ext.getCmp('Koltiva.view.Traceability_new.Transaction.FormTransaction-Agent').setValue('');
                                                            }
                                                        }
                                                    }
                                                }
                                            ]
                                        }]
                                    }]
                                },
                                {
                                    xtype: 'textfield',
                                    id: 'Koltiva.view.Traceability_new.Transaction.FormTransaction-OtherAgentName',
                                    name: 'Koltiva.view.Traceability_new.Transaction.FormTransaction-OtherAgentName',
                                    fieldLabel: lang('Other Agent Name'),
                                    labelAlign:'top',
                                    hidden:true									
                                },
                                {
                                    xtype: 'textfield',
                                    id: 'Koltiva.view.Traceability_new.Transaction.FormTransaction-OtherAgentNin',
                                    name: 'Koltiva.view.Traceability_new.Transaction.FormTransaction-OtherAgentNin',
                                    fieldLabel: lang('Agent Nin'),
                                    labelAlign:'top',
                                    hidden:true									
                                },{
                                    xtype: 'fieldcontainer',
                                    fieldLabel: lang('Agents want to be surveyed later'),
                                    defaultType: 'checkboxfield',
                                    labelWidth:'250',
                                    id:'OtherAgentSurvey',
                                    labelAlign:'top',
                                    hidden: true,
                                    items: [
                                        {
                                            name      : 'Koltiva.view.Traceability_new.Transaction.FormTransaction-OtherAgentSurvey',
                                            inputValue: '1',
                                            id        : 'Koltiva.view.Traceability_new.Transaction.FormTransaction-OtherAgentSurvey'
                                        }
                                    ]
                                }
                            ]
                            },
                            {
                                layout: 'column',
                                border: false,
                                items: [{
                                    //LEFT CONTENT
                                    columnWidth: 0.40,
                                    items: [
                                        thisObj.ObjPanelDataUnitDirectBatch
                                    ]
                                }, {
                                    //RIGHT CONTENT
                                    columnWidth: 0.60,
                                    items: [
                                        thisObj.ObjPanelPaymentDirectBatch
                                    ]
                                }]
                            },
                            {
                                columnWidth:1,
                                id: 'Koltiva.view.Traceability_new.Transaction_neo.MainForm-FormBasicData-SectionNonFarmer',	
                                hidden:true,				
                                layout: 'form',
                                style: 'margin-top:15px;margin-left:10px;margin-right:10px',
                                defaults: {
                                    labelAlign: 'left',
                                    labelWidth: 150
                                },
                                items:[
                                    {   
                                        xtype: 'label', 
                                        html:'<div class="companyLabel" style="background-color: #848D98 !important;">'+lang('Non Farmer')+'</div>'
                                    },
                                    {
                                        xtype: 'combobox',
                                        id: 'Koltiva.view.Traceability_new.Transaction_neo.MainForm-FormBasicData-PlantationNrNonFarmer',
                                        name: 'Koltiva.view.Traceability_new.Transaction_neo.MainForm-FormBasicData-PlantationNrNonFarmer',
                                        allowBlank : true,
                                        readOnly:true,
                                        store: ComboPlantationNonFarmer, 
                                        fieldLabel: lang('Farm Number'), 
                                        queryMode: 'local',
                                        displayField: 'PlantationName',
                                        valueField: 'PlantationName',
                                        typeAhead: true, 
                                        disableKeyFilter : true,
                                        triggerAction : 'all', 
                                        listeners : { 
                                            change: function(combo, /* Array */ value){ 
                                            var records = combo.store.findRecord('PlantationName', value); 
                                                //alert(rec.get('FarmingType'))

                                            Ext.getCmp('Koltiva.view.Traceability_new.Transaction_neo.PanelDataUnit').show()
                                            Ext.getCmp('Koltiva.view.Traceability_new.Transaction_neo.MainForm-FormBasicData-SectionTransactionInfoData').show()
                                            Ext.getCmp('Koltiva.view.Traceability_new.Transaction_neo.MainForm-FormBasicData-SectionTransactionInfoDataNonFarmer').show()
                                            
                                            } 							
                                        },onFocus: function() {
                                            var me = this;
                                        
                                            if (!me.isExpanded) {
                                                me.expand()
                                            }
                                            me.getPicker().focus();
                                        }
                                    }, 
                                    {
                                        layout: 'column',
                                        border: false,
                                        items: [{
                                            // LEFT CONTENT
                                            columnWidth: 0.40,
                                            items: [
                                                thisObj.ObjPanelDataUnitNonFarmer
                                            ]
                                        }, {
                                           //RIGHT CONTENT
                                            columnWidth: 0.60,
                                            items: [
                                                thisObj.ObjPanelPaymentNonFarmer
                                            ]   
                                        }]
                                    }
                                ]
                            }
                           ],
                           
                            buttons: [{
                            xtype: 'button',
                            icon: varjs.config.base_url + 'images/icons/new/save.png',
                            text: lang('Save'),
                            cls: 'Sfr_BtnFormBlue',
                            overCls: 'Sfr_BtnFormBlue-Hover',
                            id: 'Koltiva.view.Traceability_new.Transaction_neo.MainForm-FormBasicData-BtnSave',
                                handler: function () {
                                    var Formnya = Ext.getCmp('Koltiva.view.Traceability_new.Transaction_neo.MainForm-FormBasicData').getForm();
                                    if (Formnya.isValid()) {
                                        
                                        if (thisObj.viewVar.OpsiDisplay == 'insert') {
                                            if (sessionStorage.getItem('setSupplyTransID') === null) {
                                                Ext.MessageBox.show({
                                                    title: lang('Attention'),
                                                    msg: lang('Form not complete yet'),
                                                    buttons: Ext.MessageBox.OK,
                                                    animateTarget: 'mb9',
                                                    icon: 'ext-mb-info'
                                                });

                                                return;
                                            } 
                                        }

                                        Formnya.submit({
                                            url: m_api + '/traceability_api/web_transaction/data',
                                            method: 'POST',
                                            waitMsg: 'Saving data...',
                                            params: {
                                                OpsiDisplay: thisObj.viewVar.OpsiDisplay,
                                                SupplyTransID : Object.is(sessionStorage.getItem('setSupplyTransID'), null) == false ? sessionStorage.getItem('setSupplyTransID') : thisObj.viewVar.SupplyTransID
                                            },
                                            success: function (fp, o) {
                                                Ext.MessageBox.show({
                                                    title: 'Information',
                                                    msg: lang('Data saved'),
                                                    buttons: Ext.MessageBox.OK,
                                                    animateTarget: 'mb9',
                                                    icon: 'ext-mb-success',
                                                    fn: function (btn) {
                                                        if (btn == 'ok') {
                                                            Ext.getCmp('Koltiva.view.Traceability_new.Transaction_neo.MainForm').destroy(); //destory current view
                                                            var MainForm = [];
                                                            if (Ext.getCmp('Koltiva.view.Traceability_new.Transaction_neo.MainForm') == undefined) {
                                                                MainForm = Ext.create('Koltiva.view.Traceability_new.Transaction_neo.MainForm', {
                                                                    viewVar: {
                                                                        OpsiDisplay: 'view',
                                                                        SupplyTransID: o.result.SupplyTransID
                                                                    }
                                                                });
                                                            } else {
                                                                Ext.getCmp('Koltiva.view.Traceability_new.Transaction_neo.MainForm').destroy();
                                                                MainForm = Ext.create('Koltiva.view.Traceability_new.Transaction_neo.MainForm', {
                                                                    viewVar: {
                                                                        OpsiDisplay: 'view',
                                                                        SupplyTransID: o.result.SupplyTransID
                                                                    }
                                                                });
                                                            }
                                                        }
                                                    }
                                                });
                                            },
                                            failure: function (fp, o) {
                                                try {
                                                    var r = Ext.decode(o.response.responseText);
                                                    Ext.MessageBox.show({
                                                        title: 'Error',
                                                        msg: (r.error) ? r.error : r.message,
                                                        buttons: Ext.MessageBox.OK,
                                                        animateTarget: 'mb9',
                                                        icon: 'ext-mb-error'
                                                    });
                                                } catch (err) {
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
                                            msg: lang('Form not complete yets'),
                                            buttons: Ext.MessageBox.OK,
                                            animateTarget: 'mb9',
                                            icon: 'ext-mb-info'
                                        });
                                    }
                                }
                            }]
                }]
        });
        //Panel Basic ==================================== (End)
        
        //========================================================== LAYOUT UTAMA (Begin) ========================================//
        thisObj.items = [{
                xtype: 'panel',
                border: false,
                layout: {
                    type: 'hbox'
                },
                items: [{
                        id: 'Koltiva.view.Traceability_new.Transaction_neo.MainForm-labelInfoInsert',
                        html: '<div id="header_title_farmer">' + lang('Buying') + '</div>'
                    }]
            }, {
                items: [{
                        id: 'Koltiva.view.Traceability_new.Transaction_neo.MainForm-LinkBackToList',
                        html: '<div id="Sfr_IdBoxInfoDataGrid" class="Sfr_BoxInfoDataGrid"><ul class="Sft_UlListInfoDataGrid"><li class="Sft_ListInfoDataGrid"><a href="javascript:Ext.getCmp(\'Koltiva.view.Traceability_new.Transaction_neo.MainForm\').BackToList()"><img class="Sft_ListIconInfoDataGrid" src="' + varjs.config.base_url + 'images/icons/new/back.png" width="20" />&nbsp;&nbsp;' + lang('Back to Buying List') + '</a></li></div>'
                    }]
            }, {
                html: '<br />'
            }, {
                layout: 'column',
                border: false,
                items: [{
                        columnWidth: 1,
                        items: [
                            thisObj.ObjPanelBasicData
                        ]
                    }]
            }];
        //========================================================== LAYOUT UTAMA (END) ========================================//

        this.callParent(arguments);
    },
    BackToList: function () {
        Ext.getCmp('Koltiva.view.Traceability_new.Transaction_neo.MainForm').destroy(); //destory current view
        var GridMain = [];
        if (Ext.getCmp('Koltiva.view.Traceability_new.Transaction_neo.MainGrid') == undefined) {
            GridMain = Ext.create('Koltiva.view.Traceability_new.Transaction_neo.MainGrid');
        } else {
            //destroy, create ulang
            Ext.getCmp('Koltiva.view.Traceability_new.Transaction_neo.MainGrid').destroy();
            GridMain = Ext.create('Koltiva.view.Traceability_new.Transaction_neo.MainGrid');
        }
    }
});

function checkImageExists(imageUrl, callBack) {
    var imageData = new Image();
    imageData.onload = function() {
        callBack(true);
    };
    imageData.onerror = function() {
        callBack(false);
    };
    imageData.src = imageUrl;
}
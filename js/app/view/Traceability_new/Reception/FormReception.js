Ext.define('Koltiva.view.Traceability_new.Reception.FormReception' ,{
    extend: 'Ext.panel.Panel',
    id: 'Koltiva.view.Traceability_new.Reception.FormReception',
    style:'padding:0 15px 15px 15px;margin:12px 0 0 0;',
    opsiDisplay: false,
    setOpsiDisplay: function(value){
        this.opsiDisplay = value;
    },
    formVar: false,
    setFormVar: function(value){
        this.formVar = value;
    },
    renderTo: 'ext-content',
    initComponent: function() {
        Ext.util.CSS.createStyleSheet([
            '.disabledListItem {',
            '    color:#eadada',
            '}'
        ].join('\n'));
        
        Ext.create('Ext.data.Store', {
            storeId: 'dateStore',
            fields: [{
                name: 'DateData',
                type: 'date'
            }],
            data: [{
                DateData: new Date()
            }]
        });

        var thisObj = this;
        var SupplyTransID = thisObj.viewVar.SupplyTransID;
        var DeliveryID = thisObj.viewVar.DeliveryID;
        var SupplychainID = thisObj.viewVar.DestinationID;

        thisObj.StoreComboPaymentMethod     = Ext.create('Koltiva.store.Traceability_new.Transaction_neo.StoreComboPaymentMethod');
      
        var objPanelBatchData = Ext.create('Koltiva.view.Traceability_new.Reception.FormBatch', {
                                    opsiDisplay: 'view',
                                    viewVar: {
                                        SupplyTransID : SupplyTransID,
                                        DeliveryID : DeliveryID,
                                        SupplychainID : SupplychainID,
                                        
                                    }
                                });
        thisObj.objPanelBatchData = objPanelBatchData;
    

        function backToList(){
            Ext.getCmp('Koltiva.view.Traceability_new.Reception.FormReception').destroy(); //destory current view
            if(Ext.getCmp('Koltiva.view.Traceability_new.Reception.GridReception') == undefined){
                Ext.create('Koltiva.view.Traceability_new.Reception.GridReception', {
                    opsiDisplay: 'view',
                    viewVar: {
                        
                    }
                });
            }else{
                Ext.create('Koltiva.view.Traceability_new.Reception.GridReception', {
                    opsiDisplay: 'view',
                    viewVar: {
                        //SupplyTransID: sm[0].get('SupplyTransID'),
                        
                    }
                });
            }
        }

        var objPanelBasicData = Ext.create('Ext.form.Panel',{
            title: lang('Form Reception'),
            frame: true,
            id: 'Koltiva.view.Traceability_new.Reception.FormReception-form',
            fileUpload: false,
            
            margin:'0 0 20 0',
            items: [{
                layout: 'column',
                border: false,
                padding:5,
                items:[{
                    columnWidth: 1,
                    layout:'form',
                    items:[{
                        xtype: 'panel',
                        items:[thisObj.objPanelBatchData] //disini ud 
                    },
                    {
                        xtype: 'panel',
                        items:[{
                            layout: 'column',
                            border: false,
                            items:[{
                                columnWidth: 1,
                                layout:'form',
                                style: 'padding: 0 10px 0 10px;margin-top:10px;',
                                items:[{
                                    xtype: 'panel',
                                    title: lang('Form Receiving'),
                                    items: [{
                                        layout: 'column',
                                        items: [{
                                            columnWidth: 1,
                                            layout: 'form',
                                            padding:5,
                                            items:[{
                                                xtype: 'textfield',
                                                id: 'Koltiva.view.Traceability_new.Reception.FormReception-form-SupplyTransID',
                                                name: 'SupplyTransID',
                                                value: SupplyTransID,
                                                inputType: 'hidden'
                                            },
                                            {
                                                xtype: 'textfield',
                                                id: 'Koltiva.view.Traceability_new.Reception.FormReception-form-DeliveryID',
                                                name: 'DeliveryID',
                                                value: DeliveryID,
                                                inputType: 'hidden'
                                            },
                                            {
                                                xtype: 'textfield',
                                                id: 'Koltiva.view.Traceability_new.Reception.FormReception-form-SupplychainID',
                                                name: 'SupplychainID',
                                                value: SupplychainID,
                                                inputType: 'hidden'
                                            },
                                            {
                                                xtype: 'textfield',
                                                id: 'Koltiva.view.Traceability_new.Reception.FormReception-form-uid',
                                                name: 'FormReception-uid',
                                                inputType: 'hidden'
                                            },
                                            {
                                                xtype: 'textfield',
                                                id: 'Koltiva.view.Traceability_new.Reception.FormReception-form-IsProcess',
                                                name: 'FormReception-IsProcess',
                                                inputType: 'hidden'
                                            },
                                            {
                                                xtype: 'textfield',
                                                id: 'Koltiva.view.Traceability_new.Reception.FormReception-form-IsDelivery',
                                                name: 'FormReception-IsDelivery',
                                                inputType: 'hidden'
                                            },
                                            {
                                                xtype: 'textfield',
                                                id: 'Koltiva.view.Traceability_new.Reception.FormReception-form-PackageWeight',
                                                name: 'PackageWeight',
                                                inputType: 'hidden'
                                            },
                                            {
                                                xtype: 'textfield',
                                                id: 'Koltiva.view.Traceability_new.Reception.FormReception-form-UnitTotal',
                                                name: 'UnitTotal',
                                                inputType: 'hidden'
                                            },
                                            {
                                                xtype: 'textfield',
                                                id: 'Koltiva.view.Traceability_new.Reception.FormReception-form-FAQTotalPaymentReal',
                                                name: 'FAQTotalPaymentReal',
                                                inputType: 'hidden'
                                            }]
                                        }, {
                                            columnWidth: 0.5,
                                            layout: 'form',
                                            padding:5,
                                            items:[{
                                                xtype: 'datefield',
                                                labelWidth: 150,
                                                fieldLabel: lang('Date receipt'),
                                                width: 225,
                                                id: 'Koltiva.view.Traceability_new.Reception.FormReception-form-DeliveryDate',
                                                name: 'DateData',
                                                format: 'Y-m-d',
                                                allowBlank: false,
                                                value: Ext.Date.format(new Date(), "Y-m-d"),
                                                enableKeyEvents: true,
                                                listeners: {
                                                    afterrender: function (form) {
                                                        var store = Ext.data.StoreManager.lookup('dateStore'),
                                                        date = store.getAt(0).get('DateData');                                                    
                                                    }
                                                }
                                            }, 
                                            // {
                                            //     xtype: 'timefield',
                                            //     labelWidth: 150,
                                            //     fieldLabel: lang('Time'),
                                            //     allowBlank: false,
                                            //     width: 225,
                                            //     id: 'Koltiva.view.Traceability_new.Reception.FormReception-form-TimeTransaction',
                                            //     name: 'TimeTransaction',
                                            //     format:'H:i',
                                            //     value: m_time,
                                            //     enableKeyEvents: true,
                                            //     listeners: {
                                            //         keydown : function (field_, e_  )  {
                                            //             e_.stopEvent();
                                            //             return false;
                                            //         }
                                            //     }
                                            // },
                                            // {
                                            //     xtype: 'numericfield',
                                            //     labelWidth: 150,
                                            //     fieldLabel: lang('Gross Weight (Kg)'),
                                            //     id: 'Koltiva.view.Traceability_new.Reception.FormReception-form-FAQVolumeBruto',
                                            //     name: 'FAQVolumeBruto',
                                            //     allowBlank: false,
                                            //     hidden: false,
                                            //     listeners: {
                                            //         change : function(){
                                            //             HitungIncentive();
                                            //         }
                                            //     }
                                            // },
                                            {
                                                xtype: 'textfield',
                                                labelWidth: 150,
                                                readOnly: true,
                                                fieldLabel: lang('Package Type'),
                                                id: 'Koltiva.view.Traceability_new.Reception.FormReception-form-PackageType',
                                                name: 'PackageType',
                                                hidden : true
                                            },
                                            // {
                                            //     xtype: 'numericfield',
                                            //     labelWidth: 150,
                                            //     fieldLabel: lang('Total Package'),
                                            //     id: 'Koltiva.view.Traceability_new.Reception.FormReception-form-FAQNumberPackage',
                                            //     name: 'FAQNumberPackage',
                                            //     hidden: false,
                                            //     listeners: {
                                            //         change : function(){
                                            //             HitungIncentive();
                                            //         }
                                            //     }
                                            // }, 
                                            {
                                                xtype: 'numericfield',
                                                labelWidth: 150,
                                                fieldLabel: lang('Receipt Total (Kg)'),
                                                id: 'Koltiva.view.Traceability_new.Reception.FormReception-form-TotalCapacity',
                                                name: 'TotalCapacity',
                                                allowBlank: false,
                                                readOnly: true
                                            }]
                                        }, {
                                            columnWidth: 0.5,
                                            layout: 'form',
                                            padding:5,
                                            items:[
                                                {
                                                    xtype: 'textfield',
                                                    labelWidth: 150,
                                                    hidden:true,
                                                    fieldLabel: lang('InvoiceNumber'),
                                                    id: 'Koltiva.view.Traceability_new.Reception.FormReception-form-InvoiceNumber',
                                                    name: 'InvoiceNumber',
                                                    listeners: {
                                                        change : function(){
                                                            
                                                        }
                                                    }
                                                },
                                                // {
                                                //     xtype: 'textfield',
                                                //     labelWidth: 150,
                                                //     readOnly: true,
                                                //     fieldLabel: lang('Unit'),
                                                //     id: 'Koltiva.view.Traceability_new.Reception.FormBatch-form-UnitName',
                                                //     name: 'UnitName'
                                                // },
                                                // {
                                                //     xtype: 'textfield',
                                                //     labelWidth: 150,
                                                //     readOnly: true,
                                                //     fieldLabel: lang('Seaweed Type'),
                                                //     id: 'Koltiva.view.Traceability_new.Reception.FormReception-form-SeaweedTypeName',
                                                //     name: 'SeaweedTypeName',
                                                //     hidden: true
                                                // },
                                                // {
                                                //     xtype: 'combo',
                                                //     emptyText: lang(' - '),
                                                //     labelWidth: 150,
                                                //     fieldLabel: lang('Seaweed Type'),
                                                //     id: 'Koltiva.view.Traceability_new.Reception.FormReception-form-SeaweedTypeID',
                                                //     name: 'SeaweedTypeID',
                                                //     store: storeComboSeaweedType,
                                                //     displayField: 'label',
                                                //     valueField: 'id',
                                                //     queryMode: 'local',
                                                //     allowBlank: false,
                                                // }, 
                                                // {
                                                //     xtype: 'numericfield',
                                                //     labelWidth: 150,
                                                //     allowBlank: false,
                                                //     fieldLabel: lang('Price per Kg (Rp)'),
                                                //     id: 'Koltiva.view.Traceability_new.Reception.FormReception-form-FAQContractPrice',
                                                //     name: 'FAQContractPrice',
                                                //     baseCls: 'Sfr_FormInputMandatory',
                                                //     minValue:0,
                                                //     hidden:false,
                                                //     listeners: {
                                                //         change : function(){
                                                //             HitungIncentive();
                                                //         }
                                                //     }
                                                // }, 
                                                // {
                                                //     xtype: 'numericfield',
                                                //     labelWidth: 150,
                                                //     fieldLabel: lang('Total Payment (Rp)'),
                                                //     id: 'Koltiva.view.Traceability_new.Reception.FormReception-form-FAQTotalPayment',
                                                //     name: 'FAQTotalPayment'
                                                // }, 
                                                // {
                                                //     xtype: 'textarea',
                                                //     labelWidth: 150,
                                                //     fieldLabel: lang('Notes'),
                                                //     id: 'Koltiva.view.Traceability_new.Reception.FormReception-form-ChangeLog',
                                                //     name: 'ChangeLog'
                                                
                                                // }
                                            ]
                                        }]
                                    }]
                                }]
                            }]
                        }], //disini
                    },
                    // {
                    //     xtype: 'panel',
                    //     items:[{
                    //         layout: 'column',
                    //         border: false,
                    //         items:[{
                    //             columnWidth: 1,
                    //             layout:'form',
                    //             style: 'padding: 0 10px 0 10px;margin-top:10px;',
                    //             items:[{
                    //                 xtype: 'panel',
                    //                 id: 'Koltiva.view.Traceability_new.Reception.FormReception-form-FormPayment',
                    //                 title: lang('Form Payment'),
                    //                 items: [{
                    //                     layout: 'column',
                    //                     items: [{
                    //                         columnWidth: 0.5,
                    //                         layout: 'form',
                    //                         padding:5,
                    //                         items:[{
                    //                             xtype: 'numericfield',
                    //                             id: 'Koltiva.view.Traceability_new.Reception.FormReception-form-PaymentPaid',
                    //                             name: 'PaymentPaid',
                    //                             fieldLabel: lang('Payment Amount')
                    //                         },
                    //                         {
                    //                             xtype: 'numericfield',
                    //                             id: 'Koltiva.view.Traceability_new.Reception.FormReception-form-PaymentStatusID',
                    //                             name: 'PaymentStatusID',
                    //                             fieldLabel: lang('Payment Status'),
                    //                             value:0,
                    //                             readOnly: true
                    //                         },
                    //                         {
                    //                             xtype: 'combo',
                    //                             id: 'Koltiva.view.Traceability_new.Reception.FormReception-form-PaymentMethodID',
                    //                             name: 'PaymentMethodID',
                    //                             fieldLabel: lang('Payment Method'),
                    //                             store: thisObj.StoreComboPaymentMethod ,
                    //                             displayField: 'label',
                    //                             valueField: 'id',
                    //                             queryMode: 'local',
                    //                             listeners: {
                    //                                 change : function(){
                    //                                     // Ext.getCmp('Koltiva.view.Traceability_new.Reception.FormReception-form-btnBayar').show();
                    //                                 }
                    //                             }
                    //                             // valueField:id
                    //                         },
                    //                         {
                    //                             xtype: 'textfield',
                    //                             id: 'Koltiva.view.Traceability_new.Reception.FormReception-form-BankCode',
                    //                             name: 'BankCode',
                    //                             fieldLabel: lang('Bank Code')
                    //                         },
                    //                         {
                    //                             xtype: 'textfield',
                    //                             id: 'Koltiva.view.Traceability_new.Reception.FormReception-form-BankName',
                    //                             name: 'BankName',
                    //                             fieldLabel: lang('Bank Name')
                    //                         },
                    //                         {
                    //                             xtype: 'textfield',
                    //                             id: 'Koltiva.view.Traceability_new.Reception.FormReception-form-AccountNumber',
                    //                             name: 'AccountNumber',
                    //                             fieldLabel: lang('Account Number')
                    //                         },
                    //                         {
                    //                             xtype: 'textfield',
                    //                             id: 'Koltiva.view.Traceability_new.Reception.FormReception-form-AccountName',
                    //                             name: 'AccountName',
                    //                             fieldLabel: lang('Account Name')
                    //                         },
                    //                         {
                    //                             xtype:'button',
                    //                             icon: varjs.config.base_url + 'images/icons/new/script_link_white.png',
                    //                             // hidden:true,
                    //                             text: lang('Bayar'),
                    //                             id: 'Koltiva.view.Traceability_new.Reception.FormReception-form-btnBayar',
                    //                             margin: '5px',
                    //                             cls:'Sfr_BtnFormGreen',
                    //                             // overCls:'Sfr_BtnFormGreen-Hover',
                    //                             handler: function () {
                    //                                 Ext.MessageBox.confirm(lang('Confirmation'), lang('Are you sure ?'), function(btn) {
                    //                                     if (btn == 'yes') {
                    //                                         if (objPanelBasicData.isValid()) {
                    //                                             var method = 'POST';
                    //                                                 objPanelBasicData.submit({
                    //                                                     url: m_api + '/traceability_api/reception/submit_payment',
                    //                                                     method:method,
                    //                                                     waitMsg: lang('Processing ...'),
                    //                                                     success: function(fp, o) {
                    //                                                         var r = Ext.decode(fp.responseText);
                    //                                                         Ext.MessageBox.show({
                    //                                                             title: 'Information',
                    //                                                             msg: lang('Submit Payment Success'),
                    //                                                             buttons: Ext.MessageBox.OK,
                    //                                                             animateTarget: 'mb9',
                    //                                                             icon: 'ext-mb-success',
                                                                                
                    //                                                         });

                    //                                                         Ext.getCmp('Koltiva.view.Traceability_new.Reception.FormReception-form-btnBayar').hide();
                    //                                                         Ext.getCmp('Koltiva.view.Traceability_new.Reception.FormReception-form-btnPaymentInstruction').show();
                    //                                                         Ext.getCmp('Koltiva.view.Traceability_new.Reception.FormReception-form-btnPaymentInstruction').el.dom.click();
                                                                            
                    //                                                     },
                    //                                                     failure: function(fp, o){
                    //                                                         var pesanNya;
                    //                                                         if(o.result.message != undefined){
                    //                                                             pesanNya = o.result.message;
                    //                                                         }else{
                    //                                                             pesanNya = lang('Connection error');
                    //                                                         }
                    //                                                         Ext.MessageBox.show({
                    //                                                             title: 'Error',
                    //                                                             msg: pesanNya,
                    //                                                             buttons: Ext.MessageBox.OK,
                    //                                                             animateTarget: 'mb9',
                    //                                                             icon: 'ext-mb-error'
                    //                                                         });
                    //                                                     }
                    //                                                 });
                    //                                         } else {
                    //                                             Ext.MessageBox.show({
                    //                                                 title: 'Attention',
                    //                                                 msg: lang('Form not valid yet'),
                    //                                                 buttons: Ext.MessageBox.OK,
                    //                                                 animateTarget: 'mb9',
                    //                                                 icon: 'ext-mb-info'
                    //                                             });
                    //                                         }
                    //                                     }
                    //                                 });
                    //                             }
                    //                         },{
                    //                             xtype:'button',
                    //                             icon: varjs.config.base_url + 'images/icons/new/script_link_white.png',
                    //                             hidden:true,
                    //                             text: lang('Payment Instruction'),
                    //                             id:'Koltiva.view.Traceability_new.Reception.FormReception-form-btnPaymentInstruction',
                    //                             margin: '5px',
                    //                             cls:'Sfr_BtnFormGreen',
                    //                             handler: function() {
                    //                                var SupplyTransID = Ext.getCmp('Koltiva.view.Traceability_new.Reception.FormReception-form-SupplyTransID').getValue(); 
                                                   
                    //                                if(Ext.getCmp('Koltiva.view.Traceability_new.Transaction_neo.PaymentInstuction') == undefined){
                    //                                     var PanelPaymentIntruction = Ext.create('Koltiva.view.Traceability_new.Transaction_neo.PaymentInstuction', {
                    //                                         opsiDisplay: 'insert',
                    //                                         viewVar: { 
                    //                                            SupplyTransID : SupplyTransID ,
                    //                                            btnSave: true
                    //                                         }
                    //                                     });
                    //                                 }else{
                    //                                     //destroy, create ulang
                    //                                     Ext.getCmp('Koltiva.view.Traceability_new.Transaction_neo.PaymentInstuction').destroy();
                    //                                     var PanelPaymentIntruction = Ext.create('Koltiva.view.Traceability_new.Transaction_neo.PaymentInstuction', {
                    //                                         opsiDisplay: 'insert',
                    //                                         viewVar: { 
                    //                                            SupplyTransID : SupplyTransID ,
                    //                                            btnSave: true
                    //                                         }
                    //                                     });
                    //                                 }
                    //                                 if (!PanelPaymentIntruction.isVisible()) {
                    //                                    PanelPaymentIntruction.center();
                    //                                    PanelPaymentIntruction.show();
                    //                                 } else {
                    //                                    PanelPaymentIntruction.close();
                    //                                 } 
                    //                             }
                    //                         }]
                    //                     }]
                    //                 }]
                    //             }]
                    //         }]
                    //     }], //disini
                    //     buttons: [  
                    //     // {
                    //     //     xtype: 'button',
                    //     //     icon: varjs.config.base_url + 'images/icons/new/printout.png',
                    //     //     text: lang('Print Invoice'),
                    //     //     cls: 'Sfr_BtnFormGreen',
                    //     //     overCls: 'Sfr_BtnFormGreen-Hover',
                    //     //     hidden:true,
                    //     //     id: 'Koltiva.view.Traceability_new.Reception.FormReception-btnPrintInvoice',
                    //     //     handler: function () {
                    //     //         var url = m_api + '/printout/print_invoice_reception';
                    //     //         preview_cetak_surat(url + '/' + Ext.getCmp('Koltiva.view.Traceability_new.Reception.FormReception-form-SupplyTransID').getValue());
                    //     //     }
                    //     // },
                    //     // {
                    //     //     text: lang('Submit'),
                    //     //     margin: '5 15 5 5',
                    //     //     scale: 'large',
                    //     //     ui: 's-button',
                    //     //     id: 'Koltiva.view.Traceability_new.Reception.FormReception-btnSave',
                    //     //     cls: 's-green',
                    //     //     handler: function () {
                    //     //         if (Ext.getCmp('Koltiva.view.Traceability_new.Reception.FormReception-form-SupplyTransID').getValue() === null || Ext.getCmp('Koltiva.view.Traceability_new.Reception.FormReception-form-SupplyTransID').getValue() === "") {
                    //     //             Ext.MessageBox.show({
                    //     //                 title: 'Attention',
                    //     //                 msg: lang('Please fill data delivery receiving first'),
                    //     //                 buttons: Ext.MessageBox.OK,
                    //     //                 animateTarget: 'mb9',
                    //     //                 icon: 'ext-mb-info'
                    //     //             });

                    //     //             return;
                    //     //         }
                               
                    //     //         if (objPanelBasicData.isValid()) {
                    //     //             var method = 'POST';
                    //     //                 objPanelBasicData.submit({
                    //     //                     url: m_api + '/traceability_api/reception/submit_reception',
                    //     //                     method:method,
                    //     //                     params: {
                    //     //                         DeliveryStatusID: 6
                    //     //                     },
                    //     //                     waitMsg: lang('Saving data'),
                    //     //                     success: function(fp, o) {
                    //     //                         Ext.MessageBox.show({
                    //     //                             title: 'Information',
                    //     //                             msg: lang('Data saved'),
                    //     //                             buttons: Ext.MessageBox.OK,
                    //     //                             animateTarget: 'mb9',
                    //     //                             icon: 'ext-mb-success'
                    //     //                         });

                    //     //                         Ext.getCmp('Koltiva.view.Traceability_new.Reception.FormReception-form-SupplyTransID').setValue(o.result.data.SupplyTransID);

                    //     //                         Ext.getCmp('Koltiva.view.Traceability_new.Reception.FormReception-btnSave').hide();
                    //     //                         Ext.getCmp('Koltiva.view.Traceability_new.Reception.FormReception-btnSaveDraft').hide();
                    //     //                         Ext.getCmp('PanelDataDeliveryReceiving-ButtonAdd').hide();
                    //     //                         Ext.getCmp('PanelDataDeliveryReceiving-ButtonActionGrid').hide();

                    //     //                         Ext.getCmp('Koltiva.view.Traceability_new.Reception.FormReception-form-DateTransaction').setReadOnly(true);
                    //     //                         Ext.getCmp('Koltiva.view.Traceability_new.Reception.FormReception-form-TimeTransaction').setReadOnly(true);
                    //     //                         Ext.getCmp('Koltiva.view.Traceability_new.Reception.FormReception-form-FAQTotalPayment').setReadOnly(true);
                    //     //                         Ext.getCmp('Koltiva.view.Traceability_new.Reception.FormReception-form-ChangeLog').setReadOnly(true);
                    //     //                     },
                    //     //                     failure: function(fp, o){
                    //     //                         var pesanNya;
                    //     //                         if(o.message != undefined){
                    //     //                             pesanNya = o.message;
                    //     //                         }else{
                    //     //                             pesanNya = lang('Connection error');
                    //     //                         }
                    //     //                         Ext.MessageBox.show({
                    //     //                             title: 'Error',
                    //     //                             msg: pesanNya,
                    //     //                             buttons: Ext.MessageBox.OK,
                    //     //                             animateTarget: 'mb9',
                    //     //                             icon: 'ext-mb-error'
                    //     //                         });
                    //     //                     }
                    //     //                 });
                    //     //         } else {
                    //     //             Ext.MessageBox.show({
                    //     //                 title: 'Attention',
                    //     //                 msg: lang('Form not valid yet'),
                    //     //                 buttons: Ext.MessageBox.OK,
                    //     //                 animateTarget: 'mb9',
                    //     //                 icon: 'ext-mb-info'
                    //     //             });
                    //     //         }
                    //     //     }
                    //     // },
                    //     // {
                    //     //     text: lang('Save as Draft'),
                    //     //     margin: '5 15 5 5',
                    //     //     scale: 'large',
                    //     //     ui: 's-button',
                    //     //     id: 'Koltiva.view.Traceability_new.Reception.FormReception-btnSaveDraft',
                    //     //     cls: 's-blue',
                    //     //     handler: function () {
                    //     //         if (Ext.getCmp('Koltiva.view.Traceability_new.Reception.FormReception-form-SupplyTransID').getValue() === null || Ext.getCmp('Koltiva.view.Traceability_new.Reception.FormReception-form-SupplyTransID').getValue() === "") {
                    //     //             Ext.MessageBox.show({
                    //     //                 title: 'Attention',
                    //     //                 msg: lang('Please fill data delivery receiving first'),
                    //     //                 buttons: Ext.MessageBox.OK,
                    //     //                 animateTarget: 'mb9',
                    //     //                 icon: 'ext-mb-info'
                    //     //             });

                    //     //             return;
                    //     //         }
                               
                    //     //         if (objPanelBasicData.isValid()) {
                    //     //             var method = 'POST';
                    //     //                 objPanelBasicData.submit({
                    //     //                     url: m_api + '/traceability_api/reception/submit_reception',
                    //     //                     method:method,
                    //     //                     params: {
                    //     //                         DeliveryStatusID: 4
                    //     //                     },
                    //     //                     waitMsg: lang('Saving data'),
                    //     //                     success: function(fp, o) {
                    //     //                         Ext.MessageBox.show({
                    //     //                             title: 'Information',
                    //     //                             msg: lang('Data saved'),
                    //     //                             buttons: Ext.MessageBox.OK,
                    //     //                             animateTarget: 'mb9',
                    //     //                             icon: 'ext-mb-success',
                    //     //                             fn: function (btn) {
                    //     //                                 if (btn == 'ok') {
                    //     //                                     Ext.getCmp('Koltiva.view.Traceability_new.Reception.FormReception').destroy(); //destory current view
                    //     //                                     var MainFormBatch = [];
                    //     //                                     if (Ext.getCmp('Koltiva.view.Traceability_new.Reception.FormReception') == undefined) {
                    //     //                                         MainFormBatch = Ext.create('Koltiva.view.Traceability_new.Reception.FormReception', {
                    //     //                                             viewVar: {
                    //     //                                                 OpsiDisplay: 'update',
                    //     //                                                 DeliveryID: o.result.DeliveryID,
                    //     //                                                 SupplyTransID: o.result.SupplyTransID,
                    //     //                                                 DestinationID: o.result.DestinationID
                    //     //                                             }
                    //     //                                         });
                    //     //                                     } else {
                    //     //                                         Ext.getCmp('Koltiva.view.Traceability_new.Reception.FormReception').destroy();
                    //     //                                         MainFormBatch = Ext.create('Koltiva.view.Traceability_new.Reception.FormReception', {
                    //     //                                             viewVar: {
                    //     //                                                 OpsiDisplay: 'update',
                    //     //                                                 DeliveryID: o.result.DeliveryID,
                    //     //                                                 SupplyTransID: o.result.SupplyTransID,
                    //     //                                                 DestinationID: o.result.DestinationID
                    //     //                                             }
                    //     //                                         });
                    //     //                                     }
                    //     //                                 }
                    //     //                             }
                    //     //                         });

                    //     //                         Ext.getCmp('Koltiva.view.Traceability_new.Reception.FormReception-form-SupplyTransID').setValue(o.result.data.SupplyTransID);
                    //     //                     },
                    //     //                     failure: function(fp, o){
                    //     //                         var pesanNya;
                    //     //                         if(o.message != undefined){
                    //     //                             pesanNya = o.message;
                    //     //                         }else{
                    //     //                             pesanNya = lang('Connection error');
                    //     //                         }
                    //     //                         Ext.MessageBox.show({
                    //     //                             title: 'Error',
                    //     //                             msg: pesanNya,
                    //     //                             buttons: Ext.MessageBox.OK,
                    //     //                             animateTarget: 'mb9',
                    //     //                             icon: 'ext-mb-error'
                    //     //                         });
                    //     //                     }
                    //     //                 });
                    //     //         } else {
                    //     //             Ext.MessageBox.show({
                    //     //                 title: 'Attention',
                    //     //                 msg: lang('Form not valid yet'),
                    //     //                 buttons: Ext.MessageBox.OK,
                    //     //                 animateTarget: 'mb9',
                    //     //                 icon: 'ext-mb-info'
                    //     //             });
                    //     //         }
                    //     //     }
                    //     // },
                    //     {
                    //         text: lang('Back'),
                    //         margin: '5 15 5 5',
                    //         scale: 'large',
                    //         ui: 's-button',
                    //         id: 'Koltiva.view.Traceability_new.Reception.FormReception-btnCancel',
                    //         cls: '',
                    //         handler: function () {
                    //             backToList();
                    //         }
                    //     }]
                    // }
                ]
                }]
            }],
            listeners:{
                afterrender: function(c){
                    var SupplyTransID = thisObj.viewVar.SupplyTransID;
                    var DeliveryID = thisObj.viewVar.DeliveryID;
                    var SupplychainID = thisObj.viewVar.SupplychainID;
                    var OpsiDisplay = thisObj.viewVar.OpsiDisplay;
                    
                    Ext.Ajax.request({
                        url: m_api + '/traceability_api/reception/reception_detail',
                        method: 'GET',
                        params: {
                            SupplyTransID: SupplyTransID, 
                            DeliveryID: DeliveryID
                        },
                        success: function(fp, o){
                            var r = Ext.decode(fp.responseText);

                            if(m_IsPaymentMethod != 1){
                                Ext.getCmp('Koltiva.view.Traceability_new.Reception.FormReception-form-FormPayment').hide();
                            }
                            else{
                                // Ext.getCmp('Koltiva.view.Traceability_new.Reception.FormReception-form-FormPayment').show();
                            }

                            // console.log(r[0].PaymentStatusID);

                            if(r[0].PaymentStatusID == 2) {
                                Ext.getCmp('Koltiva.view.Traceability_new.Reception.FormReception-form-btnBayar').hide();
                                Ext.getCmp('Koltiva.view.Traceability_new.Reception.FormReception-form-btnPaymentInstruction').show();
                            }
                            if(r[0].PaymentStatusID == 0) {
                                Ext.getCmp('Koltiva.view.Traceability_new.Reception.FormReception-form-btnBayar').show();
                            }
                            
                            // Ext.getCmp('Koltiva.view.Traceability_new.Reception.FormReception-form-SupplyTransID').setValue(r.SupplyTransID);

                            if (r.DateReceipt === null) {
                                Ext.getCmp('Koltiva.view.Traceability_new.Reception.FormReception-form-DateTransaction').setValue(m_date);
                            } 
                            // else {
                            //     Ext.getCmp('Koltiva.view.Traceability_new.Reception.FormReception-form-DateTransaction').setValue(r.DateReceipt);
                            // }

                            if (r.TimeReceipt === null) {
                                Ext.getCmp('Koltiva.view.Traceability_new.Reception.FormReception-form-TimeTransaction').setValue(m_time);
                            } 
                            // else {
                            //     let date = r.TimeReceipt;
                            //     Ext.getCmp('Koltiva.view.Traceability_new.Reception.FormReception-form-TimeTransaction').setValue(date.split(":").slice(0, 2).join(':'));
                            // }

                            // if (r.GrossWeightReceipt === null) {
                            //     Ext.getCmp('Koltiva.view.Traceability_new.Reception.FormReception-form-FAQVolumeBruto').setValue(r.DestWeight);
                            // } else {
                            //     Ext.getCmp('Koltiva.view.Traceability_new.Reception.FormReception-form-FAQVolumeBruto').setValue(r.GrossWeightReceipt);
                            // }

                            // if (r.PackageNumberReceipt === null) {
                            //     Ext.getCmp('Koltiva.view.Traceability_new.Reception.FormReception-form-FAQNumberPackage').setValue(r.PackageNumber);
                            // } else {
                            //     Ext.getCmp('Koltiva.view.Traceability_new.Reception.FormReception-form-FAQNumberPackage').setValue(r.PackageNumberReceipt);
                            // }

                            // if (r.NetWeightReceipt === null) {
                            //     let VolumeNetto = parseFloat(r.DestWeight) - ( parseFloat(r.PackageWeight) * parseFloat(r.PackageNumber) );

                            //     Ext.getCmp('Koltiva.view.Traceability_new.Reception.FormReception-form-FAQVolumeNetto').setValue(VolumeNetto);
                            // } else {
                            //     Ext.getCmp('Koltiva.view.Traceability_new.Reception.FormReception-form-FAQVolumeNetto').setValue(r.NetWeightReceipt);
                            // }

                            if (Ext.getCmp('Koltiva.view.Traceability_new.Reception.FormReception-form-Weight').getValue() === null || Ext.getCmp('Koltiva.view.Traceability_new.Reception.FormReception-form-Weight').getValue() === 0) {
                                Ext.getCmp('Koltiva.view.Traceability_new.Reception.FormReception-form-Weight').setValue(sessionStorage.getItem('Weight'));
                            }

                            // if (Ext.getCmp('Koltiva.view.Traceability_new.Reception.FormReception-form-FAQTotalPayment').getValue() === null || Ext.getCmp('Koltiva.view.Traceability_new.Reception.FormReception-form-FAQTotalPayment').getValue() === 0) {
                            //     Ext.getCmp('Koltiva.view.Traceability_new.Reception.FormReception-form-FAQTotalPayment').setValue(sessionStorage.getItem('totalPayment'));
                            // }

                            if (r.TotalPaymentReceipt == null) {
                                Ext.getCmp('Koltiva.view.Traceability_new.Reception.FormReception-form-FAQTotalPaymentReal').setValue(0);
                            } else {
                                Ext.getCmp('Koltiva.view.Traceability_new.Reception.FormReception-form-FAQTotalPaymentReal').setValue(r.TotalPaymentReceipt);
                            }
                            // else {
                            //     let totalPaymentReceipt = r.TotalPaymentReceipt
                            //     let valueTotalPayment

                            //     if (totalPaymentReceipt === null) {
                            //         valueTotalPayment = sessionStorage.getItem('totalPayment')
                            //     } else {
                            //         valueTotalPayment = totalPaymentReceipt
                            //     }

                            //     Ext.getCmp('Koltiva.view.Traceability_new.Reception.FormReception-form-FAQTotalPayment').setValue(valueTotalPayment);
                            // }

                            if (Ext.getCmp('Koltiva.view.Traceability_new.Reception.FormReception-form-UnitTotal').getValue() === "") {
                                Ext.getCmp('Koltiva.view.Traceability_new.Reception.FormReception-form-UnitTotal').setValue(sessionStorage.getItem('unitTotal'));
                            }

                            // Ext.getCmp('Koltiva.view.Traceability_new.Reception.FormReception-form-SeaweedTypeName').setValue(r.SeaweedTypeName);
                            Ext.getCmp('Koltiva.view.Traceability_new.Reception.FormReception-form-PackageWeight').setValue(r.PackageWeight);
                            Ext.getCmp('Koltiva.view.Traceability_new.Reception.FormReception-form-PackageType').setValue(r.PackageType);
                            // Ext.getCmp('Koltiva.view.Traceability_new.Reception.FormReception-form-FAQContractPrice').setValue(r.PriceReceipt);
                            // Ext.getCmp('Koltiva.view.Traceability_new.Reception.FormReception-form-FAQTotalPayment').setValue(r.TotalPaymentReceipt);
                            // Ext.getCmp('Koltiva.view.Traceability_new.Reception.FormReception-form-ChangeLog').setValue(r.NotesReceipt);
                            
                            if(OpsiDisplay=='view'){
                                if(r.DeliveryStatusID == 4){
                                    Ext.getCmp('Koltiva.view.Traceability_new.Reception.FormBatch-form-ButtonAdd').hide();
                                } else {
                                    Ext.getCmp('Koltiva.view.Traceability_new.Reception.FormBatch-form-ButtonAdd').show();
                                }
    
                                // if(r[0].PaymentStatusID == 2) {
                                //     Ext.getCmp('Koltiva.view.Traceability_new.Reception.FormReception-form-btnBayar').hide();
                                //     Ext.getCmp('Koltiva.view.Traceability_new.Reception.FormReception-form-btnPaymentInstruction').show();
                                // }
                                
                                Ext.getCmp('Koltiva.view.Traceability_new.Reception.FormReception-btnSave').hide();
                                Ext.getCmp('Koltiva.view.Traceability_new.Reception.FormReception-btnSaveDraft').hide();
                                Ext.getCmp('PanelDataDeliveryReceiving-ButtonActionGrid').hide();

                                Ext.getCmp('Koltiva.view.Traceability_new.Reception.FormReception-form-DateTransaction').setReadOnly(true);
                                Ext.getCmp('Koltiva.view.Traceability_new.Reception.FormReception-form-TimeTransaction').setReadOnly(true);
                                // Ext.getCmp('Koltiva.view.Traceability_new.Reception.FormReception-form-FAQTotalPayment').setReadOnly(true);
                                // Ext.getCmp('Koltiva.view.Traceability_new.Reception.FormReception-form-ChangeLog').setReadOnly(true);

                                // if (r.DeliveryStatusID == 4) {
                                //     Ext.getCmp('Koltiva.view.Traceability_new.Reception.FormReception-btnPrintInvoice').show();
                                // } else {
                                //     Ext.getCmp('Koltiva.view.Traceability_new.Reception.FormReception-btnPrintInvoice').hide();
                                // }
                                
                            }
                        }
                     });
                }
            }
        });

        

        thisObj.items = [{
            xtype: 'panel',
            border:false,
            layout:{
                type:'hbox'
            },
            items:[{
                id:'Koltiva.view.Traceability_new.Reception.FormReception-title',
               html:'<h3 style="margin:0px 0 7px 0;padding:0px;">'+lang('Detail Reception')+'</h3>'
            },{
                id: 'Koltiva.view.Traceability_new.Reception.FormReception-labelInfoInsert',
                html:'',
            }]
        },{
            xtype: 'component',
            autoEl: {
                tag: 'a',
                href: '#',
                html: lang('Back to Reception List'),
                style:'text-decoration:underline;'
            },
            listeners: {
                click: {
                    element: 'el',
                    preventDefault: true,
                    fn: function(e, target){
                        backToList();
                    }
                }
            }
        },{
            html:'<br />'
        },{
            layout: 'column',
            border: false,
            items: [{
                //LEFT CONTENT
                columnWidth: 1,
                items:[
                    objPanelBasicData
                ]
            }]
        }];
        //========================================================== LAYOUT UTAMA (END) ========================================//
        this.callParent(arguments);
    },
    listeners: {
        afterrender: function(){
            
        }
    }
});

function HitungIncentive(){
            
    let FAQVolumeBruto = 0; 
    let Package = 0; 
    let FAQNumberPackage = 0; 
    let FAQVolumeNetto = 0; 
    let FAQContractPrice = 0; 
    let FAQTotalPayment = 0;
    

    if(Ext.isDefined(Ext.getCmp('Koltiva.view.Traceability_new.Reception.FormReception-form-FAQVolumeBruto'))){
        FAQVolumeBruto = isNaN(parseFloat(Ext.getCmp('Koltiva.view.Traceability_new.Reception.FormReception-form-FAQVolumeBruto').getValue()))?0:parseFloat(Ext.getCmp('Koltiva.view.Traceability_new.Reception.FormReception-form-FAQVolumeBruto').getValue());
    }

    Package = parseFloat(Ext.getCmp('Koltiva.view.Traceability_new.Reception.FormReception-form-PackageWeight').getValue());

    if(Ext.isDefined(Ext.getCmp('Koltiva.view.Traceability_new.Reception.FormReception-form-FAQNumberPackage'))){
        FAQNumberPackage = isNaN(parseFloat(Ext.getCmp('Koltiva.view.Traceability_new.Reception.FormReception-form-FAQNumberPackage').getValue()))?0:parseFloat(Ext.getCmp('Koltiva.view.Traceability_new.Reception.FormReception-form-FAQNumberPackage').getValue());
    }

    FAQVolumeNetto = FAQVolumeBruto - (Package * FAQNumberPackage);

    Ext.getCmp('Koltiva.view.Traceability_new.Reception.FormReception-form-FAQVolumeNetto').setValue(FAQVolumeNetto);

    if(Ext.isDefined(Ext.getCmp('Koltiva.view.Traceability_new.Reception.FormReception-form-FAQContractPrice'))){
        FAQContractPrice = isNaN(parseFloat(Ext.getCmp('Koltiva.view.Traceability_new.Reception.FormReception-form-FAQContractPrice').getValue()))?0:parseFloat(Ext.getCmp('Koltiva.view.Traceability_new.Reception.FormReception-form-FAQContractPrice').getValue());
    }

    FAQTotalPayment = FAQContractPrice * FAQVolumeNetto;
    Ext.getCmp('Koltiva.view.Traceability_new.Reception.FormReception-form-FAQTotalPayment').setValue(FAQTotalPayment);
    
}
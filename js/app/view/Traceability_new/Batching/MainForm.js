Ext.define('Koltiva.view.Traceability_new.Batching.MainForm', {
    extend: 'Ext.panel.Panel',
    id: 'Koltiva.view.Traceability_new.Batching.MainForm',
    style: 'padding:0 15px 15px 15px;margin:5px 0 0 0;',
    viewVar: false,
    setViewVar: function (value) {
        this.viewVar = value;
    },
    renderTo: 'ext-content',
    listeners: {
        afterRender: function () {
            var thisObj = this;

            if (thisObj.viewVar.OpsiDisplay == 'view' || thisObj.viewVar.OpsiDisplay == 'update') {
                if (thisObj.viewVar.OpsiDisplay == 'view') {
                    Ext.getCmp('Koltiva.view.Traceability_new.Batching.MainForm-FormBasicData-BtnSave').setVisible(false);
                    Ext.getCmp('Koltiva.view.Traceability_new.Batching.PanelDataPurchase-BtnAdd').setVisible(true);
                    Ext.getCmp('Koltiva.view.Traceability_new.Batching.PanelDataPurchase-BtnClose').setVisible(false);
                    Ext.getCmp('Koltiva.view.Traceability_new.Batching.PanelDataPurchase-ActionColumn').setVisible(false);

                    Ext.getCmp('Koltiva.view.Traceability_new.Batching.PanelDataPurchaseDetail-BtnAdd').setVisible(false);
                    Ext.getCmp('Koltiva.view.Traceability_new.Batching.PanelDataPurchaseDetail-BtnComplete').setVisible(false);
                    Ext.getCmp('Koltiva.view.Traceability_new.Batching.PanelDataPurchaseDetail-ActionColumn').setVisible(false);
                    Ext.getCmp('Koltiva.view.Traceability_new.Batching.MainForm-FormBasicData-BtnProcess').setVisible(false);
                } 

                //load formnya
                Ext.getCmp('Koltiva.view.Traceability_new.Batching.MainForm-FormBasicData').getForm().load({
                    url: m_api + '/traceability_api/batching/supplychain_batch_form_open',
                    method: 'GET',
                    params: {
                        SupplyBatchID: this.viewVar.SupplyBatchID
                    },
                    success: function (form, action) {
                        Ext.MessageBox.hide();
                        var r = Ext.decode(action.response.responseText);

                        //Title
                        Ext.getCmp('Koltiva.view.Traceability_new.Batching.MainForm-labelInfoInsert').update('<div id="header_title_farmer">' + Ext.getCmp('Koltiva.view.Traceability_new.Batching.MainForm-FormBasicData-SupplyBatchNumber').getValue() + '</div>');
                        Ext.getCmp('Koltiva.view.Traceability_new.Batching.MainForm-labelInfoInsert').doLayout();

                        var SupplyBatchStatusID = Ext.getCmp('Koltiva.view.Traceability_new.Batching.MainForm-FormBasicData-SupplyBatchStatusID').getValue();
                        
                        if (SupplyBatchStatusID == "Open") {
                            if (thisObj.viewVar.OpsiDisplay == 'view') {
                                Ext.getCmp('Koltiva.view.Traceability_new.Batching.MainForm-FormBasicData-SupplyBatchNumber').setReadOnly(true);
                                Ext.getCmp('Koltiva.view.Traceability_new.Batching.MainForm-FormBasicData-SupplyBatchDate').setReadOnly(true);
                                Ext.getCmp('Koltiva.view.Traceability_new.Batching.MainForm-FormBasicData-ExternalBatchCode').setReadOnly(true);

                                Ext.getCmp('Koltiva.view.Traceability_new.Batching.PanelDataPurchase-BtnAdd').setVisible(false);
                                Ext.getCmp('Koltiva.view.Traceability_new.Batching.PanelDataPurchase-BtnClose').setVisible(false);
                                Ext.getCmp('Koltiva.view.Traceability_new.Batching.PanelDataPurchaseDetail-BtnComplete').setVisible(false);
                                Ext.getCmp('Koltiva.view.Traceability_new.Batching.PanelDataPurchaseDetail-ActionColumn').setVisible(false);
                                Ext.getCmp('Koltiva.view.Traceability_new.Batching.MainForm-FormBasicData-BtnSave').setVisible(false);
                                Ext.getCmp('Koltiva.view.Traceability_new.Batching.MainForm-FormBasicData-BtnProcess').setVisible(false);
                            } else {
                                Ext.getCmp('Koltiva.view.Traceability_new.Batching.MainForm-FormBasicData-SupplyBatchNumber').setVisible(true);
                                Ext.getCmp('Koltiva.view.Traceability_new.Batching.MainForm-FormBasicData-SupplyBatchDate').setVisible(true);
                                Ext.getCmp('Koltiva.view.Traceability_new.Batching.MainForm-FormBasicData-ExternalBatchCode').setVisible(true);

                                Ext.getCmp('Koltiva.view.Traceability_new.Batching.PanelDataPurchase-BtnAdd').setVisible(true);
                                Ext.getCmp('Koltiva.view.Traceability_new.Batching.PanelDataPurchase-BtnClose').setVisible(true);
                                Ext.getCmp('Koltiva.view.Traceability_new.Batching.PanelDataPurchaseDetail-BtnComplete').setVisible(false);
                                Ext.getCmp('Koltiva.view.Traceability_new.Batching.PanelDataPurchaseDetail-ActionColumn').setVisible(false);
                                Ext.getCmp('Koltiva.view.Traceability_new.Batching.MainForm-FormBasicData-BtnSave').setVisible(false);
                                Ext.getCmp('Koltiva.view.Traceability_new.Batching.MainForm-FormBasicData-BtnProcess').setVisible(false);
                            }
                        } else {
                                Ext.getCmp('Koltiva.view.Traceability_new.Batching.MainForm-FormBasicData-SupplyBatchNumber').setReadOnly(true);
                                Ext.getCmp('Koltiva.view.Traceability_new.Batching.MainForm-FormBasicData-SupplyBatchDate').setReadOnly(true);
                                Ext.getCmp('Koltiva.view.Traceability_new.Batching.MainForm-FormBasicData-ExternalBatchCode').setReadOnly(true);

                                Ext.getCmp('Koltiva.view.Traceability_new.Batching.PanelDataPurchase-BtnAdd').setVisible(false);
                                Ext.getCmp('Koltiva.view.Traceability_new.Batching.PanelDataPurchase-BtnClose').setVisible(false);
                                Ext.getCmp('Koltiva.view.Traceability_new.Batching.PanelDataPurchaseDetail-BtnComplete').setVisible(false);
                                Ext.getCmp('Koltiva.view.Traceability_new.Batching.PanelDataPurchaseDetail-ActionColumn').setVisible(false);
                                Ext.getCmp('Koltiva.view.Traceability_new.Batching.MainForm-FormBasicData-BtnSave').setVisible(false);
                                Ext.getCmp('Koltiva.view.Traceability_new.Batching.MainForm-FormBasicData-BtnProcess').setVisible(false);
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
                Ext.getCmp('Koltiva.view.Traceability_new.Batching.MainForm-FormBasicData-SupplyBatchStatusID').setValue('1');
                Ext.getCmp('Koltiva.view.Traceability_new.Batching.MainForm-FormBasicData-BtnSave').setVisible(false);
                Ext.getCmp('Koltiva.view.Traceability_new.Batching.MainForm-FormBasicData-BtnProcess').setVisible(false);
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
        var labelWidth = 200;

        //Store ==================================== (Begin)
        var BatchStatus = Ext.create('Ext.data.Store', {
            fields: ['id', 'label'],
            data: [{
                "id": "1",
                "label": lang("Open")
            }, {
                "id": "2",
                "label": lang("Closed")
            }, {
                "id": "3",
                "label": lang("Sent")
            },{
                "id": "4",
                "label": lang("Delivered")
            }]
        });
        //Store ==================================== (End)

        //Additional Panel ==================================== (Begin)
        thisObj.ObjPanelDataPurchase = Ext.create('Koltiva.view.Traceability_new.Batching.PanelDataPurchase', {
            viewVar: {
                SupplyBatchID: thisObj.viewVar.SupplyBatchID
            }
        });

        if (thisObj.viewVar.OpsiDisplay == 'view' || thisObj.viewVar.OpsiDisplay == 'update') {
            thisObj.ObjPanelDataPurchaseDetail = Ext.create('Koltiva.view.Traceability_new.Batching.PanelDataPurchaseDetail', {
                viewVar: {
                    SupplyBatchID: thisObj.viewVar.SupplyBatchID
                }
            });

            thisObj.ObjPanelDataPurchase.show()

            if (thisObj.viewVar.SupplyBatchStatusID == "4") {
                thisObj.ObjPanelDataPurchaseDetail.show()
            } else {
                thisObj.ObjPanelDataPurchaseDetail.hide()
            }

        } else {
            thisObj.ObjPanelDataPurchase.show()
        }
        
        //Additional Panel ==================================== (End)

        //Panel Basic ==================================== (Begin)

        thisObj.ObjPanelBasicData = Ext.create('Ext.panel.Panel', {
            title: lang('Batch Form'),
            frame: true,
            cls: 'Sfr_PanelLayoutForm',
            id: 'Koltiva.view.Traceability_new.Batching.MainForm-FormGeneralData',
            collapsible: true,
            items: [{
                    xtype: 'form',
                    id: 'Koltiva.view.Traceability_new.Batching.MainForm-FormBasicData',
                    buttonAlign: 'right',
                    cls: 'Sfr_PanelSubLayoutForm',
                    items: [{
                            xtype: 'panel',
                            title: lang('Information'),
                            frame: false,
                            id: 'Koltiva.view.Traceability_new.Batching.MainForm-FormBasicData-SectionProcessing',
                            style: 'margin-top:15px;margin-left:10px;margin-right:10px',
                            cls: 'Sfr_PanelSubLayoutFormRoundedGray',
                            items: [{
                                layout: 'column',
                                border: false,
                                items: [{
                                        columnWidth: 0.435,
                                        layout: 'form',
                                        style: 'padding:10px 5px 10px 20px;',
                                        defaults: {
                                            labelAlign: 'left',
                                            labelWidth: 150
                                        },
                                        items: [{
                                            xtype: 'textfield',
                                            id: 'Koltiva.view.Traceability_new.Batching.MainForm-FormBasicData-SupplyBatchID',
                                            name: 'Koltiva.view.Traceability_new.Batching.MainForm-FormBasicData-SupplyBatchID',
                                            inputType: 'hidden'
                                        },{
                                            xtype: 'textfield',
                                            id: 'Koltiva.view.Traceability_new.Batching.MainForm-FormBasicData-SupplyBatchNumber',
                                            name: 'Koltiva.view.Traceability_new.Batching.MainForm-FormBasicData-SupplyBatchNumber',
                                            fieldLabel: lang('Batch Number'),
                                            readOnly: true,
                                        },{
                                            xtype: 'datefield',
                                            id: 'Koltiva.view.Traceability_new.Batching.MainForm-FormBasicData-SupplyBatchDate',
                                            name: 'Koltiva.view.Traceability_new.Batching.MainForm-FormBasicData-SupplyBatchDate',
                                            format: 'Y-m-d',
                                            allowBlank: false,
                                            baseCls: 'Sfr_FormInputMandatory',
                                            fieldLabel: lang('Date Created'),
                                            enableKeyEvents: true,
                                            listeners: {
                                                keydown : function (field_, e_  )  {
                                                    e_.stopEvent();
                                                    return false;
                                                }
                                            }
                                        },
                                        // {
                                        //     xtype: 'textfield',
                                        //     id: 'Koltiva.view.Traceability_new.Batching.MainForm-FormBasicData-ExternalBatchCode',
                                        //     name: 'Koltiva.view.Traceability_new.Batching.MainForm-FormBasicData-ExternalBatchCode',
                                        //     fieldLabel: lang('External Batch Code'),
                                        // }
                                    ]
                                    }, {
                                        columnWidth: 0.435,
                                        layout: 'form',
                                        style: 'padding:10px 0px 10px 20px;',
                                        defaults: {
                                            labelAlign: 'left',
                                            labelWidth: 150
                                        },
                                        items: [{
                                            xtype: 'combobox',
                                            id: 'Koltiva.view.Traceability_new.Batching.MainForm-FormBasicData-SupplyBatchStatusID',
                                            name: 'Koltiva.view.Traceability_new.Batching.MainForm-FormBasicData-SupplyBatchStatusID',
                                            store: BatchStatus,
                                            queryMode: 'local',
                                            displayField: 'label',
                                            valueField: 'id',
                                            readOnly: true,
                                            fieldLabel: lang('Supply Batch Status')
                                        }
                                        ]
                                    }]
                            }, {
                                layout: 'column',
                                border: false,
                                items: [{
                                    columnWidth: 1,
                                    items: [
                                        thisObj.ObjPanelDataPurchase
                                    ]
                                },{
                                    columnWidth: 1,
                                    items: [
                                        thisObj.ObjPanelDataPurchaseDetail
                                    ]
                                }, {
                                    //RIGHT CONTENT
                                    columnWidth: 0.35,
                                    items: [
                                        thisObj.ObjPanelFinal
                                    ]
                                }]
                            }]
                        }],
                    buttons: [
                        {
                            xtype: 'button',
                            icon: varjs.config.base_url + 'images/icons/new/save.png',
                            text: lang('Process'),
                            cls: 'Sfr_BtnFormGreen',
                            overCls: 'Sfr_BtnFormGreen-Hover',
                            id: 'Koltiva.view.Traceability_new.Batching.MainForm-FormBasicData-BtnProcess',
                            handler: function () {
                                Ext.getCmp('Koltiva.view.Traceability_new.Batching.MainForm').destroy(); //destory current view

                                if(Ext.getCmp('Koltiva.view.Traceability_new.Batching.MainGrid') == undefined){
                                    Ext.create('Koltiva.view.Traceability_new.Batching.MainGrid');
                                }else{
                                    Ext.create('Koltiva.view.Traceability_new.Batching.MainGrid');
                                }
                            }
                        },
                        {
                            xtype: 'button',
                            icon: varjs.config.base_url + 'images/icons/new/save.png',
                            text: lang('Finish'),
                            cls: 'Sfr_BtnFormBlue',
                            overCls: 'Sfr_BtnFormBlue-Hover',
                            id: 'Koltiva.view.Traceability_new.Batching.MainForm-FormBasicData-BtnSave',
                            handler: function () {
                                var Formnya = Ext.getCmp('Koltiva.view.Traceability_new.Batching.MainForm-FormBasicData').getForm();

                                if (Formnya.isValid()) {

                                    Formnya.submit({
                                        url: m_api + '/traceability_api/batching/data_supplychain_batch',
                                        method: 'POST',
                                        waitMsg: 'Saving data...',
                                        params: {
                                            OpsiDisplay: thisObj.viewVar.OpsiDisplay
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
                                                        Ext.getCmp('Koltiva.view.Traceability_new.Batching.MainForm').destroy(); //destory current view
                                                        var MainForm = [];
                                                        if (Ext.getCmp('Koltiva.view.Traceability_new.Batching.MainForm') == undefined) {
                                                            MainForm = Ext.create('Koltiva.view.Traceability_new.Batching.MainForm', {
                                                                viewVar: {
                                                                    OpsiDisplay: 'view',
                                                                    SupplyBatchID: o.result.SupplyBatchID,
                                                                    SupplyBatchStatusID : o.result.SupplyBatchStatusID
                                                                }
                                                            });
                                                        } else {
                                                            Ext.getCmp('Koltiva.view.Traceability_new.Batching.MainForm').destroy();
                                                            MainForm = Ext.create('Koltiva.view.Traceability_new.Batching.MainForm', {
                                                                viewVar: {
                                                                    
                                                                    OpsiDisplay: 'view',
                                                                    SupplyBatchID: o.result.SupplyBatchID,
                                                                    SupplyBatchStatusID : o.result.SupplyBatchStatusID
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
                                        msg: lang('Form not complete yet'),
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
                        id: 'Koltiva.view.Traceability_new.Batching.MainForm-labelInfoInsert',
                        html: '<div id="header_title_farmer">' + lang('Batching') + '</div>'
                    }]
            }, {
                items: [{
                        id: 'Koltiva.view.Traceability_new.Batching.MainForm-LinkBackToList',
                        html: '<div id="Sfr_IdBoxInfoDataGrid" class="Sfr_BoxInfoDataGrid"><ul class="Sft_UlListInfoDataGrid"><li class="Sft_ListInfoDataGrid"><a href="javascript:Ext.getCmp(\'Koltiva.view.Traceability_new.Batching.MainForm\').BackToList()"><img class="Sft_ListIconInfoDataGrid" src="' + varjs.config.base_url + 'images/icons/new/back.png" width="20" />&nbsp;&nbsp;' + lang('Back to Batch List') + '</a></li></div>'
                    }]
            }, {
                html: '<br />'
            }, {
                layout: 'column',
                border: false,
                items: [{
                        //LEFT CONTENT
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
        Ext.getCmp('Koltiva.view.Traceability_new.Batching.MainForm').destroy(); //destory current view
        var GridMain = [];
        if (Ext.getCmp('Koltiva.view.Traceability_new.Batching.MainGrid') == undefined) {
            GridMain = Ext.create('Koltiva.view.Traceability_new.Batching.MainGrid');
        } else {
            //destroy, create ulang
            Ext.getCmp('Koltiva.view.Traceability_new.Batching.MainGrid').destroy();
            GridMain = Ext.create('Koltiva.view.Traceability_new.Batching.MainGrid');
        }
    }
});
Ext.define('Koltiva.view.Traceability_new.Batching.WinFormDataBatchingStep', {
    extend: 'Ext.window.Window',
    id: 'Koltiva.view.Traceability_new.Batching.WinFormDataBatchingStep',
    cls: 'Sfr_LayoutPopupWindows',
    title: lang('Form Data BatchStep'),
    closable: true,
    modal: true,
    closeAction: 'destroy',
    width: '30%',
    //height: 450,
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
                var FormNya = Ext.getCmp('Koltiva.view.Traceability_new.Batching.WinFormDataBatchingStep-Form');

                FormNya.getForm().load({
                    url: m_api + '/traceability_api/batching/data_supplychain_batch_batching_form_open',
                    method: 'GET',
                    params: {
                        SupplyBatchBatchingID: thisObj.viewVar.SupplyBatchBatchingID
                    },
                    success: function (form, action) {
                        var r = Ext.decode(action.response.responseText);
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

            if (sessionStorage.getItem('recordPanelDataPurchaseDetail') == 0) {
                Ext.getCmp('Koltiva.view.Traceability_new.Batching.WinFormDataBatchingStep-Form-WeightBefore').setValue(sessionStorage.getItem('nettWeight'))
            } else {
                Ext.getCmp('Koltiva.view.Traceability_new.Batching.WinFormDataBatchingStep-Form-WeightBefore').setValue(sessionStorage.getItem('maxpaluePanelDataPurchaseDetail'))
            }

        }
    },
    initComponent: function () {

        var thisObj = this;
        var labelWidth = 150;

        //Store ==================================== (Begin)
        thisObj.BatchingStep = Ext.create('Koltiva.store.Traceability_new.Batching.BatchingStepGroup');
        
        //Store ==================================== (End)
    
        //items -------------------------------------------------------------- (begin)
        thisObj.items = [{
            xtype: 'form',
            id: 'Koltiva.view.Traceability_new.Batching.WinFormDataBatchingStep-Form',
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
                        id: 'Koltiva.view.Traceability_new.Batching.WinFormDataBatchingStep-Form-SupplyBatchBatchingID',
                        name: 'Koltiva.view.Traceability_new.Batching.WinFormDataBatchingStep-Form-SupplyBatchBatchingID'
                    },{
                        xtype: 'combobox',
                        id: 'Koltiva.view.Traceability_new.Batching.WinFormDataBatchingStep-Form-BatchingStepID',
                        name: 'Koltiva.view.Traceability_new.Batching.WinFormDataBatchingStep-Form-BatchingStepID',
                        store: thisObj.BatchingStep,
                        queryMode: 'local',
                        displayField: 'label',
                        valueField: 'id',
                        allowBlank: false,
                        baseCls: 'Sfr_FormInputMandatory',
                        fieldLabel: lang('Batch Step'),
                        enableKeyEvents: true,
                        listeners: {
                            keydown : function (field_, e_  )  {
                                e_.stopEvent();
                                return false;
                            }
                        }
                    },
                    {
                        xtype: 'datefield',
                        id: 'Koltiva.view.Traceability_new.Batching.WinFormDataBatchingStep-Form-ProcessStartDate',
                        name: 'Koltiva.view.Traceability_new.Batching.WinFormDataBatchingStep-Form-ProcessStartDate',
                        format: 'Y-m-d',
                        allowBlank: false,
                        baseCls: 'Sfr_FormInputMandatory',
                        labelWidth: labelWidth,
                        fieldLabel: lang('Start Date'),
                        enableKeyEvents: true,
                        listeners: {
                            keydown : function (field_, e_  )  {
                                e_.stopEvent();
                                return false;
                            }
                        }
                    },{
                        xtype: 'datefield',
                        id: 'Koltiva.view.Traceability_new.Batching.WinFormDataBatchingStep-Form-ProcessEndDate',
                        name: 'Koltiva.view.Traceability_new.Batching.WinFormDataBatchingStep-Form-ProcessEndDate',
                        format: 'Y-m-d',
                        allowBlank: false,
                        baseCls: 'Sfr_FormInputMandatory',
                        labelWidth: labelWidth,
                        fieldLabel: lang('End Date'),
                        enableKeyEvents: true,
                        listeners: {
                            keydown : function (field_, e_  )  {
                                e_.stopEvent();
                                return false;
                            }
                        }
                    },
                    {
                        xtype: 'numberfield',
                        id: 'Koltiva.view.Traceability_new.Batching.WinFormDataBatchingStep-Form-WeightBefore',
                        name: 'Koltiva.view.Traceability_new.Batching.WinFormDataBatchingStep-Form-WeightBefore',
                        fieldLabel: lang('Total Weight'),
                        labelWidth: labelWidth,
                        allowBlank: false,
                        baseCls: 'Sfr_FormInputMandatory',
                        minValue: 0
                    },
                    {
                        xtype: 'numberfield',
                        id: 'Koltiva.view.Traceability_new.Batching.WinFormDataBatchingStep-Form-WeightAfter',
                        name: 'Koltiva.view.Traceability_new.Batching.WinFormDataBatchingStep-Form-WeightAfter',
                        fieldLabel: lang('Weight After Batching'),
                        labelWidth: labelWidth,
                        allowBlank: false,
                        baseCls: 'Sfr_FormInputMandatory',
                        minValue:0,
                        listeners: {
                            change : function(record){
                                var weightBefore = Ext.getCmp('Koltiva.view.Traceability_new.Batching.WinFormDataBatchingStep-Form-WeightBefore').getValue();
                                var weightAfter  = record.getValue();

                                if (parseFloat(weightAfter) > parseFloat(weightBefore)){
                                    Ext.getCmp('Koltiva.view.Traceability_new.Batching.WinFormDataBatchingStep-Form-WeightAfter').setValue("");

                                    Ext.MessageBox.show({
                                        title: 'Warning',
                                        msg: 'Weight After should not more than Weight Before !.',
                                        buttons: Ext.MessageBox.OK,
                                        animateTarget: 'mb9',
                                        icon: 'ext-mb-error'
                                    });
                                }
                            }
                        }
                    },
                    {
                        xtype: 'textareafield',
                        id: 'Koltiva.view.Traceability_new.Batching.WinFormDataBatchingStep-Form-Remark',
                        name: 'Koltiva.view.Traceability_new.Batching.WinFormDataBatchingStep-Form-Remark',
                        fieldLabel: lang('Note'),
                        labelWidth: labelWidth
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
            id: 'Koltiva.view.Traceability_new.Batching.WinFormDataBatchingStep-Form-BtnSave',
            handler: function () {

                var Formnya = Ext.getCmp('Koltiva.view.Traceability_new.Batching.WinFormDataBatchingStep-Form').getForm();

                if (Formnya.isValid()) {
                    Formnya.submit({
                        url: m_api + '/traceability_api/batching/data_supplychain_batch_batching',
                        method: 'POST',
                        waitMsg: lang('Saving data'),
                        params: {
                            OpsiDisplay: thisObj.viewVar.OpsiDisplay,
                            SupplyBatchID: thisObj.viewVar.SupplyBatchID
                        },
                        success: function (fp, o) {
                            var r = Ext.decode(o.response.responseText);
                            Ext.MessageBox.show({
                                title: 'Information',
                                msg: r.message,
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
                                                    OpsiDisplay: 'update',
                                                    SupplyBatchID: o.result.SupplyBatchID,
                                                    SupplyBatchStatusID : o.result.SupplyBatchStatusID
                                                }
                                            });
                                        } else {
                                            Ext.getCmp('Koltiva.view.Traceability_new.Batching.MainForm').destroy();
                                            MainForm = Ext.create('Koltiva.view.Traceability_new.Batching.MainForm', {
                                                viewVar: {
                                                    OpsiDisplay: 'update',
                                                    SupplyBatchID: o.result.SupplyBatchID,
                                                    SupplyBatchStatusID : o.result.SupplyBatchStatusID
                                                }
                                            });
                                        }
                                    }
                                }
                            });

                            thisObj.close();
                            // thisObj.viewVar.StoreGridMain.load();
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
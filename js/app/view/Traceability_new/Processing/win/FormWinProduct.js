Ext.define('Koltiva.view.Traceability_new.Processing.win.FormWinProduct' ,{
    extend: 'Ext.window.Window',
    id: 'Koltiva.view.Traceability_new.Processing.win.FormWinProduct',
    title:lang('Form Product'),
    closable: true,
    modal: true,
    closeAction: 'destroy',
    width: '30%',
    maxHeight: 700,
//    height: '50%',
    overflowY: 'auto',
    viewVar: false,
    setViewVar: function(value){
        this.viewVar = value;
    },
    listeners: {
        afterRender: function(){
            var thisObj = this;

            //form reset
            Ext.getCmp('Koltiva.view.Traceability_new.Processing.win.FormWinProduct-Form').getForm().reset();

            // Ext.MessageBox.show({
            //     msg: 'Please wait...',
            //     progressText: 'Loading...',
            //     width: 300,
            //     wait: true,
            //     waitConfig: {
            //         interval: 200
            //     },
            //     icon: 'ext-mb-info', //custom class in msg-box.html
            //     animateTarget: 'mb9'
            // });

            Ext.Ajax.request({
                waitMsg: lang('Please Wait'),
                url: m_api + '/processing/transaction/fetchvehiclebyID',
                method: 'GET',
                params: {
                    ProcessingProductID: this.viewVar.ProcessingProductID,
                    ProcessingID: this.viewVar.ProcessingID
                },
                success: function(response, opts){
                    var r = Ext.decode(response.responseText);
                    Ext.getCmp('Koltiva.view.Traceability_new.Processing.win.FormWinProduct-Form-ProcessingID').setValue(r.ProcessingID);
                    Ext.getCmp('Koltiva.view.Traceability_new.Processing.win.FormWinProduct-Form-FlagOer').setValue(r.FlagOer);
                    Ext.getCmp('Koltiva.view.Traceability_new.Processing.win.FormWinProduct-Form-ProcessingVolume').setValue(r.ProcessingVolume);
                    Ext.getCmp('Koltiva.view.Traceability_new.Processing.win.FormWinProduct-Form-ProcessingProductID').setValue(r.ProcessingProductID);
                    Ext.getCmp('Koltiva.view.Traceability_new.Processing.win.FormWinProduct-Form-ProductID').setValue(r.ProductID);
                    Ext.getCmp('Koltiva.view.Traceability_new.Processing.win.FormWinProduct-Form-ProductPercentage').setValue(r.ProductPercentage);
                    Ext.getCmp('Koltiva.view.Traceability_new.Processing.win.FormWinProduct-Form-ProductVolume').setValue(r.ProductVolume);
                    Ext.MessageBox.hide();
                },
                failure: function(response, opts){
                    //Ext.MessageBox.hide();
                    Ext.MessageBox.show({
                        title: 'Failed',
                        msg: lang('Failed to retrieve data'),
                        buttons: Ext.MessageBox.OK,
                        animateTarget: 'mb9',
                        icon: 'ext-mb-error'
                    });
                }
            });

            //if (thisObj.viewVar.OpsiDisplay == 'update') {
                // Ext.getCmp('Koltiva.view.Traceability_new.Processing.win.FormWinProduct-Form').getForm().load({
                //     url: m_api + '/processing/transaction/fetchvehiclebyID',
                //     method: 'GET',
                //     params: {
                //         ProcessingProductID: this.viewVar.ProcessingProductID,
                //         ProcessingID: this.viewVar.ProcessingID
                //     },
                //     success: function(form, action) {
                //         var r = Ext.decode(action.response.responseText);
                //     },
                //     failure: function(form, action) {
                //         Ext.MessageBox.show({
                //             title: 'Failed',
                //             msg: 'Failed to retrieve data',
                //             buttons: Ext.MessageBox.OK,
                //             animateTarget: 'mb9',
                //             icon: 'ext-mb-error'
                //         });
                //     }
                // });
            //}
        }
    },
    initComponent: function() {
        var thisObj = this;

        var cmb_product = Ext.create('Koltiva.store.Traceability_new.Processing.CmbProduct');

        // var cmb_product_type = Ext.create('Koltiva.store.Traceability_new.Processing.ProductType', {
        //     storeVar: {
        //         ProductID : thisObj.viewVar.ProductID 
        //     } 
        // });

        //items -------------------------------------------------------------- (begin)
        thisObj.items = [{
        	xtype: 'form',
            id: 'Koltiva.view.Traceability_new.Processing.win.FormWinProduct-Form',
            padding:'5 25 5 8',
            items:[{
                layout: 'column',
                border: false,
                items:[{
                    columnWidth: 1,
                    layout:'form',
                    items:[{
                        xtype: 'hiddenfield',
                        id: 'Koltiva.view.Traceability_new.Processing.win.FormWinProduct-Form-ProcessingProductID',
                        name: 'Koltiva.view.Traceability_new.Processing.win.FormWinProduct-Form-ProcessingProductID'
                    },{
                        xtype: 'hiddenfield',
                        id: 'Koltiva.view.Traceability_new.Processing.win.FormWinProduct-Form-ProcessingID',
                        name: 'Koltiva.view.Traceability_new.Processing.win.FormWinProduct-Form-ProcessingID',
                        value: thisObj.viewVar.ProcessingID
                    },{
                        xtype: 'hiddenfield',
                        id: 'Koltiva.view.Traceability_new.Processing.win.FormWinProduct-Form-FlagOer',
                        name: 'Koltiva.view.Traceability_new.Processing.win.FormWinProduct-Form-FlagOer',
                        listeners: {
                            change: function (cb, nv, ov) {
                                var FlagOer = Ext.getCmp('Koltiva.view.Traceability_new.Processing.win.FormWinProduct-Form-FlagOer').getValue();
                                if(FlagOer=='2'){
                                    Ext.getCmp('Koltiva.view.Traceability_new.Processing.win.FormWinProduct-Form-ProductVolume').setReadOnly(true);
                                    Ext.getCmp('Koltiva.view.Traceability_new.Processing.win.FormWinProduct-Form-ProductPercentage').show();
                                }else{
                                    Ext.getCmp('Koltiva.view.Traceability_new.Processing.win.FormWinProduct-Form-ProductVolume').setReadOnly(false);
                                    Ext.getCmp('Koltiva.view.Traceability_new.Processing.win.FormWinProduct-Form-ProductPercentage').hide();
                                }
                            }
                        }
                    },{
                        xtype: 'hiddenfield',
                        id: 'Koltiva.view.Traceability_new.Processing.win.FormWinProduct-Form-ProcessingVolume',
                        name: 'Koltiva.view.Traceability_new.Processing.win.FormWinProduct-Form-ProcessingVolume'
                    },{
                        xtype: 'combobox',
                        id: 'Koltiva.view.Traceability_new.Processing.win.FormWinProduct-Form-ProductID',
                        name: 'Koltiva.view.Traceability_new.Processing.win.FormWinProduct-Form-ProductID',
                        store: cmb_product,
                        fieldLabel: lang('Product'),
                        labelWidth: 200,
                        queryMode: 'local',
                        displayField: 'label',
                        valueField: 'id'
                    },{
                        xtype: 'numericfield',
                        id: 'Koltiva.view.Traceability_new.Processing.win.FormWinProduct-Form-ProductPercentage',
                        name: 'Koltiva.view.Traceability_new.Processing.win.FormWinProduct-Form-ProductPercentage',
                        fieldLabel: lang('Product Percentage (%)'),
                        labelWidth: 200,
                        listeners: {
                            change: function (cb, nv, ov) {
                                var ProcessingVolume = Ext.getCmp('Koltiva.view.Traceability_new.Processing.win.FormWinProduct-Form-ProcessingVolume').getValue();
                                var ProductPercentage = Ext.getCmp('Koltiva.view.Traceability_new.Processing.win.FormWinProduct-Form-ProductPercentage').getValue();
                                var ProductVolume = (parseFloat(ProcessingVolume)*parseFloat(ProductPercentage)/100).toFixed(2);
                                Ext.getCmp('Koltiva.view.Traceability_new.Processing.win.FormWinProduct-Form-ProductVolume').setValue(ProductVolume);
                            }
                        }
                    },{
                        xtype: 'numericfield',
                        id: 'Koltiva.view.Traceability_new.Processing.win.FormWinProduct-Form-ProductVolume',
                        name: 'Koltiva.view.Traceability_new.Processing.win.FormWinProduct-Form-ProductVolume',
                        fieldLabel: lang('Product Volume (kg)'),
                        labelWidth: 200
                    }]
                }]
            }]
        }];
        //items -------------------------------------------------------------- (end)

        //buttons -------------------------------------------------------------- (begin)
        thisObj.buttons = [{
            text: lang('Save'),
            id: 'Koltiva.view.Traceability_new.Processing.win.FormWinProduct-Form-BtnSave',
            icon: varjs.config.base_url + 'images/icons/new/save.png',
            cls: 'Sfr_BtnFormBlue',
            overCls: 'Sfr_BtnFormBlue-Hover',
            handler: function () {
            	var FormNya = Ext.getCmp('Koltiva.view.Traceability_new.Processing.win.FormWinProduct-Form').getForm();
                if (FormNya.isValid()) {
                    FormNya.submit({
                        url: m_api + '/processing/transaction/vehicle_list',
                        method:'POST',
                        waitMsg: 'Saving data...',
                        success: function(rp, o){
                            var r = Ext.decode(o.response.responseText);
                            Ext.MessageBox.show({
                                title: 'Information',
                                msg: lang('Product Added'),
                                buttons: Ext.MessageBox.OK,
                                animateTarget: 'mb9',
                                icon: 'ext-mb-success'
                            });
                            
                            //load store CallerStore
                            Ext.getCmp('Koltiva.view.Traceability_new.Processing.GridProduct-Grid').getStore().load();
                            thisObj.close();
                        },
                        failure: function(rp, o){
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
                }else{
                    Ext.MessageBox.show({
                        title: 'Attention',
                        msg: lang('Form not valid yet'),
                        buttons: Ext.MessageBox.OK,
                        animateTarget: 'mb9',
                        icon: 'ext-mb-info'
                    });
                }
            }
        },{
            text: lang('Close'),
            icon: varjs.config.base_url + 'images/icons/new/close.png',
            cls: 'Sfr_BtnFormGrey',
            overCls: 'Sfr_BtnFormGrey-Hover',
            handler: function() {
                thisObj.close();
            }
        }];
        //buttons -------------------------------------------------------------- (end)

        this.callParent(arguments);
    }
});
Ext.define('Koltiva.view.Traceability_new.Delivery.WinApplyFilter', {
    extend: 'Ext.window.Window',
    id: 'Koltiva.view.Traceability_new.Delivery.WinApplyFilter',
    cls: 'Sfr_LayoutPopupWindows',
    title: lang('Apply Filter'),
    closable: true,
    modal: true,
    closeAction: 'destroy',
    width: '40%',
    height: 320,
    overflowY: 'auto',
    initComponent: function () {
        var thisObj = this;

        thisObj.DeliveryStatus = Ext.create('Koltiva.store.Traceability_new.Delivery.DeliveryStatus');
        thisObj.StoreComboDestination = Ext.create('Koltiva.store.Traceability_new.Delivery.StoreComboDestination');
        thisObj.items = [{
                xtype: 'panel',
                border: false,
                padding: '5 12 5 5',
                items: [{
                        layout: 'column',
                        border: false,
                        items: [{
                                columnWidth: 1,
                                items: [{
                                        layout: 'column',
                                        border: false,
                                        style: 'margin-top:12px;',
                                        items: []
                                    },
                                    {
                                        layout: 'column',
                                        border: false,
                                        style: 'margin-top:12px;',
                                        items: [{
                                                columnWidth: 0.2,
                                                layout: 'form',
                                                items: [{
                                                        xtype: 'label',
                                                        cls: 'x-form-item-label',
                                                        text: lang('External Code') + ':',
                                                    }]
                                            }, {
                                                columnWidth: 0.8,
                                                border: false,
                                                layout: 'column',
                                                items: [{
                                                        xtype: 'textfield',
                                                        width: 375,
                                                        id: 'Koltiva.view.Traceability_new.Delivery.WinApplyFilter-TextFilterExternalCode',
                                                        name: 'Koltiva.view.Traceability_new.Delivery.WinApplyFilter-TextFilterExternalCode'
                                                    }]
                                            }]
                                    },
                                    // {
                                    //     layout: 'column',
                                    //     border: false,
                                    //     items: [{
                                    //             columnWidth: 0.2,
                                    //             layout: 'form',
                                    //             items: [{
                                    //                     xtype: 'label',
                                    //                     cls: 'x-form-item-label',
                                    //                     text: lang('Destination Name') + ':',
                                    //                 }]
                                    //         }, 
                                    //         {
                                    //             columnWidth: 0.8,
                                    //             border: false,
                                    //             layout: 'column',
                                    //             items: [{
                                    //                     xtype: 'combobox',
                                    //                     width: 375,
                                    //                     id: 'Koltiva.view.Traceability_new.Delivery.WinApplyFilter-TextFilterDestinationID',
                                    //                     name: 'Koltiva.view.Traceability_new.Delivery.WinApplyFilter-TextFilterDestinationID',
                                    //                     store: thisObj.StoreComboDestination,
                                    //                     queryMode: 'local',
                                    //                     displayField: 'label',
                                    //                     valueField: 'id',
                                    //                     enableKeyEvents: true,
                                    //                     listeners: {
                                    //                         keydown : function (field_, e_  )  {
                                    //                             e_.stopEvent();
                                    //                             return false;
                                    //                         }
                                    //                     }
                                    //                 }]
                                    //         }
                                    //     ]
                                    // },
                                     {
                                        layout: 'column',
                                        border: false,
                                        items: [{
                                                columnWidth: 0.2,
                                                layout: 'form',
                                                items: [{
                                                        xtype: 'label',
                                                        cls: 'x-form-item-label',
                                                        text: lang('Selling Status') + ':',
                                                    }]
                                            }, {
                                                columnWidth: 0.8,
                                                border: false,
                                                layout: 'column',
                                                items: [{
                                                        xtype: 'combobox',
                                                        width: 375,
                                                        id: 'Koltiva.view.Traceability_new.Delivery.WinApplyFilter-TextFilterDeliveryStatusID',
                                                        name: 'Koltiva.view.Traceability_new.Delivery.WinApplyFilter-TextFilterDeliveryStatusID',
                                                        store: thisObj.DeliveryStatus,
                                                        queryMode: 'local',
                                                        displayField: 'label',
                                                        valueField: 'id',
                                                        enableKeyEvents: true,
                                                        listeners: {
                                                            keydown : function (field_, e_  )  {
                                                                e_.stopEvent();
                                                                return false;
                                                            }
                                                        }
                                                    }]
                                            }]
                                    },{
                                        layout: 'column',
                                        border: false,
                                        items:[{
                                            columnWidth: 0.2,
                                            layout: 'form',
                                            items:[{
                                                xtype: 'label',
                                                cls: 'x-form-item-label',
                                                text: lang('Selling Date')+':',
                                            }]
                                        },{
                                            columnWidth: 0.8,
                                            border: false,
                                            layout: 'column',
                                            items:[{
                                                xtype: 'datefield',
                                                width: 160,
                                                id: 'Koltiva.view.Traceability_new.Delivery.WinApplyFilter-TextFilterStartDeliveryDate',
                                                name: 'Koltiva.view.Traceability_new.Delivery.WinApplyFilter-TextFilterStartDeliveryDate',
                                                format: 'Y-m-d',
                                                enableKeyEvents: true,
                                                listeners: {
                                                    keydown : function (field_, e_  )  {
                                                        e_.stopEvent();
                                                        return false;
                                                    }
                                                }
                                            },{
                                                xtype: 'label',
                                                cls: 'x-form-item-label',
                                                text: lang('to'),
                                                margin: '0px 15px 0 15px'
                                            },{
                                                xtype: 'datefield',
                                                width: 160,
                                                id: 'Koltiva.view.Traceability_new.Delivery.WinApplyFilter-TextFilterEndDeliveryDate',
                                                name: 'Koltiva.view.Traceability_new.Delivery.WinApplyFilter-TextFilterEndDeliveryDate',
                                                format: 'Y-m-d',
                                                enableKeyEvents: true,
                                                listeners: {
                                                    keydown : function (field_, e_  )  {
                                                        e_.stopEvent();
                                                        return false;
                                                    }
                                                }
                                            }]
                                        }]
                                    }]
                            }]
                    }]
            }];

        thisObj.buttons = [{
                icon: varjs.config.base_url + 'images/icons/new/search-white.png',
                text: lang('Apply Filter'),
                cls: 'Sfr_BtnFormBlue',
                overCls: 'Sfr_BtnFormBlue-Hover',
                handler: function () {
                    //Cek Validasi =================== (Begin)
                    thisObj.AddValidation = true;
                    thisObj.MsgAddValidation = "";
                    thisObj.AddValidationBasicForm();
                    
                    if (thisObj.AddValidation == true) {
                        var ArrFilter = [];
                        var ArrFilterLang = [];
                        let StartDeliveryDate = Ext.Date.format(Ext.getCmp('Koltiva.view.Traceability_new.Delivery.WinApplyFilter-TextFilterStartDeliveryDate').getValue(), 'Y-m-d');
                        let EndDeliveryDate = Ext.Date.format(Ext.getCmp('Koltiva.view.Traceability_new.Delivery.WinApplyFilter-TextFilterEndDeliveryDate').getValue(), 'Y-m-d');

                        if (Ext.getCmp('Koltiva.view.Traceability_new.Delivery.WinApplyFilter-TextFilterExternalCode').getValue() != "") {
                            ArrFilter.push('ExternalCode');
                            ArrFilterLang.push(lang('External Code'));
                        }

                        // if (Ext.getCmp('Koltiva.view.Traceability_new.Delivery.WinApplyFilter-TextFilterDestinationID').getValue() != null) {
                        //     ArrFilter.push('DestinationID');
                        //     ArrFilterLang.push(lang('Destination'));
                        // }

                        if (Ext.getCmp('Koltiva.view.Traceability_new.Delivery.WinApplyFilter-TextFilterDeliveryStatusID').getValue() != null) {
                            ArrFilter.push('DeliveryStatusID');
                            ArrFilterLang.push(lang('Selling Status'));
                        }

                        if (Ext.getCmp('Koltiva.view.Traceability_new.Delivery.WinApplyFilter-TextFilterStartDeliveryDate').getValue() != null) {
                            ArrFilter.push('StartDeliveryDate');
                            ArrFilterLang.push(lang('Start Delivery Date'));
                        }

                        if (Ext.getCmp('Koltiva.view.Traceability_new.Delivery.WinApplyFilter-TextFilterEndDeliveryDate').getValue() != null) {
                            ArrFilter.push('EndDeliveryDate');
                            ArrFilterLang.push(lang('End Delivery Date'));
                        }
                        //Cek filter apa saja yg dimasukkan ================================= (End)

                        //Set LocalStorage ================================= (Begin)
                        localStorage.setItem('cof_griddelivery_params', JSON.stringify({
                            ArrFilter: ArrFilter,
                            ArrFilterLang: ArrFilterLang,
                            TextFilterExernalCode: Ext.getCmp('Koltiva.view.Traceability_new.Delivery.WinApplyFilter-TextFilterExternalCode').getValue(),
                            // TextFilterDestinationID: Ext.getCmp('Koltiva.view.Traceability_new.Delivery.WinApplyFilter-TextFilterDestinationID').getValue(),
                            TextFilterDeliveryStatusID: Ext.getCmp('Koltiva.view.Traceability_new.Delivery.WinApplyFilter-TextFilterDeliveryStatusID').getValue(),
                            TextFilterStartDeliveryDate: StartDeliveryDate,
                            TextFilterEndDeliveryDate: EndDeliveryDate,
                        }));
                        //Set LocalStorage ================================= (End)

                        //reload store main grid
                        Ext.getCmp('Koltiva.view.Traceability_new.Delivery.MainGrid-Grid').getStore().loadPage(1);
                        thisObj.close();
                    } else {
                        Ext.MessageBox.show({
                            title: lang('Information'),
                            msg: thisObj.MsgAddValidation,
                            buttons: Ext.MessageBox.OK,
                            animateTarget: 'mb9',
                            icon: 'ext-mb-info'
                        });
                    }
                    //Cek Validasi =================== (End)
                }
            }, {
                icon: varjs.config.base_url + 'images/icons/new/delete.svg',
                text: lang('Reset'),
                cls: 'Sfr_BtnFormRed',
                overCls: 'Sfr_BtnFormRed-Hover',
                handler: function () {

                    Ext.getCmp('Koltiva.view.Traceability_new.Delivery.WinApplyFilter-TextFilterExternalCode').setValue('');
                    // Ext.getCmp('Koltiva.view.Traceability_new.Delivery.WinApplyFilter-TextFilterDestinationID').setValue(null);
                    Ext.getCmp('Koltiva.view.Traceability_new.Delivery.WinApplyFilter-TextFilterDeliveryStatusID').setValue('');
                    Ext.getCmp('Koltiva.view.Traceability_new.Delivery.WinApplyFilter-TextFilterStartDeliveryDate').setValue('');
                    Ext.getCmp('Koltiva.view.Traceability_new.Delivery.WinApplyFilter-TextFilterEndDeliveryDate').setValue('');
                    
                    localStorage.removeItem('cof_griddelivery_params');
                    thisObj.viewVar.StoreGridMain.load();

                    thisObj.close();
                }
            }, {
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
    listeners: {
        afterRender: function () {
            var thisObj = this;

            //ngeload filter parameters
            var cof_griddelivery_params = JSON.parse(localStorage.getItem('cof_griddelivery_params'));

            if (cof_griddelivery_params != null) {

                Ext.getCmp('Koltiva.view.Traceability_new.Delivery.WinApplyFilter-TextFilterExternalCode').setValue(cof_griddelivery_params.TextFilterExernalCode);
                // Ext.getCmp('Koltiva.view.Traceability_new.Delivery.WinApplyFilter-TextFilterDestinationID').setValue(cof_griddelivery_params.TextFilterDestinationID);
                Ext.getCmp('Koltiva.view.Traceability_new.Delivery.WinApplyFilter-TextFilterDeliveryStatusID').setValue(cof_griddelivery_params.TextFilterDeliveryStatusID);
                Ext.getCmp('Koltiva.view.Traceability_new.Delivery.WinApplyFilter-TextFilterStartDeliveryDate').setValue(cof_griddelivery_params.TextFilterStartDeliveryDate);
                Ext.getCmp('Koltiva.view.Traceability_new.Delivery.WinApplyFilter-TextFilterEndDeliveryDate').setValue(cof_griddelivery_params.TextFilterEndDeliveryDate);
            }
        }
    },
    AddValidationBasicForm: function() {
        var thisObj = this;
        var ArrMsg = [];
        thisObj.AddValidation = true;

        //Cek Date collection
        let StartDeliveryDate = Ext.getCmp('Koltiva.view.Traceability_new.Delivery.WinApplyFilter-TextFilterStartDeliveryDate').getValue();
        let EndDeliveryDate = Ext.getCmp('Koltiva.view.Traceability_new.Delivery.WinApplyFilter-TextFilterEndDeliveryDate').getValue();
        if( ( StartDeliveryDate != null && EndDeliveryDate == null ) || ( StartDeliveryDate != null && EndDeliveryDate == null ) ) {
            thisObj.AddValidation = false;
            ArrMsg.push(lang('Filter delivery date parameters not valid'));
        }

        if(StartDeliveryDate != null && EndDeliveryDate != null) {
            let ValidDate1 = moment(StartDeliveryDate, 'YYYY-MM-DD',true).isValid();
            let ValidDate2 = moment(EndDeliveryDate, 'YYYY-MM-DD',true).isValid();
            if(ValidDate1 == false || ValidDate2 == false) {
                thisObj.AddValidation = false;
                ArrMsg.push(lang('Filter delivery date parameters not valid'));
            }
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
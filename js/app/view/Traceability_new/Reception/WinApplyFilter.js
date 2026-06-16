Ext.define('Koltiva.view.Traceability_new.Reception.WinApplyFilter', {
    extend: 'Ext.window.Window',
    id: 'Koltiva.view.Traceability_new.Reception.WinApplyFilter',
    cls: 'Sfr_LayoutPopupWindows',
    title: lang('Apply Filter'),
    closable: true,
    modal: true,
    closeAction: 'destroy',
    width: '60%',
    height: 320,
    overflowY: 'auto',
    initComponent: function () {
        var thisObj = this;

        thisObj.ComboWarehouse = Ext.create('Koltiva.store.Traceability_new.Reception.StoreComboWarehouse');

        thisObj.items = [{
                xtype: 'panel',
                border: false,
                padding: '5 12 5 5',
                items: [{
                        layout: 'column',
                        border: false,
                        items: [{
                                columnWidth: 1,
                                items: [
                                    // {
                                    //     layout: 'column',
                                    //     border: false,
                                    //     style: 'margin-top:12px;',
                                    //     items: [{
                                    //             columnWidth: 0.2,
                                    //             layout: 'form',
                                    //             items: [{
                                    //                     xtype: 'label',
                                    //                     cls: 'x-form-item-label',
                                    //                     text: lang('Keyword') + ':',
                                    //                 }]
                                    //         }, {
                                    //             columnWidth: 0.8,
                                    //             border: false,
                                    //             layout: 'column',
                                    //             items: [{
                                    //                     xtype: 'textfield',
                                    //                     width: 375,
                                    //                     id: 'Koltiva.view.Traceability_new.Reception.WinApplyFilter-TextFilterKeyword',
                                    //                     name: 'Koltiva.view.Traceability_new.Reception.WinApplyFilter-TextFilterKeyword',
                                    //                     emptyText: lang('Search by Shipment Number / Ext Code'), 
                                    //                 }]
                                    //         }]
                                    // }, 
                                    {
                                        layout: 'column',
                                        border: false,
                                        items:[{
                                            columnWidth: 0.2,
                                            layout: 'form',
                                            items:[{
                                                xtype: 'label',
                                                cls: 'x-form-item-label',
                                                text: lang('Shipment Date')+':',
                                            }]
                                        },{
                                            columnWidth: 0.8,
                                            border: false,
                                            layout: 'column',
                                            items:[{
                                                xtype: 'datefield',
                                                width: 150,
                                                id: 'Koltiva.view.Traceability_new.Reception.WinApplyFilter-TextFilterStartShipmentDate',
                                                name: 'Koltiva.view.Traceability_new.Reception.WinApplyFilter-TextFilterStartShipmentDate',
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
                                                width: 150,
                                                id: 'Koltiva.view.Traceability_new.Reception.WinApplyFilter-TextFilterEndShipmentDate',
                                                name: 'Koltiva.view.Traceability_new.Reception.WinApplyFilter-TextFilterEndShipmentDate',
                                                // format: Ext.Date.format(new Date(), 'Y-m-d'),
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
                                    //                     text: lang('Mill') + ':',
                                    //                 }]
                                    //         }, {
                                    //             columnWidth: 0.8,
                                    //             border: false,
                                    //             layout: 'column',
                                    //             items: [{
                                    //                     xtype: 'combobox',
                                    //                     width: 375,
                                    //                     id: 'Koltiva.view.Traceability_new.Reception.WinApplyFilter-TextFilterWarehouseID',
                                    //                     name: 'Koltiva.view.Traceability_new.Reception.WinApplyFilter-TextFilterWarehouseID',
                                    //                     store: thisObj.ComboWarehouse,
                                    //                     queryMode: 'local',
                                    //                     displayField: 'label',
                                    //                     valueField: 'id',
                                    //                     listeners: {
                                    //                         change: function (record) {
                                                                
                                    //                         },
                                    //                         keydown : function (field_, e_  )  {
                                    //                             e_.stopEvent();
                                    //                             return false;
                                    //                         }
                                    //                     }
                                    //                 }]
                                    //         },
                                           
                                    //     ]
                                    // }
                                ]
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

                        let StartShipmentDate =  Ext.Date.format(Ext.getCmp('Koltiva.view.Traceability_new.Reception.WinApplyFilter-TextFilterStartShipmentDate').getValue(), 'Y-m-d');
                        let EndShipmenthDate  =  Ext.Date.format(Ext.getCmp('Koltiva.view.Traceability_new.Reception.WinApplyFilter-TextFilterEndShipmentDate').getValue(), 'Y-m-d');

                        // if (Ext.getCmp('Koltiva.view.Traceability_new.Reception.WinApplyFilter-TextFilterKeyword').getValue() != "") {
                        //     ArrFilter.push('Keyword');
                        //     ArrFilterLang.push(lang('Keyword'));
                        // }

                        // if (Ext.getCmp('Koltiva.view.Traceability_new.Reception.WinApplyFilter-TextFilterWarehouseID').getValue() != null) {
                        //     ArrFilter.push('WarehouseID');
                        //     ArrFilterLang.push(lang('Warehouse'));
                        // }


                        if (Ext.getCmp('Koltiva.view.Traceability_new.Reception.WinApplyFilter-TextFilterStartShipmentDate').getValue() != null) {
                            ArrFilter.push('StartShipmentDate');
                            ArrFilterLang.push(lang('Start Shipment Date'));
                        }

                        if (Ext.getCmp('Koltiva.view.Traceability_new.Reception.WinApplyFilter-TextFilterEndShipmentDate').getValue() != null) {
                            ArrFilter.push('EndShipmentDate');
                            ArrFilterLang.push(lang('End Shipment Date'));
                        }
                        //Cek filter apa saja yg dimasukkan ================================= (End)

                        //Set LocalStorage ================================= (Begin)
                        localStorage.setItem('cof_gridreception_params', JSON.stringify({
                            ArrFilter: ArrFilter,
                            ArrFilterLang: ArrFilterLang,
                            // TextFilterKeyword: Ext.getCmp('Koltiva.view.Traceability_new.Reception.WinApplyFilter-TextFilterKeyword').getValue(),
                            // TextFilterWarehouseID: Ext.getCmp('Koltiva.view.Traceability_new.Reception.WinApplyFilter-TextFilterWarehouseID').getValue(),
                            TextFilterStartShipmentDate: StartShipmentDate,
                            TextFilterEndShipmentDate: EndShipmenthDate
                        }));
                        //Set LocalStorage ================================= (End)

                        //reload store main grid
                        Ext.getCmp('Koltiva.view.Traceability_new.Reception.GridReception-Grid').getStore().loadPage(1);
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

                    // Ext.getCmp('Koltiva.view.Traceability_new.Reception.WinApplyFilter-TextFilterKeyword').setValue('');
                    // Ext.getCmp('Koltiva.view.Traceability_new.Reception.WinApplyFilter-TextFilterWarehouseID').setValue('');
                    Ext.getCmp('Koltiva.view.Traceability_new.Reception.WinApplyFilter-TextFilterStartShipmentDate').setValue('');
                    Ext.getCmp('Koltiva.view.Traceability_new.Reception.WinApplyFilter-TextFilterEndShipmentDate').setValue('');

                    localStorage.removeItem('cof_gridreception_params');
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
            var cof_gridreception_params = JSON.parse(localStorage.getItem('cof_gridreception_params'));
            console.log(cof_gridreception_params);

            if (cof_gridreception_params != null) {

                    // Ext.getCmp('Koltiva.view.Traceability_new.Reception.WinApplyFilter-TextFilterKeyword').setValue(cof_gridreception_params.TextFilterKeyword);
                    // Ext.getCmp('Koltiva.view.Traceability_new.Reception.WinApplyFilter-TextFilterWarehouseID').setValue(cof_gridreception_params.TextFilterWarehouseID);
                    Ext.getCmp('Koltiva.view.Traceability_new.Reception.WinApplyFilter-TextFilterStartShipmentDate').setValue(cof_gridreception_params.TextFilterStartShipmentDate);
                    Ext.getCmp('Koltiva.view.Traceability_new.Reception.WinApplyFilter-TextFilterEndShipmentDate').setValue(cof_gridreception_params.TextFilterEndShipmentDate);
            }
        }
    },
    AddValidationBasicForm: function() {
        var thisObj = this;
        var ArrMsg = [];
        thisObj.AddValidation = true;

        //Cek Date collection
        let StartShipmentDate = Ext.getCmp('Koltiva.view.Traceability_new.Reception.WinApplyFilter-TextFilterStartShipmentDate').getValue();
        let EndShipmenthDate = Ext.getCmp('Koltiva.view.Traceability_new.Reception.WinApplyFilter-TextFilterEndShipmentDate').getValue();
        if( ( StartShipmentDate != null && EndShipmenthDate == null ) || ( StartShipmentDate != null && EndShipmenthDate == null ) ) {
            thisObj.AddValidation = false;
            ArrMsg.push(lang('Filter date transaction parameters not valid'));
        }

        if(StartShipmentDate != null && EndShipmenthDate != null) {
            let ValidDate1 = moment(StartShipmentDate, 'YYYY-MM-DD',true).isValid();
            let ValidDate2 = moment(EndShipmenthDate, 'YYYY-MM-DD',true).isValid();
            if(ValidDate1 == false || ValidDate2 == false) {
                thisObj.AddValidation = false;
                ArrMsg.push(lang('Filter date transaction parameters not valid'));
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
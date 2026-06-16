Ext.define('Koltiva.view.Traceability_new.Transaction_neo.WinApplyFilter', {
    extend: 'Ext.window.Window',
    id: 'Koltiva.view.Traceability_new.Transaction_neo.WinApplyFilter',
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
        
        thisObj.StoreComboDestination = Ext.create('Koltiva.store.Traceability_new.Transaction_neo.ComboTransType');

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
                                        items: [{
                                                columnWidth: 0.2,
                                                layout: 'form',
                                                items: [{
                                                        xtype: 'label',
                                                        cls: 'x-form-item-label',
                                                        text: lang('Transaction Type') + ':',
                                                    }]
                                            }, {
                                                columnWidth: 0.8,
                                                border: false,
                                                layout: 'column',
                                                items: [{
                                                        xtype: 'combobox',
                                                        width: 375,
                                                        id: 'Koltiva.view.Traceability_new.Transaction_neo.WinApplyFilter-TextFilterTransTypeName',
                                                        name: 'Koltiva.view.Traceability_new.Transaction_neo.WinApplyFilter-TextFilterTransTypeName',
                                                        store: thisObj.StoreComboDestination,
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
                                    }, {
                                        layout: 'column',
                                        border: false,
                                        items: [{
                                                columnWidth: 0.2,
                                                layout: 'form',
                                                items: [{
                                                        xtype: 'label',
                                                        cls: 'x-form-item-label',
                                                        text: 'Transaction ID' + ':',
                                                    }]
                                            }, {
                                                columnWidth: 0.8,
                                                border: false,
                                                layout: 'column',
                                                items: [{
                                                        xtype: 'textfield',
                                                        width: 175,
                                                        id: 'Koltiva.view.Traceability_new.Transaction_neo.WinApplyFilter-TextFilterTransSupplyID',
                                                        name: 'Koltiva.view.Traceability_new.Transaction_neo.WinApplyFilter-TextFilterTransSupplyID'
                                                    }]
                                            }]
                                    }, {
                                        layout: 'column',
                                        border: false,
                                        items: [{
                                                columnWidth: 0.2,
                                                layout: 'form',
                                                items: [{
                                                        xtype: 'label',
                                                        cls: 'x-form-item-label',
                                                        text: lang('Farmer Name') + ':',
                                                    }]
                                            }, {
                                                columnWidth: 0.8,
                                                border: false,
                                                layout: 'column',
                                                items: [{
                                                        xtype: 'textfield',
                                                        width: 375,
                                                        id: 'Koltiva.view.Traceability_new.Transaction_neo.WinApplyFilter-TextFilterMemberName',
                                                        name: 'Koltiva.view.Traceability_new.Transaction_neo.WinApplyFilter-TextFilterMemberName'
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
                                                text: lang('Buying Date')+':',
                                            }]
                                        },{
                                            columnWidth: 0.8,
                                            border: false,
                                            layout: 'column',
                                            items:[{
                                                xtype: 'datefield',
                                                width: 110,
                                                id: 'Koltiva.view.Traceability_new.Transaction_neo.WinApplyFilter-TextFilterStartDateTransaction',
                                                name: 'Koltiva.view.Traceability_new.Transaction_neo.WinApplyFilter-TextFilterStartDateTransaction',
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
                                                width: 110,
                                                id: 'Koltiva.view.Traceability_new.Transaction_neo.WinApplyFilter-TextFilterEndDateTransaction',
                                                name: 'Koltiva.view.Traceability_new.Transaction_neo.WinApplyFilter-TextFilterEndDateTransaction',
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
                        let StartDateTransaction = Ext.Date.format(Ext.getCmp('Koltiva.view.Traceability_new.Transaction_neo.WinApplyFilter-TextFilterStartDateTransaction').getValue(), 'Y-m-d');
                        let EndDateTransaction = Ext.Date.format(Ext.getCmp('Koltiva.view.Traceability_new.Transaction_neo.WinApplyFilter-TextFilterEndDateTransaction').getValue(), 'Y-m-d');

                        if (Ext.getCmp('Koltiva.view.Traceability_new.Transaction_neo.WinApplyFilter-TextFilterTransTypeName').getValue() != "") {
                            ArrFilter.push('TransTypeName');
                            ArrFilterLang.push(lang('Trans Type Name'));
                        }

                        if (Ext.getCmp('Koltiva.view.Traceability_new.Transaction_neo.WinApplyFilter-TextFilterTransSupplyID').getValue() != "") {
                            ArrFilter.push('TransSupplyID');
                            ArrFilterLang.push(lang('Trans Supply ID'));
                        }

                        if (Ext.getCmp('Koltiva.view.Traceability_new.Transaction_neo.WinApplyFilter-TextFilterMemberName').getValue() != "") {
                            ArrFilter.push('MemberName');
                            ArrFilterLang.push(lang('Member Name'));
                        }

                        if (Ext.getCmp('Koltiva.view.Traceability_new.Transaction_neo.WinApplyFilter-TextFilterStartDateTransaction').getValue() != null) {
                            ArrFilter.push('StartDateTransaction');
                            ArrFilterLang.push(lang('Start Date Transaction'));
                        }

                        if (Ext.getCmp('Koltiva.view.Traceability_new.Transaction_neo.WinApplyFilter-TextFilterEndDateTransaction').getValue() != null) {
                            ArrFilter.push('EndDateTransaction');
                            ArrFilterLang.push(lang('End Date Transaction'));
                        }
                        //Cek filter apa saja yg dimasukkan ================================= (End)

                        //Set LocalStorage ================================= (Begin)
                        localStorage.setItem('cof_gridtransaction_params', JSON.stringify({
                            ArrFilter: ArrFilter,
                            ArrFilterLang: ArrFilterLang,
                            TextFilterTransTypeName: Ext.getCmp('Koltiva.view.Traceability_new.Transaction_neo.WinApplyFilter-TextFilterTransTypeName').getValue(),
                            TextFilterTransSupplyID: Ext.getCmp('Koltiva.view.Traceability_new.Transaction_neo.WinApplyFilter-TextFilterTransSupplyID').getValue(),
                            TextFilterMemberName: Ext.getCmp('Koltiva.view.Traceability_new.Transaction_neo.WinApplyFilter-TextFilterMemberName').getValue(),
                            TextFilterStartDateTransaction: StartDateTransaction,
                            TextFilterEndDateTransaction: EndDateTransaction,
                        }));
                        //Set LocalStorage ================================= (End)

                        //reload store main grid
                        Ext.getCmp('Koltiva.view.Traceability_new.Transaction_neo.MainGrid-Grid').getStore().loadPage(1);
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

                    Ext.getCmp('Koltiva.view.Traceability_new.Transaction_neo.WinApplyFilter-TextFilterTransTypeName').setValue('');
                    Ext.getCmp('Koltiva.view.Traceability_new.Transaction_neo.WinApplyFilter-TextFilterTransSupplyID').setValue('');
                    Ext.getCmp('Koltiva.view.Traceability_new.Transaction_neo.WinApplyFilter-TextFilterMemberName').setValue('');
                    Ext.getCmp('Koltiva.view.Traceability_new.Transaction_neo.WinApplyFilter-TextFilterStartDateTransaction').setValue('');
                    Ext.getCmp('Koltiva.view.Traceability_new.Transaction_neo.WinApplyFilter-TextFilterEndDateTransaction').setValue('');
                    
                    localStorage.removeItem('cof_gridtransaction_params');
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
            var cof_gridtransaction_params = JSON.parse(localStorage.getItem('cof_gridtransaction_params'));

            if (cof_gridtransaction_params != null) {

                Ext.getCmp('Koltiva.view.Traceability_new.Transaction_neo.WinApplyFilter-TextFilterTransTypeName').setValue(cof_gridtransaction_params.TextFilterTransTypeName);
                Ext.getCmp('Koltiva.view.Traceability_new.Transaction_neo.WinApplyFilter-TextFilterTransSupplyID').setValue(cof_gridtransaction_params.TextFilterTransSupplyID);
                Ext.getCmp('Koltiva.view.Traceability_new.Transaction_neo.WinApplyFilter-TextFilterMemberName').setValue(cof_gridtransaction_params.TextFilterMemberName);
                Ext.getCmp('Koltiva.view.Traceability_new.Transaction_neo.WinApplyFilter-TextFilterStartDateTransaction').setValue(cof_gridtransaction_params.TextFilterStartDateTransaction);
                Ext.getCmp('Koltiva.view.Traceability_new.Transaction_neo.WinApplyFilter-TextFilterEndDateTransaction').setValue(cof_gridtransaction_params.TextFilterEndDateTransaction);
            }
        }
    },
    AddValidationBasicForm: function() {
        var thisObj = this;
        var ArrMsg = [];
        thisObj.AddValidation = true;

        //Cek Date collection
        let StartDateTransaction = Ext.getCmp('Koltiva.view.Traceability_new.Transaction_neo.WinApplyFilter-TextFilterStartDateTransaction').getValue();
        let EndDateTransaction = Ext.getCmp('Koltiva.view.Traceability_new.Transaction_neo.WinApplyFilter-TextFilterEndDateTransaction').getValue();
        if( ( StartDateTransaction != null && EndDateTransaction == null ) || ( StartDateTransaction != null && EndDateTransaction == null ) ) {
            thisObj.AddValidation = false;
            ArrMsg.push(lang('Filter date transaction parameters not valid'));
        }

        if(StartDateTransaction != null && EndDateTransaction != null) {
            let ValidDate1 = moment(StartDateTransaction, 'YYYY-MM-DD',true).isValid();
            let ValidDate2 = moment(EndDateTransaction, 'YYYY-MM-DD',true).isValid();
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

function getFilterLs() {
    var filters = {};

    //ngeload filter parameters
    var cof_gridreception_params = JSON.parse(localStorage.getItem('cof_gridreception_params'));

    if (cof_gridreception_params != null) {

        filters.TextFilterStartShipmentDate = cof_gridreception_params.TextFilterStartShipmentDate;
        filters.TextFilterEndShipmentDate   = cof_gridreception_params.TextFilterEndShipmentDate;
    }
    
    return filters;
}
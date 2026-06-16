Ext.define('Koltiva.view.Traceability_new.Transaction_neo.WinApplyFilterSmsReport', {
    extend: 'Ext.window.Window',
    id: 'Koltiva.view.Traceability_new.Transaction_neo.WinApplyFilterSmsReport',
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
        
        thisObj.StoreComboTransType   = Ext.create('Koltiva.store.Traceability_new.Transaction_neo.ComboTransType');
        thisObj.StoreComboProvince    = Ext.create('Koltiva.store.ComboGeneral.ComboProvince'); 

        thisObj.StoreComboDistrict    = Ext.create('Koltiva.store.ComboGeneral.ComboDistrict');

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
                                    {
                                        layout: 'column',
                                        border: false,
                                        items:[
                                        {
                                            columnWidth: 0.2,
                                            layout: 'form',
                                            items:[{
                                                xtype: 'label',
                                                cls: 'x-form-item-label',
                                                text: lang('SMS Date')+':',
                                            }]
                                        },{
                                            columnWidth: 0.8,
                                            border: false,
                                            layout: 'column',
                                            items:[{
                                                xtype: 'datefield',
                                                width: 110,
                                                id: 'Koltiva.view.Traceability_new.Transaction_neo.WinApplyFilterSmsReport-TextFilterStartDateTransaction',
                                                name: 'Koltiva.view.Traceability_new.Transaction_neo.WinApplyFilterSmsReport-TextFilterStartDateTransaction',
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
                                                id: 'Koltiva.view.Traceability_new.Transaction_neo.WinApplyFilterSmsReport-TextFilterEndDateTransaction',
                                                name: 'Koltiva.view.Traceability_new.Transaction_neo.WinApplyFilterSmsReport-TextFilterEndDateTransaction',
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
                                    {
                                        layout: 'column',
                                        border: false,
                                        style: 'margin-top:12px;',
                                        items: [
                                            {
                                                columnWidth: 0.2,
                                                layout: 'form',
                                                items: [{
                                                        xtype: 'label',
                                                        cls: 'x-form-item-label',
                                                        text: lang('Transaction Type') + ':',
                                                    }]
                                            }, 
                                            {
                                                columnWidth: 0.8,
                                                border: false,
                                                layout: 'column',
                                                items: [{
                                                        xtype: 'combobox',
                                                        width: 375,
                                                        id: 'Koltiva.view.Traceability_new.Transaction_neo.WinApplyFilterSmsReport-TextFilterTransTypeName',
                                                        name: 'Koltiva.view.Traceability_new.Transaction_neo.WinApplyFilterSmsReport-TextFilterTransTypeName',
                                                        store: thisObj.StoreComboTransType,
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
                                            }
                                        ]
                                    }, 
                                    {
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
                                                        id: 'Koltiva.view.Traceability_new.Transaction_neo.WinApplyFilterSmsReport-TextFilterTransSupplyID',
                                                        name: 'Koltiva.view.Traceability_new.Transaction_neo.WinApplyFilterSmsReport-TextFilterTransSupplyID'
                                                    }]
                                            }]
                                    }, 
                                    {
                                        layout: 'column',
                                        border: false,
                                        items: [
                                            {
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
                                                        id: 'Koltiva.view.Traceability_new.Transaction_neo.WinApplyFilterSmsReport-TextFilterMemberName',
                                                        name: 'Koltiva.view.Traceability_new.Transaction_neo.WinApplyFilterSmsReport-TextFilterMemberName'
                                                    }]
                                            }
                                        ]
                                    },
                                    {
                                        layout: 'column',
                                        border: false,
                                        style: 'margin-top:12px;',
                                        items: [
                                            {
                                                columnWidth: 0.2,
                                                layout: 'form',
                                                items: [{
                                                        xtype: 'label',
                                                        cls: 'x-form-item-label',
                                                        text: lang('Province') + ':',
                                                    }]
                                            }, 
                                            {
                                                columnWidth: 0.8,
                                                border: false,
                                                layout: 'column',
                                                items: [{
                                                        xtype: 'combobox',
                                                        width: 375,
                                                        id: 'Koltiva.view.Traceability_new.Transaction_neo.WinApplyFilterSmsReport-TextFilterProvince',
                                                        name: 'Koltiva.view.Traceability_new.Transaction_neo.WinApplyFilterSmsReport-TextFilterProvince',
                                                        store: thisObj.StoreComboProvince,
                                                        queryMode: 'local',
                                                        displayField: 'label',
                                                        valueField: 'id',
                                                        enableKeyEvents: true,
                                                        listeners: {
                                                        select : function()
                                                        {
                                                            Ext.getCmp('Koltiva.view.Traceability_new.Transaction_neo.WinApplyFilterSmsReport-TextFilterDistrict').setValue('');
                                                        },
                                                        change: function (record) {
                                                            thisObj.StoreComboDistrict.setStoreVar({'ProvinceID':record.getValue()}); 
                                                            thisObj.StoreComboDistrict.load(); 
                                                        }
                                                    }
                                                }]
                                            }
                                        ]
                                    }, 
                                    {
                                        layout: 'column',
                                        border: false,
                                        style: 'margin-top:12px;',
                                        items: [
                                            {
                                                columnWidth: 0.2,
                                                layout: 'form',
                                                items: [{
                                                        xtype: 'label',
                                                        cls: 'x-form-item-label',
                                                        text: lang('District') + ':',
                                                    }]
                                            }, 
                                            {
                                                columnWidth: 0.8,
                                                border: false,
                                                layout: 'column',
                                                items: [{
                                                        xtype: 'combobox',
                                                        width: 375,
                                                        id: 'Koltiva.view.Traceability_new.Transaction_neo.WinApplyFilterSmsReport-TextFilterDistrict',
                                                        name: 'Koltiva.view.Traceability_new.Transaction_neo.WinApplyFilterSmsReport-TextFilterDistrict',
                                                        store: thisObj.StoreComboDistrict,
                                                        queryMode: 'local',
                                                        displayField: 'label',
                                                        valueField: 'id'
                                                    }]
                                            }
                                        ]
                                    }
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
                        let StartDateTransaction = Ext.Date.format(Ext.getCmp('Koltiva.view.Traceability_new.Transaction_neo.WinApplyFilterSmsReport-TextFilterStartDateTransaction').getValue(), 'Y-m-d');
                        let EndDateTransaction = Ext.Date.format(Ext.getCmp('Koltiva.view.Traceability_new.Transaction_neo.WinApplyFilterSmsReport-TextFilterEndDateTransaction').getValue(), 'Y-m-d');

                        if (Ext.getCmp('Koltiva.view.Traceability_new.Transaction_neo.WinApplyFilterSmsReport-TextFilterTransTypeName').getValue() != "") {
                            ArrFilter.push('TransTypeName');
                            ArrFilterLang.push(lang('Trans Type Name'));
                        }

                        if (Ext.getCmp('Koltiva.view.Traceability_new.Transaction_neo.WinApplyFilterSmsReport-TextFilterTransSupplyID').getValue() != "") {
                            ArrFilter.push('TransSupplyID');
                            ArrFilterLang.push(lang('Trans Supply ID'));
                        }

                        if (Ext.getCmp('Koltiva.view.Traceability_new.Transaction_neo.WinApplyFilterSmsReport-TextFilterMemberName').getValue() != "") {
                            ArrFilter.push('MemberName');
                            ArrFilterLang.push(lang('Member Name'));
                        }

                        if (Ext.getCmp('Koltiva.view.Traceability_new.Transaction_neo.WinApplyFilterSmsReport-TextFilterStartDateTransaction').getValue() != null) {
                            ArrFilter.push('StartDateTransaction');
                            ArrFilterLang.push(lang('Start Date Transaction'));
                        }

                        if (Ext.getCmp('Koltiva.view.Traceability_new.Transaction_neo.WinApplyFilterSmsReport-TextFilterEndDateTransaction').getValue() != null) {
                            ArrFilter.push('EndDateTransaction');
                            ArrFilterLang.push(lang('End Date Transaction'));
                        }

                        if (Ext.getCmp('Koltiva.view.Traceability_new.Transaction_neo.WinApplyFilterSmsReport-TextFilterProvince').getValue() != "") {
                            ArrFilter.push('TextFilterProvince');
                            ArrFilterLang.push(lang('Province'));
                        }

                        if (Ext.getCmp('Koltiva.view.Traceability_new.Transaction_neo.WinApplyFilterSmsReport-TextFilterDistrict').getValue() != "") {
                            ArrFilter.push('TextFilterDistrict');
                            ArrFilterLang.push(lang('District'));
                        }

                        //Cek filter apa saja yg dimasukkan ================================= (End)

                        //Set LocalStorage ================================= (Begin)
                        localStorage.setItem('cof_gridtransaction_params', JSON.stringify({
                            ArrFilter: ArrFilter,
                            ArrFilterLang: ArrFilterLang,
                            TextFilterTransTypeName: Ext.getCmp('Koltiva.view.Traceability_new.Transaction_neo.WinApplyFilterSmsReport-TextFilterTransTypeName').getValue(),
                            TextFilterTransSupplyID: Ext.getCmp('Koltiva.view.Traceability_new.Transaction_neo.WinApplyFilterSmsReport-TextFilterTransSupplyID').getValue(),
                            TextFilterMemberName: Ext.getCmp('Koltiva.view.Traceability_new.Transaction_neo.WinApplyFilterSmsReport-TextFilterMemberName').getValue(),
                            TextFilterStartDateTransaction: StartDateTransaction,
                            TextFilterEndDateTransaction: EndDateTransaction,
                            TextFilterProvince : Ext.getCmp('Koltiva.view.Traceability_new.Transaction_neo.WinApplyFilterSmsReport-TextFilterProvince').getValue(),
                            TextFilterDistrict : Ext.getCmp('Koltiva.view.Traceability_new.Transaction_neo.WinApplyFilterSmsReport-TextFilterDistrict').getValue(),
                        }));
                        //Set LocalStorage ================================= (End)

                        //reload store main grid
                        Ext.getCmp('Koltiva.view.Traceability_new.report.MainGridSms-Grid').getStore().loadPage(1);
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

                    Ext.getCmp('Koltiva.view.Traceability_new.Transaction_neo.WinApplyFilterSmsReport-TextFilterTransTypeName').setValue('');
                    Ext.getCmp('Koltiva.view.Traceability_new.Transaction_neo.WinApplyFilterSmsReport-TextFilterTransSupplyID').setValue('');
                    Ext.getCmp('Koltiva.view.Traceability_new.Transaction_neo.WinApplyFilterSmsReport-TextFilterMemberName').setValue('');
                    Ext.getCmp('Koltiva.view.Traceability_new.Transaction_neo.WinApplyFilterSmsReport-TextFilterStartDateTransaction').setValue('');
                    Ext.getCmp('Koltiva.view.Traceability_new.Transaction_neo.WinApplyFilterSmsReport-TextFilterEndDateTransaction').setValue('');
                    Ext.getCmp('Koltiva.view.Traceability_new.Transaction_neo.WinApplyFilterSmsReport-TextFilterProvince').setValue('');
                    Ext.getCmp('Koltiva.view.Traceability_new.Transaction_neo.WinApplyFilterSmsReport-TextFilterDistrict').setValue('');
                    
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

                Ext.getCmp('Koltiva.view.Traceability_new.Transaction_neo.WinApplyFilterSmsReport-TextFilterTransTypeName').setValue(cof_gridtransaction_params.TextFilterTransTypeName);
                Ext.getCmp('Koltiva.view.Traceability_new.Transaction_neo.WinApplyFilterSmsReport-TextFilterTransSupplyID').setValue(cof_gridtransaction_params.TextFilterTransSupplyID);
                Ext.getCmp('Koltiva.view.Traceability_new.Transaction_neo.WinApplyFilterSmsReport-TextFilterMemberName').setValue(cof_gridtransaction_params.TextFilterMemberName);
                Ext.getCmp('Koltiva.view.Traceability_new.Transaction_neo.WinApplyFilterSmsReport-TextFilterStartDateTransaction').setValue(cof_gridtransaction_params.TextFilterStartDateTransaction);
                Ext.getCmp('Koltiva.view.Traceability_new.Transaction_neo.WinApplyFilterSmsReport-TextFilterEndDateTransaction').setValue(cof_gridtransaction_params.TextFilterEndDateTransaction);
                Ext.getCmp('Koltiva.view.Traceability_new.Transaction_neo.WinApplyFilterSmsReport-TextFilterProvince').setValue(cof_gridtransaction_params.TextFilterProvince);
                Ext.getCmp('Koltiva.view.Traceability_new.Transaction_neo.WinApplyFilterSmsReport-TextFilterDistrict').setValue(cof_gridtransaction_params.TextFilterDistrict);
            }
        }
    },
    AddValidationBasicForm: function() {
        var thisObj = this;
        var ArrMsg = [];
        thisObj.AddValidation = true;

        //Cek Date collection
        let StartDateTransaction = Ext.getCmp('Koltiva.view.Traceability_new.Transaction_neo.WinApplyFilterSmsReport-TextFilterStartDateTransaction').getValue();
        let EndDateTransaction = Ext.getCmp('Koltiva.view.Traceability_new.Transaction_neo.WinApplyFilterSmsReport-TextFilterEndDateTransaction').getValue();
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
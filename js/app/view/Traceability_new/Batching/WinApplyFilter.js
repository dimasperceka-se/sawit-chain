Ext.define('Koltiva.view.Traceability_new.Batching.WinApplyFilter', {
    extend: 'Ext.window.Window',
    id: 'Koltiva.view.Traceability_new.Batching.WinApplyFilter',
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

        thisObj.BatchStatus = Ext.create('Koltiva.store.Traceability_new.Batching.BatchStatus');

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
                                                        text: lang('Supply Batch Number') + ':',
                                                    }]
                                            }, {
                                                columnWidth: 0.8,
                                                border: false,
                                                layout: 'column',
                                                items: [{
                                                        xtype: 'textfield',
                                                        width: 375,
                                                        id: 'Koltiva.view.Traceability_new.Batching.WinApplyFilter-TextFilterSupplyBatchNumber',
                                                        name: 'Koltiva.view.Traceability_new.Batching.WinApplyFilter-TextFilterSupplyBatchNumber'
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
                                    //                     text: lang('Supply Batch Status') + ':',
                                    //                 }]
                                    //         }, {
                                    //             columnWidth: 0.8,
                                    //             border: false,
                                    //             layout: 'column',
                                    //             items: [{
                                    //                     xtype: 'combobox',
                                    //                     width: 375,
                                    //                     id: 'Koltiva.view.Traceability_new.Batching.WinApplyFilter-TextFilterSupplyBatchStatusID',
                                    //                     name: 'Koltiva.view.Traceability_new.Batching.WinApplyFilter-TextFilterSupplyBatchStatusID',
                                    //                     store: thisObj.BatchStatus,
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
                                                text: lang('Supply Batch Date')+':',
                                            }]
                                        },{
                                            columnWidth: 0.8,
                                            border: false,
                                            layout: 'column',
                                            items:[{
                                                xtype: 'datefield',
                                                width: 110,
                                                id: 'Koltiva.view.Traceability_new.Batching.WinApplyFilter-TextFilterStartSupplyBatchDate',
                                                name: 'Koltiva.view.Traceability_new.Batching.WinApplyFilter-TextFilterStartSupplyBatchDate',
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
                                                id: 'Koltiva.view.Traceability_new.Batching.WinApplyFilter-TextFilterEndSupplyBatchDate',
                                                name: 'Koltiva.view.Traceability_new.Batching.WinApplyFilter-TextFilterEndSupplyBatchDate',
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
                        let StartSupplyBatchDate = Ext.Date.format(Ext.getCmp('Koltiva.view.Traceability_new.Batching.WinApplyFilter-TextFilterStartSupplyBatchDate').getValue(), 'Y-m-d');
                        let EndSupplyBatchDate = Ext.Date.format(Ext.getCmp('Koltiva.view.Traceability_new.Batching.WinApplyFilter-TextFilterEndSupplyBatchDate').getValue(), 'Y-m-d');

                        if (Ext.getCmp('Koltiva.view.Traceability_new.Batching.WinApplyFilter-TextFilterSupplyBatchNumber').getValue() != "") {
                            ArrFilter.push('SupplyBatchNumber');
                            ArrFilterLang.push(lang('Supply BatchNumber'));
                        }

                        // if (Ext.getCmp('Koltiva.view.Traceability_new.Batching.WinApplyFilter-TextFilterSupplyBatchStatusID').getValue() != null) {
                        //     ArrFilter.push('SupplyBatchStatusID');
                        //     ArrFilterLang.push(lang('Supply Batch Status'));
                        // }

                        if (Ext.getCmp('Koltiva.view.Traceability_new.Batching.WinApplyFilter-TextFilterStartSupplyBatchDate').getValue() != null) {
                            ArrFilter.push('StartSupplyBatchDate');
                            ArrFilterLang.push(lang('Start Supply BatchDate'));
                        }

                        if (Ext.getCmp('Koltiva.view.Traceability_new.Batching.WinApplyFilter-TextFilterEndSupplyBatchDate').getValue() != null) {
                            ArrFilter.push('EndSupplyBatchDate');
                            ArrFilterLang.push(lang('End Supply BatchDate'));
                        }
                        //Cek filter apa saja yg dimasukkan ================================= (End)

                        //Set LocalBatch================================= (Begin)
                        localStorage.setItem('cof_gridprocessing_params', JSON.stringify({
                            ArrFilter: ArrFilter,
                            ArrFilterLang: ArrFilterLang,
                            TextFilterSupplyBatchNumber: Ext.getCmp('Koltiva.view.Traceability_new.Batching.WinApplyFilter-TextFilterSupplyBatchNumber').getValue(),
                            // TextFilterSupplyBatchStatusID: Ext.getCmp('Koltiva.view.Traceability_new.Batching.WinApplyFilter-TextFilterSupplyBatchStatusID').getValue(),
                            TextFilterStartSupplyBatchDate: StartSupplyBatchDate,
                            TextFilterEndSupplyBatchDate: EndSupplyBatchDate,
                        }));
                        //Set LocalBatch================================= (End)

                        //reload store main grid
                        Ext.getCmp('Koltiva.view.Traceability_new.Batching.MainGrid-Grid').getStore().loadPage(1);
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

                    Ext.getCmp('Koltiva.view.Traceability_new.Batching.WinApplyFilter-TextFilterSupplyBatchNumber').setValue('');
                    // Ext.getCmp('Koltiva.view.Traceability_new.Batching.WinApplyFilter-TextFilterSupplyBatchStatusID').setValue('');
                    Ext.getCmp('Koltiva.view.Traceability_new.Batching.WinApplyFilter-TextFilterStartSupplyBatchDate').setValue('');
                    Ext.getCmp('Koltiva.view.Traceability_new.Batching.WinApplyFilter-TextFilterEndSupplyBatchDate').setValue('');
                    
                    localStorage.removeItem('cof_gridprocessing_params');
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
            var cof_gridprocessing_params = JSON.parse(localStorage.getItem('cof_gridprocessing_params'));
            console.log(cof_gridprocessing_params);

            if (cof_gridprocessing_params != null) {

                Ext.getCmp('Koltiva.view.Traceability_new.Batching.WinApplyFilter-TextFilterSupplyBatchNumber').setValue(cof_gridprocessing_params.TextFilterSupplyBatchNumber);
                // Ext.getCmp('Koltiva.view.Traceability_new.Batching.WinApplyFilter-TextFilterSupplyBatchStatusID').setValue(cof_gridprocessing_params.TextFilterSupplyBatchStatusID);
                Ext.getCmp('Koltiva.view.Traceability_new.Batching.WinApplyFilter-TextFilterStartSupplyBatchDate').setValue(cof_gridprocessing_params.TextFilterStartSupplyBatchDate);
                Ext.getCmp('Koltiva.view.Traceability_new.Batching.WinApplyFilter-TextFilterEndSupplyBatchDate').setValue(cof_gridprocessing_params.TextFilterEndSupplyBatchDate);
            }
        }
    },
    AddValidationBasicForm: function() {
        var thisObj = this;
        var ArrMsg = [];
        thisObj.AddValidation = true;

        //Cek Date collection
        let StartSupplyBatchDate = Ext.getCmp('Koltiva.view.Traceability_new.Batching.WinApplyFilter-TextFilterStartSupplyBatchDate').getValue();
        let EndSupplyBatchDate = Ext.getCmp('Koltiva.view.Traceability_new.Batching.WinApplyFilter-TextFilterEndSupplyBatchDate').getValue();
        if( ( StartSupplyBatchDate != null && EndSupplyBatchDate == null ) || ( StartSupplyBatchDate != null && EndSupplyBatchDate == null ) ) {
            thisObj.AddValidation = false;
            ArrMsg.push(lang('Filter date transaction parameters not valid'));
        }

        if(StartSupplyBatchDate != null && EndSupplyBatchDate != null) {
            let ValidDate1 = moment(StartSupplyBatchDate, 'YYYY-MM-DD',true).isValid();
            let ValidDate2 = moment(EndSupplyBatchDate, 'YYYY-MM-DD',true).isValid();
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
Ext.define('Koltiva.view.Traceability_new.report.MainGridSms', {
    extend: 'Ext.panel.Panel',
    id: 'Koltiva.view.Traceability_new.report.MainGridSms',
    renderTo: 'ext-content',
    style: 'padding:0 7px 7px 7px;margin:2px 0 0 0;',
    listeners: {
        afterRender: function (component, eOpts) {
            var thisObj = this;
            // document.getElementById('divCommonContentRegion2').style.display = 'block';
        }
    },
    initComponent: function () {
        var thisObj = this;
        //Store
        thisObj.StoreGridMain = Ext.create('Koltiva.store.Traceability_new.Report.MainGridReportSms');
        //ContextMenu
        thisObj.items = [{
                xtype: 'grid',
                id: 'Koltiva.view.Traceability_new.report.MainGridSms-Grid',
                style: 'border:1px solid #CCC;margin-top:4px;',
                minHeight:600,
                cls: 'Sfr_GridNew',
                loadMask: true,
                selType: 'rowmodel',
                store: thisObj.StoreGridMain,
                enableColumnHide: false,
                viewConfig: {
                    deferEmptyText: false,
                    emptyText: GetDefaultContentNoData()
                },
                dockedItems: [{
                        xtype: 'pagingtoolbar',
                        store: thisObj.StoreGridMain,
                        dock: 'bottom',
                        displayInfo: true,
                        displayMsg: lang('Showing') + ' {0} ' + lang('to') + ' {1} ' + lang('of') + ' {2} ' + lang('data')
                    }, {
                        xtype: 'toolbar',
                        dock: 'top',
                        items: [
                            {
                                xtype: 'button',
                                id: 'Koltiva.view.Traceability_new.Transaction.MainGrid-gridToolbar-BtnExport',
                                icon: varjs.config.base_url + 'images/icons/new/export.png', cls:'Sfr_BtnGridPaleBlue',
                                margin: '0px 10px 0px 6px',
                                text: lang('Export'),
                                cls:'Sfr_BtnGridGreen',
                                overCls:'Sfr_BtnGridGreen-Hover',
                                handler: function() {

                                    Ext.MessageBox.show({
                                        msg: 'Please wait...',
                                        progressText: 'Exporting...',
                                        width: 300,
                                        wait: true,
                                        waitConfig: {
                                            interval: 200
                                        },
                                        icon: 'ext-mb-download', //custom class in msg-box.html
                                        animateTarget: 'mb7'
                                    });

                                    var filter = getFilterLs();
                                    var keys = Object.keys(filter);
                                    var param_string = '?';
                                    $.each(keys, function (index, val) {
                                        param_string += '' +'&' + val + '=' + filter[val];
                                    });
                                    
                                    try {
                                        Ext.destroy(Ext.get('downloadIframe'));
                                    }
                                    catch(e) {}
    
                                    Ext.Ajax.request({
                                        url: m_api+'/traceability_api/web_transaction/export_excel_sms/'+param_string,
                                    
                                        method: 'GET',
                                        waitMsg: lang('Please Wait'),
                                        timeout: 360000,
                                        success: function(data) {
                                            Ext.MessageBox.hide();
                                            var jsonResp = JSON.parse(data.responseText);
                                            window.location = jsonResp.filenya;
                                        },
                                        failure: function() {
                                            Ext.MessageBox.hide();
                                            Ext.MessageBox.show({
                                                title: 'Notifications',
                                                msg: 'Failed to export, Please try again.',
                                                buttons: Ext.MessageBox.OK,
                                                animateTarget: 'mb9',
                                                icon: 'ext-mb-error'
                                            });
                                        }
                                    });
                                    
                                }
                            }, 
                            {
                                xtype: 'tbspacer',
                                flex: 1
                            }, {
                                xtype: 'button',
                                icon: varjs.config.base_url + 'images/icons/new/add-filter.png',
                                text: lang('Apply Filter'),
                                cls: 'Sfr_BtnGridPaleBlue',
                                overCls: 'Sfr_BtnGridPaleBlue-Hover',
                                handler: function () {
                                    var WinApplyFilterSmsReport = Ext.create('Koltiva.view.Traceability_new.Transaction_neo.WinApplyFilterSmsReport', {
                                        viewVar: {
                                            StoreGridMain: thisObj.StoreGridMain
                                        }
                                    });
                                    if (!WinApplyFilterSmsReport.isVisible()) {
                                        WinApplyFilterSmsReport.center();
                                        WinApplyFilterSmsReport.show();
                                    } else {
                                        WinApplyFilterSmsReport.close();
                                    }
                                }
                            }, {
                                xtype: 'button',
                                icon: varjs.config.base_url + 'images/icons/new/reload.png',
                                cls: 'Sfr_BtnGridBlue',
                                overCls: 'Sfr_BtnGridBlue-Hover',
                                handler: function () {
                                    Ext.getCmp('Koltiva.view.Traceability_new.report.MainGridSms-Grid').getStore().loadPage(1);
                                }
                            }]
                    }],
                columns: [
                    {
                        text: '',
                        xtype: 'actioncolumn',
                        width: 30,
                        items: [
                            {
                                icon: varjs.config.base_url + 'images/icons/new/action.png',
                                handler: function (grid, rowIndex, colIndex, item, e, record) {
                                    if(Ext.isDefined(Ext.getCmp('ContextMenuGridSms'))){
                                        Ext.getCmp('ContextMenuGridSms').destroy();
                                    }
                                    thisObj.ContextMenuGrid = Ext.create('Ext.menu.Menu', {
                                        cls: 'Sfr_ConMenu',
                                        id:"ContextMenuGridSms",
                                        items: [
                                            {
                                                icon: varjs.config.base_url + 'images/icons/new/view.png',
                                                text: lang('View'),
                                                id:'Koltiva.view.Traceability_new.report.btnView',
                                                cls: 'Sfr_BtnConMenuWhite',
                                                handler: function () {
                                                    var sm = Ext.getCmp('Koltiva.view.Traceability_new.report.MainGridSms-Grid').getSelectionModel().getSelection()[0];
                                                    
                                                    Ext.getCmp('Koltiva.view.Traceability_new.report.MainGridSms').destroy(); //destory current view
                            
                                                    var MainFormSms = [];
                                                    //create object View untuk FormMainGrower
                                                    if (Ext.getCmp('Koltiva.view.Traceability_new.report.MainFormSms') == undefined) {
                                                        MainFormSms = Ext.create('Koltiva.view.Traceability_new.report.MainFormSms', {
                                                            viewVar: {
                                                                OpsiDisplay: 'view',
                                                                AutoID: sm.get('AutoID'),
                                                            }
                                                        });
                                                    } else {
                                                        //destroy, create ulang
                                                        Ext.getCmp('Koltiva.view.Traceability_new.report.MainFormSms').destroy();
                                                        MainFormSms = Ext.create('Koltiva.view.Traceability_new.report.MainFormSms', {
                                                            viewVar: {
                                                                OpsiDisplay: 'view',
                                                                AutoID: sm.get('AutoID'),
                                                            }
                                                        });
                                                    }
                                                }
                                            },
                                            {
                                                id: 'Koltiva.view.Traceability_new.report-BtnResend',
                                                icon: varjs.config.base_url + 'images/icons/new/refresh1.png',
                                                text: lang('Resend SMS'),
                                                hidden: false,
                                                handler: function(){
                                                    Ext.MessageBox.confirm('Confirmation', lang('Apakah anda yakin untuk mengirim sms ?'), function (btn) {
                                                        if (btn == 'yes') {
                                                            Ext.MessageBox.show({
                                                                msg: 'Loading, please wait...',
                                                                progressText: 'Saving...',
                                                                width:300,
                                                                wait:true,
                                                                waitConfig: {interval:200},
                                                                icon:'ext-mb-download', //custom class in msg-box.html
                                                                iconHeight: 50,
                                                                animateTarget: 'mb7'
                                                            });
                                
                                                            var sm = Ext.getCmp('Koltiva.view.Traceability_new.report.MainGridSms-Grid').getSelectionModel().getSelection()[0];
                                                            Ext.Ajax.request({
                                                                waitMsg: lang('Please Wait'),
                                                                url: m_api + '/traceability_api/web_transaction/resend_sms',
                                                                method: 'POST',
                                                                params: {AutoID: sm.get('AutoID')},
                                                                success: function (response, opts) {
                                                                    Ext.MessageBox.hide();
                                                                    var obj = Ext.decode(response.responseText);
                                                                    if (obj.success=='true') {
                                                                        Ext.MessageBox.show({
                                                                            title: 'Information',
                                                                            msg: lang(obj.message),
                                                                            buttons: Ext.MessageBox.OK,
                                                                            animateTarget: 'mb9',
                                                                            icon: 'ext-mb-success'
                                                                        });
                                                                        Ext.getCmp('Koltiva.view.Traceability_new.report.MainGridSms-Grid').getStore().loadPage();
                                                                    } else {
                                                                        Ext.MessageBox.show({
                                                                            title: 'Fail',
                                                                            msg: lang(obj.message),
                                                                            buttons: Ext.MessageBox.OK,
                                                                            animateTarget: 'mb9',
                                                                            icon: 'ext-mb-error'
                                                                        });
                                                                    }
                                                                },
                                                                failure: function (response, opts) {
                                                                    Ext.MessageBox.hide();
                                                                    var obj = Ext.decode(response.responseText);
                                                                    Ext.MessageBox.alert(lang('error'), lang('Could not connect to the database. Retry later'));
                                                                }
                                                            });
                                                        }
                                                    });
                                                }
                                            }, 
                                            {
                                                id: 'Koltiva.view.Traceability_new.report-BtnCheckSms',
                                                icon: varjs.config.base_url + 'images/icons/new/system-2.png',
                                                text: lang('Checking SMS'),
                                                hidden: false,
                                                handler: function(){
                                                    Ext.MessageBox.confirm('Confirmation', lang('Apakah anda yakin untuk melakukan pengecekan sms ?'), function (btn) {
                                                        if (btn == 'yes') {
                                                            Ext.MessageBox.show({
                                                                msg: 'Loading, please wait...',
                                                                progressText: 'Saving...',
                                                                width:300,
                                                                wait:true,
                                                                waitConfig: {interval:200},
                                                                icon:'ext-mb-download', //custom class in msg-box.html
                                                                iconHeight: 50,
                                                                animateTarget: 'mb7'
                                                            });
                                
                                                            var sm = Ext.getCmp('Koltiva.view.Traceability_new.report.MainGridSms-Grid').getSelectionModel().getSelection()[0];
                                                            Ext.Ajax.request({
                                                                waitMsg: lang('Please Wait'),
                                                                url: m_api + '/traceability_api/web_transaction/checking_sms',
                                                                method: 'POST',
                                                                params: {AutoID: sm.get('AutoID')},
                                                                success: function (response, opts) {
                                                                    Ext.MessageBox.hide();
                                                                    var obj = Ext.decode(response.responseText);
                                                                    if (obj.success=='true') {
                                                                        Ext.MessageBox.show({
                                                                            title: 'Information',
                                                                            msg: lang(obj.message),
                                                                            buttons: Ext.MessageBox.OK,
                                                                            animateTarget: 'mb9',
                                                                            icon: 'ext-mb-success'
                                                                        });
                                                                        Ext.getCmp('Koltiva.view.Traceability_new.report.MainGridSms-Grid').getStore().loadPage();
                                                                    } else {
                                                                        Ext.MessageBox.show({
                                                                            title: 'Fail',
                                                                            msg: lang(obj.message),
                                                                            buttons: Ext.MessageBox.OK,
                                                                            animateTarget: 'mb9',
                                                                            icon: 'ext-mb-error'
                                                                        });
                                                                    }
                                                                },
                                                                failure: function (response, opts) {
                                                                    Ext.MessageBox.hide();
                                                                    var obj = Ext.decode(response.responseText);
                                                                    Ext.MessageBox.alert(lang('error'), lang('Could not connect to the database. Retry later'));
                                                                }
                                                            });
                                                        }
                                                    });
                                                }
                                            }
                                        ]
                                    });

                                    thisObj.ContextMenuGrid.showAt(e.getXY());
                                }
                        }],
                    },
                    {
                        text: 'No',
                        width: '5%',
                        xtype: 'rownumberer'
                    },
                    {
                        text: 'ID',
                        dataIndex: 'SupplyID',
                        hidden: true,
                    },{
                        text: lang('SMS Type'),
                        dataIndex: 'SMSType',
                        flex:20
                    }, {
                        text: lang('SMS Status'),
                        dataIndex: 'SMSStatus',
                        flex:20
                    }, {
                        text: lang('Send Date'),
                        dataIndex: 'SendDate',
                        flex:20
                    },{
                        text: lang('Handphone'),
                        dataIndex: 'Handphone',
                        flex:20
                    },{
                        text: lang('Transaction ID'),
                        dataIndex: 'SupplyTransID',
                        flex:20
                    },{
                        text: lang('Farmer ID'),
                        dataIndex: 'MemberDisplayID',
                        flex:20
                    }, {
                        text: lang('Farmer Name'),
                        dataIndex: 'SupplierName',
                        flex:20
                    },{
                        text: lang('Group Name'),
                        dataIndex: 'GroupName',
                        flex:20
                    },{
                        text: lang('Province'),
                        dataIndex: 'Province',
                        flex:20
                    },
                    {
                        text: lang('District'),
                        dataIndex: 'District',
                        flex:20
                    },
                    {
                        text: lang('Sub-District'),
                        dataIndex: 'SubDistrict',
                        flex:20
                    },
                    {
                        text: lang('Village'),
                        dataIndex: 'Village',
                        flex:20
                    },
                    {
                        text: lang('Supply Type'),
                        dataIndex: 'SupplyType',
                        flex:20
                    },
                    {
                        text: lang('Transaction Date'),
                        dataIndex: 'DateTransaction',
                        renderer: Ext.util.Format.dateRenderer('d-m-Y'),
                        flex:20
                    },{
                        text: lang('Netto'),
                        dataIndex: 'VolumeNetto',
                        flex:20
                    }],
                    listeners: {
                        afterRender: function(data, r) {
    
                        }
                    }
            }];
        this.callParent(arguments);
    }
});

function getFilterLs() {
    var filters = {};

    //ngeload filter parameters
    var cof_gridtransaction_params = JSON.parse(localStorage.getItem('cof_gridtransaction_params'));

    if (cof_gridtransaction_params != null) {
        filters.ArrFilter                      = cof_gridtransaction_params.ArrFilter.join(',');
        filters.TextFilterTransTypeName        = cof_gridtransaction_params.TextFilterTransTypeName;
        filters.TextFilterTransSupplyID        = cof_gridtransaction_params.TextFilterTransSupplyID;
        filters.TextFilterMemberName           = cof_gridtransaction_params.TextFilterMemberName;
        filters.TextFilterStartDateTransaction = cof_gridtransaction_params.TextFilterStartDateTransaction;
        filters.TextFilterEndDateTransaction   = cof_gridtransaction_params.TextFilterEndDateTransaction;
        filters.TextFilterProvince             = cof_gridtransaction_params.TextFilterProvince          ;
        filters.TextFilterDistrict             = cof_gridtransaction_params.TextFilterDistrict;
    } else {
        //reset params
        filters.ArrFilter                      = null;
        filters.TextFilterTransTypeName        = null;
        filters.TextFilterTransSupplyID        = null;
        filters.TextFilterMemberName           = null;
        filters.TextFilterStartDateTransaction = null;
        filters.TextFilterEndDateTransaction   = null;
        filters.TextFilterProvince             = null;
        filters.TextFilterDistrict             = null;
    }
    
    return filters;
}
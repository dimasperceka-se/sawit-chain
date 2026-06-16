Ext.define('Koltiva.view.Traceability_new.Delivery.MainGridTransaction', {
    extend: 'Ext.panel.Panel',
    id: 'Koltiva.view.Traceability_new.Delivery.MainGridTransaction',
    renderTo: 'ext-content',
    style: 'padding:0 7px 7px 7px;margin:2px 0 0 0;',
    listeners: {
        afterRender: function (component, eOpts) {
            var thisObj = this;
        }
    },
    initComponent: function () {
        var thisObj = this;
        
        //Store
        thisObj.StoreGridTransactionDetail = Ext.create('Koltiva.store.Traceability_new.Delivery.MainGridTransactionDetail', {
            storeVar: {
                DeliveryID : thisObj.viewVar.DeliveryID
            }
        });
  
        //ContextMenu       
        thisObj.items = [{
                xtype: 'grid',
                minHeight:500,
                id: 'Koltiva.view.Traceability_new.Delivery.MainGridTransaction-Grid',
                style: 'border:1px solid #CCC;margin-top:4px;',
                cls: 'Sfr_GridNew',
                loadMask: true,
                selType: 'rowmodel',
                store: thisObj.StoreGridTransactionDetail,
                enableColumnHide: false,
                viewConfig: {
                    deferEmptyText: false,
                    emptyText: GetDefaultContentNoData()
                },
                dockedItems: [{
                        xtype: 'pagingtoolbar',
                        store: thisObj.StoreGridTransactionDetail,
                        dock: 'bottom',
                        displayInfo: true,
                        displayMsg: lang('Showing') + ' {0} ' + lang('to') + ' {1} ' + lang('of') + ' {2} ' + lang('data')
                    }, {
                        xtype: 'toolbar',
                        dock: 'top',
                        items: [
                            {
                                xtype: 'button',
                                icon: varjs.config.base_url + 'images/icons/new/back.png"',
                                text: lang('Back to Selling'),
                                cls: 'Sfr_BtnGridBlue',
                                overCls: 'Sfr_BtnGridBlue-Hover',
                                handler: function () {
                                    window.location.href = "/traceability_new/delivery";
                                }
                            },
                            {
                                xtype: 'button',
                                id: 'Koltiva.view.Traceability_new.Transaction.MainGridPengiriman-gridToolbar-BtnExport',
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

                                    // var param_string             = thisObj.viewVar.SupplyBatchNumber;
                                    var param_string_delivery    = thisObj.viewVar.DeliveryID;
                                    
                                    try {
                                        Ext.destroy(Ext.get('downloadIframe'));
                                    }
                                    catch(e) {}
    
                                    Ext.Ajax.request({
                                        url: m_api+'/traceability_api/web_pengiriman/export_excel_detail_transaction/'+param_string_delivery,
                                    
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
                            }]
                    }],
                columns: [{
                        text: 'No',
                        width: 45,
                        align:'center',
                        xtype: 'rownumberer'
                    },
                    {
                        text: lang('Supply Type'),
                        dataIndex: 'SupplyType',
                        width: 100,
                    },
                    {
                        text: lang('Supplier ID'),
                        dataIndex: 'MemberDisplayID',
                        flex:1,
                    },
                    {
                        text: lang('Supply Selling Number'),
                        dataIndex: 'SupplyBatchNumber',
                        flex:1,
                    },
                    {
                        text: lang('Supplier Name'),
                        dataIndex: 'SupplierName',
                        width: 100,
                    },
                    {
                        text: lang('Certification'),
                        dataIndex: 'Certified',
                        width: 100,
                    },
                    {
                        text: lang('Date'),
                        dataIndex: 'DateTransaction',
                        width: 100,
                    },
                    {
                        text: lang('Gross Weight (kg)'),
                        dataIndex: 'VolumeBruto',
                        width: 100,
                    },
                    {
                        text: lang('Nett Weight (kg)'),
                        dataIndex: 'VolumeNetto',
                        width: 100,
                    },
                    {
                        text: lang('Registered Agent'),
                        dataIndex: 'AgentOtherSurvey',
                        width: 100,
                    },
                    {
                        text: lang('Survery Agent'),
                        dataIndex: 'AgentOther',
                        width: 100,
                    }
                ]
            }];

        this.callParent(arguments);
    }
});
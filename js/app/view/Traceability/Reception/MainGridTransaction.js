Ext.define('Koltiva.view.Traceability.Reception.MainGridTransaction', {
    extend: 'Ext.panel.Panel',
    id: 'Koltiva.view.Traceability.Reception.MainGridTransaction',
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
        thisObj.StoreGridTransactionDetail = Ext.create('Koltiva.store.Traceability.Reception.MainGridTransactionDetail', {
            storeVar: {
                ShippingDate : thisObj.viewVar.ShippingDate
            }
        });
  
        //ContextMenu       
        thisObj.items = [{
                xtype: 'grid',
                minHeight:500,
                id: 'Koltiva.view.Traceability.Reception.MainGridTransaction-Grid',
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
                                text: lang('Back to Reception'),
                                cls: 'Sfr_BtnGridBlue',
                                overCls: 'Sfr_BtnGridBlue-Hover',
                                handler: function () {
                                    window.location.href = "/traceability/reception";
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

                                    var param_string    = thisObj.viewVar.ShippingDate;
                                    
                                    try {
                                        Ext.destroy(Ext.get('downloadIframe'));
                                    }
                                    catch(e) {}
    
                                    Ext.Ajax.request({
                                        url: m_api+'/dispatch/refinery/export_reception_detail/'+param_string,
                                    
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
                        width: 35,
                        align:'center',
                        xtype: 'rownumberer'
                    },
                    {
                        text: lang('Date'),
                        dataIndex: 'DateBuying',
                        width:100,
                    },
                    {
                        text: lang('Mill Name'),
                        dataIndex: 'CompanyName',
                        width:100,
                    },
                    {
                        text: lang('Product Name'),
                        dataIndex: 'ProductName',
                        width:100,
                    },
                    {
                        text: lang('Reception Volume'),
                        dataIndex: 'ReceptionVolume',
                        width:100,
                    },
                    {
                        text: lang('Shipping Date'),
                        dataIndex: 'ShippingDate',
                        width:100,
                    },
                    {
                        text: lang('Dealer'),
                        dataIndex: 'Dealer',
                        width:100,
                    },
                    {
                        text: lang('Reception Number'),
                        dataIndex: 'ReceptionNumber',
                        width:100,
                    },
                    {
                        text: lang('Dispatch Number'),
                        dataIndex: 'DespatchNumber',
                        width:100,
                    },
                    {
                        text: lang('Supplier'),
                        dataIndex: 'SupplierName',
                        width:100,
                    },
                    {
                        text: lang('Date Selling'),
                        dataIndex: 'DateSelling',
                        width:100,
                    },
                    {
                        text: lang('Supply Batch'),
                        dataIndex: 'SupplyBatchNumber',
                        width:100,
                    }
                ]
            }];

        this.callParent(arguments);
    },

    BackToList: function () {
        Ext.getCmp('Koltiva.view.Traceability.Reception.MainGridTransaction-Grid').destroy(); //destory current view
        var GridMain = [];
        if (Ext.getCmp('Koltiva.view.Traceability_new.Reception.GridReception-Grid') == undefined) {
            GridMain = Ext.create('Koltiva.view.Traceability_new.Reception.GridReception-Grid');
        } else {
            //destroy, create ulang
            Ext.getCmp('Koltiva.view.Traceability_new.Reception.GridReception-Grid').destroy();
            GridMain = Ext.create('Koltiva.view.Traceability_new.Reception.GridReception-Grid');
        }
    }
    
});
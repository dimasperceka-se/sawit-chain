Ext.define('Koltiva.view.Traceability_new.Batching.WinFormDataTransaction' ,{
    extend: 'Ext.window.Window',
    id: 'Koltiva.view.Traceability_new.Batching.WinFormDataTransaction',
    title: lang('List Transaction'),
    closable: true,
    modal: true,
    closeAction: 'destroy',
    width: '90%',
    height: 555,
    overflowY: 'auto',
    style:'padding:2px;',
    viewVar: false,
    setViewVar: function(value){
        this.viewVar = value;
    },
    listeners: {
        afterRender: function () {
            var thisObj = this;
        }
    },
    initComponent: function() {
        var thisObj = this;

        thisObj.ArrCheckTransDetailID = [];
       
        thisObj.StoreGridMain = Ext.create('Koltiva.store.Traceability_new.Batching.WinFormDataTransactionGrid',{
            storeVar: {
                SupplierName: null,
                StartTransactionDate: null,
                EndTransactionDate: null,
                SupplyType: null
            }
        });
        //Store ========================= (End)

        var ComboSupplyBatchCategory = Ext.create('Ext.data.Store', {
            fields: ['id', 'label'],
            data : [
                {"label":lang('Dealer'), "id":'Farmer'},
                {"label":lang('Own state'), "id":'Nonfarmer'}
            ]
        });

        thisObj.items = [{
            xtype: 'grid',
            id: 'Koltiva.view.Traceability_new.Batching.WinFormDataTransaction-MainGrid',
            style: 'border:1px solid #CCC;margin-top:4px;',
            cls:'Sfr_GridNew',
            loadMask: true,
            selType: 'rowmodel',
            store: thisObj.StoreGridMain,
            enableColumnHide: false,
            height:450,
            viewConfig: {
                deferEmptyText: false,
                emptyText: GetDefaultContentNoData()
            },
            dockedItems: [{
                xtype: 'toolbar',
                dock: 'top',
            },{
                layout: 'column',
                border: false,
                items:  [{
                    columnWidth: 0.435,
                    layout: 'form',
                    style: 'padding:10px 5px 10px 20px;',
                    defaults: {
                        labelAlign: 'left',
                        labelWidth: 150
                    },
                    items: [{
                        xtype: 'textfield',
                        id: 'Koltiva.view.Traceability_new.Batching.WinFormDataTransaction-MemberName',
                        name: 'Koltiva.view.Traceability_new.Batching.WinFormDataTransaction-MemberName',
                        fieldLabel: lang('Farmer Name')
                    },{
                        xtype: 'button',
                        id: 'Koltiva.view.Traceability_new.Transaction.window.WinListTransactionPenerimaan-gridToolbar-BtnExport',
                        icon: varjs.config.base_url + 'images/icons/new/export.png',
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
                            Ext.Ajax.request({
                                url: m_api + '/traceability_api/web_penerimaan/export_detail_batch',
                                method: 'GET',
                                waitMsg: lang('Export data...'),
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
                }, {
                    columnWidth: 0.435,
                    layout: 'form',
                    style: 'padding:10px 0px 10px 20px;',
                    defaults: {
                        labelAlign: 'left',
                        labelWidth: 150
                    },
                    items: [{
                        xtype: 'datefield',
                        id: 'Koltiva.view.Traceability_new.Batching.WinFormDataTransaction-StartTransactionDate',
                        name: 'Koltiva.view.Traceability_new.Batching.WinFormDataTransaction-StartTransactionDate',
                        format: 'Y-m-d',
                        fieldLabel: lang('Start Transaction Date')
                    },{
                        xtype: 'datefield',
                        id: 'Koltiva.view.Traceability_new.Batching.WinFormDataTransaction-EndTransactionDate',
                        name: 'Koltiva.view.Traceability_new.Batching.WinFormDataTransaction-EndTransactionDate',
                        format: 'Y-m-d',
                        fieldLabel: lang('End Transaction Date')
                    }]
                }, {
                    columnWidth: 0.13,
                    layout: 'form',
                    style: 'padding:10px 0px 10px 20px;',
                    items: [{
                        xtype:'button',
                        icon: varjs.config.base_url + 'images/icons/silk/search.png',
                        text: lang('Search'),
                        cls:'Sfr_BtnGridBlue',
                        overCls:'Sfr_BtnGridBlue-Hover',
                        handler: function() {
                            thisObj.AddValidation = true;
                            thisObj.MsgAddValidation = "";
                            thisObj.AddValidationBasicForm();
                            if(thisObj.AddValidation == true) {

                                let StartTransactionDate = Ext.Date.format(Ext.getCmp('Koltiva.view.Traceability_new.Batching.WinFormDataTransaction-StartTransactionDate').getValue(), 'Y-m-d');
                                let EndTransactionDate = Ext.Date.format(Ext.getCmp('Koltiva.view.Traceability_new.Batching.WinFormDataTransaction-EndTransactionDate').getValue(), 'Y-m-d');
                                
                                thisObj.StoreGridMain.storeVar.MemberName           = Ext.getCmp('Koltiva.view.Traceability_new.Batching.WinFormDataTransaction-MemberName').getValue();
                                thisObj.StoreGridMain.storeVar.StartTransactionDate = StartTransactionDate;
                                thisObj.StoreGridMain.storeVar.EndTransactionDate   = EndTransactionDate;
                                // thisObj.StoreGridMain.storeVar.SupplyType           = Ext.getCmp('Koltiva.view.Traceability_new.Batching.MainForm-FormBasicData-SupplyType').getValue();
                                
                                thisObj.StoreGridMain.load();
                            } else {
                                Ext.MessageBox.show({
                                    title: lang('Information'),
                                    msg: thisObj.MsgAddValidation,
                                    buttons: Ext.MessageBox.OK,
                                    animateTarget: 'mb9',
                                    icon: 'ext-mb-info'
                                });
                            }
                        }
                    }]
                }]
            }],
            columns: [{
                xtype: 'checkcolumn',
                sortable: false,
                text: '',
                dataIndex: '',
                width: 55,
                listeners: {
                    checkchange: function(column, rowIndex, checked){
                        var rec = Ext.getCmp('Koltiva.view.Traceability_new.Batching.WinFormDataTransaction-MainGrid').getStore().getAt(rowIndex);
                    
                        if(checked == true) {
                            thisObj.ArrCheckTransDetailID.push(rec.raw);
                        } else {
                            if (parseInt(thisObj.ArrCheckTransDetailID.length) > 0) {
                                thisObj.ArrCheckTransDetailID.forEach(function(v, k, o){
                                    if (v.TransSupplyID == rec.raw.TransSupplyID) {
                                        o.splice(k, 1);
                                    }
                                })
                            }
                        }

                        //sorting berdasarkan transsuplyidnya yang terbesar dulu
                        if (parseInt(thisObj.ArrCheckTransDetailID.length) > 0) {
                            thisObj.ArrCheckTransDetailID.sort( function ( a, b ) {return b.TransSupplyID - a.TransSupplyID})
                        }
                    }
                }
            }, {
                text: lang('Trans Type'),
                dataIndex: 'TransTypeName',
                flex:20
            }, {
                text: lang('Trans Supply ID'),
                dataIndex: 'TransSupplyID',
                flex:20
            }, {
                text: lang('Farmer Name'),
                dataIndex: 'MemberName',
                flex:20
            }, {
                text: lang('Suplier Category'),
                dataIndex: 'SupplyType',
                flex:20
            }, {
                text: lang('Transaction Date'),
                dataIndex: 'DateTransaction',
                flex:20
            }, {
                text: lang('Gross Weight'),
                dataIndex: 'GrossWeight',
                flex:20
            },{
                text: lang('Nett Weight'),
                dataIndex: 'NettWeight',
                flex:20
            }]
        }];

        thisObj.buttons = [{
            icon: varjs.config.base_url + 'images/icons/new/save.png',
            cls: 'Sfr_BtnFormBlue',
            overCls: 'Sfr_BtnFormBlue-Hover',
            text: lang('Save'),
            id: 'Koltiva.view.Traceability_new.Batching.WinFormDataTransaction-Form-BtnSave',
            handler: function (rowIndex) {
                if (thisObj.ArrCheckTransDetailID === undefined || thisObj.ArrCheckTransDetailID.length == 0) {
                    Ext.MessageBox.show({
                        title: lang('Attention'),
                        msg: lang('Please select a transaction that want to in the process'),
                        buttons: Ext.MessageBox.OK,
                        animateTarget: 'mb9',
                        icon: 'ext-mb-info'
                    });
                } else {
                    var Formnya = Ext.getCmp('Koltiva.view.Traceability_new.Batching.MainForm-FormBasicData').getForm();

                    Formnya.submit({
                        waitMsg: 'Please Wait',
                        url: m_api + '/traceability_api/batching/data_batch_transaction',
                        method: 'POST',
                        params: {
                            TransDetailID : JSON.stringify(thisObj.ArrCheckTransDetailID)
                        },
                        success: function (response, opts) {

                            Ext.MessageBox.show({
                                title: 'Information',
                                msg: lang('Data saved'),
                                buttons: Ext.MessageBox.OK,
                                animateTarget: 'mb9',
                                icon: 'ext-mb-success',
                                fn: function (btn) {
                                    if (btn == 'ok') {

                                        thisObj.close();

                                        Ext.getCmp('Koltiva.view.Traceability_new.Batching.MainForm').destroy();
                                        var MainForm = [];
                                        if (Ext.getCmp('Koltiva.view.Traceability_new.Batching.MainForm') == undefined) {
                                            MainForm = Ext.create('Koltiva.view.Traceability_new.Batching.MainForm', {
                                                viewVar: {
                                                    OpsiDisplay: 'update',
                                                    SupplyBatchID: opts.result.SupplyBatchID,
                                                    SupplyBatchStatusID: '3'
                                                }
                                            });
                                        } else {
                                            Ext.getCmp('Koltiva.view.Traceability_new.Batching.MainForm').destroy();
                                            MainForm = Ext.create('Koltiva.view.Traceability_new.Batching.MainForm', {
                                                viewVar: {
                                                    OpsiDisplay: 'update',
                                                    SupplyBatchID: opts.result.SupplyBatchID,
                                                    SupplyBatchStatusID: '3'
                                                }
                                            });
                                        }
                                    }
                                }
                            });
                        },
                        failure: function (rp, o) {
                            try {
                                var r = Ext.decode(o.response.responseText);
                                Ext.MessageBox.show({
                                    title: lang('Error'),
                                    msg: r.message,
                                    buttons: Ext.MessageBox.OK,
                                    animateTarget: 'mb9',
                                    icon: 'ext-mb-error',
                                    fn: function (btn) {
                                        if (btn == 'ok') {

                                            thisObj.close();

                                            Ext.getCmp('Koltiva.view.Traceability_new.Batching.MainForm').destroy();
                                            var MainForm = [];
                                            if (Ext.getCmp('Koltiva.view.Traceability_new.Batching.MainForm') == undefined) {
                                                MainForm = Ext.create('Koltiva.view.Traceability_new.Batching.MainForm', {
                                                    viewVar: {
                                                        OpsiDisplay: 'update',
                                                        SupplyBatchID: r.SupplyBatchID
                                                    }
                                                });
                                            } else {
                                                Ext.getCmp('Koltiva.view.Traceability_new.Batching.MainForm').destroy();
                                                MainForm = Ext.create('Koltiva.view.Traceability_new.Batching.MainForm', {
                                                    viewVar: {
                                                        OpsiDisplay: 'update',
                                                        SupplyBatchID: r.SupplyBatchID
                                                    }
                                                });
                                            }
                                        }
                                    }
                                });

                            } catch (err) {
                                Ext.MessageBox.show({
                                    title: lang('Error'),
                                    msg: lang('Connection Error'),
                                    buttons: Ext.MessageBox.OK,
                                    animateTarget: 'mb9',
                                    icon: 'ext-mb-error'
                                });
                            }
                        }
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
                thisObj.ArrCheckTransDetailID = [];
            }
        }];

        this.callParent(arguments);
    },
    AddValidationBasicForm: function() {
        var thisObj = this;
        var ArrMsg = [];
        thisObj.AddValidation = true;

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
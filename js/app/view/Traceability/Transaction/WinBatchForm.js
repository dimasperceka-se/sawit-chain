Ext.define('Koltiva.view.Traceability.Transaction.WinBatchForm' ,{
    extend: 'Ext.window.Window',
    id: 'Koltiva.view.Traceability.Transaction.WinBatchFrom',
    title: lang('Form Batch'),
    closable: false,
    modal: true,
    closeAction: 'destroy',
    width: '70%',
    height: '90%',
    overflowY: 'auto',
    viewVar: false,
    setViewVar: function(value){
        this.viewVar = value;
    },
    initComponent: function() {
        var thisObj = this;

        //store yg dipakai ======================= (begin)
        var storeGridTransactionBatch = Ext.create('Koltiva.store.Traceability.Transaction.GridTransactionBatch');
        var cmbDestination = Ext.create('Koltiva.store.Traceability.Transaction.ComboDestination');
        //store yg dipakai ======================= (end)

        thisObj.items = [{
            xtype: 'form',
            id: 'Koltiva.view.Traceability.Transaction.WinBatchFrom-Form',
            padding:'5 25 5 8',
            items:[{
                layout: 'column',
                border: false,
                items:[{
                    columnWidth: 1,
                    layout:'form',
                    items:[{
                        xtype:'panel',
                        title: 'A. '+lang('Basic Data'),
                        frame:true,
                        style:'margin-bottom:13px;',
                        padding:5,
                        items:[{
                            layout: 'column',
                            border: false,
                            items: [{
                                columnWidth: 0.5,
                                layout:'form',
                                items:[{
                                    xtype: 'hiddenfield',
                                    id: 'Koltiva.view.Traceability.Transaction.WinBatchFrom-Form-SupplyBatchID',
                                    name: 'SupplyBatchID'
                                },{
                                    xtype: 'textfield',
                                    fieldLabel: lang('Batch Number'),
                                    labelWidth: 200,
                                    id: 'Koltiva.view.Traceability.Transaction.WinBatchFrom-Form-SupplyBatchNumber',
                                    name: 'SupplyBatchNumber'
                                },{
                                    xtype: 'textfield',
                                    fieldLabel: lang('No. PO'),
                                    labelWidth: 200,
                                    id: 'Koltiva.view.Traceability.Transaction.WinBatchFrom-Form-DestPO',
                                    name: 'DestPO'
                                }, {
                                    layout: 'column',
                                    items: [{
                                            columnWidth: 0.7,
                                            layout: 'form',
                                            bodyPadding: 0,
                                            style: 'margin-top: -10px',
                                            fieldDefaults: {
                                                labelAlign: 'left',
                                                labelWidth: 200,
                                                anchor: '100%'
                                            },
                                            items: [{
                                                    xtype: 'datefield',
                                                    fieldLabel: lang('Batch Date'),
                                                    labelWidth: 200,
                                                    id: 'Koltiva.view.Traceability.Transaction.WinBatchFrom-Form-SupplyBatchDate',
                                                    name: 'SupplyBatchDate',
                                                    format: 'Y-m-d',
                                                    value: m_date
                                                }]
                                        }, {
                                            columnWidth: 0.3,
                                            layout: 'form',
                                            padding: 0,
                                            style: 'margin-top: -10px',
                                            fieldDefaults: {
                                                labelAlign: 'left',
                                                anchor: '100%'
                                            },
                                            items: [{
                                                    xtype: 'timefield',
                                                    id: 'Koltiva.view.Traceability.Transaction.WinBatchFrom-Form-SupplyBatchTime',
                                                    name: 'SupplyBatchTime',
                                                    format: 'H:i',
                                                    value: m_time,
                                                    listeners: {
                                                        change: function (c, v) {

                                                        }
                                                    }
                                                }]
                                        }]
                                }, {
                                    layout: 'column',
                                    items: [{
                                            columnWidth: 0.7,
                                            layout: 'form',
                                            bodyPadding: 0,
                                            style: 'margin-top: -10px',
                                            fieldDefaults: {
                                                labelAlign: 'left',
                                                labelWidth: 200,
                                                anchor: '100%'
                                            },
                                            items: [{
                                                    xtype: 'datefield',
                                                    fieldLabel: lang('Delivery Date'),
                                                    labelWidth: 200,
                                                    id: 'Koltiva.view.Traceability.Transaction.WinBatchFrom-Form-DeliveryDate',
                                                    name: 'DeliveryDate',
                                                    format: 'Y-m-d',
                                                    value: m_date
                                                }]
                                        }, {
                                            columnWidth: 0.3,
                                            layout: 'form',
                                            padding: 0,
                                            style: 'margin-top: -10px',
                                            fieldDefaults: {
                                                labelAlign: 'left',
                                                anchor: '100%'
                                            },
                                            items: [{
                                                    xtype: 'timefield',
                                                    id: 'Koltiva.view.Traceability.Transaction.WinBatchFrom-Form-DeliveryTime',
                                                    name: 'DeliveryTime',
                                                    format: 'H:i',
                                                    value: m_time,
                                                    listeners: {
                                                        change: function (c, v) {

                                                        }
                                                    }
                                                }]
                                        }]
                                }, {
                                    layout: 'column',
                                    items: [{
                                            columnWidth: 0.7,
                                            layout: 'form',
                                            bodyPadding: 0,
                                            style: 'margin-top: -10px',
                                            fieldDefaults: {
                                                labelAlign: 'left',
                                                labelWidth: 200,
                                                anchor: '100%'
                                            },
                                            items: [{
                                                    xtype: 'datefield',
                                                    fieldLabel: lang('Estimated Time'),
                                                    labelWidth: 200,
                                                    id: 'Koltiva.view.Traceability.Transaction.WinBatchFrom-Form-EstimatedDate',
                                                    name: 'EstimatedDate',
                                                    format: 'Y-m-d'
                                                }]
                                        }, {
                                            columnWidth: 0.3,
                                            layout: 'form',
                                            padding: 0,
                                            style: 'margin-top: -10px',
                                            fieldDefaults: {
                                                labelAlign: 'left',
                                                anchor: '100%'
                                            },
                                            items: [{
                                                    xtype: 'timefield',
                                                    id: 'Koltiva.view.Traceability.Transaction.WinBatchFrom-Form-EstimatedTime',
                                                    name: 'EstimatedTime',
                                                    format: 'H:i',
                                                    listeners: {
                                                        change: function (c, v) {

                                                        }
                                                    }
                                                }]
                                        }]
                                }]
                            }, {
                                columnWidth: 0.5,
                                layout:'form',
                                style:'margin-left:10px;',
                                items:[{
                                    xtype: 'textfield',
                                    hidden: true,
                                    fieldLabel: lang('Total Bruto'),
                                    labelWidth: 200,
                                    readOnly: true,
                                    id: 'Koltiva.view.Traceability.Transaction.WinBatchFrom-Form-VolumeBruto',
                                    name: 'VolumeBruto'
                                }, {
                                    xtype: 'textfield',
                                    fieldLabel: lang('Total Netto'),
                                    labelWidth: 200,
                                    readOnly: true,
                                    id: 'Koltiva.view.Traceability.Transaction.WinBatchFrom-Form-VolumeNetto',
                                    name: 'VolumeNetto'
                                }, {
                                    xtype: 'combobox',
                                    labelWidth: 200,
                                    fieldLabel: lang('Destination'),
                                    store: cmbDestination,
                                    queryMode: 'local',
                                    displayField: 'name',
                                    valueField: 'id',
                                    allowBlank: false,
                                    id: 'Koltiva.view.Traceability.Transaction.WinBatchFrom-Form-SupplyDestOrgID',
                                    name: 'SupplyDestOrgID'
                                }, {
                                    xtype: 'textfield',
                                    fieldLabel: lang('Destination Weight'),
                                    labelWidth: 200,
                                    id: 'Koltiva.view.Traceability.Transaction.WinBatchFrom-Form-DestWeight',
                                    name: 'DestWeight'
                                }, {
                                    xtype: 'textfield',
                                    fieldLabel: lang('Total Tandan'),
                                    labelWidth: 200,
                                    id: 'Koltiva.view.Traceability.Transaction.WinBatchFrom-Form-DestNumberPackage',
                                    name: 'DestNumberPackage'
                                }]
                            }]
                        }]
                    }, {
                        xtype:'panel',
                        title: 'B. '+lang('Driver'),
                        frame:true,
                        style:'margin-bottom:13px;',
                        padding:5,
                        items:[{
                            layout: 'column',
                            border: false,
                            items: [{
                                columnWidth: 0.5,
                                layout:'form',
                                items:[{
                                    xtype: 'textfield',
                                    fieldLabel: lang('Name'),
                                    labelWidth: 200,
                                    id: 'Koltiva.view.Traceability.Transaction.WinBatchFrom-Form-DestDriver',
                                    name: 'DestDriver'
                                },{
                                    xtype: 'textfield',
                                    fieldLabel: lang('Address'),
                                    labelWidth: 200,
                                    id: 'Koltiva.view.Traceability.Transaction.WinBatchFrom-Form-DestDriverAddress',
                                    name: 'DestDriverAddress'
                                }]
                            }, {
                                columnWidth: 0.5,
                                layout:'form',
                                style:'margin-left:10px;',
                                items:[{
                                    xtype: 'textfield',
                                    fieldLabel: lang('Handphone'),
                                    labelWidth: 200,
                                    id: 'Koltiva.view.Traceability.Transaction.WinBatchFrom-Form-DestDriverHandphone',
                                    name: 'DestDriverHandphone'
                                },{
                                    xtype: 'textfield',
                                    fieldLabel: lang('Vehicle Number'),
                                    labelWidth: 200,
                                    id: 'Koltiva.view.Traceability.Transaction.WinBatchFrom-Form-DestNoPolisi',
                                    name: 'DestNoPolisi'
                                }]
                            }]
                        }]
                    }, {
                        xtype:'panel',
                        title: 'C. '+lang('Transactions List'),
                        frame:true,
                        style:'margin-bottom:13px;',
                        padding:5,
                        items:[{
                            xtype: 'grid',
                            id: 'Koltiva.view.Traceability.Transaction.WinBatchFrom-Form-gridTransaction',
                            style: 'border:1px solid #CCC;margin-top:4px;',
                            loadMask: true,
                            selType: 'rowmodel',
                            store: storeGridTransactionBatch,
                            viewConfig: {
                                deferEmptyText: false,
                                emptyText: lang('No data Available')
                            },
                            dockedItems: [{
                                xtype: 'pagingtoolbar',
                                id: 'Koltiva.view.Traceability.Transaction.WinBatchFrom-Form-gridTransaction-Toolbar',
                                store: storeGridTransactionBatch,
                                dock: 'bottom',
                                displayInfo: true
                            },{
                                xtype: 'toolbar',
                                dock:'top',
                                items: [{
                                    name: 'Keyword',
                                    id: 'Koltiva.view.Traceability.Transaction.WinBatchFrom-Form-gridTransaction-Keyword',
                                    xtype: 'textfield',
                                    width: 300,
                                    emptyText: lang('Search by Name / ID')
                                },{
                                    xtype: 'button',
                                    id: 'Koltiva.view.Traceability.Transaction.WinBatchFrom-Form-gridTransaction-BtnSearch',
                                    icon: varjs.config.base_url + 'images/icons/silk/search.png',
                                    margin: '0px 10px 0px 6px',
                                    text: lang('Search'),
                                    handler: function() {
                                        storeGridTransactionBatch.load()
                                    }
                                }, {
                                    xtype: 'container',
                                    flex: 1
                                }, {
                                    icon: varjs.config.base_url + 'images/icons/new/add.png', cls:'Sfr_BtnGridGreen', overCls:'Sfr_BtnGridGreen-Hover',
                                    text: lang('Add'),
                                    //hidden: m_act_add,
                                    handler: function() {
                                        var WinTransactionAvailableForm = Ext.create('Koltiva.view.Traceability.Transaction.WinTransactionAvailableForm',{
                                            viewVar: {
                                                opsiDisplay: 'view'
                                            }
                                        });
                                        if (!WinTransactionAvailableForm.isVisible()) {
                                            WinTransactionAvailableForm.center();
                                            WinTransactionAvailableForm.show();
                                        } else {
                                            WinTransactionAvailableForm.close();
                                        }
                                    }
                                }]
                            }],
                            columns: [{
                                text: lang('Action'),
                                xtype:'actioncolumn',
                                width:'4%',
                                items:[{
                                    icon: varjs.config.base_url + 'images/icons/new/action.png',
                                    handler: function(grid, rowIndex, colIndex, item, e, record) {
                                        /*contextMenuTransactionGrid.showAt(e.getXY());
                                        var sm = record;
                                        if(sm.data.SupplyStatus == "Sent"){
                                            contextMenuTransactionGrid.getComponent('Koltiva.view.Traceability.Transaction.MainGrid-contextMenuUpdateItem').setVisible(false);
                                            contextMenuTransactionGrid.getComponent('Koltiva.view.Traceability.Transaction.MainGrid-contextMenuViewItem').setVisible(true);
                                            contextMenuTransactionGrid.getComponent('Koltiva.view.Traceability.Transaction.MainGrid-contextMenuDeleteItem').setVisible(false);
                                        }else{
                                            contextMenuTransactionGrid.getComponent('Koltiva.view.Traceability.Transaction.MainGrid-contextMenuUpdateItem').setVisible(true);
                                            contextMenuTransactionGrid.getComponent('Koltiva.view.Traceability.Transaction.MainGrid-contextMenuViewItem').setVisible(false);
                                            contextMenuTransactionGrid.getComponent('Koltiva.view.Traceability.Transaction.MainGrid-contextMenuDeleteItem').setVisible(true);
                                        }*/
                                    }
                                }]
                            },{
                                text: 'ID',
                                dataIndex: 'SupplyTransID',
                                hidden: true
                            },{
                                text: lang('Type'),
                                dataIndex: 'SupplyType',
                                flex: 1,
                            },{
                                text: lang('Date'),
                                dataIndex: 'DateTransaction',
                                renderer: Ext.util.Format.dateRenderer('d-m-Y'),
                                flex: 1,
                            },{
                                text: lang('Faktur Number'),
                                dataIndex: 'FakturNumber',
                                flex: 2,
                            },{
                                text: lang('From'),
                                dataIndex: 'Name',
                                flex: 2,
                            },{
                                text: lang('Netto'),
                                dataIndex: 'VolumeNetto',
                                flex: 1,
                            }]
                        }]
                    }]
                }]
            }]
        }];

        thisObj.buttons = [{
            id: 'Koltiva.view.Traceability.Transaction.WinBatchFrom-Form-btnSave',
            text: lang('Save'),
            margin: '5px',
            scale: 'large',
            ui: 's-button',
            cls: 's-blue',
            handler: function() {
                var formNya = Ext.getCmp('Koltiva.view.Traceability.Transaction.WinBatchFrom-Form').getForm();
                if (formNya.isValid()) {

                    formNya.submit({
                        url: m_api + '/tc_transaction/batch',
                        method:'put',
                        waitMsg: 'Saving data...',
                        success: function (fp, o) {
                            var flds = JSON.parse(o.response.responseText);  
                            if(flds.success==true){
                                Ext.getCmp('Koltiva.view.Traceability.Transaction.WinBatchFrom-Form-BtnCancel').hide();
                                Ext.getCmp('Koltiva.view.Traceability.Transaction.WinBatchFrom-Form-BtnClose').show();
                                Ext.getCmp('Koltiva.view.Traceability.Transaction.WinBatchFrom-Form-gridTransaction').getStore().load();
                                Ext.getCmp('Koltiva.view.Traceability.Transaction.WinBatchFrom-Form-gridBatch').getStore().load();
                            }
                            //Ext.MessageBox.alert(flds.info, lang(flds.message));
                            Ext.MessageBox.show({
                                title: lang(flds.info),
                                msg: lang(flds.message),
                                buttons: Ext.MessageBox.OK,
                                animateTarget: 'mb9',
                                icon: flds.icon
                            });
                        },
                        failure: function(response, opts) {
                            Ext.MessageBox.show({
                                title: 'Error',
                                msg: lang('Could not connect to the database. Retry later'),
                                buttons: Ext.MessageBox.OK,
                                animateTarget: 'mb9',
                                icon: 'ext-mb-error'
                            });
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
            id: 'Koltiva.view.Traceability.Transaction.WinBatchFrom-Form-BtnClose',
            hidden: true,
            margin: '5px',
            scale: 'large',
            ui: 's-button',
            cls: 's-grey',
            handler: function() {
                //tutup popup
                thisObj.close();
            }
        },{
            text: lang('Cancel'),
            id: 'Koltiva.view.Traceability.Transaction.WinBatchFrom-Form-BtnCancel',
            margin: '5px',
            scale: 'large',
            ui: 's-button',
            cls: 's-grey',
            handler: function() {
                Ext.Ajax.request({
                    waitMsg: 'Please Wait',
                    url: m_api + '/tc_transaction/batch',
                    method: 'DELETE',
                    params: {
                        SupplyBatchID: Ext.getCmp('Koltiva.view.Traceability.Transaction.WinBatchFrom-Form-SupplyBatchID').getValue(),
                    },
                    success: function(response, opts) {
                        var r = Ext.decode(response.responseText);
                        if(r.success==true){
                            thisObj.close();
                        }else{
                            Ext.MessageBox.show({
                                title: lang(flds.info),
                                msg: lang(flds.message),
                                buttons: Ext.MessageBox.OK,
                                animateTarget: 'mb9',
                                icon: flds.icon
                            });
                        }
                    },
                    failure: function(response, opts) {
                        Ext.MessageBox.show({
                            title: 'Error',
                            msg: lang('Could not connect to the database. Retry later'),
                            buttons: Ext.MessageBox.OK,
                            animateTarget: 'mb9',
                            icon: 'ext-mb-error'
                        });
                    }
                });
                thisObj.close();
            }
        }];

        this.callParent(arguments);
    },
    listeners: {
        afterRender: function(){
            var thisObj = this;

            //form reset
            var formNya = Ext.getCmp('Koltiva.view.Traceability.Transaction.WinBatchFrom-Form');
            formNya.getForm().reset();

            if(thisObj.viewVar.opsiDisplay == 'insert'){
                Ext.getCmp('Koltiva.view.Traceability.Transaction.WinBatchFrom-Form-BtnCancel').show();
                Ext.getCmp('Koltiva.view.Traceability.Transaction.WinBatchFrom-Form-BtnClose').hide();
            }

            if(thisObj.viewVar.opsiDisplay == 'view' || thisObj.viewVar.opsiDisplay == 'update'){
                Ext.getCmp('Koltiva.view.Traceability.Transaction.WinBatchFrom-Form-BtnCancel').hide();
                Ext.getCmp('Koltiva.view.Traceability.Transaction.WinBatchFrom-Form-BtnClose').show();
            }
            
            Ext.Ajax.request({
                waitMsg: 'Please Wait',
                url: m_api + '/tc_transaction/batch',
                method: 'POST',
                params: {
                    SupplychainID: '',
                },
                success: function(response, opts) {
                    var r = Ext.decode(response.responseText);
                    if(r.success==true){
                        var dt = new Date();
                        Ext.getCmp('Koltiva.view.Traceability.Transaction.WinBatchFrom-Form-SupplyBatchID').setValue(r.SupplyBatchID);
                        Ext.getCmp('Koltiva.view.Traceability.Transaction.WinBatchFrom-Form-SupplyBatchDate').setValue(Ext.Date.format(dt, 'Y-m-d'));
                        Ext.getCmp('Koltiva.view.Traceability.Transaction.WinBatchFrom-Form-SupplyBatchTime').setValue(Ext.Date.format(dt, 'H:i'));
                        Ext.getCmp('Koltiva.view.Traceability.Transaction.WinBatchFrom-Form-DeliveryDate').setValue(Ext.Date.format(dt, 'Y-m-d'));
                        Ext.getCmp('Koltiva.view.Traceability.Transaction.WinBatchFrom-Form-DeliveryTime').setValue(Ext.Date.format(dt, 'H:i'));
                    }else{
                        Ext.MessageBox.show({
                            title: lang(flds.info),
                            msg: lang(flds.message),
                            buttons: Ext.MessageBox.OK,
                            animateTarget: 'mb9',
                            icon: flds.icon
                        });
                    }
                },
                failure: function(response, opts) {
                    Ext.MessageBox.show({
                        title: 'Error',
                        msg: lang('Could not connect to the database. Retry later'),
                        buttons: Ext.MessageBox.OK,
                        animateTarget: 'mb9',
                        icon: 'ext-mb-error'
                    });
                }
            }); 
        }
    }
});
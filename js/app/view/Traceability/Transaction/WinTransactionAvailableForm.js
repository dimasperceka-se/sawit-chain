Ext.define('Koltiva.view.Traceability.Transaction.WinTransactionAvailableForm' ,{
    extend: 'Ext.window.Window',
    id: 'Koltiva.view.Traceability.Transaction.WinTransactionAvailableForm',
    title: lang('Form Batch'),
    closable: true,
    modal: true,
    closeAction: 'destroy',
    width: '50%',
    height: '90%',
    overflowY: 'auto',
    viewVar: false,
    setViewVar: function(value){
        this.viewVar = value;
    },
    initComponent: function() {
        var thisObj = this;

        //store yg dipakai ======================= (begin)
        var storeGridTransactionAvailable = Ext.create('Koltiva.store.Traceability.Transaction.GridTransactionAvailable');
        var storeGridTransactionBatch = Ext.getCmp('Koltiva.view.Traceability.Transaction.WinBatchFrom-Form-gridTransaction').getStore();
        //store yg dipakai ======================= (end)

        thisObj.items = [{
            xtype: 'form',
            id: 'Koltiva.view.Traceability.Transaction.WinTransactionAvailableForm-Form',
            padding:'5 25 5 8',
            items:[{
                    layout: 'column',
                    border: false,
                    items: [{
                        columnWidth: 1,
                        layout:'form',
                        items:[{
                            xtype: 'grid',
                            id: 'Koltiva.view.Traceability.Transaction.WinTransactionAvailableForm-Form-gridTransaction',
                            style: 'border:1px solid #CCC;margin-top:4px;',
                            loadMask: true,
                            selType: 'rowmodel',
                            store: storeGridTransactionAvailable,
                            viewConfig: {
                                deferEmptyText: false,
                                emptyText: lang('No data Available')
                            },
                            dockedItems: [{
                                xtype: 'pagingtoolbar',
                                id: 'Koltiva.view.Traceability.Transaction.WinTransactionAvailableForm-Form-gridTransaction-Toolbar',
                                store: storeGridTransactionAvailable,
                                dock: 'bottom',
                                displayInfo: true
                            },{
                                xtype: 'toolbar',
                                dock:'top',
                                items: [{
                                    name: 'Keyword',
                                    id: 'Koltiva.view.Traceability.Transaction.WinTransactionAvailableForm-Form-gridTransaction-Keyword',
                                    xtype: 'textfield',
                                    width: 300,
                                    emptyText: lang('Search by Name / ID')
                                },{
                                    xtype: 'button',
                                    id: 'Koltiva.view.Traceability.Transaction.WinTransactionAvailableForm-Form-gridTransaction-BtnSearch',
                                    icon: varjs.config.base_url + 'images/icons/silk/search.png',
                                    margin: '0px 10px 0px 6px',
                                    text: lang('Search'),
                                    handler: function() {
                                        storeGridTransactionAvailable.load();
                                    }
                                }]
                            }],
                            selType: 'checkboxmodel',
                            selModel: {
                                checkOnly: true,
                                mode: "MULTI",
                                headerWidth: 50
                            },
                            columns: [{
                                text: 'ID',
                                dataIndex: 'SupplyTransID',
                                hidden: true
                            },{
                                text: lang('SupplyType'),
                                dataIndex: 'SupplyType',
                                flex: 1,
                            },{
                                text: lang('Date'),
                                dataIndex: 'DateTransaction',
                                renderer: Ext.util.Format.dateRenderer('d-m-Y'),
                                flex: 2,
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
        }];

        thisObj.buttons = [{
            id: 'Koltiva.view.Traceability.Transaction.WinTransactionAvailableForm-Form-btnSave',
            text: lang('Save'),
            margin: '5px',
            scale: 'large',
            ui: 's-button',
            cls: 's-blue',
            handler: function() {
                var trans = '';
                Ext.each(Ext.getCmp('Koltiva.view.Traceability.Transaction.WinTransactionAvailableForm-Form-gridTransaction').getSelectionModel().getSelection(), function(row, index, value) {
                    trans = row.data.SupplyTransID + '|';
                });
                if (trans != '') {
                    Ext.Ajax.request({
                        url: m_api + '/tc_transaction/transaction_to_batch',
                        method: 'POST',
                        waitMsg: lang('Sending data...'),
                        params: {
                            SupplyBatchID: Ext.getCmp('Koltiva.view.Traceability.Transaction.WinBatchFrom-Form-SupplyBatchID').getValue(),
                            trans: trans
                        },
                        success: function(response, opts) {
                            var flds = Ext.decode(response.responseText);
                            if(flds.success==true){
                                storeGridTransactionAvailable.load();
                                storeGridTransactionBatch.load();
                            }
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
                } else {
                    Ext.MessageBox.show({
                        title: 'Warning',
                        msg: lang('Please select transaction!'),
                        buttons: Ext.MessageBox.OK,
                        animateTarget: 'mb9',
                        icon: 'ext-mb-warning'
                    });
                }
            }
        },{
            text: lang('Close'),
            margin: '5px',
            scale: 'large',
            ui: 's-button',
            cls: 's-grey',
            handler: function() {
                //tutup popup
                thisObj.close();
            }
        }];

        this.callParent(arguments);
    },
    listeners: {
        afterRender: function(){
            var thisObj = this;
        }
    }
});
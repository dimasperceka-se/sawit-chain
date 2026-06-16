/*
 * @Author: mawwatudi
 * @Date:   2018-01-03 11:18:00
 * @Last Modified by:   
 * @Last Modified time:
*/

Ext.define('Koltiva.view.Traceability.Transaction.MainGrid' ,{
    extend: 'Ext.panel.Panel',
    id: 'Koltiva.view.Traceability.Transaction.MainGrid',
    style:'padding:0 15px 15px 15px;margin:12px 0 0 0;',
    viewVar: false,
    setViewVar: function(value){
        this.viewVar = value;
    },
    renderTo: 'ext-content',
    initComponent: function() {
        function SetFormTransaction(SupplyTransID, Tipe) {
            Ext.Ajax.request({
                waitMsg: 'Please Wait',
                url: m_api + '/tc_transaction/transaction_detail',
                method: 'get',
                params: {
                    SupplyTransID: SupplyTransID
                },
                success: function(response, opts) {
                    var r = Ext.decode(response.responseText);
                    Ext.getCmp('Koltiva.view.Traceability.Transaction.TransactionForm-Form-SupplyTransID').setValue(r.SupplyTransID);
                    Ext.getCmp('Koltiva.view.Traceability.Transaction.TransactionForm-Form-SupplyID').setValue(r.SupplyID);
                    Ext.getCmp('Koltiva.view.Traceability.Transaction.TransactionForm-Form-SupplyType').setValue(r.SupplyType);
                    Ext.getCmp('Koltiva.view.Traceability.Transaction.TransactionForm-Form-DisplaySupplyID').setValue(r.DisplaySupplyID);
                    Ext.getCmp('Koltiva.view.Traceability.Transaction.TransactionForm-Form-Village').setValue(r.Village);
                    Ext.getCmp('Koltiva.view.Traceability.Transaction.TransactionForm-Form-GroupName').setValue(r.GroupName);
                    Ext.getCmp('Koltiva.view.Traceability.Transaction.TransactionForm-Form-AgentName').setValue(r.AgentName);
                    Ext.getCmp('Koltiva.view.Traceability.Transaction.TransactionForm-Form-SupplyName').setValue(r.Name);
                    Ext.getCmp('Koltiva.view.Traceability.Transaction.TransactionForm-Form-FakturNumber').setValue(r.FakturNumber);
                    if(r.SupplyType=='Batch'){
                        Ext.getCmp('Koltiva.view.Traceability.Transaction.TransactionForm-Form-DateTransaction').hide();
                        Ext.getCmp('Koltiva.view.Traceability.Transaction.TransactionForm-Form-DeliveryDate').show();
                        Ext.getCmp('Koltiva.view.Traceability.Transaction.TransactionForm-Form-AgentName').show();
                        Ext.getCmp('Koltiva.view.Traceability.Transaction.TransactionForm-Form-SupplyName').setFieldLabel(lang('DO'));
                    }else{
                        Ext.getCmp('Koltiva.view.Traceability.Transaction.TransactionForm-Form-DateTransaction').show();
                        Ext.getCmp('Koltiva.view.Traceability.Transaction.TransactionForm-Form-DeliveryDate').hide();
                        Ext.getCmp('Koltiva.view.Traceability.Transaction.TransactionForm-Form-AgentName').hide();
                        Ext.getCmp('Koltiva.view.Traceability.Transaction.TransactionForm-Form-SupplyName').setFieldLabel(lang('Nama'));
                    }
                    if(r.DateTransaction!='' && r.DateTransaction!=null){
                        Ext.getCmp('Koltiva.view.Traceability.Transaction.TransactionForm-Form-DateTransaction').setValue(r.DateTransaction);
                    }else{
                        if(r.SupplyType=='Batch'){
                            var dt = new Date();
                            Ext.getCmp('Koltiva.view.Traceability.Transaction.TransactionForm-Form-DateTransaction').setValue(Ext.Date.format(dt, 'Y-m-d H:i:s'));
                        }
                    }
                    if(r.DateBruto1!='' && r.DateBruto1!=null){
                        var date1 = new Date(r.DateBruto1);
                        Ext.getCmp('Koltiva.view.Traceability.Transaction.TransactionForm-Form-1stDateWeight').setValue(Ext.Date.format(date1, 'Y-m-d'));
                        Ext.getCmp('Koltiva.view.Traceability.Transaction.TransactionForm-Form-1stTimeWeight').setValue(Ext.Date.format(date1, 'H:i'));
                    }else{
                        if(r.SupplyType=='Batch'){
                            var dt = new Date();
                            Ext.getCmp('Koltiva.view.Traceability.Transaction.TransactionForm-Form-1stDateWeight').setValue(Ext.Date.format(dt, 'Y-m-d'));
                            Ext.getCmp('Koltiva.view.Traceability.Transaction.TransactionForm-Form-1stTimeWeight').setValue(Ext.Date.format(dt, 'H:i'));
                        }else{
                            Ext.getCmp('Koltiva.view.Traceability.Transaction.TransactionForm-Form-1stDateWeight').setValue();
                            Ext.getCmp('Koltiva.view.Traceability.Transaction.TransactionForm-Form-1stTimeWeight').setValue();
                        }
                    }
                    if(r.DateBruto2!='' && r.DateBruto2!=null){
                        var date2 = new Date(r.DateBruto2);
                        Ext.getCmp('Koltiva.view.Traceability.Transaction.TransactionForm-Form-2ndDateWeight').setValue(Ext.Date.format(date2, 'Y-m-d'));
                        Ext.getCmp('Koltiva.view.Traceability.Transaction.TransactionForm-Form-2ndTimeWeight').setValue(Ext.Date.format(date2, 'H:i'));
                    }else{
                        Ext.getCmp('Koltiva.view.Traceability.Transaction.TransactionForm-Form-2ndDateWeight').setValue();
                        Ext.getCmp('Koltiva.view.Traceability.Transaction.TransactionForm-Form-2ndTimeWeight').setValue();
                    }
                    Ext.getCmp('Koltiva.view.Traceability.Transaction.TransactionForm-Form-1stWeight').setValue(r.VolumeBruto1);
                    Ext.getCmp('Koltiva.view.Traceability.Transaction.TransactionForm-Form-2ndWeight').setValue(r.VolumeBruto2);
                    Ext.getCmp('Koltiva.view.Traceability.Transaction.TransactionForm-Form-Tandan').setValue(r.NumberPackage);
                    //Ext.getCmp('Koltiva.view.Traceability.Transaction.TransactionForm-Form-Netto').setValue();
                    Ext.getCmp('Koltiva.view.Traceability.Transaction.TransactionForm-Form-AdjustWeight1').setValue(r.Pemotongan1);
                    Ext.getCmp('Koltiva.view.Traceability.Transaction.TransactionForm-Form-AdjustWeight2').setValue(r.Pemotongan2);
                    Ext.getCmp('Koltiva.view.Traceability.Transaction.TransactionForm-Form-AdjustNetto').setValue(r.VolumeNetto);
                    Ext.getCmp('Koltiva.view.Traceability.Transaction.TransactionForm-Form-Price').setValue(r.NetPrice);
                    Ext.getCmp('Koltiva.view.Traceability.Transaction.TransactionForm-Form-TotalPayment').setValue(r.TotalPayment);
                    Ext.getCmp('Koltiva.view.Traceability.Transaction.TransactionForm-Form-DestWeight').setValue(r.DestWeight);
                    Ext.getCmp('Koltiva.view.Traceability.Transaction.TransactionForm-Form-DestNumberPackage').setValue(r.DestNumberPackage);
                    /*for (i = 1; i < 9; i++) { 
                        if(i==8){
                            var i = "Total";
                        }
                        Ext.getCmp('Koltiva.view.Traceability.Transaction.TransactionForm-Form-Tandan'+i).setValue();
                        Ext.getCmp('Koltiva.view.Traceability.Transaction.TransactionForm-Form-Persen'+i).setValue();
                        Ext.getCmp('Koltiva.view.Traceability.Transaction.TransactionForm-Form-Denda'+i).setValue();
                        Ext.getCmp('Koltiva.view.Traceability.Transaction.TransactionForm-Form-Potongan'+i).setValue();
                    }*/
                    if(Tipe=='update'){
                        Ext.getCmp('Koltiva.view.Traceability.Transaction.TransactionForm-Form-Save').setText(lang('Save'));
                        Ext.getCmp('Koltiva.view.Traceability.Transaction.TransactionForm-Form-Save').show();
                    }else{
                        
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
        
        var thisObj = this;

        //store yg dipakai (begin)
        var storeGridMainTransaction = Ext.create('Koltiva.store.Traceability.Transaction.MainGridTransaction');
        var storeGridMainBatch = Ext.create('Koltiva.store.Traceability.Transaction.MainGridBatch');
        var cmbSupplyType = Ext.create('Koltiva.store.Traceability.Transaction.ComboSupplyType');
        var cmbSupplyStatus = Ext.create('Koltiva.store.Traceability.Transaction.ComboSupplyStatus');
        //store yg dipakai (end)

        var contextMenuTransactionGrid = Ext.create('Ext.menu.Menu',{
            items:[{
                icon: varjs.config.base_url + 'images/icons/new/view.png',
                text: lang('View'),
                itemId: 'Koltiva.view.Traceability.Transaction.MainGrid-contextMenuViewItem',
                handler: function() {
                    var sm = Ext.getCmp('Koltiva.view.Traceability.Transaction.MainGrid-gridTransaction').getSelectionModel().getSelection()[0];
                    SetFormTransaction(sm.get('SupplyTransID'), 'view');
                }
            },{
                icon: varjs.config.base_url + 'images/icons/new/update.png',
                text: lang('Update'),
                itemId: 'Koltiva.view.Traceability.Transaction.MainGrid-contextMenuUpdateItem',
                //hidden: m_act_update,
                handler: function() {
                    var sm = Ext.getCmp('Koltiva.view.Traceability.Transaction.MainGrid-gridTransaction').getSelectionModel().getSelection()[0];
                    SetFormTransaction(sm.get('SupplyTransID'), 'update');
                    Ext.getCmp('Koltiva.view.Traceability.Transaction.TransactionForm').setInputFieldOn();
                }
            },{
                icon: varjs.config.base_url + 'images/icons/new/update.png',
                text: lang('Farmer Data'),
                itemId: 'Koltiva.view.Traceability.Transaction.MainGrid-contextMenuFarmerData',
                

               handler: function() {
                var sm = Ext.getCmp('Koltiva.view.Traceability.Transaction.MainGrid-gridTransaction').getSelectionModel().getSelection()[0];
                var DataFarmer = Ext.create('Koltiva.view.Traceability.Transaction.DataFarmer',{
                   
                });

                if (!DataFarmer.isVisible()) {
                    DataFarmer.center();
                    DataFarmer.show();
                    Ext.getCmp('view.FarmerGrid-gridMainGrid').getStore().load();
                } else {
                    DataFarmer.close();
                }
                
            }

				
            },{
                icon: varjs.config.base_url + 'images/icons/new/delete.png',
                text: lang('Delete'),
                itemId: 'Koltiva.view.Traceability.Transaction.MainGrid-contextMenuDeleteItem',
                //hidden: m_act_delete,
                handler: function(){
                    var sm = Ext.getCmp('Koltiva.view.Traceability.Transaction.MainGrid-gridTransaction').getSelectionModel().getSelection()[0];

                    Ext.MessageBox.confirm('Message', 'Do you want to delete this data ?', function(btn) {
                        if (btn == 'yes') {
                            Ext.Ajax.request({
                                waitMsg: 'Please Wait',
                                url: m_api + '/tc_transaction/transaction',
                                method: 'DELETE',
                                params: {
                                    SupplyTransID: sm.get('SupplyTransID')
                                },
                                success: function(response, opts) {
                                    Ext.MessageBox.show({
                                        title: 'Information',
                                        msg: lang('Data deleted'),
                                        buttons: Ext.MessageBox.OK,
                                        animateTarget: 'mb9',
                                        icon: 'ext-mb-success'
                                    });
                                    storeGridMainTransaction.load()
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
                    });

                }
            }]
        });
        var objPanelTransactionForm = Ext.create('Koltiva.view.Traceability.Transaction.TransactionForm');
        thisObj.objPanelTransactionForm = objPanelTransactionForm;

        // Penerimaan
        var Grid_ListPenerimaan = Ext.create('Koltiva.view.Traceability_new.Transaction.List_penerimaan');
        //items
        thisObj.items = [{
            layout: 'column',
            border: false,
            items: [{
                columnWidth: 0.5,
                layout: 'form',
                items:[{
                    xtype: 'tabpanel',
                    flex: 1,
                    padding: 5,
                    activeTab: 0,
                    plain: true,
                    id: 'sectionTab',
                    listeners: {
                      'tabchange': function(tabPanel, tab) {

                      }
                    },
                    items: [{
                        xtype: 'panel',
                        viewVar: false,
                        setViewVar: function (value) {
                            this.viewVar = value;
                        },
                        frame: true,
                        collapsible: true,
                        margin: '0 0 0 0',
                        padding: 5,
                        title: lang('List Transaksi'),
                        items: [{
                            xtype: 'grid',
                            id: 'Koltiva.view.Traceability.Transaction.MainGrid-gridTransaction',
                            style: 'border:1px solid #CCC;margin-top:4px;',
                            loadMask: true,
                            selType: 'rowmodel',
                            store: storeGridMainTransaction,
                            viewConfig: {
                                deferEmptyText: false,
                                emptyText: lang('No data Available')
                            },
                            dockedItems: [{
                                xtype: 'pagingtoolbar',
                                id: 'Koltiva.view.Traceability.Transaction.MainGrid-gridToolbar',
                                store: storeGridMainTransaction,
                                dock: 'bottom',
                                displayInfo: true
                            },{
                                xtype: 'toolbar',
                                dock:'top',
                                items: [{
                                    xtype: 'combo',
                                    store: cmbSupplyType,
                                    id: 'Koltiva.view.Traceability.Transaction.MainGrid-gridToolbar-SupplyType',
                                    name: 'SupplyType',
                                    queryMode: 'local',
                                    displayField: 'label',
                                    valueField: 'id',
                                    width: 100,
                                    emptyText: lang('Type Transaction')
                                }, {
                                    xtype: 'combo',
                                    store: cmbSupplyStatus,
                                    id: 'Koltiva.view.Traceability.Transaction.MainGrid-gridToolbar-SupplyStatus',
                                    name: 'SupplyStatus',
                                    queryMode: 'local',
                                    displayField: 'label',
                                    width: 100,
                                    valueField: 'id',
                                    emptyText: lang('Status Transaction')
                                }, {
                                    name: 'SupplyKey',
                                    id: 'Koltiva.view.Traceability.Transaction.MainGrid-gridToolbar-SupplyKey',
                                    xtype: 'textfield',
                                    width: 150,
                                    emptyText: lang('Search by Name / ID')
                                },{
                                    xtype: 'button',
                                    id: 'Koltiva.view.Traceability.Transaction.MainGrid-gridToolbar-BtnSearch',
                                    icon: varjs.config.base_url + 'images/icons/silk/search.png',
                                    margin: '0px 10px 0px 6px',
                                    text: lang('Search'),
                                    handler: function() {
                                        storeGridMainTransaction.load()
                                    }
                                }, {
                                    xtype: 'container',
                                    flex: 1
                                }, {
                                    icon: varjs.config.base_url + 'images/icons/new/add.png', cls:'Sfr_BtnGridGreen', overCls:'Sfr_BtnGridGreen-Hover',
                                    text: lang('Send Transaction (Batch)'),
                                    id: 'Koltiva.view.Traceability.Transaction.MainGrid-gridToolbar-BtnSendBatch',
                                    handler: function() {
                                        var WinBatchForm = Ext.create('Koltiva.view.Traceability.Transaction.WinBatchForm',{
                                            viewVar: {
                                                opsiDisplay: 'insert'
                                            }
                                        });
                                        if (!WinBatchForm.isVisible()) {
                                            WinBatchForm.center();
                                            WinBatchForm.show();
                                        } else {
                                            WinBatchForm.close();
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
                                        contextMenuTransactionGrid.showAt(e.getXY());
                                        var sm = record;
                                        if(sm.data.SupplyStatus == "Sent"){
                                            contextMenuTransactionGrid.getComponent('Koltiva.view.Traceability.Transaction.MainGrid-contextMenuUpdateItem').setVisible(false);
											contextMenuTransactionGrid.getComponent('Koltiva.view.Traceability.Transaction.MainGrid-contextMenuFarmerData').setVisible(false);
										    contextMenuTransactionGrid.getComponent('Koltiva.view.Traceability.Transaction.MainGrid-contextMenuViewItem').setVisible(true);
                                            contextMenuTransactionGrid.getComponent('Koltiva.view.Traceability.Transaction.MainGrid-contextMenuDeleteItem').setVisible(false);
                                        }else{
                                            contextMenuTransactionGrid.getComponent('Koltiva.view.Traceability.Transaction.MainGrid-contextMenuUpdateItem').setVisible(true);
											contextMenuTransactionGrid.getComponent('Koltiva.view.Traceability.Transaction.MainGrid-contextMenuFarmerData').setVisible(true);										  
                                            contextMenuTransactionGrid.getComponent('Koltiva.view.Traceability.Transaction.MainGrid-contextMenuViewItem').setVisible(false);
                                            contextMenuTransactionGrid.getComponent('Koltiva.view.Traceability.Transaction.MainGrid-contextMenuDeleteItem').setVisible(true);
                                        }
                                    }
                                }]
                            },{
                                text: 'ID',
                                dataIndex: 'SupplyTransID',
                                hidden: true
                            },{
                                text: lang('SupplyType'),
                                dataIndex: 'SupplyType',
                                width:100,
                            },{
                                text: lang('Date'),
                                dataIndex: 'DateTransaction',
                                renderer: Ext.util.Format.dateRenderer('d-m-Y'),
                                width:100,
                            },{
                                text: lang('Agent'),
                                dataIndex: 'AgentName',
                                width:150
                            },{
                                text: lang('DO'),
                                dataIndex: 'DOName',
                                width:200
                            },{
                                text: lang('Bruto'),
                                dataIndex: 'VolumeBruto',
                                width:100
                            },{
                                text: lang('Netto'),
                                dataIndex: 'VolumeNetto',
                                width:100
                            },{
                                text: lang('Jumlah Petani'),
                                dataIndex: 'Farmers',
                                width:100
                            },{
                                text: lang('Status'),
                                dataIndex: 'SupplyStatus',
                                width:100
                            }]
                        }]
                    }, {
                        xtype: 'panel',
                        viewVar: false,
                        setViewVar: function (value) {
                            this.viewVar = value;
                        },
                        frame: true,
                        //hidden: true,
                        collapsible: true,
                        margin: '0 0 0 0',
                        padding: 5,
                        title: lang('List Batch'),
                        id: 'Koltiva.view.Traceability.Transaction.MainGrid-panelBatch',
                        items: [{
                            xtype: 'grid',
                            id: 'Koltiva.view.Traceability.Transaction.MainGrid-gridBatch',
                            style: 'border:1px solid #CCC;margin-top:4px;',
                            loadMask: true,
                            selType: 'rowmodel',
                            store: storeGridMainBatch,
                            viewConfig: {
                                deferEmptyText: false,
                                emptyText: lang('No data Available')
                            },
                            dockedItems: [{
                                xtype: 'pagingtoolbar',
                                id: 'Koltiva.view.Traceability.Transaction.MainGrid-gridBatch-Toolbar',
                                store: storeGridMainBatch,
                                dock: 'bottom',
                                displayInfo: true
                            }],
                            columns: [{
                                text: lang('Action'),
                                xtype:'actioncolumn',
                                width:'4%',
                                items:[{
                                    icon: varjs.config.base_url + 'images/icons/new/action.png',
                                    handler: function(grid, rowIndex, colIndex, item, e, record) {
                                        
                                    }
                                }]
                            },{
                                text: 'ID',
                                dataIndex: 'SupplyBatchID',
                                hidden: true
                            },{
                                text: lang('Delivery Date'),
                                dataIndex: 'DeliveryDate',
                                flex: 1
                            },{
                                text: lang('Destination'),
                                dataIndex: 'Destination',
                                flex: 2
                            },{
                                text: lang('Netto'),
                                dataIndex: 'VolumeNetto',
                                flex: 1
                            },{
                                text: lang('Destination Weight'),
                                dataIndex: 'DestWeight',
                                flex: 1
                            },{
                                text: lang('Tandan'),
                                dataIndex: 'DestNumberPackage',
                                flex: 1
                            },{
                                text: lang('Status'),
                                dataIndex: 'BatchStatus',
                                flex: 1
                            }]
                        }]
                    },{
                        xtype: 'panel',
                        viewVar: false,
                        setViewVar: function (value) {
                            this.viewVar = value;
                        },
                        frame: true, 
                        collapsible: false,
                        margin: '0 0 0 0',
                        padding: 5,
                        //hidden : true,
                        title: lang('List Penerimaan'),
                        id: 'Koltiva.view.Traceability.Transaction.MainGrid-panelPenerimaan',
                        items: [Grid_ListPenerimaan]
                    }]
                }]
            }, 
            {
                columnWidth: 0.5,
                layout: 'form',
                items:[thisObj.objPanelTransactionForm]
            }]    
        }];

        this.callParent(arguments);
    }, 
    listeners: {
        afterlayout: function (c, v) {
            Ext.Ajax.request({
                waitMsg: 'Please Wait',
                url: m_api + '/tc_transaction/set',
                method: 'get',
                success: function(response, opts) {
                    var r = Ext.decode(response.responseText);
                    if(r.OrgType=='mill'){
                        Ext.getCmp('Koltiva.view.Traceability.Transaction.MainGrid-gridToolbar-BtnSendBatch').hide();
                        Ext.getCmp('Koltiva.view.Traceability.Transaction.TransactionForm-Form-2ndWeighing').show();
                        Ext.getCmp('Koltiva.view.Traceability.Transaction.TransactionForm-Form-2ndWeight').show();
                        //Ext.getCmp('Koltiva.view.Traceability.Transaction.MainGrid-panelBatch').hide();
                    }else if(r.OrgType=='agent'){
                        Ext.getCmp('Koltiva.view.Traceability.Transaction.MainGrid-gridToolbar-BtnSendBatch').show();
                        Ext.getCmp('Koltiva.view.Traceability.Transaction.TransactionForm-Form-2ndWeighing').hide();
                        Ext.getCmp('Koltiva.view.Traceability.Transaction.TransactionForm-Form-2ndWeight').hide();
                        //Ext.getCmp('Koltiva.view.Traceability.Transaction.MainGrid-panelBatch').hide();
                    }
                }
            });
        }
    },
});
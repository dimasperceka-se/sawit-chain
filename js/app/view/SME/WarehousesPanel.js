 
/*
    Param2 yg diperlukan ketika load View ini
    1. MemberID
*/

// Define Variabel2 / Object2 / Fungsi yg diperlukan oleh view ini (begin)

// Define Variabel2 / Object2 / Fungsi yg diperlukan oleh view ini (end)

Ext.define('Koltiva.view.SME.WarehousesPanel' ,{
    extend: 'Ext.panel.Panel',
    id: 'Koltiva.view.SME.WarehousesPanel',
    title: lang('Business Shop and Warehouse Location'),
    viewVar: false,
    setViewVar: function(value){
        this.viewVar = value;
    },
    loadStoreGrid: function(){
        var thisObj = this;
        //load store
        thisObj.storeGridTraderWarehouses.setStoreVar({MemberID:thisObj.viewVar.MemberID});
        thisObj.storeGridTraderWarehouses.load();
    },
    frame: true,
    collapsible:true,
    margin:'0 0 20 10',
    initComponent: function() {
        var thisObj = this;

        //store
        var storeGridTraderWarehouses = Ext.create('Koltiva.store.SME.GridTraderWarehouses');
        thisObj.storeGridTraderWarehouses = storeGridTraderWarehouses;

        var contextMenuGridTraderWarehouses = Ext.create('Ext.menu.Menu',{
            items:[{
                icon: varjs.config.base_url + 'images/icons/new/view.png',
                text: lang('View'),
                handler: function() {
                    var sm = Ext.getCmp('Koltiva.view.SME.WarehousesPanel-gridTraderWarehouses').getSelectionModel().getSelection()[0];

                    var WinFormWarehouses = Ext.create('Koltiva.view.SME.WinFormWarehouses',{
                        viewVar: {
                            opsiDisplay:'view',
                            callerObjID:thisObj.viewVar.MemberID, 
                            callerStore: storeGridTraderWarehouses,
                            WarehousesNr: sm.get('WarehousesNr') 
                        }
                    });
                    if (!WinFormWarehouses.isVisible()) {
                        WinFormWarehouses.center();
                        WinFormWarehouses.show();
                    } else {
                        WinFormWarehouses.close();
                    }
                }
            },{
                icon: varjs.config.base_url + 'images/icons/new/update.png',
                text: lang('Update'),
                handler: function() {
                    var sm = Ext.getCmp('Koltiva.view.SME.WarehousesPanel-gridTraderWarehouses').getSelectionModel().getSelection()[0];
 
                    var WinFormWarehouses = Ext.create('Koltiva.view.SME.WinFormWarehouses',{
                        viewVar: {
                            opsiDisplay:'update',
                            callerObjID:thisObj.viewVar.MemberID, 
                            callerStore: storeGridTraderWarehouses,
                            WarehousesNr: sm.get('WarehousesNr'), 
                        }
                    });
                    if (!WinFormWarehouses.isVisible()) {
                        WinFormWarehouses.center();
                        WinFormWarehouses.show();
                    } else {
                        WinFormWarehouses.close();
                    }
                }
            },{
                icon: varjs.config.base_url + 'images/icons/new/delete.png',
                text: lang('Delete'),
                hidden: m_act_delete,
                handler: function(){
                    var sm = Ext.getCmp('Koltiva.view.SME.WarehousesPanel-gridTraderWarehouses').getSelectionModel().getSelection()[0];

                    Ext.MessageBox.confirm('Message', 'Do you want to delete this data ?', function(btn) {
                        if (btn == 'yes') {
                            Ext.Ajax.request({
                                waitMsg: 'Please Wait',
                                url: m_api + '/sme/warehouses',
                                method: 'DELETE',
                                params: {
                                    MemberID :thisObj.viewVar.MemberID, 
									WarehousesNr: sm.get('WarehousesNr')
                                },
                                success: function(response, opts) {
                                    Ext.MessageBox.show({
                                        title: 'Information',
                                        msg: lang('Data deleted'),
                                        buttons: Ext.MessageBox.OK,
                                        animateTarget: 'mb9',
                                        icon: 'ext-mb-success'
                                    });

                                    //refresh store
                                    thisObj.loadStoreGrid();
                                },
                                failure: function(response, opts) {
                                    var pesanNya;
                                    if(o.result.message != undefined){
                                        pesanNya = o.result.message;
                                    }else{
                                        pesanNya = lang('Connection error');
                                    }
                                    Ext.MessageBox.show({
                                        title: 'Error',
                                        msg: pesanNya,
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

        thisObj.dockedItems = [{
            xtype: 'toolbar',
            baseCls: 'bgToolbarTitlePanel',
            dock: 'top',
            items:[{
                xtype: 'tbtext',
                style:'font-weight:bold;text-decoration:underline;',
                text: lang('List of Warehouses')
            },{
                xtype:'tbspacer',
                flex:1
            },{
                icon: varjs.config.base_url + 'images/icons/new/add.png', cls:'Sfr_BtnGridGreen', overCls:'Sfr_BtnGridGreen-Hover',
                text: lang('Add'),
                hidden: m_act_add,
                handler: function() {
                    var WinFormWarehouses = Ext.create('Koltiva.view.SME.WinFormWarehouses',{
                        viewVar: {
                            opsiDisplay:'insert',
                            callerObjID:thisObj.viewVar.MemberID, 
                            callerStore: storeGridTraderWarehouses
                        }
                    });
                    if (!WinFormWarehouses.isVisible()) {
                        WinFormWarehouses.center();
                        WinFormWarehouses.show();
                    } else {
                        WinFormWarehouses.close();
                    }
                }
            }]
        }];

        thisObj.items = [{
            xtype: 'grid',
            id: 'Koltiva.view.SME.WarehousesPanel-gridTraderWarehouses',
            loadMask: true,
            minHeight:125,
            selType: 'rowmodel',
            store: storeGridTraderWarehouses,
            viewConfig: {
                deferEmptyText: false,
                emptyText: GetDefaultContentNoData()
            },
            columns: [{
                text: lang('Action'),
                xtype:'actioncolumn',
                flex: 0.3,
                items:[{
                    icon: varjs.config.base_url + 'images/icons/new/action.png',
                    tooltip: 'Action',
                    handler: function(grid, rowIndex, colIndex, item, e, record) {
                        contextMenuGridTraderWarehouses.showAt(e.getXY());
                    }
                }]
            },{
                text: lang('Warehouses Nr'),
                dataIndex: 'WarehousesNr',
                flex: 1,
            },{
                text: lang('Warehouse Type'),
                dataIndex: 'Warehousetype',
                flex: 1,
				renderer:function(v)
				{ 
					if(v ==  1) { return 'Buying Station'; }
					if(v ==  2) { return 'Agry-Inputs Kiosk'; }
					if(v ==  3) { return 'Seedling Nursery'; }
					if(v ==  4) { return 'Food Shop'; }
					if(v ==  5) { return 'Plantation'; }
				}
            },{
                text: lang('Latitude'),
                dataIndex: 'Latitude',
                flex: 1, 
            },{
                text: lang('Longitude'),
                dataIndex: 'Longitude',
                flex: 1, 
            }]
        }];

        this.callParent(arguments);
    }
});
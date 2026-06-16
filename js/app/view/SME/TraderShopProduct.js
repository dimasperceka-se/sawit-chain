 

/*
    Param2 yg diperlukan ketika load View ini
    1. MemberID
*/

// Define Variabel2 / Object2 / Fungsi yg diperlukan oleh view ini (begin)

// Define Variabel2 / Object2 / Fungsi yg diperlukan oleh view ini (end)

Ext.define('Koltiva.view.Trader.TraderShopProduct' ,{
    extend: 'Ext.panel.Panel',
    id: 'Koltiva.view.Trader.TraderShopProduct',
    title: lang('Shop Products'),
    viewVar: false,
    setViewVar: function(value){
        this.viewVar = value;
    },
    loadStoreGrid: function(){
        var thisObj = this;

        //load store
        thisObj.storeGridTraderShopProduct.setStoreVar({MemberID:thisObj.viewVar.MemberID});
        thisObj.storeGridTraderShopProduct.load();
    },
    frame: true,
    collapsible:true,
    margin:'0 0 20 8',
    initComponent: function() {
        var thisObj = this;

        //store
        var storeGridTraderShopProduct = Ext.create('Koltiva.store.Trader.GridTraderShopProduct');
        thisObj.storeGridTraderShopProduct = storeGridTraderShopProduct;

        //context menu
        var contextMenuGridTraderShopProduct = Ext.create('Ext.menu.Menu',{
            items:[{
                icon: varjs.config.base_url + 'images/icons/new/view.png',
                text: lang('View'),
                handler: function() {
                    var sm = Ext.getCmp('Koltiva.view.Trader.TraderShopProduct-GridTraderShopProduct').getSelectionModel().getSelection()[0];

                    var WinFormShopProduct = Ext.create('Koltiva.view.Trader.WinFormShopProduct');
                    WinFormShopProduct.setViewVar({
                        MemberID:thisObj.viewVar.MemberID,
                        opsiDisplay:'view',
                        VehID: sm.get('VehID')
                    });

                    if (!WinFormShopProduct.isVisible()) {
                        WinFormShopProduct.center();
                        WinFormShopProduct.show();
                    } else {
                        WinFormShopProduct.close();
                    }
                }
            },{
                icon: varjs.config.base_url + 'images/icons/new/update.png',
                text: lang('Update'),
                hidden: m_act_update,
                handler: function() {
                    var sm = Ext.getCmp('Koltiva.view.Trader.TraderShopProduct-GridTraderShopProduct').getSelectionModel().getSelection()[0];

                    var WinFormShopProduct = Ext.create('Koltiva.view.Trader.WinFormShopProduct');
                    WinFormShopProduct.setViewVar({
                        MemberID:thisObj.viewVar.MemberID,
                        opsiDisplay:'update',
                        VehID: sm.get('VehID')
                    });

                    if (!WinFormShopProduct.isVisible()) {
                        WinFormShopProduct.center();
                        WinFormShopProduct.show();
                    } else {
                        WinFormShopProduct.close();
                    }
                }
            },{
                icon: varjs.config.base_url + 'images/icons/new/delete.png',
                text: lang('Delete'),
                hidden: m_act_delete,
                handler: function(){
                    var sm = Ext.getCmp('Koltiva.view.Trader.TraderShopProduct-GridTraderShopProduct').getSelectionModel().getSelection()[0];

                    Ext.MessageBox.confirm('Message', 'Do you want to delete this data ?', function(btn) {
                        if (btn == 'yes') {
                            Ext.Ajax.request({
                                waitMsg: 'Please Wait',
                                url: m_api + '/trader_mem/trader_vehicle',
                                method: 'DELETE',
                                params: {
                                    VehID: sm.get('VehID')
                                },
                                success: function(response, opts) {
                                    Ext.MessageBox.show({
                                        title: 'Information',
                                        msg: lang('Data deleted'),
                                        buttons: Ext.MessageBox.OK,
                                        animateTarget: 'mb9',
                                        icon: 'ext-mb-success'
                                    });

                                    //refresh store FamLab
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
                text: lang('List of Shop Products')
            },{
                xtype:'tbspacer',
                flex:1
            },{
                icon: varjs.config.base_url + 'images/icons/new/add.png', cls:'Sfr_BtnGridGreen', overCls:'Sfr_BtnGridGreen-Hover',
                text: lang('Add'),
                hidden: m_act_add,
                handler: function() {
                    var WinFormShopProduct = Ext.create('Koltiva.view.Trader.WinFormShopProduct');
                    WinFormShopProduct.setViewVar({MemberID:thisObj.viewVar.MemberID,opsiDisplay:'insert'});

                    if (!WinFormShopProduct.isVisible()) {
                        WinFormShopProduct.center();
                        WinFormShopProduct.show();
                    } else {
                        WinFormShopProduct.close();
                    }
                }
            }]
        }];

        thisObj.items = [{
            xtype: 'grid',
            id: 'Koltiva.view.Trader.TraderShopProduct-GridTraderShopProduct',
            loadMask: true,
            minHeight:125,
            selType: 'rowmodel',
            store: storeGridTraderShopProduct,
            viewConfig: {
                deferEmptyText: false,
                emptyText: lang('No data Available')
            },
            columns: [{
                text: lang('Action'),
                xtype:'actioncolumn',
                width: '10%',
                items:[{
                    icon: varjs.config.base_url + 'images/icons/new/action.png',
                    tooltip: 'Action',
                    handler: function(grid, rowIndex, colIndex, item, e, record) {
                        contextMenuGridTraderShopProduct.showAt(e.getXY());
                    }
                }]
            },{ 
                text: lang('VehID'),
                dataIndex: 'VehID',
                hidden:true
            },{
                text: 'No',
                xtype: 'rownumberer',
                width: '7%'
            },{
                text: lang('Image'),
                //dataIndex: 'BrandName',
                width: '20%'
            },{
                text: lang('Product'),
                //dataIndex: 'VehName',
                width: '30%'
            },{
                text: lang('Code'),
                //dataIndex: 'VehPoliceNr',
                hidden: true
            },{
                text: lang('Status'),
                //dataIndex: 'VehCapacity',
                width: '31%'
            },{
                text: lang('Company'),
                //dataIndex: 'Driver',
                hidden: true
            }]
        }];

        this.callParent(arguments);
    }
});
/*
* @Author: nikolius
* @Date:   2017-09-07 14:11:26
* @Last Modified by:   nikolius
* @Last Modified time: 2018-03-27 14:39:57
*/

/*
    Param2 yg diperlukan ketika load View ini
    1. MemberID
*/

// Define Variabel2 / Object2 / Fungsi yg diperlukan oleh view ini (begin)

// Define Variabel2 / Object2 / Fungsi yg diperlukan oleh view ini (end)

Ext.define('Koltiva.view.SME.TraderVehiclePanel' ,{
    extend: 'Ext.panel.Panel',
    id: 'Koltiva.view.SME.TraderVehiclePanel',
    title: lang('Vehicle'),
    viewVar: false,
    setViewVar: function(value){
        this.viewVar = value;
    },
    loadStoreGrid: function(){
        var thisObj = this;

        //load store
        thisObj.storeGridTraderVehicle.setStoreVar({MemberID:thisObj.viewVar.MemberID});
        thisObj.storeGridTraderVehicle.load();
    },
    frame: true,
    collapsible:true,
    margin:'0 0 20 8',
    initComponent: function() {
        var thisObj = this;

        //store
        var storeGridTraderVehicle = Ext.create('Koltiva.store.SME.GridTraderVehicle');
        thisObj.storeGridTraderVehicle = storeGridTraderVehicle;

        //context menu
        var contextMenuGridTraderVehicle = Ext.create('Ext.menu.Menu',{
            items:[{
                icon: varjs.config.base_url + 'images/icons/new/view.png',
                text: lang('View'),
                handler: function() {
                    var sm = Ext.getCmp('Koltiva.view.SME.TraderVehiclePanel-gridTraderVehicle').getSelectionModel().getSelection()[0];

                    var WinFormVehicle = Ext.create('Koltiva.view.SME.WinFormVehicle');
                    WinFormVehicle.setViewVar({
                        MemberID:thisObj.viewVar.MemberID,
                        opsiDisplay:'view',
                        VehID: sm.get('VehID')
                    });

                    if (!WinFormVehicle.isVisible()) {
                        WinFormVehicle.center();
                        WinFormVehicle.show();
                    } else {
                        WinFormVehicle.close();
                    }
                }
            },{
                icon: varjs.config.base_url + 'images/icons/new/update.png',
                text: lang('Update'),
                hidden: m_act_update,
                handler: function() {
                    var sm = Ext.getCmp('Koltiva.view.SME.TraderVehiclePanel-gridTraderVehicle').getSelectionModel().getSelection()[0];

                    var WinFormVehicle = Ext.create('Koltiva.view.SME.WinFormVehicle');
                    WinFormVehicle.setViewVar({
                        MemberID:thisObj.viewVar.MemberID,
                        opsiDisplay:'update',
                        VehID: sm.get('VehID')
                    });

                    if (!WinFormVehicle.isVisible()) {
                        WinFormVehicle.center();
                        WinFormVehicle.show();
                    } else {
                        WinFormVehicle.close();
                    }
                }
            },{
                icon: varjs.config.base_url + 'images/icons/new/delete.png',
                text: lang('Delete'),
                hidden: m_act_delete,
                handler: function(){
                    var sm = Ext.getCmp('Koltiva.view.SME.TraderVehiclePanel-gridTraderVehicle').getSelectionModel().getSelection()[0];

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
                text: lang('List of Vehicle Owned by SME')
            },{
                xtype:'tbspacer',
                flex:1
            },{
                icon: varjs.config.base_url + 'images/icons/new/add.png', cls:'Sfr_BtnGridGreen', overCls:'Sfr_BtnGridGreen-Hover',
                text: lang('Add'),
                hidden: m_act_add,
                handler: function() {
                    var WinFormVehicle = Ext.create('Koltiva.view.SME.WinFormVehicle');
                    WinFormVehicle.setViewVar({MemberID:thisObj.viewVar.MemberID,opsiDisplay:'insert'});

                    if (!WinFormVehicle.isVisible()) {
                        WinFormVehicle.center();
                        WinFormVehicle.show();
                    } else {
                        WinFormVehicle.close();
                    }
                }
            }]
        }];

        thisObj.items = [{
            xtype: 'grid',
            id: 'Koltiva.view.SME.TraderVehiclePanel-gridTraderVehicle',
            loadMask: true,
            minHeight:125,
            selType: 'rowmodel',
            store: storeGridTraderVehicle,
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
                        contextMenuGridTraderVehicle.showAt(e.getXY());
                    }
                }]
            },{fields: ['VehID','BrandName','VehName','VehPoliceNr','VehCapacity','Driver'],
                text: lang('VehID'),
                dataIndex: 'VehID',
                hidden:true
            },{
                text: 'No',
                xtype: 'rownumberer',
                flex: 0.3,
            },{
                text: lang('Brand'),
                dataIndex: 'BrandName',
                flex: 1,
            },{
                text: lang('Type'),
                dataIndex: 'VehName',
                flex: 1,
            },{
                text: lang('Police Number'),
                dataIndex: 'VehPoliceNr',
                hidden: true
            },{
                text: lang('Capacity')+' (kg)',
                dataIndex: 'VehCapacity',
                flex: 1,
            },{
                text: lang('Driver'),
                dataIndex: 'Driver',
                hidden: true
            }]
        }];

        this.callParent(arguments);
    }
});
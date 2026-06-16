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

Ext.define('Koltiva.view.SME.TraderCollectingPointPanel' ,{
    extend: 'Ext.panel.Panel',
    id: 'Koltiva.view.SME.TraderCollectingPointPanel',
    title: lang('Collecting Point'),
    viewVar: false,
    setViewVar: function(value){
        this.viewVar = value;
    },
    loadStoreGrid: function(){
        var thisObj = this;

        //load store
        thisObj.storeGridTraderCollectingPoint.setStoreVar({MemberID:thisObj.viewVar.MemberID});
        thisObj.storeGridTraderCollectingPoint.load();
    },
    frame: true,
    collapsible:true,
    margin:'0 0 20 8',
    initComponent: function() {
        var thisObj = this;

        //store
        var storeGridTraderCollectingPoint = Ext.create('Koltiva.store.SME.GridTraderCollectingPoint');
        thisObj.storeGridTraderCollectingPoint = storeGridTraderCollectingPoint;

        //context menu
        var contextMenuGridTraderCollectingPoint = Ext.create('Ext.menu.Menu',{
            items:[{
                icon: varjs.config.base_url + 'images/icons/new/view.png',
                text: lang('View'),
                handler: function() {
                    var sm = Ext.getCmp('Koltiva.view.SME.TraderCollectingPointPanel-gridTraderCollectingPoint').getSelectionModel().getSelection()[0];

                    var WinFormCollectingPoint = Ext.create('Koltiva.view.SME.WinFormCollectingPoint');
                    WinFormCollectingPoint.setViewVar({
                        MemberID:thisObj.viewVar.MemberID,
                        opsiDisplay:'view',
                        CollectpointID: sm.get('CollectpointID')
                    });

                    if (!WinFormCollectingPoint.isVisible()) {
                        WinFormCollectingPoint.center();
                        WinFormCollectingPoint.show();
                    } else {
                        WinFormCollectingPoint.close();
                    }
                }
            },{
                icon: varjs.config.base_url + 'images/icons/new/update.png',
                text: lang('Update'),
                hidden: m_act_update,
                handler: function() {
                    var sm = Ext.getCmp('Koltiva.view.SME.TraderCollectingPointPanel-gridTraderCollectingPoint').getSelectionModel().getSelection()[0];

                    var WinFormCollectingPoint = Ext.create('Koltiva.view.SME.WinFormCollectingPoint');
                    WinFormCollectingPoint.setViewVar({
                        MemberID:thisObj.viewVar.MemberID,
                        opsiDisplay:'update',
                        CollectpointID: sm.get('CollectpointID')
                    });

                    if (!WinFormCollectingPoint.isVisible()) {
                        WinFormCollectingPoint.center();
                        WinFormCollectingPoint.show();
                    } else {
                        WinFormCollectingPoint.close();
                    }
                }
            },{
                icon: varjs.config.base_url + 'images/icons/new/delete.png',
                text: lang('Delete'),
                hidden: m_act_delete,
                handler: function(){
                    var sm = Ext.getCmp('Koltiva.view.SME.TraderCollectingPointPanel-gridTraderCollectingPoint').getSelectionModel().getSelection()[0];

                    Ext.MessageBox.confirm('Message', 'Do you want to delete this data ?', function(btn) {
                        if (btn == 'yes') {
                            Ext.Ajax.request({
                                waitMsg: 'Please Wait',
                                url: m_api + '/tph/tph_form',
                                method: 'DELETE',
                                params: {
                                    CollectpointID: sm.get('CollectpointID')
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
                text: lang('List of Collecting Point')
            },{
                xtype:'tbspacer',
                flex:1
            },{
                icon: varjs.config.base_url + 'images/icons/new/add.png', cls:'Sfr_BtnGridGreen', overCls:'Sfr_BtnGridGreen-Hover',
                text: lang('Add'),
                hidden: m_act_add,
                handler: function() {
                    var WinFormCollectingPoint = Ext.create('Koltiva.view.SME.WinFormCollectingPoint');
                    WinFormCollectingPoint.setViewVar({MemberID:thisObj.viewVar.MemberID,opsiDisplay:'insert'});

                    if (!WinFormCollectingPoint.isVisible()) {
                        WinFormCollectingPoint.center();
                        WinFormCollectingPoint.show();
                    } else {
                        WinFormCollectingPoint.close();
                    }
                }
            }]
        }];

        thisObj.items = [{
            xtype: 'grid',
            id: 'Koltiva.view.SME.TraderCollectingPointPanel-gridTraderCollectingPoint',
            loadMask: true,
            scroll: false,
            minHeight:125,
            selType: 'rowmodel',
            store: storeGridTraderCollectingPoint,
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
                        contextMenuGridTraderCollectingPoint.showAt(e.getXY());
                    }
                }]
            },{fields: ['CollectpointID','CollectpointDisplayID','Name','OrgIDLabel','SubDistrict','Village','Latitude','Longitude','LastUpdated'],
                text: lang('CollectpointID'),
                dataIndex: 'CollectpointID',
                hidden:true
            },{
                text: 'No',
                xtype: 'rownumberer',
                flex: 0.3,
            },{
                text: lang('ID'),
                dataIndex: 'CollectpointDisplayID',
                flex: 1,
            },{
                text: lang('Name'),
                dataIndex: 'Name',
                flex: 1,
            },{
                text: lang('Village'),
                dataIndex: 'Village',
                flex: 1,
            },{
                text: lang('LastUpdated'),
                dataIndex: 'LastUpdated',
                flex: 1,
            }]
        }];

        this.callParent(arguments);
    }
});
/*
* @Author: nikolius
* @Date:   2017-09-07 13:18:55
* @Last Modified by:   nikolius
* @Last Modified time: 2018-01-05 14:36:11
*/

/*
    Param2 yg diperlukan ketika load View ini
    1. MemberID
*/

// Define Variabel2 / Object2 / Fungsi yg diperlukan oleh view ini (begin)

// Define Variabel2 / Object2 / Fungsi yg diperlukan oleh view ini (end)

Ext.define('Koltiva.view.Trader.TraderStaffPanel' ,{
    extend: 'Ext.panel.Panel',
    id: 'Koltiva.view.Trader.TraderStaffPanel',
    title: lang('SME Staff'),
    viewVar: false,
    setViewVar: function(value){
        this.viewVar = value;
    },
    loadStoreGrid: function(){
        var thisObj = this;
        //load store
        thisObj.storeGridTraderStaff.setStoreVar({MemberID:thisObj.viewVar.MemberID});
        thisObj.storeGridTraderStaff.load();
    },
    frame: true,
    collapsible:true,
    margin:'0 0 20 8',
    initComponent: function() {
        var thisObj = this;

        //store
        var storeGridTraderStaff = Ext.create('Koltiva.store.Trader.GridTraderStaff');
        thisObj.storeGridTraderStaff = storeGridTraderStaff;

        var contextMenuGridTraderStaff = Ext.create('Ext.menu.Menu',{
            items:[{
                icon: varjs.config.base_url + 'images/icons/new/view.png',
                text: lang('View'),
                handler: function() {
                    var sm = Ext.getCmp('Koltiva.view.Trader.TraderStaffPanel-gridTraderStaff').getSelectionModel().getSelection()[0];

                    var WinFormStaff = Ext.create('Koltiva.view.Staff.WinFormStaffGeneral',{
                        viewVar: {
                            opsiDisplay:'view',
                            callerObjID:thisObj.viewVar.MemberID,
                            callFromRole: 'agent',
                            callerStore: storeGridTraderStaff,
                            StaffID: sm.get('StaffID'),
                            PersonID: sm.get('PersonID')
                        }
                    });
                    if (!WinFormStaff.isVisible()) {
                        WinFormStaff.center();
                        WinFormStaff.show();
                    } else {
                        WinFormStaff.close();
                    }
                }
            },{
                icon: varjs.config.base_url + 'images/icons/new/update.png',
                text: lang('Update'),
                handler: function() {
                    var sm = Ext.getCmp('Koltiva.view.Trader.TraderStaffPanel-gridTraderStaff').getSelectionModel().getSelection()[0];

                    var WinFormStaff = Ext.create('Koltiva.view.Staff.WinFormStaffGeneral',{
                        viewVar: {
                            opsiDisplay:'update',
                            callerObjID:thisObj.viewVar.MemberID,
                            callFromRole: 'agent',
                            callerStore: storeGridTraderStaff,
                            StaffID: sm.get('StaffID'),
                            PersonID: sm.get('PersonID')
                        }
                    });
                    if (!WinFormStaff.isVisible()) {
                        WinFormStaff.center();
                        WinFormStaff.show();
                    } else {
                        WinFormStaff.close();
                    }
                }
            },{
                icon: varjs.config.base_url + 'images/icons/new/delete.png',
                text: lang('Delete'),
                hidden: m_act_delete,
                handler: function(){
                    var sm = Ext.getCmp('Koltiva.view.Trader.TraderStaffPanel-gridTraderStaff').getSelectionModel().getSelection()[0];

                    Ext.MessageBox.confirm('Message', 'Do you want to delete this data ?', function(btn) {
                        if (btn == 'yes') {
                            Ext.Ajax.request({
                                waitMsg: 'Please Wait',
                                url: m_api + '/basic_staff/staff_general',
                                method: 'DELETE',
                                params: {
                                    StaffID: sm.get('StaffID')
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
                text: lang('List of Staff Work for this SME')
            },{
                xtype:'tbspacer',
                flex:1
            },{
                icon: varjs.config.base_url + 'images/icons/new/add.png', cls:'Sfr_BtnGridGreen', overCls:'Sfr_BtnGridGreen-Hover',
                text: lang('Add'),
                hidden: m_act_add,
                handler: function() {
                    var WinFormStaff = Ext.create('Koltiva.view.Staff.WinFormStaffGeneral',{
                        viewVar: {
                            opsiDisplay:'insert',
                            callerObjID:thisObj.viewVar.MemberID,
                            callFromRole: 'agent',
                            callerStore: storeGridTraderStaff
                        }
                    });
                    if (!WinFormStaff.isVisible()) {
                        WinFormStaff.center();
                        WinFormStaff.show();
                    } else {
                        WinFormStaff.close();
                    }
                }
            }]
        }];

        thisObj.items = [{
            xtype: 'grid',
            id: 'Koltiva.view.Trader.TraderStaffPanel-gridTraderStaff',
            loadMask: true,
            selType: 'rowmodel',
            minHeight:125,
            store: storeGridTraderStaff,
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
                        contextMenuGridTraderStaff.showAt(e.getXY());
                    }
                }]
            },{
                text: lang('Name'),
                dataIndex: 'Name',
                width: '40%'
            },{
                text: lang('StaffID'),
                dataIndex: 'StaffID',
                hidden:true
            },{
                text: lang('PersonID'),
                dataIndex: 'PersonID',
                hidden:true
            },{
                text: lang('UserID'),
                dataIndex: 'UserID',
                hidden:true
            },{
                text: lang('Position'),
                dataIndex: 'Position',
                width: '35%'
            },{
                text: lang('Age'),
                dataIndex: 'Age',
                width: '12%'
            }]
        }];

        this.callParent(arguments);
    }
});
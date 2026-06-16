/*
* @Author: nikolius
* @Date:   2017-08-22 11:02:50
* @Last Modified by:   nikolius
* @Last Modified time: 2018-01-05 15:03:03
*/

/*
    Param2 yg diperlukan ketika load View ini
    1. MillID
*/

// Define Variabel2 / Object2 / Fungsi yg diperlukan oleh view ini (begin)

// Define Variabel2 / Object2 / Fungsi yg diperlukan oleh view ini (end)

Ext.define('Koltiva.view.Mill.MillStaffPanel' ,{
    extend: 'Ext.panel.Panel',
    id: 'Koltiva.view.Mill.MillStaffPanel',
    title: lang('Mill Staff'),
    viewVar: false,
    setViewVar: function(value){
        this.viewVar = value;
    },
    loadStoreGrid: function(){
        var thisObj = this;
        //load store
        thisObj.storeGridMillStaff.setStoreVar({MillID:thisObj.viewVar.MillID});
        thisObj.storeGridMillStaff.load();
    },
    frame: true,
    collapsible:true,
    margin:'0 0 20 8',
    initComponent: function() {
        var thisObj = this;

        //store
        var storeGridMillStaff = Ext.create('Koltiva.store.Mill.GridMillStaff');
        thisObj.storeGridMillStaff = storeGridMillStaff;

        var contextMenuGridMillStaff = Ext.create('Ext.menu.Menu',{
            items:[{
                icon: varjs.config.base_url + 'images/icons/new/view.png',
                text: lang('View'),
                handler: function() {
                    var sm = Ext.getCmp('Koltiva.view.Mill.MillStaffPanel-gridMillStaff').getSelectionModel().getSelection()[0];

                    var WinFormStaff = Ext.create('Koltiva.view.Staff.WinFormStaffGeneral',{
                        viewVar: {
                            opsiDisplay:'view',
                            callerObjID:thisObj.viewVar.MillID,
                            callFromRole: 'mill',
                            callerStore: storeGridMillStaff,
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
                    var sm = Ext.getCmp('Koltiva.view.Mill.MillStaffPanel-gridMillStaff').getSelectionModel().getSelection()[0];

                    var WinFormStaff = Ext.create('Koltiva.view.Staff.WinFormStaffGeneral',{
                        viewVar: {
                            opsiDisplay:'update',
                            callerObjID:thisObj.viewVar.MillID,
                            callFromRole: 'mill',
                            callerStore: storeGridMillStaff,
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
                // hidden: m_act_delete,
                handler: function(){
                    var sm = Ext.getCmp('Koltiva.view.Mill.MillStaffPanel-gridMillStaff').getSelectionModel().getSelection()[0];

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
                style:'font-weight:bold;text-decoration:underline;margin-top:20px',
                text: lang('List of Staff Work for this Mill')
            },{
                xtype:'tbspacer',
                flex:1
            },{
                icon: varjs.config.base_url + 'images/icons/new/add.png', cls:'Sfr_BtnGridGreen', overCls:'Sfr_BtnGridGreen-Hover',
                text: lang('Add'),
                // hidden: m_act_add,
                handler: function() {
                    var WinFormStaff = Ext.create('Koltiva.view.Staff.WinFormStaffGeneral',{
                        viewVar: {
                            opsiDisplay:'insert',
                            callerObjID:thisObj.viewVar.MillID,
                            callFromRole: 'mill',
                            callerStore: storeGridMillStaff
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
            id: 'Koltiva.view.Mill.MillStaffPanel-gridMillStaff',
            loadMask: true,
            selType: 'rowmodel',
            store: storeGridMillStaff,
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
                        contextMenuGridMillStaff.showAt(e.getXY());
                    }
                }]
            },{
                text: lang('Name'),
                dataIndex: 'Name',
                flex: 1,
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
                flex: 1,
            },{
                text: lang('Age'),
                dataIndex: 'Age',
                flex: 1,
            }]
        }];

        this.callParent(arguments);
    }
});
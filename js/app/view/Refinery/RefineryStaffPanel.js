Ext.define('Koltiva.view.Refinery.RefineryStaffPanel' ,{
    extend: 'Ext.panel.Panel',
    id: 'Koltiva.view.Refinery.RefineryStaffPanel',
    title: lang('Refinery Staff'),
    viewVar: false,
    setViewVar: function(value){
        this.viewVar = value;
    },
    loadStoreGrid: function(){
        var thisObj = this;
        //load store
        thisObj.storeGridRefineryStaff.setStoreVar({RefineryID:thisObj.viewVar.RefineryID});
        thisObj.storeGridRefineryStaff.load();
    },
    frame: true,
    collapsible:true,
    margin:'0 0 20 8',
    initComponent: function() {
        var thisObj = this;

        //store
        var storeGridRefineryStaff = Ext.create('Koltiva.store.Refinery.GridRefineryStaff');
        thisObj.storeGridRefineryStaff = storeGridRefineryStaff;

        var contextMenuGridRefineryStaff = Ext.create('Ext.menu.Menu',{
            items:[{
                icon: varjs.config.base_url + 'images/icons/new/view.png',
                text: lang('View'),
                handler: function() {
                    var sm = Ext.getCmp('Koltiva.view.Refinery.RefineryStaffPanel-gridRefineryRefinery').getSelectionModel().getSelection()[0];

                    var WinFormStaff = Ext.create('Koltiva.view.Staff.WinFormStaffGeneral',{
                        viewVar: {
                            opsiDisplay:'view',
                            callerObjID:thisObj.viewVar.RefineryID,
                            callFromRole: 'refinery',
                            callerStore: storeGridRefineryStaff,
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
                    var sm = Ext.getCmp('Koltiva.view.Refinery.RefineryStaffPanel-gridRefineryRefinery').getSelectionModel().getSelection()[0];

                    var WinFormStaff = Ext.create('Koltiva.view.Staff.WinFormStaffGeneral',{
                        viewVar: {
                            opsiDisplay:'update',
                            callerObjID:thisObj.viewVar.RefineryID,
                            callFromRole: 'refinery',
                            callerStore: storeGridRefineryStaff,
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
                    var sm = Ext.getCmp('Koltiva.view.Refinery.RefineryStaffPanel-gridRefineryRefinery').getSelectionModel().getSelection()[0];

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
                text: lang('List of Staff Work for this Refinery')
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
                            callerObjID:thisObj.viewVar.RefineryID,
                            callFromRole: 'refinery',
                            callerStore: storeGridRefineryStaff
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
            id: 'Koltiva.view.Refinery.RefineryStaffPanel-gridRefineryRefinery',
            loadMask: true,
            selType: 'rowmodel',
            store: storeGridRefineryStaff,
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
                        contextMenuGridRefineryStaff.showAt(e.getXY());
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
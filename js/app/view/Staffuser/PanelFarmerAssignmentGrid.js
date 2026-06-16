/******************************************
 *  Author : fikrifauzul@gmail.com   
 *  Created On : 05-02-2020
 *  File : PanelEmployessMainGrid.js
 *******************************************/

/*
 Param2 yg diperlukan ketika load View ini
 - SMEID
 */

Ext.define('Koltiva.view.Staffuser.PanelFarmerAssignmentGrid', {
    extend: 'Ext.panel.Panel',
    id: 'Koltiva.view.Staffuser.PanelFarmerAssignmentGrid',
    style:'margin-top:15px;',
    title: lang('Farmer Assignment for FX Neo'),
    frame: true,
    collapsible: true,
    viewVar: false,
    setViewVar: function (value) {
        this.viewVar = value;
    },
    listeners: {
        afterRender: function () {
            var thisObj = this;
        }
    },
    initComponent: function () {
        var thisObj = this;
        thisObj.MainGrid = Ext.create('Koltiva.store.Staffuser.FarmerAssignGrid', {
            storeVar: {
                StaffID: thisObj.viewVar.StaffID
            }
        });

        thisObj.ContextMenu = Ext.create('Ext.menu.Menu', {
            cls: 'Sfr_ConMenu',
            items: [{
                icon: varjs.config.base_url + 'images/icons/new/view.png',
                text: lang('View'),
                cls: 'Sfr_BtnConMenuWhite',
                itemId: 'Koltiva.view.Staffuser.PanelFarmerAssignmentGrid.ContextMenuView',
                handler: function () {
                    var sm = Ext.getCmp('Koltiva.view.Staffuser.PanelFarmerAssignmentGrid-MainGrid').getSelectionModel().getSelection()[0];

                    var WinFormFarmerAssignment = Ext.create('Koltiva.view.Staffuser.WinFormFarmerAssignment');
                    WinFormFarmerAssignment.setViewVar({
                        Title: lang('Add Farmer Assignment'),
                        OpsiDisplay: 'view',
                        CallerStore: thisObj.MainGrid,
                        
                        StaffID: thisObj.viewVar.StaffID,
                        StaffAssignmentID: sm.get('StaffAssignmentID')
                    });
                    if (!WinFormFarmerAssignment.isVisible()) {
                        WinFormFarmerAssignment.center();
                        WinFormFarmerAssignment.show();
                    } else {
                        WinFormFarmerAssignment.close();
                    }
                }
            }, {
                icon: varjs.config.base_url + 'images/icons/new/update.png',
                text: lang('Update'),
                cls: 'Sfr_BtnConMenuWhite',
                hidden: m_act_update,
                itemId: 'Koltiva.view.Staffuser.PanelFarmerAssignmentGrid.ContextMenuUpdate',
                handler: function () {
                    var sm = Ext.getCmp('Koltiva.view.Staffuser.PanelFarmerAssignmentGrid-MainGrid').getSelectionModel().getSelection()[0];

                    var WinFormFarmerAssignment = Ext.create('Koltiva.view.Staffuser.WinFormFarmerAssignment');
                    WinFormFarmerAssignment.setViewVar({
                        Title: lang('Add Farmer Assignment'),
                        OpsiDisplay: 'update',
                        CallerStore: thisObj.MainGrid,
                        
                        StaffID: thisObj.viewVar.StaffID,
                        StaffAssignmentID: sm.get('StaffAssignmentID')
                    });
                    if (!WinFormFarmerAssignment.isVisible()) {
                        WinFormFarmerAssignment.center();
                        WinFormFarmerAssignment.show();
                    } else {
                        WinFormFarmerAssignment.close();
                    }
                }
            }]
        });

        thisObj.items = [{
            xtype: 'grid',
            id: 'Koltiva.view.Staffuser.PanelFarmerAssignmentGrid-MainGrid',
            cls: 'Sfr_GridNew',
            loadMask: true,
            selType: 'rowmodel',
            style: 'border:1px solid #CCC;',
            store: thisObj.MainGrid,
            viewConfig: {
                deferEmptyText: false,
                emptyText: GetDefaultContentNoData()
            },
            dockedItems: [{
                xtype: 'pagingtoolbar',
                store: thisObj.MainGrid,
                dock: 'bottom',
                displayInfo: true,
                displayMsg: lang('Showing')+' {0} '+lang('to')+' {1} '+lang('of')+' {2} '+lang('data')
            },{
                xtype: 'toolbar',
                dock: 'top',
                items: [{
                    xtype: 'button',
                    icon: varjs.config.base_url + 'images/icons/new/add.png',
                    text: lang('Add'),
                    hidden: m_act_add,
                    cls: 'Sfr_BtnGridGreen',
                    overCls: 'Sfr_BtnGridGreen-Hover',
                    id: 'Koltiva.view.Staffuser.PanelFarmerAssignmentGrid.BtnAdd',
                    handler: function () {
                        var WinFormFarmerAssignment = Ext.create('Koltiva.view.Staffuser.WinFormFarmerAssignment');
                        WinFormFarmerAssignment.setViewVar({
                            Title: lang('Add Farmer Assignment'),
                            OpsiDisplay: 'insert',
                            CallerStore: thisObj.MainGrid,
                            
                            StaffID: thisObj.viewVar.StaffID,
                        });
                        if (!WinFormFarmerAssignment.isVisible()) {
                            WinFormFarmerAssignment.center();
                            WinFormFarmerAssignment.show();
                        } else {
                            WinFormFarmerAssignment.close();
                        }
                    }
                }]
            }],
            columns: [{
                text: ' ',
                xtype: 'actioncolumn',
                width: '5%',
                items: [{
                    icon: varjs.config.base_url + 'images/icons/new/action.png',
                    handler: function (grid, rowIndex, colIndex, item, e, record) {
                        thisObj.ContextMenu.showAt(e.getXY());
                    }
                }]
            }, {
                text: lang('StaffAssignmentID'),
                dataIndex: 'StaffAssignmentID',
                hidden:true,
            }, {
                text: lang('ID'),
                dataIndex: 'StaffAssignmentExtID',
                flex:1
            }, {
                text: lang('Start Date'),
                dataIndex: 'StartDate',
                flex:1
            }, {
                text: lang('End Date'),
                dataIndex: 'EndDate',
                flex:1
            }, {
                text: lang('Nr of Farmer'),
                dataIndex: 'FarmerNr',
                flex:1
            }, {
                text: lang('Status'),
                dataIndex: 'StatusCode',
                flex:1
            }]
        }];

        this.callParent(arguments);
    }
});
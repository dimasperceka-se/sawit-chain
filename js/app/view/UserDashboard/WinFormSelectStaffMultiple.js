/******************************************
 *  Author : n1colius.lau@gmail.com
 *  Created On : Fri Mar 13 2020
 *  File : WinFormSelectStaffMultiple.js
 *******************************************/

/*
 Param2 yg diperlukan ketika load View ini
 - ParentObj
 - CallFrom
 - ExceptIDSource
 */

Ext.define('Koltiva.view.UserDashboard.WinFormSelectStaffMultiple', {
    extend: 'Ext.window.Window',
    id: 'Koltiva.view.UserDashboard.WinFormSelectStaffMultiple',
    title: lang('List of Staffs'),
    closable: true,
    modal: true,
    closeAction: 'destroy',
    width: '92%',
    height: 650,
    overflowY: 'auto',
    style: 'padding:2px;',
    viewVar: false,
    setViewVar: function (value) {
        this.viewVar = value;
    },
    initComponent: function () {
        var thisObj = this;

        //Store ========================= (Begin)
        thisObj.CmbGroup = Ext.create('Koltiva.store.Staff.RegisterStaff.ComboGroupUser');

        thisObj.StoreGridMain = Ext.create('Koltiva.store.UserDashboard.WinFormSelectStaffMultipleMainGrid', {
            storeVar: {
                DashID: thisObj.viewVar.DashID,
                TxtSearchLabel: null,
                CmbGroup: null
            }
        });
        //Store ========================= (End)

        thisObj.items = [{
                xtype: 'grid',
                id: 'Koltiva.view.UserDashboard.WinFormSelectStaffMultiple-MainGrid',
                style: 'border:1px solid #CCC;',
                cls: 'Sfr_GridNew',
                loadMask: true,
                selType: 'rowmodel',
                store: thisObj.StoreGridMain,
                enableColumnHide: false,
                height: 550,
                viewConfig: {
                    deferEmptyText: false,
                    emptyText: GetDefaultContentNoData()
                },
                dockedItems: [{
                        xtype: 'pagingtoolbar',
                        store: thisObj.StoreGridMain,
                        dock: 'bottom',
                        displayInfo: true,
                        style: 'padding-right:12px;'
                    }, {
                        xtype: 'toolbar',
                        items: [{
                                name: 'Koltiva.view.UserDashboard.WinFormSelectStaffMultiple-TxtSearchLabel',
                                id: 'Koltiva.view.UserDashboard.WinFormSelectStaffMultiple-TxtSearchLabel',
                                xtype: 'textfield',
                                baseCls: 'Sfr_TxtfieldSearchGrid',
                                width: 200,
                                emptyText: lang('Cari berdasar nama / id')
                            }, {
                                store: thisObj.CmbGroup,
                                editable: false,
                                xtype: 'combobox',
                                queryMode: 'local',
                                displayField: 'GroupName',
                                valueField: 'GroupId',
                                id: 'Koltiva.view.UserDashboard.WinFormSelectStaffMultiple-CmbGroup',
                                name: 'Koltiva.view.UserDashboard.WinFormSelectStaffMultiple-CmbGroup',
                                emptyText: lang('All Group'),
                                style: 'margin-top:5px;',
                                listeners: {
                                    change: function (cb, nv, ov) {
                                    }
                                }
                            }, {
                                xtype: 'button',
                                icon: varjs.config.base_url + 'images/icons/silk/search.png',
                                text: lang('Search'),
                                cls: 'Sfr_BtnGridBlue',
                                overCls: 'Sfr_BtnGridBlue-Hover',
                                handler: function () {
                                    thisObj.StoreGridMain.storeVar.TxtSearchLabel = Ext.getCmp('Koltiva.view.UserDashboard.WinFormSelectStaffMultiple-TxtSearchLabel').getValue();
                                    thisObj.StoreGridMain.storeVar.CmbGroup = Ext.getCmp('Koltiva.view.UserDashboard.WinFormSelectStaffMultiple-CmbGroup').getValue();
                                    thisObj.StoreGridMain.load();
                                }
                            }]
                    }],
                columns: [{
                        dataIndex: 'PersonID',
                        hidden: true
                    }, {
                        text: 'No',
                        width: '5%',
                        xtype: 'rownumberer'
                    }, {
                        xtype: 'checkcolumn',
                        text: '&nbsp;',
                        dataIndex: 'chdata',
                        width: '5%'
                    }, {
                        text: lang('UserID'),
                        dataIndex: 'UserID',
                        flex: 2
                    }, {
                        text: lang('User FullName'),
                        dataIndex: 'UserRealName',
                        flex: 3
                    }, {
                        text: lang('UserName'),
                        dataIndex: 'UserName',
                        flex: 2
                    }, {
                        text: lang('GroupName'),
                        dataIndex: 'GroupName',
                        flex: 2
                    }, {
                        text: lang('PositionName'),
                        dataIndex: 'PositionName',
                        flex: 2
                    }, {
                        text: lang('RoleName'),
                        dataIndex: 'RoleName',
                        flex: 2
                    }]
            }];

        //buttons -------------------------------------------------------------- (begin)
        thisObj.buttons = [{
                icon: varjs.config.base_url + 'images/icons/new/save.png',
                cls: 'Sfr_BtnFormBlue',
                overCls: 'Sfr_BtnFormBlue-Hover',
                text: lang('Save'),
                handler: function () {
                    var records = thisObj.StoreGridMain.queryBy(function (record) {
                        return record.get('chdata') === true;
                    });
                    var ids = [];
                    records.each(function (record) {
                        ids.push(record.get('UserID'));
                    });

                    if (ids.length > 0) {
                        thisObj.viewVar.ParentObj.AddParticipants(ids.join(','));
                    }
                }
            }, {
                icon: varjs.config.base_url + 'images/icons/new/close.png',
                text: lang('Close'),
                cls: 'Sfr_BtnFormGrey',
                overCls: 'Sfr_BtnFormGrey-Hover',
                handler: function () {
                    thisObj.close();
                }
            }];
        //buttons -------------------------------------------------------------- (end)

        this.callParent(arguments);
    }
});
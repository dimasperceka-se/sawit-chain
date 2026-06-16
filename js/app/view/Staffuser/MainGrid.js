/******************************************
 *  Author : n1colius.lau@gmail.com   
 *  Created On : Mon Jul 13 2020
 *  File : MainGrid.js
 *******************************************/
Ext.define('Koltiva.view.Staffuser.MainGrid', {
    extend: 'Ext.panel.Panel',
    id: 'Koltiva.view.Staffuser.MainGrid',
    renderTo: 'ext-content',
    style: 'padding:0 7px 7px 7px;margin:2px 0 0 0;',
    listeners: {
        afterRender: function (component, eOpts) {
            var thisObj = this;
            // document.getElementById('Sfr_Cont_IdBoxInfoDataGrid').style.display = 'block';
            document.getElementById('Sfr_IdBoxInfoDataGrid').style.display = 'block';
        }
    },
    initComponent: function () {
        var thisObj = this;

        thisObj.StoreGridMain = Ext.create('Koltiva.store.Staffuser.MainGrid', {
            storeVar: {
                KeySearch: '',
                CmbSearchRole: null
            }
        });
        thisObj.StoreComboStaffRole = Ext.create('Koltiva.store.ComboGeneral.ComboStaffRole');

        thisObj.ContextMenuGrid = Ext.create('Ext.menu.Menu', {
            cls: 'Sfr_ConMenu',
            items: [{
                icon: varjs.config.base_url + 'images/icons/new/view.png',
                text: lang('View'),
                cls: 'Sfr_BtnConMenuWhite',
                handler: function () {
                    var sm = Ext.getCmp('Koltiva.view.Staffuser.MainGrid-Grid').getSelectionModel().getSelection()[0];

                    Ext.getCmp('Koltiva.view.Staffuser.MainGrid').destroy(); //destory current view
                    var FormMainApp = [];
                    if (Ext.getCmp('Koltiva.view.Staffuser.MainForm') == undefined) {
                        FormMainFarmer = Ext.create('Koltiva.view.Staffuser.MainForm', {
                            viewVar: {
                                OpsiDisplay: 'view',
                                PersonID: sm.get('PersonID'),
                                StaffID: sm.get('StaffID')
                            }
                        });
                    } else {
                        //destroy, create ulang
                        Ext.getCmp('Koltiva.view.Staffuser.MainForm').destroy();
                        FormMainFarmer = Ext.create('Koltiva.view.Staffuser.MainForm', {
                            viewVar: {
                                OpsiDisplay: 'view',
                                PersonID: sm.get('PersonID'),
                                StaffID: sm.get('StaffID')
                            }
                        });
                    }
                }
            }, {
                icon: varjs.config.base_url + 'images/icons/new/update.png',
                text: lang('Update'),
                cls: 'Sfr_BtnConMenuWhite',
                hidden: m_act_update,
                handler: function () {
                    var sm = Ext.getCmp('Koltiva.view.Staffuser.MainGrid-Grid').getSelectionModel().getSelection()[0];

                    Ext.getCmp('Koltiva.view.Staffuser.MainGrid').destroy(); //destory current view
                    var FormMainApp = [];
                    if (Ext.getCmp('Koltiva.view.Staffuser.MainForm') == undefined) {
                        FormMainFarmer = Ext.create('Koltiva.view.Staffuser.MainForm', {
                            viewVar: {
                                OpsiDisplay: 'update',
                                PersonID: sm.get('PersonID'),
                                StaffID: sm.get('StaffID')
                            }
                        });
                    } else {
                        //destroy, create ulang
                        Ext.getCmp('Koltiva.view.Staffuser.MainForm').destroy();
                        FormMainFarmer = Ext.create('Koltiva.view.Staffuser.MainForm', {
                            viewVar: {
                                OpsiDisplay: 'update',
                                PersonID: sm.get('PersonID'),
                                StaffID: sm.get('StaffID')
                            }
                        });
                    }
                }
            }, {
                icon: varjs.config.base_url + 'images/icons/silk/page_portrait_shot.png',
                text: lang('Staff Position'),
                hidden: !m_act_staff_position,
                handler: function () {
                    var sm = Ext.getCmp('Koltiva.view.Staffuser.MainGrid-Grid').getSelectionModel().getSelection()[0];
                    var WinStaffPosition = Ext.create('Koltiva.view.Staffuser.WinStaffPosition', {
                        viewVar: {
                            StaffID: sm.get('StaffID'),
                            ObjType: sm.get('ObjType')
                        }
                    });
                    if (!WinStaffPosition.isVisible()) {
                        WinStaffPosition.center();
                        WinStaffPosition.show();
                    } else {
                        WinStaffPosition.close();
                    }
                }
            }, {
                icon: varjs.config.base_url + 'images/icons/new/delete.png',
                text: lang('Delete'),
                cls: 'Sfr_BtnConMenuWhite',
                hidden: m_act_delete,
                handler: function () {
                    var sm = Ext.getCmp('Koltiva.view.Staffuser.MainGrid-Grid').getSelectionModel().getSelection()[0];
                    Ext.MessageBox.confirm(lang('Message'), lang('Do you want to delete this staff ?'), function (btn) {
                        if (btn == 'yes') {
                            Ext.Ajax.request({
                                waitMsg: 'Please Wait',
                                url: m_api + '/staffuser/staff_data',
                                method: 'DELETE',
                                params: {
                                    PersonID: sm.get('PersonID')
                                },
                                success: function (rp, o) {
                                    var r = Ext.decode(rp.responseText);
                                    Ext.MessageBox.show({
                                        title: 'Information',
                                        msg: r.message,
                                        buttons: Ext.MessageBox.OK,
                                        animateTarget: 'mb9',
                                        icon: 'ext-mb-success'
                                    });

                                    //refresh store
                                    thisObj.StoreGridMain.load();
                                },
                                failure: function (rp, o) {
                                    try {
                                        var r = Ext.decode(rp.responseText);
                                        Ext.MessageBox.show({
                                            title: lang('Error'),
                                            msg: r.message,
                                            buttons: Ext.MessageBox.OK,
                                            animateTarget: 'mb9',
                                            icon: 'ext-mb-info'
                                        });
                                    } catch (err) {
                                        Ext.MessageBox.show({
                                            title: lang('Error'),
                                            msg: 'Connection Error',
                                            buttons: Ext.MessageBox.OK,
                                            animateTarget: 'mb9',
                                            icon: 'ext-mb-error'
                                        });
                                    }
                                }
                            });
                        }
                    });
                }
            }]
        });

        thisObj.items = [{
            xtype: 'grid',
            id: 'Koltiva.view.Staffuser.MainGrid-Grid',
            style: 'border:1px solid #CCC;margin-top:4px;',
            cls: 'Sfr_GridNew',
            loadMask: true,
            selType: 'rowmodel',
            store: thisObj.StoreGridMain,
            enableColumnHide: false,
            viewConfig: {
                deferEmptyText: false,
                emptyText: GetDefaultContentNoData()
            },
            dockedItems: [{
                xtype: 'pagingtoolbar',
                store: thisObj.StoreGridMain,
                dock: 'bottom',
                displayInfo: true,
                displayMsg: lang('Showing') + ' {0} ' + lang('to') + ' {1} ' + lang('of') + ' {2} ' + lang('data')
            }, {
                xtype: 'toolbar',
                dock: 'top',
                items: [{
                    xtype: 'button',
                    icon: varjs.config.base_url + 'images/icons/new/add.png',
                    text: lang('Add'),
                    hidden: m_act_add,
                    cls: 'Sfr_BtnGridGreen',
                    overCls: 'Sfr_BtnGridGreen-Hover',
                    handler: function () {
                        Ext.getCmp('Koltiva.view.Staffuser.MainGrid').destroy(); //destory current view

                        var FormMainApp = [];
                        //create object View untuk FormMainGrower
                        if (Ext.getCmp('Koltiva.view.Staffuser.MainForm') == undefined) {
                            FormMainFarmer = Ext.create('Koltiva.view.Staffuser.MainForm', {
                                viewVar: {
                                    OpsiDisplay: 'insert'
                                }
                            });
                        } else {
                            //destroy, create ulang
                            Ext.getCmp('Koltiva.view.Staffuser.MainForm').destroy();
                            FormMainFarmer = Ext.create('Koltiva.view.Staffuser.MainForm', {
                                viewVar: {
                                    OpsiDisplay: 'insert'
                                }
                            });
                        }
                    }
                }, {
                    xtype: 'tbspacer',
                    flex: 1
                }, {
                    id: 'Koltiva.view.Staffuser.MainGrid-CmbSearchRole',
                    name: 'Koltiva.view.Staffuser.MainGrid-CmbSearchRole',
                    xtype: 'combo',
                    width: 175,
                    store: thisObj.StoreComboStaffRole,
                    displayField: 'label',
                    valueField: 'id',
                    queryMode: 'local',
                    selectOnFocus: true,
                    baseCls: 'Sfr_CombofieldSearchGrid',
                    emptyText: lang('Staff Role'),
                    style: 'margin-right:15px;'
                }, {
                    name: 'Koltiva.view.Staffuser.MainGrid-TxtSearchNama',
                    id: 'Koltiva.view.Staffuser.MainGrid-TxtSearchNama',
                    xtype: 'textfield',
                    baseCls: 'Sfr_TxtfieldSearchGrid',
                    width: 400,
                    emptyText: lang('Cari berdasar nama'),
                    listeners: {
                        specialkey: thisObj.submitOnEnterGrid
                    }
                }, {
                    xtype: 'button',
                    icon: varjs.config.base_url + 'images/icons/new/search_white.png',
                    text: lang('Search'),
                    cls: 'Sfr_BtnGridBlue',
                    overCls: 'Sfr_BtnGridBlue-Hover',
                    handler: function () {
                        thisObj.StoreGridMain.storeVar.KeySearch = Ext.getCmp('Koltiva.view.Staffuser.MainGrid-TxtSearchNama').getValue();
                        thisObj.StoreGridMain.storeVar.CmbSearchRole = Ext.getCmp('Koltiva.view.Staffuser.MainGrid-CmbSearchRole').getValue();
                        thisObj.StoreGridMain.loadPage(1);
                    }
                }]
            }],
            columns: [{
                text: '',
                xtype: 'actioncolumn',
                width:'4%',
                items: [{
                    icon: varjs.config.base_url + 'images/icons/new/action.png',
                    handler: function (grid, rowIndex, colIndex, item, e, record) {
                        thisObj.ContextMenuGrid.showAt(e.getXY());
                    }
                }]
            }, {
                text: 'No',
                xtype: 'rownumberer',
                width:'4%'
            }, {
                text: lang('PersonID'),
                dataIndex: 'PersonID',
                hidden: true
            }, {
                text: lang('StaffID'),
                dataIndex: 'StaffID',
                hidden: true
            }, {
                text: lang('ObjType'),
                dataIndex: 'ObjType',
                hidden: true
            }, {
                text: lang('PersonNm'),
                dataIndex: 'PersonNm',
                width:'15%'
            }, {
                text: lang('UserName'),
                dataIndex: 'UserName',
                width:'12%'
            }, {
                text: lang('Gender'),
                dataIndex: 'Gender',
                width:'7%',
                renderer: function (value) {
                    var RetVal;

                    if (value != null && value != '') {
                        switch (value) {
                            case 'm':
                                RetVal = lang('Male');
                                break;
                            case 'f':
                                RetVal = lang('Female');
                                break;
                            default:
                                RetVal = '-';
                                break;
                        }
                    } else {
                        RetVal = '-';
                    }

                    return RetVal;
                }
            }, {
                text: lang('Role'),
                dataIndex: 'Role',
                width:'10%'
            }, {
                text: lang('Status'),
                dataIndex: 'Status',
                width:'8%'
            }, {
                text: lang('Email'),
                dataIndex: 'Email',
                width:'12%'
            }, {
                text: lang('AccountGroup'),
                dataIndex: 'AccountGroup',
                width:'12%'
            }, {
                text: lang('AccountActive'),
                dataIndex: 'AccountActive',
                width:'7%'
            }, {
                text: lang('ID Server'),
                dataIndex: 'AccountCognito',
                width:'8%'
            }]
        }];

        this.callParent(arguments);
    },
    submitOnEnterGrid: function (field, event) {
        if (event.getKey() == event.ENTER) {
            Ext.getCmp('Koltiva.view.Staffuser.MainGrid-Grid').getStore().storeVar.KeySearch = Ext.getCmp('Koltiva.view.Staffuser.MainGrid-TxtSearchNama').getValue();
            Ext.getCmp('Koltiva.view.Staffuser.MainGrid-Grid').getStore().storeVar.CmbSearchRole = Ext.getCmp('Koltiva.view.Staffuser.MainGrid-CmbSearchRole').getValue();
            Ext.getCmp('Koltiva.view.Staffuser.MainGrid-Grid').getStore().loadPage(1);
        }
    }
});
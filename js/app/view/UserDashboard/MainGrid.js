

Ext.define('Koltiva.view.UserDashboard.MainGrid', {
    extend: 'Ext.panel.Panel',
    id: 'Koltiva.view.UserDashboard.MainGrid',
    renderTo: 'ext-content',
    style: 'padding:0 7px 7px 7px;margin:5px 0 0 0;',
    submitOnEnterGrid: function (field, event) {
        if (event.getKey() == event.ENTER) {
            var resourcesStore = Ext.data.StoreManager.lookup('Koltiva.store.UserDashboard.MainGrid');
            resourcesStore.storeVar.KeySearch = Ext.getCmp('Koltiva.view.UserDashboard.MainGrid-textSearch').getValue();
            resourcesStore.loadPage(1);
        }
    },
    listeners: {
        afterRender: function (component, eOpts) {
            var thisObj = this;
            document.getElementById('divCommonContentRegion').style.display = 'none';

        }
    },
    initComponent: function () {
        var thisObj = this;

        //Define Store Main Grid
        thisObj.StoreGridMain = Ext.create('Koltiva.store.UserDashboard.MainGrid', {
            storeVar: {
                KeySearch: ''
            }
        });

        //Context Menu
        thisObj.ContextMenuGrid = Ext.create('Ext.menu.Menu', {
            cls: 'Sfr_ConMenu',
            items: [{
                    icon: varjs.config.base_url + 'images/icons/new/update.png',
                    text: lang('Update'),
                    itemId: 'UpdateDashboard',
                    cls: 'Sfr_BtnConMenuWhite',
                    handler: function () {
                        var sm = Ext.getCmp('Koltiva.view.UserDashboard.MainGrid-GridDashboard').getSelectionModel().getSelection()[0];
                        Ext.getCmp('Koltiva.view.UserDashboard.MainGrid').destroy(); //destory current view
                        var FormMain = [];
                        //create object View untuk FormMainGrower
                        if (Ext.getCmp('Koltiva.view.UserDashboard.MainForm') == undefined) {
                            FormMain = Ext.create('Koltiva.view.UserDashboard.MainForm', {
                                viewVar: {
                                    OpsiDisplay: 'update',
                                    DashID: sm.get('DashID')
                                }
                            });
                        } else {
                            //destroy, create ulang
                            Ext.getCmp('Koltiva.view.UserDashboard.MainForm').destroy();
                            FormMain = Ext.create('Koltiva.view.UserDashboard.MainForm', {
                                viewVar: {
                                    OpsiDisplay: 'update',
                                    DashID: sm.get('DashID')
                                }
                            });
                        }
                    }
                }, {
                    icon: varjs.config.base_url + 'images/icons/new/view.png',
                    text: lang('View Dashboard'),
                    itemId: 'ViewDashboard',
                    cls: 'Sfr_BtnConMenuWhite',
                    handler: function () {
                        var sm = Ext.getCmp('Koltiva.view.UserDashboard.MainGrid-GridDashboard').getSelectionModel().getSelection()[0];
                        Ext.getCmp('Koltiva.view.UserDashboard.MainGrid').destroy(); //destory current view
                        var FormMain = [];
                        //create object View untuk FormMainGrower
                        if (Ext.getCmp('Koltiva.view.UserDashboard.ViewDashboard') == undefined) {
                            FormMain = Ext.create('Koltiva.view.UserDashboard.ViewDashboard', {
                                viewVar: {
                                    DashID: sm.get('DashID')
                                }
                            });
                        } else {
                            //destroy, create ulang
                            Ext.getCmp('Koltiva.view.UserDashboard.ViewDashboard').destroy();
                            FormMain = Ext.create('Koltiva.view.UserDashboard.ViewDashboard', {
                                viewVar: {
                                    DashID: sm.get('DashID')
                                }
                            });
                        }
                    }
                }, {
                    icon: varjs.config.base_url + 'images/icons/new/delete.png',
                    text: lang('Delete'),
                    itemId: 'DeleteDashboard',
                    cls: 'Sfr_BtnConMenuWhite',
                    handler: function () {
                        var sm = Ext.getCmp('Koltiva.view.UserDashboard.MainGrid-GridDashboard').getSelectionModel().getSelection()[0];

                        Ext.MessageBox.confirm('Message', 'Do you want to delete this data ?', function (btn) {
                            if (btn == 'yes') {

                                Ext.Ajax.request({
                                    waitMsg: 'Please Wait',
                                    url: m_api + '/user_dashboard/data_input',
                                    method: 'DELETE',
                                    params: {
                                        DashID: sm.get('DashID')
                                    },
                                    success: function (response, opts) {
                                        Ext.MessageBox.show({
                                            title: 'Information',
                                            msg: lang('Data deleted'),
                                            buttons: Ext.MessageBox.OK,
                                            animateTarget: 'mb9',
                                            icon: 'ext-mb-success'
                                        });

                                        //refresh store
                                        Ext.getCmp('Koltiva.view.UserDashboard.MainGrid-GridDashboard').getStore().load();
                                    },
                                    failure: function (response, opts) {
                                        var pesanNya;
                                        if (opts.result.message != undefined) {
                                            pesanNya = opts.result.message;
                                        } else {
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

        thisObj.items = [{
                xtype: 'grid',
                id: 'Koltiva.view.UserDashboard.MainGrid-GridDashboard',
                style: 'border:1px solid #CCC;margin-top:4px;',
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
                        displayInfo: true
                    }, {
                        xtype: 'toolbar',
                        dock: 'top',
                        items: [{
                                icon: varjs.config.base_url + 'images/icons/new/add.png',
                                text: lang('Add'),
                                hidden: m_act_add,
                                cls: 'Sfr_BtnGridGreen',
                                overCls: 'Sfr_BtnGridGreen-Hover',
                                handler: function () {
                                    Ext.getCmp('Koltiva.view.UserDashboard.MainGrid').destroy(); //destory current view
                                    var FormMain = [];
                                    //create object View untuk FormMainGrower
                                    if (Ext.getCmp('Koltiva.view.UserDashboard.MainForm') == undefined) {
                                        FormMain = Ext.create('Koltiva.view.UserDashboard.MainForm', {
                                            viewVar: {
                                                OpsiDisplay: 'insert'
                                            }
                                        });
                                    } else {
                                        //destroy, create ulang
                                        Ext.getCmp('Koltiva.view.UserDashboard.MainForm').destroy();
                                        FormMain = Ext.create('Koltiva.view.UserDashboard.MainForm', {
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
                                name: 'key',
                                id: 'Koltiva.view.UserDashboard.MainGrid-textSearch',
                                xtype: 'textfield',
                                baseCls: 'Sfr_TxtfieldSearchGrid',
                                width: 400,
                                emptyText: lang('Cari berdasar nama/ID') + ', ' + lang('press_enter_search'),
                                listeners: {
                                    specialkey: thisObj.submitOnEnterGrid
                                }
                            }, {
                                icon: varjs.config.base_url + 'images/icons/new/search_white.png',
                                text: lang('Search'),
                                cls: 'Sfr_BtnGridBlue',
                                overCls: 'Sfr_BtnGridBlue-Hover',
                                handler: function () {
                                    thisObj.StoreGridMain.storeVar.KeySearch = Ext.getCmp('Koltiva.view.UserDashboard.MainGrid-textSearch').getValue();
                                    thisObj.StoreGridMain.loadPage(1);
                                }
                            }]
                    }],
                columns: [{
                        text: '',
                        xtype: 'actioncolumn',
                        width: '4%',
                        items: [{
                                icon: varjs.config.base_url + 'images/icons/new/action.png',
                                handler: function (grid, rowIndex, colIndex, item, e, record) {
                                    if (m_userid == record.data.CreatedBy) {
                                        if(m_act_update == false) thisObj.ContextMenuGrid.items.get('UpdateDashboard').setVisible(true);
                                        if(m_act_delete == false) thisObj.ContextMenuGrid.items.get('DeleteDashboard').setVisible(true);
                                    } else {
                                        // dibypass kalau admin
                                        if (m_is_admin != 1) {
                                            thisObj.ContextMenuGrid.items.get('UpdateDashboard').setVisible(false);
                                            thisObj.ContextMenuGrid.items.get('DeleteDashboard').setVisible(false);
                                        } else {
                                            if(m_act_update == false) thisObj.ContextMenuGrid.items.get('UpdateDashboard').setVisible(true);
                                            if(m_act_delete == false) thisObj.ContextMenuGrid.items.get('DeleteDashboard').setVisible(true);
                                        }
                                    }
                                    thisObj.ContextMenuGrid.showAt(e.getXY());
                                }
                            }]
                    }, {
                        text: lang('Dashboard Name'),
                        dataIndex: 'DashName',
                        flex: 3
                    }, {
                        text: lang('Description'),
                        dataIndex: 'Description',
                        flex: 5
                    }, {
                        text: lang('Board ID'),
                        dataIndex: 'BoardID',
                        width: '8%'
                    }, {
                        text: lang('Author'),
                        dataIndex: 'CreatedName',
                        flex: 2
                    }, {
                        text: lang('Date Updated'),
                        dataIndex: 'DateUpdated',
                        flex: 2
                    }]
            }];

        this.callParent(arguments);
    }
});
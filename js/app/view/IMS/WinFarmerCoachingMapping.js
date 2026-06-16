/*
 Param2 yg diperlukan ketika load View ini
 - IMSID
 */

Ext.define('Koltiva.view.IMS.WinFarmerCoachingMapping', {
    extend: 'Ext.window.Window',
    id: 'Koltiva.view.IMS.WinFarmerCoachingMapping',
    title: lang('IMS - Farmer Coaching Mapping'),
    closable: true,
    modal: true,
    closeAction: 'destroy',
    width: '94%',
    height: '90%',
    overflowY: 'auto',
    viewVar: false,
    setViewVar: function (value) {
        this.viewVar = value;
    },
    initComponent: function () {
        var thisObj = this;

        //store (Begin)
        thisObj.store_grid_coaching_mapping = Ext.create('Koltiva.store.IMS.GridCoachingMapping', {
            storeVar: {
                IMSID: thisObj.viewVar.IMSID
            }
        });
        //store (End)

        thisObj.contextMenuGridCoachingMapping = Ext.create('Ext.menu.Menu', {
            cls: 'Sfr_ConMenu',
            items: [{
                    icon: varjs.config.base_url + 'images/icons/new/delete.png',
                    text: lang('Delete'),
                    cls: 'Sfr_BtnConMenuWhite',
                    handler: function () {
                        var sm = Ext.getCmp('Koltiva.view.IMS.WinFarmerCoachingMapping-GridCoachingMapping').getSelectionModel().getSelection()[0];
                        Ext.MessageBox.confirm('Message', lang('Do you want to delete this data ?'), function (btn) {
                            if (btn == 'yes') {
                                Ext.Ajax.request({
                                    waitMsg: 'Please Wait',
                                    url: m_api + '/ims_coaching/farmer_coaching_mapping',
                                    method: 'DELETE',
                                    params: {
                                        IMSID: sm.get('IMSID'),
                                        UserName: sm.get('UserName'),
                                        FarmerID: sm.get('FarmerID')
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
                                        Ext.getCmp('Koltiva.view.IMS.WinFarmerCoachingMapping-GridCoachingMapping').getStore().load();
                                    },
                                    failure: function (response, o) {
                                        var pesanNya;
                                        if (o.result.message != undefined) {
                                            pesanNya = o.result.message;
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
                id: 'Koltiva.view.IMS.WinFarmerCoachingMapping-GridCoachingMapping',
                style: 'border:1px solid #CCC;padding-right:3px;',
                store: thisObj.store_grid_coaching_mapping,
                cls: 'Sfr_GridNew',
                width: '100%',
                loadMask: true,
                selType: 'rowmodel',
                viewConfig: {
                    deferEmptyText: false,
                    emptyText: lang('No data Available')
                },
                dockedItems: [{
                        xtype: 'pagingtoolbar',
                        store: thisObj.store_grid_coaching_mapping,
                        dock: 'bottom',
                        displayInfo: true
                    }, {
                        xtype: 'toolbar',
                        items: [{
                                icon: varjs.config.base_url + 'images/icons/new/add.png',
                                text: lang('Add'),
                                id: 'imsAddFarmerCoachingMapping',
                                handler: function () {
                                    console.log(thisObj.viewVar.IMSID);
                                    $prosesCek = cekSaveDulu(thisObj.viewVar.IMSID);
                                    if ($prosesCek == true) {
                                        var WinFormCoachingMapping = Ext.create('Koltiva.view.IMS.WinFormCoachingMapping', {
                                            viewVar: {
                                                IMSID: thisObj.viewVar.IMSID
                                            }
                                        });
                                        if (!WinFormCoachingMapping.isVisible()) {
                                            WinFormCoachingMapping.center();
                                            WinFormCoachingMapping.show();
                                        } else {
                                            WinFormCoachingMapping.close();
                                        }
                                    }
                                }
                            }, {
                                name: 'key',
                                id: 'Koltiva.view.IMS.WinFarmerCoachingMapping-TextSearch',
                                xtype: 'textfield',
                                baseCls: 'Sfr_TxtfieldSearchGrid',
                                width: 400,
                                emptyText: lang('Cari berdasar nama/ID') + ', ' + lang('press_enter_search'),
                                listeners: {
                                    specialkey: function (f, e) {
                                        if (e.getKey() == e.ENTER) {
                                            thisObj.store_grid_coaching_mapping.load({
                                                params: {
                                                    TextSearch: Ext.getCmp('Koltiva.view.IMS.WinFarmerCoachingMapping-TextSearch').getValue()
                                                }
                                            });
                                        }
                                    }
                                }
                            }, {
                                xtype: 'button',
                                text: lang('Search'),
                                cls: 'Sfr_BtnGridBlue',
                                overCls: 'Sfr_BtnGridBlue-Hover',
                                handler: function () {
                                    thisObj.store_grid_coaching_mapping.load({
                                        params: {
                                            TextSearch: Ext.getCmp('Koltiva.view.IMS.WinFarmerCoachingMapping-TextSearch').getValue()
                                        }
                                    });
                                }
                            }]
                    }],
                columns: [{
                        text: '',
                        xtype:'actioncolumn',
                        width:'4%',
                        items:[{
                            icon: varjs.config.base_url + 'images/icons/new/action.png',
                            handler: function(grid, rowIndex, colIndex, item, e, record) {
                                thisObj.contextMenuGridCoachingMapping.showAt(e.getXY());
                            }
                        }]
                    },{
                        dataIndex: 'IMSID',
                        hidden: true
                    }, {
                        text: lang('Username'),
                        width: '10%',
                        dataIndex: 'UserName'
                    }, {
                        text: lang('UserRealName'),
                        flex: 1,
                        dataIndex: 'UserRealName'
                    }, {
                        text: lang('Farmer ID'),
                        width: '10%',
                        dataIndex: 'FarmerID'
                    }, {
                        text: lang('Farmer Name'),
                        flex: 1,
                        dataIndex: 'FarmerName'
                    }, {
                        text: lang('Gender'),
                        flex: 1,
                        dataIndex: 'Gender'
                    }, {
                        text: lang('NC Major'),
                        flex: 1,
                        dataIndex: 'NC_Major'
                    }, {
                        text: lang('NC Minor'),
                        flex: 1,
                        dataIndex: 'NC_Minor'
                    }, {
                        text: lang('Farmer Group'),
                        width: '10%',
                        dataIndex: 'FarmerGroup'
                    }, {
                        text: lang('Village'),
                        width: '10%',
                        dataIndex: 'Village'
                    }, {
                        text: lang('SubDistrict'),
                        width: '10%',
                        dataIndex: 'SubDistrict'
                    }, {
                        text: lang('District'),
                        width: '10%',
                        dataIndex: 'District'
                    }],
                listeners: {
                    itemclick: function (view, record, item, index, e) {
                        thisObj.contextMenuGridCoachingMapping.showAt(e.getXY());
                    }
                }
            }];

        thisObj.buttons = [{
                margin: '5px',
                icon: varjs.config.base_url + 'images/icons/new/close.png',
                text: lang('Close'),
                cls: 'Sfr_BtnFormGrey',
                overCls: 'Sfr_BtnFormGrey-Hover',
                handler: function () {
                    thisObj.close();
                }
            }];

        this.callParent(arguments);
    },
    listeners: {
        afterRender: function () {
            var thisObj = this;
            Ext.getCmp('Koltiva.view.IMS.WinFarmerCoachingMapping-GridCoachingMapping').getStore().load();
        }
    }
});
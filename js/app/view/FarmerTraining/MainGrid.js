function submitOnEnter(field, event) {
    if (event.getKey() == event.ENTER) {
        setFilterLs();
        Ext.getCmp('Koltiva.view.FarmerTraining.MainGrid-Grid').getStore().loadPage(1);
    }
}

function setFilterLs() {
    localStorage.setItem('patchouli_farmergroup_ls',
        JSON.stringify({
            ptextSearch: Ext.getCmp('Koltiva.view.FarmerTraining.MainGrid-textSearch').getValue()
        })
    );
}

Ext.define('Koltiva.view.FarmerTraining.MainGrid', {
    extend: 'Ext.panel.Panel',
    id: 'Koltiva.view.FarmerTraining.MainGrid',
    renderTo: 'ext-content',
    style: 'padding:0 15px 15px 15px;margin:5px 0 0 0;',
    listeners: {
        afterRender: function () {
            //hilangkan view Filter region
            document.getElementById('divCommonContentRegion').style.display = 'none';

            //load storenya sebelum viewnya aktif
            setFilterLs();
            Ext.getCmp('Koltiva.view.FarmerTraining.MainGrid-Grid').getStore().load();
        }
    },
    initComponent: function () {
        var thisObj = this;

        //store
        thisObj.storeMainGrid = Ext.create('Koltiva.store.FarmerTraining.MainGrid');

        thisObj.contextMenuMainGrid = Ext.create('Ext.menu.Menu', {
            items: [{
                icon: varjs.config.base_url + 'images/icons/new/view.png',
                text: lang('View'),
                handler: function () {
                    var sm = Ext.getCmp('Koltiva.view.FarmerTraining.MainGrid-Grid').getSelectionModel().getSelection()[0];

                    Ext.getCmp('Koltiva.view.FarmerTraining.MainGrid').destroy(); //destory current view
                    var MainForm = [];

                    //create object View
                    if (Ext.getCmp('Koltiva.view.FarmerTraining.MainForm') == undefined) {
                        MainForm = Ext.create('Koltiva.view.FarmerTraining.MainForm', {
                            viewVar: {
                                opsiDisplay: 'view',
                                trainMasterID: sm.get('id')
                            }
                        });
                    } else {
                        //destroy, create ulang
                        Ext.getCmp('Koltiva.view.FarmerTraining.MainForm').destroy();
                        MainForm = Ext.create('Koltiva.view.FarmerTraining.MainForm', {
                            viewVar: {
                                opsiDisplay: 'view',
                                trainMasterID: sm.get('id')
                            }
                        });
                    }
                }
            }, {
                icon: varjs.config.base_url + 'images/icons/new/update.png',
                text: lang('Update'),
                hidden: !m_act_update,
                handler: function () {
                    var sm = Ext.getCmp('Koltiva.view.FarmerTraining.MainGrid-Grid').getSelectionModel().getSelection()[0];

                    Ext.getCmp('Koltiva.view.FarmerTraining.MainGrid').destroy(); //destory current view
                    var MainForm = [];

                    //create object View
                    if (Ext.getCmp('Koltiva.view.FarmerTraining.MainForm') == undefined) {
                        MainForm = Ext.create('Koltiva.view.FarmerTraining.MainForm', {
                            viewVar: {
                                opsiDisplay: 'update',
                                trainMasterID: sm.get('id')
                            }
                        });
                    } else {
                        //destroy, create ulang
                        Ext.getCmp('Koltiva.view.FarmerTraining.MainForm').destroy();
                        MainForm = Ext.create('Koltiva.view.FarmerTraining.MainForm', {
                            viewVar: {
                                opsiDisplay: 'update',
                                trainMasterID: sm.get('id')
                            }
                        });
                    }
                }
            }, {
                icon: varjs.config.base_url + 'images/icons/new/delete.png',
                text: lang('Delete'),
                hidden: !m_act_delete,
                handler: function () {
                    var sm = Ext.getCmp('Koltiva.view.FarmerTraining.MainGrid-Grid').getSelectionModel().getSelection()[0];

                    Ext.MessageBox.confirm('Message', 'Do you want to delete this data ?', function (btn) {
                        if (btn == 'yes') {
                            Ext.Ajax.request({
                                waitMsg: 'Please Wait',
                                url: m_api + '/farmer_group/farmer_group_form',
                                method: 'DELETE',
                                params: {
                                    FarmerGroupID: sm.get('FarmerGroupID')
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
                                    setFilterLs();
                                    Ext.getCmp('Koltiva.view.FarmerTraining.MainGrid-Grid').getStore().load();
                                },
                                failure: function (response, opts) {
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
            layout: 'column',
            border: false,
            items: [{
                columnWidth: 1,
                xtype: 'panel',
                frame: false,
                id: 'Koltiva.view.FarmerTraining.MainGrid-gridInformation',
                html: ''
            }]
        }, {
            xtype: 'grid',
            id: 'Koltiva.view.FarmerTraining.MainGrid-Grid',
            style: 'border:1px solid #CCC;margin-top:4px;',
            loadMask: true,
            selType: 'rowmodel',
            store: thisObj.storeMainGrid,
            viewConfig: {
                deferEmptyText: false,
                emptyText: GetDefaultContentNoData()
            },
            dockedItems: [{
                xtype: 'pagingtoolbar',
                store: thisObj.storeMainGrid,
                dock: 'bottom',
                displayInfo: true
            }, {
                xtype: 'toolbar',
                dock: 'top',
                items: [{
                    icon: varjs.config.base_url + 'images/icons/new/add.png',
                    cls: 'Sfr_BtnGridGreen',
                    overCls: 'Sfr_BtnGridGreen-Hover',
                    text: lang('Add'),
                    hidden: !m_act_add,
                    handler: function () {
                        Ext.getCmp('Koltiva.view.FarmerTraining.MainGrid').destroy(); //destory current view
                        var MainForm = [];

                        //create object View
                        if (Ext.getCmp('Koltiva.view.FarmerTraining.MainForm') == undefined) {
                            MainForm = Ext.create('Koltiva.view.FarmerTraining.MainForm', {
                                viewVar: {
                                    opsiDisplay: 'insert'
                                }
                            });
                        } else {
                            //destroy, create ulang
                            Ext.getCmp('Koltiva.view.FarmerTraining.MainForm').destroy();
                            MainForm = Ext.create('Koltiva.view.FarmerTraining.MainForm', {
                                viewVar: {
                                    opsiDisplay: 'insert'
                                }
                            });
                        }
                    }
                }, {
                    name: 'key',
                    baseCls: 'Sfr_TxtfieldSearchGrid',
                    id: 'Koltiva.view.FarmerTraining.MainGrid-textSearch',
                    xtype: 'textfield',
                    width: 400,
                    emptyText: lang('Cari berdasar nama/ID') + ', ' + lang('Press \'Enter\' to search'),
                    hidden: true,
                    listeners: {
                        specialkey: submitOnEnter
                    }
                }, {
                    xtype: 'tbspacer',
                    flex: 1
                }, {
                    xtype: 'button',
                    icon: varjs.config.base_url + 'images/icons/new/add-filter.png',
                    text: lang('Apply Filter'),
                    cls: 'Sfr_BtnGridPaleBlue',
                    overCls: 'Sfr_BtnGridPaleBlue-Hover',
                    handler: function () {
                        var WinApplyFilter = Ext.create('Koltiva.view.FarmerTraining.WinApplyFilter', {
                            viewVar: {
                                StoreGrid: thisObj.storeMainGrid
                            }
                        });
                        if (!WinApplyFilter.isVisible()) {
                            WinApplyFilter.center();
                            WinApplyFilter.show();
                        } else {
                            WinApplyFilter.close();
                        }
                    }
                }, {
                    icon: varjs.config.base_url + 'images/icons/new/reload.png',
                    cls: 'Sfr_BtnGridBlue',
                    overCls: 'Sfr_BtnGridBlue-Hover',
                    tooltip: lang('Reload'),
                    handler: function () {
                        //reload
                        Ext.getCmp('Koltiva.view.FarmerTraining.MainGrid-Grid').getStore().loadPage(1);
                    }
                }]
            }],
            columns: [{
                text: lang('Action'),
                xtype: 'actioncolumn',
                flex: 0.3,
                items: [{
                    icon: varjs.config.base_url + 'images/icons/new/action.png',
                    handler: function (grid, rowIndex, colIndex, item, e, record) {
                        thisObj.contextMenuMainGrid.showAt(e.getXY());
                    }
                }]
            }, {
                text: lang('ID'),
                dataIndex: 'id',
                width: '5%'
            }, {
                text: lang('Trainings'),
                flex: 1,
                dataIndex: 'training'
            }, {
                text: lang('District'),
                flex: 1,
                dataIndex: 'tot'
            }, {
                text: lang('Participants'),
                flex: 1,
                dataIndex: 'participant'
            }, {
                text: lang('Start'),
                flex: 1,
                dataIndex: 'start'
            }, {
                text: lang('End'),
                flex: 1,
                dataIndex: 'end'
            }, {
                text: lang('Days'),
                flex: 1,
                dataIndex: 'days'
            }, {
                text: lang('Status'),
                flex: 1,
                dataIndex: 'TrainingStatus'
            }]
        }];

        this.callParent(arguments);
    }
});
/*******************************************
 * Author : aji.alhabsyi@koltiva.com
 * Created On : Tue June 28 2022
 * File : MainGrid.js
********************************************/

Ext.define('Koltiva.view.TraceabilitySetting.VehicleType.MainGrid', {
    extend: 'Ext.panel.Panel',
    id: 'Koltiva.view.TraceabilitySetting.VehicleType.MainGrid',
    renderTo: 'ext-content',
    style: 'padding: 0 15px 15px 15px; margin: 5px 0 0 0;',
    listeners: {
        afterRender: function(component, eOpts) {
            var thisObj = this;
            document.getElementById('Sfr_IdBoxInfoDataGrid').style.display = 'block';
        }
    },
    initComponent: function() {
        var thisObj = this;

        //Store
        thisObj.StoreGridMain = Ext.create('Koltiva.store.TraceabilitySetting.VehicleType.MainGrid', {
            storeVar: {
                textSearch: ''
            }
        });

        //ContextMenu
        thisObj.ContextMenuGrid = Ext.create('Ext.menu.Menu', {
            cls: 'Sfr_ConMenu',
            items: [{
                icon: varjs.config.base_url + 'images/icons/new/update.png',
                text: lang('Update'),
                cls: 'Sfr_BtnConMenuWhite',
                hidden: m_act_update,
                handler: function() {
                    var sm = Ext.getCmp('Koltiva.view.TraceabilitySetting.VehicleType.MainGrid-Grid').getSelectionModel().getSelection()[0];

                    Ext.getCmp('Koltiva.view.TraceabilitySetting.VehicleType.MainGrid').destroy(); //destroy current view
                    let FormMainApp = [];
                    
                    //create object view for MainGrid
                    if (Ext.getCmp('Koltiva.veiw.TraceabilitySetting.VehicleType.MainForm') == undefined) {
                        FormMainApp = Ext.create('Koltiva.view.TraceabilitySetting.VehicleType.MainForm', {
                            viewVar: {
                                OpsiDisplay: 'update',
                                GHGVehicleTypeID: sm.get('GHGVehicleTypeID')
                            }
                        });
                    } else {
                        //destroy, recreate
                        Ext.getCmp('Koltiva.view.TraceabilitySetting.VehicleType.MainForm').destroy();
                        FormMainApp = Ext.create('Koltiva.view.TraceabilitySetting.VehicleType.MainForm', {
                            viewVar: {
                                OpsiDisplay: 'update',
                                GHGVehicleTypeID: sm.get('GHGVehicleTypeID')
                            }
                        });
                    }
                }
            }, {
                icon: varjs.config.base_url + 'images/icons/new/delete.png',
                text: lang('Delete'),
                cls: 'Sfr_BtnConMenuWhite',
                hidden: m_act_delete,
                handler: function() {
                    var sm = Ext.getCmp('Koltiva.view.TraceabilitySetting.VehicleType.MainGrid-Grid').getSelectionModel().getSelection()[0];

                    Ext.MessageBox.confirm(lang('Message'), lang('Do you want to delete this data?'), function (btn) {
                        if (btn == 'yes') {
                            Ext.Ajax.request({
                                waitingMsg: 'Please Wait',
                                url: m_api + '/traceability_setting/vehicle_type/vehicle_type_data',
                                method: 'DELETE',
                                params: {
                                    GHGVehicleTypeID: sm.get('GHGVehicleTypeID'),
                                },
                                success: function(rp, o) {
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
                                failure: function(rp, o) {
                                    try {
                                        var r =Ext.decode(rp.responseText);
                                        Ext,MessageBox.show({
                                            title: lang('Erro'),
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
            id: 'Koltiva.view.TraceabilitySetting.VehicleType.MainGrid-Grid',
            style: 'border: 1px solid #CCC; margin-top: 4px',
            loadMask: true,
            selType: 'rowmodel',
            store: thisObj.StoreGridMain,
            cls: 'Sfr_GridNew',
            viewConfig: {
                deferEmptyText: false,
                forceFit: false,
                emptyText: GetDefaultContentNoData(),
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
                hidden: false,
                items: [{
                    xtype: 'button',
                    icon: varjs.config.base_url + 'images/icons/new/add.png',
                    text: lang('Add'),
                    hidden: m_act_add,
                    cls: 'Sfr_BtnGridGreen',
                    overCls: 'Sfr_BtnGridGreen-Hover',
                    handler: function() {
                        Ext.getCmp('Koltiva.view.TraceabilitySetting.VehicleType.MainGrid').destroy(); //destroy current view

                        //create object view
                        if (Ext.getCmp('Koltiva.view.TraceabilitySetting.VehicleType.MainForm') == undefined) {
                            Ext.create('Koltiva.view.TraceabilitySetting.VehicleType.MainForm', {
                                viewVar: {
                                    OpsiDisplay: 'insert',
                                }
                            });
                        } else {
                            //destroy object view
                            Ext.getCmp('Koltiva.view.TraceabilitySetting.VehicleType.MainForm').destroy();
                            Ext.create('Koltiva.view.TraceabilitySetting.VehicleType.MainForm', {
                                viewVar: {
                                    OpsiDisplay: 'insert',
                                }
                            });
                        }
                    }
                }, {
                    xtype: 'tbspacer',
                    flex: 1
                }, {
                    name: 'Koltiva.view.TraceabilitySetting.VehicleType.MainGrid-textVehicleTypeNameSearch',
                    id: 'Koltiva.view.TraceabilitySetting.VehicleType.MainGrid-textVehicleTypeNameSearch',
                    xtype: 'textfield',
                    baseCls: 'Sfr_TxtfieldSearchGrid',
                    width: 500,
                    emptyText: lang('Cari berdasarkan nama'),
                    listeners: {
                        specialkey: thisObj.submitOnEnterGrid
                    }
                }, {
                    xtype: 'button',
                    icon: varjs.config.base_url + 'images/icons/new/search_white.png',
                    text: lang('Search'),
                    cls: 'Sfr_BtnGridBlue',
                    overCls: 'Sfr_BtnGridBlue-Hover',
                    handler: function() {
                        thisObj.StoreGridMain.storeVar.textSearch = Ext.getCmp('Koltiva.view.TraceabilitySetting.VehicleType.MainGrid-textVehicleTypeNameSearch').getValue();
                        thisObj.StoreGridMain.loadPage(1);
                    }
                }]
            }],
            columns: [{
                xtype: 'actioncolumn',
                text: lang(''),
                width: '50',
                items: [{
                    icon: varjs.config.base_url + 'images/icons/new/action.png',
                    handler: function(grid, rowIndex, colIndex, item, e, record) {
                        thisObj.ContextMenuGrid.showAt(e.getXY());
                    }
                }]
            }, {
                text: lang('GHG Vehicle Type ID'),
                hidden: true,
                dataIndex: 'GHGVehicleTypeID'
            }, {
                text: lang('Vehicle Type Name'),
                dataIndex: 'VehicleTypeName',
                flex: 1
            }, {
                text: lang('Fuel Consumption'),
                dataIndex: 'FuelConsumption',
                flex: 1
            }, {
                text: lang('Status Code'),
                dataIndex: 'StatusCode',
                flex: 1
            }]
        }];

        this.callParent(arguments);
    },
    submitOnEnterGrid: function (field, event) {
        if (event.getKey() == event.ENTER) {
            Ext.getCmp('Koltiva.view.TraceabilitySetting.VehicleType.MainGrid-Grid').getStore().storeVar.textSearch = Ext.getCmp('Koltiva.view.TraceabilitySetting.VehicleType.MainGrid-textVehicleTypeNameSearch').getValue();
            Ext.getCmp('Koltiva.view.TraceabilitySetting.FuelType.MainGrid-Grid').getStore().loadPage(1);
        }
    }
});
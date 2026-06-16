/******************************************
 *  Author : hasbycs@gmail.com
 *  Created On : 2021-10-06
 *  File : MainGrid.js
 *******************************************/
Ext.define('Koltiva.view.Coaching.MainGrid', {
    extend: 'Ext.panel.Panel',
    id: 'Koltiva.view.Coaching.MainGrid',
    renderTo: 'ext-content',
    style: 'padding:0 7px 7px 7px;margin:2px 0 0 0;',
    listeners: {
        afterRender: function (component, eOpts) {
            var thisObj = this;
            // document.getElementById('ContentTopBar').style.display = 'block';
        }
    },
    initComponent: function () {
        var thisObj = this;
        thisObj.StoreGridMain = Ext.create('Koltiva.store.Coaching.MainGrid', {
            storeVar: {
                KeySearch: ''
            }
        });

        thisObj.ContextMenuGrid = Ext.create('Ext.menu.Menu', {
            cls: 'Sfr_ConMenu',
            items: [{
                    icon: varjs.config.base_url + 'images/icons/new/view.png',
                    text: lang('View'),
                    cls: 'Sfr_BtnConMenuWhite',
                    handler: function () {
                        var sm = Ext.getCmp('Koltiva.view.Coaching.MainGrid-Grid').getSelectionModel().getSelection()[0];

                        Ext.getCmp('Koltiva.view.Coaching.MainGrid').destroy(); //destory current view
                        var FormMainApp = [];
                        if (Ext.getCmp('Koltiva.view.Coaching.MainForm') == undefined) {
                            FormMainFarmer = Ext.create('Koltiva.view.Coaching.MainForm', {
                                viewVar: {
                                    OpsiDisplay: 'view',
                                    CoachingID: sm.get('CoachingID')
                                }
                            });
                        } else {
                            //destroy, create ulang
                            Ext.getCmp('Koltiva.view.Coaching.MainForm').destroy();
                            FormMainFarmer = Ext.create('Koltiva.view.Coaching.MainForm', {
                                viewVar: {
                                    OpsiDisplay: 'view',
                                    CoachingID: sm.get('CoachingID')
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
                        var sm = Ext.getCmp('Koltiva.view.Coaching.MainGrid-Grid').getSelectionModel().getSelection()[0];

                        Ext.getCmp('Koltiva.view.Coaching.MainGrid').destroy(); //destory current view
                        var FormMainApp = [];
                        if (Ext.getCmp('Koltiva.view.Coaching.MainForm') == undefined) {
                            FormMainFarmer = Ext.create('Koltiva.view.Coaching.MainForm', {
                                viewVar: {
                                    OpsiDisplay: 'update',
                                    CoachingID: sm.get('CoachingID')
                                }
                            });
                        } else {
                            //destroy, create ulang
                            Ext.getCmp('Koltiva.view.Coaching.MainForm').destroy();
                            FormMainFarmer = Ext.create('Koltiva.view.Coaching.MainForm', {
                                viewVar: {
                                    OpsiDisplay: 'update',
                                    CoachingID: sm.get('CoachingID')
                                }
                            });
                        }
                    }
                }, {
                    icon: varjs.config.base_url + 'images/icons/new/delete.png',
                    text: lang('Delete'),
                    cls: 'Sfr_BtnConMenuWhite',
                    hidden: m_act_delete,
                    handler: function () {
                        var sm = Ext.getCmp('Koltiva.view.Coaching.MainGrid-Grid').getSelectionModel().getSelection()[0];
                        Ext.MessageBox.confirm(lang('Message'), lang('Do you want to delete this staff ?'), function (btn) {
                            if (btn == 'yes') {
                                Ext.Ajax.request({
                                    waitMsg: 'Please Wait',
                                    url: m_api + '/coaching/coaching_data',
                                    method: 'DELETE',
                                    params: {
                                        CoachingID: sm.get('CoachingID')
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

        var cmb_farmer_group = Ext.create('Koltiva.store.ComboGeneral.CmbFarmerGroup');
        cmb_farmer_group.load();

        thisObj.items = [{
                xtype: 'grid',
                id: 'Koltiva.view.Coaching.MainGrid-Grid',
                style: 'border:1px solid #CCC;margin-top:4px;',
                cls: 'Sfr_GridNew',
                loadMask: true,
                selType: 'rowmodel',
                store: thisObj.StoreGridMain,
                minHeight:300,
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
                                    Ext.getCmp('Koltiva.view.Coaching.MainGrid').destroy(); //destory current view

                                    var FormMainApp = [];
                                    //create object View untuk FormMainGrower
                                    if (Ext.getCmp('Koltiva.view.Coaching.MainForm') == undefined) {
                                        FormMainFarmer = Ext.create('Koltiva.view.Coaching.MainForm', {
                                            viewVar: {
                                                OpsiDisplay: 'insert'
                                            }
                                        });
                                    } else {
                                        //destroy, create ulang
                                        Ext.getCmp('Koltiva.view.Coaching.MainForm').destroy();
                                        FormMainFarmer = Ext.create('Koltiva.view.Coaching.MainForm', {
                                            viewVar: {
                                                OpsiDisplay: 'insert'
                                            }
                                        });
                                    }
                                }
                            }, {
                                name: 'Koltiva.view.Coaching.MainGrid-TxtSearchNama',
                                id: 'Koltiva.view.Coaching.MainGrid-TxtSearchNama',
                                xtype: 'textfield',
                                baseCls: 'Sfr_TxtfieldSearchGrid',
                                width: 400,
                                emptyText: lang('Cari berdasar nama'),
                                listeners: {
                                    specialkey: thisObj.submitOnEnterGrid
                                }
                            }, {
                                xtype: 'tbspacer',
                                flex: 1
                            }, {
                                xtype: 'combobox',
                                id: 'Koltiva.view.Coaching.MainGrid-FarmerGroupID',
                                name: 'Koltiva.view.Coaching.MainGrid-FarmerGroupID',
                                store: cmb_farmer_group,
                                emptyText: lang('Farmer Group'),
                                labelAlign:'top',
                                queryMode: 'local',
                                displayField: 'label',
                                valueField: 'id'
                            }, {
                                xtype: 'datefield',
                                id: 'Koltiva.view.Coaching.MainGrid-StartDate',
                                name: 'Koltiva.view.Coaching.MainGrid-StartDate',
                                format: 'Y-m-d',
                                listeners : {
                                    render : function(datefield) {
                                        /// code to convert GMT String to date object
                                        datefield.setValue(new Date());
                                    }
                                }
                            }, {
                                xtype: 'datefield',
                                id: 'Koltiva.view.Coaching.MainGrid-EndDate',
                                name: 'Koltiva.view.Coaching.MainGrid-EndDate',
                                format: 'Y-m-d',
                                listeners : {
                                    render : function(datefield) {
                                        /// code to convert GMT String to date object
                                        datefield.setValue(new Date());
                                    }
                                }
                            },{
                                xtype: 'button',
                                icon: varjs.config.base_url + 'images/icons/new/search_white.png',
                                text: lang('Search'),
                                cls: 'Sfr_BtnGridBlue',
                                overCls: 'Sfr_BtnGridBlue-Hover',
                                handler: function () {
                                    thisObj.StoreGridMain.storeVar.KeySearch = Ext.getCmp('Koltiva.view.Coaching.MainGrid-TxtSearchNama').getValue();
                                    thisObj.StoreGridMain.storeVar.FarmerGroupID = Ext.getCmp('Koltiva.view.Coaching.MainGrid-FarmerGroupID').getValue();
                                    thisObj.StoreGridMain.storeVar.StartDate = Ext.getCmp('Koltiva.view.Coaching.MainGrid-StartDate').getValue();
                                    thisObj.StoreGridMain.storeVar.EndDate = Ext.getCmp('Koltiva.view.Coaching.MainGrid-EndDate').getValue();
                                    thisObj.StoreGridMain.loadPage(1);
                                }
                            },{
                                xtype: 'button',
                                icon: varjs.config.base_url + 'images/icons/new/export.png',
                                text: lang('Export Farmers'),
                                cls: 'Sfr_BtnGridPaleBlue',
                                overCls: 'Sfr_BtnGridBlue-Hover',
                                handler: function () {
                                    Ext.MessageBox.show({
                                        msg: 'Please wait...',
                                        progressText: 'Exporting...',
                                        width: 300,
                                        wait: true,
                                        waitConfig: {
                                            interval: 200
                                        },
                                        icon: 'ext-mb-download', //custom class in msg-box.html
                                        animateTarget: 'mb7'
                                    });

                                    Ext.Ajax.request({
                                        url: m_api + '/coaching/export_coaching/',

                                        method: 'GET',
                                        waitMsg: lang('Please Wait'),
                                        timeout: 360000,
                                        params:{
                                            KeySearch : Ext.getCmp('Koltiva.view.Coaching.MainGrid-TxtSearchNama').getValue(),
                                            FarmerGroupID : Ext.getCmp('Koltiva.view.Coaching.MainGrid-FarmerGroupID').getValue(),
                                            StartDate : Ext.getCmp('Koltiva.view.Coaching.MainGrid-StartDate').getValue(),
                                            EndDate : Ext.getCmp('Koltiva.view.Coaching.MainGrid-EndDate').getValue()
                                        },
                                        success: function (data) {
                                            Ext.MessageBox.hide();
                                            var jsonResp = JSON.parse(data.responseText);

                                            if (jsonResp.success == true) {
                                                window.location = jsonResp.filenya;
                                            } else {
                                                Ext.MessageBox.show({
                                                    title: lang('Warning'),
                                                    msg: lang('Data Not Found'),
                                                    buttons: Ext.MessageBox.OK,
                                                    animateTarget: 'mb9',
                                                    icon: 'ext-mb-error'
                                                });

                                                return;
                                            }
                                        },
                                        failure: function () {
                                            Ext.MessageBox.hide();
                                            Ext.MessageBox.show({
                                                title: 'Notifications',
                                                msg: 'Failed to export, Please try again.',
                                                buttons: Ext.MessageBox.OK,
                                                animateTarget: 'mb9',
                                                icon: 'ext-mb-error'
                                            });
                                        }
                                    });
                                }
                            }]
                    }],
                columns: [{
                        text: '',
                        xtype: 'actioncolumn',
                        flex: 0.05,
                        items: [{
                                icon: varjs.config.base_url + 'images/icons/new/action.png',
                                handler: function (grid, rowIndex, colIndex, item, e, record) {
                                    thisObj.ContextMenuGrid.showAt(e.getXY());
                                }
                            }]
                    }, {
                        text: 'No',
                        flex: 0.05,
                        xtype: 'rownumberer'
                    }, {
                        text: lang('CoachingID'),
                        dataIndex: 'CoachingID',
                        hidden: true
                    }, {
                        text: lang('SupplierID'),
                        dataIndex: 'SupplierID',
                        hidden: true
                    }, {
                        text: lang('UserID'),
                        dataIndex: 'UserID',
                        hidden: true
                    }, {
                        text: lang('Coaching Date'),
                        dataIndex: 'CoachingDate',
                        flex: 0.1
                    }, {
                        text: lang('Farmer ID'),
                        dataIndex: 'MemberDisplayID',
                        flex: 0.1
                    }, {
                        text: lang('Coaching Recipient Name'),
                        dataIndex: 'CoachingRecipientName',
                        flex: 0.2
                    }, {
                        text: lang('Farmer Group'),
                        dataIndex: 'GroupName',
                        flex: 0.2
                    }, {
                        text: lang('Coaching Recipient'),
                        dataIndex: 'CoachingRecipient',
                        flex: 0.1,
                        renderer: function (value) {
                            var RetVal;
                            if(value != null && value != ''){
                                RetVal = lang(value);
                            }else{
                                RetVal = '-';
                            }
                            return RetVal;
                        }
                    }, {
                        text: lang('FA Coaching'),
                        dataIndex: 'PersonNm',
                        flex: 0.1
                    }, {
                        text: lang('Number of Coaching Sessions'),
                        dataIndex: 'sesi',
                        width: '16%',
                        renderer: function (value, meta) {
                            meta.style = "text-align:center;"; return value
                        }
                    }]
            }];

        this.callParent(arguments);
    },
    submitOnEnterGrid: function (field, event) {
        if (event.getKey() == event.ENTER) {
            Ext.getCmp('Koltiva.view.Coaching.MainGrid-Grid').getStore().storeVar.KeySearch = Ext.getCmp('Koltiva.view.Coaching.MainGrid-TxtSearchNama').getValue();
            Ext.getCmp('Koltiva.view.Coaching.MainGrid-Grid').getStore().storeVar.FarmerGroupID = Ext.getCmp('Koltiva.view.Coaching.MainGrid-FarmerGroupID').getValue();
            Ext.getCmp('Koltiva.view.Coaching.MainGrid-Grid').getStore().storeVar.StartDate = Ext.getCmp('Koltiva.view.Coaching.MainGrid-StartDate').getValue();
            Ext.getCmp('Koltiva.view.Coaching.MainGrid-Grid').getStore().storeVar.EndDate = Ext.getCmp('Koltiva.view.Coaching.MainGrid-EndDate').getValue();
            Ext.getCmp('Koltiva.view.Coaching.MainGrid-Grid').getStore().loadPage(1);
        }
    }
});
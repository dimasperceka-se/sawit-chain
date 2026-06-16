/******************************************
 *  Author : fikrifauzul@gmail.com
 *  Created On : 2020-11-17
 *  File : MainForm.js
 *******************************************/
/*
 Param2 yg diperlukan ketika load View ini
 - OpsiDisplay
 - DashID
 */

Ext.define('Koltiva.view.UserDashboard.MainForm', {
    extend: 'Ext.panel.Panel',
    id: 'Koltiva.view.UserDashboard.MainForm',
    style: 'padding:0 15px 15px 15px;margin:5px 0 0 0;',
    viewVar: false,
    setViewVar: function (value) {
        this.viewVar = value;
    },
    renderTo: 'ext-content',
    listeners: {
        afterRender: function () {
            var thisObj = this;
            document.getElementById('divCommonContentRegion').style.display = 'none';


            if (thisObj.viewVar.OpsiDisplay == 'update') {

                //load formnya
                Ext.getCmp('Koltiva.view.UserDashboard.MainForm-Form').getForm().load({
                    url: m_api + '/user_dashboard/user_dashboard_form_open',
                    method: 'GET',
                    params: {
                        DashID: this.viewVar.DashID
                    },
                    success: function (form, action) {
                        Ext.MessageBox.hide();
                        var r = Ext.decode(action.response.responseText);
                        //console.log(r);

                    },
                    failure: function (form, action) {
                        Ext.MessageBox.hide();
                        Ext.MessageBox.show({
                            title: lang('Failed'),
                            msg: lang('Failed to retrieve data'),
                            buttons: Ext.MessageBox.OK,
                            animateTarget: 'mb9',
                            icon: 'ext-mb-error'
                        });
                    }
                });
            }
        },
        beforerender: function () {
            var thisObj = this;

            if (thisObj.viewVar.OpsiDisplay != 'insert') {
                Ext.MessageBox.show({
                    msg: 'Please wait...',
                    progressText: 'Loading...',
                    width: 300,
                    wait: true,
                    waitConfig: {
                        interval: 200
                    },
                    icon: 'ext-mb-info', //custom class in msg-box.html
                    animateTarget: 'mb9'
                });
            }
        }
    },
    initComponent: function () {
        var thisObj = this;
        var labelWidth = 250;

        //Store yg dipakai =============================================================== (Begin)
        thisObj.StoreUserSharing = Ext.create('Koltiva.store.UserDashboard.UserSharingGrid', {
            storeVar: {
                DashID: thisObj.viewVar.DashID,
                KeySearch: ''
            }
        });
        //Store yg dipakai =============================================================== (End)

        thisObj.ObjPanelMain = Ext.create('Ext.panel.Panel', {
            title: lang('User Dashboard Form'),
            frame: true,
            cls: 'Sfr_PanelLayoutForm',
            collapsible: true,
            style: 'margin-top:0px;padding-top:0px;',
            items: [{
                    xtype: 'form',
                    id: 'Koltiva.view.UserDashboard.MainForm-Form',
                    fileUpload: true,
                    buttonAlign: 'right',
                    cls: 'Sfr_PanelSubLayoutForm',
                    items: [{
                            layout: 'column',
                            border: false,
                            items: [{
                                    columnWidth: 1,
                                    layout: 'form',
                                    style: 'padding:10px 20px 10px 20px;',
                                    items: [{
                                            xtype: 'textfield',
                                            id: 'Koltiva.view.UserDashboard.MainForm-Form-DashID',
                                            name: 'Koltiva.view.UserDashboard.MainForm-Form-DashID',
                                            hidden: true
                                        }, {
                                            xtype: 'textfield',
                                            id: 'Koltiva.view.UserDashboard.MainForm-Form-DashName',
                                            name: 'Koltiva.view.UserDashboard.MainForm-Form-DashName',
                                            fieldLabel: lang('Dashboard Name'),
                                            allowBlank: false,
                                            baseCls: 'Sfr_FormInputMandatory',
                                            labelWidth: labelWidth
                                        }, {
                                            xtype: 'numericfield',
                                            id: 'Koltiva.view.UserDashboard.MainForm-Form-BoardID',
                                            name: 'Koltiva.view.UserDashboard.MainForm-Form-BoardID',
                                            fieldLabel: lang('Board ID'),
                                            allowBlank: false,
                                            baseCls: 'Sfr_FormInputMandatory',
                                            labelWidth: labelWidth
                                        }, {
                                            xtype: 'radiogroup',
                                            fieldLabel: lang('Active Status'),
                                            labelWidth: labelWidth,
                                            allowBlank: false,
                                            baseCls: 'Sfr_FormInputMandatory',
                                            msgTarget: 'side',
                                            columns: 3,
                                            items: [{
                                                    boxLabel: lang('Yes'),
                                                    name: 'Koltiva.view.UserDashboard.MainForm-Form-ActiveStatus',
                                                    inputValue: 'yes',
                                                    id: 'Koltiva.view.UserDashboard.MainForm-Form-ActiveStatus1',
                                                    listeners: {
                                                        change: function () {
                                                            return false;
                                                        }
                                                    }
                                                }, {
                                                    boxLabel: lang('No'),
                                                    name: 'Koltiva.view.UserDashboard.MainForm-Form-ActiveStatus',
                                                    inputValue: 'no',
                                                    id: 'Koltiva.view.UserDashboard.MainForm-Form-ActiveStatus2',
                                                    listeners: {
                                                        change: function () {
                                                            return false;
                                                        }
                                                    }
                                                }]
                                        }, {
                                            xtype: 'textarea',
                                            fieldLabel: lang('Description'),
                                            id: 'Koltiva.view.UserDashboard.MainForm-Form-Description',
                                            name: 'Koltiva.view.UserDashboard.MainForm-Form-Description',
                                            height: 80
                                        }]
                                }]
                        }],
                    buttons: [{
                            xtype: 'button',
                            icon: varjs.config.base_url + 'images/icons/new/save.png',
                            text: lang('Save'),
                            cls: 'Sfr_BtnFormBlue',
                            overCls: 'Sfr_BtnFormBlue-Hover',
                            id: 'Koltiva.view.UserDashboard.MainForm-Form-BtnSave',
                            handler: function () {
                                var Formnya = Ext.getCmp('Koltiva.view.UserDashboard.MainForm-Form').getForm();

                                if (Formnya.isValid()) {
                                    Formnya.submit({
                                        url: m_api + '/user_dashboard/data_input',
                                        method: 'POST',
                                        waitMsg: lang('Saving data'),
                                        params: {
                                            OpsiDisplay: thisObj.viewVar.OpsiDisplay
                                        },
                                        success: function (fp, o) {
                                            var r = Ext.decode(o.response.responseText);

                                            Ext.MessageBox.show({
                                                title: 'Information',
                                                msg: lang('Data saved'),
                                                buttons: Ext.MessageBox.OK,
                                                animateTarget: 'mb9',
                                                icon: 'ext-mb-success',
                                                fn: function (btn) {
                                                    if (btn == 'ok') {
                                                        Ext.getCmp('Koltiva.view.UserDashboard.MainForm').destroy(); //destory current view
                                                        var FormMain = [];

                                                        if (Ext.getCmp('Koltiva.view.UserDashboard.MainForm') == undefined) {
                                                            FormMain = Ext.create('Koltiva.view.UserDashboard.MainForm', {
                                                                viewVar: {
                                                                    OpsiDisplay: 'update',
                                                                    DashID: r.DashID
                                                                }
                                                            });
                                                        } else {
                                                            //destroy, create ulang
                                                            Ext.getCmp('Koltiva.view.UserDashboard.MainForm').destroy();
                                                            FormMain = Ext.create('Koltiva.view.UserDashboard.MainForm', {
                                                                viewVar: {
                                                                    OpsiDisplay: 'update',
                                                                    DashID: r.DashID
                                                                }
                                                            });
                                                        }
                                                    }
                                                }
                                            });
                                        },
                                        failure: function (fp, o) {
                                            try {
                                                var r = Ext.decode(o.response.responseText);
                                                Ext.MessageBox.show({
                                                    title: 'Error',
                                                    msg: r.message,
                                                    buttons: Ext.MessageBox.OK,
                                                    animateTarget: 'mb9',
                                                    icon: 'ext-mb-error'
                                                });
                                            } catch (err) {
                                                Ext.MessageBox.show({
                                                    title: 'Error',
                                                    msg: 'Connection Error',
                                                    buttons: Ext.MessageBox.OK,
                                                    animateTarget: 'mb9',
                                                    icon: 'ext-mb-error'
                                                });
                                            }
                                        }
                                    });
                                } else {
                                    Ext.MessageBox.show({
                                        title: lang('Attention'),
                                        msg: lang('Form not complete yet'),
                                        buttons: Ext.MessageBox.OK,
                                        animateTarget: 'mb9',
                                        icon: 'ext-mb-info'
                                    });
                                }
                            }
                        }]
                }]
        });

        thisObj.ContextMenuUserGrid = Ext.create('Ext.menu.Menu', {
            cls: 'Sfr_ConMenu',
            items: [{
                    icon: varjs.config.base_url + 'images/icons/new/delete.png',
                    text: lang('Delete'),
                    cls: 'Sfr_BtnConMenuWhite',
                    hidden: m_act_delete,
                    handler: function () {
                        var sm = Ext.getCmp('Koltiva.view.UserDashboard.MainForm-UserSharingGrid').getSelectionModel().getSelection()[0];

                        Ext.MessageBox.confirm('Message', lang('Do you want to delete this data ?'), function (btn) {
                            if (btn == 'yes') {
                                Ext.Ajax.request({
                                    waitMsg: 'Please Wait',
                                    url: m_api + '/user_dashboard/user_sharing',
                                    method: 'DELETE',
                                    params: {
                                        DashSetID: sm.get('DashSetID')
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
                                        thisObj.StoreUserSharing.load();
                                    },
                                    failure: function (rp, o) {
                                        try {
                                            var r = Ext.decode(rp.responseText);
                                            Ext.MessageBox.show({
                                                title: lang('Error'),
                                                msg: r.message,
                                                buttons: Ext.MessageBox.OK,
                                                animateTarget: 'mb9',
                                                icon: 'ext-mb-error'
                                            });
                                        } catch (err) {
                                            Ext.MessageBox.show({
                                                title: lang('Error'),
                                                msg: lang('Connection Error'),
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

        thisObj.ObjPanelDetail = [];
        if (thisObj.viewVar.OpsiDisplay == 'update') {

            thisObj.ObjPanelDetail = Ext.create('Ext.panel.Panel', {
                title: lang('User Sharing'),
                frame: true,
                cls: 'Sfr_PanelLayoutForm',
                collapsible: true,
                style: 'margin-top:0px;padding-top:0px;',
                items: [{
                        xtype: 'grid',
                        id: 'Koltiva.view.UserDashboard.MainForm-UserSharingGrid',
                        style: 'border:1px solid #CCC;',
                        cls: 'Sfr_GridNew',
                        loadMask: true,
                        selType: 'rowmodel',
                        store: thisObj.StoreUserSharing,
                        enableColumnHide: false,
                        viewConfig: {
                            deferEmptyText: false,
                            emptyText: GetDefaultContentNoData()
                        },
                        dockedItems: [{
                                xtype: 'pagingtoolbar',
                                store: thisObj.StoreUserSharing,
                                dock: 'bottom',
                                displayInfo: true,
                                displayMsg: lang('Showing') + ' {0} ' + lang('to') + ' {1} ' + lang('of') + ' {2} ' + lang('data')
                            }, {
                                xtype: 'toolbar',
                                dock: 'top',
                                items: [{
                                        xtype: 'button',
                                        icon: varjs.config.base_url + 'images/icons/new/add.png',
                                        text: lang('Add User'),
                                        hidden: m_act_add,
                                        cls: 'Sfr_BtnGridGreen',
                                        overCls: 'Sfr_BtnGridGreen-Hover',
                                        handler: function () {
                                            var WinFormSelectFarmerMultiple = Ext.create('Koltiva.view.UserDashboard.WinFormSelectStaffMultiple', {
                                                viewVar: {
                                                    ParentObj: thisObj,
                                                    CallFrom: 'TrainingFarmer',
                                                    DashID: thisObj.viewVar.DashID
                                                }
                                            });
                                            if (!WinFormSelectFarmerMultiple.isVisible()) {
                                                WinFormSelectFarmerMultiple.center();
                                                WinFormSelectFarmerMultiple.show();
                                            } else {
                                                WinFormSelectFarmerMultiple.close();
                                            }
                                        }
                                    }, {
                                        xtype: 'tbspacer',
                                        flex: 1
                                    }, {
                                        name: 'key',
                                        id: 'Koltiva.view.UserDashboard.MainForm-UserSharingGrid-textSearch',
                                        xtype: 'textfield',
                                        baseCls: 'Sfr_TxtfieldSearchGrid',
                                        width: 400,
                                        emptyText: lang('Cari berdasar nama/ID') + ', ' + lang('press_enter_search'),
                                        listeners: {
                                            specialkey: function (field, event) {
//                                                console.log(event);
                                                if (event.getKey() == event.ENTER) {
                                                    thisObj.StoreUserSharing.storeVar.DashID = thisObj.viewVar.DashID;
                                                    thisObj.StoreUserSharing.storeVar.KeySearch = Ext.getCmp('Koltiva.view.UserDashboard.MainForm-UserSharingGrid-textSearch').getValue();
                                                    thisObj.StoreUserSharing.loadPage(1);
                                                }

                                            }
                                        }
                                    }, {
                                        icon: varjs.config.base_url + 'images/icons/new/search_white.png',
                                        text: lang('Search'),
                                        cls: 'Sfr_BtnGridBlue',
                                        overCls: 'Sfr_BtnGridBlue-Hover',
                                        handler: function () {
                                            thisObj.StoreUserSharing.storeVar.DashID = thisObj.viewVar.DashID;
                                            thisObj.StoreUserSharing.storeVar.KeySearch = Ext.getCmp('Koltiva.view.UserDashboard.MainForm-UserSharingGrid-textSearch').getValue();
                                            thisObj.StoreUserSharing.loadPage(1);
                                        }
                                    }]
                            }],
                        columns: [{
                                text: '',
                                xtype: 'actioncolumn',
                                width: '3%',
                                items: [{
                                        icon: varjs.config.base_url + 'images/icons/new/action.png',
                                        handler: function (grid, rowIndex, colIndex, item, e, record) {
                                            thisObj.ContextMenuUserGrid.showAt(e.getXY());
                                        }
                                    }]
                            }, {
                                text: 'No',
                                width: '3%',
                                xtype: 'rownumberer'
                            }, {
                                text: lang('DashSetID'),
                                dataIndex: 'DashSetID',
                                hidden: true
                            }, {
                                text: lang('DashID'),
                                dataIndex: 'DashID',
                                hidden: true
                            }, {
                                text: lang('UserID'),
                                dataIndex: 'UserID',
                                hidden: true
                            }, {
                                text: lang('User Name'),
                                dataIndex: 'UserName',
                                flex: 2
                            }, {
                                text: lang('User Fullname'),
                                dataIndex: 'UserRealName',
                                flex: 3
                            }, {
                                text: lang('User Group'),
                                dataIndex: 'GroupName',
                                flex: 2
                            }, {
                                text: lang('Position'),
                                dataIndex: 'PositionName',
                                flex: 2
                            }, {
                                text: lang('Role'),
                                dataIndex: 'RoleName',
                                flex: 2
                            }]
                    }]
            });
        }

        //========================================================== LAYOUT UTAMA (Begin) ========================================//
        thisObj.items = [{
                xtype: 'panel',
                border: false,
                layout: {
                    type: 'hbox'
                },
                items: [{
//                        id: 'Koltiva.view.UserDashboard.MainForm-labelInfoInsert',
//                        html: '<div id="header_title_farmer">' + lang('User Dashboard Data') + '</div>'
                    }]
            }, {
                items: [{
                        id: 'Koltiva.view.UserDashboard.MainForm-LinkBackToList',
                        html: '<div id="Sfr_IdBoxInfoDataGrid" class="Sfr_BoxInfoDataGrid"><ul class="Sft_UlListInfoDataGrid"><li class="Sft_ListInfoDataGrid"><a href="javascript:Ext.getCmp(\'Koltiva.view.UserDashboard.MainForm\').BackToList()"><img class="Sft_ListIconInfoDataGrid" src="' + varjs.config.base_url + 'images/icons/new/back.png" width="20" />&nbsp;&nbsp;' + lang('Back to List') + '</a></li></div>'
                    }]
            }, {
                html: '<br />'
            }, {
                layout: 'column',
                border: false,
                items: [{
                        //LEFT CONTENT
                        columnWidth: 1,
                        items: [
                            thisObj.ObjPanelMain,
                            {
                                html: '<br>'
                            },
                            thisObj.ObjPanelDetail
                        ]
                    }]
            }];
        //========================================================== LAYOUT UTAMA (End) ========================================//

        this.callParent(arguments);
    },
    BackToList: function () {
        Ext.getCmp('Koltiva.view.UserDashboard.MainForm').destroy(); //destory current view
        var GridMainGrower = [];
        if (Ext.getCmp('Koltiva.view.UserDashboard.MainGrid') == undefined) {
            GridMainGrower = Ext.create('Koltiva.view.UserDashboard.MainGrid');
        } else {
            //destroy, create ulang
            Ext.getCmp('Koltiva.view.UserDashboard.MainGrid').destroy();
            GridMainGrower = Ext.create('Koltiva.view.UserDashboard.MainGrid');
        }
    },
    AddParticipants: function (IdAdd) {
        var thisObj = this;
        //console.log(IdAdd);

        Ext.Ajax.request({
            url: m_api + '/user_dashboard/user_sharing',
            method: 'POST',
            params: {
                UserIDs: IdAdd,
                DashID: thisObj.viewVar.DashID
            },
            success: function (rp, o) {
                var r = Ext.decode(rp.responseText);
                Ext.MessageBox.show({
                    title: lang('Information'),
                    msg: lang('User Sharing added'),
                    buttons: Ext.MessageBox.OK,
                    animateTarget: 'mb9',
                    icon: 'ext-mb-success'
                });

                thisObj.StoreUserSharing.load();
                Ext.getCmp('Koltiva.view.UserDashboard.WinFormSelectStaffMultiple-MainGrid').getStore().load();
            },
            failure: function (rp, o) {
                try {
                    var r = Ext.decode(rp.responseText);
                    Ext.MessageBox.show({
                        title: lang('Error'),
                        msg: r.message,
                        buttons: Ext.MessageBox.OK,
                        animateTarget: 'mb9',
                        icon: 'ext-mb-error'
                    });
                } catch (err) {
                    Ext.MessageBox.show({
                        title: lang('Error'),
                        msg: lang('Connection Error'),
                        buttons: Ext.MessageBox.OK,
                        animateTarget: 'mb9',
                        icon: 'ext-mb-error'
                    });
                }
            }
        });
    }
});

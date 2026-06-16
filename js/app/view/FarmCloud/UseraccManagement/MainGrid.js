Ext.define('Koltiva.view.FarmCloud.UseraccManagement.MainGrid' ,{
    extend: 'Ext.panel.Panel',
    id: 'Koltiva.view.FarmCloud.UseraccManagement.MainGrid',
    renderTo: 'ext-content',
    style:'padding:0 7px 7px 7px;margin:2px 0 0 0;',
    listeners: {
        afterRender: function(component, eOpts){
            var thisObj = this;
            document.getElementById('Sfr_IdBoxInfoDataGrid').style.display = 'block';
        }
    },
    initComponent: function() {
        var thisObj = this;

        thisObj.StoreGridMain = Ext.create('Koltiva.store.FarmCloud.UseraccManagement.MainGrid', {
            storeVar: {
                KeySearch: ''
            }
        });
        thisObj.ContextMenuGrid = Ext.create('Ext.menu.Menu',{
            cls:'Sfr_ConMenu',
	        items:[{
                icon: varjs.config.base_url + 'images/icons/new/update.png',
                text: lang('Reset Password'),
                cls:'Sfr_BtnConMenuWhite',
                itemId: 'UseraccManagement.MainGrid.ResetPass',
                hidden: m_act_update,
	            handler: function() {
                    var sm = Ext.getCmp('Koltiva.view.FarmCloud.UseraccManagement.MainGrid-Grid').getSelectionModel().getSelection()[0];

                    Ext.Ajax.request({
                        waitMsg: 'Please Wait',
                        url: m_api + '/farmcloud/user_check_in_congnito',
                        method: 'POST',
                        params: {
                            Username: sm.get('Username')
                        },
                        success: function(rp, o) {
                            var r = Ext.decode(rp.responseText);
                            if(r.success) {
                                var WinFormChangePassword = Ext.create('Koltiva.view.FarmCloud.UseraccManagement.WinFormChangePassword', {
                                    viewVar: {
                                        Username: sm.get('Username')
                                    }
                                });
                                if (!WinFormChangePassword.isVisible()) {
                                    WinFormChangePassword.center();
                                    WinFormChangePassword.show();
                                } else {
                                    WinFormChangePassword.close();
                                }
                            } else {
                                Ext.MessageBox.show({
                                    title: 'Error',
                                    msg: r.message,
                                    buttons: Ext.MessageBox.OK,
                                    animateTarget: 'mb9',
                                    icon: 'ext-mb-error'
                                });
                            }
                        },
                        failure: function(rp, o) {
                            Ext.MessageBox.hide();
                            try {
                                var r = Ext.decode(rp.responseText);
                                Ext.MessageBox.show({
                                    title: 'Error',
                                    msg: r.message,
                                    buttons: Ext.MessageBox.OK,
                                    animateTarget: 'mb9',
                                    icon: 'ext-mb-error'
                                });
                            }
                            catch(err) {
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
                }
            },{
                icon: varjs.config.base_url + 'images/icons/new/update.png',
                text: lang('Enable Account'),
                cls:'Sfr_BtnConMenuWhite',
                itemId: 'UseraccManagement.MainGrid.EnableAcc',
                hidden: m_act_update,
	            handler: function() {
                    var sm = Ext.getCmp('Koltiva.view.FarmCloud.UseraccManagement.MainGrid-Grid').getSelectionModel().getSelection()[0];

                    Ext.MessageBox.show({
                        msg: lang('Please wait'),
                        progressText: lang('Loading'),
                        width: 300,
                        wait: true,
                        waitConfig: {
                            interval: 200
                        },
                        icon: 'ext-mb-info', //custom class in msg-box.html
                        animateTarget: 'mb9'
                    });


                    //Cek apakah create baru atau linked
                    Ext.Ajax.request({
                        waitMsg: 'Please Wait',
                        url: m_api + '/farmcloud/enable_account',
                        method: 'POST',
                        params: {
                            FarmerID: sm.get('FarmerID'),
                            Username: sm.get('Username')
                        },
                        success: function(rp, o) {
                            Ext.MessageBox.hide();
                            var r = Ext.decode(rp.responseText);

                            Ext.MessageBox.show({
                                title: lang('Information'),
                                msg: r.message,
                                buttons: Ext.MessageBox.OK,
                                animateTarget: 'mb9',
                                icon: 'ext-mb-success'
                            });

                            thisObj.StoreGridMain.load();
                        },
                        failure: function(rp, o) {
                            Ext.MessageBox.hide();
                            try {
                                var r = Ext.decode(rp.responseText);
                                Ext.MessageBox.show({
                                    title: 'Error',
                                    msg: r.message,
                                    buttons: Ext.MessageBox.OK,
                                    animateTarget: 'mb9',
                                    icon: 'ext-mb-error'
                                });
                            }
                            catch(err) {
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

                }
            },{
                icon: varjs.config.base_url + 'images/icons/new/update.png',
                text: lang('Disable Account'),
                cls:'Sfr_BtnConMenuWhite',
                itemId: 'UseraccManagement.MainGrid.DisableAcc',
                hidden: m_act_update,
	            handler: function() {
                    var sm = Ext.getCmp('Koltiva.view.FarmCloud.UseraccManagement.MainGrid-Grid').getSelectionModel().getSelection()[0];

                    Ext.MessageBox.show({
                        msg: lang('Please wait'),
                        progressText: lang('Loading'),
                        width: 300,
                        wait: true,
                        waitConfig: {
                            interval: 200
                        },
                        icon: 'ext-mb-info', //custom class in msg-box.html
                        animateTarget: 'mb9'
                    });


                    //Cek apakah create baru atau linked
                    Ext.Ajax.request({
                        waitMsg: 'Please Wait',
                        url: m_api + '/farmcloud/disable_account',
                        method: 'POST',
                        params: {
                            FarmerID: sm.get('FarmerID'),
                            Username: sm.get('Username')
                        },
                        success: function(rp, o) {
                            Ext.MessageBox.hide();
                            var r = Ext.decode(rp.responseText);

                            Ext.MessageBox.show({
                                title: lang('Information'),
                                msg: r.message,
                                buttons: Ext.MessageBox.OK,
                                animateTarget: 'mb9',
                                icon: 'ext-mb-success'
                            });

                            thisObj.StoreGridMain.load();
                        },
                        failure: function(rp, o) {
                            Ext.MessageBox.hide();
                            try {
                                var r = Ext.decode(rp.responseText);
                                Ext.MessageBox.show({
                                    title: 'Error',
                                    msg: r.message,
                                    buttons: Ext.MessageBox.OK,
                                    animateTarget: 'mb9',
                                    icon: 'ext-mb-error'
                                });
                            }
                            catch(err) {
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
                }
            },{
                icon: varjs.config.base_url + 'images/icons/new/delete.png',
                text: lang('Delete Account'),
                cls:'Sfr_BtnConMenuWhite',
                itemId: 'UseraccManagement.MainGrid.DeleteAcc',
                hidden: m_act_delete,
                handler: function(){
                    var sm = Ext.getCmp('Koltiva.view.FarmCloud.UseraccManagement.MainGrid-Grid').getSelectionModel().getSelection()[0];

                    Ext.MessageBox.show({
                        msg: lang('Please wait'),
                        progressText: lang('Loading'),
                        width: 300,
                        wait: true,
                        waitConfig: {
                            interval: 200
                        },
                        icon: 'ext-mb-info', //custom class in msg-box.html
                        animateTarget: 'mb9'
                    });


                    //Cek apakah create baru atau linked
                    Ext.Ajax.request({
                        waitMsg: 'Please Wait',
                        url: m_api + '/farmcloud/delete_account',
                        method: 'POST',
                        params: {
                            FarmerID: sm.get('FarmerID'),
                            Username: sm.get('Username')
                        },
                        success: function(rp, o) {
                            Ext.MessageBox.hide();
                            var r = Ext.decode(rp.responseText);

                            Ext.MessageBox.show({
                                title: lang('Information'),
                                msg: r.message,
                                buttons: Ext.MessageBox.OK,
                                animateTarget: 'mb9',
                                icon: 'ext-mb-success'
                            });

                            thisObj.StoreGridMain.load();
                        },
                        failure: function(rp, o) {
                            Ext.MessageBox.hide();
                            try {
                                var r = Ext.decode(rp.responseText);
                                Ext.MessageBox.show({
                                    title: 'Error',
                                    msg: r.message,
                                    buttons: Ext.MessageBox.OK,
                                    animateTarget: 'mb9',
                                    icon: 'ext-mb-error'
                                });
                            }
                            catch(err) {
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

                }
            }]
        });

        thisObj.items = [{
            xtype: 'grid',
            id: 'Koltiva.view.FarmCloud.UseraccManagement.MainGrid-Grid',
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
                    text: lang('Register New Account'),
                    hidden: m_act_add,
                    cls: 'Sfr_BtnGridGreen',
                    overCls: 'Sfr_BtnGridGreen-Hover',
                    handler: function () {
                        var WinFormRegisAccount = Ext.create('Koltiva.view.FarmCloud.UseraccManagement.WinFormRegisAccount',{
                            viewVar:{
                                OpsiDisplay: 'insert',
                                CallerStore: thisObj.StoreGridMain
                            }
                        });
    
                        if (!WinFormRegisAccount.isVisible()) {
                            WinFormRegisAccount.center();
                            WinFormRegisAccount.show();
                        } else {
                            WinFormRegisAccount.close();
                        }
                    }
                },{
                    xtype: 'button',
                    icon: varjs.config.base_url + 'images/icons/new/export.png',
                    text: lang('Export'),
                    hidden: m_act_export,
                    cls: 'Sfr_BtnGridPaleBlue',
                    overCls: 'Sfr_BtnGridPaleBlue-Hover',
                    handler: function () {

                            Ext.MessageBox.show({
                                msg: 'Please wait...',
                                progressText: 'Exporting...',
                                width: 300,
                                wait: true,
                                waitConfig: {
                                    interval: 200
                                },
                                icon: 'ext-mb-info', //custom class in msg-box.html
                                animateTarget: 'mb9'
                            });

                            Ext.Ajax.request({
                                url: m_api + '/farmcloud/useracc_grid_export',
                                method: 'POST',
                                waitMsg: lang('Please Wait'),
                                params: {
                                    KeySearch: Ext.getCmp('Koltiva.view.FarmCloud.UseraccManagement.MainGrid-TxtSearchNama').getValue()
                                },
                                success: function (data) {
                                    Ext.MessageBox.hide();
                                    var jsonResp = JSON.parse(data.responseText);
                                    window.location = jsonResp.filenya;
                                },
                                failure: function () {
                                    Ext.MessageBox.hide();
                                    Ext.MessageBox.show({
                                        title: 'Notifications',
                                        msg: 'Failed to Export, Please Try Again.',
                                        buttons: Ext.MessageBox.OK,
                                        animateTarget: 'mb9',
                                        icon: 'ext-mb-error'
                                    });
                                }
                            });
                        }
                }, {
                    xtype: 'tbspacer',
                    flex: 1
                },{
                    name: 'Koltiva.view.FarmCloud.UseraccManagement.MainGrid-TxtSearchNama',
                    id: 'Koltiva.view.FarmCloud.UseraccManagement.MainGrid-TxtSearchNama',
                    xtype: 'textfield',
                    baseCls: 'Sfr_TxtfieldSearchGrid',
                    width: 400,
                    emptyText: lang('Cari berdasar nama/ID'),
                    listeners: {
                        specialkey: thisObj.submitOnEnterGrid
                    }
                },{
                    xtype: 'button',
                    icon: varjs.config.base_url + 'images/icons/silk/search.png',
                    text: lang('Search'),
                    cls: 'Sfr_BtnGridBlue',
                    overCls: 'Sfr_BtnGridBlue-Hover',
                    handler: function () {
                        thisObj.StoreGridMain.storeVar.KeySearch = Ext.getCmp('Koltiva.view.FarmCloud.UseraccManagement.MainGrid-TxtSearchNama').getValue();
                        thisObj.StoreGridMain.loadPage(1);
                    }
                }]
            }],
            columns:[{
            	text: '',
                xtype:'actioncolumn',
                width:'2%',
                items:[{
                    icon: varjs.config.base_url + 'images/icons/new/action.png',
                    handler: function(grid, rowIndex, colIndex, item, e, record) {
                        thisObj.ContextMenuGrid.showAt(e.getXY());

                        let sm = record;
                        if(sm.data.StatusAccount == 'Active') {
                            thisObj.ContextMenuGrid.getComponent('UseraccManagement.MainGrid.EnableAcc').setDisabled(true);
                            thisObj.ContextMenuGrid.getComponent('UseraccManagement.MainGrid.DisableAcc').setDisabled(false);
                            thisObj.ContextMenuGrid.getComponent('UseraccManagement.MainGrid.DeleteAcc').setDisabled(true);
                        } else {
                            thisObj.ContextMenuGrid.getComponent('UseraccManagement.MainGrid.EnableAcc').setDisabled(false);
                            thisObj.ContextMenuGrid.getComponent('UseraccManagement.MainGrid.DisableAcc').setDisabled(true);
                            thisObj.ContextMenuGrid.getComponent('UseraccManagement.MainGrid.DeleteAcc').setDisabled(false);
                        }
                    }
                }]
            },
            {
                 text: 'No',
                 xtype: 'rownumberer',
                 width:'3%'
            },{
            	text: lang('ID'),
                dataIndex: 'FarmerID',
                width: '7%'
            },{
            	text: lang('Name'),
                dataIndex: 'MemberName',
                width: '13%'
            },{
            	text: lang('Gender'),
                dataIndex: 'Gender',
                width: '8%',
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
            },{
            	text: lang('Group Name'),
                dataIndex: 'GroupName',
                width: '10%'
            },{
            	text: lang('Disctrict'),
                dataIndex: 'District',
                width: '8%'
            },{
            	text: lang('Sub Disctrict'),
                dataIndex: 'SubDistrict',
                width: '8%'
            },{
            	text: lang('Partner'),
                dataIndex: 'Partner',
                width: '9%'
            },{
                text: lang('Username'),
                dataIndex: 'Username',
                width: '6%'
            },{
            	text: lang('Email'),
                dataIndex: 'Email',
                width: '10%'
            },{
            	text: lang('Handphone'),
                dataIndex: 'HandPhone',
                width: '8%'
            },{
                text: lang('Status Account'),
                dataIndex: 'StatusAccount',
                width: '7%'
            }]
        }];

        this.callParent(arguments);
    },
    submitOnEnterGrid: function (field, event) {
        if (event.getKey() == event.ENTER) {
            Ext.getCmp('Koltiva.view.FarmCloud.UseraccManagement.MainGrid-Grid').getStore().storeVar.KeySearch = Ext.getCmp('Koltiva.view.FarmCloud.UseraccManagement.MainGrid-TxtSearchNama').getValue();
            Ext.getCmp('Koltiva.view.FarmCloud.UseraccManagement.MainGrid-Grid').getStore().loadPage(1);
        }
    }
});
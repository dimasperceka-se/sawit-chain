Ext.define('Koltiva.view.ImportGardens.MainGrid', {
    extend: 'Ext.panel.Panel',
    id: 'Koltiva.view.ImportGardens.MainGrid',
    renderTo: 'ext-content',
    style: 'padding:0 7px 7px 7px;margin:12px 0 0 0;',
    listeners: {
        afterRender: function (component, eOpts) {
            var thisObj = this;
        }
    },
    GridFarmerValid: true,
    initComponent: function () {
        var thisObj = this;
        thisObj.StoreGridMain = Ext.create('Koltiva.store.Tools.ImportGardenMainGrid');

        thisObj.items = [
            {
                xtype: 'grid',
                id: 'Koltiva.view.ImportGardens.MainGrid-MainGrid',
                style: 'border:1px solid #CCC;margin-top:4px;',
                cls: 'Sfr_GridNew',
                loadMask: true,
                selType: 'rowmodel',
                store: thisObj.StoreGridMain,
                viewConfig: {
                   deferEmptyText: false,
                   emptyText: '<center>' + lang("No Data Available") + '</center',
                },
                dockedItems: [
                    {
                        xtype: 'pagingtoolbar',
                        store: thisObj.StoreGridMain,
                        dock: 'bottom',
                        displayInfo: true,
                        displayMsg: 'Showing {0} to {1} of {2} entries'
                    }, {
                        xtype: 'toolbar',
                        dock: 'top',
                        items: [{
                                xtype: 'button',
                                icon: varjs.config.base_url + 'images/icons/new/export.png', cls:'Sfr_BtnGridPaleBlue',
                                text: lang('Download Template'),
                                cls: 'Sfr_BtnGridGreen',
                                overCls: 'Sfr_BtnGridGreen-Hover',
                                handler: function () {
                                    window.open(varjs.config.base_url + '/api/files/template-import-gardens-palm.xlsx');
                                }
                            }, {
                                xtype: 'form',
                                fileUpload: true,
                                id: 'Koltiva.view.ImportGardens.MainGrid-MainGrid-Form',
                                style: 'margin-top:3px;paddiimport_farmer_uploadng-top:5px;',
                                items: [{
                                        xtype: 'fileuploadfield',
                                        id: 'Koltiva.view.ImportGardens.MainGrid-MainGrid-Form-FileInput',
                                        name: 'Koltiva.view.ImportGardens.MainGrid-MainGrid-Form-FileInput',
                                        buttonText: 'Browse',
                                        cls: 'Sfr_FormBrowseBtn',
                                        hidden: m_act_import,
                                        listeners: {
                                            'change': function (fb, v) {
                                                Ext.MessageBox.confirm(lang('Message'), lang('Yakin mau mengupload data survei kebun baru? (Data temporary sebelumnnya akan di truncate)'), function (btn) {
                                                    if (btn == 'yes') {
                                                        Ext.getCmp('Koltiva.view.ImportGardens.MainGrid-MainGrid-Form').getForm().submit({
                                                            url: m_api + '/tools/import_garden_upload',
                                                            clientValidation: false,
                                                            waitMsg: 'Sending File',
                                                            success: function (rp, o) {
                                                                var r = Ext.decode(o.response.responseText);
                                                                Ext.MessageBox.show({
                                                                    title: 'Information',
                                                                    msg: lang(r.message),
                                                                    buttons: Ext.MessageBox.OK,
                                                                    animateTarget: 'mb9',
                                                                    icon: 'ext-mb-success'
                                                                });

                                                                //Load store
                                                                Ext.getCmp('Koltiva.view.ImportGardens.MainGrid').GridFarmerValid = true;
                                                                Ext.getCmp('Koltiva.view.ImportGardens.MainGrid-MainGrid').getStore().loadPage(1);
                                                            },
                                                            failure: function (rp, o) {
                                                                try {
                                                                    var r = Ext.decode(o.response.responseText);
                                                                    Ext.MessageBox.show({
                                                                        title: 'Error',
                                                                        msg: lang(r.message),
                                                                        buttons: Ext.MessageBox.OK,
                                                                        animateTarget: 'mb9',
                                                                        icon: 'ext-mb-error'
                                                                    });
                                                                } catch (err) {
                                                                    Ext.MessageBox.show({
                                                                        title: 'Error',
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
                                        }
                                    }]
                            }, {
                                xtype: 'button',
                                icon: varjs.config.base_url + 'images/icons/new/add.png',
                                text: lang('Import Garden'),
                                cls: 'Sfr_BtnGridBlue',
                                overCls: 'Sfr_BtnGridBlue-Hover',
                                hidden: m_act_import,
                                handler: function () {
                                    //alert(Ext.getCmp('Koltiva.view.Tools.ImportFarmerMainGrid').GridFarmerValid);
                                    Ext.MessageBox.confirm(lang('Message'), lang('Anda yakin akan import data pada grid dibawah ini?'), function (btn) {
                                        if (btn == 'yes') {
                                            if (Ext.getCmp('Koltiva.view.ImportGardens.MainGrid').GridFarmerValid == true) {

                                                Ext.MessageBox.show({
                                                    msg: lang('Please wait...'),
                                                    progressText: lang('Importing...'),
                                                    width: 300,
                                                    wait: true,
                                                    waitConfig: {
                                                        interval: 200
                                                    },
                                                    icon: 'ext-mb-info', //custom class in msg-box.html
                                                    animateTarget: 'mb9'
                                                });

                                                Ext.Ajax.request({
                                                    url: m_api + '/tools/import_garden',
                                                    method: 'POST',
                                                    success: function (rp, o) {
                                                        Ext.MessageBox.hide();
                                                        var r = Ext.decode(rp.responseText);
                                                        Ext.MessageBox.show({
                                                            title: 'Information',
                                                            msg: lang(r.message),
                                                            buttons: Ext.MessageBox.OK,
                                                            animateTarget: 'mb9',
                                                            icon: 'ext-mb-success'
                                                        });

                                                        console.log(r);

                                                        //Load store
                                                        Ext.getCmp('Koltiva.view.ImportGardens.MainGrid-MainGrid').getStore().loadPage(1);
                                                    },
                                                    failure: function (rp, o) {
                                                        Ext.MessageBox.hide();
                                                        try {
                                                            var r = Ext.decode(rp.responseText);
                                                            Ext.MessageBox.show({
                                                                title: 'Error',
                                                                msg: lang(r.message),
                                                                buttons: Ext.MessageBox.OK,
                                                                animateTarget: 'mb9',
                                                                icon: 'ext-mb-error'
                                                            });
                                                        } catch (err) {
                                                            Ext.MessageBox.show({
                                                                title: 'Error',
                                                                msg: lang('Connection Error'),
                                                                buttons: Ext.MessageBox.OK,
                                                                animateTarget: 'mb9',
                                                                icon: 'ext-mb-error'
                                                            });
                                                        }
                                                        console.log(r);
                                                    }
                                                });
                                            } else {
                                                Ext.MessageBox.show({
                                                    title: lang('Information'),
                                                    msg: lang('Masih ada data yang tidak valid'),
                                                    buttons: Ext.MessageBox.OK,
                                                    animateTarget: 'mb9',
                                                    icon: 'ext-mb-info'
                                                });
                                            }
                                        }
                                    });
                                }
                            }, {
                                xtype: 'tbspacer',
                                flex: 1
                            }, {
                                name: 'key', baseCls:'Sfr_TxtfieldSearchGrid',
                                id: 'Koltiva.view.ImportGardens.MainGrid-TextSearch',
                                xtype: 'textfield',
                                baseCls: 'Sfr_TxtfieldSearchGrid',
                                width: 400,
                                emptyText: lang('Cari berdasar nama/ID') + ', ' + lang('press_enter_search'),
                                listeners: {
                                    specialkey: thisObj.submitOnEnterGrid
                                }
                            }, {
                                xtype: 'button',
                                icon: varjs.config.base_url + 'images/icons/new/reload.png',
                                cls: 'Sfr_BtnGridBlue',
                                overCls: 'Sfr_BtnGridBlue-Hover',
                                handler: function () {
                                    Ext.getCmp('Koltiva.view.ImportGardens.MainGrid-MainGrid').getStore().loadPage(1);
                                }
                            }]
                }],
                columns: [
                    {
                        text: 'No',
                        xtype: 'rownumberer',
                        flex: 0.3
                    }, {
                        text: lang('Member ID'),
                        dataIndex: 'MemberID',
                        flex: 1,
                        renderer: function (value) {
                            var RetVal;

                            if (value != null && value != '' && value  != 'NotValid') {
                                RetVal = value;
                            } else {
                                RetVal = '<span style="color:red;font-weight:bold;">Not Valid</span>';
                                Ext.getCmp('Koltiva.view.ImportGardens.MainGrid').GridFarmerValid = false;
                            }

                            return RetVal;
                        }
                    }, {
                        text: lang('Plot Number'),
                        dataIndex: 'PlotNr',
                        flex: 1,
                        renderer: function (value) {
                            var RetVal;

                            if (value != null && value != '' && value  != 'NotValid') {
                                RetVal = value;
                            } else {
                                RetVal = '<span style="color:red;font-weight:bold;">Not Valid</span>';
                                Ext.getCmp('Koltiva.view.ImportGardens.MainGrid').GridFarmerValid = false;
                            }

                            return RetVal;
                        }
                    }, {
                        text: lang('Survey Nr'),
                        dataIndex: 'SurveyNr',
                        flex: 1,
                        renderer: function (value) {
                            var RetVal;

                            if (value != null && value != '' && value  != 'NotValid') {
                                RetVal = value;
                            } else {
                                RetVal = '<span style="color:red;font-weight:bold;">Not Valid</span>';
                                Ext.getCmp('Koltiva.view.ImportGardens.MainGrid').GridFarmerValid = false;
                            }

                            return RetVal;
                        }
                    }, {
                        text: lang('Latitude'),
                        dataIndex: 'Latitude',
                        flex: 1,
                        renderer: function (value) {
                            var RetVal;

                            if (value != null && value != '' && !isNaN(value) && value  != 'NotValid') {
                                RetVal = value;
                            } else {
                                RetVal = '<span style="color:red;font-weight:bold;">Not Valid</span>';
                                Ext.getCmp('Koltiva.view.ImportGardens.MainGrid').GridFarmerValid = false;
                            }

                            return RetVal;
                        }
                    }, {
                        text: lang('Longitude'),
                        dataIndex: 'Longitude',
                        flex: 1,
                        renderer: function (value) {
                            var RetVal;

                            if (value != null && value != '' && !isNaN(value) && value  != 'NotValid') {
                                RetVal = value;
                            } else {
                                RetVal = '<span style="color:red;font-weight:bold;">Not Valid</span>';
                                Ext.getCmp('Koltiva.view.ImportGardens.MainGrid').GridFarmerValid = false;
                            }

                            return RetVal;
                        }
                    }, {
                        text: lang('Garden Area (Ha)'),
                        dataIndex: 'GardenAreaHa',
                        flex: 1,
                        renderer: function (value) {
                            var RetVal;

                            if (value != null && value != '' && !isNaN(value) && value  != 'NotValid') {
                                RetVal = value;
                            } else {
                                RetVal = '<span style="color:red;font-weight:bold;">Not Valid</span>';
                                Ext.getCmp('Koltiva.view.ImportGardens.MainGrid').GridFarmerValid = false;
                            }

                            return RetVal;
                        }
                    }]
            }];

        this.callParent(arguments);
    },
    submitOnEnterGrid: function (field, event) {
        if (event.getKey() === event.ENTER) {
            Ext.getCmp('Koltiva.view.ImportGardens.MainGrid-MainGrid').getStore().loadPage(1);
        }
    }
});
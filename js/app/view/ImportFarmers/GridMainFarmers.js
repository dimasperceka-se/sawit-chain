/*
 * @Author: fikri.fauzul
 * @Date:   2019-09-04 16:20:51
 */
/* global Ext, varjs, m_api */

Ext.define('Koltiva.view.ImportFarmers.GridMainFarmers', {
    extend: 'Ext.panel.Panel',
    id: 'Koltiva.view.ImportFarmers.GridMainFarmers',
    renderTo: 'ext-content',
    style: 'padding:0 7px 7px 7px;margin:12px 0 0 0;',
    listeners: {
        afterRender: function (component, eOpts) {
            var thisObj = this;
//            Ext.getCmp('view.ImportFarmers.GridMainFarmers-MainGrid').getStore().load();
        }
    },
    GridFarmerValid: true,
    initComponent: function () {
        var thisObj = this;
        thisObj.StoreGridMain = Ext.create('Koltiva.store.Tools.ImportFarmerMainGrid');

        thisObj.items = [
            {
                xtype: 'grid',
                id: 'Koltiva.view.ImportFarmers.GridMainFarmers-MainGrid',
                style: 'border:1px solid #CCC;margin-top:4px;',
                cls: 'Sfr_GridNew',
                loadMask: true,
                selType: 'rowmodel',
                store: thisObj.StoreGridMain,
//                viewConfig: {
//                    deferEmptyText: false,
//                    emptyText: GetDefaultContentNoData()
//                },
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
                                    window.open(varjs.config.base_url + '/api/files/template-import-farmers-palm.xlsx');
                                }
                            }, {
                                xtype: 'form',
                                fileUpload: true,
                                id: 'Koltiva.view.ImportFarmers.GridMainFarmers-MainGrid-Form',
                                style: 'margin-top:3px;paddiimport_farmer_uploadng-top:5px;',
                                items: [{
                                        xtype: 'fileuploadfield',
                                        id: 'Koltiva.view.ImportFarmers.GridMainFarmers-MainGrid-Form-FileInput',
                                        name: 'Koltiva.view.ImportFarmers.GridMainFarmers-MainGrid-Form-FileInput',
                                        buttonText: 'Browse',
                                        cls: 'Sfr_FormBrowseBtn',
                                        listeners: {
                                            'change': function (fb, v) {
                                                Ext.MessageBox.confirm('Message', 'Yakin mau mengupload data calon farmer baru? (Data temporary sebelumnnya akan di truncate)', function (btn) {
                                                    if (btn == 'yes') {
                                                        Ext.getCmp('Koltiva.view.ImportFarmers.GridMainFarmers-MainGrid-Form').getForm().submit({
                                                            url: m_api + '/tools/import_farmer_upload',
                                                            clientValidation: false,
                                                            waitMsg: 'Sending File',
                                                            success: function (rp, o) {
                                                                var r = Ext.decode(o.response.responseText);
                                                                Ext.MessageBox.show({
                                                                    title: 'Information',
                                                                    msg: r.message,
                                                                    buttons: Ext.MessageBox.OK,
                                                                    animateTarget: 'mb9',
                                                                    icon: 'ext-mb-success'
                                                                });

                                                                //Load store
                                                                Ext.getCmp('Koltiva.view.ImportFarmers.GridMainFarmers').GridFarmerValid = true;
                                                                Ext.getCmp('Koltiva.view.ImportFarmers.GridMainFarmers-MainGrid').getStore().loadPage(1);
                                                            },
                                                            failure: function (rp, o) {
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
                                                    }
                                                });
                                            }
                                        }
                                    }]
                            }, {
                                xtype: 'button',
                                icon: varjs.config.base_url + 'images/icons/new/add.png',
                                text: lang('Import Farmer'),
                                cls: 'Sfr_BtnGridBlue',
                                overCls: 'Sfr_BtnGridBlue-Hover',
                                handler: function () {
                                    //alert(Ext.getCmp('Koltiva.view.Tools.ImportFarmerMainGrid').GridFarmerValid);
                                    Ext.MessageBox.confirm('Message', 'Yakin mau import semua calon petani didalam grid dibawah ini?', function (btn) {
                                        if (btn == 'yes') {
                                            if (Ext.getCmp('Koltiva.view.ImportFarmers.GridMainFarmers').GridFarmerValid == true) {

                                                Ext.MessageBox.show({
                                                    msg: 'Please wait...',
                                                    progressText: 'Importing...',
                                                    width: 300,
                                                    wait: true,
                                                    waitConfig: {
                                                        interval: 200
                                                    },
                                                    icon: 'ext-mb-info', //custom class in msg-box.html
                                                    animateTarget: 'mb9'
                                                });

                                                Ext.Ajax.request({
                                                    url: m_api + '/tools/import_farmer',
                                                    method: 'POST',
                                                    success: function (rp, o) {
                                                        Ext.MessageBox.hide();
                                                        var r = Ext.decode(rp.responseText);
                                                        Ext.MessageBox.show({
                                                            title: 'Information',
                                                            msg: r.message,
                                                            buttons: Ext.MessageBox.OK,
                                                            animateTarget: 'mb9',
                                                            icon: 'ext-mb-success'
                                                        });

                                                        //Load store
                                                        Ext.getCmp('Koltiva.view.ImportFarmers.GridMainFarmers-MainGrid').getStore().loadPage(1);
                                                    },
                                                    failure: function (rp, o) {
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
                                                    title: 'Information',
                                                    msg: lang('Masih ada data calon farmer yg tidak valid'),
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
                                id: 'Koltiva.view.ImportFarmers.GridMainFarmers-TextSearch',
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
                                    Ext.getCmp('Koltiva.view.ImportFarmers.GridMainFarmers-MainGrid').getStore().loadPage(1);
                                }
                            }]
                    }],
                columns: [
                    {
                        text: 'No',
                        xtype: 'rownumberer',
                        width: '3%'
                    }, {
                        text: lang('FarmerName'),
                        dataIndex: 'FarmerName',
                        flex: 2,
                        renderer: function (value) {
                            var RetVal;

                            if (value != null && value != '' && value != '0') {
                                RetVal = value;
                            } else {
                                RetVal = '<span style="color:red;font-weight:bold;">NotValid</span>';
                                Ext.getCmp('Koltiva.view.ImportFarmers.GridMainFarmers').GridFarmerValid = false;
                            }

                            return RetVal;
                        }
                    }, {
                        text: lang('Birthdate'),
                        dataIndex: 'Birthdate',
                        flex: 1,
                        renderer: function (value) {
                            var RetVal;

                            if (value != null && value != '' && value != '0' && value  != 'NotValid') {
                                if (value != '0000-00-00') {
                                    RetVal = value;
                                } else {
                                    RetVal = '<span style="color:red;font-weight:bold;">NotValid</span>';
                                    Ext.getCmp('Koltiva.view.ImportFarmers.GridMainFarmers').GridFarmerValid = false;
                                }
                            } else {
                                RetVal = '<span style="color:red;font-weight:bold;">NotValid</span>';
                                Ext.getCmp('Koltiva.view.ImportFarmers.GridMainFarmers').GridFarmerValid = false;
                            }

                            return RetVal;
                        }
                    }, {
                        text: lang('Gender'),
                        dataIndex: 'Gender',
                        flex: 1,
                        renderer: function (value) {
                            var RetVal;

                            if (value != null && value != '' && value != '0') {
                                if (value == 'Male' || value == 'Female') {
                                    RetVal = value;
                                } else {
                                    RetVal = '<span style="color:red;font-weight:bold;">NotValid</span>';
                                    Ext.getCmp('Koltiva.view.ImportFarmers.GridMainFarmers').GridFarmerValid = false;
                                }
                            } else {
                                RetVal = '<span style="color:red;font-weight:bold;">NotValid</span>';
                                Ext.getCmp('Koltiva.view.ImportFarmers.GridMainFarmers').GridFarmerValid = false;
                            }

                            return RetVal;
                        }
                    }, {
                        text: lang('Village'),
                        dataIndex: 'Village',
                        flex: 1,
                        renderer: function (value) {
                            var RetVal;

                            if (value != null && value != '' && value != '0' && value  != 'NotValid') {
                                RetVal = value;
                            } else {
                                RetVal = '<span style="color:red;font-weight:bold;">NotValid</span>';
                                Ext.getCmp('Koltiva.view.ImportFarmers.GridMainFarmers').GridFarmerValid = false;
                            }

                            return RetVal;
                        }
                    }, {
                        text: lang('PartnerName'),
                        dataIndex: 'PartnerName',
                        flex: 1,
                        renderer: function (value) {
                            var RetVal;

                            if (value != null && value != '' && value != '0' && value  != 'NotValid') {
                                RetVal = value;
                            } else {
                                RetVal = '<span style="color:red;font-weight:bold;">NotValid</span>';
                                Ext.getCmp('Koltiva.view.ImportFarmers.GridMainFarmers').GridFarmerValid = false;
                            }

                            return RetVal;
                        }
                    }]
            }];

        this.callParent(arguments);
    },
    submitOnEnterGrid: function (field, event) {
        if (event.getKey() === event.ENTER) {
            Ext.getCmp('Koltiva.view.ImportFarmers.GridMainFarmers-MainGrid').getStore().loadPage(1);
        }
    }
});
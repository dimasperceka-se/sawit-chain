/******************************************
 *  Author : fikri
 *******************************************/

// Define Variabel2 / Object2 / Fungsi yg diperlukan oleh view ini (begin)
// IMSID
// Define Variabel2 / Object2 / Fungsi yg diperlukan oleh view ini (end)

Ext.define('Koltiva.view.IMS.WinFormCoachingMapping', {
    extend: 'Ext.window.Window',
    id: 'Koltiva.view.IMS.WinFormCoachingMapping',
    title: lang('Add Farmer Coaching Mapping'),
    closable: true,
    modal: true,
    closeAction: 'destroy',
    width: '75%',
    height: '80%',
    viewVar: false,
    setViewVar: function (value) {
        this.viewVar = value;
    },
    setFormVar: function (value) {
        this.formVar = value;
    },
    overflowY: 'auto',
    initComponent: function () {
        var thisObj = this;

        //store --------------------------------------------------------------------------------------------------------------- (begin)
        thisObj.cmb_username = Ext.create('Koltiva.store.IMS.CmbUserCoachingMapping', {
            storeVar: {
                IMSID: thisObj.viewVar.IMSID
            }
        });

        thisObj.storeFarmer = Ext.create('Koltiva.store.IMS.GridCheckboxFarmer', {
            storeVar: {
                IMSID: thisObj.viewVar.IMSID
            }
        });
        //store --------------------------------------------------------------------------------------------------------------- (end)


        //items ---------------------------------------------------------------------------------------------------------------------------- (begin)
        thisObj.items = [{
                xtype: 'form',
                id: 'Koltiva.view.IMS.WinFormCoachingMapping-Form',
                columnWidth: 1,
                padding: '5 25 5 25',
                items: [{
                        layout: 'column',
                        border: false,
                        items: [{
                                columnWidth: 1,
                                layout: 'form',
                                style: '',
                                items: [{
                                        layout: 'column',
                                        border: false,
                                        items: [{
                                                columnWidth: 1,
                                                style: 'padding-right:25px;',
                                                layout: 'form',
                                                items: [{
                                                        xtype: 'combobox',
                                                        id: 'Koltiva.view.IMS.WinFormCoachingMapping-Form-UserName',
                                                        name: 'Koltiva.view.IMS.WinFormCoachingMapping-Form-UserName',
                                                        fieldLabel: lang('Username'),
                                                        store: thisObj.cmb_username,
                                                        allowBlank: false,
                                                        queryMode: 'local',
                                                        displayField: 'label',
                                                        valueField: 'id',
                                                        listeners: {
                                                            change: function (cb, nv, ov) {
                                                                thisObj.storeFarmer.load({
                                                                    params: {
                                                                        UserName: nv
                                                                    }
                                                                });
                                                            }
                                                        }
                                                    }]
                                            }]
                                    }]
                            }, {
                                columnWidth: 1,
                                layout: 'form',
                                style: '',
                                items: [{
                                        layout: 'column',
                                        border: false,
                                        items: [{
                                                columnWidth: 1,
                                                layout: 'form',
                                                style: 'padding-right:25px;',
                                                items: [{
                                                        xtype: 'panel',
                                                        margin: '12px 0 0 0',
                                                        items: [{
                                                                xtype: 'grid',
                                                                id: 'view.IMS.WinFormCoachingMapping-Form-GridFarmer',
                                                                style: 'border:1px solid #CCC;margin-top:4px;',
                                                                title: 'Farmer',
                                                                cls: 'Sfr_GridNew',
                                                                loadMask: true,
                                                                selType: 'checkboxmodel',
                                                                store: thisObj.storeFarmer,
                                                                viewConfig: {
                                                                    deferEmptyText: false,
                                                                    emptyText: lang('No data Available')
                                                                },
                                                                dockedItems: [{
                                                                        xtype: 'pagingtoolbar',
                                                                        store: thisObj.storeFarmer,
                                                                        dock: 'bottom',
                                                                        displayInfo: true,
                                                                        style: 'padding-right:12px;'
                                                                    }, {
                                                                        xtype: 'toolbar',
                                                                        dock: 'top',
                                                                        items: [{
                                                                                name: 'key',
                                                                                id: 'Koltiva.view.IMS.WinFormCoachingMapping-TextSearch',
                                                                                xtype: 'textfield',
                                                                                baseCls: 'Sfr_TxtfieldSearchGrid',
                                                                                width: 400,
                                                                                emptyText: lang('Cari berdasar nama/ID') + ', ' + lang('press_enter_search'),
                                                                                listeners: {
                                                                                    specialkey: function (f, e) {
                                                                                        if (e.getKey() == e.ENTER) {
                                                                                            thisObj.storeFarmer.load({
                                                                                                params: {
                                                                                                    UserName: Ext.getCmp('Koltiva.view.IMS.WinFormCoachingMapping-Form-UserName').getValue(),
                                                                                                    TextSearch: Ext.getCmp('Koltiva.view.IMS.WinFormCoachingMapping-TextSearch').getValue()
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
                                                                                    thisObj.storeFarmer.load({
                                                                                        params: {
                                                                                            UserName: Ext.getCmp('Koltiva.view.IMS.WinFormCoachingMapping-Form-UserName').getValue(),
                                                                                            TextSearch: Ext.getCmp('Koltiva.view.IMS.WinFormCoachingMapping-TextSearch').getValue()
                                                                                        }
                                                                                    });
                                                                                }
                                                                            }]
                                                                    }],
                                                                columns: [{
                                                                        HeaderCheckbox: true,
                                                                        dataIndex: 'CheckData',
                                                                        flex: 0.5
                                                                    }, {
                                                                        id: 'view.IMS.WinFormCoachingMapping-Form-GridFarmer-colIMSID',
                                                                        text: lang('IMSID'),
                                                                        hidden: true,
                                                                        dataIndex: 'IMSID'
                                                                    }, {
                                                                        id: 'view.IMS.WinFormCoachingMapping-Form-GridFarmer-colFarmeID',
                                                                        text: lang('Farmer ID'),
                                                                        width: '10%',
                                                                        dataIndex: 'FarmerID'
                                                                    }, {
                                                                        id: 'view.IMS.WinFormCoachingMapping-Form-GridFarmer-colFarmerName',
                                                                        text: lang('Farmer Name'),
                                                                        dataIndex: 'FarmerName',
                                                                        width: '25%'
                                                                    }, {
                                                                        id: 'view.IMS.WinFormCoachingMapping-Form-GridFarmer-colFarmerGroup',
                                                                        text: lang('Farmer Group'),
                                                                        dataIndex: 'FarmerGroup',
                                                                        width: '30%'
                                                                    }, {
                                                                        id: 'view.IMS.WinFormCoachingMapping-Form-GridFarmer-colVillage',
                                                                        text: lang('Village'),
                                                                        dataIndex: 'Village',
                                                                        width: '20%'
                                                                    }]
                                                            }]
                                                    }]
                                            }]
                                    }]
                            }]
                    }]
            }];
        //items ---------------------------------------------------------------------------------------------------------------------------- (end)


        //buttons -------------------------------------------------------------- (begin)
        thisObj.buttons = [{
                icon: varjs.config.base_url + 'images/icons/new/save.png',
                text: lang('Save'),
                margin: '5 15 5 5',
                cls: 'Sfr_BtnFormBlue',
                overCls: 'Sfr_BtnFormBlue-Hover',
                id: 'Koltiva.view.IMS.WinFormCoachingMapping-Form-BtnSave',
                handler: function () {
                    var FormNya = Ext.getCmp('Koltiva.view.IMS.WinFormCoachingMapping-Form').getForm();
                    var FormValidOrNot = FormNya.isValid();

                    var gridSelected = Ext.getCmp('view.IMS.WinFormCoachingMapping-Form-GridFarmer').getSelectionModel().getSelection();

                    var IdSelectedArr = [];
                    for (var i = gridSelected.length - 1; i >= 0; i--) {
                        IdSelectedArr.push(gridSelected[i].get('FarmerID'));
                    }

                    if (IdSelectedArr.length > 0) {
                        var FarmerIDSel = IdSelectedArr.join(',');
                        if (FormValidOrNot == true) {
                            Ext.MessageBox.confirm('Message', lang('Are you sure to Save this data?'), function (btn) {
                                FormNya.submit({
                                    url: m_api + '/ims_coaching/farmer_coaching_mapping',
                                    method: 'POST',
                                    params: {
                                        UserName: Ext.getCmp('Koltiva.view.IMS.WinFormCoachingMapping-Form-UserName').getValue(),
                                        FarmerIDSel: FarmerIDSel,
                                        IMSID: thisObj.viewVar.IMSID
                                    },
                                    waitMsg: 'Saving data...',
                                    success: function (fp, o) {
                                        Ext.MessageBox.show({
                                            title: 'Information',
                                            msg: lang('Data saved'),
                                            buttons: Ext.MessageBox.OK,
                                            animateTarget: 'mb9',
                                            icon: 'ext-mb-success'
                                        });

                                        //form reset
                                        FormNya.reset();

                                        //refresh page content
                                        Ext.getCmp('Koltiva.view.IMS.WinFarmerCoachingMapping-GridCoachingMapping').getStore().loadPage(1);

                                        //tutup popup
                                        thisObj.close();
                                    },
                                    failure: function (fp, o) {
                                        var pesanNya;
                                        if (o.result.message != undefined) {
                                            pesanNya = o.result.message;
                                        } else {
                                            pesanNya = lang('Connection error');
                                        }
                                        Ext.MessageBox.show({
                                            title: 'Attention',
                                            msg: pesanNya,
                                            buttons: Ext.MessageBox.OK,
                                            animateTarget: 'mb9',
                                            icon: 'ext-mb-error'
                                        });
                                    }
                                });
                            })
                        } else {
                            Ext.MessageBox.show({
                                title: lang('Attention'),
                                msg: lang('Form not valid yet'),
                                buttons: Ext.MessageBox.OK,
                                animateTarget: 'mb9',
                                icon: 'ext-mb-info'
                            });
                        }
                    }
                }
            }, {
                icon: varjs.config.base_url + 'images/icons/new/close.png',
                text: lang('Close'),
                cls: 'Sfr_BtnFormGrey',
                overCls: 'Sfr_BtnFormGrey-Hover',
                handler: function () {
                    thisObj.close();
                }
            }];
        //buttons -------------------------------------------------------------- (end)

        this.callParent(arguments);
    },
    listeners: {
        afterRender: function () {
            var thisObj = this;
        }
    }
});
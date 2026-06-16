/******************************************
 *  Author : n1colius.lau@gmail.com   
 *  Created On : Mon Oct 01 2018
 *  File : WinFormImsAssetRcpChBs.js
 *******************************************/

/*
    Param2 yg diperlukan ketika load View ini
    - IMSID
    - RcpID
    - OpsiDisplay
    - CallerStore
*/

Ext.define('Koltiva.view.IMS.WinFormImsAssetRcpChBs' ,{
    extend: 'Ext.window.Window',
    id: 'Koltiva.view.IMS.WinFormImsAssetRcpChBs',
    title: lang('IMS - Receipt Form (CH & BS)'),
    closable: true,
    modal: true,
    closeAction: 'destroy',
    width: '94%',
    height: '90%',
    overflowY: 'auto',
    viewVar: false,
    setViewVar: function(value){
        this.viewVar = value;
    },
    initComponent: function() {
        var thisObj = this;

        //======== Store ============================ (Begin)
        thisObj.StoreGridRcpItems = Ext.create('Koltiva.store.IMS.ImsAssetRcpGridRcpChBsItems');
        //======== Store ============================ (End)


        //======== ContextMenu ============================ (Begin)
        thisObj.ContextMenuGridRcpItems = Ext.create('Ext.menu.Menu', {
            cls: 'Sfr_ConMenu',
            items: [{
                    icon: varjs.config.base_url + 'images/icons/new/view.png',
                    text: lang('View'),
                    cls: 'Sfr_BtnConMenuWhite',
                    handler: function () {
                        var sm = Ext.getCmp('Koltiva.view.IMS.WinFormImsAssetRcpChBs-Form-GridRcpItems').getSelectionModel().getSelection()[0];
                        var WinFormImsAssetRcpChBsItem = Ext.create('Koltiva.view.IMS.WinFormImsAssetRcpChBsItem', {
                            viewVar: {
                                RcpID: Ext.getCmp('Koltiva.view.IMS.WinFormImsAssetRcpChBs-Form-RcpID').getValue(),
                                OpsiDisplay: 'view',
                                CallerStore: thisObj.StoreGridRcpItems,
                                CallerStoreMain: thisObj.viewVar.CallerStore,
                                IMSID: thisObj.viewVar.IMSID,
                                RcpItemID: sm.get('RcpItemID')
                            }
                        });
                        if (!WinFormImsAssetRcpChBsItem.isVisible()) {
                            WinFormImsAssetRcpChBsItem.center();
                            WinFormImsAssetRcpChBsItem.show();
                        } else {
                            WinFormImsAssetRcpChBsItem.close();
                        }
                    }
                }, {
                    icon: varjs.config.base_url + 'images/icons/new/update.png',
                    text: lang('Update'),
                    cls: 'Sfr_BtnConMenuWhite',
                    hidden: m_act_update,
                    handler: function () {
                        var sm = Ext.getCmp('Koltiva.view.IMS.WinFormImsAssetRcpChBs-Form-GridRcpItems').getSelectionModel().getSelection()[0];
                        var WinFormImsAssetRcpChBsItem = Ext.create('Koltiva.view.IMS.WinFormImsAssetRcpChBsItem', {
                            viewVar: {
                                RcpID: Ext.getCmp('Koltiva.view.IMS.WinFormImsAssetRcpChBs-Form-RcpID').getValue(),
                                OpsiDisplay: 'update',
                                CallerStore: thisObj.StoreGridRcpItems,
                                CallerStoreMain: thisObj.viewVar.CallerStore,
                                IMSID: thisObj.viewVar.IMSID,
                                RcpItemID: sm.get('RcpItemID')
                            }
                        });
                        if (!WinFormImsAssetRcpChBsItem.isVisible()) {
                            WinFormImsAssetRcpChBsItem.center();
                            WinFormImsAssetRcpChBsItem.show();
                        } else {
                            WinFormImsAssetRcpChBsItem.close();
                        }
                    }
                }, {
                    icon: varjs.config.base_url + 'images/icons/new/delete.png',
                    text: lang('Delete'),
                    cls: 'Sfr_BtnConMenuWhite',
                    hidden: m_act_delete,
                    handler: function () {
                        var sm = Ext.getCmp('Koltiva.view.IMS.WinFormImsAssetRcpChBs-Form-GridRcpItems').getSelectionModel().getSelection()[0];
                        Ext.MessageBox.confirm('Message', 'Do you want to delete this data ?', function (btn) {
                            if (btn == 'yes') {
                                Ext.Ajax.request({
                                    waitMsg: 'Please Wait',
                                    url: m_api + '/ims_asset_rcp/rcp_ch_bs_item',
                                    method: 'DELETE',
                                    params: {
                                        RcpItemID: sm.get('RcpItemID')
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
                                        thisObj.StoreGridRcpItems.load();
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
        //======== ContextMenu ============================ (End)        

        //items ---------------------------------------------------------------------------------------------------------------------------- (Begin)
        thisObj.items = [{
                xtype: 'form',
                id: 'Koltiva.view.IMS.WinFormImsAssetRcpChBs-Form',
                fileUpload: true,
                padding: '5 25 5 8',
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
                                                columnWidth: 0.495,
                                                style: 'padding-right:25px;',
                                                layout: 'form',
                                                defaults: {
                                                    labelWidth: 225,
                                                },
                                                items: [{
                                                        xtype: 'hiddenfield',
                                                        id: 'Koltiva.view.IMS.WinFormImsAssetRcpChBs-Form-RcpID',
                                                        name: 'Koltiva.view.IMS.WinFormImsAssetRcpChBs-Form-RcpID'
                                                    }, {
                                                        xtype: 'hiddenfield',
                                                        id: 'Koltiva.view.IMS.WinFormImsAssetRcpChBs-Form-IMSID',
                                                        name: 'Koltiva.view.IMS.WinFormImsAssetRcpChBs-Form-IMSID',
                                                        value: thisObj.viewVar.IMSID
                                                    }, {
                                                        xtype: 'textfield',
                                                        id: 'Koltiva.view.IMS.WinFormImsAssetRcpChBs-Form-RcpTransNumber',
                                                        name: 'Koltiva.view.IMS.WinFormImsAssetRcpChBs-Form-RcpTransNumber',
                                                        fieldLabel: lang('Transaction Number'),
                                                        allowBlank: false,
                                                        msgTarget: 'under'
                                                    }, {
                                                        xtype: 'datefield',
                                                        id: 'Koltiva.view.IMS.WinFormImsAssetRcpChBs-Form-RcpDate',
                                                        name: 'Koltiva.view.IMS.WinFormImsAssetRcpChBs-Form-RcpDate',
                                                        fieldLabel: lang('Receipt Date'),
                                                        allowBlank: false,
                                                        msgTarget: 'under',
                                                        format: 'Y-m-d'
                                                    }, {
                                                        xtype: 'textareafield',
                                                        id: 'Koltiva.view.IMS.WinFormImsAssetRcpChBs-Form-RcpRemark',
                                                        name: 'Koltiva.view.IMS.WinFormImsAssetRcpChBs-Form-RcpRemark',
                                                        fieldLabel: lang('Remark'),
                                                    }]
                                            }, {
                                                columnWidth: 0.5,
                                                layout: 'form',
                                                style: 'padding-left:15px;',
                                                defaults: {
                                                    labelWidth: 225,
                                                },
                                                items: [{
                                                        xtype: 'textfield',
                                                        id: 'Koltiva.view.IMS.WinFormImsAssetRcpChBs-Form-ReceiverName',
                                                        name: 'Koltiva.view.IMS.WinFormImsAssetRcpChBs-Form-ReceiverName',
                                                        fieldLabel: lang('Asset Receiver'),
                                                        allowBlank: false,
                                                        msgTarget: 'under'
                                                    }, {
                                                        xtype: 'textfield',
                                                        id: 'Koltiva.view.IMS.WinFormImsAssetRcpChBs-Form-CreatedByLabel',
                                                        name: 'Koltiva.view.IMS.WinFormImsAssetRcpChBs-Form-CreatedByLabel',
                                                        fieldLabel: lang('Created by'),
                                                        readOnly: true
                                                    }, {
                                                        xtype: 'textfield',
                                                        id: 'Koltiva.view.IMS.WinFormImsAssetRcpChBs-Form-ModifiedByLabel',
                                                        name: 'Koltiva.view.IMS.WinFormImsAssetRcpChBs-Form-ModifiedByLabel',
                                                        fieldLabel: lang('Modified by'),
                                                        readOnly: true
                                                    }]
                                            }]
                                    }, {
                                        xtype: 'gridpanel',
                                        title: lang('Receipt Items'),
                                        id: 'Koltiva.view.IMS.WinFormImsAssetRcpChBs-Form-GridRcpItems',
                                        style: 'border:1px solid #CCC;padding-right:3px;',
                                        store: thisObj.StoreGridRcpItems,
                                        width: '100%',
                                        loadMask: true,
                                        selType: 'rowmodel',
                                        viewConfig: {
                                            deferEmptyText: false,
                                            emptyText: lang('No Data Available')
                                        },
                                        dockedItems: [{
                                                xtype: 'toolbar',
                                                items: [{
                                                        xtype: 'button',
                                                        icon: varjs.config.base_url + 'images/icons/new/add.png',
                                                        margin: '0px 0px 0px 6px',
                                                        hidden: m_act_add,
                                                        id: 'Koltiva.view.IMS.WinFormImsAssetRcpChBs-Form-GridRcpItems-BtnAddItems',
                                                        cls: 'Sfr_BtnGridGreen',
                                                        overCls: 'Sfr_BtnGridGreen-Hover',
                                                        text: lang('Add Item'),
                                                        handler: function () {
                                                            if (Ext.getCmp('Koltiva.view.IMS.WinFormImsAssetRcpChBs-Form-RcpID').getValue() == "")
                                                                var CekID = null;
                                                            else
                                                                var CekID = true;
                                                            prosesCek = cekSaveDulu(CekID);
                                                            if (prosesCek == true) {
                                                                var WinFormImsAssetRcpChBsItem = Ext.create('Koltiva.view.IMS.WinFormImsAssetRcpChBsItem', {
                                                                    viewVar: {
                                                                        RcpID: Ext.getCmp('Koltiva.view.IMS.WinFormImsAssetRcpChBs-Form-RcpID').getValue(),
                                                                        OpsiDisplay: 'insert',
                                                                        CallerStore: thisObj.StoreGridRcpItems,
                                                                        CallerStoreMain: thisObj.viewVar.CallerStore,
                                                                        IMSID: thisObj.viewVar.IMSID
                                                                    }
                                                                });
                                                                if (!WinFormImsAssetRcpChBsItem.isVisible()) {
                                                                    WinFormImsAssetRcpChBsItem.center();
                                                                    WinFormImsAssetRcpChBsItem.show();
                                                                } else {
                                                                    WinFormImsAssetRcpChBsItem.close();
                                                                }
                                                            }
                                                        }
                                                    }]
                                            }],
                                        columns: [{
                                                dataIndex: 'RcpItemID',
                                                hidden: true
                                            }, {
                                                id: 'Koltiva.view.IMS.WinFormImsAssetRcpChBs-Form-GridRcpItems-ColActionColumn',
                                                text: lang('Action'),
                                                xtype: 'actioncolumn',
                                                width: '6%',
                                                items: [{
                                                        icon: varjs.config.base_url + 'images/icons/new/action.png',
                                                        tooltip: 'Action',
                                                        handler: function (grid, rowIndex, colIndex, item, e, record) {
                                                            thisObj.ContextMenuGridRcpItems.showAt(e.getXY());
                                                        }
                                                    }]
                                            }, {
                                                text: lang('Qty'),
                                                width: '4%',
                                                dataIndex: 'ItemQty'
                                            }, {
                                                text: lang('Item'),
                                                width: '20%',
                                                dataIndex: 'ItemLabel'
                                            }, {
                                                text: lang('Item Remark'),
                                                width: '15%',
                                                //dataIndex: 'ItemRemark',
                                                renderer: function (t, meta, record) {
                                                    var data = record.getData();
                                                    return '<div style="white-space: normal;">' + data.ItemRemark + '</div>';
                                                }
                                            }, {
                                                text: lang('User Type'),
                                                width: '5%',
                                                dataIndex: 'UserAssType'
                                            }, {
                                                text: lang('User Name'),
                                                flex: 1,
                                                dataIndex: 'UserAssLabel'
                                            }, {
                                                text: lang('User Location'),
                                                width: '15%',
                                                dataIndex: 'UserAssLocation'
                                            }, {
                                                text: lang('Remark'),
                                                flex: 1,
                                                //dataIndex: 'Remark'
                                                renderer: function (t, meta, record) {
                                                    var data = record.getData();
                                                    return '<div style="white-space: normal;">' + data.Remark + '</div>';
                                                }
                                            }]
                                    }, {
                                        html: '<br><div class="subtitleForm">' + lang('Additional Information') + '</div>'
                                    }, {
                                        layout: 'column',
                                        border: false,
                                        items: [{
                                                columnWidth: 0.495,
                                                style: 'padding-right:25px;',
                                                layout: 'form',
                                                defaults: {
                                                    labelWidth: 225,
                                                },
                                                items: [{
                                                        xtype: 'textfield',
                                                        id: 'Koltiva.view.IMS.WinFormImsAssetRcpChBs-Form-PengesahDisiapkan',
                                                        name: 'Koltiva.view.IMS.WinFormImsAssetRcpChBs-Form-PengesahDisiapkan',
                                                        fieldLabel: lang('Disiapkan oleh')
                                                    }, {
                                                        xtype: 'textfield',
                                                        id: 'Koltiva.view.IMS.WinFormImsAssetRcpChBs-Form-PengesahDiketahui',
                                                        name: 'Koltiva.view.IMS.WinFormImsAssetRcpChBs-Form-PengesahDiketahui',
                                                        fieldLabel: lang('Diketahui oleh')
                                                    }]
                                            }, {
                                                columnWidth: 0.5,
                                                layout: 'form',
                                                style: 'padding-left:15px;',
                                                defaults: {
                                                    labelWidth: 225,
                                                },
                                                items: [{
                                                        xtype: 'textfield',
                                                        id: 'Koltiva.view.IMS.WinFormImsAssetRcpChBs-Form-PengesahVerifikasi',
                                                        name: 'Koltiva.view.IMS.WinFormImsAssetRcpChBs-Form-PengesahVerifikasi',
                                                        fieldLabel: lang('Diverifikasi oleh')
                                                    }, {
                                                        xtype: 'textfield',
                                                        id: 'Koltiva.view.IMS.WinFormImsAssetRcpChBs-Form-PengesahDisetujui',
                                                        name: 'Koltiva.view.IMS.WinFormImsAssetRcpChBs-Form-PengesahDisetujui',
                                                        fieldLabel: lang('Disetujui oleh')
                                                    }]
                                            }]
                                    }]
                            }]
                    }]
            }];
        //items ---------------------------------------------------------------------------------------------------------------------------- (End)

        thisObj.buttons = [{
                icon: varjs.config.base_url + 'images/icons/new/save.png',
                text: lang('Save'),
                margin: '5 15 5 5',
                cls: 'Sfr_BtnFormBlue',
                overCls: 'Sfr_BtnFormBlue-Hover',
                id: 'Koltiva.view.IMS.WinFormImsAssetRcpChBs-Form-BtnSave',
                handler: function () {
                    var FormNya = Ext.getCmp('Koltiva.view.IMS.WinFormImsAssetRcpChBs-Form').getForm();
                    var FormValidOrNot = FormNya.isValid();

                    if (FormValidOrNot == true) {
                        FormNya.submit({
                            url: m_api + '/ims_asset_rcp/rcp_ch_bs',
                            method: 'POST',
                            params: {
                                OpsiDisplay: thisObj.viewVar.OpsiDisplay
                            },
                            waitMsg: 'Saving data...',
                            success: function (fp, o) {
                                Ext.MessageBox.show({
                                    title: 'Information',
                                    msg: o.result.message,
                                    buttons: Ext.MessageBox.OK,
                                    animateTarget: 'mb9',
                                    icon: 'ext-mb-success'
                                });

                                if (thisObj.viewVar.OpsiDisplay == 'insert') {
                                    //Set ID
                                    Ext.getCmp('Koltiva.view.IMS.WinFormImsAssetRcpChBs-Form-RcpID').setValue(o.result.RcpID);
                                }

                                //refresh store yg manggil
                                thisObj.viewVar.CallerStore.load();
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
                    } else {
                        Ext.MessageBox.show({
                            title: 'Attention',
                            msg: 'Form not valid yet',
                            buttons: Ext.MessageBox.OK,
                            animateTarget: 'mb9',
                            icon: 'ext-mb-info'
                        });
                    }
                }
            }, {
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
        afterRender: function(){
            var thisObj = this;

            //form reset
            var FormNya = Ext.getCmp('Koltiva.view.IMS.WinFormImsAssetRcpChBs-Form').getForm();
            FormNya.reset();

            if(thisObj.viewVar.OpsiDisplay == 'update' || thisObj.viewVar.OpsiDisplay == 'view'){
                //load formnya
                FormNya.load({
                    url: m_api + '/ims_asset_rcp/rcp_ch_bs_form_data',
                    method: 'GET',
                    params: {
                        RcpID: thisObj.viewVar.RcpID
                    },
                    success: function(form, action) {
                        var r = Ext.decode(action.response.responseText);
                        //console.log(r);

                        //kasih readonly untuk field yg tak boleh ubah
                        Ext.getCmp('Koltiva.view.IMS.WinFormImsAssetRcpChBs-Form-RcpTransNumber').setReadOnly(true);                        

                        //Load Store Grid
                        thisObj.StoreGridRcpItems.setStoreVar({
                            RcpID: thisObj.viewVar.RcpID
                        });
                        thisObj.StoreGridRcpItems.load();

                        if(thisObj.viewVar.OpsiDisplay == 'view'){
                            Ext.getCmp('Koltiva.view.IMS.WinFormImsAssetRcpChBs-Form-BtnSave').setVisible(false);
                            Ext.getCmp('Koltiva.view.IMS.WinFormImsAssetRcpChBs-Form-GridRcpItems-BtnAddItems').setVisible(false);
                            Ext.getCmp('Koltiva.view.IMS.WinFormImsAssetRcpChBs-Form-GridRcpItems-ColActionColumn').setVisible(false);
                        }                        
                    },
                    failure: function(form, action) {
                        Ext.MessageBox.show({
                            title: 'Failed',
                            msg: 'Failed to retrieve data',
                            buttons: Ext.MessageBox.OK,
                            animateTarget: 'mb9',
                            icon: 'ext-mb-error'
                        });
                    }
                });
            }

        }
    }
});
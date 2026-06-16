/******************************************
 *  Author : n1colius.lau@gmail.com   
 *  Created On : Tue Oct 02 2018
 *  File : WinFormImsAssetRcpChBsItem.js
 *******************************************/

/*
    Param2 yg diperlukan ketika load View ini    
    - RcpID
    - OpsiDisplay
    - CallerStore
    - IMSID
    - RcpItemID
*/

Ext.define('Koltiva.view.IMS.WinFormImsAssetRcpChBsItem' ,{
    extend: 'Ext.window.Window',
    id: 'Koltiva.view.IMS.WinFormImsAssetRcpChBsItem',
    title: lang('IMS - Receipt Form Item (CH & BS)'),
    closable: true,
    modal: true,
    closeAction: 'destroy',
    width: '78%',
    height: '42%',
    overflowY: 'auto',
    viewVar: false,
    setViewVar: function(value){
        this.viewVar = value;
    },
    initComponent: function() {
        var thisObj = this;
        
        //Store =============== (Begin)
        var CmbAssetItem = Ext.create('Koltiva.store.IMS.CmbIMSAssetItem',{
        	storeVar: {
                UseIn: 'ch_bs'
            }
        });
        thisObj.CmbAssetUser = Ext.create('Koltiva.store.IMS.CmbAssetUser');
        //Store =============== (End)

        //items ---------------------------------------------------------------------------------------------------------------------------- (Begin)
        thisObj.items = [{
            xtype: 'form',
            id: 'Koltiva.view.IMS.WinFormImsAssetRcpChBsItem-Form',
            fileUpload: true,
            padding:'5 25 5 8',
            items:[{
                layout: 'column',
                border: false,
                items:[{
                    columnWidth: 1,
                    layout:'form',
                    style: '',
                    items:[{
                        layout: 'column',
                        border: false,
                        items:[{
                            columnWidth: 0.495,
                            style:'padding-right:25px;',
                            layout:'form',
                            defaults:{
                                labelWidth: 175,
                            },
                            items:[{
                                xtype: 'hiddenfield',
                                id: 'Koltiva.view.IMS.WinFormImsAssetRcpChBsItem-Form-RcpItemID',
                                name: 'Koltiva.view.IMS.WinFormImsAssetRcpChBsItem-Form-RcpItemID'
                            },{
                                xtype: 'hiddenfield',
                                id: 'Koltiva.view.IMS.WinFormImsAssetRcpChBsItem-Form-RcpID',
                                name: 'Koltiva.view.IMS.WinFormImsAssetRcpChBsItem-Form-RcpID',
                                value: thisObj.viewVar.RcpID
                            },{
                                xtype: 'combobox',
                                id: 'Koltiva.view.IMS.WinFormImsAssetRcpChBsItem-Form-ItemID',
                                name: 'Koltiva.view.IMS.WinFormImsAssetRcpChBsItem-Form-ItemID',
                                store: CmbAssetItem,
                                fieldLabel: lang('Item'),
                                allowBlank: false,
                                msgTarget: 'under',
                                queryMode: 'local',
                                displayField: 'label',
                                valueField: 'id'
                            },{
                                xtype: 'textfield',
                                id: 'Koltiva.view.IMS.WinFormImsAssetRcpChBsItem-Form-ItemName',
                                name: 'Koltiva.view.IMS.WinFormImsAssetRcpChBsItem-Form-ItemName',
                                fieldLabel: lang('Item Name'),
                                allowBlank: false,
                                msgTarget: 'under'
                            },{
                                xtype: 'numericfield',
                                id: 'Koltiva.view.IMS.WinFormImsAssetRcpChBsItem-Form-ItemQty',
                                name: 'Koltiva.view.IMS.WinFormImsAssetRcpChBsItem-Form-ItemQty',
                                fieldLabel: lang('Item Qty'),
                                allowNegative: false,
                                minValue: 0,
                                allowBlank: false,
                                msgTarget: 'under'
                            },{
                                xtype: 'textareafield',
                                id: 'Koltiva.view.IMS.WinFormImsAssetRcpChBsItem-Form-ItemRemark',
                                name: 'Koltiva.view.IMS.WinFormImsAssetRcpChBsItem-Form-ItemRemark',
                                fieldLabel: lang('Item Remark')
                            }]
                        },{
                            columnWidth: 0.5,
                            layout:'form',
                            style:'padding-left:15px;',
                            defaults:{
                                labelWidth: 175,
                            },
                            items:[{
                                xtype: 'radiogroup',
                                fieldLabel: lang('Asset User Type'),
                                allowBlank: false,
                                msgTarget: 'under',
                                items: [{
                                    name: 'Koltiva.view.IMS.WinFormImsAssetRcpChBsItem-Form-UserAssType',
                                    id: 'Koltiva.view.IMS.WinFormImsAssetRcpChBsItem-Form-UserAssTypeCH',
                                    boxLabel: lang('CH'),
                                    inputValue: 'CH',
                                    listeners: {
                                        change: function(field, nv, ov) {
                                            if(this.checked == true){
                                                Ext.getCmp('Koltiva.view.IMS.WinFormImsAssetRcpChBsItem-Form-UserAssID').setValue(null);
                                                thisObj.CmbAssetUser.setStoreVar({
                                                    Type:'CH',
                                                    IMSID: thisObj.viewVar.IMSID
                                                });
                                                thisObj.CmbAssetUser.load();
                                            }
                                            return false;
                                        }
                                    }
                                }, {
                                    name: 'Koltiva.view.IMS.WinFormImsAssetRcpChBsItem-Form-UserAssType',
                                    id: 'Koltiva.view.IMS.WinFormImsAssetRcpChBsItem-Form-UserAssTypeBS',
                                    boxLabel: lang('BS'),
                                    inputValue: 'BS',
                                    listeners: {
                                        change: function(field, nv, ov) {
                                            if(this.checked == true){
                                                Ext.getCmp('Koltiva.view.IMS.WinFormImsAssetRcpChBsItem-Form-UserAssID').setValue(null);
                                                thisObj.CmbAssetUser.setStoreVar({
                                                    Type:'BS',
                                                    IMSID: thisObj.viewVar.IMSID
                                                });
                                                thisObj.CmbAssetUser.load();
                                            }                     
                                            return false;
                                        }
                                    }
                                }]
                            },{
                                xtype: 'combobox',
                                id: 'Koltiva.view.IMS.WinFormImsAssetRcpChBsItem-Form-UserAssID',
                                name: 'Koltiva.view.IMS.WinFormImsAssetRcpChBsItem-Form-UserAssID',
                                store: thisObj.CmbAssetUser,
                                fieldLabel: lang('Asset User'),
                                allowBlank: false,
                                msgTarget: 'under',
                                queryMode: 'local',
                                displayField: 'label',
                                valueField: 'id'
                            },{
                                xtype: 'textfield',
                                id: 'Koltiva.view.IMS.WinFormImsAssetRcpChBsItem-Form-UserAssLocation',
                                name: 'Koltiva.view.IMS.WinFormImsAssetRcpChBsItem-Form-UserAssLocation',
                                fieldLabel: lang('Asset User Location'),
                            },{
                                xtype: 'textareafield',
                                id: 'Koltiva.view.IMS.WinFormImsAssetRcpChBsItem-Form-Remark',
                                name: 'Koltiva.view.IMS.WinFormImsAssetRcpChBsItem-Form-Remark',
                                fieldLabel: lang('Remark')
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
                id: 'Koltiva.view.IMS.WinFormImsAssetRcpChBsItem-Form-BtnSave',
                handler: function () {
                    var FormNya = Ext.getCmp('Koltiva.view.IMS.WinFormImsAssetRcpChBsItem-Form').getForm();
                    var FormValidOrNot = FormNya.isValid();

                    if (FormValidOrNot == true) {
                        FormNya.submit({
                            url: m_api + '/ims_asset_rcp/rcp_ch_bs_item',
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

                                //refresh store yg manggil
                                thisObj.viewVar.CallerStore.setStoreVar({
                                    RcpID: thisObj.viewVar.RcpID
                                });
                                thisObj.viewVar.CallerStore.load();
                                thisObj.viewVar.CallerStoreMain.load();

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
            var FormNya = Ext.getCmp('Koltiva.view.IMS.WinFormImsAssetRcpChBsItem-Form').getForm();
            FormNya.reset();

            if(thisObj.viewVar.OpsiDisplay == 'update' || thisObj.viewVar.OpsiDisplay == 'view'){
                //load formnya
                FormNya.load({
                    url: m_api + '/ims_asset_rcp/rcp_ch_bs_item_form_data',
                    method: 'GET',
                    params: {
                        RcpItemID: thisObj.viewVar.RcpItemID
                    },
                    success: function(form, action) {
                        var r = Ext.decode(action.response.responseText);
                        //console.log(r);
                        
                        if(r.data.UserAssType == 'CH' || r.data.UserAssType == 'BS'){
                            Ext.getCmp('Koltiva.view.IMS.WinFormImsAssetRcpChBsItem-Form-UserAssID').setValue(null);
                            thisObj.CmbAssetUser.setStoreVar({
                                Type:r.data.UserAssType,
                                IMSID: thisObj.viewVar.IMSID
                            });
                            thisObj.CmbAssetUser.load({
                                callback: function(records, operation, success){
                                    Ext.getCmp('Koltiva.view.IMS.WinFormImsAssetRcpChBsItem-Form-UserAssID').setValue(r.data.UserAssID);
                                }
                            });
                        }

                        if(thisObj.viewVar.OpsiDisplay == 'view'){
                            Ext.getCmp('Koltiva.view.IMS.WinFormImsAssetRcpChBsItem-Form-BtnSave').setVisible(false);
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
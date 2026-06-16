/******************************************
 *  Author : n1colius.lau@gmail.com   
 *  Created On : Fri Oct 12 2018
 *  File : WinFormImsAssetFarmerCardRcp.js
 *******************************************/

/*
    Param2 yg diperlukan ketika load View ini
    - IMSID
    - RcpID
    - OpsiDisplay
    - CallerStore
*/

Ext.define('Koltiva.view.IMS.WinFormImsAssetFarmerCardRcp' ,{
    extend: 'Ext.window.Window',
    id: 'Koltiva.view.IMS.WinFormImsAssetFarmerCardRcp',
    title: lang('IMS - Farmer ID Card Receipt Form'),
    closable: true,
    modal: true,
    closeAction: 'destroy',
    width: '90%',
    height: '94%',
    overflowY: 'auto',
    viewVar: false,
    setViewVar: function(value){
        this.viewVar = value;
    },
    initComponent: function() {
        var thisObj = this;

        //Store ================================== (Begin)
        thisObj.store_grid_farmer_rec = Ext.create('Koltiva.store.IMS.ImsAssetRcpGridFarmerCardFormFarmerRec',{
        	storeVar: {
                RcpID: thisObj.viewVar.RcpID
            }
        });
        //Store ================================== (End)

        //items ---------------------------------------------------------------------------------------------------------------------------- (Begin)
        thisObj.items = [{
                xtype: 'form',
                id: 'Koltiva.view.IMS.WinFormImsAssetFarmerCardRcp-Form',
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
                                                        id: 'Koltiva.view.IMS.WinFormImsAssetFarmerCardRcp-Form-RcpID',
                                                        name: 'Koltiva.view.IMS.WinFormImsAssetFarmerCardRcp-Form-RcpID'
                                                    }, {
                                                        xtype: 'hiddenfield',
                                                        id: 'Koltiva.view.IMS.WinFormImsAssetFarmerCardRcp-Form-IMSID',
                                                        name: 'Koltiva.view.IMS.WinFormImsAssetFarmerCardRcp-Form-IMSID',
                                                        value: thisObj.viewVar.IMSID
                                                    }, {
                                                        xtype: 'textfield',
                                                        id: 'Koltiva.view.IMS.WinFormImsAssetFarmerCardRcp-Form-RcpTransNumber',
                                                        name: 'Koltiva.view.IMS.WinFormImsAssetFarmerCardRcp-Form-RcpTransNumber',
                                                        fieldLabel: lang('Transaction Number'),
                                                        readOnly: true
                                                    }, {
                                                        xtype: 'textareafield',
                                                        id: 'Koltiva.view.IMS.WinFormImsAssetFarmerCardRcp-Form-Remark',
                                                        name: 'Koltiva.view.IMS.WinFormImsAssetFarmerCardRcp-Form-Remark',
                                                        fieldLabel: lang('Remark')
                                                    }]
                                            }, {
                                                columnWidth: 0.5,
                                                layout: 'form',
                                                style: 'padding-left:15px;',
                                                defaults: {
                                                    labelWidth: 225,
                                                },
                                                items: [{
                                                        xtype: 'datefield',
                                                        id: 'Koltiva.view.IMS.WinFormImsAssetFarmerCardRcp-Form-RcpDate',
                                                        name: 'Koltiva.view.IMS.WinFormImsAssetFarmerCardRcp-Form-RcpDate',
                                                        fieldLabel: lang('Receipt Date'),
                                                        allowBlank: false,
                                                        msgTarget: 'under',
                                                        format: 'Y-m-d'
                                                    }, {
                                                        xtype: 'textfield',
                                                        id: 'Koltiva.view.IMS.WinFormImsAssetFarmerCardRcp-Form-ReceiverRep',
                                                        name: 'Koltiva.view.IMS.WinFormImsAssetFarmerCardRcp-Form-ReceiverRep',
                                                        fieldLabel: lang('Recipient / Representatives'),
                                                        allowBlank: false,
                                                        msgTarget: 'under'
                                                    }]
                                            }]
                                    }, {
                                        layout: 'fit',
                                        id: 'Koltiva.view.IMS.WinFormImsAssetFarmerCardRcp-Form-LayFitGridFarmerRec',
                                        hidden: true,
                                        items: [{
                                                xtype: 'grid',
                                                store: thisObj.store_grid_farmer_rec,
                                                width: '98%',
                                                height: 375,
                                                id: 'Koltiva.view.IMS.WinFormImsAssetFarmerCardRcp-Form-GridFarmerRec',
                                                style: 'border:1px solid #CCC;margin-top:5px;',
                                                loadMask: true,
                                                title: lang('Farmer Recipient'),
                                                selType: 'checkboxmodel',
                                                viewConfig: {
                                                    deferEmptyText: false,
                                                    emptyText: lang('No Data Available')
                                                },
                                                dockedItems: [{
                                                        xtype: 'pagingtoolbar',
                                                        store: thisObj.store_grid_farmer_rec,
                                                        dock: 'bottom',
                                                        displayInfo: true
                                                    }, {
                                                        xtype: 'toolbar',
                                                        items: [{
                                                                icon: varjs.config.base_url + 'images/icons/new/add.png',
                                                                hidden: m_act_add,
                                                                text: lang('Add'),
                                                                cls: 'Sfr_BtnGridGreen',
                                                                overCls: 'Sfr_BtnGridGreen-Hover',
                                                                scope: this,
                                                                handler: function () {
                                                                    var WinFormImsAssetFarmerCardAddFarmerRec = Ext.create('Koltiva.view.IMS.WinFormImsAssetFarmerCardAddFarmerRec', {
                                                                        viewVar: {
                                                                            RcpID: thisObj.viewVar.RcpID,
                                                                            CallerStore: thisObj.store_grid_farmer_rec
                                                                        }
                                                                    });
                                                                    if (!WinFormImsAssetFarmerCardAddFarmerRec.isVisible()) {
                                                                        WinFormImsAssetFarmerCardAddFarmerRec.center();
                                                                        WinFormImsAssetFarmerCardAddFarmerRec.show();
                                                                    } else {
                                                                        WinFormImsAssetFarmerCardAddFarmerRec.close();
                                                                    }
                                                                }
                                                            }, {
                                                                icon: varjs.config.base_url + 'images/icons/new/delete.png',
                                                                hidden: m_act_delete,
                                                                text: lang('Delete'),
                                                                scope: this,
                                                                handler: function () {
                                                                    var gridSelected = Ext.getCmp('Koltiva.view.IMS.WinFormImsAssetFarmerCardRcp-Form-GridFarmerRec').getSelectionModel().getSelection();

                                                                    var IdSelectedArr = [];
                                                                    for (var i = gridSelected.length - 1; i >= 0; i--) {
                                                                        IdSelectedArr.push(gridSelected[i].get('FarmerID'));
                                                                    }

                                                                    if (IdSelectedArr.length > 0) {
                                                                        Ext.MessageBox.confirm('Message', lang('Are you sure want to delete the selected data ?'), function (btn) {
                                                                            if (btn == 'yes') {
                                                                                Ext.Ajax.request({
                                                                                    waitMsg: 'Please Wait',
                                                                                    url: m_api + '/ims_asset_rcp/farmer_card_farmer_rec_farmer',
                                                                                    method: 'DELETE',
                                                                                    params: {
                                                                                        FarmerIDSel: Ext.encode(IdSelectedArr),
                                                                                        RcpID: thisObj.viewVar.RcpID
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

                                                                                        thisObj.store_grid_farmer_rec.load();
                                                                                    },
                                                                                    failure: function (rp, o) {
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
                                                                            }
                                                                        });
                                                                    } else {
                                                                        Ext.MessageBox.show({
                                                                            title: 'Notifications',
                                                                            msg: 'No item selected',
                                                                            buttons: Ext.MessageBox.OK,
                                                                            animateTarget: 'mb9',
                                                                            icon: 'ext-mb-info'
                                                                        });
                                                                    }
                                                                }
                                                            }]
                                                    }],
                                                columns: [{
                                                        HeaderCheckbox: true,
                                                        dataIndex: 'CheckData',
                                                        width: '5%'
                                                    }, {
                                                        text: lang('Farmer ID'),
                                                        width: '15%',
                                                        dataIndex: 'FarmerID'
                                                    }, {
                                                        text: lang('Name'),
                                                        width: '25%',
                                                        dataIndex: 'FarmerName'
                                                    }, {
                                                        text: lang('Gender'),
                                                        width: '10%',
                                                        dataIndex: 'Gender'
                                                    }, {
                                                        text: lang('Farmer Group'),
                                                        width: '24%',
                                                        dataIndex: 'FarmerGroup'
                                                    }, {
                                                        text: lang('ID Card Received'),
                                                        width: '18%',
                                                        dataIndex: 'ReceiverStatus'
                                                    }]
                                            }]
                                    }, {
                                        html: '<br>'
                                    }, {
                                        layout: 'column',
                                        border: false,
                                        items: [{
                                                columnWidth: 0.5,
                                                layout: 'form',
                                                defaults: {
                                                    labelWidth: 225,
                                                },
                                                items: [{
                                                        xtype: 'fileuploadfield',
                                                        fieldLabel: lang('File Import for Received Status'),
                                                        id: 'Koltiva.view.IMS.WinFormImsAssetFarmerCardRcp-Form-FileImport',
                                                        name: 'Koltiva.view.IMS.WinFormImsAssetFarmerCardRcp-Form-FileImport',
                                                        hidden: true,
                                                        buttonText: 'Browse',
                                                        listeners: {
                                                            'change': function (fb, v) {
                                                                var FormNya = Ext.getCmp('Koltiva.view.IMS.WinFormImsAssetFarmerCardRcp-Form').getForm();
                                                                FormNya.submit({
                                                                    url: m_api + '/ims_asset_rcp/farmer_card_import_farmer_received',
                                                                    waitMsg: 'Sending and importing file...',
                                                                    clientValidation: false,
                                                                    success: function (rp, o) {
                                                                        var r = Ext.decode(o.response.responseText);
                                                                        Ext.MessageBox.show({
                                                                            title: 'Information',
                                                                            msg: r.message,
                                                                            buttons: Ext.MessageBox.OK,
                                                                            animateTarget: 'mb9',
                                                                            icon: 'ext-mb-success'
                                                                        });

                                                                        //Refresh Grid
                                                                        thisObj.store_grid_farmer_rec.load();
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
                                                        }
                                                    }, {
                                                        id: 'Koltiva.view.IMS.WinFormImsAssetFarmerCardRcp-Form-FileImport-TemplateUrl',
                                                        hidden: true,
                                                        style: 'margin-top:-10px;text-align:right;',
                                                        html: '<a style="text-decoration:underline;" href="' + varjs.config.base_url + 'api/ims_asset_rcp/farmer_card_template_import_farmer_received/' + thisObj.viewVar.RcpID + '" target="_blank">Download Template File for Import (type:xlsx)</a>'
                                                    }]
                                            }]
                                    }, {
                                        layout: 'column',
                                        border: false,
                                        id: 'Koltiva.view.IMS.WinFormImsAssetFarmerCardRcp-Form-LayColRcpAttach',
                                        hidden: true,
                                        items: [{
                                                columnWidth: 0.5,
                                                border: false,
                                                layout: 'form',
                                                items: [{
                                                        xtype: 'fileuploadfield',
                                                        labelWidth: 225,
                                                        fieldLabel: lang('Receipt Attachment'),
                                                        id: 'Koltiva.view.IMS.WinFormImsAssetFarmerCardRcp-Form-RcpFile',
                                                        name: 'Koltiva.view.IMS.WinFormImsAssetFarmerCardRcp-Form-RcpFile',
                                                        buttonText: 'Browse',
                                                        listeners: {
                                                            'change': function (fb, v) {
                                                                var FormNya = Ext.getCmp('Koltiva.view.IMS.WinFormImsAssetFarmerCardRcp-Form').getForm();
                                                                FormNya.submit({
                                                                    url: m_api + '/ims_asset_rcp/farmer_card_farmer_rcp_image',
                                                                    waitMsg: 'Uploading file...',
                                                                    clientValidation: false,
                                                                    success: function (rp, o) {
                                                                        var r = Ext.decode(o.response.responseText);

                                                                        Ext.getCmp('Koltiva.view.IMS.WinFormImsAssetFarmerCardRcp-Form-FileImport-RcpFileDesc').update('<a href="' + m_api_base_url + '/files/ims_asset/farmer_card_receipt/' + r.file_with_rand + '" data-lightbox="image-1" data-title="Receipt File" title="View Image"><img src="' + m_api_base_url + '/files/ims_asset/farmer_card_receipt/' + r.file_with_rand + '" style="height:200px;" /></a>');
                                                                        Ext.getCmp('Koltiva.view.IMS.WinFormImsAssetFarmerCardRcp-Form-FileImport-RcpFileDesc').doLayout();
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
                                                        }
                                                    }]
                                            }, {
                                                columnWidth: 0.5,
                                                border: false,
                                                style: 'padding-left:35px;margin-top:-8px;',
                                                items: [{
                                                        id: 'Koltiva.view.IMS.WinFormImsAssetFarmerCardRcp-Form-FileImport-RcpFileDesc',
                                                        html: '<img src="' + m_api_base_url + '/images/video/thumb-defa.png" height="200" />'
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
                id: 'Koltiva.view.IMS.WinFormImsAssetFarmerCardRcp-Form-BtnSave',
                handler: function () {
                    var FormNya = Ext.getCmp('Koltiva.view.IMS.WinFormImsAssetFarmerCardRcp-Form').getForm();
                    var FormValidOrNot = FormNya.isValid();

                    if (FormValidOrNot == true) {
                        FormNya.submit({
                            url: m_api + '/ims_asset_rcp/farmer_card_rcp',
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
                                thisObj.viewVar.CallerStore.load();
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
            var FormNya = Ext.getCmp('Koltiva.view.IMS.WinFormImsAssetFarmerCardRcp-Form').getForm();
            FormNya.reset();

            if(thisObj.viewVar.OpsiDisplay == 'insert'){
                Ext.Ajax.request({
                    waitMsg: lang('Please Wait'),
                    url: m_api + '/ims_asset_rcp/farmer_card_rcp_input_prep',
                    method : 'GET',
                    params: {
                        IMSID: thisObj.viewVar.IMSID
                    },
                    success: function(response, opts){
                        var r = Ext.decode(response.responseText);
                        //console.log(r);
                        
                        Ext.getCmp('Koltiva.view.IMS.WinFormImsAssetFarmerCardRcp-Form-RcpID').setValue(r.RcpID);
                    },
                    failure: function(response, opts){
                        Ext.MessageBox.show({
                            title: 'Failed',
                            msg: 'Network Error',
                            buttons: Ext.MessageBox.OK,
                            animateTarget: 'mb9',
                            icon: 'ext-mb-error'
                        });
                    }
                });
            }

            if(thisObj.viewVar.OpsiDisplay == 'view' || thisObj.viewVar.OpsiDisplay == 'update'){
                //Munculkan yg khusus view/update
                Ext.getCmp('Koltiva.view.IMS.WinFormImsAssetFarmerCardRcp-Form-LayFitGridFarmerRec').setVisible(true);
                Ext.getCmp('Koltiva.view.IMS.WinFormImsAssetFarmerCardRcp-Form-FileImport').setVisible(true);
                Ext.getCmp('Koltiva.view.IMS.WinFormImsAssetFarmerCardRcp-Form-FileImport-TemplateUrl').setVisible(true);
                Ext.getCmp('Koltiva.view.IMS.WinFormImsAssetFarmerCardRcp-Form-LayColRcpAttach').setVisible(true);

                //load formnya
                FormNya.load({
                    url: m_api + '/ims_asset_rcp/farmer_card_rcp_form_data',
                    method: 'GET',
                    params: {
                        RcpID: thisObj.viewVar.RcpID
                    },
                    success: function(form, action) {
                        var r = Ext.decode(action.response.responseText);
                        //console.log(r);

                        //kasih readonly untuk field yg tak boleh ubah
                        Ext.getCmp('Koltiva.view.IMS.WinFormImsAssetFarmerCardRcp-Form-RcpTransNumber').setReadOnly(true);                        

                        //Load Store Grid
                        //thisObj.store_grid_farmer_rec.load();

                        //Image Receipt
                        if(r.data.RcpFile != ""){
                            var FotoRcpFile = m_api_base_url + '/files/ims_asset/farmer_card_receipt/'+ r.data.RcpFile;
                            var AngkaRand = Math.floor((Math.random() * 100) + 1);
    
                            checkImageExistsGeneral(FotoRcpFile, function(existsImage) {
                                if (existsImage == true) {
                                    Ext.getCmp('Koltiva.view.IMS.WinFormImsAssetFarmerCardRcp-Form-FileImport-RcpFileDesc').update('<a href="'+FotoRcpFile+'" data-lightbox="image-1" data-title="Receipt File" title="View Image"><img src="'+FotoRcpFile+'?'+AngkaRand+'" style="height:200px;" /></a>');
                                } else {
                                    Ext.getCmp('Koltiva.view.IMS.WinFormImsAssetFarmerCardRcp-Form-FileImport-RcpFileDesc').update('<img src="'+m_api_base_url+'/images/video/thumb-defa.png" height="200" />');
                                }
                                Ext.getCmp('Koltiva.view.IMS.WinFormImsAssetFarmerCardRcp-Form-FileImport-RcpFileDesc').doLayout();
                            });
                        }

                        if(thisObj.viewVar.OpsiDisplay == 'view'){
                            //Pastikan tidak ada aksi input disini
                            Ext.getCmp('Koltiva.view.IMS.WinFormImsAssetFarmerCardRcp-Form-BtnSave').setVisible(false);
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
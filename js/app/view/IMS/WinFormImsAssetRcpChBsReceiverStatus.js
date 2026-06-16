/******************************************
 *  Author : n1colius.lau@gmail.com   
 *  Created On : Tue Oct 02 2018
 *  File : WinFormImsAssetRcpChBsReceiverStatus.js
 *******************************************/

/*
    Param2 yg diperlukan ketika load View ini
    - IMSID
    - RcpID    
    - CallerStore
*/

Ext.define('Koltiva.view.IMS.WinFormImsAssetRcpChBsReceiverStatus' ,{
    extend: 'Ext.window.Window',
    id: 'Koltiva.view.IMS.WinFormImsAssetRcpChBsReceiverStatus',
    title: lang('IMS - Receiver Status Form (CH & BS)'),
    closable: true,
    modal: true,
    closeAction: 'destroy',
    width: '58%',
    height: '62%',
    overflowY: 'auto',
    viewVar: false,
    setViewVar: function(value){
        this.viewVar = value;
    },
    initComponent: function() {
        var thisObj = this;

        //items ---------------------------------------------------------------------------------------------------------------------------- (Begin)
        thisObj.items = [{
            xtype: 'form',
            id: 'Koltiva.view.IMS.WinFormImsAssetRcpChBsReceiverStatus-Form',
            fileUpload: true,
            padding:'5 25 5 8',
            items:[{
                layout: 'column',
                border: false,
                items:[{
                    columnWidth: 1,
                    layout:'form',
                    style: '',
                    defaults:{
                        labelWidth: 180,
                    },
                    items:[{
                        xtype: 'hiddenfield',
                        id: 'Koltiva.view.IMS.WinFormImsAssetRcpChBsReceiverStatus-Form-RcpID',
                        name: 'Koltiva.view.IMS.WinFormImsAssetRcpChBsReceiverStatus-Form-RcpID'
                    },{
                        xtype: 'hiddenfield',
                        id: 'Koltiva.view.IMS.WinFormImsAssetRcpChBsReceiverStatus-Form-IMSID',
                        name: 'Koltiva.view.IMS.WinFormImsAssetRcpChBsReceiverStatus-Form-IMSID'
                    },{
                        xtype: 'textfield',
                        id: 'Koltiva.view.IMS.WinFormImsAssetRcpChBsReceiverStatus-Form-ReceiverName',
                        name: 'Koltiva.view.IMS.WinFormImsAssetRcpChBsReceiverStatus-Form-ReceiverName',
                        fieldLabel: lang('Receiver Name'),
                        readOnly: true
                    },{
                        xtype: 'radiogroup',
                        fieldLabel: lang('Receiver Status'),
                        allowBlank: false,
                        msgTarget: 'under',
                        items: [{
                            name: 'Koltiva.view.IMS.WinFormImsAssetRcpChBsReceiverStatus-Form-ReceiverStatus',
                            id: 'Koltiva.view.IMS.WinFormImsAssetRcpChBsReceiverStatus-Form-ReceiverStatus1',
                            boxLabel: lang('Yes'),
                            inputValue: '1',
                            listeners: {
                                change: function(field, nv, ov) {                                                                                
                                    return false;
                                }
                            }
                        },{
                            name: 'Koltiva.view.IMS.WinFormImsAssetRcpChBsReceiverStatus-Form-ReceiverStatus',
                            id: 'Koltiva.view.IMS.WinFormImsAssetRcpChBsReceiverStatus-Form-ReceiverStatus2',
                            boxLabel: lang('No'),
                            inputValue: '2',
                            listeners: {
                                change: function(field, nv, ov) {                                                                                
                                    return false;
                                }
                            }
                        }]
                    },{
                        xtype: 'datefield',
                        id: 'Koltiva.view.IMS.WinFormImsAssetRcpChBsReceiverStatus-Form-ReceiverDate',
                        name: 'Koltiva.view.IMS.WinFormImsAssetRcpChBsReceiverStatus-Form-ReceiverDate',
                        fieldLabel: lang('Receiver Date'),
                        allowBlank: false,
                        msgTarget: 'under',
                        format:'Y-m-d'
                    },{
                        layout:'column',
                        border:false,          
                        items:[{
                            columnWidth: 0.55,
                            border: false,
                            layout: 'form',
                            items:[{
                                xtype: 'fileuploadfield',
                                labelWidth: 180,
                                fieldLabel: lang('File Attachment'),
                                id: 'Koltiva.view.IMS.WinFormImsAssetRcpChBsReceiverStatus-Form-ReceiverFile',
                                name: 'Koltiva.view.IMS.WinFormImsAssetRcpChBsReceiverStatus-Form-ReceiverFile',
                                buttonText: 'Browse',
                                listeners: {
                                    'change': function (fb, v) {
                                        Ext.getCmp('Koltiva.view.IMS.WinFormImsAssetRcpChBsReceiverStatus-Form').getForm().submit({
                                            url: m_api + '/ims_asset_rcp/rcp_image',
                                            clientValidation: false,
                                            params: {                                                
                                                RcpID: Ext.getCmp('Koltiva.view.IMS.WinFormImsAssetRcpChBsReceiverStatus-Form-RcpID').getValue()
                                            },
                                            waitMsg: 'Sending Photo...',
                                            success: function (fp, o) {
                                                Ext.getCmp('Koltiva.view.IMS.WinFormImsAssetRcpChBsReceiverStatus-Form-RcpImage').update('<a href="'+m_api_base_url+'/files/ims_asset/receipt/'+o.result.file_with_rand+'" data-lightbox="image-1" data-title="Receipt File" title="View Image"><img src="'+m_api_base_url+'/files/ims_asset/receipt/'+o.result.file_with_rand+'" style="float:right;height:160px;" /></a>');
                                                Ext.getCmp('Koltiva.view.IMS.WinFormImsAssetRcpChBsReceiverStatus-Form-RcpImage').doLayout();                               
                                            },
                                            failure: function (fp, o) {
                                                Ext.MessageBox.show({
                                                    title: lang('Attention'),
                                                    msg: o.result.message,
                                                    buttons: Ext.MessageBox.OK,
                                                    animateTarget: 'mb9',
                                                    icon: 'ext-mb-error'
                                                });
                                            }
                                        });
                                    }
                                }
                            }]
                        },{
                            columnWidth: 0.45,
                            border: false,                            
                            items:[{
                                id:'Koltiva.view.IMS.WinFormImsAssetRcpChBsReceiverStatus-Form-RcpImage',
                                html:'<img src="'+m_api_base_url+'/images/video/thumb-defa.png" width="240" height="150" style="float:right;" />'        
                            },{
                                html:'<div style="float:right;margin-top:-5px;font-size:10px;font-style:italic;">'+lang('File must be image')+'</div>'
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
                id: 'Koltiva.view.IMS.WinFormImsAssetRcpChBsReceiverStatus-Form-BtnSave',
                handler: function () {
                    var FormNya = Ext.getCmp('Koltiva.view.IMS.WinFormImsAssetRcpChBsReceiverStatus-Form').getForm();
                    var FormValidOrNot = FormNya.isValid();

                    if (FormValidOrNot == true) {
                        FormNya.submit({
                            url: m_api + '/ims_asset_rcp/rcp_ch_bs_receiver_status',
                            method: 'POST',
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
            var FormNya = Ext.getCmp('Koltiva.view.IMS.WinFormImsAssetRcpChBsReceiverStatus-Form').getForm();
            FormNya.reset();            

            //load formnya
            FormNya.load({
                url: m_api + '/ims_asset_rcp/rcp_ch_bs_receiver_status_form_data',
                method: 'GET',
                params: {
                    RcpID: thisObj.viewVar.RcpID
                },
                success: function(form, action) {
                    var r = Ext.decode(action.response.responseText);
                    //console.log(r);

                    if(r.data.ReceiverFile != ""){
                        var FotoReceiverFile = m_api_base_url + '/files/ims_asset/receipt/'+ r.data.ReceiverFile;
                        var angkaRand = Math.floor((Math.random() * 100) + 1);

                        checkImageExistsGeneral(FotoReceiverFile, function(existsImage) {
                            if (existsImage == true) {
                                Ext.getCmp('Koltiva.view.IMS.WinFormImsAssetRcpChBsReceiverStatus-Form-RcpImage').update('<a href="'+FotoReceiverFile+'" data-lightbox="image-1" data-title="Receipt File" title="View Image"><img src="'+FotoReceiverFile+'?'+angkaRand+'" style="float:right;height:160px;" /></a>');
                            } else {
                                Ext.getCmp('Koltiva.view.IMS.WinFormImsAssetRcpChBsReceiverStatus-Form-RcpImage').update('<img src="'+m_api_base_url+'/images/video/thumb-defa.png" width="240" height="150" style="float:right;" />');
                            }
                            Ext.getCmp('Koltiva.view.IMS.WinFormImsAssetRcpChBsReceiverStatus-Form-RcpImage').doLayout();
                        });
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
});
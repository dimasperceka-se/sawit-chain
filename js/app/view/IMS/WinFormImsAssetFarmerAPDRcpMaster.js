/******************************************
 *  Author : n1colius.lau@gmail.com   
 *  Created On : Wed Oct 10 2018
 *  File : WinFormImsAssetFarmerAPDRcpMaster.js
 *******************************************/

/*
    Param2 yg diperlukan ketika load View ini
    - CallerStore
    - IMSID
    - FarmerID
    - FarmerName
    - OpsiDisplay
    - RcpID
*/

Ext.define('Koltiva.view.IMS.WinFormImsAssetFarmerAPDRcpMaster' ,{
    extend: 'Ext.window.Window',
    id: 'Koltiva.view.IMS.WinFormImsAssetFarmerAPDRcpMaster',
    title: lang('IMS - Farmer APD Receipt Form'),
    closable: true,
    modal: true,
    closeAction: 'destroy',
    width: '60%',
    height: '80%',
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
            id: 'Koltiva.view.IMS.WinFormImsAssetFarmerAPDRcpMaster-Form',
            fileUpload: true,
            padding:'5 25 5 8',
            items:[{
                layout: 'column',
                border: false,
                items:[{
                    columnWidth: 1,
                    layout:'form',
                    defaults:{
                        labelWidth: 175,
                    },
                    items:[{
                        xtype: 'hiddenfield',
                        id: 'Koltiva.view.IMS.WinFormImsAssetFarmerAPDRcpMaster-Form-RcpID',
                        name: 'Koltiva.view.IMS.WinFormImsAssetFarmerAPDRcpMaster-Form-RcpID'
                    },{
                        xtype: 'hiddenfield',
                        id: 'Koltiva.view.IMS.WinFormImsAssetFarmerAPDRcpMaster-Form-IMSID',
                        name: 'Koltiva.view.IMS.WinFormImsAssetFarmerAPDRcpMaster-Form-IMSID'
                    },{
                        xtype: 'hiddenfield',
                        id: 'Koltiva.view.IMS.WinFormImsAssetFarmerAPDRcpMaster-Form-FarmerID',
                        name: 'Koltiva.view.IMS.WinFormImsAssetFarmerAPDRcpMaster-Form-FarmerID'
                    },{
                        xtype: 'textfield',
                        id: 'Koltiva.view.IMS.WinFormImsAssetFarmerAPDRcpMaster-Form-RcpTransNumber',
                        name: 'Koltiva.view.IMS.WinFormImsAssetFarmerAPDRcpMaster-Form-RcpTransNumber',
                        fieldLabel: lang('Transaction Number'),
                        readOnly: true
                    },{
                        xtype: 'datefield',
                        id: 'Koltiva.view.IMS.WinFormImsAssetFarmerAPDRcpMaster-Form-RcpDate',
                        name: 'Koltiva.view.IMS.WinFormImsAssetFarmerAPDRcpMaster-Form-RcpDate',
                        fieldLabel: lang('Receipt Date'),
                        allowBlank: false,
                        msgTarget: 'under',
                        format:'Y-m-d'
                    },{
                        xtype: 'textareafield',
                        id: 'Koltiva.view.IMS.WinFormImsAssetFarmerAPDRcpMaster-Form-Remark',
                        name: 'Koltiva.view.IMS.WinFormImsAssetFarmerAPDRcpMaster-Form-Remark',
                        fieldLabel: lang('Remark')
                    },{
                        xtype: 'checkboxgroup',
                        fieldLabel: lang('APD Items'),
                        columns: 2,
                        items:[{
                            boxLabel: lang('Kotak Pestisida'),
                            name: 'Koltiva.view.IMS.WinFormImsAssetFarmerAPDRcpMaster-Form-ItemAPD_KotakPestisida',
                            inputValue: '1',
                            id: 'Koltiva.view.IMS.WinFormImsAssetFarmerAPDRcpMaster-Form-ItemAPD_KotakPestisida',
                            listeners:{
                                change: function(){
                                    return false;
                                }
                            }
                        },{
                            boxLabel: lang('Masker'),
                            name: 'Koltiva.view.IMS.WinFormImsAssetFarmerAPDRcpMaster-Form-ItemAPD_Masker',
                            inputValue: '1',
                            id: 'Koltiva.view.IMS.WinFormImsAssetFarmerAPDRcpMaster-Form-ItemAPD_Masker',
                            listeners:{
                                change: function(){
                                    return false;
                                }
                            }
                        },{
                            boxLabel: lang('Sarung Tangan'),
                            name: 'Koltiva.view.IMS.WinFormImsAssetFarmerAPDRcpMaster-Form-ItemAPD_SarungTangan',
                            inputValue: '1',
                            id: 'Koltiva.view.IMS.WinFormImsAssetFarmerAPDRcpMaster-Form-ItemAPD_SarungTangan',
                            listeners:{
                                change: function(){
                                    return false;
                                }
                            }
                        },{
                            boxLabel: lang('Goggles'),
                            name: 'Koltiva.view.IMS.WinFormImsAssetFarmerAPDRcpMaster-Form-ItemAPD_Goggles',
                            inputValue: '1',
                            id: 'Koltiva.view.IMS.WinFormImsAssetFarmerAPDRcpMaster-Form-ItemAPD_Goggles',
                            listeners:{
                                change: function(){
                                    return false;
                                }
                            }
                        },{
                            boxLabel: lang('Boots'),
                            name: 'Koltiva.view.IMS.WinFormImsAssetFarmerAPDRcpMaster-Form-ItemAPD_Boots',
                            inputValue: '1',
                            id: 'Koltiva.view.IMS.WinFormImsAssetFarmerAPDRcpMaster-Form-ItemAPD_Boots',
                            listeners:{
                                change: function(){
                                    return false;
                                }
                            }
                        },{
                            boxLabel: lang('Mantel'),
                            name: 'Koltiva.view.IMS.WinFormImsAssetFarmerAPDRcpMaster-Form-ItemAPD_Mantel',
                            inputValue: '1',
                            id: 'Koltiva.view.IMS.WinFormImsAssetFarmerAPDRcpMaster-Form-ItemAPD_Mantel',
                            listeners:{
                                change: function(){
                                    return false;
                                }
                            }
                        }]
                    },{
                        xtype: 'radiogroup',
                        fieldLabel: lang('Receiver Status'),
                        hidden: true,
                        id: 'Koltiva.view.IMS.WinFormImsAssetFarmerAPDRcpMaster-Form-ReceiverStatusRowRadioGroup',
                        items: [{
                            name: 'Koltiva.view.IMS.WinFormImsAssetFarmerAPDRcpMaster-Form-ReceiverStatus',
                            id: 'Koltiva.view.IMS.WinFormImsAssetFarmerAPDRcpMaster-Form-ReceiverStatus1',
                            boxLabel: lang('Yes'),
                            inputValue: '1',
                            listeners: {
                                change: function(field, nv, ov) {                                                                                
                                    return false;
                                }
                            }
                        },{
                            name: 'Koltiva.view.IMS.WinFormImsAssetFarmerAPDRcpMaster-Form-ReceiverStatus',
                            id: 'Koltiva.view.IMS.WinFormImsAssetFarmerAPDRcpMaster-Form-ReceiverStatus2',
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
                        id: 'Koltiva.view.IMS.WinFormImsAssetFarmerAPDRcpMaster-Form-ReceiverDate',
                        name: 'Koltiva.view.IMS.WinFormImsAssetFarmerAPDRcpMaster-Form-ReceiverDate',
                        fieldLabel: lang('Receiver Date'),
                        format:'Y-m-d',
                        hidden:true
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
                                id: 'Koltiva.view.IMS.WinFormImsAssetFarmerAPDRcpMaster-Form-ReceiverFile',
                                name: 'Koltiva.view.IMS.WinFormImsAssetFarmerAPDRcpMaster-Form-ReceiverFile',
                                buttonText: 'Browse',
                                hidden: true,
                                listeners: {
                                    'change': function (fb, v) {
                                        Ext.getCmp('Koltiva.view.IMS.WinFormImsAssetFarmerAPDRcpMaster-Form').getForm().submit({
                                            url: m_api + '/ims_asset_rcp/farmer_apd_rcp_image',
                                            clientValidation: false,
                                            params: {                                                
                                                RcpID: Ext.getCmp('Koltiva.view.IMS.WinFormImsAssetFarmerAPDRcpMaster-Form-RcpID').getValue()
                                            },
                                            waitMsg: 'Sending Photo...',
                                            success: function (fp, o) {
                                                Ext.getCmp('Koltiva.view.IMS.WinFormImsAssetFarmerAPDRcpMaster-Form-RcpImage').update('<a href="'+m_api_base_url+'/files/ims_asset/farmer_apd_receipt/'+o.result.file_with_rand+'" data-lightbox="image-1" data-title="Receipt File" title="View Image"><img src="'+m_api_base_url+'/files/ims_asset/farmer_apd_receipt/'+o.result.file_with_rand+'" style="float:right;height:160px;" /></a>');
                                                Ext.getCmp('Koltiva.view.IMS.WinFormImsAssetFarmerAPDRcpMaster-Form-RcpImage').doLayout();
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
                                id:'Koltiva.view.IMS.WinFormImsAssetFarmerAPDRcpMaster-Form-RcpImage',
                                hidden: true,
                                html:'<img src="'+m_api_base_url+'/images/video/thumb-defa.png" width="240" height="150" style="float:right;" />'        
                            },{
                                id:'Koltiva.view.IMS.WinFormImsAssetFarmerAPDRcpMaster-Form-RcpImageDescription',
                                hidden: true,
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
                id: 'Koltiva.view.IMS.WinFormImsAssetFarmerAPDRcpMaster-Form-BtnSave',
                handler: function () {
                    var FormNya = Ext.getCmp('Koltiva.view.IMS.WinFormImsAssetFarmerAPDRcpMaster-Form').getForm();
                    var FormValidOrNot = FormNya.isValid();
                    var NotValidMessages = '';

                    //Cek apakah item ada terpilih
                    if (
                            Ext.getCmp('Koltiva.view.IMS.WinFormImsAssetFarmerAPDRcpMaster-Form-ItemAPD_KotakPestisida').getValue() == true ||
                            Ext.getCmp('Koltiva.view.IMS.WinFormImsAssetFarmerAPDRcpMaster-Form-ItemAPD_Masker').getValue() == true ||
                            Ext.getCmp('Koltiva.view.IMS.WinFormImsAssetFarmerAPDRcpMaster-Form-ItemAPD_SarungTangan').getValue() == true ||
                            Ext.getCmp('Koltiva.view.IMS.WinFormImsAssetFarmerAPDRcpMaster-Form-ItemAPD_Goggles').getValue() == true ||
                            Ext.getCmp('Koltiva.view.IMS.WinFormImsAssetFarmerAPDRcpMaster-Form-ItemAPD_Boots').getValue() == true ||
                            Ext.getCmp('Koltiva.view.IMS.WinFormImsAssetFarmerAPDRcpMaster-Form-ItemAPD_Mantel').getValue() == true
                            ) {
                        FormValidOrNot = FormValidOrNot && true;
                    } else {
                        FormValidOrNot = false;
                        NotValidMessages = '<br> * ' + lang('No item selected');
                    }

                    if (FormValidOrNot == true) {
                        FormNya.submit({
                            url: m_api + '/ims_asset_rcp/farmer_apd_rcp',
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
                            msg: 'Form not valid yet' + NotValidMessages,
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
            thisObj.setTitle(lang('IMS - Farmer APD Receipt Form')+' ['+thisObj.viewVar.FarmerID+' - '+thisObj.viewVar.FarmerName+']')
            Ext.getCmp('Koltiva.view.IMS.WinFormImsAssetFarmerAPDRcpMaster-Form-IMSID').setValue(thisObj.viewVar.IMSID);
            Ext.getCmp('Koltiva.view.IMS.WinFormImsAssetFarmerAPDRcpMaster-Form-FarmerID').setValue(thisObj.viewVar.FarmerID);

            if(thisObj.viewVar.OpsiDisplay == 'update'){
                //Set Visible Receiver
                Ext.getCmp('Koltiva.view.IMS.WinFormImsAssetFarmerAPDRcpMaster-Form-ReceiverStatusRowRadioGroup').setVisible(true);
                Ext.getCmp('Koltiva.view.IMS.WinFormImsAssetFarmerAPDRcpMaster-Form-ReceiverDate').setVisible(true);
                Ext.getCmp('Koltiva.view.IMS.WinFormImsAssetFarmerAPDRcpMaster-Form-ReceiverFile').setVisible(true);
                Ext.getCmp('Koltiva.view.IMS.WinFormImsAssetFarmerAPDRcpMaster-Form-RcpImage').setVisible(true);
                Ext.getCmp('Koltiva.view.IMS.WinFormImsAssetFarmerAPDRcpMaster-Form-RcpImageDescription').setVisible(true);


                var FormNya = Ext.getCmp('Koltiva.view.IMS.WinFormImsAssetFarmerAPDRcpMaster-Form').getForm();
                FormNya.load({
                    url: m_api + '/ims_asset_rcp/farmer_apd_rcp_form_data',
                    method: 'GET',
                    params: {
                        RcpID: thisObj.viewVar.RcpID
                    },
                    success: function(form, action) {
                        var r = Ext.decode(action.response.responseText);
                        //console.log(r);

                        if(r.data.ReceiverFile != ""){
                            var FotoReceiverFile = m_api_base_url + '/files/ims_asset/farmer_apd_receipt/'+ r.data.ReceiverFile;
                            var angkaRand = Math.floor((Math.random() * 100) + 1);
    
                            checkImageExistsGeneral(FotoReceiverFile, function(existsImage) {
                                if (existsImage == true) {
                                    Ext.getCmp('Koltiva.view.IMS.WinFormImsAssetFarmerAPDRcpMaster-Form-RcpImage').update('<a href="'+FotoReceiverFile+'" data-lightbox="image-1" data-title="Receipt File" title="View Image"><img src="'+FotoReceiverFile+'?'+angkaRand+'" style="float:right;height:160px;" /></a>');
                                } else {
                                    Ext.getCmp('Koltiva.view.IMS.WinFormImsAssetFarmerAPDRcpMaster-Form-RcpImage').update('<img src="'+m_api_base_url+'/images/video/thumb-defa.png" width="240" height="150" style="float:right;" />');
                                }
                                Ext.getCmp('Koltiva.view.IMS.WinFormImsAssetFarmerAPDRcpMaster-Form-RcpImage').doLayout();
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
    }
});
/******************************************
 *  Author : n1colius.lau@gmail.com   
 *  Created On : Thu Jan 24 2019
 *  File : WinFormICSVerifyFarmer.js
 *******************************************/

/*
    Param2 yg diperlukan ketika load View ini
    - CallFrom
    - IMSID
    - FarmerID
    - AFLStatus
*/

Ext.define('Koltiva.view.IMS.WinFormICSVerifyFarmer' ,{
    extend: 'Ext.window.Window',
    id: 'Koltiva.view.IMS.WinFormICSVerifyFarmer',
    title: lang('ICS - Verify Farmer Form'),
    closable: true,
    modal: true,
    closeAction: 'destroy',
    width: '46%',
    height: '80%',
    cls: 'Sfr_LayoutPopupWindows',
    overflowY: 'auto',
    viewVar: false,
    setViewVar: function(value){
        this.viewVar = value;
    },
    listeners: {
        show: function(){
            var thisObj = this;
            var FormNya = Ext.getCmp('Koltiva.view.IMS.WinFormICSVerifyFarmer-Form').getForm();

            //Cek Status AFL nya dl
            if(thisObj.viewVar.AFLStatus == '-'){
                Ext.MessageBox.show({
                    title: 'Information',
                    msg: lang('This farmer does not have a certified status yet'),
                    buttons: Ext.MessageBox.OK,
                    animateTarget: 'mb9',
                    icon: 'ext-mb-info'
                });
                thisObj.close();
            }else{
                //Ambil data dan cek data sebelum isi form
                FormNya.load({
                    url: m_api + '/ims/ics_farmer_verified_form_data',
                    method: 'GET',
                    params: {
                        IMSID: thisObj.viewVar.IMSID,
                        FarmerID: thisObj.viewVar.FarmerID
                    },
                    success: function(form, action) {
                        var r = Ext.decode(action.response.responseText);
                        //console.log(r);

                        if(r.data.CertStatusVerified == null){
                            Ext.getCmp('Koltiva.view.IMS.WinFormICSVerifyFarmer-Form-CertStatusVerifiedChangeBy').setVisible(false);
                        }
                        if(thisObj.viewVar.CallFrom == 'VerifyCL'){
                            if(r.data.CertStatusVerified == "2"){
                                //Lock Form
                                Ext.getCmp('Koltiva.view.IMS.WinFormICSVerifyFarmer-Form').query('.textfield, .checkboxfield, .datefield, .combobox, .radiogroup, .textarea, .boxselect').forEach(function(c){c.setReadOnly(true);});
                                Ext.getCmp('Koltiva.view.IMS.WinFormICSVerifyFarmer-BtnSave').hide();
                            }else{
                                Ext.getCmp('Koltiva.view.IMS.WinFormICSVerifyFarmer-Form-CertStatusVerified2').setVisible(false);
                            }
                        }

                        if(r.data.CekSur == false){
                            Ext.MessageBox.show({
                                title: 'Information',
                                msg: r.data.CekSurMessage,
                                buttons: Ext.MessageBox.OK,
                                animateTarget: 'mb9',
                                icon: 'ext-mb-info'
                            });
                            thisObj.close();
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
                        thisObj.close();
                    }
                });
            }
        }
    },
    initComponent: function() {
        var thisObj = this;

        //items -------------------------------------------------------------- (begin)
        thisObj.items = [{
            xtype: 'form',
            id: 'Koltiva.view.IMS.WinFormICSVerifyFarmer-Form',
            padding:'5 25 5 8',
            items:[{
                layout: 'column',
                border: false,
                items:[{
                    columnWidth: 1,
                    layout:'form',
                    items:[{
                        xtype: 'hiddenfield',
                        id: 'Koltiva.view.IMS.WinFormICSVerifyFarmer-Form-IMSID',
                        name: 'Koltiva.view.IMS.WinFormICSVerifyFarmer-Form-IMSID',
                        value: thisObj.viewVar.IMSID
                    },{
                        xtype: 'textfield',
                        id: 'Koltiva.view.IMS.WinFormICSVerifyFarmer-Form-FarmerID',
                        name: 'Koltiva.view.IMS.WinFormICSVerifyFarmer-Form-FarmerID',
                        fieldLabel: lang('Farmer ID'),
                        labelWidth: 175,
                        readOnly: true
                    },{
                        xtype: 'textfield',
                        id: 'Koltiva.view.IMS.WinFormICSVerifyFarmer-Form-FarmerName',
                        name: 'Koltiva.view.IMS.WinFormICSVerifyFarmer-Form-FarmerName',
                        fieldLabel: lang('Farmer Name'),
                        labelWidth: 175,
                        readOnly: true
                    },{
                        xtype: 'radiogroup',
                        allowBlank: false,
                        fieldLabel: lang('Verify Status'),
                        labelWidth: 175,
                        columns: 1,
                        allowBlank: false,
                        id:'Koltiva.view.IMS.WinFormICSVerifyFarmer-Form-RowCertStatusVerified',
                        items: [{
                            boxLabel: lang('Verified by CL'),
                            inputValue: '1',
                            id: 'Koltiva.view.IMS.WinFormICSVerifyFarmer-Form-CertStatusVerified1',
                            name: 'Koltiva.view.IMS.WinFormICSVerifyFarmer-Form-CertStatusVerified'
                        }, {
                            boxLabel: lang('Verified by IMS Manager'),
                            inputValue: '2',
                            id: 'Koltiva.view.IMS.WinFormICSVerifyFarmer-Form-CertStatusVerified2',
                            name: 'Koltiva.view.IMS.WinFormICSVerifyFarmer-Form-CertStatusVerified'
                        }]
                    },{
                        xtype: 'textfield',
                        id: 'Koltiva.view.IMS.WinFormICSVerifyFarmer-Form-CertStatusVerifiedChangeBy',
                        name: 'Koltiva.view.IMS.WinFormICSVerifyFarmer-Form-CertStatusVerifiedChangeBy',
                        fieldLabel: lang('Verified by'),
                        labelWidth: 175,
                        readOnly: true
                    },{
                        xtype: 'textareafield',
                        id: 'Koltiva.view.IMS.WinFormICSVerifyFarmer-Form-CertStatusVerifiedComment',
                        name: 'Koltiva.view.IMS.WinFormICSVerifyFarmer-Form-CertStatusVerifiedComment',
                        allowBlank: false,
                        fieldLabel: lang('Comment'),
                        labelWidth: 175,
                        height:200
                    }]
                }]
            }]
        }];
        //items -------------------------------------------------------------- (end)

        //buttons -------------------------------------------------------------- (begin)
        thisObj.buttons = [{
                icon: varjs.config.base_url + 'images/icons/new/save.png',
                cls: 'Sfr_BtnFormBlue',
                overCls: 'Sfr_BtnFormBlue-Hover',
                text: lang('Save'),
                id: 'Koltiva.view.IMS.WinFormICSVerifyFarmer-BtnSave',
                handler: function () {
                    var FormNya = Ext.getCmp('Koltiva.view.IMS.WinFormICSVerifyFarmer-Form').getForm();
                    var FormValidOrNot = FormNya.isValid();

                    if (FormValidOrNot == true) {
                        FormNya.submit({
                            url: m_api + '/ims/ics_farmer_verified_form',
                            method: 'POST',
                            waitMsg: 'Saving data...',
                            success: function (rp, o) {
                                var r = Ext.decode(o.response.responseText);

                                Ext.MessageBox.show({
                                    title: 'Information',
                                    msg: r.message,
                                    buttons: Ext.MessageBox.OK,
                                    animateTarget: 'mb9',
                                    icon: 'ext-mb-success'
                                });

                                Ext.data.StoreManager.lookup('StoreImgEventDetailGridAfl').load();
                                thisObj.close();
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
    }
});
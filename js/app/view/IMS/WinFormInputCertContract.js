/*
* @Author: nikolius
* @Date:   2018-07-19 14:56:49
* @Last Modified by:   nikolius
* @Last Modified time: 2018-07-20 11:37:07
*/

/*
    Param2 yg diperlukan ketika load View ini
    - FarmerID
    - IMSID
*/

function checkImageExists(imageUrl, callBack) {
    var imageData = new Image();
    imageData.onload = function() {
        callBack(true);
    };
    imageData.onerror = function() {
        callBack(false);
    };
    imageData.src = imageUrl;
}

Ext.define('Koltiva.view.IMS.WinFormInputCertContract' ,{
    extend: 'Ext.window.Window',
    id: 'Koltiva.view.IMS.WinFormInputCertContract',
    title: lang('Certification Contract Form'),
    closable: true,
    modal: true,
    closeAction: 'destroy',
    width: '44%',
    height: '60%',
    overflowY: 'auto',
    viewVar: false,
    setViewVar: function(value){
        this.viewVar = value;
    },
    listeners: {
        afterRender: function(){
            var thisObj = this;
            var FormNya = Ext.getCmp('Koltiva.view.IMS.WinFormInputCertContract-Form').getForm();

            //load formnya
            FormNya.load({
                url: m_api + '/ims/pre_afl_input_certification_contract',
                method: 'GET',
                params: {
                    FarmerID: thisObj.viewVar.FarmerID,
                    IMSID: thisObj.viewVar.IMSID
                },
                success: function(form, action) {
                    var r = Ext.decode(action.response.responseText);
                    console.log(r);

                    if(r.data.CertContractFile != "" && r.data.CertContractFile != null){
                        Ext.getCmp('Koltiva.view.IMS.WinFormInputCertContract-Form-CertContractFileUrl').update('<a style="text-decoration:underline;" href="'+r.data.CertContractFile+'" target="_blank">'+lang('View Certification Contract File')+'</a>');
                    }

                    if(r.data.CertContractFilePath != "" && r.data.CertContractFilePath != null){
                        Ext.getCmp('Koltiva.view.IMS.WinFormInputCertContract-Form-CertContractFileUrlOld').setValue(r.data.CertContractFilePath);
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
    },
    initComponent: function() {
        var thisObj = this;

        //Items -------------------------------------------------------------- (Begin)
        thisObj.items = [{
            xtype: 'form',
            id: 'Koltiva.view.IMS.WinFormInputCertContract-Form',
            fileUpload: true,
            padding:'5 25 5 8',
            items:[{
                layout: 'column',
                border: false,
                items:[{
                    columnWidth: 1,
                    layout:'form',
                    items:[{
                    	xtype: 'hiddenfield',
                        id: 'Koltiva.view.IMS.WinFormInputCertContract-Form-IMSID',
                        name: 'Koltiva.view.IMS.WinFormInputCertContract-Form-IMSID'
                    },{
                    	xtype: 'hiddenfield',
                        id: 'Koltiva.view.IMS.WinFormInputCertContract-Form-FarmerID',
                        name: 'Koltiva.view.IMS.WinFormInputCertContract-Form-FarmerID'
                    },{
                    	xtype: 'textfield',
                        id: 'Koltiva.view.IMS.WinFormInputCertContract-Form-MemberDisplayID',
                        name: 'Koltiva.view.IMS.WinFormInputCertContract-Form-MemberDisplayID',
                        fieldLabel: 'Farmer ID',
                        labelWidth: 225,
                        readOnly: true
                    },{
                    	xtype: 'textfield',
                        id: 'Koltiva.view.IMS.WinFormInputCertContract-Form-FarmerName',
                        name: 'Koltiva.view.IMS.WinFormInputCertContract-Form-FarmerName',
                        fieldLabel: lang('Farmer Name'),
                        labelWidth: 225,
                        readOnly: true
                    },{
                    	xtype: 'radiogroup',
			            allowBlank: false,
			            fieldLabel: lang('Status'),
			            labelWidth: 225,
			            columns: 2,
			            items: [{
			                boxLabel: lang('Agree'),
			                inputValue: '1',
			                id: 'Koltiva.view.IMS.WinFormInputCertContract-Form-CertContractStatus1',
			                name: 'Koltiva.view.IMS.WinFormInputCertContract-Form-CertContractStatus'
			            },{
			            	boxLabel: lang('Disagree'),
			                inputValue: '2',
			                id: 'Koltiva.view.IMS.WinFormInputCertContract-Form-CertContractStatus2',
			                name: 'Koltiva.view.IMS.WinFormInputCertContract-Form-CertContractStatus'
			            }]
                    },{
                    	xtype: 'datefield',
                        id: 'Koltiva.view.IMS.WinFormInputCertContract-Form-CertContractSignDate',
                        name: 'Koltiva.view.IMS.WinFormInputCertContract-Form-CertContractSignDate',
                        fieldLabel: lang('Sign Date'),
                        labelWidth: 225,
                        allowBlank: false,
                        format: 'Y-m-d'
                    },{
                    	xtype: 'fileuploadfield',
                        fieldLabel: lang('Certification Contract File'),
                        labelWidth: 225,
                        id: 'Koltiva.view.IMS.WinFormInputCertContract-Form-CertContractFile',
                        name: 'Koltiva.view.IMS.WinFormInputCertContract-Form-CertContractFile',
                        buttonText: 'Browse',
                        listeners: {
                            'change': function (fb, v) {
                            	var FormNya = Ext.getCmp('Koltiva.view.IMS.WinFormInputCertContract-Form').getForm();
                            	FormNya.submit({
                                    url: m_api + '/ims/certification_contract',
                                    clientValidation: false,
                                    waitMsg: 'Sending File...',
                                    params: {
                                        FarmerID: Ext.getCmp('Koltiva.view.IMS.WinFormInputCertContract-Form-FarmerID').getValue(),
                                        IMSID: Ext.getCmp('Koltiva.view.IMS.WinFormInputCertContract-Form-IMSID').getValue(),
                                    },
                                    success: function (fp, o) {
                                        Ext.getCmp('Koltiva.view.IMS.WinFormInputCertContract-Form-CertContractFileUrl').update('<a style="text-decoration:underline;" href="'+o.result.file+'" target="_blank">'+lang('View Certification Contract File')+'</a>');
                                        Ext.getCmp('Koltiva.view.IMS.WinFormInputCertContract-Form-CertContractFileUrlOld').setValue(o.result.filepath);

                                        Ext.MessageBox.show({
                                            title: 'Information',
                                            msg: lang('File Uploaded'),
                                            buttons: Ext.MessageBox.OK,
                                            animateTarget: 'mb9',
                                            icon: 'ext-mb-success'
                                        });
                                    },
                                    failure: function (fp, o) {
                                        var r = Ext.decode(o.response.responseText);
                                        Ext.MessageBox.show({
                                            title: 'Failed',
                                            msg: r.message,
                                            buttons: Ext.MessageBox.OK,
                                            animateTarget: 'mb9',
                                            icon: 'ext-mb-error'
                                        });
                                    }
                                });
                            }
                        }
                    },{
                    	layout: 'column',
                        border: false,
                        items:[{
                            columnWidth: 1,
                            layout:'form',
                            style:'margin-top:-15px;',
                            items:[{
                                id:'Koltiva.view.IMS.WinFormInputCertContract-Form-CertContractFileUrl',
                                html:'<a style="text-decoration:underline;" href="#" target="_blank">No Certification File</a>'
                            }]
                        }]
                    },{
                        xtype: 'textfield',
                        id: 'Koltiva.view.IMS.WinFormInputCertContract-Form-CertContractFileUrlOld',
                        name: 'Koltiva.view.IMS.WinFormInputCertContract-Form-CertContractFileUrlOld',
                        inputType: 'hidden'
                    }]
                }]
            }]
        }];
        //Items -------------------------------------------------------------- (End)

        //buttons -------------------------------------------------------------- (begin)
        thisObj.buttons = [{
                icon: varjs.config.base_url + 'images/icons/new/save.png',
                text: lang('Save'),
                margin: '5 15 5 5',
                cls: 'Sfr_BtnFormBlue',
                overCls: 'Sfr_BtnFormBlue-Hover',
                id: 'Koltiva.view.IMS.WinFormInputCertContract-Form-BtnSave',
                handler: function () {
                    var FormNya = Ext.getCmp('Koltiva.view.IMS.WinFormInputCertContract-Form').getForm();
                    var FormValidOrNot = FormNya.isValid();

                    if (FormValidOrNot == true) {
                        FormNya.submit({
                            url: m_api + '/ims/certification_contract_form',
                            method: 'POST',
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
        //buttons -------------------------------------------------------------- (end)

        this.callParent(arguments);
    }
});
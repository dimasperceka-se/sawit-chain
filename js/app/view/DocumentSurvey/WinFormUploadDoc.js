/*
* @Author: nikolius
* @Date:   2017-08-11 13:43:09
* @Last Modified by:   nikolius
* @Last Modified time: 2017-08-11 15:57:14
*/

/*
    Param2 yg diperlukan ketika load View ini
    1. MemberID
    2. DocNameID
    3. Store yg dipanggil
*/

// Define Variabel2 / Object2 / Fungsi yg diperlukan oleh view ini (begin)

// Define Variabel2 / Object2 / Fungsi yg diperlukan oleh view ini (end)

Ext.define('Koltiva.view.DocumentSurvey.WinFormUploadDoc' ,{
    extend: 'Ext.window.Window',
    id: 'Koltiva.view.DocumentSurvey.WinFormUploadDoc',
    title: lang('Form Upload Document Survey'),
    closable: true,
    modal: true,
    closeAction: 'destroy',
    width: '40%',
    height: '100px',
    overflowY: 'auto',
    viewVar: false,
    setViewVar: function(value){
        this.viewVar = value;
    },
    initComponent: function() {
        var thisObj = this;

        //items ---------------------------------------------------------------------------------------------------------------------------- (begin)
        thisObj.items = [{
            xtype: 'form',
            id: 'Koltiva.view.DocumentSurvey.WinFormUploadDoc-Form',
            fileUpload: true,
            padding:'5 25 5 8',
            items:[{
                layout: 'column',
                border: false,
                items:[{
                    columnWidth: 1,
                    layout:'form',
                    items:[{
                        xtype: 'fileuploadfield',
                        fieldLabel: lang('Consent Notes'),
                        labelWidth: 300,
                        id: 'Koltiva.view.DocumentSurvey.WinFormUploadDoc-Form-ConsentNotesInput',
                        name: 'Koltiva.view.DocumentSurvey.WinFormUploadDoc-Form-ConsentNotesInput',
                        buttonText: 'Browse',
                        hidden: true,
                        listeners: {
                            'change': function (fb, v) {
                                Ext.getCmp('Koltiva.view.DocumentSurvey.WinFormUploadDoc-Form').getForm().submit({
                                    url: m_api + '/grower/consent_member_upload',
                                    clientValidation: false,
                                    params: {
                                        MemberID: thisObj.viewVar.MemberID,
                                        DocNameID: thisObj.viewVar.DocNameID
                                    },
                                    waitMsg: 'Sending File...',
                                    success: function (fp, o) {
                                        Ext.MessageBox.show({
                                            title: 'Information',
                                            msg: lang('File Uploaded'),
                                            buttons: Ext.MessageBox.OK,
                                            animateTarget: 'mb9',
                                            icon: 'ext-mb-success'
                                        });

                                        //refresh store yg manggil
                                        thisObj.viewVar.callerStore.load();

                                        //tutup popup
                                        thisObj.close();
                                    },
                                    failure: function(fp, o){
                                        Ext.MessageBox.show({
                                            title: 'Warning',
                                            msg: lang('Upload Failed'),
                                            buttons: Ext.MessageBox.OK,
                                            animateTarget: 'mb9',
                                            icon: 'ext-mb-error'
                                        });
                                        //tutup popup
                                        thisObj.close();
                                    }
                                });
                            }
                        }
                    },{
                        xtype: 'fileuploadfield',
                        fieldLabel: lang('Withdrawal of Consent Notes'),
                        labelWidth: 300,
                        id: 'Koltiva.view.DocumentSurvey.WinFormUploadDoc-Form-WithdrawalConsentNotesInput',
                        name: 'Koltiva.view.DocumentSurvey.WinFormUploadDoc-Form-WithdrawalConsentNotesInput',
                        buttonText: 'Browse',
                        hidden: true,
                        listeners: {
                            'change': function (fb, v) {
                                Ext.getCmp('Koltiva.view.DocumentSurvey.WinFormUploadDoc-Form').getForm().submit({
                                    url: m_api + '/grower/consent_member_upload',
                                    clientValidation: false,
                                    params: {
                                        MemberID: thisObj.viewVar.MemberID,
                                        DocNameID: thisObj.viewVar.DocNameID
                                    },
                                    waitMsg: 'Sending File...',
                                    success: function (fp, o) {
                                        Ext.MessageBox.show({
                                            title: 'Information',
                                            msg: lang('File Uploaded'),
                                            buttons: Ext.MessageBox.OK,
                                            animateTarget: 'mb9',
                                            icon: 'ext-mb-success'
                                        });

                                        //refresh store yg manggil
                                        thisObj.viewVar.callerStore.load();

                                        //tutup popup
                                        thisObj.close();
                                    },
                                    failure: function(fp, o){
                                        Ext.MessageBox.show({
                                            title: 'Warning',
                                            msg: lang('Upload Failed'),
                                            buttons: Ext.MessageBox.OK,
                                            animateTarget: 'mb9',
                                            icon: 'ext-mb-error'
                                        });
                                        //tutup popup
                                        thisObj.close();
                                    }
                                });
                            }
                        }
                    }]
                }]
            }]
        }];

        this.callParent(arguments);
    },
    listeners: {
        afterRender: function(){
            var thisObj = this;
            if(thisObj.viewVar.DocNameID == 'ConNotes'){
                Ext.getCmp('Koltiva.view.DocumentSurvey.WinFormUploadDoc-Form-ConsentNotesInput').setVisible(true);
            }
            if(thisObj.viewVar.DocNameID == 'Withdrawal'){
                Ext.getCmp('Koltiva.view.DocumentSurvey.WinFormUploadDoc-Form-WithdrawalConsentNotesInput').setVisible(true);
            }
        }
    }
});
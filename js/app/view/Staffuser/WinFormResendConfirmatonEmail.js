/******************************************
 *  Author : n1colius.lau@gmail.com   
 *  Created On : Thu Dec 05 2019
 *  File : WinFormResendConfirmatonEmail.js
 *******************************************/
/*
    Param2 yg diperlukan ketika load View ini
    * PersonID
    * UserId
*/

Ext.define('Koltiva.view.Staffuser.WinFormResendConfirmatonEmail' ,{
    extend: 'Ext.window.Window',
    id: 'Koltiva.view.Staffuser.WinFormResendConfirmatonEmail',
    cls: 'Sfr_LayoutPopupWindows',
    title: lang('Resend User Confirmation Email'),
    closable: true,
    modal: true,
    closeAction: 'destroy',
    width: '50%',
    height: '50%',
    overflowY: 'auto',
    viewVar: false,
    setViewVar: function(value){
        this.viewVar = value;
    },
    initComponent: function() {
        var thisObj = this;
        let labelWidth = 220;

        //items -------------------------------------------------------------- (begin)
        thisObj.items = [{
        	xtype: 'form',
            id: 'Koltiva.view.Staffuser.WinFormResendConfirmatonEmail-Form',
            padding:'5 25 5 8',
            items:[{
                layout: 'column',
                border: false,
                items:[{
                    columnWidth: 1,
                    layout:'form',
                    items:[{
                        xtype: 'textfield',
                        labelWidth: labelWidth,
                        inputType: 'password',
                        fieldLabel: lang('Temporary Password'),
                        allowBlank: false,
                        baseCls: 'Sfr_FormInputMandatory',
                        id: 'Koltiva.view.Staffuser.WinFormResendConfirmatonEmail-Form-UserPassword',
                        name: 'Koltiva.view.Staffuser.WinFormResendConfirmatonEmail-Form-UserPassword',
                        listeners:{
                            afterrender:function(cmp){
                                cmp.inputEl.set({
                                    autocomplete:'off'
                                });
                            }
                        }
                    },{
                        xtype: 'textfield',
                        labelWidth: labelWidth,
                        inputType: 'password',
                        fieldLabel: lang('Re Type Temporary Password'),
                        allowBlank: false,
                        baseCls: 'Sfr_FormInputMandatory',
                        id: 'Koltiva.view.Staffuser.WinFormResendConfirmatonEmail-Form-UserPasswordRe',
                        name: 'Koltiva.view.Staffuser.WinFormResendConfirmatonEmail-Form-UserPasswordRe',
                        listeners:{
                            afterrender:function(cmp){
                                cmp.inputEl.set({
                                    autocomplete:'off'
                                });
                            }
                        }
                    }]
                }]
            }]
        }];
        //items -------------------------------------------------------------- (end)

        //buttons -------------------------------------------------------------- (begin)
        thisObj.buttons = [{
            icon: varjs.config.base_url + 'images/icons/new/save.png',
            cls:'Sfr_BtnFormBlue',
            overCls:'Sfr_BtnFormBlue-Hover',
            text: lang('Resend Email Confirmation'),
            id: 'Koltiva.view.Staffuser.WinFormResendConfirmatonEmail-Form-BtnSave',
            handler: function () {
                //Submit Form
                let Form = Ext.getCmp('Koltiva.view.Staffuser.WinFormResendConfirmatonEmail-Form').getForm();
                if (Form.isValid()) {

                    //Data Control Tambahan ======================================= (Begin)
                    thisObj.AddValidation = true;
                    thisObj.MsgAddValidation = "";
                    thisObj.AddValidationBasicForm();
                    //Data Control Tambahan ======================================= (Emd)

                    if(thisObj.AddValidation == true){
                        
                        Ext.MessageBox.show({
                            msg: lang('Please wait'),
                            progressText: lang('Loading'),
                            width: 300,
                            wait: true,
                            waitConfig: {
                                interval: 200
                            },
                            icon: 'ext-mb-info', //custom class in msg-box.html
                            animateTarget: 'mb9'
                        });

                        Ext.Ajax.request({
                            waitMsg: 'Please Wait',
                            url: m_api + '/staffuser/resend_confirmation_email',
                            method: 'POST',
                            params: {
                                PersonID: thisObj.viewVar.PersonID,
                                UserPassword: Ext.getCmp('Koltiva.view.Staffuser.WinFormResendConfirmatonEmail-Form-UserPassword').getValue()
                            },
                            success: function(rp, o) {
                                Ext.MessageBox.hide();
                                var r = Ext.decode(rp.responseText);
                                Ext.MessageBox.show({
                                    title: lang('Information'),
                                    msg: r.message,
                                    buttons: Ext.MessageBox.OK,
                                    animateTarget: 'mb9',
                                    icon: 'ext-mb-success'
                                });
                                thisObj.close();
                            },
                            failure: function(rp, o) {
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
                                }
                                catch(err) {
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
                            title: lang('Attention'),
                            msg: thisObj.MsgAddValidation,
                            buttons: Ext.MessageBox.OK,
                            animateTarget: 'mb9',
                            icon: 'ext-mb-info'
                        });
                    }

                } else {
                    Ext.MessageBox.show({
                        title: lang('Attention'),
                        msg: lang('Form not complete yet'),
                        buttons: Ext.MessageBox.OK,
                        animateTarget: 'mb9',
                        icon: 'ext-mb-info'
                    });
                }
            }
        },{
            icon: varjs.config.base_url + 'images/icons/new/close.png',
			text: lang('Close'),
			cls:'Sfr_BtnFormGrey',
			overCls:'Sfr_BtnFormGrey-Hover',
            handler: function() {
                thisObj.close();
            }
        }];
        //buttons -------------------------------------------------------------- (end)

        this.callParent(arguments);
    },
    AddValidationBasicForm: function() {
        var thisObj = this;
        var ArrMsg = [];
        thisObj.AddValidation = true;

        //Password Sama ================================================== (Begin)
        if(Ext.getCmp('Koltiva.view.Staffuser.WinFormResendConfirmatonEmail-Form-UserPassword').getValue() != Ext.getCmp('Koltiva.view.Staffuser.WinFormResendConfirmatonEmail-Form-UserPasswordRe').getValue()) {
            thisObj.AddValidation = false;
            ArrMsg.push(lang('Password did not match'));
        }
        //Password Sama ================================================== (End)

        if(thisObj.AddValidation == false){
            var HtmlMsg = '<ul>';
            for (var index = 0; index < ArrMsg.length; index++) {
                HtmlMsg += '<li>'+ArrMsg[index]+'</li>'
            }
            HtmlMsg+='</ul>';
            thisObj.MsgAddValidation = HtmlMsg;
        }
    }
});
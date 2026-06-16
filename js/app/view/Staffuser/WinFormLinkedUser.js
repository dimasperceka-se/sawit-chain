/******************************************
 *  Author : n1colius.lau@gmail.com
 *  Created On : Thu Dec 05 2019
 *  File : WinFormLinkedUser.js
 *******************************************/
/*
    Param2 yg diperlukan ketika load View ini
    * PersonID
    * UserId
*/

Ext.define('Koltiva.view.Staffuser.WinFormLinkedUser' ,{
    extend: 'Ext.window.Window',
    id: 'Koltiva.view.Staffuser.WinFormLinkedUser',
    cls: 'Sfr_LayoutPopupWindows',
    title: lang('Linked Account to Identity Server'),
    closable: true,
    modal: true,
    closeAction: 'destroy',
    width: '48%',
    height: '60%',
    overflowY: 'auto',
    viewVar: false,
    setViewVar: function(value){
        this.viewVar = value;
    },
    initComponent: function() {
        var thisObj = this;
        let labelWidth = 185;

        //items -------------------------------------------------------------- (begin)
        thisObj.items = [{
            xtype: 'form',
            id: 'Koltiva.view.Staffuser.WinFormLinkedUser-FormCreate',
            padding:'5 25 5 8',
            items:[{
                layout: 'column',
                border: false,
                items:[{
                    columnWidth: 1,
                    layout:'form',
                    items:[{
                        xtype: 'textfield',
                        id: 'Koltiva.view.Staffuser.WinFormLinkedUser-FormCreate-UserId',
                        name: 'Koltiva.view.Staffuser.WinFormLinkedUser-FormCreate-UserId',
                        inputType: 'hidden'
                    },{
                        xtype: 'textfield',
                        id: 'Koltiva.view.Staffuser.WinFormLinkedUser-FormCreate-PersonID',
                        name: 'Koltiva.view.Staffuser.WinFormLinkedUser-FormCreate-PersonID',
                        inputType: 'hidden'
                    },{
                        xtype: 'textfield',
                        labelWidth: labelWidth,
                        fieldLabel: lang('Username'),
                        allowBlank: false,
                        baseCls: 'Sfr_FormInputMandatory',
                        id: 'Koltiva.view.Staffuser.WinFormLinkedUser-FormCreate-Username',
                        name: 'Koltiva.view.Staffuser.WinFormLinkedUser-FormCreate-Username'
                    },{
                        xtype: 'textfield',
                        labelWidth: labelWidth,
                        fieldLabel: lang('Phonenumber'),
                        allowBlank: false,
                        readOnly:true,
                        baseCls: 'Sfr_FormInputMandatory',
                        id: 'Koltiva.view.Staffuser.WinFormLinkedUser-FormCreate-Phonenumber',
                        name: 'Koltiva.view.Staffuser.WinFormLinkedUser-FormCreate-Phonenumber'
                    },{
                        xtype: 'textfield',
                        labelWidth: labelWidth,
                        fieldLabel: lang('Email'),
                        allowBlank: false,
                        readOnly:true,
                        baseCls: 'Sfr_FormInputMandatory',
                        id: 'Koltiva.view.Staffuser.WinFormLinkedUser-FormCreate-Email',
                        name: 'Koltiva.view.Staffuser.WinFormLinkedUser-FormCreate-Email'
                    },{
                        xtype: 'textfield',
                        labelWidth: labelWidth,
                        inputType: 'password',
                        fieldLabel: lang('Password'),
                        allowBlank: false,
                        baseCls: 'Sfr_FormInputMandatory',
                        id: 'Koltiva.view.Staffuser.WinFormLinkedUser-FormCreate-UserPassword',
                        name: 'Koltiva.view.Staffuser.WinFormLinkedUser-FormCreate-UserPassword',
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
                        fieldLabel: lang('Re Type Password'),
                        allowBlank: false,
                        baseCls: 'Sfr_FormInputMandatory',
                        id: 'Koltiva.view.Staffuser.WinFormLinkedUser-FormCreate-UserPasswordRe',
                        name: 'Koltiva.view.Staffuser.WinFormLinkedUser-FormCreate-UserPasswordRe',
                        listeners:{
                            afterrender:function(cmp){
                                cmp.inputEl.set({
                                    autocomplete:'off'
                                });
                            }
                        }
                    },{
                        xtype: 'radiogroup',
                        fieldLabel: lang('Send Email Confirmation'),
                        allowBlank: false,
                        baseCls: 'Sfr_FormInputMandatory',
                        labelWidth: labelWidth,
                        columns: 2,
                        id: 'Koltiva.view.Staffuser.WinFormLinkedUser-FormCreate-RowSendEmailConfirm',
                        items: [{
                            boxLabel: lang('Yes'),
                            name: 'Koltiva.view.Staffuser.WinFormLinkedUser-FormCreate-SendEmailConfirm',
                            inputValue: '1',
                            id: 'Koltiva.view.Staffuser.WinFormLinkedUser-FormCreate-SendEmailConfirmYes',
                            listeners: {
                                change: function () {
                                    return false;
                                }
                            }
                        }, {
                            boxLabel: lang('No'),
                            name: 'Koltiva.view.Staffuser.WinFormLinkedUser-FormCreate-SendEmailConfirm',
                            inputValue: '2',
                            id: 'Koltiva.view.Staffuser.WinFormLinkedUser-FormCreate-SendEmailConfirmNo',
                            listeners: {
                                change: function () {
                                    return false;
                                }
                            }
                        }]
                    },{
                        xtype: 'radiogroup',
                        fieldLabel: lang('Auto-Confirm User Account'),
                        allowBlank: false,
                        baseCls: 'Sfr_FormInputMandatory',
                        labelWidth: labelWidth,
                        columns: 2,
                        id: 'Koltiva.view.Staffuser.WinFormLinkedUser-FormCreate-RowAutoConfirmUser',
                        items: [{
                            boxLabel: lang('Yes'),
                            name: 'Koltiva.view.Staffuser.WinFormLinkedUser-FormCreate-AutoConfirmUser',
                            inputValue: '1',
                            id: 'Koltiva.view.Staffuser.WinFormLinkedUser-FormCreate-AutoConfirmUserYes',
                            listeners: {
                                change: function () {
                                    return false;
                                }
                            }
                        }, {
                            boxLabel: lang('No'),
                            name: 'Koltiva.view.Staffuser.WinFormLinkedUser-FormCreate-AutoConfirmUser',
                            inputValue: '2',
                            id: 'Koltiva.view.Staffuser.WinFormLinkedUser-FormCreate-AutoConfirmUserNo',
                            listeners: {
                                change: function () {
                                    return false;
                                }
                            }
                        }]
                    }]
                }]
            }]
        }];
        //items -------------------------------------------------------------- (begin)

        //buttons -------------------------------------------------------------- (begin)
        thisObj.buttons = [{
            icon: varjs.config.base_url + 'images/icons/new/save.png',
            cls:'Sfr_BtnFormBlue',
            overCls:'Sfr_BtnFormBlue-Hover',
            text: lang('Submit'),
            id: 'Koltiva.view.Staffuser.WinFormLinkedUser-FormCreate-BtnSave',
            handler: function () {
                let Form = Ext.getCmp('Koltiva.view.Staffuser.WinFormLinkedUser-FormCreate').getForm();
                if (Form.isValid()) {

                    //Data Control Tambahan ======================================= (Begin)
                    thisObj.AddValidation = true;
                    thisObj.MsgAddValidation = "";
                    thisObj.AddValidationBasicForm();
                    //Data Control Tambahan ======================================= (Emd)

                    if(thisObj.AddValidation == true){
                        Form.submit({
                            url: m_api + '/staffuser/linked_user_cognito_form',
                            waitMsg: lang('Please wait'),
                            success: function(rp, o){
                                var r = Ext.decode(o.response.responseText);
                                let PersonID = thisObj.viewVar.PersonID;
                                //console.log(r);

                                Ext.MessageBox.show({
                                    title: lang('Information'),
                                    msg: r.message,
                                    buttons: Ext.MessageBox.OK,
                                    animateTarget: 'mb9',
                                    icon: 'ext-mb-success'
                                });
                                thisObj.close();

                                //load ulang page
                                Ext.getCmp('Koltiva.view.Staffuser.MainForm').destroy(); //destory current view
                                let FormMainApp = [];
                                if(Ext.getCmp('Koltiva.view.Staffuser.MainForm') == undefined){
                                    FormMainApp = Ext.create('Koltiva.view.Staffuser.MainForm', {
                                        viewVar: {
                                            OpsiDisplay: 'update',
                                            PersonID: PersonID
                                        }
                                    });
                                }else{
                                    //destroy, create ulang
                                    Ext.getCmp('Koltiva.view.Staffuser.MainForm').destroy();
                                    FormMainApp = Ext.create('Koltiva.view.Staffuser.MainForm', {
                                        viewVar: {
                                            OpsiDisplay: 'update',
                                            PersonID: PersonID
                                        }
                                    });
                                }
                            },
                            failure: function(rp, o){
                                try {
                                    var r = Ext.decode(o.response.responseText);
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
    listeners: {
        afterRender: function(){
            var thisObj = this;

            //Nilai Default
            Ext.getCmp('Koltiva.view.Staffuser.WinFormLinkedUser-FormCreate-SendEmailConfirmYes').setValue(true);
            Ext.getCmp('Koltiva.view.Staffuser.WinFormLinkedUser-FormCreate-AutoConfirmUserNo').setValue(true);

            //Load main Form
            Ext.getCmp('Koltiva.view.Staffuser.WinFormLinkedUser-FormCreate').getForm().load({
                url: m_api + '/staffuser/linked_user_form_open',
                method: 'GET',
                params: {
                    PersonID: thisObj.viewVar.PersonID,
                    UserId: thisObj.viewVar.UserId
                },
                success: function (form, action) {
                    let r = Ext.decode(action.response.responseText);
                    //console.log(r);
                },
                failure: function (form, action) {
                    Ext.MessageBox.show({
                        title: lang('Failed'),
                        msg: lang('Failed to retrieve data'),
                        buttons: Ext.MessageBox.OK,
                        animateTarget: 'mb9',
                        icon: 'ext-mb-error'
                    });
                }
            });

        }
    },
    AddValidationBasicForm: function() {
        var thisObj = this;
        var ArrMsg = [];
        thisObj.AddValidation = true;

        //Password Sama ================================================== (Begin)
        if(Ext.getCmp('Koltiva.view.Staffuser.WinFormLinkedUser-FormCreate-UserPassword').getValue() != Ext.getCmp('Koltiva.view.Staffuser.WinFormLinkedUser-FormCreate-UserPasswordRe').getValue()) {
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
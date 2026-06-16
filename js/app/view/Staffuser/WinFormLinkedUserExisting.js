/******************************************
 *  Author : n1colius.lau@gmail.com   
 *  Created On : Thu Dec 05 2019
 *  File : WinFormLinkedUserExisting.js
 *******************************************/
/*
    Param2 yg diperlukan ketika load View ini
    * PersonID
    * UserId
*/

Ext.define('Koltiva.view.Staffuser.WinFormLinkedUserExisting' ,{
    extend: 'Ext.window.Window',
    id: 'Koltiva.view.Staffuser.WinFormLinkedUserExisting',
    cls: 'Sfr_LayoutPopupWindows',
    title: lang('Linked Account to Identity Server'),
    closable: true,
    modal: true,
    closeAction: 'destroy',
    width: '46%',
    height: '60%',
    overflowY: 'auto',
    viewVar: false,
    setViewVar: function(value){
        this.viewVar = value;
    },
    initComponent: function() {
        var thisObj = this;
        let labelWidth = 150;

        //items -------------------------------------------------------------- (begin)
        thisObj.items = [{
            xtype: 'form',
            id: 'Koltiva.view.Staffuser.WinFormLinkedUserExisting-Form',
            padding:'5 25 5 8',
            items:[{
                layout: 'column',
                border: false,
                items:[{
                    columnWidth: 1,
                    layout:'form',
                    items:[{
                        xtype:'label',
                        cls: 'x-form-item-label',
                        style:'font-style:italic;text-decoration:underline;',
                        text: lang('This staff email address already registered on identity server, you can linked it through this form')
                    },{
                        xtype: 'textfield',
                        id: 'Koltiva.view.Staffuser.WinFormLinkedUserExisting-Form-UserId',
                        name: 'Koltiva.view.Staffuser.WinFormLinkedUserExisting-Form-UserId',
                        inputType: 'hidden'
                    },{
                        xtype: 'textfield',
                        id: 'Koltiva.view.Staffuser.WinFormLinkedUserExisting-Form-PersonID',
                        name: 'Koltiva.view.Staffuser.WinFormLinkedUserExisting-Form-PersonID',
                        inputType: 'hidden'
                    },{
                        xtype: 'textfield',
                        id: 'Koltiva.view.Staffuser.WinFormLinkedUserExisting-Form-UserSub',
                        name: 'Koltiva.view.Staffuser.WinFormLinkedUserExisting-Form-UserSub',
                        inputType: 'hidden'
                    },{
                        xtype: 'textfield',
                        id: 'Koltiva.view.Staffuser.WinFormLinkedUserExisting-Form-UserCogStatus',
                        name: 'Koltiva.view.Staffuser.WinFormLinkedUserExisting-Form-UserCogStatus',
                        inputType: 'hidden'
                    },{
                        xtype: 'textfield',
                        labelWidth: labelWidth,
                        fieldLabel: lang('Fullname'),
                        allowBlank: false,
                        baseCls: 'Sfr_FormInputMandatory',
                        readOnly:true,
                        id: 'Koltiva.view.Staffuser.WinFormLinkedUserExisting-Form-Fullname',
                        name: 'Koltiva.view.Staffuser.WinFormLinkedUserExisting-Form-Fullname'
                    },{
                        xtype: 'radiogroup',
                        fieldLabel: lang('Gender'),
                        allowBlank: false,
                        baseCls: 'Sfr_FormInputMandatory',
                        msgTarget: 'side',
                        readOnly:true,
                        columns: 2,
                        items: [{
                            boxLabel: lang('Male'),
                            name: 'Koltiva.view.Staffuser.WinFormLinkedUserExisting-Form-Gender',
                            inputValue: 'm',
                            id: 'Koltiva.view.Staffuser.WinFormLinkedUserExisting-Form-GenderM',
                            listeners: {
                                change: function () {
                                    return false;
                                }
                            }
                        }, {
                            boxLabel: lang('Female'),
                            name: 'Koltiva.view.Staffuser.WinFormLinkedUserExisting-Form-Gender',
                            inputValue: 'f',
                            id: 'Koltiva.view.Staffuser.WinFormLinkedUserExisting-Form-GenderF',
                            listeners: {
                                change: function () {
                                    return false;
                                }
                            }
                        }]
                    },{
                        xtype: 'textfield',
                        labelWidth: labelWidth,
                        fieldLabel: lang('Phonenumber'),
                        allowBlank: false,
                        baseCls: 'Sfr_FormInputMandatory',
                        readOnly:true,
                        id: 'Koltiva.view.Staffuser.WinFormLinkedUserExisting-Form-Phonenumber',
                        name: 'Koltiva.view.Staffuser.WinFormLinkedUserExisting-Form-Phonenumber'
                    },{
                        xtype: 'textfield',
                        labelWidth: labelWidth,
                        fieldLabel: lang('Email'),
                        allowBlank: false,
                        baseCls: 'Sfr_FormInputMandatory',
                        readOnly:true,
                        id: 'Koltiva.view.Staffuser.WinFormLinkedUserExisting-Form-Email',
                        name: 'Koltiva.view.Staffuser.WinFormLinkedUserExisting-Form-Email'
                    },{
                        xtype: 'textfield',
                        labelWidth: labelWidth,
                        fieldLabel: lang('Username'),
                        allowBlank: false,
                        baseCls: 'Sfr_FormInputMandatory',
                        readOnly:true,
                        id: 'Koltiva.view.Staffuser.WinFormLinkedUserExisting-Form-Username',
                        name: 'Koltiva.view.Staffuser.WinFormLinkedUserExisting-Form-Username'
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
            text: lang('Link'),
            id: 'Koltiva.view.Staffuser.WinFormLinkedUserExisting-Form-BtnSave',
            handler: function () {
                let Form = Ext.getCmp('Koltiva.view.Staffuser.WinFormLinkedUserExisting-Form').getForm();

                Form.submit({
                    url: m_api + '/staffuser/linked_user_existing_cognito_form',
                    waitMsg: lang('Please wait'),
                    success: function(rp, o){
                        var r = Ext.decode(o.response.responseText);
                        let PersonID = thisObj.viewVar.PersonID;

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

            //Load main Form
            Ext.getCmp('Koltiva.view.Staffuser.WinFormLinkedUserExisting-Form').getForm().load({
                url: m_api + '/staffuser/linked_user_existing_form_open',
                method: 'GET',
                params: {
                    PersonID: thisObj.viewVar.PersonID,
                    UserId: thisObj.viewVar.UserId
                },
                success: function (form, action) {
                    let r = Ext.decode(action.response.responseText);
                    //console.log(r);

                    if(r.prosesStatus == "failedGetDataOnAws") {
                        Ext.MessageBox.show({
                            title: lang('Failed'),
                            msg: r.message,
                            buttons: Ext.MessageBox.OK,
                            animateTarget: 'mb9',
                            icon: 'ext-mb-error'
                        });
                    }
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
    }
});
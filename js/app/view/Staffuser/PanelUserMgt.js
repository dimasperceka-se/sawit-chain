/******************************************
 *  Author : n1colius.lau@gmail.com   
 *  Created On : Tue Jul 14 2020
 *  File : PanelUserMgt.js
 *******************************************/
/*
    Param2 yg diperlukan ketika load View ini
    * PersonID
    * OpsiDisplay
*/

Ext.define('Koltiva.view.Staffuser.PanelUserMgt' ,{
    extend: 'Ext.panel.Panel',
    id: 'Koltiva.view.Staffuser.PanelUserMgt',
    style:'margin-left:15px;padding:8px 13px;background:white !important;',
    title:lang('Account Identity'),
    frame: true,
    viewVar: false,
    setViewVar: function(value){
        this.viewVar = value;
    },
    listeners: {
        afterRender: function(){
            var thisObj = this;

            //Biar autocomplete offnya jalan
            Ext.getCmp('Koltiva.view.Staffuser.PanelUserMgt-Form-Username').setReadOnly(true);

            //Checkbox is_admin
            if(m_id_admin == 1) {
                Ext.getCmp('Koltiva.view.Staffuser.PanelUserMgt-Form-RowUserIsAdmin').setVisible(true);
            } else {
                Ext.getCmp('Koltiva.view.Staffuser.PanelUserMgt-Form-RowUserIsAdmin').setVisible(false);
            }

            //Load main Form
            Ext.getCmp('Koltiva.view.Staffuser.PanelUserMgt-Form').getForm().load({
                url: m_api + '/staffuser/account_form_open',
                method: 'GET',
                params: {
                    PersonID: thisObj.viewVar.PersonID
                },
                success: function (form, action) {
                    let r = Ext.decode(action.response.responseText);
                    //console.log(r);

                    switch(r.data.StateStaff) {
                        case 'NOUSER':
                            //set readonly semua
                            Ext.getCmp('Koltiva.view.Staffuser.PanelUserMgt-Form').query('.textfield, .checkboxfield, .datefield, .combobox, .radiogroup').forEach(function(c){c.setReadOnly(true);});
                            Ext.getCmp('Koltiva.view.Staffuser.PanelUserMgt-Form-GroupIds').setDisabled(true);
                            Ext.getCmp('Koltiva.view.Staffuser.PanelUserMgt-Form-AccessStaff').setDisabled(true);
                            Ext.getCmp('Koltiva.view.Staffuser.PanelUserMgt-Form-StaffState').setValue('NOUSER');
                            Ext.getCmp('Koltiva.view.Staffuser.PanelUserMgt-Form-BtnCreate').setVisible(true);
                            Ext.getCmp('Koltiva.view.Staffuser.PanelUserMgt-StatusLinkedCognitoNo').setValue(true);
                        break;
                        case 'NOTLINKED':
                            //set readonly semua
                            Ext.getCmp('Koltiva.view.Staffuser.PanelUserMgt-Form').query('.textfield, .checkboxfield, .datefield, .combobox, .radiogroup').forEach(function(c){c.setReadOnly(true);});
                            Ext.getCmp('Koltiva.view.Staffuser.PanelUserMgt-Form-GroupIds').setDisabled(true);
                            Ext.getCmp('Koltiva.view.Staffuser.PanelUserMgt-Form-AccessStaff').setDisabled(true);
                            Ext.getCmp('Koltiva.view.Staffuser.PanelUserMgt-Form-StaffState').setValue('NOTLINKED');
                            Ext.getCmp('Koltiva.view.Staffuser.PanelUserMgt-Form-UserId').setValue(r.data.UserId);
                            Ext.getCmp('Koltiva.view.Staffuser.PanelUserMgt-Form-BtnLinkend').setVisible(true);
                            Ext.getCmp('Koltiva.view.Staffuser.PanelUserMgt-StatusLinkedCognitoNo').setValue(true);
                        break;
                        case 'LINKEDUNCONFIRMED':
                            Ext.getCmp('Koltiva.view.Staffuser.PanelUserMgt-StatusLinkedCognitoYes').setValue(true);
                            Ext.getCmp('Koltiva.view.Staffuser.PanelUserMgt-Form-BtnSave').setVisible(true);
                            Ext.getCmp('Koltiva.view.Staffuser.PanelUserMgt-Form-BtnResendEmail').setVisible(true);
                            Ext.getCmp('Koltiva.view.Staffuser.PanelUserMgt-Form-BtnResetPass').setVisible(true);

                            //Set Combo Group
                            setTimeout(function(){
                                thisObj.SetSelectedGroups();
                                Ext.getCmp('Koltiva.view.Staffuser.PanelUserMgt-Form-UserGroupIsDefault').setValue(r.data.UserGroupIsDefault);
                            }, 1500);
                        break;
                        case 'LINKEDCONFIRMED':
                            Ext.getCmp('Koltiva.view.Staffuser.PanelUserMgt-StatusLinkedCognitoYes').setValue(true);
                            Ext.getCmp('Koltiva.view.Staffuser.PanelUserMgt-Form-BtnSave').setVisible(true);
                            Ext.getCmp('Koltiva.view.Staffuser.PanelUserMgt-Form-BtnResetPass').setVisible(true);

                            //Set Combo Group
                            setTimeout(function(){
                                thisObj.SetSelectedGroups();
                                Ext.getCmp('Koltiva.view.Staffuser.PanelUserMgt-Form-UserGroupIsDefault').setValue(r.data.UserGroupIsDefault);
                            }, 1500);
                        break;
                        default:
                            Ext.getCmp('Koltiva.view.Staffuser.PanelUserMgt-Form-BtnLinkend').setVisible(false);
                            Ext.getCmp('Koltiva.view.Staffuser.PanelUserMgt-Form-BtnResendEmail').setVisible(false);
                            Ext.getCmp('Koltiva.view.Staffuser.PanelUserMgt-Form-BtnCreate').setVisible(false);
                            Ext.getCmp('Koltiva.view.Staffuser.PanelUserMgt-Form-BtnResetPass').setVisible(false);
                            Ext.getCmp('Koltiva.view.Staffuser.PanelUserMgt-Form-BtnSave').setVisible(false);
                        break;
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
    },
    initComponent: function() {
        var thisObj = this;
        let labelWidth = 160;

        //Store & Combo =======================================
        let cmb_language = Ext.create('Ext.data.Store', {
            extend: 'Ext.data.Model',
            fields: ['id', 'name'],
            autoLoad: true,
            proxy: {
                type: 'ajax',
                url: m_api + '/system/lang_list',
                reader: {
                    type: 'json',
                    root: 'data'
                }
            }
        });
        thisObj.CmbUserGroup = Ext.create('Koltiva.store.Staffuser.ComboUserGroup');
        thisObj.CmbUserGroupDefault = Ext.create('Ext.data.ArrayStore', {
            fields: ['GroupId', 'GroupName'],
            autoLoad: false
        });
        thisObj.ComboAccessStaff = Ext.create('Koltiva.store.Staffuser.ComboAccessStaff');
        //Store & Combo =======================================


        thisObj.items = [{
            layout: 'column',
            border: false,
            cls: 'Sfr_PanelLayoutForm',
            items:[{
                columnWidth: 1,
                layout:'form',
                style:'margin-bottom:10px;border-bottom:1px solid #f0f0f0;',
                items:[{
                    xtype: 'radiogroup',
                    fieldLabel: lang('Linked to Identity Server'),
                    labelWidth: labelWidth,
                    columns: 2,
                    items: [{
                        boxLabel: lang('Yes'),
                        name: 'Koltiva.view.Staffuser.PanelUserMgt-StatusLinkedCognito',
                        inputValue: 'Yes',
                        id: 'Koltiva.view.Staffuser.PanelUserMgt-StatusLinkedCognitoYes',
                        readOnly:true,
                        listeners: {
                            change: function () {
                                return false;
                            }
                        }
                    }, {
                        boxLabel: lang('No'),
                        name: 'Koltiva.view.Staffuser.PanelUserMgt-StatusLinkedCognito',
                        inputValue: 'No',
                        id: 'Koltiva.view.Staffuser.PanelUserMgt-StatusLinkedCognitoNo',
                        readOnly:true,
                        listeners: {
                            change: function () {
                                return false;
                            }
                        }
                    }]
                }]
            }]
        },{
            layout: 'column',
            border: false,
            items:[{
                columnWidth: 1,
                items:[{
                    xtype:'form',
                    cls: 'Sfr_PanelLayoutForm',
                    id: 'Koltiva.view.Staffuser.PanelUserMgt-Form',
                    buttonAlign : 'right',
                    items: [{
                        layout: 'column',
                        border: false,
                        items:[{
                            columnWidth: 1,
                            layout:'form',
                            cls: 'Sfr_PanelLayoutFormContainer',
                            items:[{
                                xtype: 'textfield',
                                id: 'Koltiva.view.Staffuser.PanelUserMgt-Form-StaffState',
                                name: 'Koltiva.view.Staffuser.PanelUserMgt-Form-StaffState',
                                inputType: 'hidden'
                            },{
                                xtype: 'textfield',
                                id: 'Koltiva.view.Staffuser.PanelUserMgt-Form-UserInCognito',
                                name: 'Koltiva.view.Staffuser.PanelUserMgt-Form-UserInCognito',
                                inputType: 'hidden'
                            },{
                                xtype: 'textfield',
                                id: 'Koltiva.view.Staffuser.PanelUserMgt-Form-CognitoUserSub',
                                name: 'Koltiva.view.Staffuser.PanelUserMgt-Form-CognitoUserSub',
                                inputType: 'hidden'
                            },{
                                xtype: 'textfield',
                                id: 'Koltiva.view.Staffuser.PanelUserMgt-Form-CognitoUserStatus',
                                name: 'Koltiva.view.Staffuser.PanelUserMgt-Form-CognitoUserStatus',
                                inputType: 'hidden'
                            },{
                                xtype: 'textfield',
                                id: 'Koltiva.view.Staffuser.PanelUserMgt-Form-UserId',
                                name: 'Koltiva.view.Staffuser.PanelUserMgt-Form-UserId',
                                inputType: 'hidden'
                            },{
                                xtype: 'textfield',
                                id: 'Koltiva.view.Staffuser.PanelUserMgt-Form-Username',
                                name: 'Koltiva.view.Staffuser.PanelUserMgt-Form-Username',
                                allowBlank: false,
                                baseCls: 'Sfr_FormInputMandatory',
                                fieldLabel: lang('Username'),
                                labelWidth: labelWidth,
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
                                fieldLabel: lang('Password'),
                                allowBlank: false,
                                baseCls: 'Sfr_FormInputMandatory',
                                id: 'Koltiva.view.Staffuser.PanelUserMgt-Form-UserPassword',
                                name: 'Koltiva.view.Staffuser.PanelUserMgt-Form-UserPassword',
                                hidden:true,
                                disabled: true,
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
                                id: 'Koltiva.view.Staffuser.PanelUserMgt-Form-UserPasswordRe',
                                name: 'Koltiva.view.Staffuser.PanelUserMgt-Form-UserPasswordRe',
                                hidden:true,
                                disabled: true,
                                listeners:{
                                    afterrender:function(cmp){
                                        cmp.inputEl.set({
                                            autocomplete:'off'
                                        });
                                    }
                                }
                            },{
                                xtype: 'combobox',
                                id: 'Koltiva.view.Staffuser.PanelUserMgt-Form-UserLanguage',
                                name: 'Koltiva.view.Staffuser.PanelUserMgt-Form-UserLanguage',
                                store: cmb_language,
                                fieldLabel: lang('Interface Language'),
                                labelWidth: labelWidth,
                                allowBlank: false,
                                baseCls: 'Sfr_FormInputMandatory',
                                queryMode: 'local',
                                displayField: 'name',
                                valueField: 'id'
                            },{
                                xtype: 'radiogroup',
                                fieldLabel: lang('Administrator Status'),
                                labelWidth: labelWidth,
                                columns: 2,
                                id: 'Koltiva.view.Staffuser.PanelUserMgt-Form-RowUserIsAdmin',
                                hidden: true,
                                items: [{
                                    boxLabel: lang('Yes'),
                                    name: 'Koltiva.view.Staffuser.PanelUserMgt-Form-UserIsAdmin',
                                    inputValue: '1',
                                    id: 'Koltiva.view.Staffuser.PanelUserMgt-Form-UserIsAdminYes',
                                    listeners: {
                                        change: function () {
                                            return false;
                                        }
                                    }
                                }, {
                                    boxLabel: lang('No'),
                                    name: 'Koltiva.view.Staffuser.PanelUserMgt-Form-UserIsAdmin',
                                    inputValue: '0',
                                    id: 'Koltiva.view.Staffuser.PanelUserMgt-Form-UserIsAdminNo',
                                    listeners: {
                                        change: function () {
                                            return false;
                                        }
                                    }
                                }]
                            },{
                                xtype: 'radiogroup',
                                fieldLabel: lang('Account Status'),
                                labelWidth: labelWidth,
                                allowBlank: false,
                                baseCls: 'Sfr_FormInputMandatory',
                                msgTarget: 'side',
                                columns: 2,
                                items: [{
                                    boxLabel: lang('Active'),
                                    name: 'Koltiva.view.Staffuser.PanelUserMgt-Form-UserActive',
                                    inputValue: 'Yes',
                                    id: 'Koltiva.view.Staffuser.PanelUserMgt-Form-UserActiveYes',
                                    listeners: {
                                        change: function () {
                                            return false;
                                        }
                                    }
                                }, {
                                    boxLabel: lang('Inactive'),
                                    name: 'Koltiva.view.Staffuser.PanelUserMgt-Form-UserActive',
                                    inputValue: 'No',
                                    id: 'Koltiva.view.Staffuser.PanelUserMgt-Form-UserActiveNo',
                                    listeners: {
                                        change: function () {
                                            return false;
                                        }
                                    }
                                }]
                            },{
                                xtype: 'itemselector',
                                flex:true,
                                id: 'Koltiva.view.Staffuser.PanelUserMgt-Form-GroupIds',
                                name: 'Koltiva.view.Staffuser.PanelUserMgt-Form-GroupIds',
                                cls: 'itemSelFontKecil',
                                fieldLabel: lang('Account Group'),
                                fromTitle: lang('Available'),
                                toTitle: lang('Selected'),
                                anchor: '100%',
                                height:280,
                                store: thisObj.CmbUserGroup,
                                displayField: 'GroupName',
                                valueField: 'GroupId',
                                value: [],
                                allowBlank: false,
                                //msgTarget: 'side',
                                listeners: {
                                    change: function() {
                                        thisObj.SetSelectedGroups();
                                    }
                                }
                            },{
                                html:'<div style="margin-bottom:11px;"></div>'
                            },{
                                id: 'Koltiva.view.Staffuser.PanelUserMgt-Form-UserGroupIsDefault',
                                name: 'Koltiva.view.Staffuser.PanelUserMgt-Form-UserGroupIsDefault',
                                xtype: 'combobox',
                                allowBlank:false,
                                fieldLabel: lang('Default Group'),
                                store: thisObj.CmbUserGroupDefault,
                                displayField: 'GroupName',
                                valueField: 'GroupId',
                                queryMode:'local'
                            },{
                                html:'<div style="margin-bottom:5px;"></div>'
                            },{
                                xtype: 'itemselector',
                                cls: 'itemSelFontKecil',
                                flex:true,
                                id: 'Koltiva.view.Staffuser.PanelUserMgt-Form-AccessStaff',
                                name: 'Koltiva.view.Staffuser.PanelUserMgt-Form-AccessStaff',
                                allowBlank:false,
                                //msgTarget: 'side',
                                fieldLabel: lang('Access Area'),
                                fromTitle: lang('Available'),
                                toTitle: lang('Selected'),
                                anchor: '100%',
                                height:280,
                                store: thisObj.ComboAccessStaff,
                                displayField: 'name',
                                valueField: 'id',
                                value: []
                            },{
                                html:'<div style="margin-bottom:11px;"></div><style>.x-toolbar-footer {background: white none repeat scroll 0 0 !important;}.x-window-body {background-color: white !important;}.x-panel-default-outer-border-rbl {border-bottom-width: 2px !important;}</style>'
                            }]
                        }]
                    }]
                }]
            }],
            buttons: [{
                xtype: 'button',
                icon: varjs.config.base_url + 'images/icons/new/save.png',
                text: lang('Linked Account Identity'),
                cls: 'Sfr_BtnFormOrange',
                overCls: 'Sfr_BtnFormOrange-Hover',
                id: 'Koltiva.view.Staffuser.PanelUserMgt-Form-BtnLinkend',
                hidden:true,
                handler: function () {
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


                    //Cek apakah create baru atau linked
                    Ext.Ajax.request({
                        waitMsg: 'Please Wait',
                        url: m_api + '/staffuser/check_aws_linked',
                        method: 'POST',
                        params: {
                            PersonID: thisObj.viewVar.PersonID,
                            UserId: thisObj.viewVar.UserId
                        },
                        success: function(rp, o) {
                            Ext.MessageBox.hide();
                            var r = Ext.decode(rp.responseText);

                            if(r.statusLinked == 'No') {
                                var WinFormLinkedUser = Ext.create('Koltiva.view.Staffuser.WinFormLinkedUser', {
                                    viewVar: {
                                        PersonID: thisObj.viewVar.PersonID,
                                        UserId: Ext.getCmp('Koltiva.view.Staffuser.PanelUserMgt-Form-UserId').getValue()
                                    }
                                });
                                if (!WinFormLinkedUser.isVisible()) {
                                    WinFormLinkedUser.center();
                                    WinFormLinkedUser.show();
                                } else {
                                    WinFormLinkedUser.close();
                                }
                            }

                            if(r.statusLinked == 'Yes') {
                                var WinFormLinkedUserExisting = Ext.create('Koltiva.view.Staffuser.WinFormLinkedUserExisting', {
                                    viewVar: {
                                        PersonID: thisObj.viewVar.PersonID,
                                        UserId: Ext.getCmp('Koltiva.view.Staffuser.PanelUserMgt-Form-UserId').getValue()
                                    }
                                });
                                if (!WinFormLinkedUserExisting.isVisible()) {
                                    WinFormLinkedUserExisting.center();
                                    WinFormLinkedUserExisting.show();
                                } else {
                                    WinFormLinkedUserExisting.close();
                                }
                            }
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
                }
            },{
                xtype: 'button',
                icon: varjs.config.base_url + 'images/icons/new/save.png',
                text: lang('Resend Confirmation Email'),
                cls: 'Sfr_BtnFormGrey',
                overCls: 'Sfr_BtnFormGrey-Hover',
                id: 'Koltiva.view.Staffuser.PanelUserMgt-Form-BtnResendEmail',
                hidden:true,
                handler: function () {
                    var WinFormResendConfirmatonEmail = Ext.create('Koltiva.view.Staffuser.WinFormResendConfirmatonEmail', {
                        viewVar: {
                            PersonID: thisObj.viewVar.PersonID,
                            UserId: Ext.getCmp('Koltiva.view.Staffuser.PanelUserMgt-Form-UserId').getValue()
                        }
                    });
                    if (!WinFormResendConfirmatonEmail.isVisible()) {
                        WinFormResendConfirmatonEmail.center();
                        WinFormResendConfirmatonEmail.show();
                    } else {
                        WinFormResendConfirmatonEmail.close();
                    }
                }
            },{
                xtype: 'button',
                icon: varjs.config.base_url + 'images/icons/new/save.png',
                text: lang('Create Account Identity'),
                cls: 'Sfr_BtnFormGreen',
                overCls: 'Sfr_BtnFormGreen-Hover',
                id: 'Koltiva.view.Staffuser.PanelUserMgt-Form-BtnCreate',
                hidden:true,
                handler: function () {
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


                    //Cek apakah create baru atau linked
                    Ext.Ajax.request({
                        waitMsg: 'Please Wait',
                        url: m_api + '/staffuser/check_aws_linked',
                        method: 'POST',
                        params: {
                            PersonID: thisObj.viewVar.PersonID,
                            UserId: thisObj.viewVar.UserId
                        },
                        success: function(rp, o) {
                            Ext.MessageBox.hide();
                            var r = Ext.decode(rp.responseText);
                            
                            var WinFormCreatedUser = Ext.create('Koltiva.view.Staffuser.WinFormCreatedUser', {
                                viewVar: {
                                    PersonID: thisObj.viewVar.PersonID,
                                    UserId: Ext.getCmp('Koltiva.view.Staffuser.PanelUserMgt-Form-UserId').getValue(),
                                    Linked: r.statusLinked
                                }
                            });
                            if (!WinFormCreatedUser.isVisible()) {
                                WinFormCreatedUser.center();
                                WinFormCreatedUser.show();
                            } else {
                                WinFormCreatedUser.close();
                            }
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
                }
            },{
                xtype: 'button',
                icon: varjs.config.base_url + 'images/icons/new/save.png',
                text: lang('Change Password'),
                cls: 'Sfr_BtnFormRed',
                overCls: 'Sfr_BtnFormRed-Hover',
                id: 'Koltiva.view.Staffuser.PanelUserMgt-Form-BtnResetPass',
                hidden:true,
                handler: function () {
                    var WinFormChangePassword = Ext.create('Koltiva.view.Staffuser.WinFormChangePassword', {
                        viewVar: {
                            PersonID: thisObj.viewVar.PersonID,
                            UserId: Ext.getCmp('Koltiva.view.Staffuser.PanelUserMgt-Form-UserId').getValue()
                        }
                    });
                    if (!WinFormChangePassword.isVisible()) {
                        WinFormChangePassword.center();
                        WinFormChangePassword.show();
                    } else {
                        WinFormChangePassword.close();
                    }
                }
            },{
                xtype: 'button',
                icon: varjs.config.base_url + 'images/icons/new/save.png',
                text: lang('Update Account'),
                cls: 'Sfr_BtnFormBlue',
                overCls: 'Sfr_BtnFormBlue-Hover',
                id: 'Koltiva.view.Staffuser.PanelUserMgt-Form-BtnSave',
                hidden:true,
                handler: function () {
                    let StaffState = Ext.getCmp('Koltiva.view.Staffuser.PanelUserMgt-Form-StaffState').getValue();
                    let Form = Ext.getCmp('Koltiva.view.Staffuser.PanelUserMgt-Form').getForm();

                    if (Form.isValid()) {
                        Form.submit({
                            url: m_api + '/staffuser/user_account',
                            waitMsg: lang('Please wait'),
                            params: {
                                PersonID: thisObj.viewVar.PersonID
                            },
                            success: function(rp, o){
                                var r = Ext.decode(o.response.responseText);
                                let PersonID    = thisObj.viewVar.PersonID;
                                let StaffID     = thisObj.viewVar.StaffID;

                                Ext.MessageBox.show({
                                    title: lang('Information'),
                                    msg: r.message,
                                    buttons: Ext.MessageBox.OK,
                                    animateTarget: 'mb9',
                                    icon: 'ext-mb-success'
                                });

                                //Load ulang page
                                Ext.getCmp('Koltiva.view.Staffuser.MainForm').destroy(); //destory current view
                                let FormMainApp = [];
                                if(Ext.getCmp('Koltiva.view.Staffuser.MainForm') == undefined){
                                    FormMainApp = Ext.create('Koltiva.view.Staffuser.MainForm', {
                                        viewVar: {
                                            OpsiDisplay: 'update',
                                            PersonID: PersonID,
                                            StaffID:StaffID
                                        }
                                    });
                                }else{
                                    //destroy, create ulang
                                    Ext.getCmp('Koltiva.view.Staffuser.MainForm').destroy();
                                    FormMainApp = Ext.create('Koltiva.view.Staffuser.MainForm', {
                                        viewVar: {
                                            OpsiDisplay: 'update',
                                            PersonID: PersonID,
                                            StaffID:StaffID
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
                            msg: lang('Form not complete yet, Account Group and Access Area cannot be empty'),
                            buttons: Ext.MessageBox.OK,
                            animateTarget: 'mb9',
                            icon: 'ext-mb-info'
                        });
                    }

                }
            }]
        }];

        this.callParent(arguments);
    },
    SetSelectedGroups: function() {
        var thisObj = this;
        Ext.getCmp('Koltiva.view.Staffuser.PanelUserMgt-Form-UserGroupIsDefault').setValue(null);

        var itemSelectorField   = Ext.getCmp('Koltiva.view.Staffuser.PanelUserMgt-Form-GroupIds');
        var fieldList           = itemSelectorField.toField.store.getRange();
        var value = Ext.getCmp('Koltiva.view.Staffuser.PanelUserMgt-Form-UserGroupIsDefault').getValue();
        var exist = false;
        thisObj.CmbUserGroupDefault.removeAll();
        $.each(fieldList, function(index, val) {
            if (value == val.data.GroupId) {
                exist = true;
            }
            thisObj.CmbUserGroupDefault.add({
                GroupId: val.data.GroupId,
                GroupName: val.data.GroupName,
            });
        });
    }
});
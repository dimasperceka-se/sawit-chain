/******************************************
 *  Author : n1colius.lau@gmail.com
 *  Created On : Wed Dec 04 2019
 *  File : WinFormCreatedUser.js
 *******************************************/
/*
    Param2 yg diperlukan ketika load View ini
    * PersonID
    * UserId
    * Linked (Yes|No)
*/

Ext.define('Koltiva.view.Staffuser.WinFormCreatedUser' ,{
    extend: 'Ext.window.Window',
    id: 'Koltiva.view.Staffuser.WinFormCreatedUser',
    cls: 'Sfr_LayoutPopupWindows',
    title: lang('Create Account to Identity Server'),
    closable: true,
    modal: true,
    closeAction: 'destroy',
    width: '48%',
    height: '75%',
    overflowY: 'auto',
    viewVar: false,
    setViewVar: function(value){
        this.viewVar = value;
    },
    initComponent: function() {
        var thisObj = this;
        let labelWidth = 180;

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

        //items -------------------------------------------------------------- (begin)
        thisObj.items = [{
            xtype: 'form',
            id: 'Koltiva.view.Staffuser.WinFormCreatedUser-Form',
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
                        id: 'FormLabelDescription',
                        hidden:true,
                        text: lang('This staff email address already registered on identity server, you can linked it through this form')
                    },{
                        xtype: 'textfield',
                        id: 'Koltiva.view.Staffuser.WinFormCreatedUser-Form-UserSub',
                        name: 'Koltiva.view.Staffuser.WinFormCreatedUser-Form-UserSub',
                        inputType: 'hidden'
                    },{
                        xtype: 'textfield',
                        id: 'Koltiva.view.Staffuser.WinFormCreatedUser-Form-UserCogStatus',
                        name: 'Koltiva.view.Staffuser.WinFormCreatedUser-Form-UserCogStatus',
                        inputType: 'hidden'
                    },{
                        xtype: 'textfield',
                        labelWidth: labelWidth,
                        fieldLabel: lang('Fullname'),
                        readOnly:true,
                        id: 'Koltiva.view.Staffuser.WinFormCreatedUser-Form-Fullname',
                        name: 'Koltiva.view.Staffuser.WinFormCreatedUser-Form-Fullname'
                    },{
                        xtype: 'radiogroup',
                        fieldLabel: lang('Gender'),
                        readOnly:true,
                        columns: 2,
                        id: 'Koltiva.view.Staffuser.WinFormCreatedUser-Form-RowGender',
                        items: [{
                            boxLabel: lang('Male'),
                            name: 'Koltiva.view.Staffuser.WinFormCreatedUser-Form-Gender',
                            inputValue: 'm',
                            id: 'Koltiva.view.Staffuser.WinFormCreatedUser-Form-GenderM',
                            listeners: {
                                change: function () {
                                    return false;
                                }
                            }
                        }, {
                            boxLabel: lang('Female'),
                            name: 'Koltiva.view.Staffuser.WinFormCreatedUser-Form-Gender',
                            inputValue: 'f',
                            id: 'Koltiva.view.Staffuser.WinFormCreatedUser-Form-GenderF',
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
                        readOnly:true,
                        id: 'Koltiva.view.Staffuser.WinFormCreatedUser-Form-Phonenumber',
                        name: 'Koltiva.view.Staffuser.WinFormCreatedUser-Form-Phonenumber'
                    },{
                        xtype: 'textfield',
                        labelWidth: labelWidth,
                        fieldLabel: lang('Email'),
                        readOnly:true,
                        id: 'Koltiva.view.Staffuser.WinFormCreatedUser-Form-Email',
                        name: 'Koltiva.view.Staffuser.WinFormCreatedUser-Form-Email'
                    },{
                        xtype: 'textfield',
                        id: 'Koltiva.view.Staffuser.WinFormCreatedUser-Form-Username',
                        name: 'Koltiva.view.Staffuser.WinFormCreatedUser-Form-Username',
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
                        id: 'Koltiva.view.Staffuser.WinFormCreatedUser-Form-UserPassword',
                        name: 'Koltiva.view.Staffuser.WinFormCreatedUser-Form-UserPassword',
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
                        id: 'Koltiva.view.Staffuser.WinFormCreatedUser-Form-UserPasswordRe',
                        name: 'Koltiva.view.Staffuser.WinFormCreatedUser-Form-UserPasswordRe',
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
                        id: 'Koltiva.view.Staffuser.WinFormCreatedUser-Form-RowSendEmailConfirm',
                        items: [{
                            boxLabel: lang('Yes'),
                            name: 'Koltiva.view.Staffuser.WinFormCreatedUser-Form-SendEmailConfirm',
                            inputValue: '1',
                            id: 'Koltiva.view.Staffuser.WinFormCreatedUser-Form-SendEmailConfirmYes',
                            listeners: {
                                change: function () {
                                    return false;
                                }
                            }
                        }, {
                            boxLabel: lang('No'),
                            name: 'Koltiva.view.Staffuser.WinFormCreatedUser-Form-SendEmailConfirm',
                            inputValue: '2',
                            id: 'Koltiva.view.Staffuser.WinFormCreatedUser-Form-SendEmailConfirmNo',
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
                        id: 'Koltiva.view.Staffuser.WinFormCreatedUser-Form-RowAutoConfirmUser',
                        items: [{
                            boxLabel: lang('Yes'),
                            name: 'Koltiva.view.Staffuser.WinFormCreatedUser-Form-AutoConfirmUser',
                            inputValue: '1',
                            id: 'Koltiva.view.Staffuser.WinFormCreatedUser-Form-AutoConfirmUserYes',
                            listeners: {
                                change: function () {
                                    return false;
                                }
                            }
                        }, {
                            boxLabel: lang('No'),
                            name: 'Koltiva.view.Staffuser.WinFormCreatedUser-Form-AutoConfirmUser',
                            inputValue: '2',
                            id: 'Koltiva.view.Staffuser.WinFormCreatedUser-Form-AutoConfirmUserNo',
                            listeners: {
                                change: function () {
                                    return false;
                                }
                            }
                        }]
                    },{
                        xtype: 'combobox',
                        id: 'Koltiva.view.Staffuser.WinFormCreatedUser-Form-UserLanguage',
                        name: 'Koltiva.view.Staffuser.WinFormCreatedUser-Form-UserLanguage',
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
                        id: 'Koltiva.view.Staffuser.WinFormCreatedUser-Form-RowUserIsAdmin',
                        hidden: true,
                        items: [{
                            boxLabel: lang('Yes'),
                            name: 'Koltiva.view.Staffuser.WinFormCreatedUser-Form-UserIsAdmin',
                            inputValue: '1',
                            id: 'Koltiva.view.Staffuser.WinFormCreatedUser-Form-UserIsAdminYes',
                            listeners: {
                                change: function () {
                                    return false;
                                }
                            }
                        }, {
                            boxLabel: lang('No'),
                            name: 'Koltiva.view.Staffuser.WinFormCreatedUser-Form-UserIsAdmin',
                            inputValue: '0',
                            id: 'Koltiva.view.Staffuser.WinFormCreatedUser-Form-UserIsAdminNo',
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
                            name: 'Koltiva.view.Staffuser.WinFormCreatedUser-Form-UserActive',
                            inputValue: 'Yes',
                            id: 'Koltiva.view.Staffuser.WinFormCreatedUser-Form-UserActiveYes',
                            listeners: {
                                change: function () {
                                    return false;
                                }
                            }
                        }, {
                            boxLabel: lang('Inactive'),
                            name: 'Koltiva.view.Staffuser.WinFormCreatedUser-Form-UserActive',
                            inputValue: 'No',
                            id: 'Koltiva.view.Staffuser.WinFormCreatedUser-Form-UserActiveNo',
                            listeners: {
                                change: function () {
                                    return false;
                                }
                            }
                        }]
                    },{
                        xtype: 'itemselector',
                        flex:true,
                        id: 'Koltiva.view.Staffuser.WinFormCreatedUser-Form-GroupIds',
                        name: 'Koltiva.view.Staffuser.WinFormCreatedUser-Form-GroupIds',
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
                        listeners: {
                            change: function() {
                                thisObj.SetSelectedGroups();
                            }
                        }
                    },{
                        html:'<div style="margin-bottom:11px;"></div>'
                    },{
                        id: 'Koltiva.view.Staffuser.WinFormCreatedUser-Form-UserGroupIsDefault',
                        name: 'Koltiva.view.Staffuser.WinFormCreatedUser-Form-UserGroupIsDefault',
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
                        id: 'Koltiva.view.Staffuser.WinFormCreatedUser-Form-AccessStaff',
                        name: 'Koltiva.view.Staffuser.WinFormCreatedUser-Form-AccessStaff',
                        allowBlank:false,
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
                        html:'<div style="margin-bottom:11px;"></div>'
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
            text: lang('Create'),
            id: 'Koltiva.view.Staffuser.WinFormLinkedUser-FormCreate-BtnSave',
            handler: function () {
                let Form = Ext.getCmp('Koltiva.view.Staffuser.WinFormCreatedUser-Form').getForm();

                if (Form.isValid()) {

                    //Data Control Tambahan ======================================= (Begin)
                    thisObj.AddValidation = true;
                    thisObj.MsgAddValidation = "";
                    thisObj.AddValidationBasicForm();
                    //Data Control Tambahan ======================================= (Emd)

                    if(thisObj.AddValidation == true){
                        Form.submit({
                            url: m_api + '/staffuser/user_account_create',
                            waitMsg: lang('Please wait'),
                            params: {
                                PersonID: thisObj.viewVar.PersonID,
                                UserId: thisObj.viewVar.UserId,
                                Linked: thisObj.viewVar.Linked
                            },
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
                        msg: lang('Form not complete yet, Account Group and Access Area cannot be empty'),
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
            Ext.getCmp('Koltiva.view.Staffuser.WinFormCreatedUser-Form-SendEmailConfirmYes').setValue(true);
            Ext.getCmp('Koltiva.view.Staffuser.WinFormCreatedUser-Form-AutoConfirmUserNo').setValue(true);

            //Checkbox is_admin
            if(m_id_admin == 1) {
                Ext.getCmp('Koltiva.view.Staffuser.WinFormCreatedUser-Form-RowUserIsAdmin').setVisible(true);
            } else {
                Ext.getCmp('Koltiva.view.Staffuser.WinFormCreatedUser-Form-RowUserIsAdmin').setVisible(false);
            }

            if(thisObj.viewVar.Linked == "No") {
                Ext.getCmp('Koltiva.view.Staffuser.WinFormCreatedUser-Form-Fullname').setVisible(false);
                Ext.getCmp('Koltiva.view.Staffuser.WinFormCreatedUser-Form-RowGender').setVisible(false);
                Ext.getCmp('Koltiva.view.Staffuser.WinFormCreatedUser-Form-Email').setVisible(false);
                Ext.getCmp('Koltiva.view.Staffuser.WinFormCreatedUser-Form-Phonenumber').setVisible(false);
            }

            if(thisObj.viewVar.Linked == "Yes") {
                Ext.getCmp('Koltiva.view.Staffuser.WinFormCreatedUser-Form-Username').setReadOnly(true);
                Ext.getCmp('FormLabelDescription').setVisible(true);
                Ext.getCmp('Koltiva.view.Staffuser.WinFormCreatedUser-Form-UserPassword').setVisible(false);
                Ext.getCmp('Koltiva.view.Staffuser.WinFormCreatedUser-Form-UserPasswordRe').setVisible(false);
                Ext.getCmp('Koltiva.view.Staffuser.WinFormCreatedUser-Form-UserPassword').setDisabled(true);
                Ext.getCmp('Koltiva.view.Staffuser.WinFormCreatedUser-Form-UserPasswordRe').setDisabled(true);
                Ext.getCmp('Koltiva.view.Staffuser.WinFormCreatedUser-Form-RowSendEmailConfirm').setVisible(false);
                Ext.getCmp('Koltiva.view.Staffuser.WinFormCreatedUser-Form-RowAutoConfirmUser').setVisible(false);

                //load data dari aws cognito
                Ext.getCmp('Koltiva.view.Staffuser.WinFormCreatedUser-Form').getForm().load({
                    url: m_api + '/staffuser/create_user_existing_form_open',
                    method: 'GET',
                    params: {
                        PersonID: thisObj.viewVar.PersonID
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
    },
    AddValidationBasicForm: function() {
        var thisObj = this;
        var ArrMsg = [];
        thisObj.AddValidation = true;

        //Password Sama ================================================== (Begin)
        if(Ext.getCmp('Koltiva.view.Staffuser.WinFormCreatedUser-Form-UserPassword').getValue() != Ext.getCmp('Koltiva.view.Staffuser.WinFormCreatedUser-Form-UserPasswordRe').getValue()) {
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
    },
    SetSelectedGroups: function() {
        var thisObj = this;
        Ext.getCmp('Koltiva.view.Staffuser.WinFormCreatedUser-Form-UserGroupIsDefault').setValue(null);

        var itemSelectorField   = Ext.getCmp('Koltiva.view.Staffuser.WinFormCreatedUser-Form-GroupIds');
        var fieldList           = itemSelectorField.toField.store.getRange();
        var value = Ext.getCmp('Koltiva.view.Staffuser.WinFormCreatedUser-Form-UserGroupIsDefault').getValue();
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
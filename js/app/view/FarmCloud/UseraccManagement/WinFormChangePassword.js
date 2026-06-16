Ext.define('Koltiva.view.FarmCloud.UseraccManagement.WinFormChangePassword' ,{
    extend: 'Ext.window.Window',
    id: 'Koltiva.view.FarmCloud.UseraccManagement.WinFormChangePassword',
    cls: 'Sfr_LayoutPopupWindows',
    title: lang('Change Password on Identity Server'),
    closable: true,
    modal: true,
    closeAction: 'destroy',
    width: '30%',
    height: '35%',
    overflowY: 'auto',
    viewVar: false,
    setViewVar: function(value){
        this.viewVar = value;
    },
    initComponent: function() {
        var thisObj = this;
        let labelWidth = 190;

        //items -------------------------------------------------------------- (begin)
        thisObj.items = [{
        	xtype: 'form',
            id: 'Koltiva.view.FarmCloud.UseraccManagement.WinFormChangePassword-Form',
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
                        fieldLabel: lang('Password'),
                        allowBlank: false,
                        baseCls: 'Sfr_FormInputMandatory',
                        id: 'Koltiva.view.FarmCloud.UseraccManagement.WinFormChangePassword-Form-UserPassword',
                        name: 'Koltiva.view.FarmCloud.UseraccManagement.WinFormChangePassword-Form-UserPassword',
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
                        id: 'Koltiva.view.FarmCloud.UseraccManagement.WinFormChangePassword-Form-UserPasswordRe',
                        name: 'Koltiva.view.FarmCloud.UseraccManagement.WinFormChangePassword-Form-UserPasswordRe',
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
            text: lang('Reset'),
            id: 'Koltiva.view.FarmCloud.UseraccManagement.WinFormChangePassword-Form-BtnSave',
            handler: function () {
                //Submit Form
                let Form = Ext.getCmp('Koltiva.view.FarmCloud.UseraccManagement.WinFormChangePassword-Form').getForm();
                if (Form.isValid()) {

                    //Data Control Tambahan ======================================= (Begin)
                    thisObj.AddValidation = true;
                    thisObj.MsgAddValidation = "";
                    thisObj.AddValidationBasicForm();
                    //Data Control Tambahan ======================================= (Emd)

                    if(thisObj.AddValidation == true){
                        Form.submit({
                            url: m_api + '/farmcloud/user_account_change_passwd',
                            waitMsg: lang('Please wait'),
                            params: {
                                Username: thisObj.viewVar.Username
                            },
                            success: function(rp, o){
                                var r = Ext.decode(o.response.responseText);
                                
                                Ext.MessageBox.show({
                                    title: lang('Information'),
                                    msg: r.message,
                                    buttons: Ext.MessageBox.OK,
                                    animateTarget: 'mb9',
                                    icon: 'ext-mb-success'
                                });

                                thisObj.close();
                            },
                            failure: function(rp, o){
                                try {
                                    var r = Ext.decode(o.response.responseText);
                                    Ext.MessageBox.show({
                                        title: lang('Error'),
                                        msg: r.message,
                                        buttons: Ext.MessageBox.OK,
                                        animateTarget: 'mb9',
                                        icon: 'ext-mb-error'
                                    });
                                }
                                catch(err) {
                                    Ext.MessageBox.show({
                                        title: lang('Error'),
                                        msg: lang('Connection Error'),
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
        if(Ext.getCmp('Koltiva.view.FarmCloud.UseraccManagement.WinFormChangePassword-Form-UserPassword').getValue() != Ext.getCmp('Koltiva.view.FarmCloud.UseraccManagement.WinFormChangePassword-Form-UserPasswordRe').getValue()) {
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
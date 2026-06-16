/******************************************
 *  Author : n1colius.lau@gmail.com   
 *  Created On : Wed Jul 15 2020
 *  File : PanelUserDhis.js
 *******************************************/

/*
    Param2 yg diperlukan ketika load View ini
    * PersonID
*/

Ext.define('Koltiva.view.Staffuser.PanelUserDhis' ,{
    extend: 'Ext.panel.Panel',
    id: 'Koltiva.view.Staffuser.PanelUserDhis',
    style:'margin-left:15px;margin-top:15px;padding:8px 13px;background:white !important;',
    title:lang('Update User to DHIS'),
    frame: true,
    viewVar: false,
    setViewVar: function(value){
        this.viewVar = value;
    },
    listeners: {
        afterRender: function(){
            var thisObj = this;

            //Load main Form
            Ext.getCmp('Koltiva.view.Staffuser.PanelUserDhis-Form').getForm().load({
                url: m_api + '/staffuser/user_dhis_form_open',
                method: 'GET',
                params: {
                    PersonID: thisObj.viewVar.PersonID
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
    initComponent: function() {
        var thisObj = this;
        let labelWidth = 160;

        let CmbDhisRole = Ext.create('Koltiva.store.Staffuser.CmbDhisRole');
        let CmbDhisGroup = Ext.create('Koltiva.store.Staffuser.CmbDhisGroup');

        thisObj.items = [{
            layout: 'column',
            border: false,
            items:[{
                columnWidth: 1,
                items:[{
                    xtype:'form',
                    cls: 'Sfr_PanelLayoutForm',
                    id: 'Koltiva.view.Staffuser.PanelUserDhis-Form',
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
                                id: 'Koltiva.view.Staffuser.PanelUserDhis-Form-UserId',
                                name: 'Koltiva.view.Staffuser.PanelUserDhis-Form-UserId',
                                inputType: 'hidden'
                            },{
                                xtype: 'textfield',
                                id: 'Koltiva.view.Staffuser.PanelUserDhis-Form-UserName',
                                name: 'Koltiva.view.Staffuser.PanelUserDhis-Form-UserName',
                                inputType: 'hidden'
                            },{
                                xtype: 'textfield',
                                id: 'Koltiva.view.Staffuser.PanelUserDhis-Form-Name',
                                name: 'Koltiva.view.Staffuser.PanelUserDhis-Form-Name',
                                inputType: 'hidden'
                            },{
                                xtype: 'textfield',
                                id: 'Koltiva.view.Staffuser.PanelUserDhis-Form-UserExtId',
                                name: 'Koltiva.view.Staffuser.PanelUserDhis-Form-UserExtId',
                                inputType: 'hidden'
                            },{
                                xtype: 'itemselector',
                                cls: 'itemSelFontKecil',
                                flex:true,
                                id: 'Koltiva.view.Staffuser.PanelUserDhis-Form-CmbDhisRole',
                                name: 'Koltiva.view.Staffuser.PanelUserDhis-Form-CmbDhisRole',
                                store: CmbDhisRole,
                                fieldLabel: lang('DHIS Role'),
                                labelWidth: labelWidth,
                                allowBlank: false,
                                baseCls: 'Sfr_FormInputMandatory',
                                fromTitle: lang('Available'),
                                toTitle: lang('Selected'),
                                anchor: '100%',
                                height:280,
                                displayField: 'label',
                                valueField: 'id',
                                value: []
                            },{
                                html:'<br>'
                            },{
                                xtype: 'itemselector',
                                cls: 'itemSelFontKecil',
                                flex:true,
                                id: 'Koltiva.view.Staffuser.PanelUserDhis-Form-CmbDhisGroup',
                                name: 'Koltiva.view.Staffuser.PanelUserDhis-Form-CmbDhisGroup',
                                store: CmbDhisGroup,
                                fieldLabel: lang('DHIS Group'),
                                labelWidth: labelWidth,
                                allowBlank: false,
                                baseCls: 'Sfr_FormInputMandatory',
                                fromTitle: lang('Available'),
                                toTitle: lang('Selected'),
                                anchor: '100%',
                                height:280,
                                displayField: 'label',
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
                text: lang('Update to DHIS (With Predefined Password)'),
                cls: 'Sfr_BtnFormBlue',
                overCls: 'Sfr_BtnFormBlue-Hover',
                id: 'Koltiva.view.Staffuser.PanelUserDhis-Form-BtnUpdate',
                handler: function () {
                    let Form = Ext.getCmp('Koltiva.view.Staffuser.PanelUserDhis-Form').getForm();

                    //cek user
                    if(Ext.getCmp('Koltiva.view.Staffuser.PanelUserDhis-Form-UserId').getValue() == "") {
                        Ext.MessageBox.show({
                            title: lang('Attention'),
                            msg: lang('User not created yet'),
                            buttons: Ext.MessageBox.OK,
                            animateTarget: 'mb9',
                            icon: 'ext-mb-info'
                        });
                    } else {
                        if (Form.isValid()) {
                            Form.submit({
                                url: m_api + '/staffuser/user_dhis_form',
                                waitMsg: lang('Please wait'),
                                success: function(rp, o){
                                    var r = Ext.decode(o.response.responseText);
                                    Ext.MessageBox.show({
                                        title: lang('Information'),
                                        msg: r.message,
                                        buttons: Ext.MessageBox.OK,
                                        animateTarget: 'mb9',
                                        icon: 'ext-mb-success'
                                    });
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
                                msg: lang('Form not complete yet'),
                                buttons: Ext.MessageBox.OK,
                                animateTarget: 'mb9',
                                icon: 'ext-mb-info'
                            });
                        }
                    }
                }
            }]
        }];

        this.callParent(arguments);
    }
});
/*
* @Author: Fashah Darullah
* @Date:   2019-06-12 11:19:19
*/

/*
    Param2 yg diperlukan ketika load View ini
    1. opsiDisplay
    2. viewVar
*/

Ext.define('Koltiva.view.FarmCloud.UserRegisterForm' ,{
    extend: 'Ext.panel.Panel',
    id: 'Koltiva.view.FarmCloud.UserRegisterForm',
    style:'padding:0 15px 15px 15px;margin:12px 0 0 0;',
    opsiDisplay: false,
    setOpsiDisplay: function(value){
        this.opsiDisplay = value;
    },
    viewVar: false,
    setViewVar: function(value){
        this.viewVar = value;
    },
    renderTo: 'ext-content',
    listeners: {
        afterRender: function(){
            var thisObj = this;

            //Div nya Filter Region
            document.getElementById('divCommonContentRegion').style.display = 'none';
            // document.getElementById('main-breadcrumb').style.display = 'none';

        	if(thisObj.viewVar.opsiDisplay == 'insert'){
        		//form reset
                Ext.getCmp('Koltiva.view.FarmCloud.UserRegisterForm-Form').getForm().reset();
            }
            
            if(thisObj.viewVar.opsiDisplay == 'view'){
        		//Set ReadOnly
            }
        }
    },
    initComponent: function() {
        var thisObj = this;

        //Panel Basic ==================================== (Begin)
        thisObj.ObjPanelBasicData = Ext.create('Ext.form.Panel',{
            title: lang('Register Account'),
            frame: true,
            cls: 'Sfr_PanelLayoutForm',
            id: 'Koltiva.view.FarmCloud.UserRegisterForm-Form',
            fileUpload: true,
            collapsible:true,
		    buttonAlign : 'center',
            items: [{
                layout: 'column',
                border: false,
                padding:10,
                items:[{
                    columnWidth: 1,
                    layout:'form',
                    cls: 'Sfr_PanelLayoutFormContainer',
                    items:[{
                        xtype: 'panel',
                        flex: 1,
                        activeTab: 0,
                        plain: true,
                        cls:'Sfr_TabForm',
                        id: 'Koltiva.view.FarmCloud.UserRegisterForm-Form-Tab',
                        items:[{
                            layout: 'column',
                            border: false,
                            items:[
                                {
                                    columnWidth: 0.5,
                                    layout:'form',
                                    style:'padding:10px 5px 10px 20px;',
                                    defaults: {
                                        labelAlign: 'left',
                                        labelWidth: 150
                                    },
                                    items:[
                                        {
                                            xtype: 'textfield',
                                            id: 'Koltiva.view.FarmCloud.UserRegisterForm-Form-PersonName',
                                            name: 'PersonName',
                                            fieldLabel: lang('Full Name'),
                                            labelAlign:'top',
                                            allowBlank: false,
                                            baseCls: 'Sfr_FormInputMandatory'
                                        },{html:'<div style="height:3px;">&nbsp;</div>'},
                                        {
                                            xtype: 'textfield',
                                            id: 'Koltiva.view.FarmCloud.UserRegisterForm-Form-Email',
                                            name: 'Email',
                                            fieldLabel: lang('Email'),
                                            labelAlign:'top',
                                            allowBlank: false,
                                            baseCls: 'Sfr_FormInputMandatory'
                                        }
                                        ,{html:'<div style="height:3px;">&nbsp;</div>'}
                                    ]
                                },
                                {
                                    columnWidth: 0.5,
                                    layout:'form',
                                    style:'padding:10px 5px 10px 20px;',
                                    defaults: {
                                        labelAlign: 'left',
                                        labelWidth: 150
                                    },
                                    items:[
                                        {
                                            xtype: 'textfield',
                                            id: 'Koltiva.view.FarmCloud.UserRegisterForm-Form-Handphone',
                                            name: 'Handphone',
                                            fieldLabel: lang('Handphone'),
                                            labelAlign:'top',
                                            allowBlank: false,
                                            baseCls: 'Sfr_FormInputMandatory'
                                        }
                                        ,{html:'<div style="height:3px;">&nbsp;</div>'}
                                    ]
                                }
                            ]
                        }],
                        listeners: {
                            
                        }
                    }]
                }],
                buttons: [{
                    xtype: 'button',
                    icon: varjs.config.base_url + 'images/icons/new/save.png',
                    text: lang('Save'),
                    cls: 'Sfr_BtnFormBlue',
                    overCls: 'Sfr_BtnFormBlue-Hover',
                    id: 'Koltiva.view.FarmCloud.UserRegisterForm-Form-BtnSave',
                    handler: function () {
                        var Formnya = Ext.getCmp('Koltiva.view.FarmCloud.UserRegisterForm-Form').getForm();

                        if (Formnya.isValid()) {
                            Formnya.submit({
                                url: m_api + '/farmcloud/register_user',
                                method: 'POST',
                                waitMsg: 'Saving data...',
                                params: {
                                    opsiDisplay: thisObj.viewVar.opsiDisplay
                                },
                                success: function (fp, o) {
                                    var pesanNya;
                                    if (o.result.messages != undefined) {
                                        pesanNya = o.result.messages;
                                    } else {
                                        pesanNya = lang('Connection error!');
                                    }
                                    Ext.MessageBox.show({
                                        title: 'Information',
                                        msg: pesanNya,
                                        buttons: Ext.MessageBox.OK,
                                        animateTarget: 'mb9',
                                        icon: 'ext-mb-success',
                                        fn: function (btn) {
                                            if (btn == 'ok') {
                                                Ext.getCmp('Koltiva.view.FarmCloud.UserRegisterForm').destroy(); //destory current view
                                                var MainForm = [];
                                                if (Ext.getCmp('Koltiva.view.FarmCloud.UserManagementGrid') == undefined) {
                                                    MainForm = Ext.create('Koltiva.view.FarmCloud.UserManagementGrid');
                                                } else {
                                                    Ext.getCmp('Koltiva.view.FarmCloud.UserManagementGrid').destroy();
                                                    MainForm = Ext.create('Koltiva.view.FarmCloud.UserManagementGrid');
                                                }
                                            }
                                        }
                                    });
                                },
                                failure: function (fp, o) {
                                    var pesanNya;
                                    if (o.result.message != undefined) {
                                        pesanNya = o.result.message;
                                    } else {
                                        pesanNya = lang('Connection error');
                                    }
                                    Ext.MessageBox.show({
                                        title: 'Fail',
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
                                msg: lang('Form not complete yet'),
                                buttons: Ext.MessageBox.OK,
                                animateTarget: 'mb9',
                                icon: 'ext-mb-info'
                            });
                        }
                    }
                }]
            }]
        });
        
        //Panel Basic ==================================== (End)

        //========================================================== LAYOUT UTAMA (Begin) ========================================//
        thisObj.items = [{
            xtype: 'panel',
            border:false,
            layout:{
                type:'hbox'
            },
            items:[{
                id: 'Koltiva.view.Grower.FormMainGrower-labelInfoInsert',
                html:'<div id="header_title_farmer">'+lang('Register Account')+'</div>'
            }]
        },{
            items:[{
                id: 'Koltiva.view.Grower.FormMainGrower-LinkBackToList',
                html:'<div id="Sfr_IdBoxInfoDataGrid" class="Sfr_BoxInfoDataGrid"><ul class="Sft_UlListInfoDataGrid"><li class="Sft_ListInfoDataGrid"><a href="javascript:Ext.getCmp(\'Koltiva.view.FarmCloud.UserRegisterForm\').BackToList()"><img class="Sft_ListIconInfoDataGrid" src="'+varjs.config.base_url+'images/icons/new/back.png" width="20" />&nbsp;&nbsp;'+lang('Back to User Management List')+'</a></li></div>'
            }]
        },{
            html:'<br />'
        },{
            layout: 'column',
            border: false,
            items: [{
                //LEFT CONTENT
                columnWidth: 0.6,
                items:[
                    thisObj.ObjPanelBasicData
                ]
            }]
        }];
        //========================================================== LAYOUT UTAMA (END) ========================================//

        this.callParent(arguments);
    },
    BackToList: function(){
        Ext.getCmp('Koltiva.view.FarmCloud.UserRegisterForm').destroy(); //destory current view
        var GridMainGrower = [];
        if(Ext.getCmp('Koltiva.view.FarmCloud.UserManagementGrid') == undefined){
            GridMainGrower = Ext.create('Koltiva.view.FarmCloud.UserManagementGrid');
        }else{
            //destroy, create ulang
            Ext.getCmp('Koltiva.view.FarmCloud.UserManagementGrid').destroy();
            GridMainGrower = Ext.create('Koltiva.view.FarmCloud.UserManagementGrid');
        }
    }
});
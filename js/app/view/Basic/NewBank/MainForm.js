Ext.define('Koltiva.view.Basic.NewBank.MainForm', {
    extend: 'Ext.panel.Panel',
    id: 'Koltiva.view.Basic.NewBank.MainForm',
    style: 'padding:0 15px 15px 15px;margin:12px 0 0 0;',
    opsiDisplay: false,
    setOpsiDisplay: function (value) {
        this.opsiDisplay = value;
    },
    viewVar: false,
    setViewVar: function (value) {
        this.viewVar = value;
    },
    renderTo: 'ext-content',
    listeners: {
        afterRender: function () {
            var thisObj = this;

            if (thisObj.viewVar.opsiDisplay == 'insert') {
                //form reset
                Ext.getCmp('Koltiva.view.Basic.NewBank.MainForm-FormBasicData').getForm().reset();
            }

            if (thisObj.viewVar.opsiDisplay == 'view' || thisObj.viewVar.opsiDisplay == 'update') {

                if (thisObj.viewVar.opsiDisplay == 'view') {
                    Ext.getCmp('Koltiva.view.Basic.NewBank.MainForm-FormBasicData-BtnSave').setVisible(false);
                }

                //form reset
                Ext.getCmp('Koltiva.view.Basic.NewBank.MainForm-FormBasicData').getForm().reset();

                //load data form
                Ext.getCmp('Koltiva.view.Basic.NewBank.MainForm-FormBasicData').getForm().load({
                    url: m_api + '/bank/newbank_basic_data_form',
                    method: 'GET',
                    params: {
                        BankID: this.viewVar.BankID
                    },
                    success: function (form, action) {
                        Ext.MessageBox.hide();
                        var r = Ext.decode(action.response.responseText);

                        //Set Title
                        Ext.getCmp('Koltiva.view.Grower.FormMainGrower-labelInfoInsert').doLayout();
                    },
                    failure: function (form, action) {
                        Ext.MessageBox.hide();
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
        beforerender: function () {
            var thisObj = this;

            if (thisObj.viewVar.opsiDisplay != 'insert') {
                Ext.MessageBox.show({
                    msg: 'Please wait...',
                    progressText: 'Loading...',
                    width: 300,
                    wait: true,
                    waitConfig: {
                        interval: 200
                    },
                    icon: 'ext-mb-info', //custom class in msg-box.html
                    animateTarget: 'mb9'
                });
            }
        }
    },
    initComponent: function () {
        var thisObj = this;

        //Panel Basic ==================================== (Begin)
        thisObj.ObjPanelBasicData = Ext.create('Ext.form.Panel', {
            title: lang('Basic Data'),
            frame: true,
            cls: 'Sfr_PanelLayoutForm',
            id: 'Koltiva.view.Basic.NewBank.MainForm-FormBasicData',
            fileUpload: true,
            collapsible: true,
            buttonAlign: 'center',
            items: [{
                    layout: 'column',
                    border: false,
                    padding: 10,
                    items: [{
                            columnWidth: 1,
                            layout: 'form',
                            cls: 'Sfr_PanelLayoutFormContainer',
                            items: [{
                                    xtype: 'panel',
                                    flex: 1,
                                    activeTab: 0,
                                    plain: true,
                                    cls: 'Sfr_TabForm',
                                    id: 'Koltiva.view.Basic.NewBank.MainForm-FormBasicData-Tab',
                                    items: [{
                                            xtype: 'panel',
                                            title: lang('Bank Reference'),
                                            frame: false,
                                            id: 'Koltiva.view.Basic.NewBank.MainForm-FormBasicData-NewBank',
                                            style: 'margin-top:12px;',
                                            cls: 'Sfr_PanelSubLayoutFormRoundedGray',
                                            items: [{
                                                    layout: 'column',
                                                    border: false,
                                                    items: [
                                                        {
                                                            columnWidth: 0.7,
                                                            layout: 'form',
                                                            style: 'padding:10px 5px 10px 20px;',
                                                            defaults: {
                                                                labelAlign: 'left',
                                                                labelWidth: 150
                                                            },
                                                            items: [{
                                                                    xtype: 'textfield',
                                                                    id: 'Koltiva.view.Basic.NewBank.MainForm-FormBasicData-BankID',
                                                                    name: 'Koltiva.view.Basic.NewBank.MainForm-FormBasicData-BankID',
                                                                    fieldLabel: lang('Bank ID'),
                                                                    queryMode: 'local',
                                                                    allowBlank: true,
                                                                    valueField: 'id',
                                                                    readOnly: true,
                                                                    hidden: true
                                                                }, {
                                                                    xtype: 'textfield',
                                                                    id: 'Koltiva.view.Basic.NewBank.MainForm-FormBasicData-BankCode',
                                                                    name: 'Koltiva.view.Basic.NewBank.MainForm-FormBasicData-BankCode',
                                                                    fieldLabel: lang('Bank Code'),
                                                                    allowBlank: false,
                                                                    baseCls: 'Sfr_FormInputMandatory'
                                                                }, {
                                                                    html: '<div style="height:3px;">&nbsp;</div>'
                                                                }, {
                                                                    xtype: 'textfield',
                                                                    id: 'Koltiva.view.Basic.NewBank.MainForm-FormBasicData-BankName',
                                                                    name: 'Koltiva.view.Basic.NewBank.MainForm-FormBasicData-BankName',
                                                                    fieldLabel: lang('Bank Name'),
                                                                    allowBlank: false,
                                                                    baseCls: 'Sfr_FormInputMandatory'
                                                                }, {
                                                                    html: '<div style="height:3px;">&nbsp;</div>'
                                                                }, {
                                                                    xtype: 'textarea',
                                                                    id: 'Koltiva.view.Basic.NewBank.MainForm-FormBasicData-BankDesc',
                                                                    name: 'Koltiva.view.Basic.NewBank.MainForm-FormBasicData-BankDesc',
                                                                    fieldLabel: lang('Bank Desc'),
                                                                    allowBlank: true
                                                                }]
                                                        }]
                                                }]
                                        }]
                                }]
                        }]
                }],
            buttons: [{
                    xtype: 'button',
                    icon: varjs.config.base_url + 'images/icons/new/save.png',
                    text: lang('Save'),
                    cls: 'Sfr_BtnFormBlue',
                    overCls: 'Sfr_BtnFormBlue-Hover',
                    id: 'Koltiva.view.Basic.NewBank.MainForm-FormBasicData-BtnSave',
                    handler: function () {
                        if (thisObj.ObjPanelBasicData.isValid()) {
                            thisObj.ObjPanelBasicData.submit({
                                url: m_api + '/bank/add',
                                method: 'POST',
                                waitMsg: 'Saving data...',
                                params: {
                                    opsiDisplay: thisObj.viewVar.opsiDisplay
                                },
                                success: function (fp, o) {
                                    Ext.MessageBox.show({
                                        title: 'Information',
                                        msg: lang(o.result.message),
                                        buttons: Ext.MessageBox.OK,
                                        animateTarget: 'mb9',
                                        icon: 'ext-mb-success',
                                        fn: function (btn) {
                                            if (btn == 'ok') {
                                                Ext.getCmp('Koltiva.view.Basic.NewBank.MainForm').destroy(); //destory current view
                                                var MainForm = [];
                                                if (Ext.getCmp('Koltiva.view.Basic.NewBank.MainForm') == undefined) {
                                                    MainForm = Ext.create('Koltiva.view.Basic.NewBank.MainForm', {
                                                        viewVar: {
                                                            opsiDisplay: 'update',
                                                            BankID: o.result.BankID
                                                        }
                                                    });
                                                } else {
                                                    Ext.getCmp('Koltiva.view.Basic.NewBank.MainForm').destroy();
                                                    MainForm = Ext.create('Koltiva.view.Basic.NewBank.MainForm', {
                                                        viewVar: {
                                                            opsiDisplay: 'update',
                                                            BankID: o.result.BankID
                                                        }
                                                    });
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
        });
        //Panel Basic ==================================== (End)

        //========================================================== LAYOUT UTAMA (Begin) ========================================//
        thisObj.items = [{
                xtype: 'panel',
                border: false,
                layout: {
                    type: 'hbox'
                },
                items: [{
                        id: 'Koltiva.view.Grower.FormMainGrower-labelInfoInsert',
                        html: '<div id="header_title_farmer">' + lang('Bank Reference') + '</div>'
                    }]
            }, {
                items: [{
                        id: 'Koltiva.view.Grower.FormMainGrower-LinkBackToList',
                        html: '<div id="Sfr_IdBoxInfoDataGrid" class="Sfr_BoxInfoDataGrid"><ul class="Sft_UlListInfoDataGrid"><li class="Sft_ListInfoDataGrid"><a href="javascript:Ext.getCmp(\'Koltiva.view.Basic.NewBank.MainForm\').BackToList()"><img class="Sft_ListIconInfoDataGrid" src="' + varjs.config.base_url + 'images/icons/new/back.png" width="20" />&nbsp;&nbsp;' + lang('Back to Bank List') + '</a></li></div>'
                    }]
            }, {
                html: '<br />'
            }, {
                layout: 'column',
                border: false,
                items: [{
                        //LEFT CONTENT
                        columnWidth: 0.6,
                        items: [
                            thisObj.ObjPanelBasicData
                        ]
                    }]
            }];
        //========================================================== LAYOUT UTAMA (END) ========================================//

        this.callParent(arguments);
    },
    BackToList: function () {
        Ext.getCmp('Koltiva.view.Basic.NewBank.MainForm').destroy(); //destory current view
        var GridMainGrower = [];

        if (Ext.getCmp('Koltiva.view.Basic.NewBank.MainGrid') == undefined) {
            GridMainGrower = Ext.create('Koltiva.view.Basic.NewBank.MainGrid');
        } else {
            //destroy, create ulang
            Ext.getCmp('Koltiva.view.Basic.NewBank.MainGrid').destroy();
            GridMainGrower = Ext.create('Koltiva.view.Basic.NewBank.MainGrid');
        }
    }
});
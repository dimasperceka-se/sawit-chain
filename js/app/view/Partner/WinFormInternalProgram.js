Ext.define('Koltiva.view.Partner.WinFormInternalProgram', {
    extend: 'Ext.window.Window',
    id: 'Koltiva.view.Partner.WinFormInternalProgram',
    cls: 'Sfr_LayoutPopupWindows',
    title: lang('Form Internal Program'),
    closable: true,
    modal: true,
    closeAction: 'destroy',
    width: '35%',
    height: 250,
    overflowY: 'auto',
    viewVar: false,
    setViewVar: function (value) {
        this.viewVar = value;
    },
    initComponent: function () {
        var thisObj = this;

        //items -------------------------------------------------------------- (begin)
        thisObj.items = [{
            xtype: 'form',
            id: 'Koltiva.view.Partner.WinFormInternalProgram-Form',
            padding: '5 25 5 8',
            items: [{
                layout: 'column',
                border: false,
                items: [{
                    columnWidth: 1,
                    layout: 'form',
                    style: 'padding-bottom:10px;',
                    items: [{
                        xtype: 'textfield',
                        labelAlign: 'top',
                        fieldLabel: lang('Program Name'),
                        allowBlank: false,
                        baseCls: 'Sfr_FormInputMandatory',
                        id: 'Koltiva.view.Partner.WinFormInternalProgram-Form-ProgramName',
                        name: 'Koltiva.view.Partner.WinFormInternalProgram-Form-ProgramName'
                    }]
                }]
            }]
        }];
        //items -------------------------------------------------------------- (end)

        thisObj.buttons = [{
            icon: varjs.config.base_url + 'images/icons/new/save.png',
            cls: 'Sfr_BtnFormBlue',
            overCls: 'Sfr_BtnFormBlue-Hover',
            text: lang('Save'),
            id: 'Koltiva.view.Partner.WinFormInternalProgram-Form-BtnSave',
            handler: function () {
                var Formnya = Ext.getCmp('Koltiva.view.Partner.WinFormInternalProgram-Form').getForm();
                if (Formnya.isValid()) {
                    Formnya.submit({
                        url: m_api + '/partner_new/internal_program_input',
                        method: 'POST',
                        waitMsg: lang('Saving data'),
                        params: {
                            OpsiDisplay: thisObj.viewVar.OpsiDisplay,
                            PartnerID: thisObj.viewVar.PartnerID
                        },
                        success: function (fp, o) {
                            var r = Ext.decode(o.response.responseText);
                            Ext.MessageBox.show({
                                title: 'Information',
                                msg: r.message,
                                buttons: Ext.MessageBox.OK,
                                animateTarget: 'mb9',
                                icon: 'ext-mb-success'
                            });

                            thisObj.close();
                            thisObj.viewVar.CallerStore.load();
                        },
                        failure: function (fp, o) {
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
            cls: 'Sfr_BtnFormGrey',
            overCls: 'Sfr_BtnFormGrey-Hover',
            handler: function () {
                thisObj.close();
            }
        }];

        this.callParent(arguments);
    }
});
/******************************************
 *  Author : n1colius.lau@gmail.com
 *  Created On : Wed Jan 22 2020
 *  File : PanelFormAdditional.js
 *******************************************/
/*
    Param2 yg diperlukan ketika load View ini
    - SupplierID
*/

Ext.define('Koltiva.view.Ext_staff.PanelFormAdditional', {
    extend: 'Ext.form.Panel',
    id: 'Koltiva.view.Ext_staff.PanelFormAdditional',
    buttonAlign: 'center',
    cls: 'Sfr_PanelSubLayoutForm',
    viewVar: false,
    setViewVar: function (value) {
        this.viewVar = value;
    },
    initComponent: function () {
        var thisObj = this;
        var cmb_bank = Ext.create('Koltiva.store.ComboGeneral.CmbBank', {
            storeVar: {
                CountryID: null
            }
        });

        thisObj.items = [{
            layout: 'column',
            border: false,
            style: 'padding-top:10px;padding-bottom:10px;',
            items: [{
                columnWidth: 1,
                layout: 'form',
                cls: 'Sfr_PanelLayoutFormContainer',
                items: [{
                    xtype: 'panel',
                    title: lang('Bank Account'),
                    frame: false,
                    id: 'Koltiva.view.Ext_staff.PanelFormAdditional-SectionBankAccount',
                    cls: 'Sfr_PanelSubLayoutFormRoundedGray',
                    items: [{
                        layout: 'column',
                        border: false,
                        items: [{
                            columnWidth: 1,
                            layout: 'form',
                            style: 'padding:10px 0px 0px 0px;',
                            defaults: {
                                labelAlign: 'left',
                                labelWidth:'250'
                            },
                            items: [{
                                xtype: 'textfield',
                                fieldLabel: lang('Bank Account Holder'),
                                id: 'Koltiva.view.Ext_staff.PanelFormAdditional-Form-AccountBeneficiary',
                                name: 'Koltiva.view.Ext_staff.PanelFormAdditional-Form-AccountBeneficiary'
                            }, { html: '<div style="height:13px;">&nbsp;</div>' }, {
                                xtype: 'combobox',
                                id: 'Koltiva.view.Ext_staff.PanelFormAdditional-Form-BankID',
                                name: 'Koltiva.view.Ext_staff.PanelFormAdditional-Form-BankID',
                                store: cmb_bank,
                                fieldLabel: lang('Bank Name'),
                                editable: false,
                                queryMode: 'local',
                                displayField: 'label',
                                valueField: 'id',
                                listeners: {
                                    change: function (cb, nv, ov) { }
                                }
                            }, { html: '<div style="height:13px;">&nbsp;</div>' }, {
                                xtype: 'textfield',
                                fieldLabel: lang('Bank Account Number'),
                                id: 'Koltiva.view.Ext_staff.PanelFormAdditional-Form-AccountNumber',
                                name: 'Koltiva.view.Ext_staff.PanelFormAdditional-Form-AccountNumber'
                            }]
                        }]
                    }]
                }]
            }]
        }];

        thisObj.buttons = [{
            xtype: 'button',
            icon: varjs.config.base_url + 'images/icons/new/save.png',
            text: lang('Save'),
            cls: 'Sfr_BtnFormBlue',
            overCls: 'Sfr_BtnFormBlue-Hover',
            id: 'Koltiva.view.Ext_staff.PanelFormAdditional-BtnSave',
            handler: function () {
                var Formnya = Ext.getCmp('Koltiva.view.Ext_staff.PanelFormAdditional').getForm();

                if (Formnya.isValid()) {
                    Formnya.submit({
                        url: m_api + '/ext_staff/staff_additional',
                        method: 'POST',
                        waitMsg: 'Saving data...',
                        params: {
                            PersonID: thisObj.viewVar.PersonID,
                            StaffID : thisObj.viewVar.StaffID
                        },
                        success: function (fp, o) {
                            Ext.MessageBox.show({
                                title: 'Information',
                                msg: lang('Data saved'),
                                buttons: Ext.MessageBox.OK,
                                animateTarget: 'mb9',
                                icon: 'ext-mb-success'
                            });
                        },
                        failure: function (fp, o) {
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
                            catch (err) {
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
        }];

        this.callParent(arguments);
    },
    listeners: {
        afterRender: function (component, eOpts) {
            var thisObj = this;

            //load data form
            Ext.getCmp('Koltiva.view.Ext_staff.PanelFormAdditional').getForm().load({
                url: m_api + '/ext_staff/ext_staff_form_additional_open',
                method: 'GET',
                params: {
                    PersonID: thisObj.viewVar.PersonID,
                    StaffID : thisObj.viewVar.StaffID
                },
                success: function (form, action) {
                    var r = Ext.decode(action.response.responseText);
                    var RegID = r.data.RegID;
                    var CountryID = RegID.substr(0, 2);
                    Ext.getCmp('Koltiva.view.Ext_staff.PanelFormAdditional-Form-BankID').getStore().storeVar.CountryID = CountryID;
                    Ext.getCmp('Koltiva.view.Ext_staff.PanelFormAdditional-Form-BankID').getStore().load({
                        callback: function (records, operation, success) {
                            if (r.data.BankID) {
                                Ext.getCmp('Koltiva.view.Ext_staff.PanelFormAdditional-Form-BankID').setValue(r.data.BankID);
                            }
                        }
                    });
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

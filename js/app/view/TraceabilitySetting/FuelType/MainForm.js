/**************************************
 * Author : aji.alhabsyi@koltiva.com
 * Created On : Fri June 24 2022
 * File : MainForm.js
 ************************************** */
/*
    Param-param yang diperlukan ketika load View ini
    - OpsiDisplay
    - GHGFuelTypeID
*/

Ext.define('Koltiva.view.TraceabilitySetting.FuelType.MainForm', {
    extend: 'Ext.panel.Panel',
    id: 'Koltiva.view.TraceabilitySetting.FuelType.MainForm',
    style: 'padding: 0 15px 15px 15px; margin: 2px 0 0 0;',
    viewVar: false,
    setViewVar: function(value) {
        this.viewVar = value;
    },
    renderTo: 'ext-content',
    listeners: {
        afterRender: function() {
            var thisObj = this;
            document.getElementById('Sfr_IdBoxInfoDataGrid').style.display = 'none';
            document.getElementById('Sfr_Cont_IdBoxInfoDataGrid').style.display = 'none';

            if (thisObj.viewVar.OpsiDisplay == 'update') {
                //form reset
                Ext.getCmp('Koltiva.view.TraceabilitySetting.FuelType.MainForm-Form').getForm().reset();

                //Load Form
                Ext.getCmp('Koltiva.view.TraceabilitySetting.FuelType.MainForm-Form').getForm().load({
                    url: m_api + '/traceability_setting/fuel_type/fuel_type_data',
                    method: 'GET',
                    params: {
                        GHGFuelTypeID: this.viewVar.GHGFuelTypeID
                    },
                    success: function(form, action) {
                        let r = Ext.decode(action.response.responseText);

                        Ext.getCmp('Koltiva.view.TraceabilitySetting.FuelType.MainForm-Form-GHGFuelTypeID').setValue(r.data.GHGFuelTypeID);
                        Ext.getCmp('Koltiva.view.TraceabilitySetting.FuelType.MainForm-Form-FuelTypeName').setValue(r.data.FuelTypeName);
                        Ext.getCmp('Koltiva.view.TraceabilitySetting.FuelType.MainForm-Form-FuelTypeCoefficient').setValue(r.data.FuelTypeCoefficient);
                        Ext.getCmp('Koltiva.view.TraceabilitySetting.FuelType.MainForm-Form-StatusCode').setValue(r.data.StatusCode);
                    },
                    failure: function(form, action) {
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
    initComponent: function() {
        var thisObj = this;
        var labelWidth = 150;
        // var GHGFuelTypeID = thisObj.viewVar.GHGFuelTypeID;

        var cmbStatus = Ext.create('Ext.data.Store', {
            fields: ['id', 'label'],
            data: [{
                'id': 'active',
                'label': lang('Active')
            }, {
                'id': 'inactive',
                'label': lang('Inactive')
            }, {
                'id': 'nulified',
                'label': lang('Nulified')
            }]
        });

        thisObj.items = [{
            xtype: 'panel',
            border: false,
            layout: {
                type: 'hbox'
            },
            items: [{
                id: 'Koltiva.view.TraceabilitySetting.FuelType.MainForm-labelInfoInsert',
                html: '<div id="header_title_farmer">' + lang('Fuel Type Data') + '</div>'
            }]
        }, {
            items: [{
                id: 'Koltiva.view.TraceabilitySetting.FuelType.MainForm-LinkBackToList',
                html: '<div style="padding-bottom: 4px;" id="Sfr_IdBoxInfoDataGrid" class="Sfr_BoxInfoDataGrid"><ul class="Sft_UlListInfoDataGrid"><li class="Sft_ListInfoDataGrid"><a href="javascript:Ext.getCmp(\'Koltiva.view.TraceabilitySetting.FuelType.MainForm\').BackToList()"><img class="Sft_ListIconInfoDataGrid" src="' + varjs.config.base_url + 'images/icons/new/back.png" width="20" />&nbsp;&nbsp;' + lang('Back to List') + '</a></li></div>'
            }]
        }, {
            html: '<br />'
        }, {
            layout: 'column',
            border: false,
            items: [{
                columnWidth: 1,
                items: [{
                    xtype: 'form',
                    title: lang('Fuel Type Form'),
                    frame: true,
                    cls: 'Sfr_PanelLayoutForm',
                    id: 'Koltiva.view.TraceabilitySetting.FuelType.MainForm-Form',
                    fileUpload: false,
                    collapsible: true,
                    buttonAlign: 'right',
                    items: [{
                        layout: 'column',
                        border: false,
                        padding: 10,
                        items: [{
                            columnWidth: 1,
                            layout: 'form',
                            cls: 'Sfr_PanelLayoutFormContainer',
                            items: [{
                                xtype: 'textfield',
                                id: 'Koltiva.view.TraceabilitySetting.FuelType.MainForm-Form-GHGFuelTypeID',
                                name: 'Koltiva.view.TraceabilitySetting.FuelType.MainForm-Form-GHGFuelTypeID',
                                allowBlank: true,
                                fieldLabel: lang('GHG Fuel Type ID'),
                                labelWidth: labelWidth,
                                hidden: true
                            }, {
                                xtype: 'textfield',
                                id: 'Koltiva.view.TraceabilitySetting.FuelType.MainForm-Form-FuelTypeName',
                                name: 'Koltiva.view.TraceabilitySetting.FuelType.MainForm-Form-FuelTypeName',
                                allowBlank: false,
                                baseCls: 'Sfr_FormInputMandatory',
                                fieldLabel: lang('Fuel Type Name'),
                                labelWidth: labelWidth
                            }, {
                                xtype: 'numberfield',
                                id: 'Koltiva.view.TraceabilitySetting.FuelType.MainForm-Form-FuelTypeCoefficient',
                                name: 'Koltiva.view.TraceabilitySetting.FuelType.MainForm-Form-FuelTypeCoefficient',
                                allowBlank: false,
                                baseCls: 'Sfr_FormInputMandatory',
                                fieldLabel: lang('Fuel Type Coefficient'),
                                labelWidth: labelWidth,
                                minValue: 0,
                                // Remove spinner buttons, and arrow key and mouse wheel listeners
                                hideTrigger: false,
                                keyNavEnabled: false,
                                mouseWheelEnabled: false,
                                decimalPrecision: 3 // This will allow only 3 decimal places..
                            }, {
                                xtype: 'combobox',
                                id: 'Koltiva.view.TraceabilitySetting.FuelType.MainForm-Form-StatusCode',
                                name: 'Koltiva.view.TraceabilitySetting.FuelType.MainForm-Form-StatusCode',
                                allowBlank: false,
                                store: cmbStatus,
                                displayField: 'label',
                                valueField: 'id',
                                queryMode: 'local',
                                baseCls: 'Sfr_FormInputMandatory',
                                fieldLabel: lang('Status'),
                                labelWidth: labelWidth,
                                editable: false,
                                emptyText: lang('Pilih Status'),
                            }]
                        }]
                    }],
                    buttons: [{
                        xtype: 'button',
                        icon: varjs.config.base_url + 'images/icons/new/save.png',
                        text: lang('Save'),
                        cls: 'Sfr_BtnFormBlue',
                        overCls: 'Sfr_BtnFormBlue-Hover',
                        id: 'Koltiva.view.TraceabilitySetting.FuelType.MainForm-Form-BtnSave',
                        handler: function() {
                            let form = Ext.getCmp('Koltiva.view.TraceabilitySetting.FuelType.MainForm-Form').getForm();
                            if (form.isValid()) {
                                var method = 'POST';
                                form.submit({
                                    url: m_api + '/traceability_setting/fuel_type/fuel_type_data',
                                    method: method,
                                    waitMsg: lang('Saving data') +  '...',
                                    submitEmptyText: false,
                                    params: {
                                        OpsiDisplay: thisObj.viewVar.OpsiDisplay
                                    },
                                    success: function(rp, o) {
                                        var r = Ext.decode(o.response.responseText);

                                        let GHGFuelTypeID;
                                        let OpsiDisplay = thisObj.viewVar.OpsiDisplay;

                                        if(OpsiDisplay == 'insert') {
                                            GHGFuelTypeID = r.GHGFuelTypeID;
                                        } else {
                                            GHGFuelTypeID = Ext.getCmp('Koltiva.view.TraceabilitySetting.FuelType.MainForm-Form-GHGFuelTypeID').getValue();
                                        }

                                        Ext.MessageBox.show({
                                            title: lang('Information'),
                                            msg: r.message,
                                            buttons: Ext.MessageBox.OK,
                                            animateTarget: 'mb9',
                                            icon: 'ext-mb-success'
                                        });

                                        Ext.getCmp('Koltiva.view.TraceabilitySetting.FuelType.MainForm').destroy(); // destroy current view
                                        let FormMainApp = [];
                                        if(Ext.getCmp('Koltiva.view.TraceabilitySetting.FuelType.MainGrid') == undefined){
                                            FormMainApp = Ext.create('Koltiva.view.TraceabilitySetting.FuelType.MainGrid');
                                        }else{
                                            //destroy, create ulang
                                            Ext.getCmp('Koltiva.view.TraceabilitySetting.FuelType.MainGrid').destroy();
                                            FormMainApp = Ext.create('Koltiva.view.TraceabilitySetting.FuelType.MainGrid');
                                        }
                                    },
                                    failure: function(rp, o) {
                                        try {
                                            var r = Ext.decode(p.response.responseText);
                                            Ext.MessageBox.show({
                                                title: lang('Error'),
                                                msg: r.message,
                                                button: Ext.MessageBox.OK,
                                                animateTarget: 'mb9',
                                                icon: 'ext-mb-info'
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
                                    title: lang('Information'),
                                    msg: lang('Form is not valid yet'),
                                    buttons: Ext.MessageBox.OK,
                                    animateTarget: 'mb9',
                                    icon: 'ext-mb-error'
                                });
                            }
                        }
                    }]
                }]
            }]
        }];

        this.callParent(arguments);
    },
    BackToList: function(){
        Ext.getCmp('Koltiva.view.TraceabilitySetting.FuelType.MainForm').destroy(); //destory current view
        var GridMain = [];

        if(Ext.getCmp('Koltiva.view.TraceabilitySetting.FuelType.MainGrid') == undefined){
            GridMain = Ext.create('Koltiva.view.TraceabilitySetting.FuelType.MainGrid');
        }else{
            //destroy, create ulang
            Ext.getCmp('Koltiva.view.TraceabilitySetting.FuelType.MainGrid').destroy();
            GridMain = Ext.create('Koltiva.view.TraceabilitySetting.FuelType.MainGrid');
        }
    }
});
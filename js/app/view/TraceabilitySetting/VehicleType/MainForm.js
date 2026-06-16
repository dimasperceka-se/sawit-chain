/*******************************************
 * Author : aji.alhabsyi@koltiva.com
 * Created On : Tue June 28 2022
 * File : MainForm.js
********************************************/
/*
    Param-param yang diperlukan ketika load View ini
    - OpsiDisplay
    - GHGVehicleTypeID
*/

Ext.define('Koltiva.view.TraceabilitySetting.VehicleType.MainForm', {
    extend: 'Ext.panel.Panel',
    id: 'Koltiva.view.TraceabilitySetting.VehicleType.MainForm',
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
                //Form reset
                Ext.getCmp('Koltiva.view.TraceabilitySetting.VehicleType.MainForm-Form').getForm().reset();

                //Load Form
                Ext.getCmp('Koltiva.view.TraceabilitySetting.VehicleType.MainForm-Form').getForm().load({
                    url: m_api + '/traceability_setting/vehicle_type/vehicle_type_data',
                    method: 'GET',
                    params: {
                        GHGVehicleTypeID: this.viewVar.GHGVehicleTypeID
                    },
                    success: function(form, action) {
                        let r = Ext.decode(action.response.responseText);

                        Ext.getCmp('Koltiva.view.TraceabilitySetting.VehicleType.MainForm-Form-GHGVehicleTypeID').setValue(r.data.GHGVehicleTypeID);
                        Ext.getCmp('Koltiva.view.TraceabilitySetting.VehicleType.MainForm-Form-VehicleTypeName').setValue(r.data.VehicleTypeName);
                        Ext.getCmp('Koltiva.view.TraceabilitySetting.VehicleType.MainForm-Form-FuelConsumption').setValue(r.data.FuelConsumption);
                        Ext.getCmp('Koltiva.view.TraceabilitySetting.VehicleType.MainForm-Form-StatusCode').setValue(r.data.StatusCode);
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

        var cmbStatus = Ext.create('Ext.data.Store', {
            fields: ['id', 'label'],
            data:[{
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
                id: 'Koltiva.view.TraceabilitySetting.VehicleType.MainForm-labelInfoInsert',
                html: '<div id="header_title_farmer">' + lang('Vehicle Type Data') + '</div>'
            }]
        }, {
            items: [{
                id: 'Koltiva.view.TraceabilitySetting.VehicleType.MainForm-LinkBackToList',
                html: '<div style="padding-bottom: 4px;" id="Sfr_IdBoxInfoDataGrid" class="Sfr_BoxInfoDataGrid"><ul class="Sft_UlListInfoDataGrid"><li class="Sft_ListInfoDataGrid"><a href="javascript:Ext.getCmp(\'Koltiva.view.TraceabilitySetting.VehicleType.MainForm\').BackToList()"><img class="Sft_ListIconInfoDataGrid" src="' + varjs.config.base_url + 'images/icons/new/back.png" width="20" />&nbsp;&nbsp;' + lang('Back to List') + '</a></li></div>'
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
                    title: lang('Vehicle Type Form'),
                    frame: true,
                    cls: 'Sfr_PanelLayoutForm',
                    id: 'Koltiva.view.TraceabilitySetting.VehicleType.MainForm-Form',
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
                                id: 'Koltiva.view.TraceabilitySetting.VehicleType.MainForm-Form-GHGVehicleTypeID',
                                name: 'Koltiva.view.TraceabilitySetting.VehicleType.MainForm-Form-GHGVehicleTypeID',
                                allowBlank: true,
                                fieldLabel: lang('GHG Vehicle Type ID'),
                                labelWidth: labelWidth,
                                hidden: true
                            }, {
                                xtype: 'textfield',
                                id: 'Koltiva.view.TraceabilitySetting.VehicleType.MainForm-Form-VehicleTypeName',
                                name: 'Koltiva.view.TraceabilitySetting.VehicleType.MainForm-Form-VehicleTypeName',
                                allowBlank: false,
                                baseCls: 'Sfr_FormInputMandatory',
                                fieldLabel: lang('Vehicle Type Name'),
                                labelWidth: labelWidth
                            }, {
                                xtype: 'numberfield',
                                id: 'Koltiva.view.TraceabilitySetting.VehicleType.MainForm-Form-FuelConsumption',
                                name: 'Koltiva.view.TraceabilitySetting.VehicleType.MainForm-Form-FuelConsumption',
                                allowBlank: false,
                                baseCls: 'Sfr_FormInputMandatory',
                                fieldLabel: lang('Fuel Consumption'),
                                labelWidth: labelWidth,
                                minValue: 0,
                                // Remove spinner buttons, and arrow key and mouse wheel listeners
                                hideTrigger: false,
                                keyNavEnabled: false,
                                mouseWheelEnabled: false,
                                decimalPrecision: 3 // This will allow only 3 decimal places..
                            }, {
                                xtype: 'combobox',
                                id: 'Koltiva.view.TraceabilitySetting.VehicleType.MainForm-Form-StatusCode',
                                name: 'Koltiva.view.TraceabilitySetting.VehicleType.MainForm-Form-StatusCode',
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
                        id: 'Koltiva.view.TraceabilitySetting.VehicleType.MainForm-Form-BtnSave',
                        handler: function() {
                            let form = Ext.getCmp('Koltiva.view.TraceabilitySetting.VehicleType.MainForm-Form').getForm();
                            if (form.isValid()) {
                                var method = 'POST';
                                form.submit({
                                    url: m_api + '/traceability_setting/vehicle_type/vehicle_type_data',
                                    method: method,
                                    waitMsg: lang('Saving data') + '...',
                                    submitEmptyText: false,
                                    params: {
                                        OpsiDisplay: thisObj.viewVar.OpsiDisplay
                                    },
                                    success: function(rp, o) {
                                        var r = Ext.decode(o.response.responseText);
                                        
                                        let GHGVehicleTypeID;
                                        let OpsiDisplay = thisObj.viewVar.OpsiDisplay;

                                        if (OpsiDisplay == 'insert') {
                                            GHGVehicleTypeID = r.GHGVehicleTypeID;
                                        } else {
                                            GHGVehicleTypeID = Ext.getCmp('Koltiva.view.TraceabilitySetting.VehicleType.MainForm-Form-GHGVehicleTypeID').getValue();
                                        }

                                        Ext.MessageBox.show({
                                            title: lang('Information'),
                                            msg: r.message,
                                            buttons: Ext.MessageBox.OK,
                                            animateTarget: 'mb9',
                                            icon: 'ext-mb-success'
                                        });

                                        Ext.getCmp('Koltiva.view.TraceabilitySetting.VehicleType.MainForm').destroy(); // destroy current view
                                        let FormMainApp = [];
                                        if (Ext.getCmp('Koltiva.view.TraceabilitySetting.VehicleTYpe.MainGrid') == undefined) {
                                            FormMainApp = Ext.create('Koltiva.view.TraceabilitySetting.VehicleType.MainGrid');
                                        } else {
                                            // destroy, create ulang
                                            Ext.getCmp('Koltiva.view.TraceabilitySetting.VehicleType.MainGrid').destroy();
                                            FormMainApp = Ext.create('Koltiva.view.TraceabilitySetting.VehicleType.MainGrid');
                                        }
                                    },
                                    failure: function(rp, o) {
                                        try {
                                            var r = Ext.decode(p.response.responseText);
                                            Ext.MessageBox.show({
                                                title: lang('Error'),
                                                msg: r.message,
                                                buttons: Ext.MessageBox.OK,
                                                animateTarget: 'mb9',
                                                icon: 'ext-mb-info'
                                            });
                                        }
                                        catch (err) {
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
    BackToList: function() {
        Ext.getCmp('Koltiva.view.TraceabilitySetting.VehicleType.MainForm').destroy(); //destroy current view
        var GridMain = [];

        if (Ext.getCmp('Koltiva.view.TraceabilitySetting.VehicleType.MainGrid') == undefined) {
            GridMain = Ext.create('Koltiva.view.TraceabilitySetting.VehicleType.MainGrid');
        } else {
            //destroy, create ulang
            Ext.getCmp('Koltiva.view.TraceabilitySetting.VehicleType.MainGrid').destroy();
            GridMain = Ext.getCmp('Koltiva.view.TraceabilitySetting.VehicleType.MainGrid');
        }
    }
});
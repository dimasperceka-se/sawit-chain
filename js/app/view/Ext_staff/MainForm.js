/******************************************
 *  Author : fashah.darullah@koltiva.com
 *  Created On : Mon Jan 20 2020
 *  File : MainForm.js
 *******************************************/
/*
    Param2 yg diperlukan ketika load View ini
    - OpsiDisplay
    - PersonID
    - StaffID
*/

Ext.define('Koltiva.view.Ext_staff.MainForm', {
    extend: 'Ext.panel.Panel',
    id: 'Koltiva.view.Ext_staff.MainForm',
    style: 'padding:0 15px 15px 15px;margin:5px 0 0 0;',
    viewVar: false,
    setViewVar: function (value) {
        this.viewVar = value;
    },
    renderTo: 'ext-content',
    listeners: {
        afterRender: function () {
            var thisObj = this;
            document.getElementById('Sfr_IdBoxInfoDataGrid').style.display = 'none';

            if (thisObj.viewVar.OpsiDisplay == 'insert') {
                Ext.getCmp('Koltiva.view.Ext_staff.MainForm-FormBasicData-TabAddDataForm').setDisabled(true);
                Ext.getCmp('Koltiva.view.Ext_staff.MainForm-FormBasicData-TabInteractions').setDisabled(true);

                if (m_PartnerAsParent != 'Yes') {
                    Ext.getCmp('Koltiva.view.Ext_staff.MainForm-FormBasicData-PartnerID').setDisabled(true);
                    Ext.getCmp('Koltiva.view.Ext_staff.MainForm-FormBasicData-PartnerID').setVisible(false);
                    Ext.getCmp('Koltiva.view.Ext_staff.MainForm-FormBasicData-PartnerID').allowBlank = true;
                }
            }

            if (thisObj.viewVar.OpsiDisplay == 'view' || thisObj.viewVar.OpsiDisplay == 'update') {

                //Set ReadOnly
                Ext.getCmp('Koltiva.view.Ext_staff.MainForm-FormBasicData-StaffRole').setReadOnly(true);

                //load formnya
                Ext.getCmp('Koltiva.view.Ext_staff.MainForm-FormBasicData').getForm().load({
                    url: m_api + '/ext_staff/ext_staff_form_open',
                    method: 'GET',
                    params: {
                        PersonID: this.viewVar.PersonID,
                        StaffID:this.viewVar.StaffID
                    },
                    success: function (form, action) {
                        Ext.MessageBox.hide();
                        var r = Ext.decode(action.response.responseText);
                        var angkaRand = Math.floor((Math.random() * 100) + 1);

                        if (r.data.Photo != null) {
                            Ext.getCmp('Koltiva.view.Ext_staff.MainForm-FormBasicData-Photo').update('<a href="' + m_url_awss3 + '/' + r.data.Photo + '" data-lightbox="image-1" data-title="Farmer Photo" title="View Photo"><img src="' + m_url_awss3 + '/' + r.data.Photo + '" style="height:150px;margin:0px 5px 5px 0px;float:left;" /></a>');
                        } else {
                            Ext.getCmp('Koltiva.view.Ext_staff.MainForm-FormBasicData-Photo').update('<img src="' + m_api_base_url + '/assets/images/farmer-default.png" style="height:150px;margin:0px 5px 5px 0px;float:left;" />');
                        }

                        //Title
                        Ext.getCmp('Koltiva.view.Ext_staff.MainForm-labelInfoInsert').update('<div id="header_title_farmer">' + ' <strong>' + Ext.getCmp('Koltiva.view.Ext_staff.MainForm-FormBasicData-PersonNm').getValue() + '</strong></div>');
                        Ext.getCmp('Koltiva.view.Ext_staff.MainForm-labelInfoInsert').doLayout();

                        //set all form input readyonly
                        Ext.getCmp('Koltiva.view.Ext_staff.MainForm-FormBasicData').getForm().getFields().each(function(field) {
                            field.setReadOnly(true);
                        });
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

            // set map
            // Ext.getCmp('Koltiva.view.Ext_staff.MainForm').initMap('map-picker', Ext.getCmp('Koltiva.view.Ext_staff.MainForm-FormBasicData-Latitude'), Ext.getCmp('Koltiva.view.Ext_staff.MainForm-FormBasicData-Longitude'));
        },
        beforerender: function () {
            var thisObj = this;

            if (thisObj.viewVar.OpsiDisplay != 'insert') {
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
        thisObj.TotalCostFertil = 0;
        thisObj.TotalCostAgrochemical = 0;
        thisObj.TotalCostCulturalPratic = 0;

        //Store ========== (Begin)
        thisObj.StoreComboStaffRole = Ext.create('Koltiva.store.ComboGeneral.ComboStaffRole');
        thisObj.StoreComboWorkareaProvince = Ext.create('Koltiva.store.ComboGeneral.ComboWorkareaProvince');
        thisObj.StoreComboWorkareaDistrict = Ext.create('Koltiva.store.ComboGeneral.ComboWorkareaDistrict', {
            storeVar: {
                prov: null
            }
        });
        thisObj.StoreComboPosition = Ext.create('Koltiva.store.ComboGeneral.ComboStaffPosition', {
            storeVar: {
                ObjType: null
            }
        });
        thisObj.StoreComboPosition.storeVar.ObjType = 'program';
        thisObj.StoreComboPosition.load();

        thisObj.StoreComboProvince = Ext.create('Koltiva.store.ComboGeneral.CmbProvinceAccess');
        thisObj.StoreComboDistrict = Ext.create('Koltiva.store.ComboGeneral.CmbDistrictAccess');
        thisObj.StoreComboObjID = Ext.create('Koltiva.store.ComboGeneral.ComboStaffObjID', {
            storeVar: {
                ObjType: 'program',
                DistrictID: null
            }
        });
        thisObj.StoreComboObjID.load();
        //Store ========== (End)

        //Panel Basic ==================================== (Begin)
        thisObj.ObjPanelBasicData = Ext.create('Ext.panel.Panel', {
            title: lang('Agronomist Data'),
            frame: true,
            cls: 'Sfr_PanelLayoutForm',
            id: 'Koltiva.view.Ext_staff.MainForm-FormGeneralData',
            collapsible: true,
            items: [{
                layout: 'column',
                border: false,
                padding: 10,
                items: [{
                    columnWidth: 1,
                    layout: 'form',
                    cls: 'Sfr_PanelLayoutFormContainer',
                    items: [{
                        xtype: 'tabpanel',
                        flex: 1,
                        activeTab: 0,
                        plain: true,
                        cls: 'Sfr_TabForm',
                        id: 'Koltiva.view.Ext_staff.MainForm-FormBasicData-Tab',
                        items: [{
                            xtype: 'form',
                            id: 'Koltiva.view.Ext_staff.MainForm-FormBasicData',
                            fileUpload: true,
                            buttonAlign: 'center',
                            title: lang('Basic Data'),
                            cls: 'Sfr_PanelSubLayoutForm',
                            items: [{
                                xtype: 'panel',
                                title: lang('Agronomist Profile'),
                                frame: false,
                                id: 'Koltiva.view.Ext_staff.MainForm-FormBasicData-SectionFarmerProfile',
                                style: 'margin-top:10px;',
                                cls: 'Sfr_PanelSubLayoutFormRoundedGray',
                                items: [{
                                    layout: 'column',
                                    border: false,
                                    items: [{
                                        columnWidth: 0.5,
                                        layout: 'form',
                                        style: 'padding:10px 0px 10px 5px;',
                                        defaults: {
                                            labelAlign: 'top',
                                            labelWidth: 150
                                        },
                                        items: [{
                                            layout: 'column',
                                            border: false,
                                            items: [{
                                                columnWidth: 0.4,
                                                layout: 'form',
                                                style: 'padding:10px 10px 10px 5px;',
                                                items: [{
                                                    xtype: 'panel',
                                                    id: 'Koltiva.view.Ext_staff.MainForm-FormBasicData-Photo',
                                                    html: '<img src="' + m_api_base_url + '/assets/images/default-staff.png" style="height:100%;width:100%;margin:0px 10px 5px 0px;float:left;" />'
                                                }]
                                            }]
                                        }, {
                                            html: '<div style="height:13px;">&nbsp;</div>'
                                        },{
                                            xtype: 'textfield',
                                            inputType: 'hidden',
                                            id: 'Koltiva.view.Ext_staff.MainForm-FormBasicData-PersonID',
                                            name: 'Koltiva.view.Ext_staff.MainForm-FormBasicData-PersonID'
                                        },{
                                            xtype: 'textfield',
                                            inputType: 'hidden',
                                            id: 'Koltiva.view.Ext_staff.MainForm-FormBasicData-StaffID',
                                            name: 'Koltiva.view.Ext_staff.MainForm-FormBasicData-StaffID'
                                        }, {
                                            html: '<div style="height:13px;">&nbsp;</div>'
                                        }, {
                                            xtype: 'textfield',
                                            id: 'Koltiva.view.Ext_staff.MainForm-FormBasicData-StaffRegisteredNumber',
                                            name: 'Koltiva.view.Ext_staff.MainForm-FormBasicData-StaffRegisteredNumber',
                                            fieldLabel: lang('ID')
                                        }, {
                                            html: '<div style="height:10px;">&nbsp;</div>'
                                        }, {
                                            xtype: 'textfield',
                                            id: 'Koltiva.view.Ext_staff.MainForm-FormBasicData-PersonNm',
                                            name: 'Koltiva.view.Ext_staff.MainForm-FormBasicData-PersonNm',
                                            fieldLabel: lang('Full Name'),
                                            allowBlank: false,
                                            baseCls: 'Sfr_FormInputMandatory'
                                        }, {
                                            html: '<div style="height:10px;">&nbsp;</div>'
                                        },{
                                            xtype: 'datefield',
                                            id: 'Koltiva.view.Ext_staff.MainForm-FormBasicData-Birthdate',
                                            name: 'Koltiva.view.Ext_staff.MainForm-FormBasicData-Birthdate',
                                            fieldLabel: lang('Birthdate'),
                                            allowBlank: false,
                                            baseCls: 'Sfr_FormInputMandatory',
                                            format: 'Y-m-d',
                                            listeners: {
                                                change: function (cb, nv, ov) {
                                                    var TglNya = Ext.Date.format(nv, 'Y-m-d');
                                                    var today = new Date();
                                                    var birthDate = new Date(TglNya);
                                                    var age = today.getFullYear() - birthDate.getFullYear();
                                                    var m = today.getMonth() - birthDate.getMonth();
                                                    if (m < 0 || (m === 0 && today.getDate() < birthDate.getDate())) {
                                                        age--;
                                                    }
                                                }
                                            }
                                        }, {
                                            html: '<div style="height:10px;">&nbsp;</div>'
                                        },{
                                            xtype: 'radiogroup',
                                            fieldLabel: lang('Gender'),
                                            allowBlank: false,
                                            baseCls: 'Sfr_FormInputMandatory',
                                            msgTarget: 'side',
                                            columns: 3,
                                            items: [{
                                                boxLabel: lang('Male'),
                                                name: 'Koltiva.view.Ext_staff.MainForm-FormBasicData-Gender',
                                                inputValue: 'm',
                                                id: 'Koltiva.view.Ext_staff.MainForm-FormBasicData-GenderM',
                                                listeners: {
                                                    change: function () {
                                                        return false;
                                                    }
                                                }
                                            }, {
                                                boxLabel: lang('Female'),
                                                name: 'Koltiva.view.Ext_staff.MainForm-FormBasicData-Gender',
                                                inputValue: 'f',
                                                id: 'Koltiva.view.Ext_staff.MainForm-FormBasicData-GenderF',
                                                listeners: {
                                                    change: function () {
                                                        return false;
                                                    }
                                                }
                                            }]
                                        }, {
                                            html: '<div style="height:10px;">&nbsp;</div>'
                                        }]
                                    }, {
                                        columnWidth: 0.5,
                                        layout: 'form',
                                        style: 'padding:10px 5px 10px 20px;',
                                        defaults: {
                                            labelAlign: 'top',
                                            labelWidth: 150
                                        },
                                        items: [{
                                            xtype: 'panel',
                                            title: lang('Communication and Media'),
                                            frame: false,
                                            id: 'Koltiva.view.Ext_staff.MainForm-FormBasicData-SectionCom',
                                            style: 'margin-top:15px;',
                                            cls: 'Sfr_PanelSubLayoutFormRoundedGray',
                                            items: [{
                                                layout: 'column',
                                                border: false,
                                                items: [{
                                                    columnWidth: 1,
                                                    layout: 'form',
                                                    style: 'padding:10px 0px 0px 0px;',
                                                    defaults: {
                                                        labelAlign: 'top'
                                                    },
                                                    items: [ {
                                                        columnWidth: 1,
                                                        border: false,
                                                        layout: 'column',
                                                        style: 'margin-bottom:3px;',
                                                        items: [{
                                                            xtype: 'textfield',
                                                            fieldLabel: lang('Handphone'),
                                                            labelAlign: 'top',
                                                            style: 'margin-right:5px;',
                                                            width: 50,
                                                            readOnly: true,
                                                            id: 'Koltiva.view.Ext_staff.MainForm-FormBasicData-HandphoneCode',
                                                            name: 'Koltiva.view.Ext_staff.MainForm-FormBasicData-HandphoneCode'
                                                        }, {
                                                            xtype: 'textfield',
                                                            style: 'margin-top:30px;',
                                                            id: 'Koltiva.view.Ext_staff.MainForm-FormBasicData-Handphone',
                                                            name: 'Koltiva.view.Ext_staff.MainForm-FormBasicData-Handphone',
                                                            allowBlank: false,
                                                            baseCls: 'Sfr_FormInputMandatory'
                                                        }]
                                                    }, {
                                                        html: '<div style="height:13px;">&nbsp;</div>'
                                                    }, {
                                                        xtype: 'textfield',
                                                        id: 'Koltiva.view.Ext_staff.MainForm-FormBasicData-Email',
                                                        name: 'Koltiva.view.Ext_staff.MainForm-FormBasicData-Email',
                                                        fieldLabel: lang('Email'),
                                                        allowBlank: false,
                                                        baseCls: 'Sfr_FormInputMandatory'
                                                    }]
                                                }]
                                            }]
                                        },{
                                            xtype: 'panel',
                                            title: lang('Address and Location'),
                                            frame: false,
                                            id: 'Koltiva.view.Ext_staff.MainForm-FormBasicData-SectionGeneralData',
                                            style: 'margin-top:12px;',
                                            cls: 'Sfr_PanelSubLayoutFormRoundedGray',
                                            items: [{
                                                layout: 'column',
                                                border: false,
                                                items: [{
                                                    columnWidth: 1,
                                                    layout: 'form',
                                                    defaults: {
                                                        labelAlign: 'top'
                                                    },
                                                    items: [{
                                                        xtype: 'combobox',
                                                        id: 'Koltiva.view.Ext_staff.MainForm-FormBasicData-ProvinceID',
                                                        name: 'Koltiva.view.Ext_staff.MainForm-FormBasicData-ProvinceID',
                                                        store: thisObj.StoreComboProvince,
                                                        fieldLabel: lang('Province'),
                                                        queryMode: 'local',
                                                        displayField: 'label',
                                                        valueField: 'id',
                                                        listeners: {
                                                            change: function(cb, nv, ov) {
                                                                thisObj.StoreComboDistrict.load({
                                                                    params: {
                                                                        ProvinceID: nv
                                                                    }
                                                                });
                                                            }
                                                        }
                                                    },{
                                                        xtype: 'combobox',
                                                        id: 'Koltiva.view.Ext_staff.MainForm-FormBasicData-DistrictID',
                                                        name: 'Koltiva.view.Ext_staff.MainForm-FormBasicData-DistrictID',
                                                        store: thisObj.StoreComboDistrict,
                                                        fieldLabel: lang('District'),
                                                        queryMode: 'local',
                                                        displayField: 'label',
                                                        valueField: 'id',
                                                        listeners: {
                                                            change: function(cb, nv, ov) {
                                                                thisObj.StoreComboObjID.storeVar.ObjType = 'program';
                                                                thisObj.StoreComboObjID.storeVar.DistrictID = nv;
                                                                thisObj.StoreComboObjID.load();
                                                            }
                                                        }
                                                    },{
                                                        xtype: 'textarea',
                                                        fieldLabel: lang('Address'),
                                                        id: 'Koltiva.view.Ext_staff.MainForm-FormBasicData-Address',
                                                        name: 'Koltiva.view.Ext_staff.MainForm-FormBasicData-Address',
                                                        height: 80
                                                    }]
                                                }]
                                            }]
                                        }]
                                    }]
                                }]
                            },{
                                layout: 'column',
                                border: false,
                                items: [{
                                    columnWidth: 0.5,
                                    layout: 'form',
                                    style: 'padding:10px 0px 10px 5px;',
                                    defaults: {
                                        labelAlign: 'top',
                                        labelWidth: 150
                                    },
                                    items: [{
                                        xtype: 'panel',
                                        title: lang('Status'),
                                        frame: false,
                                        id: 'Koltiva.view.Ext_staff.MainForm-FormBasicData-SectionStatus',
                                        style: 'margin-top:15px;',
                                        cls: 'Sfr_PanelSubLayoutFormRoundedGray',
                                        items: [{
                                            layout: 'column',
                                            border: false,
                                            items: [{
                                                columnWidth: 1,
                                                layout: 'form',
                                                style: 'padding:10px 0px 0px 0px;',
                                                defaults: {
                                                    labelAlign: 'top'
                                                },
                                                items: [{
                                                    xtype: 'radiogroup',
                                                    fieldLabel: lang('Status Agronomist'),
                                                    allowBlank: false,
                                                    baseCls: 'Sfr_FormInputMandatory',
                                                    msgTarget: 'side',
                                                    columns: 3,
                                                    items: [{
                                                        boxLabel: lang('Active'),
                                                        name: 'Koltiva.view.Ext_staff.MainForm-FormBasicData-StatusCode',
                                                        inputValue: 'active',
                                                        id: 'Koltiva.view.Ext_staff.MainForm-FormBasicData-StatusCodeActive',
                                                        listeners: {
                                                            change: function () {
                                                                return false;
                                                            }
                                                        }
                                                    }, {
                                                        boxLabel: lang('Inactive'),
                                                        name: 'Koltiva.view.Ext_staff.MainForm-FormBasicData-StatusCode',
                                                        inputValue: 'inactive',
                                                        id: 'Koltiva.view.Ext_staff.MainForm-FormBasicData-StatusCodeInactive',
                                                        listeners: {
                                                            change: function () {
                                                                return false;
                                                            }
                                                        }
                                                    }]
                                                },{ html:'<br>' },{
                                                    xtype: 'combobox',
                                                    id: 'Koltiva.view.Ext_staff.MainForm-FormBasicData-StaffRole',
                                                    name: 'Koltiva.view.Ext_staff.MainForm-FormBasicData-StaffRole',
                                                    store: thisObj.cmb_staffrole,
                                                    fieldLabel: lang('Role'),
                                                    allowBlank: false,
                                                    baseCls: 'Sfr_FormInputMandatory',
                                                    queryMode: 'local',
                                                    displayField: 'label',
                                                    valueField: 'id',
                                                    listeners: {
                                                        change: function (cb, nv, ov) {
                                                            return false;
                                                        }
                                                    }
                                                },{ html:'<br>' },{
                                                    xtype: 'combobox',
                                                    id: 'Koltiva.view.Ext_staff.MainForm-FormBasicData-StaffPosPositionID',
                                                    name: 'Koltiva.view.Ext_staff.MainForm-FormBasicData-StaffPosPositionID',
                                                    store: thisObj.StoreComboPosition,
                                                    fieldLabel: lang('Position'),
                                                    allowBlank: false,
                                                    baseCls: 'Sfr_FormInputMandatory',
                                                    queryMode: 'local',
                                                    displayField: 'label',
                                                    valueField: 'id'
                                                },{ html:'<br>' },{
                                                    xtype: 'combobox',
                                                    id: 'Koltiva.view.Ext_staff.MainForm-FormBasicData-PartnerID',
                                                    name: 'Koltiva.view.Ext_staff.MainForm-FormBasicData-PartnerID',
                                                    store: thisObj.StoreComboObjID,
                                                    fieldLabel: lang('Partner'),
                                                    allowBlank: false,
                                                    baseCls: 'Sfr_FormInputMandatory',
                                                    queryMode: 'local',
                                                    displayField: 'label',
                                                    valueField: 'id',
                                                    listeners: {
                                                        change: function (cb, nv, ov) {
                                                            return false
                                                        }
                                                    }
                                                }]
                                            }]
                                        }]
                                    }]
                                }]
                            }],
                            /*buttons: [{
                                xtype: 'button',
                                icon: varjs.config.base_url + 'images/icons/new/save.png',
                                text: lang('Save'),
                                cls: 'Sfr_BtnFormBlue',
                                overCls: 'Sfr_BtnFormBlue-Hover',
                                id: 'Koltiva.view.Ext_staff.MainForm-FormBasicData-BtnSave',
                                handler: function () {
                                    var Formnya = Ext.getCmp('Koltiva.view.Ext_staff.MainForm-FormBasicData').getForm();

                                    if (Formnya.isValid()) {

                                        Formnya.submit({
                                            url: m_api + '/ext_staff/ext_staff_form',
                                            method: 'POST',
                                            waitMsg: 'Saving data...',
                                            params: {
                                                OpsiDisplay: thisObj.viewVar.OpsiDisplay
                                            },
                                            success: function (fp, o) {
                                                Ext.MessageBox.show({
                                                    title: 'Information',
                                                    msg: lang('Data saved'),
                                                    buttons: Ext.MessageBox.OK,
                                                    animateTarget: 'mb9',
                                                    icon: 'ext-mb-success',
                                                    fn: function (btn) {
                                                        if (btn == 'ok') {
                                                            Ext.getCmp('Koltiva.view.Ext_staff.MainForm').destroy(); //destory current view
                                                            var MainForm = [];
                                                            if (Ext.getCmp('Koltiva.view.Ext_staff.MainForm') == undefined) {
                                                                MainForm = Ext.create('Koltiva.view.Ext_staff.MainForm', {
                                                                    viewVar: {
                                                                        OpsiDisplay: 'update',
                                                                        PersonID: o.result.PersonID,
                                                                        StaffID: o.result.StaffID
                                                                    }
                                                                });
                                                            } else {
                                                                Ext.getCmp('Koltiva.view.Ext_staff.MainForm').destroy();
                                                                MainForm = Ext.create('Koltiva.view.Ext_staff.MainForm', {
                                                                    viewVar: {
                                                                        OpsiDisplay: 'update',
                                                                        PersonID: o.result.SupplierID,
                                                                        StaffID: o.result.StaffID
                                                                    }
                                                                });
                                                            }
                                                        }
                                                    }
                                                });
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
                                                } catch (err) {
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
                            }]*/
                        }],
                        listeners: {
                            'tabchange': function (tabPanel, tab) {
                                /*switch (tab.id) {
                                    case 'Koltiva.view.Ext_staff.MainForm-FormBasicData-TabFamily':
                                    case 'Koltiva.view.Ext_staff.MainForm-FormBasicData-TabLabour':
                                        //Ext.getCmp('Koltiva.view.Ext_staff.MainForm-FormBasicData-BtnSave').setVisible(false);
                                        break;
                                    case 'Koltiva.view.Ext_staff.MainForm-FormBasicData-TabContract':
                                        thisObj.ObjPanelContractGridCertificationContract.store.setStoreVar({
                                            FarmerID: Ext.getCmp('Koltiva.view.Ext_staff.MainForm-FormBasicData-FarmerID').getValue()
                                        });
                                        thisObj.ObjPanelContractGridCertificationContract.store.load();
                                        //Ext.getCmp('Koltiva.view.Ext_staff.MainForm-FormBasicData-BtnSave').setVisible(false);
                                        break;
                                    default:
                                        //Ext.getCmp('Koltiva.view.Ext_staff.MainForm-FormBasicData-BtnSave').setVisible(true);
                                        break;
                                }*/
                            }
                        }
                    }]
                }]
            }]
        });
        //Panel Basic ==================================== (End)

        //======================================================================= OBJ PANEL USER ACCOUNT (BEGIN) =====================================//
        var ObjPanelRight = [];

        if (thisObj.viewVar.OpsiDisplay == 'view' || thisObj.viewVar.OpsiDisplay == 'update') {
            //Farmer Assignement
            thisObj.ObjPanelGridFarmRelation = Ext.create('Koltiva.view.Ext_staff.PanelFarmerAssignmentGrid', {
                viewVar: {
                    StaffID: thisObj.viewVar.StaffID
                }
            });
            ObjPanelRight.push(thisObj.ObjPanelGridFarmRelation);
            
            //Access User Menu
            /*thisObj.ObjUserAccount = Ext.create('Koltiva.view.Staffuser.PanelUserMgt', {
                viewVar: {
                    PersonID: thisObj.viewVar.PersonID,
                    StaffID: thisObj.viewVar.StaffID,
                    OpsiDisplay: thisObj.viewVar.OpsiDisplay,
                    CallFrom:'agro'
                }
            });
            ObjPanelRight.push(thisObj.ObjUserAccount);*/
        }

        //======================================================================= OBJ PANEL DINAMIS (END) =====================================//

        //========================================================== LAYOUT UTAMA (Begin) ========================================//
        thisObj.items = [{
            xtype: 'panel',
            border: false,
            layout: {
                type: 'hbox'
            },
            items: [{
                id: 'Koltiva.view.Ext_staff.MainForm-labelInfoInsert',
                html: '<div id="header_title_farmer">' + lang('Agronomist Data') + '</div>'
            }]
        }, {
            items: [{
                id: 'Koltiva.view.Ext_staff.MainForm-LinkBackToList',
                html: '<div id="Sfr_IdBoxInfoDataGrid" class="Sfr_BoxInfoDataGrid"><ul class="Sft_UlListInfoDataGrid"><li class="Sft_ListInfoDataGrid"><a href="javascript:Ext.getCmp(\'Koltiva.view.Ext_staff.MainForm\').BackToList()"><img class="Sft_ListIconInfoDataGrid" src="' + varjs.config.base_url + 'images/icons/new/back.png" width="20" />&nbsp;&nbsp;' + lang('Back to Agronomist List') + '</a></li></div>'
            }]
        }, {
            html: '<br />'
        }, {
            layout: 'column',
            border: false,
            items: [{
                //LEFT CONTENT
                columnWidth: 0.55,
                items: [
                    thisObj.ObjPanelBasicData
                ]
            }, {
                //RIGHT CONTENT
                columnWidth: 0.45,
                items: ObjPanelRight
            }]
        }];
        //========================================================== LAYOUT UTAMA (END) ========================================//

        this.callParent(arguments);
    },
    BackToList: function () {
        Ext.getCmp('Koltiva.view.Ext_staff.MainForm').destroy(); //destory current view
        var GridMainExtStaff = [];
        if (Ext.getCmp('Koltiva.view.Ext_staff.MainGrid') == undefined) {
            GridMainExtStaff = Ext.create('Koltiva.view.Ext_staff.MainGrid');
        } else {
            //destroy, create ulang
            Ext.getCmp('Koltiva.view.Ext_staff.MainGrid').destroy();
            GridMainExtStaff = Ext.create('Koltiva.view.Ext_staff.MainGrid');
        }
    }
});
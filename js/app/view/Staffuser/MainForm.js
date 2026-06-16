/******************************************
 *  Author : n1colius.lau@gmail.com   
 *  Created On : Mon Jul 13 2020
 *  File : MainForm.js
 *******************************************/
/*
    Param2 yg diperlukan ketika load View ini
    * OpsiDisplay
    * PersonID
*/

Ext.define('Koltiva.view.Staffuser.MainForm' ,{
    extend: 'Ext.panel.Panel',
    id: 'Koltiva.view.Staffuser.MainForm',
    style:'padding:0 15px 15px 15px;margin:2px 0 0 0;',
    viewVar: false,
    setViewVar: function(value){
        this.viewVar = value;
    },
    renderTo: 'ext-content',
    listeners: {
        afterRender: function(){
            var thisObj = this;
            document.getElementById('Sfr_IdBoxInfoDataGrid').style.display = 'none';
            document.getElementById('Sfr_Cont_IdBoxInfoDataGrid').style.display = 'none';

            if (thisObj.viewVar.OpsiDisplay == 'view' || thisObj.viewVar.OpsiDisplay == 'update') {
                //form reset
                Ext.getCmp('Koltiva.view.Staffuser.MainForm-StaffForm').getForm().reset();

                //Set ReadOnly
                Ext.getCmp('Koltiva.view.Staffuser.MainForm-StaffForm-StaffRole').setReadOnly(true);
                Ext.getCmp('Koltiva.view.Staffuser.MainForm-StaffForm-PositionID').setReadOnly(true);

                //Button
                if(thisObj.viewVar.OpsiDisplay == 'view') {
                    Ext.getCmp('Koltiva.view.Staffuser.MainForm-StaffForm-BtnSave').setVisible(false);
                }

                //Load Form
                Ext.getCmp('Koltiva.view.Staffuser.MainForm-StaffForm').getForm().load({
                    url: m_api + '/staffuser/staff_data_form_open',
                    method: 'GET',
                    params: {
                        PersonID: this.viewVar.PersonID
                    },
                    success: function (form, action) {
                        let r = Ext.decode(action.response.responseText);
                        //console.log(r);

                        thisObj.StoreComboStaffRole.load({
                            callback: function(records, operation, success){
                                Ext.getCmp('Koltiva.view.Staffuser.MainForm-StaffForm-StaffRole').setValue(r.data.StaffRole);

                                //Position
                                Ext.getCmp('Koltiva.view.Staffuser.MainForm-StaffForm-PositionID').setValue(null);
                                thisObj.StoreComboPosition.storeVar.ObjType = r.data.StaffRole;
                                thisObj.StoreComboPosition.load({
                                    callback: function(records, operation, success){
                                        Ext.getCmp('Koltiva.view.Staffuser.MainForm-StaffForm-PositionID').setValue(r.data.PositionID);
                                    }
                                });

                                //Cek Role
                                switch (r.data.StaffRole) {
                                    case 'extension':
                                    case 'private':
                                    case 'program':
                                    case 'service':
                                    case 'mill':
                                        Ext.getCmp('Koltiva.view.Staffuser.MainForm-StaffForm-RoleProvinceID').setValue('');
                                        Ext.getCmp('Koltiva.view.Staffuser.MainForm-StaffForm-RoleProvinceID').setDisabled(true);
                                        Ext.getCmp('Koltiva.view.Staffuser.MainForm-StaffForm-RoleDistrictID').setDisabled(true);

                                        thisObj.StoreComboObjID.storeVar.ObjType = r.data.StaffRole;
                                        thisObj.StoreComboObjID.storeVar.DistrictID = null;
                                        thisObj.StoreComboObjID.load({
                                            callback: function(records, operation, success){
                                                Ext.getCmp('Koltiva.view.Staffuser.MainForm-StaffForm-ObjID').setValue(r.data.ObjID);
                                            }
                                        });
                                    case 'refinery':
                                        Ext.getCmp('Koltiva.view.Staffuser.MainForm-StaffForm-RoleProvinceID').setValue('');
                                        Ext.getCmp('Koltiva.view.Staffuser.MainForm-StaffForm-RoleProvinceID').setDisabled(true);
                                        Ext.getCmp('Koltiva.view.Staffuser.MainForm-StaffForm-RoleDistrictID').setDisabled(true);

                                        thisObj.StoreComboObjID.storeVar.ObjType = r.data.StaffRole;
                                        thisObj.StoreComboObjID.storeVar.DistrictID = null;
                                        thisObj.StoreComboObjID.load({
                                            callback: function(records, operation, success){
                                                Ext.getCmp('Koltiva.view.Staffuser.MainForm-StaffForm-ObjID').setValue(r.data.ObjID);
                                            }
                                        });
                                    break;

                                    case 'bank':
                                    case 'cooperative':
                                    case 'farmergroup':
                                    case 'sce':
                                    case 'trader':
                                    case 'warehouse':
                                    case 'agent':
                                        Ext.getCmp('Koltiva.view.Staffuser.MainForm-StaffForm-RoleProvinceID').setDisabled(false);
                                        Ext.getCmp('Koltiva.view.Staffuser.MainForm-StaffForm-RoleDistrictID').setDisabled(false);
                                        Ext.getCmp('Koltiva.view.Staffuser.MainForm-StaffForm-RoleProvinceID').setValue('');

                                        thisObj.StoreComboProvince.load({
                                            callback: function(records, operation, success){
                                                Ext.getCmp('Koltiva.view.Staffuser.MainForm-StaffForm-RoleProvinceID').setValue(r.data.RoleProvinceID);

                                                Ext.getCmp('Koltiva.view.Staffuser.MainForm-StaffForm-RoleDistrictID').setValue('');
                                                thisObj.StoreComboDistrict.load({
                                                    params: {
                                                        ProvinceID: r.data.RoleProvinceID
                                                    },
                                                    callback: function(records, operation, success){
                                                        Ext.getCmp('Koltiva.view.Staffuser.MainForm-StaffForm-RoleDistrictID').setValue(r.data.RoleDistrictID);

                                                        thisObj.StoreComboObjID.storeVar.ObjType = r.data.StaffRole;
                                                        thisObj.StoreComboObjID.storeVar.DistrictID = r.data.RoleDistrictID;
                                                        thisObj.StoreComboObjID.load({
                                                            callback: function(records, operation, success){
                                                                Ext.getCmp('Koltiva.view.Staffuser.MainForm-StaffForm-ObjID').setValue(r.data.ObjID);
                                                            }
                                                        });
                                                    }
                                                });
                                            }
                                        });
                                    break;
                                }
                            }
                        });

                        thisObj.StoreComboWorkareaProvince.load({
                            callback: function(records, operation, success){
                                Ext.getCmp('Koltiva.view.Staffuser.MainForm-StaffForm-WorkAreaProvinceID').setValue(r.data.WorkAreaProvinceID);

                                thisObj.StoreComboWorkareaDistrict.storeVar.prov = r.data.WorkAreaProvinceID;
                                thisObj.StoreComboWorkareaDistrict.load();
                                Ext.getCmp('Koltiva.view.Staffuser.MainForm-StaffForm-WorkAreaID').setValue(null);
                                thisObj.StoreComboWorkareaDistrict.load({
                                    callback: function(records, operation, success){
                                        Ext.getCmp('Koltiva.view.Staffuser.MainForm-StaffForm-WorkAreaID').setValue(r.data.WorkAreaID);
                                    }
                                });
                            }
                        });

                        if(r.data.Photo != "") {
                            var fotoUser = r.data.Photo;
                            checkImageExistsGeneral(fotoUser, function(existsImage) {
                                if (existsImage == true) {
                                    Ext.getCmp('Koltiva.view.Staffuser.MainForm-StaffForm-Photo').update('<img src="' + fotoUser + '" style="height:150px;margin:0px 5px 5px 0px;float:left;" />');
                                } else {
                                    Ext.getCmp('Koltiva.view.Staffuser.MainForm-StaffForm-Photo').update('<img src="' + m_api_base_url + '/images/Photo/default-user.png" style="height:150px;margin:0px 5px 5px 0px;float:left;" />');
                                }

                                Ext.getCmp('Koltiva.view.Staffuser.MainForm-StaffForm-Photo').doLayout();
                            });
                        }

                        if(r.data.UserInCognito == "Yes") {
                            if(m_sess_username == 'nikolius.lau' || m_sess_username == 'zaenal.arifin' || m_sess_username == 'gitandi.nadzari') { //hanya niko yg boleh update email untuk jaga kevalidan data
                                Ext.getCmp('Koltiva.view.Staffuser.MainForm-StaffForm-OfficialEmail').setReadOnly(false);
                            } else {
                                Ext.getCmp('Koltiva.view.Staffuser.MainForm-StaffForm-OfficialEmail').setReadOnly(true);
                            }
                        }

                        Ext.MessageBox.hide()
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
        beforeRender: function () {
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
    initComponent: function() {
        var thisObj = this;
        var labelWidth = 135;

        thisObj.StoreComboMill = Ext.create('Ext.data.Store', {
            extend: 'Ext.data.Model',
            fields: ['id', 'label'],
            autoLoad: true,
            proxy: {
                type: 'ajax',
                url: m_api + '/basic_staff/list_mill',
                reader: {
                    type: 'json',
                    root: 'data'
                }
            }
        });        

        thisObj.StorePhoneCode = Ext.create('Ext.data.Store', {
            extend: 'Ext.data.Model',
            fields: ['id', 'label'],
            autoLoad: true,
            proxy: {
                type: 'ajax',
                url: m_api + '/basic_staff/phone_code',
                reader: {
                    type: 'json',
                    root: 'data'
                }
            }
        });

        thisObj.StoreComboRefinery = Ext.create('Ext.data.Store', {
            extend: 'Ext.data.Model',
            fields: ['id', 'label'],
            autoLoad: true,
            proxy: {
                type: 'ajax',
                url: m_api + '/basic_staff/list_refinery',
                reader: {
                    type: 'json',
                    root: 'data'
                }
            }
        });

        thisObj.StoreComboTrader = Ext.create('Ext.data.Store', {
            extend: 'Ext.data.Model',
            fields: ['id', 'label'],
            autoLoad: false,
            proxy: {
                type: 'ajax',
                url: m_api + '/basic_staff/list_sme',
                reader: {
                    type: 'json',
                    root: 'data'
                }
            },
            listeners: {
                'beforeload': function(store, options) {
                    store.proxy.extraParams.MillID = Ext.getCmp('MillID').getValue();
                }
            }
        });

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
        thisObj.StoreComboProvince = Ext.create('Koltiva.store.ComboGeneral.CmbProvinceAccess');
        thisObj.StoreComboDistrict = Ext.create('Koltiva.store.ComboGeneral.CmbDistrictAccess');
        thisObj.StoreComboObjID = Ext.create('Koltiva.store.ComboGeneral.ComboStaffObjID', {
            storeVar: {
                ObjType: null,
                DistrictID: null
            }
        });
        //Store ========== (End)

        let ObjPanelUserMgt = [];
        let ObjPanelGridLogUserLogin = [];
        let ObjPanelGridFGRelation = [];
        let ObjPanelGridFarmRelation = [];
        let ObjPanelDhis = [];
        if(thisObj.viewVar.OpsiDisplay == 'view' || thisObj.viewVar.OpsiDisplay == 'update') {
            ObjPanelUserMgt = Ext.create('Koltiva.view.Staffuser.PanelUserMgt', {
                viewVar: {
                    PersonID: thisObj.viewVar.PersonID,
                    StaffID: thisObj.viewVar.StaffID,
                    OpsiDisplay: thisObj.viewVar.OpsiDisplay
                }
            });

            // ObjPanelDhis = Ext.create('Koltiva.view.Staffuser.PanelUserDhis', {
            //     viewVar: {
            //         PersonID: thisObj.viewVar.PersonID,
            //         StaffID: thisObj.viewVar.StaffID
            //     }
            // });

            ObjPanelGridFGRelation = Ext.create('Koltiva.view.Staffuser.GridFarmGateRelation', {
                viewVar: {
                    StaffID: thisObj.viewVar.StaffID
                }
            });

            //Farmer Assignement
            // ObjPanelGridFarmRelation = Ext.create('Koltiva.view.Staffuser.PanelFarmerAssignmentGrid', {
            //     viewVar: {
            //         StaffID: thisObj.viewVar.StaffID
            //     }
            // });

            ObjPanelGridLogUserLogin = Ext.create('Koltiva.view.Staffuser.GridLogUserLogin', {
                viewVar: {
                    PersonID: thisObj.viewVar.PersonID
                }
            });
        }

        thisObj.items = [{
            xtype: 'panel',
            border:false,
            layout:{
                type:'hbox'
            },
            items:[{
                id: 'Koltiva.view.Staffuser.MainForm-labelInfoInsert',
                html:'<div id="header_title_farmer">'+lang('Staff Data')+'</div>'
            }]
        },{
            items:[{
                id: 'Koltiva.view.Staffuser.MainForm-LinkBackToList',
                html:'<div style="padding-bottom:4px;" id="Sfr_IdBoxInfoDataGrid" class="Sfr_BoxInfoDataGrid"><ul class="Sft_UlListInfoDataGrid"><li class="Sft_ListInfoDataGrid"><a href="javascript:Ext.getCmp(\'Koltiva.view.Staffuser.MainForm\').BackToList()"><img class="Sft_ListIconInfoDataGrid" src="'+varjs.config.base_url+'images/icons/new/back.png" width="20" />&nbsp;&nbsp;'+lang('Back to List')+'</a></li></div>'
            }]
        },{
            html:'<br />'
        },{
            layout: 'column',
            border: false,
            items: [{
                columnWidth: 0.52,
                items:[{
                    xtype:'form',
                    title: lang('Staff Form'),
                    frame: true,
                    cls: 'Sfr_PanelLayoutForm',
                    id: 'Koltiva.view.Staffuser.MainForm-StaffForm',
                    fileUpload: true,
                    collapsible:true,
                    buttonAlign : 'right',
                    items: [{
                        layout: 'column',
                        border: false,
                        padding:10,
                        items:[{
                            columnWidth: 1,
                            layout:'form',
                            cls: 'Sfr_PanelLayoutFormContainer',
                            items:[{
                                xtype:'panel',
                                title: lang('Basic Data'),
                                frame: false,
                                id: 'Koltiva.view.Staffuser.MainForm-StaffForm-SectionBasicData',
                                style:'margin-top:7px;',
                                cls: 'Sfr_PanelSubLayoutFormRoundedGray',
                                items:[{
                                    layout: 'column',
                                    border: false,
                                    items:[{
                                        columnWidth: 0.4,
                                        layout:'form',
                                        style:'padding:12px 8px;',
                                        items:[{
                                            xtype: 'panel',
                                            id: 'Koltiva.view.Staffuser.MainForm-StaffForm-Photo',
                                            html: '<img src="' + m_api_base_url + '/images/Photo/default-user.png" style="height:150px;margin:0px 5px 5px 0px;float:left;" />'
                                        },{
                                            xtype: 'fileuploadfield',
                                            id: 'Koltiva.view.Staffuser.MainForm-StaffForm-PhotoInput',
                                            name: 'Koltiva.view.Staffuser.MainForm-StaffForm-PhotoInput',
                                            buttonText: 'Browse',
                                            cls: 'Sfr_FormBrowseBtn',
                                            listeners: {
                                                'change': function (fb, v) {
                                                    Ext.getCmp('Koltiva.view.Staffuser.MainForm-StaffForm').getForm().submit({
                                                        url: m_api + '/staffuser/staff_photo',
                                                        clientValidation: false,
                                                        params: {
                                                            OpsiDisplay: thisObj.viewVar.OpsiDisplay,
                                                            PersonID: thisObj.viewVar.PersonID
                                                        },
                                                        waitMsg: 'Sending Photo...',
                                                        success: function (fp, o) {
                                                            if(thisObj.viewVar.OpsiDisplay == 'insert') {
                                                                Ext.getCmp('Koltiva.view.Staffuser.MainForm-StaffForm-Photo').update('<img src="' + o.result.FileImage + '" style="height:150px;margin:0px 5px 5px 0px;float:left;" />');
                                                                Ext.getCmp('Koltiva.view.Staffuser.MainForm-StaffForm-PhotoInputData').setValue(o.result.PhotoInput);
                                                            }

                                                            if(thisObj.viewVar.OpsiDisplay == 'update') {
                                                                Ext.getCmp('Koltiva.view.Staffuser.MainForm-StaffForm-Photo').update('<img src="' + o.result.FileImage + '" style="height:150px;margin:0px 5px 5px 0px;float:left;" />');
                                                                Ext.getCmp('Koltiva.view.Staffuser.MainForm-StaffForm-PhotoInputData').setValue(o.result.PhotoInput);
                                                            }
                                                        },
                                                        failure: function (fp, o) {
                                                            Ext.MessageBox.show({
                                                                title: lang('Error'),
                                                                msg: o.result.message,
                                                                buttons: Ext.MessageBox.OK,
                                                                animateTarget: 'mb9',
                                                                icon: 'ext-mb-error'
                                                            });
                                                        }
                                                    });
                                                }
                                            }
                                        }, {
                                            xtype: 'textfield',
                                            id: 'Koltiva.view.Staffuser.MainForm-StaffForm-PhotoInputData',
                                            name: 'Koltiva.view.Staffuser.MainForm-StaffForm-PhotoInputData',
                                            inputType: 'hidden'
                                        }]
                                    },{
                                        columnWidth: 0.6,
                                        layout:'form',
                                        style:'padding:12px 8px;',
                                        items:[{
                                            xtype: 'textfield',
                                            id: 'Koltiva.view.Staffuser.MainForm-StaffForm-PersonID',
                                            name: 'Koltiva.view.Staffuser.MainForm-StaffForm-PersonID',
                                            inputType: 'hidden'
                                        },{
                                            xtype: 'textfield',
                                            id: 'Koltiva.view.Staffuser.MainForm-StaffForm-PersonNm',
                                            name: 'Koltiva.view.Staffuser.MainForm-StaffForm-PersonNm',
                                            allowBlank: false,
                                            baseCls: 'Sfr_FormInputMandatory',
                                            fieldLabel: lang('Fullname'),
                                            labelWidth: labelWidth
                                        },{
                                            xtype: 'textfield',
                                            id: 'Koltiva.view.Staffuser.MainForm-StaffForm-Ssn',
                                            name: 'Koltiva.view.Staffuser.MainForm-StaffForm-Ssn',
                                            fieldLabel: lang('ID Number'),
                                            labelWidth: labelWidth
                                        },{
                                            xtype: 'datefield',
                                            id: 'Koltiva.view.Staffuser.MainForm-StaffForm-Birthdate',
                                            name: 'Koltiva.view.Staffuser.MainForm-StaffForm-Birthdate',
                                            fieldLabel: lang('Birthdate'),
                                            allowBlank: false,
                                            baseCls: 'Sfr_FormInputMandatory',
                                            format: 'Y-m-d'
                                        },{
                                            xtype: 'radiogroup',
                                            fieldLabel: lang('Gender'),
                                            allowBlank: false,
                                            baseCls: 'Sfr_FormInputMandatory',
                                            msgTarget: 'side',
                                            columns: 2,
                                            items: [{
                                                boxLabel: lang('Male'),
                                                name: 'Koltiva.view.Staffuser.MainForm-StaffForm-Gender',
                                                inputValue: 'm',
                                                id: 'Koltiva.view.Staffuser.MainForm-StaffForm-GenderM',
                                                listeners: {
                                                    change: function () {
                                                        return false;
                                                    }
                                                }
                                            }, {
                                                boxLabel: lang('Female'),
                                                name: 'Koltiva.view.Staffuser.MainForm-StaffForm-Gender',
                                                inputValue: 'f',
                                                id: 'Koltiva.view.Staffuser.MainForm-StaffForm-GenderF',
                                                listeners: {
                                                    change: function () {
                                                        return false;
                                                    }
                                                }
                                            }]
                                        },{
                                            xtype: 'textfield',
                                            id: 'Koltiva.view.Staffuser.MainForm-StaffForm-Address',
                                            name: 'Koltiva.view.Staffuser.MainForm-StaffForm-Address',
                                            fieldLabel: lang('Address'),
                                            labelWidth: labelWidth
                                        }]
                                    }]
                                }]
                            },{
                                xtype:'panel',
                                title: lang('Staff Data'),
                                frame: false,
                                id: 'Koltiva.view.Staffuser.MainForm-StaffForm-SectionStaffData',
                                style:'margin-top:7px;',
                                cls: 'Sfr_PanelSubLayoutFormRoundedGray',
                                items:[{
                                    layout: 'column',
                                    border: false,
                                    items:[{
                                        columnWidth: 0.5,
                                        layout:'form',
                                        style:'padding:12px 8px;',
                                        items:[{
                                            xtype: 'combobox',
                                            id: 'Koltiva.view.Staffuser.MainForm-StaffForm-StaffRole',
                                            name: 'Koltiva.view.Staffuser.MainForm-StaffForm-StaffRole',
                                            store: thisObj.StoreComboStaffRole,
                                            fieldLabel: lang('Role'),
                                            labelWidth: labelWidth,
                                            allowBlank: false,
                                            baseCls: 'Sfr_FormInputMandatory',
                                            queryMode: 'local',
                                            displayField: 'label',
                                            valueField: 'id',
                                            listeners: {
                                                change: function(cb, nv, ov) {
                                                    //console.log(nv);

                                                    //Cek Role
                                                    switch (nv) {
                                                        case 'extension':
                                                        case 'private':
                                                        case 'program':
                                                        case 'service':
                                                        case 'mill':
                                                            Ext.getCmp('Koltiva.view.Staffuser.MainForm-StaffForm-RoleProvinceID').setValue('');
                                                            Ext.getCmp('Koltiva.view.Staffuser.MainForm-StaffForm-RoleProvinceID').setDisabled(true);
                                                            Ext.getCmp('Koltiva.view.Staffuser.MainForm-StaffForm-RoleDistrictID').setDisabled(true);

                                                            thisObj.StoreComboObjID.storeVar.ObjType = nv;
                                                            thisObj.StoreComboObjID.storeVar.DistrictID = null;
                                                            thisObj.StoreComboObjID.load();
                                                        case 'refinery':
                                                            Ext.getCmp('Koltiva.view.Staffuser.MainForm-StaffForm-RoleProvinceID').setValue('');
                                                            Ext.getCmp('Koltiva.view.Staffuser.MainForm-StaffForm-RoleProvinceID').setDisabled(true);
                                                            Ext.getCmp('Koltiva.view.Staffuser.MainForm-StaffForm-RoleDistrictID').setDisabled(true);

                                                            thisObj.StoreComboObjID.storeVar.ObjType = nv;
                                                            thisObj.StoreComboObjID.storeVar.DistrictID = null;
                                                            thisObj.StoreComboObjID.load();
                                                        break;

                                                        case 'bank':
                                                        case 'cooperative':
                                                        case 'farmergroup':
                                                        case 'sce':
                                                        case 'trader':
                                                        case 'warehouse':
                                                        case 'agent':
                                                            Ext.getCmp('Koltiva.view.Staffuser.MainForm-StaffForm-RoleProvinceID').setDisabled(false);
                                                            Ext.getCmp('Koltiva.view.Staffuser.MainForm-StaffForm-RoleDistrictID').setDisabled(false);
                                                            Ext.getCmp('Koltiva.view.Staffuser.MainForm-StaffForm-RoleProvinceID').setValue('');
                                                        break;
                                                    }

                                                    //Position
                                                    Ext.getCmp('Koltiva.view.Staffuser.MainForm-StaffForm-PositionID').setValue(null);
                                                    thisObj.StoreComboPosition.storeVar.ObjType = nv;
                                                    thisObj.StoreComboPosition.load();
                                                }
                                            }
                                        }, ,{
                                            xtype: 'combobox',
                                            id: 'Koltiva.view.Staffuser.MainForm-StaffForm-RoleProvinceID',
                                            name: 'Koltiva.view.Staffuser.MainForm-StaffForm-RoleProvinceID',
                                            store: thisObj.StoreComboProvince,
                                            fieldLabel: lang('Province'),
                                            labelWidth: labelWidth,
                                            queryMode: 'local',
                                            displayField: 'label',
                                            valueField: 'id',
                                            disabled:true,
                                            listeners: {
                                                change: function(cb, nv, ov) {
                                                    thisObj.StoreComboDistrict.load({
                                                        params: {
                                                            ProvinceID: nv
                                                        }
                                                    });
                                                    Ext.getCmp('Koltiva.view.Staffuser.MainForm-StaffForm-RoleDistrictID').setValue('');
                                                }
                                            }
                                        },{
                                            xtype: 'combobox',
                                            id: 'Koltiva.view.Staffuser.MainForm-StaffForm-RoleDistrictID',
                                            name: 'Koltiva.view.Staffuser.MainForm-StaffForm-RoleDistrictID',
                                            store: thisObj.StoreComboDistrict,
                                            fieldLabel: lang('District'),
                                            labelWidth: labelWidth,
                                            queryMode: 'local',
                                            displayField: 'label',
                                            valueField: 'id',
                                            disabled:true,
                                            listeners: {
                                                change: function(cb, nv, ov) {
                                                    thisObj.StoreComboObjID.storeVar.ObjType = Ext.getCmp('Koltiva.view.Staffuser.MainForm-StaffForm-StaffRole').getValue();
                                                    thisObj.StoreComboObjID.storeVar.DistrictID = nv;
                                                    thisObj.StoreComboObjID.load();
                                                }
                                            }
                                        },{
                                            xtype: 'combobox',
                                            fieldLabel: lang('Object ID'),
                                            labelWidth: labelWidth,
                                            allowBlank: false,
                                            baseCls: 'Sfr_FormInputMandatory',
                                            store: thisObj.StoreComboObjID,
                                            allowBlank:false,
                                            anyMatch: true,
                                            typeAhead: true,
                                            displayField: 'label',
                                            valueField: 'id',
                                            id: 'Koltiva.view.Staffuser.MainForm-StaffForm-ObjID',
                                            name: 'Koltiva.view.Staffuser.MainForm-StaffForm-ObjID',
                                            queryMode: 'local'
                                        },{
                                            xtype: 'combobox',
                                            fieldLabel: lang('Position'),
                                            labelWidth: labelWidth,
                                            store: thisObj.StoreComboPosition,
                                            displayField: 'label',
                                            valueField: 'id',
                                            allowBlank: false,
                                            baseCls: 'Sfr_FormInputMandatory',
                                            id: 'Koltiva.view.Staffuser.MainForm-StaffForm-PositionID',
                                            name: 'Koltiva.view.Staffuser.MainForm-StaffForm-PositionID',
                                            queryMode: 'local',
                                            allowBlank: false,
                                            baseCls: 'Sfr_FormInputMandatory'
                                        },{
                                            fieldLabel: lang('Status Staff'),
                                            labelWidth: labelWidth,
                                            allowBlank: false,
                                            baseCls: 'Sfr_FormInputMandatory',
                                            msgTarget: 'side',
                                            xtype: 'radiogroup',
                                            width: '100%',
                                            items:[{
                                                boxLabel: lang('Active'),
                                                id: 'Koltiva.view.Staffuser.MainForm-StaffForm-StatusCodeActive',
                                                name: 'Koltiva.view.Staffuser.MainForm-StaffForm-StatusCode',
                                                inputValue: 'active',
                                                listeners:{
                                                    change: function(){
                                                        return false;
                                                    }
                                                }
                                            },{
                                                boxLabel: lang('Inactive'),
                                                id: 'Koltiva.view.Staffuser.MainForm-StaffForm-StatusCodeInactive',
                                                name: 'Koltiva.view.Staffuser.MainForm-StaffForm-StatusCode',
                                                inputValue: 'inactive',
                                                listeners:{
                                                    change: function(){
                                                        return false;
                                                    }
                                                }
                                            }]
                                        },{
                                            xtype: 'combobox',
                                            fieldLabel: lang('Mill'),
                                            store: thisObj.StoreComboMill,
                                            displayField: 'label',
                                            valueField: 'id',
                                            id: 'MillID',
                                            name: 'MillID',
                                            queryMode: 'local',
                                            listeners: {
                                                change: function(cb, nv, ov) {
                                                    thisObj.StoreComboTrader.load();
                                                    Ext.getCmp('SmeID').setValue('');
                                                }
                                            }
                                        },{
                                            xtype: 'combobox',
                                            fieldLabel: lang('SME'),
                                            store: thisObj.StoreComboTrader,
                                            displayField: 'label',
                                            valueField: 'id',
                                            id: 'SmeID',
                                            name: 'SmeID',
                                            queryMode: 'local'
                                        },{
                                            xtype: 'combobox',
                                            fieldLabel: lang('Refinery'),
                                            store: thisObj.StoreComboRefinery,
                                            displayField: 'label',
                                            valueField: 'id',
                                            id: 'RefineryID',
                                            name: 'RefineryID',
                                            queryMode: 'local'
                                        }]
                                    },{
                                        columnWidth: 0.5,
                                        layout:'form',
                                        style:'padding:12px 8px;',
                                        items:[{
                                            xtype: 'combobox',
                                            fieldLabel: lang('Work Area Province'),
                                            labelWidth: labelWidth,
                                            allowBlank: false,
                                            baseCls: 'Sfr_FormInputMandatory',
                                            store: thisObj.StoreComboWorkareaProvince,
                                            displayField: 'label',
                                            valueField: 'id',
                                            id: 'Koltiva.view.Staffuser.MainForm-StaffForm-WorkAreaProvinceID',
                                            name: 'Koltiva.view.Staffuser.MainForm-StaffForm-WorkAreaProvinceID',
                                            queryMode: 'local',
                                            listeners: {
                                                change: function(cb, nv, ov) {
                                                    thisObj.StoreComboWorkareaDistrict.storeVar.prov = nv;
                                                    thisObj.StoreComboWorkareaDistrict.load();
                                                    Ext.getCmp('Koltiva.view.Staffuser.MainForm-StaffForm-WorkAreaID').setValue(null);
                                                }
                                            }
                                        },{
                                            xtype: 'combobox',
                                            fieldLabel: lang('Work Area District'),
                                            labelWidth: labelWidth,
                                            allowBlank: false,
                                            baseCls: 'Sfr_FormInputMandatory',
                                            store: thisObj.StoreComboWorkareaDistrict,
                                            displayField: 'label',
                                            valueField: 'id',
                                            id: 'Koltiva.view.Staffuser.MainForm-StaffForm-WorkAreaID',
                                            name: 'Koltiva.view.Staffuser.MainForm-StaffForm-WorkAreaID',
                                            queryMode: 'local'
                                        },{
                                            html : lang("Cell Phone")
                                        },{
                                            columnWidth: 1,
                                            border: false,
                                            layout: 'column',
                                            style:'margin-bottom:3px;',
                                            items:[{
                                                xtype: 'combobox',
                                                allowBlank: false,
                                                baseCls: 'Sfr_FormInputMandatory',
                                                style:'margin-right:5px;',
                                                width:100,
                                                store: thisObj.StorePhoneCode,
                                                displayField: 'label',
                                                valueField: 'id',
                                                id: 'Koltiva.view.Staffuser.MainForm-StaffForm-OfficialCellPhoneCode',
                                                name: 'Koltiva.view.Staffuser.MainForm-StaffForm-OfficialCellPhoneCode',
                                                value: '+62'
                                            },{
                                                xtype: 'textfield',
                                                style:'margin-top:3px;',
                                                allowBlank: false,
                                                emptyText: lang('ex: 81212344321'),
                                                id: 'Koltiva.view.Staffuser.MainForm-StaffForm-OfficialCellPhone',
                                                name: 'Koltiva.view.Staffuser.MainForm-StaffForm-OfficialCellPhone'
                                            }]
                                        },{
                                            xtype: 'textfield',
                                            fieldLabel: lang('Email'),
                                            labelWidth: labelWidth,
                                            vtype: 'email',
                                            allowBlank: false,
                                            baseCls: 'Sfr_FormInputMandatory',
                                            id: 'Koltiva.view.Staffuser.MainForm-StaffForm-OfficialEmail',
                                            name: 'Koltiva.view.Staffuser.MainForm-StaffForm-OfficialEmail'
                                        },{
                                            xtype: 'textfield',
                                            fieldLabel: lang('CC Email'),
                                            labelWidth: labelWidth,
                                            emptyText: lang('Comma separated value'),
                                            id: 'Koltiva.view.Staffuser.MainForm-StaffForm-CCEmail',
                                            name: 'Koltiva.view.Staffuser.MainForm-StaffForm-CCEmail'
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
                        id: 'Koltiva.view.Staffuser.MainForm-StaffForm-BtnSave',
                        handler: function () {
                            let form = Ext.getCmp('Koltiva.view.Staffuser.MainForm-StaffForm').getForm();
                            if (form.isValid()) {
                                form.submit({
                                    url: m_api + '/staffuser/staff_form',
                                    method:'POST',
                                    waitMsg: lang('Saving data')+'...',
                                    submitEmptyText : false,
                                    params: {
                                        OpsiDisplay: thisObj.viewVar.OpsiDisplay
                                    },
                                    success: function(rp, o){
                                        var r = Ext.decode(o.response.responseText);
                                        //console.log(r);
                                        let PersonID;
                                        let OpsiDisplay = thisObj.viewVar.OpsiDisplay;

                                        if(OpsiDisplay == 'insert') {
                                            PersonID    = r.PersonID;
                                            StaffID     = r.StaffID;
                                        } else {
                                            PersonID    = Ext.getCmp('Koltiva.view.Staffuser.MainForm-StaffForm-PersonID').getValue();
                                            StaffID     = thisObj.viewVar.StaffID;
                                        }

                                        Ext.MessageBox.show({
                                            title: lang('Information'),
                                            msg: r.message,
                                            buttons: Ext.MessageBox.OK,
                                            animateTarget: 'mb9',
                                            icon: 'ext-mb-success'
                                        });

                                        //load ulang page
                                        Ext.getCmp('Koltiva.view.Staffuser.MainForm').destroy(); //destory current view
                                        let FormMainApp = [];
                                        if(Ext.getCmp('Koltiva.view.Staffuser.MainForm') == undefined){
                                            FormMainApp = Ext.create('Koltiva.view.Staffuser.MainForm', {
                                                viewVar: {
                                                    OpsiDisplay: 'update',
                                                    PersonID: PersonID,
                                                    StaffID: StaffID
                                                }
                                            });
                                        }else{
                                            //destroy, create ulang
                                            Ext.getCmp('Koltiva.view.Staffuser.MainForm').destroy();
                                            FormMainApp = Ext.create('Koltiva.view.Staffuser.MainForm', {
                                                viewVar: {
                                                    OpsiDisplay: 'update',
                                                    PersonID: PersonID,
                                                    StaffID: StaffID
                                                }
                                            });
                                        }
                                    },
                                    failure: function(rp, o){
                                        try {
                                            var r = Ext.decode(o.response.responseText);
                                            Ext.MessageBox.show({
                                                title: lang('Error'),
                                                msg: r.message,
                                                buttons: Ext.MessageBox.OK,
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
                },ObjPanelGridFGRelation,ObjPanelGridFarmRelation,ObjPanelGridLogUserLogin]
            },{
                columnWidth: 0.48,
                items:[ObjPanelUserMgt,ObjPanelDhis]
            }]
        }];

        this.callParent(arguments);
    },
    BackToList: function(){
        Ext.getCmp('Koltiva.view.Staffuser.MainForm').destroy(); //destory current view
        var GridMain = [];

        if(Ext.getCmp('Koltiva.view.Staffuser.MainGrid') == undefined){
            GridMain = Ext.create('Koltiva.view.Staffuser.MainGrid');
        }else{
            //destroy, create ulang
            Ext.getCmp('Koltiva.view.Staffuser.MainGrid').destroy();
            GridMain = Ext.create('Koltiva.view.Staffuser.MainGrid');
        }
    }
});
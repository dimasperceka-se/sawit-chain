/*
* @Author: nikolius
* @Date:   2017-10-13 15:01:03
 * @Last Modified by: komarudin
 * @Last Modified time: 2018-05-22 14:22:07
*/

var cmbSelectedGroups = Ext.create('Ext.data.ArrayStore', {
    fields: ['GroupId', 'GroupName'],
    autoLoad: false
});

function set_selected_groups () {
    var itemSelectorField   = Ext.getCmp('Koltiva.view.Staff.RegisterStaff.WinRegisterStaffForm-Form-UserGroupIDs');
    var fieldList           = itemSelectorField.toField.store.getRange();
    var value = Ext.getCmp('Koltiva.view.Staff.RegisterStaff.WinRegisterStaffForm-Form-GroupIDDefa').getValue();
    var exist = false;

    cmbSelectedGroups.removeAll();
    $.each(fieldList, function(index, val) {
        if (value == val.data.GroupId) {
            exist = true;
        }
        cmbSelectedGroups.add({
            GroupId: val.data.GroupId,
            GroupName: val.data.GroupName,
        });
    });
    if (!exist) {
        Ext.getCmp('Koltiva.view.Staff.RegisterStaff.WinRegisterStaffForm-Form-GroupIDDefa').setValue('');
    }
}

Ext.define('Koltiva.view.Staff.RegisterStaff.WinRegisterStaffForm' ,{
    extend: 'Ext.window.Window',
    id: 'Koltiva.view.Staff.RegisterStaff.WinRegisterStaffForm',
    title: lang('Form Register Staff'),
    closable: true,
    modal: true,
    closeAction: 'destroy',
    width: '70%',
    height: '90%',
    overflowY: 'auto',
    viewVar: false,
    setViewVar: function(value){
        this.viewVar = value;
    },
    initComponent: function() {
        var thisObj = this;

        //store yg dipakai ======================= (begin)
        var cmbStaffRole = Ext.create('Koltiva.store.Staff.RegisterStaff.ComboStaffRole');

        var cmbPropinsiGeneral = Ext.create('Koltiva.store.ComboGeneral.ComboProvince');
        var cmbDistrictGeneral = Ext.create('Koltiva.store.ComboGeneral.ComboDistrict');

        var cmbObjID = Ext.create('Koltiva.store.Staff.RegisterStaff.ComboStaffObjID');
        var cmbPosition = Ext.create('Koltiva.store.Staff.RegisterStaff.ComboPosition');
        var cmbGroupUser = Ext.create('Koltiva.store.Staff.RegisterStaff.ComboGroupUser');

        var cmbAccessArea = Ext.create('Koltiva.store.Staff.RegisterStaff.ComboAccessArea');

        var cmbDhisRole = Ext.create('Koltiva.store.Staff.RegisterStaff.ComboDhisRole');
        var cmbDhisGroup = Ext.create('Koltiva.store.Staff.RegisterStaff.ComboDhisGroup');
        //store yg dipakai ======================= (end)

        thisObj.items = [{
            xtype: 'form',
            id: 'Koltiva.view.Staff.RegisterStaff.WinRegisterStaffForm-Form',
            padding:'5 25 5 8',
            items:[{
                layout: 'column',
                border: false,
                items:[{
                    columnWidth: 1,
                    layout:'form',
                    items:[{
                        xtype:'panel',
                        title: 'A. '+lang('Basic Data User'),
                        frame:true,
                        style:'margin-bottom:13px;',
                        padding:5,
                        items:[{
                            layout: 'column',
                            border: false,
                            items: [{
                                columnWidth: 1,
                                layout:'form',
                                items:[{
                                    xtype: 'hiddenfield',
                                    id: 'Koltiva.view.Staff.RegisterStaff.WinRegisterStaffForm-Form-RegID',
                                    name: 'Koltiva.view.Staff.RegisterStaff.WinRegisterStaffForm-Form-RegID'
                                },{
                                    xtype: 'textfield',
                                    fieldLabel: lang('Fullname'),
                                    labelWidth: 200,
                                    allowBlank: false,
                                    id: 'Koltiva.view.Staff.RegisterStaff.WinRegisterStaffForm-Form-Fullname',
                                    name: 'Koltiva.view.Staff.RegisterStaff.WinRegisterStaffForm-Form-Fullname'
                                },{
                                    xtype: 'textfield',
                                    fieldLabel: lang('Email'),
                                    labelWidth: 200,
                                    allowBlank: false,
                                    id: 'Koltiva.view.Staff.RegisterStaff.WinRegisterStaffForm-Form-Email',
                                    name: 'Koltiva.view.Staff.RegisterStaff.WinRegisterStaffForm-Form-Email'
                                },{
                                    xtype: 'textfield',
                                    fieldLabel: lang('Username'),
                                    labelWidth: 200,
                                    allowBlank: false,
                                    id: 'Koltiva.view.Staff.RegisterStaff.WinRegisterStaffForm-Form-Username',
                                    name: 'Koltiva.view.Staff.RegisterStaff.WinRegisterStaffForm-Form-Username'
                                },{
                                    html: '<div style="border-bottom:1px dashed gray;margin:8px 0"></div>'
                                },{
                                    xtype: 'combobox',
                                    fieldLabel: lang('Role'),
                                    labelWidth: 200,
                                    allowBlank: false,
                                    store: cmbStaffRole,
                                    queryMode: 'local',
                                    displayField: 'label',
                                    valueField: 'id',
                                    id: 'Koltiva.view.Staff.RegisterStaff.WinRegisterStaffForm-Form-ObjType',
                                    name: 'Koltiva.view.Staff.RegisterStaff.WinRegisterStaffForm-Form-ObjType',
                                    listeners: {
                                        change: function(cb, nv, ov) {
                                            switch (nv) {
                                                case 'private':
                                                case 'program':
                                                case 'service':
                                                case 'mill':
                                                case 'refinery':
                                                    Ext.getCmp('Koltiva.view.Staff.RegisterStaff.WinRegisterStaffForm-Form-Province').setDisabled(true);
                                                    Ext.getCmp('Koltiva.view.Staff.RegisterStaff.WinRegisterStaffForm-Form-District').setDisabled(true);

                                                    Ext.getCmp('Koltiva.view.Staff.RegisterStaff.WinRegisterStaffForm-Form-ObjID').setValue('');
                                                    cmbObjID.setStoreVar({
                                                        ObjType: nv,
                                                        DistrictID: null
                                                    });
                                                    cmbObjID.load();
                                                break;

                                                case 'agent':
                                                    Ext.getCmp('Koltiva.view.Staff.RegisterStaff.WinRegisterStaffForm-Form-Province').setDisabled(false);
                                                    Ext.getCmp('Koltiva.view.Staff.RegisterStaff.WinRegisterStaffForm-Form-District').setDisabled(false);
                                                break;
                                            }

                                            cmbPosition.setStoreVar({
                                                ObjType: nv
                                            });
                                            cmbPosition.load();
                                        }
                                    }
                                },{
                                    xtype: 'combobox',
                                    fieldLabel: lang('Province'),
                                    labelWidth: 200,
                                    disabled: true,
                                    store: cmbPropinsiGeneral,
                                    queryMode: 'local',
                                    displayField: 'label',
                                    valueField: 'id',
                                    id: 'Koltiva.view.Staff.RegisterStaff.WinRegisterStaffForm-Form-Province',
                                    name: 'Koltiva.view.Staff.RegisterStaff.WinRegisterStaffForm-Form-Province',
                                    listeners: {
                                        change: function(cb, nv, ov) {
                                            cmbDistrictGeneral.setStoreVar({
                                                ProvinceID: nv
                                            });
                                            cmbDistrictGeneral.load();

                                            Ext.getCmp('Koltiva.view.Staff.RegisterStaff.WinRegisterStaffForm-Form-District').setValue('');
                                        }
                                    }
                                },{
                                    xtype: 'combobox',
                                    fieldLabel: lang('District'),
                                    labelWidth: 200,
                                    disabled: true,
                                    store: cmbDistrictGeneral,
                                    queryMode: 'local',
                                    displayField: 'label',
                                    valueField: 'id',
                                    id: 'Koltiva.view.Staff.RegisterStaff.WinRegisterStaffForm-Form-District',
                                    name: 'Koltiva.view.Staff.RegisterStaff.WinRegisterStaffForm-Form-District',
                                    listeners: {
                                        change: function(cb, nv, ov) {
                                            Ext.getCmp('Koltiva.view.Staff.RegisterStaff.WinRegisterStaffForm-Form-ObjID').setValue('');
                                            cmbObjID.setStoreVar({
                                                ObjType: Ext.getCmp('Koltiva.view.Staff.RegisterStaff.WinRegisterStaffForm-Form-ObjType').getValue(),
                                                DistrictID: nv
                                            });
                                            cmbObjID.load()
                                        }
                                    }
                                },{
                                    xtype: 'combobox',
                                    fieldLabel: lang('ObjID'),
                                    labelWidth: 200,
                                    allowBlank: false,
                                    anyMatch: true,
                                    typeAhead: true,
                                    store: cmbObjID,
                                    queryMode: 'local',
                                    displayField: 'label',
                                    valueField: 'id',
                                    id: 'Koltiva.view.Staff.RegisterStaff.WinRegisterStaffForm-Form-ObjID',
                                    name: 'Koltiva.view.Staff.RegisterStaff.WinRegisterStaffForm-Form-ObjID',
                                },{
                                    xtype: 'combobox',
                                    fieldLabel: lang('Position'),
                                    store: cmbPosition,
                                    displayField: 'label',
                                    valueField: 'id',
                                    id: 'Koltiva.view.Staff.RegisterStaff.WinRegisterStaffForm-Form-PositionID',
                                    name: 'Koltiva.view.Staff.RegisterStaff.WinRegisterStaffForm-Form-PositionID',
                                    queryMode: 'local',
                                    allowBlank: false
                                },{
                                    html: '<div style="border-bottom:1px dashed gray;margin:8px 0"></div>'
                                },{
                                    layout: 'column',
                                    border: false,
                                    items:[{
                                        columnWidth: 1,
                                        margin:'5 5 10 5',
                                        padding:3,
                                        layout:{
                                            type:'vbox',
                                            align:'stretch'
                                        },
                                        items:[{
                                            xtype: 'itemselector',
                                            flex:true,
                                            id: 'Koltiva.view.Staff.RegisterStaff.WinRegisterStaffForm-Form-UserGroupIDs',
                                            name: 'Koltiva.view.Staff.RegisterStaff.WinRegisterStaffForm-Form-UserGroupIDs',
                                            fieldLabel: lang('Group User'),
                                            fromTitle: lang('Available'),
                                            toTitle: lang('Selected'),
                                            anchor: '100%',
                                            height:300,
                                            store: cmbGroupUser,
                                            valueField: 'GroupId',
                                            displayField: 'GroupName',
                                            value: [],
                                            allowBlank: false,
                                            msgTarget: 'side',
                                            listeners: {
                                                change: function() {
                                                    set_selected_groups();
                                                }
                                            }
                                        },{
                                            html:'<br />'
                                        },{
                                            id: 'Koltiva.view.Staff.RegisterStaff.WinRegisterStaffForm-Form-GroupIDDefa',
                                            name: 'Koltiva.view.Staff.RegisterStaff.WinRegisterStaffForm-Form-GroupIDDefa',
                                            xtype: 'combobox',
                                            allowBlank:false,
                                            fieldLabel: lang('Default Group'),
                                            store: cmbSelectedGroups,
                                            valueField: 'GroupId',
                                            displayField: 'GroupName',
                                            queryMode:'local'
                                        },{
                                            xtype: 'itemselector',
                                            flex:true,
                                            id: 'Koltiva.view.Staff.RegisterStaff.WinRegisterStaffForm-Form-AccessAreas',
                                            name: 'Koltiva.view.Staff.RegisterStaff.WinRegisterStaffForm-Form-AccessAreas',
                                            allowBlank:false,
                                            msgTarget: 'side',
                                            fieldLabel: lang('Access Area'),
                                            fromTitle: lang('Available'),
                                            toTitle: lang('Selected'),
                                            anchor: '100%',
                                            height:300,
                                            store: cmbAccessArea,
                                            valueField: 'id',
                                            displayField: 'name',
                                            value: []
                                        }]
                                    }]
                                }]
                            }]
                        }]
                    },{
                        html: '<br />'
                    },{
                        xtype:'panel',
                        title: 'B. '+lang('User Mobile'),
                        frame:true,
                        style:'margin-bottom:13px;',
                        padding:5,
                        items:[{
                            layout: 'column',
                            border: false,
                            items: [{
                                columnWidth: 1,
                                layout:'form',
                                items:[{
                                    fieldLabel: lang('Access to Mobile DHIS ?'),
                                    xtype: 'radiogroup',
                                    labelWidth: 200,
                                    columns: 2,
                                    items:[{
                                        boxLabel: lang('Yes'),
                                        name: 'Koltiva.view.Staff.RegisterStaff.WinRegisterStaffForm-Form-IsMobileDhisUser',
                                        inputValue: '1',
                                        id: 'Koltiva.view.Staff.RegisterStaff.WinRegisterStaffForm-Form-IsMobileDhisUserYes',
                                        listeners:{
                                            change: function(){
                                                if(this.checked == true){
                                                    Ext.getCmp('Koltiva.view.Staff.RegisterStaff.WinRegisterStaffForm-Form-UserExtRoleId').setDisabled(false);
                                                    Ext.getCmp('Koltiva.view.Staff.RegisterStaff.WinRegisterStaffForm-Form-UserExtGroupId').setDisabled(false);
                                                }else{
                                                    Ext.getCmp('Koltiva.view.Staff.RegisterStaff.WinRegisterStaffForm-Form-UserExtRoleId').setDisabled(true);
                                                    Ext.getCmp('Koltiva.view.Staff.RegisterStaff.WinRegisterStaffForm-Form-UserExtGroupId').setDisabled(true);
                                                }
                                                return false;
                                            }
                                        }
                                    },{
                                        boxLabel: lang('No'),
                                        name: 'Koltiva.view.Staff.RegisterStaff.WinRegisterStaffForm-Form-IsMobileDhisUser',
                                        inputValue: '0',
                                        id: 'Koltiva.view.Staff.RegisterStaff.WinRegisterStaffForm-Form-IsMobileDhisUserNo',
                                        listeners:{
                                            change: function(){
                                                return false;
                                            }
                                        }
                                    }]
                                },{
                                    xtype: 'combobox',
                                    labelWidth: 200,
                                    fieldLabel: lang('DHIS Role'),
                                    store: cmbDhisRole,
                                    queryMode: 'local',
                                    displayField: 'name',
                                    valueField: 'id',
                                    allowBlank: false,
                                    disabled: true,
                                    id: 'Koltiva.view.Staff.RegisterStaff.WinRegisterStaffForm-Form-UserExtRoleId',
                                    name: 'Koltiva.view.Staff.RegisterStaff.WinRegisterStaffForm-Form-UserExtRoleId'
                                },{
                                    xtype: 'combobox',
                                    labelWidth: 200,
                                    fieldLabel: lang('DHIS Group'),
                                    store: cmbDhisGroup,
                                    queryMode: 'local',
                                    displayField: 'name',
                                    valueField: 'id',
                                    allowBlank: false,
                                    disabled: true,
                                    id: 'Koltiva.view.Staff.RegisterStaff.WinRegisterStaffForm-Form-UserExtGroupId',
                                    name: 'Koltiva.view.Staff.RegisterStaff.WinRegisterStaffForm-Form-UserExtGroupId'
                                }]
                            }]
                        }]
                    }]
                }]
            }]
        }];

        thisObj.buttons = [{
            id: 'Koltiva.view.Staff.RegisterStaff.WinRegisterStaffForm-Form-btnSave',
            text: lang('Save'),
            margin: '5px',
            scale: 'large',
            ui: 's-button',
            cls: 's-blue',
            handler: function() {
                var formNya = Ext.getCmp('Koltiva.view.Staff.RegisterStaff.WinRegisterStaffForm-Form').getForm();
                if (formNya.isValid()) {

                    formNya.submit({
                        url: m_api + '/basic_staff/register_staff',
                        method:'POST',
                        waitMsg: 'Saving data...',
                        success: function(fp, o) {
                            Ext.MessageBox.show({
                                title: 'Information',
                                msg: lang('Data saved'),
                                buttons: Ext.MessageBox.OK,
                                animateTarget: 'mb9',
                                icon: 'ext-mb-success'
                            });

                            //form reset
                            formNya.reset();

                            //refresh store yg manggil
                            var caller_store_grid = Ext.data.StoreManager.lookup('Koltiva.store.Staff.RegisterStaff.MainGrid');
                            caller_store_grid.load();

                            //tutup popup
                            thisObj.close();
                        },
                        failure: function(fp, o){
                            var pesanNya;
                            if(o.result.message != undefined){
                                pesanNya = o.result.message;
                            }else{
                                pesanNya = lang('Connection error');
                            }
                            Ext.MessageBox.show({
                                title: 'Error',
                                msg: pesanNya,
                                buttons: Ext.MessageBox.OK,
                                animateTarget: 'mb9',
                                icon: 'ext-mb-error'
                            });
                        }
                    });

                }else{
                    Ext.MessageBox.show({
                        title: 'Attention',
                        msg: lang('Form not complete yet'),
                        buttons: Ext.MessageBox.OK,
                        animateTarget: 'mb9',
                        icon: 'ext-mb-info'
                    });
                }
            }
        },{
            text: lang('Close'),
            margin: '5px',
            scale: 'large',
            ui: 's-button',
            cls: 's-grey',
            handler: function() {
                //tutup popup
                thisObj.close();
            }
        }];

        this.callParent(arguments);
    },
    listeners: {
        afterRender: function(){
            var thisObj = this;

            //form reset
            var formNya = Ext.getCmp('Koltiva.view.Staff.RegisterStaff.WinRegisterStaffForm-Form');
            formNya.getForm().reset();

            if(thisObj.viewVar.opsiDisplay == 'insert'){
                Ext.getCmp('Koltiva.view.Staff.RegisterStaff.WinRegisterStaffForm-Form-IsMobileDhisUserNo').setValue(true);
            }

            if(thisObj.viewVar.opsiDisplay == 'view' || thisObj.viewVar.opsiDisplay == 'update'){
                if(thisObj.viewVar.opsiDisplay == 'view'){
                    Ext.getCmp('Koltiva.view.Staff.RegisterStaff.WinRegisterStaffForm-Form-btnSave').setVisible(false);
                }

                //load formnya
                formNya.getForm().load({
                    url: m_api + '/basic_staff/register_staff_fill_form',
                    method: 'GET',
                    params: {
                        RegID: thisObj.viewVar.RegID
                    },
                    success: function(form, action) {
                        var r = Ext.decode(action.response.responseText);
                        console.log(r);

                        //Role (begin) ============================================================
                        switch (r.data.ObjType) {
                            case 'agent':
                                //untuk handle combo bertingkat
                                var cmb_province = Ext.data.StoreManager.lookup('Koltiva.store.ComboGeneral.ComboProvince');
                                var cmb_distrct = Ext.data.StoreManager.lookup('Koltiva.store.ComboGeneral.ComboDistrict');

                                cmb_province.load({
                                    callback: function(records, operation, success){
                                        if (success == true) {
                                            Ext.getCmp('Koltiva.view.Staff.RegisterStaff.WinRegisterStaffForm-Form-Province').setValue(r.data.ProvinceID);

                                            cmb_distrct.load({
                                                params: {
                                                    ProvinceID: r.data.ProvinceID
                                                },
                                                callback: function(records, operation, success){
                                                    if(success == true){
                                                        Ext.getCmp('Koltiva.view.Staff.RegisterStaff.WinRegisterStaffForm-Form-District').setValue(r.data.DistrictID);
                                                        Ext.getCmp('Koltiva.view.Staff.RegisterStaff.WinRegisterStaffForm-Form-ObjID').setValue(r.data.ObjID);
                                                    }
                                                }
                                            });

                                        }
                                    }
                                });

                            break;
                        }
                        //Role (end) ============================================================

                        //user groups ============================================================ (begin)
                        Ext.getCmp('Koltiva.view.Staff.RegisterStaff.WinRegisterStaffForm-Form-UserGroupIDs').setValue(r.data.groups);
                        set_selected_groups();
                        Ext.getCmp('Koltiva.view.Staff.RegisterStaff.WinRegisterStaffForm-Form-GroupIDDefa').setValue(r.data.GroupIDDefa);
                        //user groups ============================================================ (end)

                        //acess area ============================================================ (begin)
                        Ext.getCmp('Koltiva.view.Staff.RegisterStaff.WinRegisterStaffForm-Form-AccessAreas').setValue(r.data.access_area);
                        //acess area ============================================================ (end)
                    },
                    failure: function(form, action) {
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
        }
    }
});
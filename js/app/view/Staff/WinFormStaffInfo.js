/*
* @Author: nikolius
* @Date:   2017-08-22 12:16:27
* @Last Modified by:   nikolius
* @Last Modified time: 2017-09-07 13:53:06
*/

/*
    Param2 yg diperlukan ketika load View ini
    1. opsiDisplay
    2. ObjType
    3. ObjID
    4. StaffID
    5. PersonID
    6. UserID
*/

// Define Variabel2 / Object2 / Fungsi yg diperlukan oleh view ini (begin)
function checkImageExists(imageUrl, callBack) {
    var imageData = new Image();
    imageData.onload = function() {
        callBack(true);
    };
    imageData.onerror = function() {
        callBack(false);
    };
    imageData.src = imageUrl;
}
// Define Variabel2 / Object2 / Fungsi yg diperlukan oleh view ini (end)

Ext.define('Koltiva.view.Staff.WinFormStaffInfo' ,{
    extend: 'Ext.window.Window',
    id: 'Koltiva.view.Staff.WinFormStaffInfo',
    title: lang('Staff Form'),
    closable: true,
    modal: true,
    closeAction: 'destroy',
    width: '70%',
    height: '85%',
    overflowY: 'auto',
    viewVar: false,
    setViewVar: function(value){
        this.viewVar = value;
    },
    initComponent: function() {
        var thisObj = this;

        //declare store2nya =================================================================================================== (begin)
        thisObj.cmb_gender = Ext.create('Ext.data.Store', {
            fields: ['id', 'name'],
            data: [{
                "id": "m",
                "name": lang("Laki-laki")
            }, {
                "id": "f",
                "name": lang("Perempuan")
            }]
        });

        thisObj.cmb_status = Ext.create('Ext.data.Store', {
            fields: ['id', 'label'],
            data: [{
                "id": "active",
                "label": "Active"
            }, {
                "id": "inactive",
                "label": "Inactive"
            },{
                "id": "nullified",
                "label": "Nullified"
            }]
        });

        thisObj.cmb_marital_status = Ext.create('Ext.data.Store', {
            fields: ['id', 'name'],
            data: [{
                "id": "2",
                "name": lang("Single")
            }, {
                "id": "1",
                "name": lang("Menikah")
            },{
                "id": "3",
                "name": lang("Janda/Duda")
            }]
        });

        thisObj.cmb_propinsi = Ext.create('Ext.data.Store', {
            extend: 'Ext.data.Model',
            fields: ['id', 'label'],
            autoLoad: true,
            proxy: {
                type: 'ajax',
                url: m_api + '/basic_staff/propinsi',
                reader: {
                    type: 'json',
                    root: 'data'
                }
            }
        });

        thisObj.cmb_kabupaten = Ext.create('Ext.data.Store', {
            extend: 'Ext.data.Model',
            fields: ['id', 'label'],
            autoLoad: false,
            proxy: {
                type: 'ajax',
                url: m_api + '/farmer/Kabupatens',
                reader: {
                    type: 'json',
                    root: 'data'
                }
            },
            listeners: {
                'beforeload': function(store, options) {
                    store.proxy.extraParams.prov = Ext.getCmp('ProvinceID').getValue();
                }
            }
        });

        thisObj.cmb_farmer_district = Ext.create('Ext.data.Store', {
            extend: 'Ext.data.Model',
            fields: ['id', 'label'],
            autoLoad: false,
            proxy: {
                type: 'ajax',
                url: m_api + '/farmer/Kabupatens',
                reader: {
                    type: 'json',
                    root: 'data'
                }
            },
            listeners: {
                'beforeload': function(store, options) {
                    store.proxy.extraParams.prov = Ext.getCmp('FarmerProvinceID').getValue();
                }
            }
        });

        thisObj.cmb_role_district = Ext.create('Ext.data.Store', {
            extend: 'Ext.data.Model',
            fields: ['id', 'label'],
            autoLoad: false,
            proxy: {
                type: 'ajax',
                url: m_api + '/farmer/Kabupatens',
                reader: {
                    type: 'json',
                    root: 'data'
                }
            },
            listeners: {
                'beforeload': function(store, options) {
                    store.proxy.extraParams.prov = Ext.getCmp('RoleProvinceID').getValue();
                }
            }
        });

        thisObj.cmb_workarea = Ext.create('Ext.data.Store', {
            extend: 'Ext.data.Model',
            fields: ['id', 'label'],
            autoLoad: false,
            proxy: {
                type: 'ajax',
                url: m_api + '/basic_staff/workarea',
                reader: {
                    type: 'json',
                    root: 'data'
                }
            },
            listeners: {
                'beforeload': function(store, options) {
                    store.proxy.extraParams.prov = Ext.getCmp('WorkAreaProvinceID').getValue();
                }
            }
        });

        thisObj.cmb_kecamatan = Ext.create('Ext.data.Store', {
            extend: 'Ext.data.Model',
            fields: ['id', 'label'],
            autoLoad: false,
            pageSize: 10,
            proxy: {
                type: 'ajax',
                url: m_api + '/basic_staff/kecamatan',
                reader: {
                    type: 'json',
                    root: 'data'
                }
            },
            listeners: {
                'beforeload': function(store, options) {
                    store.proxy.extraParams.DistrictID = Ext.getCmp('DistrictID').getValue();
                }
            }
        });

        thisObj.cmb_desa = Ext.create('Ext.data.Store', {
            extend: 'Ext.data.Model',
            fields: ['id', 'label'],
            autoLoad: false,
            pageSize: 10,
            proxy: {
                type: 'ajax',
                url: m_api + '/basic_staff/desa',
                reader: {
                    type: 'json',
                    root: 'data'
                }
            },
            listeners: {
                'beforeload': function(store, options) {
                    store.proxy.extraParams.SubDistrictID = Ext.getCmp('SubDistrictID').getValue();
                }
            }
        });

        thisObj.cmb_farmer_cpg = Ext.create('Ext.data.Store', {
            extend: 'Ext.data.Model',
            fields: ['id', 'label'],
            autoLoad: false,
            pageSize: 10,
            proxy: {
                type: 'ajax',
                url: m_api + '/basic_staff/cpg',
                reader: {
                    type: 'json',
                    root: 'data'
                }
            },
            listeners: {
                'beforeload': function(store, options) {
                    store.proxy.extraParams.DistrictID = Ext.getCmp('FarmerDistrictID').getValue();
                }
            }
        });

        thisObj.cmb_farmer = Ext.create('Ext.data.Store', {
            extend: 'Ext.data.Model',
            fields: ['id', 'label'],
            autoLoad: false,
            pageSize: 10,
            proxy: {
                type: 'ajax',
                url: m_api + '/basic_staff/farmer',
                reader: {
                    type: 'json',
                    root: 'data'
                }
            },
            listeners: {
                'beforeload': function(store, options) {
                    store.proxy.extraParams.CPGid = Ext.getCmp('FarmerCpgID').getValue();
                }
            }
        });

        thisObj.cmb_role_obj_id = Ext.create('Ext.data.Store', {
            extend: 'Ext.data.Model',
            fields: ['id', 'label'],
            autoLoad: false,
            pageSize: 10,
            proxy: {
                type: 'ajax',
                url: m_api + '/basic_staff/objectid',
                reader: {
                    type: 'json',
                    root: 'data'
                }
            },
            listeners: {
                'beforeload': function(store, options) {
                    store.proxy.extraParams.ObjType = Ext.getCmp('ObjType').getValue();
                    store.proxy.extraParams.DistrictID = Ext.getCmp('RoleDistrictID').getValue();
                }
            }
        });

        thisObj.cmb_position = Ext.create('Ext.data.Store', {
            extend: 'Ext.data.Model',
            fields: ['id', 'label'],
            autoLoad: false,
            pageSize: 10,
            proxy: {
                type: 'ajax',
                url: m_api + '/basic_staff/position',
                reader: {
                    type: 'json',
                    root: 'data'
                }
            },
            listeners: {
                'beforeload': function(store, options) {
                    store.proxy.extraParams.ObjType = Ext.getCmp('ObjType').getValue();
                }
            }
        });

        thisObj.store_view_group_user = Ext.create('Ext.data.Store', {
            extend: 'Ext.data.Model',
            fields: ['nama', 'deksripsi', 'unit'],
            autoLoad: false,
            proxy: {
                type: 'ajax',
                url: m_api + '/basic_staff/user_info_group_user',
                reader: {
                    type: 'json',
                    root: 'data'
                }
            },
            listeners: {
                'beforeload': function(store, options) {
                    store.proxy.extraParams.userInfoUserId = Ext.getCmp('userInfoUserId').getValue();
                }
            }
        });

        thisObj.store_view_district_access = Ext.create('Ext.data.Store', {
            extend: 'Ext.data.Model',
            fields: ['provinsi', 'kabupaten'],
            autoLoad: false,
            proxy: {
                type: 'ajax',
                url: m_api + '/basic_staff/user_info_district_access',
                reader: {
                    type: 'json',
                    root: 'data'
                }
            },
            listeners: {
                'beforeload': function(store, options) {
                    store.proxy.extraParams.userInfoUserId = Ext.getCmp('userInfoUserId').getValue();
                }
            }
        });

        thisObj.cmb_objtype = Ext.create('Ext.data.Store', {
            extend: 'Ext.data.Model',
            fields: ['id', 'label'],
            autoLoad: true,
            proxy: {
                type: 'ajax',
                url: m_api + '/basic_staff/objtype_list',
                reader: {
                    type: 'json',
                    root: 'data'
                }
            }
        });
        //declare store2nya =================================================================================================== (end)

        //items ---------------------------------------------------------------------------------------------------------------------------- (begin)
        thisObj.items = [{
            xtype: 'form',
            id: 'Koltiva.view.Staff.WinFormStaffInfo-Form',
            fileUpload: true,
            padding:'5 25 5 8',
            items:[{
                xtype:'panel',
                title: 'A. '+lang('Basic Data'),
                frame:true,
                bodyStyle:{"background-color":"#F0F0F0"},
                style:'background-color:#F0F0F0;margin-bottom:13px;',
                padding:2,
                //height:570,
                items:[{
                    layout: 'column',
                    border: false,
                    items: [{
                        columnWidth: 0.45,
                        padding: 4,
                        layout:'form',
                        items:[{
                            xtype: 'hiddenfield',
                            id: 'StaffID',
                            name: 'StaffID'
                        },{
                            xtype: 'hiddenfield',
                            id: 'userInfoUserId',
                            name: 'userInfoUserId'
                        },{
                            xtype: 'textfield',
                            fieldLabel: lang('Full Name'),
                            labelWidth: 150,
                            allowBlank: false,
                            id: 'PersonNm',
                            name: 'PersonNm'
                        },{
                            xtype: 'textfield',
                            fieldLabel: lang('SSN'),
                            allowBlank: false,
                            id: 'Ssn',
                            name: 'Ssn'
                        },{
                            xtype: 'datefield',
                            fieldLabel: lang('Birth Date'),
                            allowBlank: false,
                            id: 'BirthDate',
                            name: 'BirthDate',
                            format: 'Y-m-d'
                        },{
                            xtype: 'textfield',
                            fieldLabel: lang('Birth Place'),
                            id: 'BirthPlace',
                            name: 'BirthPlace'
                        },{
                            fieldLabel: lang('Gender'),
                            allowBlank: false,
                            msgTarget: 'side',
                            xtype: 'radiogroup',
                            width: '100%',
                            items:[{
                                boxLabel: lang('Laki-laki'),
                                id: 'Gender1',
                                name: 'Gender',
                                inputValue: 'm',
                                listeners:{
                                    change: function(){
                                        return false;
                                    }
                                }
                            },{
                                boxLabel: lang('Perempuan'),
                                id: 'Gender2',
                                name: 'Gender',
                                inputValue: 'f',
                                listeners:{
                                    change: function(){
                                        return false;
                                    }
                                }
                            }]
                        },{
                            xtype: 'combobox',
                            fieldLabel: lang('Marital Status'),
                            allowBlank: false,
                            store: thisObj.cmb_marital_status,
                            queryMode: 'local',
                            displayField: 'name',
                            valueField: 'id',
                            id: 'MaritalSt',
                            name: 'MaritalSt'
                        },{
                            fieldLabel: lang('Nationality'),
                            xtype: 'radiogroup',
                            allowBlank: false,
                            msgTarget: 'side',
                            width: '100%',
                            columns: 2,
                            items: [{
                                boxLabel: lang('Local'),
                                name: 'NationalityNm',
                                inputValue: 'local',
                                id: 'NationalityNm_local',
                                listeners:{
                                    change: function(){
                                        return false;
                                    }
                                }
                            }, {
                                boxLabel: lang('Expat'),
                                name: 'NationalityNm',
                                inputValue: 'expat',
                                id: 'NationalityNm_expat',
                                listeners:{
                                    change: function(){
                                        return false;
                                    }
                                }
                            }]
                        }]
                    },{
                        columnWidth: 0.05,
                        padding: 4,
                        layout:'form',
                        items:[{}]
                    },{
                        columnWidth: 0.5,
                        padding: 4,
                        layout:'form',
                        items:[{
                            layout:'column',
                            border:false,
                            style:'margin-bottom:5px;margin-right:-5px;',
                            items:[{
                                columnWidth: 1,
                                border: false,
                                layout:{
                                    type:'hbox',
                                    pack:'end'
                                },
                                items:[{
                                    xtype: 'image',
                                    id: 'iphoto',
                                    width: '100px',
                                    height:'100px',
                                    src: m_api_base_url + '/images/Photo/no-user.jpg'
                                },{
                                    xtype: 'textfield',
                                    id: 'Photo_old',
                                    name: 'Photo_old',
                                    inputType: 'hidden'
                                }]
                            }]
                        },{
                            xtype: 'fileuploadfield',
                            fieldLabel: lang('Photo'),
                            labelWidth: 130,
                            id: 'Photo',
                            name: 'Photo',
                            buttonText: 'Browse',
                            hidden: true
                            /*
                            listeners: {
                                'change': function (fb, v) {
                                    var form = Ext.getCmp('winFormDataStaff').getForm();
                                    form.submit({
                                        url: m_api + '/basic_staff/image_staff',
                                        clientValidation: false,
                                        waitMsg: 'Sending Photo...',
                                        success: function (fp, o) {
                                            Ext.getCmp('iphoto').setSrc(m_api_base_url + '/images/staff/' + o.result.file);
                                            Ext.getCmp('Photo_old').setValue(o.result.file);
                                        }
                                    });
                                }
                            }
                            */
                        },{
                            xtype: 'textfield',
                            fieldLabel: lang('Address'),
                            labelWidth: 130,
                            allowBlank: false,
                            id: 'Address',
                            name: 'Address'
                        },{
                            xtype: 'combobox',
                            fieldLabel: lang('Province'),
                            store: thisObj.cmb_propinsi,
                            displayField: 'label',
                            valueField: 'id',
                            id: 'ProvinceID',
                            name: 'ProvinceID',
                            queryMode: 'local',
                            listeners: {
                                change: function(cb, nv, ov) {
                                    thisObj.cmb_kabupaten.load();
                                    //Ext.getCmp('DistrictID').setValue('');
                                }
                            }
                        },{
                            xtype: 'combobox',
                            fieldLabel: lang('District'),
                            store: thisObj.cmb_kabupaten,
                            displayField: 'label',
                            valueField: 'id',
                            id: 'DistrictID',
                            name: 'DistrictID',
                            queryMode: 'local',
                            listeners: {
                                change: function(cb, nv, ov) {
                                    thisObj.cmb_kecamatan.load();
                                    //Ext.getCmp('SubDistrictID').setValue('');
                                }
                            }
                        },{
                            xtype: 'combobox',
                            fieldLabel: lang('Sub District'),
                            store: thisObj.cmb_kecamatan,
                            displayField: 'label',
                            valueField: 'id',
                            id: 'SubDistrictID',
                            name: 'SubDistrictID',
                            queryMode: 'local',
                            listeners: {
                                change: function(cb, nv, ov) {
                                    thisObj.cmb_desa.load();
                                    //Ext.getCmp('VillageID').setValue('');
                                }
                            }
                        },{
                            xtype: 'combobox',
                            fieldLabel: lang('Village'),
                            store: thisObj.cmb_desa,
                            displayField: 'label',
                            valueField: 'id',
                            id: 'VillageID',
                            name: 'VillageID',
                            queryMode: 'local'
                        }]
                    }]
                }]
            },{
                xtype:'panel',
                title: 'B. '+lang('Staff Data'),
                frame:true,
                bodyStyle:{"background-color":"#F0F0F0"},
                style:'background-color:#F0F0F0;',
                padding:2,
                items:[{
                    layout: 'column',
                    border: false,
                    items: [{
                        columnWidth: 0.45,
                        padding: 4,
                        layout:'form',
                        items:[{
                            xtype: 'textfield',
                            fieldLabel: lang('Staff Number'),
                            labelWidth: 150,
                            allowBlank: false,
                            id: 'StaffRegisteredNumber',
                            name: 'StaffRegisteredNumber'
                        },{
                            xtype: 'textfield',
                            fieldLabel: lang('Old Staff ID'),
                            labelWidth: 150,
                            id: 'OldStaffID',
                            name: 'OldStaffID'
                        },{
                            fieldLabel: lang('Is Farmer'),
                            xtype: 'radiogroup',
                            allowBlank: false,
                            width: '100%',
                            columns: 2,
                            items: [{
                                boxLabel: lang('Yes'),
                                name: 'isFarmer',
                                inputValue: '1',
                                id: 'isFarmer1',
                                listeners:{
                                    change: function(){
                                        /*Ext.getCmp('Koltiva.view.Staff.WinFormStaffInfo').scrollBy(0,500,false);*/
                                        return false;
                                    }
                                }
                            }, {
                                boxLabel: lang('No'),
                                name: 'isFarmer',
                                inputValue: '2',
                                id: 'isFarmer2',
                                listeners:{
                                    change: function(){
                                        /*
                                        if(this.checked == true){
                                            Ext.getCmp('FarmerProvinceID').hide();
                                            Ext.getCmp('FarmerDistrictID').hide();
                                            Ext.getCmp('FarmerCpgID').hide();
                                            Ext.getCmp('FarmerID').hide();
                                        }else{
                                            Ext.getCmp('FarmerProvinceID').show();
                                            Ext.getCmp('FarmerDistrictID').show();
                                            Ext.getCmp('FarmerCpgID').show();
                                            Ext.getCmp('FarmerID').show();
                                        }

                                        Ext.getCmp('Koltiva.view.Staff.WinFormStaffInfo').scrollBy(0,500,false);
                                        */
                                        return false;
                                    }
                                }
                            }]
                        },{
                            xtype: 'combobox',
                            fieldLabel: lang('Farmer Province'),
                            labelWidth: 150,
                            hidden:true,
                            store: thisObj.cmb_propinsi,
                            displayField: 'label',
                            valueField: 'id',
                            id: 'FarmerProvinceID',
                            name: 'FarmerProvinceID',
                            queryMode: 'local',
                            listeners: {
                                change: function(cb, nv, ov) {
                                    thisObj.cmb_farmer_district.load();
                                    //Ext.getCmp('FarmerDistrictID').setValue('');
                                }
                            }
                        },{
                            xtype: 'combobox',
                            fieldLabel: lang('Farmer District'),
                            hidden:true,
                            labelWidth: 150,
                            store: thisObj.cmb_farmer_district,
                            displayField: 'label',
                            valueField: 'id',
                            id: 'FarmerDistrictID',
                            name: 'FarmerDistrictID',
                            queryMode: 'local',
                            listeners: {
                                change: function(cb, nv, ov) {
                                    thisObj.cmb_farmer_cpg.load();
                                    //Ext.getCmp('FarmerCpgID').setValue('');
                                }
                            }
                        },{
                            xtype: 'combobox',
                            fieldLabel: lang('Farmer CPG'),
                            hidden:true,
                            labelWidth: 150,
                            store: thisObj.cmb_farmer_cpg,
                            displayField: 'label',
                            valueField: 'id',
                            id: 'FarmerCpgID',
                            name: 'FarmerCpgID',
                            queryMode: 'local',
                            listeners: {
                                change: function(cb, nv, ov) {
                                    thisObj.cmb_farmer.load();
                                    //Ext.getCmp('FarmerID').setValue('');
                                }
                            }
                        },{
                            xtype: 'combobox',
                            fieldLabel: lang('Farmer'),
                            hidden:true,
                            labelWidth: 150,
                            store: thisObj.cmb_farmer,
                            anyMatch: true,
                            typeAhead: true,
                            displayField: 'label',
                            valueField: 'id',
                            id: 'FarmerID',
                            name: 'FarmerID',
                            queryMode: 'local',
                            listeners: {
                                change: function(cb, nv, ov) {
                                }
                            }
                        },{
                            xtype: 'combobox',
                            fieldLabel: lang('Work Area Province'),
                            labelWidth: 150,
                            store: thisObj.cmb_propinsi,
                            displayField: 'label',
                            valueField: 'id',
                            id: 'WorkAreaProvinceID',
                            name: 'WorkAreaProvinceID',
                            queryMode: 'local',
                            listeners: {
                                change: function(cb, nv, ov) {
                                    thisObj.cmb_workarea.load();
                                    //Ext.getCmp('WorkAreaID').setValue('');
                                }
                            }
                        },{
                            xtype: 'combobox',
                            fieldLabel: lang('Work Area'),
                            labelWidth: 150,
                            store: thisObj.cmb_workarea,
                            allowBlank: false,
                            displayField: 'label',
                            valueField: 'id',
                            id: 'WorkAreaID',
                            name: 'WorkAreaID',
                            queryMode: 'local'
                        }]
                    },{
                        columnWidth: 0.05,
                        padding: 4,
                        layout:'form',
                        items:[{}]
                    },{
                        columnWidth: 0.5,
                        padding: 4,
                        layout:'form',
                        items:[{
                            xtype: 'textfield',
                            fieldLabel: lang('Private Cell Phone'),
                            labelWidth: 150,
                            id: 'PrivateCellPhone',
                            name: 'PrivateCellPhone'
                        },{
                            xtype: 'textfield',
                            fieldLabel: lang('Official Cell Phone'),
                            labelWidth: 150,
                            allowBlank: false,
                            id: 'OfficialCellPhone',
                            name: 'OfficialCellPhone'
                        },{
                            xtype: 'textfield',
                            fieldLabel: lang('Private Email'),
                            labelWidth: 150,
                            vtype: 'email',
                            id: 'PrivateEmail',
                            name: 'PrivateEmail',
                        },{
                            xtype: 'textfield',
                            fieldLabel: lang('Official Email'),
                            labelWidth: 150,
                            vtype: 'email',
                            allowBlank: false,
                            id: 'OfficialEmail',
                            name: 'OfficialEmail'
                        },{
                            xtype: 'textfield',
                            fieldLabel: lang('Cc Email'),
                            labelWidth: 150,
                            id: 'CcEmail',
                            name: 'CcEmail',
                            emptyText: 'Comma separated value'
                        },{
                            fieldLabel: lang('Work Period'),
                            xtype: 'radiogroup',
                            allowBlank: false,
                            msgTarget: 'side',
                            width: '100%',
                            columns: 2,
                            items: [{
                                boxLabel: lang('Full-time'),
                                name: 'WorkPeriod',
                                inputValue: '1',
                                id: 'WorkPeriod1',
                                listeners:{
                                    change: function(){
                                        return false;
                                    }
                                }
                            }, {
                                boxLabel: lang('Part-time'),
                                name: 'WorkPeriod',
                                inputValue: '2',
                                id: 'WorkPeriod2',
                                listeners:{
                                    change: function(){
                                        return false;
                                    }
                                }
                            }]
                        },{
                            xtype: 'combobox',
                            fieldLabel: lang('Status'),
                            allowBlank: false,
                            store: thisObj.cmb_status,
                            queryMode: 'local',
                            displayField: 'label',
                            valueField: 'id',
                            id: 'StatusCode',
                            name: 'StatusCode'
                        }]
                    }]
                },{
                    layout: 'column',
                    border: false,
                    items: [{
                        columnWidth: 0.6,
                        padding: 4,
                        layout:'form',
                        items:[{
                            xtype: 'combobox',
                            fieldLabel: lang('Role'),
                            labelWidth: 150,
                            allowBlank: false,
                            store: thisObj.cmb_objtype,
                            queryMode: 'local',
                            displayField: 'label',
                            valueField: 'id',
                            id: 'ObjType',
                            name: 'ObjType',
                            listeners: {
                                change: function(cb, nv, ov) {
                                    //cmb_workarea.load();
                                    //Ext.getCmp('WorkAreaID').setValue('');
                                    switch (nv) {
                                        case 'extension':
                                        case 'private':
                                        case 'program':
                                        case 'service':
                                        case 'mill':
                                        case 'refinery':
                                            Ext.getCmp('RoleProvinceID').setDisabled(true);
                                            Ext.getCmp('RoleDistrictID').setDisabled(true);

                                            thisObj.cmb_role_obj_id.load();
                                        break;

                                        case 'bank':
                                        case 'cooperative':
                                        case 'farmergroup':
                                        case 'sce':
                                        case 'trader':
                                        case 'warehouse':
                                        case 'agent':
                                            Ext.getCmp('RoleProvinceID').setDisabled(false);
                                            Ext.getCmp('RoleDistrictID').setDisabled(false);

                                            //Ext.getCmp('RoleProvinceID').setValue('');
                                        break;
                                    }

                                    thisObj.cmb_position.load();
                                    //Ext.getCmp('ObjID').setValue('');
                                    //Ext.getCmp('PositionID').setValue('');
                                }
                            }
                        },{
                            xtype: 'combobox',
                            fieldLabel: lang('Province'),
                            store: thisObj.cmb_propinsi,
                            displayField: 'label',
                            valueField: 'id',
                            id: 'RoleProvinceID',
                            name: 'RoleProvinceID',
                            queryMode: 'local',
                            listeners: {
                                change: function(cb, nv, ov) {
                                    thisObj.cmb_role_district.load();
                                    //Ext.getCmp('RoleDistrictID').setValue('');
                                }
                            }
                        },{
                            xtype: 'combobox',
                            fieldLabel: lang('District'),
                            store: thisObj.cmb_role_district,
                            displayField: 'label',
                            valueField: 'id',
                            id: 'RoleDistrictID',
                            name: 'RoleDistrictID',
                            queryMode: 'local',
                            listeners: {
                                change: function(cb, nv, ov) {
                                    thisObj.cmb_role_obj_id.load();
                                    //Ext.getCmp('ObjID').setValue('');
                                }
                            }
                        },{
                            xtype: 'combobox',
                            fieldLabel: lang('Object ID'),
                            store: thisObj.cmb_role_obj_id,
                            allowBlank:false,
                            anyMatch: true,
                            typeAhead: true,
                            displayField: 'label',
                            valueField: 'id',
                            id: 'ObjID',
                            name: 'ObjID',
                            queryMode: 'local',
                            listeners: {
                                change: function(cb, nv, ov) {
                                }
                            }
                        },{
                            xtype: 'combobox',
                            fieldLabel: lang('Position'),
                            store: thisObj.cmb_position,
                            displayField: 'label',
                            valueField: 'id',
                            id: 'PositionID',
                            name: 'PositionID',
                            queryMode: 'local',
                            allowBlank: false
                        }]
                    },{
                        columnWidth: 0.4,
                        padding: 4,
                        layout:'form',
                        items:[{}]
                    }]
                }]
            },{
                xtype:'panel',
                title: 'C. '+lang('User Information'),
                frame:true,
                //hidden: !viewOnly,
                bodyStyle:{"background-color":"#F0F0F0"},
                style:'background-color:#F0F0F0;margin-top:15px;',
                padding:2,
                items:[{
                    layout: 'column',
                    border: false,
                    items: [{
                        columnWidth: 1,
                        padding: 4,
                        layout:'form',
                        items:[{
                            xtype: 'textfield',
                            fieldLabel: lang('Username'),
                            labelWidth: 150,
                            readOnly: true,
                            id: 'userInfoUsername',
                            name: 'userInfoUsername'
                        },{
                            xtype: 'textfield',
                            fieldLabel: lang('User Status'),
                            labelWidth: 150,
                            readOnly: true,
                            id: 'userInfoStatus',
                            name: 'userInfoStatus'
                        },{
                            xtype: 'textfield',
                            fieldLabel: lang('Default Group'),
                            labelWidth: 150,
                            readOnly: true,
                            id: 'userInfoDefaultGroup',
                            name: 'userInfoDefaultGroup'
                        },{
                            layout: 'fit',
                            items:[{
                                xtype:'grid',
                                store: thisObj.store_view_group_user,
                                width: '98%',
                                id: 'grid_view_group_user',
                                style: 'border:1px solid #CCC;margin-top:12px;',
                                loadMask: true,
                                title:lang('Group Access'),
                                selType: 'rowmodel',
                                columns: [{
                                    text: lang('No'),
                                    xtype: 'rownumberer',
                                    width: '5%'
                                },{
                                    text: lang('Name'),
                                    width: '30%',
                                    dataIndex: 'nama'
                                },{
                                    text: lang('Description'),
                                    width: '40%',
                                    dataIndex: 'deksripsi'
                                },{
                                    text: lang('Unit'),
                                    width: '24%',
                                    dataIndex: 'unit'
                                }]
                            }]
                        },{
                            layout: 'fit',
                            items:[{
                                xtype:'grid',
                                store: thisObj.store_view_district_access,
                                width: '98%',
                                id: 'grid_view_district_user',
                                style: 'border:1px solid #CCC;margin-top:12px;',
                                loadMask: true,
                                title:lang('District Access'),
                                selType: 'rowmodel',
                                columns: [{
                                    text: lang('No'),
                                    xtype: 'rownumberer',
                                    width: '5%'
                                },{
                                    text: lang('Province'),
                                    width: '45%',
                                    dataIndex: 'provinsi'
                                },{
                                    text: lang('District'),
                                    width: '49%',
                                    dataIndex: 'kabupaten'
                                }]
                            }]
                        }]
                    }]
                }]
            }]
        }]
        //items ---------------------------------------------------------------------------------------------------------------------------- (end)

        //buttons -------------------------------------------------------------- (begin)
        thisObj.buttons = [{
            text: lang('Close'),
            margin: '5px',
            scale: 'large',
            ui: 's-button',
            cls: 's-grey',
            handler: function() {
                thisObj.close();
            }
        }];
        //buttons -------------------------------------------------------------- (end)

        this.callParent(arguments);
    },
    listeners: {
        afterRender: function(){
            var thisObj = this;

            //form reset
            var formNya = Ext.getCmp('Koltiva.view.Staff.WinFormStaffInfo-Form');
            formNya.getForm().reset();

            if(thisObj.viewVar.opsiDisplay == 'view'){
                //load formnya
                formNya.getForm().load({
                    url: m_api + '/basic_staff/form_staff',
                    method: 'GET',
                    params: {
                        StaffID: thisObj.viewVar.StaffID
                    },
                    success: function(form, action) {
                        var r = Ext.decode(action.response.responseText);

                        //photo===========================================
                        if(r.data.Photo_old != ""){
                            var fotoUser = m_api_base_url + '/images/staff/' + r.data.Photo_old;
                            checkImageExists(fotoUser, function(existsImage) {
                                if (existsImage == true) {
                                    Ext.getCmp('iphoto').setSrc(fotoUser);
                                } else {
                                    Ext.getCmp('iphoto').setSrc(m_api_base_url + '/images/Photo/no-user.jpg');
                                }
                            });
                        }

                        //region=================================================
                        setTimeout(function() {
                            if(r.data.ProvinceID == "") Ext.getCmp('ProvinceID').setValue(null);
                            if(r.data.DistrictID == "0") Ext.getCmp('DistrictID').setValue(null);
                            if(r.data.SubDistrictID == "") Ext.getCmp('SubDistrictID').setValue(null);
                            if(r.data.VillageID == "") Ext.getCmp('VillageID').setValue(null);
                        }, 1000);

                        //Is Farmer=============================================
                        if(r.data.isFarmerValue == "1"){ //farmer
                            Ext.getCmp('isFarmer1').setValue(true);
                            Ext.getCmp('FarmerProvinceID').show();
                            Ext.getCmp('FarmerDistrictID').show();
                            Ext.getCmp('FarmerCpgID').show();
                            Ext.getCmp('FarmerID').show();
                        } else if(r.data.isFarmerValue == "2"){ //bukan farmer
                            Ext.getCmp('isFarmer2').setValue(true);
                        }

                        //Role (begin) ============================================================
                        Ext.getCmp('ObjType').setValue(r.data.ObjTypeValue);
                        switch (r.data.ObjTypeValue) {
                            case 'bank':
                            case 'cooperative':
                            case 'farmergroup':
                            case 'sce':
                            case 'trader':
                            case 'warehouse':
                            case 'agent':
                                Ext.getCmp('RoleProvinceID').setValue(r.data.RoleProvinceID);
                                Ext.getCmp('RoleDistrictID').setValue(r.data.RoleDistrictID);
                            break;
                        }
                        Ext.getCmp('ObjID').setValue(r.data.ObjIDValue);
                        //Role (end) ============================================================

                        //focus keatas
                        Ext.getCmp('Koltiva.view.Staff.WinFormStaffInfo').scrollBy(0,-500,false);

                        Ext.getCmp('ObjType').setReadOnly(true);
                        Ext.getCmp('PositionID').setReadOnly(true);

                        //tab user information ================================= (begin)
                        thisObj.store_view_group_user.load();
                        thisObj.store_view_district_access.load();
                        //tab user information ================================= (end)
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
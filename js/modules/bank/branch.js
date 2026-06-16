if(Ext.getCmp('winGridStaff')) Ext.getCmp('winGridStaff').destroy();
if(Ext.getCmp('winFormStaff')) Ext.getCmp('winFormStaff').destroy();
Ext.onReady(function () {
    Ext.tip.QuickTipManager.init();
    var store = Ext.create('Ext.data.Store', {
        extend: 'Ext.data.Model',
        fields: ['id', 'bank', 'name', 'Province', 'District', 'SubDistrict',],
        autoLoad: true,
        pageSize: 50,
        proxy: {
            type: 'ajax',
            url: m_crud + 's',
            reader: {
                type: 'json',
                root: 'data',
                totalProperty: 'count'
            }
        }
    });
    var store_branch_staff = Ext.create('Ext.data.Store', {
        extend: 'Ext.data.Model',
        fields: ['StaffID','BranchID','StaffName','Phone','Email','StaffBirth','StaffGender','Photo','IdentityNumber','VillageID','Address',],
        autoLoad: false,
        // pageSize: 50,
        proxy: {
            type: 'ajax',
            url: m_staff+'s',
            reader: {
                type: 'json',
                root: 'data',
                totalProperty: 'total'
            }
        }
    });
    var mc_bank = Ext.create('Ext.data.Store', {
        extend: 'Ext.data.Model',
        fields: ['id', 'name'],
        autoLoad: true,
        proxy: {
            type: 'ajax',
            url: m_bank,
            reader: {
                type: 'json',
                root: 'data'
            }
        }
    });
    var mc_province = Ext.create('Ext.data.Store', {
        extend: 'Ext.data.Model',
        fields: ['id', 'name'],
        autoLoad: true,
        proxy: {
            type: 'ajax',
            url: m_province,
            reader: {
                type: 'json',
                root: 'data'
            }
        }
    });
    var mc_district = Ext.create('Ext.data.Store', {
        extend: 'Ext.data.Model',
        fields: ['id', 'name'],
        autoLoad: false,
        proxy: {
            type: 'ajax',
            url: m_district,
            reader: {
                type: 'json',
                root: 'data'
            }
        }
    });
    var mc_subdistrict = Ext.create('Ext.data.Store', {
        extend: 'Ext.data.Model',
        fields: ['id', 'name'],
        autoLoad: false,
        proxy: {
            type: 'ajax',
            url: m_subdistrict,
            reader: {
                type: 'json',
                root: 'data'
            }
        }
    });
    var mc_village = Ext.create('Ext.data.Store', {
        extend: 'Ext.data.Model',
        fields: ['id', 'name'],
        autoLoad: false,
        proxy: {
            type: 'ajax',
            url: m_village,
            reader: {
                type: 'json',
                root: 'data'
            }
        }
    });
    var mc_group = Ext.create('Ext.data.Store', {
        extend: 'Ext.data.Model',
        fields: ['id', 'name'],
        autoLoad: true,
        proxy: {
            type: 'ajax',
            url: m_group,
            reader: {
                type: 'json',
                root: 'data'
            }
        }
    });
    var mc_gender = Ext.create('Ext.data.Store', {
        fields: ['id','label'],
        data: [{
            "id":1,
            "label": lang("Male")
        }, {
            "id":2,
            "label": lang("Female")
        }]
    });

    function displayFormWindow() {
        if (!win.isVisible()) {
            resetForm();
            win.show();
            //Ext.getCmp('name').focus(true,true);
        } else {
            win.hide(this, function () {
            });
            win.toFront();
        }
    }

    function displayFormStaff() {
        var sm = Ext.getCmp('grid').getSelectionModel().getSelection()[0];
        Ext.getCmp('DataFormStaff').getForm().reset();
        Ext.getCmp('BranchID').setValue(sm.get('id'));
        if (!winFormStaff.isVisible()) {
            winFormStaff.show();
        } else {
            winFormStaff.hide();
            winFormStaff.toFront();
        }
    }

    function displayGridStaff() {
        // if (!Ext.getCmp('id').getValue()) {
        //     Ext.MessageBox.alert('Warning', 'Silahkan simpan dulu data cabang');
        //     return false;
        // }
        var sm = Ext.getCmp('grid').getSelectionModel().getSelection()[0];
        if (sm) {
            store_branch_staff.load({
                params: {
                    BranchID: sm.get('id')
                }
            });
            if (!winGridStaff.isVisible()) {
                winGridStaff.show();
            } else {
                winGridStaff.hide();
                winGridStaff.toFront();
            }
        } else {
            Ext.MessageBox.alert('Warning', lang('Silahkan pilih cabang'));
        }
    }

    function resetForm() {
        Ext.getCmp('id').setValue('');
        Ext.getCmp('bankid').setValue('');
        Ext.getCmp('name').setValue('');
        Ext.getCmp('provinceid').setValue('');
        Ext.getCmp('districtid').setValue('');
        Ext.getCmp('subdistrictid').setValue('');
        Ext.getCmp('villageid').setValue('');
        Ext.getCmp('address').setValue('');
        Ext.getCmp('phone').setValue('');
        Ext.getCmp('latitude').setValue('');
        Ext.getCmp('longitude').setValue('');
        Ext.getCmp('desc').setValue('');
    }
    function setFormStaff () {
        displayFormStaff();
        var sm = Ext.getCmp('grid_branch_staff').getSelectionModel().getSelection()[0];
        Ext.Ajax.request({
            url: m_staff,
            method: 'GET',
            params: {StaffID: sm.get('StaffID')},
            success: function (fp, o) {
                var r = Ext.decode(fp.responseText);
                Ext.getCmp('StaffID').setValue(sm.get('StaffID'));
                Ext.getCmp('BranchID').setValue(r.BranchID);
                Ext.getCmp('StaffName').setValue(r.StaffName);
                Ext.getCmp('Phone').setValue(r.Phone);
                Ext.getCmp('Email').setValue(r.Email);
                Ext.getCmp('StaffBirth').setValue(r.StaffBirth);
                Ext.getCmp('StaffGender').setValue(r.StaffGender);
                Ext.getCmp('Photo').setValue(r.Photo);
                Ext.getCmp('IdentityNumber').setValue(r.IdentityNumber);
                Ext.getCmp('ProvinceID').setValue(r.ProvinceID);
                setTimeout(function(){
                    Ext.getCmp('DistrictID').setValue(r.DistrictID);
                    setTimeout(function(){
                        Ext.getCmp('SubDistrictID').setValue(r.SubDistrictID);
                        setTimeout(function(){
                            Ext.getCmp('VillageID').setValue(r.VillageID);
                        },100);
                    },100);
                },100);
                Ext.getCmp('Address').setValue(r.Address);
                Ext.getCmp('UserId').setValue(r.UserId);
                Ext.getCmp('Username').setValue(r.UserName);
                Ext.getCmp('GroupId').setValue(r.GroupId);
            }
        });
        // body...
    }

    var DataForm = Ext.create('Ext.form.Panel', {
        frame: false,
        height: 600,
        width: 400,
        bodyPadding: 5,
        id: 'dataForm',
        fieldDefaults: {
            labelAlign: 'left',
            labelWidth: 100,
            anchor: '100%'
        },
        items: [
            {
                xtype: 'textfield',
                id: 'id',
                name: 'id',
                inputType: 'hidden'
            },
            {
                id: 'bankid',
                name: 'bankid',
                xtype: 'combobox',
                fieldLabel: 'Bank',
                store: mc_bank,
                displayField: 'name',
                valueField: 'id',
                queryMode: 'local',
                allowBlank: false
            },
            {
                xtype: 'textfield',
                fieldLabel: 'Branch Name',
                allowBlank: false,
                id: 'name',
                name: 'name'
            },
            {
                id: 'provinceid',
                name: 'provinceid',
                xtype: 'combobox',
                fieldLabel: 'Province',
                store: mc_province,
                displayField: 'name',
                valueField: 'id',
                queryMode: 'local',
                listeners: {
                    change: function(cb, nv, ov) {
                        mc_district.load({
                            params: {
                                provinceid: Ext.getCmp('provinceid').getValue()
                            }
                        });
                    }
                }
            },
            {
                id: 'districtid',
                name: 'districtid',
                xtype: 'combobox',
                fieldLabel: 'District',
                store: mc_district,
                displayField: 'name',
                valueField: 'id',
                queryMode: 'local',
                listeners: {
                    change: function(cb, nv, ov) {
                        mc_subdistrict.load({
                            params: {
                                districtid: Ext.getCmp('districtid').getValue()
                            }
                        });
                    }
                }
            },
            {
                id: 'subdistrictid',
                name: 'subdistrictid',
                xtype: 'combobox',
                fieldLabel: 'Sub District',
                store: mc_subdistrict,
                displayField: 'name',
                valueField: 'id',
                queryMode: 'local',
                listeners: {
                    change: function(cb, nv, ov) {
                        mc_village.load({
                            params: {
                                subdistrictid: Ext.getCmp('subdistrictid').getValue()
                            }
                        });
                    }
                }
            },
            {
                id: 'villageid',
                name: 'villageid',
                xtype: 'combobox',
                fieldLabel: 'Village',
                store: mc_village,
                displayField: 'name',
                valueField: 'id',
                queryMode: 'local'
            },
            {
                xtype: 'textfield',
                fieldLabel: 'Address',
                id: 'address',
                name: 'address'
            },
            {
                xtype: 'textfield',
                fieldLabel: 'Phone',
                id: 'phone',
                name: 'phone'
            },
            {
                xtype: 'textfield',
                fieldLabel: 'Latitude',
                id: 'latitude',
                name: 'latitude'
            },
            {
                xtype: 'textfield',
                fieldLabel: 'Longitude',
                id: 'longitude',
                name: 'longitude'
            },
            {
                xtype: 'textareafield',
                fieldLabel: 'Description',
                id: 'desc',
                name: 'desc'
            },
        ],
        buttons: [{
            id: 'saveButton',
            text: 'Save',
            margin: '5px',
            scale: 'large',
            ui: 's-button',
            cls: 's-blue',
            handler: function () {
                var form = this.up('form').getForm();
                var methode;
                if (Ext.getCmp('id').getValue() == '') methode = 'POST'; else methode = 'PUT';
                form.submit({
                    url: m_crud,
                    method: methode,
                    waitMsg: 'Sending data...',
                    success: function (fp, o) {
                        Ext.MessageBox.alert('Success', 'Data saved.');
                    }
                });
                win.hide(this, function () {
                    store.load();
                });
            }
        // }, {
        //     text: 'Staffs',
        //     margin: '5px',
        //     scale: 'large',
        //     ui: 's-button',
        //     cls: 's-blue',
        //     disabled: false,
        //     handler: function () {
        //         displayGridStaff();
        //     }
        }, {
            text: 'Close',
            margin: '5px',
            scale: 'large',
            ui: 's-button',
            cls: 's-grey',
            disabled: false,
            handler: function () {
                win.hide();
            }
        }]
    });
    var DataFormStaff = Ext.create('Ext.form.Panel', {
        frame: false,
        height: 650,
        width: 400,
        bodyPadding: 5,
        id: 'DataFormStaff',
        fieldDefaults: {
            labelAlign: 'left',
            labelWidth: 100,
            anchor: '100%'
        },
        items: [
            {
                xtype: 'textfield',
                id: 'StaffID',
                name: 'StaffID',
                inputType: 'hidden'
            },
            {
                xtype: 'textfield',
                id: 'BranchID',
                name: 'BranchID',
                inputType: 'hidden'
            },
            {
                xtype: 'textfield',
                fieldLabel: 'Staff Name',
                allowBlank: false,
                id: 'StaffName',
                name: 'StaffName'
            },
            {
                xtype: 'hiddenfield',
                id: 'Photo',
                name: 'Photo'
            },
            {
                xtype: 'filefield',
                name: 'PhotoUpload',
                fieldLabel: 'Photo',
                msgTarget: 'side',
                allowBlank: true,
                anchor: '100%',
                buttonText: lang('Select Photo...')
            },
            {
                xtype: 'textfield',
                fieldLabel: 'Phone',
                id: 'Phone',
                name: 'Phone'
            },
            {
                xtype: 'textfield',
                fieldLabel: 'Email',
                id: 'Email',
                name: 'Email',
                validator: function(value) {
                    var ereg = /^\w+([-+.']\w+)*@\w+([-.]\w+)*\.\w+([-.]\w+)*$/;
                    if(ereg.test(value)) {
                        return true;
                    } else {
                        return lang('Invalid email address');
                    }
                }
            },
            {
                xtype: 'datefield',
                fieldLabel: lang('Birthdate'),
                id: 'StaffBirth',
                name: 'StaffBirth',
                format: 'Y-m-d'
            },
            {
                id: 'StaffGender',
                name: 'StaffGender',
                xtype: 'combobox',
                fieldLabel: lang('Gender'),
                store: mc_gender,
                displayField: 'label',
                valueField: 'id',
                queryMode: 'local',
            },
            {
                xtype: 'textfield',
                fieldLabel: 'IdentityNumber',
                id: 'IdentityNumber',
                name: 'IdentityNumber'
            },
            {
                id: 'ProvinceID',
                name: 'ProvinceID',
                xtype: 'combobox',
                fieldLabel: 'Province',
                store: mc_province,
                displayField: 'name',
                valueField: 'id',
                queryMode: 'local',
                listeners: {
                    change: function(cb, nv, ov) {
                        mc_district.load({
                            params: {
                                provinceid: Ext.getCmp('ProvinceID').getValue()
                            }
                        });
                    }
                }
            },
            {
                id: 'DistrictID',
                name: 'DistrictID',
                xtype: 'combobox',
                fieldLabel: 'District',
                store: mc_district,
                displayField: 'name',
                valueField: 'id',
                queryMode: 'local',
                listeners: {
                    change: function(cb, nv, ov) {
                        mc_subdistrict.load({
                            params: {
                                districtid: Ext.getCmp('DistrictID').getValue()
                            }
                        });
                    }
                }
            },
            {
                id: 'SubDistrictID',
                name: 'SubDistrictID',
                xtype: 'combobox',
                fieldLabel: 'Sub District',
                store: mc_subdistrict,
                displayField: 'name',
                valueField: 'id',
                queryMode: 'local',
                listeners: {
                    change: function(cb, nv, ov) {
                        mc_village.load({
                            params: {
                                subdistrictid: Ext.getCmp('SubDistrictID').getValue()
                            }
                        });
                    }
                }
            },
            {
                id: 'VillageID',
                name: 'VillageID',
                xtype: 'combobox',
                fieldLabel: 'Village',
                store: mc_village,
                displayField: 'name',
                valueField: 'id',
                queryMode: 'local',
                allowBlank: false
            },
            {
                xtype: 'textfield',
                fieldLabel: 'Address',
                id: 'Address',
                name: 'Address'
            },
            {
                xtype: 'hiddenfield',
                id: 'UserId',
                name: 'UserId'
            },
            {
                xtype: 'textfield',
                fieldLabel: 'Username',
                id: 'Username',
                name: 'Username',
                allowBlank: false,
                listeners : {
                    'change': function(textfield,newValue,oldValue) {
                        Ext.Ajax.request({
                            url: m_user_check,
                            method: 'GET',
                            params: {
                                id: Ext.getCmp('UserId').getValue(),
                                username: newValue
                            },
                            scope: textfield,
                            success: function(response){
                                data = $.parseJSON(response.responseText);
                                if (data.success){
                                    this.clearInvalid();
                                    this.textValid = true;
                                } else {
                                    this.markInvalid('Username not available');
                                    this.textValid = false;
                                }
                            }
                        });
                    }
                }
            },
            {
                xtype: 'textfield',
                inputType: 'password',
                fieldLabel: 'Password',
                id: 'Password',
                name: 'Password',
                allowBlank: false,
            },
            {
                xtype: 'textfield',
                inputType: 'password',
                fieldLabel: 'Retype Password',
                id: 'RePassword',
                name: 'RePassword',
                allowBlank: false,
                validator: function(value) {
                    if (Ext.getCmp('Password').getValue() == value) {
                        return true;
                    } else {
                        return lang('Password tidak cocok');
                    }
                }
            },
            {
                id: 'GroupId',
                name: 'GroupId',
                xtype: 'combobox',
                fieldLabel: 'Role',
                store: mc_group,
                displayField: 'name',
                valueField: 'id',
                queryMode: 'local',
            },
        ],
        buttons: [{
            // id: 'saveButton',
            text: 'Save',
            margin: '5px',
            scale: 'large',
            ui: 's-button',
            cls: 's-blue',
            handler: function () {
                var form = this.up('form').getForm();
                if (form.isValid()) {
                    var methode;
                    if (Ext.getCmp('StaffID').getValue() === '') methode = 'POST'; else methode = 'PUT';
                    form.submit({
                        url: m_staff,
                        method: methode,
                        waitMsg: 'Sending data...',
                        success: function (fp, o) {
                            Ext.MessageBox.alert('Success', 'Data saved.');
                        }
                    });
                    var sm = Ext.getCmp('grid').getSelectionModel().getSelection()[0];
                    winFormStaff.hide(this, function () {
                        store_branch_staff.load({
                            params: {
                                BranchID: sm.get('id')
                            }
                        });
                    });
                } else {
                    Ext.MessageBox.alert('Warning', lang('Silahkan isi form dengan data yang benar'));
                }
            }
        }, {
            text: 'Close',
            margin: '5px',
            scale: 'large',
            ui: 's-button',
            cls: 's-grey',
            disabled: false,
            handler: function () {
                winFormStaff.hide();
            }
        }]
    });
    var DataGridStaff = Ext.create('Ext.form.Panel', {
        frame: false,
        height: 200,
        width: 300,
        bodyPadding: 0,
        id: 'DataGridStaff',
        items: [{
            xtype: 'gridpanel',
            id: 'grid_branch_staff',
            store: store_branch_staff,
            width: '100%',
            columns: [{
                text: lang('Staff Name'),
                dataIndex: 'StaffName',
                flex: 1,
            }, {
                text: lang('Gender'),
                dataIndex: 'StaffGender',
                flex: 1,
                renderer: function(value, metaData, record, row, col, store, gridView){
                    return value == 1 ? lang('Laki-laki') : lang('Perempuan');
                }
            }, {
                text: lang('Phone'),
                dataIndex: 'Phone',
                flex: 2,
            }, {
                text: lang('Email'),
                dataIndex: 'Email',
                flex: 2,
            }, ],
            listeners: {
                itemdblclick: function (dv, record, item, index, e) {
                    //setFormStaff();
                }
            },
            dockedItems: [{
                xtype: 'toolbar',
                items: [
                {
                    icon: varjs.config.base_url + 'images/icons/new/add.png', cls:'Sfr_BtnGridGreen', overCls:'Sfr_BtnGridGreen-Hover',
                    text: 'Add',
                    scope: this,
                    handler: displayFormStaff,
                    hidden:true,
                    cls: m_act_add
                },
                {
                    icon: varjs.config.base_url + 'images/icons/new/update.png',
                    text: 'Update',
                    scope: this,
                    hidden:true,
                    handler: function() {
                        setFormStaff();
                    },
                    cls: m_act_update
                }, {
                    itemId: 'remove',
                    icon: varjs.config.base_url + 'images/icons/new/delete.png',
                    cls: m_act_delete,
                    hidden:true,
                    text: lang('Hapus'),
                    scope: this,
                    handler: function () {
                        var smb = Ext.getCmp('grid_branch_staff').getSelectionModel().getSelection()[0];
                        Ext.MessageBox.confirm('Message', lang('Apakah anda mau menghapus data ini?'), function (btn) {
                            if (btn == 'yes') {
                                Ext.Ajax.request({
                                    waitMsg: lang('Please Wait'),
                                    url: m_staff,
                                    method: 'DELETE',
                                    params: {StaffID: smb.raw.StaffID},
                                    success: function (response, opts) {
                                        var obj = Ext.decode(response.responseText);
                                        switch (obj.success) {
                                            case true:
                                                store_branch_staff.load();
                                                break;
                                            default:
                                                Ext.MessageBox.alert('Warning', obj.message);
                                                break;
                                        }
                                    },
                                    failure: function (response, opts) {
                                        var obj = Ext.decode(response.responseText);
                                        Ext.MessageBox.alert('error', lang('Could not connect to the database. Retry later'));
                                    }
                                });
                            }
                        });
                    }
                }
                ]
            }
            ]
        }]
    })
    var win = Ext.create('widget.window', {
        title: 'Branch',
        frame: false,
        closable: true,
        id: 'win',
        modal: true,
        closeAction: 'show',
        width: 430,
        minWidth: 370,
        height: 510,
        layout: 'fit',
        items: [DataForm]
    });
    var winFormStaff = Ext.create('widget.window', {
        title: 'Branch',
        frame: false,
        closable: true,
        id: 'winFormStaff',
        modal: true,
        closeAction: 'show',
        width: 430,
        minWidth: 370,
        height: 620,
        layout: 'fit',
        items: [DataFormStaff]
    });
    var winGridStaff = Ext.create('widget.window', {
        title: 'Branch Staffs',
        frame: false,
        closable: true,
        id: 'winGridStaff',
        modal: true,
        closeAction: 'show',
        width: 500,
        // minWidth: 800,
        height: 300,
        layout: 'fit',
        items: [DataGridStaff]
    });

    function submitOnEnter(field, event) {
        if (event.getKey() == event.ENTER) {
            store.load({
                params: {
                    key: Ext.getCmp('key').getValue()
                }
            });
        }
    }

    var grid = Ext.create('Ext.grid.Panel', {
        store: store,
        width: '100%',
        id: 'grid',
        minHeight: 250,
        //title: 'User List',
        style: 'border:1px solid #CCC;',
        renderTo: 'ext-content',
        loadMask: true,
        selType: 'rowmodel',
        listeners: {
            itemdblclick: function (dv, record, item, index, e) {
                displayFormWindow();
                var sm = record;
                Ext.Ajax.request({
                    url: m_crud,
                    method: 'GET',
                    params: {id: sm.get('id')},
                    success: function (fp, o) {
                        var r = Ext.decode(fp.responseText);
                        Ext.getCmp('id').setValue(sm.get('id'));
                        Ext.getCmp('bankid').setValue(r.bankid);
                        Ext.getCmp('name').setValue(r.name);
                        Ext.getCmp('provinceid').setValue(r.provinceid);
                        Ext.getCmp('districtid').setValue(r.districtid);
                        Ext.getCmp('subdistrictid').setValue(r.subdistrictid);
                        Ext.getCmp('villageid').setValue(r.villageid);
                        Ext.getCmp('address').setValue(r.address);
                        Ext.getCmp('phone').setValue(r.phone);
                        Ext.getCmp('latitude').setValue(r.latitude);
                        Ext.getCmp('longitude').setValue(r.longitude);
                        Ext.getCmp('desc').setValue(r.desc);
                    }
                });
            }
        },
        dockedItems: [{
            xtype: 'pagingtoolbar',
            store: store,   // same store GridPanel is using
            dock: 'bottom',
            displayInfo: true
        }, {
            xtype: 'toolbar',
            items: [
                {
                    icon: varjs.config.base_url + 'images/icons/new/add.png', cls:'Sfr_BtnGridGreen', overCls:'Sfr_BtnGridGreen-Hover',
                    text: 'Add',
                    scope: this,
                    handler: displayFormWindow,
                    cls: m_act_add
                },
                {
                    icon: varjs.config.base_url + 'images/icons/new/update.png',
                    text: 'Update',
                    scope: this,
                    handler: function() {
                        displayFormWindow();
                        var sm = Ext.getCmp('grid').getSelectionModel().getSelection()[0];
                        Ext.Ajax.request({
                            url: m_crud,
                            method: 'GET',
                            params: {id: sm.get('id')},
                            success: function (fp, o) {
                                var r = Ext.decode(fp.responseText);
                                Ext.getCmp('id').setValue(sm.get('id'));
                                Ext.getCmp('bankid').setValue(r.bankid);
                                Ext.getCmp('name').setValue(r.name);
                                Ext.getCmp('provinceid').setValue(r.provinceid);
                                Ext.getCmp('districtid').setValue(r.districtid);
                                Ext.getCmp('subdistrictid').setValue(r.subdistrictid);
                                Ext.getCmp('villageid').setValue(r.villageid);
                                Ext.getCmp('address').setValue(r.address);
                                Ext.getCmp('phone').setValue(r.phone);
                                Ext.getCmp('latitude').setValue(r.latitude);
                                Ext.getCmp('longitude').setValue(r.longitude);
                                Ext.getCmp('desc').setValue(r.desc);
                            }
                        });
                    },
                    cls: m_act_update
                }, {
                    itemId: 'remove',
                    icon: varjs.config.base_url + 'images/icons/new/delete.png',
                    cls: m_act_delete,
                    text: lang('Hapus'),
                    scope: this,
                    handler: function () {
                        var smb = Ext.getCmp('grid').getSelectionModel().getSelection()[0];
                        Ext.MessageBox.confirm('Message', 'Apakah anda mau menghapus data ini ?', function (btn) {
                            if (btn == 'yes') {
                                Ext.Ajax.request({
                                    waitMsg: 'Please Wait',
                                    url: m_crud,
                                    method: 'DELETE',
                                    params: {id: smb.raw.id},
                                    success: function (response, opts) {
                                        var obj = Ext.decode(response.responseText);
                                        switch (obj.success) {
                                            case true:
                                                store.load();
                                                break;
                                            default:
                                                Ext.MessageBox.alert('Warning', obj.message);
                                                break;
                                        }
                                    },
                                    failure: function (response, opts) {
                                        var obj = Ext.decode(response.responseText);
                                        Ext.MessageBox.alert('error', 'Could not connect to the database. Retry later');
                                    }
                                });
                            }
                        });
                    }
                }, {
                    icon: varjs.config.base_url + 'images/icons/silk/group.png',
                    text: 'Staffs',
                    scope: this,
                    handler: displayGridStaff,
                }, {
                    xtype: 'textfield',
                    name: 'key', baseCls:'Sfr_TxtfieldSearchGrid',
                    id: 'key',
                    listeners: {
                        specialkey: submitOnEnter
                    }
                }, {
                    xtype: 'button',
                    margin: '0px 0px 0px 6px',
                    text: 'Search',
                    handler: function () {
                        store.load({
                            params: {
                                key: Ext.getCmp('key').getValue()
                            }
                        });
                    }
                }]
        }],
        columns: [
            {
                text: 'ID',
                dataIndex: 'id',
                hidden: true
            },
            {
                text: lang('No'),
                xtype: 'rownumberer',
                width: '5%'
            },
            {
                text: lang('Bank Name'),
                width: '20%',
                dataIndex: 'bank'
            },
            {
                text: lang('Brach Name'),
                width: '25%',
                dataIndex: 'name'
            },
            {
                text: lang('Province'),
                width: '25%',
                dataIndex: 'Province'
            },
            {
                text: lang('District'),
                width: '25%',
                dataIndex: 'District'
            },
            {
                text: lang('SubDistrict'),
                width: '25%',
                dataIndex: 'SubDistrict'
            },
        ]
    });
});

Ext.onReady(function() {
    Ext.tip.QuickTipManager.init();
    var store = Ext.create('Ext.data.Store', {
        extend: 'Ext.data.Model',
        fields: ['WarehouseID', 'WarehouseName', 'Address', 'District'],
        autoLoad: true,
        pageSize: 50,
        proxy: {
            type: 'ajax',
            url: m_crud + 'datawarehouse/' + m_type,
            extraParams: {prov: m_param,kab:m_district,kec:m_SubDistrictID},
            reader: {
                type: 'json',
                root: 'data',
                totalProperty: 'total'
            }
        }
    });
    var mc_Provinsi = Ext.create('Ext.data.Store', {
        extend: 'Ext.data.Model',
        fields: ['id', 'label'],
        autoLoad: true,
        pageSize: 10,
        proxy: {
            type: 'ajax',
            url: m_Provinsi,
            reader: {
                type: 'json',
                root: 'data'
            }
        }
    });

    var mc_Kabupaten = Ext.create('Ext.data.Store', {
        extend: 'Ext.data.Model',
        fields: ['id', 'label'],
        autoLoad: true,
        pageSize: 10,
        proxy: {
            type: 'ajax',
            url: m_Kabupaten,
            extraParams: {prov: m_param},
            reader: {
                type: 'json',
                root: 'data'
            }
        }
    });
    // mc_Kabupaten.on('load', function(st) {
    //     Ext.getCmp('sKabupaten').setValue(Ext.getCmp('sKabupaten').store.getAt(0).get('label'))
    //     store.load({
    //         params: {
    //             prov: m_paramval,
    //             kab: Ext.getCmp('sKabupaten').store.getAt(0).get('label')
    //         }
    //     });
    // });

    var mc_Kecamatan = Ext.create('Ext.data.Store', {
        extend: 'Ext.data.Model',
        fields: ['id', 'label'],
        autoLoad: true,
        pageSize: 10,
        proxy: {
            type: 'ajax',
            url: m_Kecamatan,
            reader: {
                type: 'json',
                root: 'data'
            }
        }
    });
    var mc_Desa = Ext.create('Ext.data.Store', {
        extend: 'Ext.data.Model',
        fields: ['label', 'id'],
        autoLoad: true,
        pageSize: 10,
        proxy: {
            type: 'ajax',
            url: m_Desa,
            reader: {
                type: 'json',
                root: 'data'
            }
        }
    });

    var store_partner = Ext.create('Ext.data.Store', {
        extend: 'Ext.data.Model',
        fields: ['PartnerID', 'PartnerName'],
        autoLoad: true,
        pageSize: 10,
        proxy: {
            type: 'ajax',
            url: m_partner,
            reader: {
                type: 'json',
                root: 'data'
            }
        }
    });

    function displayFormWindow() {
        if (!win.isVisible()) {
            DataForm.getForm().reset();
            win.center();
            win.show();
        } else {
            win.hide(this, function() {
            });
            win.toFront();
        }
    }
    function hideSave() {
        Ext.getCmp('saveButton').hide();
        if (Ext.getCmp('WarehouseID').getValue() === '' && m_act_add) {
            Ext.getCmp('saveButton').show();
        }
        if (Ext.getCmp('WarehouseID').getValue() !== '' && m_act_update) {
            Ext.getCmp('saveButton').show();
        }
    }
    //staff
    Ext.define('staff.Model', {
        extend: 'Ext.data.Model',
        fields: ['StaffID', 'UserId', 'WarehouseID', 'StaffName', 'Phone', 'Email', 'StaffBirth', 'StaffGender', 'Photo', 'IdentityNumber', 'VillageID', 'Education', 'FamilyMembers', 'Address', 'Position'],
    });
    var store_staff = Ext.create('Ext.data.Store', {
        model: 'staff.Model',
        autoLoad: false,
        pageSize: 10,
        proxy: {
            type: 'ajax',
            url: m_staff,
            reader: {
                type: 'json',
                root: 'data',
                totalProperty: 'total'
            }
        }
    });
    var RowEditing = Ext.create('Ext.grid.plugin.RowEditing', {
        id: 'RowEditing',
        clicksToMoveEditor: 0,
        autoCancel: false,
        errorSummary: false,
        clicksToEdit: 2
    });
    var cposition = Ext.create('Ext.data.Store', {
        fields: ['label'],
        data: [
            {"label": "Staff"},
            {"label": "Coordinator"}
        ]
    });
    var cfarmer = Ext.create('Ext.data.Store', {
        fields: ['label'],
        data: [
            {"label": "Farmer"},
            {"label": "Non Farmer"}
        ]
    });
    var ckelamin = Ext.create('Ext.data.Store', {
        fields: ['id', 'label'],
        data: [
            {"id": "1", "label": lang("Laki-laki")},
            {"id": "2", "label": lang("Perempuan")}
        ]
    });
    var ceducation = Ext.create('Ext.data.Store', {
        fields: ['id', 'label'],
        data: [
            {"id": "1", "label": lang("Belum pernah sekolah")},
            {"id": "2", "label": lang("Tidak tamat SD")},
            {"id": "3", "label": lang("Tamat SD, tidak melanjutkan")},
            {"id": "4", "label": lang("Tamat SMP")},
            {"id": "5", "label": lang("Tamat SMA/SMK")},
            {"id": "6", "label": lang("Tamat perguruan tinggi")}
        ]
    });
    //end staff

    Ext.define("Post", {
        extend: 'Ext.data.Model',
        proxy: {
            type: 'ajax',
            url: m_staff + '_farmers',
            reader: {
                type: 'json',
                root: 'data',
                totalProperty: 'total'
            }
        },
        fields: [
            {name: 'id', mapping: 'id'},
            {name: 'name', mapping: 'name'},
            {name: 'handphone', mapping: 'hp'},
            {name: 'email', mapping: 'email'},
            {name: 'birtday', mapping: 'birthday', type: 'date', dateFormat: 'timestamp'},
            {name: 'kelamin', mapping: 'kelamin'}
        ]
    });

    var ds = Ext.create('Ext.data.Store', {
        pageSize: 10,
        model: 'Post'
    });
    function gs_edit() {
        if (Ext.getCmp('farmer').getValue() == 'Farmer') {
            Ext.getCmp('lid').setVisible(true)
            Ext.getCmp('lnama').setVisible(false)
            Ext.getCmp('lhp').setReadOnly(true)
            Ext.getCmp('lemail').setReadOnly(true)
            Ext.getCmp('StaffGender').setReadOnly(true)
            Ext.getCmp('lbirthday').setReadOnly(true)
        } else {
            Ext.getCmp('lid').setVisible(false)
            Ext.getCmp('lnama').setVisible(true)
            Ext.getCmp('lhp').setReadOnly(false)
            Ext.getCmp('lemail').setReadOnly(false)
            Ext.getCmp('StaffGender').setReadOnly(false)
            Ext.getCmp('lbirthday').setReadOnly(false)
        }
    }
    var store_nursey_penjualan = Ext.create('Ext.data.Store', {
        model: 'penjualan.Model',
        autoLoad: false,
        proxy: {
            type: 'ajax',
            url: m_store_nursey_penjualans,
            reader: {
                type: 'json',
                root: 'data'
            }
        }
    });
    var mc_pembeli = Ext.create('Ext.data.Store', {
        fields: ['label'],
        data: [{'label': lang('Anggota Kelompok')}, {'label': lang('Petani Lain')}, {'label': lang('Traders')}, {'label': lang('Lainnya')}, {'label': lang('Pemerintah')}],
    });
    var nRowEditing = Ext.create('Ext.grid.plugin.RowEditing', {
        id: 'nRowEditing',
        clicksToMoveEditor: 0,
        autoCancel: false,
        errorSummary: false,
        clicksToEdit: 2
    });
    var DataForm = Ext.create('Ext.form.Panel', {
        frame: false,
        height: 500,
        autoScroll: true,
        width: 1010,
        bodyPadding: 5,
        //fileUpload: true,
        //enctype:'multipart/form-data',
        id: 'dataForm',
        fieldDefaults: {
            labelAlign: 'left',
            labelWidth: 140,
            anchor: '100%'
        },
        items: [{
                xtype: 'tabpanel',
                flex: 1,
                margin: 2,
                activeTab: 0,
                plain: true,
                items: [{
                        xtype: 'panel',
                        autoScroll: true,
                        title: lang('Data Umum'),
                        padding: 5,
                        style: 'border:2px solid #ADD2ED',
                        items: [{
                                xtype: 'textfield',
                                id: 'WarehouseID',
                                name: 'WarehouseID',
                                hidden: true
                            }, {
                                layout: 'column',
                                items: [{
                                        columnWidth: 0.5,
                                        items: [{
                                                xtype: 'fieldset',
                                                title: lang('Data Perusahaan'),
                                                items: [
                                                    {
                                                        id: 'PartnerID',
                                                        name: 'PartnerID',
                                                        xtype: 'combo',
                                                        fieldLabel: lang('Partner'),
                                                        store: store_partner,
                                                        displayField: 'PartnerName',
                                                        valueField: 'PartnerID',
                                                        queryMode: 'local'
                                                    },{
                                                        xtype: 'textfield',
                                                        id: 'WarehouseName',
                                                        name: 'WarehouseName',
                                                        fieldLabel: lang('Nama Warehouse')
                                                    }, {
                                                        xtype: 'textfield',
                                                        id: 'Alias',
                                                        name: 'Alias',
                                                        fieldLabel: lang('Nama Alias')
                                                    }, {
                                                        xtype: 'textfield',
                                                        id: 'Phone',
                                                        name: 'Phone',
                                                        fieldLabel: lang('No Telepon')
                                                    }, {
                                                        xtype: 'radiogroup',
                                                        fieldLabel: lang('Status Hukum Perusahaan'),
                                                        columns: 3,
                                                        items: [{
                                                                xtype: 'radiofield',
                                                                boxLabel: lang('UD'),
                                                                id: 'Status',
                                                                name: 'Status',
                                                                inputValue: 'UD'
                                                            }, {
                                                                xtype: 'radiofield',
                                                                boxLabel: lang('Firma'),
                                                                id: 'Status2',
                                                                name: 'Status',
                                                                inputValue: 'Firma'
                                                            }, {
                                                                xtype: 'radiofield',
                                                                boxLabel: lang('CV'),
                                                                id: 'Status3',
                                                                name: 'Status',
                                                                inputValue: 'CV'
                                                            }, {
                                                                xtype: 'radiofield',
                                                                boxLabel: lang('Koperasi'),
                                                                id: 'Status4',
                                                                name: 'Status',
                                                                inputValue: 'Koperasi'
                                                            }, {
                                                                xtype: 'radiofield',
                                                                boxLabel: lang('PT'),
                                                                id: 'Status5',
                                                                name: 'Status',
                                                                inputValue: 'PT'
                                                            }, {
                                                                xtype: 'radiofield',
                                                                boxLabel: lang('Tidak berbadan hukum'),
                                                                id: 'Status6',
                                                                name: 'Status',
                                                                inputValue: 'Tidak Berbadan Hukum'
                                                            }]
                                                    }, {
                                                        xtype: 'textfield',
                                                        id: 'Year',
                                                        name: 'Year',
                                                        fieldLabel: lang('Tahun Berdiri')
                                                    }, {
                                                        xtype: 'fieldset',
                                                        title: lang('Status Karyawan'),
                                                        items: [{
                                                                xtype: 'label',
                                                                text: lang('Karyawan Tetap')
                                                            }, {
                                                                xtype: 'textfield',
                                                                id: 'PermanentEmployeeMale',
                                                                name: 'PermanentEmployeeMale',
                                                                fieldLabel: lang('Laki-laki')
                                                            }, {
                                                                xtype: 'textfield',
                                                                id: 'PermanentEmployeeFemale',
                                                                name: 'PermanentEmployeeFemale',
                                                                fieldLabel: lang('Perempuan')
                                                            }, {
                                                                xtype: 'label',
                                                                text: lang('Karyawan Tidak Tetap')
                                                            }, {
                                                                xtype: 'textfield',
                                                                id: 'TemporaryEmployeeMale',
                                                                name: 'TemporaryEmployeeMale',
                                                                fieldLabel: lang('Laki-laki')
                                                            }, {
                                                                xtype: 'textfield',
                                                                id: 'TemporaryEmployeeFemale',
                                                                name: 'TemporaryEmployeeFemale',
                                                                fieldLabel: lang('Perempuan')
                                                            }]
                                                    }]
                                            }]
                                    }, {
                                        columnWidth: 0.5,
                                        margin: 5,
                                        items: [{
                                                layout: 'column',
                                                hidden: true,
                                                border: true,
                                                items: [{
                                                        columnWidth: 0.6,
                                                        padding: 10,
                                                        items: [{
                                                                xtype: 'textfield',
                                                                id: 'Photo_old',
                                                                name: 'Photo_old',
                                                                inputType: 'hidden'
                                                            }, {
                                                                //xtype: 'fileuploadfield',
                                                                fieldLabel: lang('Icon'),
                                                                labelWidth: 50,
                                                                id: 'Photo',
                                                                name: 'Photo',
                                                                buttonText: 'Browse',
                                                                listeners: {
                                                                    'change': function(fb, v) {
                                                                        var form = this.up('form').getForm();
                                                                        form.submit({
                                                                            url: m_crud + 'data_image',
                                                                            waitMsg: 'Sending Photo...',
                                                                            success: function(fp, o) {
                                                                                Ext.getCmp('iphoto').setSrc(m_photo + o.result.file);
                                                                                Ext.getCmp('Photo_old').setValue(o.result.file);
                                                                            }
                                                                        });
                                                                    }
                                                                }
                                                            }]
                                                    }, {
                                                        columnWidth: 0.4,
                                                        items: [{
                                                                xtype: 'image',
                                                                id: 'iphoto',
                                                                height: '120px'
                                                            }]
                                                    }]
                                            }, {
                                                xtype: 'fieldset',
                                                title: lang('Lokasi'),
                                                items: [
                                                    {
                                                        id: 'Provinsi',
                                                        name: 'Provinsi',
                                                        xtype: 'combo',
                                                        fieldLabel: lang('Provinsi'),
                                                        store: mc_Provinsi,
                                                        displayField: 'label',
                                                        valueField: 'label',
                                                        queryMode: 'local',
                                                        disabled: 'true',
                                                        listeners: {
                                                            change: function(cb, nv, ov) {
                                                                mc_Kabupaten.load({
                                                                    params: {
                                                                        key: Ext.getCmp('Provinsi').getValue()
                                                                    }});
                                                            }
                                                        }
                                                    },
                                                    {
                                                        id: 'Kabupaten',
                                                        name: 'Kabupaten',
                                                        xtype: 'combo',
                                                        fieldLabel: lang('Kabupaten'),
                                                        store: mc_Kabupaten,
                                                        displayField: 'label',
                                                        valueField: 'label',
                                                        queryMode: 'local',
                                                        listeners: {
                                                            change: function(cb, nv, ov) {
                                                                mc_Kecamatan.load({
                                                                    params: {
                                                                        key: Ext.getCmp('Kabupaten').getValue()
                                                                    }});
                                                                Ext.getCmp('Kecamatan').enable();
                                                            }
                                                        }
                                                    }, {
                                                        id: 'Kecamatan',
                                                        name: 'Kecamatan',
                                                        xtype: 'combo',
                                                        fieldLabel: lang('Kecamatan'),
                                                        store: mc_Kecamatan,
                                                        displayField: 'label',
                                                        valueField: 'label',
                                                        queryMode: 'local',
                                                        disabled: 'true',
                                                        listeners: {
                                                            change: function(cb, nv, ov) {
                                                                mc_Desa.load({
                                                                    params: {
                                                                        key: Ext.getCmp('Kecamatan').getValue()
                                                                    }});
                                                                Ext.getCmp('Desa').enable();
                                                            }
                                                        }
                                                    }, {
                                                        id: 'Desa',
                                                        name: 'Desa',
                                                        xtype: 'combo',
                                                        fieldLabel: lang('Desa'),
                                                        store: mc_Desa,
                                                        displayField: 'label',
                                                        disabled: 'true',
                                                        valueField: 'id',
                                                        queryMode: 'local'
                                                    }, {
                                                        xtype: 'textfield',
                                                        fieldLabel: lang('Alamat'),
                                                        id: 'Address',
                                                        name: 'Address'
                                                    }, {
                                                        xtype: 'textfield',
                                                        id: 'LatSec',
                                                        name: 'LatSec',
                                                        fieldLabel: lang('Latitude(Dec)'),
                                                        readOnly: m_hakakses_lat_short
                                                    }, {
                                                        xtype: 'textfield',
                                                        id: 'LongSec',
                                                        name: 'LongSec',
                                                        fieldLabel: lang('Longitude(Dec)'),
                                                        readOnly: m_hakakses_long_short
                                                    }, {
                                                        xtype: 'textfield',
                                                        id: 'LatDeg',
                                                        name: 'LatDeg',
                                                        fieldLabel: lang('Latitude(Dec)'),
                                                        hidden: true
                                                    }, {
                                                        xtype: 'textfield',
                                                        id: 'LongDeg',
                                                        name: 'LongDeg',
                                                        fieldLabel: lang('Longitude(Dec)'),
                                                        hidden: true
                                                    }, {
                                                        xtype: 'textfield',
                                                        id: 'Elevation',
                                                        name: 'Elevation',
                                                        fieldLabel: lang('Elevation(Meter)'),
                                                        readOnly: m_hakakses_elevation
                                                    }]
                                            }]
                                    }]
                            }]
                    }, {
                        xtype: 'panel',
                        autoScroll: true,
                        id: 'panel_staff',
                        disabled: true,
                        hidden:true,
                        title: lang('Staff'),
                        padding: 5,
                        style: 'border:2px solid #ADD2ED',
                        items: [{
                                xtype: 'gridpanel',
                                id: 'grid_staff',
                                store: store_staff,
                                height: 400,
                                width: '100%',
                                loadMask: true,
                                selType: 'rowmodel',
                                dockedItems: [{
                                        xtype: 'toolbar',
                                        items: [{
                                                icon: varjs.config.base_url + 'images/icons/new/add.png', cls:'Sfr_BtnGridGreen', overCls:'Sfr_BtnGridGreen-Hover',
                                                text: lang('Add'),
                                                // cls: m_act_save,
                                                hidden: !m_act_add,
                                                hidden:true,
                                                scope: this,
                                                handler: function() {
                                                    RowEditing.cancelEdit();
                                                    var r = Ext.create('staff.Model', {
                                                        StaffID: '', UserId: '', WarehouseID: '', StaffName: '', Phone: '', Email: '',
                                                        StaffBirth: '', StaffGender: '', Photo: '',
                                                        IdentityNumber: '', VillageID: '', Education: '', FamilyMembers: '', Address: '', Position: ''
                                                    });
                                                    store_staff.insert(0, r);
                                                    RowEditing.startEdit(0, 0);
                                                }
                                            }, {
                                                icon: varjs.config.base_url + 'images/icons/new/update.png',
                                                // cls: m_act_save,
                                                hidden: !m_act_update,
                                                hidden:true,
                                                text: lang('Edit'),
                                                scope: this,
                                                handler: function() {
                                                    RowEditing.cancelEdit();
                                                    var sm = Ext.getCmp('grid_staff').getSelectionModel().getSelection();
                                                    RowEditing.startEdit(sm[0].index, 0);
                                                    gs_edit();
                                                }
                                            }, {
                                                itemId: 'remove',
                                                icon: varjs.config.base_url + 'images/icons/new/delete.png',
                                                text: lang('Hapus'),
                                                hidden: !m_act_delete,
                                                hidden:true,
                                                scope: this,
                                                handler: function() {
                                                    var smb = Ext.getCmp('grid_staff').getSelectionModel().getSelection()[0];
                                                    RowEditing.cancelEdit();
                                                    Ext.MessageBox.confirm('Message', lang('Apakah anda mau menghapus staff ini ?'), function(btn) {
                                                        if (btn == 'yes') {
                                                            Ext.Ajax.request({
                                                                waitMsg: 'Please Wait',
                                                                url: m_staff,
                                                                method: 'DELETE',
                                                                params: {
                                                                    id: smb.raw.StaffID,
                                                                    userid: smb.raw.UserId
                                                                },
                                                                success: function(response, opts) {
                                                                    var obj = Ext.decode(response.responseText);
                                                                    switch (obj.success) {
                                                                        case true:
                                                                            store_staff.load({
                                                                                params: {
                                                                                    id: Ext.getCmp('WarehouseID').getValue()
                                                                                }});
                                                                            break;
                                                                        default:
                                                                            Ext.MessageBox.alert('Warning', obj.message);
                                                                            break;
                                                                    }
                                                                },
                                                                failure: function(response, opts) {
                                                                    var obj = Ext.decode(response.responseText);
                                                                    Ext.MessageBox.alert('error', lang('Could not connect to the database. Retry later'));
                                                                }
                                                            });
                                                        }
                                                    });
                                                }
                                            }]
                                    }],
                                columns: [{
                                        text: lang('Status'),
                                        id: 'lstatus',
                                        dataIndex: 'Status',
                                        width: '10%',
                                        editor: {
                                            xtype: 'combo',
                                            store: cfarmer,
                                            id: 'farmer',
                                            queryMode: 'local',
                                            displayField: 'label',
                                            valueField: 'label',
                                            listeners: {
                                                change: function(combo, selection) {
                                                    gs_edit();
                                                }
                                            }
                                        }
                                    }, {
                                        text: lang('Nama'),
                                        id: 'lid',
                                        dataIndex: 'FarmerID',
                                        width: '35%',
                                        editor: {
                                            xtype: 'combo',
                                            store: ds,
                                            displayField: 'name',
                                            typeAhead: false,
                                            hideLabel: true,
                                            hideTrigger: true,
                                            anchor: '100%',
                                            listConfig: {
                                                loadingText: lang('Searching...'),
                                                emptyText: lang('No matching farmer found.'),
                                                // Custom rendering template for each item
                                                getInnerTpl: function() {
                                                    return '<div class="search-item">' +
                                                            '{id} - {name}' +
                                                            '{excerpt}' +
                                                            '</div>';
                                                }
                                            },
                                            pageSize: 10,
                                            // override default onSelect to do redirect
                                            listeners: {
                                                select: function(combo, selection) {
                                                    var post = selection[0];
                                                    if (post) {
                                                        Ext.getCmp('ltnama').setValue(post.get('id'))
                                                        Ext.getCmp('lhp').setValue(post.get('handphone'))
                                                        Ext.getCmp('lhp').setReadOnly(true)
                                                        Ext.getCmp('lemail').setValue(post.get('email'))
                                                        Ext.getCmp('lemail').setReadOnly(true)
                                                        Ext.getCmp('StaffGender').setValue(post.get('kelamin'))
                                                        Ext.getCmp('StaffGender').setReadOnly(true)
                                                        Ext.getCmp('lbirthday').setValue(post.get('birthday'))
                                                        Ext.getCmp('lbirthday').setReadOnly(true)
                                                    }
                                                }
                                            }
                                        }
                                    }, {
                                        text: lang('StaffID'),
                                        id: 'StaffID',
                                        dataIndex: 'StaffID',
                                        width: '35%',
                                        hidden: true
                                    }, {
                                        text: lang('Nama'),
                                        id: 'lnama',
                                        dataIndex: 'StaffName',
                                        width: '35%',
                                        hidden: true,
                                        editor: {
                                            id: 'ltnama',
                                            xtype: 'textfield'
                                        }
                                    }, {
                                        text: lang('Position'),
                                        dataIndex: 'Position',
                                        width: '10%',
                                        editor: {
                                            xtype: 'combo',
                                            store: cposition,
                                            id: 'Position',
                                            queryMode: 'local',
                                            displayField: 'label',
                                            valueField: 'label'
                                        }
                                    }, {
                                        text: lang('Handphone'),
                                        dataIndex: 'Phone',
                                        width: '10%',
                                        editor: {
                                            id: 'lhp',
                                            xtype: 'textfield'
                                        }
                                    }, {
                                        text: lang('Official Handphone'),
                                        dataIndex: 'OfficialCellphone',
                                        width: '5%',
                                        hidden: true,
                                        editor: {
                                            xtype: 'textfield'
                                        }
                                    }, {
                                        text: lang('Email'),
                                        dataIndex: 'Email',
                                        width: '10%',
                                        editor: {
                                            xtype: 'textfield',
                                            id: 'lemail'
                                        }
                                    }, {
                                        text: lang('Address'),
                                        dataIndex: 'Address',
                                        width: '15%',
                                        hidden: true,
                                        editor: {
                                            xtype: 'textfield'
                                        }
                                    }, {
                                        text: lang('Identity Number'),
                                        dataIndex: 'IdentityNumber',
                                        hidden: true,
                                        width: '10%',
                                        editor: {
                                            xtype: 'textfield'
                                        }
                                    }, {
                                        text: lang('Birthday'),
                                        dataIndex: 'StaffBirth',
                                        width: '10%',
                                        editor: {
                                            xtype: 'datefield',
                                            id: 'lbirthday',
                                            allowBlank: false,
                                            format: 'Y-m-d'
                                        }
                                    }, {
                                        text: lang('Kelamin'),
                                        dataIndex: 'StaffGender',
                                        width: '10%',
                                        editor: {
                                            xtype: 'combo',
                                            store: ckelamin,
                                            queryMode: 'local',
                                            id: 'StaffGender',
                                            allowBlank: false,
                                            displayField: 'label',
                                            valueField: 'label'
                                        }
                                    }, {
                                        text: lang('Education'),
                                        dataIndex: 'Education',
                                        hidden: true,
                                        width: '15%',
                                        editor: {
                                            xtype: 'combo',
                                            store: ceducation,
                                            queryMode: 'local',
                                            id: 'Education',
                                            displayField: 'label',
                                            valueField: 'id'
                                        }
                                    }],
                                //plugins: [RowEditing],
                                listeners: {
                                    itemdblclick: function(dv, record, item, index, e) {
                                        if (!m_act_update) {
                                            RowEditing.cancelEdit();
                                        } else {
                                            gs_edit();
                                        }
                                    },
                                    'canceledit': function(editor, e, eOpts) {
                                        store_staff.load({
                                            params: {
                                                id: Ext.getCmp('WarehouseID').getValue()
                                            }});
                                    },
                                    'edit': function(editor, e) {
                                        if (e.record.data.StaffID == '') {
                                            Ext.Ajax.request({
                                                waitMsg: 'Please wait...',
                                                url: m_staff,
                                                method: 'POST',
                                                params: {
                                                    WarehouseID: Ext.getCmp('WarehouseID').getValue(),
                                                    StaffName: e.record.data.StaffName,
                                                    Status: e.record.data.Status,
                                                    Phone: e.record.data.Phone,
                                                    Email: e.record.data.Email,
                                                    StaffBirth: e.record.data.StaffBirth,
                                                    StaffGender: e.record.data.StaffGender,
                                                    Photo: e.record.data.Photo,
                                                    IdentityNumber: e.record.data.IdentityNumber,
                                                    Education: e.record.data.Education,
                                                    Position: e.record.data.Position,
                                                    Address: e.record.data.Address
                                                },
                                                success: function(response, opts) {
                                                    var obj = Ext.decode(response.responseText);
                                                    switch (obj.success) {
                                                        case true:
                                                            Ext.MessageBox.alert('Success', obj.message);
                                                            store_staff.load({
                                                                params: {
                                                                    id: Ext.getCmp('WarehouseID').getValue()
                                                                }});
                                                            break;
                                                        default:
                                                            Ext.MessageBox.alert('Warning', obj.message);
                                                            break;
                                                    }
                                                },
                                                failure: function(response, opts) {
                                                    var obj = Ext.decode(response.responseText);
                                                    Ext.MessageBox.alert('error', lang('Could not connect to the database. Retry later'));
                                                }
                                            });
                                        } else {
                                            Ext.MessageBox.confirm('Message', lang('Update data staff ini ?'), function(btn) {
                                                if (btn == 'yes') {
                                                    Ext.Ajax.request({
                                                        waitMsg: 'Please wait...',
                                                        url: m_staff,
                                                        method: 'PUT',
                                                        params: {
                                                            WarehouseID: Ext.getCmp('WarehouseID').getValue(),
                                                            StaffName: e.record.data.StaffName,
                                                            Status: e.record.data.Status,
                                                            Phone: e.record.data.Phone,
                                                            Email: e.record.data.Email,
                                                            StaffBirth: e.record.data.StaffBirth,
                                                            StaffGender: e.record.data.StaffGender,
                                                            Photo: e.record.data.Photo,
                                                            IdentityNumber: e.record.data.IdentityNumber,
                                                            Education: e.record.data.Education,
                                                            Position: e.record.data.Position,
                                                            Address: e.record.data.Address,
                                                            StaffID: e.record.data.StaffID
                                                        },
                                                        success: function(response, opts) {
                                                            var obj = Ext.decode(response.responseText);
                                                            switch (obj.success) {
                                                                case true:
                                                                    Ext.MessageBox.alert('Success', obj.message);
                                                                    store_staff.load({
                                                                        params: {
                                                                            id: Ext.getCmp('WarehouseID').getValue()
                                                                        }});
                                                                    break;
                                                                default:
                                                                    Ext.MessageBox.alert('Warning', obj.message);
                                                                    break;
                                                            }
                                                        },
                                                        failure: function(response, opts) {
                                                            var obj = Ext.decode(response.responseText);
                                                            Ext.MessageBox.alert('error', lang('Could not connect to the database. Retry later'));
                                                        }
                                                    });
                                                }
                                            });
                                        }
                                    }
                                }
                            }]
                    }]
            }],
        buttons: [{
                id: 'saveButton',
                text: lang('Save'),
                margin: '5px',
                scale: 'large',
                ui: 's-button',
                cls: 's-blue',
                handler: function() {
                    var form = this.up('form').getForm();
                    var urle = m_crud + 'datawarehouse/' + m_type;
                    form.submit({
                        url: urle,
                        method: 'POST',
                        waitMsg: 'Sending data...',
                        success: function(fp, o) {
                            Ext.MessageBox.alert('Success', 'Data saved.');
                            store.load();
                            if (Ext.getCmp('WarehouseID').getValue() != '')
                                win.hide();
                            Ext.getCmp('WarehouseID').setValue(o.result.id);
                            Ext.getCmp('panel_staff').enable()
                        }
                    });
                }
            }, {
                text: lang('Close'),
                margin: '5px',
                scale: 'large',
                ui: 's-button',
                cls: 's-grey',
                disabled: false,
                handler: function() {
                    win.hide();
                }
            }]
    });
    if (m_farmer_staff == 'show') {
        Ext.getCmp('lstatus').show();
        Ext.getCmp('lid').show();
        Ext.getCmp('lnama').hide();
    } else {
        Ext.getCmp('lstatus').hide();
        Ext.getCmp('lid').hide();
        Ext.getCmp('lnama').show();
    }

    var win = Ext.create('widget.window', {
        title: m_title,
        id: 'win',
        closable: true,
        modal: true,
        closeAction: 'show',
        autoScroll: true,
        width: '90%',
        height: '90%',
        layout: {
            type: 'fit'
        },
        items: [DataForm]
    });
    function fset(r) {
        Ext.getCmp('panel_staff').enable()
        store_staff.load({
            params: {
                id: Ext.getCmp('WarehouseID').getValue()
            }});
        Ext.getCmp('Address').setValue(r.Address);
        Ext.getCmp('Phone').setValue(r.Phone);
        if (r.VillageID != '') {
            Ext.getCmp('Provinsi').setValue(r.Province);
            Ext.getCmp('Kabupaten').setValue(r.District);
            Ext.getCmp('Kecamatan').setValue(r.SubDistrict);
            Ext.getCmp('Desa').setValue(r.Village);
        }
        Ext.getCmp('WarehouseName').setValue(r.WarehouseName);
        if (r.Status == 'UD')
            Ext.getCmp('Status').setValue(true);
        if (r.Status == 'Firma')
            Ext.getCmp('Status2').setValue(true);
        if (r.Status == 'CV')
            Ext.getCmp('Status3').setValue(true);
        if (r.Status == 'Koperasi')
            Ext.getCmp('Status4').setValue(true);
        if (r.Status == 'PT')
            Ext.getCmp('Status5').setValue(true);
        if (r.Status == 'Tidak Berbadan Hukum')
            Ext.getCmp('Status6').setValue(true);
        Ext.getCmp('Year').setValue(r.Year);
        Ext.getCmp('Alias').setValue(r.Alias);
        Ext.getCmp('PermanentEmployeeMale').setValue(r.PermanentEmployeeMale);
        Ext.getCmp('PermanentEmployeeFemale').setValue(r.PermanentEmployeeFemale);
        Ext.getCmp('TemporaryEmployeeMale').setValue(r.TemporaryEmployeeMale);
        Ext.getCmp('TemporaryEmployeeFemale').setValue(r.TemporaryEmployeeFemale);
//        Ext.getCmp('LatDeg').setValue(r.LatDeg);
        //Ext.getCmp('LatMin').setValue(r.LatMin);
        Ext.getCmp('LatSec').setValue(r.Latitude);
//        Ext.getCmp('LongDeg').setValue(r.LongDeg);
        //Ext.getCmp('LongMin').setValue(r.LongMin);
        Ext.getCmp('LongSec').setValue(r.Longitude);
        Ext.getCmp('Elevation').setValue(r.Elevation);
//        h.PartnerID,h.PartnerName
      Ext.getCmp('PartnerID').setValue(r.PartnerName);
//        Ext.getCmp('Photo_old').setValue(r.Photo);
//        Ext.getCmp('iphoto').setSrc(m_photo + '/' + r.Photo);
        ds.proxy.extraParams = {VillageID: r.VillageID};
    }

    var grid = Ext.create('Ext.grid.Panel', {
        store: store,
        width: '100%',
        minHeight: 250,
        id: 'grid',
        style: 'border:1px solid #CCC;',
        renderTo: 'ext-content',
        loadMask: true,
        selType: 'rowmodel',
        listeners: {
            itemdblclick: function(dv, record, item, index, e) {
                displayFormWindow();
                var sm = record;
                Ext.Ajax.request({
                    url: m_crud + 'datawarehouse/' + m_type,
                    method: 'GET',
                    params: {id: sm.get('WarehouseID')},
                    success: function(fp, o) {
                        var r = Ext.decode(fp.responseText);
                        Ext.getCmp('WarehouseID').setValue(sm.get('WarehouseID'));
                        fset(r);
                        hideSave();
                    }
                });
            }
        },
        dockedItems: [{
                xtype: 'pagingtoolbar',
                store: store, // same store GridPanel is using
                dock: 'bottom',
                displayInfo: true
            }, {
                xtype: 'toolbar',
                items: [{
                        icon: varjs.config.base_url + 'images/icons/new/add.png', cls:'Sfr_BtnGridGreen', overCls:'Sfr_BtnGridGreen-Hover',
                        text: lang('Add'),
                        hidden: !m_act_add,
                        scope: this,
                        handler: function() {
                            Ext.getCmp('panel_staff').disable()
                            displayFormWindow();
                            hideSave();
                            Ext.getCmp('iphoto').setSrc('');
                            Ext.getCmp('Provinsi').setValue(m_paramval);
                            Ext.getCmp('Kecamatan').disable()
                            Ext.getCmp('Desa').disable()
                        },
                        cls: m_act_add
                    }, {
                        icon: varjs.config.base_url + 'images/icons/new/update.png',
                        text: lang('Update'),
                        hidden: !m_act_update,
                        scope: this,
                        handler: function() {
                            displayFormWindow();
                            var sm = Ext.getCmp('grid').getSelectionModel().getSelection()[0];
                            Ext.Ajax.request({
                                url: m_crud + 'datawarehouse/' + m_type,
                                method: 'GET',
                                params: {id: sm.get('WarehouseID')},
                                success: function(fp, o) {
                                    var r = Ext.decode(fp.responseText);
                                    Ext.getCmp('WarehouseID').setValue(sm.get('WarehouseID'));
                                    fset(r);
                                    hideSave();
                                }
                            });
                        },
                        cls: m_act_update
                    }, {
                        itemId: 'remove',
                        icon: varjs.config.base_url + 'images/icons/new/delete.png',
                        cls: m_act_delete,
                        hidden: !m_act_delete,
                        text: lang('Hapus'),
                        scope: this,
                        handler: function() {
                            var smb = Ext.getCmp('grid').getSelectionModel().getSelection()[0];
                            Ext.MessageBox.confirm('Message', lang('Apakah anda mau menghapus data ini ?'), function(btn) {
                                if (btn == 'yes') {
                                    Ext.Ajax.request({
                                        waitMsg: 'Please Wait',
                                        url: m_crud + 'data',
                                        method: 'DELETE',
                                        params: {id: smb.raw.WarehouseID},
                                        success: function(response, opts) {
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
                                        failure: function(response, opts) {
                                            var obj = Ext.decode(response.responseText);
                                            Ext.MessageBox.alert('error', lang('Could not connect to the database. Retry later'));
                                        }
                                    });
                                }
                            });
                        }
                    }, {
                        name: 'key', baseCls:'Sfr_TxtfieldSearchGrid',
                        id: 'key',
                        xtype: 'textfield',
                        emptyText: lang('Cari berdasar nama/ID')
                    },
                    {
                        id: 'sProvinsi',
                        name: 'sProvinsi',
                        xtype: 'combo',
                        hidden:true,
                        readOnly:true,
                        store: mc_Provinsi,
                        displayField: 'label',
                        value:m_paramval,
                        valueField: 'label',
                        queryMode: 'local',
                        listeners: {
                            change: function(cb, nv, ov) {
                                mc_Kabupaten.load({
                                    params: {
                                        key: Ext.getCmp('sProvinsi').getValue()
                                    }});
                                Ext.getCmp('sKabupaten').enable();
                            }
                        }
                    },
                    {
                        id: 'sKabupaten',
                        name: 'sKabupaten',
                        xtype: 'combo',
                        store: mc_Kabupaten,
                        value:m_district,
                        displayField: 'label',
                        valueField: 'label',
                        selectOnFocus:true,
                        queryMode: 'local',
                        hidden: true,
                    }, {
                        xtype: 'button',
                        icon: varjs.config.base_url + 'images/icons/silk/search.png',
                        margin: '0px 0px 0px 6px',
                        text: lang('Search'),
                        handler: function() {
                            store.load({
                                params: {
                                    key: Ext.getCmp('key').getValue(),
                                    // kab: Ext.getCmp('sKabupaten').getValue(),
                                    // prov: Ext.getCmp('sProvinsi').getValue()
                                }});
                        }
                    }]
            }],
        columns: [{
                text: lang('ID'),
                dataIndex: 'SupplychainID',
                hidden: true
            }, {
                text: lang('No'),
                xtype: 'rownumberer',
                width: '5%'
            }, {
                text: lang('ID'),
                width: '10%',
                dataIndex: 'WarehouseID'
            }, {
                text: lang('Nama'),
                width: '30%',
                dataIndex: 'WarehouseName'
            }, {
                text: lang('Alamat'),
                width: '30%',
                dataIndex: 'Address'
            }, {
                text: lang('District'),
                width: '25%',
                dataIndex: 'District'
            }]
    });

//    mc_Kabupaten.load({
//            params: {
//                key: Ext.getCmp('sProvinsi').getValue()
//            }});
//                                Ext.getCmp('sKabupaten').enable();
//    var combo = Ext.getCmp('sKabupaten');
//    combo.select(combo.getStore().getAt(0));

});



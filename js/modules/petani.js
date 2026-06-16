Ext.Loader.setConfig({enabled: true});

Ext.Loader.setPath('js/ext-4.2.0.663', '../ux');
Ext.Loader.setPath('js/ext-4.2.0.663', '../ux/DataView/');
Ext.require([
    'Ext.util.*',
    'Ext.view.View',
    'Ext.ux.DataView.DragSelector'
]);

var error = false;
Ext.onReady(function () {
    Ext.tip.QuickTipManager.init();
    Ext.define('Scpp.Model', {
        extend: 'Ext.data.Model',
        fields: ['id', 'nama', 'alamat', 'kelamin', 'lahir', 'DateCollection', 'DateUpdated', 'CPGid', 'VillageID', 'Address',
            'FarmerName', 'HandPhone', 'Gender', 'MaritalStatus', 'Birthdate', 'Education', 'error_status', 'errors'],
    });
    var store = Ext.create('Ext.data.Store', {
        model: 'Scpp.Model',
        autoLoad: true,
        pageSize: 10,
        proxy: {
            type: 'ajax',
            url: m_crud + 's',
            params: {
                'X-API-KEY': '030584'
            },
            reader: {
                type: 'json',
                root: 'data'
            }
        }
    });

    store.on('load', function (store, records, model, operation) {
        error = false;
        $.each(records, function (index, val) {
            if (val.data.error_status == 1) {
                error = true;
            }
        });
        if (error) {
            Ext.getCmp('asaveButton').disable();
        } else {
            Ext.getCmp('asaveButton').enable();
        }
    });

    ImageModel = Ext.define('ImageModel', {
        extend: 'Ext.data.Model',
        fields: [
            {name: 'PhotoID'},
            {name: 'FarmerID'},
            {name: 'Photo'},
            {name: 'IsActive'},
            {name: 'DateCreated'},
            {name: 'CreatedBy'},
            {name: 'DateUpdated'}
        ]
    });

    Ext.create('Ext.form.Panel', {
        hidden: true,
        items: [{
                xtype: 'textfield',
                name: 'name',
                id: 'defaultFarmerID'
            }]
    });

    var store_photo_history = Ext.create('Ext.data.Store', {
        model: 'ImageModel',
        pageSize: 8,
        proxy: {
            type: 'ajax',
            url: m_photo_history,
            //extraParams : {id_default: Ext.getCmp('defaultFarmerID').getValue()}, 
            reader: {
                type: 'json',
                root: 'data',
                totalProperty: 'total'
            }
        }, listeners: {
            beforeload: function (store, operation) {
                store.proxy.extraParams.id_default = Ext.getCmp('defaultFarmerID').getValue();
            }
        }
    });

    Ext.define('photo.Model', {
        extend: 'Ext.data.Model',
        fields: ['FarmerID', 'FarmerName', 'FarmerGender', 'Path', 'Status']
    });
    var store_photo = Ext.create('Ext.data.Store', {
        model: 'photo.Model',
        autoLoad: false,
        pageSize: 30,
        proxy: {
            type: 'ajax',
            url: m_crud + '_photo',
            reader: {
                type: 'json',
                root: 'data',
                totalProperty: 'total'
            }
        },
        listeners: {
            beforeload: function (store, operation) {
                store.proxy.extraParams.key = Ext.getCmp('skey').getValue();
                store.proxy.extraParams.kab = Ext.getCmp('skab').getValue();
                store.proxy.extraParams.prov = Ext.getCmp('sprov').getValue();
                store.proxy.extraParams.kec = Ext.getCmp('skec').getValue();
                store.proxy.extraParams.village = Ext.getCmp('svillage').getValue();
            }
        }
    });
    Ext.define('training.Model', {
        extend: 'Ext.data.Model',
        fields: ['CpgBatchTrainingsFarmerID', 'FarmerID', 'FarmerName', 'FarmerGender', 'Village']
    });
    var store_training = Ext.create('Ext.data.Store', {
        model: 'training.Model',
        autoLoad: false,
        pageSize: 20,
        proxy: {
            type: 'ajax',
            url: m_crud + '_training',
            reader: {
                type: 'json',
                root: 'data',
                totalProperty: 'total'
            }
        },
        listeners: {
            beforeload: function (store, operation) {
                store.proxy.extraParams.CpgBatchTrainingID = Ext.getCmp('straining').getValue();
            }
        }
    });
    var mc_cpg = Ext.create('Ext.data.Store', {
        extend: 'Ext.data.Model',
        fields: ['id', 'label'],
        autoLoad: true,
        pageSize: 10,
        proxy: {
            type: 'ajax',
            url: m_crud + '_cpg',
            reader: {
                type: 'json',
                root: 'data'
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
            reader: {
                type: 'json',
                root: 'data'
            }
        }
    });
    var mc_Kecamatan = Ext.create('Ext.data.Store', {
        extend: 'Ext.data.Model',
        fields: ['id', 'label'],
        autoLoad: true,
        pageSize: 10,
        proxy: {
            type: 'ajax',
            url: m_crud + '_subdistrict',
            reader: {
                type: 'json',
                root: 'data'
            }
        }
    });

    var mc_Desa = Ext.create('Ext.data.Store', {
        extend: 'Ext.data.Model',
        fields: ['id', 'label'],
        autoLoad: true,
        pageSize: 10,
        proxy: {
            type: 'ajax',
            url: m_crud + '_village',
            reader: {
                type: 'json',
                root: 'data'
            }
        }
    });
    var mc_training = Ext.create('Ext.data.Store', {
        extend: 'Ext.data.Model',
        fields: ['id', 'label'],
        pageSize: 10,
        proxy: {
            type: 'ajax',
            url: m_api + '/report/printout_list_learning',
            reader: {
                type: 'json',
                root: 'data'
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

    function LoadStorePhoto() {
        store_photo.load({
            params: {
                key: Ext.getCmp('skey').getValue(),
                prov: Ext.getCmp('sprov').getValue(),
                kab: Ext.getCmp('skab').getValue(),
                kec: Ext.getCmp('skec').getValue(),
                des: Ext.getCmp('svillage').getValue()
            }
        });
    }

    var DataForm = Ext.create('Ext.form.Panel', {
        frame: false,
        renderTo: 'ext-content',
        layout: {
            type: 'vbox',
            align: 'stretch'
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
                        title: 'Basic Data',
                        padding: 5,
                        style: 'border:2px solid #ADD2ED',
                        items: [{
                                xtype: 'gridpanel',
                                store: store,
                                width: '100%',
                                minHeight: 550,
                                //title: 'Survey List',
                                style: 'border:1px solid #CCC;',
                                loadMask: true,
                                selType: 'rowmodel',
                                dockedItems: [{
                                        xtype: 'toolbar',
                                        items: [{
                                                xtype: 'form',
                                                fileUpload: true,
                                                enctype: 'multipart/form-data',
                                                id: 'upload',
                                                items: [{
                                                        xtype: 'fileuploadfield',
                                                        fieldLabel: 'File (.xls)',
                                                        labelWidth: 60,
                                                        id: 'file',
                                                        padding: 5,
                                                        name: 'file',
                                                        buttonText: 'Browse',
                                                        listeners: {
                                                            'change': function (fb, v) {
                                                                var form = Ext.getCmp('upload').getForm();
                                                                form.submit({
                                                                    url: m_crud + '_upload',
                                                                    waitMsg: 'Sending and insert data temporary...',
                                                                    success: function (fp, o) {
                                                                        store.load();
                                                                    }
                                                                    // ,failure: function(form, action) {
                                                                    //     console.log(action.result.msg);
                                                                    // }
                                                                });
                                                            }
                                                        }
                                                    }]
                                            }
                                        ]
                                    }],
                                columns: [
                                    {
                                        text: 'ID',
                                        dataIndex: 'id',
                                        width: '10%',
                                        hidden: true
                                    }, {
                                        text: lang('tools_no'),
                                        xtype: 'rownumberer',
                                        width: '5%'
                                    }, {
                                        text: lang('tools_name'),
                                        width: '10%',
                                        dataIndex: 'nama'
                                    }, {
                                        text: lang('tools_address'),
                                        width: '10%',
                                        dataIndex: 'alamat'
                                    }, {
                                        text: lang('tools_gender'),
                                        width: '6%',
                                        dataIndex: 'kelamin'
                                    }, {
                                        text: lang('tools_birthdate'),
                                        width: '10%',
                                        dataIndex: 'lahir'
                                    }, {
//,,,,Address,FarmerName,,Gender,,Birthdate,       
                                        text: 'DateCollection',
                                        width: '12%',
                                        dataIndex: 'DateCollection'
                                    }, {
                                        text: 'DateUpdated',
                                        width: '12%',
                                        dataIndex: 'DateUpdated'
                                    }, {
                                        text: 'CPGid',
                                        width: '7%',
                                        dataIndex: 'CPGid'
                                    }, {
                                        text: 'VillageID',
                                        width: '7%',
                                        dataIndex: 'VillageID'
                                    }, {
                                        text: 'HandPhone',
                                        width: '7%',
                                        dataIndex: 'HandPhone'
                                    }, {
                                        text: lang('tools_marital'),
                                        width: '7%',
                                        dataIndex: 'MaritalStatus'
                                    }, {
                                        text: lang('tools_education'),
                                        width: '7%',
                                        dataIndex: 'Education'
                                    }, {
                                        text: lang('error'),
                                        width: '20%',
                                        dataIndex: 'errors'
                                    }
                                ],
                                viewConfig: {
                                    stripeRows: false,
                                    getRowClass: function (record) {
                                        return record.get('error_status') == 1 ? 'error' : 'no-error';
                                    }
                                },
                                buttons: [{
                                        id: 'asaveButton',
                                        text: lang('Proses'),
                                        margin: '5px',
                                        scale: 'large',
                                        ui: 's-button',
                                        cls: 's-blue ',
                                        buttonAlign: 'left',
                                        handler: function () {
                                            var form = Ext.getCmp('upload').getForm();
                                            form.submit({
                                                url: m_crud + '_upload_data',
                                                waitMsg: 'Memindahkan data...',
                                                success: function (fp, o) {
                                                    Ext.MessageBox.alert('Success', 'Data saved.');
                                                }
                                            });
                                        }
                                    }]
                            }]
                    }, {
                        xtype: 'panel',
                        autoScroll: true,
                        title: 'Photo',
                        padding: 5,
                        style: 'border:2px solid #ADD2ED',
                        items: [{
                                xtype: 'form',
                                fileUpload: true,
                                enctype: 'multipart/form-data',
                                id: 'upload_foto',
                                items: [{
                                        xtype: 'fileuploadfield',
                                        fieldLabel: 'File (.zip)',
                                        labelWidth: 120,
                                        anchor: '50%',
                                        id: 'file_foto',
                                        padding: 5,
                                        name: 'file_foto',
                                        buttonText: 'Browse',
                                        listeners: {
                                            'change': function (fb, v) {
                                                var form = Ext.getCmp('upload_foto').getForm();
                                                form.submit({
                                                    url: m_crud + '_upload_foto',
                                                    waitMsg: 'Sending and insert photo...',
                                                    success: function (fp, o) {
                                                        //Ext.MessageBox.alert('Success', obj.message);
                                                        Ext.MessageBox.alert('Success', 'Upload foto berhasil');
                                                    }
                                                });
                                            }
                                        }
                                    }]
                            }
                        ]
                    }, {
                        xtype: 'panel',
                        autoScroll: true,
                        title: lang('Cek Foto Petani'),
                        padding: 5,
                        style: 'border:2px solid #ADD2ED',
                        items: [{
                                xtype: 'gridpanel',
                                store: store_photo,
                                id: 'grid',
                                width: '100%',
                                minHeight: 550,
                                //title: 'Survey List',
                                style: 'border:1px solid #CCC;',
                                loadMask: true,
                                selType: 'rowmodel',
                                viewConfig: {
                                    enableTextSelection: true
                                },
                                dockedItems: [{
                                        xtype: 'pagingtoolbar',
                                        store: store_photo,
                                        dock: 'bottom',
                                        displayInfo: true
                                    }, {
                                        xtype: 'toolbar',
                                        items: [/*{
                                         icon: varjs.config.base_url + 'images/icons/silk/picture_go.png',
                                         text: 'Photo',
                                         scope: this,
                                         handler: function () {
                                         displayFormPreview()
                                         var sm = Ext.getCmp('grid').getSelectionModel().getSelection()[0];
                                         Ext.getCmp('iphoto').setSrc(sm.get('Path'));
                                         }
                                         }*/, {
                                                icon: varjs.config.base_url + 'images/icons/silk/picture_go.png',
                                                text: 'Photo History',
                                                hidden: true,
                                                scope: this,
                                                handler: function () {
                                                    var sm = Ext.getCmp('grid').getSelectionModel().getSelection()[0];
                                                    Ext.getCmp('defaultFarmerID').setValue(sm.get('FarmerID'));
                                                    store_photo_history.load({
                                                        params: {
                                                            id: sm.get('FarmerID')
                                                        }
                                                    });
                                                    displayFormPreviewPhotoHistory();
                                                }
                                            }, {
                                                icon: varjs.config.base_url + 'images/icons/silk/printer.png',
                                                text: lang('Kartu'),
                                                hidden: true,
                                                scope: this,
                                                handler: function () {
                                                    var sm = Ext.getCmp('grid').getSelectionModel().getSelection()[0];
                                                    preview_cetak_surat(m_cetak_kartu + sm.get('FarmerID'));
                                                }
                                            }, {
                                                id: 'pcpg',
                                                name: 'pcpg',
                                                xtype: 'combo',
                                                store: mc_cpg,
                                                displayField: 'label',
                                                valueField: 'id',
                                                queryMode: 'local',
                                                hidden: true
                                            }, {
                                                name: 'start',
                                                id: 'start',
                                                xtype: 'textfield',
                                                emptyText: 'start',
                                                hidden: true
                                            }, {
                                                name: 'limit',
                                                id: 'limit',
                                                xtype: 'textfield',
                                                emptyText: 'limit',
                                                hidden: true
                                            }, {
                                                name: 'key', baseCls:'Sfr_TxtfieldSearchGrid',
                                                id: 'skey',
                                                xtype: 'textfield',
                                                emptyText: lang('Cari berdasar Nama/ID'),
                                                width: 150
                                            }, {
                                                id: 'sprov',
                                                name: 'sProvinsi',
                                                xtype: 'combo',
                                                store: mc_Provinsi,
                                                displayField: 'label',
                                                valueField: 'id',
                                                queryMode: 'local',
                                                listeners: {
                                                    change: function (cb, nv, ov) {
                                                        mc_Kabupaten.load({
                                                            params: {
                                                                key: Ext.getCmp('sprov').getValue()
                                                            }
                                                        });
                                                        Ext.getCmp('skab').enable();
                                                        mc_Desa.load({
                                                            params: {
                                                                prov: Ext.getCmp('sprov').getValue(),
                                                                kab: Ext.getCmp('skab').getValue(),
                                                            }
                                                        });
                                                    }
                                                }
                                            }, {
                                                id: 'skab',
                                                name: 'sKabupaten',
                                                xtype: 'combo',
                                                store: mc_Kabupaten,
                                                displayField: 'label',
                                                valueField: 'id',
                                                queryMode: 'local',
                                                listeners: {
                                                    change: function (cb, nv, ov) {
                                                        mc_Kecamatan.load({
                                                            params: {
                                                                prov: Ext.getCmp('sprov').getValue(),
                                                                kab: Ext.getCmp('skab').getValue(),
                                                            }
                                                        });
                                                        mc_Desa.load({
                                                            params: {
                                                                prov: Ext.getCmp('sprov').getValue(),
                                                                kab: Ext.getCmp('skab').getValue(),
                                                            }
                                                        });
                                                    }
                                                }
                                            }, {
                                                id: 'skec',
                                                name: 'sKecamatan',
                                                xtype: 'combo',
                                                store: mc_Kecamatan,
                                                displayField: 'label',
                                                valueField: 'id',
                                                queryMode: 'local',
                                                listeners: {
                                                    change: function (cb, nv, ov) {
                                                        mc_Desa.load({
                                                            params: {
                                                                prov: Ext.getCmp('sprov').getValue(),
                                                                kab: Ext.getCmp('skab').getValue(),
                                                                kec: Ext.getCmp('skec').getValue(),
                                                            }
                                                        });
                                                    }
                                                }
                                            }, {
                                                id: 'svillage',
                                                name: 'sVillage',
                                                xtype: 'combo',
                                                store: mc_Desa,
                                                displayField: 'label',
                                                valueField: 'id',
                                                queryMode: 'local',
                                                width: 150
                                            }, {
                                                xtype: 'fieldcontainer',
                                                defaultType: 'checkboxfield',
                                                hidden: true,
                                                items: [{
                                                        boxLabel: lang('Tersertifikasi'),
                                                        name: 'certFarmPhoto',
                                                        inputValue: '1',
                                                        id: 'certFarmPhoto'
                                                    }]
                                            }, {
                                                xtype: 'form',
                                                items: [{
                                                        xtype: 'button',
                                                        icon: varjs.config.base_url + 'images/icons/silk/search.png',
                                                        margin: '0px 0px 0px 6px',
                                                        text: 'Search',
                                                        handler: function () {
                                                            LoadStorePhoto()
                                                        }
                                                    }]
                                            }, {
                                                xtype: 'form',
                                                items: [{
                                                        xtype: 'button',
                                                        icon: varjs.config.base_url + 'images/icons/silk/folder_explore.png',
                                                        margin: '0px 0px 0px 6px',
                                                        text: 'Process',
                                                        handler: function () {
                                                            Ext.Ajax.request({
                                                                url: m_process,
                                                                method: 'GET',
                                                                waitMsg: 'Processing data...',
                                                                params: {
                                                                    cpg: Ext.getCmp('scpg').getValue(),
                                                                    key: Ext.getCmp('skey').getValue(),
                                                                    prov: Ext.getCmp('sprov').getValue(),
                                                                    kab: Ext.getCmp('skab').getValue()
                                                                },
                                                                success: function (fp, o) {
                                                                    Ext.MessageBox.alert('Success', 'Data Processed.');
                                                                    LoadStorePhoto()
                                                                }
                                                            })
                                                        }
                                                    }]
                                            }
                                        ]
                                    }],
                                columns: [
                                    {
                                        text: 'ID',
                                        dataIndex: 'id',
                                        width: '1%',
                                        hidden: true
                                    }, {
                                        text: lang('tools_no'),
                                        xtype: 'rownumberer',
                                        width: '4%'
                                    }, {
                                        text: 'FarmerID',
                                        width: '10%',
                                        dataIndex: 'FarmerID'
                                    }, {
                                        text: 'FarmerName',
                                        width: '17%',
                                        dataIndex: 'FarmerName'
                                    }, {
                                        text: 'Gender',
                                        width: '7%',
                                        dataIndex: 'FarmerGender'
                                    }, {
                                        text: 'Path',
                                        width: '50%',
                                        dataIndex: 'Path'
                                    }, {
                                        text: 'Photo',
                                        dataIndex: 'Path',
                                        renderer: function (value) {
                                            console.log(value);
                                            if (value) {
                                                return '<img src="' + m_photo_path + value + '" width="35" height="50"/>';
                                            }
                                        },
                                    }]
                            }]
                    }, {
                        xtype: 'panel',
                        autoScroll: true,
                        title: lang('Sertifikat'),
                        padding: 5,
                        style: 'border:2px solid #ADD2ED',
                        items: [{
                                xtype: 'gridpanel',
                                store: store_training,
                                id: 'grid_training',
                                width: '100%',
                                minHeight: 550,
                                //title: 'Survey List',
                                style: 'border:1px solid #CCC;',
                                loadMask: true,
                                selType: 'rowmodel',
                                viewConfig: {
                                    enableTextSelection: true
                                },
                                dockedItems: [
                                    {
                                        xtype: 'pagingtoolbar',
                                        store: store_training,
                                        dock: 'bottom',
                                        displayInfo: true
                                    },
                                    {
                                        xtype: 'toolbar',
                                        items: [{
                                                icon: varjs.config.base_url + 'images/icons/silk/printer.png',
                                                text: lang('Sertifikat'),
                                                scope: this,
                                                handler: function () {
                                                    var sm = Ext.getCmp('grid_training').getSelectionModel().getSelection()[0];
                                                    preview_cetak_surat(m_cetak_sertifikat + sm.get('CpgBatchTrainingsFarmerID'));
                                                }
                                            }, {
                                                id: 'tprov',
                                                name: 'tProvinsi',
                                                xtype: 'combo',
                                                store: mc_Provinsi,
                                                displayField: 'label',
                                                valueField: 'id',
                                                queryMode: 'local',
                                                listeners: {
                                                    change: function (cb, nv, ov) {
                                                        mc_Kabupaten.load({
                                                            params: {
                                                                key: Ext.getCmp('tprov').getValue()
                                                            }
                                                        });
                                                        Ext.getCmp('tkab').enable();
                                                        mc_cpg.load({
                                                            params: {
                                                                prov: Ext.getCmp('tprov').getValue(),
                                                                kab: Ext.getCmp('tkab').getValue(),
                                                            }
                                                        });
                                                    }
                                                }
                                            }, {
                                                id: 'tkab',
                                                name: 'tKabupaten',
                                                xtype: 'combo',
                                                store: mc_Kabupaten,
                                                displayField: 'label',
                                                valueField: 'label',
                                                queryMode: 'local',
                                                listeners: {
                                                    change: function (cb, nv, ov) {
                                                        mc_cpg.load({
                                                            params: {
                                                                prov: Ext.getCmp('sprov').getValue(),
                                                                kab: Ext.getCmp('tkab').getValue(),
                                                            }
                                                        });
                                                    }
                                                }
                                            }, {
                                                id: 'tcpg',
                                                name: 'tCPG',
                                                xtype: 'combo',
                                                store: mc_cpg,
                                                displayField: 'label',
                                                valueField: 'id',
                                                queryMode: 'local',
                                                width: 250,
                                                listeners: {
                                                    change: function (cb, nv, ov) {
                                                        mc_training.load({
                                                            params: {
                                                                cpg: Ext.getCmp('tcpg').getValue()
                                                            }
                                                        });
                                                    }
                                                }
                                            }, {
                                                id: 'straining',
                                                name: 'sTraining',
                                                xtype: 'combo',
                                                store: mc_training,
                                                displayField: 'label',
                                                valueField: 'id',
                                                queryMode: 'local',
                                                width: 250,
                                            }, {
                                                xtype: 'form',
                                                items: [{
                                                        xtype: 'button',
                                                        icon: varjs.config.base_url + 'images/icons/silk/search.png',
                                                        margin: '0px 0px 0px 6px',
                                                        text: 'Search',
                                                        handler: function () {
                                                            store_training.load({
                                                                params: {
                                                                    CpgBatchTrainingID: Ext.getCmp('straining').getValue()
                                                                }
                                                            })
                                                        }
                                                    }]
                                            }
                                        ]
                                    }],
                                columns: [
                                    {
                                        text: 'ID',
                                        dataIndex: 'CpgBatchTrainingsFarmerID',
                                        width: '1%',
                                        hidden: true
                                    }, {
                                        text: lang('tools_no'),
                                        xtype: 'rownumberer',
                                        width: '5%'
                                    }, {
                                        text: 'FarmerID',
                                        width: '20%',
                                        dataIndex: 'FarmerID'
                                    }, {
                                        text: 'FarmerName',
                                        width: '35%',
                                        dataIndex: 'FarmerName'
                                    }, {
                                        text: 'Gender',
                                        width: '10%',
                                        dataIndex: 'FarmerGender'
                                    },
                                    {
                                        text: 'Village',
                                        width: '30%',
                                        dataIndex: 'Village'
                                    }
                                ]
                            }]
                    }, {
                        xtype: 'panel',
                        autoScroll: true,
                        title: 'Learning Contract',
                        padding: 5,
                        style: 'border:2px solid #ADD2ED',
                        items: [{
                                xtype: 'form',
                                fileUpload: true,
                                enctype: 'multipart/form-data',
                                id: 'upload_learning_contract',
                                items: [{
                                        xtype: 'fileuploadfield',
                                        fieldLabel: 'File (.zip)',
                                        labelWidth: 200,
                                        anchor: '50%',
                                        id: 'file_learning_contract',
                                        padding: 5,
                                        name: 'file_learning_contract',
                                        buttonText: 'Browse',
                                        listeners: {
                                            'change': function (fb, v) {
                                                var form = Ext.getCmp('upload_learning_contract').getForm();
                                                form.submit({
                                                    url: m_crud + '_upload_learning_contract',
                                                    waitMsg: 'Sending and save file...',
                                                    success: function (fp, o) {
                                                        Ext.MessageBox.alert('Success', 'Upload Success');
                                                    }, failure: function (fp, o) {
                                                        Ext.MessageBox.alert('Error', 'Upload Error. Please try again.');
                                                    }
                                                });
                                            }
                                        }
                                    }]
                            }]
                    }]
            }]
    });
    var PreviewForm = Ext.create('Ext.form.Panel', {
        frame: true,
        height: 500,
        autoScroll: true,
        width: 600,
        bodyPadding: 5,
        id: 'previewForm',
        items: [{
                xtype: 'image',
                id: 'iphoto'
            }],
        buttons: [{
                text: 'Close',
                margin: '5px',
                scale: 'large',
                ui: 's-button',
                cls: 's-grey',
                disabled: false,
                handler: function () {
                    winPreview.hide();
                }
            }]
    });
    var winPreview = Ext.widget('window', {
        title: 'Photo Preview',
        height: 400,
        width: 600,
        id: 'winPreview',
        autoScroll: true,
        modal: true,
        layout: {
            type: 'fit'
        },
        items: [PreviewForm]
    });

    function displayFormPreview(photo) {
        if (!winPreview.isVisible()) {
            winPreview.show();
            winPreview.toFront();
        } else {
            winPreview.hide(this, function () {
            });
            winPreview.toFront();
        }
    }

    function displayFormPreviewPhotoHistory() {
        if (!winPreviewPhotoHistory.isVisible()) {
            winPreviewPhotoHistory.show();
            //winPreviewPhotoHistory.toFront();
        } else {
            //winPreviewPhotoHistory.hide();
            winPreviewPhotoHistory.show();
            //winPreviewPhotoHistory.toFront();
        }
    }

    var Form_show = Ext.create('Ext.form.Panel', {
        frame: false,
        autoScroll: false,
        width: 400,
        height: 500,
        bodyPadding: 5,
        id: 'Form_show',
        fieldDefaults: {labelAlign: 'left', labelWidth: 70},
        items: [
            {
                xtype: 'image',
                id: 'foto_view',
                width: '390px',
                height: '480px',
                style: 'padding:5px;border:1px solid #CCC; background-color:#F2F2F2;',
            }
        ],
        buttons: [
            {
                text: lang('Close'),
                margin: '5px',
                scale: 'large',
                ui: 's-button',
                cls: 's-grey',
                disabled: false,
                handler: function () {
                    win_show.hide();
                }
            }
        ]
    });

    var win_show = Ext.create('widget.window', {
        title: 'Preview',
        id: 'win_show',
        closable: true,
        modal: true,
        closeAction: 'hide',
        width: 420,
        height: 550,
        layout: {type: 'border', padding: 5},
        items: [Form_show]
    });

    function displayFormShow() {
        if (!win_show.isVisible()) {
            Form_show.getForm().reset();
            Ext.getCmp('foto_view').setSrc('');
            win_show.show();
        } else {
            win_show.hide(this, function () {});
            win_show.toFront();
        }
    }



    var PreviewPhotoHistoryForm = Ext.create('Ext.form.Panel', {
        frame: true,
        height: 500,
        autoScroll: true,
        width: 750,
        bodyPadding: 5,
        closeAction: 'hide',
        id: 'images-view',
        dockedItems: [{
                xtype: 'pagingtoolbar',
                store: store_photo_history,
                dock: 'bottom',
                displayInfo: true
            }, {
                xtype: 'toolbar',
                id: 'toolbar_satu',
                items: [{
                        itemId: 'isactive',
                        hidden: true,
                        id: 'isactive',
                        icon: varjs.config.base_url + 'images/icons/silk/accept.png',
                        cls: m_act_delete,
                        text: lang('Set Active'),
                        scope: this,
                        handler: function () {
                            if (!Ext.getCmp('PhotoID').getValue()) {
                                Ext.MessageBox.alert('Failed', lang('Please select photo first !'));
                                Ext.getCmp('isactive').setVisible(true);
                                Ext.getCmp('remove').setVisible(false);
                                Ext.getCmp('cancel').setVisible(false);

                            } else {
                                Ext.MessageBox.confirm('Message', lang('Do you want to set active this photo ?'), function (btn) {
                                    if (btn == 'yes') {
                                        Ext.Ajax.request({
                                            url: m_photo_history + '_isactive',
                                            method: 'PUT',
                                            params: {
                                                id: Ext.getCmp('PhotoID').getValue(),
                                                fid: Ext.getCmp('FarmerID').getValue(),
                                                photo: Ext.getCmp('Photo').getValue()
                                            },
                                            waitMsg: lang('loading ...'),
                                            success: function (response, opts) {
                                                //var obj    = Ext.decode(response.responseText);                                                    
                                                var proxy = store_photo_history.getProxy();
                                                proxy.extraParams = {
                                                    id: Ext.getCmp('FarmerID').getValue()// sm.get('MonitoringId')
                                                };
                                                store_photo_history.load();
                                                //Ext.getCmp('PhotoID').setValue("")
                                                //Ext.getCmp('FarmerID').setValue("")
                                                //Ext.getCmp('Photo').setValue("")
                                            },
                                            failure: function (response, opts) {
                                                Ext.MessageBox.alert('Failed', 'Photo could not be selected.');
                                            }
                                        });
                                    }
                                });
                            }

                        }
                    }, {
                        itemId: 'remove',
                        hidden: true,
                        id: 'remove',
                        icon: varjs.config.base_url + 'images/icons/new/delete.png',
                        cls: m_act_delete,
                        text: lang('Delete'),
                        scope: this,
                        handler: function () {
                            if (!Ext.getCmp('PhotoID').getValue()) {
                                Ext.MessageBox.alert('Failed', lang('Please select photo first !'));
                                Ext.getCmp('isactive').setVisible(true);
                                Ext.getCmp('remove').setVisible(false);
                                Ext.getCmp('cancel').setVisible(false);
                            } else {
                                Ext.MessageBox.confirm('Message', lang('Do you want to delete this photo ?'), function (btn) {
                                    if (btn == 'yes') {
                                        Ext.Ajax.request({
                                            url: m_photo_history,
                                            method: 'delete',
                                            params: {
                                                id: Ext.getCmp('PhotoID').getValue(),
                                                fid: Ext.getCmp('FarmerID').getValue(),
                                                photo: Ext.getCmp('Photo').getValue()
                                            },
                                            waitMsg: lang('loading ...'),
                                            success: function (response, opts) {
                                                var obj = Ext.decode(response.responseText);
                                                var proxy = store_photo_history.getProxy();
                                                proxy.extraParams = {
                                                    id: Ext.getCmp('FarmerID').getValue()// sm.get('MonitoringId')
                                                };
                                                store_photo_history.load();
                                                if (obj.success == true) {
                                                    Ext.MessageBox.alert('Success', 'Photo successfully removed');
                                                } else {
                                                    Ext.MessageBox.alert('Error', 'Photo could not be removed');
                                                }
                                                //Ext.getCmp('PhotoID').setValue("")
                                                //Ext.getCmp('FarmerID').setValue("")
                                                //Ext.getCmp('Photo').setValue("")
                                            },
                                            failure: function (response, opts) {
                                                Ext.MessageBox.alert('Error', 'Photo could not be removed');
                                            }
                                        });
                                    }
                                });
                            }

                        }
                    },
                    {
                        itemId: 'cancel',
                        hidden: true,
                        id: 'cancel',
                        icon: varjs.config.base_url + 'images/icons/silk/building_go.png',
                        cls: m_act_cancel,
                        text: lang('Cancel'),
                        scope: this,
                        handler: function () {
                            Ext.getCmp('isactive').setVisible(false);
                            Ext.getCmp('remove').setVisible(false);
                            Ext.getCmp('cancel').setVisible(false);
                        }
                    },
                    {
                        xtype: 'textfield',
                        hidden: true,
                        id: 'PhotoID'
                    },
                    {
                        xtype: 'textfield',
                        hidden: true,
                        id: 'FarmerID'
                    },
                    {
                        xtype: 'textfield',
                        hidden: true,
                        id: 'Photo'
                    }
                ]
            },
            {
                xtype: 'toolbar',
                id: 'toolbar_dua',
                dock: 'bottom',
                hidden: false,
                items: [
                    {
                        xtype: 'displayfield',
                        hidden: false,
                        style: 'fontSize: 8px;',
                        id: 'file_detail',
                        name: 'file_detail'
                    }
                ]
            }
        ],
        items: Ext.create('Ext.view.View', {
            title: lang('Photo History Farmer'),
            style: 'padding:5px;border-top:1px solid #CCC;',
            store: store_photo_history,
            tpl: [
                '<tpl for=".">',
                '<div class="thumb-wrap" id="{PhotoID}" style="height: 210px; {IsActive}">',
                '<div class="thumb" style="height: 200px;">',
                '<img style="height: 195px;" width="145px" src="api/images/Photo/{Photo}" title="{Photo:htmlEncode}">',
                '</div>',
                '</div>',
                '</tpl>',
                '<div class="x-clear"></div>'
            ],
            multiSelect: false,
            minHeight: 165,
            trackOver: true,
            overItemCls: 'x-item-over',
            itemSelector: 'div.thumb-wrap',
            emptyText: lang('No photo to display'),
            listeners: {
                selectionchange: function (dv, nodes) {
                    if (nodes.length != 1)
                        Ext.getCmp('toolbar_dua').setVisible(false);
                    else {

                        Ext.getCmp('toolbar_dua').setVisible(true);

                        Ext.getCmp('PhotoID').setValue(nodes[0].data.PhotoID);
                        Ext.getCmp('FarmerID').setValue(nodes[0].data.FarmerID);
                        Ext.getCmp('Photo').setValue(nodes[0].data.Photo);
                        //Ext.getCmp('foto_name').setValue(nodes[0].data.FileName);
                        //Ext.getCmp('foto_titles').setValue(nodes[0].data.FileTitle);
                        //Ext.getCmp('foto_dates').setValue('Date : '+nodes[0].data.DateCreated+', by : '+nodes[0].data.nama);

                        Ext.getCmp('file_detail').setValue('*klik dua kali untuk preview.');

                        //Ext.getCmp('add').setVisible(false); 
                        //Ext.getCmp('foto').disable();
                        //Ext.getCmp('title').disable();

                        Ext.getCmp('isactive').setVisible(true);
                        Ext.getCmp('remove').setVisible(true);
                        Ext.getCmp('cancel').setVisible(true);
                    }
                },
                itemdblclick: function (dv, record, item, index, e) {
                    if (!Ext.getCmp('Photo').getValue()) {
                        Ext.MessageBox.alert('Warning', lang('Please select photo first !'));
                        return;
                    }
                    displayFormShow();
                    Ext.getCmp('foto_view').setSrc('api/images/Photo/' + Ext.getCmp('Photo').getValue());
                    //Ext.getCmp('foto_title').setValue(Ext.getCmp('foto_titles').getValue());
                    //Ext.getCmp('foto_date').setValue(Ext.getCmp('foto_dates').getValue());

                }
            }
        }),
        buttons: [{
                text: 'Close',
                margin: '5px',
                scale: 'large',
                ui: 's-button',
                cls: 's-grey',
                disabled: false,
                handler: function () {
                    winPreviewPhotoHistory.hide();
                }
            }]
    });

    var winPreviewPhotoHistory = Ext.widget('window', {
        title: 'Photo History Preview',
        height: 500,
        width: 750,
        id: 'winPreviewPhotoHistory',
        autoScroll: true,
        modal: true,
        closeAction: 'hide',
        layout: {
            type: 'fit'
        },
        items: [PreviewPhotoHistoryForm]
    });

});

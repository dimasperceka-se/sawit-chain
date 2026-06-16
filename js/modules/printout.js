
var mc_petani;
var kml_farmer;

Ext.onReady(function () {
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
    var kml_Provinsi = Ext.create('Ext.data.Store', {
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

    var combo_year = Ext.create('Ext.data.Store', {
        extend: 'Ext.data.Model',
        fields: ['id', 'label'],
        autoLoad: true,
        proxy: {
            type: 'ajax',
            url: m_api + '/farmer/farmer_adopt_obs_combo_survey_year',
            reader: {
                type: 'json',
                root: 'data'
            }
        }
    });

    var mc_warehouse = Ext.create('Ext.data.Store', {
        extend: 'Ext.data.Model',
        fields: ['id', 'label'],
        autoLoad: true,
        pageSize: 10,
        proxy: {
            type: 'ajax',
            url: m_po+'_warehouse',
            reader: {
                type: 'json',
                root: 'data'
            }
        }
    });
    var mc_Pwarehouse = Ext.create('Ext.data.Store', {
        extend: 'Ext.data.Model',
        fields: ['id', 'label'],
        autoLoad: true,
        pageSize: 10,
        proxy: {
            type: 'ajax',
            url: m_Pwarehouse,
            reader: {
                type: 'json',
                root: 'data'
            }
        }
    });
    var mc_Pcooperative = Ext.create('Ext.data.Store', {
        extend: 'Ext.data.Model',
        fields: ['id', 'label'],
        autoLoad: true,
        pageSize: 10,
        proxy: {
            type: 'ajax',
            url: m_Pcooperative,
            reader: {
                type: 'json',
                root: 'data'
            }
        }
    });
    var mc_PbuyingStation = Ext.create('Ext.data.Store', {
        extend: 'Ext.data.Model',
        fields: ['id', 'label'],
        autoLoad: false,
        // pageSize: 10,
        proxy: {
            type: 'ajax',
            url: m_PbuyingStation,
            reader: {
                type: 'json',
                root: 'data'
            }
        }
    });
    var mc_Pstatus = Ext.create('Ext.data.Store', {
        fields: ['label'],
        data: [
            {"label": "All"},
            {"label": "Delivered"},
            {"label": "Closed"},
            {"label": "Other"},
            {"label": "Open"},
            {"label": "Close Batch"}
        ]
    });
    var mc_language = Ext.create('Ext.data.Store', {
        fields: ['id','label'],
        data: [
            {'id':'indonesia', "label": "Bahasa"},
            {'id':'english', "label": "English"},
        ]
    });

    var mc_Provinsi_Luwu_Utara = Ext.create('Ext.data.Store', {
        fields: ['id','label'],
        data: [
            {'id':'73', "label": "Sulawesi Selatan"}
        ]
    });

    var mc_koperasi = Ext.create('Ext.data.Store', {
        extend: 'Ext.data.Model',
        fields: ['id', 'label'],
        autoLoad: false,
        pageSize: 10,
        proxy: {
            type: 'ajax',
            url: m_rekap+'_koperasi',
            reader: {
                type: 'json',
                root: 'data'
            }
        }
    });
    var mc_bs = Ext.create('Ext.data.Store', {
        extend: 'Ext.data.Model',
        fields: ['id', 'label'],
        autoLoad: false,
        pageSize: 10,
        proxy: {
            type: 'ajax',
            url: m_rekap+'_bs',
            reader: {
                type: 'json',
                root: 'data'
            }
        }
    });
    var mc_status = Ext.create('Ext.data.Store', {
        extend: 'Ext.data.Model',
        fields: ['label'],
        data: [
            {"label": "Open"},
            {"label": "Close Batch"},
            {"label": "Sent"},
            {"label": "Delivered"},
            {"label": "Closed"},
            {"label": "Other"}
        ]
    });
    var mc_periode = Ext.create('Ext.data.Store', {
        extend: 'Ext.data.Model',
        fields: ['label'],
        autoLoad: false,
        pageSize: 10,
        proxy: {
            type: 'ajax',
            url: m_po+'_periode',
            reader: {
                type: 'json',
                root: 'data'
            }
        }
    });
    var mc_bu = Ext.create('Ext.data.Store', {
        extend: 'Ext.data.Model',
        fields: ['id', 'label'],
        autoLoad: true,
        pageSize: 10,
        proxy: {
            type: 'ajax',
            url: m_bu,
            reader: {
                type: 'json',
                root: 'data'
            }
        }
    });
    var mc_cooperatives = Ext.create('Ext.data.Store', {
        extend: 'Ext.data.Model',
        fields: ['id', 'label'],
        autoLoad: false,
        // pageSize: 10,
        proxy: {
            type: 'ajax',
            url: m_cooperatives,
            reader: {
                type: 'json',
                root: 'data'
            }
        }
    });

    var mc_Kabupaten = Ext.create('Ext.data.Store', {
        extend: 'Ext.data.Model',
        fields: ['id', 'label'],
        autoLoad: false,
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
        autoLoad: false,
        proxy: {
            type: 'ajax',
            url: m_api + '/report/combo_kecamatan',
            reader: {
                type: 'json',
                root: 'data'
            }
        }
    });

    var mc_Desa = Ext.create('Ext.data.Store', {
        extend: 'Ext.data.Model',
        fields: ['id', 'label'],
        autoLoad: false,
        proxy: {
            type: 'ajax',
            url: m_api + '/report/combo_desa',
            reader: {
                type: 'json',
                root: 'data'
            }
        }
    });

    var kml_Kabupaten = Ext.create('Ext.data.Store', {
        extend: 'Ext.data.Model',
        fields: ['id', 'label'],
        autoLoad: false,
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

    var mc_Kabupaten_Luwu_Utara = Ext.create('Ext.data.Store', {
        fields: ['label'],
        data: [
            {"label": "Luwu Utara"}
        ]
    });

    var mc_Warehouse = Ext.create('Ext.data.Store', {
        extend: 'Ext.data.Model',
        fields: ['id', 'label'],
        autoLoad: false,
        pageSize: 10,
        proxy: {
            type: 'ajax',
            url: m_Warehouse,
            reader: {
                type: 'json',
                root: 'data'
            }
        }
    });

    var mc_Survey = Ext.create('Ext.data.Store', {
        extend: 'Ext.data.Model',
        fields: ['id', 'label'],
        autoLoad: true,
        pageSize: 10,
        proxy: {
            type: 'ajax',
            url: m_Survey,
            reader: {
                type: 'json',
                root: 'data'
            }
        }
    });

    var mc_Survey_with_latest = Ext.create('Ext.data.Store', {
        extend: 'Ext.data.Model',
        fields: ['id', 'label'],
        autoLoad: true,
        pageSize: 10,
        proxy: {
            type: 'ajax',
            url: m_Survey,
            extraParams: {
                addLatestSurvey: 'yes'
            },
            reader: {
                type: 'json',
                root: 'data'
            }
        },
        listeners: {
            'beforeload': function(store, options) {
                store.proxy.extraParams.jenisSurvey = null;
            }
        }
    });

    var mc_cpgbatch = Ext.create('Ext.data.Store', {
        extend: 'Ext.data.Model',
        fields: ['id', 'label'],
        autoLoad: true,
        pageSize: 10,
        proxy: {
            type: 'ajax',
            url: m_crud + '_cpgbatch',
            reader: {
                type: 'json',
                root: 'data'
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
    var kml_cpg = Ext.create('Ext.data.Store', {
        extend: 'Ext.data.Model',
        fields: ['id', 'label'],
        autoLoad: false,
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

    var mc_cpg_luwu_utara = Ext.create('Ext.data.Store', {
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
            },
            extraParams: {
                kab:'Luwu Utara'
            }
        }
    });

    mc_petani = Ext.create('Ext.data.Store', {
        extend: 'Ext.data.Model',
        fields: ['id', 'label'],
        autoLoad: false,
        pageSize: 10,
        proxy: {
            type: 'ajax',
            url: m_crud + '_petani',
            reader: {
                type: 'json',
                root: 'data'
            }
        }
    });
    kml_farmer = Ext.create('Ext.data.Store', {
        extend: 'Ext.data.Model',
        fields: ['id', 'label'],
        autoLoad: false,
        pageSize: 10,
        proxy: {
            type: 'ajax',
            url: m_api + '/geospatial/kml_farmer_list',
            reader: {
                type: 'json',
                root: 'data'
            }
        }
    });

    var mc_role_petani = Ext.create('Ext.data.Store', {
        extend: 'Ext.data.Model',
        fields: ['id', 'label'],
        autoLoad: false,
        proxy: {
            type: 'ajax',
            url: m_api + '/report/combo_role_farmer',
            reader: {
                type: 'json',
                root: 'data'
            }
        }
    });

    var mc_role_agent = Ext.create('Ext.data.Store', {
        extend: 'Ext.data.Model',
        fields: ['id', 'label'],
        autoLoad: false,
        proxy: {
            type: 'ajax',
            url: m_api + '/report/combo_role_agent',
            reader: {
                type: 'json',
                root: 'data'
            }
        }
    });

    var mc_mill = Ext.create('Ext.data.Store', {
        extend: 'Ext.data.Model',
        fields: ['id', 'label'],
        autoLoad: false,
        proxy: {
            type: 'ajax',
            url: m_api + '/report/combo_mill',
            reader: {
                type: 'json',
                root: 'data'
            }
        }
    });

    var certified_farmers = Ext.create('Ext.data.Store', {
        extend: 'Ext.data.Model',
        fields: ['id', 'label'],
        autoLoad: false,
        pageSize: 10,
        proxy: {
            type: 'ajax',
            url: m_crud + '_petani_sertifikasi',
            reader: {
                type: 'json',
                root: 'data'
            }
        }
    });

    var store_nursery_objtype = Ext.create('Ext.data.Store', {
        fields: ['id','label'],
        data: [
            {"label": lang("Farmer Group"),"id": "cpg"},
            {"label": lang("Farmer"),"id": "farmer"},
            {"label": lang("Trader"),"id": "trader"},
            {"label": lang("Koperasi"),"id": "koperasi"}
        ]
    });

    var store_nursery_owner = Ext.create('Ext.data.Store', {
        extend: 'Ext.data.Model',
        fields: ['id', 'label'],
        autoLoad: false,
        proxy: {
            type: 'ajax',
            url: m_api + '/nursery/nursery_owner',
            reader: {
                type: 'json',
                root: 'data'
            }
        },
        listeners: {
            'beforeload': function(store, options) {
                store.proxy.extraParams.kabupaten = Ext.getCmp('npKabupaten').getValue();
                store.proxy.extraParams.objType = Ext.getCmp('npNurseryType').getValue();
                store.proxy.extraParams.printtype = Ext.getCmp('npPrinttype').getValue();
            }
        }
    });

    var store_nursery_nr = Ext.create('Ext.data.Store', {
        extend: 'Ext.data.Model',
        fields: ['id', 'label'],
        autoLoad: false,
        proxy: {
            type: 'ajax',
            url: m_api + '/nursery/nursery_nr',
            reader: {
                type: 'json',
                root: 'data'
            }
        },
        listeners: {
            'beforeload': function(store, options) {
                store.proxy.extraParams.objId = Ext.getCmp('npNurseryOwner').getValue();
                store.proxy.extraParams.objType = Ext.getCmp('npNurseryType').getValue();
            }
        }
    });

    var mc_jenis_form = Ext.create('Ext.data.Store', {
        fields: ['label'],
        data: [
            {"label": lang("Form Kosong (Simple)"),"id": "form_kosong_simple"},
            {"label": lang("Form Kosong"),"id": "Form Kosong"},
            {"label": lang("Form Hasil"),"id": "Form Hasil"},
            {"label": lang("Form Hasil (Simple)"),"id": "form_hasil_simple"}
        ]
    });


    var mc_jenis_form_attendance = Ext.create('Ext.data.Store', {
        extend: 'Ext.data.Model',
        fields: ['label'],
        pageSize: 2,
        proxy: {
            type: 'ajax',
            url: m_crud + '_list_jenis_form',
            reader: {
                type: 'json',
                root: 'data'
            }
        }
    });
    var mc_jenis_training = Ext.create('Ext.data.Store', {
        fields: ['label'],
        data: [
            {"label": "Kelompok Petani"},
            {"label": "Kader"},
            {"label": "Master"}
        ]
    });
    var mc_jenissurvey = Ext.create('Ext.data.Store', {
        fields: ['label'],
        data: [
            {"label": "GAP"},
            {"label": "GAP Certification"},
            {"label": "GNP"},
            {"label": "GFP"},
            {"label": "PPI"},
            {"label": "Saving Pilot"},
            {"label": "AO"}
        ]
    });

    var mc_sertifikasi = Ext.create('Ext.data.Store', {
        fields: ['label'],
        data: [
            {"label": "Semua"},
            {"label": "Tersertifikasi"},
            {"label": "Belum Tersertifikasi"}
        ]
    });

    var mc_tipe = Ext.create('Ext.data.Store', {
        fields: ['id','label'],
        data: [
            {"id":"0","label": lang('No Photo')},
            {"id":"1","label": lang('With Photo')}
        ]
    });

    var mc_nursery_printtype = Ext.create('Ext.data.Store', {
        fields: ['id','label'],
        data: [
            {"id":"empty","label": lang('Empty Form')},
            {"id":"result","label": lang('Result Form')},
            {"id":"profile","label": lang('Profile')}
        ]
    });

    var mc_list_training = Ext.create('Ext.data.Store', {
        extend: 'Ext.data.Model',
        fields: ['id', 'label'],
        pageSize: 10,
        proxy: {
            type: 'ajax',
            url: m_crud + '_list_training',
            reader: {
                type: 'json',
                root: 'data'
            }
        }
    });
    var mc_list_learning = Ext.create('Ext.data.Store', {
        extend: 'Ext.data.Model',
        fields: ['id', 'label'],
        pageSize: 10,
        proxy: {
            type: 'ajax',
            url: m_crud + '_list_learning',
            reader: {
                type: 'json',
                root: 'data'
            }
        }
    });
    var mc_list_trader = Ext.create('Ext.data.Store', {
        extend: 'Ext.data.Model',
        fields: ['id', 'label'],
        pageSize: 10,
        proxy: {
            type: 'ajax',
            url: m_crud + '_list_trader',
            reader: {
                type: 'json',
                root: 'data'
            }
        }
    });
    var store_partner = Ext.create('Ext.data.Store', {
        extend: 'Ext.data.Model',
        fields: ['id', 'label'],
        proxy: {
            type: 'ajax',
            url: m_partner,
            reader: {
                type: 'json',
                root: 'data'
            }
        }
    });

    var mc_Unit = Ext.create('Ext.data.Store', {
        extend: 'Ext.data.Model',
        fields: ['id', 'tipe', 'nama','SupplychainID'],
        autoLoad: false,
        pageSize: 10,
        proxy: {
            type: 'ajax',
            url: m_Unit,
            reader: {
                type: 'json',
                root: 'data'
            }
        }
    });

    var mc_Batch = Ext.create('Ext.data.Store', {
        extend: 'Ext.data.Model',
        fields: ['SupplyBatchID','SupplyBatchNumber', 'DestPO'],
        autoLoad: false,
        pageSize: 10,
        proxy: {
            type: 'ajax',
            url: m_Batch,
            reader: {
                type: 'json',
                root: 'data'
            }
        }
    });

    var p1p2_Provinsi = Ext.create('Ext.data.Store', {
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

    var p1p2_Kabupaten = Ext.create('Ext.data.Store', {
        extend: 'Ext.data.Model',
        fields: ['id', 'label'],
        autoLoad: false,
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

    var p1p2_Kecamatan = Ext.create('Ext.data.Store', {
        extend: 'Ext.data.Model',
        fields: ['id', 'label'],
        autoLoad: false,
        proxy: {
            type: 'ajax',
            url: m_api + '/report/combo_kecamatan',
            reader: {
                type: 'json',
                root: 'data'
            }
        }
    });

    var p1p2_Desa = Ext.create('Ext.data.Store', {
        extend: 'Ext.data.Model',
        fields: ['id', 'label'],
        autoLoad: false,
        proxy: {
            type: 'ajax',
            url: m_api + '/report/combo_desa',
            reader: {
                type: 'json',
                root: 'data'
            }
        }
    });

    var p1p2_role_petani = Ext.create('Ext.data.Store', {
        extend: 'Ext.data.Model',
        fields: ['id', 'label'],
        autoLoad: false,
        proxy: {
            type: 'ajax',
            url: m_api + '/report/combo_role_farmer',
            reader: {
                type: 'json',
                root: 'data'
            }
        }
    });

    var tab = Ext.create('Ext.Panel', {
        renderTo: 'ext-content',
        //height: 2500,
        frame: false,
        items: [
        {
            xtype: 'fieldset',
            title: lang('Farmer Profile'),
            id: 'beneficiary_profiles',
            hidden: m_act_printout_beneficiary_profiles,
            items: [{
                xtype: 'form',
                padding: 5,
                fieldDefaults: {
                    labelAlign: 'center',
                    labelWidth: 160,
                    anchor: '100%'
                },
                items: [{
                    layout: 'column',
                    border: false,
                    items: [{
                        columnWidth: 0.15,
                        layout: 'form',
                        padding: 3,
                        border: false,
                        items: [{
                            emptyText: '-- ' + lang('Provinsi') + '--',
                            id: 'bpProvinsi',
                            name: 'bpProvinsi',
                            xtype: 'combo',
                            labelWidth: 60,
                            store: mc_Provinsi,
                            displayField: 'label',
                            valueField: 'label',
                            queryMode: 'local',
                            listeners: {
                                change: function (cb, nv, ov) {
                                    mc_Kabupaten.load({params: {key: Ext.getCmp('bpProvinsi').getValue()}});
                                }
                            }
                        }]
                    }, {
                        columnWidth: 0.15,
                        layout: 'form',
                        padding: 3,
                        border: false,
                        items: [{
                            id: 'bpKabupaten',
                            name: 'bpKabupaten',
                            xtype: 'combo',
                            emptyText: '-- ' + lang('Kabupaten') + '--',
                            labelWidth: 70,
                            store: mc_Kabupaten,
                            displayField: 'label',
                            valueField: 'label',
                            queryMode: 'local',
                            listeners: {
                                change: function (cb, nv, ov) {
                                    mc_Kecamatan.load({params: {kab: Ext.getCmp('bpKabupaten').getValue()}});
                                }
                            }
                        }]
                    }, {
                        columnWidth: 0.2,
                        layout: 'form',
                        padding: 3,
                        border: false,
                        items: [{
                            id: 'bpKecamatan',
                            name: 'bpKecamatan',
                            emptyText: '-- ' + lang('Sub-Districts') + '--',
                            xtype: 'combo',
                            labelWidth: 40,
                            store: mc_Kecamatan,
                            displayField: 'label',
                            valueField: 'id',
                            queryMode: 'local',
                            listeners: {
                                change: function (cb, nv, ov) {
                                    mc_Desa.load({params: {kec: Ext.getCmp('bpKecamatan').getValue()}});
                                }
                            }
                        }]
                    }, {
                        columnWidth: 0.2,
                        layout: 'form',
                        padding: 3,
                        border: false,
                        items: [{
                            id: 'bpDesa',
                            name: 'bpDesa',
                            emptyText: '-- ' + lang('Desa') + '--',
                            xtype: 'combo',
                            labelWidth: 40,
                            store: mc_Desa,
                            displayField: 'label',
                            valueField: 'id',
                            queryMode: 'local',
                            listeners: {
                                change: function (cb, nv, ov) {
                                    mc_role_petani.load({params: {desa: Ext.getCmp('bpDesa').getValue()}});
                                }
                            }
                        }]
                    },{
                        columnWidth: 0.2,
                        layout: 'form',
                        padding: 3,
                        border: false,
                        items: [{
                            id: 'bppetani',
                            name: 'bppetani',
                            emptyText: '-- ' + lang('Petani') + '--',
                            xtype: 'combo',
                            multiSelect: true,
                            labelWidth: 40,
                            store: mc_role_petani,
                            displayField: 'label',
                            valueField: 'id',
                            queryMode: 'local'
                        }]
                    }]
                }],
                buttons: [{
                    text: lang('Cetak'),
                    margin: '5px',
                    scale: 'large',
                    ui: 's-button',
                    cls: 's-blue',
                    handler: function () {
                        var url = m_cetak_beneficiary_profiles;
                        if (Ext.getCmp('bppetani').getValue() == '') {
                            Ext.MessageBox.show({
                                title: 'Warning',
                                msg: lang('No Farmer Selected'),
                                buttons: Ext.MessageBox.OK,
                                animateTarget: 'mb9',
                                icon: 'ext-mb-error'
                            });
                            return false;
                        }
                        preview_cetak_surat(url + '/MemberID/' + Ext.getCmp('bppetani').getValue().join().replace(/,/g, '::'));
                    }
                }]
            }]
        },{
            xtype: 'fieldset',
            title: lang('Consent Notes'),
            id: 'consent_notes',
            hidden: m_act_printout_consent_notes,
            items: [{
                xtype: 'form',
                padding: 5,
                fieldDefaults: {
                    labelAlign: 'center',
                    labelWidth: 160,
                    anchor: '100%'
                },
                items: [{
                    layout: 'column',
                    border: false,
                    items: [{
                        columnWidth: 0.15,
                        layout: 'form',
                        padding: 3,
                        border: false,
                        items: [{
                            emptyText: '-- ' + lang('Provinsi') + '--',
                            id: 'cnProvinsi',
                            name: 'cnProvinsi',
                            xtype: 'combo',
                            labelWidth: 60,
                            store: mc_Provinsi,
                            displayField: 'label',
                            valueField: 'label',
                            queryMode: 'local',
                            listeners: {
                                change: function (cb, nv, ov) {
                                    mc_Kabupaten.load({params: {key: Ext.getCmp('cnProvinsi').getValue()}});
                                }
                            }
                        }]
                    }, {
                        columnWidth: 0.15,
                        layout: 'form',
                        padding: 3,
                        border: false,
                        items: [{
                            id: 'cnKabupaten',
                            name: 'cnKabupaten',
                            xtype: 'combo',
                            emptyText: '-- ' + lang('Kabupaten') + '--',
                            labelWidth: 70,
                            store: mc_Kabupaten,
                            displayField: 'label',
                            valueField: 'label',
                            queryMode: 'local',
                            listeners: {
                                change: function (cb, nv, ov) {
                                    mc_Kecamatan.load({params: {kab: Ext.getCmp('cnKabupaten').getValue()}});
                                }
                            }
                        }]
                    }, {
                        columnWidth: 0.2,
                        layout: 'form',
                        padding: 3,
                        border: false,
                        items: [{
                            id: 'cnKecamatan',
                            name: 'cnKecamatan',
                            emptyText: '-- ' + lang('Sub-Districts') + '--',
                            xtype: 'combo',
                            labelWidth: 40,
                            store: mc_Kecamatan,
                            displayField: 'label',
                            valueField: 'id',
                            queryMode: 'local',
                            listeners: {
                                change: function (cb, nv, ov) {
                                    mc_Desa.load({params: {kec: Ext.getCmp('cnKecamatan').getValue()}});
                                }
                            }
                        }]
                    }, {
                        columnWidth: 0.2,
                        layout: 'form',
                        padding: 3,
                        border: false,
                        items: [{
                            id: 'cnDesa',
                            name: 'cnDesa',
                            emptyText: '-- ' + lang('Desa') + '--',
                            xtype: 'combo',
                            labelWidth: 40,
                            store: mc_Desa,
                            displayField: 'label',
                            valueField: 'id',
                            queryMode: 'local',
                            listeners: {
                                change: function (cb, nv, ov) {
                                    mc_role_petani.load({params: {desa: Ext.getCmp('cnDesa').getValue()}});
                                }
                            }
                        }]
                    },{
                        columnWidth: 0.2,
                        layout: 'form',
                        padding: 3,
                        border: false,
                        items: [{
                            id: 'cnpetani',
                            name: 'cnpetani',
                            emptyText: '-- ' + lang('Petani') + '--',
                            xtype: 'combo',
                            multiSelect: true,
                            labelWidth: 40,
                            store: mc_role_petani,
                            displayField: 'label',
                            valueField: 'id',
                            queryMode: 'local'
                        }]
                    }]
                }],
                buttons: [{
                    text: lang('Cetak'),
                    margin: '5px',
                    scale: 'large',
                    ui: 's-button',
                    cls: 's-blue',
                    handler: function () {
                        var url = m_api + '/grower/cetak_consent_notes';
                        if (Ext.getCmp('cnpetani').getValue() == '') {
                            Ext.MessageBox.show({
                                title: 'Warning',
                                msg: lang('No Farmer Selected'),
                                buttons: Ext.MessageBox.OK,
                                animateTarget: 'mb9',
                                icon: 'ext-mb-info'
                            });
                            return false;
                        }
                        preview_cetak_surat(url + '/' + Ext.getCmp('cnpetani').getValue().join().replace(/,/g, '::')+'/result');
                    }
                },{
                    text: lang('Empty Template'),
                    margin: '5px',
                    scale: 'large',
                    ui: 's-button',
                    cls: 's-blue',
                    //hidden: m_act_printout_consent_notes,
                    hidden: true,
                    handler: function () {
                        var url = m_api + '/grower/cetak_consent_notes';
                        preview_cetak_surat(url + '/null/empty');
                    }
                }]
            }]
        },{
            xtype: 'fieldset',
            title: lang('Withdrawal of Consent Notes'),
            id: 'withdrawal_consent_notes',
            hidden: m_act_printout_withdrawal_consent_notes,
            items: [{
                xtype: 'form',
                padding: 5,
                fieldDefaults: {
                    labelAlign: 'center',
                    labelWidth: 160,
                    anchor: '100%' 
                },
                items: [{
                    layout: 'column',
                    border: false,
                    items: [{
                        columnWidth: 0.15,
                        layout: 'form',
                        padding: 3,
                        border: false,
                        items: [{
                            emptyText: '-- ' + lang('Provinsi') + '--',
                            id: 'wcnProvinsi',
                            name: 'wcnProvinsi',
                            xtype: 'combo',
                            labelWidth: 60,
                            store: mc_Provinsi,
                            displayField: 'label',
                            valueField: 'label',
                            queryMode: 'local',
                            listeners: {
                                change: function (cb, nv, ov) {
                                    mc_Kabupaten.load({params: {key: Ext.getCmp('wcnProvinsi').getValue()}});
                                }
                            }
                        }]
                    }, {
                        columnWidth: 0.15,
                        layout: 'form',
                        padding: 3,
                        border: false,
                        items: [{
                            id: 'wcnKabupaten',
                            name: 'wcnKabupaten',
                            xtype: 'combo',
                            emptyText: '-- ' + lang('Kabupaten') + '--',
                            labelWidth: 70,
                            store: mc_Kabupaten,
                            displayField: 'label',
                            valueField: 'label',
                            queryMode: 'local',
                            listeners: {
                                change: function (cb, nv, ov) {
                                    mc_Kecamatan.load({params: {kab: Ext.getCmp('wcnKabupaten').getValue()}});
                                }
                            }
                        }]
                    }, {
                        columnWidth: 0.2,
                        layout: 'form',
                        padding: 3,
                        border: false,
                        items: [{
                            id: 'wcnKecamatan',
                            name: 'wcnKecamatan',
                            emptyText: '-- ' + lang('Sub-Districts') + '--',
                            xtype: 'combo',
                            labelWidth: 40,
                            store: mc_Kecamatan,
                            displayField: 'label',
                            valueField: 'id',
                            queryMode: 'local',
                            listeners: {
                                change: function (cb, nv, ov) {
                                    mc_Desa.load({params: {kec: Ext.getCmp('wcnKecamatan').getValue()}});
                                }
                            }
                        }]
                    }, {
                        columnWidth: 0.2,
                        layout: 'form',
                        padding: 3,
                        border: false,
                        items: [{
                            id: 'wcnDesa',
                            name: 'wcnDesa',
                            emptyText: '-- ' + lang('Desa') + '--',
                            xtype: 'combo',
                            labelWidth: 40,
                            store: mc_Desa,
                            displayField: 'label',
                            valueField: 'id',
                            queryMode: 'local',
                            listeners: {
                                change: function (cb, nv, ov) {
                                    mc_role_petani.load({params: {desa: Ext.getCmp('wcnDesa').getValue()}});
                                }
                            }
                        }]
                    },{
                        columnWidth: 0.2,
                        layout: 'form',
                        padding: 3,
                        border: false,
                        items: [{
                            id: 'wcnpetani',
                            name: 'wcnpetani',
                            emptyText: '-- ' + lang('Petani') + '--',
                            xtype: 'combo',
                            multiSelect: true,
                            labelWidth: 40,
                            store: mc_role_petani,
                            displayField: 'label',
                            valueField: 'id',
                            queryMode: 'local'
                        }]
                    }]
                }],
                buttons: [{
                    text: lang('Cetak'),
                    margin: '5px',
                    scale: 'large',
                    ui: 's-button',
                    cls: 's-blue',
                    handler: function () {
                        var url = m_api + '/document_survey/cetak_withdrawal_consent_notes';
                        if (Ext.getCmp('wcnpetani').getValue() == '') {
                            Ext.MessageBox.show({
                                title: 'Warning',
                                msg: lang('No Farmer Selected'),
                                buttons: Ext.MessageBox.OK,
                                animateTarget: 'mb9',
                                icon: 'ext-mb-info'
                            });
                            return false;
                        }
                        preview_cetak_surat(url + '/' + Ext.getCmp('wcnpetani').getValue().join().replace(/,/g, '::')+'/result');
                    }
                },{
                    text: lang('Empty Template'),
                    margin: '5px',
                    scale: 'large',
                    ui: 's-button',
                    cls: 's-blue',
                    handler: function () {
                        var url = m_api + '/document_survey/cetak_withdrawal_consent_notes';
                        preview_cetak_surat(url + '/null/empty');
                    }
                }]
            }]
        },{
            xtype: 'fieldset',
            title: lang('SME Profile'),
            id: 'agent_profile',
            hidden: m_act_printout_agent_profile,
            items: [{
                xtype: 'form',
                padding: 5,
                fieldDefaults: {
                    labelAlign: 'center',
                    labelWidth: 160,
                    anchor: '100%'
                },
                items: [{
                    layout: 'column',
                    border: false,
                    items: [{
                        columnWidth: 0.15,
                        layout: 'form',
                        padding: 3,
                        border: false,
                        items: [{
                            emptyText: '-- ' + lang('Provinsi') + '--',
                            id: 'apProvinsi',
                            name: 'apProvinsi',
                            xtype: 'combo',
                            labelWidth: 60,
                            store: mc_Provinsi,
                            displayField: 'label',
                            valueField: 'label',
                            queryMode: 'local',
                            listeners: {
                                change: function (cb, nv, ov) {
                                    mc_Kabupaten.load({params: {key: Ext.getCmp('apProvinsi').getValue()}});
                                }
                            }
                        }]
                    }, {
                        columnWidth: 0.15,
                        layout: 'form',
                        padding: 3,
                        border: false,
                        items: [{
                            id: 'apKabupaten',
                            name: 'apKabupaten',
                            xtype: 'combo',
                            emptyText: '-- ' + lang('Kabupaten') + '--',
                            labelWidth: 70,
                            store: mc_Kabupaten,
                            displayField: 'label',
                            valueField: 'label',
                            queryMode: 'local',
                            listeners: {
                                change: function (cb, nv, ov) {
                                    mc_Kecamatan.load({params: {kab: Ext.getCmp('apKabupaten').getValue()}});
                                }
                            }
                        }]
                    }, {
                        columnWidth: 0.2,
                        layout: 'form',
                        padding: 3,
                        border: false,
                        items: [{
                            id: 'apKecamatan',
                            name: 'apKecamatan',
                            emptyText: '-- ' + lang('Sub-Districts') + '--',
                            xtype: 'combo',
                            labelWidth: 40,
                            store: mc_Kecamatan,
                            displayField: 'label',
                            valueField: 'id',
                            queryMode: 'local',
                            listeners: {
                                change: function (cb, nv, ov) {
                                    mc_Desa.load({params: {kec: Ext.getCmp('apKecamatan').getValue()}});
                                }
                            }
                        }]
                    }, {
                        columnWidth: 0.2,
                        layout: 'form',
                        padding: 3,
                        border: false,
                        items: [{
                            id: 'apDesa',
                            name: 'apDesa',
                            emptyText: '-- ' + lang('Desa') + '--',
                            xtype: 'combo',
                            labelWidth: 40,
                            store: mc_Desa,
                            displayField: 'label',
                            valueField: 'id',
                            queryMode: 'local',
                            listeners: {
                                change: function (cb, nv, ov) {
                                    mc_role_agent.load({params: {desa: Ext.getCmp('apDesa').getValue()}});
                                }
                            }
                        }]
                    },{
                        columnWidth: 0.2,
                        layout: 'form',
                        padding: 3,
                        border: false,
                        items: [{
                            id: 'apAgent',
                            name: 'apAgent',
                            emptyText: '-- ' + lang('SME') + '--',
                            xtype: 'combo',
                            multiSelect: true,
                            labelWidth: 40,
                            store: mc_role_agent,
                            displayField: 'label',
                            valueField: 'id',
                            queryMode: 'local'
                        }]
                    }]
                }],
                buttons: [{
                    text: lang('Cetak'),
                    margin: '5px',
                    scale: 'large',
                    ui: 's-button',
                    cls: 's-blue',
                    handler: function () {
                        var url = m_cetak_agent_profiles;

                        if (Ext.getCmp('apAgent').getValue() == '') {
                            Ext.MessageBox.show({
                                title: 'Warning',
                                msg: lang('No SME Selected'),
                                buttons: Ext.MessageBox.OK,
                                animateTarget: 'mb9',
                                icon: 'ext-mb-error'
                            });
                            return false;
                        }
                        preview_cetak_surat(url + '/MemberID/' + Ext.getCmp('apAgent').getValue().join().replace(/,/g, '::'));
                    }
                }]
            }]
        },{
            xtype: 'fieldset',
            title: lang('Mill Profile'),
            id: 'mill_profile',
            hidden: m_act_printout_mill_profile,
            items: [{
                xtype: 'form',
                padding: 5,
                fieldDefaults: {
                    labelAlign: 'center',
                    labelWidth: 160,
                    anchor: '100%'
                },
                items: [{
                    layout: 'column',
                    border: false,
                    items: [{
                        columnWidth: 0.15,
                        layout: 'form',
                        padding: 3,
                        border: false,
                        items: [{
                            emptyText: '-- ' + lang('Provinsi') + '--',
                            id: 'miProvinsi',
                            name: 'miProvinsi',
                            xtype: 'combo',
                            labelWidth: 60,
                            store: mc_Provinsi,
                            displayField: 'label',
                            valueField: 'label',
                            queryMode: 'local',
                            listeners: {
                                change: function (cb, nv, ov) {
                                    mc_Kabupaten.load({params: {key: Ext.getCmp('miProvinsi').getValue()}});
                                }
                            }
                        }]
                    }, {
                        columnWidth: 0.15,
                        layout: 'form',
                        padding: 3,
                        border: false,
                        items: [{
                            id: 'miKabupaten',
                            name: 'miKabupaten',
                            xtype: 'combo',
                            emptyText: '-- ' + lang('Kabupaten') + '--',
                            labelWidth: 70,
                            store: mc_Kabupaten,
                            displayField: 'label',
                            valueField: 'label',
                            queryMode: 'local',
                            listeners: {
                                change: function (cb, nv, ov) {
                                    mc_Kecamatan.load({params: {kab: Ext.getCmp('miKabupaten').getValue()}});
                                }
                            }
                        }]
                    }, {
                        columnWidth: 0.15,
                        layout: 'form',
                        padding: 3,
                        border: false,
                        items: [{
                            id: 'miKecamatan',
                            name: 'miKecamatan',
                            emptyText: '-- ' + lang('Sub-Districts') + '--',
                            xtype: 'combo',
                            labelWidth: 40,
                            store: mc_Kecamatan,
                            displayField: 'label',
                            valueField: 'id',
                            queryMode: 'local',
                            listeners: {
                                change: function (cb, nv, ov) {
                                    mc_Desa.load({params: {kec: Ext.getCmp('miKecamatan').getValue()}});
                                }
                            }
                        }]
                    }, {
                        columnWidth: 0.15,
                        layout: 'form',
                        padding: 3,
                        border: false,
                        items: [{
                            id: 'miDesa',
                            name: 'miDesa',
                            emptyText: '-- ' + lang('Desa') + '--',
                            xtype: 'combo',
                            labelWidth: 40,
                            store: mc_Desa,
                            displayField: 'label',
                            valueField: 'id',
                            queryMode: 'local',
                            listeners: {
                                change: function (cb, nv, ov) {
                                    mc_mill.load({params: {desa: Ext.getCmp('miDesa').getValue()}});
                                }
                            }
                        }]
                    },{
                        columnWidth: 0.4,
                        layout: 'form',
                        padding: 3,
                        border: false,
                        items: [{
                            id: 'miMill',
                            name: 'miMill',
                            emptyText: '-- ' + lang('Mill') + '--',
                            xtype: 'combo',
                            multiSelect: true,
                            labelWidth: 40,
                            store: mc_mill,
                            displayField: 'label',
                            valueField: 'id',
                            queryMode: 'local'
                        }]
                    }]
                }],
                buttons: [{
                    text: lang('Cetak'),
                    margin: '5px',
                    scale: 'large',
                    ui: 's-button',
                    cls: 's-blue',
                    handler: function () {
                        var url = m_cetak_mill_profiles;

                        if (Ext.getCmp('miMill').getValue() == '') {
                            Ext.MessageBox.show({
                                title: 'Warning',
                                msg: lang('No Mill Selected'),
                                buttons: Ext.MessageBox.OK,
                                animateTarget: 'mb9',
                                icon: 'ext-mb-error'
                            });
                            return false;
                        }
                        preview_cetak_surat(url + '/MillID/' + Ext.getCmp('miMill').getValue().join().replace(/,/g, '::'));
                    }
                }]
            }]
        },{
            xtype: 'fieldset',
            title: lang('Farmer P1/P2'),
            id: 'p1_p2_farmer',
            hidden: m_act_printout_p1_p2,
            items: [{
                xtype: 'form',
                padding: 5,
                fieldDefaults: {
                    labelAlign: 'center',
                    labelWidth: 160,
                    anchor: '100%'
                },
                items: [{
                    layout: 'column',
                    border: false,
                    items: [{
                        columnWidth: 0.15,
                        layout: 'form',
                        padding: 3,
                        border: false,
                        items: [{
                            emptyText: '-- ' + lang('Provinsi') + '--',
                            id: 'p1p2Provinsi',
                            name: 'p1p2Provinsi',
                            xtype: 'combo',
                            labelWidth: 60,
                            store: p1p2_Provinsi,
                            displayField: 'label',
                            valueField: 'label',
                            queryMode: 'local',
                            listeners: {
                                change: function (cb, nv, ov) {
                                    p1p2_Kabupaten.load({params: {key: Ext.getCmp('p1p2Provinsi').getValue()}});
                                }
                            }
                        }]
                    }, {
                        columnWidth: 0.15,
                        layout: 'form',
                        padding: 3,
                        border: false,
                        items: [{
                            id: 'p1p2Kabupaten',
                            name: 'p1p2Kabupaten',
                            xtype: 'combo',
                            emptyText: '-- ' + lang('Kabupaten') + '--',
                            labelWidth: 70,
                            store: p1p2_Kabupaten,
                            displayField: 'label',
                            valueField: 'label',
                            queryMode: 'local',
                            listeners: {
                                change: function (cb, nv, ov) {
                                    p1p2_Kecamatan.load({params: {kab: Ext.getCmp('p1p2Kabupaten').getValue()}});
                                }
                            }
                        }]
                    }, {
                        columnWidth: 0.2,
                        layout: 'form',
                        padding: 3,
                        border: false,
                        items: [{
                            id: 'p1p2Kecamatan',
                            name: 'p1p2Kecamatan',
                            emptyText: '-- ' + lang('Sub-Districts') + '--',
                            xtype: 'combo',
                            labelWidth: 40,
                            store: p1p2_Kecamatan,
                            displayField: 'label',
                            valueField: 'id',
                            queryMode: 'local',
                            listeners: {
                                change: function (cb, nv, ov) {
                                    p1p2_Desa.load({params: {kec: Ext.getCmp('p1p2Kecamatan').getValue()}});
                                }
                            }
                        }]
                    }, {
                        columnWidth: 0.2,
                        layout: 'form',
                        padding: 3,
                        border: false,
                        items: [{
                            id: 'p1p2Desa',
                            name: 'p1p2Desa',
                            emptyText: '-- ' + lang('Desa') + '--',
                            xtype: 'combo',
                            labelWidth: 40,
                            store: p1p2_Desa,
                            displayField: 'label',
                            valueField: 'id',
                            queryMode: 'local',
                            listeners: {
                                change: function (cb, nv, ov) {
                                    p1p2_role_petani.load({params: {desa: Ext.getCmp('p1p2Desa').getValue()}});
                                }
                            }
                        }]
                    },{
                        columnWidth: 0.2,
                        layout: 'form',
                        padding: 3,
                        border: false,
                        items: [{
                            id: 'p1p2Petani',
                            name: 'p1p2Petani',
                            emptyText: '-- ' + lang('Petani') + '--',
                            xtype: 'combo',
                            multiSelect: true,
                            labelWidth: 40,
                            store: p1p2_role_petani,
                            displayField: 'label',
                            valueField: 'id',
                            queryMode: 'local'
                        }]
                    }]
                }],
                buttons: [{
                    text: lang('Cetak'),
                    margin: '5px',
                    scale: 'large',
                    ui: 's-button',
                    cls: 's-blue',
                    handler: function () {
                        var url = m_cetak_p1_p2;
                        if (Ext.getCmp('p1p2Petani').getValue() == '') {
                            Ext.MessageBox.show({
                                title: 'Warning',
                                msg: lang('No Farmer Selected'),
                                buttons: Ext.MessageBox.OK,
                                animateTarget: 'mb9',
                                icon: 'ext-mb-error'
                            });
                            return false;
                        }
                        preview_cetak_surat(url + '/MemberID/' + Ext.getCmp('p1p2Petani').getValue().join().replace(/,/g, '::'));
                        let tempp = url + '/MemberID/' + Ext.getCmp('p1p2Petani').getValue().join().replace(/,/g, '::');
                        console.log(tempp);
                    }
                }]
            }]
        }
        ]
    });

});
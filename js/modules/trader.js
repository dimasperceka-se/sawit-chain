if (Ext.getCmp('winNurseyTraderList')) Ext.getCmp('winNurseyTraderList').destroy();
Ext.Loader.setConfig({
    enabled: true
});
Ext.Loader.setPath('js/ext-4.2.0.663', '../ux');
Ext.require(['Ext.ux.form.ItemSelector']);
Ext.onReady(function() {
    Ext.tip.QuickTipManager.init();
    var store = Ext.create('Ext.data.Store', {
        extend: 'Ext.data.Model',
        fields: ['SupplychainID', 'TraderID', 'TraderName', 'Company', 'Address', 'District'],
        autoLoad: {
            start: 0,
            limit: 50
        },
        pageSize: 50,
        proxy: {
            type: 'ajax',
            url: m_crud + 'data/trader',
            params: {
                'X-API-KEY': '030584'
            },
            reader: {
                type: 'json',
                root: 'data',
                totalProperty: 'total'
            }
        },
        listeners: {
            'beforeload': function(store, options) {
                store.proxy.extraParams.prov = m_param;
                store.proxy.extraParams.kab = m_district;
                store.proxy.extraParams.kec = m_SubDistrictID;
                store.proxy.extraParams.key = Ext.getCmp('key').getValue();
            }
        }
    });
    var store_partner = Ext.create('Ext.data.Store', {
        extend: 'Ext.data.Model',
        autoLoad: true,
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
            extraParams: {
                prov: m_param
            },
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
    //    NURSERY
    var mc_pembeli = Ext.create('Ext.data.Store', {
        fields: ['label'],
        //data: [{'label': lang('Anggota Kelompok')}, {'label': lang('Petani Lain')}, {'label': lang('Traders')}, {'label': lang('Lainnya')}, {'label': lang('Pemerintah')}],
        data: [{
            'label': 'Anggota Kelompok'
        }, {
            'label': 'Petani Lain'
        }, {
            'label': 'Traders'
        }, {
            'label': 'Lainnya'
        }, {
            'label': 'Pemerintah'
        }],
    });

    function displayFormNurseyTrader() {
        if (!winNurseyTrader.isVisible()) {
            Ext.getCmp('dataFormNurseyTrader').getForm().reset();
            winNurseyTrader.center();
            winNurseyTrader.show();
        } else {
            winNurseyTrader.hide(this, function() {});
            winNurseyTrader.toFront();
        }
    }
    var store_nursey = Ext.create('Ext.data.Store', {
        extend: 'Ext.data.Model',
        fields: ['id', 'FarmerPIC', 'Volume', 'DateStarted'],
        proxy: {
            type: 'ajax',
            url: m_crud + 'data/trader',
            reader: {
                type: 'json',
                root: 'data'
            }
        }
    });
    Ext.define('nurseryTransaction.Model', {
        extend: 'Ext.data.Model',
        fields: ['NurseryTransactionID', 'NurseryID', 'Buyer', 'Volume', 'Price', 'Total', 'DateTransaction'],
    });
    var store_nursey_trans = Ext.create('Ext.data.Store', {
        model: 'nurseryTransaction.Model',
        autoLoad: false,
        proxy: {
            type: 'ajax',
            url: m_crud + 'trans',
            reader: {
                type: 'json',
                root: 'data'
            }
        }
    });
    var nRowEditing = Ext.create('Ext.grid.plugin.RowEditing', {
        id: 'nRowEditing',
        clicksToMoveEditor: 0,
        autoCancel: false,
        errorSummary: false,
        clicksToEdit: 2
    });
    // combobox status monitoring action
    function act_nursery_status(val) {
        if (val != 'Tidak Berjalan') {
            Ext.getCmp('mDescription_idtrader').allowBlank = true;
            Ext.getCmp('mDescription_idtrader').getStore().loadData(['']);
        } else {
            Ext.getCmp('mDescription_idtrader').allowBlank = false;
            Ext.getCmp('mDescription_idtrader').getStore().loadData([
                [lang('Masalah air/Penyakit')],
                [lang('Rusak')],
                [lang('Tidak ada pemeliharaan/Konflik anggota kelompok')],
                [lang('Tidak ada pasar penjualan')]
            ]);
        }
    }
    var mRowEditing = Ext.create('Ext.grid.plugin.RowEditing', {
        id: 'mRowEditing',
        clicksToMoveEditor: 0,
        autoCancel: false,
        errorSummary: false,
        clicksToEdit: 2
    });
    // store nursey monitoring
    // model monitoring
    Ext.define('monitoring.Model', {
        extend: 'Ext.data.Model',
        fields: ['id', 'MonitoringDate', 'MonitoringStatus', 'Description'],
    });
    // store nursery monitoring
    var store_nursey_monitoring = Ext.create('Ext.data.Store', {
        model: 'monitoring.Model',
        autoLoad: false,
        proxy: {
            type: 'ajax',
            url: m_store_nursey_monitorings,
            reader: {
                type: 'json',
                root: 'data'
            }
        }
    });
    // store combobox monitoring
    var mc_status_monitoring = Ext.create('Ext.data.Store', {
        fields: ['label'],
        data: [{
            'label': lang('Sedang di bangun/Belum selesai')
        }, {
            'label': lang('Berjalan/Produktif')
        }, {
            'label': lang('Tidak Berjalan')
        }]
    });

    function submitOnEnter(field, event) {
        if (event.getKey() == event.ENTER) {
            store.load({
                params: {
                    page: 1,
                    start: 0,
                    limit: 50
                }
            });
            store.loadPage(1);
        }
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
                    scope: this,
                    handler: function() {
                        Ext.getCmp('panel_staff').enable()
                            //                            Ext.getCmp('panel_kualitas').disable()
                            //                            Ext.getCmp('panel_harga').disable()
                            //                            Ext.getCmp('panel_kemasan').disable()
                        displayFormWindow();
                        hideSave();
                        Ext.getCmp('Provinsi').setValue(m_Province);
                        Ext.getCmp('iphoto').setSrc('');
                        //Ext.getCmp('Provinsi').setValue('');
                        Ext.getCmp('Kabupaten').enable()
                        Ext.getCmp('Kecamatan').disable()
                        Ext.getCmp('Desa').disable()
                    },
                    hidden: !m_act_add
                }, {
                    icon: varjs.config.base_url + 'images/icons/new/update.png',
                    text: lang('Update'),
                    scope: this,
                    handler: function() {
                        displayFormWindow();
                        var sm = Ext.getCmp('grid').getSelectionModel().getSelection()[0];
                        Ext.Ajax.request({
                            url: m_crud + 'data/trader',
                            method: 'GET',
                            params: {
                                id: sm.get('TraderID')
                            },
                            success: function(fp, o) {
                                var r = Ext.decode(fp.responseText);
                                Ext.getCmp('TraderID').setValue(sm.get('TraderID'));
                                fset(r)
                            }
                        });
                        hideSave();
                    },
                    hidden: !m_act_update
                }, {
                    itemId: 'remove',
                    icon: varjs.config.base_url + 'images/icons/new/delete.png',
                    hidden: !m_act_delete,
                    text: lang('Hapus'),
                    scope: this,
                    handler: function() {
                        var smb = Ext.getCmp('grid').getSelectionModel().getSelection()[0];
                        Ext.MessageBox.confirm('Message', lang('Apakah anda mau menghapus data ini ?'), function(btn) {
                            if (btn == 'yes') {
                                Ext.Ajax.request({
                                    waitMsg: lang('Please Wait'),
                                    url: m_crud + 'data',
                                    method: 'DELETE',
                                    params: {
                                        id: smb.raw.TraderID
                                    },
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
                    emptyText: lang('Cari berdasar nama/ID'),
                    listeners: {
                        specialkey: submitOnEnter
                    }
                },
                //                    {
                //                        id: 'sProvinsi',
                //                        name: 'sProvinsi',
                //                        xtype: 'combo',
                //                        store: mc_Provinsi,
                //                        displayField: 'label',
                //                        valueField: 'label',
                //                        queryMode: 'local',
                //                        listeners: {
                //                            change: function(cb, nv, ov) {
                //                                mc_Kabupaten.load({
                //                                    params: {
                //                                        key: Ext.getCmp('sProvinsi').getValue()
                //                                    }});
                //                                Ext.getCmp('sKabupaten').enable();
                //                            }
                //                        }
                //                    },
                // {
                //     id: 'sKabupaten',
                //     name: 'sKabupaten',
                //     xtype: 'combo',
                //     store: mc_Kabupaten,
                //     //value:m_district,
                //     displayField: 'label',
                //     valueField: 'label',
                //     queryMode: 'local',
                //     selectOnFocus: true
                // },
                {
                    xtype: 'button',
                    icon: varjs.config.base_url + 'images/icons/silk/search.png',
                    margin: '0px 0px 0px 6px',
                    text: lang('Search'),
                    handler: function() {
                        store.load({
                            params: {
                                page: 1,
                                start: 0,
                                limit: 50
                            }
                        });
                    }
                }, '->', {
                    xtype: 'button',
                    text: lang('Cetak Form'),
                    handler: function() {
                        //                            var grid = Ext.ComponentQuery.query('grid')[0];
                        //                        var grid = Ext.getCmp('grid');
                        //                            var selectedRecord = grid.getSelectionModel().getSelection()[0];
                        //                            var data = grid.getSelectionModel().getSelection();
                        //                            if (data.length == 0) {
                        //                                displayBeforeCetakKosong();
                        //                            } else {
                        //                                console.log(selectedRecord.data.id);
                        displayBeforeCetak();
                        //                            }
                    }
                }
            ]
        }],
        columns: [{
            text: lang('ID'),
            dataIndex: 'id',
            hidden: true
        }, {
            text: lang('No'),
            xtype: 'rownumberer',
            width: '5%'
        }, {
            text: lang('ID'),
            width: '10%',
            dataIndex: 'TraderID'
        }, {
            text: lang('Trader Name'),
            width: '25%',
            dataIndex: 'TraderName'
        }, {
            text: lang('Company Name'),
            width: '25%',
            dataIndex: 'Company'
        }, {
            text: lang('Alamat'),
            width: '20%',
            dataIndex: 'Address'
        }, {
            text: lang('District'),
            width: '15%',
            dataIndex: 'District'
        }],
        listeners: {
            itemdblclick: function(dv, record, item, index, e) {
                displayFormWindow();
                var sm = record;
                Ext.Ajax.request({
                    url: m_crud + 'data/trader',
                    method: 'GET',
                    params: {
                        id: sm.get('TraderID')
                    },
                    success: function(fp, o) {
                        var r = Ext.decode(fp.responseText);
                        Ext.getCmp('TraderID').setValue(sm.get('TraderID'));
                        fset(r)
                    }
                });
                hideSave();
            }
        }
    });
    var areawindow_nursery_idtrader = Ext.create('widget.window', {
        id: 'areawindow_nursery_idtrader',
        title: lang('Nursery Polygon'),
        closable: false,
        modal: true,
        layout: 'fit',
        closeAction: 'show',
        width: '75%',
        height: 600,
        bodyPadding: 5,
        listeners: {
            close: function(cb, nv, ov) {
                hitung_area_nursery_idtrader();
            }
        },
        buttons: [
            /*{
                        id: 'polygonsaveButton',
                        text: lang('Save'),
                        margin: '5px',
                        scale: 'large',
                        ui: 's-button',
                        cls: 's-blue ' + m_act_save,
                        handler: function() {

                        }
                    },*/
            {
                text: lang('Close'),
                margin: '5px',
                id: 'cLosePolygon_idtrader',
                scale: 'large',
                ui: 's-button',
                cls: 's-grey',
                disabled: false,
                handler: function() {
                    areawindow_nursery_idtrader.hide();
                    hitung_area_nursery_idtrader();
                }
            }
        ]
    });

    function hitung_area_nursery_idtrader() {
        Ext.Ajax.request({
            url: m_crud + 'nursery_polygon_area',
            method: 'GET',
            params: {
                ObjType: 'trader',
                ObjID: Ext.getCmp('TraderID').getValue(),
                NurseryNr: Ext.getCmp('NurseryNr_idtrader').getValue(),
                NurseryID: Ext.getCmp('NurseryID_idtrader').getValue(),
            },
            success: function(fp, o) {
                var r = Ext.decode(fp.responseText);
                //Ext.getCmp('Area_idcoop').setValue(r.Area);
                Ext.getCmp('Latitude_idtrader').setValue(r.Latitude);
                Ext.getCmp('Longitude_idtrader').setValue(r.Longitude);
            }
        })
    }

    function display_area_nursery(nursery_id, nursery_nr) {
        var areaPanel = Ext.getCmp('areawindow_nursery_idtrader');
        areaPanel.center();
        areaPanel.show();
        Ext.Ajax.request({
            url: m_crud + 'nursery_polygon/trader',
            method: 'GET',
            params: {
                NurseryID: nursery_id,
                NurseryNr: nursery_nr,
                lati: Ext.getCmp('Latitude_idtrader').getValue(),
                longi: Ext.getCmp('Longitude_idtrader').getValue(),
                hakAksesPolygon: m_hakakses_polygon
            },
            success: function(response) {
                var htmlText = response.responseText;
                //Get the Panel component using its id
                // update the panel content's with
                // HTML response from Ajax call
                areaPanel.update(htmlText, true);
            }
        });
    }

    var cmb_respon_type = Ext.create('Ext.data.Store', {
        fields: ['id', 'label'],
        data: [
        {
            "id": "staff",
            "label": "Staff"
        }, {
            "id": "other",
            "label": lang("Other")
        }
        ]
    });

    var cmb_respon_id = Ext.create('Ext.data.Store', {
        extend: 'Ext.data.Model',
        fields: ['id', 'label'],
        autoLoad: false,
        proxy: {
            type: 'ajax',
            url: m_api + '/trader/nursery_respon_by_type',
            reader: {
                type: 'json',
                root: 'data'
            }
        },
        listeners: {
            'beforeload': function(store, options) {
                store.proxy.extraParams.responsibleType = Ext.getCmp('ResponsibleType_idtrader').getValue();
                store.proxy.extraParams.TraderID = Ext.getCmp('TraderID').getValue();
            }
        }
    });

    // nursery panel container
    var DataFormNurseyTrader = Ext.create('Ext.panel.Panel', {
        frame: false,
        autoScroll: true,
        height: 475,
        width: '100%',
        bodyPadding: 5,
        id: 'dataFormNurseyTraderWin',
        items:[{
            xtype: 'form',
            id: 'dataFormNurseyTrader',
            fileUpload: true,
            fieldDefaults: {
                labelAlign: 'left',
                labelWidth: 200,
                anchor: '95%'
            },
            items: [{
                layout: 'column',
                border: false,
                items: [{
                    columnWidth: .5,
                    layout: 'form',
                    border: false,
                    padding: 5,
                    items: [{
                        xtype: 'textfield',
                        id: 'NurseryID_idtrader',
                        name: 'NurseryID',
                        hidden: true
                    }, {
                        xtype: 'textfield',
                        id: 'nid_obj_idtrader',
                        name: 'id_obj',
                        hidden: true
                    }, {
                        xtype: 'textfield',
                        id: 'ntype_obj_idtrader',
                        name: 'type_obj',
                        value: 'trader',
                        hidden: true
                    }, /*{
                        xtype: 'textfield',
                        hidden: true,
                        id: 'Responsible_idtrader',
                        name: 'Responsible'
                    },*/{
                        xtype: 'numberfield',
                        fieldLabel: lang('NurseryNr'),
                        id: 'NurseryNr_idtrader',
                        name: 'NurseryNr',
                        allowBlank: false,
                        minValue: 1
                    }, {
                        xtype: 'combo',
                        store: cmb_respon_type,
                        labelWidth: '175',
                        fieldLabel: lang('Responsible Type'),
                        id: 'ResponsibleType_idtrader',
                        name: 'ResponsibleType_idtrader',
                        displayField: 'label',
                        valueField: 'id',
                        queryMode: 'local',
                        allowBlank: false,
                        listeners: {
                            change: function(cb, nv, ov) {
                                if(nv != 'other'){
                                    Ext.getCmp('Responsible_idtrader').setDisabled(false);
                                    Ext.getCmp('ResponsibleName_idtrader').setVisible(false);
                                    Ext.getCmp('ResponsibleBirthday_idtrader').setVisible(false);
                                    Ext.getCmp('ResponsiblePhone_idtrader').setVisible(false);
                                    Ext.getCmp('ResponsibleGender_idtrader').setVisible(false);
                                    Ext.getCmp('divPhotoResponsible_idtrader').setVisible(false);
                                    Ext.getCmp('PhotoResponsible_idtrader').setVisible(false);
                                    cmb_respon_id.load();
                                }else{
                                    Ext.getCmp('Responsible_idtrader').setDisabled(true);
                                    Ext.getCmp('ResponsibleName_idtrader').setVisible(true);
                                    Ext.getCmp('ResponsibleBirthday_idtrader').setVisible(true);
                                    Ext.getCmp('ResponsiblePhone_idtrader').setVisible(true);
                                    Ext.getCmp('ResponsibleGender_idtrader').setVisible(true);
                                    Ext.getCmp('divPhotoResponsible_idtrader').setVisible(true);
                                    Ext.getCmp('PhotoResponsible_idtrader').setVisible(true);
                                }
                            }
                        }
                    },{
                        xtype: 'combo',
                        store: cmb_respon_id,
                        fieldLabel: lang('Penanggung Jawab'),
                        id: 'Responsible_idtrader',
                        name: 'Responsible_idtrader',
                        displayField: 'label',
                        valueField: 'id',
                        queryMode: 'local'
                    },{
                        xtype: 'textfield',
                        fieldLabel: lang('Responsible Name'),
                        id: 'ResponsibleName_idtrader',
                        name: 'ResponsibleName_idtrader',
                        hidden:true
                    },{
                        xtype: 'datefield',
                        fieldLabel: lang('Responsible Birthdate'),
                        id: 'ResponsibleBirthday_idtrader',
                        name: 'ResponsibleBirthday_idtrader',
                        format: 'Y-m-d',
                        hidden:true
                    },{
                        xtype: 'textfield',
                        fieldLabel: lang('Responsible Phone'),
                        id: 'ResponsiblePhone_idtrader',
                        name: 'ResponsiblePhone_idtrader',
                        hidden:true
                    },{
                        xtype: 'radiogroup',
                        fieldLabel: lang('Responsible Gender'),
                        id:'ResponsibleGender_idtrader',
                        hidden:true,
                        items: [{
                            name: 'ResponsibleGender_idtrader',
                            id: 'ResponsibleGenderM_idtrader',
                            boxLabel: lang('Male'),
                            inputValue: 'm'
                        }, {
                            name: 'ResponsibleGender_idtrader',
                            id: 'ResponsibleGenderF_idtrader',
                            boxLabel: lang('Female'),
                            inputValue: 'f'
                        }]
                    },{
                        layout:'column',
                        border:false,
                        style:'margin-bottom:5px;margin-right:-5px;',
                        id:'divPhotoResponsible_idtrader',
                        hidden:true,
                        items:[{
                            columnWidth: 1,
                            border: false,
                            layout:{
                                type:'hbox',
                                pack:'end'
                            },
                            items:[{
                                xtype: 'image',
                                id: 'iphotoResponsible_idtrader',
                                width: '150px',
                                height:'150px',
                                src: m_api_base_url + '/images/Photo/no-user.jpg'
                            },{
                                xtype: 'textfield',
                                id: 'Photo_old_responsible_idtrader',
                                name: 'Photo_old_responsible_idtrader',
                                inputType: 'hidden'
                            }]
                        }]
                    },{
                        xtype: 'fileuploadfield',
                        fieldLabel: lang('Photo'),
                        labelWidth: 130,
                        id: 'PhotoResponsible_idtrader',
                        name: 'PhotoResponsible_idtrader',
                        buttonText: 'Browse',
                        hidden:true,
                        listeners: {
                            'change': function (fb, v) {
                                var form = Ext.getCmp('dataFormNurseyTrader').getForm();
                                form.submit({
                                    url: m_api + '/trader/nursery_form_photo_responsible',
                                    clientValidation: false,
                                    waitMsg: 'Sending Photo...',
                                    success: function (fp, o) {
                                        Ext.getCmp('iphotoResponsible_idtrader').setSrc(m_api_base_url + '/images/photo_responsible/' + o.result.file);
                                        Ext.getCmp('Photo_old_responsible_idtrader').setValue(o.result.file);
                                    }
                                });
                            }
                        }
                    },/*{
                        xtype: 'textfield',
                        fieldLabel: lang('Penanggung Jawab'),
                        id: 'NamaResponsible_idtrader',
                        name: 'NamaResponsible',
                        readOnly: true
                    },*/{
                        xtype: 'datefield',
                        fieldLabel: lang('Tanggal Berdiri'),
                        id: 'Established_idtrader',
                        name: 'Established',
                        format: 'Y-m-d'
                    }, {
                        xtype: 'radiogroup',
                        fieldLabel: lang('Certification Status'),
                        items: [{
                            name: 'CertificationStatus',
                            id: 'CertificationStatus1_idtrader',
                            boxLabel: lang('Yes, BP2MB'),
                            inputValue: 'Yes'
                        }, {
                            name: 'CertificationStatus',
                            id: 'CertificationStatus2_idtrader',
                            boxLabel: lang('Tidak'),
                            inputValue: 'No',
                            // checked: true,
                        }],
                        listeners: {
                            change: function(cb, nv, ov) {
                                if (Ext.getCmp('CertificationStatus1_idtrader').getValue() == true) {
                                    Ext.getCmp('DateCertification_idtrader').setDisabled(false);
                                    Ext.getCmp('DateAppliedCertification_idtrader').setDisabled(false);
                                } else {
                                    Ext.getCmp('DateCertification_idtrader').setDisabled(true);
                                    Ext.getCmp('DateCertification_idtrader').setValue('');
                                    Ext.getCmp('DateAppliedCertification_idtrader').setDisabled(true);
                                    Ext.getCmp('DateAppliedCertification_idtrader').setValue('');
                                }
                            }
                        }
                    }, {
                        xtype: 'datefield',
                        fieldLabel: lang('Date of Certification Status'),
                        id: 'DateCertification_idtrader',
                        name: 'DateCertification',
                        format: 'Y-m-d'
                    }, {
                        xtype: 'datefield',
                        fieldLabel: lang('Date Applied for Certification'),
                        id: 'DateAppliedCertification_idtrader',
                        name: 'DateAppliedCertification',
                        format: 'Y-m-d'
                    },{
                        xtype: 'button',
                        margin: '0',
                        width:'150px',
                        id: 'buttonPrintNurseryProfile',
                        text: lang('Print Nursery Profile'),
                        handler: function() {
                            if (Ext.getCmp('NurseryID_idtrader').getValue() == '') {
                                Ext.MessageBox.alert('Warning', 'Please save Nursery first!');
                            }else{
                                var urlPrint = m_api + '/nursery/cetak_nursery_summary/trader/'+Ext.getCmp('TraderID').getValue()+'/'+Ext.getCmp('NurseryNr_idtrader').getValue()+'/';
                                preview_cetak_surat(urlPrint);
                            }
                        }
                    }]
                }, {
                    columnWidth: .5,
                    layout: 'form',
                    border: false,
                    padding: 5,
                    items: [{
                            layout: 'column',
                            border: false,
                            items: [{
                                columnWidth: 1,
                                layout: 'form',
                                border: false,
                                //padding: 5,
                                items: [{
                                    xtype: 'textfield',
                                    fieldLabel: lang('Panjang (m)'),
                                    id: 'Panjang_idtrader',
                                    name: 'Panjang',
                                    fieldCls: 'classuang',
                                    listeners: {
                                        change: function(cb, nv, ov) {
                                            Ext.getCmp('Luas_idtrader').setValue(nnumber_format(nnumber_format(Ext.getCmp('Panjang_idtrader').getValue(), 2) * nnumber_format(Ext.getCmp('Lebar_idtrader').getValue(), 2)))
                                        }
                                    }
                                }]
                            }, {
                                columnWidth: 1,
                                layout: 'form',
                                border: false,
                                //padding: 5,
                                items: [{
                                    xtype: 'textfield',
                                    fieldLabel: lang('Lebar (m)'),
                                    id: 'Lebar_idtrader',
                                    margin: '0 0 0 10',
                                    name: 'Lebar',
                                    listeners: {
                                        change: function(cb, nv, ov) {
                                            Ext.getCmp('Luas_idtrader').setValue(nnumber_format(nnumber_format(Ext.getCmp('Panjang_idtrader').getValue(), 2) * nnumber_format(Ext.getCmp('Lebar_idtrader').getValue(), 2)))
                                        }
                                    }
                                }]
                            }]
                        }, {
                            xtype: 'textfield',
                            fieldLabel: lang('Luas (m2)'),
                            id: 'Luas_idtrader',
                            name: 'Luas',
                            readOnly: true,
                            listeners: {
                                change: function(cb, nv, ov) {
                                    Ext.getCmp('Kapasitas_idtrader').setValue(nnumber_format(nnumber_format(Ext.getCmp('Luas_idtrader').getValue(), 2) * 40))
                                }
                            }
                        }, {
                            xtype: 'textfield',
                            fieldLabel: lang('Kapasitas (Luas (m2) x 40)'),
                            id: 'Kapasitas_idtrader',
                            name: 'Kapasitas',
                            labelWidth: 160,
                            readOnly: true
                        }, {
                            xtype: 'textfield',
                            fieldLabel: lang('Latitude (Dec)'),
                            id: 'Latitude_idtrader',
                            name: 'Latitude',
                            readOnly: m_hakakses_lat_short
                        }, {
                            xtype: 'textfield',
                            fieldLabel: lang('Longitude (Dec)'),
                            id: 'Longitude_idtrader',
                            name: 'Longitude',
                            readOnly: m_hakakses_long_short
                        }, {
                            items: [{
                                layout: 'column',
                                items: [{
                                    html: lang('Map Area'),
                                    //hidden: true
                                }, {
                                    items: [{
                                        xtype: 'button',
                                        margin: '0 0 0 148',
                                        id: 'buttonShowPolygonNursery_idtrader',
                                        text: lang('Show Polygon'),
                                        handler: function() {
                                            if (Ext.getCmp('NurseryID_idtrader').getValue() == '') {
                                                Ext.MessageBox.alert('Warning', 'Please save Nursery first!');
                                            } else {
                                                display_area_nursery(Ext.getCmp('NurseryID_idtrader').getValue(), Ext.getCmp('NurseryNr_idtrader').getValue());
                                            }
                                        },
                                        //hidden: true
                                    }]
                                }]
                            }]
                        },{
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
                                    id: 'iphoto_idtrader',
                                    width: '150px',
                                    height:'150px',
                                    src: m_api_base_url + '/images/nursery/no-image.png'
                                },{
                                    xtype: 'textfield',
                                    id: 'Photo_old_idtrader',
                                    name: 'Photo_old_idtrader',
                                    inputType: 'hidden'
                                }]
                            }]
                        },{
                            xtype: 'fileuploadfield',
                            fieldLabel: lang('Photo'),
                            id: 'Photo_idtrader',
                            name: 'Photo_idtrader',
                            buttonText: 'Browse',
                            listeners: {
                                'change': function (fb, v) {
                                    var form = Ext.getCmp('dataFormNurseyTrader').getForm();
                                    form.submit({
                                        url: m_api + '/trader/nursery_form_photo',
                                        clientValidation: false,
                                        waitMsg: 'Sending Photo...',
                                        success: function (fp, o) {
                                            Ext.getCmp('iphoto_idtrader').setSrc(m_api_base_url + '/images/nursery/' + o.result.file);
                                            Ext.getCmp('Photo_old_idtrader').setValue(o.result.file);
                                        }
                                    });
                                }
                            }
                        }
                    ]
                }]
            }, {
                xtype: 'tabpanel',
                flex: 1,
                margin: 2,
                activeTab: 0,
                plain: true,
                items: [{ // grid nursery penjualan
                    xtype: 'gridpanel',
                    title: lang('Nursery Penjualan'),
                    id: 'gnurseypenjualan_idtrader',
                    style: 'border:1px solid #CCC;',
                    store: store_nursey_trans,
                    width: '100%',
                    height: 500,
                    loadMask: true,
                    selType: 'rowmodel',
                    minHeight: 190,
                    dockedItems: [{
                        xtype: 'toolbar',
                        items: [{
                            icon: varjs.config.base_url + 'images/icons/new/add.png', cls:'Sfr_BtnGridGreen', overCls:'Sfr_BtnGridGreen-Hover',
                            hidden: !m_act_add,
                            text: lang('Add'),
                            scope: this,
                            handler: function() {
                                if (Ext.getCmp('NurseryID_idtrader').getValue() == '') {
                                    Ext.MessageBox.alert('Warning', 'Please save Nursery first!');
                                } else {
                                    nRowEditing.cancelEdit();
                                    var r = Ext.create('nurseryTransaction.Model', {
                                        NurseryTransactionID: '',
                                        Buyer: '',
                                        Volume: '',
                                        Price: '',
                                        Total: '',
                                        DateTransaction: ''
                                    });
                                    store_nursey_trans.insert(0, r);
                                    nRowEditing.startEdit(0, 0);
                                    uang(document.getElementById('nvol_idtrader'))
                                }
                            }
                        }, {
                            icon: varjs.config.base_url + 'images/icons/new/update.png',
                            hidden: !m_act_update,
                            text: lang('Update'),
                            scope: this,
                            handler: function() {
                                nRowEditing.cancelEdit();
                                var sm = Ext.getCmp('gnurseypenjualan_idtrader').getSelectionModel().getSelection();
                                nRowEditing.startEdit(sm[0].index, 0);
                            }
                        }, {
                            itemId: 'remove',
                            icon: varjs.config.base_url + 'images/icons/new/delete.png',
                            hidden: !m_act_delete,
                            text: lang('Delete'),
                            scope: this,
                            handler: function() {
                                var smb = Ext.getCmp('gnurseypenjualan_idtrader').getSelectionModel().getSelection()[0];
                                nRowEditing.cancelEdit();
                                Ext.MessageBox.confirm('Message', lang('Apakah anda mau menghapus data ini ?'), function(btn) {
                                    if (btn == 'yes') {
                                        Ext.Ajax.request({
                                            waitMsg: lang('Please Wait'),
                                            url: m_crud + 'transaction',
                                            method: 'DELETE',
                                            params: {
                                                id: smb.raw.NurseryTransactionID
                                            },
                                            success: function(response, opts) {
                                                var obj = Ext.decode(response.responseText);
                                                switch (obj.success) {
                                                    case true:
                                                        store_nursey_trans.load({
                                                            params: {
                                                                id: Ext.getCmp('NurseryID_idtrader').getValue()
                                                            }
                                                        });
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
                        text: lang('NurseryTransactionID'),
                        dataIndex: 'NurseryTransactionID',
                        hidden: true
                    }, {
                        text: lang('NurseryID'),
                        dataIndex: 'NurseryID',
                        hidden: true
                    }, {
                        text: lang('No'),
                        xtype: 'rownumberer',
                        width: '5%'
                    }, {
                        text: lang('Pembeli'),
                        dataIndex: 'Buyer',
                        width: '20%',
                        editor: {
                            xtype: 'combo',
                            store: mc_pembeli,
                            displayField: 'label',
                            valueField: 'label',
                            queryMode: 'local',
                            allowBlank: false
                        }
                    }, {
                        text: lang('Bibit Dijual'),
                        dataIndex: 'Volume',
                        width: '15%',
                        editor: {
                            xtype: 'textfield',
                            id: 'nvol_idtrader',
                            allowBlank: false,
                            listeners: {
                                change: function() {
                                    Ext.getCmp('ntot_idtrader').setValue(Ext.getCmp('nvol_idtrader').getValue() * Ext.getCmp('npri_idtrader').getValue());
                                }
                            }
                        }
                    }, {
                        text: lang('Harga Satuan'),
                        dataIndex: 'Price',
                        width: '15%',
                        editor: {
                            xtype: 'textfield',
                            id: 'npri_idtrader',
                            allowBlank: false,
                            listeners: {
                                change: function() {
                                    Ext.getCmp('ntot_idtrader').setValue(Ext.getCmp('nvol_idtrader').getValue() * Ext.getCmp('npri_idtrader').getValue());
                                }
                            }
                        }
                    }, {
                        text: lang('Total'),
                        dataIndex: 'Total',
                        width: '15%',
                        editor: {
                            xtype: 'textfield',
                            allowBlank: false,
                            id: 'ntot_idtrader',
                            readOnly: true
                        }
                    }, {
                        text: lang('Tanggal Transaksi'),
                        dataIndex: 'DateTransaction',
                        format: 'Y-m-d',
                        width: '28%',
                        editor: {
                            xtype: 'datefield',
                            format: 'Y-m-d',
                            allowBlank: false
                        }
                    }],
                    plugins: [nRowEditing],
                    listeners: {
                        itemdblclick: function(dv, record, item, index, e) {
                            if (!m_act_update) {
                                nRowEditing.cancelEdit();
                            }
                        },
                        'canceledit': function(editor, e, eOpts) {
                            store_nursey_trans.load({
                                params: {
                                    id: Ext.getCmp('NurseryID_idtrader').getValue()
                                }
                            });
                        },
                        'edit': function(editor, e) {
                            if (e.record.data.NurseryTransactionID == '') {
                                Ext.Ajax.request({
                                    waitMsg: lang('Please wait...'),
                                    url: m_crud + 'transaction',
                                    method: 'POST',
                                    params: {
                                        id_nursey: Ext.getCmp('NurseryID_idtrader').getValue(),
                                        Buyer: e.record.data.Buyer,
                                        Volume: e.record.data.Volume,
                                        Price: e.record.data.Price,
                                        Total: e.record.data.Totel,
                                        DateTransaction: e.record.data.DateTransaction
                                    },
                                    success: function(response, opts) {
                                        var obj = Ext.decode(response.responseText);
                                        switch (obj.success) {
                                            case true:
                                                Ext.MessageBox.alert('Success', obj.message);
                                                store_nursey_trans.load({
                                                    params: {
                                                        id: Ext.getCmp('NurseryID_idtrader').getValue()
                                                    }
                                                });
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
                                Ext.MessageBox.confirm('Message', lang('Update data ini ?'), function(btn) {
                                    if (btn == 'yes') {
                                        Ext.Ajax.request({
                                            waitMsg: lang('Please wait...'),
                                            url: m_crud + 'transaction',
                                            method: 'PUT',
                                            params: {
                                                id: e.record.data.NurseryTransactionID,
                                                id_nursey: Ext.getCmp('NurseryID_idtrader').getValue(),
                                                Buyer: e.record.data.Buyer,
                                                Volume: e.record.data.Volume,
                                                Price: e.record.data.Price,
                                                Total: e.record.data.Totel,
                                                DateTransaction: e.record.data.DateTransaction
                                            },
                                            success: function(response, opts) {
                                                var obj = Ext.decode(response.responseText);
                                                switch (obj.success) {
                                                    case true:
                                                        Ext.MessageBox.alert('Success', obj.message);
                                                        store_nursey_trans.load({
                                                            params: {
                                                                id: Ext.getCmp('NurseryID_idtrader').getValue()
                                                            }
                                                        });
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
                }, { // tab nursery monitoring
                    xtype: 'gridpanel',
                    title: lang('Nursery Monitoring'),
                    id: 'gnurseymonitoring_idtrader',
                    style: 'border:1px solid #CCC;',
                    store: store_nursey_monitoring,
                    width: '100%',
                    loadMask: true,
                    selType: 'rowmodel',
                    minHeight: 190,
                    dockedItems: [{
                        xtype: 'toolbar',
                        items: [{
                            icon: varjs.config.base_url + 'images/icons/new/add.png', cls:'Sfr_BtnGridGreen', overCls:'Sfr_BtnGridGreen-Hover',
                            hidden: !m_act_add,
                            text: lang('Add'),
                            scope: this,
                            handler: function() {
                                if (Ext.getCmp('NurseryID_idtrader').getValue() == '') {
                                    Ext.MessageBox.alert('Warning', 'Please save Nursery first!');
                                } else {
                                    mRowEditing.cancelEdit();
                                    var r = Ext.create('monitoring.Model', {
                                        id: '',
                                        MonitoringDate: '',
                                        MonitoringStatus: '',
                                        Description: ''
                                    });
                                    store_nursey_monitoring.insert(0, r);
                                    mRowEditing.startEdit(0, 0);
                                }
                            }
                        }, {
                            icon: varjs.config.base_url + 'images/icons/new/update.png',
                            hidden: !m_act_update,
                            text: lang('Update'),
                            scope: this,
                            handler: function() {
                                mRowEditing.cancelEdit();
                                var sm = Ext.getCmp('gnurseymonitoring_idtrader').getSelectionModel().getSelection();
                                mRowEditing.startEdit(sm[0].index, 0);
                                act_nursery_status(Ext.getCmp('mStatus_idtrader').getValue());
                            }
                        }, {
                            itemId: 'remove',
                            icon: varjs.config.base_url + 'images/icons/new/delete.png',
                            hidden: !m_act_delete,
                            text: lang('Delete'),
                            scope: this,
                            handler: function() {
                                var smb = Ext.getCmp('gnurseymonitoring_idtrader').getSelectionModel().getSelection()[0];
                                mRowEditing.cancelEdit();
                                Ext.MessageBox.confirm('Message', lang('Apakah anda mau menghapus data ini ?'), function(btn) {
                                    if (btn == 'yes') {
                                        Ext.Ajax.request({
                                            waitMsg: lang('Please Wait'),
                                            url: m_nursey + '_monitorings',
                                            method: 'DELETE',
                                            params: {
                                                id: smb.raw.id
                                            },
                                            success: function(response, opts) {
                                                var obj = Ext.decode(response.responseText);
                                                switch (obj.success) {
                                                    case true:
                                                        store_nursey_monitoring.load({
                                                            params: {
                                                                nursery_id: Ext.getCmp('NurseryID_idtrader').getValue()
                                                            }
                                                        });
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
                        text: lang('ID'),
                        dataIndex: 'id',
                        hidden: true
                    }, {
                        text: lang('No'),
                        xtype: 'rownumberer',
                        width: '5%'
                    }, {
                        text: lang('Tanggal Kedatangan'),
                        dataIndex: 'MonitoringDate',
                        width: '15%',
                        editor: {
                            xtype: 'datefield',
                            id: 'mDate',
                            format: 'Y-m-d',
                            allowBlank: false
                        }
                    }, {
                        text: lang('Status'),
                        dataIndex: 'MonitoringStatus',
                        width: '20%',
                        editor: {
                            xtype: 'combo',
                            id: 'mStatus_idtrader',
                            store: mc_status_monitoring,
                            displayField: 'label',
                            valueField: 'label',
                            queryMode: 'local',
                            allowBlank: false,
                            listeners: {
                                change: function(combo, selection) {
                                    Ext.getCmp('mDescription_idtrader').setValue('');
                                    act_nursery_status(Ext.getCmp('mStatus_idtrader').getValue());
                                }
                            }
                        }
                    }, {
                        text: lang('Keterangan'),
                        dataIndex: 'Description',
                        width: '59%',
                        editor: {
                            xtype: 'combo',
                            id: 'mDescription_idtrader',
                            allowBlank: true,
                            store: [''],
                            hideTrigger: false,
                            listeners: {
                                beforequery: function(record) {
                                    record.query = new RegExp(record.query, 'i');
                                    record.forceAll = true;
                                }
                            }
                        }
                    }],
                    plugins: [mRowEditing],
                    listeners: {
                        itemdblclick: function(dv, record, item, index, e) {
                            if (!m_act_update) {
                                mRowEditing.cancelEdit();
                            }
                        },
                        'canceledit': function(editor, e, eOpts) {
                            store_nursey_monitoring.load({
                                params: {
                                    nursery_id: Ext.getCmp('NurseryID_idtrader').getValue()
                                }
                            });
                        },
                        'edit': function(editor, e) {
                            if (Ext.getCmp('NurseryID_idtrader').getValue() == '' || Ext.getCmp('NurseryID_idtrader').getValue() == undefined) {
                                Ext.Msg.alert("Alert", 'Belum ada data nursery');
                            } else {
                                if (e.record.data.id == '') {
                                    Ext.Ajax.request({
                                        waitMsg: lang('Please wait...'),
                                        url: m_nursey + '_monitorings',
                                        method: 'POST',
                                        params: {
                                            id_nursey: Ext.getCmp('NurseryID_idtrader').getValue(),
                                            MonitoringDate: e.record.data.MonitoringDate,
                                            MonitoringStatus: e.record.data.MonitoringStatus,
                                            Description: e.record.data.Description
                                        },
                                        success: function(response, opts) {
                                            var obj = Ext.decode(response.responseText);
                                            switch (obj.success) {
                                                case true:
                                                    Ext.MessageBox.alert('Success', obj.message);
                                                    store_nursey_monitoring.load({
                                                        params: {
                                                            nursery_id: Ext.getCmp('NurseryID_idtrader').getValue()
                                                        }
                                                    });
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
                                    Ext.MessageBox.confirm('Message', lang('Update data ini ?'), function(btn) {
                                        if (btn == 'yes') {
                                            Ext.Ajax.request({
                                                waitMsg: lang('Please wait...'),
                                                url: m_nursey + '_monitorings',
                                                method: 'PUT',
                                                params: {
                                                    id: e.record.data.id,
                                                    id_nursey: Ext.getCmp('NurseryID_idtrader').getValue(),
                                                    MonitoringDate: e.record.data.MonitoringDate,
                                                    MonitoringStatus: e.record.data.MonitoringStatus,
                                                    Description: e.record.data.Description
                                                },
                                                success: function(response, opts) {
                                                    var obj = Ext.decode(response.responseText);
                                                    switch (obj.success) {
                                                        case true:
                                                            Ext.MessageBox.alert('Success', obj.message);
                                                            store_nursey_monitoring.load({
                                                                params: {
                                                                    nursery_id: Ext.getCmp('NurseryID_idtrader').getValue()
                                                                }
                                                            });
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
                        },
                        itemdblclick: function() {
                            act_nursery_status(Ext.getCmp('mStatus_idtrader').getValue());
                        }
                    }
                },{
                    //tab nursery checklist
                    xtype: 'panel',
                    autoScroll: true,
                    width:'100%',
                    minHeight: 200,
                    title: lang('Nursery Checklist'),
                    padding: 3,
                    items:[{
                        layout:'column',
                        width:'100%',
                        border:false,
                        items:[{
                            columnWidth: 0.05,
                            padding: 2,
                            items:[{
                                xtype: 'label',
                                style:'font-weight:bold;font-size:11px;',
                                text: 'No'
                            }]
                        },{
                            columnWidth: 0.5,
                            padding: 2,
                            items:[{
                                xtype: 'label',
                                style:'font-weight:bold;font-size:11px;',
                                text: lang('Key Quality Attribute')
                            }]
                        },{
                            columnWidth: 0.15,
                            padding: 2,
                            items:[{
                                xtype: 'label',
                                style:'font-weight:bold;font-size:11px;',
                                text: lang('Yes / No')
                            }]
                        },{
                            columnWidth: 0.3,
                            padding: 2,
                            items:[{
                                xtype: 'label',
                                style:'font-weight:bold;font-size:11px;',
                                text: lang('If No, Justification')
                            }]
                        }]
                    },{
                        layout:'column',
                        width:'100%',
                        border:false,
                        items:[{
                            columnWidth: 0.05,
                            padding: 2,
                            items:[{
                                xtype: 'label',
                                style:'font-size:11px;line-height:31px;',
                                text: lang('1.')
                            }]
                        },{
                            columnWidth: 0.5,
                            padding: 2,
                            items:[{
                                xtype: 'label',
                                style:'font-size:11px;line-height:31px;',
                                text: lang('Location with good access to main roads')
                            }]
                        },{
                            columnWidth: 0.15,
                            padding: 2,
                            items:[{
                                xtype: 'radiogroup',
                                width: '100%',
                                items:[{
                                    boxLabel: lang('Yes'),
                                    id: 'LocationCloseToCommunity1',
                                    name: 'LocationCloseToCommunity',
                                    style:'font-size:11px;',
                                    inputValue: '1',
                                    listeners:{
                                        change: function(){
                                            return false;
                                        }
                                    }
                                },{
                                    boxLabel: lang('No'),
                                    id: 'LocationCloseToCommunity2',
                                    name: 'LocationCloseToCommunity',
                                    style:'font-size:11px;',
                                    inputValue: '2',
                                    listeners:{
                                        change: function(){
                                            return false;
                                        }
                                    }
                                }]
                            }]
                        },{
                            columnWidth: 0.3,
                            padding: 2,
                            items:[{
                                xtype: 'textfield',
                                width:'100%',
                                id: 'LocationCloseToCommunityNo',
                                name: 'LocationCloseToCommunityNo',
                                fieldStyle: {
                                    'fontSize' : '11px',
                                    'margin': '3px 0'
                                }
                            }]
                        }]
                    },{
                        layout:'column',
                        width:'100%',
                        border:false,
                        items:[{
                            columnWidth: 0.05,
                            padding: 2,
                            items:[{
                                xtype: 'label',
                                style:'font-size:11px;line-height:31px;',
                                text: lang('2.')
                            }]
                        },{
                            columnWidth: 0.5,
                            padding: 2,
                            items:[{
                                xtype: 'label',
                                style:'font-size:11px;line-height:31px;',
                                text: lang('Flat, well drained and uniform land area')
                            }]
                        },{
                            columnWidth: 0.15,
                            padding: 2,
                            items:[{
                                xtype: 'radiogroup',
                                width: '100%',
                                items:[{
                                    boxLabel: lang('Yes'),
                                    id: 'GoodLandArea1',
                                    name: 'GoodLandArea',
                                    style:'font-size:11px;',
                                    inputValue: '1',
                                    listeners:{
                                        change: function(){
                                            return false;
                                        }
                                    }
                                },{
                                    boxLabel: lang('No'),
                                    id: 'GoodLandArea2',
                                    name: 'GoodLandArea',
                                    style:'font-size:11px;',
                                    inputValue: '2',
                                    listeners:{
                                        change: function(){
                                            return false;
                                        }
                                    }
                                }]
                            }]
                        },{
                            columnWidth: 0.3,
                            padding: 2,
                            items:[{
                                xtype: 'textfield',
                                id: 'GoodLandAreaNo',
                                name: 'GoodLandAreaNo',
                                width:'100%',
                                fieldStyle: {
                                    'fontSize' : '11px',
                                    'margin': '3px 0',
                                    'width': '100%'
                                }
                            }]
                        }]
                    },{
                        layout:'column',
                        width:'100%',
                        border:false,
                        items:[{
                            columnWidth: 0.05,
                            padding: 2,
                            items:[{
                                xtype: 'label',
                                style:'font-size:11px;line-height:31px;',
                                text: lang('3.')
                            }]
                        },{
                            columnWidth: 0.5,
                            padding: 2,
                            items:[{
                                xtype: 'label',
                                style:'font-size:11px;line-height:31px;',
                                text: lang('Located at least 100 metres from cocoa plantations')
                            }]
                        },{
                            columnWidth: 0.15,
                            padding: 2,
                            items:[{
                                xtype: 'radiogroup',
                                width: '100%',
                                items:[{
                                    boxLabel: lang('Yes'),
                                    id: 'LocationNearCocoaFarm1',
                                    name: 'LocationNearCocoaFarm',
                                    style:'font-size:11px;',
                                    inputValue: '1',
                                    listeners:{
                                        change: function(){
                                            return false;
                                        }
                                    }
                                },{
                                    boxLabel: lang('No'),
                                    id: 'LocationNearCocoaFarm2',
                                    name: 'LocationNearCocoaFarm',
                                    style:'font-size:11px;',
                                    inputValue: '2',
                                    listeners:{
                                        change: function(){
                                            return false;
                                        }
                                    }
                                }]
                            }]
                        },{
                            columnWidth: 0.3,
                            padding: 2,
                            items:[{
                                xtype: 'textfield',
                                id: 'LocationNearCocoaFarmNo',
                                name: 'LocationNearCocoaFarmNo',
                                width:'100%',
                                fieldStyle: {
                                    'fontSize' : '11px',
                                    'margin': '3px 0',
                                    'width': '100%'
                                }
                            }]
                        }]
                    },{
                        layout:'column',
                        width:'100%',
                        border:false,
                        items:[{
                            columnWidth: 0.05,
                            padding: 2,
                            items:[{
                                xtype: 'label',
                                style:'font-size:11px;line-height:31px;',
                                text: lang('4.')
                            }]
                        },{
                            columnWidth: 0.5,
                            padding: 2,
                            items:[{
                                xtype: 'label',
                                style:'font-size:11px;line-height:31px;',
                                text: lang('Continuous water supply available')
                            }]
                        },{
                            columnWidth: 0.15,
                            padding: 2,
                            items:[{
                                xtype: 'radiogroup',
                                width: '100%',
                                items:[{
                                    boxLabel: lang('Yes'),
                                    id: 'ContinuousWaterSupply1',
                                    name: 'ContinuousWaterSupply',
                                    style:'font-size:11px;',
                                    inputValue: '1',
                                    listeners:{
                                        change: function(){
                                            return false;
                                        }
                                    }
                                },{
                                    boxLabel: lang('No'),
                                    id: 'ContinuousWaterSupply2',
                                    name: 'ContinuousWaterSupply',
                                    style:'font-size:11px;',
                                    inputValue: '2',
                                    listeners:{
                                        change: function(){
                                            return false;
                                        }
                                    }
                                }]
                            }]
                        },{
                            columnWidth: 0.3,
                            padding: 2,
                            items:[{
                                xtype: 'textfield',
                                id: 'ContinuousWaterSupplyNo',
                                name: 'ContinuousWaterSupplyNo',
                                width:'100%',
                                fieldStyle: {
                                    'fontSize' : '11px',
                                    'margin': '3px 0',
                                    'width': '100%'
                                }
                            }]
                        }]
                    },{
                        layout:'column',
                        width:'100%',
                        border:false,
                        items:[{
                            columnWidth: 0.05,
                            padding: 2,
                            items:[{
                                xtype: 'label',
                                style:'font-size:11px;line-height:31px;',
                                text: lang('5.')
                            }]
                        },{
                            columnWidth: 0.5,
                            padding: 2,
                            items:[{
                                xtype: 'label',
                                style:'font-size:11px;line-height:31px;',
                                text: lang('Irrigation system installed')
                            }]
                        },{
                            columnWidth: 0.15,
                            padding: 2,
                            items:[{
                                xtype: 'radiogroup',
                                width: '100%',
                                items:[{
                                    boxLabel: lang('Yes'),
                                    id: 'IrrigationInstalled1',
                                    name: 'IrrigationInstalled',
                                    style:'font-size:11px;',
                                    inputValue: '1',
                                    listeners:{
                                        change: function(){
                                            return false;
                                        }
                                    }
                                },{
                                    boxLabel: lang('No'),
                                    id: 'IrrigationInstalled2',
                                    name: 'IrrigationInstalled',
                                    style:'font-size:11px;',
                                    inputValue: '2',
                                    listeners:{
                                        change: function(){
                                            return false;
                                        }
                                    }
                                }]
                            }]
                        },{
                            columnWidth: 0.3,
                            padding: 2,
                            items:[{
                                xtype: 'textfield',
                                id: 'IrrigationInstalledNo',
                                name: 'IrrigationInstalledNo',
                                width:'100%',
                                fieldStyle: {
                                    'fontSize' : '11px',
                                    'margin': '3px 0',
                                    'width': '100%'
                                }
                            }]
                        }]
                    },{
                        layout:'column',
                        width:'100%',
                        border:false,
                        items:[{
                            columnWidth: 0.05,
                            padding: 2,
                            items:[{
                                xtype: 'label',
                                style:'font-size:11px;line-height:31px;',
                                text: lang('6.')
                            }]
                        },{
                            columnWidth: 0.5,
                            padding: 2,
                            items:[{
                                xtype: 'label',
                                style:'font-size:11px;line-height:31px;',
                                text: lang('Use of appropriate shading')
                            }]
                        },{
                            columnWidth: 0.15,
                            padding: 2,
                            items:[{
                                xtype: 'radiogroup',
                                width: '100%',
                                items:[{
                                    boxLabel: lang('Yes'),
                                    id: 'UseShadingNet1',
                                    name: 'UseShadingNet',
                                    style:'font-size:11px;',
                                    inputValue: '1',
                                    listeners:{
                                        change: function(){
                                            return false;
                                        }
                                    }
                                },{
                                    boxLabel: lang('No'),
                                    id: 'UseShadingNet2',
                                    name: 'UseShadingNet',
                                    style:'font-size:11px;',
                                    inputValue: '2',
                                    listeners:{
                                        change: function(){
                                            return false;
                                        }
                                    }
                                }]
                            }]
                        },{
                            columnWidth: 0.3,
                            padding: 2,
                            items:[{
                                xtype: 'textfield',
                                id: 'UseShadingNetNo',
                                name: 'UseShadingNetNo',
                                width:'100%',
                                fieldStyle: {
                                    'fontSize' : '11px',
                                    'margin': '3px 0',
                                    'width': '100%'
                                }
                            }]
                        }]
                    },{
                        layout:'column',
                        width:'100%',
                        border:false,
                        items:[{
                            columnWidth: 0.05,
                            padding: 2,
                            items:[{
                                xtype: 'label',
                                style:'font-size:11px;line-height:31px;',
                                text: lang('7.')
                            }]
                        },{
                            columnWidth: 0.5,
                            padding: 2,
                            items:[{
                                xtype: 'label',
                                style:'font-size:11px;line-height:31px;',
                                text: lang('Adequate supply of top soil or substrate for potting mix')
                            }]
                        },{
                            columnWidth: 0.15,
                            padding: 2,
                            items:[{
                                xtype: 'radiogroup',
                                width: '100%',
                                items:[{
                                    boxLabel: lang('Yes'),
                                    id: 'AdequateSupplyTopSoil1',
                                    name: 'AdequateSupplyTopSoil',
                                    style:'font-size:11px;',
                                    inputValue: '1',
                                    listeners:{
                                        change: function(){
                                            return false;
                                        }
                                    }
                                },{
                                    boxLabel: lang('No'),
                                    id: 'AdequateSupplyTopSoil2',
                                    name: 'AdequateSupplyTopSoil',
                                    style:'font-size:11px;',
                                    inputValue: '2',
                                    listeners:{
                                        change: function(){
                                            return false;
                                        }
                                    }
                                }]
                            }]
                        },{
                            columnWidth: 0.3,
                            padding: 2,
                            items:[{
                                xtype: 'textfield',
                                id: 'AdequateSupplyTopSoilNo',
                                name: 'AdequateSupplyTopSoilNo',
                                width:'100%',
                                fieldStyle: {
                                    'fontSize' : '11px',
                                    'margin': '3px 0',
                                    'width': '100%'
                                }
                            }]
                        }]
                    },{
                        layout:'column',
                        width:'100%',
                        border:false,
                        items:[{
                            columnWidth: 0.05,
                            padding: 2,
                            items:[{
                                xtype: 'label',
                                style:'font-size:11px;line-height:31px;',
                                text: lang('8.')
                            }]
                        },{
                            columnWidth: 0.5,
                            padding: 2,
                            items:[{
                                xtype: 'label',
                                style:'font-size:11px;line-height:31px;',
                                text: lang('Improved varieties from certified seed and budwood sources')
                            }]
                        },{
                            columnWidth: 0.15,
                            padding: 2,
                            items:[{
                                xtype: 'radiogroup',
                                width: '100%',
                                items:[{
                                    boxLabel: lang('Yes'),
                                    id: 'ImprovedVariety1',
                                    name: 'ImprovedVariety',
                                    style:'font-size:11px;',
                                    inputValue: '1',
                                    listeners:{
                                        change: function(){
                                            return false;
                                        }
                                    }
                                },{
                                    boxLabel: lang('No'),
                                    id: 'ImprovedVariety2',
                                    name: 'ImprovedVariety',
                                    style:'font-size:11px;',
                                    inputValue: '2',
                                    listeners:{
                                        change: function(){
                                            return false;
                                        }
                                    }
                                }]
                            }]
                        },{
                            columnWidth: 0.3,
                            padding: 2,
                            items:[{
                                xtype: 'textfield',
                                id: 'ImprovedVarietyNo',
                                name: 'ImprovedVarietyNo',
                                width:'100%',
                                fieldStyle: {
                                    'fontSize' : '11px',
                                    'margin': '3px 0',
                                    'width': '100%'
                                }
                            }]
                        }]
                    },{
                        layout:'column',
                        width:'100%',
                        border:false,
                        hidden:true,
                        items:[{
                            columnWidth: 0.05,
                            padding: 2,
                            items:[{
                                xtype: 'label',
                                style:'font-size:11px;line-height:31px;',
                                text: lang('9.')
                            }]
                        },{
                            columnWidth: 0.5,
                            padding: 2,
                            items:[{
                                xtype: 'label',
                                style:'font-size:11px;line-height:31px;',
                                text: lang('Construction of storing and bag-filling facilities')
                            }]
                        },{
                            columnWidth: 0.15,
                            padding: 2,
                            items:[{
                                xtype: 'radiogroup',
                                width: '100%',
                                items:[{
                                    boxLabel: lang('Yes'),
                                    id: 'ConstructStoring1',
                                    name: 'ConstructStoring',
                                    style:'font-size:11px;',
                                    inputValue: '1',
                                    listeners:{
                                        change: function(){
                                            return false;
                                        }
                                    }
                                },{
                                    boxLabel: lang('No'),
                                    id: 'ConstructStoring2',
                                    name: 'ConstructStoring',
                                    style:'font-size:11px;',
                                    inputValue: '2',
                                    listeners:{
                                        change: function(){
                                            return false;
                                        }
                                    }
                                }]
                            }]
                        },{
                            columnWidth: 0.3,
                            padding: 2,
                            items:[{
                                xtype: 'textfield',
                                id: 'ConstructStoringNo',
                                name: 'ConstructStoringNo',
                                width:'100%',
                                fieldStyle: {
                                    'fontSize' : '11px',
                                    'margin': '3px 0',
                                    'width': '100%'
                                }
                            }]
                        }]
                    },{
                        layout:'column',
                        width:'100%',
                        border:false,
                        items:[{
                            columnWidth: 0.05,
                            padding: 2,
                            items:[{
                                xtype: 'label',
                                style:'font-size:11px;line-height:31px;',
                                text: lang('9.')
                            }]
                        },{
                            columnWidth: 0.5,
                            padding: 2,
                            items:[{
                                xtype: 'label',
                                style:'font-size:11px;line-height:31px;',
                                text: lang('Correct equipment is available to operator(s)')
                            }]
                        },{
                            columnWidth: 0.15,
                            padding: 2,
                            items:[{
                                xtype: 'radiogroup',
                                width: '100%',
                                items:[{
                                    boxLabel: lang('Yes'),
                                    id: 'CorrectEquipment1',
                                    name: 'CorrectEquipment',
                                    style:'font-size:11px;',
                                    inputValue: '1',
                                    listeners:{
                                        change: function(){
                                            return false;
                                        }
                                    }
                                },{
                                    boxLabel: lang('No'),
                                    id: 'CorrectEquipment2',
                                    name: 'CorrectEquipment',
                                    style:'font-size:11px;',
                                    inputValue: '2',
                                    listeners:{
                                        change: function(){
                                            return false;
                                        }
                                    }
                                }]
                            }]
                        },{
                            columnWidth: 0.3,
                            padding: 2,
                            items:[{
                                xtype: 'textfield',
                                id: 'CorrectEquipmentNo',
                                name: 'CorrectEquipmentNo',
                                width:'100%',
                                fieldStyle: {
                                    'fontSize' : '11px',
                                    'margin': '3px 0',
                                    'width': '100%'
                                }
                            }]
                        }]
                    },{
                        layout:'column',
                        width:'100%',
                        border:false,
                        items:[{
                            columnWidth: 0.05,
                            padding: 2,
                            items:[{
                                xtype: 'label',
                                style:'font-size:11px;line-height:31px;',
                                text: lang('10.')
                            }]
                        },{
                            columnWidth: 0.5,
                            padding: 2,
                            items:[{
                                xtype: 'label',
                                style:'font-size:11px;line-height:31px;',
                                text: lang('Wind break installed (if needed)')
                            }]
                        },{
                            columnWidth: 0.15,
                            padding: 2,
                            items:[{
                                xtype: 'radiogroup',
                                width: '100%',
                                items:[{
                                    boxLabel: lang('Yes'),
                                    id: 'WindBreakInstalled1',
                                    name: 'WindBreakInstalled',
                                    style:'font-size:11px;',
                                    inputValue: '1',
                                    listeners:{
                                        change: function(){
                                            return false;
                                        }
                                    }
                                },{
                                    boxLabel: lang('No'),
                                    id: 'WindBreakInstalled2',
                                    name: 'WindBreakInstalled',
                                    style:'font-size:11px;',
                                    inputValue: '2',
                                    listeners:{
                                        change: function(){
                                            return false;
                                        }
                                    }
                                }]
                            }]
                        },{
                            columnWidth: 0.3,
                            padding: 2,
                            items:[{
                                xtype: 'textfield',
                                id: 'WindBreakInstalledNo',
                                name: 'WindBreakInstalledNo',
                                width:'100%',
                                fieldStyle: {
                                    'fontSize' : '11px',
                                    'margin': '3px 0',
                                    'width': '100%'
                                }
                            }]
                        }]
                    },{
                        layout:'column',
                        width:'100%',
                        border:false,
                        items:[{
                            columnWidth: 0.05,
                            padding: 2,
                            items:[{
                                xtype: 'label',
                                style:'font-size:11px;line-height:31px;',
                                text: lang('11.')
                            }]
                        },{
                            columnWidth: 0.5,
                            padding: 2,
                            items:[{
                                xtype: 'label',
                                style:'font-size:11px;line-height:31px;',
                                text: lang('Security fence installed (if needed)')
                            }]
                        },{
                            columnWidth: 0.15,
                            padding: 2,
                            items:[{
                                xtype: 'radiogroup',
                                width: '100%',
                                items:[{
                                    boxLabel: lang('Yes'),
                                    id: 'SecurityFenceInstalled1',
                                    name: 'SecurityFenceInstalled',
                                    style:'font-size:11px;',
                                    inputValue: '1',
                                    listeners:{
                                        change: function(){
                                            return false;
                                        }
                                    }
                                },{
                                    boxLabel: lang('No'),
                                    id: 'SecurityFenceInstalled2',
                                    name: 'SecurityFenceInstalled',
                                    style:'font-size:11px;',
                                    inputValue: '2',
                                    listeners:{
                                        change: function(){
                                            return false;
                                        }
                                    }
                                }]
                            }]
                        },{
                            columnWidth: 0.3,
                            padding: 2,
                            items:[{
                                xtype: 'textfield',
                                id: 'SecurityFenceInstalledNo',
                                name: 'SecurityFenceInstalledNo',
                                width:'100%',
                                fieldStyle: {
                                    'fontSize' : '11px',
                                    'margin': '3px 0',
                                    'width': '100%'
                                }
                            }]
                        }]
                    },{
                        layout:'column',
                        width:'100%',
                        border:false,
                        items:[{
                            columnWidth: 0.05,
                            padding: 2,
                            items:[{
                                xtype: 'label',
                                style:'font-size:11px;line-height:31px;',
                                text: lang('12.')
                            }]
                        },{
                            columnWidth: 0.5,
                            padding: 2,
                            items:[{
                                xtype: 'label',
                                style:'font-size:11px;line-height:31px;',
                                text: lang('Fertilizer used in seedling establishment')
                            }]
                        },{
                            columnWidth: 0.15,
                            padding: 2,
                            items:[{
                                xtype: 'radiogroup',
                                width: '100%',
                                items:[{
                                    boxLabel: lang('Yes'),
                                    id: 'FertilizerUsed1',
                                    name: 'FertilizerUsed',
                                    style:'font-size:11px;',
                                    inputValue: '1',
                                    listeners:{
                                        change: function(){
                                            return false;
                                        }
                                    }
                                },{
                                    boxLabel: lang('No'),
                                    id: 'FertilizerUsed2',
                                    name: 'FertilizerUsed',
                                    style:'font-size:11px;',
                                    inputValue: '2',
                                    listeners:{
                                        change: function(){
                                            return false;
                                        }
                                    }
                                }]
                            }]
                        },{
                            columnWidth: 0.3,
                            padding: 2,
                            items:[{
                                xtype: 'textfield',
                                id: 'FertilizerUsedNo',
                                name: 'FertilizerUsedNo',
                                width:'100%',
                                fieldStyle: {
                                    'fontSize' : '11px',
                                    'margin': '3px 0',
                                    'width': '100%'
                                }
                            }]
                        }]
                    },{
                        layout:'column',
                        width:'100%',
                        border:false,
                        items:[{
                            columnWidth: 0.05,
                            padding: 2,
                            items:[{
                                xtype: 'label',
                                style:'font-size:11px;line-height:31px;',
                                text: lang('13.')
                            }]
                        },{
                            columnWidth: 0.5,
                            padding: 2,
                            items:[{
                                xtype: 'label',
                                style:'font-size:11px;line-height:31px;',
                                text: lang('Operators possess adequate skills')
                            }]
                        },{
                            columnWidth: 0.15,
                            padding: 2,
                            items:[{
                                xtype: 'radiogroup',
                                width: '100%',
                                items:[{
                                    boxLabel: lang('Yes'),
                                    id: 'OperatorAdequateTraining1',
                                    name: 'OperatorAdequateTraining',
                                    style:'font-size:11px;',
                                    inputValue: '1',
                                    listeners:{
                                        change: function(){
                                            return false;
                                        }
                                    }
                                },{
                                    boxLabel: lang('No'),
                                    id: 'OperatorAdequateTraining2',
                                    name: 'OperatorAdequateTraining',
                                    style:'font-size:11px;',
                                    inputValue: '2',
                                    listeners:{
                                        change: function(){
                                            return false;
                                        }
                                    }
                                }]
                            }]
                        },{
                            columnWidth: 0.3,
                            padding: 2,
                            items:[{
                                xtype: 'textfield',
                                id: 'OperatorAdequateTrainingNo',
                                name: 'OperatorAdequateTrainingNo',
                                width:'100%',
                                fieldStyle: {
                                    'fontSize' : '11px',
                                    'margin': '3px 0',
                                    'width': '100%'
                                }
                            }]
                        }]
                    },{
                        layout:'column',
                        width:'100%',
                        border:false,
                        items:[{
                            columnWidth: 0.05,
                            padding: 2,
                            items:[{
                                xtype: 'label',
                                style:'font-size:11px;line-height:31px;',
                                text: lang('14.')
                            }]
                        },{
                            columnWidth: 0.5,
                            padding: 2,
                            items:[{
                                xtype: 'label',
                                style:'font-size:11px;line-height:31px;',
                                text: lang('Adequate facilities for workers, and requisite safety equipment provided')
                            }]
                        },{
                            columnWidth: 0.15,
                            padding: 2,
                            items:[{
                                xtype: 'radiogroup',
                                width: '100%',
                                items:[{
                                    boxLabel: lang('Yes'),
                                    id: 'AdequateFacility1',
                                    name: 'AdequateFacility',
                                    style:'font-size:11px;',
                                    inputValue: '1',
                                    listeners:{
                                        change: function(){
                                            return false;
                                        }
                                    }
                                },{
                                    boxLabel: lang('No'),
                                    id: 'AdequateFacility2',
                                    name: 'AdequateFacility',
                                    style:'font-size:11px;',
                                    inputValue: '2',
                                    listeners:{
                                        change: function(){
                                            return false;
                                        }
                                    }
                                }]
                            }]
                        },{
                            columnWidth: 0.3,
                            padding: 2,
                            items:[{
                                xtype: 'textfield',
                                id: 'AdequateFacilityNo',
                                name: 'AdequateFacilityNo',
                                width:'100%',
                                fieldStyle: {
                                    'fontSize' : '11px',
                                    'margin': '3px 0',
                                    'width': '100%'
                                }
                            }]
                        }]
                    },{
                        layout:'column',
                        width:'100%',
                        border:false,
                        items:[{
                            columnWidth: 0.05,
                            padding: 2,
                            items:[{
                                xtype: 'label',
                                style:'font-size:11px;line-height:31px;',
                                text: lang('15.')
                            }]
                        },{
                            columnWidth: 0.5,
                            padding: 2,
                            items:[{
                                xtype: 'label',
                                style:'font-size:11px;line-height:31px;',
                                text: lang('Sustainable and rational pest and disease control')
                            }]
                        },{
                            columnWidth: 0.15,
                            padding: 2,
                            items:[{
                                xtype: 'radiogroup',
                                width: '100%',
                                items:[{
                                    boxLabel: lang('Yes'),
                                    id: 'SustainablePestDisease1',
                                    name: 'SustainablePestDisease',
                                    style:'font-size:11px;',
                                    inputValue: '1',
                                    listeners:{
                                        change: function(){
                                            return false;
                                        }
                                    }
                                },{
                                    boxLabel: lang('No'),
                                    id: 'SustainablePestDisease2',
                                    name: 'SustainablePestDisease',
                                    style:'font-size:11px;',
                                    inputValue: '2',
                                    listeners:{
                                        change: function(){
                                            return false;
                                        }
                                    }
                                }]
                            }]
                        },{
                            columnWidth: 0.3,
                            padding: 2,
                            items:[{
                                xtype: 'textfield',
                                id: 'SustainablePestDiseaseNo',
                                name: 'SustainablePestDiseaseNo',
                                width:'100%',
                                fieldStyle: {
                                    'fontSize' : '11px',
                                    'margin': '3px 0',
                                    'width': '100%'
                                }
                            }]
                        }]
                    },{
                        layout:'column',
                        width:'100%',
                        border:false,
                        hidden:true,
                        items:[{
                            columnWidth: 0.05,
                            padding: 2,
                            items:[{
                                xtype: 'label',
                                style:'font-size:11px;line-height:31px;',
                                text: lang('17.')
                            }]
                        },{
                            columnWidth: 0.5,
                            padding: 2,
                            items:[{
                                xtype: 'label',
                                style:'font-size:11px;line-height:31px;',
                                text: lang('There are clone grading in nursery')
                            }]
                        },{
                            columnWidth: 0.15,
                            padding: 2,
                            items:[{
                                xtype: 'radiogroup',
                                width: '100%',
                                items:[{
                                    boxLabel: lang('Yes'),
                                    id: 'CloneGrading1',
                                    name: 'CloneGrading',
                                    style:'font-size:11px;',
                                    inputValue: '1',
                                    listeners:{
                                        change: function(){
                                            return false;
                                        }
                                    }
                                },{
                                    boxLabel: lang('No'),
                                    id: 'CloneGrading2',
                                    name: 'CloneGrading',
                                    style:'font-size:11px;',
                                    inputValue: '2',
                                    listeners:{
                                        change: function(){
                                            return false;
                                        }
                                    }
                                }]
                            }]
                        },{
                            columnWidth: 0.3,
                            padding: 2,
                            items:[{
                                xtype: 'textfield',
                                id: 'CloneGradingNo',
                                name: 'CloneGradingNo',
                                width:'100%',
                                fieldStyle: {
                                    'fontSize' : '11px',
                                    'margin': '3px 0',
                                    'width': '100%'
                                }
                            }]
                        }]
                    },{
                        layout:'column',
                        width:'100%',
                        border:false,
                        items:[{
                            columnWidth: 0.05,
                            padding: 2,
                            items:[{
                                xtype: 'label',
                                style:'font-size:11px;line-height:31px;',
                                text: lang('16.')
                            }]
                        },{
                            columnWidth: 0.5,
                            padding: 2,
                            items:[{
                                xtype: 'label',
                                style:'font-size:11px;line-height:31px;',
                                text: lang('Seedling culling is done')
                            }]
                        },{
                            columnWidth: 0.15,
                            padding: 2,
                            items:[{
                                xtype: 'radiogroup',
                                width: '100%',
                                items:[{
                                    boxLabel: lang('Yes'),
                                    id: 'SeedlingCullingDone1',
                                    name: 'SeedlingCullingDone',
                                    style:'font-size:11px;',
                                    inputValue: '1',
                                    listeners:{
                                        change: function(){
                                            return false;
                                        }
                                    }
                                },{
                                    boxLabel: lang('No'),
                                    id: 'SeedlingCullingDone2',
                                    name: 'SeedlingCullingDone',
                                    style:'font-size:11px;',
                                    inputValue: '2',
                                    listeners:{
                                        change: function(){
                                            return false;
                                        }
                                    }
                                }]
                            }]
                        },{
                            columnWidth: 0.3,
                            padding: 2,
                            items:[{
                                xtype: 'textfield',
                                id: 'SeedlingCullingDoneNo',
                                name: 'SeedlingCullingDoneNo',
                                width:'100%',
                                fieldStyle: {
                                    'fontSize' : '11px',
                                    'margin': '3px 0',
                                    'width': '100%'
                                }
                            }]
                        }]
                    },{
                        layout:'column',
                        width:'100%',
                        border:false,
                        items:[{
                            columnWidth: 0.05,
                            padding: 2,
                            items:[{
                                xtype: 'label',
                                style:'font-size:11px;line-height:31px;',
                                text: lang('17.')
                            }]
                        },{
                            columnWidth: 0.5,
                            padding: 2,
                            items:[{
                                xtype: 'label',
                                style:'font-size:11px;line-height:31px;',
                                text: lang('Proper input and sales records are maintained')
                            }]
                        },{
                            columnWidth: 0.15,
                            padding: 2,
                            items:[{
                                xtype: 'radiogroup',
                                width: '100%',
                                items:[{
                                    boxLabel: lang('Yes'),
                                    id: 'ProperInputSalesRecord1',
                                    name: 'ProperInputSalesRecord',
                                    style:'font-size:11px;',
                                    inputValue: '1',
                                    listeners:{
                                        change: function(){
                                            return false;
                                        }
                                    }
                                },{
                                    boxLabel: lang('No'),
                                    id: 'ProperInputSalesRecord2',
                                    name: 'ProperInputSalesRecord',
                                    style:'font-size:11px;',
                                    inputValue: '2',
                                    listeners:{
                                        change: function(){
                                            return false;
                                        }
                                    }
                                }]
                            }]
                        },{
                            columnWidth: 0.3,
                            padding: 2,
                            items:[{
                                xtype: 'textfield',
                                id: 'ProperInputSalesRecordNo',
                                name: 'ProperInputSalesRecordNo',
                                width:'100%',
                                fieldStyle: {
                                    'fontSize' : '11px',
                                    'margin': '3px 0',
                                    'width': '100%'
                                }
                            }]
                        }]
                    },{
                        layout:'column',
                        width:'100%',
                        border:false,
                        items:[{
                            columnWidth: 0.05,
                            padding: 2,
                            items:[{
                                xtype: 'label',
                                style:'font-size:11px;line-height:31px;',
                                text: lang('18.')
                            }]
                        },{
                            columnWidth: 0.5,
                            padding: 2,
                            items:[{
                                xtype: 'label',
                                style:'font-size:11px;line-height:31px;',
                                text: lang('Seeds are pre-germinated before planting')
                            }]
                        },{
                            columnWidth: 0.15,
                            padding: 2,
                            items:[{
                                xtype: 'radiogroup',
                                width: '100%',
                                items:[{
                                    boxLabel: lang('Yes'),
                                    id: 'SeedsPreGerminated1',
                                    name: 'SeedsPreGerminated',
                                    style:'font-size:11px;',
                                    inputValue: '1',
                                    listeners:{
                                        change: function(){
                                            return false;
                                        }
                                    }
                                },{
                                    boxLabel: lang('No'),
                                    id: 'SeedsPreGerminated2',
                                    name: 'SeedsPreGerminated',
                                    style:'font-size:11px;',
                                    inputValue: '2',
                                    listeners:{
                                        change: function(){
                                            return false;
                                        }
                                    }
                                }]
                            }]
                        },{
                            columnWidth: 0.3,
                            padding: 2,
                            items:[{
                                xtype: 'textfield',
                                id: 'SeedsPreGerminatedNo',
                                name: 'SeedsPreGerminatedNo',
                                width:'100%',
                                fieldStyle: {
                                    'fontSize' : '11px',
                                    'margin': '3px 0',
                                    'width': '100%'
                                }
                            }]
                        }]
                    }]
                }]
            }],
        }],
        buttons: [{
            id: 'nsaveButton_idtrader',
            text: lang('Save'),
            margin: '5px',
            scale: 'large',
            ui: 's-button',
            cls: 's-blue ' + m_act_save,
            handler: function() {
                var form = Ext.getCmp('dataFormNurseyTrader').getForm();
                var methode;
                if (Ext.getCmp('NurseryID_idtrader').getValue() != '') methode = 'POST';
                else methode = 'POST';
                Ext.getCmp('Luas_idtrader').setValue(nnumber_format(Ext.getCmp('Luas_idtrader').getValue(), 2))
                Ext.getCmp('Kapasitas_idtrader').setValue(nnumber_format(Ext.getCmp('Kapasitas_idtrader').getValue(), 2))

                if(form.isValid()){
                    form.submit({
                        url: m_crud + 'nursery',
                        method: methode,
                        waitMsg: lang('Sending data...'),
                        success: function(fp, o) {
                            Ext.MessageBox.alert('Success', 'Data saved.');
                            Ext.getCmp('Luas_idtrader').setValue(nnumber_format(Ext.getCmp('Luas_idtrader').getValue()))
                                //Ext.getCmp('Kapasitas_idtrader').setValue(nnumber_format(Ext.getCmp('Kapasitas_idtrader').getValue()))
                            Ext.getCmp('NurseryID_idtrader').setValue(o.result.id);
                            //                            Ext.getCmp('gnurseypenjualan_idtrader').setDisabled(false);
                            var r = Ext.decode(o.response.responseText);
                            Ext.getCmp('NurseryID_idtrader').setValue(r.id);
                            Ext.getCmp('NurseryNr_idtrader').setReadOnly(true);
                            //fillNurseryForm();
                            store_nursery_list.load({
                                params: {
                                    ObjType: 'trader',
                                    ObjID: Ext.getCmp('TraderID').getValue()
                                }
                            });
                        },
                        failure: function(fp, o) {
                            if(o.response.responseText == undefined){
                                var errText = "Form is not complete yet";
                            }else{
                                var errText = o.response.responseText;
                                errText = errText.replace(/^"(.*)"$/, '$1');
                            }

                            Ext.MessageBox.show({
                                title: 'Failed',
                                msg: errText,
                                buttons: Ext.MessageBox.OK,
                                animateTarget: 'mb9',
                                icon: 'ext-mb-error'
                            });
                        }
                    });
                }else{
                    Ext.MessageBox.show({
                        title: 'Failed',
                        msg: 'Form is not complete yet',
                        buttons: Ext.MessageBox.OK,
                        animateTarget: 'mb9',
                        icon: 'ext-mb-error'
                    });
                }
            }
        }, {
            text: lang('Close'),
            margin: '5px',
            scale: 'large',
            ui: 's-button',
            cls: 's-grey',
            disabled: false,
            handler: function() {
                winNurseyTrader.hide();
            }
        }]
    });

    var winNurseyTrader = Ext.create('widget.window', {
        title: lang('Trader Nursery Unit'),
        id: 'winNurseyTrader',
        closable: true,
        modal: true,
        closeAction: 'show',
        width: '80%',
        height: '90%',
        layout: 'fit',
        items: [DataFormNurseyTrader]
    });
    //END NURSERY
    function hideSave() {
        Ext.getCmp('saveButton').hide();
        Ext.getCmp('nsaveButton_idtrader').hide();
        if (Ext.getCmp('TraderID').getValue() === '' && m_act_add) {
            Ext.getCmp('saveButton').show();
            Ext.getCmp('nsaveButton_idtrader').show();
        }
        if (Ext.getCmp('TraderID').getValue() !== '' && m_act_update) {
            Ext.getCmp('saveButton').show();
            Ext.getCmp('nsaveButton_idtrader').show();
        }
    }

    function displayFormWindow() {
        if (!win.isVisible()) {
            DataForm.getForm().reset();
            win.center();
            win.show();
        } else {
            win.hide(this, function() {});
            win.toFront();
        }
    }
    //staff
    Ext.define('staff.Model', {
        extend: 'Ext.data.Model',
        fields: ['TraderStaffID', 'StaffID', 'UserId', 'StaffSupplychainID', 'StaffName', 'PrivateCellphone', 'OfficialCellphone', 'PrivateStaffEmail', 'OfficialStaffEmail', 'StaffBirth', 'StaffGender', 'Educatio', 'StaffGende', 'IdentityNumber', 'Education', 'FamilyMembers', 'Address', 'Position'],
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
        data: [{
            "label": "Staff"
        }, {
            "label": "Coordinator"
        }, {
            "label": "Pemilik"
        }]
    });
    var ckelamin = Ext.create('Ext.data.Store', {
        fields: ['id', 'label'],
        data: [{
            "id": "1",
            "label": "Laki-laki"
        }, {
            "id": "2",
            "label": "Perempuan"
        }]
    });
    var ceducation = Ext.create('Ext.data.Store', {
        fields: ['id', 'label'],
        data: [{
            "id": "1",
            "label": "Belum pernah sekolah"
        }, {
            "id": "2",
            "label": "Tidak tamat SD"
        }, {
            "id": "3",
            "label": "Tamat SD, tidak melanjutkan"
        }, {
            "id": "4",
            "label": "Tamat SMP"
        }, {
            "id": "5",
            "label": "Tamat SMA/SMK"
        }, {
            "id": "6",
            "label": "Tamat perguruan tinggi"
        }]
    });
    //end staff
    //quality standard
    Ext.define('quality_standard.Model', {
        extend: 'Ext.data.Model',
        fields: ['StandardID', 'StandardSupplychainID', 'StandardName', 'Moisture', 'BeanCount', 'Waste', 'Mouldy', 'Insect', 'Slaty'],
    });
    var store_quality_standard = Ext.create('Ext.data.Store', {
        model: 'quality_standard.Model',
        autoLoad: false,
        pageSize: 10,
        proxy: {
            type: 'ajax',
            url: m_quality_standard + 's',
            reader: {
                type: 'json',
                root: 'data',
                totalProperty: 'total'
            }
        }
    });
    var qsRowEditing = Ext.create('Ext.grid.plugin.RowEditing', {
        id: 'qsRowEditing',
        clicksToMoveEditor: 0,
        autoCancel: false,
        errorSummary: false,
        clicksToEdit: 2
    });
    //end quality standard
    //quality
    Ext.define('quality.Model', {
        extend: 'Ext.data.Model',
        fields: ['QualityID', 'QualitySupplychainID', 'QualityDate', 'StandardName', 'Moisture', 'BeanCount', 'Waste', 'Mouldy', 'Insect', 'Slaty', 'StandardID'],
    });
    var store_quality = Ext.create('Ext.data.Store', {
        model: 'quality.Model',
        autoLoad: false,
        pageSize: 10,
        proxy: {
            type: 'ajax',
            url: m_quality,
            reader: {
                type: 'json',
                root: 'data',
                totalProperty: 'total'
            }
        }
    });
    var qRowEditing = Ext.create('Ext.grid.plugin.RowEditing', {
        id: 'qRowEditing',
        clicksToMoveEditor: 0,
        autoCancel: false,
        errorSummary: false,
        clicksToEdit: 2
    });
    var store_standard = Ext.create('Ext.data.Store', {
        extend: 'Ext.data.Model',
        fields: ['id', 'label'],
        proxy: {
            type: 'ajax',
            url: m_standard,
            reader: {
                type: 'json',
                root: 'data'
            }
        }
    });
    //end quality
    //price
    Ext.define('price.Model', {
        extend: 'Ext.data.Model',
        fields: ['PriceID', 'PriceSupplychainID', 'PriceDate', 'Price', 'District'],
    });
    var store_price = Ext.create('Ext.data.Store', {
        model: 'price.Model',
        autoLoad: false,
        pageSize: 10,
        proxy: {
            type: 'ajax',
            url: m_price,
            reader: {
                type: 'json',
                root: 'data',
                totalProperty: 'total'
            }
        }
    });
    var pRowEditing = Ext.create('Ext.grid.plugin.RowEditing', {
        id: 'pRowEditing',
        clicksToMoveEditor: 0,
        autoCancel: false,
        errorSummary: false,
        clicksToEdit: 2
    });
    //end price
    //package
    Ext.define('package.Model', {
        extend: 'Ext.data.Model',
        fields: ['PackageID', 'PackageSupplychainID', 'PackageType', 'PackageWeight'],
    });
    var store_package = Ext.create('Ext.data.Store', {
        model: 'package.Model',
        autoLoad: false,
        pageSize: 10,
        proxy: {
            type: 'ajax',
            url: m_package,
            reader: {
                type: 'json',
                root: 'data',
                totalProperty: 'total'
            }
        }
    });
    var paRowEditing = Ext.create('Ext.grid.plugin.RowEditing', {
        id: 'paRowEditing',
        clicksToMoveEditor: 0,
        autoCancel: false,
        errorSummary: false,
        clicksToEdit: 2
    });
    //end package
    var store_nursery_list = Ext.create('Ext.data.Store', {
        extend: 'Ext.data.Model',
        fields: ['NurseryID', 'NurseryNr', 'ObjID', 'Luas'],
        autoLoad: false,
        proxy: {
            type: 'ajax',
            url: m_crud + 'nursery_list',
            reader: {
                type: 'json',
                root: 'data'
            }
        }
    });
    var DataFormNurseyTraderList = Ext.create('Ext.form.Panel', {
        autoScroll: true,
        bodyPadding: 5,
        id: 'DataFormNurseyTraderList',
        fieldDefaults: {
            labelAlign: 'left',
            labelWidth: 120,
            anchor: '95%'
        },
        items: [{
            xtype: 'gridpanel',
            id: 'gridDataFormNurseyTraderList',
            style: 'border:1px solid #CCC;',
            store: store_nursery_list,
            width: '100%',
            loadMask: true,
            dockedItems: [{
                xtype: 'toolbar',
                items: [{
                    icon: varjs.config.base_url + 'images/icons/new/add.png', cls:'Sfr_BtnGridGreen', overCls:'Sfr_BtnGridGreen-Hover',
                    //cls: m_act_save,
                    hidden: !m_act_add,
                    text: lang('Add'),
                    scope: this,
                    handler: function() {
                        //reset form
                        Ext.getCmp('dataFormNurseyTrader').getForm().reset();
                        Ext.getCmp('iphoto_idtrader').setSrc(m_api_base_url + '/images/nursery/no-image.png');
                        Ext.getCmp('iphotoResponsible_idtrader').setSrc(m_api_base_url + '/images/Photo/no-user.jpg');

                        displayFormNurseyTrader();
                        //fillNurseryForm();
                        Ext.getCmp('nid_obj_idtrader').setValue(Ext.getCmp('TraderID').getValue());
                        //Ext.getCmp('NamaResponsible_idtrader').setValue(Ext.getCmp('TraderName').getValue());
                        //Ext.getCmp('Responsible_idtrader').setValue(Ext.getCmp('TraderID').getValue());
                        Ext.getCmp('NurseryNr_idtrader').setReadOnly(false);
                        store_nursey_trans.clearData();
                        store_nursey_trans.removeAll();
                        store_nursey_monitoring.clearData();
                        store_nursey_monitoring.removeAll();
                    }
                }, {
                    icon: varjs.config.base_url + 'images/icons/new/update.png',
                    //cls: m_act_save,
                    hidden: !m_act_update,
                    text: lang('Update'),
                    scope: this,
                    handler: function() {
                        var sm = Ext.getCmp('gridDataFormNurseyTraderList').getSelectionModel().getSelection()[0];
                        if (sm == undefined) {
                            Ext.MessageBox.alert('Warning', lang('Please select Nursery!'));
                        } else {
                            //reset form
                            Ext.getCmp('dataFormNurseyTrader').getForm().reset();
                            Ext.getCmp('iphoto_idtrader').setSrc(m_api_base_url + '/images/nursery/no-image.png');
                            Ext.getCmp('iphotoResponsible_idtrader').setSrc(m_api_base_url + '/images/Photo/no-user.jpg');

                            displayFormNurseyTrader();
                            fillNurseryForm(sm.get('NurseryID'));
                            Ext.getCmp('nid_obj_idtrader').setValue(Ext.getCmp('TraderID').getValue());
                            //Ext.getCmp('NamaResponsible_idtrader').setValue(Ext.getCmp('TraderName').getValue());
                            //Ext.getCmp('Responsible_idtrader').setValue(Ext.getCmp('TraderID').getValue());
                            Ext.getCmp('NurseryNr_idtrader').setReadOnly(true);
                        }
                    }
                }, {
                    itemId: 'remove',
                    icon: varjs.config.base_url + 'images/icons/new/delete.png',
                    cls: m_act_save,
                    hidden: !m_act_delete,
                    text: lang('Delete'),
                    scope: this,
                    handler: function() {
                        var sm = Ext.getCmp('gridDataFormNurseyTraderList').getSelectionModel().getSelection()[0];
                        if (sm == undefined) {
                            Ext.MessageBox.alert('Warning', lang('Please select Nursery!'));
                        } else {
                            Ext.MessageBox.confirm('Message', lang('Apakah anda mau menghapus data ini ?'), function(btn) {
                                if (btn == 'yes') {
                                    Ext.Ajax.request({
                                        waitMsg: lang('Please Wait'),
                                        url: m_crud + 'nursery',
                                        method: 'DELETE',
                                        params: {
                                            ObjType: 'trader',
                                            ObjID: Ext.getCmp('TraderID').getValue(),
                                            NurseryID: sm.get('NurseryID')
                                        },
                                        success: function(response, opts) {
                                            var obj = Ext.decode(response.responseText);
                                            switch (obj.success) {
                                                case true:
                                                    Ext.MessageBox.alert('Success', obj.message);
                                                    store_nursery_list.load({
                                                        params: {
                                                            ObjType: 'trader',
                                                            ObjID: Ext.getCmp('TraderID').getValue()
                                                        }
                                                    });
                                                    break;
                                                default:
                                                    Ext.MessageBox.alert('Warning', obj.message);
                                                    break;
                                            }
                                        },
                                        failure: function(response, opts) {
                                            Ext.MessageBox.alert('error', lang('Could not connect to the database. Retry later'));
                                        }
                                    });
                                }
                            });
                        }
                    }
                }]
            }],
            columns: [{
                text: lang('NurseryID'),
                dataIndex: 'NurseryID',
                align: 'center',
                hidden: true
            }, {
                text: lang('No'),
                xtype: 'rownumberer',
                width: '10%',
                align: 'center'
            }, {
                text: lang('NurseryNr'),
                dataIndex: 'NurseryNr',
                width: '45%',
            }, {
                text: lang('Area (m2)'),
                dataIndex: 'Luas',
                width: '45%',
            }],
            listeners: {
                itemdblclick: function() {
                    //reset form
                    Ext.getCmp('dataFormNurseyTrader').getForm().reset();
                    Ext.getCmp('iphoto_idtrader').setSrc(m_api_base_url + '/images/nursery/no-image.png');
                    Ext.getCmp('iphotoResponsible_idtrader').setSrc(m_api_base_url + '/images/Photo/no-user.jpg');

                    var sm = Ext.getCmp('gridDataFormNurseyTraderList').getSelectionModel().getSelection()[0];
                    displayFormNurseyTrader();
                    fillNurseryForm(sm.get('NurseryID'));
                    Ext.getCmp('nid_obj_idtrader').setValue(Ext.getCmp('TraderID').getValue());
                    //Ext.getCmp('NamaResponsible_idtrader').setValue(Ext.getCmp('TraderName').getValue());
                    //Ext.getCmp('Responsible_idtrader').setValue(Ext.getCmp('TraderID').getValue());
                    Ext.getCmp('NurseryNr_idtrader').setReadOnly(false);
                }
            }
        }],
        buttons: [{
            text: lang('Close'),
            margin: '5px',
            scale: 'large',
            ui: 's-button',
            cls: 's-grey',
            disabled: false,
            handler: function() {
                winNurseyTraderList.hide();
            }
        }]
    });
    var winNurseyTraderList = Ext.create('widget.window', {
        title: lang('Nursery'),
        id: 'winNurseyTraderList',
        closable: true,
        modal: true,
        closeAction: 'show',
        width: '50%',
        height: 400,
        layout: {
            type: 'fit'
        },
        items: [DataFormNurseyTraderList]
    });

    function displayFormNurseyTraderList() {
        if (!winNurseyTraderList.isVisible()) {
            DataFormNurseyTraderList.getForm().reset();
            winNurseyTraderList.center();
            winNurseyTraderList.show();
        } else {
            winNurseyTraderList.hide(this, function() {});
            winNurseyTraderList.toFront();
        }
    }
    // general panel container
    var DataForm = Ext.create('Ext.form.Panel', {
        frame: false,
        height: 500,
        autoScroll: true,
        width: 1010,
        bodyPadding: 5,
        fileUpload: true,
        enctype: 'multipart/form-data',
        id: 'dataForm',
        fieldDefaults: {
            msgTarget: 'side',
            blankText: 'Tidak Boleh Kosong',
            labelAlign: 'left',
            labelWidth: 140,
            anchor: '100%'
        },
        dockedItems: [{
            xtype: 'toolbar',
            id: 'toolbar_trader',
            flex: 1,
            dock: 'top',
            cls: 'x-toolbar-garis',
            items: [{
                xtype: 'button',
                height: 85,
                width: 85,
                text: '<img src="' + varjs.config.base_url + 'img/general/nursery-24px.png" /> <br /> ' + lang('Nursery'),
                tooltip: 'Nursery',
                hidden: !m_act_trader_nursery,
                handler: function() {
                    store_nursery_list.load({
                        params: {
                            ObjType: 'trader',
                            ObjID: Ext.getCmp('TraderID').getValue()
                        }
                    });
                    displayFormNurseyTraderList();
                }
            },{
                xtype: 'button',
                height: 85,
                width: 85,
                text: '<img src="' + varjs.config.base_url + 'img/general/summary-24px.png" /> <br /> ' + lang('Survey'),
                tooltip: lang('Survey'),
                hidden: !m_act_trader_survey,
                handler: function() {
                    displayFormSurvey();
                }
            }]
        }],
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
                        style: 'border:2px solid #D6EDA4',
                        items: [{
                            xtype: 'textfield',
                            id: 'TraderID',
                            name: 'TraderID',
                            hidden: true
                        }, {
                            layout: 'column',
                            items: [{
                                columnWidth: 0.5,
                                items: [{
                                    xtype: 'fieldset',
                                    title: lang('Data Perusahaan'),
                                    items: [{
                                            xtype: 'textfield',
                                            id: 'TraderName',
                                            name: 'TraderName',
                                            allowBlank: false,
                                            fieldLabel: lang('Nama Pedagang')
                                        }, {
                                            xtype: 'textfield',
                                            id: 'IdentityNum',
                                            name: 'IdentityNum',
                                            //allowBlank: false,
                                            fieldLabel: lang('Nomor Identitas')
                                        }, {
                                            xtype: 'datefield',
                                            id: 'Birthdate',
                                            name: 'Birthdate',
                                            format: 'Y-m-d',
                                            //allowBlank: false,
                                            fieldLabel: lang('Tanggal Lahir')
                                        }, {
                                            fieldLabel: lang('Jenis Kelamin'),
                                            xtype: 'radiogroup',
                                            width: '100%',
                                            items: [{
                                                boxLabel: lang('Laki-laki'),
                                                name: 'Sex',
                                                id: 'Sex1',
                                                inputValue: '1'
                                            }, {
                                                boxLabel: lang('Perempuan'),
                                                name: 'Sex',
                                                id: 'Sex2',
                                                inputValue: '2'
                                            }]
                                        }, {
                                            xtype: 'textfield',
                                            id: 'Handphone',
                                            name: 'Handphone',
                                            //allowBlank: false,
                                            fieldLabel: lang('Handphone')
                                        }, {
                                            xtype: 'textfield',
                                            id: 'NoTelp',
                                            name: 'NoTelp',
                                            //allowBlank: false,
                                            fieldLabel: lang('No Telepon')
                                        }, {
                                            xtype: 'textfield',
                                            id: 'Email',
                                            name: 'Email',
                                            //allowBlank: false,
                                            fieldLabel: lang('Email')
                                        },
                                        //                                                    {
                                        //                                                        xtype: 'textfield',
                                        //                                                        id: 'education',
                                        //                                                        name: 'education',
                                        //                                                        allowBlank: false,
                                        //                                                        fieldLabel: lang('Pendidikan')
                                        //                                                    },
                                        {
                                            xtype: 'radiogroup',
                                            fieldLabel: lang('Pendidikan Terakhir'),
                                            columns: [200, 200],
                                            vertical: true,
                                            items: [{
                                                boxLabel: lang('Belum pernah sekolah'),
                                                name: 'Education',
                                                id: 'pendidikan1',
                                                inputValue: 1
                                            }, {
                                                boxLabel: lang('Tamat SD, tidak melanjutkan'),
                                                name: 'Education',
                                                id: 'pendidikan3',
                                                inputValue: 3
                                            }, {
                                                boxLabel: lang('Tamat SMA/SMK'),
                                                name: 'Education',
                                                id: 'pendidikan5',
                                                inputValue: 5
                                            }, {
                                                boxLabel: lang('Tidak tamat SD'),
                                                name: 'Education',
                                                id: 'pendidikan2',
                                                inputValue: 2
                                            }, {
                                                boxLabel: lang('Tamat SMP'),
                                                name: 'Education',
                                                id: 'pendidikan4',
                                                inputValue: 4
                                            }, {
                                                boxLabel: lang('Tamat perguruan tinggi'),
                                                name: 'Education',
                                                id: 'pendidikan6',
                                                inputValue: 6
                                            }]
                                        },
                                        // {
                                        //     xtype: 'numberfield',
                                        //     id: 'FamilyMembers',
                                        //     name: 'FamilyMembers',
                                        //     fieldLabel: lang('Jumlah Anggota Keluarga')
                                        // },
                                        {
                                            xtype: 'textfield',
                                            id: 'Name',
                                            name: 'Name',
                                            allowBlank: false,
                                            fieldLabel: lang('Nama Perusahaan')
                                        }, {
                                            xtype: 'textfield',
                                            id: 'Alias',
                                            name: 'Alias',
                                            fieldLabel: lang('Nama Alias/Singkatan')
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
                                        }
                                    ]
                                }]
                            }, {
                                columnWidth: 0.5,
                                margin: 5,
                                items: [{
                                    layout: 'column',
                                    border: true,
                                    hidden: true,
                                    items: [{
                                        columnWidth: 0.6,
                                        padding: 10,
                                        items: [{
                                            xtype: 'textfield',
                                            id: 'Photo_old',
                                            name: 'Photo_old',
                                            inputType: 'hidden'
                                        }, {
                                            xtype: 'fileuploadfield',
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
                                                        waitMsg: lang('Sending Photo...'),
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
                                    }, {
                                        xtype: 'label',
                                        text: lang('Anggota Keluarga yang Bekerja Didalam Perusahaan')
                                    }, {
                                        xtype: 'textfield',
                                        id: 'FamilyMembersMale',
                                        name: 'FamilyMembersMale',
                                        fieldLabel: lang('Laki-laki')
                                    }, {
                                        xtype: 'textfield',
                                        id: 'FamilyMembersFemale',
                                        name: 'FamilyMembersFemale',
                                        fieldLabel: lang('Perempuan')
                                    }]
                                }, {
                                    xtype: 'fieldset',
                                    title: lang('Lokasi'),
                                    items: [{
                                            id: 'Provinsi',
                                            allowBlank: false,
                                            name: 'Provinsi',
                                            xtype: 'combo',
                                            fieldLabel: lang('Provinsi'),
                                            store: mc_Provinsi,
                                            displayField: 'label',
                                            valueField: 'label',
                                            queryMode: 'local',
                                            // disabled: 'true',
                                            listeners: {
                                                change: function(cb, nv, ov) {
                                                    mc_Kabupaten.load({
                                                        params: {
                                                            key: Ext.getCmp('Provinsi').getValue()
                                                        }
                                                    });
                                                    Ext.getCmp('Kabupaten').enable();
                                                }
                                            }
                                        }, {
                                            id: 'Kabupaten',
                                            allowBlank: false,
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
                                                        }
                                                    });
                                                    Ext.getCmp('Kecamatan').enable();
                                                    store_partner.load({
                                                        params: {
                                                            district: Ext.getCmp('Kabupaten').getValue()
                                                        }
                                                    });
                                                }
                                            }
                                        }, {
                                            id: 'Kecamatan',
                                            allowBlank: false,
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
                                                        }
                                                    });
                                                    Ext.getCmp('Desa').enable();
                                                }
                                            }
                                        }, {
                                            id: 'Desa',
                                            allowBlank: false,
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
                                            fieldLabel: lang('Latitude (Dec)'),
                                            readOnly: m_hakakses_lat_short
                                        }, {
                                            xtype: 'textfield',
                                            id: 'LongSec',
                                            name: 'LongSec',
                                            fieldLabel: lang('Longitude (Dec)'),
                                            readOnly: m_hakakses_long_short
                                        }, {
                                            xtype: 'textfield',
                                            id: 'LatDeg',
                                            name: 'LatDeg',
                                            hidden: true,
                                            fieldLabel: lang('Latitude (Dec)')
                                        }, {
                                            xtype: 'textfield',
                                            id: 'LongDeg',
                                            name: 'LongDeg',
                                            hidden: true,
                                            fieldLabel: lang('Longitude (Dec)')
                                        }
                                        //                                                     , {
                                        //                                                         xtype: 'textfield',
                                        //                                                         id: 'Elevation',
                                        //                                                         name: 'Elevation',
                                        // //                                                        hidden:true,
                                        //                                                         fieldLabel: lang('Elevation(Meter)')
                                        //                                                     }
                                    ]
                                }]
                            }]
                        }]
                    }, {
                        xtype: 'panel',
                        autoScroll: true,
                        height: '700',
                        id: 'panel_staff',
                        disabled: false,
                        title: lang('Staff'),
                        padding: 5,
                        style: 'border:2px solid #D6EDA4',
                        items: [{
                            xtype: 'gridpanel',
                            height: 500,
                            id: 'grid_staff',
                            store: store_staff,
                            width: '100%',
                            loadMask: true,
                            selType: 'rowmodel',
                            dockedItems: [{
                                xtype: 'toolbar',
                                items: [{
                                    icon: varjs.config.base_url + 'images/icons/new/add.png', cls:'Sfr_BtnGridGreen', overCls:'Sfr_BtnGridGreen-Hover',
                                    text: lang('Add'),
                                    cls: m_act_save,
                                    hidden: true,
                                    scope: this,
                                    handler: function() {
                                        RowEditing.cancelEdit();
                                        var r = Ext.create('staff.Model', {
                                            TraderStaffID: '',
                                            StaffID: '',
                                            UserId: '',
                                            StaffSupplychainID: '',
                                            StaffName: '',
                                            PrivateCellphone: '',
                                            OfficialCellphone: '',
                                            PrivateStaffEmail: '',
                                            OfficialStaffEmail: '',
                                            StaffBirth: '',
                                            StaffGender: '',
                                            Educatio: '',
                                            StaffGende: '',
                                            IdentityNumber: '',
                                            Education: '',
                                            FamilyMembers: '',
                                            Address: '',
                                            Position: ''
                                        });
                                        store_staff.insert(0, r);
                                        RowEditing.startEdit(0, 0);
                                    }
                                }, {
                                    icon: varjs.config.base_url + 'images/icons/new/update.png',
                                    cls: m_act_save,
                                    hidden: true,
                                    text: lang('Edit'),
                                    scope: this,
                                    handler: function() {
                                        RowEditing.cancelEdit();
                                        var sm = Ext.getCmp('grid_staff').getSelectionModel().getSelection();
                                        Ext.getCmp('')
                                        RowEditing.startEdit(sm[0].index, 0);
                                    }
                                }, {
                                    itemId: 'remove',
                                    icon: varjs.config.base_url + 'images/icons/new/delete.png',
                                    text: lang('Hapus'),
                                    scope: this,
                                    hidden: true,
                                    handler: function() {
                                        var smb = Ext.getCmp('grid_staff').getSelectionModel().getSelection()[0];
                                        RowEditing.cancelEdit();
                                        Ext.MessageBox.confirm('Message', lang('Apakah anda mau menghapus staff ini ?'), function(btn) {
                                            if (btn == 'yes') {
                                                Ext.Ajax.request({
                                                    waitMsg: lang('Please Wait'),
                                                    url: m_staff,
                                                    method: 'DELETE',
                                                    params: {
                                                        id: smb.raw.TraderStaffID,
                                                        userid: smb.raw.UserId
                                                    },
                                                    success: function(response, opts) {
                                                        var obj = Ext.decode(response.responseText);
                                                        switch (obj.success) {
                                                            case true:
                                                                store_staff.load({
                                                                    params: {
                                                                        id: Ext.getCmp('TraderID').getValue()
                                                                    }
                                                                });
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
                                hidden: true,
                                dataIndex: 'UserId'
                            }, {
                                text: lang('ID'),
                                dataIndex: 'TraderStaffID',
                                width: '5%',
                                editor: {
                                    xtype: 'textfield',
                                    readOnly: true
                                }
                            }, {
                                text: lang('Nama Staff'),
                                dataIndex: 'StaffName',
                                width: '30%',
                                editor: {
                                    xtype: 'textfield',
                                    allowBlank: false
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
                                dataIndex: 'PrivateCellphone',
                                width: '15%',
                                editor: {
                                    xtype: 'textfield',
                                    allowBlank: false
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
                                dataIndex: 'PrivateStaffEmail',
                                width: '20%',
                                editor: {
                                    xtype: 'textfield'
                                }
                            }, {
                                text: lang('Official Email'),
                                dataIndex: 'OfficialStaffEmail',
                                width: '10%',
                                hidden: true,
                                editor: {
                                    xtype: 'textfield'
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
                                    allowBlank: false,
                                    format: 'Y-m-d'
                                }
                            }, {
                                text: lang('Kelamin'),
                                dataIndex: 'StaffGende',
                                width: '10%',
                                editor: {
                                    xtype: 'combo',
                                    store: ckelamin,
                                    queryMode: 'local',
                                    id: 'StaffGender',
                                    displayField: 'label',
                                    valueField: 'id'
                                }
                            }, {
                                text: lang('Education'),
                                dataIndex: 'Educatio',
                                width: '15%',
                                hidden: true,
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
                                'canceledit': function(editor, e, eOpts) {
                                    store_staff.load({
                                        params: {
                                            id: Ext.getCmp('TraderID').getValue()
                                        }
                                    });
                                },
                                'edit': function(editor, e) {
                                    console.log(m_staff);
                                    if (e.record.data.TraderStaffID == '') {
                                        Ext.Ajax.request({
                                            waitMsg: lang('Please wait...'),
                                            url: m_staff,
                                            method: 'POST',
                                            params: {
                                                TraderID: Ext.getCmp('TraderID').getValue(),
                                                StaffName: e.record.data.StaffName,
                                                PrivateCellphone: e.record.data.PrivateCellphone,
                                                PrivateStaffEmail: e.record.data.PrivateStaffEmail,
                                                OfficialCellphone: e.record.data.OfficialCellphone,
                                                OfficialStaffEmail: e.record.data.OfficialStaffEmail,
                                                StaffBirth: e.record.data.StaffBirth,
                                                StaffGender: e.record.data.StaffGende,
                                                IdentityNumber: e.record.data.IdentityNumber,
                                                Education: e.record.data.Educatio,
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
                                                                id: Ext.getCmp('TraderID').getValue()
                                                            }
                                                        });
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
                                                    waitMsg: lang('Please wait...'),
                                                    url: m_staff,
                                                    method: 'PUT',
                                                    params: {
                                                        TraderID: Ext.getCmp('TraderID').getValue(),
                                                        TraderStaffID: e.record.data.TraderStaffID,
                                                        StaffName: e.record.data.StaffName,
                                                        PrivateCellphone: e.record.data.PrivateCellphone,
                                                        PrivateStaffEmail: e.record.data.PrivateStaffEmail,
                                                        OfficialCellphone: e.record.data.OfficialCellphone,
                                                        OfficialStaffEmail: e.record.data.OfficialStaffEmail,
                                                        StaffBirth: e.record.data.StaffBirth,
                                                        StaffGender: e.record.data.StaffGender,
                                                        IdentityNumber: e.record.data.IdentityNumber,
                                                        Education: e.record.data.Education,
                                                        Position: e.record.data.Position,
                                                        Address: e.record.data.Address,
                                                        StaffGende: e.record.data.StaffGende,
                                                        Educatio: e.record.data.Educatio
                                                    },
                                                    success: function(response, opts) {
                                                        var obj = Ext.decode(response.responseText);
                                                        switch (obj.success) {
                                                            case true:
                                                                Ext.MessageBox.alert('Success', obj.message);
                                                                store_staff.load({
                                                                    params: {
                                                                        id: Ext.getCmp('TraderID').getValue()
                                                                    }
                                                                });
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
                    }
                    //                    , {
                    //                        xtype: 'panel',
                    //                        autoScroll: true,
                    //                        id: 'panel_kualitas_standard',
                    //                        disabled: true,
                    //                        title: lang('Standar Kualitas'),
                    //                        padding: 5,
                    //                        style: 'border:2px solid #ADD2ED',
                    //                        items: [{
                    //                                xtype: 'gridpanel',
                    //                                id: 'grid_quality_standard',
                    //                                store: store_quality_standard,
                    //                                width: '100%',
                    //                                loadMask: true,
                    //                                selType: 'rowmodel',
                    //                                dockedItems: [{
                    //                                        xtype: 'toolbar',
                    //                                        items: [{
                    //                                                icon: varjs.config.base_url + 'images/icons/new/add.png', cls:'Sfr_BtnGridGreen', overCls:'Sfr_BtnGridGreen-Hover',
                    //                                                text: lang('Add'),
                    //                                                cls: m_act_save,
                    //                                                scope: this,
                    //                                                handler: function() {
                    //                                                    qsRowEditing.cancelEdit();
                    //                                                    var r = Ext.create('quality_standard.Model', {
                    //                                                        StandardID: '', StandardSupplychainID: '', StandardName: '', Moisture: '', BeanCount: '', Waste: '',
                    //                                                        Mouldy: '', Insect: '', Slaty: ''
                    //                                                    });
                    //                                                    store_quality_standard.insert(0, r);
                    //                                                    qsRowEditing.startEdit(0, 0);
                    //                                                }
                    //                                            }, {
                    //                                                icon: varjs.config.base_url + 'images/icons/new/update.png',
                    //                                                cls: m_act_save,
                    //                                                text: lang('Edit'),
                    //                                                scope: this,
                    //                                                handler: function() {
                    //                                                    qsRowEditing.cancelEdit();
                    //                                                    var sm = Ext.getCmp('grid_quality_standard').getSelectionModel().getSelection();
                    //                                                    qsRowEditing.startEdit(sm[0].index, 0);
                    //                                                }
                    //                                            }, {
                    //                                                itemId: 'remove',
                    //                                                icon: varjs.config.base_url + 'images/icons/new/delete.png',
                    //                                                text: lang('Hapus'),
                    //                                                scope: this,
                    //                                                handler: function() {
                    //                                                    var smb = Ext.getCmp('grid_quality_standard').getSelectionModel().getSelection()[0];
                    //                                                    qsRowEditing.cancelEdit();
                    //                                                    Ext.MessageBox.confirm('Message', lang('Apakah anda mau menghapus data kualitas ini ?'), function(btn) {
                    //                                                        if (btn == 'yes') {
                    //                                                            Ext.Ajax.request({
                    //                                                                waitMsg: lang('Please Wait'),
                    //                                                                url: m_quality_standard,
                    //                                                                method: 'DELETE',
                    //                                                                params: {
                    //                                                                    id: smb.raw.StandardID
                    //                                                                },
                    //                                                                success: function(response, opts) {
                    //                                                                    var obj = Ext.decode(response.responseText);
                    //                                                                    switch (obj.success) {
                    //                                                                        case true:
                    //                                                                            store_quality_standard.load({
                    //                                                                                params: {
                    //                                                                                    id: Ext.getCmp('SupplychainID').getValue()
                    //                                                                                }});
                    //                                                                            break;
                    //                                                                        default:
                    //                                                                            Ext.MessageBox.alert('Warning', obj.message);
                    //                                                                            break;
                    //                                                                    }
                    //                                                                },
                    //                                                                failure: function(response, opts) {
                    //                                                                    var obj = Ext.decode(response.responseText);
                    //                                                                    Ext.MessageBox.alert('error', lang('Could not connect to the database. Retry later'));
                    //                                                                }
                    //                                                            });
                    //                                                        }
                    //                                                    });
                    //                                                }
                    //                                            }]
                    //                                    }],
                    //                                columns: [{
                    //                                        text: lang('No'),
                    //                                        xtype: 'rownumberer',
                    //                                        width: '5%'
                    //                                    }, {
                    //                                        text: lang('Nama'),
                    //                                        dataIndex: 'StandardName',
                    //                                        width: '35%',
                    //                                        editor: {
                    //                                            xtype: 'textfield',
                    //                                            allowBlank: false
                    //                                        }
                    //                                    }, {
                    //                                        text: lang('Moisture'),
                    //                                        dataIndex: 'Moisture',
                    //                                        width: '10%',
                    //                                        editor: {
                    //                                            xtype: 'textfield',
                    //                                            allowBlank: false
                    //                                        }
                    //                                    }, {
                    //                                        text: lang('BeanCount'),
                    //                                        dataIndex: 'BeanCount',
                    //                                        width: '10%',
                    //                                        editor: {
                    //                                            xtype: 'textfield',
                    //                                            allowBlank: false
                    //                                        }
                    //                                    }, {
                    //                                        text: lang('Waste'),
                    //                                        dataIndex: 'Waste',
                    //                                        width: '10%',
                    //                                        editor: {
                    //                                            xtype: 'textfield',
                    //                                            allowBlank: false
                    //                                        }
                    //                                    }, {
                    //                                        text: lang('Mouldy'),
                    //                                        dataIndex: 'Mouldy',
                    //                                        width: '10%',
                    //                                        editor: {
                    //                                            xtype: 'textfield',
                    //                                            allowBlank: false
                    //                                        }
                    //                                    }, {
                    //                                        text: lang('Insect'),
                    //                                        dataIndex: 'Insect',
                    //                                        width: '10%',
                    //                                        editor: {
                    //                                            xtype: 'textfield',
                    //                                            allowBlank: false
                    //                                        }
                    //                                    }, {
                    //                                        text: lang('Slaty'),
                    //                                        dataIndex: 'Slaty',
                    //                                        width: '10%',
                    //                                        editor: {
                    //                                            xtype: 'textfield',
                    //                                            allowBlank: false
                    //                                        }
                    //                                    }],
                    //                                plugins: [qsRowEditing],
                    //                                listeners: {
                    //                                    'canceledit': function(editor, e, eOpts) {
                    //                                        store_quality_standard.load({
                    //                                            params: {
                    //                                                id: Ext.getCmp('id').getValue()
                    //                                            }});
                    //                                    },
                    //                                    'edit': function(editor, e) {
                    //                                        if (e.record.data.StandardID == '') {
                    //                                            Ext.Ajax.request({
                    //                                                waitMsg: lang('Please wait...'),
                    //                                                url: m_quality_standard,
                    //                                                method: 'POST',
                    //                                                params: {
                    //                                                    StandardSupplychainID: Ext.getCmp('SupplychainID').getValue(),
                    //                                                    StandardName: e.record.data.StandardName,
                    //                                                    Moisture: e.record.data.Moisture,
                    //                                                    BeanCount: e.record.data.BeanCount,
                    //                                                    Waste: e.record.data.Waste,
                    //                                                    Mouldy: e.record.data.Mouldy,
                    //                                                    Insect: e.record.data.Insect,
                    //                                                    Slaty: e.record.data.Slaty,
                    //                                                },
                    //                                                success: function(response, opts) {
                    //                                                    var obj = Ext.decode(response.responseText);
                    //                                                    switch (obj.success) {
                    //                                                        case true:
                    //                                                            Ext.MessageBox.alert('Success', obj.message);
                    //                                                            store_quality_standard.load({
                    //                                                                params: {
                    //                                                                    id: Ext.getCmp('SupplychainID').getValue()
                    //                                                                }});
                    //                                                            break;
                    //                                                        default:
                    //                                                            Ext.MessageBox.alert('Warning', obj.message);
                    //                                                            break;
                    //                                                    }
                    //                                                },
                    //                                                failure: function(response, opts) {
                    //                                                    var obj = Ext.decode(response.responseText);
                    //                                                    Ext.MessageBox.alert('error', lang('Could not connect to the database. Retry later'));
                    //                                                }
                    //                                            });
                    //                                        } else {
                    //                                            Ext.MessageBox.confirm('Message', lang('Update data quality standard ini ?'), function(btn) {
                    //                                                if (btn == 'yes') {
                    //                                                    Ext.Ajax.request({
                    //                                                        waitMsg: lang('Please wait...'),
                    //                                                        url: m_quality_standard,
                    //                                                        method: 'PUT',
                    //                                                        params: {
                    //                                                            StandardSupplychainID: Ext.getCmp('SupplychainID').getValue(),
                    //                                                            StandardID: e.record.data.StandardID,
                    //                                                            StandardName: e.record.data.StandardName,
                    //                                                            Moisture: e.record.data.Moisture,
                    //                                                            BeanCount: e.record.data.BeanCount,
                    //                                                            Waste: e.record.data.Waste,
                    //                                                            Mouldy: e.record.data.Mouldy,
                    //                                                            Insect: e.record.data.Insect,
                    //                                                            Slaty: e.record.data.Slaty,
                    //                                                        },
                    //                                                        success: function(response, opts) {
                    //                                                            var obj = Ext.decode(response.responseText);
                    //                                                            switch (obj.success) {
                    //                                                                case true:
                    //                                                                    Ext.MessageBox.alert('Success', obj.message);
                    //                                                                    store_quality_standard.load({
                    //                                                                        params: {
                    //                                                                            id: Ext.getCmp('SupplychainID').getValue()
                    //                                                                        }});
                    //                                                                    break;
                    //                                                                default:
                    //                                                                    Ext.MessageBox.alert('Warning', obj.message);
                    //                                                                    break;
                    //                                                            }
                    //                                                        },
                    //                                                        failure: function(response, opts) {
                    //                                                            var obj = Ext.decode(response.responseText);
                    //                                                            Ext.MessageBox.alert('error', lang('Could not connect to the database. Retry later'));
                    //                                                        }
                    //                                                    });
                    //                                                }
                    //                                            });
                    //                                        }
                    //                                    }
                    //                                }
                    //                            }]
                    //                    }, {
                    //                        xtype: 'panel',
                    //                        autoScroll: true,
                    //                        id: 'panel_kualitas',
                    //                        disabled: true,
                    //                        title: lang('Kualitas'),
                    //                        padding: 5,
                    //                        style: 'border:2px solid #ADD2ED',
                    //                        items: [{
                    //                                xtype: 'gridpanel',
                    //                                id: 'grid_quality',
                    //                                store: store_quality,
                    //                                width: '100%',
                    //                                loadMask: true,
                    //                                selType: 'rowmodel',
                    //                                dockedItems: [{
                    //                                        xtype: 'toolbar',
                    //                                        items: [{
                    //                                                icon: varjs.config.base_url + 'images/icons/new/add.png', cls:'Sfr_BtnGridGreen', overCls:'Sfr_BtnGridGreen-Hover',
                    //                                                text: lang('Add'),
                    //                                                cls: m_act_save,
                    //                                                scope: this,
                    //                                                handler: function() {
                    //                                                    qRowEditing.cancelEdit();
                    //                                                    var r = Ext.create('quality.Model', {
                    //                                                        QualityID: '', QualitySupplychainID: '', QualityDate: '', StandardName: '', Moisture: '', BeanCount: '', Waste: '',
                    //                                                        Mouldy: '', Insect: '', Slaty: '', StandardID: ''
                    //                                                    });
                    //                                                    store_quality.insert(0, r);
                    //                                                    qRowEditing.startEdit(0, 0);
                    //                                                    store_standard.load({
                    //                                                        params: {
                    //                                                            id: Ext.getCmp('SupplychainID').getValue()
                    //                                                        }});
                    //                                                }
                    //                                            }, {
                    //                                                icon: varjs.config.base_url + 'images/icons/new/update.png',
                    //                                                cls: m_act_save,
                    //                                                text: lang('Edit'),
                    //                                                scope: this,
                    //                                                handler: function() {
                    //                                                    qRowEditing.cancelEdit();
                    //                                                    var sm = Ext.getCmp('grid_quality').getSelectionModel().getSelection();
                    //                                                    store_standard.load({
                    //                                                        params: {
                    //                                                            id: Ext.getCmp('SupplychainID').getValue()
                    //                                                        }});
                    //                                                    qRowEditing.startEdit(sm[0].index, 0);
                    //                                                }
                    //                                            }, {
                    //                                                itemId: 'remove',
                    //                                                icon: varjs.config.base_url + 'images/icons/new/delete.png',
                    //                                                text: lang('Hapus'),
                    //                                                scope: this,
                    //                                                handler: function() {
                    //                                                    var smb = Ext.getCmp('grid_quality').getSelectionModel().getSelection()[0];
                    //                                                    qRowEditing.cancelEdit();
                    //                                                    Ext.MessageBox.confirm('Message', lang('Apakah anda mau menghapus data kualitas ini ?'), function(btn) {
                    //                                                        if (btn == 'yes') {
                    //                                                            Ext.Ajax.request({
                    //                                                                waitMsg: lang('Please Wait'),
                    //                                                                url: m_quality,
                    //                                                                method: 'DELETE',
                    //                                                                params: {
                    //                                                                    id: smb.raw.QualityID
                    //                                                                },
                    //                                                                success: function(response, opts) {
                    //                                                                    var obj = Ext.decode(response.responseText);
                    //                                                                    switch (obj.success) {
                    //                                                                        case true:
                    //                                                                            store_quality.load({
                    //                                                                                params: {
                    //                                                                                    id: Ext.getCmp('SupplychainID').getValue()
                    //                                                                                }});
                    //                                                                            break;
                    //                                                                        default:
                    //                                                                            Ext.MessageBox.alert('Warning', obj.message);
                    //                                                                            break;
                    //                                                                    }
                    //                                                                },
                    //                                                                failure: function(response, opts) {
                    //                                                                    var obj = Ext.decode(response.responseText);
                    //                                                                    Ext.MessageBox.alert('error', lang('Could not connect to the database. Retry later'));
                    //                                                                }
                    //                                                            });
                    //                                                        }
                    //                                                    });
                    //                                                }
                    //                                            }]
                    //                                    }],
                    //                                columns: [{
                    //                                        text: lang('No'),
                    //                                        xtype: 'rownumberer',
                    //                                        width: '5%'
                    //                                    }, {
                    //                                        text: lang('Tanggal'),
                    //                                        dataIndex: 'QualityDate',
                    //                                        width: '15%',
                    //                                        editor: {
                    //                                            xtype: 'datefield',
                    //                                            allowBlank: false,
                    //                                            format: 'Y-m-d'
                    //                                        }
                    //                                    }, {
                    //                                        text: lang('Nama'),
                    //                                        dataIndex: 'StandardName',
                    //                                        width: '20%',
                    //                                        editor: {
                    //                                            xtype: 'combo',
                    //                                            store: store_standard,
                    //                                            id: 'StandardID',
                    //                                            queryMode: 'local',
                    //                                            displayField: 'label',
                    //                                            valueField: 'id',
                    //                                            listeners: {
                    //                                                change: function(cb, nv, ov) {
                    //                                                    Ext.Ajax.request({
                    //                                                        url: m_quality_standard,
                    //                                                        method: 'GET',
                    //                                                        params: {id: this.value},
                    //                                                        success: function(fp, o) {
                    //                                                            var r = Ext.decode(fp.responseText);
                    //                                                            Ext.getCmp('iMoisture').setValue(r.Moisture);
                    //                                                            Ext.getCmp('iBeanCount').setValue(r.BeanCount);
                    //                                                            Ext.getCmp('iWaste').setValue(r.Waste);
                    //                                                            Ext.getCmp('iMouldy').setValue(r.Mouldy);
                    //                                                            Ext.getCmp('iInsect').setValue(r.Insect);
                    //                                                            Ext.getCmp('iSlaty').setValue(r.Slaty);
                    //                                                        }
                    //                                                    });
                    //                                                }
                    //                                            }
                    //                                        }
                    //                                    }, {
                    //                                        text: lang('Moisture'),
                    //                                        dataIndex: 'Moisture',
                    //                                        width: '10%',
                    //                                        editor: {
                    //                                            xtype: 'textfield',
                    //                                            allowBlank: false,
                    //                                            id: 'iMoisture',
                    //                                            readOnly: true
                    //                                        }
                    //                                    }, {
                    //                                        text: lang('BeanCount'),
                    //                                        dataIndex: 'BeanCount',
                    //                                        width: '10%',
                    //                                        editor: {
                    //                                            xtype: 'textfield',
                    //                                            allowBlank: false,
                    //                                            id: 'iBeanCount',
                    //                                            readOnly: true
                    //                                        }
                    //                                    }, {
                    //                                        text: lang('Waste'),
                    //                                        dataIndex: 'Waste',
                    //                                        width: '10%',
                    //                                        editor: {
                    //                                            xtype: 'textfield',
                    //                                            allowBlank: false,
                    //                                            id: 'iWaste',
                    //                                            readOnly: true
                    //                                        }
                    //                                    }, {
                    //                                        text: lang('Mouldy'),
                    //                                        dataIndex: 'Mouldy',
                    //                                        width: '10%',
                    //                                        editor: {
                    //                                            xtype: 'textfield',
                    //                                            allowBlank: false,
                    //                                            id: 'iMouldy',
                    //                                            readOnly: true
                    //                                        }
                    //                                    }, {
                    //                                        text: lang('Insect'),
                    //                                        dataIndex: 'Insect',
                    //                                        width: '10%',
                    //                                        editor: {
                    //                                            xtype: 'textfield',
                    //                                            allowBlank: false,
                    //                                            id: 'iInsect',
                    //                                            readOnly: true
                    //                                        }
                    //                                    }, {
                    //                                        text: lang('Slaty'),
                    //                                        dataIndex: 'Slaty',
                    //                                        width: '10%',
                    //                                        editor: {
                    //                                            xtype: 'textfield',
                    //                                            allowBlank: false,
                    //                                            id: 'iSlaty',
                    //                                            readOnly: true
                    //                                        }
                    //                                    }],
                    //                                plugins: [qRowEditing],
                    //                                listeners: {
                    //                                    itemdblclick: function(dv, record, item, index, e) {
                    //                                        store_standard.load({
                    //                                            params: {
                    //                                                id: Ext.getCmp('SupplychainID').getValue()
                    //                                            }});
                    //                                    },
                    //                                    'canceledit': function(editor, e, eOpts) {
                    //                                        store_quality.load({
                    //                                            params: {
                    //                                                id: Ext.getCmp('SupplychainID').getValue()
                    //                                            }});
                    //                                    },
                    //                                    'edit': function(editor, e) {
                    //                                        if (e.record.data.QualityID == '') {
                    //                                            Ext.Ajax.request({
                    //                                                waitMsg: lang('Please wait...'),
                    //                                                url: m_quality,
                    //                                                method: 'POST',
                    //                                                params: {
                    //                                                    QualitySupplychainID: Ext.getCmp('SupplychainID').getValue(),
                    //                                                    QualityDate: e.record.data.QualityDate,
                    //                                                    StandardID: Ext.getCmp('StandardID').getValue(),
                    //                                                    Moisture: e.record.data.Moisture,
                    //                                                    BeanCount: e.record.data.BeanCount,
                    //                                                    Waste: e.record.data.Waste,
                    //                                                    Mouldy: e.record.data.Mouldy,
                    //                                                    Insect: e.record.data.Insect,
                    //                                                    Slaty: e.record.data.Slaty,
                    //                                                },
                    //                                                success: function(response, opts) {
                    //                                                    var obj = Ext.decode(response.responseText);
                    //                                                    switch (obj.success) {
                    //                                                        case true:
                    //                                                            Ext.MessageBox.alert('Success', obj.message);
                    //                                                            store_quality.load({
                    //                                                                params: {
                    //                                                                    id: Ext.getCmp('SupplychainID').getValue()
                    //                                                                }});
                    //                                                            break;
                    //                                                        default:
                    //                                                            Ext.MessageBox.alert('Warning', obj.message);
                    //                                                            break;
                    //                                                    }
                    //                                                },
                    //                                                failure: function(response, opts) {
                    //                                                    var obj = Ext.decode(response.responseText);
                    //                                                    Ext.MessageBox.alert('error', lang('Could not connect to the database. Retry later'));
                    //                                                }
                    //                                            });
                    //                                        } else {
                    //                                            Ext.MessageBox.confirm('Message', lang('Update data quality ini ?'), function(btn) {
                    //                                                if (btn == 'yes') {
                    //                                                    Ext.Ajax.request({
                    //                                                        waitMsg: lang('Please wait...'),
                    //                                                        url: m_quality,
                    //                                                        method: 'PUT',
                    //                                                        params: {
                    //                                                            QualitySupplychainID: Ext.getCmp('SupplychainID').getValue(),
                    //                                                            QualityID: e.record.data.QualityID,
                    //                                                            QualityDate: e.record.data.QualityDate,
                    //                                                            StandardID: Ext.getCmp('StandardID').getValue(),
                    //                                                            Moisture: e.record.data.Moisture,
                    //                                                            BeanCount: e.record.data.BeanCount,
                    //                                                            Waste: e.record.data.Waste,
                    //                                                            Mouldy: e.record.data.Mouldy,
                    //                                                            Insect: e.record.data.Insect,
                    //                                                            Slaty: e.record.data.Slaty,
                    //                                                        },
                    //                                                        success: function(response, opts) {
                    //                                                            var obj = Ext.decode(response.responseText);
                    //                                                            switch (obj.success) {
                    //                                                                case true:
                    //                                                                    Ext.MessageBox.alert('Success', obj.message);
                    //                                                                    store_quality.load({
                    //                                                                        params: {
                    //                                                                            id: Ext.getCmp('SupplychainID').getValue()
                    //                                                                        }});
                    //                                                                    break;
                    //                                                                default:
                    //                                                                    Ext.MessageBox.alert('Warning', obj.message);
                    //                                                                    break;
                    //                                                            }
                    //                                                        },
                    //                                                        failure: function(response, opts) {
                    //                                                            var obj = Ext.decode(response.responseText);
                    //                                                            Ext.MessageBox.alert('error', lang('Could not connect to the database. Retry later'));
                    //                                                        }
                    //                                                    });
                    //                                                }
                    //                                            });
                    //                                        }
                    //                                    }
                    //                                }
                    //                            }]
                    //                    }, {
                    //                        xtype: 'panel',
                    //                        autoScroll: true,
                    //                        id: 'panel_harga',
                    //                        disabled: true,
                    //                        title: lang('Harga'),
                    //                        padding: 5,
                    //                        style: 'border:2px solid #ADD2ED',
                    //                        items: [{
                    //                                xtype: 'gridpanel',
                    //                                id: 'grid_price',
                    //                                store: store_price,
                    //                                width: '100%',
                    //                                loadMask: true,
                    //                                selType: 'rowmodel',
                    //                                dockedItems: [{
                    //                                        xtype: 'toolbar',
                    //                                        items: [{
                    //                                                icon: varjs.config.base_url + 'images/icons/new/add.png', cls:'Sfr_BtnGridGreen', overCls:'Sfr_BtnGridGreen-Hover',
                    //                                                text: lang('Add'),
                    //                                                cls: m_act_save,
                    //                                                scope: this,
                    //                                                handler: function() {
                    //                                                    pRowEditing.cancelEdit();
                    //                                                    var r = Ext.create('price.Model', {
                    //                                                        PriceID: '', PriceSupplychainID: '', PriceDate: '', Price: '', District: ''
                    //                                                    });
                    //                                                    store_price.insert(0, r);
                    //                                                    pRowEditing.startEdit(0, 0);
                    //                                                }
                    //                                            }, {
                    //                                                icon: varjs.config.base_url + 'images/icons/new/update.png',
                    //                                                cls: m_act_save,
                    //                                                text: lang('Edit'),
                    //                                                scope: this,
                    //                                                handler: function() {
                    //                                                    pRowEditing.cancelEdit();
                    //                                                    var sm = Ext.getCmp('grid_price').getSelectionModel().getSelection();
                    //                                                    pRowEditing.startEdit(sm[0].index, 0);
                    //                                                }
                    //                                            }, {
                    //                                                itemId: 'remove',
                    //                                                icon: varjs.config.base_url + 'images/icons/new/delete.png',
                    //                                                text: lang('Hapus'),
                    //                                                scope: this,
                    //                                                handler: function() {
                    //                                                    var smb = Ext.getCmp('grid_price').getSelectionModel().getSelection()[0];
                    //                                                    pRowEditing.cancelEdit();
                    //                                                    Ext.MessageBox.confirm('Message', lang('Apakah anda mau menghapus harga ini ?'), function(btn) {
                    //                                                        if (btn == 'yes') {
                    //                                                            Ext.Ajax.request({
                    //                                                                waitMsg: lang('Please Wait'),
                    //                                                                url: m_price,
                    //                                                                method: 'DELETE',
                    //                                                                params: {
                    //                                                                    id: smb.raw.PriceID
                    //                                                                },
                    //                                                                success: function(response, opts) {
                    //                                                                    var obj = Ext.decode(response.responseText);
                    //                                                                    switch (obj.success) {
                    //                                                                        case true:
                    //                                                                            store_price.load({
                    //                                                                                params: {
                    //                                                                                    id: Ext.getCmp('SupplychainID').getValue()
                    //                                                                                }});
                    //                                                                            break;
                    //                                                                        default:
                    //                                                                            Ext.MessageBox.alert('Warning', obj.message);
                    //                                                                            break;
                    //                                                                    }
                    //                                                                },
                    //                                                                failure: function(response, opts) {
                    //                                                                    var obj = Ext.decode(response.responseText);
                    //                                                                    Ext.MessageBox.alert('error', lang('Could not connect to the database. Retry later'));
                    //                                                                }
                    //                                                            });
                    //                                                        }
                    //                                                    });
                    //                                                }
                    //                                            }]
                    //                                    }],
                    //                                columns: [{
                    //                                        text: lang('No'),
                    //                                        xtype: 'rownumberer',
                    //                                        width: '5%'
                    //                                    }, {
                    //                                        text: lang('Tanggal'),
                    //                                        dataIndex: 'PriceDate',
                    //                                        width: '55%',
                    //                                        editor: {
                    //                                            xtype: 'datefield',
                    //                                            allowBlank: false,
                    //                                            format: 'Y-m-d'
                    //                                        }
                    //                                    }, {
                    //                                        text: lang('District'),
                    //                                        dataIndex: 'District',
                    //                                        width: '20%',
                    //                                        editor: {
                    //                                            xtype: 'combo',
                    //                                            store: mc_Kabupaten,
                    //                                            queryMode: 'local',
                    //                                            id: 'District',
                    //                                            displayField: 'label',
                    //                                            valueField: 'label'
                    //                                        }
                    //                                    }, {
                    //                                        text: lang('Harga'),
                    //                                        dataIndex: 'Price',
                    //                                        width: '20%',
                    //                                        editor: {
                    //                                            xtype: 'textfield',
                    //                                            allowBlank: false
                    //                                        }
                    //                                    }],
                    //                                plugins: [pRowEditing],
                    //                                listeners: {
                    //                                    'canceledit': function(editor, e, eOpts) {
                    //                                        store_price.load({
                    //                                            params: {
                    //                                                id: Ext.getCmp('SupplychainID').getValue()
                    //                                            }});
                    //                                    },
                    //                                    'edit': function(editor, e) {
                    //                                        if (e.record.data.PriceID == '') {
                    //                                            Ext.Ajax.request({
                    //                                                waitMsg: lang('Please wait...'),
                    //                                                url: m_price,
                    //                                                method: 'POST',
                    //                                                params: {
                    //                                                    PriceSupplychainID: Ext.getCmp('SupplychainID').getValue(),
                    //                                                    PriceDate: e.record.data.PriceDate,
                    //                                                    Price: e.record.data.Price,
                    //                                                    District: e.record.data.District
                    //                                                },
                    //                                                success: function(response, opts) {
                    //                                                    var obj = Ext.decode(response.responseText);
                    //                                                    switch (obj.success) {
                    //                                                        case true:
                    //                                                            Ext.MessageBox.alert('Success', obj.message);
                    //                                                            store_price.load({
                    //                                                                params: {
                    //                                                                    id: Ext.getCmp('SupplychainID').getValue()
                    //                                                                }});
                    //                                                            break;
                    //                                                        default:
                    //                                                            Ext.MessageBox.alert('Warning', obj.message);
                    //                                                            break;
                    //                                                    }
                    //                                                },
                    //                                                failure: function(response, opts) {
                    //                                                    var obj = Ext.decode(response.responseText);
                    //                                                    Ext.MessageBox.alert('error', lang('Could not connect to the database. Retry later'));
                    //                                                }
                    //                                            });
                    //                                        } else {
                    //                                            Ext.MessageBox.confirm('Message', lang('Update data price ini ?'), function(btn) {
                    //                                                if (btn == 'yes') {
                    //                                                    Ext.Ajax.request({
                    //                                                        waitMsg: lang('Please wait...'),
                    //                                                        url: m_price,
                    //                                                        method: 'PUT',
                    //                                                        params: {
                    //                                                            PriceSupplychainID: Ext.getCmp('SupplychainID').getValue(),
                    //                                                            PriceID: e.record.data.PriceID,
                    //                                                            PriceDate: e.record.data.PriceDate,
                    //                                                            Price: e.record.data.Price,
                    //                                                            District: e.record.data.District
                    //                                                        },
                    //                                                        success: function(response, opts) {
                    //                                                            var obj = Ext.decode(response.responseText);
                    //                                                            switch (obj.success) {
                    //                                                                case true:
                    //                                                                    Ext.MessageBox.alert('Success', obj.message);
                    //                                                                    store_price.load({
                    //                                                                        params: {
                    //                                                                            id: Ext.getCmp('SupplychainID').getValue()
                    //                                                                        }});
                    //                                                                    break;
                    //                                                                default:
                    //                                                                    Ext.MessageBox.alert('Warning', obj.message);
                    //                                                                    break;
                    //                                                            }
                    //                                                        },
                    //                                                        failure: function(response, opts) {
                    //                                                            var obj = Ext.decode(response.responseText);
                    //                                                            Ext.MessageBox.alert('error', lang('Could not connect to the database. Retry later'));
                    //                                                        }
                    //                                                    });
                    //                                                }
                    //                                            });
                    //                                        }
                    //                                    }
                    //                                }
                    //                            }]
                    //                    }, {
                    //                        xtype: 'panel',
                    //                        autoScroll: true,
                    //                        id: 'panel_kemasan',
                    //                        disabled: true,
                    //                        title: lang('Kemasan'),
                    //                        padding: 5,
                    //                        style: 'border:2px solid #ADD2ED',
                    //                        items: [{
                    //                                xtype: 'gridpanel',
                    //                                id: 'grid_package',
                    //                                store: store_package,
                    //                                width: '100%',
                    //                                loadMask: true,
                    //                                selType: 'rowmodel',
                    //                                dockedItems: [{
                    //                                        xtype: 'toolbar',
                    //                                        items: [{
                    //                                                icon: varjs.config.base_url + 'images/icons/new/add.png', cls:'Sfr_BtnGridGreen', overCls:'Sfr_BtnGridGreen-Hover',
                    //                                                text: lang('Add'),
                    //                                                cls: m_act_save,
                    //                                                scope: this,
                    //                                                handler: function() {
                    //                                                    paRowEditing.cancelEdit();
                    //                                                    var r = Ext.create('package.Model', {
                    //                                                        PackageID: '', PackageSupplychainID: '', PackageType: '', PackageWeight: ''
                    //                                                    });
                    //                                                    store_package.insert(0, r);
                    //                                                    paRowEditing.startEdit(0, 0);
                    //                                                }
                    //                                            }, {
                    //                                                icon: varjs.config.base_url + 'images/icons/new/update.png',
                    //                                                cls: m_act_save,
                    //                                                text: lang('Edit'),
                    //                                                scope: this,
                    //                                                handler: function() {
                    //                                                    paRowEditing.cancelEdit();
                    //                                                    var sm = Ext.getCmp('grid_package').getSelectionModel().getSelection();
                    //                                                    paRowEditing.startEdit(sm[0].index, 0);
                    //                                                }
                    //                                            }, {
                    //                                                itemId: 'remove',
                    //                                                icon: varjs.config.base_url + 'images/icons/new/delete.png',
                    //                                                text: lang('Hapus'),
                    //                                                scope: this,
                    //                                                handler: function() {
                    //                                                    var smp = Ext.getCmp('grid_package').getSelectionModel().getSelection()[0];
                    //                                                    paRowEditing.cancelEdit();
                    //                                                    Ext.MessageBox.confirm('Message', lang('Apakah anda mau menghapus package ini ?'), function(btn) {
                    //                                                        if (btn == 'yes') {
                    //                                                            Ext.Ajax.request({
                    //                                                                waitMsg: lang('Please Wait'),
                    //                                                                url: m_package,
                    //                                                                method: 'DELETE',
                    //                                                                params: {
                    //                                                                    id: smp.raw.PackageID
                    //                                                                },
                    //                                                                success: function(response, opts) {
                    //                                                                    var obj = Ext.decode(response.responseText);
                    //                                                                    switch (obj.success) {
                    //                                                                        case true:
                    //                                                                            store_package.load({
                    //                                                                                params: {
                    //                                                                                    id: Ext.getCmp('SupplychainID').getValue()
                    //                                                                                }});
                    //                                                                            break;
                    //                                                                        default:
                    //                                                                            Ext.MessageBox.alert('Warning', obj.message);
                    //                                                                            break;
                    //                                                                    }
                    //                                                                },
                    //                                                                failure: function(response, opts) {
                    //                                                                    var obj = Ext.decode(response.responseText);
                    //                                                                    Ext.MessageBox.alert('error', lang('Could not connect to the database. Retry later'));
                    //                                                                }
                    //                                                            });
                    //                                                        }
                    //                                                    });
                    //                                                }
                    //                                            }]
                    //                                    }],
                    //                                columns: [{
                    //                                        text: lang('No'),
                    //                                        xtype: 'rownumberer',
                    //                                        width: '5%'
                    //                                    }, {
                    //                                        text: lang('Nama'),
                    //                                        dataIndex: 'PackageType',
                    //                                        width: '75%',
                    //                                        editor: {
                    //                                            xtype: 'textfield',
                    //                                            allowBlank: false
                    //                                        }
                    //                                    }, {
                    //                                        text: lang('Berat Pemotongan'),
                    //                                        dataIndex: 'PackageWeight',
                    //                                        width: '20%',
                    //                                        editor: {
                    //                                            xtype: 'textfield',
                    //                                            allowBlank: false
                    //                                        }
                    //                                    }],
                    //                                plugins: [paRowEditing],
                    //                                listeners: {
                    //                                    'canceledit': function(editor, e, eOpts) {
                    //                                        store_package.load({
                    //                                            params: {
                    //                                                id: Ext.getCmp('id').getValue()
                    //                                            }});
                    //                                    },
                    //                                    'edit': function(editor, e) {
                    //                                        if (e.record.data.PackageID == '') {
                    //                                            Ext.Ajax.request({
                    //                                                waitMsg: lang('Please wait...'),
                    //                                                url: m_package,
                    //                                                method: 'POST',
                    //                                                params: {
                    //                                                    PackageSupplychainID: Ext.getCmp('SupplychainID').getValue(),
                    //                                                    PackageType: e.record.data.PackageType,
                    //                                                    PackageWeight: e.record.data.PackageWeight
                    //                                                },
                    //                                                success: function(response, opts) {
                    //                                                    var obj = Ext.decode(response.responseText);
                    //                                                    switch (obj.success) {
                    //                                                        case true:
                    //                                                            Ext.MessageBox.alert('Success', obj.message);
                    //                                                            store_package.load({
                    //                                                                params: {
                    //                                                                    id: Ext.getCmp('SupplychainID').getValue()
                    //                                                                }});
                    //                                                            break;
                    //                                                        default:
                    //                                                            Ext.MessageBox.alert('Warning', obj.message);
                    //                                                            break;
                    //                                                    }
                    //                                                },
                    //                                                failure: function(response, opts) {
                    //                                                    var obj = Ext.decode(response.responseText);
                    //                                                    Ext.MessageBox.alert('error', lang('Could not connect to the database. Retry later'));
                    //                                                }
                    //                                            });
                    //                                        } else {
                    //                                            Ext.MessageBox.confirm('Message', lang('Update data package ini ?'), function(btn) {
                    //                                                if (btn == 'yes') {
                    //                                                    Ext.Ajax.request({
                    //                                                        waitMsg: lang('Please wait...'),
                    //                                                        url: m_package,
                    //                                                        method: 'PUT',
                    //                                                        params: {
                    //                                                            PackageSupplychainID: Ext.getCmp('SupplychainID').getValue(),
                    //                                                            PackageID: e.record.data.PackageID,
                    //                                                            PackageType: e.record.data.PackageType,
                    //                                                            PackageWeight: e.record.data.PackageWeight
                    //                                                        },
                    //                                                        success: function(response, opts) {
                    //                                                            var obj = Ext.decode(response.responseText);
                    //                                                            switch (obj.success) {
                    //                                                                case true:
                    //                                                                    Ext.MessageBox.alert('Success', obj.message);
                    //                                                                    store_package.load({
                    //                                                                        params: {
                    //                                                                            id: Ext.getCmp('SupplychainID').getValue()
                    //                                                                        }});
                    //                                                                    break;
                    //                                                                default:
                    //                                                                    Ext.MessageBox.alert('Warning', obj.message);
                    //                                                                    break;
                    //                                                            }
                    //                                                        },
                    //                                                        failure: function(response, opts) {
                    //                                                            var obj = Ext.decode(response.responseText);
                    //                                                            Ext.MessageBox.alert('error', lang('Could not connect to the database. Retry later'));
                    //                                                        }
                    //                                                    });
                    //                                                }
                    //                                            });
                    //                                        }
                    //                                    }
                    //                                }
                    //                            }
                ]
                //                    }]
        }],
        buttons: [{
            id: 'cetakButton',
            text: lang('Cetak'),
            margin: '5px',
            scale: 'large',
            ui: 's-button',
            cls: 's-blue',
            handler: function() {
                displayBeforeCetak();
            }
        }, {
            id: 'saveButton',
            text: lang('Save'),
            margin: '5px',
            scale: 'large',
            ui: 's-button',
            cls: 's-blue',
            handler: function() {
                var form = this.up('form').getForm();
                var urle = m_crud + 'data/trader';
                if (form.isValid()) {
                    form.submit({
                        url: urle,
                        method: 'POST',
                        waitMsg: lang('Sending data...'),
                        success: function(fp, o) {
                            Ext.MessageBox.alert('Success', 'Data saved.');
                        }
                    });
                    win.hide(this, function() {
                        store.load();
                    });
                } else {
                    Ext.Msg.alert("Error!", "Your form is invalid!");
                }
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
    var win = Ext.create('widget.window', {
        title: lang('Trader'),
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

    function fillNurseryForm(NurseryNr) {
        Ext.getCmp('dataFormNurseyTrader').getForm().load({
            url: m_crud + 'dataFormNursery',
            method: 'GET',
            params: {
                id: Ext.getCmp('TraderID').getValue(),
                nursery_id: NurseryNr
            },
            success: function(form, action) {
                //  console.log(form)
                var actionData = Ext.decode(action.response.responseText);
                var d = actionData.data;
                console.log(d);

                store_nursey_trans.load({
                    params: {
                        id: d.NurseryID
                    }
                });
                store_nursey_monitoring.load({
                    params: {
                        nursery_id: d.NurseryID
                    }
                });
                if (d.NurseryID == null) {
                    store_nursey_trans.removeAll();
                    store_nursey_trans.sync()
                    Ext.getCmp('gnurseypenjualan_idtrader').setDisabled(true);
                    Ext.getCmp('gnurseymonitoring_idtrader').setDisabled(true);
                } else {
                    store_nursey_trans.load({
                        params: {
                            id: d.NurseryID
                        }
                    });
                    Ext.getCmp('NurseryID_idtrader').setValue(d.NurseryID);
                    Ext.getCmp('NurseryNr_idtrader').setValue(d.NurseryNr);
                    Ext.getCmp('nid_obj_idtrader').setValue(d.ObjID);
                    Ext.getCmp('ntype_obj_idtrader').setValue(d.ObjType);
                    //Ext.getCmp('Responsible_idtrader').setValue(d.Responsible);
                    Ext.getCmp('Established_idtrader').setValue(d.Established);
                    Ext.getCmp('Panjang_idtrader').setValue(d.Panjang);
                    Ext.getCmp('Lebar_idtrader').setValue(d.Lebar);
                    Ext.getCmp('Luas_idtrader').setValue(d.Luas);
                    Ext.getCmp('Kapasitas_idtrader').setValue(nnumber_format(d.Kapasitas));
                    Ext.getCmp('Latitude_idtrader').setValue(d.Latitude);
                    Ext.getCmp('Longitude_idtrader').setValue(d.Longitude);
                    if (d.CertificationStatus == 'Yes') {
                        Ext.getCmp('CertificationStatus1_idtrader').setValue(true);
                        Ext.getCmp('DateCertification_idtrader').setValue(d.DateCertification);
                        Ext.getCmp('DateAppliedCertification_idtrader').setValue(d.DateAppliedCertification);
                    } else {
                        Ext.getCmp('CertificationStatus2_idtrader').setValue(true);
                    }
                    Ext.getCmp('gnurseypenjualan_idtrader').setDisabled(false);
                    Ext.getCmp('gnurseymonitoring_idtrader').setDisabled(false);

                    //photo===========================================
                    if(d.Photo != ""){
                        var fotoUser = m_api_base_url + '/images/nursery/' + d.Photo;
                        Ext.getCmp('Photo_old_idtrader').setValue(d.Photo);
                        checkImageExists(fotoUser, function(existsImage) {
                            if (existsImage == true) {
                                Ext.getCmp('iphoto_idtrader').setSrc(fotoUser);
                            } else {
                                Ext.getCmp('iphoto_idtrader').setSrc(m_api_base_url + '/images/nursery/no-image.png');
                            }
                        });
                    }

                    //photo responsible=====================================
                    if(d.ResponsiblePhoto != ""){
                        var fotoUserResponsible = m_api_base_url + '/images/photo_responsible/' + d.ResponsiblePhoto;
                        Ext.getCmp('Photo_old_responsible_idtrader').setValue(d.ResponsiblePhoto);
                        checkImageExists(fotoUserResponsible, function(existsImage) {
                            if (existsImage == true) {
                                Ext.getCmp('iphotoResponsible_idtrader').setSrc(fotoUserResponsible);
                            } else {
                                Ext.getCmp('iphotoResponsible_idtrader').setSrc(m_api_base_url + '/images/Photo/no-user.jpg');
                            }
                        });
                    }

                    if(d.ResponsibleGender == "m"){
                        Ext.getCmp('ResponsibleGenderM_idtrader').setValue(true);
                    }
                    if(d.ResponsibleGender == "f"){
                        Ext.getCmp('ResponsibleGenderF_idtrader').setValue(true);
                    }

                    Ext.getCmp('ResponsibleType_idtrader').setValue(d.ResponsibleType);
                    Ext.getCmp('Responsible_idtrader').setValue(d.Responsible);
                    Ext.getCmp('ResponsibleType_idtrader').setValue(d.ResponsibleType);
                    Ext.getCmp('ResponsibleName_idtrader').setValue(d.ResponsibleName);
                    Ext.getCmp('ResponsiblePhone_idtrader').setValue(d.ResponsiblePhone);
                    Ext.getCmp('ResponsibleBirthday_idtrader').setValue(d.ResponsibleBirthday);
                }
            },
            failure: function(form, action) {
                Ext.MessageBox.show({
                    title: 'Failed',
                    msg: 'Failed to get data',
                    buttons: Ext.MessageBox.OK,
                    animateTarget: 'mb9',
                    icon: 'ext-mb-error'
                });
            }
        });
    }

    function fset(r) {
        Ext.getCmp('panel_staff').enable()
        store_staff.load({
            params: {
                id: Ext.getCmp('TraderID').getValue()
            }
        });
        //        Ext.getCmp('panel_kualitas_standard').enable()
        //        store_quality_standard.load({
        //            params: {
        //                id: Ext.getCmp('TraderID').getValue()
        //            }});
        //        store_standard.load({
        //            params: {
        //                id: Ext.getCmp('TraderID').getValue()
        //            }});
        //        Ext.getCmp('panel_kualitas').enable()
        //        store_quality.load({
        //            params: {
        //                id: Ext.getCmp('TraderID').getValue()
        //            }});
        //        Ext.getCmp('panel_harga').enable()
        //        store_price.load({
        //            params: {
        //                id: Ext.getCmp('TraderID').getValue()
        //            }});
        //        Ext.getCmp('panel_kemasan').enable()
        //        store_package.load({
        //            params: {
        //                id: Ext.getCmp('TraderID').getValue()
        //            }});
        if (r.Education != null && r.Education != '0') {
            Ext.getCmp('pendidikan' + r.Education).setValue(true);
        }
        Ext.getCmp('TraderName').setValue(r.TraderName);
        Ext.getCmp('IdentityNum').setValue(r.IdentityNum);
        //        Ext.getCmp('education').setValue(r.education);
        // Ext.getCmp('FamilyMembers').setValue(r.FamilyMembers);
        Ext.getCmp('Address').setValue(r.Address);
        Ext.getCmp('Handphone').setValue(r.Handphone);
        Ext.getCmp('NoTelp').setValue(r.NoTelp);
        if (r.TraderID != '') {
            Ext.getCmp('Provinsi').setValue(r.Provinsi);
            Ext.getCmp('Kabupaten').setValue(r.Kabupaten);
            Ext.getCmp('Kecamatan').setValue(r.Kecamatan);
            Ext.getCmp('Desa').setValue(r.VillageID);
        }
        Ext.getCmp('FamilyMembersMale').setValue(r.FamilyMembersMale);
        Ext.getCmp('FamilyMembersFemale').setValue(r.FamilyMembersFemale);
        Ext.getCmp('Email').setValue(r.Email);
        Ext.getCmp('Birthdate').setValue(r.Birthdate);
        Ext.getCmp('Name').setValue(r.Company);
        if (r.CompanyStatus == 'UD') Ext.getCmp('Status').setValue(true);
        if (r.CompanyStatus == 'Firma') Ext.getCmp('Status2').setValue(true);
        if (r.CompanyStatus == 'CV') Ext.getCmp('Status3').setValue(true);
        if (r.CompanyStatus == 'Koperasi') Ext.getCmp('Status4').setValue(true);
        if (r.CompanyStatus == 'PT') Ext.getCmp('Status5').setValue(true);
        if (r.CompanyStatus == 'Tidak Berbadan Hukum') Ext.getCmp('Status6').setValue(true);
        Ext.getCmp('Year').setValue(r.CompanyYear);
        Ext.getCmp('Alias').setValue(r.CompanyAlias);
        Ext.getCmp('PermanentEmployeeMale').setValue(r.PermanentEmployeeMale);
        Ext.getCmp('PermanentEmployeeFemale').setValue(r.PermanentEmployeeFemale);
        Ext.getCmp('TemporaryEmployeeMale').setValue(r.TemporaryEmployeeMale);
        Ext.getCmp('TemporaryEmployeeFemale').setValue(r.TemporaryEmployeeFemale);
        Ext.getCmp('LatDeg').setValue(r.LatDeg);
        //Ext.getCmp('LatMin').setValue(r.LatMin);
        //Ext.getCmp('LatSec').setValue(r.LatSec);
        Ext.getCmp('LatSec').setValue(r.Latitude);
        Ext.getCmp('LongDeg').setValue(r.LongDeg);
        //Ext.getCmp('LongMin').setValue(r.LongMin);
        //Ext.getCmp('LongSec').setValue(r.LongSec);
        Ext.getCmp('LongSec').setValue(r.Longitude);
        // Ext.getCmp('Elevation').setValue(r.Elevation);
        Ext.getCmp('Photo_old').setValue(r.Photo);
        Ext.getCmp('iphoto').setSrc(m_photo + '/' + r.Photo);
        Ext.getCmp('Sex' + r.Sex).setValue(r.Sex);
    }
    var DataBeforeCetak = Ext.create('Ext.form.Panel', {
        autoScroll: true,
        width: 420,
        height: 100,
        id: 'dataBeforeCetak',
        xtype: 'form',
        bodyPadding: 5,
        layout: {
            align: 'stretch',
            type: 'vbox'
        },
        header: false,
        title: lang('My Form'),
        items: [{
            xtype: 'combobox',
            id: 'partner',
            name: 'partner',
            store: store_partner,
            fieldLabel: lang('Partner'),
            displayField: 'label',
            valueField: 'id',
            queryMode: 'local'
        }, {
            xtype: 'radiogroup',
            id: 'tipeFormTrader',
            fieldLabel: lang('Type'),
            items: [{
                xtype: 'radiofield',
                //                id: 'tipeFormTrader',
                name: 'tipe',
                boxLabel: 'Form Kosong',
                inputValue: 1,
                listeners: {
                    change: function(cb, nv, ov) {
                        //                        if (Ext.getCmp('basic').getValue()) store_CekSurvey.load();
                    }
                }
            }, {
                xtype: 'radiofield',
                //                id: 'tipeFormTrader',
                name: 'tipe',
                boxLabel: 'Hasil',
                inputValue: 0,
                listeners: {
                    change: function(cb, nv, ov) {
                        //                        if (Ext.getCmp('jenis').getValue() == null) {
                        //                            Ext.MessageBox.alert('Warning', 'Silahkan pilih jenis yang akan dicetak');
                        //                            Ext.getCmp('result').setValue(false);
                        //                            return;
                        //                        }
                        //                        if (Ext.getCmp('result').getValue()) store_CekSurvey.load({
                        //                            params: {
                        //                                jenis: Ext.getCmp('jenis').getValue(),
                        //                                FarmerID: FarmerID
                        //                            }
                        //                        });
                    }
                }
            }]
        }],
        buttons: [{
            xtype: 'button',
            text: lang('Close'),
            margin: '5px',
            scale: 'large',
            ui: 's-button',
            disabled: false,
            handler: function() {
                winBeforeCetak.hide();
            }
        }, {
            xtype: 'button',
            text: lang('Print'),
            margin: '5px',
            scale: 'large',
            ui: 's-button',
            cls: 's-blue',
            disabled: false,
            handler: function() {
                var tipe = Ext.getCmp('tipeFormTrader').getValue().tipe;
                if (tipe == 1) {
                    //form kosong
                    winBeforeCetak.hide();
                    preview_cetak_surat(m_cetak + 'kosong' + '/' + Ext.getCmp('partner').getValue());
                } else {
                    //hasil
                    var grid = Ext.getCmp('grid');
                    var selectedRecord = grid.getSelectionModel().getSelection()[0];
                    var data = grid.getSelectionModel().getSelection();
                    if (data.length == 0) {
                        Ext.Msg.alert("Cetak Trader", "Pilih Trader Terlebih Dahulu");
                    } else {
                        //                                console.log(selectedRecord.data.id);
                        //                                displayBeforeCetak(selectedRecord.data.id);
                        winBeforeCetak.hide();
                        preview_cetak_surat(m_cetak + selectedRecord.data.id + '/' + Ext.getCmp('partner').getValue());
                        //Ext.getCmp('kodejenjangmaster').setReadOnly(false);
                        //                                var formSiswaGrid = Ext.getCmp('formSiswaGrid');
                        //                                formSiswaGrid.getForm().load({
                        //                                    url: SITE_URL + 'backend/loadFormData/SiswaGrid/1',
                        //                                    params: {
                        //                                        extraparams: 'a.idtax:' + selectedRecord.data.idtax
                        //                                    },
                        //                                    success: function(form, action) {
                        //                                        // Ext.Msg.alert("Load failed", action.result.errorMessage);
                        //                                    },
                        //                                    failure: function(form, action) {
                        //                                        Ext.Msg.alert("Load failed", action.result.errorMessage);
                        //                                    }
                        //                                })
                        //                                wSiswaGrid.show();
                        //                                Ext.getCmp('statusformSiswaGrid').setValue('edit');
                    }
                }
            }
        }]
    });

    function displayBeforeCetak() {
        if (!winBeforeCetak.isVisible()) {
            winBeforeCetak.center();
            winBeforeCetak.show();
        } else {
            winBeforeCetak.hide(this, function() {});
            winBeforeCetak.toFront();
        }
        Ext.getCmp('partner').setValue()
    }
    var winBeforeCetak = Ext.create('widget.window', {
        id: 'print',
        title: lang('Cetak'),
        closable: false,
        modal: true,
        layout: {
            type: 'fit'
        },
        width: 430,
        height: 170,
        items: [DataBeforeCetak]
    });

});

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
/*
 * @Author: nikolius
 * @Date:   2016-08-26 10:19:35
 * @Last Modified by:   nikolius
 * @Last Modified time: 2016-12-28 16:51:35
 */
Ext.onReady(function() {
    Ext.tip.QuickTipManager.init();

    //cek apakah ada sce terselect
    if(m_SceID == ""){
        window.location = m_base_url+'prog_sce/profile';
        /*
        Ext.MessageBox.show({
            title: 'Notifications',
            msg: 'Failed to get data. No Professional Farmer selected',
            buttons: Ext.MessageBox.OK,
            animateTarget: 'mb9',
            icon: 'ext-mb-info'
        });
        */
    }

    var cRowEditing = Ext.create('Ext.grid.plugin.RowEditing', {
        id: 'cRowEditing',
        clicksToMoveEditor: 0,
        autoCancel: false,
        errorSummary: false,
        clicksToEdit: 2
    });

    var mRowEditing = Ext.create('Ext.grid.plugin.RowEditing', {
        id: 'cRowEditing',
        clicksToMoveEditor: 0,
        autoCancel: false,
        errorSummary: false,
        clicksToEdit: 2
    });

    var mc_combo_nurserynr = Ext.create('Ext.data.Store', {
        extend: 'Ext.data.Model',
        fields: ['id', 'label'],
        autoLoad: true,
        proxy: {
            type: 'ajax',
            url: m_api + '/prog_sce/nurserynr_combo',
            reader: {
                type: 'json',
                root: 'data'
            }
        }
    });

    Ext.define('penjualan.Model', {
        extend: 'Ext.data.Model',
        fields: ['id', 'Buyer', 'Volume', 'Price', 'Total', 'DateTransaction', 'CloneTypeID', 'CloneTypeName'],
    });
    var store_nursery_penjualan = Ext.create('Ext.data.Store', {
        model: 'penjualan.Model',
        autoLoad: false,
        pageSize: 10,
        proxy: {
            type: 'ajax',
            url: m_api + '/prog_sce/nursery_penjualan',
            reader: {
                type: 'json',
                root: 'data',
                totalProperty: 'total'
            }
        }
    });
    store_nursery_penjualan.on('beforeload', function() {
        var proxy = store_nursery_penjualan.getProxy();
        proxy.setExtraParam('NurseryID', Ext.getCmp('NurseryID').getValue());
    });


    Ext.define('monitoring.Model', {
        extend: 'Ext.data.Model',
        fields: ['id', 'MonitoringDate', 'MonitoringStatus', 'Description'],
    });
    var store_nursery_monitoring = Ext.create('Ext.data.Store', {
        model: 'monitoring.Model',
        autoLoad: false,
        pageSize: 10,
        proxy: {
            type: 'ajax',
            url: m_api + '/prog_sce/nursery_monitoring',
            reader: {
                type: 'json',
                root: 'data',
                totalProperty: 'total'
            }
        }
    });
    store_nursery_monitoring.on('beforeload', function() {
        var proxy = store_nursery_monitoring.getProxy();
        proxy.setExtraParam('NurseryID', Ext.getCmp('NurseryID').getValue());
    });

    var mc_pembeli = Ext.create('Ext.data.Store', {
        fields: ['label'],
        data: [{
            'label': 'Anggota Kelompok'
        }, {
            'label': 'Petani Lain'
        }, {
            'label': 'Traders'
        }, {
            'label': 'Dll'
        }, {
            'label': 'Pemerintah'
        }]
    });

    var mc_clone_type_combo = Ext.create('Ext.data.Store', {
        extend: 'Ext.data.Model',
        fields: ['id', 'label'],
        autoLoad: true,
        proxy: {
            type: 'ajax',
            url: m_api + '/cpg/clone_ref_combo',
            reader: {
                type: 'json',
                root: 'data'
            }
        }
    });

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

    function act_nursery_status(val) {
        if (val != 'Tidak Berjalan') {
            //Ext.getCmp('mDescription').allowBlank = true;
            Ext.getCmp('mDescription').getStore().loadData(['']);
        } else {
            //Ext.getCmp('mDescription').allowBlank = false;
            Ext.getCmp('mDescription').getStore().loadData([
                [lang('Masalah air/Penyakit')],
                [lang('Rusak')],
                [lang('Tidak ada pemeliharaan/Konflik anggota kelompok')],
                [lang('Tidak ada pasar penjualan')]
            ]);
        }
    }

    function setUpdate(NurseryID){
        Ext.getCmp('dataFormMainPanel').getForm().load({
            url: m_api + '/prog_sce/nursery_by_id',
            method: 'GET',
            waitMsg: lang('Please Wait'),
            params: {
                NurseryID: NurseryID
            },
            success: function(response, opts) {
                var actionData = Ext.decode(opts.response.responseText);
                var obj = actionData.data;
                //console.log(obj);

                Ext.getCmp('NurseryID').setValue(obj.NurseryID);
                Ext.getCmp('NurseryNrSend').setValue(obj.NurseryNrSend);
                Ext.getCmp('nEstablished').setValue(obj.Established);
                Ext.getCmp('Panjang').setValue(obj.Panjang);
                Ext.getCmp('Lebar').setValue(obj.Lebar);
                Ext.getCmp('Luas').setValue(obj.Panjang * obj.Lebar);
                //Ext.getCmp('NursCertBp2YaTidak').setValue(obj.CertificationStatus);
                if(obj.CertificationStatus == "Yes"){
                    Ext.getCmp('NursCertBp2Ya').setValue(true);

                    Ext.getCmp('tglCertificate').setDisabled(false);
                    Ext.getCmp('tglCertificate').setValue(obj.DateCertification);
                    Ext.getCmp('DateAppliedCertification').setValue(obj.DateAppliedCertification);
                }else{
                    Ext.getCmp('NursCertBp2Tidak').setValue(true);

                    Ext.getCmp('tglCertificate').setDisabled(true);
                }
                Ext.getCmp('Kapasitas').setValue(nnumber_format(Ext.getCmp('Luas').getValue() * 40));

                Ext.getCmp('nLatitude').setValue(obj.Latitude);
                Ext.getCmp('nLongitude').setValue(obj.Longitude);

                //photo===========================================
                if(obj.Photo != ""){
                    var fotoUser = m_api_base_url + '/images/nursery/' + obj.Photo;
                    Ext.getCmp('Photo_old').setValue(obj.Photo);
                    checkImageExists(fotoUser, function(existsImage) {
                        if (existsImage == true) {
                            Ext.getCmp('iphoto').setSrc(fotoUser);
                        } else {
                            Ext.getCmp('iphoto').setSrc(m_api_base_url + '/images/nursery/no-image.png');
                        }
                    });
                }

                //photo responsible=====================================
                if(obj.ResponsiblePhoto != ""){
                    var fotoUserResponsible = m_api_base_url + '/images/photo_responsible/' + obj.ResponsiblePhoto;
                    Ext.getCmp('Photo_old_responsible').setValue(obj.ResponsiblePhoto);
                    checkImageExists(fotoUserResponsible, function(existsImage) {
                        if (existsImage == true) {
                            Ext.getCmp('iphotoResponsible').setSrc(fotoUserResponsible);
                        } else {
                            Ext.getCmp('iphotoResponsible').setSrc(m_api_base_url + '/images/Photo/no-user.jpg');
                        }
                    });
                }

                if(obj.ResponsibleGender == "m"){
                    Ext.getCmp('ResponsibleGenderM').setValue(true);
                }
                if(obj.ResponsibleGender == "f"){
                    Ext.getCmp('ResponsibleGenderF').setValue(true);
                }

                //load store transaksi
                store_nursery_penjualan.load();
                store_nursery_monitoring.load();
            },
            failure: function(response, opts) {
                var obj = Ext.decode(response.responseText);
                Ext.MessageBox.alert('Warning', 'Data not found');
            }
        });
    }

    function cekDetailTrans(){
        if(Ext.getCmp('NurseryID').getValue() != ""){
            return true;
        }else{
            Ext.MessageBox.show({
                title: 'Notifications',
                msg: 'No Nursery Nr selected',
                buttons: Ext.MessageBox.OK,
                animateTarget: 'mb9',
                icon: 'ext-mb-info'
            });
            return false;
        }
    }

    function display_area(NurseryID,NurseryNr){
        var areawindow = Ext.create('widget.window', {
            id : 'areawindow',
            title: lang('Nursery Polygon'),
            closable: true,
            modal:true,
            layout : 'fit',
            closeAction: 'destroy',
            width: '75%',
            height: 550,
            bodyPadding: 5,
            listeners: {
                close: function(cb, nv, ov) {
                    hitung_area();
                }
            }
        });
        areawindow.show();

        Ext.Ajax.request({
            url: m_api + '/prog_sce/nursery_polygon',
            method: 'GET',
            params: {
                NurseryID: NurseryID,
                NurseryNr: NurseryNr,
                lati: Ext.getCmp('nLatitude').getValue(),
                longi: Ext.getCmp('nLongitude').getValue(),
                hakAksesPolygon: m_hakakses_polygon
            },
            success: function(response){
                var htmlText = response.responseText;
                areawindow.update(htmlText, true);
            }
        });
    }

    function hitung_area(){
        Ext.Ajax.request({
            url: m_api + '/prog_sce/update_nursery_area',
            method: 'GET',
            params: {
                NurseryID: Ext.getCmp('NurseryID').getValue(),
            },
            success: function(fp, o) {
                var r = Ext.decode(fp.responseText);
                Ext.getCmp('nLatitude').setValue(r.Latitude);
                Ext.getCmp('nLongitude').setValue(r.Longitude);
            }
       })
    }

    var cmb_respon_type = Ext.create('Ext.data.Store', {
        fields: ['id', 'label'],
        data: [
        {
            "id": "farmer",
            "label": lang("Farmer")
        }, {
            "id": "staff",
            "label": "Staff"
        }, {
            "id": "other",
            "label": lang("Other")
        },
        ]
    });

    var cmb_respon_id = Ext.create('Ext.data.Store', {
        extend: 'Ext.data.Model',
        fields: ['id', 'label'],
        autoLoad: false,
        proxy: {
            type: 'ajax',
            url: m_api + '/prog_sce/nursery_respon_by_type',
            reader: {
                type: 'json',
                root: 'data'
            }
        },
        listeners: {
            'beforeload': function(store, options) {
                store.proxy.extraParams.responsibleType = Ext.getCmp('ResponsibleType').getValue();
                store.proxy.extraParams.SceID = m_SceID;
            }
        }
    });

    var DataPanel = Ext.create('Ext.panel.Panel', {
        title: 'Nursery',
        padding: 0,
        margin: 15,
        height: 1000,
        autoScroll: true,
        frame: true,
        bodyStyle: {
            "background-color": "#F0F0F0"
        },
        style: 'background-color:#F0F0F0;',
        bodyPadding: 5,
        id: 'mainPanel',
        items:[{
            xtype: 'form',
            id: 'dataFormMainPanel',
            fileUpload: true,
            items: [{
                layout: 'column',
                border: false,
                items: [{
                    columnWidth: .5,
                    layout: 'form',
                    border: false,
                    padding: 5,
                    defaults: {
                        labelWidth: 200
                    },
                    items: [{
                        xtype: 'textfield',
                        id: 'NurseryID',
                        name: 'NurseryID',
                        hidden: true
                    },{
                        xtype: 'textfield',
                        id: 'NurseryNrSend',
                        name: 'NurseryNrSend',
                        hidden: true
                    },{
                        fieldLabel: lang('Nursery Nr'),
                        id: 'NurseryNr',
                        name: 'NurseryNr',
                        xtype: 'combo',
                        store: mc_combo_nurserynr,
                        displayField: 'label',
                        valueField: 'id',
                        queryMode: 'local',
                        allowBlank: false,
                        listeners: {
                            change: function(cb, nv, ov) {

                                //reset all form but NurseryNr ============================================
                                var fields = this.up('form').query('[isFormField][name!="NurseryNr"]');
                                for (var i = 0, len = fields.length; i < len; i++) {
                                    fields[i].reset();
                                }
                                Ext.getCmp('iphoto').setSrc(m_api_base_url + '/images/nursery/no-image.png');
                                Ext.getCmp('iphotoResponsible').setSrc(m_api_base_url + '/images/Photo/no-user.jpg');
                                //reset all form but NurseryNr ============================================

                                if (this.value != '-1') {
                                    //console.log(nv);
                                    //console.log(ov);
                                    //console.log(this.value);
                                    setUpdate(this.value);
                                }else{
                                    //proses insert
                                    //load store transaksi
                                    store_nursery_penjualan.load();
                                    store_nursery_monitoring.load();
                                }
                            }
                        }
                    }, {
                        xtype: 'combo',
                        store: cmb_respon_type,
                        fieldLabel: lang('Responsible Type'),
                        id: 'ResponsibleType',
                        name: 'ResponsibleType',
                        displayField: 'label',
                        valueField: 'id',
                        queryMode: 'local',
                        allowBlank: false,
                        listeners: {
                            change: function(cb, nv, ov) {
                                if(nv != 'other'){
                                    Ext.getCmp('Responsible').setDisabled(false);
                                    Ext.getCmp('ResponsibleName').setVisible(false);
                                    Ext.getCmp('ResponsibleBirthday').setVisible(false);
                                    Ext.getCmp('ResponsiblePhone').setVisible(false);
                                    Ext.getCmp('ResponsibleGender').setVisible(false);
                                    Ext.getCmp('divPhotoResponsible').setVisible(false);
                                    Ext.getCmp('PhotoResponsible').setVisible(false);
                                    cmb_respon_id.load();
                                }else{
                                    Ext.getCmp('Responsible').setDisabled(true);
                                    Ext.getCmp('ResponsibleName').setVisible(true);
                                    Ext.getCmp('ResponsibleBirthday').setVisible(true);
                                    Ext.getCmp('ResponsiblePhone').setVisible(true);
                                    Ext.getCmp('ResponsibleGender').setVisible(true);
                                    Ext.getCmp('divPhotoResponsible').setVisible(true);
                                    Ext.getCmp('PhotoResponsible').setVisible(true);
                                }
                            }
                        }
                    },{
                        xtype: 'combo',
                        store: cmb_respon_id,
                        labelWidth: '175',
                        fieldLabel: lang('Responsible'),
                        id: 'Responsible',
                        name: 'Responsible',
                        displayField: 'label',
                        valueField: 'id',
                        queryMode: 'local'
                    },{
                        xtype: 'textfield',
                        fieldLabel: lang('Responsible Name'),
                        id: 'ResponsibleName',
                        name: 'ResponsibleName',
                        hidden:true
                    },{
                        xtype: 'datefield',
                        fieldLabel: lang('Responsible Birthdate'),
                        labelWidth: '175',
                        id: 'ResponsibleBirthday',
                        name: 'ResponsibleBirthday',
                        format: 'Y-m-d',
                        hidden:true
                    },{
                        xtype: 'textfield',
                        fieldLabel: lang('Responsible Phone'),
                        id: 'ResponsiblePhone',
                        name: 'ResponsiblePhone',
                        hidden:true
                    },{
                        xtype: 'radiogroup',
                        fieldLabel: lang('Responsible Gender'),
                        id:'ResponsibleGender',
                        labelWidth: '175',
                        hidden:true,
                        items: [{
                            name: 'ResponsibleGender',
                            id: 'ResponsibleGenderM',
                            boxLabel: lang('Male'),
                            inputValue: 'm'
                        }, {
                            name: 'ResponsibleGender',
                            id: 'ResponsibleGenderF',
                            boxLabel: lang('Female'),
                            inputValue: 'f'
                        }]
                    },{
                        layout:'column',
                        border:false,
                        style:'margin-bottom:5px;margin-right:-5px;',
                        id:'divPhotoResponsible',
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
                                id: 'iphotoResponsible',
                                width: '150px',
                                height:'150px',
                                src: m_api_base_url + '/images/Photo/no-user.jpg'
                            },{
                                xtype: 'textfield',
                                id: 'Photo_old_responsible',
                                name: 'Photo_old_responsible',
                                inputType: 'hidden'
                            }]
                        }]
                    },{
                        xtype: 'fileuploadfield',
                        fieldLabel: lang('Photo'),
                        labelWidth: 130,
                        id: 'PhotoResponsible',
                        name: 'PhotoResponsible',
                        buttonText: 'Browse',
                        hidden:true,
                        listeners: {
                            'change': function (fb, v) {
                                var form = Ext.getCmp('dataFormMainPanel').getForm();
                                form.submit({
                                    url: m_api + '/prog_sce/nursery_form_photo_responsible',
                                    clientValidation: false,
                                    waitMsg: 'Sending Photo...',
                                    success: function (fp, o) {
                                        Ext.getCmp('iphotoResponsible').setSrc(m_api_base_url + '/images/photo_responsible/' + o.result.file);
                                        Ext.getCmp('Photo_old_responsible').setValue(o.result.file);
                                    }
                                });
                            }
                        }
                    },{
                        xtype: 'datefield',
                        fieldLabel: lang('Tanggal Berdiri'),
                        id: 'nEstablished',
                        name: 'nEstablished',
                        format: 'Y-m-d',
                        allowBlank: false
                    }, {
                        xtype: 'radiogroup',
                        fieldLabel: lang('Nursery Ceritification - BP2MB'),
                        items: [{
                            name: 'NursCertBp2YaTidak',
                            id: 'NursCertBp2Ya',
                            boxLabel: lang('Ya'),
                            inputValue: 'Yes'
                        }, {
                            name: 'NursCertBp2YaTidak',
                            id: 'NursCertBp2Tidak',
                            boxLabel: lang('Tidak'),
                            inputValue: 'No'
                        }],
                        listeners: {
                            change: function() {
                                if (Ext.getCmp('NursCertBp2Ya').getValue() == true) {
                                    Ext.getCmp('tglCertificate').setDisabled(false);
                                    Ext.getCmp('DateAppliedCertification').setDisabled(false);
                                } else {
                                    Ext.getCmp('tglCertificate').setDisabled(true);
                                    Ext.getCmp('tglCertificate').setValue('');
                                    Ext.getCmp('DateAppliedCertification').setDisabled(true);
                                    Ext.getCmp('DateAppliedCertification').setValue('');
                                }
                            }
                        }
                    }, {
                        xtype: 'datefield',
                        fieldLabel: lang('Date of Certificate Issue'),
                        id: 'tglCertificate',
                        name: 'tglCertificate',
                        format: 'Y-m-d',
                        disabled: true
                    }, {
                        xtype: 'datefield',
                        fieldLabel: lang('Date Applied for Certification'),
                        id: 'DateAppliedCertification',
                        name: 'DateAppliedCertification',
                        format: 'Y-m-d',
                        disabled: true
                    },{
                        items: [{
                            xtype: 'button',
                            margin: '0',
                            width:'150px',
                            id: 'buttonPrintNurseryProfile',
                            text: lang('Print Nursery Profile'),
                            handler: function() {
                                var cek = cekDetailTrans();
                                if(cek == true){
                                    var urlPrint = m_api + '/nursery/cetak_nursery_summary/farmer/'+m_FarmerID+'/'+Ext.getCmp('NurseryNrSend').getValue()+'/';
                                    preview_cetak_surat(urlPrint);
                                }
                            }
                        }]
                    }]
                }, {
                    columnWidth: .5,
                    layout: 'form',
                    border: false,
                    padding: 5,
                    items: [{
                        layout: 'column',
                        border: false,
                        id:'divSceLebarPanjangNursery',
                        items:[{
                            columnWidth: .5,
                            layout: 'form',
                            border: false,
                            defaults: {
                                labelWidth: 160
                            },
                            items:[{
                                xtype: 'numericfield',
                                fieldLabel: lang('Panjang (m)'),
                                id: 'Panjang',
                                name: 'Panjang',
                                fieldCls: 'classuang',
                                allowBlank: false,
                                listeners: {
                                    change: function(cb, nv, ov) {
                                        Ext.getCmp('Luas').setValue(Ext.getCmp('Panjang').getValue() * Ext.getCmp('Lebar').getValue());
                                    }
                                }
                            }]
                        },{
                            columnWidth: .5,
                            layout: 'form',
                            border: false,
                            defaults: {
                                labelWidth: 200
                            },
                            style: 'margin-left:25px;',
                            items:[{
                                xtype: 'numericfield',
                                fieldLabel: lang('Lebar (m)'),
                                id: 'Lebar',
                                name: 'Lebar',
                                allowBlank: false,
                                listeners: {
                                    change: function(cb, nv, ov) {
                                        Ext.getCmp('Luas').setValue(Ext.getCmp('Panjang').getValue() * Ext.getCmp('Lebar').getValue());
                                    }
                                }
                            }]
                        }]
                    },{
                        xtype: 'numericfield',
                        fieldLabel: lang('Luas (m2)'),
                        id: 'Luas',
                        name: 'Luas',
                        readOnly: true,
                        listeners: {
                            change: function(cb, nv, ov) {
                                Ext.getCmp('Kapasitas').setValue(nnumber_format(Ext.getCmp('Luas').getValue() * 40));
                            }
                        }
                    }, {
                        xtype: 'numericfield',
                        fieldLabel: lang('Kapasitas (Luas (m2) x 40)'),
                        id: 'Kapasitas',
                        name: 'Kapasitas',
                        labelWidth: 160,
                        readOnly: true
                    }, {
                        xtype: 'textfield',
                        fieldLabel: lang('Latitude (Dec)'),
                        id: 'nLatitude',
                        name: 'Latitude',
                        readOnly: m_hakakses_lat_long
                        //allowBlank: false
                    }, {
                        xtype: 'textfield',
                        fieldLabel: lang('Longitude (Dec)'),
                        id: 'nLongitude',
                        name: 'Longitude',
                        readOnly: m_hakakses_long_long
                        //allowBlank: false
                    },{
                        items: [{
                            layout: 'column',
                            labelWidth: 500,
                            items: [{
                                html: lang('Map Area'),
                            }, {
                                items: [{
                                    xtype: 'button',
                                    margin: '0 0 0 109',
                                    width:'100px',
                                    id: 'buttonShowPolygon',
                                    text: lang('Show Polygon'),
                                    handler: function() {
                                        if (Ext.getCmp('NurseryID').getValue() == '') {
                                            Ext.MessageBox.alert('Warning', 'Please save nursery first!');
                                        } else {
                                            display_area(Ext.getCmp('NurseryID').getValue(),Ext.getCmp('NurseryNrSend').getValue());
                                        }
                                    }
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
                                id: 'iphoto',
                                width: '150px',
                                height:'150px',
                                src: m_api_base_url + '/images/nursery/no-image.png'
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
                        listeners: {
                            'change': function (fb, v) {
                                var form = Ext.getCmp('dataFormMainPanel').getForm();
                                form.submit({
                                    url: m_api + '/prog_sce/nursery_photo',
                                    clientValidation: false,
                                    waitMsg: 'Sending Photo...',
                                    success: function (fp, o) {
                                        Ext.getCmp('iphoto').setSrc(m_api_base_url + '/images/nursery/' + o.result.file);
                                        Ext.getCmp('Photo_old').setValue(o.result.file);
                                    }
                                });
                            }
                        }
                    }]
                }]
            },{
                xtype: 'tabpanel',
                flex: 1,
                margin: 2,
                activeTab: 0,
                plain: true,
                cls:'tabSce',
                items: [{
                    xtype: 'gridpanel',
                    title: lang('Nursery Penjualan'),
                    id: 'gnurseypenjualan',
                    style: 'border:1px solid #CCC;',
                    store: store_nursery_penjualan,
                    width: '100%',
                    loadMask: true,
                    selType: 'rowmodel',
                    height:475,
                    dockedItems: [{
                        xtype: 'pagingtoolbar',
                        store: store_nursery_penjualan, // same store GridPanel is using
                        dock: 'bottom',
                        displayInfo: true
                    },{
                        xtype: 'toolbar',
                        items: [{
                            icon: varjs.config.base_url + 'images/icons/new/add.png', cls:'Sfr_BtnGridGreen', overCls:'Sfr_BtnGridGreen-Hover',
                            //cls: m_act_save,
                            hidden: m_act_add,
                            text: lang('Add'),
                            scope: this,
                            handler: function() {
                                var cek = cekDetailTrans();
                                if(cek == true){
                                    cRowEditing.cancelEdit();
                                    var r = Ext.create('penjualan.Model', {
                                        id: '',
                                        Buyer: '',
                                        Volume: '',
                                        Price: '',
                                        Total: '',
                                        DateTransaction: ''
                                    });
                                    store_nursery_penjualan.insert(0, r);
                                    cRowEditing.startEdit(0, 0);
                                }
                            }
                        },{
                            icon: varjs.config.base_url + 'images/icons/new/update.png',
                            //cls: m_act_save,
                            hidden: m_act_update,
                            text: lang('Update'),
                            scope: this,
                            handler: function() {
                                var cek = cekDetailTrans();
                                if(cek == true){
                                    cRowEditing.cancelEdit();
                                    var sm = Ext.getCmp('gnurseypenjualan').getSelectionModel().getSelection();
                                    cRowEditing.startEdit(sm[0].index, 0);
                                }
                            }
                        },{
                            itemId: 'remove',
                            icon: varjs.config.base_url + 'images/icons/new/delete.png',
                            //cls: m_act_save,
                            hidden: m_act_delete,
                            text: lang('Delete'),
                            scope: this,
                            handler: function() {
                                var cek = cekDetailTrans();
                                if(cek == true){
                                    var smb = Ext.getCmp('gnurseypenjualan').getSelectionModel().getSelection()[0];
                                    cRowEditing.cancelEdit();
                                    Ext.MessageBox.confirm('Message', 'Do you want to delete this data ?', function(btn) {
                                        if (btn == 'yes') {
                                            Ext.Ajax.request({
                                                waitMsg: 'Please Wait',
                                                url: m_api + '/prog_sce/nursery_penjualan',
                                                method: 'DELETE',
                                                params: {
                                                    id: smb.raw.id
                                                },
                                                success: function(response, opts) {
                                                    var obj = Ext.decode(response.responseText);
                                                    switch (obj.success) {
                                                        case true:
                                                            Ext.MessageBox.alert('Success', obj.message);
                                                            store_nursery_penjualan.load();
                                                        break;
                                                        default:
                                                            Ext.MessageBox.alert('Warning', obj.message);
                                                        break;
                                                    }
                                                },
                                                failure: function(response, opts) {
                                                    var obj = Ext.decode(response.responseText);
                                                    Ext.MessageBox.alert('Failed', obj.message);
                                                }
                                            });
                                        }
                                    });
                                }
                            }
                        }]
                    }],
                    columns: [{
                        text: lang('ID'),
                        dataIndex: 'id',
                        hidden: true
                    },{
                        text: lang('No'),
                        xtype: 'rownumberer',
                        width: '5%'
                    },{
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
                    },{
                        text: lang('Bibit Dijual'),
                        dataIndex: 'Volume',
                        xtype: 'numbercolumn',
                        format:'0,000',
                        width: '15%',
                        editor: {
                            xtype: 'numericfield',
                            id: 'nvol',
                            allowBlank: false,
                            listeners: {
                                change: function() {
                                    Ext.getCmp('ntot').setValue(Ext.getCmp('nvol').getValue() * Ext.getCmp('npri').getValue());
                                }
                            }
                        }
                    },{
                        text: lang('Clone Type'),
                        dataIndex: 'CloneTypeName',
                        width: '15%',
                        editor: {
                            xtype: 'combo',
                            store: mc_clone_type_combo,
                            name: 'CloneTypeID',
                            displayField: 'label',
                            valueField: 'id',
                            queryMode: 'local',
                            allowBlank: false
                        }
                    },{
                        text: lang('Harga Satuan'),
                        dataIndex: 'Price',
                        xtype: 'numbercolumn',
                        format:'0,000',
                        width: '15%',
                        editor: {
                            xtype: 'numericfield',
                            id: 'npri',
                            allowBlank: false,
                            listeners: {
                                change: function() {
                                    Ext.getCmp('ntot').setValue(Ext.getCmp('nvol').getValue() * Ext.getCmp('npri').getValue());
                                }
                            }
                        }
                    },{
                        text: lang('Total'),
                        dataIndex: 'Total',
                        width: '15%',
                        xtype: 'numbercolumn',
                        format:'0,000',
                        editor: {
                            xtype: 'numericfield',
                            allowBlank: false,
                            id: 'ntot',
                            readOnly: true
                        }
                    },{
                        text: lang('Tanggal Penjualan'),
                        dataIndex: 'DateTransaction',
                        format: 'Y-m-d',
                        width: '13%',
                        editor: {
                            xtype: 'datefield',
                            format: 'Y-m-d',
                            allowBlank: false
                        }
                    }],
                    plugins: [cRowEditing],
                    listeners: {
                        'canceledit': function(editor, e, eOpts) {
                            store_nursery_penjualan.load();
                        },
                        'edit': function(editor, e) {
                            if (e.record.data.id == '') {
                                //insert
                                Ext.Ajax.request({
                                    waitMsg: 'Please wait...',
                                    url: m_api + '/prog_sce/nursery_penjualan',
                                    method: 'POST',
                                    params: {
                                        NurseryID: Ext.getCmp('NurseryID').getValue(),
                                        Buyer: e.record.data.Buyer,
                                        Volume: e.record.data.Volume,
                                        Price: e.record.data.Price,
                                        Total: e.record.data.Totel,
                                        DateTransaction: e.record.data.DateTransaction,
                                        CloneTypeName: e.record.data.CloneTypeName,
                                        CloneTypeID: e.record.data.CloneTypeID
                                    },
                                    success: function(response, opts) {
                                        var obj = Ext.decode(response.responseText);
                                        switch (obj.success) {
                                            case true:
                                                Ext.MessageBox.alert('Success', obj.message);
                                                store_nursery_penjualan.load();
                                            break;
                                            default:
                                                Ext.MessageBox.alert('Warning', obj.message);
                                            break;
                                        }
                                    },
                                    failure: function(response, opts) {
                                        var obj = Ext.decode(response.responseText);
                                        Ext.MessageBox.alert('Failed', obj.message);
                                    }
                                });

                            }else{
                                //update
                                Ext.Ajax.request({
                                    waitMsg: 'Please wait...',
                                    url: m_api + '/prog_sce/nursery_penjualan',
                                    method: 'PUT',
                                    params: {
                                        id: e.record.data.id,
                                        Buyer: e.record.data.Buyer,
                                        Volume: e.record.data.Volume,
                                        Price: e.record.data.Price,
                                        Total: e.record.data.Totel,
                                        DateTransaction: e.record.data.DateTransaction,
                                        CloneTypeName: e.record.data.CloneTypeName,
                                        CloneTypeID: e.record.data.CloneTypeID
                                    },
                                    success: function(response, opts) {
                                        var obj = Ext.decode(response.responseText);
                                        switch (obj.success) {
                                            case true:
                                                Ext.MessageBox.alert('Success', obj.message);
                                                store_nursery_penjualan.load();
                                            break;
                                            default:
                                                Ext.MessageBox.alert('Warning', obj.message);
                                            break;
                                        }
                                    },
                                    failure: function(response, opts) {
                                        var obj = Ext.decode(response.responseText);
                                        Ext.MessageBox.alert('Failed', obj.message);
                                    }
                                });
                            }
                        }
                    }
                },{
                    xtype: 'gridpanel',
                    title: lang('Nursery Monitoring'),
                    id: 'gnurseymonitoring',
                    style: 'border:1px solid #CCC;',
                    store: store_nursery_monitoring,
                    width: '100%',
                    loadMask: true,
                    selType: 'rowmodel',
                    height:475,
                    dockedItems: [{
                        xtype: 'pagingtoolbar',
                        store: store_nursery_monitoring,
                        dock: 'bottom',
                        displayInfo: true
                    },{
                        xtype: 'toolbar',
                        items: [{
                            icon: varjs.config.base_url + 'images/icons/new/add.png', cls:'Sfr_BtnGridGreen', overCls:'Sfr_BtnGridGreen-Hover',
                            //cls: m_act_save,
                            hidden: m_act_add,
                            text: lang('Add'),
                            scope: this,
                            handler: function() {
                                var cek = cekDetailTrans();
                                if(cek == true){
                                    mRowEditing.cancelEdit();
                                    var r = Ext.create('monitoring.Model', {
                                        id: '',
                                        MonitoringDate:'',
                                        MonitoringStatus:'',
                                        Description:''
                                    });
                                    store_nursery_monitoring.insert(0, r);
                                    mRowEditing.startEdit(0, 0);
                                }
                            }
                        },{
                            icon: varjs.config.base_url + 'images/icons/new/update.png',
                            //cls: m_act_save,
                            hidden: m_act_update,
                            text: lang('Update'),
                            scope: this,
                            handler: function() {
                                var cek = cekDetailTrans();
                                if(cek == true){
                                    mRowEditing.cancelEdit();
                                    var sm = Ext.getCmp('gnurseymonitoring').getSelectionModel().getSelection();
                                    mRowEditing.startEdit(sm[0].index, 0);
                                    act_nursery_status(Ext.getCmp('mStatus').getValue());
                                }
                            }
                        },{
                            itemId: 'remove',
                            icon: varjs.config.base_url + 'images/icons/new/delete.png',
                            //cls: m_act_save,
                            hidden: m_act_delete,
                            text: lang('Delete'),
                            scope: this,
                            handler: function() {
                                var cek = cekDetailTrans();
                                if(cek == true){
                                    var smb = Ext.getCmp('gnurseymonitoring').getSelectionModel().getSelection()[0];
                                    mRowEditing.cancelEdit();
                                    Ext.MessageBox.confirm('Message', 'Do you want to delete this data ?', function(btn) {
                                        if (btn == 'yes') {
                                            Ext.Ajax.request({
                                                waitMsg: 'Please Wait',
                                                url: m_api + '/prog_sce/nursery_monitoring',
                                                method: 'DELETE',
                                                params: {
                                                    id: smb.raw.id
                                                },
                                                success: function(response, opts) {
                                                    var obj = Ext.decode(response.responseText);
                                                    switch (obj.success) {
                                                        case true:
                                                            Ext.MessageBox.alert('Success', obj.message);
                                                            store_nursery_monitoring.load();
                                                        break;
                                                        default:
                                                            Ext.MessageBox.alert('Warning', obj.message);
                                                        break;
                                                    }
                                                },
                                                failure: function(response, opts) {
                                                    var obj = Ext.decode(response.responseText);
                                                    Ext.MessageBox.alert('Failed', obj.message);
                                                }
                                            });
                                        }
                                    });
                                }
                            }
                        }]
                    }],
                    columns:[{
                        text: lang('ID'),
                        dataIndex: 'id',
                        hidden: true
                    },{
                        text: lang('No'),
                        xtype: 'rownumberer',
                        width: '5%'
                    },{
                        text: lang('Tanggal Kedatangan'),
                        dataIndex: 'MonitoringDate',
                        width: '15%',
                        editor: {
                            xtype: 'datefield',
                            id: 'mDate',
                            format: 'Y-m-d',
                            allowBlank: false
                        }
                    },{
                        text: lang('Status'),
                        dataIndex: 'MonitoringStatus',
                        width: '20%',
                        editor: {
                            xtype: 'combo',
                            id: 'mStatus',
                            store: mc_status_monitoring,
                            displayField: 'label',
                            valueField: 'label',
                            queryMode: 'local',
                            allowBlank: false,
                            listeners: {
                                change: function(combo, selection) {
                                    Ext.getCmp('mDescription').setValue('');
                                    act_nursery_status(Ext.getCmp('mStatus').getValue());
                                }
                            }
                        }
                    },{
                        text: lang('Keterangan'),
                        dataIndex: 'Description',
                        width: '59%',
                        editor: {
                            xtype: 'combo',
                            id: 'mDescription',
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
                        'canceledit': function(editor, e, eOpts) {
                            store_nursery_monitoring.load();
                        },
                        'edit': function(editor, e) {
                            if (e.record.data.id == '') {
                                //insert
                                Ext.Ajax.request({
                                    waitMsg: 'Please wait...',
                                    url: m_api + '/prog_sce/nursery_monitoring',
                                    method: 'POST',
                                    params: {
                                        NurseryID: Ext.getCmp('NurseryID').getValue(),
                                        MonitoringDate: e.record.data.MonitoringDate,
                                        MonitoringStatus: e.record.data.MonitoringStatus,
                                        Description: e.record.data.Description
                                    },
                                    success: function(response, opts) {
                                        var obj = Ext.decode(response.responseText);
                                        switch (obj.success) {
                                            case true:
                                                Ext.MessageBox.alert('Success', obj.message);
                                                store_nursery_monitoring.load();
                                            break;
                                            default:
                                                Ext.MessageBox.alert('Warning', obj.message);
                                            break;
                                        }
                                    },
                                    failure: function(response, opts) {
                                        var obj = Ext.decode(response.responseText);
                                        Ext.MessageBox.alert('Failed', obj.message);
                                    }
                                });
                            }else{
                                //update
                                Ext.Ajax.request({
                                    waitMsg: 'Please wait...',
                                    url: m_api + '/prog_sce/nursery_monitoring',
                                    method: 'PUT',
                                    params: {
                                        id: e.record.data.id,
                                        MonitoringDate: e.record.data.MonitoringDate,
                                        MonitoringStatus: e.record.data.MonitoringStatus,
                                        Description: e.record.data.Description
                                    },
                                    success: function(response, opts) {
                                        var obj = Ext.decode(response.responseText);
                                        switch (obj.success) {
                                            case true:
                                                Ext.MessageBox.alert('Success', obj.message);
                                                store_nursery_monitoring.load();
                                            break;
                                            default:
                                                Ext.MessageBox.alert('Warning', obj.message);
                                            break;
                                        }
                                    },
                                    failure: function(response, opts) {
                                        var obj = Ext.decode(response.responseText);
                                        Ext.MessageBox.alert('Failed', obj.message);
                                    }
                                });
                            }
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
            }]
        }],
        buttons: [{
            id: 'csaveButton',
            text: lang('Save'),
            margin: '5px',
            scale: 'large',
            ui: 's-button',
            cls: 's-blue',
            hidden: m_act_add,
            handler: function() {
                var form = Ext.getCmp('dataFormMainPanel').getForm();
                if(form.isValid()){
                    form.submit({
                        url: m_api + '/prog_sce/nursery',
                        method: 'POST',
                        waitMsg: 'Sending data...',
                        success: function(fp, data) {
                            var jsonResp = data.result;
                            if (jsonResp.prosesnya == 'insert') {
                                Ext.getCmp('NurseryID').setValue(jsonResp.id);
                                //load combo NurseryNr
                                mc_combo_nurserynr.load();
                                Ext.getCmp('NurseryNr').setValue(jsonResp.id);
                                //setUpdate(jsonResp.id);
                            }

                            Ext.MessageBox.alert('Success', 'Data saved.');
                        },
                        failure: function(fp, o) {
                            if(o.response.responseText == undefined){
                                var errText = "Failed to save data";
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
                        msg: 'Failed to saved data, Please fill all the require input',
                        buttons: Ext.MessageBox.OK,
                        animateTarget: 'mb9',
                        icon: 'ext-mb-error'
                    });
                }
            }
        },{
            text: lang('Delete'),
            margin: '5px',
            scale: 'large',
            ui: 's-button',
            cls: 's-red',
            hidden: m_act_delete,
            handler: function() {
                //cek apakah ada yg terpilih
                var cmbNurseryNr = Ext.getCmp('NurseryNr').getValue();

                if(cmbNurseryNr == '-1' || cmbNurseryNr == ''){
                    Ext.MessageBox.show({
                        title: 'Failed',
                        msg: 'Cannot delete this data',
                        buttons: Ext.MessageBox.OK,
                        animateTarget: 'mb9',
                        icon: 'ext-mb-error'
                    });
                }else{
                    Ext.MessageBox.confirm('Message', 'Do you want to delete this data ?', function(btn) {
                        if (btn == 'yes') {
                            Ext.Ajax.request({
                                waitMsg: 'Please Wait',
                                url: m_api + '/prog_sce/nursery',
                                method: 'DELETE',
                                params: {
                                    NurseryID: Ext.getCmp('NurseryID').getValue()
                                },
                                success: function(response, opts) {
                                    var obj = Ext.decode(response.responseText);

                                    //reset all form but NurseryNr ============================================
                                    var fields = Ext.getCmp('dataFormMainPanel').query('[isFormField][name!="NurseryNr"]');
                                    for (var i = 0, len = fields.length; i < len; i++) {
                                        fields[i].reset();
                                    }
                                    Ext.getCmp('iphoto').setSrc(m_api_base_url + '/images/nursery/no-image.png');
                                    Ext.getCmp('iphotoResponsible').setSrc(m_api_base_url + '/images/Photo/no-user.jpg');
                                    //reset all form but NurseryNr ============================================

                                    mc_combo_nurserynr.load();
                                    Ext.getCmp('NurseryNr').setValue('-1');

                                    Ext.MessageBox.alert('Notification', obj.message);
                                },
                                failure: function(response, opts) {
                                    var obj = Ext.decode(response.responseText);
                                    Ext.MessageBox.alert('Failed', obj.message);
                                }
                            });
                        }
                    });
                }
            }
        }],
        renderTo: 'ext-content'
    });

    //=================================== STUFF to do After Loading ========================================================
    //set auto select ke item pertama
    Ext.getCmp('NurseryNr').setValue('-1');
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
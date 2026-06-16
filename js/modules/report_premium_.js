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

    // var mc_jenis_bu = Ext.create('Ext.data.Store', {
    //     fields: ['label'],
    //     data: [{
    //         "label": "Farmer"
    //     }, {
    //         "label": "Non Farmer"
    //     }]
    // });

    var mc_jenis_sertifikasi = Ext.create('Ext.data.Store', {
        fields: ['id','label'],
        data: [{
            "id":1,
            "label": lang("Sertifikasi")
        }, {
            "id":0,
            "label": lang("Non Sertifikasi")
        }]
    });

    var mc_certification_period = Ext.create('Ext.data.Store', {
        extend: 'Ext.data.Model',
        fields: ['year', 'start', 'end'],
        autoLoad: false,
        proxy: {
            type: 'ajax',
            url: m_certification_period,
            reader: {
                type: 'json',
                root: 'data'
            }
        }
    });

    var mc_bu = Ext.create('Ext.data.Store', {
        extend: 'Ext.data.Model',
        fields: ['SupplychainID', 'Name'],
        autoLoad: false,
        proxy: {
            type: 'ajax',
            url: m_bu,
            reader: {
                type: 'json',
                root: 'data'
            }
        }
    });
    // Buying unit (pedagang)
    var store_view = Ext.create('Ext.data.Store', {
        extend: 'Ext.data.Model',
        fields: [
            // {name: 'PerwakilanOrgId', type: 'int'},
            // {name: 'Namec', type: 'string'},
            // {name: 'Nameb', type: 'string'},
            // {name: 'bruto', type: 'float'},
            // {name: 'netto', type: 'float'},
            // {name: 'Balance', type: 'float'},
            // {name: 'survey', type: 'int'},
            // {name: 'Rupiah', type: 'float'},
            // {name: 'Usd', type: 'float'},
            // {name: 'TotalIDR', type: 'float'},
            // {name: 'TotalUSD', type: 'float'}
            {name: 'orgid', type: 'int'},
            {name: 'name', type: 'string'},
            {name: 'name_b', type: 'string'},
            {name: 'orgtype', type: 'string'},
            {name: 'survey', type: 'float'},
            {name: 'bruto', type: 'float'},
            {name: 'netto', type: 'float'},
            {name: 'totalusd', type: 'float'},
            {name: 'totalidr', type: 'float'},
            {name: 'balance', type: 'float'},
        ],
        autoload: false,
        proxy: {
            type: 'ajax',
            url: m_viewreport,
            reader: {
                type: 'json',
                root: 'data'
            }
        },
        extraParams: {
            "eid": "0",
            "status": "9"
        },
        groupField: 'name_b'
    });
    // farmer
    var store_viewf = Ext.create('Ext.data.Store', {
        extend: 'Ext.data.Model',
        //pageSize: 10,
        fields: [
            // {name: 'FarmerID', type: 'int'},
            // {name: 'FarmerName', type: 'string'},
            // {name: 'farmer', type: 'string'},
            // {name: 'PerwakilanOrgID', type: 'int'},
            // {name: 'DateTransaction', type: 'string'},
            // {name: 'survey', type: 'int'},
            // {name: 'bruto', type: 'float'},
            // {name: 'netto', type: 'float'},
            // {name: 'Balance', type: 'float'},
            // {name: 'PremiumIDR', type: 'float'},
            // {name: 'PremiumUSD', type: 'float'},
            // {name: 'TotalIDR', type: 'float'},
            // {name: 'TotalUSD', type: 'float'}
            {name: 'datetransaction', type: 'string'},
            {name: 'id', type: 'int'},
            {name: 'farmer', type: 'string'},
            {name: 'nama', type: 'stri'},
            {name: 'survey', type: 'float'},
            {name: 'bruto', type: 'float'},
            {name: 'netto', type: 'float'},
            {name: 'totalusd', type: 'float'},
            {name: 'totalidr', type: 'float'},
            {name: 'balance', type: 'float'},
        ],
        autoload: false,
        proxy: {
            type: 'ajax',
            url: m_viewreportf,
            reader: {
                type: 'json',
                root: 'data',
                totalProperty: 'total'
            }
        },
        groupField: 'farmer',
        listeners: {
            beforeload: function (store_viewf, operation) {
                store_viewf.proxy.extraParams.startd        = Ext.getCmp('start').getValue(),
                store_viewf.proxy.extraParams.end           = Ext.getCmp('end').getValue(),
                // store_viewf.proxy.extraParams.jenis         = Ext.getCmp('JenisBuyingUnit').getValue(),
                store_viewf.proxy.extraParams.jenis         = 'Farmer',
                store_viewf.proxy.extraParams.provinsi      = Ext.getCmp('Provinsi').getValue(),
                store_viewf.proxy.extraParams.warehouse     = Ext.getCmp('Warehouse').getValue(),
                store_viewf.proxy.extraParams.sert          = Ext.getCmp('jenisSertifikasi').getValue()
            }
        }
    });


    // koperasi
    var store_viewc = Ext.create('Ext.data.Store', {
        extend: 'Ext.data.Model',
        fields: [
            // {name: 'Nameb', type: 'string'},
            // {name: 'survey', type: 'float'},
            // {name: 'bruto', type: 'float'},
            // {name: 'netto', type: 'float'},
            // {name: 'Balance', type: 'float'},
            // //{name:'PremiumIDR',type:'float'},
            // //{name:'PremiumUSD',type:'float'},
            // {name: 'TotalIDR', type: 'float'},
            // {name: 'TotalUSD', type: 'float'}
            {name: 'orgid', type: 'int'},
            {name: 'name', type: 'string'},
            {name: 'orgtype', type: 'string'},
            {name: 'survey', type: 'float'},
            {name: 'bruto', type: 'float'},
            {name: 'netto', type: 'float'},
            {name: 'totalusd', type: 'float'},
            {name: 'totalidr', type: 'float'},
            {name: 'balance', type: 'float'},
        ],
        autoload: false,
        proxy: {
            type: 'ajax',
            url: m_viewreportc,
            reader: {
                type: 'json',
                root: 'data'
            }
        }
    });

    setTimeout(function(){
        if (m_user_province) {
            Ext.getCmp('Provinsi').setValue(''+m_user_province).setReadOnly(true);
        };
    }, 1000)

    var tab = Ext.create('Ext.Panel', {
        renderTo: 'ext-content',
        //height : 900,
        frame: false,
        items: [{
            xtype: 'fieldset',
            title: 'Traceability',
            id: 'e',
            height: 1100,
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
                        columnWidth: .40,
                        layout: 'form',
                        padding: 3,
                        hidden: true,
                        border: false,
                        items: [{
                            emptyText: '-- Buying Unit --',
                            id: 'BuyingUnit',
                            name: 'BuyingUnit',
                            xtype: 'combo',
                            labelWidth: 60,
                            store: mc_bu,
                            displayField: 'Name',
                            valueField: 'SupplychainID',
                            queryMode: 'local',
                            //listeners: {
                            //  if ()
                            //}
                        }]
                    }, {
                        columnWidth: .14,
                        layout: 'form',
                        //hidden:true,
                        padding: 3,
                        border: false,
                        items: [{
                            emptyText: '-- Province --',
                            id: 'Provinsi',
                            name: 'Provinsi',
                            xtype: 'combo',
                            width: 100,
                            labelWidth: 60,
                            store: mc_Provinsi,
                            displayField: 'label',
                            valueField: 'id',
                            queryMode: 'local',
                            listeners: {
                                change: function (cb, nv, ov) {
                                    Ext.getCmp('Warehouse').setValue();
                                    mc_Warehouse.load({
                                        params: {
                                            key: Ext.getCmp('Provinsi').getValue()
                                        }
                                    });
                                }
                            }
                        }]
                    }, {
                        columnWidth: .12,
                        layout: 'form',
                        //hidden:true,
                        padding: 3,
                        border: false,
                        items: [{
                            emptyText: '-- Warehouse --',
                            id: 'Warehouse',
                            name: 'Warehouse',
                            xtype: 'combo',
                            width: 100,
                            labelWidth: 60,
                            store: mc_Warehouse,
                            displayField: 'label',
                            valueField: 'id',
                            queryMode: 'local',
                            listeners: {
                                change: function (cb, nv, ov) {
                                    Ext.Ajax.request({
                                        url: m_premium_org,
                                        method: 'GET',
                                        params: {
                                            orgid: nv
                                        },
                                        success: function(response){
                                            var data = $.parseJSON(response.responseText);
                                            var usdKoperasi     = parseFloat(data.PersenBuyinUnit)/100*parseFloat(data.USD);
                                            var rpKoperasi      = usdKoperasi*parseFloat(data.Kurs);
                                            var usdBU           = parseFloat(data.PersenPerwakilan)/100*parseFloat(data.USD);
                                            var rpBU            = usdBU*parseFloat(data.Kurs);
                                            var usdFarmer       = parseFloat(data.PersenPetani)/100*parseFloat(data.USD);
                                            var rpFarmer        = usdFarmer*parseFloat(data.Kurs);
                                            Ext.getCmp('premiumKoperasi').setText('Premium : IDR '+number_format(rpKoperasi,0,'.',',')+' | USD '+usdKoperasi);
                                            Ext.getCmp('premiumBU').setText('Premium : IDR '+number_format(rpBU,0,'.',',')+' | USD '+usdBU);
                                            Ext.getCmp('premiumFarmer').setText('Premium : IDR '+number_format(rpFarmer,0,'.',',')+' | USD '+usdFarmer);
                                        }
                                    });
                                    mc_certification_period.load({
                                        params: {
                                            wh: nv
                                        }
                                    });
                                    setTimeout(function(){
                                        // console.log(mc_certification_period.getCount());
                                        if (mc_certification_period.getCount() > 0) {
                                            Ext.getCmp('layoutJenisSertifikasi').show();
                                            // Ext.getCmp('layoutPeriodeSertifikasi').show();
                                            mc_jenis_sertifikasi.clearData();
                                            mc_jenis_sertifikasi.removeAll();
                                            mc_jenis_sertifikasi.add({id:1, label: lang("Sertifikasi")});
                                            mc_jenis_sertifikasi.add({id:0, label: lang("Non Sertifikasi")});
                                        } else {
                                            Ext.getCmp('layoutJenisSertifikasi').hide();
                                            Ext.getCmp('layoutPeriodeSertifikasi').hide();
                                            Ext.getCmp('jenisSertifikasi').setValue(0);

                                            mc_jenis_sertifikasi.clearData();
                                            mc_jenis_sertifikasi.removeAll();
                                            mc_jenis_sertifikasi.add({id:0, label: lang("Non Sertifikasi")});
                                        }
                                    }, 500);
                                }
                            }
                        }]
                    }, 
                    // {
                    //     columnWidth: .10,
                    //     layout: 'form',
                    //     //hidden:true,
                    //     padding: 3,
                    //     border: false,
                    //     items: [{
                    //         emptyText: '-- Jenis --',
                    //         id: 'JenisBuyingUnit',
                    //         name: 'JenisBuyingUnit',
                    //         xtype: 'combo',
                    //         width: 100,
                    //         labelWidth: 60,
                    //         store: mc_jenis_bu,
                    //         displayField: 'label',
                    //         valueField: 'label',
                    //         queryMode: 'local'
                    //     }]
                    // }, 
                    {
                        columnWidth: .15,
                        layout: 'form',
                        id: 'layoutJenisSertifikasi',
                        hidden:true,
                        padding: 3,
                        border: false,
                        items: [{
                            emptyText: '-- Jenis --',
                            id: 'jenisSertifikasi',
                            name: 'jenisSertifikasi',
                            xtype: 'combo',
                            width: 100,
                            labelWidth: 60,
                            store: mc_jenis_sertifikasi,
                            displayField: 'label',
                            valueField: 'id',
                            queryMode: 'local',
                            listeners: {
                                change: function (cb, nv, ov) {
                                    if (nv == 1) {
                                        Ext.getCmp('layoutPeriodeSertifikasi').show();
                                        Ext.getCmp('start').setReadOnly(true);
                                        Ext.getCmp('end').setReadOnly(true);
                                    } else {
                                        Ext.getCmp('layoutPeriodeSertifikasi').hide();
                                        Ext.getCmp('start').setReadOnly(false);
                                        Ext.getCmp('end').setReadOnly(false);
                                    }
                                }
                            }
                        }]
                    }, 
                    {
                        columnWidth: .10,
                        layout: 'form',
                        hidden:true,
                        id: 'layoutPeriodeSertifikasi',
                        padding: 3,
                        border: false,
                        items: [{
                            emptyText: '-- Periode --',
                            id: 'periodSertifikasi',
                            name: 'periodSertifikasi',
                            xtype: 'combo',
                            width: 100,
                            labelWidth: 60,
                            store: mc_certification_period,
                            displayField: 'year',
                            valueField: 'year',
                            queryMode: 'local',
                            listeners: {
                                change: function (cb, nv, ov) {
                                    var newRecord = cb.findRecordByValue(nv);
                                    if (newRecord) {
                                        Ext.getCmp('start')
                                            .setValue(newRecord.data.start)
                                            .setReadOnly(true)
                                        Ext.getCmp('end')
                                            .setValue(newRecord.data.end)
                                            .setReadOnly(true)
                                    }
                                }
                            }
                        }]
                    }, 
                    {
                        columnWidth: .10,
                        layout: 'form',
                        padding: 3,
                        border: false,
                        items: [{
                            xtype: 'datefield',
                            format: 'Y-m-d',
                            fieldLabel: '',
                            id: 'start',
                            name: 'start',
                            width: 120,
                            emptyText: '-- ' + lang('Awal') + ' --',
                            padding: 5
                        }]
                    }, {
                        columnWidth: .04,
                        layout: 'form',
                        padding: 3,
                        border: false,
                        items: [{
                            xtype: 'label',
                            text: lang('s.d.')
                        }]
                    }, {
                        columnWidth: .10,
                        layout: 'form',
                        padding: 3,
                        border: false,
                        items: [{
                            xtype: 'datefield',
                            format: 'Y-m-d',
                            fieldLabel: '',
                            id: 'end',
                            name: 'end',
                            emptyText: '-- ' + lang('Akhir') + ' --',
                            padding: 5
                        }]
                    }, 
                    // {
                    //     columnWidth: .10,
                    //     layout: 'form',
                    //     padding: 3,
                    //     border: false,
                    //     items: [{
                    //         xtype: 'fieldcontainer',
                    //         defaultType: 'checkboxfield',
                    //         items: [{
                    //             boxLabel: lang('Tersertifikasi'),
                    //             name: 'sertifikasis',
                    //             inputValue: '1',
                    //             id: 'sertifikasis'
                    //         }]
                    //     }]
                    // }, 
                    {
                        columnWidth: .13,
                        layout: 'form',
                        padding: 3,
                        border: false,
                        items: [{
                            xtype: 'fieldcontainer',
                            defaultType: 'checkboxfield',
                            items: [{
                                boxLabel: lang('With Premium'),
                                name: 'traceabilityonly',
                                inputValue: '1',
                                id: 'traceabilityonly',
                                listeners: {
                                    change: function (cb, nv, ov) {
                                        with_premium(nv);
                                    }
                                }
                            }]
                        }]
                    }, {
                        columnWidth: .10,
                        layout: 'form',
                        padding: 3,
                        border: false,
                        items: [{
                            xtype: 'button',
                            id: 'btnViewPremium',
                            name: 'btnViewPremium',
                            text: 'View',
                            padding: 3,
                            handler: function () {
                                var ware = Ext.getCmp('Warehouse').getValue();
                                if (ware == '' || ware == 'undefined' || ware == '%%' || ware == null) {
                                    Ext.Msg.alert('Warning', 'Silahkan pilih salah satu warehouse');
                                    return;
                                }
                                if (!Ext.getCmp('start').getValue() || !Ext.getCmp('end').getValue()) {
                                    Ext.Msg.alert('Warning', 'Silahkan tentukan rentang tanggal');
                                    return;    
                                };

                                store_view.load({
                                    params: {
                                        start: Ext.getCmp('start').getValue(),
                                        end: Ext.getCmp('end').getValue(),
                                        // jenis: Ext.getCmp('JenisBuyingUnit').getValue(),
                                        jenis: 'Farmer',
                                        provinsi: Ext.getCmp('Provinsi').getValue(),
                                        warehouse: Ext.getCmp('Warehouse').getValue(),
                                        sert: Ext.getCmp('jenisSertifikasi').getValue()
                                    }
                                    ,callback: function(records, operation, success) {
                                        var type = [];
                                        $.each(records, function(index, val) {
                                            if (type.indexOf(lang(val.data.orgtype)) == -1) {
                                                type.push(lang(val.data.orgtype));
                                            }
                                        });
                                        Ext.getCmp('grid-bu-premium').setTitle(type.join(', '));
                                    }
                                });
                                store_viewc.load({
                                    params: {
                                        start: Ext.getCmp('start').getValue(),
                                        end: Ext.getCmp('end').getValue(),
                                        // jenis: Ext.getCmp('JenisBuyingUnit').getValue(),
                                        jenis: 'Farmer',
                                        provinsi: Ext.getCmp('Provinsi').getValue(),
                                        warehouse: Ext.getCmp('Warehouse').getValue(),
                                        sert: Ext.getCmp('jenisSertifikasi').getValue()
                                    }
                                    ,callback: function(records, operation, success) {
                                        if (records.length > 0) {
                                            Ext.getCmp('grid_koperasi').show();
                                            Ext.getCmp('grid_koperasi').setTitle(lang(records[0].data.orgtype));
                                        } else {
                                            Ext.getCmp('grid_koperasi').hide();
                                        }
                                    }
                                });
                                store_viewf.load();
                                // Ext.getCmp('premiumId').setValue('xxx');
                            }
                        }]
                    }]
                }]
                /*
                 buttons: [{
                 id: 'Tracebility_koperasi',
                 text: 'Koperasi',
                 margin: '5px',
                 scale: 'large',
                 ui: 's-button',
                 cls: 's-blue',
                 handler: function() {
                 preview_cetak_surat(m_cetak+'_koperasi/'+Ext.getCmp('BuyingUnit').getValue()+'/'+
                 Ext.getCmp('start').getSubmitValue()+'/'+Ext.getCmp('end').getSubmitValue()+'/'+
                 Ext.getCmp('JenisBuyingUnit').getValue());
                 }
                 },{
                 id: 'Tracebility_koperasi_excel',
                 text: 'Excel Koperasi',
                 margin: '5px',
                 scale: 'large',
                 ui: 's-button',
                 cls: 's-blue',
                 handler: function() {
                 window.location = m_cetak+'_koperasi/'+Ext.getCmp('BuyingUnit').getValue()+'/'+
                 Ext.getCmp('start').getSubmitValue()+'/'+Ext.getCmp('end').getSubmitValue()+'/excel';
                 }
                 },{
                 id: 'Tracebility',
                 text: 'Buying Unit',
                 margin: '5px',
                 scale: 'large',
                 ui: 's-button',
                 cls: 's-blue',
                 handler: function() {
                 preview_cetak_surat(m_cetak+'_bu/'+Ext.getCmp('BuyingUnit').getValue()+'/'+
                 Ext.getCmp('start').getSubmitValue()+'/'+Ext.getCmp('end').getSubmitValue()+'/'+
                 Ext.getCmp('JenisBuyingUnit').getValue());
                 }
                 },{
                 id: 'Tracebility_excel',
                 text: 'Excel Buying Unit',
                 margin: '5px',
                 scale: 'large',
                 ui: 's-button',
                 cls: 's-blue',
                 handler: function() {
                 window.location = m_cetak+'_bu/'+Ext.getCmp('BuyingUnit').getValue()+'/'+
                 Ext.getCmp('start').getSubmitValue()+'/'+Ext.getCmp('end').getSubmitValue()+'/excel';
                 }
                 },{
                 id: 'Tracebility_detail',
                 text: 'Petani',
                 margin: '5px',
                 scale: 'large',
                 ui: 's-button',
                 cls: 's-blue',
                 handler: function() {
                 preview_cetak_surat(m_cetak+'_detail/'+Ext.getCmp('BuyingUnit').getValue()+'/'+
                 Ext.getCmp('start').getSubmitValue()+'/'+Ext.getCmp('end').getSubmitValue()+'/'+
                 Ext.getCmp('JenisBuyingUnit').getValue(),1100,'40%');
                 }
                 },{
                 id: 'Tracebility_per_petani_excel',
                 text: 'Excel Petani',
                 margin: '5px',
                 scale: 'large',
                 ui: 's-button',
                 cls: 's-blue',
                 handler: function() {
                 window.location = m_cetak+'_detail/'+Ext.getCmp('BuyingUnit').getValue()+'/'+
                 Ext.getCmp('start').getSubmitValue()+'/'+Ext.getCmp('end').getSubmitValue()+'/excel';
                 }
                 }]*/
            }, {
                xtype: 'gridpanel',
                id: 'grid_koperasi',
                padding: 6,
                width: '100%',
                border: true,
                title: lang('Koperasi'),
                store: store_viewc,
                height: 180,
                features: [{
                    ftype: 'summary'
                }],
                dockedItems: [{
                    xtype: 'toolbar',
                    dock: 'top',
                    items: [{
                        xtype: 'button',
                        text: 'Print Preview',
                        handler: function () {                            
                            if (!Ext.getCmp('Provinsi').getValue()) { Ext.Msg.alert('Warning', 'Silahkan pilih provinsi'); return;}
                            if (!Ext.getCmp('Warehouse').getValue()) { Ext.Msg.alert('Warning', 'Silahkan pilih warehouse'); return;}
                            // if (!Ext.getCmp('JenisBuyingUnit').getValue()) { Ext.Msg.alert('Warning', 'Silahkan pilih jenis'); return;}
                            start   = Ext.getCmp('start').getValue();
                            end     = Ext.getCmp('end').getValue();
                            if (!start) { Ext.Msg.alert('Warning', 'Silahkan pilih tanggal awal'); return;}
                            if (!end) { Ext.Msg.alert('Warning', 'Silahkan pilih tanggal akhir'); return;}
                            url     = m_cetak + '_koperasi'
                                + '/' + formatDate(start)
                                + '/' + formatDate(end)
                                // + '/' + Ext.getCmp('JenisBuyingUnit').getValue()
                                + '/Farmer'
                                + '/' + Ext.getCmp('Provinsi').getValue()
                                + '/' + Ext.getCmp('Warehouse').getValue()
                                + '/' + Ext.getCmp('jenisSertifikasi').getValue();
                            preview_cetak_surat(url);
                        }
                    }, {
                        xtype: 'splitbutton',
                        text: 'Export',
                        menu: {
                            items: [{
                                text: 'Excel',
                                handler: function () {
                                    if (!Ext.getCmp('Provinsi').getValue()) { Ext.Msg.alert('Warning', 'Silahkan pilih provinsi'); return;}
                                    if (!Ext.getCmp('Warehouse').getValue()) { Ext.Msg.alert('Warning', 'Silahkan pilih warehouse'); return;}
                                    // if (!Ext.getCmp('JenisBuyingUnit').getValue()) { Ext.Msg.alert('Warning', 'Silahkan pilih jenis'); return;}
                                    start   = Ext.getCmp('start').getValue();
                                    end     = Ext.getCmp('end').getValue();
                                    if (!start) { Ext.Msg.alert('Warning', 'Silahkan pilih tanggal awal'); return;}
                                    if (!end) { Ext.Msg.alert('Warning', 'Silahkan pilih tanggal akhir'); return;}
                                    url     = m_cetak + '_koperasi'
                                        + '/' + formatDate(start)
                                        + '/' + formatDate(end)
                                        // + '/' + Ext.getCmp('JenisBuyingUnit').getValue()
                                        + '/Farmer'
                                        + '/' + Ext.getCmp('Provinsi').getValue()
                                        + '/' + Ext.getCmp('Warehouse').getValue()
                                        + '/' + Ext.getCmp('jenisSertifikasi').getValue()
                                        + '/excel';
                                    // window.location = url;
                                    window.open(url, 'cetak', "height=200,width=200");
                                }
                            }
                                // ,{
                                //     text: 'PDF',
                                //     handler: function(){

                                //     }
                                // }
                            ]
                        }
                    }, {
                        xtype: 'label',
                        id: 'premiumKoperasi',
                        text: ''
                    }]
                }],
                columns: [{
                    header: lang('Koperasi'),
                    dataIndex: 'name',
                    width: 230,
                    flex: 1
                }, {
                    header: 'Survey Volume (Kg)',
                    dataIndex: 'survey',
                    align: 'right',
                    width: 130,
                    renderer: Ext.util.Format.numberRenderer('0,000.00'),
                    //summaryType: 'sum'
                }, {
                    header: lang('Bruto')+' (Kg)',
                    dataIndex: 'bruto',
                    width: 120,
                    align: 'right',
                    renderer: Ext.util.Format.numberRenderer('0,000.00')
                }, {
                    header: lang('Netto')+' (Kg)',
                    dataIndex: 'netto',
                    width: 120,
                    align: 'right',
                    renderer: Ext.util.Format.numberRenderer('0,000.00')
                }, {
                    header: 'Balance (Kg)',
                    dataIndex: 'balance',
                    align: 'right',
                    width: 120,
                    renderer: Ext.util.Format.numberRenderer('0,000.00'),
                    //summaryType: 'sum'
                }, {
                    header: 'TOTAL (IDR)',
                    dataIndex: 'totalidr',
                    id: 'koperasiTotalIDR',
                    hidden: true,
                    width: 150,
                    align: 'right',
                    renderer: Ext.util.Format.numberRenderer('0,000.00')
                }, {
                    header: 'TOTAL (USD)',
                    dataIndex: 'totalusd',
                    id: 'koperasiTotalUSD',
                    hidden: true,
                    width: 150,
                    align: 'right',
                    renderer: Ext.util.Format.numberRenderer('0,000.00')
                }]
            }, {
                xtype: 'gridpanel',
                padding: 6,
                title: 'Buying Unit Details',
                id: 'grid-bu-premium',
                width: '100%',
                border: true,
                store: store_view,
                height: 330,
                autoScroll: true,
                features: [{
                    id: 'groupPremium',
                    ftype: 'groupingsummary',
                    showSummaryRow: true,
                    groupHeaderTpl: '{name}',
                    hideGroupedHeader: true,
                    enableGroupingMenu: false
                }],
                dockedItems: [{
                    xtype: 'toolbar',
                    dock: 'top',
                    items: [{
                        xtype: 'button',
                        text: 'Print Preview',
                        handler: function () {                            
                            if (!Ext.getCmp('Provinsi').getValue()) { Ext.Msg.alert('Warning', 'Silahkan pilih provinsi'); return;}
                            if (!Ext.getCmp('Warehouse').getValue()) { Ext.Msg.alert('Warning', 'Silahkan pilih warehouse'); return;}
                            // if (!Ext.getCmp('JenisBuyingUnit').getValue()) { Ext.Msg.alert('Warning', 'Silahkan pilih jenis'); return;}
                            start   = Ext.getCmp('start').getValue();
                            end     = Ext.getCmp('end').getValue();
                            if (!start) { Ext.Msg.alert('Warning', 'Silahkan pilih tanggal awal'); return;}
                            if (!end) { Ext.Msg.alert('Warning', 'Silahkan pilih tanggal akhir'); return;}
                            url     = m_cetak + '_bu'
                                + '/' + formatDate(start)
                                + '/' + formatDate(end)
                                // + '/' + Ext.getCmp('JenisBuyingUnit').getValue()
                                + '/Farmer'
                                + '/' + Ext.getCmp('Provinsi').getValue()
                                + '/' + Ext.getCmp('Warehouse').getValue()
                                + '/' + Ext.getCmp('jenisSertifikasi').getValue();
                            preview_cetak_surat(url);
                        }
                    }, {
                        xtype: 'splitbutton',
                        text: 'Export',
                        menu: {
                            items: [{
                                text: 'Excel',
                                handler: function () {
                                    if (!Ext.getCmp('Provinsi').getValue()) { Ext.Msg.alert('Warning', 'Silahkan pilih provinsi'); return;}
                                    if (!Ext.getCmp('Warehouse').getValue()) { Ext.Msg.alert('Warning', 'Silahkan pilih warehouse'); return;}
                                    // if (!Ext.getCmp('JenisBuyingUnit').getValue()) { Ext.Msg.alert('Warning', 'Silahkan pilih jenis'); return;}
                                    start   = Ext.getCmp('start').getValue();
                                    end     = Ext.getCmp('end').getValue();
                                    if (!start) { Ext.Msg.alert('Warning', 'Silahkan pilih tanggal awal'); return;}
                                    if (!end) { Ext.Msg.alert('Warning', 'Silahkan pilih tanggal akhir'); return;}
                                    url     = m_cetak + '_bu'
                                        + '/' + formatDate(start)
                                        + '/' + formatDate(end)
                                        // + '/' + Ext.getCmp('JenisBuyingUnit').getValue()
                                        + '/Farmer'
                                        + '/' + Ext.getCmp('Provinsi').getValue()
                                        + '/' + Ext.getCmp('Warehouse').getValue()
                                        + '/' + Ext.getCmp('jenisSertifikasi').getValue()
                                        + '/excel';
                                    // window.location = url;
                                    window.open(url, 'cetak', "height=200,width=200");
                                }
                            }
                                // ,{
                                //     text: 'PDF',
                                //     handler: function(){}
                                // }
                            ]
                        }
                    }, {
                        xtype: 'label',
                        id: 'premiumBU',
                        text: ''
                    }]
                }],
                columns: [
                // fields:['PerwakilanOrgId','Namec','Nameb','bruto','netto','PersenPerwakilan','Rupiah']
                // {
                //     text: 'PerwakilanOrgId',
                //     dataIndex: 'PerwakilanOrgId',
                //     hidden: true
                // }, 
                {
                    header: 'Buying Unit',
                    //text: 'Namec',
                    dataIndex: 'name',
                    width: 170,
                    summaryType: 'count',
                    summaryRenderer: function (value, summaryData, dataIndex) {
                        return ((value === 0 || value > 1) ? '(' + value + ' Buying Units)' : '(1 Buying Unit)');
                    },
                    flex: 1
                // }, {
                //     text: 'BU',
                //     dataIndex: 'Nameb',
                //     hidden: true
                }, {
                    header: 'Survey Volume (Kg)',
                    dataIndex: 'survey',
                    align: 'right',
                    width: 130,
                    renderer: Ext.util.Format.numberRenderer('0,000.00'),
                    summaryType: 'sum'
                }, {
                    header: lang('Bruto')+' (Kg)',
                    dataIndex: 'bruto',
                    align: 'right',
                    width: 120,
                    renderer: Ext.util.Format.numberRenderer('0,000.00'),
                    summaryType: 'sum'
                }, {
                    header: lang('Netto')+' (Kg)',
                    dataIndex: 'netto',
                    align: 'right',
                    width: 120,
                    renderer: Ext.util.Format.numberRenderer('0,000.00'),
                    summaryType: 'sum'
                }, {
                    header: 'Balance (Kg)',
                    dataIndex: 'balance',
                    align: 'right',
                    width: 120,
                    renderer: Ext.util.Format.numberRenderer('0,000.00'),
                    summaryType: 'sum'
                // }, {
                //     header: 'Premium (IDR)',
                //     dataIndex: 'Rupiah',
                //     align: 'right',
                //     width: 120,
                //     renderer: Ext.util.Format.numberRenderer('0,000.00'),
                //     summaryType: 'min',
                //     hidden: true
                // }, {
                //     header: 'Premium (USD)',
                //     dataIndex: 'Usd',
                //     align: 'right',
                //     width: 120,
                //     renderer: Ext.util.Format.numberRenderer('0,000.00'),
                //     summaryType: 'min',
                //     hidden: true
                }, {
                    header: 'TOTAL (IDR)',
                    dataIndex: 'totalidr',
                    id: 'buTotalIDR',
                    hidden: true,
                    align: 'right',
                    width: 150,
                    renderer: Ext.util.Format.numberRenderer('0,000.00'),
                    summaryType: 'sum'
                }, {
                    header: 'TOTAL (USD)',
                    dataIndex: 'totalusd',
                    id: 'buTotalUSD',
                    hidden: true,
                    align: 'right',
                    width: 150,
                    renderer: Ext.util.Format.numberRenderer('0,000.00'),
                    summaryType: 'sum'
                }],
                listeners: {
                    select: function (selModel, record, index, options) {
                        // console.log(record);
                        //Ext.Msg.alert("",record.get('PerwakilanOrgId'));
                        store_viewf.load({
                            params: {
                                buid: record.get('orgid')
                            }
                        });
                    }
                }
            }, {
                xtype: 'gridpanel',
                padding: 6,
                width: '100%',
                border: true,
                title: 'Farmer Details',
                store: store_viewf,
                height: 470,
                features: [{
                    id: 'groupFarmerPremium',
                    ftype: 'groupingsummary',
                    startCollapsed: true,
                    groupHeaderTpl: '{name}',
                    hideGroupedHeader: true,
                    enableGroupingMenu: false
                }],
                dockedItems: [{
                    xtype: 'toolbar',
                    dock: 'top',
                    items: [{
                        xtype: 'splitbutton',
                        text: 'Print Preview',
                        menu: {
                            items: [{
                                text: 'Summary Per Farmer',
                                handler: function () {                                    
                                    if (!Ext.getCmp('Provinsi').getValue()) { Ext.Msg.alert('Warning', 'Silahkan pilih provinsi'); return;}
                                    if (!Ext.getCmp('Warehouse').getValue()) { Ext.Msg.alert('Warning', 'Silahkan pilih warehouse'); return;}
                                    // if (!Ext.getCmp('JenisBuyingUnit').getValue()) { Ext.Msg.alert('Warning', 'Silahkan pilih jenis'); return;}
                                    start   = Ext.getCmp('start').getValue();
                                    end     = Ext.getCmp('end').getValue();
                                    if (!start) { Ext.Msg.alert('Warning', 'Silahkan pilih tanggal awal'); return;}
                                    if (!end) { Ext.Msg.alert('Warning', 'Silahkan pilih tanggal akhir'); return;}
                                    url     = m_cetak + '_detail/summary'
                                        + '/' + formatDate(start)
                                        + '/' + formatDate(end)
                                        // + '/' + Ext.getCmp('JenisBuyingUnit').getValue()
                                        + '/Farmer'
                                        + '/' + Ext.getCmp('Provinsi').getValue()
                                        + '/' + Ext.getCmp('Warehouse').getValue()
                                        + '/' + Ext.getCmp('jenisSertifikasi').getValue();
                                    preview_cetak_surat(url, 1100, '40%');                                
                                }
                            }, {
                                text: 'Details Transaction Farmer',
                                handler: function () {                                    
                                    if (!Ext.getCmp('Provinsi').getValue()) { Ext.Msg.alert('Warning', 'Silahkan pilih provinsi'); return;}
                                    if (!Ext.getCmp('Warehouse').getValue()) { Ext.Msg.alert('Warning', 'Silahkan pilih warehouse'); return;}
                                    // if (!Ext.getCmp('JenisBuyingUnit').getValue()) { Ext.Msg.alert('Warning', 'Silahkan pilih jenis'); return;}
                                    start   = Ext.getCmp('start').getValue();
                                    end     = Ext.getCmp('end').getValue();
                                    if (!start) { Ext.Msg.alert('Warning', 'Silahkan pilih tanggal awal'); return;}
                                    if (!end) { Ext.Msg.alert('Warning', 'Silahkan pilih tanggal akhir'); return;}
                                    url     = m_cetak + '_detail/details'
                                        + '/' + formatDate(start)
                                        + '/' + formatDate(end)
                                        // + '/' + Ext.getCmp('JenisBuyingUnit').getValue()
                                        + '/Farmer'
                                        + '/' + Ext.getCmp('Provinsi').getValue()
                                        + '/' + Ext.getCmp('Warehouse').getValue()
                                        + '/' + Ext.getCmp('jenisSertifikasi').getValue();
                                    preview_cetak_surat(url, 1100, '40%');
                                }
                            }, {
                                text: 'Summary Farmer per CPG',
                                handler: function () {
                                    if (!Ext.getCmp('Provinsi').getValue()) { Ext.Msg.alert('Warning', 'Silahkan pilih provinsi'); return;}
                                    if (!Ext.getCmp('Warehouse').getValue()) { Ext.Msg.alert('Warning', 'Silahkan pilih warehouse'); return;}
                                    // if (!Ext.getCmp('JenisBuyingUnit').getValue()) { Ext.Msg.alert('Warning', 'Silahkan pilih jenis'); return;}
                                    start   = Ext.getCmp('start').getValue();
                                    end     = Ext.getCmp('end').getValue();
                                    if (!start) { Ext.Msg.alert('Warning', 'Silahkan pilih tanggal awal'); return;}
                                    if (!end) { Ext.Msg.alert('Warning', 'Silahkan pilih tanggal akhir'); return;}
                                    url     = m_cetak + '_detail/summarycpg'
                                        + '/' + formatDate(start)
                                        + '/' + formatDate(end)
                                        // + '/' + Ext.getCmp('JenisBuyingUnit').getValue()
                                        + '/Farmer'
                                        + '/' + Ext.getCmp('Provinsi').getValue()
                                        + '/' + Ext.getCmp('Warehouse').getValue()
                                        + '/' + Ext.getCmp('jenisSertifikasi').getValue();
                                    preview_cetak_surat(url, 1100, '40%');
                                }
                            }, {
                                text: 'Details Farmer per CPG',
                                handler: function () {
                                    if (!Ext.getCmp('Provinsi').getValue()) { Ext.Msg.alert('Warning', 'Silahkan pilih provinsi'); return;}
                                    if (!Ext.getCmp('Warehouse').getValue()) { Ext.Msg.alert('Warning', 'Silahkan pilih warehouse'); return;}
                                    // if (!Ext.getCmp('JenisBuyingUnit').getValue()) { Ext.Msg.alert('Warning', 'Silahkan pilih jenis'); return;}
                                    start   = Ext.getCmp('start').getValue();
                                    end     = Ext.getCmp('end').getValue();
                                    if (!start) { Ext.Msg.alert('Warning', 'Silahkan pilih tanggal awal'); return;}
                                    if (!end) { Ext.Msg.alert('Warning', 'Silahkan pilih tanggal akhir'); return;}
                                    url     = m_cetak + '_detail/detailscpg'
                                        + '/' + formatDate(start)
                                        + '/' + formatDate(end)
                                        // + '/' + Ext.getCmp('JenisBuyingUnit').getValue()
                                        + '/Farmer'
                                        + '/' + Ext.getCmp('Provinsi').getValue()
                                        + '/' + Ext.getCmp('Warehouse').getValue()
                                        + '/' + Ext.getCmp('jenisSertifikasi').getValue();
                                    preview_cetak_surat(url, 1100, '40%');
                                }
                            }]
                        }
                    }, {
                        xtype: 'splitbutton',
                        text: 'Export',
                        menu: {
                            items: [{
                                text: 'Excel',
                                menu: {
                                    items: [{
                                        text: 'Summary Per Farmer',
                                        handler: function () {
                                            if (!Ext.getCmp('Provinsi').getValue()) { Ext.Msg.alert('Warning', 'Silahkan pilih provinsi'); return;}
                                            if (!Ext.getCmp('Warehouse').getValue()) { Ext.Msg.alert('Warning', 'Silahkan pilih warehouse'); return;}
                                            // if (!Ext.getCmp('JenisBuyingUnit').getValue()) { Ext.Msg.alert('Warning', 'Silahkan pilih jenis'); return;}
                                            start   = Ext.getCmp('start').getValue();
                                            end     = Ext.getCmp('end').getValue();
                                            if (!start) { Ext.Msg.alert('Warning', 'Silahkan pilih tanggal awal'); return;}
                                            if (!end) { Ext.Msg.alert('Warning', 'Silahkan pilih tanggal akhir'); return;}
                                            url     = m_cetak + '_detail/summary'
                                                + '/' + formatDate(start)
                                                + '/' + formatDate(end)
                                                // + '/' + Ext.getCmp('JenisBuyingUnit').getValue()
                                                + '/Farmer'
                                                + '/' + Ext.getCmp('Provinsi').getValue()
                                                + '/' + Ext.getCmp('Warehouse').getValue()
                                                + '/' + Ext.getCmp('jenisSertifikasi').getValue()
                                                + '/excel';
                                            // window.location = url;
                                            window.open(url, 'cetak', "height=200,width=200");
                                        }
                                    }, {
                                        text: 'Details Transaction Farmer',
                                        handler: function () {
                                            if (!Ext.getCmp('Provinsi').getValue()) { Ext.Msg.alert('Warning', 'Silahkan pilih provinsi'); return;}
                                            if (!Ext.getCmp('Warehouse').getValue()) { Ext.Msg.alert('Warning', 'Silahkan pilih warehouse'); return;}
                                            // if (!Ext.getCmp('JenisBuyingUnit').getValue()) { Ext.Msg.alert('Warning', 'Silahkan pilih jenis'); return;}
                                            start   = Ext.getCmp('start').getValue();
                                            end     = Ext.getCmp('end').getValue();
                                            if (!start) { Ext.Msg.alert('Warning', 'Silahkan pilih tanggal awal'); return;}
                                            if (!end) { Ext.Msg.alert('Warning', 'Silahkan pilih tanggal akhir'); return;}
                                            url     = m_cetak + '_detail/details'
                                                + '/' + formatDate(start)
                                                + '/' + formatDate(end)
                                                // + '/' + Ext.getCmp('JenisBuyingUnit').getValue()
                                                + '/Farmer'
                                                + '/' + Ext.getCmp('Provinsi').getValue()
                                                + '/' + Ext.getCmp('Warehouse').getValue()
                                                + '/' + Ext.getCmp('jenisSertifikasi').getValue()
                                                + '/excel';
                                            // window.location = url;
                                            window.open(url, 'cetak', "height=200,width=200");
                                        }
                                    }, {
                                        text: 'Summary Farmer per CPG',
                                        handler: function () {
                                            if (!Ext.getCmp('Provinsi').getValue()) { Ext.Msg.alert('Warning', 'Silahkan pilih provinsi'); return;}
                                            if (!Ext.getCmp('Warehouse').getValue()) { Ext.Msg.alert('Warning', 'Silahkan pilih warehouse'); return;}
                                            // if (!Ext.getCmp('JenisBuyingUnit').getValue()) { Ext.Msg.alert('Warning', 'Silahkan pilih jenis'); return;}
                                            start   = Ext.getCmp('start').getValue();
                                            end     = Ext.getCmp('end').getValue();
                                            if (!start) { Ext.Msg.alert('Warning', 'Silahkan pilih tanggal awal'); return;}
                                            if (!end) { Ext.Msg.alert('Warning', 'Silahkan pilih tanggal akhir'); return;}
                                            url     = m_cetak + '_detail/summarycpg'
                                                + '/' + formatDate(start)
                                                + '/' + formatDate(end)
                                                // + '/' + Ext.getCmp('JenisBuyingUnit').getValue()
                                                + '/Farmer'
                                                + '/' + Ext.getCmp('Provinsi').getValue()
                                                + '/' + Ext.getCmp('Warehouse').getValue()
                                                + '/' + Ext.getCmp('jenisSertifikasi').getValue()
                                                + '/excel';
                                            // window.location = url;
                                            window.open(url, 'cetak', "height=200,width=200");
                                        }
                                    }, {
                                        text: 'Details Farmer per CPG',
                                        handler: function () {
                                            if (!Ext.getCmp('Provinsi').getValue()) { Ext.Msg.alert('Warning', 'Silahkan pilih provinsi'); return;}
                                            if (!Ext.getCmp('Warehouse').getValue()) { Ext.Msg.alert('Warning', 'Silahkan pilih warehouse'); return;}
                                            // if (!Ext.getCmp('JenisBuyingUnit').getValue()) { Ext.Msg.alert('Warning', 'Silahkan pilih jenis'); return;}
                                            start   = Ext.getCmp('start').getValue();
                                            end     = Ext.getCmp('end').getValue();
                                            if (!start) { Ext.Msg.alert('Warning', 'Silahkan pilih tanggal awal'); return;}
                                            if (!end) { Ext.Msg.alert('Warning', 'Silahkan pilih tanggal akhir'); return;}
                                            url     = m_cetak + '_detail/detailscpg'
                                                + '/' + formatDate(start)
                                                + '/' + formatDate(end)
                                                // + '/' + Ext.getCmp('JenisBuyingUnit').getValue()
                                                + '/Farmer'
                                                + '/' + Ext.getCmp('Provinsi').getValue()
                                                + '/' + Ext.getCmp('Warehouse').getValue()
                                                + '/' + Ext.getCmp('jenisSertifikasi').getValue()
                                                + '/excel';
                                            // window.location = url;
                                            window.open(url, 'cetak', "height=200,width=200");
                                        }
                                    }]
                                }
                            }
                                // ,{
                                //     text: 'PDF',
                                //     handler: function(){}
                                // }
                            ]
                        }
                    }, {
                        xtype: 'label',
                        id: 'premiumFarmer',
                        text: ''
                    }]
                }],
                columns: [{
                    text: 'FarmerID',
                    dataIndex: 'id',
                    hidden: true
                }, {
                    header: 'farmer',
                    dataIndex: 'farmer',
                    hidden: true
                }, {
                    header: 'Date Transaction',
                    dataIndex: 'datetransaction',
                    width: 130,
                    summaryType: 'count',
                    summaryRenderer: function (value, summaryData, dataIndex) {
                        return ((value === 0 || value > 1) ? '(' + value + ' Transactions)' : '(1 Transaction)');
                    },
                    flex: 1
                }, {
                    header: 'Survey Volume (Kg)',
                    dataIndex: 'survey',
                    width: 130,
                    align: 'right',
                    summaryType: 'max',
                    renderer: Ext.util.Format.numberRenderer('0,000.00')
                }, {
                    header: lang('Bruto')+' (Kg)',
                    dataIndex: 'bruto',
                    align: 'right',
                    width: 120,
                    renderer: Ext.util.Format.numberRenderer('0,000.00'),
                    summaryType: 'sum'
                }, {
                    header: lang('Netto')+' (Kg)',
                    text: 'netto',
                    dataIndex: 'netto',
                    align: 'right',
                    width: 120,
                    renderer: Ext.util.Format.numberRenderer('0,000.00'),
                    summaryType: 'sum'
                }, {
                    header: 'Balance (Kg)',
                    dataIndex: 'balance',
                    align: 'right',
                    width: 120,
                    renderer: Ext.util.Format.numberRenderer('0,000.00'),
                    summaryType: 'min'
                // }, {
                //     header: 'Premium (IDR)',
                //     dataIndex: 'PremiumIDR',
                //     align: 'right',
                //     renderer: Ext.util.Format.numberRenderer('0,000.00'),
                //     type: 'float',
                //     width: 120,
                //     summaryType: 'min',
                //     hidden: true
                // }, {
                //     header: 'Premium (USD)',
                //     dataIndex: 'PremiumUSD',
                //     align: 'right',
                //     renderer: Ext.util.Format.numberRenderer('0,000.00'),
                //     width: 120,
                //     summaryType: 'min',
                //     hidden: true
                }, {
                    header: 'TOTAL (IDR)',
                    dataIndex: 'totalidr',
                    id: 'farmerTotalIDR',
                    hidden: true,
                    align: 'right',
                    width: 150,
                    renderer: Ext.util.Format.numberRenderer('0,000.00'),
                    summaryType: 'sum'
                }, {
                    header: 'TOTAL (USD)',
                    dataIndex: 'totalusd',
                    id: 'farmerTotalUSD',
                    hidden: true,
                    align: 'right',
                    width: 150,
                    renderer: Ext.util.Format.numberRenderer('0,000.00'),
                    summaryType: 'sum'
                }],
                bbar: Ext.create('Ext.PagingToolbar', {
                    store: store_viewf,
                    displayInfo: true,
                    //displayMsg: 'Farmers {0} - {1} of {2}',
                    displayMsg: 'Records : {2}',
                    emptyMsg: "Record empty",
                    items: [
                        '-', {
                            //text: 'Show Preview',
                            enableToggle: true,
                            toggleHandler: function (btn, pressed) {
                            }
                        }]
                })
            }]
        }]
    });

});
function traceability_only(set) {
    if (!set) {
        Ext.getCmp('koperasiTotalIDR').show();
        Ext.getCmp('koperasiTotalUSD').show();
        Ext.getCmp('buTotalIDR').show();
        Ext.getCmp('buTotalUSD').show();
        Ext.getCmp('farmerTotalIDR').show();
        Ext.getCmp('farmerTotalUSD').show();
    } else {
        Ext.getCmp('koperasiTotalIDR').hide();
        Ext.getCmp('koperasiTotalUSD').hide();
        Ext.getCmp('buTotalIDR').hide();
        Ext.getCmp('buTotalUSD').hide();
        Ext.getCmp('farmerTotalIDR').hide();
        Ext.getCmp('farmerTotalUSD').hide();
    }
}
function with_premium(set) {
    if (set) {
        Ext.getCmp('koperasiTotalIDR').show();
        Ext.getCmp('koperasiTotalUSD').show();
        Ext.getCmp('buTotalIDR').show();
        Ext.getCmp('buTotalUSD').show();
        Ext.getCmp('farmerTotalIDR').show();
        Ext.getCmp('farmerTotalUSD').show();
    } else {
        Ext.getCmp('koperasiTotalIDR').hide();
        Ext.getCmp('koperasiTotalUSD').hide();
        Ext.getCmp('buTotalIDR').hide();
        Ext.getCmp('buTotalUSD').hide();
        Ext.getCmp('farmerTotalIDR').hide();
        Ext.getCmp('farmerTotalUSD').hide();
    }
}
function formatDate(date) {
    var d = new Date(date),
        month = '' + (d.getMonth() + 1),
        day = '' + d.getDate(),
        year = d.getFullYear();

    if (month.length < 2) month = '0' + month;
    if (day.length < 2) day = '0' + day;

    return [year, month, day].join('-');
}
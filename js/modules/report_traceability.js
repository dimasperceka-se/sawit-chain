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

    var mc_Koperasi = Ext.create('Ext.data.Store', {
        extend: 'Ext.data.Model',
        fields: ['id', 'label'],
        autoLoad: true,
        pageSize: 10,
        proxy: {
            type: 'ajax',
            url: m_Koperasi,
            reader: {
                type: 'json',
                root: 'data'
            }
        }
    });
    
    var mc_BuyingUnit = Ext.create('Ext.data.Store', {
        extend: 'Ext.data.Model',
        fields: ['id', 'label'],
        autoLoad: true,
        pageSize: 10,
        proxy: {
            type: 'ajax',
            url: m_BuyingUnit,
            reader: {
                type: 'json',
                root: 'data'
            }
        }
    });
    
    var mc_Farmer = Ext.create('Ext.data.Store', {
        extend: 'Ext.data.Model',
        fields: ['id', 'label'],
        autoLoad: true,
        pageSize: 10,
        proxy: {
            type: 'ajax',
            url: m_Farmer,
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

    var mc_jenis_sertifikasi = Ext.create('Ext.data.Store', {
        fields: ['id','label'],
        data: [{
            "id":1,
            "label": lang("Sertifikasi")
        }, {
            "id":0,
            "label": lang("All Transaction")
        }]
    });

    var mc_jenis_report = Ext.create('Ext.data.Store', {
        fields: ['id','label'],
        data: [{
            "id":'wh',
            "label": lang("Warehouse")
        }, {
            "id":'coop',
            "label": lang("Koperasi")
        }, {
            "id":'bu',
            "label": lang("Trader / Sce")
        }, {
            "id":'farmer',
            "label": lang("Farmer")
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
            {name: 'orgid', type: 'int'},
            {name: 'name', type: 'string'},
            {name: 'name_b', type: 'string'},
            {name: 'orgtype', type: 'string'},
            {name: 'survey', type: 'float'},
            {name: 'quota', type: 'float'},
            {name: 'bruto', type: 'float'},
            {name: 'netto', type: 'float'},
            {name: 'totalusd', type: 'float'},
            {name: 'totalidr', type: 'float'},
            {name: 'balance', type: 'float'},
            {name: 'paidkg', type: 'float'},
            {name: 'unpaidkg', type: 'float'},
            {name: 'paidusd', type: 'float'},
            {name: 'unpaidusd', type: 'float'}
        ],
        autoload: false,
        proxy: {
            type: 'ajax',
            url: m_viewreport,
            reader: {
                type: 'json',
                root: 'data',
                idProperty:'orgid'
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
        fields: [
            {name: 'datetransaction', type: 'string'},
            //{name: 'id', type: 'int'},
            {name: 'farmer', type: 'string'},
            {name: 'name', type: 'string'},
            {name: 'survey', type: 'float'},
            {name: 'quota', type: 'float'},
            {name: 'bruto', type: 'float'},
            {name: 'netto', type: 'float'},
            {name: 'totalusd', type: 'float'},
            {name: 'totalidr', type: 'float'},
            {name: 'balance', type: 'float'},
            {name: 'paidkg', type: 'float'},
            {name: 'unpaidkg', type: 'float'},
            {name: 'paidusd', type: 'float'},
            {name: 'unpaidusd', type: 'float'}
        ],
        autoload: false,
        proxy: {
            type: 'ajax',
            url: m_viewreportf,
            reader: {
                type: 'json',
                root: 'data',
                idProperty:'id'
                //totalProperty: 'total'
            }
        },
        groupField: 'farmer',
        listeners: {
            beforeload: function (store_viewf, operation) {
            //console.log(store_viewf)
                store_viewf.proxy.extraParams.startd        = Ext.getCmp('start').getValue(),
                store_viewf.proxy.extraParams.end           = Ext.getCmp('end').getValue(),
                // store_viewf.proxy.extraParams.jenis         = Ext.getCmp('JenisBuyingUnit').getValue(),
                store_viewf.proxy.extraParams.jenis         = 'Farmer',
                store_viewf.proxy.extraParams.provinsi      = Ext.getCmp('Provinsi').getValue(),
                store_viewf.proxy.extraParams.warehouse     = Ext.getCmp('Warehouse').getValue(),
                store_viewf.proxy.extraParams.sert          = Ext.getCmp('jenisSertifikasi').getValue(),
                store_viewf.proxy.extraParams.buid          = Ext.getCmp('defBU').getValue(),
                store_viewf.proxy.extraParams.coopID          = Ext.getCmp('defCoop').getValue()
                //console.log(store_viewf);
            }
        }
    });
    var store_viewftot = Ext.create('Ext.data.Store', {
        fields: [
            {name: 'datetransaction', type: 'string'},
            //{name: 'id', type: 'int'},
            {name: 'farmer', type: 'string'},
            {name: 'name', type: 'string'},
            {name: 'survey', type: 'float'},
            {name: 'quota', type: 'float'},
            {name: 'bruto', type: 'float'},
            {name: 'netto', type: 'float'},
            {name: 'totalusd', type: 'float'},
            {name: 'totalidr', type: 'float'},
            {name: 'balance', type: 'float'},
            {name: 'paidkg', type: 'float'},
            {name: 'unpaidkg', type: 'float'},
            {name: 'paidusd', type: 'float'},
            {name: 'unpaidusd', type: 'float'}
        ],
        autoload: false,
        proxy: {
            type: 'ajax',
            url: m_viewreportf,
            reader: {
                type: 'json',
                root: 'data',
                idProperty:'id'
                //totalProperty: 'total'
            }
        },
        groupField: 'farmer',
        listeners: {
            beforeload: function (store_viewftot, operation) {
            //console.log(store_viewf)
                store_viewftot.proxy.extraParams.startd        = Ext.getCmp('start').getValue(),
                store_viewftot.proxy.extraParams.end           = Ext.getCmp('end').getValue(),
                // store_viewf.proxy.extraParams.jenis         = Ext.getCmp('JenisBuyingUnit').getValue(),
                store_viewftot.proxy.extraParams.jenis         = 'Farmer',
                store_viewftot.proxy.extraParams.provinsi      = Ext.getCmp('Provinsi').getValue(),
                store_viewftot.proxy.extraParams.warehouse     = Ext.getCmp('Warehouse').getValue(),
                store_viewftot.proxy.extraParams.sert          = Ext.getCmp('jenisSertifikasi').getValue(),
                store_viewftot.proxy.extraParams.buid          = Ext.getCmp('defBU').getValue(),
                store_viewftot.proxy.extraParams.coopID        = Ext.getCmp('defCoop').getValue()
                store_viewftot.proxy.extraParams.tot           = 1;
                //console.log(store_viewf);
            }
        }
    });
   function set_org(orgid,date) {
      Ext.Ajax.request({
          url: m_premium_org,
          method: 'GET',
          params: {
              orgid: orgid,date:date
          },
          success: function(response){
              var data = $.parseJSON(response.responseText);
              var usdKoperasi     = parseFloat(data.PersenBuyinUnit)/100*parseFloat(data.USD);
              var rpKoperasi      = usdKoperasi*parseFloat(data.Kurs);
              var usdBU           = parseFloat(data.PersenPerwakilan)/100*parseFloat(data.USD);
              var rpBU            = usdBU*parseFloat(data.Kurs);
              var usdFarmer       = parseFloat(data.PersenPetani)/100*parseFloat(data.USD);
              var rpFarmer        = usdFarmer*parseFloat(data.Kurs);
              //Ext.getCmp('premiumKoperasi').setText('Premium : IDR '+number_format(rpKoperasi,0,'.',',')+' | USD '+parseFloat(usdKoperasi).toFixed(2));
              //Ext.getCmp('premiumBU').setText('Premium : IDR '+number_format(rpBU,0,'.',',')+' | USD '+parseFloat(usdBU).toFixed(2));
              //Ext.getCmp('premiumFarmer').setText('Premium : IDR '+number_format(rpFarmer,0,'.',',')+' | USD '+parseFloat(usdFarmer).toFixed(2));
          }
      });
   }

    //warehouse
    var store_vieww = Ext.create('Ext.data.Store', {
        extend: 'Ext.data.Model',
        fields: [
            {name: 'name', type: 'string'},
            {name: 'survey', type: 'int'},
            {name: 'quota', type: 'int'},
            {name: 'transcount', type: 'int'},
            {name: 'batchcount', type: 'int'},
            {name: 'coopcount', type: 'int'},
            {name: 'bruto', type: 'float'},
            {name: 'netto', type: 'float'}
        ],
        autoload: false,
        proxy: {
            type: 'ajax',
            url: m_viewreportw,
            reader: {
                type: 'json',
                root: 'data'
            }
        }
    });

    var store_viewcoop = Ext.create('Ext.data.Store', {
        extend: 'Ext.data.Model',
        fields: [
            {name: 'name', type: 'string'},
            {name: 'survey', type: 'int'},
            {name: 'quota', type: 'int'},
            {name: 'transcount', type: 'int'},
            {name: 'batchcount', type: 'int'},
            {name: 'deliveredcount', type: 'int'},
            {name: 'bruto', type: 'float'},
            {name: 'netto', type: 'float'}
        ],
        autoload: false,
        proxy: {
            type: 'ajax',
            url: m_viewreportcoop,
            reader: {
                type: 'json',
                root: 'data'
            }
        }
    });
    
    var store_viewbu = Ext.create('Ext.data.Store', {
        extend: 'Ext.data.Model',
        fields: [
            {name: 'name', type: 'string'},
            {name: 'survey', type: 'int'},
            {name: 'quota', type: 'int'},
            {name: 'transcount', type: 'int'},
            {name: 'batchcount', type: 'int'},
            {name: 'deliveredcount', type: 'int'},
            {name: 'bruto', type: 'float'},
            {name: 'netto', type: 'float'}
        ],
        autoload: false,
        proxy: {
            type: 'ajax',
            url: m_viewreportbu,
            reader: {
                type: 'json',
                root: 'data'
            }
        }
    });
    
    var store_viewfarmer = Ext.create('Ext.data.Store', {
        extend: 'Ext.data.Model',
        fields: [
            {name: 'name', type: 'string'},
            {name: 'cpg', type: 'string'},
            {name: 'village', type: 'string'},
            {name: 'survey', type: 'int'},
            {name: 'quota', type: 'int'},
            {name: 'transcount', type: 'int'},
            {name: 'deliveredcount', type: 'int'},
            {name: 'bruto', type: 'float'},
            {name: 'netto', type: 'float'}
        ],
        autoload: false,
        proxy: {
            type: 'ajax',
            url: m_viewreportfarmer,
            reader: {
                type: 'json',
                root: 'data'
            }
        }
    });

    var store_viewwtrans = Ext.create('Ext.data.Store', {
        extend: 'Ext.data.Model',
        fields: [
            {name: 'datetransaction', type: 'string'},
            {name: 'po', type: 'string'},
            {name: 'batchnumber', type: 'string'},
            {name: 'bruto', type: 'float'},
            {name: 'netto', type: 'float'}
        ],
        autoload: false,
        proxy: {
            type: 'ajax',
            url: m_viewreportwtrans,
            reader: {
                type: 'json',
                root: 'data'
            }
        },
        listeners: {
            beforeload: function (store_viewwtrans, operation) {
                store_viewwtrans.proxy.extraParams.startd        = Ext.getCmp('start').getValue(),
                store_viewwtrans.proxy.extraParams.end           = Ext.getCmp('end').getValue(),
                store_viewwtrans.proxy.extraParams.provinsi      = Ext.getCmp('Provinsi').getValue(),
                store_viewwtrans.proxy.extraParams.warehouse     = Ext.getCmp('Warehouse').getValue(),
                store_viewwtrans.proxy.extraParams.sert          = Ext.getCmp('jenisSertifikasi').getValue(),
                store_viewwtrans.proxy.extraParams.jenisReport   = Ext.getCmp('jenisReport').getValue()
            }
        }
    });
    
    var store_viewcooptrans = Ext.create('Ext.data.Store', {
        extend: 'Ext.data.Model',
        fields: [
            {name: 'name', type: 'string'},
            {name: 'datetransaction', type: 'string'},
            {name: 'po', type: 'string'},
            {name: 'batchnumber', type: 'string'},
            {name: 'batchstatus', type: 'string'},
            {name: 'destination', type: 'string'},
            {name: 'bruto', type: 'float'},
            {name: 'netto', type: 'float'}
        ],
        autoload: false,
        proxy: {
            type: 'ajax',
            url: m_viewreportcooptrans,
            reader: {
                type: 'json',
                root: 'data'
            }
        },
        listeners: {
            beforeload: function (store_viewwtrans, operation) {
                store_viewwtrans.proxy.extraParams.startd        = Ext.getCmp('start').getValue(),
                store_viewwtrans.proxy.extraParams.end           = Ext.getCmp('end').getValue(),
                store_viewwtrans.proxy.extraParams.provinsi      = Ext.getCmp('Provinsi').getValue(),
                store_viewwtrans.proxy.extraParams.warehouse     = Ext.getCmp('Warehouse').getValue(),
                store_viewwtrans.proxy.extraParams.coop          = Ext.getCmp('fKoperasi').getValue(),
                store_viewwtrans.proxy.extraParams.sert          = Ext.getCmp('jenisSertifikasi').getValue(),
                store_viewwtrans.proxy.extraParams.jenisReport   = Ext.getCmp('jenisReport').getValue()
            }
        }
    });
    
    var store_viewbutrans = Ext.create('Ext.data.Store', {
        extend: 'Ext.data.Model',
        fields: [
            {name: 'name', type: 'string'},
            {name: 'datetransaction', type: 'string'},
            {name: 'po', type: 'string'},
            {name: 'batchnumber', type: 'string'},
            {name: 'batchstatus', type: 'string'},
            {name: 'destination', type: 'string'},
            {name: 'bruto', type: 'float'},
            {name: 'netto', type: 'float'}
        ],
        autoload: false,
        proxy: {
            type: 'ajax',
            url: m_viewreportbutrans,
            reader: {
                type: 'json',
                root: 'data'
            }
        },
        listeners: {
            beforeload: function (store_viewwtrans, operation) {
                store_viewwtrans.proxy.extraParams.startd        = Ext.getCmp('start').getValue(),
                store_viewwtrans.proxy.extraParams.end           = Ext.getCmp('end').getValue(),
                store_viewwtrans.proxy.extraParams.provinsi      = Ext.getCmp('Provinsi').getValue(),
                store_viewwtrans.proxy.extraParams.warehouse     = Ext.getCmp('Warehouse').getValue(),
                store_viewwtrans.proxy.extraParams.coop          = Ext.getCmp('fKoperasi').getValue(),
                store_viewwtrans.proxy.extraParams.bu          = Ext.getCmp('fBuyingUnit').getValue(),
                store_viewwtrans.proxy.extraParams.sert          = Ext.getCmp('jenisSertifikasi').getValue(),
                store_viewwtrans.proxy.extraParams.jenisReport   = Ext.getCmp('jenisReport').getValue()
            }
        }
    });
    
    var store_viewfarmertrans = Ext.create('Ext.data.Store', {
        extend: 'Ext.data.Model',
        fields: [
            {name: 'name', type: 'string'},
            {name: 'cpg', type: 'string'},
            {name: 'village', type: 'string'},
            {name: 'datetransaction', type: 'string'},
            {name: 'transactionnumber', type: 'string'},
            {name: 'po', type: 'string'},
            {name: 'batchnumber', type: 'string'},
            {name: 'batchstatus', type: 'string'},
            {name: 'destination', type: 'string'},
            {name: 'bruto', type: 'float'},
            {name: 'netto', type: 'float'}
        ],
        autoload: false,
        proxy: {
            type: 'ajax',
            url: m_viewreportfarmertrans,
            reader: {
                type: 'json',
                root: 'data'
            }
        },
        listeners: {
            beforeload: function (store_viewwtrans, operation) {
                store_viewwtrans.proxy.extraParams.startd        = Ext.getCmp('start').getValue(),
                store_viewwtrans.proxy.extraParams.end           = Ext.getCmp('end').getValue(),
                store_viewwtrans.proxy.extraParams.provinsi      = Ext.getCmp('Provinsi').getValue(),
                store_viewwtrans.proxy.extraParams.warehouse     = Ext.getCmp('Warehouse').getValue(),
                store_viewwtrans.proxy.extraParams.coop          = Ext.getCmp('fKoperasi').getValue(),
                store_viewwtrans.proxy.extraParams.bu            = Ext.getCmp('fBuyingUnit').getValue(),
                store_viewwtrans.proxy.extraParams.farmer        = Ext.getCmp('fFarmer').getValue(),
                store_viewwtrans.proxy.extraParams.sert          = Ext.getCmp('jenisSertifikasi').getValue(),
                store_viewwtrans.proxy.extraParams.jenisReport   = Ext.getCmp('jenisReport').getValue()
            }
        }
    });

    // koperasi
    var store_viewc = Ext.create('Ext.data.Store', {
        extend: 'Ext.data.Model',
        fields: [
            {name: 'orgid', type: 'int'},
            {name: 'name', type: 'string'},
            {name: 'orgtype', type: 'string'},
            {name: 'survey', type: 'float'},
            {name: 'quota', type: 'float'},
            {name: 'bruto', type: 'float'},
            {name: 'netto', type: 'float'},
            {name: 'totalusd', type: 'float'},
            {name: 'totalidr', type: 'float'},
            {name: 'balance', type: 'float'},
            {name: 'paidkg', type: 'float'},
            {name: 'unpaidkg', type: 'float'},
            {name: 'paidusd', type: 'float'},
            {name: 'unpaidusd', type: 'float'}
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
        user_province = m_user_province.split(',');
        if (user_province.length == 1 && m_user_province!='') { //untuk admin m_user_province=''
            Ext.getCmp('Provinsi').setValue(''+m_user_province).setReadOnly(true);
        }
    }, 1000)

    var tab = Ext.create('Ext.Panel', {
        renderTo: 'ext-content',
        //height : 900,
        frame: false,
        items: [{
            xtype: 'fieldset',
            title: 'Traceability',
            id: 'e',
            height: 1500,
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
                                    set_org(nv)
                                    mc_certification_period.load({
                                        params: {
                                            wh: nv
                                        },
                                        callback: function(records, operation, success) {
                                             if (success) {
                                                 if (mc_certification_period.getCount() > 0) {
                                                     Ext.getCmp('layoutJenisSertifikasi').show();
                                                     // Ext.getCmp('layoutPeriodeSertifikasi').show();
                                                     mc_jenis_sertifikasi.clearData();
                                                     mc_jenis_sertifikasi.removeAll();
                                                     mc_jenis_sertifikasi.add({id:1, label: lang("Sertifikasi")});
                                                     mc_jenis_sertifikasi.add({id:0, label: lang("All Transaction")});
                                                 } else {
                                                     Ext.getCmp('layoutJenisSertifikasi').hide();
                                                     Ext.getCmp('layoutPeriodeSertifikasi').hide();
                                                     Ext.getCmp('jenisSertifikasi').setValue(0);
         
                                                     mc_jenis_sertifikasi.clearData();
                                                     mc_jenis_sertifikasi.removeAll();
                                                     mc_jenis_sertifikasi.add({id:0, label: lang("All Transaction")});
                                                 }
                                             }
                                        }                                        
                                    });

                                    /*mc_Koperasi.load({
                                        params: {
                                            wh: nv,
                                            start : Ext.getCmp('start').getValue(),
                                            end : Ext.getCmp('end').getValue()
                                        }
                                    });*/
                                }
                            }
                        }]
                    }, 
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
                            padding: 5,
                            listeners: {
                                change: function (cb, nv, ov) {
                                    set_org(Ext.getCmp('Warehouse').getValue(),this.value)
                                }
                            }
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
                            padding: 5,
                            listeners: {
                                change: function (cb, nv, ov) {
                                }
                            }
                        }]
                    },  {
                        columnWidth: .10,
                        layout: 'form',
                        padding: 3,
                        hidden: true,
                        border: false,
                        items: [{
                            xtype: 'button',
                            id: 'btnGenerateRPT',
                            name: 'btnGenerateRPT',
                            text: 'Generate RPT',
                            padding: 3,
                            handler: function () {
                                Ext.MessageBox.confirm('Message', 'Generate RPT Traceability ?', function(btn) {
                                    if (btn == 'yes') {
                                        Ext.Ajax.request({
                                            waitMsg: 'Please Wait',
                                            url: m_rpt,
                                            success: function(response, opts) {
                                                var obj = Ext.decode(response.responseText);
                                                switch (obj.success) {
                                                    case true:
                                                        Ext.MessageBox.alert('Success', lang('Generate RPT Traceability success.'));
                                                        break;
                                                    default:
                                                        Ext.MessageBox.alert('error', lang('Could not connect to the database. Retry later'));
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
                    }, {
                        xtype: 'textfield',
                        id: 'defCoop',
                        name: 'defCoop',
                        inputType: 'hidden'
                    }, {
                        xtype: 'textfield',
                        id: 'defBU',
                        name: 'defBU',
                        inputType: 'hidden'
                    }]
                }, 
                //**//
                {
                    layout: 'column',
                    border: false,
                    items: [{
                        columnWidth: .15,
                        layout: 'form',
                        id: 'layoutJenisReport',
                        padding: 3,
                        border: false,
                        items: [{
                            emptyText: '-- Jenis Report--',
                            id: 'jenisReport',
                            name: 'jenisReport',
                            xtype: 'combo',
                            width: 100,
                            labelWidth: 60,
                            store: mc_jenis_report,
                            displayField: 'label',
                            valueField: 'id',
                            queryMode: 'local',
                            listeners: {
                                change: function (cb, nv, ov) {
                                    var ware = Ext.getCmp('Warehouse').getValue();
                                    var st = Ext.getCmp('start').getValue();
                                    var en = Ext.getCmp('end').getValue();
                                    if (ware == '' || ware == 'undefined' || ware == '%%' || ware == null || st == '' || st == 'undefined' || st == '%%' || st == null || en == '' || end == 'undefined' || end == '%%' || end == null) {
                                        Ext.Msg.alert('Warning', 'Warehouse, Start Date, End Date must be filled!');
                                    }else{
                                        Ext.getCmp('layoutKoperasi').hide();
                                        Ext.getCmp('layoutBuyingUnit').hide();
                                        Ext.getCmp('layoutFarmer').hide();
                                        //tambah hidenya disini

                                        if (nv == 'wh') {
                                            //Ext.getCmp('layoutPeriodeSertifikasi').show();
                                            //Ext.getCmp('start').setReadOnly(true);
                                            //Ext.getCmp('end').setReadOnly(true);
                                        }
                                        if (nv == 'coop' /*|| nv == 'bu'*/){
                                            mc_Koperasi.load({
                                                params: {
                                                    wh: Ext.getCmp('Warehouse').getValue(),
                                                    start : Ext.getCmp('start').getValue(),
                                                    end : Ext.getCmp('end').getValue()
                                                }
                                            });
                                            Ext.getCmp('layoutKoperasi').show();
                                        }
                                        if (nv == 'bu'){
                                            mc_BuyingUnit.load({
                                                params: {
                                                    wh: Ext.getCmp('Warehouse').getValue(),
                                                    start : Ext.getCmp('start').getValue(),
                                                    end : Ext.getCmp('end').getValue()
                                                }
                                            });
                                            Ext.getCmp('layoutBuyingUnit').show();
                                        }
                                        
                                        if (nv == 'farmer'){
                                            mc_Farmer.load({
                                                params: {
                                                    wh: Ext.getCmp('Warehouse').getValue(),
                                                    start : Ext.getCmp('start').getValue(),
                                                    end : Ext.getCmp('end').getValue()
                                                }
                                            });
                                            Ext.getCmp('layoutFarmer').show();
                                        }
                                    }
                                }
                            }
                        }]
                    }, {
                        columnWidth: .14,
                        layout: 'form',
                        id: 'layoutKoperasi',
                        hidden:true,
                        padding: 3,
                        border: false,
                        items: [{
                            emptyText: '-- '+ lang('All')+' '+lang('Koperasi')+' --',
                            id: 'fKoperasi',
                            name: 'fKoperasi',
                            xtype: 'combo',
                            width: 100,
                            labelWidth: 60,
                            multiSelect: true,
                            store: mc_Koperasi,
                            displayField: 'label',
                            valueField: 'id',
                            queryMode: 'local',
                            listeners: {
                                change: function (cb, nv, ov) {
                                    
                                }
                            }
                        }]
                    }, {
                        columnWidth: .14,
                        layout: 'form',
                        id: 'layoutBuyingUnit',
                        hidden:true,
                        padding: 3,
                        border: false,
                        items: [{
                            emptyText: '-- '+ lang('All')+' '+lang('Buying Unit')+' --',
                            id: 'fBuyingUnit',
                            name: 'fBuyingUnit',
                            xtype: 'combo',
                            width: 100,
                            labelWidth: 60,
                            multiSelect: true,
                            store: mc_BuyingUnit,
                            displayField: 'label',
                            valueField: 'id',
                            queryMode: 'local',
                            listeners: {
                                change: function (cb, nv, ov) {
                                    
                                }
                            }
                        }]
                    }, {
                        columnWidth: .14,
                        layout: 'form',
                        id: 'layoutFarmer',
                        hidden:true,
                        padding: 3,
                        border: false,
                        items: [{
                            emptyText: '-- '+ lang('All')+' '+lang('Farmer')+' --',
                            id: 'fFarmer',
                            name: 'fFarmer',
                            xtype: 'combo',
                            width: 100,
                            labelWidth: 60,
                            multiSelect: true,
                            store: mc_Farmer,
                            displayField: 'label',
                            valueField: 'id',
                            queryMode: 'local',
                            listeners: {
                                change: function (cb, nv, ov) {
                                    
                                }
                            }
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
                                Ext.getCmp('BuyingUnit').setValue('0');
                                var ware = Ext.getCmp('Warehouse').getValue();
                                if (ware == '' || ware == 'undefined' || ware == '%%' || ware == null) {
                                    Ext.Msg.alert('Warning', 'Silahkan pilih salah satu warehouse');
                                    return;
                                }
                                if (!Ext.getCmp('start').getValue() || !Ext.getCmp('end').getValue()) {
                                    Ext.Msg.alert('Warning', 'Silahkan tentukan rentang tanggal');
                                    return;    
                                };
                                //console.log(store_viewf)
                                //store_viewf.loadData([],false);
                                //store_viewf.load();

                                var jenisReport = Ext.getCmp('jenisReport').getValue();
                                if (jenisReport == '' || jenisReport == 'undefined' || jenisReport == '%%' || jenisReport == null) {
                                    Ext.Msg.alert('Warning', 'Silahkan pilih salah satu jenis report!');
                                    return;
                                }else{
                                    Ext.getCmp('grid_warehouse_sum').hide();  
                                    Ext.getCmp('grid_warehouse_trans').hide();
                                    Ext.getCmp('grid_coop_sum').hide();
                                    Ext.getCmp('grid_coop_trans').hide();
                                    Ext.getCmp('grid_bu_sum').hide();
                                    Ext.getCmp('grid_bu_trans').hide();
                                    Ext.getCmp('grid_farmer_sum').hide();
                                    Ext.getCmp('grid_farmer_trans').hide();
                                    //lanjut disini hide nya
                                    
                                    if (jenisReport == 'wh'){
                                        Ext.getCmp('grid_warehouse_sum').show();  
                                        Ext.getCmp('grid_warehouse_trans').show();
                                        store_vieww.load({
                                            params: {
                                                start: Ext.getCmp('start').getValue(),
                                                end: Ext.getCmp('end').getValue(),
                                                provinsi: Ext.getCmp('Provinsi').getValue(),
                                                warehouse: Ext.getCmp('Warehouse').getValue(),
                                                sert: Ext.getCmp('jenisSertifikasi').getValue()
                                            }
                                        });

                                        store_viewwtrans.load({
                                            params: {
                                                provinsi: Ext.getCmp('Provinsi').getValue(),
                                                warehouse: Ext.getCmp('Warehouse').getValue(),
                                                sert: Ext.getCmp('jenisSertifikasi').getValue()
                                            }
                                        });
                                    }else if (jenisReport == 'coop'){
                                        Ext.getCmp('grid_coop_sum').show();  
                                        Ext.getCmp('grid_coop_trans').show();
                                        if (Ext.getCmp('fKoperasi').getValue() == '') {
                                            //var combocoop = Ext.getCmp('fKoperasi');
                                            //combocoop.select(combocoop.getStore().getRange());
                                            //  combo.setSelectedCount(combo.getStore().getRange().length);
                                        }
                                        store_viewcoop.load({
                                            params: {
                                                start: Ext.getCmp('start').getValue(),
                                                end: Ext.getCmp('end').getValue(),
                                                provinsi: Ext.getCmp('Provinsi').getValue(),
                                                warehouse: Ext.getCmp('Warehouse').getValue(),
                                                sert: Ext.getCmp('jenisSertifikasi').getValue(),
                                                coop: Ext.getCmp('fKoperasi').getValue().join().replace(/,/g, '::')
                                            }
                                        });

                                        store_viewcooptrans.load({
                                            params: {
                                                provinsi: Ext.getCmp('Provinsi').getValue(),
                                                warehouse: Ext.getCmp('Warehouse').getValue(),
                                                sert: Ext.getCmp('jenisSertifikasi').getValue(),
                                                coop: Ext.getCmp('fKoperasi').getValue().join().replace(/,/g, '::')
                                            }
                                        });
                                    }else if (jenisReport == 'bu'){
                                        Ext.getCmp('grid_bu_sum').show();  
                                        Ext.getCmp('grid_bu_trans').show();
                                        
                                        store_viewbu.load({
                                            params: {
                                                start: Ext.getCmp('start').getValue(),
                                                end: Ext.getCmp('end').getValue(),
                                                provinsi: Ext.getCmp('Provinsi').getValue(),
                                                warehouse: Ext.getCmp('Warehouse').getValue(),
                                                sert: Ext.getCmp('jenisSertifikasi').getValue(),
                                                coop: Ext.getCmp('fKoperasi').getValue().join().replace(/,/g, '::'),
                                                bu: Ext.getCmp('fBuyingUnit').getValue().join().replace(/,/g, '::')
                                            }
                                        });

                                        store_viewbutrans.load({
                                            params: {
                                                provinsi: Ext.getCmp('Provinsi').getValue(),
                                                warehouse: Ext.getCmp('Warehouse').getValue(),
                                                sert: Ext.getCmp('jenisSertifikasi').getValue(),
                                                coop: Ext.getCmp('fKoperasi').getValue().join().replace(/,/g, '::'),
                                                bu: Ext.getCmp('fBuyingUnit').getValue().join().replace(/,/g, '::')
                                            }
                                        });
                                    }else if (jenisReport == 'farmer'){
                                        Ext.getCmp('grid_farmer_sum').show();  
                                        Ext.getCmp('grid_farmer_trans').show();
                                        
                                        store_viewfarmer.load({
                                            params: {
                                                start: Ext.getCmp('start').getValue(),
                                                end: Ext.getCmp('end').getValue(),
                                                provinsi: Ext.getCmp('Provinsi').getValue(),
                                                warehouse: Ext.getCmp('Warehouse').getValue(),
                                                sert: Ext.getCmp('jenisSertifikasi').getValue(),
                                                coop: Ext.getCmp('fKoperasi').getValue().join().replace(/,/g, '::'),
                                                bu: Ext.getCmp('fBuyingUnit').getValue().join().replace(/,/g, '::'),
                                                farmer: Ext.getCmp('fFarmer').getValue().join().replace(/,/g, '::')
                                            }
                                        });

                                        store_viewfarmertrans.load({
                                            params: {
                                                provinsi: Ext.getCmp('Provinsi').getValue(),
                                                warehouse: Ext.getCmp('Warehouse').getValue(),
                                                sert: Ext.getCmp('jenisSertifikasi').getValue(),
                                                coop: Ext.getCmp('fKoperasi').getValue().join().replace(/,/g, '::'),
                                                bu: Ext.getCmp('fBuyingUnit').getValue().join().replace(/,/g, '::'),
                                                farmer: Ext.getCmp('fFarmer').getValue().join().replace(/,/g, '::')
                                            }
                                        });
                                    }//lanjut disini shownya
                                }
                            }
                        }]
                    }]
                //**//
                }]
            }, {
                xtype: 'gridpanel',
                id: 'grid_warehouse_sum',
                padding: 6,
                width: '100%',
                border: true,
                title: lang('Summary Warehouse'),
                store: store_vieww,
                hidden: true,
                height: 160,
                columns: [{
                    header: lang('Warehouse Name'),
                    dataIndex: 'name',
                    width: '30%',
                    //flex: 1
                }, {
                    header: 'Survey Volume (Kg)',
                    dataIndex: 'survey',
                    align: 'right',
                    width: '10%',
                    renderer: Ext.util.Format.numberRenderer('0,000.00'),
                    //summaryType: 'sum'
                }, {
                    header: 'Quota (Survey + 10%)',
                    dataIndex: 'quota',
                    align: 'right',
                    width: '10%',
                    renderer: Ext.util.Format.numberRenderer('0,000.00'),
                    //summaryType: 'sum'
                }, {
                    header: lang('Transaction Count'),
                    dataIndex: 'transcount',
                    width: '10%',
                    align: 'right',
                    //renderer: Ext.util.Format.numberRenderer('0,000.00'),
                    //summaryType: 'sum'
                }, {
                    header: lang('Batch Count'),
                    dataIndex: 'batchcount',
                    width: '10%',
                    align: 'right',
                    //renderer: Ext.util.Format.numberRenderer('0,000.00'),
                    //summaryType: 'sum'
                }, {
                    header: lang('Coop Count'),
                    dataIndex: 'coopcount',
                    align: 'right',
                    width: '10%',
                    //renderer: Ext.util.Format.numberRenderer('0,000.00'),
                    //summaryType: 'sum'
                }, {
                    header: lang('Total Bruto'),
                    dataIndex: 'bruto',
                    align: 'right',
                    width: '10%',
                    renderer: Ext.util.Format.numberRenderer('0,000.00'),
                    //summaryType: 'sum'
                }, {
                    header: lang('Total Netto'),
                    dataIndex: 'netto',
                    align: 'right',
                    width: '10%',
                    renderer: Ext.util.Format.numberRenderer('0,000.00'),
                    //summaryType: 'sum'
                }],
                listeners: {
                    
                }
            }, {
                xtype: 'gridpanel',
                id: 'grid_warehouse_trans',
                padding: 6,
                width: '100%',
                hidden: true,
                border: true,
                title: lang('Warehouse Detail'),
                store: store_viewwtrans,
                height: 500,
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
                            url     = m_cetak + '?'
                                + 'start=' + formatDate(start)
                                + '&end=' + formatDate(end)
                                + '&prov=' + Ext.getCmp('Provinsi').getValue()
                                + '&wh=' + Ext.getCmp('Warehouse').getValue()
                                + '&whname=' + Ext.getCmp('Warehouse').getRawValue()
                                + '&coop=' + Ext.getCmp('fKoperasi').getValue().join().replace(/,/g, '::')
                                + '&bu=' + Ext.getCmp('fBuyingUnit').getValue().join().replace(/,/g, '::')
                                + '&farmer=' + Ext.getCmp('fFarmer').getValue().join().replace(/,/g, '::')
                                + '&jenisReport=' + Ext.getCmp('jenisReport').getValue()
                                + '&sert=' + Ext.getCmp('jenisSertifikasi').getValue();
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
                                    url     = m_cetak + '?'
                                        + 'start=' + formatDate(start)
                                        + '&end=' + formatDate(end)
                                        + '&prov=' + Ext.getCmp('Provinsi').getValue()
                                        + '&wh=' + Ext.getCmp('Warehouse').getValue()
                                        + '&whname=' + Ext.getCmp('Warehouse').getRawValue()
                                        + '&coop=' + Ext.getCmp('fKoperasi').getValue().join().replace(/,/g, '::')
                                        + '&bu=' + Ext.getCmp('fBuyingUnit').getValue().join().replace(/,/g, '::')
                                        + '&farmer=' + Ext.getCmp('fFarmer').getValue().join().replace(/,/g, '::')
                                        + '&jenisReport=' + Ext.getCmp('jenisReport').getValue()
                                        + '&sert=' + Ext.getCmp('jenisSertifikasi').getValue()
                                        + '&tipe=Excel';
                                    window.open(url, 'cetak', "height=200,width=200");
                                }
                            }]
                        }
                    }, {
                        xtype: 'label',
                        id: 'premiumWarehouse',
                        text: ''
                    }]
                }/*, {
                    xtype: 'pagingtoolbar',
                    store: store_viewwtrans,   // same store GridPanel is using
                    dock: 'bottom',
                    displayInfo: true
                }*/],
                columns: [{
                    header: lang('Transaction Date'),
                    dataIndex: 'datetransaction',
                    width: '20%',
                    summaryType: 'count',
                    summaryRenderer: function (value, summaryData, dataIndex) {
                        return ((value === 0 || value > 1) ? '(' + value + ' Transactions)' : '(1 Transaction)');
                    },
                    flex: 1
                }, {
                    header: lang('PO Number'),
                    dataIndex: 'po',
                    width: '25%',
                    flex: 1
                }, {
                    header: lang('Batch Number'),
                    dataIndex: 'batchnumber',
                    width: '25%',
                    flex: 1
                }, {
                    header: lang('Bruto')+' (Kg)',
                    dataIndex: 'bruto',
                    width: '15%',
                    align: 'right',
                    renderer: Ext.util.Format.numberRenderer('0,000.00'),
                    summaryType: 'sum'
                }, {
                    header: lang('Netto')+' (Kg)',
                    dataIndex: 'netto',
                    width: '15%',
                    align: 'right',
                    renderer: Ext.util.Format.numberRenderer('0,000.00'),
                    summaryType: 'sum'
                }],
                listeners: {
                    
                }
            }, {
                xtype: 'gridpanel',
                id: 'grid_coop_sum',
                padding: 6,
                width: '100%',
                border: true,
                title: lang('Summary Cooperative'),
                store: store_viewcoop,
                hidden: true,
                height: 350,
                features: [{
                    ftype: 'summary'
                }],
                columns: [{
                    header: lang('Cooperative Name'),
                    dataIndex: 'name',
                    width: '30%',
                    summaryType: 'count',
                    summaryRenderer: function (value, summaryData, dataIndex) {
                        return ((value === 0 || value > 1) ? '(' + value + ' Cooperatives)' : '(1 Cooperative)');
                    },
                    flex: 1
                }, {
                    header: 'Survey Volume (Kg)',
                    dataIndex: 'survey',
                    align: 'right',
                    width: '10%',
                    renderer: Ext.util.Format.numberRenderer('0,000.00'),
                    summaryType: 'sum'
                }, {
                    header: 'Quota (Survey + 10%)',
                    dataIndex: 'quota',
                    align: 'right',
                    width: '10%',
                    renderer: Ext.util.Format.numberRenderer('0,000.00'),
                    summaryType: 'sum'
                }, {
                    header: lang('Transaction Count'),
                    dataIndex: 'transcount',
                    width: '10%',
                    align: 'right',
                    //renderer: Ext.util.Format.numberRenderer('0,000.00'),
                    summaryType: 'sum'
                }, {
                    header: lang('Batch Count'),
                    dataIndex: 'batchcount',
                    width: '10%',
                    align: 'right',
                    //renderer: Ext.util.Format.numberRenderer('0,000.00'),
                    summaryType: 'sum'
                }, {
                    header: lang('Delivered Batch'),
                    dataIndex: 'deliveredcount',
                    align: 'right',
                    width: '10%',
                    //renderer: Ext.util.Format.numberRenderer('0,000.00'),
                    summaryType: 'sum'
                }, {
                    header: lang('Total Bruto'),
                    dataIndex: 'bruto',
                    align: 'right',
                    width: '10%',
                    renderer: Ext.util.Format.numberRenderer('0,000.00'),
                    summaryType: 'sum'
                }, {
                    header: lang('Total Netto'),
                    dataIndex: 'netto',
                    align: 'right',
                    width: '10%',
                    renderer: Ext.util.Format.numberRenderer('0,000.00'),
                    summaryType: 'sum'
                }],
                listeners: {
                    
                }
            }, {
                xtype: 'gridpanel',
                id: 'grid_coop_trans',
                padding: 6,
                width: '100%',
                hidden: true,
                border: true,
                title: lang('Cooperative Detail'),
                store: store_viewcooptrans,
                height: 500,
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
                            url     = m_cetak + '?'
                                + 'start=' + formatDate(start)
                                + '&end=' + formatDate(end)
                                + '&prov=' + Ext.getCmp('Provinsi').getValue()
                                + '&wh=' + Ext.getCmp('Warehouse').getValue()
                                + '&whname=' + Ext.getCmp('Warehouse').getRawValue()
                                + '&coop=' + Ext.getCmp('fKoperasi').getValue().join().replace(/,/g, '::')
                                + '&bu=' + Ext.getCmp('fBuyingUnit').getValue().join().replace(/,/g, '::')
                                + '&farmer=' + Ext.getCmp('fFarmer').getValue().join().replace(/,/g, '::')
                                + '&jenisReport=' + Ext.getCmp('jenisReport').getValue()
                                + '&sert=' + Ext.getCmp('jenisSertifikasi').getValue();
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
                                    url     = m_cetak + '?'
                                        + 'start=' + formatDate(start)
                                        + '&end=' + formatDate(end)
                                        + '&prov=' + Ext.getCmp('Provinsi').getValue()
                                        + '&wh=' + Ext.getCmp('Warehouse').getValue()
                                        + '&whname=' + Ext.getCmp('Warehouse').getRawValue()
                                        + '&coop=' + Ext.getCmp('fKoperasi').getValue().join().replace(/,/g, '::')
                                        + '&bu=' + Ext.getCmp('fBuyingUnit').getValue().join().replace(/,/g, '::')
                                        + '&farmer=' + Ext.getCmp('fFarmer').getValue().join().replace(/,/g, '::')
                                        + '&jenisReport=' + Ext.getCmp('jenisReport').getValue()
                                        + '&sert=' + Ext.getCmp('jenisSertifikasi').getValue()
                                        + '&tipe=Excel';
                                    window.open(url, 'cetak', "height=200,width=200");
                                }
                            }]
                        }
                    }, {
                        xtype: 'label',
                        id: 'premiumCoop',
                        text: ''
                    }]
                }],
                columns: [{
                    header: lang('Name'),
                    dataIndex: 'name',
                    width: '25%',
                    summaryType: 'count',
                    summaryRenderer: function (value, summaryData, dataIndex) {
                        return ((value === 0 || value > 1) ? '(' + value + ' Batchs)' : '(1 Batch)');
                    },
                    flex: 1
                }, {
                    header: lang('Transaction Date'),
                    dataIndex: 'datetransaction',
                    width: '20%',
                    flex: 1
                }, {
                    header: lang('PO Number'),
                    dataIndex: 'po',
                    width: '25%',
                    flex: 1
                }, {
                    header: lang('Batch Number'),
                    dataIndex: 'batchnumber',
                    width: '25%',
                    flex: 1
                }, {
                    header: lang('Batch Status'),
                    dataIndex: 'batchstatus',
                    width: '25%',
                    flex: 1
                }, {
                    header: lang('Destination'),
                    dataIndex: 'destination',
                    width: '25%',
                    flex: 1
                }, {
                    header: lang('Bruto')+' (Kg)',
                    dataIndex: 'bruto',
                    width: '15%',
                    align: 'right',
                    renderer: Ext.util.Format.numberRenderer('0,000.00'),
                    summaryType: 'sum'
                }, {
                    header: lang('Netto')+' (Kg)',
                    dataIndex: 'netto',
                    width: '15%',
                    align: 'right',
                    renderer: Ext.util.Format.numberRenderer('0,000.00'),
                    summaryType: 'sum'
                }],
                listeners: {
                    
                }
            }, {
                xtype: 'gridpanel',
                id: 'grid_bu_sum',
                padding: 6,
                width: '100%',
                border: true,
                title: lang('Summary Buying Unit'),
                store: store_viewbu,
                hidden: true,
                height: 350,
                features: [{
                    ftype: 'summary'
                }],
                columns: [{
                    header: lang('Buying Unit Name'),
                    dataIndex: 'name',
                    width: '30%',
                    summaryType: 'count',
                    summaryRenderer: function (value, summaryData, dataIndex) {
                        return ((value === 0 || value > 1) ? '(' + value + ' Buying Units)' : '(1 Buying Unit)');
                    },
                    flex: 1
                }, {
                    header: 'Survey Volume (Kg)',
                    dataIndex: 'survey',
                    align: 'right',
                    width: '10%',
                    renderer: Ext.util.Format.numberRenderer('0,000.00'),
                    summaryType: 'sum'
                }, {
                    header: 'Quota (Survey + 10%)',
                    dataIndex: 'quota',
                    align: 'right',
                    width: '10%',
                    renderer: Ext.util.Format.numberRenderer('0,000.00'),
                    summaryType: 'sum'
                }, {
                    header: lang('Transaction Count'),
                    dataIndex: 'transcount',
                    width: '10%',
                    align: 'right',
                    //renderer: Ext.util.Format.numberRenderer('0,000.00'),
                    summaryType: 'sum'
                }, {
                    header: lang('Batch Count'),
                    dataIndex: 'batchcount',
                    width: '10%',
                    align: 'right',
                    //renderer: Ext.util.Format.numberRenderer('0,000.00'),
                    summaryType: 'sum'
                }, {
                    header: lang('Delivered Batch'),
                    dataIndex: 'deliveredcount',
                    align: 'right',
                    width: '10%',
                    //renderer: Ext.util.Format.numberRenderer('0,000.00'),
                    summaryType: 'sum'
                }, {
                    header: lang('Total Bruto'),
                    dataIndex: 'bruto',
                    align: 'right',
                    width: '10%',
                    renderer: Ext.util.Format.numberRenderer('0,000.00'),
                    summaryType: 'sum'
                }, {
                    header: lang('Total Netto'),
                    dataIndex: 'netto',
                    align: 'right',
                    width: '10%',
                    renderer: Ext.util.Format.numberRenderer('0,000.00'),
                    summaryType: 'sum'
                }],
                listeners: {
                    
                }
            }, {
                xtype: 'gridpanel',
                id: 'grid_bu_trans',
                padding: 6,
                width: '100%',
                hidden: true,
                border: true,
                title: lang('Buying Unit Details'),
                store: store_viewbutrans,
                height: 500,
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
                            url     = m_cetak + '?'
                                + 'start=' + formatDate(start)
                                + '&end=' + formatDate(end)
                                + '&prov=' + Ext.getCmp('Provinsi').getValue()
                                + '&wh=' + Ext.getCmp('Warehouse').getValue()
                                + '&whname=' + Ext.getCmp('Warehouse').getRawValue()
                                + '&coop=' + Ext.getCmp('fKoperasi').getValue().join().replace(/,/g, '::')
                                + '&bu=' + Ext.getCmp('fBuyingUnit').getValue().join().replace(/,/g, '::')
                                + '&farmer=' + Ext.getCmp('fFarmer').getValue().join().replace(/,/g, '::')
                                + '&jenisReport=' + Ext.getCmp('jenisReport').getValue()
                                + '&sert=' + Ext.getCmp('jenisSertifikasi').getValue();
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
                                    url     = m_cetak + '?'
                                        + 'start=' + formatDate(start)
                                        + '&end=' + formatDate(end)
                                        + '&prov=' + Ext.getCmp('Provinsi').getValue()
                                        + '&wh=' + Ext.getCmp('Warehouse').getValue()
                                        + '&whname=' + Ext.getCmp('Warehouse').getRawValue()
                                        + '&coop=' + Ext.getCmp('fKoperasi').getValue().join().replace(/,/g, '::')
                                        + '&bu=' + Ext.getCmp('fBuyingUnit').getValue().join().replace(/,/g, '::')
                                        + '&farmer=' + Ext.getCmp('fFarmer').getValue().join().replace(/,/g, '::')
                                        + '&jenisReport=' + Ext.getCmp('jenisReport').getValue()
                                        + '&sert=' + Ext.getCmp('jenisSertifikasi').getValue()
                                        + '&tipe=Excel';
                                    window.open(url, 'cetak', "height=200,width=200");
                                }
                            }]
                        }
                    }, {
                        xtype: 'label',
                        id: 'premiumBu',
                        text: ''
                    }]
                }],
                columns: [{
                    header: lang('Name'),
                    dataIndex: 'name',
                    width: '25%',
                    summaryType: 'count',
                    summaryRenderer: function (value, summaryData, dataIndex) {
                        return ((value === 0 || value > 1) ? '(' + value + ' Batchs)' : '(1 Batch)');
                    },
                    flex: 1
                }, {
                    header: lang('Transaction Date'),
                    dataIndex: 'datetransaction',
                    width: '20%',
                    flex: 1
                }, {
                    header: lang('PO Number'),
                    dataIndex: 'po',
                    width: '25%',
                    flex: 1
                }, {
                    header: lang('Batch Number'),
                    dataIndex: 'batchnumber',
                    width: '25%',
                    flex: 1
                }, {
                    header: lang('Batch Status'),
                    dataIndex: 'batchstatus',
                    width: '25%',
                    flex: 1
                }, {
                    header: lang('Destination'),
                    dataIndex: 'destination',
                    width: '25%',
                    flex: 1
                }, {
                    header: lang('Bruto')+' (Kg)',
                    dataIndex: 'bruto',
                    width: '15%',
                    align: 'right',
                    renderer: Ext.util.Format.numberRenderer('0,000.00'),
                    summaryType: 'sum'
                }, {
                    header: lang('Netto')+' (Kg)',
                    dataIndex: 'netto',
                    width: '15%',
                    align: 'right',
                    renderer: Ext.util.Format.numberRenderer('0,000.00'),
                    summaryType: 'sum'
                }],
                listeners: {
                    
                }
            }
            //**//
            , {
                xtype: 'gridpanel',
                id: 'grid_farmer_sum',
                padding: 6,
                width: '100%',
                border: true,
                title: lang('Summary Farmer'),
                store: store_viewfarmer,
                hidden: true,
                height: 350,
                features: [{
                    ftype: 'summary'
                }],
                columns: [{
                    header: lang('Name'),
                    dataIndex: 'name',
                    width: '30%',
                    summaryType: 'count',
                    summaryRenderer: function (value, summaryData, dataIndex) {
                        return ((value === 0 || value > 1) ? '(' + value + ' Farmers)' : '(1 Farmer)');
                    },
                    flex: 1
                }, {
                    header: lang('Farmer Group'),
                    dataIndex: 'cpg',
                    width: '20%',
                    flex: 1
                }, {
                    header: lang('Village'),
                    dataIndex: 'village',
                    width: '20%',
                    flex: 1
                }, {
                    header: 'Survey Volume (Kg)',
                    dataIndex: 'survey',
                    align: 'right',
                    width: '10%',
                    renderer: Ext.util.Format.numberRenderer('0,000.00'),
                    summaryType: 'sum'
                }, {
                    header: 'Quota (Survey + 10%)',
                    dataIndex: 'quota',
                    align: 'right',
                    width: '10%',
                    renderer: Ext.util.Format.numberRenderer('0,000.00'),
                    summaryType: 'sum'
                }, {
                    header: lang('Transaction Count'),
                    dataIndex: 'transcount',
                    width: '10%',
                    align: 'right',
                    //renderer: Ext.util.Format.numberRenderer('0,000.00'),
                    summaryType: 'sum'
                }, {
                    header: lang('Delivered Count'),
                    dataIndex: 'deliveredcount',
                    align: 'right',
                    width: '10%',
                    //renderer: Ext.util.Format.numberRenderer('0,000.00'),
                    summaryType: 'sum'
                }, {
                    header: lang('Total Bruto'),
                    dataIndex: 'bruto',
                    align: 'right',
                    width: '10%',
                    renderer: Ext.util.Format.numberRenderer('0,000.00'),
                    summaryType: 'sum'
                }, {
                    header: lang('Total Netto'),
                    dataIndex: 'netto',
                    align: 'right',
                    width: '10%',
                    renderer: Ext.util.Format.numberRenderer('0,000.00'),
                    summaryType: 'sum'
                }],
                listeners: {
                    
                }
            }, {
                xtype: 'gridpanel',
                id: 'grid_farmer_trans',
                padding: 6,
                width: '100%',
                hidden: true,
                border: true,
                title: lang('Farmer Detail'),
                store: store_viewfarmertrans,
                height: 500,
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
                            url     = m_cetak + '?'
                                + 'start=' + formatDate(start)
                                + '&end=' + formatDate(end)
                                + '&prov=' + Ext.getCmp('Provinsi').getValue()
                                + '&wh=' + Ext.getCmp('Warehouse').getValue()
                                + '&whname=' + Ext.getCmp('Warehouse').getRawValue()
                                + '&coop=' + Ext.getCmp('fKoperasi').getValue().join().replace(/,/g, '::')
                                + '&bu=' + Ext.getCmp('fBuyingUnit').getValue().join().replace(/,/g, '::')
                                + '&farmer=' + Ext.getCmp('fFarmer').getValue().join().replace(/,/g, '::')
                                + '&jenisReport=' + Ext.getCmp('jenisReport').getValue()
                                + '&sert=' + Ext.getCmp('jenisSertifikasi').getValue();
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
                                    url     = m_cetak + '?'
                                        + 'start=' + formatDate(start)
                                        + '&end=' + formatDate(end)
                                        + '&prov=' + Ext.getCmp('Provinsi').getValue()
                                        + '&wh=' + Ext.getCmp('Warehouse').getValue()
                                        + '&whname=' + Ext.getCmp('Warehouse').getRawValue()
                                        + '&coop=' + Ext.getCmp('fKoperasi').getValue().join().replace(/,/g, '::')
                                        + '&bu=' + Ext.getCmp('fBuyingUnit').getValue().join().replace(/,/g, '::')
                                        + '&farmer=' + Ext.getCmp('fFarmer').getValue().join().replace(/,/g, '::')
                                        + '&jenisReport=' + Ext.getCmp('jenisReport').getValue()
                                        + '&sert=' + Ext.getCmp('jenisSertifikasi').getValue()
                                        + '&tipe=Excel';
                                    window.open(url, 'cetak', "height=200,width=200");
                                }
                            }]
                        }
                    }, {
                        xtype: 'label',
                        id: 'premiumFarmer',
                        text: ''
                    }]
                }],
                columns: [{
                    header: lang('Name'),
                    dataIndex: 'name',
                    width: '25%',
                    summaryType: 'count',
                    summaryRenderer: function (value, summaryData, dataIndex) {
                        return ((value === 0 || value > 1) ? '(' + value + ' Transactions)' : '(1 Transaction)');
                    },
                    flex: 1
                }, {
                    header: lang('Farmer Group'),
                    dataIndex: 'cpg',
                    width: '20%',
                    flex: 1
                }, {
                    header: lang('Village'),
                    dataIndex: 'village',
                    width: '20%',
                    flex: 1
                }, {
                    header: lang('Transaction Date'),
                    dataIndex: 'datetransaction',
                    width: '20%',
                    flex: 1
                }, {
                    header: lang('Transaction Number'),
                    dataIndex: 'transactionnumber',
                    width: '20%',
                    flex: 1
                }, {
                    header: lang('PO Number'),
                    dataIndex: 'po',
                    width: '25%',
                    flex: 1
                }, {
                    header: lang('Batch Number'),
                    dataIndex: 'batchnumber',
                    width: '25%',
                    flex: 1
                }, {
                    header: lang('Batch Status'),
                    dataIndex: 'batchstatus',
                    width: '25%',
                    flex: 1
                }, {
                    header: lang('Destination'),
                    dataIndex: 'destination',
                    width: '25%',
                    flex: 1
                }, {
                    header: lang('Bruto')+' (Kg)',
                    dataIndex: 'bruto',
                    width: '15%',
                    align: 'right',
                    renderer: Ext.util.Format.numberRenderer('0,000.00'),
                    summaryType: 'sum'
                }, {
                    header: lang('Netto')+' (Kg)',
                    dataIndex: 'netto',
                    width: '15%',
                    align: 'right',
                    renderer: Ext.util.Format.numberRenderer('0,000.00'),
                    summaryType: 'sum'
                }],
                listeners: {
                    
                }
            }
            //**//  
            , {
                xtype: 'gridpanel',
                id: 'grid_koperasi',
                hidden:true,
                padding: 6,
                width: '100%',
                border: true,
                title: lang('Koperasi'),
                store: store_viewc,
                height: 300,
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
                                    url     = m_cetak + '?'
                                        + 'start=' + formatDate(start)
                                        + '&end=' + formatDate(end)
                                        + '&prov=' + Ext.getCmp('Provinsi').getValue()
                                        + '&wh=' + Ext.getCmp('Warehouse').getValue()
                                        + '&whname=' + Ext.getCmp('Warehouse').getRawValue()
                                        + '&coop=' + Ext.getCmp('fKoperasi').getValue().join().replace(/,/g, '::')
                                        + '&bu=' + Ext.getCmp('fBuyingUnit').getValue().join().replace(/,/g, '::')
                                        + '&farmer=' + Ext.getCmp('fFarmer').getValue().join().replace(/,/g, '::')
                                        + '&jenisReport=' + Ext.getCmp('jenisReport').getValue()
                                        + '&sert=' + Ext.getCmp('jenisSertifikasi').getValue()
                                        + '&tipe=Excel';
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
                    summaryType: 'sum'
                }, {
                    header: 'Quota (Survey + 10%)',
                    dataIndex: 'quota',
                    align: 'right',
                    width: 150,
                    renderer: Ext.util.Format.numberRenderer('0,000.00'),
                    summaryType: 'sum'
                }, {
                    header: lang('Bruto')+' (Kg)',
                    dataIndex: 'bruto',
                    width: 100,
                    align: 'right',
                    renderer: Ext.util.Format.numberRenderer('0,000.00'),
                    summaryType: 'sum'
                }, {
                    header: lang('Netto')+' (Kg)',
                    dataIndex: 'netto',
                    width: 100,
                    align: 'right',
                    renderer: Ext.util.Format.numberRenderer('0,000.00'),
                    summaryType: 'sum'
                }, {
                    header: 'Balance (Kg)',
                    dataIndex: 'balance',
                    align: 'right',
                    width: 100,
                    renderer: Ext.util.Format.numberRenderer('0,000.00'),
                    summaryType: 'sum'
                }, {
                    header: 'TOTAL (IDR)',
                    dataIndex: 'totalidr',
                    id: 'koperasiTotalIDR',
                    hidden: true,
                    width: 100,
                    align: 'right',
                    renderer: Ext.util.Format.numberRenderer('0,000.00'),
                    summaryType: 'sum'
                }, {
                    header: 'TOTAL (USD)',
                    dataIndex: 'totalusd',
                    id: 'koperasiTotalUSD',
                    hidden: true,
                    width: 100,
                    align: 'right',
                    renderer: Ext.util.Format.numberRenderer('0,000.00'),
                    summaryType: 'sum'
                }, {
                    header: 'PAID (Kg)',
                    dataIndex: 'paidkg',
                    id: 'koperasiPaidKg',
                    hidden: true,
                    width: 100,
                    align: 'right',
                    renderer: Ext.util.Format.numberRenderer('0,000.00'),
                    summaryType: 'sum'
                }, {
                    header: 'UNPAID (Kg)',
                    dataIndex: 'unpaidkg',
                    id: 'koperasiUnpaidKg',
                    hidden: true,
                    width: 100,
                    align: 'right',
                    renderer: Ext.util.Format.numberRenderer('0,000.00'),
                    summaryType: 'sum'
                },{
                    header: 'PAID (USD)',
                    dataIndex: 'paidusd',
                    id: 'koperasiPaidUSD',
                    hidden: true,
                    width: 100,
                    align: 'right',
                    renderer: Ext.util.Format.numberRenderer('0,000.00'),
                    summaryType: 'sum'
                }, {
                    header: 'UNPAID (USD)',
                    dataIndex: 'unpaidusd',
                    id: 'koperasiUnpaidUSD',
                    hidden: true,
                    width: 100,
                    align: 'right',
                    renderer: Ext.util.Format.numberRenderer('0,000.00'),
                    summaryType: 'sum'
                }],
                listeners: {
                    select: function (selModel, record, index, options) {
                        Ext.getCmp('BuyingUnit').setValue('0');
                        store_view.load({
                           params: {
                               start: Ext.getCmp('start').getValue(),
                               end: Ext.getCmp('end').getValue(),
                               // jenis: Ext.getCmp('JenisBuyingUnit').getValue(),
                               jenis: 'Farmer',
                               provinsi: Ext.getCmp('Provinsi').getValue(),
                               warehouse: Ext.getCmp('Warehouse').getValue(),
                               sert: Ext.getCmp('jenisSertifikasi').getValue(),
                               coopID: record.get('orgid')
                           }
                           ,callback: function(records, operation, success) {
                               var type = [];
                               $.each(records, function(index, val) {
                                   if (type.indexOf(lang(val.data.orgtype)) == -1) {
                                       type.push(lang(val.data.orgtype));
                                   }
                               });
                               //Ext.getCmp('grid-bu-premium').setTitle(type.join(', ').charAt(0).toUpperCase());
                           }
                       });
                        Ext.getCmp('defCoop').setValue(record.get('orgid'));
                        Ext.getCmp('defBU').setValue(0);
                        
                        store_viewf.load({
                            params: {
                                buid: 0,
                                coopID: record.get('orgid')
                            }
                        });

                        store_viewftot.load({
                            params: {
                                buid: 0,
                                coopID: record.get('orgid')
                            }
                        });
                    }
                }
            }, {
                xtype: 'gridpanel',
                padding: 6,
                title: 'Buying Unit Details',
                id: 'grid-bu-premium',
                width: '100%',
                hidden: true,
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
                                    url     = m_cetak + '?'
                                        + 'start=' + formatDate(start)
                                        + '&end=' + formatDate(end)
                                        + '&prov=' + Ext.getCmp('Provinsi').getValue()
                                        + '&wh=' + Ext.getCmp('Warehouse').getValue()
                                        + '&whname=' + Ext.getCmp('Warehouse').getRawValue()
                                        + '&coop=' + Ext.getCmp('fKoperasi').getValue().join().replace(/,/g, '::')
                                        + '&bu=' + Ext.getCmp('fBuyingUnit').getValue().join().replace(/,/g, '::')
                                        + '&farmer=' + Ext.getCmp('fFarmer').getValue().join().replace(/,/g, '::')
                                        + '&jenisReport=' + Ext.getCmp('jenisReport').getValue()
                                        + '&sert=' + Ext.getCmp('jenisSertifikasi').getValue()
                                        + '&tipe=Excel';
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
                }, {
                    header: 'Survey Volume (Kg)',
                    dataIndex: 'survey',
                    align: 'right',
                    width: 130,
                    renderer: Ext.util.Format.numberRenderer('0,000.00'),
                    summaryType: 'sum'
                }, {
                    header: 'Quota (Survey + 10%)',
                    dataIndex: 'quota',
                    align: 'right',
                    width: 150,
                    renderer: Ext.util.Format.numberRenderer('0,000.00'),
                    summaryType: 'sum'
                }, {
                    header: lang('Bruto')+' (Kg)',
                    dataIndex: 'bruto',
                    align: 'right',
                    width: 100,
                    renderer: Ext.util.Format.numberRenderer('0,000.00'),
                    summaryType: 'sum'
                }, {
                    header: lang('Netto')+' (Kg)',
                    dataIndex: 'netto',
                    align: 'right',
                    width: 100,
                    renderer: Ext.util.Format.numberRenderer('0,000.00'),
                    summaryType: 'sum'
                }, {
                    header: 'Balance (Kg)',
                    dataIndex: 'balance',
                    align: 'right',
                    width: 100,
                    renderer: Ext.util.Format.numberRenderer('0,000.00'),
                    summaryType: 'sum'
                }, {
                    header: 'TOTAL (IDR)',
                    dataIndex: 'totalidr',
                    id: 'buTotalIDR',
                    hidden: true,
                    align: 'right',
                    width: 100,
                    renderer: Ext.util.Format.numberRenderer('0,000.00'),
                    summaryType: 'sum'
                }, {
                    header: 'TOTAL (USD)',
                    dataIndex: 'totalusd',
                    id: 'buTotalUSD',
                    hidden: true,
                    align: 'right',
                    width: 100,
                    renderer: Ext.util.Format.numberRenderer('0,000.00'),
                    summaryType: 'sum'
                }, {
                    header: 'PAID (Kg)',
                    dataIndex: 'paidkg',
                    id: 'buPaidKg',
                    hidden: true,
                    align: 'right',
                    width: 100,
                    renderer: Ext.util.Format.numberRenderer('0,000.00'),
                    summaryType: 'sum'
                }, {
                    header: 'UNPAID (Kg)',
                    dataIndex: 'unpaidkg',
                    id: 'buUnpaidKg',
                    hidden: true,
                    align: 'right',
                    width: 100,
                    renderer: Ext.util.Format.numberRenderer('0,000.00'),
                    summaryType: 'sum'
                }, {
                    header: 'PAID (USD)',
                    dataIndex: 'paidusd',
                    id: 'buPaidUSD',
                    hidden: true,
                    align: 'right',
                    width: 100,
                    renderer: Ext.util.Format.numberRenderer('0,000.00'),
                    summaryType: 'sum'
                }, {
                    header: 'UNPAID (USD)',
                    dataIndex: 'unpaidusd',
                    id: 'buUnpaidUSD',
                    hidden: true,
                    align: 'right',
                    width: 100,
                    renderer: Ext.util.Format.numberRenderer('0,000.00'),
                    summaryType: 'sum'
                }],
                listeners: {
                    select: function (selModel, record, index, options) {
                        // console.log(record);
                        //Ext.Msg.alert("",record.get('PerwakilanOrgId'));
                        Ext.getCmp('defBU').setValue(record.get('orgid'));
                        Ext.getCmp('defCoop').setValue('');
                        Ext.getCmp('BuyingUnit').setValue(record.get('orgid'));
                        store_viewf.load({
                            params: {
                                buid: record.get('orgid')
                            }
                        });
                        store_viewftot.load({
                            params: {
                                buid: record.get('orgid')
                            }
                        });
                    }
                }
            }, {
                xtype: 'gridpanel',
                padding: 6,
                id:'grid-farmer-premium',
                width: '100%',
                hidden:true,
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
                    //remoteRoot: 'data',
                    enableGroupingMenu: false
                }],
                dockedItems: [{
                    xtype: 'toolbar',
                    dock: 'top',
                    items: [{
                            xtype: 'fieldcontainer',
                            defaultType: 'checkboxfield',
                            items: [{
                                boxLabel: lang('All'),
                                name: 'allTransaction',
                                inputValue: 'all',
                                id: 'allTransaction',
                                listeners: {

                                }
                            }]
                        }, {
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
                                        + '/' + Ext.getCmp('BuyingUnit').getValue()
                                        + '/' + Ext.getCmp('jenisSertifikasi').getValue()
                                        + '/' + Ext.getCmp('allTransaction').getValue();
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
                                        + '/' + Ext.getCmp('BuyingUnit').getValue()
                                        + '/' + Ext.getCmp('jenisSertifikasi').getValue()
                                        + '/' + Ext.getCmp('allTransaction').getValue();
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
                                        + '/' + Ext.getCmp('BuyingUnit').getValue()
                                        + '/' + Ext.getCmp('jenisSertifikasi').getValue()
                                        + '/' + Ext.getCmp('allTransaction').getValue();
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
                                        + '/' + Ext.getCmp('BuyingUnit').getValue()
                                        + '/' + Ext.getCmp('jenisSertifikasi').getValue()
                                        + '/' + Ext.getCmp('allTransaction').getValue();
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
                                        url     = m_cetak + '?'
                                            + 'start=' + formatDate(start)
                                            + '&end=' + formatDate(end)
                                            + '&prov=' + Ext.getCmp('Provinsi').getValue()
                                            + '&wh=' + Ext.getCmp('Warehouse').getValue()
                                            + '&whname=' + Ext.getCmp('Warehouse').getRawValue()
                                            + '&coop=' + Ext.getCmp('fKoperasi').getValue().join().replace(/,/g, '::')
                                            + '&bu=' + Ext.getCmp('fBuyingUnit').getValue().join().replace(/,/g, '::')
                                            + '&farmer=' + Ext.getCmp('fFarmer').getValue().join().replace(/,/g, '::')
                                            + '&jenisReport=' + Ext.getCmp('jenisReport').getValue()
                                            + '&sert=' + Ext.getCmp('jenisSertifikasi').getValue()
                                            + '&tipe=Excel';
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
                                                + '/' + Ext.getCmp('BuyingUnit').getValue()
                                                + '/' + Ext.getCmp('jenisSertifikasi').getValue()
                                                + '/' + Ext.getCmp('allTransaction').getValue()
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
                                                + '/' + Ext.getCmp('BuyingUnit').getValue()
                                                + '/' + Ext.getCmp('jenisSertifikasi').getValue()
                                                + '/' + Ext.getCmp('allTransaction').getValue()
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
                                                + '/' + Ext.getCmp('BuyingUnit').getValue()
                                                + '/' + Ext.getCmp('jenisSertifikasi').getValue()
                                                + '/' + Ext.getCmp('allTransaction').getValue()
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
                    header: 'Quota (Survey + 10%)',
                    dataIndex: 'quota',
                    align: 'right',
                    width: 150,
                    renderer: Ext.util.Format.numberRenderer('0,000.00'),
                    summaryType: 'max',
                }, {
                    header: lang('Bruto')+' (Kg)',
                    dataIndex: 'bruto',
                    align: 'right',
                    width: 100,
                    renderer: Ext.util.Format.numberRenderer('0,000.00'),
                    summaryType: 'sum'
                }, {
                    header: lang('Netto')+' (Kg)',
                    text: 'netto',
                    dataIndex: 'netto',
                    align: 'right',
                    width: 100,
                    renderer: Ext.util.Format.numberRenderer('0,000.00'),
                    summaryType: 'sum'
                }, {
                    header: 'Balance (Kg)',
                    dataIndex: 'balance',
                    align: 'right',
                    width: 100,
                    renderer: Ext.util.Format.numberRenderer('0,000.00'),
                    summaryType: 'min'
                }, {
                    header: 'TOTAL (IDR)',
                    dataIndex: 'totalidr',
                    id: 'farmerTotalIDR',
                    hidden: true,
                    align: 'right',
                    width: 100,
                    renderer: Ext.util.Format.numberRenderer('0,000.00'),
                    summaryType: 'sum'
                }, {
                    header: 'TOTAL (USD)',
                    dataIndex: 'totalusd',
                    id: 'farmerTotalUSD',
                    hidden: true,
                    align: 'right',
                    width: 100,
                    renderer: Ext.util.Format.numberRenderer('0,000.00'),
                    summaryType: 'sum'
                }, {
                    header: 'PAID (Kg)',
                    dataIndex: 'paidkg',
                    id: 'farmerPaidKg',
                    hidden: true,
                    align: 'right',
                    width: 100,
                    renderer: Ext.util.Format.numberRenderer('0,000.00'),
                    summaryType: 'sum'
                }, {
                    header: 'UNPAID (Kg)',
                    dataIndex: 'unpaidkg',
                    id: 'farmerUnpaidKg',
                    hidden: true,
                    align: 'right',
                    width: 100,
                    renderer: Ext.util.Format.numberRenderer('0,000.00'),
                    summaryType: 'sum'
                }, {
                    header: 'PAID (USD)',
                    dataIndex: 'paidusd',
                    id: 'farmerPaidUSD',
                    hidden: true,
                    align: 'right',
                    width: 100,
                    renderer: Ext.util.Format.numberRenderer('0,000.00'),
                    summaryType: 'sum'
                }, {
                    header: 'UNPAID (USD)',
                    dataIndex: 'unpaidusd',
                    id: 'farmerUnpaidUSD',
                    hidden: true,
                    align: 'right',
                    width: 100,
                    renderer: Ext.util.Format.numberRenderer('0,000.00'),
                    summaryType: 'sum'
                }],
            },{
                xtype:'gridpanel',
                hideHeaders:true,
                hidden:true,
                height:65,
                store: store_viewftot,
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
                    header: 'Quota (Survey + 10%)',
                    dataIndex: 'quota',
                    align: 'right',
                    width: 150,
                    renderer: Ext.util.Format.numberRenderer('0,000.00'),
                    summaryType: 'max',
                }, {
                    header: lang('Bruto')+' (Kg)',
                    dataIndex: 'bruto',
                    align: 'right',
                    width: 100,
                    renderer: Ext.util.Format.numberRenderer('0,000.00'),
                    summaryType: 'sum'
                }, {
                    header: lang('Netto')+' (Kg)',
                    text: 'netto',
                    dataIndex: 'netto',
                    align: 'right',
                    width: 100,
                    renderer: Ext.util.Format.numberRenderer('0,000.00'),
                    summaryType: 'sum'
                }, {
                    header: 'Balance (Kg)',
                    dataIndex: 'balance',
                    align: 'right',
                    width: 100,
                    value: '',
                    //renderer: Ext.util.Format.numberRenderer('0,000.00'),
                    //summaryType: 'min'
                }, {
                    header: 'TOTAL (IDR)',
                    dataIndex: 'totalidr',
                    id: 'farmerTotalIDRtot',
                    hidden: true,
                    align: 'right',
                    width: 100,
                    renderer: Ext.util.Format.numberRenderer('0,000.00'),
                    summaryType: 'sum'
                }, {
                    header: 'TOTAL (USD)',
                    dataIndex: 'totalusd',
                    id: 'farmerTotalUSDtot',
                    hidden: true,
                    align: 'right',
                    width: 100,
                    renderer: Ext.util.Format.numberRenderer('0,000.00'),
                    summaryType: 'sum'
                }, {
                    header: 'PAID (Kg)',
                    dataIndex: 'paidkg',
                    id: 'farmerPaidKgtot',
                    hidden: true,
                    align: 'right',
                    width: 100,
                    renderer: Ext.util.Format.numberRenderer('0,000.00'),
                    summaryType: 'sum'
                }, {
                    header: 'UNPAID (Kg)',
                    dataIndex: 'unpaidkg',
                    id: 'farmerUnpaidKgtot',
                    hidden: true,
                    align: 'right',
                    width: 100,
                    renderer: Ext.util.Format.numberRenderer('0,000.00'),
                    summaryType: 'sum'
                }, {
                    header: 'PAID (USD)',
                    dataIndex: 'paidusd',
                    id: 'farmerPaidUSDtot',
                    hidden: true,
                    align: 'right',
                    width: 100,
                    renderer: Ext.util.Format.numberRenderer('0,000.00'),
                    summaryType: 'sum'
                }, {
                    header: 'UNPAID (USD)',
                    dataIndex: 'unpaidusd',
                    id: 'farmerUnpaidUSDtot',
                    hidden: true,
                    align: 'right',
                    width: 100,
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
        Ext.getCmp('koperasiPaidKg').show();
        Ext.getCmp('koperasiUnpaidKg').show();
        Ext.getCmp('koperasiPaidUSD').show();
        Ext.getCmp('koperasiUnpaidUSD').show();
        
        Ext.getCmp('buTotalIDR').show();
        Ext.getCmp('buTotalUSD').show();
        Ext.getCmp('buPaidKg').show();
        Ext.getCmp('buUnpaidKg').show();
        Ext.getCmp('buPaidUSD').show();
        Ext.getCmp('buUnpaidUSD').show();

        Ext.getCmp('farmerTotalIDR').show();
        Ext.getCmp('farmerTotalUSD').show();
        Ext.getCmp('farmerPaidUSD').show();
        Ext.getCmp('farmerUnpaidUSD').show();
        Ext.getCmp('farmerPaidKg').show();
        Ext.getCmp('farmerUnpaidKg').show();

        Ext.getCmp('farmerTotalIDRtot').show();
        Ext.getCmp('farmerTotalUSDtot').show();
        Ext.getCmp('farmerPaidUSDtot').show();
        Ext.getCmp('farmerUnpaidUSDtot').show();
        Ext.getCmp('farmerPaidKgtot').show();
        Ext.getCmp('farmerUnpaidKgtot').show();


    } else {
        Ext.getCmp('koperasiTotalIDR').hide();
        Ext.getCmp('koperasiTotalUSD').hide();
        Ext.getCmp('koperasiPaidUSD').hide();
        Ext.getCmp('koperasiUnpaidUSD').hide();
        Ext.getCmp('koperasiPaidKg').hide();
        Ext.getCmp('koperasiUnpaidKg').hide();
        
        Ext.getCmp('buTotalIDR').hide();
        Ext.getCmp('buTotalUSD').hide();
        Ext.getCmp('buPaidKg').hide();
        Ext.getCmp('buUnpaidKg').hide();
        Ext.getCmp('buPaidUSD').hide();
        Ext.getCmp('buUnpaidUSD').hide();

        Ext.getCmp('farmerTotalIDR').hide();
        Ext.getCmp('farmerTotalUSD').hide();
        Ext.getCmp('farmerPaidUSD').hide();
        Ext.getCmp('farmerUnpaidUSD').hide();
        Ext.getCmp('farmerPaidKg').hide();
        Ext.getCmp('farmerUnpaidkg').hide();

        Ext.getCmp('farmerTotalIDRtot').hide();
        Ext.getCmp('farmerTotalUSDtot').hide();
        Ext.getCmp('farmerPaidUSDtot').hide();
        Ext.getCmp('farmerUnpaidUSDtot').hide();
        Ext.getCmp('farmerPaidKgtot').hide();
        Ext.getCmp('farmerUnpaidKgtot').hide();
    }
}
function with_premium(set) {
    if (set) {
        Ext.getCmp('koperasiTotalIDR').show();
        Ext.getCmp('koperasiTotalUSD').show();
        Ext.getCmp('koperasiPaidKg').show();
        Ext.getCmp('koperasiUnpaidKg').show();
        Ext.getCmp('koperasiPaidUSD').show();
        Ext.getCmp('koperasiUnpaidUSD').show();

        Ext.getCmp('buTotalIDR').show();
        Ext.getCmp('buTotalUSD').show();
        Ext.getCmp('buPaidUSD').show();
        Ext.getCmp('buUnpaidUSD').show();
        Ext.getCmp('buPaidKg').show();
        Ext.getCmp('buUnpaidKg').show();
        
        Ext.getCmp('farmerTotalIDR').show();
        Ext.getCmp('farmerTotalUSD').show();
        Ext.getCmp('farmerPaidUSD').show();
        Ext.getCmp('farmerUnpaidUSD').show();
        Ext.getCmp('farmerPaidKg').show();
        Ext.getCmp('farmerUnpaidKg').show();

        Ext.getCmp('farmerTotalIDRtot').show();
        Ext.getCmp('farmerTotalUSDtot').show();
        Ext.getCmp('farmerPaidUSDtot').show();
        Ext.getCmp('farmerUnpaidUSDtot').show();
        Ext.getCmp('farmerPaidKgtot').show();
        Ext.getCmp('farmerUnpaidKgtot').show();
    } else {
        Ext.getCmp('koperasiTotalIDR').hide();
        Ext.getCmp('koperasiTotalUSD').hide();
        Ext.getCmp('koperasiPaidKg').hide();
        Ext.getCmp('koperasiUnpaidKg').hide();
        Ext.getCmp('koperasiPaidUSD').hide();
        Ext.getCmp('koperasiUnpaidUSD').hide();

        Ext.getCmp('buTotalIDR').hide();
        Ext.getCmp('buTotalUSD').hide();
        Ext.getCmp('buPaidKg').hide();
        Ext.getCmp('buUnpaidkg').hide();
        Ext.getCmp('buPaidUSD').hide();
        Ext.getCmp('buUnpaidUSD').hide();

        Ext.getCmp('farmerTotalIDR').hide();
        Ext.getCmp('farmerTotalUSD').hide();
        Ext.getCmp('farmerPaidKg').hide();
        Ext.getCmp('farmerUnpaidKg').hide();
        Ext.getCmp('farmerPaidUSD').hide();
        Ext.getCmp('farmerUnpaidUSD').hide();

        Ext.getCmp('farmerTotalIDRtot').hide();
        Ext.getCmp('farmerTotalUSDtot').hide();
        Ext.getCmp('farmerPaidKgtot').hide();
        Ext.getCmp('farmerUnpaidKgtot').hide();
        Ext.getCmp('farmerPaidUSDtot').hide();
        Ext.getCmp('farmerUnpaidUSDtot').hide();
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

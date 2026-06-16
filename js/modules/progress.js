var winPreview;
var previewForm;

//override time out ajax exts js yg cuman 30 detikan
Ext.Ajax.timeout = 120000;
Ext.override(Ext.form.Basic, {
    timeout: Ext.Ajax.timeout / 1000
});
Ext.override(Ext.data.proxy.Server, {
    timeout: Ext.Ajax.timeout
});
Ext.override(Ext.data.Connection, {
    timeout: Ext.Ajax.timeout
});

Ext.onReady(function () {
    var store, grid, height;
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
    var mc_dProvinsi = Ext.create('Ext.data.Store', {
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
    var mc_dKabupaten = Ext.create('Ext.data.Store', {
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
    var mc_cpg = Ext.create('Ext.data.Store', {
        extend: 'Ext.data.Model',
        fields: ['id', 'label'],
        autoLoad: false,
        pageSize: 10,
        proxy: {
            type: 'ajax',
            url: m_Cpg,
            reader: {
                type: 'json',
                root: 'data'
            }
        }
    });
    var mc_batch = Ext.create('Ext.data.Store', {
        extend: 'Ext.data.Model',
        fields: ['id', 'label'],
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
    var mc_jenis = Ext.create('Ext.data.Store', {
        fields: ['label'],
        data: [
            {"label": "Jaman"},
            {"label": "Harian"},
            {"label": "Mingguan"},
            {"label": "Bulanan"},
            {"label": "Tahunan"}
        ]
    });
    Ext.define('detail.Model', {
        extend: 'Ext.data.Model',
        fields: ['FarmerID','FarmerName','Village','SurveyNr','Farmer','Photo','FamilyNumber','Garden','PostHarvest','Nutrition','PPI','GFP','Environment','GPS','NamaFF']
    });

    var store_act_detail = Ext.create('Ext.data.Store', {
        model: 'detail.Model',
        autoLoad: false,
        pageSize: 20,
        proxy: {
            type: 'ajax',
            url: m_activity_detail,
            reader: {
                type: 'json',
                root: 'data',
                totalProperty: 'total'
            }
        },
        listeners: {
            beforeload: function (store, operation) {
                store.proxy.extraParams.batch = Ext.getCmp('dbatch').getValue();
                store.proxy.extraParams.cpg = Ext.getCmp('dcpg').getValue().join();
            }
        }
    });


    var tab = Ext.create('Ext.form.Panel', {
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
                title: 'Summary',
                id: 'tab_summary',
                padding: 10,
                //style: 'border:2px solid #ADD2ED',
                items: [{
                    xtype: 'form',
                    padding: 5,
                    fieldDefaults: {
                        labelAlign: 'center',
                        labelWidth: 160,
                        anchor: '100%'
                    },
                    items: [{
                        layout: {type: 'hbox'},
                        items: [{
                            id: 'Provinsi',
                            name: 'Provinsi',
                            xtype: 'combo',
                            emptyText: '-- '+lang('Province')+' --',
                            //labelWidth: 60,
                            //fieldLabel: 'Provinsi',
                            store: mc_Provinsi,
                            displayField: 'label',
                            valueField: 'label',
                            queryMode: 'local',
                            listeners: {
                                change: function (cb, nv, ov) {
                                    mc_Kabupaten.load({
                                        params: {
                                            key: Ext.getCmp('Provinsi').getValue()
                                        }
                                    });
                                }
                            },
                            padding: 5
                        }, {
                            id: 'Kabupaten',
                            name: 'Kabupaten[]',
                            xtype: 'combo',
                            emptyText: '-- '+lang('District')+' --',
                            //labelWidth: 70,
                            //fieldLabel: 'Kabupaten',
                            store: mc_Kabupaten,
                            displayField: 'label',
                            valueField: 'label',
                            queryMode: 'local',
                            multiSelect: true,
                            padding: 5
                        }, {
                            id: 'jenis',
                            name: 'jenis',
                            xtype: 'combo',
                            emptyText: '-- '+lang('Type')+' --',
                            labelWidth: 40,
                            fieldLabel: 'Jenis',
                            store: mc_jenis,
                            displayField: 'label',
                            valueField: 'label',
                            queryMode: 'local',
                            hidden: true
                        }, {
                            xtype: 'button',
                            text: 'D',
                            padding: '-3px 0px 0px 0px',
                            margin: '4px 5px 0px 0px',
                            handler: function () {
                                Ext.getCmp('jenis').setValue('Jaman');
                                var date = new Date();
                                var tgl = date.getDate();
                                if (tgl < 10) tgl = '0' + tgl;
                                var bln = date.getMonth() + 1;
                                if (bln < 10) bln = '0' + bln;
                                Ext.getCmp('end').setValue(date.getFullYear() + '-' + bln + '-' + tgl);
                                Ext.getCmp('start').setValue(date.getFullYear() + '-' + bln + '-' + tgl);
                                /*var form = this.up('form').getForm();
                                 form.submit({
                                 url: m_crud,
                                 method : 'GET',
                                 waitMsg: 'Wait...',
                                 success: function(fp, o) {
                                 createStore(o.result.fielddata, o.result.values);
                                 createGrid(o.result.columndata,o.result.title);
                                 createChart(o.result.cat,o.result.data,o.result.title);
                                 }
                                 });*/
                            }
                        }, {
                            xtype: 'button',
                            text: 'W',
                            padding: '-3px 0px 0px 0px',
                            margin: '4px 5px 0px 0px',
                            handler: function () {
                                Ext.getCmp('jenis').setValue('Harian');
                                var date = new Date();
                                var tgl = date.getDate();
                                if (tgl < 10) tgl = '0' + tgl;
                                var bln = date.getMonth() + 1;
                                if (bln < 10) bln = '0' + bln;
                                Ext.getCmp('end').setValue(date.getFullYear() + '-' + bln + '-' + tgl);
                                date.setDate(date.getDate() - 6);
                                tgl = date.getDate();
                                if (tgl < 10) tgl = '0' + tgl;
                                bln = date.getMonth() + 1;
                                if (bln < 10) bln = '0' + bln;
                                Ext.getCmp('start').setValue(date.getFullYear() + '-' + bln + '-' + tgl);
                            }
                        }, {
                            xtype: 'button',
                            text: 'M',
                            padding: '-3px 0px 0px 0px',
                            margin: '4px 5px 0px 0px',
                            handler: function () {
                                Ext.getCmp('jenis').setValue('Mingguan');
                                var date = new Date();
                                var tgl = date.getDate();
                                if (tgl < 10) tgl = '0' + tgl;
                                var bln = date.getMonth() + 1;
                                if (bln < 10) bln = '0' + bln;
                                Ext.getCmp('end').setValue(date.getFullYear() + '-' + bln + '-' + tgl);
                                date.setMonth(date.getMonth() - 1);
                                date.setDate(date.getDate() + 1);
                                tgl = date.getDate();
                                if (tgl < 10) tgl = '0' + tgl;
                                bln = date.getMonth() + 1;
                                if (bln < 10) bln = '0' + bln;
                                Ext.getCmp('start').setValue(date.getFullYear() + '-' + bln + '-' + tgl);
                            }
                        }, {
                            xtype: 'button',
                            text: 'Y',
                            padding: '-3px 0px 0px 0px',
                            margin: '4px 5px 0px 0px',
                            handler: function () {
                                Ext.getCmp('jenis').setValue('Bulanan');
                                var date = new Date();
                                var tgl = date.getDate();
                                if (tgl < 10) tgl = '0' + tgl;
                                var bln = date.getMonth() + 1;
                                if (bln < 10) bln = '0' + bln;
                                Ext.getCmp('end').setValue(date.getFullYear() + '-' + bln + '-' + tgl);
                                date.setFullYear(date.getFullYear() - 1);
                                date.setDate(date.getDate() + 1);
                                tgl = date.getDate();
                                if (tgl < 10) tgl = '0' + tgl;
                                bln = date.getMonth() + 1;
                                if (bln < 10) bln = '0' + bln;
                                Ext.getCmp('start').setValue(date.getFullYear() + '-' + bln + '-' + tgl);
                            }
                        }, {
                            xtype: 'datefield',
                            format: 'Y-m-d',
                            fieldLabel: '',
                            emptyText: '-- '+lang('Start')+' --',
                            id: 'start',
                            name: 'start',
                            padding: 5
                        }, {
                            xtype: 'datefield',
                            format: 'Y-m-d',
                            emptyText: '-- '+lang('End')+' --',
                            fieldLabel: '',
                            id: 'end',
                            name: 'end',
                            padding: 5
                        }, {
                            xtype: 'button',
                            text: 'GO',
                            padding: '-3px 0px 0px 0px',
                            margin: '4px 5px 0px 0px',
                            handler: function () {
                                if (Ext.getCmp('Provinsi').getValue() == '' || Ext.getCmp('Provinsi').getValue() == null || Ext.getCmp('Provinsi').getValue() == undefined) {
                                    Ext.Msg.alert('Warning', 'Please select province');
                                } else if (Ext.getCmp('Kabupaten').getValue() == '' || Ext.getCmp('Kabupaten').getValue() == null || Ext.getCmp('Kabupaten').getValue() == undefined) {
                                    Ext.Msg.alert('Warning', 'Please select district');
                                } else {
                                    var form = this.up('form').getForm();
                                    form.submit({
                                        url: m_crud,
                                        method: 'GET',
                                        waitMsg: 'Wait...',
                                        success: function (fp, o) {
                                            createStore(o.result.fielddata, o.result.values);
                                            createGrid(o.result.columndata, o.result.title);
                                            createChart(o.result.cat, o.result.data, o.result.title);
                                        }
                                    });
                                }
                            }
                        }]
                    }]
                }]
            }, {
                xtype: 'panel',
                autoScroll: true,
                title: 'Detail',
                id: 'tab_detail',
                padding: 10,
                items: [{
                    xtype: 'gridpanel',
                    store: store_act_detail,
                    id: 'grid_detail',
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
                            store: store_act_detail,
                            dock: 'bottom',
                            displayInfo: true
                        },
                        {
                            xtype: 'toolbar',
                            items: [{
                                id: 'dprov',
                                name: 'dProvinsi',
                                emptyText: '-- '+lang('Province')+' --',
                                xtype: 'combo',
                                store: mc_dProvinsi,
                                displayField: 'label',
                                valueField: 'label',
                                queryMode: 'local',
                                listeners: {
                                    change: function (cb, nv, ov) {
                                        mc_dKabupaten.load({
                                            params: {
                                                key: Ext.getCmp('dprov').getValue()
                                            }
                                        });
                                    }
                                }
                            }, {
                                id: 'dkab',
                                name: 'dKabupaten',
                                emptyText: '-- '+lang('District')+' --',
                                xtype: 'combo',
                                store: mc_dKabupaten,
                                displayField: 'label',
                                valueField: 'id',
                                queryMode: 'local',
                                listeners: {
                                    change: function (cb, nv, ov) {
                                        mc_batch.load({
                                            params: {
                                                //prov: Ext.getCmp('dprov').getValue(),
                                                kab: Ext.getCmp('dkab').getValue(),
                                            }
                                        });
                                        mc_cpg.load({
                                            params: {
                                                kab: Ext.getCmp('dkab').getValue()
                                            }
                                        });
                                    }
                                }
                            }, {
                                id: 'dbatch',
                                name: 'dBatch',
                                xtype: 'combo',
                                emptyText: '-- '+lang('Batch')+' --',
                                store: mc_batch,
                                displayField: 'label',
                                valueField: 'id',
                                queryMode: 'local',
                                //width: 250,
                                // listeners: {
                                //     change: function (cb, nv, ov) {
                                //         mc_cpg.load({
                                //             params: {
                                //                 batch: Ext.getCmp('dbatch').getValue()
                                //             }
                                //         });
                                //     }
                                // }
                            }, {
                                id: 'dcpg',
                                name: 'dCPG',
                                emptyText: '-- '+lang('All CPG')+' --',
                                xtype: 'combo',
                                multiSelect: true,
                                store: mc_cpg,
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
                                        if (!Ext.getCmp('dbatch').getValue()) {
                                            Ext.MessageBox.alert(lang('Error'),lang('Please select batch'));
                                            return;
                                        }
                                        store_act_detail.load({
                                            params: {
                                                batch: Ext.getCmp('dbatch').getValue(),
                                                cpg: Ext.getCmp('dcpg').getValue().join(),
                                                page: 1,
                                                start: 0,
                                                limit: 20
                                            }
                                        })
                                    }
                                }]
                            }, {
                                xtype: 'form',
                                items: [{
                                    xtype: 'button',
                                    icon: varjs.config.base_url + 'images/icons/silk/page_excel.png',
                                    margin: '0px 0px 0px 6px',
                                    text: 'Export',
                                    handler: function () {
                                        if (!Ext.getCmp('dbatch').getValue()) {
                                            Ext.MessageBox.alert(lang('Error'),lang('Please select batch'));
                                        } else {
                                            window.open(m_cetak_activity_detail+'/'+Ext.getCmp('dbatch').getValue()+'/'+encodeURIComponent(Ext.getCmp('dcpg').getValue().join()));
                                        }
                                    }
                                }]
                            }
                            ]
                        }],
                    columns: [
                        {
                            text: lang('Farmer ID'),
                            dataIndex: 'FarmerID',
                        }, {
                            text: lang('Farmer Name'),
                            dataIndex: 'FarmerName',
                        }, {
                            text: lang('Village'),
                            dataIndex: 'Village',
                        },{
                            text: lang('SurveyNr'),
                            dataIndex: 'SurveyNr',
                        }, {
                            text: lang('FarmerPhoto'),
                            dataIndex: 'Photo',
                            xtype: 'actioncolumn',
                            renderer: function(value) {
                                if(value) {
                                    return Ext.String.format('<a class="previewPhoto" onclick="previewPhoto(\'{0}\')" href="#">{1}</a>', value, lang('Yes'));
                                } else {
                                    return lang('No');
                                }
                            },
                        }, {
                            text: lang('Family Number'),
                            dataIndex: 'FamilyNumber',
                        }, {
                            text: lang('Farmer'),
                            dataIndex: 'Farmer',
                        },{
                            text: lang('Kebun'),
                            dataIndex: 'Garden',
                        }, {
                            text: lang('Paska Panen'),
                            dataIndex: 'PostHarvest',
                        }, {
                            text: lang('Nutrition'),
                            dataIndex: 'Nutrition',
                        }, {
                            text: 'PPI',
                            dataIndex: 'PPI',
                        }, {
                            text: lang('Finance'),
                            dataIndex: 'GFP',
                        }, {
                            text: lang('Environment'),
                            dataIndex: 'Environment'
                        },{
                            text: 'GPS',
                            dataIndex: 'GPS',
                        }, {
                            text: lang('FieldStaff'),
                            dataIndex: 'NamaFF',
                        }
                    ]
                }]
            }]
        }]
    });

    var createGrid = function (columndata, title) {
        //ubah bahasa title header
        for (var keyColumn in columndata) {
            columndata[keyColumn].header = lang(columndata[keyColumn].header);
        }

        var tab = Ext.getCmp('tab_summary');
        tab.remove(grid);
        grid = Ext.create('Ext.grid.Panel', {
            store: store,
            columns: columndata,
            stripeRows: true,
            width: '100%',
            loadMask: true,
            selType: 'rowmodel',
            title: title,
            listeners: {
                'cellclick': function (grid, td, cellIndex, record, tr, rowIndex, e, eOpts) {
                    //cellindex: kolom
                    //rowIndex:baris
                    displayDataDetail(cellIndex, rowIndex);
                    Ext.getCmp('cDetails').setValue(cellIndex);
                    Ext.getCmp('rDetails').setValue(rowIndex);
                }
            }
        });
        tab.setHeight(220 + (height * 34));
        tab.add(grid)
        tab.doLayout();
    }

    var createStore = function (fielddata, values) {
        store = Ext.create('Ext.data.Store', {
            extend: 'Ext.data.Model',
            fields: fielddata,
            data: values
        });
        height = values.length;
    }

    var createChart = function (cat, data, judul) {
        new Highcharts.Chart({
            chart: {
                renderTo: 'et-content'
            },
            title: {
                text: judul,
                x: -20 //center
            },
            xAxis: {
                categories: cat,
                title: {
                    text: lang('Periode')
                }
            },
            yAxis: {
                title: {
                    text: lang('Jumlah')
                }
            },
            legend: {
                layout: 'vertical',
                align: 'right',
                verticalAlign: 'middle',
                borderWidth: 0
            },
            series: [{
                name: lang('Petani baru'),
                data: data['Petani baru']
            }, {
                name: lang('Ubah petani'),
                data: data['Ubah petani']
            }, {
                name: lang('Garden baru'),
                data: data['Garden baru']
            }, {
                name: lang('Ubah garden'),
                data: data['Ubah garden']
            }, {
                name: lang('Harvest baru'),
                data: data['Harvest baru']
            }, {
                name: lang('Ubah harvest'),
                data: data['Ubah harvest']
            }, {
                name: lang('Nutrition baru'),
                data: data['Nutrition baru']
            }, {
                name: lang('Ubah nutrition'),
                data: data['Ubah nutrition']
            }, {
                name: lang('PPI baru'),
                data: data['PPI baru']
            }, {
                name: lang('Ubah PPI'),
                data: data['Ubah PPI']
            }, {
                name: lang('Finance baru'),
                data: data['Finance baru']
            }, {
                name: lang('Ubah finance'),
                data: data['Ubah finance']
            },{
                name: lang('Environment baru'),
                data: data['Environment baru']
            },{
                name: lang('Ubah environment'),
                data: data['Ubah environment']
            },{
                name: lang('Village baru'),
                data: data['Village baru']
            },{
                name: lang('Ubah village'),
                data: data['Ubah village']
            }]
        });
    }

//detail
    var store_detail = Ext.create('Ext.data.Store', {
        extend: 'Ext.data.Model',
        fields: ['data', 'oleh', 'waktu'],
        autoLoad: true,
        pageSize: 10,
        proxy: {
            type: 'ajax',
            url: m_detail,
            reader: {
                type: 'json',
                root: 'data'
            }
        }
    });

    function displayDataDetail(c, r) {
        Ext.Ajax.request({
            url: m_detail,
            method: 'GET',
            params: {
                c: c, r: r, star: Ext.getCmp('start').getValue(), en: Ext.getCmp('end').getValue(),
                prov: Ext.getCmp('Provinsi').getValue(), kab: [Ext.getCmp('Kabupaten').getValue()]
            },
            success: function (fp, o) {
                var r = Ext.decode(fp.responseText);
                createStoreDetail(r.fielddata, r.values);
                createGridDetail(r.columndata);
            }
        });
        if (!winDetail.isVisible()) {
            winDetail.show();
        } else {
            winDetail.hide(this, function () {
            });
            winDetail.toFront();
        }
    }

    /*
     ,
     items: [{
     xtype: 'gridpanel',
     id:'gdetail',
     store: store_detail,
     width: '100%',
     loadMask: true,
     selType: 'rowmodel',
     columns: [{
     text: 'No',
     xtype: 'rownumberer',
     width:'5%'
     },{
     text: 'Data',
     dataIndex: 'data',
     width:'55%'
     },{
     text: 'Oleh',
     dataIndex: 'oleh',
     width:'20%'
     },{
     text: 'Waktu',
     dataIndex: 'waktu',
     width:'20%'
     }]
     }]
     */
    var grid_detail
    var createGridDetail = function (columndata) {
        DataDetail.remove(grid_detail)
        grid_detail = Ext.create('Ext.grid.Panel', {
            store: store_detail,
            width: '200%',
            height: '100%',
            autoScroll: true,
            columns: columndata
        });
        DataDetail.setHeight(220 + (height * 34));
        DataDetail.add(grid_detail)
        DataDetail.doLayout();
    }
    var createStoreDetail = function (fielddata, values) {
        store_detail = Ext.create('Ext.data.Store', {
            extend: 'Ext.data.Model',
            fields: fielddata,
            data: values
        });
        height_detail = values.length;
    }

    var DataDetail = Ext.create('Ext.form.Panel', {
        frame: false,
        autoScroll: true,
        height: 350,
        width: 600,
        bodyPadding: 5,
        id: 'dataDetail',
        dockedItems: [{
            xtype: 'toolbar',
            dock: 'top',
            items: [{
                xtype: 'button',
                text: 'Export Excel',
                handler: function () {
                    var txtStart = Ext.Date.format(Ext.getCmp('start').getValue(), 'Y-m-d');
                    var txtEnd = Ext.Date.format(Ext.getCmp('end').getValue(), 'Y-m-d');
                    var txtKab = Ext.getCmp('Kabupaten').getValue();
                    var txtZ = '';
                    if (txtKab == '' || txtKab == undefined || txtKab == ' -- All --' || txtKab === null) {
                        txtZ = 'null';
                    } else {
                        txtKab = txtKab.toString();
                        txtZ = txtKab.replace(/,/g, "-");
                        txtZ = txtZ.replace(/\s+/g, "_");
                    }

                    var txtProv = Ext.getCmp('Provinsi').getValue();
                    if (txtProv == '' || txtProv === undefined || txtProv === ' -- All --' || txtProv === null) {
                        txtProv = 'null';
                    } else {
                        txtProv = txtProv.replace(/\s+/g, "_");
                    }

                    txtStart = txtStart + 'T';
                    txtEnd = txtEnd + 'T';
                    var txtUrl = m_export_details_progress +
                        Ext.getCmp('cDetails').getValue() + '/' +
                        Ext.getCmp('rDetails').getValue() + '/' +
                        txtProv + '/' +
                        txtZ + '/' +
                        txtStart + '/' +
                        txtEnd;
                    //Ext.Msg.alert('',txtUrl);
                    window.location = txtUrl;
                }
            }]
        }],
        items: [{
            xtype: 'textfield',
            id: 'cDetails',
            namae: 'cDetails',
            hidden: true
        }, {
            xtype: 'textfield',
            id: 'rDetails',
            namae: 'rDetails',
            hidden: true
        }]
    });

    var winDetail = Ext.create('widget.window', {
        title: 'Detail',
        id: 'win',
        closable: true,
        modal: true,
        closeAction: 'show',
        width: 930,
        height: 370,
        layout: 'fit',
        items: [DataDetail]
    });

    PreviewForm = Ext.create('Ext.form.Panel', {
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
    winPreview = Ext.widget('window', {
        title: 'Photo Preview',
        height: 400,
        width: 600,
        id: 'winPreview',
        autoScroll: true,
        modal: true,
        closable: false,
        layout: {
            type: 'fit'
        },
        items: [PreviewForm]
    });
});



function previewPhoto(photo) {
    // url = m_api.replace('index.php', '')+'images/Photo/'+photo;
    url = 'http://app.cocoatrace.com/api/images/Photo/'+photo;
    Ext.getCmp('iphoto').setSrc(url);
    if (!winPreview.isVisible()) {
        winPreview.show();
        winPreview.toFront();
    } else {
        winPreview.hide(this, function () {
        });
        winPreview.toFront();
    }
}
// $(function(){
// })

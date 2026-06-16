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

Ext.onReady(function() {
    var store, grid, height;
    var mc_staff = Ext.create('Ext.data.Store', {
        extend: 'Ext.data.Model',
        fields: ['id', 'label'],
        autoLoad: false,
        pageSize: 10,
        proxy: {
            type: 'ajax',
            url: m_staff,
            reader: {
                type: 'json',
                root: 'data'
            }
        }
    });
    var mc_jenis = Ext.create('Ext.data.Store', {
        fields: ['label'],
        data: [{
            "label": "Jaman"
        }, {
            "label": "Harian"
        }, {
            "label": "Mingguan"
        }, {
            "label": "Bulanan"
        }, {
            "label": "Tahunan"
        }]
    });
    var mc_provinsi = Ext.create('Ext.data.Store', {
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
    var mc_kabupaten = Ext.create('Ext.data.Store', {
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
    var tab = Ext.create('Ext.Panel', {
        renderTo: 'ext-content',
        height: 180,
        frame: false,
        items: [{
            xtype: 'fieldset',
            title: 'Staff Activity Report',
            items: [{
                xtype: 'form',
                padding: 5,
                fieldDefaults: {
                    labelAlign: 'center',
                    labelWidth: 160,
                    anchor: '100%'
                },
                items: [{
                    layout: {
                        type: 'hbox'
                    },
                    items: [{
                        id: 'Provinsi',
                        name: lang('Provinsi'),
                        emptyText: '-- ' + lang('Provinsi') + ' --',
                        xtype: 'combo',
                        store: mc_provinsi,
                        displayField: 'label',
                        valueField: 'id',
                        width: 180,
                        padding: 5,
                        queryMode: 'local',
                        listeners: {
                            change: function(cb, nv, ov) {
                                Ext.getCmp('Kabupaten').setValue('');
                                Ext.getCmp('Staff').setValue('');
                                mc_kabupaten.load({
                                    params: {
                                        key: Ext.getCmp('Provinsi').getValue()
                                    }
                                });
                            }
                        }
                    }, {
                        id: 'Kabupaten',
                        name: lang('Kabupaten'),
                        emptyText: '-- ' + lang('Kabupaten') + ' --',
                        xtype: 'combo',
                        store: mc_kabupaten,
                        displayField: 'label',
                        valueField: 'id',
                        width: 180,
                        padding: 5,
                        queryMode: 'local',
                        listeners: {
                            change: function(cb, nv, ov) {
                                Ext.getCmp('Staff').setValue('');
                                mc_staff.load({
                                    params: {
                                        key: Ext.getCmp('Kabupaten').getValue()
                                    }
                                });
                            }
                        }
                    }, {
                        id: 'Staff',
                        name: 'Staff',
                        emptyText: '-- ' + lang('Staff') + ' --',
                        xtype: 'combo',
                        store: mc_staff,
                        displayField: 'label',
                        valueField: 'id',
                        width: 400,
                        queryMode: 'local',
                        padding: 5
                    }, {
                        id: 'jenis',
                        name: 'jenis',
                        xtype: 'combo',
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
                        handler: function() {
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
                        handler: function() {
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
                        handler: function() {
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
                        handler: function() {
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
                        id: 'start',
                        name: 'start',
                        width: 100,
                        padding: 5
                    }, {
                        xtype: 'datefield',
                        format: 'Y-m-d',
                        fieldLabel: '',
                        id: 'end',
                        name: 'end',
                        width: 100,
                        padding: 5
                    }, {
                        xtype: 'button',
                        text: 'GO',
                        padding: '-3px 0px 0px 0px',
                        margin: '4px 5px 0px 0px',
                        handler: function() {
                            var form = this.up('form').getForm();
                            form.submit({
                                url: m_crud,
                                method: 'GET',
                                waitMsg: 'Wait...',
                                success: function(fp, o) {
                                    if (!o.result.values) {
                                        Ext.Msg.alert('Warning', 'No data to display');
                                    }
                                    createStore(o.result.fielddata, o.result.values ? o.result.values : []);
                                    createGrid(o.result.columndata, o.result.title);
                                    createChart(o.result.cat, o.result.data ? o.result.data : [], o.result.title);
                                }
                            });
                        }
                    }]
                }]
            }]
        }]
    });

    var createGrid = function(columndata, title) {
        //ubah bahasa title header
        for (var keyColumn in columndata) {
            columndata[keyColumn].header = lang(columndata[keyColumn].header);
        }

        tab.remove(grid)
        grid = Ext.create('Ext.grid.Panel', {
            store: store,
            columns: columndata,
            stripeRows: true,
            width: '100%',
            loadMask: true,
            selType: 'rowmodel',
            title: title,
            listeners: {
                'cellclick': function(grid, td, cellIndex, record, tr, rowIndex, e, eOpts) {
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
    var createStore = function(fielddata, values) {
        store = Ext.create('Ext.data.Store', {
            extend: 'Ext.data.Model',
            fields: fielddata,
            data: values
        });
        height = values.length;
    }
    var createChart = function(cat, data, judul) {
        console.log(data);

            if (data) {
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
            };
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
                c: c,
                r: r,
                star: Ext.getCmp('start').getValue(),
                en: Ext.getCmp('end').getValue(),
                staff: Ext.getCmp('Staff').getValue()
            },
            success: function(fp, o) {
                var r = Ext.decode(fp.responseText);
                createStoreDetail(r.fielddata, r.values);
                createGridDetail(r.columndata);
            }
        });
        if (!winDetail.isVisible()) {
            winDetail.show();
        } else {
            winDetail.hide(this, function() {});
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
    var createGridDetail = function(columndata) {
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
    var createStoreDetail = function(fielddata, values) {
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
                handler: function() {
                    var txtStart = Ext.Date.format(Ext.getCmp('start').getValue(), 'Y-m-d');
                    var txtEnd = Ext.Date.format(Ext.getCmp('end').getValue(), 'Y-m-d');
                    txtStart = txtStart + 'T';
                    txtEnd = txtEnd + 'T';
                    var txtStaf = Ext.getCmp('Staff').getValue();
                    txtStaf = txtStaf.split(' ').join('_');
                    var txtUrl = m_export_details + Ext.getCmp('cDetails').getValue() + '/' + Ext.getCmp('rDetails').getValue() + '/' + txtStaf + '/' + txtStart + '/' + txtEnd;
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
        title: 'Detail_S',
        id: 'win',
        closable: true,
        modal: true,
        closeAction: 'show',
        width: 930,
        height: 370,
        layout: 'fit',
        items: [DataDetail]
    });
});
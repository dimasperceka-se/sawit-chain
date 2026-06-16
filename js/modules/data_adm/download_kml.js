
Ext.onReady(function () {
    var kml_province = Ext.create('Ext.data.Store', {
        extend: 'Ext.data.Model',
        fields: ['id', 'label'],
        autoLoad: true,
        pageSize: 10,
        proxy: {
            type: 'ajax',
            url: m_api+'/geospatial/kml_province',
            reader: {
                type: 'json',
                root: 'data'
            }
        }
    });
    var kml_district = Ext.create('Ext.data.Store', {
        extend: 'Ext.data.Model',
        fields: ['id', 'label'],
        autoLoad: false,
        pageSize: 10,
        proxy: {
            type: 'ajax',
            url: m_api+'/geospatial/kml_district',
            reader: {
                type: 'json',
                root: 'data'
            }
        }
    });
    var kml_subdistrict = Ext.create('Ext.data.Store', {
        extend: 'Ext.data.Model',
        fields: ['id', 'label'],
        autoLoad: false,
        pageSize: 10,
        proxy: {
            type: 'ajax',
            url: m_api+'/geospatial/kml_subdistrict',
            reader: {
                type: 'json',
                root: 'data'
            }
        }
    });
    var kml_village = Ext.create('Ext.data.Store', {
        extend: 'Ext.data.Model',
        fields: ['id', 'label'],
        autoLoad: false,
        pageSize: 10,
        proxy: {
            type: 'ajax',
            url: m_api+'/geospatial/kml_village',
            reader: {
                type: 'json',
                root: 'data'
            }
        }
    });
    var kml_farmer_group = Ext.create('Ext.data.Store', {
        extend: 'Ext.data.Model',
        fields: ['id', 'label'],
        autoLoad: false,
        pageSize: 10,
        proxy: {
            type: 'ajax',
            url: m_api + '/geospatial/kml_farmer_group',
            reader: {
                type: 'json',
                root: 'data'
            }
        }
    });
    var kml_partner = Ext.create('Ext.data.Store', {
        extend: 'Ext.data.Model',
        fields: ['id', 'label'],
        autoLoad: false,
        pageSize: 10,
        proxy: {
            type: 'ajax',
            url: m_api + '/geospatial/kml_partner',
            reader: {
                type: 'json',
                root: 'data'
            }
        },
        listeners: {
            beforeload: function(store, operation, options){
                store.proxy.extraParams.DistrictID = Ext.getCmp('kmlDistrict').getValue();
            }
        }
    });
    var kml_farmer = Ext.create('Ext.data.Store', {
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

    var tab = Ext.create('Ext.Panel', {
        renderTo: 'ext-content',
        height: 2500,
        frame: false,
        items: [
        {
            xtype: 'fieldset',
            title: lang(' Download Garden KML'),
            id: 'garden_kml',
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
                            id: 'kmlProvince',
                            name: 'kmlProvince',
                            xtype: 'combo',
                            labelWidth: 60,
                            store: kml_province,
                            displayField: 'label',
                            valueField: 'id',
                            queryMode: 'local',
                            listeners: {
                                change: function (cb, nv, ov) {
                                    Ext.getCmp('kmlDistrict').setValue('');
                                    kml_district.load({params: {ProvinceID: nv}});
                                }
                            }
                        }]
                    }, {
                        columnWidth: 0.15,
                        layout: 'form',
                        padding: 3,
                        border: false,
                        items: [{
                            id: 'kmlDistrict',
                            name: 'kmlDistrict',
                            xtype: 'combo',
                            emptyText: '-- ' + lang('Kabupaten') + '--',
                            labelWidth: 70,
                            store: kml_district,
                            displayField: 'label',
                            valueField: 'id',
                            queryMode: 'local',
                            listeners: {
                                change: function (cb, nv, ov) {
                                    Ext.getCmp('kmlSubDistrict').setValue('');
                                    kml_subdistrict.load({params: {DistrictID: nv}});
                                    Ext.getCmp('kmlPartner').setValue('');
                                    kml_partner.load();
                                }
                            }
                        }]
                    }, {
                        columnWidth: 0.15,
                        layout: 'form',
                        padding: 3,
                        border: false,
                        items: [{
                            id: 'kmlSubDistrict',
                            name: 'kmlSubDistrict',
                            xtype: 'combo',
                            emptyText: '-- ' + lang('Sub District') + '--',
                            labelWidth: 70,
                            store: kml_subdistrict,
                            displayField: 'label',
                            valueField: 'id',
                            queryMode: 'local',
                            listeners: {
                                change: function (cb, nv, ov) {
                                    Ext.getCmp('kmlVillage').setValue('');
                                    kml_village.load({params: {SubDistrictID: nv}});
                                }
                            }
                        }]
                    }, {
                        columnWidth: 0.15,
                        layout: 'form',
                        padding: 3,
                        border: false,
                        items: [{
                            id: 'kmlVillage',
                            name: 'kmlVillage',
                            xtype: 'combo',
                            emptyText: '-- ' + lang('Village') + '--',
                            labelWidth: 70,
                            store: kml_village,
                            displayField: 'label',
                            valueField: 'id',
                            queryMode: 'local',
                            listeners: {
                                change: function (cb, nv, ov) {
                                }
                            }
                        }]
                    }, {
                        hidden: true,
                        columnWidth: .20,
                        layout: 'form',
                        padding: 3,
                        border: false,
                        items: [{
                            id: 'kmlfarmer_group',
                            name: 'kmlfarmer_group',
                            emptyText: '-- ' + lang('Kelompok Petani') + '--',
                            xtype: 'combo',
                            labelWidth: 40,
                            store: kml_farmer_group,
                            displayField: 'label',
                            valueField: 'id',
                            queryMode: 'local',
                            listeners: {
                                change: function (cb, nv, ov) {
                                    Ext.getCmp('kmlFarmer').setValue(null);
                                    kml_farmer.load({
                                        params: {
                                            FarmerGroupID: nv
                                        },
                                        // callback: function () {
                                        //     setKmlFarmer();
                                        // }
                                    });
                                }
                            }
                        }]
                    }, {
                        hidden: !m_show_partner,
                        columnWidth: .20,
                        layout: 'form',
                        padding: 3,
                        border: false,
                        items: [{
                            id: 'kmlPartner',
                            name: 'kmlPartner',
                            emptyText: '-- ' + lang('Partner') + '--',
                            xtype: 'combo',
                            labelWidth: 40,
                            store: kml_partner,
                            displayField: 'label',
                            valueField: 'id',
                            queryMode: 'local',
                            listeners: {
                                change: function (cb, nv, ov) {
                                }
                            }
                        }]
                    }, {
                        hidden: true,
                        columnWidth: .20,
                        layout: 'form',
                        padding: 3,
                        border: false,
                        items: [{
                            id: 'kmlFarmer',
                            name: 'kmlFarmer',
                            emptyText: '-- ' + lang('Petani') + '--',
                            xtype: 'combo',
                            multiSelect: true,
                            labelWidth: 40,
                            store: kml_farmer,
                            displayField: 'label',
                            valueField: 'id',
                            queryMode: 'local'
                        }]
                    },{
                        columnWidth: .15,
                        layout: 'form',
                        padding: 3,
                        border: false,
                        items: [{
                            id: 'kmlStatusPolygon',
                            name: 'kmlStatusPolygon',
                            emptyText: '-- ' + lang('Status Polygon') + '--',
                            xtype: 'combo',
                            // multiSelect: true,
                            labelWidth: 40,
                            store: Ext.create('Ext.data.Store', {
                                fields: ['id','label'],
                                data: [
                                    {'id':'verified', "label": lang("Verified Polygon Only")},
                                    {'id':'new', "label": lang("New Polygon")},
                                    {'id':'all', "label": lang("All Polygon")},
                                ],
                            }),
                            displayField: 'label',
                            valueField: 'id',
                            queryMode: 'local'
                        }]
                    },
                    {
                        columnWidth: .15,
                        layout: 'form',
                        padding: 3,
                        border: false,
                        items: [{
                            id: 'kmlType',
                            name: 'kmlType',
                            emptyText: '-- ' + lang('Type') + '--',
                            xtype: 'combo',
                            // multiSelect: true,
                            labelWidth: 40,
                            store: Ext.create('Ext.data.Store', {
                                fields: ['id','label'],
                                data: [
                                    {'id':'one', "label": lang("One farmer one file")},
                                    {'id':'all', "label": lang("All farmer on one file")},
                                ],
                            }),
                            displayField: 'label',
                            valueField: 'id',
                            queryMode: 'local'
                        }]
                    },
                    {
                        hidden: true,
                        columnWidth: .15,
                        layout: 'form',
                        padding: 3,
                        border: false,
                        items: [{
                            id: 'kmlRev',
                            name: 'kmlRev',
                            emptyText: '-- ' + lang('Revision') + '--',
                            xtype: 'combo',
                            // multiSelect: true,
                            labelWidth: 40,
                            store: Ext.create('Ext.data.Store', {
                                fields: ['id','label'],
                                data: [
                                    {'id':'first', "label": lang("First")},
                                    {'id':'last', "label": lang("Last")},
                                ],
                            }),
                            displayField: 'label',
                            valueField: 'id',
                            queryMode: 'local'
                        }]
                    },
                    {
                        hidden: true,
                        columnWidth: .15,
                        layout: 'form',
                        padding: 3,
                        border: false,
                        items: [
                        {
                            xtype     : 'checkboxfield',
                            boxLabel  : lang('With FarmerID'),
                            name      : 'kmlFarmerID',
                            inputValue: '1',
                            id        : 'kmlFarmerID',
                            checked   : true,
                        }
                        ]
                    },
                    ]
                }],
                buttons: [{
                    // id: 'iCetakGL',
                    text: lang('Cetak'),
                    margin: '5px',
                    scale: 'large',
                    ui: 's-button',
                    cls: 's-blue ',
                    handler: function () {
                        if (!Ext.getCmp('kmlProvince').getValue()) {
                            Ext.MessageBox.alert('Warning', lang('Please select Province'));
                            return false;
                        }
                        // if (!Ext.getCmp('kmlDistrict').getValue()) {
                        //     Ext.MessageBox.alert('Warning', lang('Please select District'));
                        //     return false;
                        // }
                        // if (!Ext.getCmp('kmlSubDistrict').getValue()) {
                        //     Ext.MessageBox.alert('Warning', lang('Please select Sub District'));
                        //     return false;
                        // }
                        // if (!Ext.getCmp('kmlVillage').getValue()) {
                        //     Ext.MessageBox.alert('Warning', lang('Please select Village'));
                        //     return false;
                        // }
                        // if (!Ext.getCmp('kmlFarmer').getValue()) {
                        //     Ext.getCmp('kmlFarmer').setValue('all');
                        // }
                        if (!Ext.getCmp('kmlStatusPolygon').getValue()) {
                            Ext.MessageBox.alert('Warning', lang('Please select polygon status.'));
                            return false;
                        }
                        if (!Ext.getCmp('kmlType').getValue()) {
                            Ext.MessageBox.alert('Warning', lang('Please select type.'));
                            return false;
                        }
                        // var url_kml = m_api + '/geospatial/bulk_kml/?download=1' + '&DistrictID=' + Ext.getCmp('kmlDistrict').getValue()+ '&farmerGroupId=' + Ext.getCmp('kmlfarmer_group').getValue() + '&FarmerID=' + Ext.getCmp('kmlFarmer').getValue().join().replace(/,/g, '::') + '&type=' + Ext.getCmp('kmlType').getValue() + '&withFarmerID=' + Ext.getCmp('kmlFarmerID').getValue() + '&rev=' + Ext.getCmp('kmlRev').getValue();
                        // window.open(url_kml, 'Download KML');
                        // 

                        Ext.MessageBox.show({
                            msg: 'Please wait...',
                            progressText: 'Download...',
                            width: 300,
                            wait: true,
                            waitConfig: {
                                interval: 200
                            },
                            icon: 'ext-mb-info', //custom class in msg-box.html
                            animateTarget: 'mb9'
                        });

                        Ext.override(Ext.data.Connection, {
                            timeout: 60000
                        });
                        Ext.Ajax.timeout = 60000;
                        Ext.override(Ext.data.proxy.Ajax, { timeout: 60000 });
                        Ext.override(Ext.form.action.Action, { timeout: 120 });

                        Ext.Ajax.request({
                            url: m_api + '/geospatial/download_kml/',
                            method: 'POST',
                            params: {
                                ProvinceID: Ext.getCmp('kmlProvince').getValue(),
                                DistrictID: Ext.getCmp('kmlDistrict').getValue(),
                                SubDistrictID: Ext.getCmp('kmlSubDistrict').getValue(),
                                VillageID: Ext.getCmp('kmlVillage').getValue(),
                                PartnerID: Ext.getCmp('kmlPartner').getValue(),
                                FarmerID: Ext.getCmp('kmlFarmer').getValue().join().replace(/,/g, '::'),
                                status: Ext.getCmp('kmlStatusPolygon').getValue(),
                                type: Ext.getCmp('kmlType').getValue(),
                                withFarmerID: Ext.getCmp('kmlFarmerID').getValue()
                            },
                            waitMsg: lang('Please Wait'),
                            success: function(data) {
                                Ext.MessageBox.hide();
                                
                                var jsonResp = JSON.parse(data.responseText);
                                window.location = jsonResp.filedl;
                            },
                            failure: function(data) {
                                var jsonResp = JSON.parse(data.responseText);
                                
                                Ext.MessageBox.hide();
                                Ext.MessageBox.show({
                                    title: 'Notifications',
                                    msg: jsonResp.message ? jsonResp.message : lang('Failed to download'),
                                    buttons: Ext.MessageBox.OK,
                                    animateTarget: 'mb9',
                                    icon: 'ext-mb-error'
                                });
                            }
                        });
                    }
                }]
            }]
        },
        ]
    });

});

function setKmlFarmer() {
    var select = [];
    $.each(kml_farmer.data.items, function(index, val) {
        select.push(val.data.id);
    });
    Ext.getCmp('kmlFarmer').setValue(select);
}

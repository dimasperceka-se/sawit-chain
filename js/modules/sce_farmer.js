/*
 * @Author: nikolius
 * @Date:   2016-08-15 15:17:06
 * @Last Modified by:   nikolius
 * @Last Modified time: 2017-02-21 17:23:54
 */
Ext.onReady(function() {
    Ext.tip.QuickTipManager.init();
    //================================= Data Main List
    Ext.define('SceFarmer.Model', {
        extend: 'Ext.data.Model',
        fields: ['sce_id', 'FarmerID', 'FarmerName', 'GroupName', 'Desa', 'Kecamatan', 'DateUpdated', 'DateSurvey', 'Photo', 'Latitude', 'Longitude','asBuyingUnit','haveClonal','haveNursery','haveCompost']
    });
    var store = Ext.create('Ext.data.Store', {
        model: 'SceFarmer.Model',
        autoLoad: true,
        pageSize: 50,
        proxy: {
            type: 'ajax',
            url: m_crud + 's',
            params: {
                'X-API-KEY': '030584'
            },
            extraParams: {
                prov: m_param,
                dist: m_DistrictID,
                subdist: m_SubDistrictID,
            },
            reader: {
                type: 'json',
                root: 'data',
                totalProperty: 'total'
            }
        }
    });
    // store.on('beforeload', function() {
    //     var proxy = store.getProxy();
    //     proxy.setExtraParam('key', Ext.getCmp('key').getValue());
    //     proxy.setExtraParam('kab', {
    //         array: Ext.encode(Ext.getCmp('Kab').getValue())
    //     });
    // });
    //================================= Data Combo Kabupaten
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
    // mc_Kabupaten.on('load', function(st) {
    //     Ext.getCmp('Kab').setValue(Ext.getCmp('Kab').store.getAt(0).get('label'))
    //     store.load({
    //         params: {
    //             prov: m_param,
    //             kab: {
    //                 array: Ext.encode(Ext.getCmp('Kab').store.getAt(0).get('label'))
    //             }
    //         }
    //     });
    // });
    var grid = Ext.create('Ext.grid.Panel', {
        store: store,
        width: '100%',
        id: 'grid',
        minHeight: 250,
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
                // cls: m_act_add,
                hidden: !m_act_add,
                handler: function() {
                    Ext.getCmp('FarmerName').setReadOnly(false);
                    Ext.getCmp('Longitude').setReadOnly(false);
                    Ext.getCmp('Latitude').setReadOnly(false);
                    Ext.getCmp('saveButton').setVisible(true);
                    displayFormWindow();
                    //reset garden
                    store_garden_status.load({
                        params: {
                            id: null
                        }
                    });
                    Ext.getCmp('ilogo').setSrc(m_photo + 'no-user.jpg');

                    //laod staff
                    store_staff.load({
                        params: {
                           id: null
                        }
                    });
                }
            }, {
                icon: varjs.config.base_url + 'images/icons/new/update.png',
                text: lang('Update'),
                scope: this,
                // cls: m_act_add,
                hidden: !m_act_update,
                handler: function() {
                    var sm = Ext.getCmp('grid').getSelectionModel().getSelection()[0];
                    Ext.getCmp('saveButton').setVisible(true);
                    setFormSceUpdate(sm);
                }
            }, {
                itemId: 'remove',
                icon: varjs.config.base_url + 'images/icons/new/delete.png',
                // cls: m_act_delete,
                hidden: !m_act_delete,
                text: lang('Hapus'),
                scope: this,
                handler: function() {
                    var smb = Ext.getCmp('grid').getSelectionModel().getSelection()[0];
                    Ext.MessageBox.confirm('Message', lang('Apakah anda mau menghapus data ini ?'), function(btn) {
                        if (btn == 'yes') {
                            Ext.Ajax.request({
                                waitMsg: lang('Please Wait'),
                                url: m_crud,
                                method: 'DELETE',
                                params: {
                                    id: smb.raw.sce_id
                                },
                                success: function(response, opts) {
                                    var obj = Ext.decode(response.responseText);
                                    switch (obj.success) {
                                        case true:
                                            store.load({
                                                params: {
                                                    key: Ext.getCmp('key').getValue(),
                                                    // kab: {
                                                    //     array: Ext.encode(Ext.getCmp('Kab').getValue())
                                                    // }
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
            }, {
                name: 'key', baseCls:'Sfr_TxtfieldSearchGrid',
                id: 'key',
                xtype: 'textfield',
                emptyText: lang('Cari berdasar nama/ID')
            }, {
                id: 'Kab',
                name: 'Kab[]',
                xtype: 'combo',
                width: 450,
                store: mc_Kabupaten,
                displayField: 'label',
                valueField: 'label',
                queryMode: 'local',
                selectOnFocus: true,
                multiSelect: true,
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
                            // kab: {
                            //     array: Ext.encode(Ext.getCmp('Kab').getValue())
                            // },
                        }
                    });
                }
            }]
        }],
        columns: [{
            text: 'No',
            xtype: 'rownumberer',
            width: 50
        }, {
            text: lang('SCE ID'),
            dataIndex: 'sce_id',
            width: 100
        }, {
            text: lang('Name'),
            dataIndex: 'FarmerName',
            width: '30%'
        }, {
            text: lang('Farmer ID'),
            dataIndex: 'FarmerID'
        }, {
            text: lang('Group Name'),
            dataIndex: 'GroupName',
            width: '14%'
        }, {
            text: lang('Desa'),
            dataIndex: 'Desa',
            width: '12%'
        }, {
            text: lang('As Buying Unit'),
            dataIndex: 'asBuyingUnit',
            width: '8%'
        }, {
            text: lang('Have Clonal'),
            dataIndex: 'haveClonal',
            width: '8%'
        }, {
            text: lang('Have Nursery'),
            dataIndex: 'haveNursery',
            width: '8%'
        },{
            text: lang('Have Compost'),
            dataIndex: 'haveCompost',
            width: '8%'
        }],
        listeners: {
            itemdblclick: function(dv, record, item, index, e) {
                var sm = record;
                //view for readonyl
                setFormSceView(sm);
            }
        }
    });
    //==================================== Form SCE (Begin) ==========================================================//
    //Autocomplete Search Farmer ====================== (begin)
    Ext.define("Post", {
        extend: 'Ext.data.Model',
        proxy: {
            type: 'ajax',
            url: m_crud + '_farmers',
            extraParams: {
                prov: m_param
            },
            reader: {
                type: 'json',
                root: 'data',
                totalProperty: 'total'
            }
        },
        fields: [{
            name: 'id',
            mapping: 'id'
        }, {
            name: 'name',
            mapping: 'name'
        }, {
            name: 'grup',
            mapping: 'grup'
        }, {
            name: 'sub_district',
            mapping: 'sub_district'
        }, {
            name: 'district',
            mapping: 'district'
        }, {
            name: 'province',
            mapping: 'province'
        }, {
            name: 'village',
            mapping: 'village'
        }, {
            name: 'photo',
            mapping: 'photo'
        }, {
            name: 'displayField',
            mapping: 'displayField'
        }, {
            name: 'address',
            mapping: 'address'
        }, {
            name: 'handphone',
            mapping: 'handphone'
        }, {
            name: 'status',
            mapping: 'status'
        }]
    });
    var ds = Ext.create('Ext.data.Store', {
        pageSize: 10,
        model: 'Post'
    });
    //Autocomplete Search Farmer ====================== (end)
    //store garden_status =============== (begin)
    Ext.define('gardenStatus.Model', {
        extend: 'Ext.data.Model',
        fields: ['FarmerID', 'GardenNr', 'GardenStatus', 'GardenHaUnCertified', 'Commodity', 'Remarks']
    });
    var store_garden_status = Ext.create('Ext.data.Store', {
        model: 'gardenStatus.Model',
        autoLoad: false,
        proxy: {
            type: 'ajax',
            url: m_farmer_garden,
            reader: {
                type: 'json'
            }
        }
    });
    //store garden_status =============== (end)
    //store staff =============== (begin)
    Ext.define('staff.Model', {
        extend: 'Ext.data.Model',
        fields: ['SceID', 'StaffID', 'UserId', 'StaffName', 'Phone', 'Email', 'StaffBirthday', 'StaffGender', 'StaffGende', 'Position'],
    });
    var store_staff = Ext.create('Ext.data.Store', {
        model: 'staff.Model',
        autoLoad: false,
        pageSize: 25,
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
    //store staff =============== (end)
    var DataForm = Ext.create('Ext.form.Panel', {
        frame: false,
        height: 350,
        width: '100%',
        bodyPadding: 5,
        autoScroll: true,
        fileUpload: true,
        id: 'dataForm',
        items: [{
            xtype: 'tabpanel',
            flex: 1,
            margin: 2,
            activeTab: 0,
            plain: true,
            items: [{
                xtype: 'panel',
                id: 'panel_general',
                autoScroll: true,
                title: lang('Data Umum'),
                padding: 5,
                style: 'border:2px solid #D6EDA4',
                items: [{
                    layout: 'column',
                    border: false,
                    items: [{
                        columnWidth: .5,
                        layout: 'form',
                        padding: 10,
                        items: [{
                            xtype: 'fieldset',
                            title: 'Basic Data',
                            height: '100%',
                            items: [{
                                xtype: 'textfield',
                                fieldLabel: lang('SCE ID'),
                                id: 'sce_id',
                                name: 'sce_id',
                                anchor: '100%',
                                readOnly: true
                            }, {
                                xtype: 'textfield',
                                id: 'FarmerID',
                                name: 'FarmerID',
                                inputType: 'hidden'
                            }, {
                                xtype: 'combo',
                                store: ds,
                                id: 'FarmerName',
                                name: 'FarmerName',
                                displayField: 'displayField',
                                fieldLabel: lang('Farmer'),
                                typeAhead: false,
                                hideTrigger: true,
                                anchor: '100%',
                                listConfig: {
                                    loadingText: 'Searching...',
                                    emptyText: 'No matching farmer found.',
                                    getInnerTpl: function() {
                                        return '<div class="search-item">' + '{id} - {name} ({grup})' + '{excerpt}' + '</div>';
                                    }
                                },
                                pageSize: 10,
                                listeners: {
                                    select: function(combo, selection) {
                                        var post = selection[0];
                                        if (post) {
                                            Ext.getCmp('FarmerID').setValue(post.get('id'));
                                            Ext.getCmp('FarmerName').setValue(post.get('displayField'));
                                            Ext.getCmp('FarmerGroup').setValue(post.get('grup'));
                                            Ext.getCmp('SubDistrict').setValue(post.get('sub_district'));
                                            Ext.getCmp('District').setValue(post.get('district'));
                                            Ext.getCmp('Province').setValue(post.get('province'));
                                            Ext.getCmp('Village').setValue(post.get('village'));
                                            Ext.getCmp('Address').setValue(post.get('address'));
                                            Ext.getCmp('Handphone').setValue(post.get('handphone'));
                                            Ext.getCmp('Status').setValue(post.get('status'));
                                            Ext.getCmp('ilogo').setSrc(m_photo + post.get('photo'));
                                            //photo
                                            var fotoFarmer = m_photo + post.get('photo');
                                            checkImageExists(fotoFarmer, function(existsImage) {
                                                if (existsImage == true) {
                                                    Ext.getCmp('ilogo').setSrc(fotoFarmer);
                                                } else {
                                                    Ext.getCmp('ilogo').setSrc(m_photo + 'no-user.jpg');
                                                }
                                            });
                                            //load garden
                                            store_garden_status.load({
                                                params: {
                                                    id: post.get('id')
                                                }
                                            });
                                        }
                                    }
                                }
                            }, {
                                xtype: 'textfield',
                                fieldLabel: lang('Province'),
                                id: 'Province',
                                name: 'Province',
                                readOnly: true,
                                anchor: '100%'
                            }, {
                                xtype: 'textfield',
                                fieldLabel: lang('District'),
                                id: 'District',
                                name: 'District',
                                readOnly: true,
                                anchor: '100%'
                            }, {
                                xtype: 'textfield',
                                fieldLabel: lang('Sub District'),
                                id: 'SubDistrict',
                                name: 'SubDistrict',
                                readOnly: true,
                                anchor: '100%'
                            }, {
                                xtype: 'textfield',
                                fieldLabel: lang('Village'),
                                id: 'Village',
                                name: 'Village',
                                readOnly: true,
                                anchor: '100%'
                            }, {
                                xtype: 'textfield',
                                fieldLabel: lang('Address'),
                                id: 'Address',
                                name: 'Address',
                                readOnly: true,
                                anchor: '100%'
                            }, {
                                xtype: 'textfield',
                                fieldLabel: lang('Farmer Group'),
                                id: 'FarmerGroup',
                                name: 'FarmerGroup',
                                readOnly: true,
                                anchor: '100%'
                            }, {
                                xtype: 'textfield',
                                fieldLabel: lang('Handphone'),
                                id: 'Handphone',
                                name: 'Handphone',
                                readOnly: true,
                                anchor: '100%'
                            }, {
                                xtype: 'textfield',
                                fieldLabel: lang('Status'),
                                id: 'Status',
                                name: 'Status',
                                readOnly: true,
                                anchor: '100%'
                            }]
                        }, {
                            xtype: 'fieldset',
                            title: 'Buying Unit Location',
                            height: '100%',
                            items: [{
                                xtype: 'textfield',
                                fieldLabel: lang('Latitude'),
                                id: 'Latitude',
                                name: 'Latitude',
                                anchor: '100%',
                                readOnly: m_hakakses_lat_short
                            }, {
                                xtype: 'textfield',
                                fieldLabel: lang('Longitude'),
                                id: 'Longitude',
                                name: 'Longitude',
                                anchor: '100%',
                                readOnly: m_hakakses_long_short
                            }]
                        }]
                    }, {
                        columnWidth: .5,
                        layout: 'form',
                        padding: 10,
                        style: 'margin-left:12px',
                        items: [{
                            xtype: 'fieldset',
                            title: 'Photo',
                            height: '100%',
                            width:'150px',
                            id: 'panelFoto',
                            items: [{
                                xtype: 'image',
                                id: 'ilogo',
                                width: '130px',
                                src: m_photo + 'no-user.jpg'
                            }]
                        }, {
                            xtype: 'fieldset',
                            title: 'Garden',
                            height: '100%',
                            items: [{
                                xtype: 'gridpanel',
                                id: 'gridGarden',
                                store: store_garden_status,
                                width: '100%',
                                minHeight: 150,
                                loadMask: true,
                                columns: [{
                                    text: lang('GardenNr'),
                                    dataIndex: 'GardenNr',
                                    width: '15%'
                                }, {
                                    text: lang('Ha'),
                                    dataIndex: 'GardenHaUnCertified',
                                    width: '15%'
                                }, {
                                    text: lang('Garden Status'),
                                    dataIndex: 'GardenStatus',
                                    width: '30%'
                                }, {
                                    text: lang('Remarks'),
                                    dataIndex: 'Remarks',
                                    width: '39%'
                                }]
                            }]
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
                        //event click
                        var form = this.up('form').getForm();
                        var methode;
                        if (Ext.getCmp('sce_id').getValue() != '') methode = 'PUT';
                        else methode = 'POST';
                        form.submit({
                            url: m_crud,
                            method: methode,
                            waitMsg: 'Sending data...',
                            success: function(fp, o) {
                                Ext.MessageBox.alert('Success', 'Data saved.');
                                //console.log(o.result.sce_id)
                                win.close();
                                store.load({
                                    params: {
                                        key: Ext.getCmp('key').getValue(),
                                        // kab: {
                                        //     array: Ext.encode(Ext.getCmp('District').getValue())
                                        // },
                                    }
                                });
                                //select filternya
                                // Ext.getCmp('Kab').setValue(Ext.getCmp('District').getValue());
                            },
                            failure: function(response, opts) {
                                Ext.MessageBox.alert('Failed', 'Saved failed.');
                            }
                        });
                    }
                }]
            }, {
                xtype: 'panel',
                id: 'panel_staff',
                autoScroll: true,
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
                    columns: [{
                        hidden: true,
                        dataIndex: 'UserId'
                    }, {
                        text: lang('ID'),
                        dataIndex: 'StaffID',
                        width: '5%'
                    }, {
                        text: lang('Nama Staff'),
                        dataIndex: 'StaffName',
                        width: '30%'
                    }, {
                        text: lang('Position'),
                        dataIndex: 'Position',
                        width: '10%'
                    }, {
                        text: lang('Phone'),
                        dataIndex: 'Phone',
                        width: '15%'
                    }, {
                        text: lang('Email'),
                        dataIndex: 'Email',
                        width: '20%'
                    }, {
                        text: lang('Birthday'),
                        dataIndex: 'StaffBirthday',
                        width: '10%'
                    }, {
                        text: lang('Kelamin'),
                        dataIndex: 'StaffGende',
                        width: '10%'
                    }]
                }]
            }]
        }],
        buttons: [{
            text: lang('Close'),
            margin: '5px',
            scale: 'large',
            ui: 's-button',
            cls: 's-grey',
            disabled: false,
            handler: function() {
                win.close();
            }
        }],
        setReadOnlyForAll: function(bReadOnly) {
            this.getForm().getFields().each(function(field) {
                field.setReadOnly(bReadOnly);
            });
        }
    });

    function setFormSceUpdate(sm) {
        //console.log(sm);
        var sce_id = sm.get('sce_id');
        //panggil ajax untuk dapat data edit id ini
        Ext.Ajax.request({
            url: m_sce_farmer_data,
            method: 'GET',
            waitMsg: lang('Please Wait'),
            params: {
                sce_id: sce_id
            },
            success: function(data) {
                var jsonResp = JSON.parse(data.responseText);
                Ext.getCmp('FarmerName').setReadOnly(true);
                Ext.getCmp('Longitude').setReadOnly(false);
                Ext.getCmp('Latitude').setReadOnly(false);
                displayFormWindow();
                Ext.getCmp('sce_id').setValue(jsonResp.sce_id);
                Ext.getCmp('FarmerID').setValue(jsonResp.id);
                Ext.getCmp('FarmerName').setValue(jsonResp.displayField);
                Ext.getCmp('FarmerGroup').setValue(jsonResp.grup);
                Ext.getCmp('SubDistrict').setValue(jsonResp.sub_district);
                Ext.getCmp('District').setValue(jsonResp.district);
                Ext.getCmp('Province').setValue(jsonResp.province);
                Ext.getCmp('Village').setValue(jsonResp.village);
                Ext.getCmp('Address').setValue(jsonResp.address);
                Ext.getCmp('Handphone').setValue(jsonResp.handphone);
                Ext.getCmp('Status').setValue(jsonResp.status);
                Ext.getCmp('Latitude').setValue(jsonResp.Latitude);
                Ext.getCmp('Longitude').setValue(jsonResp.Longitude);
                //photo
                var fotoFarmer = m_photo + jsonResp.photo;
                checkImageExists(fotoFarmer, function(existsImage) {
                    if (existsImage == true) {
                        Ext.getCmp('ilogo').setSrc(fotoFarmer);
                    } else {
                        Ext.getCmp('ilogo').setSrc(m_photo + 'no-user.jpg');
                    }
                });

                //load garden
                store_garden_status.load({
                    params: {
                        id: jsonResp.id
                    }
                });

                //laod staff
                store_staff.load({
                    params: {
                       id: jsonResp.sce_id
                    }
                });
            },
            failure: function() {
                Ext.MessageBox.show({
                    title: 'Failed',
                    msg: 'Could not connect to the database. Retry later',
                    buttons: Ext.MessageBox.OK,
                    animateTarget: 'mb9',
                    icon: 'ext-mb-error'
                });
            }
        });
    }

    function setFormSceView(sm) {
        var sce_id = sm.get('sce_id');
        //panggil ajax untuk dapat data edit id ini
        Ext.Ajax.request({
            url: m_sce_farmer_data,
            method: 'GET',
            waitMsg: lang('Please Wait'),
            params: {
                sce_id: sce_id
            },
            success: function(data) {
                var jsonResp = JSON.parse(data.responseText);
                DataForm.setReadOnlyForAll(true);
                Ext.getCmp('saveButton').setVisible(false);
                displayFormWindow();
                Ext.getCmp('sce_id').setValue(jsonResp.sce_id);
                Ext.getCmp('FarmerID').setValue(jsonResp.id);
                Ext.getCmp('FarmerName').setValue(jsonResp.displayField);
                Ext.getCmp('FarmerGroup').setValue(jsonResp.grup);
                Ext.getCmp('SubDistrict').setValue(jsonResp.sub_district);
                Ext.getCmp('District').setValue(jsonResp.district);
                Ext.getCmp('Province').setValue(jsonResp.province);
                Ext.getCmp('Village').setValue(jsonResp.village);
                Ext.getCmp('Address').setValue(jsonResp.address);
                Ext.getCmp('Handphone').setValue(jsonResp.handphone);
                Ext.getCmp('Status').setValue(jsonResp.status);
                Ext.getCmp('Latitude').setValue(jsonResp.Latitude);
                Ext.getCmp('Longitude').setValue(jsonResp.Longitude);
                //Ext.getCmp('ilogo').setSrc(m_photo + jsonResp.photo);
                //photo
                var fotoFarmer = m_photo + jsonResp.photo;
                checkImageExists(fotoFarmer, function(existsImage) {
                    if (existsImage == true) {
                        Ext.getCmp('ilogo').setSrc(fotoFarmer);
                    } else {
                        Ext.getCmp('ilogo').setSrc(m_photo + 'no-user.jpg');
                    }
                });

                //load garden
                store_garden_status.load({
                    params: {
                        id: jsonResp.id
                    }
                });

                //laod staff
                store_staff.load({
                    params: {
                       id: sce_id
                    }
                });
            },
            failure: function() {
                Ext.MessageBox.show({
                    title: 'Failed',
                    msg: 'Could not connect to the database. Retry later',
                    buttons: Ext.MessageBox.OK,
                    animateTarget: 'mb9',
                    icon: 'ext-mb-error'
                });
            }
        });
    }
    var win = Ext.create('widget.window', {
        title: lang('Professional Farmer'),
        id: 'win',
        closable: true,
        modal: true,
        closeAction: 'show',
        width: '90%',
        height: '90%',
        layout: {
            type: 'fit'
        },
        items: [DataForm]
    });

    function displayFormWindow() {
        if (!win.isVisible()) {
            DataForm.getForm().reset();
            //Ext.getCmp('ilogo').setSrc('');
            win.center();
            win.show();
        } else {
            win.close();
        }
    }
    //==================================== Form SCE (End) ==========================================================//
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
}); // Ext.onReady
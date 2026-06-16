Ext.Loader.setConfig({enabled: true});
Ext.Loader.setPath('js/ext-4.2.0.663', '../ux');
//Ext.Loader.setPath('js/ext-4.2.0.663/ux/form');
Ext.require([
    //'Ext.form.Panel',
    //'Ext.ux.form.MultiSelect',
    'Ext.ux.form.ItemSelector'
]);

var form_data = {};
Ext.onReady(function () {
    Ext.tip.QuickTipManager.init();
    var selected_role = null;
    var store = Ext.create('Ext.data.Store', {
        extend: 'Ext.data.Model',
        fields: ['IMSID', 'HolderName', 'CertProgName', 'CertBodyName', 'ContactName', 'CertificationStart', 'CertificationEnd', 'ExtensionDate', 'SurveyNr', 'Year', 'FirstBuyer'],
        autoLoad: true,
        pageSize: 50,
        proxy: {
            type: 'ajax',
            url: m_crud + 'data',
            reader: {
                type: 'json',
                root: 'data',
                totalProperty: 'total'
            }
        },
        listeners: {
            beforeload: function(store, operation) {
                store.proxy.extraParams.key = Ext.getCmp('imskey').getValue();
                //store.proxy.extraParams.holderType = Ext.getCmp('holderType').getValue();
            }
        }
    });
    
    var store_farmer = Ext.create('Ext.data.Store', {
        extend: 'Ext.data.Model',
        fields: ['IMSID','FarmerID', 'FarmerName', 'GardenNr', 'SurveyNr', 'SurveyName', 'FarmerGroup', 'Village', 'ha', 'production'],
        autoLoad: true,
        pageSize: 50,
        proxy: {
            type: 'ajax',
            url: m_crud + 'farmers',
            reader: {
                type: 'json',
                root: 'data',
                totalProperty: 'total'
            }
        },
        listeners: {
            beforeload: function(store, operation) {
                store.proxy.extraParams.IMSID = Ext.getCmp('imsIMSID').getValue();
                //store.proxy.extraParams.key = Ext.getCmp('imsFkey').getValue();
                //store.proxy.extraParams.holderType = Ext.getCmp('holderType').getValue();
            }
        }
    });
    
    var store_afl = Ext.create('Ext.data.Store', {
        extend: 'Ext.data.Model',
        fields: ['AFLID', 'IMSID', 'FarmerID', 'FarmerName', 'AFLStatus', 'CertYear', 'CertFirstYear', 'CertHarvest', 'CertNextHarvest', 'CertHectare', 'CertFarmNr'],
        autoLoad: true,
        pageSize: 50,
        proxy: {
            type: 'ajax',
            url: m_crud + 'afls',
            reader: {
                type: 'json',
                root: 'data',
                totalProperty: 'total'
            }
        },
        listeners: {
            beforeload: function(store, operation) {
                store.proxy.extraParams.IMSID = Ext.getCmp('imsIMSID').getValue();
                //store.proxy.extraParams.key = Ext.getCmp('imsFkey').getValue();
                //store.proxy.extraParams.holderType = Ext.getCmp('holderType').getValue();
            }
        }
    });
    
    var store_staff = Ext.create('Ext.data.Store', {
        extend: 'Ext.data.Model',
        fields: ['IMSStaffID', 'IMSID', 'StaffID', 'StaffName', 'StaffEmail', 'StaffWorkArea', 'Gender'],
        autoLoad: true,
        pageSize: 50,
        proxy: {
            type: 'ajax',
            url: m_crud + 'staffs',
            reader: {
                type: 'json',
                root: 'data',
                totalProperty: 'total'
            }
        },
        listeners: {
            beforeload: function(store, operation) {
                store.proxy.extraParams.IMSID = Ext.getCmp('imsIMSID').getValue();
                //store.proxy.extraParams.key = Ext.getCmp('imsFkey').getValue();
                //store.proxy.extraParams.holderType = Ext.getCmp('holderType').getValue();
            }
        }
    });
    
    var store_farmer_add = Ext.create('Ext.data.Store', {
        extend: 'Ext.data.Model',
        fields: [{name: 'addFarmerID'}, {name: 'addFarmerName'}, {name: 'addGardenNr'}, {name: 'FarmerGroup'}, {name: 'Village'}, {name: 'ha'}, {name: 'production'}],
        //pageSize: 10,
        autoLoad: false,
        proxy: {
            type: 'ajax',
            url: m_crud + 'farmer_add_list',
            reader: {
                type: 'json',
                root: 'data',
                totalProperty: 'total'
            }
        }
    });
    
    var store_afl_add = Ext.create('Ext.data.Store', {
        extend: 'Ext.data.Model',
        fields: [{name: 'AFLIDafl'}, {name: 'AFLFarmerID'}, {name: 'AFLFarmerName'}, {name: 'AFLGroupName'}],
        //pageSize: 10,
        autoLoad: false,
        proxy: {
            type: 'ajax',
            url: m_crud + 'afl_add_list',
            reader: {
                type: 'json',
                root: 'data',
                totalProperty: 'total'
            }
        }
    });
    
    var store_staff_add = Ext.create('Ext.data.Store', {
        extend: 'Ext.data.Model',
        fields: ['addStaffID', 'addStaffName', 'addStaffEmail', 'addStaffWorkArea', 'addGender'],
        //pageSize: 10,
        autoLoad: false,
        proxy: {
            type: 'ajax',
            url: m_crud + 'staff_add_list',
            reader: {
                type: 'json',
                root: 'data',
                totalProperty: 'total'
            }
        }
    });
    
    var holders = Ext.create('Ext.data.Store', {
        extend: 'Ext.data.Model',
        fields: ['id','label'],
        autoLoad: false,
        // pageSize: 10,
        proxy: {
            type: 'ajax',
            url: m_crud + 'holders',
            reader: {
                type: 'json',
                root: 'data'
            }
        }
    });
    
    var programs = Ext.create('Ext.data.Store', {
        extend: 'Ext.data.Model',
        fields: ['id','label'],
        autoLoad: false,
        // pageSize: 10,
        proxy: {
            type: 'ajax',
            url: m_crud + 'programs',
            reader: {
                type: 'json',
                root: 'data'
            }
        }
    });
    
    var cert_body = Ext.create('Ext.data.Store', {
        extend: 'Ext.data.Model',
        fields: ['id','label'],
        autoLoad: false,
        // pageSize: 10,
        proxy: {
            type: 'ajax',
            url: m_crud + 'cert_body',
            reader: {
                type: 'json',
                root: 'data'
            }
        }
    });
    
    var cert_body_contact = Ext.create('Ext.data.Store', {
        extend: 'Ext.data.Model',
        fields: ['id','label'],
        autoLoad: false,
        // pageSize: 10,
        proxy: {
            type: 'ajax',
            url: m_crud + 'cert_body_contact',
            reader: {
                type: 'json',
                root: 'data'
            }
        }
    });
    
    var first_buyer = Ext.create('Ext.data.Store', {
        extend: 'Ext.data.Model',
        fields: ['id','label'],
        autoLoad: false,
        // pageSize: 10,
        proxy: {
            type: 'ajax',
            url: m_crud + 'first_buyer',
            reader: {
                type: 'json',
                root: 'data'
            }
        }
    });
    
    var surveys = Ext.create('Ext.data.Store', {
        extend: 'Ext.data.Model',
        fields: ['id','label'],
        autoLoad: false,
        // pageSize: 10,
        proxy: {
            type: 'ajax',
            url: m_crud + 'surveys',
            reader: {
                type: 'json',
                root: 'data'
            }
        }
    });
    
    var province = Ext.create('Ext.data.Store', {
        extend: 'Ext.data.Model',
        fields: ['id','label'],
        autoLoad: false,
        // pageSize: 10,
        proxy: {
            type: 'ajax',
            url: m_crud + 'province',
            reader: {
                type: 'json',
                root: 'data'
            }
        }
    });
    
    var staff_province = Ext.create('Ext.data.Store', {
        extend: 'Ext.data.Model',
        fields: ['id','label'],
        autoLoad: false,
        proxy: {
            type: 'ajax',
            url: m_crud + 'staff_province',
            reader: {
                type: 'json',
                root: 'data'
            }
        }
    });
    staff_province.load();
    
    var district = Ext.create('Ext.data.Store', {
        extend: 'Ext.data.Model',
        fields: ['id','label'],
        autoLoad: false,
        // pageSize: 10,
        proxy: {
            type: 'ajax',
            url: m_crud + 'district',
            reader: {
                type: 'json',
                root: 'data'
            }
        }
    });
    
    var work_area = Ext.create('Ext.data.Store', {
        extend: 'Ext.data.Model',
        fields: ['id','label'],
        autoLoad: false,
        // pageSize: 10,
        proxy: {
            type: 'ajax',
            url: m_crud + 'work_area',
            reader: {
                type: 'json',
                root: 'data'
            }
        }
    });
    
    var subdistrict = Ext.create('Ext.data.Store', {
        extend: 'Ext.data.Model',
        fields: ['id','label'],
        autoLoad: false,
        // pageSize: 10,
        proxy: {
            type: 'ajax',
            url: m_crud + 'subdistrict',
            reader: {
                type: 'json',
                root: 'data'
            }
        }
    });
    
    var village = Ext.create('Ext.data.Store', {
        extend: 'Ext.data.Model',
        fields: ['id','label'],
        autoLoad: false,
        // pageSize: 10,
        proxy: {
            type: 'ajax',
            url: m_crud + 'village',
            reader: {
                type: 'json',
                root: 'data'
            }
        }
    });
    
    var staff_type = Ext.create('Ext.data.Store', {
        fields: ['id', 'label'],
        data: [{
            "id": "program",
            "label": "Program Staff"
        }, {
            "id": "private",
            "label": "Private Staff"
        }, {
            "id": "extension",
            "label": "Extension Staff"
        }, {
            "id": "sce",
            "label": "SCE Staff"
        }, {
            "id": "trader",
            "label": "Trader Staff"
        }, {
            "id": "cooperative",
            "label": "Cooperative Staff"
        }, {
            "id": "warehouse",
            "label": "Warehouse Staff"
        }, {
            "id": "bank",
            "label": "Bank Staff"
        }, {
            "id": "farmergroup",
            "label": "Farmer Group Staff"
        }]
    });

    function displayFormWindow(editable) {
        if (!win.isVisible()) {
            //resetForm();
            win.show();
        } else {
            win.hide(this, function () {
            });
            win.toFront();
        }
    }

    function set_form_value(data) {
        form_data = data;
        Ext.getCmp('imsdataForm').getForm().reset();
        if(data) {
            Ext.getCmp('imsIMSID').setValue(data.IMSID);
            Ext.getCmp('imsSupplychainID').setValue(data.SupplychainID);
            Ext.getCmp('imsCertHolderID').setValue(data.CertHolderID);
            Ext.getCmp('imsCertBodyID').setValue(data.CertBodyID);
            Ext.getCmp('imsCertBodyContactID').setValue(data.CertBodyContactID);
            Ext.getCmp('imsFirstBuyerID').setValue(data.FirstBuyerID);
            Ext.getCmp('imsSurveyNr').setValue(data.SurveyNr);
            Ext.getCmp('imsdefaultSurveyNr').setValue(data.SurveyNr);
            Ext.getCmp('imsYear').setValue(data.Year);
            Ext.getCmp('imsCertificationStart').setValue(data.CertificationStart);
            Ext.getCmp('imsCertificationEnd').setValue(data.CertificationEnd);
            Ext.getCmp('imsInternalStart').setValue(data.InternalStart);
            Ext.getCmp('imsInternalEnd').setValue(data.InternalEnd);
            Ext.getCmp('imsExternalDate').setValue(data.ExternalDate);
            Ext.getCmp('imsExternalStart').setValue(data.ExternalStart);
            Ext.getCmp('imsExternalEnd').setValue(data.ExternalEnd);
            Ext.getCmp('imsExtensionDate').setValue(data.ExtensionDate);
            Ext.getCmp('imsValidityStart').setValue(data.ValidityStart);
            Ext.getCmp('imsValidityEnd').setValue(data.ValidityStart);
            if(data.afl!='0'){
                set_tanggal('true');
            }else{
                set_tanggal('false');
            }
            store_farmer.load({
                params: {
                    IMSID: data.IMSID
                }
            });
            store_afl.load({
                params: {
                    IMSID: data.IMSID
                }
            });
            store_staff.load({
                params: {
                    IMSID: data.IMSID
                }
            });
            cert_body_contact.load({
                params: {
                    CertBodyID: data.CertBodyID
                }
            });
        } else {            
            //Ext.getCmp('NationalityNm_local').setValue(true);
            Ext.getCmp('imsCertBodyContactID').setValue();
        }
    }
    
    function set_tanggal(key){
        if(key=='true'){
            Ext.getCmp('imsCertificationStart').enable();
            Ext.getCmp('imsCertificationEnd').enable();
            Ext.getCmp('imsInternalStart').enable();
            Ext.getCmp('imsInternalEnd').enable();
            Ext.getCmp('imsExternalDate').enable();
            Ext.getCmp('imsExternalStart').enable();
            Ext.getCmp('imsExternalEnd').enable();
            Ext.getCmp('imsExtensionDate').enable();
            Ext.getCmp('imsValidityStart').enable();
            Ext.getCmp('imsValidityEnd').enable();
        }else{
            Ext.getCmp('imsCertificationStart').disable();
            Ext.getCmp('imsCertificationEnd').disable();
            Ext.getCmp('imsInternalStart').disable();
            Ext.getCmp('imsInternalEnd').disable();
            Ext.getCmp('imsExternalDate').disable();
            Ext.getCmp('imsExternalStart').disable();
            Ext.getCmp('imsExternalEnd').disable();
            Ext.getCmp('imsExtensionDate').disable();
            Ext.getCmp('imsValidityStart').disable();
            Ext.getCmp('imsValidityEnd').disable();
        }
    }
    
    holders.load();
    cert_body.load();
    first_buyer.load();
    surveys.load();
    province.load();
    
    var DataForm = Ext.create('Ext.form.Panel', {
        height: 659,
        autoScroll: true,
        width: 1014,
        id: 'imsdataForm',
        fieldDefaults: {
            labelAlign: 'left',
            labelWidth: 150,
            anchor: '100%'
        },
        items: [{
            layout: 'column',
            border: false,
            items: [{
                columnWidth: .5,
                layout: 'form',
                padding: 5,
                border: false,
                items: [{
                        xtype: 'hiddenfield',
                        id: 'imsIMSID',
                        name: 'IMSID',
                    }, {
                        xtype: 'combobox',
                        fieldLabel: lang('Holder Name'),
                        id: 'imsSupplychainID',
                        name: 'SupplychainID',
                        store: holders,
                        allowBlank: false,
                        queryMode: 'local',
                        displayField: 'label',
                        valueField: 'id',
                        listeners: {
                            change: function (cb, nv, ov) {
                                Ext.getCmp('imsCertHolderID').setValue('');
                                programs.load({
                                    params: {
                                        SupplychainID: Ext.getCmp('imsSupplychainID').getValue()
                                    }
                                });
                            }
                        }
                    }, {
                        xtype: 'combobox',
                        fieldLabel: lang('Program Name'),
                        id: 'imsCertHolderID',
                        name: 'CertHolderID',
                        store: programs,
                        allowBlank: false,
                        queryMode: 'local',
                        displayField: 'label',
                        valueField: 'id',
                        listeners: {
                            change: function (cb, nv, ov) {
                                //Ext.getCmp('SupplychainID').setValue('');
                                Ext.Ajax.request({
                                    url: m_crud + 'set_holder',
                                    method: 'GET',
                                    params: {CertHolderID: Ext.getCmp('imsCertHolderID').getValue()},
                                    success: function (fp, o) {
                                        var data = Ext.decode(fp.responseText);
                                        //Ext.getCmp('CertProgName').setValue(data.CertProgName);
                                        Ext.getCmp('imsGIPNumber').setValue(data.GIPNumber);
                                        Ext.getCmp('imsCertProgMemberID').setValue(data.CertProgMemberID);
                                        Ext.getCmp('imsCertProgMemberDate').setValue(data.CertProgMemberDate);
                                    },
                                    failure: function (response, opts) {
                                        Ext.MessageBox.alert('error', 'Could not connect to the database. Retry later');
                                    }
                                });
                            }
                        }
                    }, {
                        xtype: 'textfield',
                        id: 'imsGIPNumber',
                        name: 'GIPNumber',
                        fieldLabel: lang('GIP Number'),
                        readOnly: true
                    }, {
                        xtype: 'textfield',
                        id: 'imsCertProgMemberID',
                        name: 'CertProgMemberID',
                        fieldLabel: lang('Program Member ID'),
                        readOnly: true
                    }, {
                        xtype: 'textfield',
                        id: 'imsCertProgMemberDate',
                        name: 'CertProgMemberDate',
                        fieldLabel: lang('Program Member Date'),
                        readOnly: true
                    }, {
                        xtype: 'combobox',
                        fieldLabel: lang('Certification Body'),
                        id: 'imsCertBodyID',
                        name: 'CertBodyID',
                        store: cert_body,
                        //allowBlank: false,
                        queryMode: 'local',
                        displayField: 'label',
                        valueField: 'id',
                        listeners: {
                            change: function (cb, nv, ov) {
                                cert_body_contact.load({
                                    params: {
                                        CertBodyID: Ext.getCmp('imsCertBodyID').getValue()
                                    }
                                });
                            }
                        }
                    }, {
                        xtype: 'combobox',
                        fieldLabel: lang('Contact / Staff'),
                        id: 'imsCertBodyContactID',
                        name: 'CertBodyContactID',
                        store: cert_body_contact,
                        //allowBlank: false,
                        queryMode: 'local',
                        displayField: 'label',
                        valueField: 'id',
                        listeners: {
                            change: function (cb, nv, ov) {
                                Ext.Ajax.request({
                                    url: m_crud + 'set_body',
                                    method: 'GET',
                                    params: {CertBodyContactID: Ext.getCmp('imsCertBodyContactID').getValue()},
                                    success: function (fp, o) {
                                        var data = Ext.decode(fp.responseText);
                                        Ext.getCmp('imsContactEmail').setValue(data.ContactEmail);
                                        Ext.getCmp('imsContactPhone').setValue(data.ContactPhone);
                                        Ext.getCmp('imsContactAddress').setValue(data.ContactAddress);
                                    },
                                    failure: function (response, opts) {
                                        Ext.MessageBox.alert('error', 'Could not connect to the database. Retry later');
                                    }
                                });
                            }
                        }
                    }, {
                        xtype: 'textfield',
                        id: 'imsContactEmail',
                        name: 'ContactEmail',
                        fieldLabel: lang('Contact Email'),
                        readOnly: true
                    }, {
                        xtype: 'textfield',
                        id: 'imsContactPhone',
                        name: 'ContactPhone',
                        fieldLabel: lang('Contact Phone'),
                        readOnly: true
                    }, {
                        xtype: 'textfield',
                        id: 'imsContactAddress',
                        name: 'ContactAddress',
                        fieldLabel: lang('Contact Address'),
                        readOnly: true
                    }]
            }, {
                columnWidth: .5,
                layout: 'form',
                padding: 5,
                border: false,
                items: [{
                    xtype: 'combobox',
                    fieldLabel: lang('First Buyer'),
                    id: 'imsFirstBuyerID',
                    name: 'FirstBuyerID',
                    store: first_buyer,
                    //allowBlank: false,
                    queryMode: 'local',
                    displayField: 'label',
                    valueField: 'id'
                }, {
                    xtype: 'combobox',
                    fieldLabel: lang('Survey Number'),
                    id: 'imsSurveyNr',
                    name: 'SurveyNr',
                    store: surveys,
                    allowBlank: false,
                    queryMode: 'local',
                    displayField: 'label',
                    valueField: 'id'
                }, {
                    xtype: 'hiddenfield',
                    id: 'imsdefaultSurveyNr',
                    name: 'defaultSurveyNr',
                }, {
                    xtype: 'numberfield',
                    fieldLabel: lang('Year Number'),
                    minValue: 1,
                    id: 'imsYear',
                    name: 'Year',
                    format: 'Y-m-d'
                }, {
                    xtype: 'datefield',
                    fieldLabel: lang('Certification Start'),
                    id: 'imsCertificationStart',
                    name: 'CertificationStart',
                    disabled: true,
                    format: 'Y-m-d'
                }, {
                    xtype: 'datefield',
                    fieldLabel: lang('Certification End'),
                    id: 'imsCertificationEnd',
                    name: 'CertificationEnd',
                    disabled: true,
                    format: 'Y-m-d'
                }, {
                    xtype: 'datefield',
                    fieldLabel: lang('Internal Audit Start'),
                    id: 'imsInternalStart',
                    name: 'InternalStart',
                    disabled: true,
                    format: 'Y-m-d'
                }, {
                    xtype: 'datefield',
                    fieldLabel: lang('Internal Audit End'),
                    id: 'imsInternalEnd',
                    name: 'InternalEnd',
                    disabled: true,
                    format: 'Y-m-d'
                }, {
                    xtype: 'datefield',
                    fieldLabel: lang('External Audit Start'),
                    id: 'imsExternalStart',
                    name: 'ExternalStart',
                    format: 'Y-m-d'
                }, {
                    xtype: 'datefield',
                    fieldLabel: lang('External Audit End'),
                    id: 'imsExternalEnd',
                    name: 'ExternalEnd',
                    disabled: true,
                    format: 'Y-m-d'
                }, {
                    xtype: 'datefield',
                    fieldLabel: lang('Extension Date'),
                    id: 'imsExtensionDate',
                    name: 'ExtensionDate',
                    disabled: true,
                    format: 'Y-m-d'
                }, {
                    xtype: 'datefield',
                    fieldLabel: lang('Validity Start'),
                    id: 'imsValidityStart',
                    name: 'ValidityStart',
                    disabled: true,
                    format: 'Y-m-d'
                }, {
                    xtype: 'datefield',
                    fieldLabel: lang('Validity End'),
                    id: 'imsValidityEnd',
                    name: 'ValidityEnd',
                    disabled: true,
                    format: 'Y-m-d'
                }, {
                    xtype: 'datefield',
                    fieldLabel: lang('Certification Issued Date'),
                    id: 'imsExternalDate',
                    name: 'ExternalDate',
                    format: 'Y-m-d'
                }]
            }]
        }, {
            xtype: 'tabpanel',
            flex: 1,
            margin:2,
            activeTab: 0,
            plain: true,
            items: [{ // grid nursery penjualan
                xtype: 'gridpanel',
                title: lang('Farmer'),
                id: 'imsgFarmer',
                style: 'border:1px solid #CCC;',
                store: store_farmer,
                width: '100%',
                height: 500,
                loadMask: true,
                selType: 'rowmodel',
                minHeight:190,
                listeners: {
                    itemclick: function(view, record, item, index, e){
                       contextMenuFarmerGrid.showAt(e.getXY());
                    }
                },
                dockedItems: [{
                    xtype: 'toolbar',
                    items: [{
                        icon: varjs.config.base_url + 'images/icons/new/add.png', cls:'Sfr_BtnGridGreen', overCls:'Sfr_BtnGridGreen-Hover',
                        //cls: m_act_save,
                        text: lang('Add'),
                        scope: this,
                        handler: function() {
                            if(Ext.getCmp('imsIMSID').getValue()==''){
                                Ext.MessageBox.alert('Warning', 'Please save IMS first!');
                            }else{
                                Ext.getCmp('imsFAProvinceID').setValue('');
                                store_farmer_add.load({
                                    params: {
                                        key: 'removeGrid'
                                    }
                                });
                                displayAddWindowFarmer();
                            }
                        }
                    }, {
                        xtype: 'textfield',
                        name: 'imsFkey',
                        id: 'imsFkey',
                        emptyText: lang('Farmer ID / Farmer Name'),
                        width: 280,
                        listeners: {}
                    }, {
                        xtype: 'button',
                        icon: varjs.config.base_url + 'images/icons/silk/search.png',
                        margin: '0px 0px 0px 6px',
                        text: lang('Search'),
                        handler: function() {
                            store_farmer.load({
                                params: {
                                    IMSID: Ext.getCmp('imsIMSID').getValue(),
                                    key: Ext.getCmp('imsFkey').getValue()
                                }
                            });
                        }
                    }]
                }, {
                    xtype: 'pagingtoolbar',
                    store: store_farmer,   // same store GridPanel is using
                    dock: 'bottom',
                    displayInfo: true
                }],
                columns: [{
                    text: 'ID',
                    dataIndex: 'IMSID',
                    hidden: true
                },
                {
                    text: 'FarmerID',
                    dataIndex: 'FarmerID',
                    hidden: true
                },
                {
                    text: 'SurveyNr',
                    dataIndex: 'SurveyNr',
                    hidden: true
                },
                {
                    text: 'No',
                    xtype: 'rownumberer',
                    align: 'center',
                    width: 50
                },
                {
                    text: lang('Farmer ID'),
                    flex: 1,
                    dataIndex: 'FarmerID'
                },
                {
                    text: lang('Farmer Name'),
                    flex: 2,
                    dataIndex: 'FarmerName'
                },
                {
                    text: lang('Farmer Group'),
                    flex: 2,
                    dataIndex: 'FarmerGroup'
                },
                {
                    text: lang('Village'),
                    flex: 2,
                    dataIndex: 'Village'
                },
                {
                    text: lang('GardenNr'),
                    flex: 1,
                    dataIndex: 'GardenNr'
                },
                {
                    text: lang('Area of Garden (Ha)'),
                    dataIndex: 'ha',
                    flex: 1
                }, {
                    text: lang('Estimated production (Kg/Year)'),
                    dataIndex: 'production',
                    flex: 2
                }]
            }, {
                xtype: 'gridpanel',
                title: lang('AFL'),
                id: 'imsgAFL',
                style: 'border:1px solid #CCC;',
                store: store_afl,
                width: '100%',
                height: 500,
                loadMask: true,
                selType: 'rowmodel',
                minHeight:190,
                listeners: {
                    itemclick: function(view, record, item, index, e){
                       contextMenuAFLGrid.showAt(e.getXY());
                    }
                },
                dockedItems: [{
                    xtype: 'toolbar',
                    items: [/*{
                        icon: varjs.config.base_url + 'images/icons/new/add.png', cls:'Sfr_BtnGridGreen', overCls:'Sfr_BtnGridGreen-Hover',
                        //cls: m_act_save,
                        text: lang('Add'),
                        scope: this,
                        handler: function() {
                            if(Ext.getCmp('imsIMSID').getValue()==''){
                                Ext.MessageBox.alert('Warning', 'Please save IMS first!');
                            }else{
                                //Ext.getCmp('imsFAProvinceID').setValue('');
                                store_afl_add.load({
                                    params: {
                                        key: 'removeGrid'
                                    }
                                });
                                displayAddWindowAFL();
                            }
                        }
                    }*/{
                        icon: varjs.config.base_url + 'images/icons/new/add.png', cls:'Sfr_BtnGridGreen', overCls:'Sfr_BtnGridGreen-Hover',
                        //cls: m_act_save,
                        text: lang('Generate AFL'),
                        scope: this,
                        handler: function() {
                            if(Ext.getCmp('imsIMSID').getValue()==''){
                                Ext.MessageBox.alert('Warning', 'Please save IMS first!');
                            }else{
                                Ext.MessageBox.confirm('Message', lang('Apakah anda yakin untuk generate data AFL?'), function (btn) {
                                    if (btn == 'yes') {
                                        Ext.MessageBox.show({
                                            msg: 'Loading, please wait...',
                                            progressText: 'Saving...',
                                            width:300,
                                            wait:true,
                                            waitConfig: {interval:200},
                                            icon:'ext-mb-download', //custom class in msg-box.html
                                            iconHeight: 50,
                                            animateTarget: 'mb7'
                                        });
                                        Ext.Ajax.request({
                                            url: m_crud + 'generate_afl',
                                            method: 'POST',
                                            waitMsg: lang('Sending data...'),
                                            params: {
                                                IMSID: Ext.getCmp('imsIMSID').getValue()
                                            },
                                            success: function(response, opts) {
                                                Ext.MessageBox.hide();
                                                var obj = Ext.decode(response.responseText);
                                                switch (obj.success) {
                                                    case true:
                                                        Ext.MessageBox.alert('Success', obj.message);
                                                        if(obj.total!='0'){
                                                            set_tanggal('true');
                                                        }
                                                        store_afl.load({
                                                            params: {
                                                                IMSID: Ext.getCmp('imsIMSID').getValue(),
                                                                key: Ext.getCmp('imsAFLkey').getValue()
                                                            }
                                                        });
                                                        break;
                                                    default:
                                                        Ext.MessageBox.alert('Warning', obj.message);
                                                        break;
                                                }
                                            },
                                            failure : function(){
                                                Ext.MessageBox.hide();
                                                Ext.MessageBox.alert('error', 'Could not connect to the database. Retry later');
                                            }
                                        });
                                    }
                                });
                            }
                        }
                    }, /*{
                        icon: varjs.config.base_url + 'images/icons/silk/page_excel.png',
                        //cls: m_act_save,
                        text: lang('Excel'),
                        scope: this,
                        handler: function() {
                            if(Ext.getCmp('imsIMSID').getValue()==''){
                                Ext.MessageBox.alert('Warning', 'Please save IMS first!');
                            }else{
                                Ext.MessageBox.confirm('Message', lang('Apakah anda yakin untuk download (Excel) data AFL?'), function (btn) {
                                    if (btn == 'yes') {
                                        Ext.MessageBox.show({
                                            msg: 'Loading, please wait...',
                                            progressText: 'Saving...',
                                            width:300,
                                            wait:true,
                                            waitConfig: {interval:200},
                                            icon:'ext-mb-download', //custom class in msg-box.html
                                            iconHeight: 50,
                                            animateTarget: 'mb7'
                                        });
                                        
                                        url = m_crud + 'print_afl/'+ Ext.getCmp('imsIMSID').getValue();
                                        if(window.open(url, 'cetak', "height=200,width=200")){
                                            Ext.MessageBox.hide();
                                        }
                                    }
                                });
                            }
                        }
                    },*/ {
                        xtype: 'splitbutton',
                        text: 'Export',
                        menu: {
                            items: [{
                                icon: varjs.config.base_url + 'images/icons/silk/page_excel.png',
                                text: 'Excel',
                                menu: {
                                    items: [{
                                        text: lang('Farmer AFL'),
                                        handler: function () {
                                            if(Ext.getCmp('imsIMSID').getValue()==''){
                                                Ext.MessageBox.alert('Warning', 'Please save IMS first!');
                                            }else{
                                                Ext.MessageBox.confirm('Message', lang('Apakah anda yakin untuk download (Excel) data Farmer AFL?'), function (btn) {
                                                    if (btn == 'yes') {
                                                        Ext.MessageBox.show({
                                                            msg: 'Loading, please wait...',
                                                            progressText: 'Saving...',
                                                            width:300,
                                                            wait:true,
                                                            waitConfig: {interval:200},
                                                            icon:'ext-mb-download', //custom class in msg-box.html
                                                            iconHeight: 50,
                                                            animateTarget: 'mb7'
                                                        });
                                                        
                                                        url = m_crud + 'print_afl/'+ Ext.getCmp('imsIMSID').getValue();
                                                        if(window.open(url, 'cetak', "height=200,width=200")){
                                                            Ext.MessageBox.hide();
                                                        }
                                                    }
                                                });
                                            }
                                        }
                                    }, {
                                        text: lang('Farmer Garden Check'),
                                        handler: function () {
                                            if(Ext.getCmp('imsIMSID').getValue()==''){
                                                Ext.MessageBox.alert('Warning', 'Please save IMS first!');
                                            }else{
                                                Ext.MessageBox.confirm('Message', lang('Apakah anda yakin untuk download (Excel) data Farmer Garden Check?'), function (btn) {
                                                    if (btn == 'yes') {
                                                        Ext.MessageBox.show({
                                                            msg: 'Loading, please wait...',
                                                            progressText: 'Saving...',
                                                            width:300,
                                                            wait:true,
                                                            waitConfig: {interval:200},
                                                            icon:'ext-mb-download', //custom class in msg-box.html
                                                            iconHeight: 50,
                                                            animateTarget: 'mb7'
                                                        });
                                                        
                                                        url = m_crud + 'print_garden_check/'+ Ext.getCmp('imsIMSID').getValue();
                                                        if(window.open(url, 'cetak', "height=200,width=200")){
                                                            Ext.MessageBox.hide();
                                                        }
                                                    }
                                                });
                                            }
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
                        xtype: 'textfield',
                        name: 'imsAFLkey',
                        id: 'imsAFLkey',
                        emptyText: lang('Farmer ID / Farmer Name'),
                        width: 280,
                        listeners: {}
                    }, {
                        xtype: 'button',
                        icon: varjs.config.base_url + 'images/icons/silk/search.png',
                        margin: '0px 0px 0px 6px',
                        text: lang('Search'),
                        handler: function() {
                            store_afl.load({
                                params: {
                                    IMSID: Ext.getCmp('imsIMSID').getValue(),
                                    key: Ext.getCmp('imsAFLkey').getValue()
                                }
                            });
                        }
                    }]
                }, {
                    xtype: 'pagingtoolbar',
                    store: store_afl,   // same store GridPanel is using
                    dock: 'bottom',
                    displayInfo: true
                }],
                columns: [{
                    text: 'AFLID',
                    dataIndex: 'AFLID',
                    hidden: true
                },
                {
                    text: 'ID',
                    dataIndex: 'IMSID',
                    hidden: true
                },
                {
                    text: 'FarmerID',
                    dataIndex: 'FarmerID',
                    hidden: true
                },
                {
                    text: 'No',
                    xtype: 'rownumberer',
                    align: 'center',
                    width: 50,
                },
                {
                    text: lang('Farmer Name'),
                    flex: 3,
                    dataIndex: 'FarmerName'
                },
                {
                    text: lang('Status'),
                    flex: 1,
                    dataIndex: 'AFLStatus'
                },
                {
                    text: lang('Farm Number'),
                    flex: 1,
                    dataIndex: 'CertFarmNr'
                },
                {
                    text: lang('Year Number'),
                    flex: 1,
                    dataIndex: 'CertYear'
                },
                {
                    text: lang('Year'),
                    flex: 1,
                    dataIndex: 'CertYear'
                },
                {
                    text: lang('First Year'),
                    flex: 1,
                    dataIndex: 'CertFirstYear'
                },
                {
                    text: lang('Harvest'),
                    flex: 1,
                    dataIndex: 'CertHarvest'
                },
                {
                    text: lang('Next Harvest'),
                    flex: 1,
                    dataIndex: 'CertNextHarvest'
                },
                {
                    text: lang('Hectare'),
                    flex: 1,
                    dataIndex: 'CertHectare'
                }]
            }, {
                xtype: 'gridpanel',
                title: lang('Staff'),
                id: 'imsgStaff',
                style: 'border:1px solid #CCC;',
                store: store_staff,
                width: '100%',
                height: 500,
                loadMask: true,
                selType: 'rowmodel',
                minHeight:190,
                listeners: {
                    itemclick: function(view, record, item, index, e){
                       contextMenuStaffGrid.showAt(e.getXY());
                    }
                },
                dockedItems: [{
                    xtype: 'toolbar',
                    items: [{
                        icon: varjs.config.base_url + 'images/icons/new/add.png', cls:'Sfr_BtnGridGreen', overCls:'Sfr_BtnGridGreen-Hover',
                        //cls: m_act_save,
                        text: lang('Add'),
                        scope: this,
                        handler: function() {
                            if(Ext.getCmp('imsIMSID').getValue()==''){
                                Ext.MessageBox.alert('Warning', 'Please save IMS first!');
                            }else{
                                Ext.getCmp('imsSAObjType').setValue('');
                                Ext.getCmp('imsSAProvinceID').setValue('');
                                Ext.getCmp('imsSAWorkAreaID').setValue('');
                                Ext.getCmp('imsSAkey').setValue('');
                                store_staff_add.load({
                                    params: {
                                        key: 'removeGrid'
                                    }
                                });
                                displayAddWindowStaff();
                            }
                        }
                    }, {
                        xtype: 'textfield',
                        name: 'imsSkey',
                        id: 'imsSkey',
                        emptyText: lang('Staff Name'),
                        width: 280,
                        listeners: {}
                    }, {
                        xtype: 'button',
                        icon: varjs.config.base_url + 'images/icons/silk/search.png',
                        margin: '0px 0px 0px 6px',
                        text: lang('Search'),
                        handler: function() {
                            store_staff.load({
                                params: {
                                    IMSID: Ext.getCmp('imsIMSID').getValue(),
                                    key: Ext.getCmp('imsSkey').getValue()
                                }
                            });
                        }
                    }]
                }, {
                    xtype: 'pagingtoolbar',
                    store: store_staff,   // same store GridPanel is using
                    dock: 'bottom',
                    displayInfo: true
                }],
                columns: [{
                    text: 'ID',
                    dataIndex: 'IMSID',
                    hidden: true
                },
                {
                    text: 'IMSStaffID',
                    dataIndex: 'IMSStaffID',
                    hidden: true
                },
                {
                    text: 'StaffID',
                    dataIndex: 'StaffID',
                    hidden: true
                },
                {
                    text: 'No',
                    xtype: 'rownumberer',
                    align: 'center',
                    width: 50,
                },
                {
                    text: lang('Staff Name'),
                    flex: 3,
                    dataIndex: 'StaffName'
                },
                {
                    text: lang('Gender'),
                    flex: 1,
                    dataIndex: lang('Gender')
                },
                {
                    text: lang('Email'),
                    flex: 1,
                    dataIndex: 'StaffEmail'
                },
                {
                    text: lang('Work Area'),
                    flex: 4,
                    dataIndex: 'StaffWorkArea'
                }]
            }]
        }],
        buttons: [{
                id: 'imssave_par',
                text: lang('Save'),
                margin: '5px',
                scale: 'large',
                ui: 's-button',
                cls: 's-blue ',
                handler: function() {
                    var form = this.up('form').getForm();
                    var methode;
                    if (Ext.getCmp('imsIMSID').getValue() == '')
                        methode = 'POST';
                    else
                        methode = 'PUT';
                    form.submit({
                        url: m_crud + 'data',
                        method: methode,
                        waitMsg: lang('Sending data...'),
                        success: function(fp, o) {
                            Ext.MessageBox.alert('Success', lang('Data saved.'));
                            if (methode == 'POST'){
                                Ext.getCmp('imsIMSID').setValue(o.result.IMSID);
                            }
                            Ext.getCmp('imsdefaultSurveyNr').setValue(Ext.getCmp('imsSurveyNr').getValue());
                            store.load();
                        },
                        failure: function (response, opts) {
                            Ext.MessageBox.alert('error', 'Could not connect to the database. Retry later');
                        }
                    });
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
        title: lang('Data IMS'),
        frame: false,
        closable: true,
        id: 'imswin',
        modal: true,
        closeAction: 'show',
        width: '80%',
        height: '90%',
        layout: 'fit',
        items: [DataForm]
    });

    function submitOnEnter(field, event) {
        if (event.getKey() == event.ENTER) {
            filterRecord();
        }
    }

    function filterRecord() {
        store.load({
            params: {
                start: 0,
                key: Ext.getCmp('imskey').getValue()
            }
        });
    }
    
    function displayAddWindowFarmer() {
        if (!winAddFarmer.isVisible()) {
            /*store_farmer_add.load({
                params: {
                    IMSID: Ext.getCmp('imsIMSID').getValue(),
                    //CpgBatchTrainingID: Ext.getCmp('idt').getValue(),
                    //cpgID: Ext.getCmp('idd').getValue()
                }
            });*/
            winAddFarmer.show();
        } else {
            winAddFarmer.hide(this, function() {
            });
            winAddFarmer.toFront();
        }
    }
    
    function displayAddWindowAFL() {
        if (!winAddAFL.isVisible()) {
            /*store_farmer_add.load({
                params: {
                    IMSID: Ext.getCmp('imsIMSID').getValue(),
                    //CpgBatchTrainingID: Ext.getCmp('idt').getValue(),
                    //cpgID: Ext.getCmp('idd').getValue()
                }
            });*/
            winAddAFL.show();
        } else {
            winAddAFL.hide(this, function() {
            });
            winAddAFL.toFront();
        }
    }
    
    function displayAddWindowStaff() {
        if (!winAddStaff.isVisible()) {
            winAddStaff.show();
        } else {
            winAddStaff.hide(this, function() {
            });
            winAddStaff.toFront();
        }
    }
    
    var DataFormAddFarmer = Ext.create('Ext.panel.Panel', {
        height: 700,
        autoScroll: true,
        width: 1200,
        bodyPadding: 5,
        id: 'imsdataFormAddFarmer',
        items: [{
            layout: 'column',
            border: false,
            items: [{
                columnWidth: 1,
                layout: 'form',
                //padding: 5,
                border: false,
                items: [{
                    xtype: 'combobox',
                    id: 'imsFAProvinceID',
                    name: 'FAProvinceID',
                    emptyText: lang('Province'),
                    store: province,
                    //allowBlank: false,
                    queryMode: 'local',
                    displayField: 'label',
                    valueField: 'id',
                    listeners: {
                        change: function (cb, nv, ov) {
                            Ext.getCmp('imsFADistrictID').setValue('');
                            Ext.getCmp('imsFASubDistrictID').setValue('');
                            district.load({
                                params: {
                                    ProvinceID: Ext.getCmp('imsFAProvinceID').getValue()
                                }
                            });
                        }
                    }
                }]
            }, {
                columnWidth: 1,
                layout: 'form',
                //padding: 5,
                border: false,
                items: [{
                    xtype: 'combobox',
                    id: 'imsFADistrictID',
                    name: 'imsFADistrictID',
                    emptyText: lang('District'),
                    store: district,
                    //allowBlank: false,
                    queryMode: 'local',
                    displayField: 'label',
                    valueField: 'id',
                    listeners: {
                        change: function (cb, nv, ov) {
                            Ext.getCmp('imsFASubDistrictID').setValue('');
                            subdistrict.load({
                                params: {
                                    ProvinceID: Ext.getCmp('imsFAProvinceID').getValue(),
                                    DistrictID: Ext.getCmp('imsFADistrictID').getValue()
                                }
                            });
                        }
                    }
                }]
            }, {
                columnWidth: 1,
                layout: 'form',
                //padding: 5,
                border: false,
                items: [{
                    xtype: 'combobox',
                    id: 'imsFASubDistrictID',
                    name: 'FASubDistrictID',
                    emptyText: lang('Sub District'),
                    store: subdistrict,
                    //allowBlank: false,
                    //multiSelect: true,
                    queryMode: 'local',
                    displayField: 'label',
                    valueField: 'id',
                    listeners: {
                        change: function (cb, nv, ov) {
                            Ext.getCmp('imsFAVillageID').setValue('');
                            village.load({
                                params: {
                                    SubDistrictID: Ext.getCmp('imsFASubDistrictID').getValue()
                                }
                            });
                        }
                    }
                }]
            }, {
                columnWidth: 1,
                layout: 'form',
                //padding: 5,
                border: false,
                items: [{
                    xtype: 'combobox',
                    id: 'imsFAVillageID',
                    name: 'FAVillageID',
                    emptyText: lang('Village'),
                    store: village,
                    //allowBlank: false,
                    multiSelect: true,
                    queryMode: 'local',
                    displayField: 'label',
                    valueField: 'id'
                }]
            }, {
                columnWidth: 0.5,
                layout: 'form',
                //padding: 5,
                border: false,
                items: [{
                    xtype: 'textfield',
                    id: 'imsFAhectare',
                    name: 'FAhectare',
                    emptyText: lang('Area of Garden (Ha)')
                }]
            }, {
                columnWidth: 0.5,
                layout: 'form',
                //padding: 5,
                border: false,
                items: [{
                    xtype: 'textfield',
                    id: 'imsFAproduction',
                    name: 'imsFAproduction',
                    emptyText: lang('Estimated production (Kg/Year)')
                }]
            }, {
                columnWidth: 1,
                layout: 'form',
                //padding: 5,
                border: false,
                items: [{
                    xtype: 'gridpanel',
                    id: 'ims_grid_farmer_add',
                    store: store_farmer_add,
                    loadMask: true,
                    dockedItems: [{
                        xtype: 'toolbar',
                        items: [{
                            xtype: 'textfield',
                            name: 'imsFAkey',
                            id: 'imsFAkey',
                            emptyText: lang('Farmer ID / Farmer Name'),
                            width: 280,
                            listeners: {}
                        }, {
                            xtype: 'button',
                            icon: varjs.config.base_url + 'images/icons/silk/search.png',
                            margin: '0px 0px 0px 6px',
                            text: lang('Search'),
                            handler: function() {
                                if(Ext.getCmp('imsFAProvinceID').getValue()=="" || Ext.getCmp('imsFAProvinceID').getValue()==undefined){
                                    Ext.MessageBox.alert('Warning', lang('Select Province first!'));
                                }else{
                                    if(Ext.getCmp('imsFADistrictID').getValue()=="" || Ext.getCmp('imsFADistrictID').getValue()==undefined){
                                        Ext.MessageBox.alert('Warning', lang('Select DistrictID first!'));
                                    }else{
                                        if(Ext.getCmp('imsFASubDistrictID').getValue()=="" || Ext.getCmp('imsFASubDistrictID').getValue()==undefined){
                                            Ext.MessageBox.alert('Warning', lang('Select SubDistrictID first!'));
                                        }else{
                                            if(Ext.getCmp('imsFAVillageID').getValue()==''){
                                                var  village = "";
                                            }else{
                                                var village = Ext.getCmp('imsFAVillageID').getValue().join().replace(/,/g, '::');
                                            }
                                            store_farmer_add.load({
                                                params: {
                                                    IMSID: Ext.getCmp('imsIMSID').getValue(),
                                                    key: Ext.getCmp('imsFAkey').getValue(),
                                                    SurveyNr : Ext.getCmp('imsdefaultSurveyNr').getValue(),
                                                    DistrictID : Ext.getCmp('imsFADistrictID').getValue(),
                                                    SubDistrictID : Ext.getCmp('imsFASubDistrictID').getValue(),
                                                    VillageID : village,
                                                    hectare : Ext.getCmp('imsFAhectare').getValue(),
                                                    production : Ext.getCmp('imsFAproduction').getValue()
                                                }
                                            });
                                        }
                                    }
                                }
                            }
                        }]
                    }],
                    selType: 'checkboxmodel',
                    selModel: {
                        checkOnly: true,
                        mode: "MULTI",
                        headerWidth: 50
                    },
                    columns: [{
                        text: 'No',
                        xtype: 'rownumberer',
                        align: 'center',
                        width: 50,
                    }, {
                        text: lang('Farmer ID'),
                        dataIndex: 'addFarmerID',
                        flex: 1
                    }, {
                        text: lang('Farmer Name'),
                        dataIndex: 'addFarmerName',
                        flex: 2
                    }, {
                        text: lang('Farmer Group'),
                        dataIndex: 'FarmerGroup',
                        flex: 2
                    }, {
                        text: lang('Village'),
                        dataIndex: 'Village',
                        flex: 2
                    }, {
                        text: lang('GardenNr'),
                        dataIndex: 'addGardenNr',
                        flex: 1
                    }, {
                        text: lang('Area of Garden (Ha)'),
                        dataIndex: 'ha',
                        flex: 1
                    }, {
                        text: lang('Estimated production (Kg/Year)'),
                        dataIndex: 'production',
                        flex: 2
                    }]
                }]
            }]
        }],
        buttons: [{
                id: 'ims_save_par_add',
                text: lang('Save'),
                margin: '5px',
                scale: 'large',
                ui: 's-button',
                cls: 's-blue ',
                handler: function() {
                    var farmers = '';
                    Ext.each(Ext.getCmp('ims_grid_farmer_add').getSelectionModel().getSelection(), function(row, index, value) {
                        farmers = farmers + ',' + row.data.addFarmerID + '_' + row.data.addGardenNr;
                    });
                    if (farmers != '') {
                        Ext.Ajax.request({
                            url: m_crud + 'farmer_add',
                            method: 'POST',
                            waitMsg: lang('Sending data...'),
                            params: {
                                IMSID: Ext.getCmp('imsIMSID').getValue(),
                                SurveyNr : Ext.getCmp('imsdefaultSurveyNr').getValue(),
                                farmers: farmers
                            },
                            success: function(response, opts) {
                                var obj = Ext.decode(response.responseText);
                                switch (obj.success) {
                                    case true:
                                        Ext.MessageBox.alert('Success', obj.message);
                                        store_farmer_add.load({
                                            params: {
                                                key: 'removeGrid'
                                            }
                                        });
                                        store_farmer.load({
                                            params: {
                                                IMSID: Ext.getCmp('imsIMSID').getValue()
                                            }
                                        });
                                        break;
                                    default:
                                        Ext.MessageBox.alert('Warning', obj.message);
                                        break;
                                }
                            }
                        });
                    } else {
                        Ext.Msg.alert("Warning", "Please select farmer");
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
                    winAddFarmer.hide();
                }
            }]
    });
    
    var DataFormAddAFL = Ext.create('Ext.panel.Panel', {
        height: 700,
        autoScroll: true,
        width: 600,
        bodyPadding: 5,
        id: 'imsdataFormAddAFL',
        items: [{
            layout: 'column',
            border: false,
            items: [{
                columnWidth: 1,
                layout: 'form',
                //padding: 5,
                border: false,
                items: [{
                    xtype: 'gridpanel',
                    id: 'ims_grid_afl_add',
                    store: store_afl_add,
                    loadMask: true,
                    dockedItems: [{
                        xtype: 'toolbar',
                        items: [{
                            xtype: 'textfield',
                            name: 'imsAFLAkey',
                            id: 'imsAFLAkey',
                            emptyText: lang('Farmer ID / Farmer Name'),
                            width: 280,
                            listeners: {}
                        }, {
                            xtype: 'button',
                            icon: varjs.config.base_url + 'images/icons/silk/search.png',
                            margin: '0px 0px 0px 6px',
                            text: lang('Search'),
                            handler: function() {
                                store_farmer_add.load({
                                    params: {
                                        IMSID: Ext.getCmp('imsIMSID').getValue(),
                                        key: Ext.getCmp('imsFAkey').getValue()
                                    }
                                });
                            }
                        }]
                    }],
                    selType: 'checkboxmodel',
                    selModel: {
                        checkOnly: true,
                        mode: "MULTI",
                        headerWidth: '10%'
                    },
                    columns: [{
                        text: 'No',
                        xtype: 'rownumberer',
                        align: 'center',
                        width: '15%',
                    }, {
                        text: lang('NAME'),
                        dataIndex: 'AFLFarmerName',
                        width: '35%'
                    }, {
                        text: lang('ID'),
                        dataIndex: 'AFLFarmerID',
                        width: '20%'
                    }, {
                        text: lang('Farmer Group'),
                        dataIndex: 'AFLFarmerGroup',
                        width: '20%'
                    }]
                }]
            }]
        }],
        buttons: [{
                id: 'ims_save_par_add2',
                text: lang('Save'),
                margin: '5px',
                scale: 'large',
                ui: 's-button',
                cls: 's-blue ',
                handler: function() {
                    var farmers = '';
                    Ext.each(Ext.getCmp('ims_grid_afl_add').getSelectionModel().getSelection(), function(row, index, value) {
                        farmers = farmers + ',' + row.data.AFLFarmerID;
                    });
                    if (farmers != '') {
                        Ext.Ajax.request({
                            url: m_crud + 'afl_add',
                            method: 'POST',
                            waitMsg: lang('Sending data...'),
                            params: {
                                IMSID: Ext.getCmp('imsIMSID').getValue(),
                                //SurveyNr : Ext.getCmp('imsdefaultSurveyNr').getValue(),
                                farmers: farmers
                            },
                            success: function(response, opts) {
                                var obj = Ext.decode(response.responseText);
                                switch (obj.success) {
                                    case true:
                                        Ext.MessageBox.alert('Success', obj.message);
                                        store_afl_add.load({
                                            params: {
                                                key: 'removeGrid'
                                            }
                                        });
                                        store_afl.load({
                                            params: {
                                                IMSID: Ext.getCmp('imsIMSID').getValue()
                                            }
                                        });
                                        break;
                                    default:
                                        Ext.MessageBox.alert('Warning', obj.message);
                                        break;
                                }
                            }
                        });
                    } else {
                        Ext.Msg.alert("Warning", "Please select farmer");
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
                    winAddAFL.hide();
                }
            }]
    });
    
    var DataFormAddStaff = Ext.create('Ext.panel.Panel', {
        height: 700,
        autoScroll: true,
        width: 750,
        bodyPadding: 5,
        id: 'imsdataFormAddStaff',
        items: [{
            layout: 'column',
            border: false,
            items: [{
                columnWidth: 1,
                layout: 'form',
                //padding: 5,
                border: false,
                items: [{
                    xtype: 'combobox',
                    id: 'imsSAObjType',
                    name: 'imsSAObjType',
                    emptyText: lang('Type'),
                    store: staff_type,
                    allowBlank: false,
                    queryMode: 'local',
                    displayField: 'label',
                    valueField: 'id',
                    listeners: {
                        
                    }
                }]
            }, {
                columnWidth: 1,
                layout: 'form',
                //padding: 5,
                border: false,
                items: [{
                    xtype: 'combobox',
                    id: 'imsSAProvinceID',
                    name: 'imsSAProvinceID',
                    emptyText: lang('Province'),
                    store: staff_province,
                    //allowBlank: false,
                    queryMode: 'local',
                    displayField: 'label',
                    valueField: 'id',
                    listeners: {
                        change: function (cb, nv, ov) {
                            Ext.getCmp('imsSAWorkAreaID').setValue('');
                            work_area.load({
                                params: {
                                    ProvinceID: Ext.getCmp('imsSAProvinceID').getValue()
                                }
                            });
                        }
                    }
                }]
            }, {
                columnWidth: 1,
                layout: 'form',
                //padding: 5,
                border: false,
                items: [{
                    xtype: 'combobox',
                    id: 'imsSAWorkAreaID',
                    name: 'imsSAWorkAreaID',
                    emptyText: lang('Work Area'),
                    store: work_area,
                    //allowBlank: false,
                    queryMode: 'local',
                    displayField: 'label',
                    valueField: 'id',
                    listeners: {
                        
                    }
                }]
            }, {
                columnWidth: 1,
                layout: 'form',
                //padding: 5,
                border: false,
                items: [{
                    xtype: 'gridpanel',
                    id: 'ims_grid_staff_add',
                    store: store_staff_add,
                    loadMask: true,
                    dockedItems: [{
                        xtype: 'toolbar',
                        items: [{
                            xtype: 'textfield',
                            name: 'imsSAkey',
                            id: 'imsSAkey',
                            emptyText: lang('Keyword'),
                            width: 280,
                            listeners: {}
                        }, {
                            xtype: 'button',
                            icon: varjs.config.base_url + 'images/icons/silk/search.png',
                            margin: '0px 0px 0px 6px',
                            text: lang('Search'),
                            handler: function() {
                                if(Ext.getCmp('imsSAObjType').getValue()=="" || Ext.getCmp('imsSAObjType').getValue()==undefined){
                                    Ext.MessageBox.alert('Warning', lang('Select Staff Type first.!'));
                                }else{
                                    store_staff_add.load({
                                        params: {
                                            IMSID: Ext.getCmp('imsIMSID').getValue(),
                                            key: Ext.getCmp('imsSAkey').getValue(),
                                            ProvinceID : Ext.getCmp('imsSAProvinceID').getValue(),
                                            WorkAreaID : Ext.getCmp('imsSAWorkAreaID').getValue()
                                        }
                                    });
                                    
                                }
                            }
                        }]
                    }],
                    selType: 'checkboxmodel',
                    selModel: {
                        checkOnly: true,
                        mode: "MULTI",
                        headerWidth: '10%'
                    },
                    columns: [{
                        text: 'No',
                        xtype: 'rownumberer',
                        align: 'center',
                        width: '15%',
                    }, {
                        text: lang('ID'),
                        dataIndex: 'addStaffID',
                        hidden:true
                    }, {
                        text: lang('Name'),
                        dataIndex: 'addStaffName',
                        width: '25%'
                    }, {
                        text: lang('Gender'),
                        dataIndex: lang('addGender'),
                        width: '10%'
                    }, {
                        text: lang('Email'),
                        dataIndex: 'addStaffEmail',
                        width: '20%'
                    }, {
                        text: lang('Work Area'),
                        dataIndex: 'addStaffWorkArea',
                        width: '20%'
                    }]
                }]
            }]
        }],
        buttons: [{
                id: 'ims_save_staff_add',
                text: lang('Save'),
                margin: '5px',
                scale: 'large',
                ui: 's-button',
                cls: 's-blue ',
                handler: function() {
                    var staffs = '';
                    Ext.each(Ext.getCmp('ims_grid_staff_add').getSelectionModel().getSelection(), function(row, index, value) {
                        staffs = staffs + ',' + row.data.addStaffID;
                    });
                    if (staffs != '') {
                        Ext.Ajax.request({
                            url: m_crud + 'staff_add',
                            method: 'POST',
                            waitMsg: lang('Sending data...'),
                            params: {
                                IMSID: Ext.getCmp('imsIMSID').getValue(),
                                staffs: staffs
                            },
                            success: function(response, opts) {
                                var obj = Ext.decode(response.responseText);
                                switch (obj.success) {
                                    case true:
                                        Ext.MessageBox.alert('Success', obj.message);
                                        store_staff_add.load({
                                            params: {
                                                key: 'removeGrid'
                                            }
                                        });
                                        store_staff.load({
                                            params: {
                                                IMSID: Ext.getCmp('imsIMSID').getValue()
                                            }
                                        });
                                        break;
                                    default:
                                        Ext.MessageBox.alert('Warning', obj.message);
                                        break;
                                }
                            }
                        });
                    } else {
                        Ext.Msg.alert("Warning", "Please select staff");
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
                    winAddStaff.hide();
                }
            }]
    });
    
    var winAddFarmer = Ext.widget('window', {
        title: lang('Add Farmer'),
        id: 'imswinAddFarmer',
        closeAction: 'hide',
        height: 700,
        autoScroll: false,
        width: 1200,
        bodyPadding: 5,
        modal: true,
        layout: 'fit',
        items: [DataFormAddFarmer]
    });
    
    var winAddAFL = Ext.widget('window', {
        title: lang('Add AFL'),
        id: 'imswinAddAFL',
        closeAction: 'hide',
        height: 700,
        autoScroll: true,
        width: 600,
        bodyPadding: 5,
        modal: true,
        layout: 'fit',
        items: [DataFormAddAFL]
    });
    
    var winAddStaff = Ext.widget('window', {
        title: lang('Add Staff'),
        id: 'imswinAddStaff',
        closeAction: 'hide',
        height: 700,
        autoScroll: true,
        width: 750,
        bodyPadding: 5,
        modal: true,
        layout: 'fit',
        items: [DataFormAddStaff]
    });
    
    var contextMenuGrid = Ext.create('Ext.menu.Menu',{
        items: [
        {
            icon: varjs.config.base_url + 'images/icons/new/update.png',
            text: lang('Update'),
            hidden: !m_act_update,
            handler: function(){
                var sm = Ext.getCmp('imsgrid').getSelectionModel().getSelection()[0];
                displayFormWindow(true);
                Ext.Ajax.request({
                    url: m_crud + 'detail',
                    method: 'GET',
                    params: {IMSID: sm.get('IMSID')},
                    success: function (fp, o) {
                        var data = Ext.decode(fp.responseText);
                        set_form_value(data);
                    },
                    failure: function (response, opts) {
                        Ext.MessageBox.alert('error', 'Could not connect to the database. Retry later');
                    }
                });
            }
        },
        {
            icon: varjs.config.base_url + 'images/icons/new/delete.png',
            text: lang('Delete'),
            hidden: !m_act_delete,
            handler: function() {
                var smb = Ext.getCmp('imsgrid').getSelectionModel().getSelection()[0];
                Ext.MessageBox.confirm('Message', lang('Apakah anda mau menghapus data ini ?'), function (btn) {
                    if (btn == 'yes') {
                        Ext.Ajax.request({
                            waitMsg: lang('Please Wait'),
                            url: m_crud + 'data',
                            method: 'DELETE',
                            params: {IMSID: smb.raw.IMSID},
                            success: function (response, opts) {
                                var obj = Ext.decode(response.responseText);
                                switch (obj.success) {
                                    case true:
                                    store.load();
                                    Ext.MessageBox.alert('Success', obj.message);
                                    break;
                                    default:
                                    Ext.MessageBox.alert('Warning', obj.message);
                                    break;
                                }
                            },
                            failure: function (response, opts) {
                                Ext.MessageBox.alert('error', 'Could not connect to the database. Retry later');
                            }
                        });
                    }
                });
            }
        }
        ]
    });
    
    var contextMenuFarmerGrid = Ext.create('Ext.menu.Menu',{
        items: [
        {
            icon: varjs.config.base_url + 'images/icons/new/delete.png',
            text: lang('Delete'),
            hidden: !m_act_delete,
            handler: function() {
                var smb = Ext.getCmp('imsgFarmer').getSelectionModel().getSelection()[0];
                Ext.MessageBox.confirm('Message', lang('Apakah anda mau menghapus data ini ?'), function (btn) {
                    if (btn == 'yes') {
                        Ext.Ajax.request({
                            waitMsg: lang('Please Wait'),
                            url: m_crud + 'farmer_add',
                            method: 'DELETE',
                            params: {
                                IMSID: smb.raw.IMSID, 
                                FarmerID: smb.raw.FarmerID, 
                                GardenNr: smb.raw.GardenNr, 
                                SurveyNr: smb.raw.SurveyNr, 
                            },
                            success: function (response, opts) {
                                var obj = Ext.decode(response.responseText);
                                switch (obj.success) {
                                    case true:
                                    store_farmer.load({
                                        params: {
                                            IMSID: Ext.getCmp('imsIMSID').getValue()
                                        }
                                    });
                                    Ext.MessageBox.alert('Success', obj.message);
                                    break;
                                    default:
                                    Ext.MessageBox.alert('Warning', obj.message);
                                    break;
                                }
                            },
                            failure: function (response, opts) {
                                Ext.MessageBox.alert('error', 'Could not connect to the database. Retry later');
                            }
                        });
                    }
                });
            }
        }
        ]
    });
    
    var contextMenuAFLGrid = Ext.create('Ext.menu.Menu',{
        items: [
        {
            icon: varjs.config.base_url + 'images/icons/new/delete.png',
            text: lang('Delete'),
            hidden: !m_act_delete,
            handler: function() {
                var smb = Ext.getCmp('imsgAFL').getSelectionModel().getSelection()[0];
                Ext.MessageBox.confirm('Message', lang('Apakah anda mau menghapus data ini ?'), function (btn) {
                    if (btn == 'yes') {
                        Ext.Ajax.request({
                            waitMsg: lang('Please Wait'),
                            url: m_crud + 'afl_add',
                            method: 'DELETE',
                            params: {
                                IMSID: smb.raw.IMSID, 
                                FarmerID: smb.raw.FarmerID
                            },
                            success: function (response, opts) {
                                var obj = Ext.decode(response.responseText);
                                switch (obj.success) {
                                    case true:
                                    store_afl.load({
                                        params: {
                                            IMSID: Ext.getCmp('imsIMSID').getValue()
                                        }
                                    });
                                    Ext.MessageBox.alert('Success', obj.message);
                                    break;
                                    default:
                                    Ext.MessageBox.alert('Warning', obj.message);
                                    break;
                                }
                            },
                            failure: function (response, opts) {
                                Ext.MessageBox.alert('error', 'Could not connect to the database. Retry later');
                            }
                        });
                    }
                });
            }
        }
        ]
    });
    
    var contextMenuStaffGrid = Ext.create('Ext.menu.Menu',{
        items: [
        {
            icon: varjs.config.base_url + 'images/icons/new/delete.png',
            text: lang('Delete'),
            hidden: !m_act_delete,
            handler: function() {
                var smb = Ext.getCmp('imsgStaff').getSelectionModel().getSelection()[0];
                Ext.MessageBox.confirm('Message', lang('Apakah anda mau menghapus data ini ?'), function (btn) {
                    if (btn == 'yes') {
                        Ext.Ajax.request({
                            waitMsg: lang('Please Wait'),
                            url: m_crud + 'staff_add',
                            method: 'DELETE',
                            params: {
                                IMSID: smb.raw.IMSID, 
                                IMSStaffID: smb.raw.IMSStaffID
                            },
                            success: function (response, opts) {
                                var obj = Ext.decode(response.responseText);
                                switch (obj.success) {
                                    case true:
                                    store_staff.load({
                                        params: {
                                            IMSID: Ext.getCmp('imsIMSID').getValue()
                                        }
                                    });
                                    Ext.MessageBox.alert('Success', obj.message);
                                    break;
                                    default:
                                    Ext.MessageBox.alert('Warning', obj.message);
                                    break;
                                }
                            },
                            failure: function (response, opts) {
                                Ext.MessageBox.alert('error', 'Could not connect to the database. Retry later');
                            }
                        });
                    }
                });
            }
        }
        ]
    });

    var grid = Ext.create('Ext.grid.Panel', {
        store: store,
        width: '100%',
        id: 'imsgrid',
        minHeight: 250,
        //title: 'User List',
        style: 'border:1px solid #CCC;',
        renderTo: 'ext-content',
        loadMask: true,
        selType: 'rowmodel',
        listeners: {
            itemclick: function(view, record, item, index, e){
               contextMenuGrid.showAt(e.getXY());
            }
        },
        dockedItems: [{
            xtype: 'pagingtoolbar',
            store: store,   // same store GridPanel is using
            dock: 'bottom',
            displayInfo: true
        }, {
            xtype: 'toolbar',
            items: [
                {
                    icon: varjs.config.base_url + 'images/icons/new/add.png', cls:'Sfr_BtnGridGreen', overCls:'Sfr_BtnGridGreen-Hover',
                    text: 'Add',
                    scope: this,
                    handler: function() {
                        displayFormWindow(true);
                        Ext.getCmp('imsCertBodyContactID').setValue('');
                        set_form_value();
                        set_tanggal('false');
                    },
                    cls: m_act_add?'':'hidden'
                },                  
                /*{
                    xtype: 'combobox',
                    id: 'holderType',
                    name: 'holderType',
                    emptyText: lang('Type'),
                    store: holder_type,
                    queryMode: 'local',
                    displayField: 'label',
                    valueField: 'label',
                    listeners: {
                        
                    }
                },*/
                {
                    xtype: 'textfield',
                    emptyText: lang('Keyword'),
                    name: 'imskey',
                    id: 'imskey',
                    listeners: {
                        specialkey: submitOnEnter
                    }
                }, 
                {
                    xtype: 'button',
                    margin: '0px 0px 0px 6px',
                    text: 'Search',
                    handler: function () {
                        filterRecord();
                    }
                }]
        }],
        columns: [
            {
                text: 'ID',
                dataIndex: 'IMSID',
                hidden: true
            },
            {
                text: 'No',
                xtype: 'rownumberer',
                align: 'center',
                width: 50,
            },
            {
                text: lang('Holder Name'),
                flex: 3,
                dataIndex: 'HolderName'
            },
            {
                text: lang('Program Name'),
                flex: 3,
                dataIndex: 'CertProgName'
            },
            {
                text: lang('Certification Start'),
                flex: 1,
                dataIndex: 'CertificationStart'
            },
            {
                text: lang('Certification End'),
                flex: 1,
                dataIndex: 'CertificationEnd'
            },
            {
                text: lang('Extension Date'),
                flex: 1.5,
                dataIndex: 'ExtensionDate'
            },
            {
                text: lang('Survey Number'),
                flex: 1.5,
                dataIndex: 'SurveyNr'
            },
            {
                text: lang('Year'),
                flex: 1,
                dataIndex: 'Year'
            },
            {
                text: lang('First Buyer'),
                flex: 2,
                dataIndex: 'FirstBuyer'
            }]
    });
});

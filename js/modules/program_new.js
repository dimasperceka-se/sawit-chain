/* global Ext */

Ext.Loader.setConfig({
    enabled: true
});
Ext.Loader.setPath('js/ext-4.2.0.663', '../ux');
//Ext.Loader.setPath('js/ext-4.2.0.663/ux/form');
Ext.require([
    //'Ext.form.Panel',
    //'Ext.ux.form.MultiSelect',
    'Ext.ux.form.ItemSelector'
]);
Array.prototype.remove = function () {
    var what, a = arguments,
            L = a.length,
            ax;
    while (L && this.length) {
        what = a[--L];
        while ((ax = this.indexOf(what)) !== -1) {
            this.splice(ax, 1);
        }
    }
    return this;
};
var selectedCPG = [];
var tmp = [];
var isCPGset = false;

function setSelectedCPG(DistrictID, value) {
    console.log('setSelectedCPG ' + DistrictID);
    console.log(value);
    // selectedCPG = [];
    // console.log('Set Selected '+DistrictID+'=>'+value);
    if (typeof (selectedCPG['d_' + DistrictID]) === 'undefined') {
        selectedCPG['d_' + DistrictID] = [];
    }
    selectedCPG['d_' + DistrictID].push(value);
    isCPGset = true;
}

function updateSelectedCPG(DistrictID, data) {
    console.log('updateSelectedCPG');
    console.log(DistrictID);
    if (DistrictID) {
        if (typeof (selectedCPG['d_' + DistrictID]) === 'undefined') {
            selectedCPG['d_' + DistrictID] = [];
        }
        // add new data to selected
        $.each(data, function (index, val) {
            if ($.inArray(val, selectedCPG['d_' + DistrictID]) === -1) {
                console.log('Add to Selected : ' + val);
                selectedCPG['d_' + DistrictID].push(val);
            }
        });
        // remove deleted data from selected
        $.each(selectedCPG['d_' + DistrictID], function (index, val) {
            if ($.inArray(val, data) === -1) {
                console.log('Remove from Selected : ' + val);
                selectedCPG['d_' + DistrictID].remove(val);
            }
        });
    } else {
        tmp = [];
        $.each(data, function (index, val) {
            var dist_id = val.toString().substring(0, 4)
            if (typeof (tmp[dist_id]) === 'undefined') {
                tmp[dist_id] = [];
            }
            tmp[dist_id].push(val);
        });
        $.each(tmp, function (index, val) {
            if (val) {
                // var dist_id = index.toString().substring(2,6);
                console.log('updateSelectedCPG(' + index + ', ' + val + ');');
                updateSelectedCPG(index, val);
            }
        });
    }
}

function setCPGItem(DistrictID) {
    console.log('setCPGItem : ' + DistrictID);
    if (DistrictID) {
        Ext.getCmp("cpgItemselector").setValue(selectedCPG['d_' + DistrictID]);
    } else {
        $.each(Ext.getCmp('district-field').getValue(), function (index, val) {
            setCPGItem(val);
        });
    }
}

Ext.onReady(function () {
    Ext.tip.QuickTipManager.init();
    Ext.tip.QuickTipManager.init();
    var PartnerID;
    var DistrictID;
    var store = Ext.create('Ext.data.Store', {
        extend: 'Ext.data.Model',
        fields: ['id', 'PartnerName', 'type', 'PartnerFullName', 'Photo', 'PartnerIndustry', 'PartnerProgramName'],
        autoLoad: true,
        pageSize: 50,
        proxy: {
            type: 'ajax',
            url: m_crud + 's',
            reader: {
                type: 'json',
                root: 'data',
                totalProperty: 'total'
            }
        },
        listeners: {
            beforeload: function(store, operation, options){
                store.proxy.extraParams.SearchText = Ext.getCmp('MainGrid-TxtSearchNama').getValue();
            }
        }
    });
    var mc_flagakses = Ext.create('Ext.data.Store', {
        fields: ['id', 'label'],
        data: [{
                "id": "0",
                "label": lang("Per District")
            }, {
                "id": "1",
                "label": lang("Per CPG")
            }]
    });

    var contextMenuGrid = Ext.create('Ext.menu.Menu', {
        items: [{
                icon: varjs.config.base_url + 'images/icons/new/view.png',
                text: lang('View'),
                handler: function () {
                    var sm = Ext.getCmp('grid').getSelectionModel().getSelection()[0];

                    Ext.getCmp('grid').destroy(); //destory current view
                    let FormMainNew = [];

                    //create object View untuk FormMainGrower
                    if(Ext.getCmp('Koltiva.view.Partner.MainFormNew') == undefined){
                        FormMainNew = Ext.create('Koltiva.view.Partner.MainFormNew', {
                            viewVar: {
                                OpsiDisplay: 'view',
                                PartnerID: sm.get('id')
                            }
                        });
                    }else{
                        //destroy, create ulang
                        Ext.getCmp('Koltiva.view.Partner.MainFormNew').destroy();
                        FormMainNew = Ext.create('Koltiva.view.Partner.MainFormNew', {
                            viewVar: {
                                OpsiDisplay: 'view',
                                PartnerID: sm.get('id')
                            }
                        });
                    }
                }
            }, {
                icon: varjs.config.base_url + 'images/icons/new/update.png',
                text: lang('Update'),
                hidden: m_act_update,
                handler: function () {
                    var sm = Ext.getCmp('grid').getSelectionModel().getSelection()[0];

                    Ext.getCmp('grid').destroy(); //destory current view
                    let FormMainNew = [];

                    //create object View untuk FormMainGrower
                    if(Ext.getCmp('Koltiva.view.Partner.MainFormNew') == undefined){
                        FormMainNew = Ext.create('Koltiva.view.Partner.MainFormNew', {
                            viewVar: {
                                OpsiDisplay: 'update',
                                PartnerID: sm.get('id')
                            }
                        });
                    }else{
                        //destroy, create ulang
                        Ext.getCmp('Koltiva.view.Partner.MainFormNew').destroy();
                        FormMainNew = Ext.create('Koltiva.view.Partner.MainFormNew', {
                            viewVar: {
                                OpsiDisplay: 'update',
                                PartnerID: sm.get('id')
                            }
                        });
                    }
                }
            }, {
                icon: varjs.config.base_url + 'images/icons/silk/user_suit.png',
                text: lang('Assign CPG'),
                hidden: !m_act_assign_cpg,
                handler: function () {
                    var sm = Ext.getCmp('grid').getSelectionModel().getSelection()[0];
                    displayFormAssignCpg(sm.get('id'), sm.get('PartnerName'));
                }
            }, {
                icon: varjs.config.base_url + 'images/icons/silk/link_edit.png',
                text: lang('Setting LDAP'),
                //hidden: !m_act_assign_cpg,
                handler: function () {
                    var sm = Ext.getCmp('grid').getSelectionModel().getSelection()[0];
                    displayFormLDAP();
                    Ext.Ajax.request({
                        url: m_crud + '_ldap_detail',
                        method: 'GET',
                        params: {
                            id: sm.get('id')
                        },
                        success: function (fp, o) {
                            var r = Ext.decode(fp.responseText);
                            Ext.getCmp('id').setValue(sm.get('id'));
                            Ext.getCmp('pad_host').setValue(r.ad_host);
                            Ext.getCmp('pad_port').setValue(r.ad_port);
                            Ext.getCmp('pad_basedn').setValue(r.ad_basedn);
                            Ext.getCmp('pad_domain').setValue(r.ad_domain);
                            if (r.ad_auth == '1') {
                                Ext.getCmp('pad_auth1').setValue(true);
                            } else if (r.ad_auth == '0') {
                                Ext.getCmp('pad_auth0').setValue(true);
                            } else {
                                Ext.getCmp('pad_auth1').setValue(false);
                                Ext.getCmp('pad_auth0').setValue(false);
                            }

                        }
                    });
                }
            }, {
                icon: varjs.config.base_url + 'images/icons/new/delete.png',
                text: lang('Delete'),
                hidden: m_act_delete,
                handler: function () {
                    var smb = Ext.getCmp('grid').getSelectionModel().getSelection()[0];
                    Ext.MessageBox.confirm('Message', lang('Apakah anda mau menghapus data ini ?'), function (btn) {
                        if (btn == 'yes') {
                            Ext.Ajax.request({
                                waitMsg: lang('Please Wait'),
                                url: m_crud,
                                method: 'DELETE',
                                params: {
                                    id: smb.raw.id
                                },
                                success: function (response, opts) {
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
                                failure: function (response, opts) {
                                    var obj = Ext.decode(response.responseText);
                                    Ext.MessageBox.alert('error', lang('Could not connect to the database. Retry later'));
                                }
                            });
                        }
                    });
                }
            }]
    });

    var grid = Ext.create('Ext.grid.Panel', {
        store: store,
        width: '100%',
        minHeight: 250,
        id: 'grid',
        //title: lang('Program List'),
        style: 'border:1px solid #CCC;',
        renderTo: 'ext-content',
        loadMask: true,
        selType: 'rowmodel',
        listeners: {
            itemclick: function (view, record, item, index, e) {
                contextMenuGrid.showAt(e.getXY());
            }
            /*itemdblclick: function(dv, record, item, index, e) {
             isCPGset = false;
             // clear item selector
             // load selected district
             dsDistrict.load({
             params: {
             id: record.get('id')
             },
             callback: function() {
             reloadDistrict();
             }
             // callback: function() {
             //     var selector = Ext.getCmp('district-field').getValue();
             //     // load cpg list based on
             //     storeListCpg.load({
             //         params: {
             //             id: record.get('id'),
             //             DistrictID: selector.join(','),
             //             stat:''
             //         },
             //         callback:function(){
             //             if (!isCPGset) {
             //                 Ext.getCmp('cpgItemselector').setValue("");
             //                 dsCpgPartner.load({
             //                     params: {
             //                         id: record.get('id'),
             //                         DistrictID: Ext.getCmp('cmbDistrict').getValue()
             //                     },
             //                     callback: function() {
             //                         var selectedCpgPartner = [];
             //                         dsCpgPartner.data.each(function(item, index, totalItems ) {
             //                             setSelectedCPG(item.data['DistrictID'], item.data['value']);
             //                             selectedCpgPartner.push(item.data['value']);
             //                         });
             //                         Ext.getCmp("cpgItemselector").setValue(selectedCpgPartner);
             //                     }
             //                 });
             //             } else {
             //                 setCPGItem(Ext.getCmp('cmbDistrict').getValue());
             //             }
             //         }
             //     });
             // }
             });
             displayFormWindow();
             var sm = record;
             Ext.Ajax.request({
             url: m_crud,
             method: 'GET',
             params: {
             id: sm.get('id')
             },
             success: function(fp, o) {
             var r = Ext.decode(fp.responseText);
             Ext.getCmp('id').setValue(sm.get('id'));
             Ext.getCmp('PartnerName').setValue(r.PartnerName);
             if (r.PartnerIndustry == '0') Ext.getCmp('PartnerIndustry').setValue(true);
             if (r.PartnerIndustry == '1') Ext.getCmp('PartnerIndustry1').setValue(true);
             if (r.PartnerIndustry == '2') Ext.getCmp('PartnerIndustry2').setValue(true);
             if (r.PartnerIndustry == '3') Ext.getCmp('PartnerIndustry3').setValue(true);
             if (r.PartnerIndustry == '4') Ext.getCmp('PartnerIndustry4').setValue(true);
             if (r.PartnerIndustry == '5') Ext.getCmp('PartnerIndustry5').setValue(true);
             Ext.getCmp('PartnerFullName').setValue(r.PartnerFullName);
             Ext.getCmp('PartnerProgramName').setValue(r.PartnerProgramName);
             Ext.getCmp('ilogo').setSrc(m_photo + r.Photo);
             Ext.getCmp('ilogoProgram').setSrc(m_photo + r.PhotoProgram);
             Ext.getCmp('Photo_old').setValue(r.Photo);
             Ext.getCmp('PhotoProgram_old').setValue(r.PhotoProgram);
             Ext.getCmp('cmbFlagAkses').setValue(r.FlagAccess);
             }
             });
             }
             */
        },
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
                        handler: function () {
                            Ext.getCmp('grid').destroy(); //destory current view
                            let FormMainNew = [];

                            //create object View untuk FormMainGrower
                            if(Ext.getCmp('Koltiva.view.Partner.MainFormNew') == undefined){
                                FormMainNew = Ext.create('Koltiva.view.Partner.MainFormNew', {
                                    viewVar: {
                                        OpsiDisplay: 'insert',
                                        PartnerID: null
                                    }
                                });
                            }else{
                                //destroy, create ulang
                                Ext.getCmp('Koltiva.view.Partner.MainFormNew').destroy();
                                FormMainNew = Ext.create('Koltiva.view.Partner.MainFormNew', {
                                    viewVar: {
                                        OpsiDisplay: 'insert',
                                        PartnerID: null
                                    }
                                });
                            }
                        },
                        cls: m_act_add
                    }, {
                        icon: varjs.config.base_url + 'images/icons/silk/group_gear.png',
                        text: lang('Set District Partner Member'),
                        scope: this,
                        handler: function () {
                            displayDistrictFormWindow();
                        }
                    }, {
                        icon: varjs.config.base_url + 'images/icons/new/update.png',
                        text: lang('Update'),
                        hidden: true,
                        scope: this,
                        handler: function () {
                            displayFormWindow();
                            var sm = Ext.getCmp('grid').getSelectionModel().getSelection()[0];
                            Ext.Ajax.request({
                                url: m_crud,
                                method: 'GET',
                                params: {
                                    id: sm.get('id')
                                },
                                success: function (fp, o) {
                                    var r = Ext.decode(fp.responseText);
                                    Ext.getCmp('id').setValue(sm.get('id'));
                                    Ext.getCmp('PartnerName').setValue(r.PartnerName);
                                    /*
                                    if (r.PartnerIndustry == '0')
                                        Ext.getCmp('PartnerIndustry').setValue(true);
                                    if (r.PartnerIndustry == '1')
                                        Ext.getCmp('PartnerIndustry1').setValue(true);
                                    if (r.PartnerIndustry == '2')
                                        Ext.getCmp('PartnerIndustry2').setValue(true);
                                    if (r.PartnerIndustry == '3')
                                        Ext.getCmp('PartnerIndustry3').setValue(true);
                                    if (r.PartnerIndustry == '4')
                                        Ext.getCmp('PartnerIndustry4').setValue(true);
                                    if (r.PartnerIndustry == '5')
                                        Ext.getCmp('PartnerIndustry5').setValue(true);
                                    */
                                    Ext.getCmp('PartnerFullName').setValue(r.PartnerFullName);
                                    Ext.getCmp('PartnerProgramName').setValue(r.PartnerProgramName);
                                    Ext.getCmp('ilogo').setSrc(r.Photo);
                                    Ext.getCmp('ilogoProgram').setSrc(r.PhotoProgram);
                                    Ext.getCmp('Photo_old').setValue(r.PhotoOld);
                                    Ext.getCmp('PhotoProgram_old').setValue(r.PhotoProgram);
                                    Ext.getCmp('cmbFlagAkses').setValue(r.PhotoProgramOld);
                                }
                            });
                        },
                        cls: m_act_update
                    }, {
                        itemId: 'remove',
                        icon: varjs.config.base_url + 'images/icons/new/delete.png',
                        cls: m_act_delete,
                        hidden: true,
                        text: lang('Hapus'),
                        scope: this,
                        handler: function () {
                            var smb = Ext.getCmp('grid').getSelectionModel().getSelection()[0];
                            Ext.MessageBox.confirm('Message', lang('Apakah anda mau menghapus data ini ?'), function (btn) {
                                if (btn == 'yes') {
                                    Ext.Ajax.request({
                                        waitMsg: lang('Please Wait'),
                                        url: m_crud,
                                        method: 'DELETE',
                                        params: {
                                            id: smb.raw.id
                                        },
                                        success: function (response, opts) {
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
                                        failure: function (response, opts) {
                                            var obj = Ext.decode(response.responseText);
                                            Ext.MessageBox.alert('error', lang('Could not connect to the database. Retry later'));
                                        }
                                    });
                                }
                            });
                        }
                    },{
                        xtype:'tbspacer',
                        flex:1
                    },{
                        name: 'MainGrid-TxtSearchNama',
                        id: 'MainGrid-TxtSearchNama',
                        xtype: 'textfield',
                        baseCls: 'Sfr_TxtfieldSearchGrid',
                        width: 400,
                        emptyText: lang('Search by Name / Full Name'),
                        listeners: {
                            specialkey: submitOnEnterCari
                        }
                    }, {
                        xtype: 'button',
                        icon: varjs.config.base_url + 'images/icons/new/search_white.png',
                        text: lang('Search'),
                        cls: 'Sfr_BtnGridBlue',
                        overCls: 'Sfr_BtnGridBlue-Hover',
                        handler: function () {
                            /* store.storeVar.KeySearch = Ext.getCmp('MainGrid-TxtSearchNama').getValue();
                            store.loadPage(1); */

                            Ext.getCmp('grid').getStore().load();
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
                text: lang('Name'),
                width: '24%',
                dataIndex: 'PartnerName'
            }, {
                text: lang('Industry'),
                width: '10%',
                dataIndex: 'type',
                hidden: true
            }, {
                text: lang('Full Name'),
                width: '30%',
                dataIndex: 'PartnerFullName'
            }, {
                text: lang('Program Name'),
                width: '30%',
                dataIndex: 'PartnerProgramName'
            }]
    });

    function submitOnEnterCari(field, event) {
          if (event.getKey() == event.ENTER) Ext.getCmp('grid').getStore().load();
    }
    /*
     var store_districtInPartner = Ext.create('Ext.data.Store', {
     extend: 'Ext.data.Model',
     fields: ['id','district', 'province'],
     proxy: {
     type: 'ajax',
     url: m_districtInPartner,
     reader: {
     type: 'json',
     root: 'data'
     }
     }
     });
     */
    /*
     var store_Province = Ext.create('Ext.data.Store', {
     extend: 'Ext.data.Model',
     fields: ['id','province'],
     proxy: {
     type: 'ajax',
     url: m_Province,
     reader: {
     type: 'json',
     root: 'data'
     }
     }
     });
     */
    /*
     var store_District = Ext.create('Ext.data.Store', {
     extend: 'Ext.data.Model',
     fields: ['id','district'],
     proxy: {
     type: 'ajax',
     url: m_District,
     reader: {
     type: 'json',
     root: 'data'
     }
     }
     });
     */
    var storeListDistrict = Ext.create('Ext.data.ArrayStore', {
        fields: [{
                name: 'value',
                type: 'int'
            }, {
                name: 'text',
                type: 'string'
            }],
        proxy: {
            type: 'ajax',
            url: m_list_district,
            reader: {
                type: 'json',
                root: 'data'
            }
        },
        autoLoad: true
    });
    var dsDistrict = Ext.create('Ext.data.ArrayStore', {
        fields: [{
                name: 'value',
                type: 'int'
            }, {
                name: 'text',
                type: 'string'
            }],
        proxy: {
            type: 'ajax',
            url: m_districtInPartner,
            extraParams: {
                id: ''
            },
            reader: {
                type: 'json',
                root: 'data'
            }
        },
        autoLoad: false,
        listeners: {
            load: function () {
                var selected = [];
                var selector = Ext.getCmp("district-field");
                dsDistrict.data.each(function (item, index, totalItems) {
                    selected.push(item.data['value']);
                });
                selector.setValue(selected);
            }
        }
    });
    var storeListCpg = Ext.create('Ext.data.ArrayStore', {
        storeId: 'store-list-cpg',
        fields: [{
                name: 'value',
                type: 'int'
            }, {
                name: 'text',
                type: 'string'
            }],
        proxy: {
            type: 'ajax',
            url: m_list_cpg,
            reader: {
                type: 'json',
                root: 'data'
            }
        },
        autoLoad: false
    });
    var dsCpgPartner = Ext.create('Ext.data.ArrayStore', {
        fields: [{
                name: 'DistrictID',
                type: 'string'
            }, {
                name: 'value',
                type: 'int'
            }, {
                name: 'text',
                type: 'string'
            }],
        proxy: {
            type: 'ajax',
            url: m_cpgInPartner,
            reader: {
                type: 'json',
                root: 'data'
            }
        },
        autoLoad: false,
        listeners: {
            load: function () {
                // if (!isCPGset) {
                //     var selectedCpgPartner = [];
                //     var selectorCpgPartner = Ext.getCmp("cpgItemselector");
                //     dsCpgPartner.data.each(function(item, index, totalItems ) {
                //         selectedCpgPartner.push(item.data['value']);
                //         // console.log('insert '+item.data['value']);
                //     });
                //     selectorCpgPartner.setValue(selectedCpgPartner);
                // }
            }
        }
    });
    var mc_district = Ext.create('Ext.data.Store', {
        extend: 'Ext.data.Model',
        fields: ['id', 'district'],
        autoload: false,
        // proxy:{
        //     type:'ajax',
        //     url:m_district_find,
        //     reader:{
        //         type:'json',
        //         root:'data'
        //     }
        // }
    });

    var DataFormLDAP = Ext.create('Ext.form.Panel', {
        id: 'dataFormLDAP',
        frame: false,
        width: 450,
        height: 200,
        autoScroll: true,
        bodyPadding: 10,
        fieldDefaults: {
            labelAlign: 'left',
            labelWidth: 120,
            padding: 10,
            // anchor: '100%'
        },
        items: [
            {
                xtype: 'panel',
                autoScroll: true,
                items: [
                    {
                        layout: 'column',
                        border: false,
                        items: [
                            {
                                columnWidth: 1,
                                layout: 'form',
                                items: [
                                    {
                                        xtype: 'radiogroup',
                                        fieldLabel: lang('Auth'),
                                        defaultType: 'radiofield',
                                        columns: 2,
                                        //hori: true,
                                        items: [{
                                                id: 'pad_auth1',
                                                name: 'ad_auth',
                                                boxLabel: lang('Ya'),
                                                inputValue: '1'
                                            }, {
                                                id: 'pad_auth0',
                                                name: 'ad_auth',
                                                boxLabel: lang('Tidak'),
                                                inputValue: '0'
                                            }]
                                    }, {
                                        xtype: 'textfield',
                                        fieldLabel: lang('Host'),
                                        id: 'pad_host',
                                        name: 'ad_host'
                                    }, {
                                        xtype: 'textfield',
                                        fieldLabel: lang('Port'),
                                        id: 'pad_port',
                                        name: 'ad_port'
                                    }, {
                                        xtype: 'textfield',
                                        fieldLabel: lang('Basedn'),
                                        id: 'pad_basedn',
                                        name: 'ad_basedn'
                                    }, {
                                        xtype: 'textfield',
                                        fieldLabel: lang('Domain'),
                                        id: 'pad_domain',
                                        name: 'ad_domain'
                                    }
                                ]
                            }
                        ]
                    },
                ],
            }
        ],
        buttons: [{
                id: 'ppsave_ldap',
                text: 'Save',
                margin: '5px',
                scale: 'large',
                ui: 's-button',
                cls: 's-blue',
                handler: function () {
                    Ext.Ajax.request({
                        url: m_crud + '_ldap',
                        method: 'POST',
                        params: {
                            id: Ext.getCmp('id').getValue(),
                            ad_host: Ext.getCmp('pad_host').getValue(),
                            ad_port: Ext.getCmp('pad_port').getValue(),
                            ad_basedn: Ext.getCmp('pad_basedn').getValue(),
                            ad_domain: Ext.getCmp('pad_domain').getValue(),
                            ad_auth: Ext.getCmp('dataFormLDAP').getForm().findField('ad_auth').getValue()
                        },
                        success: function (fp, o) {
                            var r = Ext.decode(fp.responseText);
                            if (r.success == 'true') {
                                Ext.MessageBox.alert('Success', lang(r.message));
                            } else {
                                Ext.MessageBox.alert('Warning', lang(r.message));
                            }
                        },
                        failure: function () {
                            Ext.MessageBox.alert('error', lang('Could not connect to the database. Retry later'));
                        }
                    });
                }
            }, {
                text: 'Close',
                margin: '5px',
                scale: 'large',
                ui: 's-button',
                cls: 's-grey',
                disabled: false,
                handler: function () {
                    winLADP.hide();
                }
            }]
    });

    var winLADP = Ext.create('widget.window', {
        title: lang('Setting LDAP'),
        frame: false,
        closable: true,
        id: 'pWinLDAP',
        modal: true,
        closeAction: 'show',
        width: 550,
        minWidth: 350,
        height: 300,
        layout: 'fit',
        items: [DataFormLDAP]
    });

    function displayFormLDAP() {
        if (!winLADP.isVisible()) {
            winLADP.show();
        } else {
            winLADP.hide(this, function () {
            });
            winLADP.toFront();
        }
    }

    function displayFormWindow(viewOnly = false) {
        if (!win.isVisible()) {
            DataForm.getForm().reset();
            Ext.getCmp('ilogo').setSrc('');
            Ext.getCmp('ilogoProgram').setSrc('');
            win.center();
            win.show();
            Ext.getCmp('PartnerName').focus(true, true);

            if (viewOnly == false) {
                Ext.getCmp('saveButton').setVisible(true);
            } else {
                Ext.getCmp('saveButton').setVisible(false);
            }
        } else {
            win.hide(this, function () {});
            win.toFront();
        }
    }

    function displayDistrictFormWindow() {
        if (!winDistrictPartner.isVisible()) {
            winDistrictPartner.center();
            winDistrictPartner.show();
        } else {
            winDistrictPartner.hide(this, function () {});
            winDistrictPartner.toFront();
        }
    }
    var DataForm = Ext.create('Ext.form.Panel', {
        frame: false,
        height: 350,
        width: '100%',
        bodyPadding: 5,
        autoScroll: true,
        fileUpload: true,
        enctype: 'multipart/form-data',
        id: 'dataForm',
        fieldDefaults: {
            labelAlign: 'left',
            labelWidth: 100,
            anchor: '100%'
        },
        items: [{
                layout: 'column',
                border: false,
                items: [{
                        columnWidth: .48,
                        layout: 'form',
                        padding: 10,
                        //border: false,
                        xtype: 'fieldset',
                        title: lang('Basic Data'),
                        height: '100%',
                        items: [{
                                xtype: 'textfield',
                                id: 'id',
                                name: 'id',
                                inputType: 'hidden'
                            }, {
                                xtype: 'textfield',
                                id: 'Photo_old',
                                name: 'Photo_old',
                                inputType: 'hidden'
                            }, {
                                xtype: 'textfield',
                                id: 'PhotoProgram_old',
                                name: 'PhotoProgram_old',
                                inputType: 'hidden'
                            }, {
                                xtype: 'textfield',
                                fieldLabel: lang('Name'),
                                id: 'PartnerName',
                                name: 'PartnerName'
                            }, /*{
                                xtype: 'radiogroup',
                                fieldLabel: lang('Industry'),
                                defaultType: 'radiofield',
                                columns: 3,
                                vertical: true,
                                items: [{
                                        id: 'PartnerIndustry',
                                        name: 'PartnerIndustry',
                                        boxLabel: lang('Implementer'),
                                        inputValue: '0'
                                    }, {
                                        id: 'PartnerIndustry1',
                                        name: 'PartnerIndustry',
                                        boxLabel: lang('Donor'),
                                        inputValue: '1'
                                    }, {
                                        id: 'PartnerIndustry2',
                                        name: 'PartnerIndustry',
                                        boxLabel: lang('Trader'),
                                        inputValue: '2'
                                    }, {
                                        id: 'PartnerIndustry3',
                                        name: 'PartnerIndustry',
                                        boxLabel: lang('Processor'),
                                        inputValue: '3'
                                    }, {
                                        id: 'PartnerIndustry4',
                                        name: 'PartnerIndustry',
                                        boxLabel: lang('Manufacturer'),
                                        inputValue: '4'
                                    }, {
                                        id: 'PartnerIndustry5',
                                        name: 'PartnerIndustry',
                                        boxLabel: lang('Input Supplier'),
                                        inputValue: '5'
                                    }]
                            },*/ {
                                xtype: 'textfield',
                                fieldLabel: lang('Full Name'),
                                id: 'PartnerFullName',
                                name: 'PartnerFullName'
                            }, {
                                xtype: 'textfield',
                                fieldLabel: lang('Program Name'),
                                id: 'PartnerProgramName',
                                name: 'PartnerProgramName'
                            }]
                    }, {// right fieldset
                        columnWidth: .26,
                        height: '100%',
                        layout: 'form',
                        xtype: 'fieldset',
                        title: lang('Logo Partner'),
                        style: 'margin-left:12px',
                        items: [{
                                layout: 'column',
                                columns: 2,
                                //height: 330,
                                //height: '100%',
                                items: [{
                                        layout: 'column',
                                        items: [{
                                                columnWidth: 0.2,
                                                items: [{
                                                        xtype: 'fileuploadfield',
                                                        fieldLabel: lang('File'),
                                                        labelWidth: 50,
                                                        id: 'Photo',
                                                        width: 250,
                                                        name: 'Photo',
                                                        buttonText: 'Browse',
                                                        listeners: {
                                                            'change': function (fb, v) {
                                                                var form = this.up('form').getForm();
                                                                form.submit({
                                                                    url: m_crud + '_image',
                                                                    waitMsg: 'Sending Photo...',
                                                                    success: function (fp, o) {
                                                                        Ext.getCmp('ilogo').setSrc(o.result.file);
                                                                        Ext.getCmp('Photo_old').setValue(o.result.filepath);
                                                                    }
                                                                });
                                                            }
                                                        }
                                                    }, {
                                                        xtype: 'image',
                                                        id: 'ilogo',
                                                        height: '120px',
                                                        border: 1,
                                                        style: {
                                                            borderColor: 'black',
                                                            borderStyle: 'solid'
                                                        },
                                                        margin: '0 0 0 55'
                                                    }]
                                            }]
                                    }]
                            }]
                    }, {// right fieldset
                        columnWidth: .26,
                        height: '100%',
                        layout: 'form',
                        xtype: 'fieldset',
                        title: lang('Logo Program'),
                        style: 'margin-left:12px',
                        items: [{
                                layout: 'column',
                                columns: 2,
                                //height: 330,
                                //height: '100%',
                                items: [{
                                        layout: 'column',
                                        items: [{
                                                columnWidth: 0.2,
                                                items: [{
                                                        xtype: 'fileuploadfield',
                                                        fieldLabel: lang('File'),
                                                        labelWidth: 50,
                                                        id: 'PhotoProgram',
                                                        width: 250,
                                                        name: 'PhotoProgram',
                                                        buttonText: 'Browse',
                                                        listeners: {
                                                            'change': function (fb, v) {
                                                                var form = this.up('form').getForm();
                                                                form.submit({
                                                                    url: m_crud + '_image_program',
                                                                    waitMsg: 'Sending Photo...',
                                                                    success: function (fp, o) {
                                                                        Ext.getCmp('ilogoProgram').setSrc(o.result.file);
                                                                        Ext.getCmp('PhotoProgram_old').setValue(o.result.filepath);
                                                                    }
                                                                });
                                                            }
                                                        }
                                                    }, {
                                                        xtype: 'image',
                                                        id: 'ilogoProgram',
                                                        height: '120px',
                                                        border: 1,
                                                        style: {
                                                            borderColor: 'black',
                                                            borderStyle: 'solid'
                                                        },
                                                        margin: '0 0 0 55'
                                                    }]
                                            }]
                                    }]
                            }]
                    }]
            }
            /*, {
             xtype: 'fieldset',
             title: lang('Setting LDAP'),
             items: [{
             layout: 'column',
             border: false,
             items: [{
             columnWidth: .5,
             layout: 'form',
             padding: 10,
             //border: false,
             //xtype: 'fieldset',
             //title: lang('Setting LDAP'),
             //height: '100%',
             items: [{
             xtype: 'textfield',
             fieldLabel: lang('Host'),
             id: 'pad_host',
             name: 'ad_host'
             }, {
             xtype: 'textfield',
             fieldLabel: lang('Port'),
             id: 'pad_port',
             name: 'ad_port'
             }, {
             xtype: 'radiogroup',
             fieldLabel: lang('Auth'),
             defaultType: 'radiofield',
             columns: 2,
             //hori: true,
             items: [{
             id: 'pad_auth1',
             name: 'ad_auth',
             boxLabel: lang('Ya'),
             inputValue: '1'
             }, {
             id: 'pad_auth0',
             name: 'ad_auth',
             boxLabel: lang('Tidak'),
             inputValue: '0'
             }]
             }]
             }, {
             columnWidth: .5,
             layout: 'form',
             padding: 10,
             //border: false,
             //xtype: 'fieldset',
             //title: lang('Setting LDAP'),
             //height: '100%',
             items: [{
             xtype: 'textfield',
             fieldLabel: lang('Basedn'),
             id: 'pad_basedn',
             name: 'ad_basedn'
             }, {
             xtype: 'textfield',
             fieldLabel: lang('Domain'),
             id: 'pad_domain',
             name: 'ad_domain'
             }]
             }]
             }]
             }*/, {
                layout: 'column',
                border: false,
                items: [{
                        columnWidth: .99,
                        //width:'100%',
                        layout: 'form',
                        padding: 30,
                        height: '100%',
                        //border: false,
                        xtype: 'fieldset',
                        title: 'Permission',
                        items: [{
                                id: 'cmbFlagAkses',
                                name: 'cmbFlagAkses',
                                xtype: 'combobox',
                                width: 370,
                                fieldLabel: 'Flag Access',
                                style: 'padding:10px 10px 15px 0;',
                                store: mc_flagakses,
                                displayField: 'label',
                                queryMode: 'local',
                                valueField: 'id',
                                listeners: {
                                    focus: function () {},
                                    select: function (combo, record, index) {}
                                }
                            }, {
                                xtype: 'itemselector',
                                name: 'districtItemselector',
                                fieldLabel: 'District',
                                id: 'district-field',
                                anchor: '90%',
                                height: 300,
                                store: storeListDistrict,
                                displayField: 'text',
                                valueField: 'value',
                                value: [],
                                allowBlank: true,
                                msgTarget: 'side',
                                fromTitle: 'Available',
                                toTitle: 'Selected',
                                listeners: {
                                    change: function (cb, nv, ov) {
                                        reloadDistrict();
                                        isCPGset = false;
                                        // var selector = Ext.getCmp('district-field').getValue();
                                        // load cpg list based on
                                        if (nv) {
                                            record_id = Ext.getCmp('grid').getSelectionModel().getSelection()[0].data.id;
                                            storeListCpg.load({
                                                params: {
                                                    id: record_id,
                                                    DistrictID: nv.join(','),
                                                    stat: ''
                                                },
                                                callback: function () {
                                                    if (!isCPGset) {
                                                        Ext.getCmp('cpgItemselector').setValue("");
                                                        dsCpgPartner.load({
                                                            params: {
                                                                id: record_id,
                                                                DistrictID: nv.join(',')
                                                            },
                                                            callback: function () {
                                                                selectedCPG = [];
                                                                var selectedCpgPartner = [];
                                                                dsCpgPartner.data.each(function (item, index, totalItems) {
                                                                    setSelectedCPG(item.data['DistrictID'], item.data['value']);
                                                                    selectedCpgPartner.push(item.data['value']);
                                                                });
                                                                Ext.getCmp("cpgItemselector").setValue(selectedCpgPartner);
                                                            }
                                                        });
                                                    } else {
                                                        setCPGItem(Ext.getCmp('cmbDistrict').getValue());
                                                    }
                                                }
                                            });
                                        }
                                    }
                                }
                            }, {
                                layout: 'column',
                                bodyStyle: 'padding:5px 5px 10px',
                                xtype: 'container',
                                columns: 3,
                                autoEl: 'div',
                                items: [{
                                        xtype: 'button',
                                        scale: 'small',
                                        //ui: 's-button',
                                        //cls: 's-blue',
                                        text: 'Select All',
                                        style: {
                                            marginLeft: '9.5%'
                                        },
                                        handler: function () {
                                            var arrVal = []; // create an empty array
                                            var selector = Ext.getCmp("district-field");
                                            selector.store.each(function (item, index, totalItems) {
                                                arrVal.push(item.data['value']);
                                            });
                                            selector.setValue(arrVal);
                                        }
                                    }, {
                                        xtype: 'button',
                                        scale: 'small',
                                        //ui: 's-button',
                                        //cls: 's-blue',
                                        text: 'Unselect All',
                                        style: {
                                            marginLeft: '10px'
                                        },
                                        handler: function () {
                                            Ext.getCmp("district-field").reset();
                                        }
                                    }]
                            }, {
                                id: 'cmbDistrict',
                                name: 'cmbDistrict',
                                xtype: 'combobox',
                                width: 370,
                                fieldLabel: 'Select District',
                                style: 'padding:20px 10px 10px 0;',
                                store: mc_district,
                                displayField: 'district',
                                queryMode: 'local',
                                valueField: 'id',
                                hidden: true,
                                listeners: {
                                    focus: function () {
                                        // var selector = Ext.getCmp('district-field').getValue();
                                        //console.log(selector);
                                        //Ext.Msg.alert('',selector);
                                        /*
                                         var sel2 = Ext.getCmp('district-field');
                                         var arrVal=[];
                                         sel2.store.each(function(item, index, totalItems ) {
                                         arrVal.push(item.data['value']);
                                         });
                                         console.log(arrVal);
                                         */
                                        // Ext.getCmp('cmbDistrict').getStore().load({
                                        //     params:{
                                        //         id:selector.join(',')
                                        //     }
                                        // });
                                    },
                                    change: function (combo, nv, ov) {
                                        record_id = Ext.getCmp('grid').getSelectionModel().getSelection()[0].data.id;
                                        //Ext.getCmp('cpg-field').getStore().remove(record);
                                        // storeListCpg.reload({
                                        //     params: {
                                        //         id: combo.getValue(),
                                        //         DistrictID: combo.getValue(),
                                        //         idPartner : Ext.getCmp('id').getValue(),
                                        //         stat:'search'
                                        //     },
                                        // callback: function() {
                                        // load cpg list based on
                                        storeListCpg.load({
                                            params: {
                                                id: record_id,
                                                DistrictID: nv,
                                                stat: ''
                                            },
                                            callback: function () {
                                                if (!isCPGset) {
                                                    Ext.getCmp('cpgItemselector').setValue("");
                                                    dsCpgPartner.load({
                                                        params: {
                                                            id: record_id,
                                                            DistrictID: nv
                                                        },
                                                        callback: function () {
                                                            var selectedCpgPartner = [];
                                                            dsCpgPartner.data.each(function (item, index, totalItems) {
                                                                setSelectedCPG(item.data['DistrictID'], item.data['value']);
                                                                selectedCpgPartner.push(item.data['value']);
                                                            });
                                                            Ext.getCmp("cpgItemselector").setValue(selectedCpgPartner);
                                                        }
                                                    });
                                                } else {
                                                    setCPGItem(nv);
                                                }
                                            }
                                        });
                                        // }
                                        // });
                                    }
                                }
                            }, {
                                xtype: 'itemselector',
                                name: 'cpgItemselector',
                                fieldLabel: 'Select CPG',
                                id: 'cpgItemselector',
                                hidden: true,
                                anchor: '90%',
                                height: 300,
                                store: storeListCpg,
                                displayField: 'text',
                                valueField: 'value',
                                value: [],
                                allowBlank: true,
                                msgTarget: 'side',
                                fromTitle: 'Available',
                                toTitle: 'Selected',
                                listeners: {
                                    change: function (cb, nv, ov) {
                                        console.log('cpgItemselector changed');
                                        console.log(nv);
                                        updateSelectedCPG(Ext.getCmp('cmbDistrict').getValue(), nv);
                                    }
                                }
                            }, {
                                layout: 'column',
                                bodyStyle: 'padding:5px 5px 10px',
                                xtype: 'container',
                                columns: 3,
                                autoEl: 'div',
                                hidden: true,
                                items: [{
                                        xtype: 'button',
                                        scale: 'small',
                                        //ui: 's-button',
                                        //cls: 's-blue',
                                        text: 'Select All',
                                        style: {
                                            marginLeft: '9.5%'
                                        },
                                        handler: function () {
                                            var arrVal = []; // create an empty array
                                            var selector = Ext.getCmp("cpgItemselector");
                                            selector.store.each(function (item, index, totalItems) {
                                                arrVal.push(item.data['value']);
                                                // if ($.inArray(item.data['value'], selectedCPG) == -1) {
                                                //     selectedCPG.push(item.data['value']);
                                                // }
                                            });
                                            selector.setValue(arrVal);
                                        }
                                    }, {
                                        xtype: 'textfield',
                                        id: 'selectedCPG',
                                        name: 'selectedCPG',
                                        inputType: 'hidden'
                                    }, {
                                        xtype: 'button',
                                        scale: 'small',
                                        //ui: 's-button',
                                        //cls: 's-blue',
                                        text: 'Unselect All',
                                        style: {
                                            marginLeft: '10px'
                                        },
                                        handler: function () {
                                            Ext.getCmp("cpgItemselector").reset();
                                        }
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
                handler: function () {
                    var tmp = [];
                    $.each(Ext.getCmp('district-field').getValue(), function (index, district) {
                        tmp = tmp.concat(selectedCPG['d_' + district]);
                    });
                    Ext.getCmp('selectedCPG').setValue(tmp);
                    var form = this.up('form').getForm();
                    var urle;
                    if (Ext.getCmp('id').getValue() != '')
                        urle = m_crud + 'u';
                    else
                        urle = m_crud;
                    form.submit({
                        url: urle,
                        waitMsg: 'Sending data...',
                        success: function (fp, o) {
                            Ext.MessageBox.alert('Success', 'Data saved.');
                            store.load();
                        }
                    });
                    // win.hide(this, function() {
                    //     store.load();
                    // });
                }
            }, {
                text: lang('Close'),
                margin: '5px',
                scale: 'large',
                ui: 's-button',
                cls: 's-grey',
                disabled: false,
                handler: function () {
                    //clearItemselector();
                    win.hide();
                }
            }]
    });

    var store_district = Ext.create('Ext.data.Store', {
        extend: 'Ext.data.Store',
        storeId: 'koltiva-store-partner-MainListDistrictPartner',
        fields: ['ProvinceID', 'Province', 'DistrictID', 'District', 'PartnerID', 'PartnerName', 'PartnerFullName', 'Alias'],
        groupField: 'Province',
        autoLoad: true,
        proxy: {
            type: 'ajax',
            url: m_api + '/partner/list_district_partner_member',
            reader: {
                type: 'json',
                root: 'data'
            }
        }
    });

    var menuDistrictPartner = Ext.create('Ext.menu.Menu', {
        items: [
            {
                icon: varjs.config.base_url + 'images/icons/new/update.png',
                text: lang('Add/ Update Partner'),
                handler: function () {
                    var sm = Ext.getCmp('mainGridDistrictPartner').getSelectionModel().getSelection()[0];
                    displayFormPartnerDistrict(sm.get('DistrictID'), sm.get('District'), sm.get('PartnerID'));
                }
            }, {
                icon: varjs.config.base_url + 'images/icons/new/delete.png',
                text: lang('Delete'),
                handler: function () {
                    var smb = Ext.getCmp('mainGridDistrictPartner').getSelectionModel().getSelection()[0];
                    Ext.MessageBox.confirm('Message', lang('Are you sure to delete this data?'), function (btn) {
                        if (btn == 'yes') {
                            Ext.Ajax.request({
                                waitMsg: lang('Please Wait'),
                                url: m_api + '/partner/district_partner_member',
                                method: 'DELETE',
                                params: {
                                    districtid: smb.get('DistrictID'),
                                    partnerid: smb.get('PartnerID')
                                },
                                success: function (response, opts) {
                                    var obj = Ext.decode(response.responseText);
                                    Ext.MessageBox.alert('success', lang(obj.message));
                                    Ext.getCmp('mainGridDistrictPartner').getStore().reload();
                                },
                                failure: function (response, opts) {
                                    var obj = Ext.decode(response.responseText);
                                    Ext.MessageBox.alert('error', lang('Could not connect to the database. Retry later'));
                                }
                            });
                        }
                    });
                }
            }]
    });

    var DataDistrictPartner = Ext.create('Ext.grid.Panel', {
        id: 'mainGridDistrictPartner',
        width: '99.5%',
        style: 'border:1px solid #CCC;margin:25px 5px 5px 5px;',
        title: lang('Initial Data by District'),
        selType: 'rowmodel',
        store: store_district,
        features: [{
                ftype: 'groupingsummary',
                groupHeaderTpl: '{name}',
                hideGroupedHeader: true,
                enableGroupingMenu: true,
                showSummaryRow: false,
                startCollapsed: true
            }],
        listeners: {
            itemclick: function (view, record, item, index, e) {
                menuDistrictPartner.showAt(e.getXY());
            }
        },
        columns: [{
                header: lang('Province'),
                sortable: true,
                dataIndex: 'Province'
            }, {
                text: lang('ID'),
                dataIndex: 'DistrictID',
                hidden: true
            }, {
                text: lang('Region'),
                dataIndex: 'District',
                width: '25%'
            }, {
                text: lang('PartnerID'),
                dataIndex: 'PartnerID',
                hidden: true
            }, {
                text: lang('Partner Name'),
                dataIndex: 'PartnerName',
                width: '25%'
            }, {
                text: lang('Partner Fullname'),
                dataIndex: 'PartnerFullName',
                width: '50%'
            }]
    });

    var win = Ext.create('widget.window', {
        title: lang('Data Program'),
        id: 'win',
        closable: true,
        modal: true,
        closeAction: 'show',
        width: '90%',
        //minWidth: 570,
        height: '90%',
        layout: {
            type: 'fit'
        },
        items: [DataForm],
        listeners: {
            'close': function () {
                //clearItemselector();
            }
        }
    });

    var winDistrictPartner = Ext.create('widget.window', {
        title: lang('District Partner Member'),
        id: 'win-district-partner',
        closable: true,
        modal: true,
        closeAction: 'show',
        width: '90%',
        height: '90%',
        layout: {
            type: 'fit'
        },
        items: [DataDistrictPartner],
        buttons: [{
                text: lang('Close'),
                margin: '5px',
                scale: 'large',
                ui: 's-button',
                cls: 's-grey',
                disabled: false,
                handler: function () {
                    //clearItemselector();
                    winDistrictPartner.hide();
                }
            }]
    });
    /*
     function clearItemselector(){
     Ext.getStore('store-list-cpg').reload({
     params: {
     id: 'empty'
     },
     callback:function(){
     Ext.getCmp('cpgItemselector').setValue("");
     }
     });
     }
     */
    function reloadDistrict() {
        var value = Ext.getCmp('cmbDistrict').getValue();
        var itemSelectorField = Ext.getCmp('district-field');
        var fieldList = itemSelectorField.toField.store.getRange();
        var exist = false;
        mc_district.removeAll();
        $.each(fieldList, function (index, val) {
            if (value == parseInt(val.data.value)) {
                exist = true;
            }
            mc_district.add({
                id: val.data.value,
                district: val.data.text,
            });
        });
        if (!exist) {
            Ext.getCmp('cmbDistrict').setValue('');
        } else {
            Ext.getCmp('cmbDistrict').setValue(value);
        }
    }
});

function displayFormPartnerDistrict(DistrictID, District, PartnerID) {

    var partner = Ext.create('Ext.data.Store', {
        extend: 'Ext.data.Store',
        storeId: 'koltiva-store-partner-MainListPartner',
        fields: ['PartnerID', 'PartnerName'],
        autoLoad: true,
        proxy: {
            type: 'ajax',
            url: m_api + '/partner/list_partner',
            reader: {
                type: 'json',
                root: 'data'
            }
        }
    });
    var FormPartnerMember = Ext.create('Ext.form.Panel', {
        id: 'FormPartnerMember',
        height: 350,
        width: '100%',
        autoScroll: true,
        frame: false,
        bodyPadding: 5,
        layout: {
            type: 'fit'
        },
        items: [{
                layout: 'column',
                border: false,
                items: [
                    {
                        columnWidth: 1,
                        layout: 'form',
                        padding: 10,
                        xtype: 'fieldset',
                        title: lang('District Partner Member'),
                        height: '100%',
                        items: [
                            {
                                id: 'districtid-field',
                                name: 'districtid',
                                xtype: 'hiddenfield',
                                width: '100%',
                                fieldLabel: 'District ID',
                                value: DistrictID
                            }, {
                                id: 'district-field',
                                name: 'district',
                                xtype: 'textfield',
                                width: '100%',
                                style: 'padding:10px 10px 15px 0;',
                                readOnly: true,
                                fieldLabel: 'District',
                                value: District
                            }, {
                                id: 'cmbPartner',
                                name: 'partner',
                                xtype: 'combobox',
                                width: '100%',
                                fieldLabel: 'Partner',
                                style: 'padding:10px 10px 15px 0;',
                                queryMode: 'local',
                                store: partner,
                                displayField: 'PartnerName',
                                valueField: 'PartnerID',
                                value: PartnerID
                            }]
                    }]
            }],
        buttons: [{
                text: 'Save',
                margin: '5px',
                scale: 'large',
                ui: 's-button',
                cls: 's-blue',
                handler: function () {
                    var form = Ext.getCmp('FormPartnerMember').getForm();
                    if (form.isValid()) {
                        if (PartnerID == null && (Ext.getCmp('cmbPartner').getValue() != null)) {
                            Ext.MessageBox.confirm('Message', lang('Are you sure you want to save this data?'), function (btn) {
                                if (btn == 'yes') {
                                    form.submit({
                                        url: m_api + '/partner/save_district_partner_member',
                                        method: 'POST',
                                        waitMsg: 'Saving data...',
                                        success: function (fp, o) {
                                            Ext.MessageBox.alert('Success', 'Data saved');
                                            Ext.getCmp('mainGridDistrictPartner').getStore().reload();
                                            winFormPartnerDistrict.close();
                                        },
                                        failure: function (fp, o) {
                                            var jsonResp = o.result;
                                            Ext.MessageBox.show({
                                                title: 'Failed',
                                                msg: jsonResp.message,
                                                buttons: Ext.MessageBox.OK,
                                                animateTarget: 'mb9',
                                                icon: 'ext-mb-error'
                                            });
                                        }
                                    });
                                }
                            });
                        } else if (PartnerID != null && (Ext.getCmp('cmbPartner').getValue() != PartnerID) && (Ext.getCmp('cmbPartner').getValue() != null)) {
                            Ext.MessageBox.confirm('Message', lang('Are you sure you want to change this data?'), function (btn) {
                                if (btn == 'yes') {
                                    form.submit({
                                        url: m_api + '/partner/change_district_partner_member',
                                        method: 'POST',
                                        waitMsg: 'Saving data...',
                                        success: function (fp, o) {
                                            Ext.MessageBox.alert('Success', 'Data saved');
                                            Ext.getCmp('mainGridDistrictPartner').getStore().reload();
                                            winFormPartnerDistrict.close();
                                        },
                                        failure: function (fp, o) {
                                            var jsonResp = o.result;
                                            Ext.MessageBox.show({
                                                title: 'Failed',
                                                msg: jsonResp.message,
                                                buttons: Ext.MessageBox.OK,
                                                animateTarget: 'mb9',
                                                icon: 'ext-mb-error'
                                            });
                                        }
                                    });
                                }
                            });
                        } else if (Ext.getCmp('cmbPartner').getValue() == null) {
                            Ext.MessageBox.alert('Danger', lang('Please change Partner'));
                        } else {
                            Ext.MessageBox.alert('Success', 'Data saved');
                            winFormPartnerDistrict.close();
                        }
                    } else {
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
                handler: function () {
                    winFormPartnerDistrict.close();
                }
            }]
    });
    var winFormPartnerDistrict = Ext.create('widget.window', {
        title: lang('Form District Partner Member'),
        id: 'winFormPartnerDistrict',
        closable: true,
        modal: true,
        closeAction: 'destroy',
        width: '50%',
        height: '82%',
        layout: {
            type: 'fit'
        },
        items: [FormPartnerMember]
    });

    //show windows
    if (!winFormPartnerDistrict.isVisible()) {
        winFormPartnerDistrict.center();
        winFormPartnerDistrict.show();
    } else {
        winFormPartnerDistrict.close();
    }
}

function displayFormAssignCpg(PartnerID, PartnerName) {

    var cmb_district_access = Ext.create('Ext.data.Store', {
        extend: 'Ext.data.Model',
        fields: ['id', 'label'],
        autoLoad: true,
        pageSize: 10,
        proxy: {
            type: 'ajax',
            url: m_api + '/partner/district_access',
            reader: {
                type: 'json',
                root: 'data'
            }
        },
        listeners: {
            'beforeload': function (store, options) {
                store.proxy.extraParams.PartnerID = PartnerID;
            }
        }
    });

    var selector_assign_cpg = Ext.create('Ext.data.Store', {
        extend: 'Ext.data.Model',
        fields: ['id', 'label'],
        autoLoad: false,
        pageSize: 10,
        proxy: {
            type: 'ajax',
            url: m_api + '/partner/cpg_access',
            reader: {
                type: 'json',
                root: 'data'
            }
        },
        listeners: {
            'beforeload': function (store, options) {
                store.proxy.extraParams.DistrictID = Ext.getCmp('AssCpgDistrictAccessSelected').getValue();
            }
        }
    });

    var winAssignCpg = Ext.create('widget.window', {
        title: lang('Assign CPG'),
        id: 'winAssignCpg',
        closable: true,
        modal: true,
        closeAction: 'destroy',
        width: '75%',
        height: '82%',
        overflowY: 'auto',
        bodyStyle: {"background-color": "#F0F0F0"},
        style: 'background-color:#F0F0F0;',
        padding: 6,
        scrollOffset: 20,
        items: [{
                xtype: 'form',
                id: 'winFormAssignCpg',
                padding: '5 20 0 8',
                items: [{
                        layout: 'column',
                        border: false,
                        items: [{
                                xtype: 'hiddenfield',
                                id: 'AssCpgPartnerID',
                                name: 'AssCpgPartnerID',
                                value: PartnerID
                            }, {
                                columnWidth: 1,
                                xtype: 'textfield',
                                fieldLabel: lang('Partner'),
                                labelWidth: 175,
                                id: 'AssCpgPartnerName',
                                name: 'AssCpgPartnerName',
                                value: PartnerName,
                                readOnly: true
                            }, {
                                columnWidth: 1,
                                xtype: 'combobox',
                                fieldLabel: lang('District Access'),
                                labelWidth: 175,
                                store: cmb_district_access,
                                displayField: 'label',
                                valueField: 'id',
                                id: 'AssCpgDistrictAccessSelected',
                                name: 'AssCpgDistrictAccessSelected',
                                queryMode: 'local',
                                allowBlank: false,
                                listeners: {
                                    change: function (cb, nv, ov) {

                                        selector_assign_cpg.removeAll();
                                        selector_assign_cpg.load({
                                            callback: function (records, operation, success) {
                                                if (success) {
                                                    Ext.getCmp('AssCpgAssignCpg').bindStore(selector_assign_cpg);

                                                    var p = Ext.MessageBox.show({
                                                        title: 'Please wait',
                                                        msg: 'Fetching CPG List...',
                                                        closable: true
                                                    });

                                                    //manggil cpg yg sudah terassign
                                                    Ext.Ajax.request({
                                                        url: m_api + '/partner/cpg_access_selected',
                                                        method: 'POST',
                                                        timeout: 3600,
                                                        params: {
                                                            PartnerID: PartnerID,
                                                            DistrictID: nv
                                                        },
                                                        success: function (response) {
                                                            var varSetItemSel = [];
                                                            var obj = response.responseText;
                                                            var objReturn = Ext.JSON.decode(obj);
                                                            var varSetItemSel = varSetItemSel.concat(objReturn.data);
                                                            //console.log(varSetItemSel);

                                                            p.close();
                                                            Ext.getCmp('AssCpgAssignCpg').setValue(varSetItemSel);
                                                        },
                                                        failure: function () {
                                                            p.close();
                                                            Ext.MessageBox.show({
                                                                title: 'Failed',
                                                                msg: 'Fetching CPG Failed',
                                                                buttons: Ext.MessageBox.OK,
                                                                animateTarget: 'mb9',
                                                                icon: 'ext-mb-error'
                                                            });
                                                            return false;
                                                        }
                                                    });

                                                } else {
                                                    Ext.MessageBox.show({
                                                        title: 'Failed',
                                                        msg: 'Connection Timed Out',
                                                        buttons: Ext.MessageBox.OK,
                                                        animateTarget: 'mb9',
                                                        icon: 'ext-mb-error'
                                                    });
                                                }
                                            }
                                        });
                                    }
                                }
                            }, {
                                columnWidth: 1,
                                xtype: 'itemselector',
                                flex: true,
                                id: 'AssCpgAssignCpg',
                                name: 'AssCpgAssignCpg',
                                fieldLabel: lang('Assign CPG'),
                                labelWidth: 175,
                                fromTitle: lang('Available'),
                                toTitle: lang('Selected'),
                                anchor: '100%',
                                height: 425,
                                store: selector_assign_cpg,
                                displayField: 'label',
                                valueField: 'id',
                                value: [],
                                listeners: {
                                    change: function () {
                                    }
                                }
                            }]
                    }]
            }],
        buttons: [{
                text: 'Save',
                margin: '5px',
                scale: 'large',
                ui: 's-button',
                cls: 's-blue',
                handler: function () {
                    var form = Ext.getCmp('winFormAssignCpg').getForm();
                    if (form.isValid()) {
                        form.submit({
                            url: m_api + '/partner/assign_cpg_update',
                            method: 'POST',
                            waitMsg: 'Saving data...',
                            success: function (fp, o) {
                                Ext.MessageBox.alert('Success', 'Data saved');
                            },
                            failure: function (fp, o) {
                                var jsonResp = o.result;
                                Ext.MessageBox.show({
                                    title: 'Failed',
                                    msg: jsonResp.message,
                                    buttons: Ext.MessageBox.OK,
                                    animateTarget: 'mb9',
                                    icon: 'ext-mb-error'
                                });
                            }
                        });
                    } else {
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
                handler: function () {
                    winAssignCpg.close();
                }
            }]
    });

    //show windows
    if (!winAssignCpg.isVisible()) {
        winAssignCpg.center();
        winAssignCpg.show();
    } else {
        winAssignCpg.close();
    }
}
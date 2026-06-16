/*
 Param2 yg diperlukan ketika load View ini
 1. ....
 */

// Define Variabel2 / Object2 / Fungsi yg diperlukan oleh view ini (begin)

//variabel2 / object2
var contextMenuGrid = Ext.create('Ext.menu.Menu', {
    items: [{
            icon: varjs.config.base_url + 'images/icons/new/view.png',
            text: lang('Farmer Profile'),
            handler: function () {
                var url = m_cetak_beneficiary_profiles;
                var sm = Ext.getCmp('view.FarmerMill.GridMainFarmerMill-gridMainGrid').getSelectionModel().getSelection()[0];
                if (sm.get('MemberIDInc') == '') {
                    Ext.MessageBox.show({
                        title: 'Warning',
                        msg: lang('No Farmer Selected'),
                        buttons: Ext.MessageBox.OK,
                        animateTarget: 'mb9',
                        icon: 'ext-mb-error'
                    });
                    return false;
                }
                preview_cetak_surat(url + '/MemberID/' + sm.get('MemberIDInc'));
            }
        }, {
            icon: varjs.config.base_url + 'images/icons/new/view.png',
            text: lang('Farmer Summary'),
            handler: function () {
                var url = m_cetak_farmer_summary;
                var sm = Ext.getCmp('view.FarmerMill.GridMainFarmerMill-gridMainGrid').getSelectionModel().getSelection()[0];
                if (sm.get('MemberIDInc') == '') {
                    Ext.MessageBox.show({
                        title: 'Warning',
                        msg: lang('No Farmer Selected'),
                        buttons: Ext.MessageBox.OK,
                        animateTarget: 'mb9',
                        icon: 'ext-mb-error'
                    });
                    return false;
                }
                preview_cetak_surat(url + '/MemberID/' + sm.get('MemberIDInc'));
            }
        }]
});

var storeGridMain = Ext.create('Koltiva.store.FarmerMill.GridMain');

function setFilterLs() {
    localStorage.setItem('patchouli_grower_ls', JSON.stringify({
        ptextSearch: Ext.getCmp('view.FarmerMill.GridMainFarmerMill-textSearch').getValue(),
        ptextSearchDesa : Ext.getCmp('view.FarmerMill.GridMainFarmerMill-textSearchDesa').getValue(),
        pCmbRoleSearch: "",
        pPartnerSearch: Ext.getCmp('view.FarmerMill.GridMainFarmerMill-FarmerMillPartnerIDFilter').getValue(),
        pPartnerFirstLoad: Ext.getCmp('view.FarmerMill.GridMainFarmerMill-FarmerMillFirstLoad').getValue(),
        pCmbCategorySearch  : Ext.getCmp('view.FarmerMill.GridMainFarmerMill-CmbCategorySearch').getValue()
    }));
}

function getFilterLs() {
    var filters = {};
    var ptextSearch, ptextSearchDesa,pCmbRoleSearch, pPartnerSearch, pCmbCategorySearch, pPartnerFirstLoad, pAdvRowEnumerator, pAdvTextEnumerator, pAdvRowHandphone, pAdvTextHandphone, pAdvRowAge, pAdvOpAge, pAdvTextAge, pAdvRowMaritalStatus, pAdvMaritalStatus, pAdvDateCollectionBegin, pAdvDateCollectionEnd, pAdvRowDateCollection;

    var patchouli_grower_ls = JSON.parse(localStorage.getItem('patchouli_grower_ls'));
    if (patchouli_grower_ls != null) {
        ptextSearch = patchouli_grower_ls.ptextSearch;
        ptextSearchDesa = patchouli_grower_ls.ptextSearchDesa;
        pCmbRoleSearch = patchouli_grower_ls.pCmbRoleSearch;
        pPartnerSearch = patchouli_grower_ls.pPartnerSearch;
        pPartnerFirstLoad = patchouli_grower_ls.pPartnerFirstLoad;
        pCmbCategorySearch = patchouli_grower_ls.pCmbCategorySearch;
    } else {
        ptextSearch = "";
        ptextSearchDesa = "";
        pCmbRoleSearch = "";
        pPartnerSearch = "";
        pPartnerFirstLoad = "";
        pCmbCategorySearch = "";
    }
    var spCmbRoleSearch = pCmbRoleSearch.toString();

    var patchouli_grower_adv_ls = JSON.parse(localStorage.getItem('patchouli_grower_adv_ls'));
    if (patchouli_grower_adv_ls != null) {
        pAdvRowEnumerator = patchouli_grower_adv_ls.pAdvRowEnumerator;
        pAdvTextEnumerator = patchouli_grower_adv_ls.pAdvTextEnumerator;
        pAdvRowHandphone = patchouli_grower_adv_ls.pAdvRowHandphone;
        pAdvTextHandphone = patchouli_grower_adv_ls.pAdvTextHandphone;
        pAdvRowAge = patchouli_grower_adv_ls.pAdvRowAge;
        pAdvOpAge = patchouli_grower_adv_ls.pAdvOpAge;
        pAdvTextAge = patchouli_grower_adv_ls.pAdvTextAge;
        pAdvRowMaritalStatus = patchouli_grower_adv_ls.pAdvRowMaritalStatus;
        pAdvMaritalStatus = patchouli_grower_adv_ls.pAdvMaritalStatus;
        pAdvRowDateCollection = patchouli_grower_adv_ls.pAdvRowDateCollection;
        pAdvDateCollectionBegin = patchouli_grower_adv_ls.pAdvDateCollectionBegin;
        pAdvDateCollectionEnd = patchouli_grower_adv_ls.pAdvDateCollectionEnd;
    } else {
        pAdvRowEnumerator = "";
        pAdvTextEnumerator = "";
        pAdvRowHandphone = "";
        pAdvTextHandphone = "";
        pAdvRowAge = "";
        pAdvOpAge = "";
        pAdvTextAge = "";
        pAdvRowMaritalStatus = "";
        pAdvMaritalStatus = "";
        pAdvRowDateCollection = "";
        pAdvDateCollectionBegin = "";
        pAdvDateCollectionEnd = "";
    }
    filters.textSearch             = ptextSearch;
    filters.textSearchDesa         = ptextSearchDesa;
    filters.roleSearch             = spCmbRoleSearch;
    filters.pPartnerSearch         = pPartnerSearch;
    filters.pPartnerFirstLoad      = pPartnerFirstLoad;
    filters.categorySearch         = pCmbCategorySearch;
    filters.AdvRowEnumerator       = pAdvRowEnumerator;
    filters.AdvTextEnumerator      = pAdvTextEnumerator;
    filters.AdvRowHandphone        = pAdvRowHandphone;
    filters.AdvTextHandphone       = pAdvTextHandphone;
    filters.AdvRowAge              = pAdvRowAge;
    filters.AdvOpAge               = pAdvOpAge;
    filters.AdvTextAge             = pAdvTextAge;
    filters.AdvRowMaritalStatus    = pAdvRowMaritalStatus;
    filters.AdvMaritalStatus       = pAdvMaritalStatus;
    filters.AdvRowDateCollection   = pAdvRowDateCollection;
    filters.AdvDateCollectionBegin = pAdvDateCollectionBegin;
    filters.AdvDateCollectionEnd   = pAdvDateCollectionEnd;

    return filters;
}

function submitOnEnterGridGrower(field, event) {
    if (event.getKey() == event.ENTER) {
        setFilterLs();
        Ext.getCmp('view.FarmerMill.GridMainFarmerMill-gridMainGrid').getStore().loadPage(1);
    }
}

function searchByPartner() {
    setFilterLs();
    Ext.getCmp('view.FarmerMill.GridMainFarmerMill-gridMainGrid').getStore().loadPage(1);
}
// Define Variabel2 / Object2 yg diperlukan oleh view ini (end)

Ext.define('Koltiva.view.FarmerMill.GridMainFarmerMill', {
    extend: 'Ext.panel.Panel',
    id: 'Koltiva.view.FarmerMill.GridMainFarmerMill-MainPanel',
    renderTo: 'ext-content',
    listeners: {
        afterRender: function () {
            // Untuk fix masalah merge (20-01-2020)
            // filter region dimunculkan kembali (20-01-2020)
            // document.getElementById('divCommonContentRegion').style.display = 'none';

            //isikan variabel dari local storage
            var patchouli_grower_ls = JSON.parse(localStorage.getItem('patchouli_grower_ls'));
            if (patchouli_grower_ls != null) {
                // Pake default value dari session (15-01-2020)
                // Ext.getCmp('view.FarmerMill.GridMainFarmerMill-textSearch').setValue(patchouli_grower_ls.ptextSearch);
                // Ext.getCmp('view.FarmerMill.GridMainFarmerMill-CmbCategorySearch').setValue(patchouli_grower_ls.pCmbCategorySearch);
            }

            //load storenya sebelum viewnya aktif
            setFilterLs();
            Ext.getCmp('view.FarmerMill.GridMainFarmerMill-gridMainGrid').getStore().load();
        }
    },
    style: 'padding:0 15px 15px 15px;margin:5px 0 0 0;',
    initComponent: function () {
        var thisObj = this;

        //store yg dipakai (begin)
        var cmb_farmer_category = Ext.create('Koltiva.store.FarmerMill.CmbFarmerCategorySearch');
        //store yg dipakai (end)

        //items
        thisObj.items = [{
                layout: 'column',
                border: false,
                items: [{
                        columnWidth: .3,
                        layout: 'form',
                        items: [{
                                xtype: 'button',
                                id: 'view.FarmerMill.GridMainFarmerMill-btnListView',
                                style: 'margin-top:5px;',
                                text: lang('All Farmers'),
                                arrowAlign: 'right',
                                hidden: true,
                                menu: [{
                                        text: lang('All Farmers'),
                                        listeners: {
                                            click: function () {
                                                alert('All Farmers Click')
                                            }
                                        }
                                    }, {
                                        text: lang('New This Week'),
                                        listeners: {
                                            click: function () {
                                                alert('New This Week Click')
                                            }
                                        }
                                    }, {
                                        text: lang('Modified This Week'),
                                        listeners: {
                                            click: function () {
                                                alert('Modified This Week Click')
                                            }
                                        }
                                    }, {
                                        text: lang('Recently Views'),
                                        listeners: {
                                            click: function () {
                                                alert('Recently Views Click')
                                            }
                                        }
                                    }]
                            }, {
                                xtype: 'textfield',
                                id: 'view.FarmerMill.GridMainFarmerMill-FarmerMillPartnerIDFilter',
                                hidden: true,
                                value: m_partner
                            }, {
                                xtype: 'textfield',
                                id: 'view.FarmerMill.GridMainFarmerMill-FarmerMillFirstLoad',
                                hidden: true,
                                value: "1"
                            }]
                    }, {
                        columnWidth: .7,
                        xtype: 'panel',
                        frame: false,
                        id: 'view.FarmerMill.GridMainFarmerMill-gridInformation',
                        html: ''
                    }]
            }, {
                xtype: 'grid',
                id: 'view.FarmerMill.GridMainFarmerMill-gridMainGrid',
                style: 'border:1px solid #CCC;margin-top:4px;',
                loadMask: true,
                selType: 'rowmodel',
                store: storeGridMain,
                viewConfig: {
                    deferEmptyText: false,
                    emptyText: GetDefaultContentNoData()
                },
                dockedItems: [{
                        xtype: 'pagingtoolbar',
                        id: 'view.FarmerMill.GridMainFarmerMill-gridToolbar',
                        store: storeGridMain,
                        dock: 'bottom',
                        displayInfo: true,
                        displayMsg: 'Showing {0} to {1} of {2} entries'
                    }, {
                        xtype: 'toolbar',
                        dock: 'top',
                        items: [
                            {
                                xtype: 'splitbutton',
                                text: lang('Export'),
                                icon: varjs.config.base_url + 'images/icons/new/export.png', cls:'Sfr_BtnGridPaleBlue',
                                menu: {
                                    items: [
                                        {
                                            text: lang('Export Farmers'),
                                            hidden: false,
                                            handler: function () {
                                                Ext.MessageBox.show({
                                                    msg: 'Please wait...',
                                                    progressText: 'Exporting...',
                                                    width: 300,
                                                    wait: true,
                                                    waitConfig: {
                                                        interval: 200
                                                    },
                                                    icon: 'ext-mb-download', //custom class in msg-box.html
                                                    animateTarget: 'mb7'
                                                });

                                                var filter = getFilterLs();
                                                var keys = Object.keys(filter);
                                                var param_string = '?search=1';
                                                $.each(keys, function (index, val) {
                                                    param_string += '&' + val + '=' + filter[val];
                                                });

                                                try {
                                                    Ext.destroy(Ext.get('downloadIframe'));
                                                } catch (e) {
                                                }

                                                Ext.Ajax.request({
                                                    url: m_api+'/grower/export_farmers_mill/'+param_string,
                                                
                                                    method: 'GET',
                                                    timeout: 360000,
                                                    waitMsg: lang('Please Wait'),
                                                    success: function(data) {
                                                        Ext.MessageBox.hide();
                                                        var jsonResp = JSON.parse(data.responseText);
                                                        window.location = jsonResp.filenya;
                                                    },
                                                    failure: function() {
                                                        Ext.MessageBox.hide();
                                                        Ext.MessageBox.show({
                                                            title: 'Notifications',
                                                            msg: 'Failed to export, Please try again.',
                                                            buttons: Ext.MessageBox.OK,
                                                            animateTarget: 'mb9',
                                                            icon: 'ext-mb-error'
                                                        });
                                                    }
                                                });

                                            }
                                        }
                                    ]
                                }
                            },{
                                id: 'view.FarmerMill.GridMainFarmerMill-CmbCategorySearch',
                                xtype: 'combobox',
                                store: cmb_farmer_category,
                                hidden: false,
                                emptyText: lang('Category Farmers'),
                                displayField: 'label',
                                valueField: 'id',
                                queryMode: 'local',
                                value: (m_grid_filter_farmer_category != "") ? m_grid_filter_farmer_category : 'Mapped',
                                width: 300,
                                listeners: {
                                    change: function(cb, nv, ov) {
                                        setFilterLs();
                                        Ext.getCmp('view.FarmerMill.GridMainFarmerMill-gridMainGrid').getStore().loadPage(1);
                                    }
                                }
                            },{
                                name: 'key', baseCls:'Sfr_TxtfieldSearchGrid',
                                id: 'view.FarmerMill.GridMainFarmerMill-textSearch',
                                xtype: 'textfield',
                                width: 400,
                                emptyText: lang('Cari berdasar nama/ID') + ', ' + lang('Press \'Enter\' to search'),
                                value: (m_grid_filter_farmer_text != "") ? m_grid_filter_farmer_text : '',
                                listeners: {
                                    specialkey: submitOnEnterGridGrower
                                }
                            },{
                                name: 'keyDesa',
                                id: 'view.FarmerMill.GridMainFarmerMill-textSearchDesa',
                                xtype: 'textfield',
                                width: 300,
                                hidden: m_act_search_desa,
                                emptyText: lang('Cari berdasar Desa')+', '+lang('Press \'Enter\' to search'),
                                value: (m_grid_filter_farmer_desa != "") ? m_grid_filter_farmer_desa : '',
                                listeners: {
                                    specialkey: submitOnEnterGridGrower
                                }
                            }, {
                                xtype: 'tbspacer',
                                flex: 1
                            }, {
                                icon: varjs.config.base_url + 'images/icons/new/reload.png', cls:'Sfr_BtnGridBlue', overCls:'Sfr_BtnGridBlue-Hover',
                                tooltip: lang('Reload'),
                                handler: function () {
                                    //reload
                                    setFilterLs();
                                    Ext.getCmp('view.FarmerMill.GridMainFarmerMill-gridMainGrid').getStore().loadPage(1);
                                }
                            }, {
                                text: lang('Apply Filter'),
                                tooltip: lang('Apply Filter'),
                                icon: varjs.config.base_url + 'images/icons/new/add-filter.png',
                                cls:'Sfr_BtnGridPaleBlue',
                                overCls:'Sfr_BtnGridPaleBlue-Hover',
                                handler: function () {
                                    //advanced search
                                    var WinPopupFarmerMillFilter = Ext.create('Koltiva.view.FarmerMill.WinPopupFarmerMillFilter', {
                                        viewVar: {
                                            PartnerID: m_partner
                                        }
                                    });
                                    if (!WinPopupFarmerMillFilter.isVisible()) {
                                        WinPopupFarmerMillFilter.center();
                                        WinPopupFarmerMillFilter.show();
                                    } else {
                                        WinPopupFarmerMillFilter.close();
                                    }
                                }
                            }]
                    }],
                columns: [{
                        text: lang('Action'),
                        xtype: 'actioncolumn',
                        width: '4%',
                        items: [{
                                icon: varjs.config.base_url + 'images/icons/new/action.png',
                                handler: function (grid, rowIndex, colIndex, item, e, record) {
                                    // Pilih dulu baris yg diklik. actioncolumn memanggil handler ini
                                    // SEBELUM RowModel men-select baris (select terjadi di event itemclick),
                                    // jadi tanpa ini getSelection() di menu masih kosong/baris lama -> Farmer Profile tidak jalan.
                                    Ext.getCmp('view.FarmerMill.GridMainFarmerMill-gridMainGrid').getSelectionModel().select(record);
                                    contextMenuGrid.showAt(e.getXY());
                                }
                            }]
                    }, {
                        id: 'view.FarmerMill.GridMainFarmerMill-colid',
                        text: lang('ID'),
                        dataIndex: 'MemberIDInc',
                        hidden: true
                    }, {
                        id: 'view.FarmerMill.GridMainFarmerMill-colPartnerSurvey',
                        dataIndex: 'PartnerSurvey',
                        hidden: true
                    }, {
                        id: 'view.FarmerMill.GridMainFarmerMill-colid',
                        text: lang('FarmerID'),
                        dataIndex: 'id',
                        width: '8%'
                    }, {
                        id: 'view.FarmerMill.GridMainFarmerMill-colFarmerName',
                        text: lang('Farmer Name'),
                        width: '20%',
                        dataIndex: 'Name'
                    }, {
                        id: 'view.FarmerMill.GridMainFarmerMill-colBirthdate',
                        text: lang('Birthdate'),
                        dataIndex: 'Birthdate',
                        width: '7%',
                        hidden: true
                    }, {
                        id: 'view.FarmerMill.GridMainFarmerMill-colAge',
                        text: lang('Age'),
                        dataIndex: 'Age',
                        width: '3%'
                    }, {
                        id: 'view.FarmerMill.GridMainFarmerMill-colHandphone',
                        text: lang('Handphone'),
                        dataIndex: 'Handphone',
                        width: '7%'
                    }, {
                        id: 'view.FarmerMill.GridMainFarmerMill-colMaritalStatus',
                        text: lang('Marital Status'),
                        dataIndex: 'MaritalStatus',
                        hidden: true
                    }, {
                        id: 'view.FarmerMill.GridMainFarmerMill-colProvince',
                        text: lang('Province'),
                        dataIndex: 'Province',
                        hidden: true
                    }, {
                        id: 'view.FarmerMill.GridMainFarmerMill-colDistrict',
                        text: lang('District'),
                        dataIndex: 'District',
                        hidden: true
                    }, {
                        id: 'view.FarmerMill.GridMainFarmerMill-colKecamatan',
                        text: lang('Kecamatan'),
                        width: '10%',
                        dataIndex: 'Kecamatan'
                    }, {
                        id: 'view.FarmerMill.GridMainFarmerMill-colDesa',
                        text: lang('Desa'),
                        width: '11%',
                        dataIndex: 'Desa'
                    }, {
                        id: 'view.FarmerMill.GridMainFarmerMill-colNrOfPlantation',
                        text: lang('Nr Of Plantation'),
                        width: '9%',
                        dataIndex: 'NrOfPlantation'
                    }, {
                        id: 'view.FarmerMill.GridMainFarmerMill-colTotalHectare',
                        text: lang('Total Hectare'),
                        width: '9%',
                        dataIndex: 'TotalHectare'
                    }, {
                        id: 'view.FarmerMill.GridMainFarmerMill-colTotalHectarePolygon',
                        text: lang('Total Hectare Polygon'),
                        width: '9%',
                        dataIndex: 'TotalHectarePolygon',
                        hidden: true
                    }, {
                        id: 'view.FarmerMill.GridMainFarmerMill-colLatitude',
                        text: lang('Latitude'),
                        width: '9%',
                        dataIndex: 'Latitude',
                        hidden: true
                    }, {
                        id: 'view.FarmerMill.GridMainFarmerMill-colLongitude',
                        text: lang('Longitude'),
                        width: '9%',
                        dataIndex: 'Longitude',
                        hidden: true
                    }, {
                        id: 'view.FarmerMill.GridMainFarmerMill-colDateCollection',
                        text: lang('Date Collection'),
                        width: '7%',
                        dataIndex: 'DateCollection'
                    }, {
                        id: 'view.FarmerMill.GridMainFarmerMill-colDateCreated',
                        text: lang('Date Created'),
                        width: '7%',
                        dataIndex: 'DateCreated',
                        hidden: true
                    }, {
                        id: 'view.FarmerMill.GridMainFarmerMill-colEnumerator',
                        text: lang('Enumerator'),
                        width: '11%',
                        dataIndex: 'Enumerator'
                    }]
            }];

        this.callParent(arguments);
    }
});
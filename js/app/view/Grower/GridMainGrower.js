/*
 * @Author: nikolius
 * @Date:   2017-05-16 12:10:39
 * @Last Modified by:   nikolius
 * @Last Modified time: 2017-12-18 13:40:27
 */

/*
    Param2 yg diperlukan ketika load View ini
    1. ....
*/

// Define Variabel2 / Object2 / Fungsi yg diperlukan oleh view ini (begin)

//variabel2 / object2
var contextMenuGrid = Ext.create('Ext.menu.Menu', {
    items: [{
        icon: varjs.config.base_url + 'images/icons/new/view.png',
        text: lang('View'),
        handler: function () {
            var sm = Ext.getCmp('view.Grower.GridMainGrower-gridMainGrid').getSelectionModel().getSelection()[0];

            Ext.getCmp('Koltiva.view.Grower.GridMainGrower-MainPanel').destroy(); //destory current view
            //create object View untuk FormMainGrower
            if (Ext.getCmp('Koltiva.view.Grower.FormMainGrower') == undefined) {
                var FormMainGrower = Ext.create('Koltiva.view.Grower.FormMainGrower', {
                    opsiDisplay: 'view',
                    formVar: {
                        MemberID: sm.get('MemberIDInc'),
                        PartnerSurvey: sm.get('PartnerSurvey')
                    }
                });
            } else {
                //destroy, create ulang
                Ext.getCmp('Koltiva.view.Grower.FormMainGrower').destroy();
                var FormMainGrower = Ext.create('Koltiva.view.Grower.FormMainGrower', {
                    opsiDisplay: 'view',
                    formVar: {
                        MemberID: sm.get('MemberIDInc'),
                        PartnerSurvey: sm.get('PartnerSurvey')
                    }
                });
            }
        }
    }, {
        icon: varjs.config.base_url + 'images/icons/new/update.png',
        text: lang('Update'),
        hidden: m_act_update,
        handler: function () {
            var sm = Ext.getCmp('view.Grower.GridMainGrower-gridMainGrid').getSelectionModel().getSelection()[0];

            Ext.getCmp('Koltiva.view.Grower.GridMainGrower-MainPanel').destroy(); //destory current view
            //create object View untuk FormMainGrower
            if (Ext.getCmp('Koltiva.view.Grower.FormMainGrower') == undefined) {
                var FormMainGrower = Ext.create('Koltiva.view.Grower.FormMainGrower', {
                    opsiDisplay: 'update',
                    formVar: {
                        MemberID: sm.get('MemberIDInc'),
                        PartnerSurvey: sm.get('PartnerSurvey')
                    }
                });
            } else {
                //destroy, create ulang
                Ext.getCmp('Koltiva.view.Grower.FormMainGrower').destroy();
                var FormMainGrower = Ext.create('Koltiva.view.Grower.FormMainGrower', {
                    opsiDisplay: 'update',
                    formVar: {
                        MemberID: sm.get('MemberIDInc'),
                        PartnerSurvey: sm.get('PartnerSurvey')
                    }
                });
            }
        }
    }, {
        icon: varjs.config.base_url + 'images/icons/new/update.png',
        text: lang('Edit Partner Ownership'),
        hidden: m_act_set_partner_member,
        handler: function () {
            var sm = Ext.getCmp('view.Grower.GridMainGrower-gridMainGrid').getSelectionModel().getSelection()[0];

            var frm = Ext.create('Koltiva.view.Grower.FormSetPartner');
            frm.getForm().load({
                method: 'GET',
                params: {
                    MemberID: sm.get('MemberIDInc')
                }
            });
            win = Ext.create('Ext.Window', {
                title: lang('Edit Farmer Ownership'),
                closable: true,
                modal: true,
                autoScroll: true,
                width: '40%',
                items: [frm]
            }).show();
        }
    }, {
        icon: varjs.config.base_url + 'images/icons/new/view.png',
        text: lang('Farmer Profile'),
        handler: function () {
            var url = m_cetak_beneficiary_profiles;
            var sm = Ext.getCmp('view.Grower.GridMainGrower-gridMainGrid').getSelectionModel().getSelection()[0];
            if (sm.get('MemberIDInc') == '') {
                Ext.MessageBox.show({
                    title: 'Warning',
                    msg: lang('No Farmer Selected'),
                    buttons: Ext.MessageBox.OK,
                    animateTarget: 'mb9',
                    icon: 'ext-mb-error'
                });
                return false;
            } else {
                preview_cetak_surat(url + '/MemberID/' + sm.get('MemberIDInc'));
            }
        }
    }, {
        icon: varjs.config.base_url + 'images/icons/new/delete.png',
        text: lang('Delete'),
        hidden: m_act_delete,
        handler: function () {
            var sm = Ext.getCmp('view.Grower.GridMainGrower-gridMainGrid').getSelectionModel().getSelection()[0];

            Ext.MessageBox.confirm('Message', 'Do you want to delete this data ?', function (btn) {
                if (btn == 'yes') {
                    Ext.Ajax.request({
                        waitMsg: 'Please Wait',
                        url: m_api + '/grower/member',
                        method: 'DELETE',
                        params: {
                            MemberID: sm.get('MemberIDInc')
                        },
                        success: function (response, opts) {
                            Ext.MessageBox.show({
                                title: 'Information',
                                msg: lang('Data deleted'),
                                buttons: Ext.MessageBox.OK,
                                animateTarget: 'mb9',
                                icon: 'ext-mb-success'
                            });

                            //refresh store
                            setFilterLs();
                            Ext.getCmp('view.Grower.GridMainGrower-gridMainGrid').getStore().load();
                        },
                        failure: function (response, opts) {
                            var pesanNya;
                            if (o.result.message != undefined) {
                                pesanNya = o.result.message;
                            } else {
                                pesanNya = lang('Connection error');
                            }
                            Ext.MessageBox.show({
                                title: 'Error',
                                msg: pesanNya,
                                buttons: Ext.MessageBox.OK,
                                animateTarget: 'mb9',
                                icon: 'ext-mb-error'
                            });
                        }
                    });
                }
            });

        }
    }]
});

var storeGridMain = Ext.create('Koltiva.store.Grower.GridMain');

function setFilterLs() {
    localStorage.setItem('patchouli_grower_ls', JSON.stringify({
        ptextSearch: Ext.getCmp('view.Grower.GridMainGrower-textSearch').getValue(),
        ptextSearchDesa: Ext.getCmp('view.Grower.GridMainGrower-textSearchDesa').getValue(),
        pCmbRoleSearch: Ext.getCmp('view.Grower.GridMainGrower-CmbRoleSearch').getValue(),
        pCmbCategorySearch: Ext.getCmp('view.Grower.GridMainGrower-CmbCategorySearch').getValue()
    }));
}

function getFilterLs() {
    var filters = {};
    var ptextSearch, ptextSearchDesa, pCmbRoleSearch, pCmbCategorySearch, pAdvRowEnumerator, pAdvTextEnumerator, pAdvRowHandphone, pAdvTextHandphone, pAdvRowAge, pAdvOpAge, pAdvTextAge, pAdvRowMaritalStatus, pAdvMaritalStatus, pAdvDateCollectionBegin, pAdvDateCollectionEnd, pAdvRowDateCollection;

    var patchouli_grower_ls = JSON.parse(localStorage.getItem('patchouli_grower_ls'));
    if (patchouli_grower_ls != null) {
        ptextSearch = patchouli_grower_ls.ptextSearch;
        ptextSearchDesa = patchouli_grower_ls.ptextSearchDesa;
        pCmbRoleSearch = patchouli_grower_ls.pCmbRoleSearch;
        pCmbCategorySearch = patchouli_grower_ls.pCmbCategorySearch;
    } else {
        ptextSearch = "";
        ptextSearchDesa = "";
        pCmbRoleSearch = "";
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
        pAdvRowDateCreated = patchouli_grower_adv_ls.pAdvRowDateCreated;
        pAdvDateCreatedBegin = patchouli_grower_adv_ls.pAdvDateCreatedBegin;
        pAdvDateCreatedEnd = patchouli_grower_adv_ls.pAdvDateCreatedEnd;
        pAdvRowDateSynced = patchouli_grower_adv_ls.pAdvRowDateSynced;
        pAdvDateSyncedBegin = patchouli_grower_adv_ls.pAdvDateSyncedBegin;
        pAdvDateSyncedEnd = patchouli_grower_adv_ls.pAdvDateSyncedEnd;
        pAdvRowLastUpdatedDate = patchouli_grower_adv_ls.pAdvRowLastUpdatedDate;
        pAdvLastUpdatedDateBegin = patchouli_grower_adv_ls.pAdvLastUpdatedBegin;
        pAdvLastUpdatedDateEnd = patchouli_grower_adv_ls.pAdvLastUpdatedEnd;
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
        pAdvRowDateCreated = '';
        pAdvDateCreatedBegin = '';
        pAdvDateCreatedEnd = '';
        pAdvRowDateSynced = '';
        pAdvDateSyncedBegin = '';
        pAdvDateSyncedEnd = '';
        pAdvRowLastUpdatedDate = '';
        pAdvLastUpdatedDateBegin = '';
        pAdvLastUpdatedDateEnd = '';
    }

    filters.prov = m_ProvinceID;
    filters.kab = m_DistrictID;
    filters.kec = m_SubDistrictID;
    filters.textSearch = ptextSearch;
    filters.textSearchDesa = ptextSearchDesa;
    filters.roleSearch = spCmbRoleSearch;
    filters.categorySearch = pCmbCategorySearch;
    filters.AdvRowEnumerator = pAdvRowEnumerator;
    filters.AdvTextEnumerator = pAdvTextEnumerator;
    filters.AdvRowHandphone = pAdvRowHandphone;
    filters.AdvTextHandphone = pAdvTextHandphone;
    filters.AdvRowAge = pAdvRowAge;
    filters.AdvOpAge = pAdvOpAge;
    filters.AdvTextAge = pAdvTextAge;
    filters.AdvRowMaritalStatus = pAdvRowMaritalStatus;
    filters.AdvMaritalStatus = pAdvMaritalStatus;
    filters.AdvRowDateCollection = pAdvRowDateCollection;
    filters.AdvDateCollectionBegin = pAdvDateCollectionBegin;
    filters.AdvDateCollectionEnd = pAdvDateCollectionEnd;
    filters.AdvRowDateCreated = pAdvRowDateCreated;
    filters.AdvDateCreatedBegin = pAdvDateCreatedBegin;
    filters.AdvDateCreatedEnd = pAdvDateCreatedEnd;
    filters.AdvRowDateSynced = pAdvRowDateSynced;
    filters.AdvDateSyncedBegin = pAdvDateSyncedBegin;
    filters.AdvDateSyncedEnd = pAdvDateSyncedEnd;
    filters.AdvRowLastUpdatedDate = pAdvRowLastUpdatedDate;
    filters.AdvLastUpdatedDateBegin = pAdvLastUpdatedDateBegin;
    filters.AdvLastUpdatedDateEnd = pAdvLastUpdatedDateEnd;

    return filters;
}

function submitOnEnterGridGrower(field, event) {
    if (event.getKey() == event.ENTER) {
        setFilterLs();
        if (field.value.length >= 3) {
            Ext.getCmp('view.Grower.GridMainGrower-gridMainGrid').getStore().loadPage(1);
        }
    }
}
// Define Variabel2 / Object2 yg diperlukan oleh view ini (end)

if(m_user_partnerid == 14)
{
    var colFarmerName = Ext.getCmp('view.Grower.GridMainGrower-colFarmerName');  
    colFarmerName = 'Member Name';  
} else {
    var colFarmerName = Ext.getCmp('view.Grower.GridMainGrower-colFarmerName');  
    colFarmerName = 'Farmer Name';  
}

Ext.define('Koltiva.view.Grower.GridMainGrower', {
    extend: 'Ext.panel.Panel',
    id: 'Koltiva.view.Grower.GridMainGrower-MainPanel',
    renderTo: 'ext-content',
    minHeight: 300,
    listeners: {
        afterRender: function () {
            //hilangkan view Filter region
            document.getElementById('divCommonContentRegion').style.display = 'block';

            //isikan variabel dari local storage
            var patchouli_grower_ls = JSON.parse(localStorage.getItem('patchouli_grower_ls'));
            if (patchouli_grower_ls != null) {
                Ext.getCmp('view.Grower.GridMainGrower-CmbRoleSearch').setValue(patchouli_grower_ls.pCmbRoleSearch);
                // Pake default value dari session (15-01-2020)
                // Ext.getCmp('view.Grower.GridMainGrower-textSearch').setValue(patchouli_grower_ls.ptextSearch);
                // Ext.getCmp('view.Grower.GridMainGrower-CmbCategorySearch').setValue(patchouli_grower_ls.pCmbCategorySearch);
            }

            //load storenya sebelum viewnya aktif
            setFilterLs();
            Ext.getCmp('view.Grower.GridMainGrower-gridMainGrid').getStore().load();
        }
    },
    style: 'padding:0 15px 15px 15px;margin:5px 0 0 0;',
    initComponent: function () {
        var thisObj = this;

        //store yg dipakai (begin)
        var cmb_role_member = Ext.create('Koltiva.store.Grower.CmbRoleMember');
        var cmb_farmer_category = Ext.create('Koltiva.store.Grower.CmbFarmerCategorySearch');
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
                    id: 'view.Grower.GridMainGrower-btnListView',
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
                }]
            }, {
                columnWidth: .7,
                xtype: 'panel',
                frame: false,
                id: 'view.Grower.GridMainGrower-gridInformation',
                html: ''
            }]
        }, {
            xtype: 'grid',
            id: 'view.Grower.GridMainGrower-gridMainGrid',
            style: 'border:1px solid #CCC;margin-top:4px;',
            loadMask: true,
            minHeight: 320,
            selType: 'rowmodel',
            store: storeGridMain,
            viewConfig: {
                deferEmptyText: false,
                emptyText: GetDefaultContentNoData()
            },
            dockedItems: [{
                xtype: 'pagingtoolbar',
                id: 'view.Grower.GridMainGrower-gridToolbar',
                store: storeGridMain,
                dock: 'bottom',
                displayInfo: true
            }, {
                xtype: 'toolbar',
                dock: 'top',
                items: [{
                        icon: varjs.config.base_url + 'images/icons/new/add.png',
                        cls: 'Sfr_BtnGridGreen',
                        overCls: 'Sfr_BtnGridGreen-Hover',
                        text: lang('Add'),
                        hidden: m_act_add,
                        handler: function () {
                            Ext.getCmp('Koltiva.view.Grower.GridMainGrower-MainPanel').destroy(); //destory current view

                            //create object View untuk FormMainGrower
                            if (Ext.getCmp('Koltiva.view.Grower.FormMainGrower') == undefined) {
                                var FormMainGrower = Ext.create('Koltiva.view.Grower.FormMainGrower', {
                                    opsiDisplay: 'insert'
                                });
                            } else {
                                //destroy, create ulang
                                Ext.getCmp('Koltiva.view.Grower.FormMainGrower').destroy();
                                var FormMainGrower = Ext.create('Koltiva.view.Grower.FormMainGrower', {
                                    opsiDisplay: 'insert'
                                });
                            }
                        }
                    },
                    {
                        xtype: 'splitbutton',
                        text: lang('Export'),
                        icon: varjs.config.base_url + 'images/icons/new/export.png',
                        cls: 'Sfr_BtnGridPaleBlue',
                        cls: 'Sfr_BtnGridPaleBlue',
                        menu: {
                            items: [{
                                    text: lang('Export Farmers'),
                                    hidden: m_act_export,
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
                                        } catch (e) {}

                                        // Ext.DomHelper.append(document.body, {
                                        //     tag: 'iframe',
                                        //     id:'downloadIframe',
                                        //     frameBorder: 0,
                                        //     width: 0,
                                        //     height: 0,
                                        //     css: 'display:none;visibility:hidden;height:0px;',
                                        //     src: m_api+'/grower/export_farmers/'+param_string
                                        // });
                                        // Ext.MessageBox.hide();

                                        Ext.Ajax.request({
                                            url: m_api + '/grower/export_farmers/' + param_string,

                                            method: 'GET',
                                            waitMsg: lang('Please Wait'),
                                            timeout: 360000,
                                            success: function (data) {
                                                Ext.MessageBox.hide();
                                                var jsonResp = JSON.parse(data.responseText);
                                                window.location = jsonResp.filenya;
                                            },
                                            failure: function () {
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
                                },
                                {
                                    text: lang('Export Farmers STA'),
                                    hidden: true,
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
                                        } catch (e) {}

                                        // Ext.DomHelper.append(document.body, {
                                        //     tag: 'iframe',
                                        //     id:'downloadIframe',
                                        //     frameBorder: 0,
                                        //     width: 0,
                                        //     height: 0,
                                        //     css: 'display:none;visibility:hidden;height:0px;',
                                        //     src: m_api+'/grower/export_farmers/'+param_string
                                        // });
                                        // Ext.MessageBox.hide();

                                        Ext.Ajax.request({
                                            url: m_api + '/grower/export_farmers_sta/' + param_string,

                                            method: 'GET',
                                            waitMsg: lang('Please Wait'),
                                            timeout: 360000,
                                            success: function (data) {
                                                Ext.MessageBox.hide();
                                                var jsonResp = JSON.parse(data.responseText);
                                                window.location = jsonResp.filenya;
                                            },
                                            failure: function () {
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
                                },
                                // {
                                //     text: lang('Export All Dataset'),
                                //     hidden: m_act_export,
                                //     handler: function() {
                                //         Ext.MessageBox.show({
                                //             title: 'Information',
                                //             msg: lang('Coming Soon!'),
                                //             buttons: Ext.MessageBox.OK,
                                //             animateTarget: 'mb9',
                                //             icon: 'ext-mb-info'
                                //         });
                                //         // var filter          = getFilterLs();
                                //         // var keys            = Object.keys(filter);
                                //         // var param_string    = '?search=1';
                                //         // $.each(keys, function(index, val) {
                                //         //     param_string += '&'+val+'='+filter[val];
                                //         // });
                                //         // try {
                                //         //     Ext.destroy(Ext.get('downloadIframe'));
                                //         // }
                                //         // catch(e) {}
                                //         // Ext.DomHelper.append(document.body, {
                                //         //     tag: 'iframe',
                                //         //     id:'downloadIframe',
                                //         //     frameBorder: 0,
                                //         //     width: 0,
                                //         //     height: 0,
                                //         //     css: 'display:none;visibility:hidden;height:0px;',
                                //         //     src: m_api+'/grower/export_all_dataset/'+param_string
                                //         // });
                                //     }
                                // },
                            ]
                        }
                    },
                    {
                        id: 'view.Grower.GridMainGrower-CmbCategorySearch',
                        xtype: 'combobox',
                        store: cmb_farmer_category,
                        hidden: false,
                        emptyText: lang('Category Farmers'),
                        value: (m_grid_filter_farmer_category != "") ? m_grid_filter_farmer_category : 'Mapped',
                        displayField: 'label',
                        valueField: 'id',
                        queryMode: 'local',
                        width: 200,
                        listeners: {
                            change: function (cb, nv, ov) {
                                setFilterLs();
                                Ext.getCmp('view.Grower.GridMainGrower-gridMainGrid').getStore().loadPage(1);
                            }
                        }
                    },
                    {
                        name: 'key',
                        baseCls: 'Sfr_TxtfieldSearchGrid',
                        id: 'view.Grower.GridMainGrower-textSearch',
                        xtype: 'textfield',
                        width: 400,
                        emptyText: lang('Cari berdasar nama/ID') + ', ' + lang('Press \'Enter\' to search'),
                        value: (m_grid_filter_farmer_text != "") ? m_grid_filter_farmer_text : '',
                        minLength: 3,
                        msgTarget: 'side',
                        listeners: {
                            specialkey: submitOnEnterGridGrower,
                            keyup: function (v) {
                                if (v.length < 3) {
                                    this.addCls('error');
                                } else {
                                    this.removeCls('error');
                                }
                            }
                        }
                    },
                    {
                        name: 'keyDesa',
                        baseCls: 'Sfr_TxtfieldSearchGrid',
                        id: 'view.Grower.GridMainGrower-textSearchDesa',
                        xtype: 'textfield',
                        width: 300,
                        hidden: m_act_search_desa,
                        emptyText: lang('Cari berdasar Desa') + ', ' + lang('Press \'Enter\' to search'),
                        value: (m_grid_filter_farmer_desa != "") ? m_grid_filter_farmer_desa : '',
                        listeners: {
                            specialkey: submitOnEnterGridGrower
                        }
                    }, {
                        xtype: 'tbspacer',
                        flex: 0.5
                    }, {
                        id: 'view.Grower.GridMainGrower-CmbRoleSearch',
                        xtype: 'combobox',
                        baseCls: 'Sfr_ComboSearchGrid',
                        store: cmb_role_member,
                        hidden: true,
                        emptyText: lang('Filter Role'),
                        displayField: 'label',
                        valueField: 'id',
                        queryMode: 'local',
                        multiSelect: true,
                        width: 300,
                        listeners: {
                            change: function (cb, nv, ov) {
                                setFilterLs();
                                Ext.getCmp('view.Grower.GridMainGrower-gridMainGrid').getStore().loadPage(1);
                            }
                        }
                    }, {
                        icon: varjs.config.base_url + 'images/icons/new/reload.png',
                        cls: 'Sfr_BtnGridBlue',
                        overCls: 'Sfr_BtnGridBlue-Hover',
                        tooltip: lang('Reload'),
                        handler: function () {
                            //reload
                            setFilterLs();
                            Ext.getCmp('view.Grower.GridMainGrower-gridMainGrid').getStore().loadPage(1);
                        }
                    }, {
                        icon: varjs.config.base_url + 'images/icons/silk/grid.png',
                        cls: 'Sfr_BtnGridPaleBlue',
                        tooltip: lang('Custom Field Grid'),
                        handler: function () {
                            //display field grid
                            var winGridDisplay = Ext.create('Koltiva.view.Grower.WinGridDisplay');
                            if (!winGridDisplay.isVisible()) {
                                winGridDisplay.center();
                                winGridDisplay.show();
                            } else {
                                winGridDisplay.close();
                            }
                        }
                    }, {
                        icon: varjs.config.base_url + 'images/icons/silk/find.png',
                        cls: 'Sfr_BtnGridPaleBlue',
                        tooltip: lang('Advanced Filter'),
                        handler: function () {
                            //advanced search
                            var winAdvFilter = Ext.create('Koltiva.view.Grower.WinAdvancedFilter');
                            if (!winAdvFilter.isVisible()) {
                                winAdvFilter.center();
                                winAdvFilter.show();
                            } else {
                                winAdvFilter.close();
                            }
                        }
                    }
                ]
            }],
            columns: [{
                text: lang('Action'),
                xtype: 'actioncolumn',
                width: '4%',
                items: [{
                    icon: varjs.config.base_url + 'images/icons/new/action.png',
                    handler: function (grid, rowIndex, colIndex, item, e, record) {
                        contextMenuGrid.showAt(e.getXY());
                    }
                }]
            }, {
                id: 'view.Grower.GridMainGrower-colid',
                text: lang('ID'),
                dataIndex: 'MemberIDInc',
                hidden: true
            }, {
                id: 'view.Grower.GridMainGrower-colPartnerSurvey',
                dataIndex: 'PartnerSurvey',
                hidden: true
            }, {
                id: 'view.Grower.GridMainGrower-colid',
                text: lang('FarmerID'),
                dataIndex: 'id',
                width: '8%'
            }, {
                id: 'view.Grower.GridMainGrower-colFarmerName',
                text: colFarmerName,
                width: '20%',
                dataIndex: 'Name'
            }, {
                id: 'view.Grower.GridMainGrower-colSupplyBaseType',
                text: 'Supply Base',
                width: '9%',
                dataIndex: 'SupplybaseType'
            }, {
                id: 'view.Grower.GridMainGrower-colBirthdate',
                text: lang('Birthdate'),
                dataIndex: 'Birthdate',
                width: '7%',
                hidden: true
            }, {
                id: 'view.Grower.GridMainGrower-colAge',
                text: lang('Age'),
                dataIndex: 'Age',
                width: '3%'
            }, {
                id: 'view.Grower.GridMainGrower-colHandphone',
                text: lang('Handphone'),
                dataIndex: 'Handphone',
                width: '7%'
            }, {
                id: 'view.Grower.GridMainGrower-colMaritalStatus',
                text: lang('Marital Status'),
                dataIndex: 'MaritalStatus',
                hidden: true
            }, {
                id: 'view.Grower.GridMainGrower-colProvince',
                text: lang('Province'),
                dataIndex: 'Province',
                hidden: true
            }, {
                id: 'view.Grower.GridMainGrower-colDistrict',
                text: lang('District'),
                dataIndex: 'District',
                hidden: true
            }, {
                id: 'view.Grower.GridMainGrower-colKecamatan',
                text: lang('Kecamatan'),
                width: '10%',
                dataIndex: 'Kecamatan'
            }, {
                id: 'view.Grower.GridMainGrower-colDesa',
                text: lang('Desa'),
                width: '11%',
                dataIndex: 'Desa'
            }, {
                id: 'view.Grower.GridMainGrower-colMill',
                text: lang('Mill'),
                width: '8%',
                dataIndex: 'MillName'
            }, {
                id: 'view.Grower.GridMainGrower-colisCertified',
                text: lang('isCertified'),
                width: '8%',
                dataIndex: 'isCertified',
                hidden: m_wags_access_area
            }, {
                id: 'view.Grower.GridMainGrower-colNrOfPlantation',
                text: lang('Nr Of Plantation'),
                width: '9%',
                dataIndex: 'NrOfPlantation'
            }, {
                id: 'view.Grower.GridMainGrower-colTotalHectare',
                text: lang('Total Hectare'),
                width: '9%',
                dataIndex: 'TotalHectare'
            }, {
                id: 'view.Grower.GridMainGrower-colTotalHectarePolygon',
                text: lang('Total Hectare Polygon'),
                width: '9%',
                dataIndex: 'TotalHectarePolygon',
                hidden: true
            }, {
                id: 'view.Grower.GridMainGrower-colDateCollection',
                text: lang('Date Collection'),
                width: '7%',
                dataIndex: 'DateCollection'
            }, {
                id: 'view.Grower.GridMainGrower-colDateCreated',
                text: lang('Date Created'),
                width: '7%',
                dataIndex: 'DateCreated',
                hidden: true
            }, {
                id: 'view.Grower.GridMainGrower-colLastUpdated',
                text: lang('Last Updated'),
                width: '10%',
                dataIndex: 'LastUpdated'
            }, {
                id: 'view.Grower.GridMainGrower-colEnumerator',
                text: lang('Enumerator'),
                width: '11%',
                dataIndex: 'Enumerator'
            }]
        }];

        this.callParent(arguments);
    }
});
/*
 * @Author: nikolius
 * @Date:   2017-05-16 15:38:19
 * @Last Modified by:   nikolius
 * @Last Modified time: 2017-10-03 16:26:28
 */
/* global Ext */

Ext.define('Koltiva.store.FarmerMill.GridMain', {
    extend: 'Ext.data.Store',
    id: 'store.FarmerMill.GridMain',
    fields: ['MemberIDInc', 'id', 'Name', 'Desa', 'Kecamatan', 'LastUpdated', 'Province', 'District', 'Birthdate', 'Age', 'DateCollection', 'Handphone', 'MaritalStatus', 'MemberRole', 'Enumerator', 'DateCreated', 'NrOfPlantation', 'TotalHectare', 'TotalHectarePolygon', 'PartnerSurvey','Latitude','Longitude'],
    pageSize: 20,
    autoLoad: false,
    remoteSort: true,
    proxy: {
        type: 'ajax',
        extraParams: {
            prov: m_ProvinceID,
            kab: m_DistrictID,
            kec: m_SubDistrictID,
        },
        url: m_api + '/grower/grid_mill_main',
        reader: {
            type: 'json',
            root: 'data',
            totalProperty: 'total'
        }
    },
    listeners: {
        load: function (store, records, success) {
            if (success == true) {
                Ext.Ajax.request({
                    url: m_api + '/tools/information_grid',
                    waitMsg: lang('Please Wait'),
                    success: function (data) {
                        Ext.getCmp('view.FarmerMill.GridMainFarmerMill-gridInformation').update(data.responseText);
                    }
                });
            }
        },
        sort: function (store, records, success) {
            if (success == true) {
                Ext.Ajax.request({
                    url: m_api + '/tools/information_grid',
                    waitMsg: lang('Please Wait'),
                    success: function (data) {
                        Ext.getCmp('view.FarmerMill.GridMainFarmerMill-gridInformation').update(data.responseText);
                    }
                });
            }
        },
        beforeload: function (store, operation, options) {
            var ptextSearch, ptextSearchDesa, pCmbRoleSearch, pPartnerSearch, pCmbCategorySearch, pPartnerFirstLoad, pAdvRowEnumerator, pAdvTextEnumerator, pAdvRowHandphone, pAdvTextHandphone, pAdvRowAge, pAdvOpAge, pAdvTextAge, pAdvRowMaritalStatus, pAdvMaritalStatus, pAdvRowDateCollection, pAdvDateCollectionBegin, pAdvDateCollectionEnd;

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

            store.proxy.extraParams.pPartnerSearch = pPartnerSearch;
            store.proxy.extraParams.pPartnerFirstLoad = pPartnerFirstLoad;
            store.proxy.extraParams.prov = m_ProvinceID;
            store.proxy.extraParams.kab = m_DistrictID;
            store.proxy.extraParams.kec = m_SubDistrictID;
            store.proxy.extraParams.roleSearch = spCmbRoleSearch;

            store.proxy.extraParams.textSearch =  Ext.getCmp('view.FarmerMill.GridMainFarmerMill-textSearch').getValue();
            store.proxy.extraParams.textSearchDesa = Ext.getCmp('view.FarmerMill.GridMainFarmerMill-textSearchDesa').getValue();
            store.proxy.extraParams.categorySearch = Ext.getCmp('view.FarmerMill.GridMainFarmerMill-CmbCategorySearch').getValue();

            store.proxy.extraParams.AdvRowEnumerator = pAdvRowEnumerator;
            store.proxy.extraParams.AdvTextEnumerator = pAdvTextEnumerator;
            store.proxy.extraParams.AdvRowHandphone = pAdvRowHandphone;
            store.proxy.extraParams.AdvTextHandphone = pAdvTextHandphone;
            store.proxy.extraParams.AdvRowAge = pAdvRowAge;
            store.proxy.extraParams.AdvOpAge = pAdvOpAge;
            store.proxy.extraParams.AdvTextAge = pAdvTextAge;
            store.proxy.extraParams.AdvRowMaritalStatus = pAdvRowMaritalStatus;
            store.proxy.extraParams.AdvMaritalStatus = pAdvMaritalStatus;
            store.proxy.extraParams.AdvRowDateCollection = pAdvRowDateCollection;
            store.proxy.extraParams.AdvDateCollectionBegin = pAdvDateCollectionBegin;
            store.proxy.extraParams.AdvDateCollectionEnd = pAdvDateCollectionEnd;
        }
    }
});
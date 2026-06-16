/*
* @Author: fashah
* @Date:   2021-05-16 15:38:19
* @Last Modified by:   fashah
* @Last Modified time: 2021-10-03 16:26:28 
*/
Ext.define('Koltiva.store.GrowerSME.GridMain', {
    extend: 'Ext.data.Store',
    id: 'store.Grower.GridMain',
    fields: ['MemberIDInc','id', 'Name','MillName','Desa','Kecamatan','LastUpdated','Province','District','Birthdate','Age','DateCollection','Handphone','MaritalStatus', 'MemberRole','Enumerator','DateCreated','NrOfPlantation','TotalHectare','TotalHectarePolygon','PartnerSurvey','isCertified','SupplybaseType','DateStart','DateEnd','Expired'],
    pageSize: 30,
    autoLoad: false,
    remoteSort: true,
    proxy: {
        type: 'ajax',
        extraParams: {
            prov: m_ProvinceID,
            kab: m_DistrictID,
            kec: m_SubDistrictID,
            SupplychainID: m_supplychain_id,
        },
        url: m_api + '/grower/grid_main_sme',
        reader: {
            type: 'json',
            root: 'data',
            totalProperty: 'total'
        }
    },
    listeners: {
        load: function(store, records, success) {
            if(success == true){
                Ext.Ajax.request({
                    url: m_api + '/tools/information_grid',
                    waitMsg: lang('Please Wait'),
                    success: function(data) {
                        Ext.getCmp('view.Grower.GridMainGrower-gridInformation').update(data.responseText);
                    }
                });
            }
        },
        sort: function(store, records, success){
            if(success == true){
                Ext.Ajax.request({
                    url: m_api + '/tools/information_grid',
                    waitMsg: lang('Please Wait'),
                    success: function(data) {
                        Ext.getCmp('view.Grower.GridMainGrower-gridInformation').update(data.responseText);
                    }
                });
            }
        },
        beforeload: function(store, operation, options){
            var ptextSearch, ptextSearchDesa, pCmbRoleSearch, pCmbCategorySearch, pAdvRowEnumerator, pAdvTextEnumerator, pAdvRowHandphone, pAdvTextHandphone, pAdvRowAge, pAdvOpAge, pAdvTextAge, pAdvRowMaritalStatus, pAdvMaritalStatus, pAdvRowDateCollection, pAdvDateCollectionBegin, pAdvDateCollectionEnd, pAdvRowDateSynced, pAdvDateSyncedBegin, pAdvDateSyncedEnd, pAdvRowDateCreated, pAdvDateCreatedBegin, pAdvDateCreatedEnd, pAdvRowLastUpdatedDate, pAdvLastUpdatedBegin, pAdvLastUpdatedEnd, pAdvLastUpdatedDateBegin, pAdvLastUpdatedDateEnd;

            var patchouli_grower_ls = JSON.parse(localStorage.getItem('patchouli_grower_ls'));
            if(patchouli_grower_ls != null){
                ptextSearch        = patchouli_grower_ls.ptextSearch;
                ptextSearchDesa    = patchouli_grower_ls.ptextSearchDesa;
                pCmbRoleSearch     = patchouli_grower_ls.pCmbRoleSearch;
                pCmbCategorySearch = patchouli_grower_ls.pCmbCategorySearch;
            }else{
                ptextSearch        = "";
                ptextSearchDesa    = "";
                pCmbRoleSearch     = "";
                pCmbCategorySearch = "";
            }
            var spCmbRoleSearch = pCmbRoleSearch.toString();

            var patchouli_grower_adv_ls = JSON.parse(localStorage.getItem('patchouli_grower_adv_ls'));
            if(patchouli_grower_adv_ls != null){
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
            }else{
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
                pAdvRowDateCreated = "";
                pAdvDateCreatedBegin = "";
                pAdvDateCreatedEnd = "";
                pAdvRowDateSynced = "";
                pAdvDateSyncedBegin = "";
                pAdvDateSyncedEnd = "";
                pAdvRowLastUpdatedDate = "";
                pAdvLastUpdatedDateBegin = "";
                pAdvLastUpdatedDateEnd = "";
            }

            store.proxy.extraParams.prov = m_ProvinceID;
            store.proxy.extraParams.kab = m_DistrictID;
            store.proxy.extraParams.kec = m_SubDistrictID;

            store.proxy.extraParams.textSearch = Ext.getCmp('view.Grower.GridMainGrower-textSearch').getValue();
            store.proxy.extraParams.textSearchDesa = Ext.getCmp('view.Grower.GridMainGrower-textSearchDesa').getValue();
            store.proxy.extraParams.categorySearch = Ext.getCmp('view.Grower.GridMainGrower-CmbCategorySearch').getValue();

            store.proxy.extraParams.roleSearch = spCmbRoleSearch;
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
            store.proxy.extraParams.AdvRowDateCreated = pAdvRowDateCreated;
            store.proxy.extraParams.AdvDateCreatedBegin = pAdvDateCreatedBegin;
            store.proxy.extraParams.AdvDateCreatedEnd = pAdvDateCreatedEnd;
            store.proxy.extraParams.AdvRowDateSynced = pAdvRowDateSynced;
            store.proxy.extraParams.AdvDateSyncedBegin = pAdvDateSyncedBegin;
            store.proxy.extraParams.AdvDateSyncedEnd = pAdvDateSyncedEnd;
            store.proxy.extraParams.AdvRowLastUpdatedDate = pAdvRowLastUpdatedDate;
            store.proxy.extraParams.AdvLastUpdatedDateBegin = pAdvLastUpdatedDateBegin;
            store.proxy.extraParams.AdvLastUpdatedDateEnd = pAdvLastUpdatedDateEnd;
        }
    }
});
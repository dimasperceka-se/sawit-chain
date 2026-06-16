/*
* @Author: nikolius
* @Date:   2017-11-08 15:59:50
* @Last Modified by:   nikolius
* @Last Modified time: 2017-11-08 17:39:13
*/

Ext.define('Koltiva.store.FarmerGroup.GridMain', {
    extend: 'Ext.data.Store',
    id: 'Koltiva.store.FarmerGroup.GridMain',
    storeId: 'Koltiva.store.FarmerGroup.GridMain',
    fields: ['FarmerGroupID','GroupName','YearEstablished','Province','District','FarmerRegistered','LastUpdated'],
    pageSize: 50,
    autoLoad: false,
    remoteSort: true,
    proxy: {
        type: 'ajax',
        extraParams: {
            prov: m_ProvinceID,
            kab: m_DistrictID
        },
        url: m_api + '/farmer_group/grid_main',
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
                        Ext.getCmp('Koltiva.view.FarmerGroup.GridMainFarmerGroup-gridInformation').update(data.responseText);
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
                        Ext.getCmp('Koltiva.view.FarmerGroup.GridMainFarmerGroup-gridInformation').update(data.responseText);
                    }
                });
            }
        },
        beforeload: function(store, operation, options){
            let cof_gridfarmergroup_params = JSON.parse(localStorage.getItem('cof_gridfarmergroup_params'));
            if(cof_gridfarmergroup_params != null){
                store.proxy.extraParams.ArrFilter = cof_gridfarmergroup_params.ArrFilter.join(',');
                store.proxy.extraParams.CmbFilterProvince = cof_gridfarmergroup_params.CmbFilterProvince;
                store.proxy.extraParams.CmbFilterDistrict = cof_gridfarmergroup_params.CmbFilterDistrict;
                store.proxy.extraParams.CmbFilterSubDistrict = cof_gridfarmergroup_params.CmbFilterSubDistrict;
                store.proxy.extraParams.CmbFilterVillage = cof_gridfarmergroup_params.CmbFilterVillage;
                store.proxy.extraParams.TextFilterID = cof_gridfarmergroup_params.TextFilterID;
                store.proxy.extraParams.TextFilterName = cof_gridfarmergroup_params.TextFilterName;
                store.proxy.extraParams.prov  = m_ProvinceID;
                store.proxy.extraParams.kab   = m_DistrictID;
            } else {
                //reset params
                store.proxy.extraParams.ArrFilter = null;
                store.proxy.extraParams.CmbFilterProvince = null;
                store.proxy.extraParams.CmbFilterDistrict = null;
                store.proxy.extraParams.CmbFilterSubDistrict = null;
                store.proxy.extraParams.CmbFilterVillage = null;
                store.proxy.extraParams.TextFilterID = null;
                store.proxy.extraParams.TextFilterName = null;
                store.proxy.extraParams.prov  = m_ProvinceID;
                store.proxy.extraParams.kab   = m_DistrictID;
            }
        }
    }
});
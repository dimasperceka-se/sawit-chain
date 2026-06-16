/******************************************
 *  Author : n1colius.lau@gmail.com   
 *  Created On : Thu May 02 2019
 *  File : GridMain.js
 *******************************************/

Ext.define('Koltiva.store.Tph.GridMain', {
    extend: 'Ext.data.Store',
    id: 'Koltiva.store.Tph.GridMain',
    fields: ['CollectpointID','CollectpointDisplayID','Name','OrgTypeLabel','OrgIDLabel','SubDistrict','Village','Latitude','Longitude','LastUpdated'],
    pageSize: 50,
    autoLoad: false,
    remoteSort: true,
    storeVar: {},
    setStoreVar: function(value){
        this.storeVar = value;
    },
    proxy: {
        type: 'ajax',
        extraParams: {
            prov: m_ProvinceID,
            kab: m_DistrictID,
            kec: m_SubDistrictID,
        },
        url: m_api + '/tph/grid_main',
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
                        Ext.getCmp('Koltiva.view.Tph.GridMainTph-GridInformation').update(data.responseText);
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
                        Ext.getCmp('Koltiva.view.Tph.GridMainTph-GridInformation').update(data.responseText);
                    }
                });
            }
        },
        beforeload: function(store, operation, options){
            let cof_gridtph_params = JSON.parse(localStorage.getItem('cof_gridtph_params'));
            if(cof_gridtph_params != null){
                store.proxy.extraParams.ArrFilter = cof_gridtph_params.ArrFilter.join(',');
                store.proxy.extraParams.CmbFilterProvince = cof_gridtph_params.CmbFilterProvince;
                store.proxy.extraParams.CmbFilterDistrict = cof_gridtph_params.CmbFilterDistrict;
                store.proxy.extraParams.CmbFilterSubDistrict = cof_gridtph_params.CmbFilterSubDistrict;
                store.proxy.extraParams.CmbFilterVillage = cof_gridtph_params.CmbFilterVillage;
                store.proxy.extraParams.TextFilterID = cof_gridtph_params.TextFilterID;
                store.proxy.extraParams.TextFilterName = cof_gridtph_params.TextFilterName;
            } else {
                //reset params
                store.proxy.extraParams.ArrFilter = null;
                store.proxy.extraParams.CmbFilterProvince = null;
                store.proxy.extraParams.CmbFilterDistrict = null;
                store.proxy.extraParams.CmbFilterSubDistrict = null;
                store.proxy.extraParams.CmbFilterVillage = null;
                store.proxy.extraParams.TextFilterID = null;
                store.proxy.extraParams.TextFilterName = null;
            }
        }
    }
});

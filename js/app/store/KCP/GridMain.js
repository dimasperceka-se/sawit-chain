/*
* @Author: nikolius
* @Date:   2017-08-03 15:28:31
* @Last Modified by:   nikolius
* @Last Modified time: 2017-08-08 12:11:43
*/

Ext.define('Koltiva.store.KCP.GridMain', {
    extend: 'Ext.data.Store',
    id: 'Koltiva.store.KCP.GridMain',
    fields: ['id','KCPDisplayID','Name','Alias','Address','Province','District','Kecamatan','Desa','StatusPerusahaan','TahunTerbentuk','Phone','TotalPermanentEmployee','LastUpdated','CompanyName', 'GPS'],
    pageSize: 50,
    autoLoad: false,
    remoteSort: true,
    proxy: {
        type: 'ajax',
        extraParams: {
            prov: m_ProvinceID,
            kab: m_DistrictID,
            kec: m_SubDistrictID,
        },
        url: m_api + '/kcp_bulk/grid_main',
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
                        Ext.getCmp('Koltiva.view.KCP.GridMainKCPBulking-gridInformation').update(data.responseText);
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
                        Ext.getCmp('Koltiva.view.KCP.GridMainKCPBulking-gridInformation').update(data.responseText);
                    }
                });
            }
        },
        beforeload: function(store, operation, options){
            var ptextSearch;

            var patchouli_mill_ls = JSON.parse(localStorage.getItem('patchouli_mill_ls'));
            if(patchouli_mill_ls != null){
                ptextSearch = patchouli_mill_ls.ptextSearch;
            }else{
                ptextSearch = "";
            }

            store.proxy.extraParams.prov = m_ProvinceID;
            store.proxy.extraParams.kab = m_DistrictID;
            store.proxy.extraParams.kec = m_SubDistrictID;
            store.proxy.extraParams.textSearch = ptextSearch;
        }
    }
});
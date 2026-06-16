/*
* @Author: nikolius
* @Date:   2017-08-03 15:28:31
* @Last Modified by:   nikolius
* @Last Modified time: 2017-08-08 12:11:43
*/

Ext.define('Koltiva.store.Mill.GridMain', {
    extend: 'Ext.data.Store',
    id: 'Koltiva.store.Mill.GridMain',
    fields: ['id','MillDisplayID','Name','Alias','Address','Province','District','Kecamatan','Desa','StatusPerusahaan','TahunTerbentuk','Phone','TotalPermanentEmployee','LastUpdated','GroupName','PartnerID','SetAsPartner','CompanyName', 'SMEName', 'NrPlantation', 'NrFarmer', 'GPS'],
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
        url: m_api + '/mill/grid_main',
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
                        Ext.getCmp('Koltiva.view.Mill.GridMainMill-gridInformation').update(data.responseText);
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
                        Ext.getCmp('Koltiva.view.Mill.GridMainMill-gridInformation').update(data.responseText);
                    }
                });
            }
        },
        beforeload: function(store, operation, options){
            var ptextSearch,pAdvRowStatusPerusahaan,pAdvCmbStatusPerusahaan,pAdvRowTahunTerbentuk,pAdvCmbOpTahunTerbentuk,pAdvTextTahunTerbentuk,pAdvRowPhone,pAdvTextPhone,pAdvRowHavePhoto,pAdvCmbHavePhoto,pAdvRowTotalPermanentEmployee,pAdvCmbOpTotalPermanentEmployee,pAdvTextTotalPermanentEmployee;

            var patchouli_mill_ls = JSON.parse(localStorage.getItem('patchouli_mill_ls'));
            if(patchouli_mill_ls != null){
                ptextSearch = patchouli_mill_ls.ptextSearch;
                pAdvRowStatusPerusahaan = patchouli_mill_ls.pAdvRowStatusPerusahaan;
                pAdvCmbStatusPerusahaan = patchouli_mill_ls.pAdvCmbStatusPerusahaan;
                pAdvRowTahunTerbentuk = patchouli_mill_ls.pAdvRowTahunTerbentuk;
                pAdvCmbOpTahunTerbentuk = patchouli_mill_ls.pAdvCmbOpTahunTerbentuk;
                pAdvTextTahunTerbentuk = patchouli_mill_ls.pAdvTextTahunTerbentuk;
                pAdvRowPhone = patchouli_mill_ls.pAdvRowPhone;
                pAdvTextPhone = patchouli_mill_ls.pAdvTextPhone;
                pAdvRowHavePhoto = patchouli_mill_ls.pAdvRowHavePhoto;
                pAdvCmbHavePhoto = patchouli_mill_ls.pAdvCmbHavePhoto;
                pAdvRowTotalPermanentEmployee = patchouli_mill_ls.pAdvRowTotalPermanentEmployee;
                pAdvCmbOpTotalPermanentEmployee = patchouli_mill_ls.pAdvCmbOpTotalPermanentEmployee;
                pAdvTextTotalPermanentEmployee = patchouli_mill_ls.pAdvTextTotalPermanentEmployee;
            }else{
                ptextSearch = "";
                pAdvRowStatusPerusahaan = "";
                pAdvCmbStatusPerusahaan = "";
                pAdvRowTahunTerbentuk = "";
                pAdvCmbOpTahunTerbentuk = "";
                pAdvTextTahunTerbentuk = "";
                pAdvRowPhone = "";
                pAdvTextPhone = "";
                pAdvRowHavePhoto = "";
                pAdvCmbHavePhoto = "";
                pAdvRowTotalPermanentEmployee = "";
                pAdvCmbOpTotalPermanentEmployee = "";
                pAdvTextTotalPermanentEmployee = "";
            }

            store.proxy.extraParams.prov = m_ProvinceID;
            store.proxy.extraParams.kab = m_DistrictID;
            store.proxy.extraParams.kec = m_SubDistrictID;
            store.proxy.extraParams.textSearch = ptextSearch;
            store.proxy.extraParams.rowStatusPerusahaan = pAdvRowStatusPerusahaan;
            store.proxy.extraParams.cmbStatusPerusahaan = pAdvCmbStatusPerusahaan;
            store.proxy.extraParams.rowTahunTerbentuk = pAdvRowTahunTerbentuk;
            store.proxy.extraParams.cmbOpTahunTerbentuk = pAdvCmbOpTahunTerbentuk;
            store.proxy.extraParams.textTahunTerbentuk = pAdvTextTahunTerbentuk;
            store.proxy.extraParams.rowPhone = pAdvRowPhone;
            store.proxy.extraParams.textPhone = pAdvTextPhone;
            store.proxy.extraParams.rowHavePhoto = pAdvRowHavePhoto;
            store.proxy.extraParams.cmbHavePhoto = pAdvCmbHavePhoto;
            store.proxy.extraParams.rowTotalPermanentEmployee = pAdvRowTotalPermanentEmployee;
            store.proxy.extraParams.cmbOpTotalPermanentEmployee = pAdvCmbOpTotalPermanentEmployee;
            store.proxy.extraParams.textTotalPermanentEmployee = pAdvTextTotalPermanentEmployee;
        }
    }
});
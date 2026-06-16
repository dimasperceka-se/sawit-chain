Ext.define('Koltiva.store.Refinery.GridMain', {
    extend: 'Ext.data.Store',
    id: 'Koltiva.store.Refinery.GridMain',
    fields: ['id','RefineryDisplayID','Name','Alias','Address','Province','District','Kecamatan','Desa','StatusPerusahaan','TahunTerbentuk','Phone','TotalPermanentEmployee','LastUpdated','GroupName','PartnerID','SetAsPartner','CompanyName', 'SMEName', 'NrPlantation', 'NrFarmer', 'GPS'],
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
        url: m_api + '/refinery/grid_main',
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
                        Ext.getCmp('Koltiva.view.Refinery.GridMainRefinery-gridInformation').update(data.responseText);
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
                        Ext.getCmp('Koltiva.view.Refinery.GridMainRefinery-gridInformation').update(data.responseText);
                    }
                });
            }
        },
        beforeload: function(store, operation, options){
            var ptextSearch,pAdvRowStatusPerusahaan,pAdvCmbStatusPerusahaan,pAdvRowTahunTerbentuk,pAdvCmbOpTahunTerbentuk,pAdvTextTahunTerbentuk,pAdvRowPhone,pAdvTextPhone,pAdvRowHavePhoto,pAdvCmbHavePhoto,pAdvRowTotalPermanentEmployee,pAdvCmbOpTotalPermanentEmployee,pAdvTextTotalPermanentEmployee;

            var patchouli_refinery_ls = JSON.parse(localStorage.getItem('patchouli_refinery_ls'));
            if(patchouli_refinery_ls != null){
                ptextSearch = patchouli_refinery_ls.ptextSearch;
                pAdvRowStatusPerusahaan = patchouli_refinery_ls.pAdvRowStatusPerusahaan;
                pAdvCmbStatusPerusahaan = patchouli_refinery_ls.pAdvCmbStatusPerusahaan;
                pAdvRowTahunTerbentuk = patchouli_refinery_ls.pAdvRowTahunTerbentuk;
                pAdvCmbOpTahunTerbentuk = patchouli_refinery_ls.pAdvCmbOpTahunTerbentuk;
                pAdvTextTahunTerbentuk = patchouli_refinery_ls.pAdvTextTahunTerbentuk;
                pAdvRowPhone = patchouli_refinery_ls.pAdvRowPhone;
                pAdvTextPhone = patchouli_refinery_ls.pAdvTextPhone;
                pAdvRowHavePhoto = patchouli_refinery_ls.pAdvRowHavePhoto;
                pAdvCmbHavePhoto = patchouli_refinery_ls.pAdvCmbHavePhoto;
                pAdvRowTotalPermanentEmployee = patchouli_refinery_ls.pAdvRowTotalPermanentEmployee;
                pAdvCmbOpTotalPermanentEmployee = patchouli_refinery_ls.pAdvCmbOpTotalPermanentEmployee;
                pAdvTextTotalPermanentEmployee = patchouli_refinery_ls.pAdvTextTotalPermanentEmployee;
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
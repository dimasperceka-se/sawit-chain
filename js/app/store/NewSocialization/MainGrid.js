Ext.define('Koltiva.store.NewSocialization.MainGrid', {
    extend: 'Ext.data.Store',
    id: 'Koltiva.store.NewSocialization.MainGrid',
    fields: [ 'IMSSocID','IMSMasterID','IMSID','BatchID','BatchNumber', 'PartnerID','EventName','CpgTrainings','ProvinceName','DistrictName','SubDistrictName','peserta','EventStart','EventEnd','EventDays','ProvinceID','DistrictID','SubDistrictID','VillageID','VillageName','Location','PICStaffID','Remarks','SocializationStatus','CertHolderOrgName','SocializationStatus','DateUpdated','label'],
    pageSize: 50,
    autoLoad: true,
    remoteSort: true,
    proxy: { 
        type: 'ajax', 
        url: m_api + '/new_socialization/main_list',
        reader: {
            type: 'json',
            root: 'data',
            totalProperty: 'total'
        }
    }, 
	sorters: [{
		direction:'DESC',
		property :'EventStart'
	}],
    listeners: { 
        
		beforeload: function(store, operation, options){
            var ptextSearch;

            var patchouli_appform_ls = JSON.parse(localStorage.getItem('patchouli_appform_ls'));
            if(patchouli_appform_ls != null){
                ptextSearch = patchouli_appform_ls.ptextSearch;
            }else{
                ptextSearch = "";
            } 
			 
            store.proxy.extraParams.ProvinceID = m_prov;
            store.proxy.extraParams.DistrictID = m_dist;
            store.proxy.extraParams.SubDistrictID = m_subdist;
            store.proxy.extraParams.textSearch = ptextSearch;
        }
    }
});
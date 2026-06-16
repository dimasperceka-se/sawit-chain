/*
* @Author: Aprianto 
*/
Ext.define('Koltiva.store.application_form.GridMain', {
    extend: 'Ext.data.Store',
    id: 'Koltiva.store.ReportChklist.GridMain',
    fields: [ 'ApplicantID','MobileUID','DisplayID','PreIMSID','Fullname','NIN','DateCollection','DateOfBirth','Age','Gender','MaritalStatus','Education','CountryID','ProvinceID','DistrictID','SubDistrictID','VillageID','VillageName','VillageNames','Address','HandphoneType','PhoneNumber','Email','GroupMemberStatus','CPGid','GroupName','NewGroupName','CertHolderID','CertProgID','IMSID','Photo','PhotoDesc','NrOfFarm','HectareOfFarm','LastYearHarvest','NrOfProductiveTrees','Latitude','Longitude','ActiveStatus',{ name :'InactiveReason' , type:'int'},'InactiveRemarks','ParticipateInSocializationStatus','ParticipateInSocializationRemarks','SelectionStatus','SelectionRemarks','LearningContractStatus','LearningContractSign','WithdrawalConsentStatus','WithdrawalConsentSign', 'StatusCode','DateCreated','CreatedBy','DateUpdated','LastModifiedBy','DateSync','uid','PartnerID','FarmertypeID','SocStatus','MemberStatus'],
    pageSize: 50,
    autoLoad: true,
    remoteSort: true,
    proxy: { 
        type: 'ajax', 
        url: m_api + '/application_form/application_store/main_list',
        reader: {
            type: 'json',
            root: 'data',
            totalProperty: 'total'
        }
    },  
	sorters: [{
		direction:'DESC',
		property :'ApplicantID'
	}],
    listeners: { 
        load: function(store, records, success) {
            
			if(success == true){
                Ext.Ajax.request({
                    url: m_api + '/tools/information_grid',
                    waitMsg: lang('Please Wait'),
                    success: function(data) {
                        // Ext.getCmp('Koltiva.view.application_form.GridMainAppForm-gridInformation').update(data.responseText);
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
                        // Ext.getCmp('Koltiva.view.application_form.GridMainAppForm-gridInformation').update(data.responseText);
                    }
                });
            }
        },
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


//////////////////////////////PARTICIPANT////////////////////////////////
Ext.define('GridMainParticipantHistory', {
    extend: 'Ext.data.Store',
    id: 'Koltiva.store.socialization.GridMain',
    fields: [ 'ParticipantID','ApplicantID','IMSSocID', 'DisplayID', 'Recommendation','GroupName','Fullname','Comments','OtherStaffName','SelectionStatus','SelectionStatus_selected','ParticipateInSocializationStatus_check','FieldAgentName','RecommendationStatus','RecommendationDate','Comments','SelectionRemarks','CPGid'],
    pageSize: 50,
    autoLoad: false,
    remoteSort: true,
	setStoreVar: function(value){
        this.storeVar = value;
    },
    proxy: { 
        type: 'ajax', 
        url: m_api + '/application_form/application_store/main_list_participant',
        reader: {
            type: 'json',
            root: 'data',
            totalProperty: 'total'
        }
    },
    listeners: {  
		beforeload: function(store, operation, options){ 
			store.proxy.extraParams.ApplicantID =  this.storeVar.ApplicantID; 
        }
    }
});
 

  
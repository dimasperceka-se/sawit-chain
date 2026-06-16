/*
* @Author: Aprianto 
*/
Ext.define('Koltiva.store.socialization.GridMain', {
    extend: 'Ext.data.Store',
    id: 'Koltiva.store.socialization.GridMain',
    fields: [ 'IMSSocID','IMSMasterID','IMSID','BatchID','BatchNumber', 'PartnerID','EventName','CpgTrainings','Province','District','SubDistrict','peserta','EventStart','EventEnd','EventDays','ProvinceID','DistrictID','SubDistrictID','VillageID','VillageName','Location','PICStaffID','Remarks','SocializationStatus','CertHolderOrgName','SocializationStatus','DateUpdated'],
    pageSize: 50,
    autoLoad: true,
    remoteSort: true,
    proxy: { 
        type: 'ajax', 
        url: m_api + '/socialization/application_store/main_list',
        reader: {
            type: 'json',
            root: 'data',
            totalProperty: 'total'
        }
    }, 
	sorters: [{
		direction:'ASC',
		property :'EventName'
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


//////////////////////////////PARTICIPANT////////////////////////////////
Ext.define('storeGridParticipant', {
    extend: 'Ext.data.Store',
    id: 'Koltiva.store.socialization.GridMain',
    fields: [ 'ParticipantID','ApplicantID','MobileUID','IMSSocID', 'DisplayID', 'Recommendation','GroupName','Fullname','Comments','OtherStaffName','SelectionStatus','SelectionStatus_selected','ParticipateInSocializationStatus_check','FieldAgentName','RecommendationStatus','RecommendationDate','Comments','SelectionRemarks','CPGid','GroupName'],
    pageSize: 25,
    autoLoad: false,
    remoteSort: true,
	setStoreVar: function(value){
        this.storeVar = value;
    },
    proxy: { 
        type: 'ajax', 
        url: m_api + '/socialization/application_store/main_list_participant',
        reader: {
            type: 'json',
            root: 'data',
            totalProperty: 'total'
        }
    },	  
	sorters: [{
		direction:'ASC',
		property :'Fullname'
	}],
    listeners: {  
		beforeload: function(store, operation, options){ 
			var IMSSocID  = localStorage.getItem('IMSSocID'); 
			if(IMSSocID != null || IMSSocID != 'undefined' ){
                store.proxy.extraParams.IMSSocID =  IMSSocID;   
            } 
			
        }
    }
});

//////////////////////////////APPLICATION DATA////////////////////////////////
Ext.define('StoreGridMainApplication', {
    extend: 'Ext.data.Store',
    id: 'Koltiva.store.ReportChklist.GridMain',
    fields: [ 'ApplicantID','Province','District','SubDistrict','Fullname','VillageNames','GroupName'],
    pageSize: 20 ,
    autoLoad: true,
    remoteSort: true, 
	setStoreVar: function(value){
        this.storeVar = value;
    },
    proxy: { 
        type: 'ajax', 
        url: m_api + '/socialization/application_store/main_application_list',
        reader: {
            type: 'json',
            root: 'data',
            totalProperty: 'total'
        }
    },  
	sorters: [{
		direction:'ASC',
		property :'ApplicantID'
	}],
    listeners: { 
		beforeload: function(store, operation, options){ 
		   store.proxy.extraParams.IMSID = this.storeVar.IMSID; 
		   store.proxy.extraParams.IMSSocID = this.storeVar.IMSSocID; 
		   
        }
    }
});

//////////////////////////////ATTANDANCE DATA////////////////////////////////
Ext.define('storeAttandanceList', {
    extend: 'Ext.data.Store',
    id: 'Koltiva.store.socialization.AttandanceList',
    fields: [ 'IMSSocID', 'ApplicantID','MobileUID','participantID','Fullname',{name :'checkbox_status', type : 'boolean'}, 'DayNumber'],
    pageSize: 25,
    autoLoad: false,
    remoteSort: true, 
	setStoreVar: function(value){
        this.storeVar = value;
    },
    proxy: { 
        type: 'ajax', 
        url: m_api + '/socialization/application_store/main_attandance_list',
        reader: {
            type: 'json',
            root: 'data',
            totalProperty: 'total'
        }
    },
	sorters: [{
		direction:'ASC',
		property :'ApplicantID'
	}],
    listeners: {  
		beforeload: function(store, operation, options){  
		   store.proxy.extraParams.DayNumber = this.storeVar.DayNumber;
		   store.proxy.extraParams.IMSSocID = this.storeVar.IMSSocID;  
        }
    }
});



//////////////////////////////STAFF////////////////////////////////
Ext.define('storeGridMainStaff', {
    extend: 'Ext.data.Store',
    id: 'Koltiva.store.socialization.GridMain',
    fields: [ 'SocStaffID','IMSSocID','PersonNm', 'StaffID'],
    pageSize: 50,
    autoLoad: true,
    remoteSort: true,
	setStoreVar: function(value){
        this.storeVar = value;
    },
    proxy: { 
        type: 'ajax', 
        url: m_api + '/socialization/application_store/main_list_staff',
        reader: {
            type: 'json',
            root: 'data',
            totalProperty: 'total'
        }
    },
    listeners: {  
		beforeload: function(store, operation, options){ 
			var IMSSocID  = localStorage.getItem('IMSSocID'); 
			if(IMSSocID != null || IMSSocID != 'undefined' ){
                store.proxy.extraParams.IMSSocID =  IMSSocID;   
            } 
			
        }
    }
});
  
 Ext.define('storeGridListStaff', {
    extend: 'Ext.data.Store',
    id: 'Koltiva.store.socialization.GridListStaff',
    fields: [ 'StaffID','PersonNm',{name : 'status_checked', type :'boolean'}],
    pageSize: 50,
    autoLoad: false,
    remoteSort: true,
    storeVar: false,
    setStoreVar: function(value){
        this.storeVar = value;
    },
    proxy: { 
        type: 'ajax', 
        url: m_api + '/socialization/application_store/main_staff',
        reader: {
                type: 'json',
                root: 'data',
                totalProperty: 'total'
            }
    },
    listeners: {  
		beforeload: function(store, operation, options){   
		   store.proxy.extraParams.IMSSocID = this.storeVar.IMSSocID;  
        }
    }
});

//////////////////////////////ATTANDANCE DATA STAFF////////////////////////////////
Ext.define('storeAttandanceStaffList', {
    extend: 'Ext.data.Store',
    id: 'Koltiva.store.socialization.AttandanceStaffList',
    fields: [ 'IMSSocID', 'StaffID','PersonNm',{name :'AttendanceStatus', type : 'boolean'}, 'DayNumber'],
    pageSize: 50,
    autoLoad: false,
    remoteSort: true, 
	setStoreVar: function(value){
        this.storeVar = value;
    },
    proxy: { 
        type: 'ajax', 
        url: m_api + '/socialization/application_store/main_staffattandance_list',
        reader: {
            type: 'json',
            root: 'data',
            totalProperty: 'total'
        }
    },
    listeners: {  
		beforeload: function(store, operation, options){  
		   store.proxy.extraParams.DayNumber = this.storeVar.DayNumber;
		   store.proxy.extraParams.IMSSocID = this.storeVar.IMSSocID;  
        }
    }
});


//////////////////////////////EXISTING FARMER DATA////////////////////////////////
Ext.define('StoreExGridMainApplication', {
    extend: 'Ext.data.Store',
    id: 'Koltiva.store.ReportChklist.GridMain',
    fields: [ 'FarmerID','FarmerName','Province','District', 'SubDistrict', 'Village'],
    pageSize: 20 ,
    autoLoad: true,
    remoteSort: true, 
	setStoreVar: function(value){
        this.storeVar = value;
    },
    proxy: { 
        type: 'ajax', 
        url: m_api + '/socialization/application_store/main_existingfarmer_list',
        reader: {
            type: 'json',
            root: 'data',
            totalProperty: 'total'
        }
    },  
	sorters: [{
		direction:'ASC',
		property :'FarmerID'
	}],
    listeners: { 
         
		beforeload: function(store, operation, options){
           store.proxy.extraParams.ProvinceID = this.storeVar.ProvinceID;
        }
    }
});
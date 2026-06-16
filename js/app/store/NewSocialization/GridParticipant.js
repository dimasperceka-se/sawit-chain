Ext.define('Koltiva.store.NewSocialization.GridParticipant', {
    extend: 'Ext.data.Store',
    id: 'Koltiva.store.NewSocialization.GridParticipant',
    fields: [ 'ParticipantID','ApplicantID','MobileUID','IMSSocID', 'DisplayID', 'Recommendation','GroupName','Fullname','Comments','OtherStaffName','SelectionStatus','SelectionStatus_selected','ParticipateInSocializationStatus_check','FieldAgentName','RecommendationStatus','RecommendationDate','Comments','SelectionRemarks','CPGid','GroupName', 'AttendanceStatus'],
    pageSize: 25,
    autoLoad: false,
    remoteSort: true,
	setStoreVar: function(value){
        this.storeVar = value;
    },
    proxy: { 
        type: 'ajax', 
        url: m_api + '/new_socialization/main_list_participant',
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
            store.proxy.extraParams.IMSSocID =  this.storeVar.IMSSocID;   
        }
    }
});
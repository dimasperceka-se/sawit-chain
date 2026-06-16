Ext.define('Koltiva.store.NewSocialization.GridAttandance', {
    extend: 'Ext.data.Store',
    id: 'Koltiva.store.NewSocialization.GridAttandance',
    fields: [ 'IMSSocID', 'ApplicantID','MobileUID','participantID','Fullname',{name :'checkbox_status', type : 'boolean'}, 'DayNumber'],
    pageSize: 25,
    autoLoad: false,
    remoteSort: true, 
	setStoreVar: function(value){
        this.storeVar = value;
    },
    proxy: { 
        type: 'ajax', 
        url: m_api + '/new_socialization/main_attandance_list',
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
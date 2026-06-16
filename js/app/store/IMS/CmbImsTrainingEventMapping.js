Ext.define('Koltiva.store.IMS.CmbImsTrainingEventMapping', {
    extend: 'Ext.data.Store',
    id: 'Koltiva.store.IMS.CmbImsTrainingEventMapping',
    fields: ['id', 'label'],
    data: [
    		{
	            "id": "Applicant",
	            "label": lang('Applicant')
        	},
        	{
	            "id": "Existing Farmer",
	            "label": lang('Existing Farmer')
        	},
        	{
	            "id": "Existing Certified Farmer",
	            "label": lang('Existing Certified Farmer')
        	}
        ]
});
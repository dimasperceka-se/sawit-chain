Ext.define('Koltiva.store.Traceability_new.Transaction.ComboPlantation', {
    extend: 'Ext.data.Store',
    id: 'Koltiva.store.Traceability_new.Transaction.ComboPlantation',
    storeId: 'Koltiva.store.Traceability_new.Transaction.ComboPlantation',
    fields: ['PlantationNr','SurveyNr','FarmingType','PlantationName','GardenGraciaSP','GardenEucheumaSP','GardenspinosumSP'],
    autoLoad: false,
    storeVar: false,
    setStoreVar: function(value){
        this.storeVar = value;
    },
    proxy: {
        type: 'ajax', 
        url : m_api + '/web-traceability/plantation', 
		reader: {
            type: 'json',  
            root: 'data'
        }
    },
    pageSize: 10,
    listeners: {
        beforeload: function (store, operation) {  
        }
    }
});
 
Ext.define('Koltiva.store.Traceability_new.Transaction_neo.ComboPlantationNonFarmer', {
    extend: 'Ext.data.Store',
    id: 'Koltiva.store.Traceability_new.Transaction_neo.ComboPlantationNonFarmer',
    storeId: 'Koltiva.store.Traceability_new.Transaction_neo.ComboPlantationNonFarmer',
    fields: ['PlantationNr','SurveyNr','FarmingType','PlantationName','GardenGraciaSP','GardenEucheumaSP','GardenspinosumSP'],
    autoLoad: false,
    storeVar: false,
    setStoreVar: function(value){
        this.storeVar = value;
    },
    proxy: {
        type: 'ajax', 
        url : m_api + '/web-traceability/plantationtcnew', 
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
 
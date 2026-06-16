Ext.define('Koltiva.store.FarmerLabourPostline.CmbSurveyNr', {
    extend: 'Ext.data.Store',
    storeId: 'Koltiva.store.FarmerLabourPostline.CmbSurveyNr',
    id: 'Koltiva.store.FarmerLabourPostline.CmbSurveyNr',
    fields: ['id', 'label'],
    autoLoad: true,
    storeVar: false,
    setStoreVar: function(value){
        this.storeVar = value;
    },
    proxy: {
        type: 'ajax',
        url: m_api + '/grower/combo_survey_nr_family_labour_postline',
        reader: {
            type: 'json'
        }
    },
    listeners: {
        beforeload: function(store, operation, options){
            store.proxy.extraParams.from = this.storeVar.from;
        }
    }
});
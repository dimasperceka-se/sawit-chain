Ext.define('Koltiva.model.trainings.list', {
    extend: 'Ext.data.Model',
    fields: [
        {name: 'CpgTrainingsID',          type: 'string'},
        {name: 'CpgTrainings',        type: 'string'},
        {name: 'AltName',         type: 'string'},
        {name: 'CpgAbbre',           type: 'string'},
    ],
    filters: function(type,component) {

    }
});

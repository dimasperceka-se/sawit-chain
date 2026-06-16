Ext.define('Koltiva.model.Reference.Vehicle.ListGrid', {
    extend: 'Ext.data.Model',
    fields: [
        {name: 'BrandID',          type: 'string'},
        {name: 'BrandName',        type: 'string'},
        {name: 'StatusCode',         type: 'string'},
        {name: 'LastUpdated',       type: 'string'}
    ],
    filters: function(type,component) {

    }
});
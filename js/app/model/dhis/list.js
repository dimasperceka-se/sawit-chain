Ext.define('Koltiva.model.dhis.list', {
    extend: 'Ext.data.Model',
    fields: [
        {name: 'FarmerID',          type: 'string'},
        {name: 'FarmerName',        type: 'string'},
        {name: 'GroupName',         type: 'string'},
        {name: 'Village',           type: 'string'},
        {name: 'SubDistrict',       type: 'string'},
        {name: 'YearCertification', type: 'string'},
        {name: 'Production',        type: 'float'},
        {name: 'LandSize',          type: 'float'},
        {name: 'LastUpdated',       type: 'string'},
        {name: 'Synced',            type: 'string'}
    ],
    filters: function(type,component) {

    }
});

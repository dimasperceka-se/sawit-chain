Ext.define('Koltiva.model.Basic.ReportRefinery.List', {
    extend: 'Ext.data.Model',
    fields: [
        {name: 'id', type: 'string'},
        {name: 'Name', type: 'string'},
        {name: 'total_transaction', type: 'string'},
        {name: 'VolumeNetto', type: 'string'},
        {name: 'SupplyOrgID', type: 'string'},
        {name: 'total_farmer', type: 'string'},
    ],
    filters: function(type,component) {

    }
});

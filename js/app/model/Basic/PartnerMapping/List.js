Ext.define('Koltiva.model.Basic.PartnerMapping.List', {
    extend: 'Ext.data.Model',
    fields: [
        {name: 'PartnerID',          type: 'string'},
        {name: 'PartnerParentID',        type: 'string'},
        {name: 'PartnerFullName',         type: 'string'},
    ],
    filters: function(type,component) {

    }
});

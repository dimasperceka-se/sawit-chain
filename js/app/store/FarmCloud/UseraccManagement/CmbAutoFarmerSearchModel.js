Ext.define("Koltiva.store.FarmCloud.UseraccManagement.CmbAutoFarmerSearchModel", {
    extend: 'Ext.data.Model',
    proxy: {
        type: 'ajax',
        url: m_api + '/farmcloud/cmb_auto_farmer_search',
        reader: {
            type: 'json',
            root: 'data',
            totalProperty: 'total'
        }
    },
    fields: [{
        name: 'label',
        mapping: 'label'
    }, {
        name: 'FarmerID',
        mapping: 'FarmerID'
    }, {
        name: 'PartnerID',
        mapping: 'PartnerID'
    },{
        name: 'Province',
        mapping: 'Province'
    },{
        name: 'District',
        mapping: 'District'
    },{
        name: 'SubDistrict',
        mapping: 'SubDistrict'
    },{
        name: 'Village',
        mapping: 'Village'
    },{
        name: 'FarmerName',
        mapping: 'FarmerName'
    },{
        name: 'PartnerLabel',
        mapping: 'PartnerLabel'
    },{
        name: 'Username',
        mapping: 'Username'
    },{
        name: 'Email',
        mapping: 'Email'
    },{
        name: 'Handphone',
        mapping: 'Handphone'
    }]
});
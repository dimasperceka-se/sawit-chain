Ext.define('Koltiva.store.FarmerLabourPostline.GridFarmerLabourPostline', {
    extend: 'Ext.data.Store',
    id: 'Koltiva.store.FarmerLabourPostline.GridFarmerLabourPostline',
    storeId: 'Koltiva.store.FarmerLabourPostline.GridFarmerLabourPostline',
    fields: [
        'LaboPostID'
        ,'LaboID'
        ,'MemberID'
        ,'LaboName'
        ,'survey_number'
        ,'ConductingPostline'
        ,'DateCreated'
        ,'DateUpdated'
    ],
    storeVar: false,
    setStoreVar: function(value){
        this.storeVar = value;
    },
    autoLoad: false,
    pageSize: 10,
    proxy: {
        type: 'ajax',
        url: m_api + '/grower/grid_labour_postline',
        reader: {
            type: 'json',
            root: 'data',
            totalProperty: 'total'
        }
    },
    listeners: {
        beforeload: function(store, operation, options){
            store.proxy.extraParams.MemberID = this.storeVar.MemberID;
        }
    }
});
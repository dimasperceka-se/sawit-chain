Ext.define('Koltiva.store.FamilyLabourPostline.GridFamilyLabourPostline', {
    extend: 'Ext.data.Store',
    id: 'Koltiva.store.FamilyLabourPostline.GridFamilyLabourPostline',
    storeId: 'Koltiva.store.FamilyLabourPostline.GridFamilyLabourPostline',
    fields: [
        'FamLabPostID'
        ,'FamLabID'
        ,'MemberID'
        ,'FamLabName'
        ,'FamLabInterviewDate'
        ,'survey_number'
        ,'conducting_postline'
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
        url: m_api + '/grower/grid_family_labour_postline',
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
Ext.define('Koltiva.store.FamilyLabourPostline.CmbFamMemberName', {
    extend: 'Ext.data.Store',
    storeId: 'Koltiva.store.FamilyLabourPostline.CmbFamMemberName',
    id: 'Koltiva.store.FamilyLabourPostline.CmbFamMemberName',
    fields: [
          'id'
        , 'label'
        , 'interview_date'
        , 'year_birthdate'
        , 'member_id'
    ],
    autoLoad: true,
    pageSize: 10,
    proxy: {
        type: 'ajax',
        url: m_api + '/grower/combo_fam_member_name',
        reader: {
            type: 'json',
            root: 'data'
        }
    },
    listeners: {
        beforeload: function(store, operation, options){
            store.proxy.extraParams.MemberID  = this.storeVar.MemberID;
        }
    }
});
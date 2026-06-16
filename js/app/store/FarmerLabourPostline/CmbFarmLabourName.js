Ext.define('Koltiva.store.FarmerLabourPostline.CmbFarmLabourName', {
    extend: 'Ext.data.Store',
    storeId: 'Koltiva.store.FarmerLabourPostline.CmbFarmLabourName',
    id: 'Koltiva.store.FarmerLabourPostline.CmbFarmLabourName',
    fields: [
          'id'
        , 'label'
        , 'MemberID'
    ],
    autoLoad: true,
    pageSize: 10,
    proxy: {
        type: 'ajax',
        url: m_api + '/grower/combo_farm_labour_name',
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
Ext.define('Koltiva.store.Dboard.CmbFilterDistrictKPI', {
    extend: 'Ext.data.Store',
    storeId: 'Koltiva.store.Dboard.CmbFilterDistrictKPI',
    id: 'Koltiva.store.Dboard.CmbFilterDistrictKPI',
    fields: ['id', 'label'],
    autoLoad: false,
    storeVar: false,
    setStoreVar: function(value){
        this.storeVar = value;
    },
    proxy: {
        type: 'ajax',
        url: m_api + '/common/combo_filter_district',
        reader: {
            type: 'json'
        }
    },
    listeners: {
        beforeload: function(store, operation, options){
            store.proxy.extraParams.ProvinceID = this.storeVar.ProvinceID;
        }
    }
});
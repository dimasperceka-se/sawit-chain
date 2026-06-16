Ext.define('Koltiva.store.Partner.StoreCommodityOptions', {
    extend: 'Ext.data.Store',
    id: 'Koltiva.store.Partner.StoreCommodityOptions',
    storeId: 'Koltiva.store.Partner.StoreCommodityOptions',
    fields: ['id','label'],
    autoLoad: true,
    storeVar: false,
    setStoreVar: function(value){
        this.storeVar = value;
    },
    proxy: {
        type: 'ajax',
        url: m_api + '/partner_new/show_commodity_options',
        reader: {
            type: 'json',
            root: 'data'
        }
    }
});
Ext.define('Koltiva.store.Menu_pull_engine_check.CmbCommodityByPartner', {
    extend: 'Ext.data.Store',
    storeId: 'Koltiva.store.Menu_pull_engine_check.CmbCommodityByPartner',
    id: 'Koltiva.store.Menu_pull_engine_check.CmbCommodityByPartner',
    fields: ['id', 'label'],
    autoLoad: true,
    proxy: {
        type: 'ajax',
        url: m_api + '/farmers_report/combo_filter_commodity_by_partner',
        reader: {
            type: 'json',
            root: 'data'
        }
    },
    listeners: {
        beforeload: function(store, operation, options){
            store.proxy.extraParams.PartnerID = this.storeVar.PartnerID;
        }
    }
});
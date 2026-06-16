Ext.define('Koltiva.store.Partner.StoreOrganizationType', {
    extend: 'Ext.data.Store',
    storeId: 'Koltiva.store.Partner.StoreOrganizationType',
    id: 'Koltiva.store.Partner.StoreOrganizationType',
    fields: ['id', 'label'],
    autoLoad: true,
    proxy: {
        type: 'ajax',
        url: m_api + '/partner_new/cmb_organization_type',
        reader: {
            type: 'json',
            root: 'data'
        }
    }
});
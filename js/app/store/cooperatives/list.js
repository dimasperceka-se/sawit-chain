Ext.define('Koltiva.store.cooperatives.list',{
  extend: 'Ext.data.Store',
  storeId:'koltiva-cooperatives-list',
  fields: ['CoopID', 'CoopCode', 'CoopName', 'Phone', 'Email', 'TahunTerbentuk', 'Status', 'District'],
  autoLoad: true,
  pageSize: 50,
  proxy: {
      type: 'ajax',
      url: m_crud + 's',
      extraParams: {
          prov: m_param
      },
      reader: {
          type: 'json',
          root: 'data',
          totalProperty: 'total'
      }
  }
});

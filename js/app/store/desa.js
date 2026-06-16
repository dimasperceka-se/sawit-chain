Ext.define('Koltiva.store.desa',{
  extend: 'Ext.data.Store',
  storeId:'koltiva-desa-list',
  fields: ['id', 'label'],
  autoLoad: true,
  pageSize: 10,
  proxy: {
      type: 'ajax',
      url: m_Desa,
      reader: {
          type: 'json',
          root: 'data'
      }
  }
});

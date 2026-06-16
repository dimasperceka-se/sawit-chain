Ext.define('Koltiva.store.kecamatan',{
  extend: 'Ext.data.Store',
  storeId:'koltiva-kecamatan-list',
  fields: ['id', 'label'],
  autoLoad: true,
  pageSize: 10,
  proxy: {
      type: 'ajax',
      url: m_Kecamatan,
      reader: {
          type: 'json',
          root: 'data'
      }
  }
});

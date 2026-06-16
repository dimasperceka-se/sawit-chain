Ext.define('Koltiva.store.kabupaten',{
  extend: 'Ext.data.Store',
  storeId:'koltiva-kabupaten-list',
  fields: ['id', 'label'],
  autoLoad: true,
  pageSize: 10,
  proxy: {
      type: 'ajax',
      url: m_Kabupaten,
      reader: {
          type: 'json',
          root: 'data'
      }
  }
});

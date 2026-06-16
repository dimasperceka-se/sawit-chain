Ext.define('Koltiva.store.provinsi',{
  extend: 'Ext.data.Store',
  storeId:'koltiva-provinsi-list',
  fields: ['id', 'label'],
  autoLoad: true,
  pageSize: 10,
  proxy: {
      type: 'ajax',
      url: m_api+'/farmer/Provinsis',
      reader: {
          type: 'json',
          root: 'data'
      }
  }
});

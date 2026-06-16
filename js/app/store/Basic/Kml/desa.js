Ext.define('Koltiva.store.Basic.Kml.desa',{
  extend: 'Ext.data.Store',
  storeId:'Koltiva.store.Basic.Kml.desa',
  fields: ['id', 'label'],
  autoLoad: false,
  pageSize: 10,
  proxy: {
      type: 'ajax',
      url: m_api+'/administration/village_list',
      reader: {
          type: 'json',
          root: 'data'
      }
  }
});

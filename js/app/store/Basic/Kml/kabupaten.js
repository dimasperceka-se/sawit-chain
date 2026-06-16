Ext.define('Koltiva.store.Basic.Kml.kabupaten',{
  extend: 'Ext.data.Store',
  storeId:'Koltiva.store.Basic.Kml.kabupaten',
  fields: ['id', 'label'],
  autoLoad: false,
  pageSize: 10,
  proxy: {
      type: 'ajax',
      url: m_api+'/administration/district_list',
      reader: {
          type: 'json',
          root: 'data'
      }
  }
});

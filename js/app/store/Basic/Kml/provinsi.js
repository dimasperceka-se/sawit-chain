Ext.define('Koltiva.store.Basic.Kml.provinsi',{
  extend: 'Ext.data.Store',
  storeId:'Koltiva.store.Basic.Kml.provinsi',
  fields: ['id', 'label'],
  autoLoad: true,
  pageSize: 10,
  proxy: {
      type: 'ajax',
      url: m_api+'/administration/province_list',
      reader: {
          type: 'json',
          root: 'data'
      }
  }
});

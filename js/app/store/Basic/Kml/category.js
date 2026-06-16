Ext.define('Koltiva.store.Basic.Kml.category',{
  extend: 'Ext.data.Store',
  storeId:'Koltiva.store.Basic.Kml.category',
  fields: ['id', 'label'],
  autoLoad: true,
  pageSize: 10,
  proxy: {
    type: 'ajax',
    url: m_api + '/basic/kml_category',
    reader: {
      type: 'json',
      root: 'data'
    }
  }
});

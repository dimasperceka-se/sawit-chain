Ext.define('Koltiva.store.Basic.Kml.kecamatan',{
  extend: 'Ext.data.Store',
  storeId:'Koltiva.store.Basic.Kml.kecamatan',
  fields: ['id', 'label'],
  autoLoad: false,
  pageSize: 10,
  proxy: {
      type: 'ajax',
      url: m_api+'/administration/subdistrict_list',
      reader: {
          type: 'json',
          root: 'data'
      }
  }
});

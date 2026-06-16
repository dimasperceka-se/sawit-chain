Ext.define('Koltiva.store.dhis.list',{
  extend: 'Ext.data.Store',
  storeId:'koltiva-dhis-list-store',
  model:'Koltiva.model.dhis.list',
  autoLoad: false,
  pageSize: 50,
  proxy: {
      type: 'ajax',
      url: m_crud,
      reader: {
          type: 'json',
          root: 'data',
          totalProperty: 'total'
      }
  }
});

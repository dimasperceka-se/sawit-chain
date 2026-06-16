Ext.define('Koltiva.store.dhis.searchoperator',{
  extend: 'Ext.data.Store',
  fields: ['id', 'label'],
  data: [{
      "id": "=",
      "label": "="
  }, {
      "id": "!=",
      "label": "!="
  }, {
      "id": ">=",
      "label": ">="
  }, {
      "id": "<=",
      "label": "<="
  }]
});

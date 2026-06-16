Ext.define('Koltiva.store.dhis.searchpicker',{
  extend: 'Ext.data.Store',
  fields: ['id', 'label'],
  data: [{
      "id": "container-adv-search-farmerid-dhis-register",
      "label": lang('Cari berdasar nama/ID')
  }, {
      "id": "container-adv-search-province-dhis-register",
      "label": lang('Region')
  }, {
      "id": "container-adv-search-production-dhis-register",
      "label": lang('Produksi')
  }, {
      "id": "container-adv-search-yearcertification-dhis-register",
      "label": lang('Petani Tersertifikasi') + ' ' + lang('Tahun')
  }, {
      "id": "container-adv-search-landsize-dhis-register",
      "label": lang('Land Size')
  }, {
      "id": "container-adv-search-synced-dhis-register",
      "label": lang('Synced')
  }]
});

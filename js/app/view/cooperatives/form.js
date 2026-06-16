Ext.define('Koltiva.view.cooperatives.form',{
  extend:'Ext.form.Panel',
  autoScroll: true,
  bodyPadding: 5,
  fileUpload: true,
  layout:'fit',
  enctype: 'multipart/form-data',
  fieldDefaults: {
      labelAlign: 'left',
      labelWidth: 175,
      anchor: '100%'
  },
  items: [
    {
      xtype: 'tabpanel',
      flex: 1,
      margin: 2,
      activeTab: 0,
      plain: true,
      items:[
        {
          xtype:'panel',
          title: lang('Data Umum'),
          padding: 5,
          style: 'border:2px solid #D6EDA4',
          layout:{
            type:'column'
          },
          items:[
            {
              xtype:'fieldset',
              columnWidth:.5,
              title: lang('Data Perusahaan'),
              items: [{
                  xtype: 'textfield',
                  id: 'CoopName',
                  name: 'CoopName',
                  labelWidth: 180,
                  fieldLabel: lang('Nama')
              }, {
                  xtype: 'textfield',
                  id: 'CoopCode',
                  name: 'CoopCode',
                  labelWidth: 180,
                  fieldLabel: lang('Code')
              }, {
                  xtype: 'textfield',
                  id: 'Phone',
                  name: 'Phone',
                  labelWidth: 180,
                  fieldLabel: lang('No Telepon')
              }, , {
                  xtype: 'textfield',
                  id: 'Email',
                  name: 'Email',
                  labelWidth: 180,
                  fieldLabel: lang('Email')
              }, {
                  xtype: 'radiogroup',
                  labelWidth: 180,
                  fieldLabel: lang('Status Hukum Perusahaan'),
                  columns: 1,
                  items: [{
                      xtype: 'radiofield',
                      boxLabel: lang('Koperasi'),
                      id: 'Status',
                      name: 'Status',
                      inputValue: 'Koperasi'
                  }, {
                      xtype: 'radiofield',
                      boxLabel: lang('Gapoktan'),
                      id: 'Status2',
                      name: 'Status',
                      inputValue: 'Gapoktan'
                  }, {
                      xtype: 'radiofield',
                      boxLabel: lang('KUR'),
                      id: 'Status3',
                      name: 'Status',
                      inputValue: 'KUR'
                  }, {
                      xtype: 'radiofield',
                      boxLabel: lang('Tidak Berbadan Hukum'),
                      id: 'Status4',
                      name: 'Status',
                      inputValue: 'Tidak Berbadan Hukum'
                  }]
              }, {
                  xtype: 'textfield',
                  id: 'TahunTerbentuk',
                  labelWidth: 180,
                  name: 'TahunTerbentuk',
                  fieldLabel: lang('Tahun Berdiri')
              }]
            },
            {
                xtype: 'fieldset',
                title: lang('Lokasi'),
                columnWidth:.5,
                items: [{
                    id: 'Provinsi',
                    name: 'Provinsi',
                    xtype: 'combo',
                    fieldLabel: lang('Provinsi'),
                    store: Ext.create('Koltiva.store.provinsi'),
                    displayField: 'label',
                    valueField: 'id',
                    queryMode: 'local',
                    readOnly: true,
                    listeners: {
                        change: function(cb, nv, ov) {
                            Ext.getCmp('Kabupaten').store.load({
                                params: {
                                    key: Ext.getCmp('Provinsi').getValue()
                                }
                            });
                            Ext.getCmp('Kabupaten').enable();
                        }
                    }
                }, {
                    id: 'Kabupaten',
                    name: 'Kabupaten',
                    xtype: 'combo',
                    fieldLabel: lang('Kabupaten'),
                    disabled: 'true',
                    store: Ext.create('Koltiva.store.kabupaten'),
                    displayField: 'label',
                    valueField: 'label',
                    queryMode: 'local',
                    listeners: {
                        change: function(cb, nv, ov) {
                            Ext.getCmp('Kecamatan').store.load({
                                params: {
                                    key: Ext.getCmp('Kabupaten').getValue()
                                }
                            });
                            Ext.getCmp('Kecamatan').enable();
                            ds.getProxy().setExtraParam("district", Ext.getCmp('Kabupaten').getValue())
                        }
                    }
                }, {
                    id: 'Kecamatan',
                    name: 'Kecamatan',
                    xtype: 'combo',
                    fieldLabel: lang('Kecamatan'),
                    store: Ext.create('Koltiva.store.kecamatan'),
                    displayField: 'label',
                    valueField: 'label',
                    queryMode: 'local',
                    disabled: 'true',
                    listeners: {
                        change: function(cb, nv, ov) {
                            Ext.getCmp('Desa').store.load({
                                params: {
                                    key: Ext.getCmp('Kecamatan').getValue()
                                }
                            });
                            Ext.getCmp('Desa').enable();
                        }
                    }
                }, {
                    id: 'Desa',
                    name: 'Desa',
                    xtype: 'combo',
                    fieldLabel: lang('Desa'),
                    store: Ext.create('Koltiva.store.desa'),
                    displayField: 'label',
                    disabled: 'true',
                    valueField: 'id',
                    queryMode: 'local'
                }, {
                    xtype: 'textfield',
                    fieldLabel: lang('Alamat'),
                    id: 'Address',
                    name: 'Address'
                }, {
                    xtype: 'textfield',
                    id: 'Latitude',
                    name: 'Latitude',
                    fieldLabel: lang('Latitude'),
                    readOnly: m_hakakses_lat_short
                }, {
                    xtype: 'textfield',
                    id: 'Longitude',
                    name: 'Longitude',
                    fieldLabel: lang('Longitude'),
                    readOnly: m_hakakses_long_short
                }]
            }
          ]
        },
        {
          xtype:'panel',
          title:'Staff'
        }
      ]
    }
  ],
  dockedItems: [{
      xtype: 'toolbar',
      flex: 1,
      dock: 'top',
      items: [{
          xtype: 'button',
          height: 70,
          width: 85,
          text: '<img src="' + varjs.config.base_url + 'img/general/compost-24px.png" /> <br /> ' + lang('Compost'),
          tooltip: lang('Compost'),
          handler: function() {

          }
      }, {
          xtype: 'button',
          height: 70,
          width: 85,
          text: '<img src="' + varjs.config.base_url + 'img/general/nursery-24px.png" /> <br /> ' + lang('Nursery'),
          tooltip: lang('Nursery'),
          handler: function() {

          }
      }, {
          xtype: 'button',
          height: 70,
          width: 85,
          text: '<img src="' + varjs.config.base_url + 'img/general/summary-24px.png" /> <br /> ' + lang('ICS'),
          tooltip: lang('Internal Monitoring System'),
          handler: function() {

          }
      }, {
          xtype: 'button',
          height: 70,
          width: 100,
          text: '<img src="' + varjs.config.base_url + 'img/general/kebun-24px.png" /> <br /> ' + lang('Clonal Garden'),
          tooltip: lang('Clonal Garden'),
          handler: function() {

          }
      }]
  }],
});

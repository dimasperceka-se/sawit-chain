Ext.define('Koltiva.view.cooperatives.list' ,{
    extend: 'Ext.grid.Panel',
    alias: 'widget.cooplist',
    renderTo:'ext-content',
    width: '100%',
    minHeight: 550,
    style: 'border:1px solid #CCC;',
    renderTo: 'ext-content',
    loadMask: true,
    selType: 'rowmodel',
    listeners: {
        itemdblclick: function(dv, record, item, index, e) {
          var win = Ext.create('widget.window', {
              title: lang('Organisasi Petani'),
              closable: true,
              modal: true,
              autoScroll: true,
              width: '90%',
              height: '90%',
              layout: {
                  type: 'fit'
              },
              items: []
          });
        }
    },
    initComponent: function() {

      this.store = Ext.create('Koltiva.store.cooperatives.list');

      this.dockedItems = [{
          xtype: 'pagingtoolbar',
          store: this.store, // same store GridPanel is using
          dock: 'bottom',
          displayInfo: true
      }, {
          xtype: 'toolbar',
          items: [{
              icon: varjs.config.base_url + 'images/icons/new/add.png', cls:'Sfr_BtnGridGreen', overCls:'Sfr_BtnGridGreen-Hover',
              text: lang('Add'),
              hidden: !m_act_add,
              scope: this,
              handler: function() {
                var frm = Ext.create('Koltiva.view.cooperatives.form');
                var win = Ext.create('Ext.Window',{
                  title: lang('Organisasi Petani'),
                  closable: true,
                  modal: true,
                  autoScroll: true,
                  width: '90%',
                  height: '90%',
                  items:[frm]
                }).show();
              },
              cls: m_act_add
          }, {
              icon: varjs.config.base_url + 'images/icons/new/update.png',
              text: lang('Update'),
              hidden: !m_act_update,
              scope: this,
              handler: function() {

              },
              cls: m_act_update
          }, {
              itemId: 'remove',
              icon: varjs.config.base_url + 'images/icons/new/delete.png',
              cls: m_act_delete,
              hidden: !m_act_delete,
              text: lang('Hapus'),
              scope: this,
              handler: function() {

              }
          }, {
              name: 'key', baseCls:'Sfr_TxtfieldSearchGrid',
              id: 'key',
              xtype: 'textfield',
              emptyText: lang('Cari berdasar nama/ID')
          }, {
              id: 'sProvinsi',
              name: 'sProvinsi',
              xtype: 'combo',
              store: Ext.create('Koltiva.store.provinsi'),
              displayField: 'label',
              valueField: 'id',
              queryMode: 'local',
              hidden: true,
              value: m_param,
              listeners: {
                  change: function(cb, nv, ov) {
                      mc_Kabupaten.load({
                          params: {
                              key: Ext.getCmp('sProvinsi').getValue()
                          }
                      });
                      Ext.getCmp('sKabupaten').enable();
                  }
              }
          }, {
              id: 'sKabupaten',
              name: 'sKabupaten',
              xtype: 'combo',
              store: Ext.create('Koltiva.store.kabupaten'),
              displayField: 'label',
              valueField: 'label',
              queryMode: 'local'
          }, {
              xtype: 'button',
              id: 'btnSimpleSearch',
              icon: varjs.config.base_url + 'images/icons/silk/search.png',
              margin: '0px 0px 0px 6px',
              text: lang('Search'),
              handler: function() {

              }
          }, {
              xtype: 'button',
              id: 'btnAdvSearch',
              icon: varjs.config.base_url + 'images/icons/silk/page_white_wrench.png',
              margin: '0px 0px 0px 6px',
              text: lang('Advanced Search'),
              handler: function() {

              }
          }]
      }];

      this.columns = [{
          text: lang('ID'),
          dataIndex: 'id',
          hidden: true
      }, {
          text: lang('No'),
          xtype: 'rownumberer',
          width: '5%'
      }, {
          text: lang('Code'),
          width: '15%',
          dataIndex: 'CoopCode'
      }, {
          text: lang('Nama'),
          width: '15%',
          dataIndex: 'CoopName'
      }, {
          text: lang('Phone'),
          width: '10%',
          dataIndex: 'Phone'
      }, {
          text: lang('Email'),
          width: '15%',
          dataIndex: 'Email'
      }, {
          text: lang('Tahun Terbentuk'),
          width: '15%',
          dataIndex: 'TahunTerbentuk'
      }, {
          text: lang('Status'),
          width: '10%',
          dataIndex: 'Status'
      }, {
          text: lang('District'),
          width: '15%',
          dataIndex: 'District'
      }];

      this.callParent(arguments);
    }
});

Ext.define('Koltiva.view.Action.MainGrid', {
    extend: 'Ext.grid.Panel',
    id: 'Koltiva.view.Action.MainGrid',
    width: '100%',
    minHeight: 250,
    title: lang('System Action'),
    style: 'border:1px solid #CCC;',
    renderTo: 'ext-content',
    loadMask: true,
    selType: 'rowmodel',
    setViewVar: function(value){
        this.viewVar = value;
    },
    initComponent: function(){
      var thisObj = this;
      thisObj.store = Ext.create('Koltiva.store.Action.MainGrid');
      thisObj.contextMenuGrid = Ext.create('Ext.menu.Menu',{
          items: [
          {
              icon: varjs.config.base_url + 'images/icons/new/view.png',
              text: lang('View'),
              hidden: false,
              handler: function() {
                  var FormWindow;
                  var sm = Ext.getCmp('Koltiva.view.Action.MainGrid').getSelectionModel().getSelection()[0];
                  if(Ext.getCmp('Koltiva.view.Action.MainForm') == undefined){
                        FormWindow = Ext.create('Koltiva.view.Action.MainForm', {
                          viewVar: {
                              opsiDisplay: 'view',
                              caller: Ext.getCmp('Koltiva.view.Action.MainGrid')
                          }
                        });
                        FormWindow.show();
                    }else{
                        FormWindow.show();
                    }
                  Ext.getCmp('Koltiva.view.Action.MainForm-AksiID').setValue(sm.get('AksiId'));
                  Ext.getCmp('Koltiva.view.Action.MainForm-AksiName').setValue(sm.get('AksiName'));
                  Ext.getCmp('Koltiva.view.Action.MainForm-AksiFungsi').setValue(sm.get('AksiFungsi'));
                  Ext.getCmp('Koltiva.view.Action.MainForm-ButtonSave').setVisible(false);
                  Ext.getCmp('Koltiva.view.Action.MainForm').show();
              }
          },
          {
              icon: varjs.config.base_url + 'images/icons/new/update.png',
              text: lang('Update'),
              hidden: !m_act_update,
              handler: function(){
                  var FormWindow;
                  var sm = Ext.getCmp('Koltiva.view.Action.MainGrid').getSelectionModel().getSelection()[0];
                  if(Ext.getCmp('Koltiva.view.Action.MainForm') == undefined){
                        FormWindow = Ext.create('Koltiva.view.Action.MainForm', {
                          viewVar: {
                              opsiDisplay: 'view',
                              caller: Ext.getCmp('Koltiva.view.Action.MainGrid')
                          }
                        });
                        FormWindow.show();
                    }else{
                        FormWindow.show();
                    }
                  Ext.getCmp('Koltiva.view.Action.MainForm-AksiID').setValue(sm.get('AksiId'));
                  Ext.getCmp('Koltiva.view.Action.MainForm-AksiName').setValue(sm.get('AksiName'));
                  Ext.getCmp('Koltiva.view.Action.MainForm-AksiFungsi').setValue(sm.get('AksiFungsi'));
                  Ext.getCmp('Koltiva.view.Action.MainForm-ButtonSave').setVisible(true);
                  Ext.getCmp('Koltiva.view.Action.MainForm').show();
              }
          },
          {
              icon: varjs.config.base_url + 'images/icons/new/delete.png',
              text: lang('Delete'),
              hidden: !m_act_delete,
              handler: function() {
                  var sm = Ext.getCmp('Koltiva.view.Action.MainGrid').getSelectionModel().getSelection()[0];
                  Ext.MessageBox.confirm(lang('Confirmation'), lang('Apakah anda yakin akan reset data ini?'), function (btn) {
                      if (btn == 'yes') {
                          Ext.Ajax.request({
                              waitMsg: lang('Please Wait'),
                              url: m_crud,
                              method: 'DELETE',
                              params: {AksiId: sm.raw.AksiId},
                              success: function (response, opts) {
                                  var obj = Ext.decode(response.responseText);
                                  if (obj.status) {
                                      Ext.MessageBox.alert(lang('Success'), lang(obj.message));
                                  } else {
                                      Ext.MessageBox.alert(lang('Success'), lang(obj.message));
                                  }
                                  thisObj.store.load({
                                      params: {
                                          start: 0,
                                          key: Ext.getCmp('Koltiva.view.Action.MainGrid.key').getValue(),
                                      }
                                  });
                              },
                              failure: function (response, opts) {
                                  var obj = Ext.decode(response.responseText);
                                  Ext.MessageBox.alert(lang('error'), lang('Could not connect to the database. Retry later'));
                              }
                          });
                      }
                  });
              }
          }
          ]
      });

      thisObj.dockedItems = [{
          xtype: 'pagingtoolbar',
          store: thisObj.store,
          dock: 'bottom',
          displayInfo: true
      }, {
          xtype: 'toolbar',
          items: [
              {
                  xtype: 'button',
                  name: 'Koltiva.view.Action.MainGrid.AddButton',
                  id: 'Koltiva.view.Action.MainGrid.AddButton',
                  text: lang('Add'),
                  hidden: !m_act_add,
                  icon: varjs.config.base_url + 'images/icons/new/add.png', cls:'Sfr_BtnGridGreen', overCls:'Sfr_BtnGridGreen-Hover',
                  handler: function(){
                    var FormWindow;
                    if(Ext.getCmp('Koltiva.view.Action.MainForm') == undefined){
                        FormWindow = Ext.create('Koltiva.view.Action.MainForm', {
                          viewVar: {
                              opsiDisplay: 'add',
                              caller: Ext.getCmp('Koltiva.view.Action.MainGrid')
                          }
                        });
                        FormWindow.show();
                    }else{
                        FormWindow.show();
                    }
                  }
              },{
                  xtype: 'textfield',
                  emptyText: lang('Username'),
                  name: 'Koltiva.view.Action.MainGrid.key',
                  id: 'Koltiva.view.Action.MainGrid.key',
                  width: 400,
                  emptyText: lang('Cari berdasar Name / Fungsi')+', '+lang('press_enter_search'),
                  listeners: {
                    specialkey: function(field, e){
                        if (e.getKey() == e.ENTER) {
                            thisObj.store.setStoreVar({
                                              start: 0,
                                              key: Ext.getCmp('Koltiva.view.Action.MainGrid.key').getValue(),
                            });
                            thisObj.store.load();
                        }
                    },
                  },
              }, 
              {
                  xtype: 'button',
                  margin: '0px 0px 0px 6px',
                  text: 'Search',
                  handler: function () {
                      thisObj.store.setStoreVar({
                                        start: 0,
                                        key: Ext.getCmp('Koltiva.view.Action.MainGrid.key').getValue(),
                      });
                      thisObj.store.load();
                  }
              }
          ]
      }];
      thisObj.columns = [
          {
              text: 'ID',
              dataIndex: 'AksiId',
              hidden: true
          },
          {
              text: lang('Action'),
              xtype:'actioncolumn',
              width:'4%',
              items:[{
                  icon: varjs.config.base_url + 'images/icons/new/action.png',
                  handler: function(grid, rowIndex, colIndex, item, e, record) {
                      thisObj.contextMenuGrid.showAt(e.getXY());
                  }
              }]
          },
          {
              text: lang('No'),
              xtype: 'rownumberer',
              align: 'center',
              width: 50,
          },
          {
              text: lang('Name'),
              flex: 2,
              dataIndex: 'AksiName'
          },
          {
              text: lang('Function'),
              flex: 2,
              dataIndex: 'AksiFungsi'
          }
      ];

      this.callParent(arguments);
    },
    reloadGridAndCloseWin: function(){
      var thisObj = this;
      thisObj.store.load({
                            params: {
                                start: 0,
                                key: Ext.getCmp('Koltiva.view.Action.MainGrid.key').getValue(),
                            }
                        });
      Ext.getCmp('Koltiva.view.Action.MainForm').close();
    }
});
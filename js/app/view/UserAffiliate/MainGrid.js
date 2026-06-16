Ext.define('Koltiva.view.UserAffiliate.MainGrid', {
    extend: 'Ext.grid.Panel',
    id: 'Koltiva.view.UserAffiliate.MainGrid',
    width: '100%',
    minHeight: 250,
    title: 'User List',
    style: 'border:1px solid #CCC;',
    renderTo: 'ext-content',
    loadMask: true,
    selType: 'rowmodel',
    setViewVar: function(value){
        this.viewVar = value;
    },
    initComponent: function(){
      var thisObj = this;
      thisObj.store = Ext.create('Koltiva.store.UserAffiliate.MainGrid');
      thisObj.contextMenuGrid = Ext.create('Ext.menu.Menu',{
          items: [
          {
              icon: varjs.config.base_url + 'images/icons/new/view.png',
              text: lang('View'),
              hidden: false,
              handler: function() {
                  var sm = Ext.getCmp('Koltiva.view.UserAffiliate.MainGrid').getSelectionModel().getSelection()[0];
                  Ext.getCmp('Koltiva.view.UserAffiliate.MainGrid').destroy();
                  if(Ext.getCmp('Koltiva.view.UserAffiliate.FormPanel') == undefined){
                      Ext.create('Koltiva.view.UserAffiliate.FormPanel', {
                          viewVar: {
                              UserId: sm.get('UserId'),
                              UserRealName: sm.get('UserRealName'),
                              IsUpdate: false
                          }
                      });
                      Ext.getCmp('Koltiva.view.UserAffiliate.UserId').setValue(sm.get('UserId'));
                      Ext.getCmp('Koltiva.view.UserAffiliate.UserName').setValue(sm.get('UserName'));
                      Ext.getCmp('Koltiva.view.UserAffiliate.UserRealName').setValue(sm.get('UserRealName'));
                  }else{
                      Ext.getCmp('Koltiva.view.UserAffiliate.UserId').setValue(sm.get('UserId'));
                      Ext.getCmp('Koltiva.view.UserAffiliate.UserName').setValue(sm.get('UserName'));
                      Ext.getCmp('Koltiva.view.UserAffiliate.UserRealName').setValue(sm.get('UserRealName'));
                  }
              }
          },
          {
              icon: varjs.config.base_url + 'images/icons/new/update.png',
              text: lang('Manage'),
              hidden: !m_act_update,
              handler: function(){
                  var sm = Ext.getCmp('Koltiva.view.UserAffiliate.MainGrid').getSelectionModel().getSelection()[0];
                  Ext.getCmp('Koltiva.view.UserAffiliate.MainGrid').destroy();
                  if(Ext.getCmp('Koltiva.view.UserAffiliate.FormPanel') == undefined){
                      Ext.create('Koltiva.view.UserAffiliate.FormPanel', {
                          viewVar: {
                              UserId: sm.get('UserId'),
                              UserRealName: sm.get('UserRealName'),
                              IsUpdate: true
                          }
                      });
                      Ext.getCmp('Koltiva.view.UserAffiliate.UserId').setValue(sm.get('UserId'));
                      Ext.getCmp('Koltiva.view.UserAffiliate.UserName').setValue(sm.get('UserName'));
                      Ext.getCmp('Koltiva.view.UserAffiliate.UserRealName').setValue(sm.get('UserRealName'));
                  }else{
                      Ext.getCmp('Koltiva.view.UserAffiliate.UserId').setValue(sm.get('UserId'));
                      Ext.getCmp('Koltiva.view.UserAffiliate.UserName').setValue(sm.get('UserName'));
                      Ext.getCmp('Koltiva.view.UserAffiliate.UserRealName').setValue(sm.get('UserRealName'));
                  }
              }
          },
          {
              icon: varjs.config.base_url + 'images/icons/new/delete.png',
              text: lang('Reset'),
              hidden: !m_act_delete,
              handler: function() {
                  var sm = Ext.getCmp('Koltiva.view.UserAffiliate.MainGrid').getSelectionModel().getSelection()[0];
                  Ext.MessageBox.confirm('Message', lang('Apakah anda yakin akan reset data ini?'), function (btn) {
                      if (btn == 'yes') {
                          Ext.Ajax.request({
                              waitMsg: lang('Please Wait'),
                              url: m_crud + 's',
                              method: 'DELETE',
                              params: {UserId: sm.raw.UserId},
                              success: function (response, opts) {
                                  var obj = Ext.decode(response.responseText);
                                  if (obj.status) {
                                      Ext.MessageBox.alert('Info', obj.message);
                                  } else {
                                      Ext.MessageBox.alert('Info', obj.message);
                                  }
                                  thisObj.store.load({
                                      params: {
                                          start: 0,
                                          key: Ext.getCmp('Koltiva.view.UserAffiliate.MainGrid.key').getValue(),
                                      }
                                  });
                              },
                              failure: function (response, opts) {
                                  var obj = Ext.decode(response.responseText);
                                  Ext.MessageBox.alert('error', 'Could not connect to the database. Retry later');
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
                  xtype: 'textfield',
                  emptyText: lang('Username'),
                  name: 'Koltiva.view.UserAffiliate.MainGrid.key',
                  id: 'Koltiva.view.UserAffiliate.MainGrid.key',
                  listeners: {
                    specialkey: function(field, e){
                        if (e.getKey() == e.ENTER) {
                            thisObj.store.setStoreVar({
                                              start: 0,
                                              key: Ext.getCmp('Koltiva.view.UserAffiliate.MainGrid.key').getValue(),
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
                                        key: Ext.getCmp('Koltiva.view.UserAffiliate.MainGrid.key').getValue(),
                      });
                      thisObj.store.load();
                  }
              }
          ]
      }];
      thisObj.columns = [
          {
              text: 'ID',
              dataIndex: 'UserId',
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
              text: lang('Real Name'),
              flex: 2,
              dataIndex: 'UserRealName'
          },
          {
              text: lang('User Name'),
              flex: 2,
              dataIndex: 'UserName'
          },
          {
              text: lang('Active'),
              flex: 1,
              dataIndex: 'UserActive'
          },
          {
              text: lang('Affiliated'),
              flex: 1,
              dataIndex: 'Affiliated'
          },
      ];

      this.callParent(arguments);
    }
});
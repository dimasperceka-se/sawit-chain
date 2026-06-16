/*
* @Author: nikolius
* @Date:   2016-03-31 15:41:34
* @Last Modified by:   nikolius
* @Last Modified time: 2016-04-04 16:48:17
*/
Ext.onReady(function() {
   Ext.tip.QuickTipManager.init();

   Ext.define('Scpp.Model', {
      extend: 'Ext.data.Model',
      fields: ['CropTypeID', 'CropTypeName', 'StatusCode'],
   });

   var store = Ext.create('Ext.data.Store', {
      extend: 'Ext.data.Model',
      fields: ['CropTypeID', 'CropTypeName', 'StatusCode'],
      autoLoad: true,
      pageSize: 50,
      proxy: {
         type: 'ajax',
         url: m_crud+'s',
         reader: {
            type: 'json',
            root: 'data',
            totalProperty: 'total'
         }
      }
   });
   store.loadPage(1);

   var cmbStatusCode = Ext.create('Ext.data.Store', {
      fields: ['id','label'],
      data: [{
         "id":"active",
         "label": "ACTIVE"
      }, {
         "id":"inactive",
         "label": "INACTIVE"
      }]
   });

   var RowEditing = Ext.create('Ext.grid.plugin.RowEditing', {
      id: 'RowEditing',
      clicksToMoveEditor: 0,
      autoCancel: false,
      errorSummary: false,
      clicksToEdit: 2
   });

   var grid = Ext.create('Ext.grid.Panel', {
      store: store,
      width: '100%',
      minHeight: 250,
      id: 'grid',
      style: 'border:1px solid #CCC;',
      renderTo: 'ext-content',
      loadMask: true,
      selType: 'rowmodel',
      dockedItems: [
         {
            xtype: 'pagingtoolbar',
            store: store, // same store GridPanel is using
            dock: 'bottom',
            displayInfo: true
         },
         {
            xtype: 'toolbar',
            minHeight: 38,
            items: [
               {
                  icon: varjs.config.base_url + 'images/icons/new/add.png', cls:'Sfr_BtnGridGreen', overCls:'Sfr_BtnGridGreen-Hover',
                  text: lang('Add'),
                  scope: this,
                  cls: m_act_add,
                  handler: function() {
                     RowEditing.cancelEdit();
                     var r = Ext.create('Scpp.Model', {
                        CropTypeID: '',
                        CropTypeName: '',
                        StatusCode:''
                     });
                     store.insert(0, r);
                     RowEditing.startEdit(0, 0);
                  }
               },
               {
                  itemId: 'remove',
                  icon: varjs.config.base_url + 'images/icons/new/delete.png',
                  cls: m_act_delete,
                  text: lang('Delete'),
                  scope: this,
                  handler: function() {
                     var smb = Ext.getCmp('grid').getSelectionModel().getSelection()[0];
                     RowEditing.cancelEdit();

                     Ext.MessageBox.confirm('Message', 'Are you sure want to delete this data ?', function(btn) {
                        if (btn == 'yes') {
                           Ext.Ajax.request({
                              waitMsg: 'Please Wait',
                              url: m_crud,
                              method: 'DELETE',
                              params: {
                                 CropTypeID: smb.raw.CropTypeID
                              },
                              success: function(response, opts) {
                                 var obj = Ext.decode(response.responseText);
                                 switch (obj.success) {
                                    case true:
                                       Ext.MessageBox.alert('Success',obj.message);
                                       store.load();
                                       break;
                                    default:
                                       Ext.MessageBox.alert('Warning', obj.message);
                                       break;
                                 }
                              },
                              failure: function(response, opts) {
                                 var obj = Ext.decode(response.responseText);
                                 Ext.MessageBox.alert('error', 'Could not connect to the database. Retry later');
                              }
                           });
                        }
                     });

                  }
               }
            ]
         }
      ],
      columns: [
         {
            dataIndex: 'CropTypeID',
            hidden: true
         },{
            text: 'No',
            xtype: 'rownumberer',
            width: '3%'
         },{
            text: lang('crop_type'),
            width: '77%',
            dataIndex: 'CropTypeName',
            editor: {
               xtype: 'textfield',
               allowBlank: false
            }
         },{
            text: lang('Status'),
            width: '20%',
            dataIndex: 'StatusCode',
            renderer: Ext.util.Format.uppercase,
            editor: {
               xtype: 'combo',
               store: cmbStatusCode,
               id: 'StatusCode',
               queryMode: 'local',
               displayField: 'label',
               valueField: 'id',
               editable: false
            }
         }
      ],
      plugins: [RowEditing],
      listeners: {
         'canceledit':function(editor,e,eOpts){
            store.load();
         },
         'edit': function(editor, e) {
            var CropTypeID = e.record.data.CropTypeID;
            var CropTypeName = e.record.data.CropTypeName;
            var StatusCode = e.record.data.StatusCode;

            if(CropTypeID.trim() === ''){
               console.log('insert');

               Ext.Ajax.request({
                  waitMsg: 'Please wait...',
                  url: m_crud,
                  method : 'POST',
                  params: {
                     CropTypeName: CropTypeName,
                     StatusCode: StatusCode
                  },
                  success: function(response, opts){
                     console.log(response);
                     var obj = Ext.decode(response.responseText);
                     console.log(obj);
                     switch(obj.success){
                        case true:
                           Ext.MessageBox.alert('Success',obj.message);
                           store.load();
                        break;
                        default:
                           Ext.MessageBox.alert('Warning',obj.message);
                        break;
                     }
                  },
                  failure: function(response, opts){
                     var obj = Ext.decode(response.responseText);
                     console.log(obj);
                     Ext.MessageBox.alert('error','Could not connect to the database. Retry later');
                  }
               });
            } else {
               Ext.MessageBox.confirm('Message', 'Do you want to update ?' , function(btn){
                  if(btn == 'yes'){
                     Ext.Ajax.request({
                        waitMsg: 'Please wait...',
                        url: m_crud,
                        method : 'PUT',
                        params: {
                           CropTypeID: CropTypeID,
                           CropTypeName: CropTypeName,
                           StatusCode: StatusCode
                        },
                        success: function(response, opts){
                           var obj = Ext.decode(response.responseText);
                           console.log(obj);
                           switch(obj.success){
                              case true:
                                 Ext.MessageBox.alert('Success',obj.message);
                                 store.load();
                              break;
                              default:
                                 Ext.MessageBox.alert('Warning',obj.message);
                              break;
                           }
                        },
                        failure: function(response, opts){
                           var obj = Ext.decode(response.responseText);
                           console.log(obj);
                           Ext.MessageBox.alert('error','Could not connect to the database. Retry later');
                        }
                     });
                  }
               });
            }
         }
      }
   });

});
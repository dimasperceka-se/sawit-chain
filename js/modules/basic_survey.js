Ext.onReady(function(){
   Ext.tip.QuickTipManager.init();
   Ext.define('Scpp.Model', {
      extend: 'Ext.data.Model',
      fields: ['id','nr','name'],
   });
   var store = Ext.create('Ext.data.Store', {
        model: 'Scpp.Model',
        autoLoad: true,
        pageSize: 10,
        proxy: {
            type: 'ajax',
            url: m_crud+'s',
            params: {
            'X-API-KEY': '030584'
            },
            reader: {
                type: 'json',
                root: 'data',
                totalProperty: 'total'
            }
        }
   });
   store.loadPage(1); 
   var RowEditing = Ext.create('Ext.grid.plugin.RowEditing', {
         id: 'RowEditing',
         clicksToMoveEditor: 0,
         autoCancel: false,
         errorSummary : false,
         clicksToEdit: 2
    });
    var grid = Ext.create('Ext.grid.Panel', {
       store: store,
       width: '100%',
       id:'grid',
       minHeight: 250,
       //title: 'Survey List',
       style: 'border:1px solid #CCC;',
       renderTo: 'ext-content',
       loadMask: true,
       selType: 'rowmodel',
       dockedItems: [{
              xtype: 'pagingtoolbar',
              store: store,   // same store GridPanel is using
              dock: 'bottom',
              displayInfo: true
       },{
            xtype: 'toolbar',
            items: [
            {
               icon: varjs.config.base_url + 'images/icons/new/add.png', cls:'Sfr_BtnGridGreen', overCls:'Sfr_BtnGridGreen-Hover', 
               text: lang('Add'),
               scope: this,
               cls : m_act_add,
               handler : function(){
                    RowEditing.cancelEdit();
                    var r = Ext.create('Scpp.Model', {
                        id: '',
                        nr:'',
                        name: ''
                    });
                    store.insert(0, r);
                    RowEditing.startEdit(0, 0);    
               }
            },{
               icon: varjs.config.base_url+'images/icons/new/update.png', cls:'Sfr_BtnGridBlue', overCls:'Sfr_BtnGridBlue-Hover', 
               text: lang('Update'),
               scope: this,
               cls : m_act_update,
               handler : function(){
                    RowEditing.cancelEdit();
                    var sm = Ext.getCmp('grid').getSelectionModel().getSelection();
                    RowEditing.startEdit(sm[0].index, 0);
               }
            },{
               itemId: 'remove',
               icon: varjs.config.base_url+'images/icons/new/delete.png', cls:'Sfr_BtnGridRed', overCls:'Sfr_BtnGridRed-Hover',
               cls:m_act_delete,
               text: lang('Hapus'),
               scope: this,
               handler : function(){
                 var smb = Ext.getCmp('grid').getSelectionModel().getSelection()[0];
                 RowEditing.cancelEdit();
                 Ext.MessageBox.confirm('Message', lang('Apakah anda mau menghapus data ini ?') , function(btn){
                     if(btn == 'yes'){
                        Ext.Ajax.request({
                        waitMsg: lang('Please Wait'),
                        url: m_crud,
                        method : 'DELETE',
                        params: {id:  smb.raw.id},
                        success: function(response, opts){
                           var obj = Ext.decode(response.responseText);
                           switch(obj.success){
                              case true: store.load();
                              break;
                              default: Ext.MessageBox.alert('Warning',obj.message);
                              break;
                           }
                        },
                        failure: function(response, opts){
                           var obj = Ext.decode(response.responseText);
                           Ext.MessageBox.alert('error',lang('Could not connect to the database. Retry later'));
                        }
                     });
                     }
                 });
               }
            }]
         }],
       columns: [
       {
        text: lang('ID'),
        dataIndex: 'id',
            width:'10%',
            hidden:true
    },{
            text: lang('Number'),
            dataIndex: 'nr',
            width:'10%',
            editor: {
               xtype      : 'textfield'
            }
       },
       {
           text: lang('Name'), 
           width: '89%',
           dataIndex: 'name',
           editor: {
                 xtype:'textfield',
                 allowBlank:false
              } 
       }],
       plugins: [RowEditing],
       listeners: {
          'canceledit':function(editor,e,eOpts){
              store.load();
          },
          'edit': function(editor, e) {
                var id = e.record.data.id;
                var nr = e.record.data.nr;
                var name = e.record.data.name;
                if(id.trim()==''){
                        Ext.Ajax.request({
                            waitMsg: lang('Please wait...'),
                            url: m_crud,
                            method : 'POST',
                            params: {
                              name:            name,
                              nr:            nr
                            },
                            success: function(response, opts){
                             var obj = Ext.decode(response.responseText);
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
                             Ext.MessageBox.alert('error',lang('Could not connect to the database. Retry later'));
                            }
                        });
                } else {
                    Ext.MessageBox.confirm('Message', lang('Update data unit ini ?') , function(btn){
                        if(btn == 'yes'){
                                Ext.Ajax.request({
                                    waitMsg: lang('Please wait...'),
                                    url: m_crud,
                                    method : 'PUT',
                                    params: {
                                       id:            id,
                                       nr:          nr,
                                       name:          name
                                    },
                                success: function(response, opts){
                                     var obj = Ext.decode(response.responseText);
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
                                    Ext.MessageBox.alert('error',lang('Could not connect to the database. Retry later'));
                                    }
                                });
                        }
                });
                }
          }
       }
    });
});

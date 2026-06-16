Ext.onReady(function(){
   Ext.tip.QuickTipManager.init();
    Ext.define('Scpp.Model', {
        extend: 'Ext.data.Model',
        fields: ['id','number','partner_id','partner'],
    });
   var store = Ext.create('Ext.data.Store', {
        model: 'Scpp.Model',
        autoLoad: true,
        pageSize: 50,
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
   var store_cpg = Ext.create('Ext.data.Store', {
      extend: 'Ext.data.Model',
      fields: ['id','label'],
      autoLoad: true,
      proxy: {
         type: 'ajax',
         url: m_cpg,
         reader: {
            type: 'json',
            root: 'data',
         }
      }
   });
   var store_partner = Ext.create('Ext.data.Store', {
      extend: 'Ext.data.Model',
      autoLoad: true,
      fields: ['id','label'],
      proxy: {
         type: 'ajax',
         url: m_partner,
         reader: {
            type: 'json',
            root: 'data',
         }
      }
   });
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
       minHeight:250,
       //title: 'CPG Batch List',
       style:'border:1px solid #CCC;',
       renderTo: 'ext-content',
       loadMask: true,
       selType: 'rowmodel',
       plugins: [RowEditing],
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
                        number:'',
                        partner:'',
                        partner_id:''
                    });
                    store.insert(0, r);
                    RowEditing.startEdit(0, 0);    
               }
            },{
               icon: varjs.config.base_url+'images/icons/silk/pencil.png', 
               text: lang('Update'),
               scope: this,
               cls : m_act_add,
               handler : function(){
                    RowEditing.cancelEdit();
                    var sm = Ext.getCmp('grid').getSelectionModel().getSelection();
                    RowEditing.startEdit(sm[0].index, 0);
               }
            },{
               itemId: 'remove',
               icon: varjs.config.base_url+'images/icons/silk/delete.png',
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
       columns: [{
        text: lang('ID'),
        dataIndex: 'id',
        hidden:true
      },{
            text: lang('ID'),
            dataIndex: 'id',
            width:'5%',
            hidden:true
       },{
            text: lang('Number'),
            dataIndex: 'number',
            width:'10%',
           editor: {
                     xtype      : 'textfield'
                 }
       },
       {
           text: lang('Partner'), 
           width: '90%',
           dataIndex: 'partner',
           editor: {
               xtype      : 'combo',
               typeAhead: true,
               store : store_partner,
               queryMode: 'local',
               displayField: 'label',
               valueField: 'id'
           }
       }],
       listeners: {
          'canceledit':function(editor,e,eOpts){
              store.load();
          },
          'edit': function(editor, e) {
                var id = e.record.data.id;
                var number = e.record.data.number;
                var partner = e.record.data.partner;
                var partner_id = e.record.data.partner_id;
                if(id.trim()==''){
                        Ext.Ajax.request({
                            waitMsg: lang('Please wait...'),
                            url: m_crud,
                            method : 'POST',
                            params: {
                              id:            id,
                              number: number,
                              partner:     partner
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
                    Ext.MessageBox.confirm('Message', lang('Update data cpg batch ini ?') , function(btn){
                        if(btn == 'yes')
                        {
                                Ext.Ajax.request({
                                    waitMsg: lang('Please wait...'),
                                    url: m_crud,
                                    method : 'PUT',
                                    params: {
                                       id:          id,
                                       number: number,
                                       partner_id: partner_id,
                                       partner: partner
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

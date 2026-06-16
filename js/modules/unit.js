Ext.onReady(function(){
   Ext.tip.QuickTipManager.init();
    Ext.define('Scpp.Model', {
        extend: 'Ext.data.Model',
        fields: ['UnitId','UnitName', 'UnitDescription'],
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
         clicksToEdit: 2,
         listeners : {
            beforeedit : function(ev) {
               return m_act_update;
            }
         }
    });
    var grid = Ext.create('Ext.grid.Panel', {
       store: store,
       width: '100%',
       minHeight:250,
       id:'grid',
       //title: 'Unit List',
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
               text: 'Add',
               scope: this,
               cls:m_act_add,
               handler : function(){
                    RowEditing.cancelEdit();
                    var r = Ext.create('Scpp.Model', {
                        UnitId: '',
                        UnitName: '',
                        UnitDescription:''
                    });
                    store.insert(0, r);
                    RowEditing.startEdit(0, 0);
               }
            },{
               itemId: 'remove',
               icon: varjs.config.base_url+'images/icons/silk/delete.png',
               cls:m_act_delete,
               text: 'Hapus',
               scope: this,
               handler : function(){
                 var smb = Ext.getCmp('grid').getSelectionModel().getSelection()[0];
                 RowEditing.cancelEdit();
                 Ext.MessageBox.confirm('Message', 'Apakah anda mau menghapus data ini ?' , function(btn){
                     if(btn == 'yes'){
                        Ext.Ajax.request({
                        waitMsg: 'Please Wait',
                        url: m_crud,
                        method : 'DELETE',
                        params: {UnitId:  smb.raw.UnitId},
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
                           Ext.MessageBox.alert('error','Could not connect to the database. Retry later');
                        }
                     });
                     }
                 });
               }
            }]
       }],
       columns: [
       {
           dataIndex: 'UnitId',
           hidden:true
       },
       {
            text: 'No',
            xtype: 'rownumberer',
            width:'5%'
       },
       {
           text: 'Unit Name',
           width: '35%',
           dataIndex: 'UnitName',
           editor: {
                    xtype:'textfield',
                    allowBlank:false
                 }
       },
       {
           text: 'Description',
           width: '60%',
           dataIndex: 'UnitDescription',
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
                var UnitId = e.record.data.UnitId;
                var UnitName = e.record.data.UnitName;
                var UnitDescription = e.record.data.UnitDescription;
                if(UnitId.trim()==''){
                    console.log('insert');
                        Ext.Ajax.request({
                            waitMsg: 'Please wait...',
                            url: m_crud,
                            method : 'POST',
                            params: {
                            UnitName:            UnitName,
                            UnitDescription:     UnitDescription
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
                    Ext.MessageBox.confirm('Message', 'Update data unit ini ?' , function(btn){

                        if(btn == 'yes')
                        {
                                Ext.Ajax.request({
                                    waitMsg: 'Please wait...',
                                    url: m_crud,
                                    method : 'PUT',
                                    params: {
                                    UnitId:            UnitId,
                                    UnitName:          UnitName,
                                    UnitDescription:   UnitDescription

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

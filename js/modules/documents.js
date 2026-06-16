Ext.onReady(function(){
   Ext.tip.QuickTipManager.init();
    Ext.define('Scpp.Model', {
        extend: 'Ext.data.Model',
        fields: ['FileID','FileLabel','FileName','FileSize','FileType','DateCreated','UserName'],
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
    function submitOnEnter(field, event) {
    	if (event.getKey() == event.ENTER) {
           store.load({
           params: {
               key: Ext.getCmp('key').getValue()
           }});
    	}
    }    
            
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
       dockedItems: [{
              xtype: 'pagingtoolbar',
              store: store,   // same store GridPanel is using
              dock: 'bottom',
              displayInfo: true
       },{
            xtype: 'toolbar',
            items: [{
                 xtype :'button',
                 icon: varjs.config.base_url+'images/icons/silk/disk_upload.png',
                 text: 'Upload',
                 handler: function() {
                   displayFormWindow();
                 }
            },{
               name: 'key', baseCls:'Sfr_TxtfieldSearchGrid',
               id: 'key',
               xtype:'textfield',
               listeners: {
              	   specialkey: submitOnEnter
              	}
           },{
                 xtype :'button',
                 icon: varjs.config.base_url+'images/icons/silk/search.png',
                 margin: '0px 0px 0px -10px',
                 text: 'Search',
                 handler: function() {
                   store.load({
                     params: {
                         key: Ext.getCmp('key').getValue()
                   }});
                 }
            }]
         },{
            xtype: 'toolbar',
            id:'toolbar_dua',
            hidden:true,
            items: [{
               itemId: 'download',
               icon: varjs.config.base_url+'images/icons/silk/disk_download.png',
               //cls:m_act_download,
               text: 'Download',
               scope: this,
               handler : function(){
                 var smb = Ext.getCmp('grid').getSelectionModel().getSelection()[0];
                 window.location = m_api+'files/documents/'+smb.raw.FileName;
               }
            },{
               itemId: 'remove',
               icon: varjs.config.base_url+'images/icons/silk/delete.png',
               cls:m_act_delete,
               text: 'Hapus',
               scope: this,
               handler : function(){
                 var sma = Ext.getCmp('grid').getSelectionModel().getSelection()[0];
                 Ext.MessageBox.confirm('Message', 'Apakah anda mau menghapus data ini ?' , function(btn){
                     if(btn == 'yes'){
                        Ext.Ajax.request({
                        waitMsg: 'Please Wait',
                        url: m_crud,
                        method : 'DELETE',
                        params: {id:  sma.raw.FileID},
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
       columns: [{
           text: 'Label', 
           width: '25%',
           dataIndex: 'FileLabel'
       },{
           text: 'Name', 
           width: '15%',
           dataIndex: 'FileName'
       },{
           text: 'Size (KB)', 
           width: '10%',
           dataIndex: 'FileSize'
       },{
           text: 'Type', 
           width: '20%',
           dataIndex: 'FileType'
       },{
           text: 'Created', 
           width: '15%',
           dataIndex: 'DateCreated'
       },{
           text: 'User', 
           width: '15%',
           dataIndex: 'UserName'
       }],
       listeners: {
         itemdblclick: function(dataview, index, item, e) {
            if (Ext.getCmp('toolbar_dua').isVisible()) Ext.getCmp('toolbar_dua').setVisible(false);
            else Ext.getCmp('toolbar_dua').setVisible(true);
         }
       }       
    });
    function displayFormWindow(){
        if(!win.isVisible()){
            win.show();
        } else {
            win.hide(this, function() {});
            win.toFront();
        }
    }

    var DataForm = Ext.create('Ext.form.Panel', {
        frame: false,
        height: 250,
        autoScroll: true,
        width: 580,
        bodyPadding: 5,
        id:'dataForm',
        fileUpload: true,
         enctype:'multipart/form-data',
         id:'upload',
        fieldDefaults: {
            labelAlign: 'left',
            labelWidth: 100,
            anchor: '100%'
        },
        items: [{
             xtype: 'textfield',
             id: 'label',
             name: 'label',
             fieldLabel: 'Label'
         },{            
            xtype: 'fileuploadfield',
            fieldLabel: 'Upload',
            labelWidth: 60,
            id: 'file',
            padding : 5,
            name: 'file',
            buttonText: 'Browse'
        }],
        buttons: [{
            id:'saveButton',
            text: 'Save',
               margin: '5px',
               scale: 'large',
            ui: 's-button',
            cls: 's-blue',
            handler: function() {
               var form = Ext.getCmp('upload').getForm();
               form.submit({
                   url: m_crud,
                   waitMsg: 'Sending files....',
                   success: function(fp, o) {
                        store.load();
                       win.hide();
                   },
                   failure: function(err) {
                     Ext.MessageBox.alert('error','Error on Upload please check FileType');
                   }
               });
            }
        },{
             text: 'Close',
               margin: '5px',
               scale: 'large',
             ui: 's-button',
             cls: 's-grey',
             disabled: false,
             handler: function() {
                 win.hide();
             }
        }]
    });
    var win = Ext.create('widget.window', {
        title: 'Upload File',
        id:'win',
        closable: true,
        modal:true,
        closeAction: 'hide',
        width: 600,
        height: 300,
        layout: {
            type: 'border',
            padding: 5
        },
        items: [DataForm]
    });
});

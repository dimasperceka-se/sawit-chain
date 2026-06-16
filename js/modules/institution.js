Ext.onReady(function(){
   Ext.tip.QuickTipManager.init();
    var store = Ext.create('Ext.data.Store', {
        extend: 'Ext.data.Model',
        fields: ['id','InstitutionName','PrivatePublic','nm'],
        autoLoad: true,
        pageSize: 10,
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
    
    function displayFormWindow(){
        if(!win.isVisible()){
            DataForm.getForm().reset();
            win.show();
            Ext.getCmp('InstitutionName').focus(true,true);
        } else {
            win.hide(this, function() {});
            win.toFront();
        }
    }
    var DataForm = Ext.create('Ext.form.Panel', {
        frame: false,
        height: 130,
        width: 400,
        bodyPadding: 5,
        id:'dataForm',
        fieldDefaults: {
            labelAlign: 'left',
            labelWidth: 120,
            anchor: '100%'
        },
        items: [
            {
                xtype: 'textfield',
                id: 'id',
                name: 'id',
                inputType:'hidden'
            },
            {
                xtype: 'textfield',
                fieldLabel: 'Name',
                id: 'InstitutionName',
                name: 'InstitutionName'
            },
            {
               fieldLabel : 'Status',
               xtype      : 'radiogroup',
               columns: 2,
               vertical: true,
               width: '100%',
               items: [//'inactive','active','normal','new'
                   {
                       boxLabel  : 'Privat',
                       name      : 'PrivatePublic',
                       inputValue: '1',
                       id        : 'PrivatePublic'
                   }, {
                       boxLabel  : 'Public',
                       name      : 'PrivatePublic',
                       inputValue: '2',
                       id        : 'PrivatePublic2'
                   }
               ]
            }
        ],
        buttons: [{
            id:'saveButton',
            text: 'Save',
            margin: '0px 0px 0px 6px',
            scale: 'medium',
            ui: 's-button',
            cls: 's-blue',
            handler: function() {
                var form = this.up('form').getForm();
                var methode;
                if (Ext.getCmp('id').getValue()=='') methode = 'POST'; else methode = 'PUT';
console.log(methode);                
form.submit({
                    url: m_crud,
                    method : methode,
                    waitMsg: 'Sending the data...',
                    success: function(fp, o) {
                        Ext.MessageBox.alert('Success', 'Data saved.');
                    }
                });
                win.hide(this, function() {
                    store.load();
                });
            }
        },{
             text: 'Close',
             margin: '0px 0px 0px 6px',
             scale: 'medium',
             ui: 's-button',
             cls: 's-grey',
             disabled: false,
             handler: function() {
                 win.hide();
             }
        }]
    });
    var win = Ext.create('widget.window', {
        title: 'Data Institution List',
        closable: true,
        modal:true,
        closeAction: 'show',
        width: 430,
        minWidth: 370,
        height: 170,
        layout: {
            type: 'border',
            padding: 5
        },
        items: [DataForm]
    });
    var grid = Ext.create('Ext.grid.Panel', {
       store: store,
       width: '100%',
       minHeight: 250,
       //title: 'Institution List List',
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
               handler : displayFormWindow,
               hidden : m_act_add
            }]
    }],
    columns: [
    {
        text: 'ID',
        dataIndex: 'id',
        hidden:true
    },
    {
        text: 'No',
        xtype: 'rownumberer',
        width:'5%'
    },
    {
        text: 'Name', 
        width: '40%',
        dataIndex: 'InstitutionName'
    },
    {
        text: 'Status', 
        width: '30%',
        dataIndex: 'nm'
    },
    {
        text: 'Action',
        xtype: 'actioncolumn',
        width: 50,
        items: [{
            icon: varjs.config.base_url+'images/icons/silk/pencil.png',
            tooltip: 'Edit',
            hidden : m_act_update,
            handler : function(grid, rowIndex, colIndex) {
               displayFormWindow();
               var sm = grid.getStore().getAt(rowIndex);
               Ext.Ajax.request({
                  url: m_crud,
                  method: 'GET',
                  params: {id: sm.get('id')},
                  success: function(fp, o){
                     var r = Ext.decode(fp.responseText);
                     console.log(r.InstitutionName)
                     Ext.getCmp('id').setValue(sm.get('id'));
                     Ext.getCmp('InstitutionName').setValue(r.InstitutionName);
                     if (r.PrivatePublic=='1') Ext.getCmp('PrivatePublic').setValue(true);
                     if (r.PrivatePublic=='2') Ext.getCmp('PrivatePublic2').setValue(true);
                  }
               });
            }
        },{
            icon: varjs.config.base_url+'images/icons/silk/delete.png',
            tooltip: 'Delete',
            hidden : m_act_delete,
            handler : function(grid, rowIndex, colIndex){
               var sma = grid.getStore().getAt(rowIndex);
               Ext.MessageBox.confirm('Message', 'Apakah anda mau menghapus institution ini ?' , function(btn){
                  if(btn == 'yes') {
                     Ext.Ajax.request({
                        waitMsg: 'Please Wait',
                        url: m_crud,
                        method : 'DELETE',
                        params: {id:  sma.get('id')},
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
    }
    ]
   });
});

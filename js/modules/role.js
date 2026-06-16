Ext.Loader.setConfig({enabled: true});
Ext.Loader.setPath('js/ext-4.2.0.663', '../ux');
//Ext.Loader.setPath('js/ext-4.2.0.663/ux/form');
Ext.require([
    //'Ext.form.Panel',
    //'Ext.ux.form.MultiSelect',
    'Ext.ux.form.ItemSelector'
]);
Ext.onReady(function(){
   Ext.tip.QuickTipManager.init();
   var store_role = Ext.create('Ext.data.Store', {
        extend: 'Ext.data.Model',
        fields: ['RoleId','RoleName','RoleObjectId','RoleDesc','ObjectName'],
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
    
    var mc_object = Ext.create('Ext.data.Store', {
        extend: 'Ext.data.Model',
        fields: ['ObjectId','ObjectName'],
        autoLoad: true,
        proxy: {
            type: 'ajax',
            url: m_object_list,
            reader: {
                type: 'json',
                root: ''
            }
        }
    });
    
    function displayFormWindow(){
        if(!win.isVisible()){
            RoleForm.getForm().reset();
            win.show();
            Ext.getCmp('RoleName').focus(true,true);
        } else {
            win.hide(this, function() {});
            win.toFront();
        }
    }   
    
    var storeGroupList = Ext.create('Ext.data.ArrayStore', {
        fields: ['GroupId','GroupName'],
        proxy: {
            type: 'ajax',
            url: m_group_list,
            reader: {
                type:'json',
                root: ''
            }
        },
        autoLoad: true
    });
    
    var dsReport = Ext.create('Ext.data.ArrayStore', {
        fields: ['RoleId','GroupId'],
        proxy: {
            type: 'ajax',
            url: m_role_group,
            extraParams: {id:''},
            reader: {
                type:'json',
                root:''
            }
        },
        autoLoad: false,
        listeners:{
            load:function(){
                var selected = [];
                var selector = Ext.getCmp("itemselector-group");
                dsReport.data.each(function(item, index, totalItems ) {
                    selected.push(item.data['GroupId']);
                });
                selector.setValue(selected);
            }
        }
    });
   
    //var RoleForm = Ext.create('Ext.form.Panel', {
    var RoleForm = Ext.widget('form',{
        frame: false,
        height: 600,
        autoScroll: true,
        width: 950,
        bodyPadding: 5,
        fieldDefaults: {
            labelAlign: 'left',
            labelWidth: 100,
            anchor: '100%'
        },
        items: [{
            xtype: 'textfield',
            id: 'RoleId',
            name: 'RoleId',
            inputType:'hidden'
        },{
            xtype: 'textfield',
            fieldLabel: lang('Nama'),
            id: 'RoleName',
            name: 'RoleName'
        },{
            xtype: 'textareafield',
            fieldLabel: lang('Deskripsi'),
            id: 'RoleDesc',
            name: 'RoleDesc'
        },{
            id: 'RoleObjectId',
            name: 'RoleObjectId',
            xtype: 'combobox',
            width: 100,
            fieldLabel: lang('Object'),
            store:mc_object,
            displayField: 'ObjectName',
            valueField: 'ObjectId',
            queryMode:'local',
        },{ // report
            xtype: 'itemselector',
            name: 'role_group',
            fieldLabel: lang('Select Group'),
            id: 'itemselector-group',
            anchor: '90%',
            height:320,
            store: storeGroupList,
            displayField: 'GroupName',
            valueField: 'GroupId',
            value: [],
            allowBlank: true,
            msgTarget: 'side',
            fromTitle: lang('Available'),
            toTitle: lang('Selected')
        },{
            layout: 'column',
            bodyStyle: 'padding:5px 5px 0',
            xtype: 'container',
            columns: 3,
            autoEl: 'div',
            items: [{
                xtype: 'button',
                scale: 'small',
                //ui: 's-button',
                //cls: 's-blue',
                text: lang('Select All'),
                style: {marginLeft:'11%'},
                handler: function() {
                    var selected = []; 
                    var selector = Ext.getCmp("itemselector-group");
                    selector.store.each(function(item, index, totalItems ) {
                        selected.push(item.data['GroupId']);
                    });
                    selector.setValue(selected);
                }
            },{
                xtype: 'button',
                scale: 'small',
                text: lang('Deselect All'),
                style: {marginLeft:'10px'},
                handler: function() {
                    Ext.getCmp("itemselector-group").reset();
                }
            }]
        }],
        buttons: [{
            text: 'Save',
            margin: '5px',
            scale: 'large',
            ui: 's-button',
            cls: 's-blue',
            handler: function() {
                var form = this.up('form').getForm();
                //var params = Ext.getCmp('menuu').getValue();
                var methode;
                if (Ext.getCmp('RoleId').getValue()=='') methode = 'POST'; else methode = 'PUT';
                
                form.submit({
                    //url: m_crud+'?'+ Ext.urlEncode(params),
                    url: m_crud,
                    method : methode,
                    waitMsg: lang('Sending data...'),
                    success: function(fp, o) {
                        Ext.MessageBox.alert('Success', 'Data saved.');
                    }
                });
               
                win.hide(this, function() {
                    store_role.load();
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
        title: 'Data Role',
        id:'win',
        closable: true,
        modal:true,
        closeAction: 'hide',
        width: 970,
        frame:false,
        minWidth: 370,
        height: 650,
        layout: {
            type: 'fit'
        },
        items: [RoleForm]
    });
    
    var grid = Ext.create('Ext.grid.Panel', {
       store: store_role,
       width: '100%',
       id:'grid',
       minHeight:250,
       //title: 'Group List',
       style: 'border:1px solid #CCC;',
       renderTo: 'ext-content',
       loadMask: true,
       selType: 'rowmodel',
       listeners : {
          itemdblclick: function(dv, record, item, index, e) {
            displayFormWindow();
            var sm = record;
            Ext.Ajax.request({
               url: m_crud,
               method: 'GET',
               params: {id: sm.get('RoleId')},
               success: function(fp, o){
                    var r = Ext.decode(fp.responseText);
                    Ext.getCmp('RoleId').setValue(sm.get('RoleId'));
                    Ext.getCmp('RoleName').setValue(r.RoleName);
                    Ext.getCmp('RoleDesc').setValue(r.RoleDesc);
                    Ext.getCmp('RoleObjectId').setValue(r.RoleObjectId);
               }
            });
            dsReport.load({params: {id: sm.get('RoleId')}});
          }
       },
       dockedItems: [{
              xtype: 'pagingtoolbar',
              store: store_role,   // same store GridPanel is using
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
               cls : m_act_add
            },{
               itemId: 'remove',
               icon: varjs.config.base_url+'images/icons/silk/delete.png',
               cls:m_act_delete,
               text: 'Hapus',
               scope: this,
               handler : function(){
                 var smb = Ext.getCmp('grid').getSelectionModel().getSelection()[0];
                 Ext.MessageBox.confirm('Message', lang('Apakah anda mau menghapus data ini ?') , function(btn){
                     if(btn == 'yes'){
                        Ext.Ajax.request({
                        waitMsg: lang('Please Wait'),
                        url: m_crud,
                        method : 'DELETE',
                        params: {RoleId:  smb.raw.RoleId},
                        success: function(response, opts){
                           var obj = Ext.decode(response.responseText);
                           switch(obj.success){
                              case true: store_role.load();
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
        text: 'Role ID',
        dataIndex: 'RoleId',
        hidden:true
    },
    {
        text: 'No',
        xtype: 'rownumberer',
        width:'5%'
    },
    {
        text: lang('Role Name'), 
        width: '25%',
        dataIndex: 'RoleName',
    },
    {
        text: lang('Object'), 
        width: '20%',
        dataIndex: 'ObjectName',
    },
    {
        text: lang('Description'), 
        width: '50%',
        dataIndex: 'RoleDesc',
    },
    ]
   });
});


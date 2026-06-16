Ext.onReady(function(){
    var store = Ext.create('Ext.data.Store', {
        extend: 'Ext.data.Model',
        fields: ['UnitMeasurementID','ParentUnitMeasurementID', 'Name', 'Description'],
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
    function set_update(sm) {
       Ext.Ajax.request({
           url: m_crud+'_c',
           method: 'GET',
           params: {UnitMeasurementID: sm.get('UnitMeasurementID')},
           success: function(fp, o){
               var r = Ext.decode(fp.responseText);console.log(r)
               Ext.getCmp('UnitMeasurementID').setValue(sm.get('UnitMeasurementID'));
               Ext.getCmp('OrgType').setValue(r.OrgType);
               Ext.getCmp('OrgID').setValue(r.OrgID);
               Ext.getCmp('OrgType').setReadOnly(true);
               Ext.getCmp('OrgID').setReadOnly(true);
               Ext.getCmp('Name').setValue(r.Name);
               Ext.getCmp('Description').setValue(r.Description);
           }
       });
    }
    //store combo
    var mc_org_type = Ext.create('Ext.data.Store', {
        fields: ['label'],
        data: [{'label': 'sce'}, {'label': 'koperasi'}],
        autoLoad: true
    });
    var mc_org_id = Ext.create('Ext.data.Store', {
        extend: 'Ext.data.Model',
        fields: ['id', 'label'],
        autoLoad: false,
        pageSize: 10,
        proxy: {
            type: 'ajax',
            url: m_api+'/bussiness/penjualan_org',
            reader: {
                type: 'json',
                root: 'data'
            }
        }
    });
    var mc_parent = Ext.create('Ext.data.Store', {
        extend: 'Ext.data.Model',
        fields: ['id', 'label'],
        autoLoad: false,
        pageSize: 10,
        proxy: {
            type: 'ajax',
            url: m_crud+'_parent',
            reader: {
                type: 'json',
                root: 'data'
            }
        }
    });
    //end store combo

    function set_disabled() {
        Ext.getCmp('OrgType').setReadOnly(false);
        Ext.getCmp('OrgID').setReadOnly(false);
        if (m_sce_id) {
            Ext.getCmp('OrgType').setValue('sce').setReadOnly(true);
            Ext.getCmp('OrgID').setValue(m_sce_id).setReadOnly(true);
        }
    }

    var grid = Ext.create('Ext.grid.Panel', {
        store: store,
        width: '100%',
        minHeight: 250,
        id:'grid',
        style: 'border:1px solid #CCC;',
        renderTo: 'ext-content',
        loadMask: true,
        selType: 'rowmodel',
        listeners : {
            itemdblclick: function(dv, record, item, index, e) {
                displayFormWindow();
                var sm = record;
                set_update(sm);
            }
        },
        dockedItems: [{
            xtype: 'pagingtoolbar',
            store: store,
            dock: 'bottom',
            displayInfo: true
        },{
            xtype: 'toolbar',
            items: [{
                icon: varjs.config.base_url + 'images/icons/new/add.png', cls:'Sfr_BtnGridGreen', overCls:'Sfr_BtnGridGreen-Hover',
                text: lang('Add'),
                scope: this,
                handler : function(){
                    displayFormWindow();
                    Ext.getCmp('OrgType').setValue();
                    Ext.getCmp('OrgID').setValue();
                    set_disabled();
                },
                cls : m_act_add
            },{
                icon: varjs.config.base_url+'images/icons/silk/pencil.png',
                text: lang('Update'),
                scope: this,
                handler : function(){
                  displayFormWindow();
                  var sm = Ext.getCmp('grid').getSelectionModel().getSelection()[0];
                  set_update(sm)
                },
                cls : m_act_update
            },{
               itemId: 'remove',
               icon: varjs.config.base_url+'images/icons/silk/delete.png',
               cls:m_act_delete,
               text: lang('Hapus'),
               scope: this,
               handler : function(){
                 var smb = Ext.getCmp('grid').getSelectionModel().getSelection()[0];
                 Ext.MessageBox.confirm('Message', lang('Apakah anda mau menghapus data ini ?') , function(btn){
                     if(btn == 'yes'){
                        Ext.Ajax.request({
                        waitMsg: lang('Please Wait'),
                        url: m_crud,
                        method : 'DELETE',
                        params: {UnitMeasurementID:  smb.raw.UnitMeasurementID},
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
            text: lang('No'),
            xtype: 'rownumberer',
            width:'5%'
        },{
            text: lang('Name'),
            width: '20%',
            dataIndex: 'Name',
        },{
            text: lang('Description'),
            width: '73%',
            dataIndex: 'Description'
        }]
    });

    function displayFormWindow(){
        if(!win.isVisible()){
            DataForm.getForm().reset();
            win.show();
        } else {
            win.hide(this, function() {});
            win.toFront();
        }
    }

    var DataForm = Ext.create('Ext.form.Panel', {
        frame: false,
        height: 350,
        width: '100%',
        bodyPadding: 5,
        autoScroll:true,
        id:'dataForm',
        fieldDefaults: {
            labelAlign: 'left',
            labelWidth: 150,
            anchor: '100%'
        },
        items: [{
                    xtype: 'textfield',
                    id: 'UnitMeasurementID',
                    name: 'UnitMeasurementID',
                    hidden:true
                },{
                   fieldLabel: lang('Org Type'),
                   id: 'OrgType',
                   name: 'OrgType',
                   xtype: 'combo',
                   store: mc_org_type,
                   displayField: 'label',
                   valueField: 'label',
                   queryMode: 'local',
                   listeners: {
                     change: function (cb, nv, ov) {
                        if (Ext.getCmp('OrgType').getValue()!=null)
                        mc_org_id.load({
                             params: {
                                 'OrgType': Ext.getCmp('OrgType').getValue()
                             }
                        });
                     }
                   }
               },{
                   fieldLabel: lang('Org'),
                   id: 'OrgID',
                   name: 'OrgID',
                   xtype: 'combo',
                   store: mc_org_id,
                   displayField: 'label',
                   valueField: 'id',
                   queryMode: 'local',
                  listeners   : {
                      beforequery: function(record){
                          record.query = new RegExp(record.query, 'i');
                          record.forceAll = true;
                      }
                  }
               },{
                    xtype: 'textfield',
                    fieldLabel: lang('Name'),
                    id: 'Name',
                    name: 'Name',
                },{
                       fieldLabel: lang('Description'),
                       xtype: 'textarea',
                       height: 40,
                       id: 'Description',
                       name: 'Description'
        }],
        buttons: [{
            id:'saveButton',
            text: lang('Save'),
            margin: '5px',
            scale: 'large',
            ui: 's-button',
            cls: 's-blue',
            handler: function() {
                var form = this.up('form').getForm();
                var method;
                if (Ext.getCmp('UnitMeasurementID').getValue()!='') method = 'PUT'; else method = 'POST';
                form.submit({
                    url: m_crud,
                    method:method,
                    waitMsg: 'Sending data...',
                    success: function(fp, o) {
                        Ext.MessageBox.alert('Success', 'Data saved.');
                    }
                });
                win.hide(this, function() {
                    store.load();
                });

            }
        },{
            text: lang('Close'),
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
        title: lang('Category'),
        id:'win',
        closable: true,
        modal:true,
        closeAction: 'show',
        width: '90%',
        height: '90%',
        layout: {
            type: 'fit'
        },
        items: [DataForm],
        listeners:{
            'close':function(){
                //clearItemselector();
            }
        }
    });

});

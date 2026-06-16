Ext.onReady(function(){
    var store = Ext.create('Ext.data.Store', {
        extend: 'Ext.data.Model',
        fields: ['DemoplotID','DateHarvest', 'AmountWetBeans', 'DateSales', 'DryingDay', 'AmountDryBeans', 'Price', 'Total'],
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
           url: m_crud,
           method: 'GET',
           params: {DemoplotID: sm.get('DemoplotID')},
           success: function(fp, o){
               var r = Ext.decode(fp.responseText);console.log(r)
               Ext.getCmp('DemoplotID').setValue(sm.get('DemoplotID'));
               Ext.getCmp('OrgType').setValue(r.OrgType);
               Ext.getCmp('OrgID').setValue(r.OrgID);
               Ext.getCmp('DateHarvest').setValue(r.DateHarvest);
               Ext.getCmp('AmountWetBeans').setValue(r.AmountWetBeans);
               Ext.getCmp('DateSales').setValue(r.DateSales);
               Ext.getCmp('DryingDay').setValue(r.DryingDay);
               Ext.getCmp('AmountDryBeans').setValue(r.AmountDryBeans);
               Ext.getCmp('Price').setValue(r.Price);
               Ext.getCmp('Total').setValue(r.Total);
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
    var mc_buyer_org_type = Ext.create('Ext.data.Store', {
        fields: ['label'],
        data: [{'label': 'koperasi'},{'label': 'warehouse'}],
        autoLoad: true
    });
    var mc_buyer_org_id = Ext.create('Ext.data.Store', {
        extend: 'Ext.data.Model',
        fields: ['id', 'label'],
        autoLoad: false,
        pageSize: 10,
        proxy: {
            type: 'ajax',
            url: m_api+'/bussiness/penjualan_buyer_org',
            reader: {
                type: 'json',
                root: 'data'
            }
        }
    });
    //end store combo
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
                    Ext.getCmp('OrgType').setValue(Ext.getCmp('searchOrgType').getValue())
                    Ext.getCmp('OrgID').setValue(Ext.getCmp('searchOrgID').getValue())
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
                        params: {DemoplotID:  smb.raw.DemoplotID},
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
            },{              emptyText: lang('Org Type'),
                   id: 'searchOrgType',
                   name: 'searchOrgType',
                   xtype: 'combo',
                   store: mc_org_type,
                   width:100,
                   displayField: 'label',
                   valueField: 'label',
                   queryMode: 'local',
                   listeners: {
                     change: function (cb, nv, ov) {
                        if (Ext.getCmp('searchOrgType').getValue()!=null)
                        mc_org_id.load({
                             params: {
                                 'OrgType': Ext.getCmp('searchOrgType').getValue()
                             }
                        });
                     }
                   }
               },{emptyText: lang('Org ID'),
                   id: 'searchOrgID',
                   name: 'searchOrgID',
                   width:250,
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
              xtype: 'datefield',
                   width:120,
              name: 'searchAwal',
              id: 'searchAwal',
              format: 'Y-m-d',
              emptyText: lang('Awal'),
          }, {
              xtype: 'label',
              text: ' s.d '
          },{
              xtype: 'datefield',
                   width:120,
              name: 'searchAkhir',
              id: 'searchAkhir',
              format: 'Y-m-d',
              emptyText: lang('Akhir'),
          }, {
              xtype: 'button',
              margin: '0px 0px 0px 6px',
              text: 'Search',
              handler: function () {
                  store.load({
                      params: {
                          Awal: Ext.getCmp('searchAwal').getSubmitValue(),
                          Akhir: Ext.getCmp('searchAkhir').getSubmitValue(),
                          OrgType: Ext.getCmp('searchOrgType').getValue(),
                          OrgID: Ext.getCmp('searchOrgID').getValue(),
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
            text: lang('Date Harvest'),
            width: '10%',
            dataIndex: 'DateHarvest'
        },{
            text: lang('Wet Beans'),
            width: '17%',
            dataIndex: 'AmountWetBeans',
            xtype:'numbercolumn',
            format:'0.00'
        },{
            text: lang('Date Sales'),
            width: '13%',
            dataIndex: 'DateSales'
        },{
            text: lang('Drying Day'),
            width: '10%',
            dataIndex: 'DryingDay'
        },{
            text: lang('Dry Beans'),
            width: '17%',
            dataIndex: 'AmountDryBeans'
        },{
            text: lang('Price'),
            width: '13%',
            dataIndex: 'Price'
        },{
            text: lang('Total'),
            width: '13%',
            dataIndex: 'Total'
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
            layout: 'column',
            border: false,
            items: [{
                 columnWidth: .48,
                 layout: 'form',
                 padding: 10,
                 xtype: 'fieldset',
                 title: 'Data',
                 height: '100%',
                 items: [{
                    xtype: 'textfield',
                    id: 'DemoplotID',
                    name: 'DemoplotID',
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
                   listeners: {
                      beforequery: function(record){  
                          record.query = new RegExp(record.query, 'i');
                          record.forceAll = true;
                      }
                   }
               },{
                    xtype: 'datefield',
                    fieldLabel: lang('Date Harvest'),
                    id: 'DateHarvest',
                    name: 'DateHarvest',
                    format: 'Y-m-d'
                },{
                    xtype: 'numericfield',
                    fieldLabel: lang('Wet Beans (Kg)'),
                    id: 'AmountWetBeans',
                    name: 'AmountWetBeans'
                },{
                    xtype: 'datefield',
                    fieldLabel: lang('Date Sales'),
                    id: 'DateSales',
                    name: 'DateSales',
                    format: 'Y-m-d',
                     listeners: {
                        change: function (cb, nv, ov) {
                           var oneDay = 24*60*60*1000; // hours*minutes*seconds*milliseconds
                           var firstDate = Ext.getCmp('DateHarvest').getValue();//new Date(2008,01,12);
                           var secondDate = Ext.getCmp('DateSales').getValue();//new Date(2008,01,22);                  
                           var diffDays = Math.round(Math.abs((firstDate.getTime() - secondDate.getTime())/(oneDay)));
                           Ext.getCmp('DryingDay').setValue(diffDays);
                        }
                     }
                },{
                    xtype: 'numericfield',
                    fieldLabel: lang('Drying Day'),
                    id: 'DryingDay',
                    name: 'DryingDay'
                },{
                    xtype: 'numericfield',
                    fieldLabel: lang('Dry Beans (Kg)'),
                    id: 'AmountDryBeans',
                    name: 'AmountDryBeans'
                }]
            },{ // right fieldset
                columnWidth: .52,
                height: '100%',
                layout: 'form',
                xtype: 'fieldset',
                title: 'Detail',
                style:'margin-left:12px',
                items: [{
                   fieldLabel: lang('Pembeli Org Type'),
                   id: 'BuyerOrgType',
                   name: 'BuyerOrgType',
                   xtype: 'combo',
                   store: mc_buyer_org_type,
                   displayField: 'label',
                   valueField: 'label',
                   queryMode: 'local',
                   listeners: {
                     change: function (cb, nv, ov) {
                        if (Ext.getCmp('BuyerOrgType').getValue()!=null)
                        mc_buyer_org_id.load({
                             params: {
                                 'BuyerOrgType': Ext.getCmp('BuyerOrgType').getValue(),
                                 'OrgType': Ext.getCmp('OrgType').getValue(),
                                 'OrgID': Ext.getCmp('OrgID').getValue()
                             }
                        });
                     }
                   }
               },{
                   fieldLabel: lang('Pembeli Org'),
                   id: 'BuyerOrgID',
                   name: 'BuyerOrgID',
                   xtype: 'combo',
                   store: mc_buyer_org_id,
                   displayField: 'label',
                   valueField: 'id',
                   queryMode: 'local',
                   listeners: {
                      beforequery: function(record){  
                          record.query = new RegExp(record.query, 'i');
                          record.forceAll = true;
                      }
                   }
               },{
                       xtype: 'numericfield',
                       fieldLabel: lang('Price (Rp)'),
                       id: 'Price',
                       name: 'Price',
                        listeners: {
                           change: function (cb, nv, ov) {
                              Ext.getCmp('Total').setValue(Ext.getCmp('Price').getValue()*Ext.getCmp('AmountDryBeans').getValue())
                           }
                        }
                   },{
                       xtype: 'numericfield',
                       fieldLabel: lang('Total'),
                       id: 'Total',
                       name: 'Total'
                   },{
                       fieldLabel: lang('Description'),
                       xtype: 'textarea',
                       height: 40,
                       id: 'Description',
                       name: 'Description'
                  }]
            }]                  
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
                if (Ext.getCmp('DemoplotID').getValue()!='') method = 'PUT'; else method = 'POST';
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
        title: lang('Wow Farm'),
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

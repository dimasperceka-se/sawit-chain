Ext.onReady(function(){
    Ext.tip.QuickTipManager.init();
    var store = Ext.create('Ext.data.Store', {
        extend: 'Ext.data.Model',
        fields: ['WarehouseTraderID','TransactionID','CompanyAlias','FarmerName','VolumeNetto','ContractPrice','NetPrice','TotalPayment'],
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
    var store_Province = Ext.create('Ext.data.Store', {
        extend: 'Ext.data.Model',
        fields: ['id','province'],
        proxy: {
            type: 'ajax',
            url: m_Province,
            reader: {
                type: 'json',
                root: 'data'
            }
        }
    });
    var store_District = Ext.create('Ext.data.Store', {
        extend: 'Ext.data.Model',
        fields: ['id','district'],
        proxy: {
            type: 'ajax',
            url: m_District,
            reader: {
                type: 'json',
                root: 'data'
            }
        }
    });
    function displayFormWindow(){
         Ext.Ajax.request({
            url: m_crud+'_ff',
            method: 'GET',
            success: function(fp, o){
                 var r = Ext.decode(fp.responseText);
                 Ext.getCmp('FFMoisture').setValue(r.Moisture);
                 Ext.getCmp('FFBeanCount').setValue(r.BeanCount);
                 Ext.getCmp('FFMouldy').setValue(r.Mouldy);
                 Ext.getCmp('FFInsect').setValue(r.Insect);
                 Ext.getCmp('FFSlaty').setValue(r.Slaty);
                 Ext.getCmp('FFWaste').setValue(r.Waste);
                 Ext.getCmp('FAQMoisture').setValue(r.Moisture);
                 Ext.getCmp('FAQBeanCount').setValue(r.BeanCount);
                 Ext.getCmp('FAQMouldy').setValue(r.Mouldy);
                 Ext.getCmp('FAQInsect').setValue(r.Insect);
                 Ext.getCmp('FAQSlaty').setValue(r.Slaty);
                 Ext.getCmp('FAQWaste').setValue(r.Waste);
                 Ext.getCmp('ContractPrice').setValue(r.Price);
                 Ext.getCmp('WarehouseTraderID').setValue(r.TraderID);
            }
         })
        if(!win.isVisible()){
            DataForm.getForm().reset();
            win.show();
        } else {
            win.hide(this, function() {});
            win.toFront();
        }
        var today = new Date();
        var mm = today.getMonth()+1;
        if (mm<10) mm = '0'+mm;
        Ext.getCmp('DateTransaction').setValue(today.getFullYear()+'-'+mm+'-'+today.getDate());
    }
    //package
   Ext.define('detail.Model', {
        extend: 'Ext.data.Model',
        fields: ['DetailID','TransactionID','PackageID','Weight','Moisture','PackageType'],
   });
   var store_detail = Ext.create('Ext.data.Store', {
      model: 'detail.Model',
      autoLoad: false,
      pageSize: 10,
      proxy: {
         type: 'ajax',
         url: m_crud+'s_detail',
         reader: {
            type: 'json',
            root: 'data',
            totalProperty: 'total'
         }
      }
   });
   var cpackage = Ext.create('Ext.data.Store', {
      extend: 'Ext.data.Model',
      fields: ['id','label','berat'],
      autoLoad: true,
      pageSize: 10,
      proxy: {
         type: 'ajax',
         url: m_crud+'_package',
         reader: {
            type: 'json',
            root: 'data'
         }
      }
   });
   var pRowEditing = Ext.create('Ext.grid.plugin.RowEditing', {
      id: 'pRowEditing',
      clicksToMoveEditor: 0,
      autoCancel: false,
      errorSummary : false,
      clicksToEdit: 2
   });
    //end package  
    function fsearch() {
      Ext.Ajax.request({
         url: m_crud+'_farmer',
         method: 'GET',
         params: {id: Ext.getCmp('FarmerID').getValue()},
         success: function(fp, o){
              var r = Ext.decode(fp.responseText);
              Ext.getCmp('FarmerName').setValue(r.FarmerName);
              Ext.getCmp('GroupName').setValue(r.GroupName);
              Ext.getCmp('District').setValue(r.District);
         }
      })      
    }
    function fsave(){
       var form = Ext.getCmp('dataForm').getForm();
       var methode;
       if (Ext.getCmp('TransactionID').getValue()!='') methode = 'PUT'; else methode = 'POST';
       form.submit({
           url: m_crud,
           method: methode,
           waitMsg: 'Sending data...',
           success: function(fp, o) {
              if (o.result.id>0) {
               Ext.getCmp('TransactionID').setValue(o.result.id);
               Ext.getCmp('PackageSupplychainID').setValue(o.result.id);
               Ext.getCmp('PriceSupplychainID').setValue(o.result.id);
              } else {
                 Ext.MessageBox.alert('Success', 'Data saved.');
                 win.hide(this, function() {
                    store.load();
                 });
              }
           }
       });
    }
    function submitOnEnter(field, event) {
    	if (event.getKey() == event.ENTER) {
    	    fsearch()
    	}
    }    
    function fset(sm) {
      Ext.Ajax.request({
         url: m_crud,
         method: 'GET',
         params: {id: sm.get('TransactionID')},
         success: function(fp, o){
              var r = Ext.decode(fp.responseText);
              Ext.getCmp('TransactionID').setValue(sm.get('TransactionID'));
              Ext.getCmp('WarehouseTraderID').setValue(r.WarehouseTraderID);
              Ext.getCmp('FarmerID').setValue(r.FarmerID);
              Ext.getCmp('FarmerName').setValue(r.FarmerName);
              Ext.getCmp('GroupName').setValue(r.GroupName);
              Ext.getCmp('District').setValue(r.District);
              Ext.getCmp('DateTransaction').setValue(r.DateTransaction);
              Ext.getCmp('VolumeBruto').setValue(r.VolumeBruto);
              Ext.getCmp('Package').setValue(r.VolumeBruto-r.VolumeNetto);
              Ext.getCmp('VolumeNetto').setValue(r.VolumeNetto);
              
              Ext.getCmp('TRMoisture').setValue(r.TRMoisture);
              Ext.getCmp('TRBeanCount').setValue(r.TRBeanCount);
              Ext.getCmp('TRMouldy').setValue(r.TRMouldy);
              Ext.getCmp('TRInsect').setValue(r.TRInsect);
              Ext.getCmp('TRSlaty').setValue(r.TRSlaty);
              Ext.getCmp('TRWaste').setValue(r.TRWaste);
              
              Ext.getCmp('RewardMoisture').setValue(r.RewardMoisture);
              Ext.getCmp('RewardBeanCount').setValue(r.RewardBeanCount);
              Ext.getCmp('RewardMouldy').setValue(r.RewardMouldy);
              Ext.getCmp('RewardInsect').setValue(r.RewardInsect);
              Ext.getCmp('RewardSlaty').setValue(r.RewardSlaty);
              Ext.getCmp('RewardWaste').setValue(r.RewardWaste);

              Ext.getCmp('ClaimMoisture').setValue(r.ClaimMoisture);
              Ext.getCmp('ClaimBeanCount').setValue(r.ClaimBeanCount);
              Ext.getCmp('ClaimMouldy').setValue(r.ClaimMouldy);
              Ext.getCmp('ClaimInsect').setValue(r.ClaimInsect);
              Ext.getCmp('ClaimSlaty').setValue(r.ClaimSlaty);
              Ext.getCmp('ClaimWaste').setValue(r.ClaimWaste);

              Ext.getCmp('ContractPrice').setValue(r.ContractPrice);
              Ext.getCmp('NetPrice').setValue(r.NetPrice);
              Ext.getCmp('Reward').setValue(r.Reward);
              Ext.getCmp('TotalPayment').setValue(r.TotalPayment);
              Ext.getCmp('VolumeNetto2').setValue(r.VolumeNetto);
               store_detail.load({
                  params: {
                     id: sm.get('TransactionID')
               }});
         }
      });
    }
    var DataForm = Ext.create('Ext.form.Panel', {
        frame: false,
        autoScroll: true,
        bodyPadding: 5,
        id:'dataForm',
        fieldDefaults: {
            labelAlign: 'left',
            labelWidth: 120,
            anchor: '100%'
        },
        items: [{
         layout: 'column',
         items: [{
          columnWidth: 0.5,
          layout: 'form',
          padding:5,
          items:[{
               xtype: 'textfield',
               fieldLabel: 'Transaction ID',
               id: 'TransactionID',
               name: 'TransactionID',
               readOnly:true
           },{
               xtype: 'textfield',
               id: 'WarehouseTraderID',
               name: 'WarehouseTraderID',
               hidden:true
           },{
            layout: 'column',
            items: [{
             columnWidth: 0.7,
             layout: 'form',
             items:[{
               xtype: 'textfield',
               fieldLabel: 'Farmer ID',
               id: 'FarmerID',
               name: 'FarmerID',
               listeners: {
              		specialkey: submitOnEnter
              	}
             }]
             },{
             columnWidth: 0.3,
             items:[{
                    xtype :'button',
                    icon: varjs.config.base_url+'images/icons/silk/search.png',
                    margin: '0px 0px 0px 6px',
                    text: 'Search',
                    handler: function() {
                        fsearch()
                    }
               }]
             }]
           },{
               xtype: 'textfield',
               fieldLabel: 'Farmer Name',
               id: 'FarmerName',
               name: 'FarmerName',
               readOnly:true
           }]
          },{
          columnWidth: 0.5,
          layout: 'form',
          padding:5,
          items:[{
               xtype: 'textfield',
               fieldLabel: 'Group Name',
               id: 'GroupName',
               name: 'GroupName',
               readOnly:true
           },{
               xtype: 'textfield',
               fieldLabel: 'District',
               id: 'District',
               name: 'District',
               readOnly:true
           },{
               xtype: 'datefield',
               fieldLabel: 'Date Transaction',
               id: 'DateTransaction',
               name: 'DateTransaction',
               format:'Y-m-d'
           }]
          }]
        },{
            xtype: 'gridpanel',
            id:'grid_detail',
            store: store_detail,
            width: '100%',
            loadMask: true,
            selType: 'rowmodel',
            dockedItems: [{
               xtype: 'toolbar',
               id:'toolbar_detail',
               items: [{
                  icon: varjs.config.base_url + 'images/icons/new/add.png', cls:'Sfr_BtnGridGreen', overCls:'Sfr_BtnGridGreen-Hover',
                  text: 'Add',
                  cls : m_act_save,
                  scope: this,
                  handler :function(){
                       if (Ext.getCmp('TransactionID').getValue()=='') fsave()
                       store_detail.load();
                       pRowEditing.cancelEdit();
                       var r = Ext.create('detail.Model', {
                           DetailID:'', TransactionID:'', PackageID:'', Weight:'', Moisture:'',PackageType:''
                       });
                       store_detail.insert(0, r);
                       pRowEditing.startEdit(0, 0);
                  }
               },{
                  icon: varjs.config.base_url+'images/icons/silk/pencil.png',
                  cls:m_act_save,
                  text: 'Edit',
                  scope: this,
                  handler : function() {
                    pRowEditing.cancelEdit();
                    var sm = Ext.getCmp('grid_detail').getSelectionModel().getSelection();
                    pRowEditing.startEdit(sm[0].index, 0);
                  }
               },{
                  itemId: 'remove',
                  icon: varjs.config.base_url+'images/icons/silk/delete.png',
                  text: 'Hapus',
                  scope: this,
                  handler : function(){
                    var smb = Ext.getCmp('grid_detail').getSelectionModel().getSelection()[0];
                    pRowEditing.cancelEdit();
                    Ext.MessageBox.confirm('Message', 'Apakah anda mau menghapus buying unit ini ?' , function(btn){
                        if(btn == 'yes'){
                           Ext.Ajax.request({
                              waitMsg: 'Please Wait',
                              url: m_crud+'_detail',
                              method : 'DELETE',
                              params: {
                                 id:  smb.raw.DetailID
                              },
                              success: function(response, opts){
                                 var obj = Ext.decode(response.responseText);
                                 switch(obj.success){
                                    case true:
                                       store_detail.load({
                                          params: {
                                             id: Ext.getCmp('TransactionID').getValue()
                                       }});
                                    break;
                                    default:
                                        Ext.MessageBox.alert('Warning',obj.message);
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
              text: 'No',
              xtype: 'rownumberer',
              width:'5%'
            },{
              text: 'Package',
              dataIndex: 'PackageType',
              width:'55%',
              editor: {
                  xtype      : 'combo',
                  store : cpackage,
                  id:'PackageI',
                  queryMode: 'local',
                  displayField: 'label',
                  valueField: 'id',
                  listeners: {
                     'change': function(){
                         var id = Ext.getCmp('PackageI').getValue();
                         var ids = id.split('-');
                         Ext.getCmp('Package').setValue(ids[1]);
                     }
                 }
              }
            },{
              text: 'Weight',
              dataIndex: 'Weight',
              width:'20%',
              editor: {
                 xtype:'textfield',
                 id:'Weigh',
                 allowBlank:false,
                 listeners: {
                     'blur': function(){
                         Ext.getCmp('VolumeBruto').setValue(parseFloat(Ext.getCmp('Weigh').getValue())+
                           parseFloat(Ext.getCmp('VolumeBruto').getValue()));
                         Ext.getCmp('VolumeNetto').setValue(parseFloat(Ext.getCmp('VolumeBruto').getValue())-
                           parseFloat(Ext.getCmp('Package').getValue()));
                         Ext.getCmp('VolumeNetto2').setValue(Ext.getCmp('VolumeNetto').getValue());
                         //Ext.getCmp('TotalPayment').setValue(parseFloat(Ext.getCmp('VolumeNetto').getValue())*
                           //parseFloat(Ext.getCmp('ContractPrice').getValue()));
                     }
                 }
              }
            },{
              text: 'Moisture',
              dataIndex: 'Moisture',
              width:'20%',
              editor: {
                 xtype:'textfield',
                 allowBlank:false
              }
            },{
              dataIndex: 'PackageID',
              hidden:true
            }],
            plugins: [pRowEditing],
            listeners: {
                'canceledit':function(editor,e,eOpts){
                     store_detail.load({
                        params: {
                           id: Ext.getCmp('TransactionID').getValue()
                     }});
                },
                'edit': function(editor, e) {
                  if(e.record.data.DetailID==''){
                     Ext.Ajax.request({
                         waitMsg: 'Please wait...',
                         url: m_crud+'_detail',
                         method : 'POST',
                         params: {
                            TransactionID      : Ext.getCmp('TransactionID').getValue(),
                            PackageType    : e.record.data.PackageType,
                            PackageID    : e.record.data.PackageI,
                            Weight   : e.record.data.Weight,
                            Moisture   : e.record.data.Moisture
                         },
                         success: function(response, opts){
                             var obj = Ext.decode(response.responseText);
                             switch(obj.success){
                                 case true:
                                    Ext.MessageBox.alert('Success',obj.message);
                                    store_detail.load({
                                       params: {
                                          id: Ext.getCmp('TransactionID').getValue()
                                    }});
                                    break;
                                 default:
                                    Ext.MessageBox.alert('Warning',obj.message);
                                 break;
                             }
                         },
                         failure: function(response, opts){
                             var obj = Ext.decode(response.responseText);
                             Ext.MessageBox.alert('error','Could not connect to the database. Retry later');
                         }
                     });
                  } else {
                     Ext.MessageBox.confirm('Message', 'Update data price ini ?' , function(btn){
                        if(btn == 'yes') {
                           Ext.Ajax.request({
                              waitMsg: 'Please wait...',
                              url: m_crud+'_detail',
                              method : 'PUT',
                              params: {
                                  TransactionID      : Ext.getCmp('TransactionID').getValue(),
                                  DetailID    : e.record.data.DetailID,
                                  PackageType    : e.record.data.PackageType,
                                  PackageID    : e.record.data.PackageID,
                                  Weight   : e.record.data.Weight,
                                  Moisture   : e.record.data.Moisture
                              },
                              success: function(response, opts){
                                  var obj = Ext.decode(response.responseText);
                                  switch(obj.success){
                                      case true:
                                         Ext.MessageBox.alert('Success',obj.message);
                                         store_detail.load({
                                             params: {
                                                id: Ext.getCmp('TransactionID').getValue()
                                         }});
                                         break;
                                      default:
                                         Ext.MessageBox.alert('Warning',obj.message);
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
               }
            }
         },{
         layout: 'column',
         items: [{
          columnWidth: 0.6,
          layout: 'form',
          padding:5,
          items:[{}]
         },{
          columnWidth: 0.4,
          layout: 'form',
          padding:5,
          items:[
         {
            xtype: 'textfield',
            fieldLabel: 'Bruto',
            id: 'VolumeBruto',
            name: 'VolumeBruto',
            //readOnly:true
        },{
            xtype: 'textfield',
            fieldLabel: 'Package',
            id: 'Package',
            name: 'Package',
            //readOnly:true
        },{
            xtype: 'textfield',
            fieldLabel: 'Netto',
            id: 'VolumeNetto',
            name: 'VolumeNetto',
            //readOnly:true
        }]}]},{
            xtype: 'fieldset',
            title: 'Quality',
            items: [{
               layout: 'column',
               items: [{
                columnWidth: 0.2,
                layout: 'form',
                padding:5,
                items:[{
                  xtype: 'label',
                  text: 'Detail'
                },{
                  xtype: 'label',
                  text: 'Moisture'
                },{
                  xtype: 'tbspacer',
                  height:3
                },{
                  xtype: 'label',
                  text: 'Bean Count'
                },{
                  xtype: 'tbspacer',
                  height:3
                },{
                  xtype: 'label',
                  text: 'Mouldy'
                },{
                  xtype: 'tbspacer',
                  height:3
                },{
                  xtype: 'label',
                  text: 'Insect'
                },{
                  xtype: 'tbspacer',
                  height:3
                },{
                  xtype: 'label',
                  text: 'Slaty'
                },{
                  xtype: 'tbspacer',
                  height:3
                },{
                  xtype: 'label',
                  text: 'Waste'
                }]
               },{
                columnWidth: 0.2,
                layout: 'form',
                padding:5,
                items:[{
                  xtype: 'label',
                  text: 'Test Result'
                },{
                  xtype: 'textfield',
                  id: 'TRMoisture',
                  name:'TRMoisture',
                  listeners: {
                     'blur': function(){
                         /*if (Ext.getCmp('TRMoisture').getValue()>Ext.getCmp('FFMoisture').getValue())
                            Ext.getCmp('RewardMoisture').setValue(parseFloat(Ext.getCmp('TRMoisture').getValue())-
                              parseFloat(Ext.getCmp('FFMoisture').getValue()));
                         else Ext.getCmp('ClaimMoisture').setValue(parseFloat(Ext.getCmp('FFMoisture').getValue())-
                           parseFloat(Ext.getCmp('TRMoisture').getValue()));*/
                     }
                 }
                },{
                  xtype: 'textfield',
                  id: 'TRBeanCount',
                  name:'TRBeanCount',
                  listeners: {
                     'blur': function(){
                         /*if (Ext.getCmp('TRBeanCount').getValue()>Ext.getCmp('FFBeanCount').getValue())
                            Ext.getCmp('RewardBeanCount').setValue(parseFloat(Ext.getCmp('TRBeanCount').getValue())-
                              parseFloat(Ext.getCmp('FFBeanCount').getValue()));
                         else Ext.getCmp('ClaimBeanCount').setValue(parseFloat(Ext.getCmp('FFBeanCount').getValue())-
                           parseFloat(Ext.getCmp('TRBeanCount').getValue()));*/
                     }
                 }
                },{
                  xtype: 'textfield',
                  id: 'TRMouldy',
                  name:'TRMouldy',
                  listeners: {
                     'blur': function(){
                         /*if (Ext.getCmp('TRMouldy').getValue()>Ext.getCmp('FFMouldy').getValue())
                            Ext.getCmp('RewardMouldy').setValue(parseFloat(Ext.getCmp('TRMouldy').getValue())-
                              parseFloat(Ext.getCmp('FFMouldy').getValue()));
                         else Ext.getCmp('ClaimMouldy').setValue(parseFloat(Ext.getCmp('FFMouldy').getValue())-
                           parseFloat(Ext.getCmp('TRMouldy').getValue()));*/
                     }
                 }
                },{
                  xtype: 'textfield',
                  id: 'TRInsect',
                  name:'TRInsect',
                  listeners: {
                     'blur': function(){
                         /*if (Ext.getCmp('TRInsect').getValue()>Ext.getCmp('FFInsect').getValue())
                            Ext.getCmp('RewardInsect').setValue(parseFloat(Ext.getCmp('TRInsect').getValue())-
                              parseFloat(Ext.getCmp('FFInsect').getValue()));
                         else Ext.getCmp('ClaimInsect').setValue(parseFloat(Ext.getCmp('FFInsect').getValue())-
                           parseFloat(Ext.getCmp('TRInsect').getValue()));*/
                     }
                 }
                },{
                  xtype: 'textfield',
                  id: 'TRSlaty',
                  name:'TRSlaty',
                  listeners: {
                     'blur': function(){
                         /*if (Ext.getCmp('TRSlaty').getValue()>Ext.getCmp('FFSlaty').getValue())
                            Ext.getCmp('RewardSlaty').setValue(parseFloat(Ext.getCmp('TRSlaty').getValue())-
                              parseFloat(Ext.getCmp('FFSlaty').getValue()));
                         else Ext.getCmp('ClaimSlaty').setValue(parseFloat(Ext.getCmp('FFSlaty').getValue())-
                           parseFloat(Ext.getCmp('TRSlaty').getValue()));*/
                     }
                 }
                },{
                  xtype: 'textfield',
                  id: 'TRWaste',
                  name:'TRWaste',
                  listeners: {
                     'blur': function(){
                         /*if (Ext.getCmp('TRWaste').getValue()>Ext.getCmp('FFWaste').getValue())
                            Ext.getCmp('RewardWaste').setValue(parseFloat(Ext.getCmp('TRWaste').getValue())-
                              parseFloat(Ext.getCmp('FFWaste').getValue()));
                         else Ext.getCmp('ClaimWaste').setValue(parseFloat(Ext.getCmp('FFWaste').getValue())-
                           parseFloat(Ext.getCmp('TRWaste').getValue()));*/
                     }
                 }
                }]
               },{
                columnWidth: 0.15,
                layout: 'form',
                padding:5,
                items:[{
                  xtype: 'label',
                  text: 'FAQ'
                },{
                  xtype: 'textfield',
                  readOnly:true,
                  id: 'FAQMoisture',
                },{
                  xtype: 'textfield',
                  readOnly:true,
                  id: 'FAQBeanCount',
                },{
                  xtype: 'textfield',
                  readOnly:true,
                  id: 'FAQMouldy',
                },{
                  xtype: 'textfield',
                  readOnly:true,
                  id: 'FAQInsect',
                },{
                  xtype: 'textfield',
                  readOnly:true,
                  id: 'FAQSlaty',
                },{
                  xtype: 'textfield',
                  readOnly:true,
                  id: 'FAQWaste'
                }]
               },{
                columnWidth: 0.15,
                layout: 'form',
                padding:5,
                items:[{
                  xtype: 'label',
                  text: 'FF'
                },{
                  xtype: 'textfield',
                  readOnly:true,
                  id: 'FFMoisture',
                },{
                  xtype: 'textfield',
                  readOnly:true,
                  id: 'FFBeanCount',
                },{
                  xtype: 'textfield',
                  readOnly:true,
                  id: 'FFMouldy',
                },{
                  xtype: 'textfield',
                  readOnly:true,
                  id: 'FFInsect',
                },{
                  xtype: 'textfield',
                  readOnly:true,
                  id: 'FFSlaty',
                },{
                  xtype: 'textfield',
                  readOnly:true,
                  id: 'FFWaste'
                }]
               },{
                columnWidth: 0.15,
                layout: 'form',
                padding:5,
                items:[{
                  xtype: 'label',
                  text: 'Claim'
                },{
                  xtype: 'textfield',
                  id: 'ClaimMoisture',
                  name:'ClaimMoisture',
                  readOnly:true
                },{
                  xtype: 'textfield',
                  id: 'ClaimBeanCount',
                  name:'ClaimBeanCount',
                  readOnly:true
                },{
                  xtype: 'textfield',
                  id: 'ClaimMouldy',
                  name:'ClaimMouldy',
                  readOnly:true
                },{
                  xtype: 'textfield',
                  id: 'ClaimInsect',
                  name:'ClaimInsect',
                  readOnly:true
                },{
                  xtype: 'textfield',
                  id: 'ClaimSlaty',
                  name:'ClaimSlaty',
                  readOnly:true
                },{
                  xtype: 'textfield',
                  id: 'ClaimWaste',
                  name:'ClaimWaste',
                  readOnly:true
                }]
               },{
                columnWidth: 0.15,
                layout: 'form',
                padding:5,
                items:[{
                  xtype: 'label',
                  text: 'Reward'
                },{
                  xtype: 'textfield',
                  id: 'RewardMoisture',
                  name:'RewardMoisture',
                  readOnly:true
                },{
                  xtype: 'textfield',
                  id: 'RewardBeanCount',
                  name:'RewardBeanCount',
                  readOnly:true
                },{
                  xtype: 'textfield',
                  id: 'RewardMouldy',
                  name:'RewardMouldy',
                  readOnly:true
                },{
                  xtype: 'textfield',
                  id: 'RewardInsect',
                  name:'RewardInsect',
                  readOnly:true
                },{
                  xtype: 'textfield',
                  id: 'RewardSlaty',
                  name:'RewardSlaty',
                  readOnly:true
                },{
                  xtype: 'textfield',
                  id: 'RewardWaste',
                  name:'RewardWaste',
                  readOnly:true
                }]
               }]
            }]
         },{
            xtype: 'fieldset',
            title: 'Payment',
            fieldDefaults: {
               labelAlign: 'left',
               labelWidth: 600,
               anchor: '100%'
           },
            items: [{
               xtype: 'textfield',
               fieldLabel: 'Contract Price',
               id: 'ContractPrice',
               name: 'ContractPrice',
               listeners: {
                  change: function (cb, nv, ov) {
                     Ext.getCmp('NetPrice').setValue((100+parseFloat(Ext.getCmp('Reward').getValue()))/100*
                        parseFloat(Ext.getCmp('ContractPrice').getValue()))
                  }
               }
           },{
               xtype: 'textfield',
               fieldLabel: 'Reward(%)',
               id: 'Reward',
               name: 'Reward',
               listeners: {
                  change: function (cb, nv, ov) {
                     Ext.getCmp('NetPrice').setValue((100+parseFloat(Ext.getCmp('Reward').getValue()))/100*
                        parseFloat(Ext.getCmp('ContractPrice').getValue()))
                  }
               }
           },{
               xtype: 'textfield',
               fieldLabel: 'Net Price',
               id: 'NetPrice',
               name: 'NetPrice'
           },{
               xtype: 'textfield',
               fieldLabel: 'Volume(Kg)',
               id: 'VolumeNetto2',
               name: 'VolumeNetto2',
               listeners: {
                  change: function (cb, nv, ov) {
                     Ext.getCmp('TotalPayment').setValue(parseFloat(Ext.getCmp('NetPrice').getValue())*
                        parseFloat(Ext.getCmp('VolumeNetto2').getValue()))
                  }
               }
           },{
               xtype: 'textfield',
               fieldLabel: 'Total Payment',
               id: 'TotalPayment',
               name: 'TotalPayment'
           }]
         }],
        buttons: [{
            id:'cetakButton',
            text: 'Cetak',
               margin: '5px',
               scale: 'large',
            ui: 's-button',
            cls: 's-blue',
            handler: function() {
               preview_cetak_surat(m_cetak_kuitansi+Ext.getCmp('TransactionID').getValue());
            }
        },{
            id:'saveButton',
            text: 'Save',
               margin: '5px',
               scale: 'large',
            ui: 's-button',
            cls: 's-blue',
            handler: function() {
               fsave()
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
        title: 'Data Program',
        closable: true,
        modal:true,
        closeAction: 'show',
        width: '80%',
        height: '80%',
        layout: {
            type: 'fit'
        },
        items: [DataForm]
    });
    var grid = Ext.create('Ext.grid.Panel', {
        store: store,
        width: '100%',
        id:'grid',
        style: 'border:1px solid #CCC;',
        renderTo: 'ext-content',
        loadMask: true,
        selType: 'rowmodel',
       listeners : {
          itemdblclick: function(dv, record, item, index, e) {
            displayFormWindow();
            var sm = record;
            fset(sm)
          }
       },
        dockedItems: [{
            xtype: 'pagingtoolbar',
            store: store,   // same store GridPanel is using
            dock: 'bottom',
            displayInfo: true
        },{
            xtype: 'toolbar',
            items: [{
                icon: varjs.config.base_url + 'images/icons/new/add.png', cls:'Sfr_BtnGridGreen', overCls:'Sfr_BtnGridGreen-Hover',
                text: 'Add',
                scope: this,
                handler : displayFormWindow,
                cls : m_act_add
            },{
                icon: varjs.config.base_url+'images/icons/silk/pencil.png',
                text: 'Update',
                scope: this,
                handler : function(){
                  displayFormWindow();
                  var sm = Ext.getCmp('grid').getSelectionModel().getSelection()[0];
                  fset(sm)
                },
                cls : m_act_update
            },{
               itemId: 'remove',
               icon: varjs.config.base_url+'images/icons/silk/delete.png',
               cls:m_act_delete,
               text: 'Hapus',
               scope: this,
               handler : function(){
                 var smb = Ext.getCmp('grid').getSelectionModel().getSelection()[0];
                 Ext.MessageBox.confirm('Message', 'Apakah anda mau menghapus data ini ?' , function(btn){
                     if(btn == 'yes'){
                        Ext.Ajax.request({
                        waitMsg: 'Please Wait',
                        url: m_crud,
                        method : 'DELETE',
                        params: {id:  smb.raw.TransactionID},
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
            text: 'TransactionID',
            dataIndex: 'TransactionID',
             width:'10%'
        },{
            text: 'Company',
            dataIndex: 'CompanyAlias',
             width:'10%'
        },{
             text: 'Farmer',
             width: '20%',
             dataIndex: 'FarmerName'
         },{
             text: 'Volume(Kg)',
             width: '15%',
             dataIndex: 'VolumeNetto'
         },{
             text: 'Contract Price',
             width: '15%',
             dataIndex: 'ContractPrice'
         },{
             text: 'Net Price',
             width: '15%',
             dataIndex: 'NetPrice'
         },{
             text: 'Total Price',
             width: '15%',
             dataIndex: 'TotalPayment'
         }]
    });
});

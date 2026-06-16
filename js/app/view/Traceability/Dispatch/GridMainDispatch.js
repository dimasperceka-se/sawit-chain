
function callFormMain(sm){
    Ext.getCmp('Koltiva.view.Traceability.Dispatch.GridMainDispatch-gridMainGrid').destroy(); //destory current view
    if(Ext.getCmp('Koltiva.view.Traceability.Dispatch.FormMainDispatch') == undefined){ 
        var MainForm = Ext.create('Koltiva.view.Traceability.Dispatch.FormMainDispatch', {
            opsiDisplay: 'update',
            viewVar: {
                DespatchID: sm.get('DespatchID'),
                btnSave: 'view',
                ProductID : sm.get('ProductID')
            }
        });
    }else{
       //destroy, create ulang
       Ext.getCmp('Koltiva.view.Traceability.Dispatch.FormMainDispatch').destroy();
       var MainForm = Ext.create('Koltiva.view.Traceability.Dispatch.FormMainDispatch', {
            opsiDisplay: 'update',
            viewVar: {
                DespatchID: sm.get('DespatchID'),
                btnSave: 'view',
                ProductID : sm.get('ProductID')
            }
        });
    }
}

var contextMainGridMainDespatch = Ext.create('Ext.menu.Menu',{
items:[{
   icon: varjs.config.base_url + 'images/icons/new/view.png',
   text: lang('View'),
   cls:'Sfr_BtnConMenuWhite',
   handler: function() {
       var sm = Ext.getCmp('Koltiva.view.Traceability.Dispatch.GridMainDispatch-gridMainGrid').getSelectionModel().getSelection()[0];
       callFormMain(sm, 'view');
   }
},{
   icon: varjs.config.base_url + 'images/icons/new/update.png',
   text: lang('Update'),
   cls:'Sfr_BtnConMenuWhite',
   id : 'Koltiva.view.Traceability.Dispatch.GridMainDispatch-GridBtnEdit',
   handler: function() {
       var sm = Ext.getCmp('Koltiva.view.Traceability.Dispatch.GridMainDispatch-gridMainGrid').getSelectionModel().getSelection()[0];
       
       Ext.getCmp('Koltiva.view.Traceability.Dispatch.GridMainDispatch-gridMainGrid').destroy(); //destory current view
       if(Ext.getCmp('Koltiva.view.Traceability.Dispatch.FormMainDispatch') == undefined){
               var MainForm = Ext.create('Koltiva.view.Traceability.Dispatch.FormMainDispatch', {
                   opsiDisplay: 'update',
                   viewVar: {
                       DespatchID: sm.get('DespatchID'),
                       btnSave: 'edit',
                       btnPick: 'edit',
                       btnVehicle:'edit',
                       ProductID : sm.get('ProductID')
                   }
               });
           }else{
               //destroy, create ulang
               Ext.getCmp('Koltiva.view.Traceability.Dispatch.FormMainDispatch').destroy();
               var MainForm = Ext.create('Koltiva.view.Traceability.Dispatch.FormMainDispatch', {
                   opsiDisplay: 'update',
                   viewVar: {
                       DespatchID: sm.get('DespatchID'),
                       btnSave: 'edit',
                       btnPick: 'edit',
                       btnVehicle:'edit',
                       ProductID : sm.get('ProductID')
                   }
               });
           }
   }
},{
   icon: varjs.config.base_url + 'images/icons/new/delete.png',
   text: lang('Delete'),
   cls:'Sfr_BtnConMenuWhite',
   id : 'Koltiva.view.Traceability.Dispatch.GridMainDispatch-GridBtnDelete',
   handler: function(){
       var sm = Ext.getCmp('Koltiva.view.Traceability.Dispatch.GridMainDispatch-gridMainGrid').getSelectionModel().getSelection()[0];
      
       Ext.MessageBox.confirm('Message', 'Do you want to delete this data ?', function(btn) {
           if (btn == 'yes') {
               Ext.Ajax.request({
                   waitMsg: 'Please Wait',
                   url: m_api + '/dispatch/transaction/delete/'+ sm.get('DespatchID'),
                   method: 'DELETE',
                   success: function(response, opts) {
                       Ext.MessageBox.show({
                           title: 'Information',
                           msg: lang('Data deleted'),
                           buttons: Ext.MessageBox.OK,
                           animateTarget: 'mb9',
                           icon: 'ext-mb-success'
                       });

                       //refresh store
                       //setFilterLs();
                       Ext.getCmp('Koltiva.view.Traceability.Dispatch.GridMainDispatch-gridMainGrid').getStore().load();
                   },
                   failure: function(response, opts) {
                       var pesanNya;
                       if(o.result.message != undefined){
                           pesanNya = o.result.message;
                       }else{
                           pesanNya = lang('Connection error');
                       }
                       Ext.MessageBox.show({
                           title: 'Error',
                           msg: pesanNya,
                           buttons: Ext.MessageBox.OK,
                           animateTarget: 'mb9',
                           icon: 'ext-mb-error'
                       });
                   }
               });
           }
       });
   }
}]
});

Ext.define('Koltiva.view.Traceability.Dispatch.GridMainDispatch' ,{
extend: 'Ext.panel.Panel',
id: 'Koltiva.view.Traceability.Dispatch.GridMainDispatch', 
renderTo: 'ext-content', 
searchByIdOrName: function(field) {
       var key = field.getValue();
       Ext.getCmp('Koltiva.view.Traceability.Dispatch.GridMainDispatch-gridMainGrid').getStore().proxy.extraParams = {key:key};
       Ext.getCmp('Koltiva.view.Traceability.Dispatch.GridMainDispatch-gridMainGrid').getStore().loadPage(1);
},
style:'padding:0 15px 15px 15px;margin:5px 0 0 0;',
initComponent: function() {
   var thisObj = this;
   
    
   storeGridMainDespatch = Ext.create('Koltiva.store.Traceability.Dispatch.MainDispatch'); 
   //items
   thisObj.items = [ {
       xtype: 'grid',
       id: 'Koltiva.view.Traceability.Dispatch.GridMainDispatch-gridMainGrid',
       style: 'border:1px solid #CCC;margin-top:4px;',
       cls:'Sfr_GridNew',
       minHeight:300,
       loadMask: true,
       selType: 'rowmodel',
       store: storeGridMainDespatch,
       viewConfig: {
           deferEmptyText: false,
           emptyText: lang('No data Available'),
       },
       dockedItems: [{
           xtype: 'pagingtoolbar',
           id: 'Koltiva.view.Traceability.Dispatch.GridMainDispatch-gridToolbar',
           store: storeGridMainDespatch,
           dock: 'bottom',
           displayInfo: true
       },{
           xtype: 'toolbar',
           dock:'top',
           items: [{
               icon: varjs.config.base_url + 'images/icons/new/add.png',
               cls:'Sfr_BtnGridGreen',
               overCls:'Sfr_BtnGridGreen-Hover',
               text: lang('Add'),
               hidden: m_act_add,
               handler: function() {
                   Ext.getCmp('Koltiva.view.Traceability.Dispatch.GridMainDispatch').destroy(); //destory current view
                   if(Ext.getCmp('Koltiva.view.Traceability.Dispatch.FormMainDispatch') == undefined){
                       var FormMainReceipt = Ext.create('Koltiva.view.Traceability.Dispatch.FormMainDispatch', {
                           opsiDisplay: 'insert',
                           viewVar: {
                               GoodDespatchID: '',
                               btnSave: false
                           }
                       });
                   }else{
                       //destroy, create ulang
                       Ext.getCmp('Koltiva.view.Traceability.Dispatch.FormMainDispatch').destroy();
                       var FormMainReceipt = Ext.create('Koltiva.view.Traceability.Dispatch.FormMainDispatch', {
                           opsiDisplay: 'insert',
                           viewVar: {
                               GoodDespatchID: '',
                               btnSave: false
                           }
                       });
                   }
               }
           },{
                icon: varjs.config.base_url + 'images/icons/new/export.png', cls:'Sfr_BtnGridPaleBlue',
                text: lang('Export'),
                handler: function() {
                    Ext.MessageBox.show({
                        msg: 'Please wait...',
                        progressText: 'Exporting...',
                        width: 300,
                        wait: true,
                        waitConfig: {
                            interval: 200
                        },
                        icon: 'ext-mb-download', //custom class in msg-box.html
                        animateTarget: 'mb7'
                    });

                    try {
                        Ext.destroy(Ext.get('downloadIframe'));
                    } catch (e) {}

                    Ext.Ajax.request({
                        url: m_api + '/dispatch/transaction/export_dispatch',

                        method: 'GET',
                        waitMsg: lang('Please Wait'),
                        timeout: 360000,
                        success: function (data) {
                            Ext.MessageBox.hide();
                            var jsonResp = JSON.parse(data.responseText);
                            window.location = jsonResp.filenya;
                        },
                        failure: function () {
                            Ext.MessageBox.hide();
                            Ext.MessageBox.show({
                                title: 'Notifications',
                                msg: 'Failed to export, Please try again.',
                                buttons: Ext.MessageBox.OK,
                                animateTarget: 'mb9',
                                icon: 'ext-mb-error'
                            });
                        }
                    });
                }
            },{
               xtype:'tbspacer',
               flex:1
           },
           {
               name: 'key',
               id: 'Koltiva.view.Supplier.GridMainSupplier-textSearch',
               xtype: 'textfield',
               baseCls:'Sfr_TxtfieldSearchGrid',
               xtype: 'textfield',
               width: 400,
               emptyText: lang('Search by Processing Number')+', '+lang('Press \'Enter\' to search'),
               listeners: {
                   specialkey: function(field, event) {
                       if (event.getKey() == event.ENTER) {
                           thisObj.searchByIdOrName(field);
                       }
                   }
               }
           },{
               icon: varjs.config.base_url + 'images/icons/new/reload.png',
               cls:'Sfr_BtnGridBlue',
               overCls:'Sfr_BtnGridBlue-Hover',
               handler: function() { 
                   Ext.getCmp('Koltiva.view.Traceability.Dispatch.GridMainDispatch-gridMainGrid').getStore().loadPage(1);
               }
           },{
               xtype:'button',
               icon: varjs.config.base_url + 'images/icons/new/add-filter.png',
               text:lang('Add Filter'),
               cls:'Sfr_BtnGridPaleBlue',
               overCls:'Sfr_BtnGridPaleBlue-Hover',
               hidden: true,
               handler: function() {
                   //advanced search
                   var winAdvFilter = Ext.create('Koltiva.view.Despatch.WinAdvancedFilter');
                   if (!winAdvFilter.isVisible()) {
                       winAdvFilter.center();
                       winAdvFilter.show();
                   } else {
                       winAdvFilter.close();
                   }
               }
           }]
       }],
       columns: [{
           text: lang(''),
           xtype:'actioncolumn',
           width: '5%',
           items:[{
               icon: varjs.config.base_url + 'images/icons/new/action.png',
               tooltip: 'Action',
               handler: function(grid, rowIndex, colIndex, item, e, record) {
                   contextMainGridMainDespatch.showAt(e.getXY());
                    if(record.data.DestpatchStatusID == 5){
                        Ext.getCmp('Koltiva.view.Traceability.Dispatch.GridMainDispatch-GridBtnEdit').setVisible(false);
                        Ext.getCmp('Koltiva.view.Traceability.Dispatch.GridMainDispatch-GridBtnDelete').setVisible(false);
                    }else if(record.data.DestpatchStatusID == 4){
                        Ext.getCmp('Koltiva.view.Traceability.Dispatch.GridMainDispatch-GridBtnDelete').setVisible(false);
                        Ext.getCmp('Koltiva.view.Traceability.Dispatch.GridMainDispatch-GridBtnEdit').setVisible(true);
                    }else{
                        Ext.getCmp('Koltiva.view.Traceability.Dispatch.GridMainDispatch-GridBtnEdit').setVisible(true);
                        Ext.getCmp('Koltiva.view.Traceability.Dispatch.GridMainDispatch-GridBtnEdit').setVisible(true);
                    }
               }
           }]
       },{
           text: lang('ID'),
           dataIndex: 'DespatchID',
           hidden:true
       },{
           text: lang('Dispatch Number'),
           dataIndex: 'DespatchNumber',
           width: '15%',
       },{
           text: lang('Shipping Date'),
           dataIndex: 'ShippingDate', 
           width: '20%',
           renderer: Ext.util.Format.dateRenderer('Y-m-d')
       },{
           text: lang('Despatch Code'),
           width: '20%',
           dataIndex: 'DespatchCode'
       },{
           text: lang('Containers Total'),
           dataIndex: 'ContainerTotal',
           width: '20%',
       },{
           text: lang('Status'),
           dataIndex: 'DestpatchStatusName',
           width: '20%',
       }]
   }];

   this.callParent(arguments);
}
});
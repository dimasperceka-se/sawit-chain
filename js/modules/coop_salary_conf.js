Ext.onReady(function(){

  var storeRateSalary = Ext.create('Ext.data.Store', {
        extend: 'Ext.data.Model',
        fields: ['salaryRateID','StaffID','PersonID','FarmerID','StaffName','Position','StaffStatus','PeriodStart','PeriodEnd','BasicSalaryAmount'],
        autoLoad: true,
        pageSize: 50,
        proxy: {
            type: 'ajax',
            url: m_api+'/coop_payroll/rate_salary',
            reader: {
                type: 'json',
                root: 'data',
                totalProperty: 'total'
            }
        }
    });


    var store_staff = Ext.create('Ext.data.Store', {
        extend: 'Ext.data.Model',
        fields: ['StaffID','PersonID','FarmerID','StaffName','Position','StaffStatus'],
        autoLoad: true,
        pageSize: 50,
        proxy: {
            type: 'ajax',
            url: m_api+'/coop_payroll/staffs',
            reader: {
                type: 'json',
                root: 'data',
                totalProperty: 'total'
            }
        }
    });



var DataFormConf = Ext.create('Ext.form.Panel', {
        // frame: false,
        // autoScroll: true,
        bodyPadding: 5,
        id: 'dataForm',
        fieldDefaults: {
            labelAlign: 'left',
            labelWidth: 150,
            anchor: '100%'
        },
        items: [{
                  xtype: 'hiddenfield',
                  id: 'type',
                  name: 'type'
              },
              {
                xtype:'hiddenfield',
                id:'StaffID',
                name:'StaffID'
              },
              {
                xtype: 'fieldcontainer',
                fieldLabel: 'Staff',
                id: 'staff_container',
                layout: 'hbox',
                align: 'stretch',
                bodyStyle: 'padding: 10px',
                items: [
                    {
                        xtype: 'textfield',
                        id: 'StaffName',
                        name: 'StaffName',
                        emptyText:'Cari staff...',
                        readOnly: true,
                        listeners:{
                          focus: function(c){

                          }
                        }
                    },
                    {
                      iconCls: 'search',
                      cls: 's-grey',
                      xtype: 'button',
                      style:'margin-left:5px',
                      handler: function(){

                            var winStaff = Ext.create('widget.window', {
                                  title: 'Farmer Data',
                                  closable: true,
                                  id: 'winFarmer',
                                  modal: true,
                                  width: 800,
                                  minWidth: 350,
                                  height: 500,
                                  layout: {
                                      type: 'fit'
                                  },
                                  items: [{
                                          xtype: 'gridpanel',
                                          id: 'grid_farmer',
                                          store: store_staff,
                                          style: 'border:1px solid #CCC;',
                                          width: '100%',
                                          minHeight: 350,
                                          loadMask: true,
                                          selType: 'rowmodel',
                                          dockedItems: [{
                                                  xtype: 'pagingtoolbar',
                                                  store: store_staff, // same store GridPanel is using
                                                  dock: 'bottom',
                                                  displayInfo: true
                                              }, {
                                                  xtype: 'toolbar',
                                                  items: [{
                                                  xtype: 'textfield',
                                                  fieldLabel: lang('Nama'),
                                                  name: 'farmerKey',
                                                  id: 'farmerKey'
                                                },{
                                                    xtype: 'button',
                                                    margin: '0px 0px 0px 6px',
                                                    text: 'Search',
                                                    handler: function() {
                                                      store_staff.getProxy().extraParams = {
                                                        key: Ext.getCmp('farmerKey').getValue(),
                                                      };
                                                      store_staff.load({params:{start:0}});
                                                    }
                                                }]
                                                    }],
                                                    columns: [{
                                                        text: 'No',
                                                        xtype: 'rownumberer',
                                                        align: 'center',
                                                        width: '5%'
                                                    }, {
                                                        text: 'StaffID',
                                                        width: '15%',
                                                        dataIndex: 'StaffID'
                                                    }, {
                                                        text: 'Staff Name',
                                                        width: '25%',
                                                        dataIndex: 'StaffName'
                                                    },{
                                                      text: lang('Position'),
                                                      width: '15%',
                                                      dataIndex: 'Position'
                                                    }, {
                                                        text: 'StaffStatus',
                                                        width: '15%',
                                                        dataIndex: 'StaffStatus'
                                                    }, {
                                                        menuDisabled: true,
                                                        sortable: false,
                                                        xtype: 'actioncolumn',
                                                        width: '7%',
                                                        align: 'center',
                                                        items: [
                                                            {
                                                                icon: varjs.config.base_url + 'images/icons/silk/page_white_edit.png',
                                                                tooltip: lang('Pilih'),
                                                                handler: function(grid, rowIndex, colIndex) {
                                                                    var rec = grid.getStore().getAt(rowIndex);

                                                                    Ext.getCmp('StaffID').setValue(rec.data.StaffID);
                                                                    Ext.getCmp('StaffName').setValue(rec.data.StaffName);

                                                                    winStaff.hide();

                                                                }
                                                            }]
                                                    }]
                                            }],
                                            buttons: [{
                                                text: 'Close',
                                                margin: '5px',
                                                scale: 'large',
                                                ui: 's-button',
                                                cls: 's-grey',
                                                disabled: false,
                                                handler: function() {
                                                    winStaff.close();
                                                }
                                            }]
                                        }).show();

                            store_staff.load({
                                params: {
                                    key: Ext.getCmp('farmerKey').getValue()
                                }
                            });
                      }
                    }
                ]
              },
              {
                  xtype: 'numericfield',
                  anchor:'70%',
                  hideTrigger:true,
                  fieldLabel:'Gaji Pokok',
                  name: 'BasicSalaryAmount'
              }, {
                  xtype: 'datefield',
                  anchor:'60%',
                  format: 'd/m/Y',
                  fieldLabel:'Tanggal Mulai Berlaku',
                  name: 'PeriodStart'
              }, {
                  xtype: 'datefield',
                  anchor:'60%',
                  format: 'd/m/Y',
                  fieldLabel:'Tanggal Mulai Berlaku',
                  name: 'PeriodEnd'
              }
        ],
        buttons: [
            {
                id: 'saveButton',
                text: lang('Save'),
                margin: '5px',
                scale: 'large',
                ui: 's-button',
                cls: 's-blue',
                handler: function() {
                    var form = Ext.getCmp('dataForm').getForm();
                    form.submit({
                        url: m_api+'/coop_payroll/rate_salary',
                        waitMsg: lang('Saving....'),
                        success: function(form, action) {
                                storeRateSalary.load();
                                Ext.Msg.alert('Success', action.result.message);
                                win.hide();
                        },
                        failure: function(form, action) {
                           Ext.Msg.alert('Failed', action.result.message);
                        }
                    });
                }
            },
            {
                text: lang('Close'),
                margin: '5px',
                scale: 'large',
                ui: 's-button',
                cls: 's-grey',
                disabled: false,
                handler: function() {
                    win.hide();
                }
            }
        ]
});

var win = Ext.create('widget.window', {
        title: 'Pengaturan Gaji Pokok',
        id: 'win',
        closable: true,
        modal: true,
        closeAction: 'show',
        width: 500,
        // height: 400,
        // autoWidth:true,
        autoHeight:true,
        layout: {
            type: 'fit'
        },
        items: [DataFormConf]
});


function displayFormWindow(){
    if(!win.isVisible()){
        // DataFormsalaryRate.getForm().reset();
        win.show();
    } else {
        win.hide(this, function() {});
        win.toFront();
    }
}


//GRID salaryRate
var gridRateSalary = Ext.create('Ext.grid.Panel', {
        store: storeRateSalary,
        renderTo: 'ext-content',
        width: '100%',
        title:'Pengaturan Rate Gaji Staff',
        minHeight: 450,
        id:'gridRateSalary',
        // style: 'border:1px solid #CCC;',
        loadMask: true,
        selType: 'rowmodel',
        listeners : {
            itemdblclick: function(dv, record, item, index, e) {
                displayFormWindow();
                var sm = record;
                set_data_salaryRate(sm);
            }
        },
        dockedItems: [{
            xtype: 'pagingtoolbar',
            store: storeRateSalary,   
            dock: 'bottom',
            displayInfo: true
        },{
            xtype: 'toolbar',
            items: [{
                icon: varjs.config.base_url + 'images/icons/new/add.png', cls:'Sfr_BtnGridGreen', overCls:'Sfr_BtnGridGreen-Hover',
                text: lang('Tambah Data'),
                scope: this,
                handler : function(){
                  displayFormWindow();
                  // DataFormsalaryRate.getForm().reset();
                },
                cls : m_act_add
            },{
                icon: varjs.config.base_url+'images/icons/silk/pencil.png',
                text: lang('Update'),
                scope: this,
                hidden:true,
                handler : function(){
                  displayFormWindow();
                  var sm = Ext.getCmp('gridRateSalary').getSelectionModel().getSelection()[0];
                  set_data_salaryRate(sm)
                   Ext.getCmp('ToolbarDetailsalaryRate').show();
                },
                cls : m_act_update
            },{
                icon: varjs.config.base_url+'images/icons/silk/pencil.png',
                text: lang('Retur'),
                hidden:true,
                scope: this,
                handler : function(){
                  var sm = Ext.getCmp('grid').getSelectionModel().getSelection()[0];
                  link(m_retur+sm.get('salaryRateID'));
                },
                cls : m_act_update
            },{
               itemId: 'remove',
               hidden:true,
               icon: varjs.config.base_url+'images/icons/silk/delete.png',
               cls:m_act_delete,
               text: lang('Hapus'),
               scope: this,
               handler : function(){
                 var smb = Ext.getCmp('gridRateSalary').getSelectionModel().getSelection()[0];
                 Ext.MessageBox.confirm('Message', lang('Apakah anda mau menghapus data ini ?') , function(btn){
                  // console.log(smb.data)
                     if(btn == 'yes'){
                        Ext.Ajax.request({
                        waitMsg: lang('Please Wait'),
                        url: m_crud,
                        method : 'DELETE',
                        params: {id:  smb.data.salaryRateID},
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
            },'->',{
              xtype: 'datefield',
              name: 'searchAwal',
              width:100,
              hidden:true,
              id: 'searchAwal',
              format: 'Y-m-d',
              emptyText: lang('Awal'),
          }, {
              xtype: 'label',
              hidden:true,
              text: ' s.d '
          },{
              width:100,
              hidden:true,
              xtype: 'datefield',
              name: 'searchAkhir',
              id: 'searchAkhir',
              format: 'Y-m-d',
              emptyText: lang('Akhir'),
          },{
              xtype:'textfield',
              id:'staffNameSearch',
              hideLabel:true,
              emptyText:'Cari staff...',
              // fieldLabel:'Nama Staff',
              name:'staffName'
          }, {
              xtype: 'button',
              margin: '0px 5px 0px 6px',
              text: 'Search',
              handler: function () {
                  storeRateSalary.load({
                      params: {
                          staffName: Ext.getCmp('staffNameSearch').getValue(),
                          Awal: Ext.getCmp('searchAwal').getSubmitValue(),
                          Akhir: Ext.getCmp('searchAkhir').getSubmitValue(),
                          // OrgType: Ext.getCmp('searchOrgType').getValue(),
                          // OrgID: Ext.getCmp('searchOrgID').getValue(),
                      }
                  });
              }
          }]
        }],        
        columns: [
        {
          text:'salaryRateID',
          dataIndex:'salaryRateID',
          hidden:true,
        },             
        {
            text: lang('No'),
            xtype: 'rownumberer',
            width:'5%'
        },{
            text: lang('Nama Staff'),
            width: '15%',
            dataIndex: 'StaffName'
        },{
            text: lang('Posisi'),
            width: '15%',
            dataIndex: 'Position'
        },{
            text: lang('Status'),
            width: '10%',
            dataIndex: 'StaffStatus'
        },{
            text: lang('Gaji Pokok'),
            flex:2,
            width: '20%',
            align:'right',
            xtype:'numbercolumn',
            format:'0,000.00',
            dataIndex: 'BasicSalaryAmount'
        },{
            text: lang('Tanggal Mulai'),
            width: '13%',
            dataIndex: 'PeriodStart'
        },{
            text: lang('Tanggal Akhir'),
            width: '13%',
            dataIndex: 'PeriodEnd'
        }]
    });


////END GRID salaryRate




});
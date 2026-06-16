Ext.Loader.setConfig({enabled: true});
Ext.Loader.setPath('js/ext-4.2.0.663', '../ux');
//Ext.Loader.setPath('js/ext-4.2.0.663/ux/form');
Ext.require([
    'Ext.ux.RowExpander',
    'Ext.selection.CheckboxModel'
]);

Ext.onReady(function(){

var storePayroll = Ext.create('Ext.data.Store', {
      extend: 'Ext.data.Model',
      fields: ['StaffID','BasicSalaryAmount','totalWorkTime','totalOvertime','numDays','workTimeAmount','overTimeAmount','takeHomePay','StaffName'],
      // autoLoad: true,
      pageSize: 50,
      proxy: {
          type: 'ajax',
          url: m_api+'/coop_payroll/generate_payroll',
          reader: {
              type: 'json',
              root: 'data',
              totalProperty: 'total'
          }
      }
  });



//GRID Payroll
 var sm = Ext.create('Ext.selection.CheckboxModel');

var gridPayroll = Ext.create('Ext.grid.Panel', {
        store: storePayroll,
        renderTo: 'ext-content',
        width: '100%',
        title:'Payroll',
        minHeight: 450,
        id:'gridPayroll',
        // style: 'border:1px solid #CCC;',
        loadMask: true,
        selType: 'rowmodel',
        selModel: sm,
        listeners : {
            itemdblclick: function(dv, record, item, index, e) {
                // displayFormWindow();
                // var sm = record;
                // set_data_Payroll(sm);
            }
        },
        dockedItems: [{
              xtype: 'pagingtoolbar',
              store: storePayroll,   
              dock: 'bottom',
              displayInfo: true
          },
          {
            xtype: 'toolbar',
            dock: 'bottom',
            items: [
                    {
                      icon: varjs.config.base_url+'images/icons/silk/disk.png',
                      text: lang('Save Generated Payroll'),
                      scope: this,
                      handler : function(){
                          // var grid = Ext.ComponentQuery.query('gridPayroll')[0];
                          var grid = Ext.getCmp('gridPayroll');
                          // var selectedRecord = grid.getSelectionModel().getSelection()[0];
                          var data = grid.getSelectionModel().getSelection();
                          if (data.length == 0) {
                              Ext.Msg.alert('Failure', 'Please select the data...');
                          } else {
                                var sm = grid.getSelectionModel();
                                selected = [];
                                Ext.each(sm.getSelection(), function(item) {
                                    selected.push(item.data);
                                });
                                Ext.Ajax.request({
                                    url: m_api+'/coop_payroll/save_generated_payroll',
                                    method: 'POST',
                                    params: {
                                        postdata: Ext.encode(selected),
                                        searchAwal: Ext.getCmp('searchAwal').getSubmitValue(),
                                        searchAkhir: Ext.getCmp('searchAkhir').getSubmitValue()
                                    },
                                    success: function(fp, o) {
                                        Ext.MessageBox.alert('Success', 'Data saved.');
                                        storePayroll.removeAll();
                                    }
                                });
                          }
                    }
                }
            ]
          },
          {
            xtype: 'toolbar',
            items: [
             {
                xtype: 'label',
                text: 'Choose Payroll Period'
             },{
                xtype: 'datefield',
                name: 'searchAwal',
                width:120,
                id: 'searchAwal',
                format: 'd-m-Y',
                emptyText: lang('Start Periode'),
             }, {
                xtype: 'label',
                text: ' to '
             },{
                width:120,
                xtype: 'datefield',
                name: 'searchAkhir',
                id: 'searchAkhir',
                format: 'd-m-Y',
                emptyText: lang('End Periode'),
             },
            {
                icon: varjs.config.base_url + 'images/icons/new/add.png', cls:'Sfr_BtnGridGreen', overCls:'Sfr_BtnGridGreen-Hover',
                text: lang('Generate Payroll'),
                scope: this,
                handler : function(){

                  storePayroll.load({
                      params: {
                          searchAwal: Ext.getCmp('searchAwal').getSubmitValue(),
                          searchAkhir: Ext.getCmp('searchAkhir').getSubmitValue()
                      }
                  });

                  // var griditem = Ext.encode(Ext.pluck(store_time_staff_items.data.items, 'data'));

                  //  Ext.Ajax.request({
                  //       // waitMsg: lang('Please Wait'),
                  //       url: m_api+'coop_payroll/get_idtimesheet',
                  //       method : 'GET',
                  //       success: function(response, opts){
                  //          var obj = Ext.decode(response.responseText);
                  //          Ext.getCmp('TimesheetID').setValue(obj.TimesheetID);
                  //       },
                  //       failure: function(response, opts){
                  //          var obj = Ext.decode(response.responseText);
                  //          // Ext.MessageBox.alert('error',lang('Could not connect to the database. Retry later'));
                  //       }
                  //    });
                  // DataFormPayroll.getForm().reset();
                },
                cls : m_act_add
            },{
                icon: varjs.config.base_url+'images/icons/silk/pencil.png',
                text: lang('Update'),
                scope: this,
                hidden:true,
                handler : function(){
                  displayFormWindow();
                  var sm = Ext.getCmp('gridPayroll').getSelectionModel().getSelection()[0];
                  set_data_Payroll(sm)
                   Ext.getCmp('ToolbarDetailPayroll').show();
                },
                cls : m_act_update
            },{
                icon: varjs.config.base_url+'images/icons/silk/pencil.png',
                text: lang('Retur'),
                hidden:true,
                scope: this,
                handler : function(){
                  var sm = Ext.getCmp('grid').getSelectionModel().getSelection()[0];
                  link(m_retur+sm.get('PayrollID'));
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
                 var smb = Ext.getCmp('gridPayroll').getSelectionModel().getSelection()[0];
                 Ext.MessageBox.confirm('Message', lang('Apakah anda mau menghapus data ini ?') , function(btn){
                  // console.log(smb.data)
                     if(btn == 'yes'){
                        Ext.Ajax.request({
                        waitMsg: lang('Please Wait'),
                        url: m_crud,
                        method : 'DELETE',
                        params: {id:  smb.data.PayrollID},
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
              xtype:'textfield',
              id:'staffNameSearch',
              hideLabel:true,
              emptyText:'Search staff...',
              // fieldLabel:'Nama Staff',
              name:'staffName'
          }, {
              xtype: 'button',
              hidden:true,
              margin: '0px 5px 0px 6px',
              text: 'Search',
              handler: function () {
                  storePayroll.load({
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
        // {
        //     text: lang('No'),
        //     xtype: 'rownumberer',
        //     width:'5%'
        // },
        {
          text:'StaffID',
          dataIndex:'StaffID'
        }, {
            text: lang('Staff Name'),
            width: '15%',
            flex:1,
            dataIndex: 'StaffName'
        },{
            text: lang('Basic Sallary'),
            width: '10%',
            align:'right',
            xtype:'numbercolumn',
            format:'0,000',
            dataIndex: 'BasicSalaryAmount'
        },{
            text: lang('Total Work Time'),
            width: '10%',
            align:'right',
            xtype:'numbercolumn',
            format:'0,000',
            dataIndex: 'totalWorkTime'
        },{
            text: lang('Total Overtime'),
            width: '10%',
            align:'right',
            xtype:'numbercolumn',
            format:'0,000',
            dataIndex: 'totalOvertime'
        },{
            text: lang('Work Amount'),
            width: '10%',
            align:'right',
            xtype:'numbercolumn',
            format:'0,000',
            dataIndex: 'workTimeAmount'
        },{
            text: lang('Overtime Amount'),
            width: '10%',
            align:'right',
            xtype:'numbercolumn',
            format:'0,000',
            dataIndex: 'overTimeAmount'
        },{
            text: lang('Take Home Pay'),
            width: '10%',
            align:'right',
            xtype:'numbercolumn',
            format:'0,000',
            dataIndex: 'takeHomePay'
        }

        ]
    });


////END GRID Payroll




});

function formatDate(d)
 {
  date = new Date(d)
  var dd = date.getDate(); 
  var mm = date.getMonth()+1;
  var yyyy = date.getFullYear(); 
  if(dd<10){dd='0'+dd} 
  if(mm<10){mm='0'+mm};
  return d = dd+'/'+mm+'/'+yyyy
}

function parseDate(str) {
    var mdy = str.split('/');
    return new Date(mdy[2], mdy[1]-1, mdy[0]);
}

function daydiff(first, second) {
    return Math.round((second-first)/(1000*60*60*24));
}
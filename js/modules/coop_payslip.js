Ext.Loader.setConfig({enabled: true});
Ext.Loader.setPath('js/ext-4.2.0.663', '../ux');
//Ext.Loader.setPath('js/ext-4.2.0.663/ux/form');
Ext.require([
    'Ext.ux.RowExpander',
    'Ext.selection.CheckboxModel'
]);

Ext.onReady(function(){

var storePayslip = Ext.create('Ext.data.Store', {
      extend: 'Ext.data.Model',
      fields: ['StaffPayrollID','StaffID','DateCreated','StartDate','EndDate','WorkHours','OvertimeHours','BasicSalaryAmount','workTimeAmount','OvertimeAmount','TakeHomePayAmount','DateCreated','StaffName','Position','Status'],
      // autoLoad: true,
      pageSize: 50,
      proxy: {
          type: 'ajax',
          url: m_api+'/coop_payroll/payslip',
          reader: {
              type: 'json',
              root: 'data',
              totalProperty: 'total'
          }
      }
  });



//GRID Payslip
 var sm = Ext.create('Ext.selection.CheckboxModel');

var gridPayslip = Ext.create('Ext.grid.Panel', {
        store: storePayslip,
        renderTo: 'ext-content',
        width: '100%',
        title:'Payslip',
        minHeight: 450,
        id:'gridPayslip',
        // style: 'border:1px solid #CCC;',
        loadMask: true,
        selType: 'rowmodel',
        selModel: sm,
        listeners : {
            itemdblclick: function(dv, record, item, index, e) {
                // displayFormWindow();
                // var sm = record;
                // set_data_Payslip(sm);
            }
        },
        dockedItems: [{
              xtype: 'pagingtoolbar',
              store: storePayslip,   
              dock: 'bottom',
              displayInfo: true
          },
          {
            xtype: 'toolbar',
            items: [
             {
                xtype: 'label',
                text: 'Period'
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
                icon: varjs.config.base_url+'images/icons/silk/find.png',
                text: lang('Go'),
                scope: this,
                handler : function(){

                  storePayslip.load({
                      params: {
                          searchAwal: Ext.getCmp('searchAwal').getSubmitValue(),
                          searchAkhir: Ext.getCmp('searchAkhir').getSubmitValue()
                      }
                  });
                },
                cls : m_act_add
            },
            {
                icon: varjs.config.base_url+'images/icons/silk/printer.png',
                text: lang('Print Payslip'),
                scope: this,
                handler : function(){

                 var grid = Ext.getCmp('gridPayslip');
                    var selectedRecord = grid.getSelectionModel().getSelection()[0];
                    var data = grid.getSelectionModel().getSelection();
                    if (data.length == 0) {
                        Ext.Msg.alert('Failure', 'Please select the data...');
                    } else {
                      // alert(selectedRecord.data.StaffPayrollID);
                      var win = window.open(m_api+'coop_payroll/print_payslip/?StaffPayrollID='+selectedRecord.data.StaffPayrollID, '_blank');
                      win.focus();
                    }
                },
                cls : m_act_add
            },{
                icon: varjs.config.base_url+'images/icons/silk/pencil.png',
                text: lang('Update'),
                scope: this,
                hidden:true,
                handler : function(){
                  displayFormWindow();
                  var sm = Ext.getCmp('gridPayslip').getSelectionModel().getSelection()[0];
                  set_data_Payslip(sm)
                   Ext.getCmp('ToolbarDetailPayslip').show();
                },
                cls : m_act_update
            },{
                icon: varjs.config.base_url+'images/icons/silk/pencil.png',
                text: lang('Retur'),
                hidden:true,
                scope: this,
                handler : function(){
                  var sm = Ext.getCmp('grid').getSelectionModel().getSelection()[0];
                  link(m_retur+sm.get('PayslipID'));
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
                 var smb = Ext.getCmp('gridPayslip').getSelectionModel().getSelection()[0];
                 Ext.MessageBox.confirm('Message', lang('Apakah anda mau menghapus data ini ?') , function(btn){
                  // console.log(smb.data)
                     if(btn == 'yes'){
                        Ext.Ajax.request({
                        waitMsg: lang('Please Wait'),
                        url: m_crud,
                        method : 'DELETE',
                        params: {id:  smb.data.PayslipID},
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
                  storePayslip.load({
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
          text:'StaffID',
          dataIndex:'StaffID'
        }, {
            text: lang('Staff Name'),
            minWidth: 200,
            flex:1,
            dataIndex: 'StaffName'
        },{
            text: lang('Position'),
            width: 150,
            dataIndex: 'Position'
        },{
            text: lang('Basic Sallary'),
            width: 150,
            align:'right',
            xtype:'numbercolumn',
            format:'0,000',
            dataIndex: 'BasicSalaryAmount'
        },{
            text: lang('Total Work Time'),
            width: 150,
            align:'right',
            xtype:'numbercolumn',
            format:'0,000',
            dataIndex: 'WorkHours'
        },{
            text: lang('Total Overtime'),
            width: 150,
            align:'right',
            xtype:'numbercolumn',
            format:'0,000',
            dataIndex: 'OvertimeHours'
        },{
            text: lang('Work Amount'),
            width: 150,
            align:'right',
            xtype:'numbercolumn',
            format:'0,000',
            dataIndex: 'workTimeAmount'
        },{
            text: lang('Overtime Amount'),
            width: 150,
            align:'right',
            xtype:'numbercolumn',
            format:'0,000',
            dataIndex: 'OvertimeAmount'
        },{
            text: lang('Take Home Pay'),
            width: 150,
            align:'right',
            xtype:'numbercolumn',
            format:'0,000',
            dataIndex: 'TakeHomePayAmount'
        },
        {
            text: lang('Payroll Date'),
            width: 100,
            dataIndex: 'DateCreated'
        }

        ]
    });


////END GRID Payslip




});

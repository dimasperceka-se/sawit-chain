Ext.onReady(function(){

var storeTimesheets = Ext.create('Ext.data.Store', {
      extend: 'Ext.data.Model',
      fields: ['TimesheetsID','StaffID','PersonID','FarmerID','StaffName','Position','StaffStatus','PeriodStart','PeriodEnd','BasicSalaryAmount'],
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

/////
var store_staff = Ext.create('Ext.data.Store', {
    extend: 'Ext.data.Model',
    fields: ['StaffID','PersonID','FarmerID','StaffName','Position','StaffStatus'],
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

var gridStaff = Ext.create('Ext.grid.Panel', {
        itemId: 'markGrid',
        store: store_staff,
        loadMask: true,
        width: 400,
        columns: [
          {
              text: 'Pilih',
              width: 65,
              xtype: 'actioncolumn',
              tooltip: 'Select',
              align: 'center',
              icon: varjs.config.base_url + '/images/icons/silk/add.png',
              handler: function(grid, rowIndex, colIndex, actionItem, event, selectedRecord, row) {
                  // console.log(Ext.getCmp('typeCustomerSales').getValue().typeCustomerSalesRb);
                      Ext.getCmp('StaffID_TimeStaff').setValue(selectedRecord.data.StaffID);
                      Ext.getCmp('StaffName').setValue(selectedRecord.data.StaffName);

                      wStaffPopup.hide();
              }
          },
          { text: 'StaffID', dataIndex: 'StaffID', hidden: true },
          { text: 'Nama', flex:1, minWidth:200, flex:1, dataIndex: 'StaffName' },
          { text: 'Position', width: 200, dataIndex: 'Position' }        
        ],
        dockedItems: [
        {
               xtype: 'toolbar',
               items: [
                    {
                        xtype:'textfield',
                        margin:'0px 0px 0px 115px',
                        id:'CariNamaStaff',
                        fieldLabel:'Cari Nama',
                        listeners: {
                          specialkey: function(f,e){
                            if (e.getKey() == e.ENTER) {
                                // console.log()
                                 store_staff.load(
                                    {
                                        params: {
                                            StaffName: this.value
                                        }
                                    }
                                 );
                            }
                          }
                        },
                        handler:function()
                        {
                          
                        }
                    },
                    {
                        xtype:'button',
                        text:'Cari'
                    },'->',
                    {
                        xtype:'button',
                        hidden:true,
                        text:'Tambah Pelanggan'
                    }]
        }
    ]
});

  var wStaffPopup = Ext.create('widget.window', {
      // id: 'wStaffPopup',
      title: 'Pilih Staff',
      closable: true,
      closeAction: 'hide',
  //    autoWidth: true,
       width: 970,
      modal:true,
      height: 430,
      layout: 'fit',
      border: false,
      items: [gridStaff]
  });
////


var storeTimesheetStaffList = Ext.create('Ext.data.Store', {
    extend: 'Ext.data.Model',
    fields: ['TimesheetID','StaffID','StaffName','totalWorkHour','totalOvertime'],
    autoLoad: true,
    pageSize: 50,
    proxy: {
        type: 'ajax',
        url: m_api+'/coop_payroll/timesheet_staff_list',
        reader: {
            type: 'json',
            root: 'data',
            totalProperty: 'total'
        }
    }
});



Ext.define('timesheetstaffitems.Model', {
    extend: 'Ext.data.Model',
    fields: ['StaffTimesheetID','WorkDate',{name:'WorkHours',type:'int'},{name:'OvertimeHours',type:'int'}], 
});

var store_time_staff_items = Ext.create('Ext.data.Store', {
    model: 'timesheetstaffitems.Model',
    autoLoad: false,
    pageSize: 10,
    proxy: {
        type: 'ajax',
        url: m_api + 'coop_payroll/time_staff_items',
        reader: {
            type: 'json',
            root: 'data',
            totalProperty: 'total'
        }
    }
});

var RowEditing = Ext.create('Ext.grid.plugin.RowEditing', {
    id: 'RowEditing',
    clicksToMoveEditor: 0,
    autoCancel: false,
    errorSummary: false,
    clicksToEdit: 2
});

var DataFormTimesheetsStaff = Ext.create('Ext.form.Panel', {
        frame: false,
        height: 330,
        width: '100%',
        bodyPadding: 5,
        autoScroll:true,
        id:'DataFormTimesheetsStaff',
        fieldDefaults: {
            labelAlign: 'left',
            labelWidth: 100,
            anchor: '100%'
        },
        items: [{
            layout: 'hbox',
            items: [{
                    items: [
                        {
                          xtype:'hiddenfield',
                          name:'StaffID',
                          id:'StaffID_TimeStaff'
                        },
                        {
                            // 
                            xtype:'textfield',
                            labelWidth:100,
                            width:400,
                            id:'StaffName',
                            name:'StaffName',
                            fieldLabel:'Pilih Staff',
                               listeners: {
                                   render: function(component) {
                                       component.getEl().on('click', function(event, el) {
                                            wStaffPopup.show();
                                            store_staff.load();
                                       });
                                   }
                               }
                        }
                    ]
                },
                {
                    margin: '0px 0px 0px 20px',
                    items: [
                        
                         
                    ]
                }
            ]
        },{
           xtype: 'gridpanel',
           id: 'gridTimeStaffItems',
           features: [{
               ftype: 'summary'
           }],
           store: store_time_staff_items,
           width: '100%',
           height:'100%',
           loadMask: true,
           selType: 'rowmodel',
           viewConfig: {
               markDirty: false
           },
           dockedItems: [],
           columns: [{
               text: lang('No'),
               xtype: 'rownumberer',
               width: '5%'
           },
            {
               dataIndex: 'WorkDate',
               flex:1,
               minWidth:200,
               text:'Tanggal Kerja'
           },{
               text: lang('Jumlah Jam Kerja'),
               dataIndex: 'WorkHours',
               width: 160,
               align:'right',
               editor: {
                   xtype: 'numberfield',
                   id:'WorkHours',
                   allowBlank: false,
                   listeners: {
                       change: function (cb, nv, ov) {
                           // Ext.getCmp('total').setValue(this.value*Ext.getCmp('harga').getValue())
                           // calc();
                       }
                   }
               }
           }, {
               text: lang('Jumlah Jam Lembur'),
               hidden:true,
               dataIndex: 'OvertimeHours',
               align:'right',
               width: 160,
               style: 'text-align: right',
               summaryType: 'sum',
               summaryRenderer: function(value, summaryData, dataIndex) {
                  var total = value;
                  Ext.getCmp('Overtime').setValue(total);
               },
               editor: {
                   xtype: 'numberfield',
                   allowBlank: false,
                   id:'total',
               }
           }],
           plugins: [RowEditing],
           listeners: {
               'canceledit': function (editor, e, eOpts) {
                   store_detail.load({
                       params: {
                           // id: Ext.getCmp('TimesheetsID').getValue()
                       }
                   });
               },
               'edit': function (editor, e) {
               }
           }
        }
        ],
        buttons: [{
            id:'saveButton',
            text: lang('Simpan'),
            margin: '5px',
            scale: 'large',
            ui: 's-button',
            cls: 's-blue',
            handler: function() {
                var griditem = Ext.encode(Ext.pluck(store_time_staff_items.data.items, 'data'));
                var form = Ext.getCmp('DataFormTimesheetsStaff').getForm();

                form.submit({
                    url: m_api+'/coop_payroll/timesheet_staff',
                    method:'POST',
                    params: {
                            TimesheetID:Ext.getCmp('TimesheetID').getValue(),
                            griditem: griditem,
                            startDate:Ext.getCmp('startDate').getValue(),
                            endDate:Ext.getCmp('endDate').getValue()
                    },
                    waitMsg: 'Sending data...',
                    success: function(fp, o) {
                        // Ext.MessageBox.alert('Success', 'Data saved.');
                        storeTimesheetStaffList.load({
                                  params: {
                                      TimesheetID:Ext.getCmp('TimesheetID').getValue()
                                  }
                        });
                        winTimesheetsStaff.hide();
                    }
                });

             
                
            }
        },{
            text: lang('Cancel'),
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

var winTimesheetsStaff = Ext.create('widget.window', {
        title: 'Form Jam Kerja',
        id: 'winTimesheetsStaff',
        closable: true,
        modal: true,
        closeAction: 'show',
        width: 500,
        // height: 500,
        height:'90%',
        // autoWidth:true,
        // autoHeight:true,
        layout: {
            type: 'fit'
        },
        items: [DataFormTimesheetsStaff]
});

function displayFormWindowTimesheetStaff(){
    if(!winTimesheetsStaff.isVisible()){
        // DataFormTimesheets.getForm().reset();
        winTimesheetsStaff.show();
    } else {
        winTimesheetsStaff.hide(this, function() {});
        winTimesheetsStaff.toFront();
    }
}

/////////////////////////////////////////

var DataFormTimesheets  = Ext.create('Ext.form.Panel', {
        frame: false,
        height: 330,
        width: '100%',
        bodyPadding: 5,
        autoScroll:true,
        id:'DataFormTimesheets',
        fieldDefaults: {
            labelAlign: 'left',
            labelWidth: 100,
            anchor: '100%'
        },
        items: [{
            layout: 'hbox',
            items: [{
                    items: [
                        {
                          xtype:'hiddenfield',
                          name:'TimesheetID',
                          id:'TimesheetID'
                        },
                        {
                            xtype:'datefield',
                            format: 'd/m/Y',
                            labelWidth:150,
                            name:'startDate',
                            id:'startDate',
                            fieldLabel:'Periode Awal'       
                        },
                        {
                            xtype:'datefield',
                            format: 'd/m/Y',
                            labelWidth:150,
                            name:'endDate',
                            id:'endDate',
                            fieldLabel:'Periode Akhir'       
                        }
                    ]
                },
                {
                    margin: '0px 0px 0px 20px',
                    items: [
                        
                         {
                            // 
                            xtype:'textarea',
                            labelWidth:100,
                            id:'Remarks',
                            name:'Remarks',
                            fieldLabel:'Keterangan'
                        }
                    ]
                }
            ]
        },{
           xtype: 'gridpanel',
           // id: 'gridTimesheetStaff',
           features: [{
               ftype: 'summary'
           }],
           store: storeTimesheetStaffList,
           width: '100%',
           height:260,
           loadMask: true,
           selType: 'rowmodel',
           viewConfig: {
               markDirty: false
           },
           dockedItems: [
              {
               xtype: 'toolbar',
               id:'ToolbarDetailpurchase',
               items: [{
                        icon: varjs.config.base_url + 'images/icons/new/add.png', cls:'Sfr_BtnGridGreen', overCls:'Sfr_BtnGridGreen-Hover',
                        text: lang('Add'),
                        cls: m_act_add,
                        scope: this,
                        handler: function () {
                             displayFormWindowTimesheetStaff();

                             var startDate = Ext.getCmp('startDate').getSubmitValue();
                             var endDate = Ext.getCmp('endDate').getSubmitValue();
                             var nextDate = startDate;

                             var numDays = daydiff(parseDate(startDate), parseDate(endDate));
                             // console.log(numDays)

                             RowEditing.cancelEdit();
                             for(i=0;i<numDays;i++)
                             {
                               var r = Ext.create('timesheetstaffitems.Model', {WorkDate: nextDate,WorkHours: 0,OvertimeHours:0});
                               store_time_staff_items.insert(i, r);

                               var mdySD = nextDate.split('/');
                               var tomorrow = new Date(mdySD[2], mdySD[1]-1, mdySD[0]); //set date format to y/m/d
                               tomorrow.setDate(tomorrow.getDate() + 1); //add one day
                               nextDate = formatDate(tomorrow);
                             }

                             // RowEditing.startEdit(0, 0);
                        }
                    }, {
                     icon: varjs.config.base_url + 'images/icons/new/update.png',
                     cls: m_act_update,
                     hidden:true,
                     text: lang('Edit'),
                     scope: this,
                     handler: function () {
                         RowEditing.cancelEdit();
                         var sm = Ext.getCmp('grid_detail').getSelectionModel().getSelection();
                         RowEditing.startEdit(sm[0].index, 0);
                     }
                 }
               ]
           }
           ],
           columns: [
           {
               text: lang('No'),
               xtype: 'rownumberer',
               width: '5%'
           },
           {
            dataIndex:'StaffID',
            hidden:true
           },
            {
               dataIndex: 'StaffName',
               flex:1,
               minWidth:200,
               text:'Nama Staff'
           },{
               text: lang('Jumlah Jam Kerja'),
               dataIndex: 'totalWorkHour',
               width: 160,
               align:'right'
           }, {
               text: lang('Jumlah Jam Lembur'),
               dataIndex: 'totalOvertime',
               align:'right',
               width: 160,
               style: 'text-align: right'
           }]
        }
        ],
        buttons: [{
            text: lang('Simpan'),
            margin: '5px',
            scale: 'large',
            ui: 's-button',
            cls: 's-blue',
            handler: function() {
                var griditem = Ext.encode(Ext.pluck(storeTimesheetStaffList.data.items, 'data'));
                var form = Ext.getCmp('DataFormTimesheets').getForm();
                form.submit({
                    url: m_api+'/coop_payroll/timesheet',
                    method:'POST',
                    params: {
                            griditem: griditem,
                            TimesheetID:Ext.getCmp('TimesheetID').getValue()
                    },
                    waitMsg: 'Sending data...',
                    success: function(fp, o) {
                        Ext.MessageBox.alert('Success', 'Data saved.');
                        storeTimesheets.load();
                    }
                });

                winTimesheet.hide(this, function() {
                    // store.load();
                });
                
            }
        },{
            text: lang('Cancel'),
            margin: '5px',
            scale: 'large',
            ui: 's-button',
            cls: 's-grey',
            disabled: false,
            handler: function() {
                winTimesheet.hide();
            }
        }]
    });
///////////////////////////

var winTimesheet = Ext.create('widget.window', {
        title: 'Form Timesheet',
        id: 'winTimesheet',
        closable: true,
        modal: true,
        closeAction: 'show',
        width: 700,
        // height: 500,
        height:'90%',
        // autoWidth:true,
        // autoHeight:true,
        layout: {
            type: 'fit'
        },
        items: [DataFormTimesheets]
});


function displayFormWindow(){
    if(!winTimesheet.isVisible()){
        // DataFormTimesheets.getForm().reset();
        winTimesheet.show();
    } else {
        winTimesheet.hide(this, function() {});
        winTimesheet.toFront();
    }
}


//GRID Timesheets
var gridTimesheets = Ext.create('Ext.grid.Panel', {
        store: storeTimesheets,
        renderTo: 'ext-content',
        width: '100%',
        title:'Timesheets',
        minHeight: 450,
        id:'gridTimesheets',
        // style: 'border:1px solid #CCC;',
        loadMask: true,
        selType: 'rowmodel',
        listeners : {
            itemdblclick: function(dv, record, item, index, e) {
                displayFormWindow();
                var sm = record;
                set_data_Timesheets(sm);
            }
        },
        dockedItems: [{
            xtype: 'pagingtoolbar',
            store: storeTimesheets,   
            dock: 'bottom',
            displayInfo: true
        },{
            xtype: 'toolbar',
            items: [{
                icon: varjs.config.base_url + 'images/icons/new/add.png', cls:'Sfr_BtnGridGreen', overCls:'Sfr_BtnGridGreen-Hover',
                text: lang('Tambah'),
                scope: this,
                handler : function(){
                  displayFormWindow();

                   Ext.Ajax.request({
                        // waitMsg: lang('Please Wait'),
                        url: m_api+'coop_payroll/get_idtimesheet',
                        method : 'GET',
                        success: function(response, opts){
                           var obj = Ext.decode(response.responseText);
                           Ext.getCmp('TimesheetID').setValue(obj.TimesheetID);
                        },
                        failure: function(response, opts){
                           var obj = Ext.decode(response.responseText);
                           // Ext.MessageBox.alert('error',lang('Could not connect to the database. Retry later'));
                        }
                     });
                  // DataFormTimesheets.getForm().reset();
                },
                cls : m_act_add
            },{
                icon: varjs.config.base_url+'images/icons/silk/pencil.png',
                text: lang('Update'),
                scope: this,
                hidden:true,
                handler : function(){
                  displayFormWindow();
                  var sm = Ext.getCmp('gridTimesheets').getSelectionModel().getSelection()[0];
                  set_data_Timesheets(sm)
                   Ext.getCmp('ToolbarDetailTimesheets').show();
                },
                cls : m_act_update
            },{
                icon: varjs.config.base_url+'images/icons/silk/pencil.png',
                text: lang('Retur'),
                hidden:true,
                scope: this,
                handler : function(){
                  var sm = Ext.getCmp('grid').getSelectionModel().getSelection()[0];
                  link(m_retur+sm.get('TimesheetsID'));
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
                 var smb = Ext.getCmp('gridTimesheets').getSelectionModel().getSelection()[0];
                 Ext.MessageBox.confirm('Message', lang('Apakah anda mau menghapus data ini ?') , function(btn){
                  // console.log(smb.data)
                     if(btn == 'yes'){
                        Ext.Ajax.request({
                        waitMsg: lang('Please Wait'),
                        url: m_crud,
                        method : 'DELETE',
                        params: {id:  smb.data.TimesheetsID},
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
              hidden:true,
              margin: '0px 5px 0px 6px',
              text: 'Search',
              handler: function () {
                  storeTimesheets.load({
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
          text:'TimesheetsID',
          dataIndex:'TimesheetsID',
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


////END GRID Timesheets




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
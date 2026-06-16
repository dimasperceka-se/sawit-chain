Ext.onReady(function(){

   Ext.tip.QuickTipManager.init();
   
   Ext.define('Scpp.Model', {
      extend: 'Ext.data.Model',
      fields: ['farmerID', 'name', 'typeID','identityNumber', 'address','placeOfBirth', 'dateOfBirth','phone','job','maritalStatus','gender','villageID','valid'],
   });
   var store = Ext.create('Ext.data.Store', {
        model: 'Scpp.Model',
        autoLoad: true,
        pageSize: 10,
        proxy: {
            type: 'ajax',
            url: m_crud+'s',
            params: {
            'X-API-KEY': '030584'
            },
            reader: {
                type: 'json',
                root: 'data'
            }
        }
   }); 
   var RowEditing = Ext.create('Ext.grid.plugin.RowEditing', {
         id: 'RowEditing',
         clicksToMoveEditor: 0,
         autoCancel: false,
         errorSummary : false,
         clicksToEdit: 2
    });
   
    var grid = Ext.create('Ext.grid.Panel', {
       store: store,
       // width: '100%',
       autoWidth:true,
       minHeight: 550,
       //title: 'Survey List',
       style: 'border:1px solid #CCC;',
       renderTo: 'ext-content',
       loadMask: true,
       selType: 'rowmodel',
       dockedItems: [{
            xtype: 'toolbar',
            items: [
               Ext.create('Ext.form.Panel', {
                  fileUpload: true,
                  enctype:'multipart/form-data',
                  id:'upload',
                  items: [{            
                     xtype: 'fileuploadfield',
                     fieldLabel: 'Upload',
                     labelWidth: 60,
                     id: 'file',
                     padding : 5,
                     name: 'file',
                     buttonText: 'Browse',
                     listeners: {
                       'change': function(fb, v){
                          store.removeAll();

                           var form = Ext.getCmp('upload').getForm();
                           form.submit({
                               url: m_crud,
                               waitMsg: 'Sending and insert data temporary...',
                               success: function(form, action) {
                               
                                var d = action.result.data;

                                Ext.Array.each(d, function(record) {
                                  store.add({
                                    farmerID: record.farmerID, 
                                    name: record.name,
                                    typeID: record.typeID,
                                    identityNumber: record.identityNumber,
                                    address: record.address,
                                    placeOfBirth: record.placeOfBirth,
                                    dateOfBirth: record.dateOfBirth,
                                    phone: record.phone,
                                    villageID: record.villageID,
                                    gender: record.gender,
                                    maritalStatus: record.maritalStatus,
                                    job: record.job,
                                    valid: record.valid
                                  });
                                });

                               },
                               failure : function(form, action){
                                Ext.Msg.alert('Failed', action.result ? action.result.message : 'No response');
                               }
                           });
                       }
                     }
                 }]
               }),
               {
                  xtype: 'label',
                  html:'<a href="./template-import-member.xlsx">File Template</a>'
               }]
         }],
       columns: [
       {
            text: 'ID',
            dataIndex: 'id',
            width:'10%',
            hidden:true
       },{
            text: 'valid',
            dataIndex: 'valid',
            width:'10%',
            hidden:true
       },{
            text: 'No',
            xtype: 'rownumberer',
            width:'50'
       },{
           text: 'Farmer ID', 
           width: '100',
           dataIndex: 'farmerID',
           hidden:true
       },{
           text: 'Nama', 
           minWidth: '200',
           dataIndex: 'name'
       },{
           text: 'Member Type ID', 
           minWidth: '150',
           dataIndex: 'typeID'
       },{
           text: 'Number ID', 
            minWidth: '150',
           dataIndex: 'identityNumber'
       },{
           text: 'Address', 
           minWidth: '150',
           dataIndex: 'address'
       },{
           text: 'Gender', 
           minWidth: '150',
           dataIndex: 'gender'
       },{       
           text: 'Place Birth', 
           minWidth: '150',
           dataIndex: 'placeOfBirth'
       },{
           text: 'Date Birth', 
           minWidth: '150',
           dataIndex: 'dateOfBirth'
       },{
           text: 'Village ID', 
          minWidth: '150',
           dataIndex: 'villageID'
       },{
           text: 'Phone Number', 
           minWidth: '150',
           dataIndex: 'phone'
       },{
           text: 'Occupation', 
          minWidth: '150',
           dataIndex: 'job'
       },{
          text: 'Marital Status', 
          minWidth: '150',
           dataIndex: 'maritalStatus'
       },{
          text: 'Remarks', 
          hidden:true,
           width: '100',
           dataIndex: 'Remarks'
       }],
       viewConfig: {
            stripeRows: false,
            getRowClass: function (record) {
                return record.get('error') == 1 ? 'error' : 'no-error';
            }
        },
       buttons: [{
         id:'asaveButton',
         text: 'Save Imported Member',
         margin: '5px',
         scale: 'large',
         ui: 's-button',
         cls: 's-blue ',
         buttonAlign:'left',
         handler: function() {

          var no=1;
          Ext.Array.each(store, function(record) {
            // console.log(record.data.items[0].data.valid);
              if(!record.data.items[0].data.valid)
              {
                Ext.MessageBox.alert('Failed', 'Error data number: '+no);
                return false;
              }
              no++;
          });

          var resultArray = [];

           Ext.Array.each(store, function(record) {
              resultArray.push(record.data.items[0].data);
          });

          Ext.Ajax.request({
              waitMsg: 'Please Wait',
              url: m_save,
              params: {
                  // jsonData: Ext.pluck(store, 'data')
                  jsonData:Ext.pluck(store, 'data')
              },
              // jsonData: Ext.pluck(store.getRange(), 'data'),
              method: 'POST',
              success: function(response, opts) {
                  var obj = Ext.JSON.decode(response.responseText);
                  Ext.Msg.alert('Import Member', obj.message);
              },
              failure: function(response, opts) {
                var obj = Ext.JSON.decode(response.responseText);
                  Ext.Msg.alert('Failure', obj.message);
              }
          });

          // Ext.Ajax.request({
          //     url: m_save,
          //     jsonData: Ext.pluck(store.getRange(), 'data')
          // });

            // var form = Ext.getCmp('upload').getForm();
            // form.submit({
            //    url: m_crud+'_upload_data',
            //    waitMsg: 'Memindahkan data...',
            //    success: function(fp, o) {
            //       Ext.MessageBox.alert('Success', 'Data saved.');
            //       store.load();
            //    }
            // });
         }
      }]
    });
});

Ext.onReady(function(){
   Ext.tip.QuickTipManager.init();
   Ext.define('Scpp.Model', {
      extend: 'Ext.data.Model',
      fields: ['FarmerID', 'CPGid', 'GardenNr','SurveyNr', 'FarmerName','Longitude', 'Latitude','StatusGPS','LandUse','error'],
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
       width: '100%',
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
                           var form = Ext.getCmp('upload').getForm();
                           form.submit({
                            url: m_crud+'_upload',
                            waitMsg: lang('Sending and insert data temporary...'),
                            success: function(fp, o) {
                                setTimeout(function(){
                                    store.load();
                                }, 500);
                            },
                            failure: function(form, action) {
                                Ext.MessageBox.alert('Error', action.result.msg);
                            }
                           });
                       }
                     }
                 }]
               }),
               {
                  xtype: 'label',
                  html:'<a href="'+m_url+'Template Upload GPS.xls">File Template</a>'
               }]
         }],
       columns: [
       {
        text: 'ID',
        dataIndex: 'id',
            flex: 1,
            hidden:true
       },{
            text: 'No',
            xtype: 'rownumberer',
            flex: 1
       },{
           text: 'CPGid', 
           flex: 2,
           dataIndex: 'CPGid',
           hidden:true
       },{
           text: 'FarmerID', 
           flex: 2,
           dataIndex: 'FarmerID'
       },{
           text: 'GardenNr', 
           flex: 2,
           dataIndex: 'GardenNr'
       },{
           text: 'SurveyNr', 
           flex: 2,
           dataIndex: 'SurveyNr'
       },{
           text: 'FarmerName', 
           flex: 2,
           dataIndex: 'FarmerName'
       },{       
           text: 'Longitude', 
           flex: 2,
           dataIndex: 'Longitude'
       },{
           text: 'Latitude', 
           flex: 2,
           dataIndex: 'Latitude'
       },{
           text: 'StatusGPS', 
           flex: 2,
           dataIndex: 'StatusGPS'
       },{
           text: 'LandUse', 
           flex: 2,
           dataIndex: 'LandUse',
           renderer: function(value){
                var text = '';
                switch(value) {
                    case '1':
                        text = lang('Converted Forest');
                        break;
                    case '2':
                        text = lang('Limited Forest');
                        break;
                    case '3':
                        text = lang('Production Forest');
                        break;
                    case '4':
                        text = lang('Protected Forest');
                        break;
                    case '5':
                        text = lang('Unspecified Area');
                        break;
                    default:
                        text = value;
                } 
                return text;
            }
       }],
       viewConfig: {
            stripeRows: false,
            getRowClass: function (record) {
                return record.get('error') == 1 ? 'error' : 'no-error';
            }
        },
       buttons: [{
         id:'asaveButton',
         text: 'Update GPS Farmer',
         margin: '5px',
         scale: 'large',
         ui: 's-button',
         cls: 's-blue ',
         buttonAlign:'left',
         handler: function() {
            var form = Ext.getCmp('upload').getForm();
            form.submit({
               url: m_crud+'_upload_data',
               waitMsg: 'Memindahkan data...',
               success: function(fp, o) {
                  Ext.MessageBox.alert('Success', 'Data saved.');
                  store.load();
               }
            });
         }
      }]
    });
});

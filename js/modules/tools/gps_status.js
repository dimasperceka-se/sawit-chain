Ext.onReady(function(){
    Ext.tip.QuickTipManager.init();
    Ext.define('Scpp.Model', {
        extend: 'Ext.data.Model',
        fields: ['MemberID','MemberDisplayID','MemberName','PlotNr','SurveyNr','StatusPoint','Latitude','Longitude','Errors', 'Valid'],
    });
    var store = Ext.create('Ext.data.Store', {
        model: 'Scpp.Model',
        autoLoad: true,
        pageSize: 20,
        proxy: {
            type: 'ajax',
            url: m_api+'/tools/gps_status',
            params: {
                'X-API-KEY': '030584'
            },
            reader: {
                type: 'json',
                root: 'data',
                count: 'total'
            }
        },
        listeners: {
            'load': function() {
                if(store.getTotalCount()>0){
                    //Store is not empty
                    Ext.getCmp('file').hide();
                    Ext.getCmp('clear-data').show();
                    Ext.getCmp('asaveButton').show();
                } else{
                    //Store empty
                    Ext.getCmp('file').show();
                    Ext.getCmp('clear-data').hide();
                    Ext.getCmp('asaveButton').hide();
                }
            }
        }
    }); 

    var grid = Ext.create('Ext.grid.Panel', {
        store: store,
        width: '100%',
        minHeight: 550,
        style: 'border:1px solid #CCC;',
        renderTo: 'ext-content',
        loadMask: true,
        selType: 'rowmodel',
        dockedItems: [        
        {
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
                                url: m_api+'/tools/gps_status',
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
               xtype: 'button',
               id:'clear-data',
               text: 'Clear Data',
               handler: function() {
                 Ext.Ajax.request({
                     url: m_api+'/tools/gps_clear_data',
                     method: "GET",
                     waitMsg: 'Mengosongkan temporary data...',
                     success: function(fp, o) {
                        Ext.MessageBox.alert('Success', 'Temporary data dikosongkan.');
                        setTimeout(function(){
                             store.load();
                         }, 500);
                     //    store.load();
                     },
                     failure: function(response, opts) {
                         console.log('server-side failure with status code ' + response.status);
                         console.log('responseText: ' + response.responseText);
                     }
                  });
               }
            }
            // {
            //     xtype: 'label',
            //     html:'<a href="'+m_url+'Template Upload GPS.xls">File Template</a>'
            // },
            ]
        },

        {
            xtype: 'pagingtoolbar',
            store: store, // same store GridPanel is using
            dock: 'top',
            displayInfo: true
        },
        ],
        columns: [
        {
            text: 'No',
            xtype: 'rownumberer',
            width: 50
        },
        {
            text: 'Member ID', 
            flex: 2,
            dataIndex: 'MemberDisplayID'
        },
        {
            text: 'Member Name', 
            flex: 2,
            dataIndex: 'MemberName'
        },
        {
            text: 'Plot Nr', 
            flex: 2,
            dataIndex: 'PlotNr'
        },
        {
            text: 'Survey Nr', 
            flex: 2,
            dataIndex: 'SurveyNr'
        },
        {       
            text: 'Status', 
            flex: 1,
            dataIndex: 'StatusPoint'
        },
        {       
            text: 'Latitude', 
            flex: 1,
            dataIndex: 'Latitude'
        },
        {       
            text: 'Longitude', 
            flex: 1,
            dataIndex: 'Longitude'
        },
        {       
            text: 'Errors', 
            flex: 2,
            dataIndex: 'Errors'
        },
        ],
        viewConfig: {
            stripeRows: false,
            getRowClass: function (record) {
                return record.get('Valid') == 1 ? 'no-error' : 'error';
            }
        },
        buttons: [{
            id:'asaveButton',
            text: lang('Update GPS Status'),
            margin: '5px',
            scale: 'large',
            ui: 's-button',
            cls: 's-blue ',
            buttonAlign:'left',
            handler: function() {
                var form = Ext.getCmp('upload').getForm();
                form.submit({
                    url: m_api+'/tools/update_gps_status',
                    waitMsg: lang('Memindahkan data...'),
                    success: function(fp, o) {
                        Ext.MessageBox.alert('Success', 'Data saved.');
                        store.load();
                    }
                });
            }
        }]
    });
});

Ext.onReady(function(){
    Ext.tip.QuickTipManager.init();
    Ext.define('Scpp.Model', {
        extend: 'Ext.data.Model',
        fields: ['MemberID','MemberDisplayID','MemberName','PlotNr','SurveyNr','Revision',],
    });
    var store = Ext.create('Ext.data.Store', {
        model: 'Scpp.Model',
        autoLoad: true,
        pageSize: 20,
        proxy: {
            type: 'ajax',
            url: m_api+'/tools/kml_farmers',
            params: {
                'X-API-KEY': '030584'
            },
            reader: {
                type: 'json',
                root: 'data'
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
                                url: m_api+'/tools/upload_kml',
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
                     url: m_api+'/tools/kml_clear_data',
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
            text: 'Revision', 
            flex: 1,
            dataIndex: 'Revision'
        },
        ],
        viewConfig: {
            stripeRows: false,
            getRowClass: function (record) {
                return record.get('error') == 1 ? 'error' : 'no-error';
            }
        },
        buttons: [{
            id:'asaveButton',
            text: lang('Update Farmer Polygon'),
            margin: '5px',
            scale: 'large',
            ui: 's-button',
            cls: 's-blue ',
            buttonAlign:'left',
            handler: function() {
                var form = Ext.getCmp('upload').getForm();
                form.submit({
                    url: m_api+'/tools/update_kml',
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

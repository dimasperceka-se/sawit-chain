Ext.onReady(function() {
    
    Ext.tip.QuickTipManager.init();
    
    Ext.create('Ext.panel.Panel', {
        layout: 'fit',
        height:1200,
        autoScroll: true,
        id: 'panel-cashflow',
        renderTo:'ext-content',
        dockedItems:[
            {
                xtype:'toolbar',
                dock:'top',
                items:[
                    {
                        xtype:'form',
                        id:'frm-cashflow',
                        layout:{
                            type:'hbox'
                        },
                        defaults:{
                            margin:'3 3 3 3'
                        },
                        items:[{
                            xtype: 'combo',
                            labelAlign:'top',
                            fieldLabel: 'Start Month <b style="color:red">*</b>',
                            store: Ext.create('Ext.data.Store', {
                                fields: ['MONTH_ID', 'MONTH_NAME'],
                                autoLoad: true,
                               data:[
                                   {'MONTH_ID':'1','MONTH_NAME':'Januari'},
                                   {'MONTH_ID':'2','MONTH_NAME':'Februari'},
                                   {'MONTH_ID':'3','MONTH_NAME':'Maret'},
                                   {'MONTH_ID':'4','MONTH_NAME':'April'},
                                   {'MONTH_ID':'5','MONTH_NAME':'Mei'},
                                   {'MONTH_ID':'6','MONTH_NAME':'Juni'},
                                   {'MONTH_ID':'7','MONTH_NAME':'Juli'},
                                   {'MONTH_ID':'8','MONTH_NAME':'Agustus'},
                                   {'MONTH_ID':'9','MONTH_NAME':'September'},
                                   {'MONTH_ID':'10','MONTH_NAME':'Oktober'},
                                   {'MONTH_ID':'11','MONTH_NAME':'November'},
                                   {'MONTH_ID':'12','MONTH_NAME':'Desember'}
                               ]
                            }),
                            displayField: 'MONTH_NAME',
                            value:m_startdate,
                            valueField: 'MONTH_ID',
                            name: 'START_MONTH_ID'

                        },{
                            xtype: 'combo',
                            labelAlign:'top',
                            fieldLabel: 'End Month <b style="color:red">*</b>',
                            store: Ext.create('Ext.data.Store', {
                                fields: ['MONTH_ID', 'MONTH_NAME'],
                                autoLoad: true,
                               data:[
                                   {'MONTH_ID':'1','MONTH_NAME':'Januari'},
                                   {'MONTH_ID':'2','MONTH_NAME':'Februari'},
                                   {'MONTH_ID':'3','MONTH_NAME':'Maret'},
                                   {'MONTH_ID':'4','MONTH_NAME':'April'},
                                   {'MONTH_ID':'5','MONTH_NAME':'Mei'},
                                   {'MONTH_ID':'6','MONTH_NAME':'Juni'},
                                   {'MONTH_ID':'7','MONTH_NAME':'Juli'},
                                   {'MONTH_ID':'8','MONTH_NAME':'Agustus'},
                                   {'MONTH_ID':'9','MONTH_NAME':'September'},
                                   {'MONTH_ID':'10','MONTH_NAME':'Oktober'},
                                   {'MONTH_ID':'11','MONTH_NAME':'November'},
                                   {'MONTH_ID':'12','MONTH_NAME':'Desember'}
                               ]
                            }),
                            displayField: 'MONTH_NAME',
                            value:m_enddate,
                            valueField: 'MONTH_ID',
                            name: 'END_MONTH_ID'

                        },{
                            xtype: 'combo',
                            labelAlign:'top',
                            fieldLabel: 'Year <b style="color:red">*</b>',
                            store: Ext.create('Ext.data.Store', {
                                fields: ['YEAR_ID', 'YEAR_NAME'],
                                autoLoad: true,
                                proxy: {
                                    type: 'rest',
                                    url: m_crud + '/common/getyear', // url that will load data with respect to start and limit params
                                    reader: {
                                        type: 'json',
                                        root: 'data',
                                        totalProperty: 'total'
                                    }
                                }
                            }),
                            displayField: 'YEAR_NAME',
                            value:m_tahun,
                            valueField: 'YEAR_ID',
                            name: 'YEAR_ID'

                        },{
                            xtype: 'combo',
                            disabled:true,
                            hidden:true,
                            fieldLabel: 'COA <b style="color:red">*</b>',
                            store: Ext.create('Ext.data.Store', {
                                fields: ['COA_CODE', 'COA_TITLE'],
                                autoLoad: true,
                                proxy: {
                                    type: 'rest',
                                    url: m_crud + '/common/getcombo', // url that will load data with respect to start and limit params
                                    extraParams: {
                                        table: 'r_coa',
                                        name: 'COA_TITLE',
                                        id: 'COA_CODE'
                                    },
                                    reader: {
                                        type: 'json',
                                        root: 'data',
                                        totalProperty: 'total'
                                    }
                                }
                            }),
                            displayField: 'COA_TITLE',
                            valueField: 'COA_CODE',
                            name: 'COA_CODE'

                        },{
                            xtype: 'combo',
                            disabled:true,
                            hidden:true,
                            disabledCls: 'disabled',
                            fieldLabel: 'Status',
                            store: Ext.create('Ext.data.Store', {
                                fields: ['STATUS_ID', 'STATUS_NAME'],
                                autoLoad: true,
                                data: [
                                    {STATUS_ID: '1', STATUS_NAME: 'Unposted'},
                                    {STATUS_ID: '2', STATUS_NAME: 'Posted'},
                                    {STATUS_ID: '3', STATUS_NAME: 'All'}
                                ]
                            }),
                            displayField: 'STATUS_NAME',
                            valueField: 'STATUS_ID',
                            name: 'JOURNAL_STATUS'
                        },{
                            xtype:'button',
                            margin:'32 3 3 3',
                            text:'Generate Report',
                            handler:function(){
                                
                                function EncodeQueryData(data)
                                {
                                    var ret = [];
                                    var d;
                                    for (d in data) {
                                        ret.push(encodeURIComponent(d) + "=" + encodeURIComponent(data[d]));
                                        }
                                    return ret.join("&");
                                    
                                }
                                
                                var params = Ext.getCmp('frm-cashflow').getForm().getValues();
                                var loader = Ext.getCmp('panel-cashflow').getLoader();
                                var querystring = EncodeQueryData(params);
                                var url = m_crud + '/cashflow/generate2';
                                
                                loader.load({
                                    method:'GET',
                                    params:params
                                });
                                
                                /*
                                
                                window.location = url + "?" + querystring;
                                */
                            }
                        },{
                            xtype:'button',
                            margin:'32 3 3 3',
                            text:'Export to xls',
                            handler:function(){
                                function EncodeQueryData(data)
                                {
                                    var ret = [];
                                    var d;
                                    for (d in data) {
                                        ret.push(encodeURIComponent(d) + "=" + encodeURIComponent(data[d]));
                                        }
                                    return ret.join("&");
                                    
                                }
                                
                                var params = Ext.getCmp('frm-cashflow').getForm().getValues();
                                
                                params.xls = "true";
                                
                                var loader = Ext.getCmp('panel-cashflow').getLoader();
                                var querystring = EncodeQueryData(params);
                                var url = m_crud + '/cashflow/generate2/true';
                                
                                window.location = url + "?" + querystring;

                            }
                        }]
                    }
                ]
            }
        ],
        loader: {
            url: m_crud + '/cashflow/generate2',
            autoLoad: false,
            loadMask:true
        }
    });

});

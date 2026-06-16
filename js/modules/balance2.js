Ext.onReady(function() {
    
    Ext.tip.QuickTipManager.init();


    Ext.define('reportNeraca', {
        extend: 'Ext.panel.Panel',
        alias: 'widget.reportNeraca',
        id: 'reportNeraca',
        title: 'Laporan Neraca',
        dockedItems: [
            {
                xtype: 'toolbar',
                dock: 'bottom',
                items: [
                       '->',
                    {
                        xtype: 'button',
                        text: 'Export to Excel',
                        iconCls: 'page_excel',
                        listeners: {
                            click: function(component) {
                                
                                function EncodeQueryData(data)
                                {
                                    var ret = [];
                                    var d;
                                    for (d in data) {
                                        ret.push(encodeURIComponent(d) + "=" + encodeURIComponent(data[d]));
                                        }
                                    return ret.join("&");
                                    
                                }

                                var frmval = Ext.getCmp("form-filter-traceability-sync").getValues();
                                frmval.xls = "true";
                                var store = Ext.getCmp("grid-report-traceability-sync").getStore();
                                var url = m_crud + '/balance/generate2';
                                var querystring = EncodeQueryData(frmval);
                                window.location = url + "?" + querystring;
                            }
                        }
                    } 
                ]
        }, {
                xtype: 'toolbar',
                dock: 'top',
                items: [{
                        xtype: 'comboxunit',
                        labelWidth: 90,
                        multiSelect:true,
                        valueField:'idunit',
                        id: 'unitReportNeraca',
                                listeners: {
                                    select: {
                                        fn: function(combo, value) {
                                            // Ext.getCmp('reportNeraca').setTitle(combo.getValue());
                                    }
                                }
                            }
                    },{
                        xtype: 'datefield',
                        id: 'tanggalReportNeraca1',
                        format: 'F Y',
                        labelWidth: 90,
                        fieldLabel: 'Bulan'
                    }, {
                        xtype: 'datefield',
                        id: 'tanggalReportNeraca2',
                        format: 'F Y',
                        labelWidth: 40,
                        fieldLabel: 's/d'
                    },
                    {
                        xtype: 'button',
                        text: 'Tampilkan Laporan',
                        iconCls: 'report_key',
                        listeners: {
                            click: function(component) {
                                var loader = Ext.getCmp('panel-balance').getLoader();
                                loader.load({
                                    params:params
                                });
                                // var report1 = Ext.getCmp('tanggalReportNeraca1').getSubmitValue();
                                // var report2 = Ext.getCmp('tanggalReportNeraca2').getSubmitValue();
                                // var unitReportNeraca = Ext.getCmp('unitReportNeraca').getValue();
                                // Ext.getCmp('reportNeraca').body.update("<iframe style='border:0;' width='100%' height='100%' id='iframeReportNeraca' src='"+SITE_URL+"laporan/neraca/" + unitReportNeraca + "/" + report1 + "/" + report2 + "'>");
                            }
                        }
                    }]
            }],
    //    html: "<iframe id='iframeReportNeraca' src='"+SITE_URL+"aktiva'/>"
    });
    
    Ext.create('Ext.panel.Panel', {
        layout: 'fit',
        autoScroll: true,
        id: 'panel-balance',
        renderTo:'ext-content',
        dockedItems:[
             {
                    xtype: 'toolbar',
                    dock: 'bottom',
                    items: [{
                        xtype: 'datefield',
                        id: 'tanggalReportNeraca1',
                        format: 'F Y',
                        labelWidth: 90,
                        fieldLabel: 'Periode'
                    }, 
                    {
                        xtype: 'datefield',
                        id: 'tanggalReportNeraca2',
                        format: 'F Y',
                        labelWidth: 40,
                        fieldLabel: 's/d'
                    },
                    {
                        xtype: 'button',
                        text: 'Go',
                        iconCls: 'report_key',
                        listeners: {
                            click: function(component) {
                                 Ext.getCmp('panel-balance').body.update("<iframe style='border:0;' width='100%' height='100%' id='iframeReportNeraca' src='"+m_crud + "'>");
                                // var loader = Ext.getCmp('panel-balance').getLoader();
                                // loader.load({
                                //     // params:params
                                // });
                                // var report1 = Ext.getCmp('tanggalReportNeraca1').getSubmitValue();
                                // var report2 = Ext.getCmp('tanggalReportNeraca2').getSubmitValue();
                                // var unitReportNeraca = Ext.getCmp('unitReportNeraca').getValue();
                                // Ext.getCmp('reportNeraca').body.update("<iframe style='border:0;' width='100%' height='100%' id='iframeReportNeraca' src='"+SITE_URL+"laporan/neraca/" + unitReportNeraca + "/" + report1 + "/" + report2 + "'>");
                            }
                        }
                    },
                           '->',
                         {
                            xtype: 'button',hidden:true,
                            text: 'Email',
                            iconCls: 'email-icon',
                            listeners: {
                                click: function(component) {

                                }
                            }
                        },{
                            xtype: 'button',
                            text: 'Print',
                            iconCls: 'print-icon',
                            listeners: {
                                click: function(component) {
                                     var report1 = Ext.getCmp('tanggalReportNeraca1').getSubmitValue();
                                    var report2 = Ext.getCmp('tanggalReportNeraca2').getSubmitValue();
                                    var unitReportNeraca = Ext.getCmp('unitReportNeraca').getValue();
                                    Ext.getCmp('reportNeraca').body.update("<iframe style='border:0;' width='100%' height='100%' id='iframeReportNeraca' src='"+SITE_URL+"laporan/neraca/" + unitReportNeraca + "/" + report1 + "/" + report2 + "/print'>");
                                }
                            }
                        },
                        {
                            xtype: 'button',hidden:true,
                            text: 'Export PDF',
                            iconCls: 'acrobat',
                            listeners: {
                                click: function(component) {

                                }
                            }
                        },
                        {
                            xtype: 'button',
                            text: 'Export Excel',
                            iconCls: 'page_excel',
                            listeners: {
                                click: function(component) {
                                     var report1 = Ext.getCmp('tanggalReportNeraca1').getSubmitValue();
                                    var report2 = Ext.getCmp('tanggalReportNeraca2').getSubmitValue();
                                    var unitReportNeraca = Ext.getCmp('unitReportNeraca').getValue();
                                    Ext.getCmp('reportNeraca').body.update("<iframe style='border:0;' width='100%' height='100%' id='iframeReportNeraca' src='"+SITE_URL+"laporan/neraca/" + unitReportNeraca + "/" + report1 + "/" + report2 + "/excel'>");
                                }
                            }
                        } 
                    ]
            }
        ],
        loader: {
            url: m_crud + '/balance/generate',
            autoLoad: false
        }
    });

});

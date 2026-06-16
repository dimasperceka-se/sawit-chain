Ext.define('Koltiva.view.Traceability_new.report.MainFormSms', {
    extend: 'Ext.panel.Panel',
    id: 'Koltiva.view.Traceability_new.report.MainFormSms',
    style: 'padding:0 15px 15px 15px;margin:5px 0 0 0;',
    viewVar: false,
    setViewVar: function (value) {
        this.viewVar = value;
    },
    renderTo: 'ext-content',
    listeners: {
        afterRender: function () {
            var thisObj = this;

            if (thisObj.viewVar.OpsiDisplay == 'view') {
                Ext.getCmp('Koltiva.view.Traceability_new.report.MainFormSms-FormBasicData-AutoID').setReadOnly(true);

                //load formnya
                Ext.getCmp('Koltiva.view.Traceability_new.report.MainFormSms-FormBasicData').getForm().load({
                    url: m_api + '/traceability_api/web_transaction/sms_detail_form_open',
                    method: 'GET',
                    params: {
                        AutoID: this.viewVar.AutoID
                    },
                    success: function (form, action) {
                        Ext.MessageBox.hide();
                        var r = Ext.decode(action.response.responseText);

                        //Title
                        Ext.getCmp('Koltiva.view.Traceability_new.report.MainFormSms-labelInfoInsert').update('<div id="header_title_farmer">' + Ext.getCmp('Koltiva.view.Traceability_new.report.MainFormSms-FormBasicData-TransNumber').getValue() + '</div>');
                        Ext.getCmp('Koltiva.view.Traceability_new.report.MainFormSms-labelInfoInsert').doLayout();
                        
                    },
                    failure: function (form, action) {
                        Ext.MessageBox.hide();
                        Ext.MessageBox.show({
                            title: 'Failed',
                            msg: 'Failed to retrieve data',
                            buttons: Ext.MessageBox.OK,
                            animateTarget: 'mb9',
                            icon: 'ext-mb-error'
                        });
                    }
                });
            } 
        },
        beforerender: function () {
            var thisObj = this;

            if (thisObj.viewVar.OpsiDisplay != 'insert') {
                Ext.MessageBox.show({
                    msg: 'Please wait...',
                    progressText: 'Loading...',
                    width: 300,
                    wait: true,
                    waitConfig: {
                        interval: 200
                    },
                    icon: 'ext-mb-info', //custom class in msg-box.html
                    animateTarget: 'mb9'
                });
            }
        }
    },
    initComponent: function () {
        var thisObj = this;
        var labelWidth = 200;
       
        // var MainGridCheckingSms = Ext.create('Koltiva.view.Traceability_new.report.MainGridCheckingSms')
        thisObj.StoreGridCheckingSms = Ext.create('Koltiva.store.Traceability_new.Report.MainGridCheckingSms', {
            storeVar: {
                AutoID : thisObj.viewVar.AutoID
            }
        });

        //Panel Basic ==================================== (Begin)
        thisObj.ObjPanelBasicData = Ext.create('Ext.panel.Panel', {
            title: lang('Detail SMS Form'),
            frame: true,
            cls: 'Sfr_PanelLayoutForm',
            id: 'Koltiva.view.Traceability_new.report.MainFormSms-FormGeneralData',
            collapsible: true,
            items: [
                {
                    xtype: 'form',
                    id: 'Koltiva.view.Traceability_new.report.MainFormSms-FormBasicData',
                    buttonAlign: 'right',
                    cls: 'Sfr_PanelSubLayoutForm',
                    items: [{
                            items: [
                            {
                                xtype: 'tabpanel',
                                id:'all_panel', 
                                flex: 1,
                                margin: 2,
                                activeTab: 0,
                                plain: true,
                                cls:'tabSce',
                                items: [{
                                    xtype: 'panel',
                                    autoScroll: true, 
                                    disabled:false,
                                    title: lang('Information'),
                                    width:'100%',
                                    padding:5,
                                    style: 'border:2px solid #ADD2ED', 
                                    items: [
                                        {
                                            layout: 'column',
                                            border: false,
                                            items: [
                                                {
                                                    columnWidth: 0.435,
                                                    layout: 'form',
                                                    style: 'padding:10px 5px 10px 20px;',
                                                    defaults: {
                                                        labelAlign: 'left',
                                                        labelWidth: 150
                                                    },
                                                    items: [{
                                                        xtype: 'textfield',
                                                        id: 'Koltiva.view.Traceability_new.report.MainFormSms-FormBasicData-AutoID',
                                                        name: 'Koltiva.view.Traceability_new.report.MainFormSms-FormBasicData-AutoID',
                                                        inputType: 'hidden'
                                                    },
                                                    {
                                                        xtype: 'textfield',
                                                        id: 'Koltiva.view.Traceability_new.report.MainFormSms-FormBasicData-SMSStatus',
                                                        name: 'Koltiva.view.Traceability_new.report.MainFormSms-FormBasicData-SMSStatus',
                                                        fieldLabel: lang('SMS Status')
                                                    },
                                                    {
                                                        xtype: 'datefield',
                                                        fieldLabel: lang('Send Date'),
                                                        width: 500,
                                                        labelAlign:'left',
                                                        format: 'Y-m-d H:i:s',
                                                        id: 'Koltiva.view.Traceability_new.report.MainFormSms-FormBasicData-SendDate',
                                                        name: 'Koltiva.view.Traceability_new.report.MainFormSms-FormBasicData-SendDate',
                                                        value: m_now,
                                                    },
                                                    {
                                                        xtype: 'htmleditor',
                                                        id: 'Koltiva.view.Traceability_new.report.MainFormSms-FormBasicData-SmsText',
                                                        fieldLabel: lang('Message'),                        
                                                        height: 100,
                                                        padding: '2',
                                                        enableColors: true,
                                                        enableAlignments: true,
                                                        enableSourceEdit: true,
                                                        enableFont: true,
                                                        enableFontSize: true,
                                                        enableFormat: true,
                                                        enableLinks: true,
                                                        enableLists: true,
                                                        readOnly: true
                                                    },
                                                    {
                                                        xtype: 'htmleditor',
                                                        id: 'Koltiva.view.Traceability_new.report.MainFormSms-FormBasicData-response',
                                                        fieldLabel: lang('Response'),                        
                                                        height: 100,
                                                        padding: '2',
                                                        enableColors: true,
                                                        enableAlignments: true,
                                                        enableSourceEdit: true,
                                                        enableFont: true,
                                                        enableFontSize: true,
                                                        enableFormat: true,
                                                        enableLinks: true,
                                                        enableLists: true,
                                                        readOnly: true
                                                    },
                                                ]},
                                                {
                                                    columnWidth: 0.435,
                                                    layout: 'form',
                                                    style: 'padding:10px 5px 10px 20px;',
                                                    defaults: {
                                                        labelAlign: 'left',
                                                        labelWidth: 150
                                                    },
                                                    items: [{
                                                        xtype: 'textfield',
                                                        id: 'Koltiva.view.Traceability_new.report.MainFormSms-FormBasicData-TransNumber',
                                                        name: 'Koltiva.view.Traceability_new.report.MainFormSms-FormBasicData-TransNumber',
                                                        inputType: 'hidden'
                                                    },
                                                    {
                                                        xtype: 'textfield',
                                                        id: 'Koltiva.view.Traceability_new.report.MainFormSms-FormBasicData-Handphone',
                                                        name: 'Koltiva.view.Traceability_new.report.MainFormSms-FormBasicData-Handphone',
                                                        fieldLabel: lang('SMS To')
                                                    },
                                                    {
                                                        xtype: 'textfield',
                                                        id: 'Koltiva.view.Traceability_new.report.MainFormSms-FormBasicData-AgentName',
                                                        name: 'Koltiva.view.Traceability_new.report.MainFormSms-FormBasicData-AgentName',
                                                        fieldLabel: lang('From')
                                                    },
                                                    {
                                                        xtype: 'textfield',
                                                        id: 'Koltiva.view.Traceability_new.report.MainFormSms-FormBasicData-FarmerName',
                                                        name: 'Koltiva.view.Traceability_new.report.MainFormSms-FormBasicData-FarmerName',
                                                        fieldLabel: lang('to')
                                                    }]
                                                }
                                            ]
                                        }, 
                                    ]
                                },
                                {
                                    xtype: 'grid',
                                    title: lang('SMS Status'),
                                    id: 'Koltiva.view.Traceability_new.report.MainGridCheckingSms-gridMainGrid',
                                    style: 'border:1px solid #CCC;margin-top:4px;',
                                    loadMask: true,
                                    selType: 'rowmodel',
                                    store: thisObj.StoreGridCheckingSms,
                                    width: '100%',
                                    minHeight:400,
                                    viewConfig: {
                                        deferEmptyText: false,
                                        emptyText: lang('No data Available'),
                                    }, 
                                    dockedItems: [{
                                        xtype: 'pagingtoolbar',
                                        id: 'Koltiva.view.Traceability_new.report.MainGridCheckingSms-gridToolbar',
                                        store: thisObj.StoreGridCheckingSms,
                                        dock: 'bottom',
                                        displayInfo: true
                                    }],
                                    columns: [
                                    {
                                        text: 'No',
                                        width: '5%',
                                        xtype: 'rownumberer'
                                    },
                                    {
                                        text: 'ID',
                                        dataIndex: 'AutoID',
                                        hidden: true,
                                    },
                                    {
                                        text: lang('Auto ID'),
                                        dataIndex: 'AutoID',
                                        flex:20
                                    }, 
                                    {
                                        text: lang('Request'),
                                        dataIndex: 'request',
                                        flex:20
                                    }, 
                                    {
                                        text: lang('Response Status'),
                                        dataIndex: 'ResponseStatus',
                                        flex:20
                                    },
                                    {
                                        text: lang('Status'),
                                        dataIndex: 'Status',
                                        flex:20
                                    },
                                    {
                                        text: lang('Date Created'),
                                        dataIndex: 'DateCreated',
                                        renderer: Ext.util.Format.dateRenderer('d-m-Y'),
                                        flex:20
                                    }],
                                    listeners: { 
                                            
                                    }
                                }
                                ],
                                listeners: { 
                                    'tabchange': function (tabPanel, tab) { 
                                      
                                    }
                                }
                            }
                            /*END TAB*/
                        ]
                        }]
                }]
        });
        //Panel Basic ==================================== (End)

        //========================================================== LAYOUT UTAMA (Begin) ========================================//
        thisObj.items = [{
                xtype: 'panel',
                border: false,
                layout: {
                    type: 'hbox'
                },
                items: [{
                        id: 'Koltiva.view.Traceability_new.report.MainFormSms-labelInfoInsert',
                        html: '<div id="header_title_farmer">' + lang('Selling') + '</div>'
                    }]
            }, {
                items: [{
                        id: 'Koltiva.view.Traceability_new.report.MainFormSms-LinkBackToList',
                        html: '<div id="Sfr_IdBoxInfoDataGrid" class="Sfr_BoxInfoDataGrid"><ul class="Sft_UlListInfoDataGrid"><li class="Sft_ListInfoDataGrid"><a href="javascript:Ext.getCmp(\'Koltiva.view.Traceability_new.report.MainFormSms\').BackToList()"><img class="Sft_ListIconInfoDataGrid" src="' + varjs.config.base_url + 'images/icons/new/back.png" width="20" />&nbsp;&nbsp;' + lang('Back to Report List') + '</a></li></div>'
                    }]
            }, {
                html: '<br />'
            }, {
                layout: 'column',
                border: false,
                items: [{
                        columnWidth: 1,
                        items: [
                            thisObj.ObjPanelBasicData
                        ]
                    }]
            }];
        //========================================================== LAYOUT UTAMA (END) ========================================//

        this.callParent(arguments);
    },
    BackToList: function () {
        Ext.getCmp('Koltiva.view.Traceability_new.report.MainFormSms').destroy(); //destory current view
        var GridMain = [];
        if (Ext.getCmp('Koltiva.view.Traceability_new.report.MainGridSms') == undefined) {
            GridMain = Ext.create('Koltiva.view.Traceability_new.report.MainGridSms');
        } else {
            //destroy, create ulang
            Ext.getCmp('Koltiva.view.Traceability_new.report.MainGridSms').destroy();
            GridMain = Ext.create('Koltiva.view.Traceability_new.report.MainGridSms');
        }
    }
});
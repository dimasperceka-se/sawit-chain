Ext.define('Koltiva.view.UserAffiliate.FormPanel', {
    extend: 'Ext.panel.Panel',
    id: 'Koltiva.view.UserAffiliate.FormPanel',
    style:'padding:0 15px 15px 15px;margin:12px 0 0 0;',
    renderTo: 'ext-content',
    viewVar: false,
    setViewVar: function(value){
        this.viewVar = value;
    },
    initComponent: function() {
        var thisObj = this;
        
        thisObj.AffiliatedGrid = Ext.create('Koltiva.view.UserAffiliate.AffiliatedGrid', {
            viewVar: {
                UserId: this.viewVar.UserId,
                IsUpdate: thisObj.viewVar.IsUpdate
            }
        });
        
        thisObj.items = [{
            xtype: 'panel',
            border: false,
            width: '100%',
            layout:{
                type:'hbox'
            },
            items:[{
                id: 'Koltiva.view.UserAffiliate.FormMain-LabelInfoTitle',
                html:'<h3 style="margin:0px 0px 4px 0px;padding:0px;">- '+lang('User Affiliate Data')+'</h3>',
            }]
        },{
            html: '<div id="Sfr_IdBoxInfoDataGrid" class="Sfr_BoxInfoDataGrid">' +
                  '<ul class="Sft_UlListInfoDataGrid"><li class="Sft_ListInfoDataGrid">' +
                  '<a><img class="Sft_ListIconInfoDataGrid" src="' + varjs.config.base_url + 'images/icons/new/back.png" width="20" />' +
                  '&nbsp;&nbsp;' + lang('Back to List')  + '</a></li></ul></div>',
            listeners: {
                click: {
                    element: 'el',
                    preventDefault: true,
                    fn: function(e, target){
                        Ext.getCmp('Koltiva.view.UserAffiliate.FormPanel').destroy();

                        if(Ext.getCmp('Koltiva.view.UserAffiliate.MainGrid') == undefined){
                            var GridMain = Ext.create('Koltiva.view.UserAffiliate.MainGrid');
                        }else{
                            Ext.getCmp('Koltiva.view.UserAffiliate.MainGrid').destroy();
                            var GridMain = Ext.create('Koltiva.view.UserAffiliate.MainGrid');
                        }
                    }
                }
            }
        },{
            html:'<br />'
        },{
            xtype: 'panel',
            border: true,
            title: lang('Data'),
            frame: true,
            margin:'0 0 20 0',
            items: [{
                xtype: 'panel',
                width: '100%',
                items:[
                    {
                        xtype: 'form',
                        id: 'Koltiva.view.UserAffiliate.form',
                        frame: false,
                        width: 900,
                        height: 100,
                        autoScroll:true,
                        bodyPadding: 10,
                        fieldDefaults: {
                            labelAlign: 'left',
                            labelWidth: 120,
                            padding: 10,
                            anchor: '100%'
                        },
                        items: [
                            {
                                xtype: 'panel',
                                autoScroll: true,
                                items: [
                                    {
                                        xtype: 'hidden',
                                        id: 'Koltiva.view.UserAffiliate.UserId',
                                        name: 'Koltiva.view.UserAffiliate.UserId',
                                    },
                                    {
                                        xtype: 'textfield',
                                        fieldLabel: lang('User Name'),
                                        labelWidth: 120,
                                        allowBlank: false,
                                        id: 'Koltiva.view.UserAffiliate.UserName',
                                        name: 'Koltiva.view.UserAffiliate.UserName',
                                        readOnly: true
                                    },
                                    {
                                        xtype: 'textfield',
                                        fieldLabel: lang('Name'),
                                        labelWidth: 120,
                                        allowBlank: true,
                                        id: 'Koltiva.view.UserAffiliate.UserRealName',
                                        name: 'Koltiva.view.UserAffiliate.UserRealName',
                                        readOnly: true
                                    },
                                ],
                            }
                        ]
                    },
                    {
                        html: '<hr>',
                    },
                    {
                        layout: 'auto',
                        border: false,
                        height: 400,
                        items: [{
                            items:[
                                thisObj.AffiliatedGrid
                            ]
                        }]
                    }
                ]
            }]
        }];

        this.callParent(arguments);
    },
    listeners: {
        afterRender: function(){
            Ext.getCmp('Koltiva.view.UserAffiliate.FormMain-LabelInfoTitle').update('<h3 style="margin:0px 0px 4px 0px;padding:0px;">'+ this.viewVar.UserRealName +'</h3>');
            Ext.getCmp('Koltiva.view.UserAffiliate.FormMain-LabelInfoTitle').doLayout();
        }
    }
});
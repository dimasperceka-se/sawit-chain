//override time out ajax exts js yg cuman 30 detikan jadi 10 menit
Ext.Ajax.timeout = 600000;
Ext.override(Ext.form.Basic, {
    timeout: Ext.Ajax.timeout / 1000
});
Ext.override(Ext.data.proxy.Server, {
    timeout: Ext.Ajax.timeout
});
Ext.override(Ext.data.Connection, {
    timeout: Ext.Ajax.timeout
});

Ext.define('Koltiva.view.Menu_pull_engine_check.MainGrid' ,{
    extend: 'Ext.panel.Panel',
    id: 'Koltiva.view.Menu_pull_engine_check.MainGrid',
    renderTo: 'ext-content',
    style:'padding:0 7px 7px 7px;margin:2px 0 0 0;',
    listeners: {
        afterRender: function(component, eOpts){
            var thisObj = this;
        }
    },
    initComponent: function() {
        var thisObj = this;

        thisObj.objPanelPullEngineCheck = Ext.create('Koltiva.view.Menu_pull_engine_check.PanelPullEngineCheck');
        thisObj.objPanelSysSetting      = Ext.create('Koltiva.view.Menu_pull_engine_check.PanelSysSetting');

        thisObj.items = [{
            layout: 'column',
            border: false,
            items: [
                {
                    columnWidth: 1,
                    items:[thisObj.objPanelPullEngineCheck]
                },
                {
                    columnWidth: 1,
                    items:[{
                        html: '<br /><div style="height:10px;border-bottom:1px dashed gray;"></div><br />'
                    }]
                },
                {
                    columnWidth: 1,
                    items:[thisObj.objPanelSysSetting]
                }
            ]
        }];

        this.callParent(arguments);
    }
});
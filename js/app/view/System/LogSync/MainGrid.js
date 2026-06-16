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

Ext.define('Koltiva.view.System.LogSync.MainGrid' ,{
    extend: 'Ext.panel.Panel',
    id: 'Koltiva.view.System.LogSync.MainGrid',
    renderTo: 'ext-content',
    style:'padding:0 7px 7px 7px;margin:2px 0 0 0;',
    listeners: {
        afterRender: function(component, eOpts){
            var thisObj = this;
        }
    },
    initComponent: function() {
        var thisObj = this;

        //Panel "Set by mw2_log_process" ================================================================================= (begin)
        thisObj.objPanelMwLogProcess = Ext.create('Koltiva.view.System.LogSync.PanelMwLogProcess');
        //Panel "Set by mw2_log_process" ================================================================================= (end)

        //Panel mw2_event_json =================================================================== (begin)
        thisObj.objPanelMwEventJson = Ext.create('Koltiva.view.System.LogSync.PanelMwEventJson');
        //Panel mw2_event_json =================================================================== (end)

        //Panel mw_pull_log2019 =================================================================== (begin)
        thisObj.objPanelMwPullLog = Ext.create('Koltiva.view.System.LogSync.PanelMwPullLog');
        //Panel mw_pull_log2019 =================================================================== (end)

        // ==================================================== MAIN PANEL =============================================//
        thisObj.items = [{
            layout: 'column',
            border: false,
            items: [
            {
                columnWidth: 1,
                items:[thisObj.objPanelMwLogProcess]
            },
            {
                columnWidth: 1,
                items:[{
                    html: '<br /><div style="height:10px;border-bottom:1px dashed gray;"></div><br />'
                }]
            },
            {
                columnWidth: 1,
                items:[thisObj.objPanelMwEventJson]
            },
            {
                columnWidth: 1,
                items:[{
                    html: '<br /><div style="height:10px;border-bottom:1px dashed gray;"></div><br />'
                }]
            },
            {
                columnWidth: 1,
                items:[thisObj.objPanelMwPullLog]
            }
            ]
        }];

        this.callParent(arguments);
    }
});
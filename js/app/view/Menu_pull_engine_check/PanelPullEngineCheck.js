Ext.define('Koltiva.view.Menu_pull_engine_check.PanelPullEngineCheck' ,{
    extend: 'Ext.panel.Panel',
    id: 'Koltiva.view.Menu_pull_engine_check.PanelPullEngineCheck',
    width: '100%',
    minHeight: 100,
    title: lang('Grid Pull Engine Chek'),
    style: 'border:1px solid #CCC;',
    renderTo: 'ext-content',
    loadMask: true,
    selType: 'rowmodel',
    listeners: {
        afterRender: function(component, eOpts){
        	var thisObj = this;
        }
    },
    initComponent: function() {
        var thisObj = this;

        //Store
        thisObj.StoreGridMain = Ext.create('Koltiva.store.Menu_pull_engine_check.MainGridPullEngineCheck');

        thisObj.items = [{
            xtype: 'grid',
            id: 'Koltiva.view.Menu_pull_engine_check.MainGridPullEngineCheck-Grid',
            style: 'border:1px solid #CCC;margin-top:4px;',
            cls:'Sfr_GridNew',
            loadMask: true,
            selType: 'rowmodel',
            store: thisObj.StoreGridMain,
            enableColumnHide: false,
            height: 150,
            viewConfig: {
                deferEmptyText: false,
                emptyText: GetDefaultContentNoData()
            },
            dockedItems: [{
                xtype: 'pagingtoolbar',
                store: thisObj.StoreGridMain,
                dock: 'bottom',
                displayInfo: true,
                displayMsg: lang('Showing')+' {0} '+lang('to')+' {1} '+lang('of')+' {2} '+lang('data')
            }],
            columns:[{
                text: lang('UID'),
                dataIndex: 'uid',
                flex: 10
            },{
                text: lang('Timecheck Send'),
                dataIndex: 'timecheck_send',
                flex: 10
            },{
                text: lang('Timecheck'),
                dataIndex: 'timecheck',
                flex: 10
            },{
                text: lang('Remark'),
                dataIndex: 'remark',
                flex: 10
            },{
                text: lang('Work Status'),
                dataIndex: 'WorkStatus',
                flex: 10
            },{
                text: lang('Last Send Email'),
                dataIndex: 'LastSendEmail',
                flex: 10
            }]
        }];

        this.callParent(arguments);
    }
});
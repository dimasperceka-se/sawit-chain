/*
* @Author: nikolius
* @Date:   2017-09-07 14:11:26
* @Last Modified by:   nikolius
* @Last Modified time: 2018-03-27 14:39:57
*/

/*
    Param2 yg diperlukan ketika load View ini
    1. MemberID
*/

// Define Variabel2 / Object2 / Fungsi yg diperlukan oleh view ini (begin)

// Define Variabel2 / Object2 / Fungsi yg diperlukan oleh view ini (end)

Ext.define('Koltiva.view.SME.AgentRelationPanel' ,{
    extend: 'Ext.panel.Panel',
    id: 'Koltiva.view.SME.AgentRelationPanel',
    title: lang('Agent Relation'),
    viewVar: false,
    setViewVar: function(value){
        this.viewVar = value;
    },
    loadStoreGrid: function(){
        var thisObj = this;

        //load store
        thisObj.storeGridAgentRelation.setStoreVar({MemberID:thisObj.viewVar.MemberID});
        thisObj.storeGridAgentRelation.load();
    },
    frame: true,
    collapsible:true,
    margin:'0 0 20 8',
    initComponent: function() {
        var thisObj = this;

        //store
        var storeGridAgentRelation = Ext.create('Koltiva.store.SME.GridAgentRelation');
        thisObj.storeGridAgentRelation = storeGridAgentRelation;

        thisObj.dockedItems = [{
            xtype: 'pagingtoolbar',
            id: 'Koltiva.view.SME.AgentRelationPanel-gridTraderCollectingPoint-gridToolbar',
            store: storeGridAgentRelation,
            dock: 'bottom',
            displayInfo: true
        }];

        thisObj.items = [{
            xtype: 'grid',
            id: 'Koltiva.view.SME.AgentRelationPanel-gridTraderCollectingPoint',
            loadMask: true,
            scroll: false,
            minHeight:125,
            selType: 'rowmodel',
            store: storeGridAgentRelation,
            viewConfig: {
                deferEmptyText: false,
                emptyText: GetDefaultContentNoData()
            },
            columns: [{
                text: 'No',
                xtype: 'rownumberer',
                flex: 0.3,
            },{
                text: lang('ID'),
                dataIndex: 'MemberDisplayID',
                flex: 1,
            },{
                text: lang('Name'),
                dataIndex: 'MemberName',
                flex: 1,
            },{
                text: lang('Start Date'),
                dataIndex: 'StartDate',
                flex: 1,
            },{
                text: lang('End Date'),
                dataIndex: 'EndDate',
                flex: 1,
            }]
        }];

        this.callParent(arguments);
    }
});
/******************************************
 *  Author : n1colius.lau@gmail.com   
 *  Created On : Fri Jan 04 2019
 *  File : WinFormGardenSummaryIssue.js
 *******************************************/

/*
    Param2 yg diperlukan ketika load View ini
    - DaconID
*/

Ext.define('Koltiva.view.PlotSurvey.WinFormGardenSummaryIssue' ,{
    extend: 'Ext.window.Window',
    id: 'Koltiva.view.PlotSurvey.WinFormGardenSummaryIssue',
    title: lang('Garden Survey Summary Issue'),
    cls: 'Sfr_LayoutPopupWindows',
    closable: true,
    modal: true,
    closeAction: 'destroy',
    width: '80%',
    height: '72%',
    overflowY: 'auto',
    viewVar: false,
    setViewVar: function(value){
        this.viewVar = value;
    },
    listeners: {
        afterRender: function(){
            var thisObj = this;
        }
    },
    initComponent: function() {
        var thisObj = this;

        thisObj.StoreIssueMainGrid  = Ext.create('Koltiva.store.PlotSurvey.GridSurveyGardenSummaryIssue',{
        	storeVar: {
                DaconID: thisObj.viewVar.DaconID
            }
        });

        thisObj.items = [{
            layout: 'column',
            border: false,
            padding: '0 25 0 10',
            items: [{
                columnWidth: 1,
                layout:'form',
                items: [{
                    items:[{
                        layout:'fit',
                        items:[{
                            xtype: 'gridpanel',
                            title: lang('List of Issue'),
                            id: 'Koltiva.view.Farmer.WinFormPostHarvestSummaryIssue-GridIssue',
                            style: 'border:1px solid #CCC;',
                            store: thisObj.StoreIssueMainGrid,
                            //width: '100%',
                            autoScroll: true,
                            loadMask: true,
                            selType: 'rowmodel',
                            viewConfig: {
                                deferEmptyText: false,
                                emptyText: lang('No data available')
                            },
                            columns: [{
                                text: 'No',
			                    xtype: 'rownumberer',
			                    align: 'center',
			                    width: '4%'
                            },{
                                text: lang('Status'),
                                dataIndex: 'IssueStatus',
                                width: '15%',
                                renderer: function (value) {
                                    var RetVal;

                                    switch(value){
                                        case 'Medium':
                                            RetVal = '<span class="Sfr_GridColYellowRounded">'+lang('Medium')+'</span>';
                                        break;
                                        case 'High':
                                            RetVal = '<span class="Sfr_GridColRedRounded">'+lang('High')+'</span>';
                                        break;
                                        default:
                                            RetVal = lang(value);
                                        break;
                                    }

                                    return RetVal;
                                }
                            },{
                                text: lang('Issue'),
                                dataIndex: 'Issue',
                                width: '80%',
                                renderer: function (value) {
                                    return lang(value);
                                }
                            }]
                        }]
                    }]
                }]
            }]
        }];

        //buttons -------------------------------------------------------------- (begin)
        thisObj.buttons = [{
        	icon: varjs.config.base_url + 'images/icons/new/close.png',
			text: lang('Close'),
			cls:'Sfr_BtnFormGrey',
			overCls:'Sfr_BtnFormGrey-Hover',
            handler: function() {
                thisObj.close();
            }
        }];
        //buttons -------------------------------------------------------------- (end)

        this.callParent(arguments);
    }
});
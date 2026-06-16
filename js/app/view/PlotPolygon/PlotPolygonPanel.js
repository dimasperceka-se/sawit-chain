/*
* @Author: nikolius
* @Date:   2017-07-28 10:28:56
* @Last Modified by:   nikolius
* @Last Modified time: 2017-08-31 15:22:27
*/

/*
    Param2 yg diperlukan ketika load View ini
    - MemberID
    - CallFrom
*/

// Define Variabel2 / Object2 / Fungsi yg diperlukan oleh view ini (begin)

// Define Variabel2 / Object2 / Fungsi yg diperlukan oleh view ini (end)

Ext.define('Koltiva.view.PlotPolygon.PlotPolygonPanel' ,{
    extend: 'Ext.panel.Panel',
    id: 'Koltiva.view.PlotPolygon.PlotPolygonPanel',
    title: lang('Polygons'),
    viewVar: false,
    setViewVar: function(value){
        this.viewVar = value;
    },
    frame: true,
    collapsible: true,
    collapsed: true,
    margin:'0 0 20 8',
    listeners: {
        afterRender: function () {
            var thisObj = this;
        },
        expand: function() {
            var thisObj = this;
            thisObj.storeGridPlotPolygonPanel.load();
        }
    },
    initComponent: function() {
        var thisObj = this;

        //store
        thisObj.storeGridPlotPolygonPanel = Ext.create('Koltiva.store.PlotPolygon.GridPlotPolygonPanel', {
            storeVar: {
                MemberID: thisObj.viewVar.MemberID,
                CallFrom: thisObj.viewVar.CallFrom
            }
        });

        //context menu
        var contextMenuGridPlotPolygon = Ext.create('Ext.menu.Menu',{
            items:[{
                icon: varjs.config.base_url + 'images/icons/new/view.png',
                text: lang('View Polygon'),
                handler: function() {
                    var sm = Ext.getCmp('Koltiva.view.PlotPolygon.PlotPolygonPanel-gridPlotPolygon').getSelectionModel().getSelection()[0];

                    if(sm.get('DateCreated') == null){
                        Ext.MessageBox.show({
                            title: 'Warning',
                            msg: 'Polygon data not available',
                            buttons: Ext.MessageBox.OK,
                            animateTarget: 'mb9',
                            icon: 'ext-mb-warning'
                        });
                    }else{
                        //window form plot survey
                        var winPlotPolygon = Ext.create('Koltiva.view.PlotPolygon.WinPlotPolygon');
                        winPlotPolygon.setViewVar({
                            MemberID:thisObj.viewVar.MemberID,
                            PlotNr: sm.get('PlotNr'),
                            SurveyNr: sm.get('SurveyNr'),
                            DateCollection: sm.get('DateCollection'),
                            CallFrom: thisObj.viewVar.CallFrom
                        });
                        if (!winPlotPolygon.isVisible()) {
                            winPlotPolygon.center();
                            winPlotPolygon.show();
                        } else {
                            winPlotPolygon.close();
                        }
                    }
                }
            }]
        });

        thisObj.items = [{
            xtype: 'grid',
            id: 'Koltiva.view.PlotPolygon.PlotPolygonPanel-gridPlotPolygon',
            loadMask: true,
            selType: 'rowmodel',
            store: thisObj.storeGridPlotPolygonPanel,
            viewConfig: {
                deferEmptyText: false,
                emptyText: GetDefaultContentNoData()
            },
            columns: [{
                text: lang('Action'),
                xtype:'actioncolumn',
                flex: 0.5,
                items:[{
                    icon: varjs.config.base_url + 'images/icons/new/action.png',
                    tooltip: 'Action',
                    handler: function(grid, rowIndex, colIndex, item, e, record) {
                        contextMenuGridPlotPolygon.showAt(e.getXY());
                    }
                }]
            },{
                text: lang('Garden Nr'),
                dataIndex: 'PlotNr',
                flex: 1,
            },{
                text: lang('SurveyNr'),
                dataIndex: 'SurveyNr',
                hidden:true
            },{
                text: lang('Survey'),
                dataIndex: 'Survey',
                flex: 1,
            },{
                dataIndex: 'DateCollection',
                hidden:true
            },{
                text: lang('Check'),
                dataIndex: 'StatusCheck',
                flex: 1,
            },{
                text: lang('Date Created'),
                dataIndex: 'DateCreated',
                flex: 1,
            },{
                text: lang('Enumerator'),
                dataIndex: 'Enumerator',
                flex: 1,
            }]
        }];

        this.callParent(arguments);
    }
});